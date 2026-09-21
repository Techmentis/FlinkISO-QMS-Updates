<?php
App::uses('AppController', 'Controller');
App::uses('Security', 'Utility');
App::uses('Cache', 'Cache');
App::uses('CakeText', 'Utility');

/**
* Authenticated client bridge for the paid API v2 AI service.
* Local history and rebuild authorization remain tenant-side.
*/
class AisController extends AppController {

    public $uses = array('CustomTable', 'QcDocument', 'Ai');
    private $_debugStream = false;
    private $_historyId = '';
    private $_rawModelResponse = '';

    public function beforeFilter() {
        $this->_loadAiConfiguration();
        if (Configure::read('AI.ai_enabled') !== true) {
            $this->autoRender = false;
            $this->response->type('json');
            $this->response->statusCode(403);
            $this->response->body(json_encode(array(
                'success' => false,
                'message' => __('AI functions are disabled.')
            )));
            return $this->response;
        }
        $this->_check_login();
    }

    public function preview() {
        $this->autoRender = false;

        if (!$this->request->is('post')) {
            return $this->_jsonResponse(false, array('message' => __('Only POST requests are accepted.')), 405);
        }

        $prompt = isset($this->request->data['prompt']) ? trim($this->request->data['prompt']) : '';
        $sourceController = isset($this->request->data['source_controller']) ? trim($this->request->data['source_controller']) : '';
        $sourceAction = isset($this->request->data['source_action']) ? trim($this->request->data['source_action']) : '';
        $customTableId = isset($this->request->data['custom_table_id']) ? trim($this->request->data['custom_table_id']) : '';
        $qcDocumentId = isset($this->request->data['qc_document_id']) ? trim($this->request->data['qc_document_id']) : '';
        $sendCurrentDocument = isset($this->request->data['send_current_document']) && (string)$this->request->data['send_current_document'] === '1';
        $debugStream = !empty($this->request->data['debug_stream']);
        $userId = $this->Session->read('User.id');
        $companyId = $this->Session->read('User.company_id');
        $canRebuild = $this->Session->read('User.is_mr') == true;

        $isGeneratedForm = strpos($sourceController, 'tbl_') === 0 || strpos($sourceController, 'chd_') === 0;
        if ($sourceController !== 'qc_documents' && !$isGeneratedForm) {
            return $this->_jsonResponse(false, array('message' => __('AI preview is not available in this section.')), 403);
        }
        if ($prompt === '') {
            return $this->_jsonResponse(false, array('message' => __('Enter a request for FlinkISO AI.')), 422);
        }
        if (strlen($prompt) > 4000) {
            return $this->_jsonResponse(false, array('message' => __('The request is too long.')), 422);
        }

        $this->_startAiHistory($prompt, $sourceController, $sourceAction, $customTableId, $qcDocumentId, isset($this->request->data['record_id']) ? trim($this->request->data['record_id']) : '');

        // Do not send simple conversation through a multi-minute document
        // analysis. Only exact, non-actionable greetings are handled here;
        // a request such as "Hi, create this form" still goes to the model.
        $quickReply = $this->_quickConversationReply($prompt, $isGeneratedForm);
        if ($quickReply !== null) {
            return $this->_jsonResponse(true, array(
            'message' => $quickReply,
            'intent' => 'message',
            'operation' => 'message',
            'form_name' => '',
            'field_details' => array(),
            'proposed_fields' => array(),
            'insert_after' => '',
            'target_field' => '',
            'new_field_label' => '',
            'options_to_add' => array(),
            'options_to_remove' => array(),
            'field_changes' => array(),
            'fields_to_remove' => array(),
            'warnings' => array(),
            'model' => '',
            'duration_ms' => 0,
            'read_only' => true,
            'auto_apply' => false,
            'apply_token' => '',
            'apply_url' => ''
            ));
        }

        $customTable = array();
        $existingFields = array();
        $formContext = null;
        $documentContext = null;
        $documentText = '';
        $documentFileBase64 = '';
        $qualityDocumentTitle = '';
        if ($isGeneratedForm) {
            if ($customTableId === '') {
                return $this->_jsonResponse(false, array('message' => __('The generated form context is missing its custom_table_id.')), 422);
            }
            $conditions = array(
            'CustomTable.id' => $customTableId,
            'CustomTable.table_name' => $sourceController
            );
            if ($companyId) $conditions['CustomTable.company_id'] = $companyId;
            $customTable = $this->CustomTable->find('first', array('recursive' => -1, 'conditions' => $conditions));
            if (!$customTable) {
                return $this->_jsonResponse(false, array('message' => __('The current generated form could not be found.')), 404);
            }
            $existingFields = json_decode($customTable['CustomTable']['fields'], true);
            if (!is_array($existingFields)) {
                return $this->_jsonResponse(false, array('message' => __('The current form has an invalid saved field definition.')), 422);
            }
            $formContext = $this->_formContext($customTable, $existingFields);
            $guidedAction = isset($this->request->data['guided_action']) ? trim($this->request->data['guided_action']) : '';
            if ($guidedAction !== '') {
                return $this->_handleGuidedFieldAction(
                $guidedAction,
                isset($this->request->data['guided_field']) ? trim($this->request->data['guided_field']) : '',
                isset($this->request->data['guided_value']) ? trim($this->request->data['guided_value']) : '',
                isset($this->request->data['guided_options']) ? $this->request->data['guided_options'] : '',
                $existingFields, $customTable, $canRebuild, $userId, $companyId, $customTableId, $sourceController
                );
            }
            $visibilityIssue = $this->_explainFieldVisibility($prompt, $existingFields);
            if ($visibilityIssue !== null) {
                return $this->_jsonResponse(true, array(
                'message' => $visibilityIssue,
                'intent' => 'message',
                'operation' => 'message',
                'form_name' => $customTable['CustomTable']['name'],
                'field_details' => array(),
                'proposed_fields' => array(),
                'insert_after' => '',
                'target_field' => '',
                'new_field_label' => '',
                'options_to_add' => array(),
                'options_to_remove' => array(),
                'field_changes' => array(),
                'fields_to_remove' => array(),
                'warnings' => array(),
                'model' => '',
                'duration_ms' => 0,
                'read_only' => true,
                'auto_apply' => false,
                'apply_token' => '',
                'apply_url' => ''
                ));
            }

            // Basic configuration commands are safer and much faster when
            // resolved directly against the authoritative saved definition.
            // This also prevents a small model from echoing the entire form.
            $directPropertyChange = $this->_inferFieldPropertyChange($prompt, $existingFields);
            if ($directPropertyChange) {
                $targetField = $directPropertyChange['target_field'];
                $fieldChanges = $directPropertyChange['field_changes'];
                $updated = $this->_updateFieldProperties($existingFields, $targetField, $fieldChanges);
                if ($updated['errors']) {
                    return $this->_jsonResponse(true, array(
                    'message' => __('I could not safely update that field configuration. Review the notes and clarify the request.'),
                    'intent' => 'clarification', 'operation' => 'update_field_properties',
                    'form_name' => $customTable['CustomTable']['name'], 'field_details' => array(), 'proposed_fields' => array(),
                    'insert_after' => '', 'target_field' => $targetField, 'new_field_label' => '',
                    'options_to_add' => array(), 'options_to_remove' => array(), 'field_changes' => $fieldChanges,
                    'fields_to_remove' => array(), 'warnings' => $updated['errors'], 'model' => '', 'duration_ms' => 0,
                    'read_only' => true, 'auto_apply' => false, 'apply_token' => '', 'apply_url' => ''
                    ));
                }

                $applyToken = '';
                $warnings = array();
                $message = __('The requested field change was prepared.');
                if ($canRebuild) {
                    $applyToken = Security::hash(uniqid((string)mt_rand(), true).$userId, 'sha256', true);
                    $pendingChange = array(
                    'ai_id' => $this->_historyId, 'user_id' => $userId, 'company_id' => $companyId,
                    'custom_table_id' => $customTableId, 'source_controller' => $sourceController,
                    'base_fields_hash' => hash('sha256', json_encode(array_values($existingFields))),
                    'fields' => $updated['fields'], 'expires' => time() + 600
                    );
                    if ($this->_writeAiPending($applyToken, $pendingChange)) {
                        $message = __('The requested field change was prepared. FlinkISO is rebuilding the form through the API.');
                    } else {
                        $applyToken = '';
                        $warnings[] = __('The server could not store the approved AI change for rebuilding.');
                    }
                } else {
                    $warnings[] = __('Only an MR user can rebuild a generated form.');
                    $message = __('The requested field change is ready for review, but your account cannot rebuild this form.');
                }
                return $this->_jsonResponse(true, array(
                'message' => $message, 'intent' => 'form_preview', 'operation' => 'update_field_properties',
                'form_name' => $customTable['CustomTable']['name'], 'field_details' => $updated['fields'],
                'proposed_fields' => array($updated['updated_field']), 'insert_after' => '', 'target_field' => $targetField,
                'new_field_label' => '', 'options_to_add' => array(), 'options_to_remove' => array(),
                'field_changes' => $fieldChanges, 'fields_to_remove' => array(), 'warnings' => $warnings,
                'model' => '', 'duration_ms' => 0, 'read_only' => $applyToken === '', 'auto_apply' => $applyToken !== '',
                'apply_token' => $applyToken,
                'apply_url' => $applyToken !== '' ? Router::url(array('controller' => 'custom_tables', 'action' => 'ai_rebuild'), true) : ''
                ));
            }

            $clarificationQuestion = $this->_clarificationQuestion($prompt, $existingFields);
            if ($clarificationQuestion !== null) {
                return $this->_jsonResponse(true, array(
                'message' => $clarificationQuestion['question'],
                'intent' => 'clarification', 'operation' => 'clarification',
                'form_name' => $customTable['CustomTable']['name'], 'field_details' => array(), 'proposed_fields' => array(),
                'insert_after' => '', 'target_field' => '', 'new_field_label' => '',
                'options_to_add' => array(), 'options_to_remove' => array(), 'field_changes' => array(),
                'fields_to_remove' => array(), 'warnings' => array(), 'clarification_question' => $clarificationQuestion,
                'model' => '', 'duration_ms' => 0, 'read_only' => true, 'auto_apply' => false,
                'apply_token' => '', 'apply_url' => ''
                ));
            }
        } elseif ($sourceController === 'qc_documents') {
            if ($qcDocumentId === '') {
                return $this->_jsonResponse(false, array('message' => __('Open the Quality Document that should be converted into a form, then try again.')), 422);
            }
            $documentConditions = array('QcDocument.id' => $qcDocumentId);
            if ($companyId) $documentConditions['QcDocument.company_id'] = $companyId;
            $qcDocument = $this->QcDocument->find('first', array('recursive' => -1, 'conditions' => $documentConditions));
            if (!$qcDocument) {
                return $this->_jsonResponse(false, array('message' => __('The current Quality Document could not be found.')), 404);
            }
            $qualityDocumentTitle = $qcDocument['QcDocument']['title'];
            if ($sendCurrentDocument) {
                $documentPath = $this->_qcDocumentPath($qcDocument);
                if ($documentPath === '') {
                    return $this->_jsonResponse(false, array('message' => __('The document displayed in ONLYOFFICE could not be found on the FlinkISO server.')), 404);
                }
                $visualExtension = strtolower(pathinfo($documentPath, PATHINFO_EXTENSION));
                $isVisualDocument = in_array($visualExtension, array('pdf', 'docx', 'xlsx'), true);
                $extracted = $this->_extractDocumentText($documentPath);
                if (!$extracted['success'] && !$isVisualDocument) {
                    return $this->_jsonResponse(false, array('message' => $extracted['message']), 422);
                }
                // A scanned PDF can have no text layer. Vision analysis can
                // still read its rendered pages, so text extraction is an
                // enhancement rather than a prerequisite for visual formats.
                $documentText = $extracted['success'] ? $extracted['text'] : '';
                // API V2 owns document AI. Send the original PDF as reference
                // material so its vision model can see tables, columns and
                // repeated line items that disappear in pdftotext output.
                if ($isVisualDocument) {
                    $documentBytes = @filesize($documentPath);
                    $maxVisionBytes = 25 * 1024 * 1024;
                    if ($documentBytes === false || $documentBytes <= 0 || $documentBytes > $maxVisionBytes) {
                        return $this->_jsonResponse(false, array('message' => __('The selected document must be smaller than 25 MB for visual AI analysis.')), 413);
                    }
                    $rawDocument = @file_get_contents($documentPath);
                    if ($rawDocument === false) {
                        return $this->_jsonResponse(false, array('message' => __('FlinkISO could not read the selected document for visual AI analysis.')), 422);
                    }
                    $documentFileBase64 = base64_encode($rawDocument);
                    unset($rawDocument);
                }
                $documentContext = array(
                'id' => $qcDocument['QcDocument']['id'],
                'title' => $qcDocument['QcDocument']['title'],
                'document_number' => $qcDocument['QcDocument']['document_number'],
                'revision_number' => $qcDocument['QcDocument']['revision_number'],
                'file_type' => $qcDocument['QcDocument']['file_type'],
                'source_file' => basename($documentPath),
                'text_extraction_warning' => $extracted['success'] ? '' : $extracted['message']
                );
            }

            // If the browser's automatic rebuild POST failed after a valid
            // preview, reuse that one-time creation job instead of running the
            // document through Ollama for another several minutes.
            if ($canRebuild && preg_match('/\b(retry|finish|resume|continue)\b.*\b(form|creation)\b/i', $prompt)) {
                $pendingCreate = $this->_findPendingCreate($userId, $companyId, $qcDocumentId);
                if ($pendingCreate) {
                    $pendingCreate['pending']['ai_id'] = $this->_historyId;
                    $this->_writeAiPending($pendingCreate['token'], $pendingCreate['pending']);
                    return $this->_jsonResponse(true, array(
                    'message' => __('The validated form definition was found. FlinkISO is retrying form creation without running AI analysis again.'),
                    'intent' => 'form_preview',
                    'operation' => 'create_form',
                    'form_name' => !empty($pendingCreate['pending']['form_name']) ? $pendingCreate['pending']['form_name'] : $qualityDocumentTitle,
                    'field_details' => array_values($pendingCreate['pending']['fields']),
                    'proposed_fields' => array(),
                    'insert_after' => '',
                    'target_field' => '',
                    'new_field_label' => '',
                    'options_to_add' => array(),
                    'options_to_remove' => array(),
                    'field_changes' => array(),
                    'fields_to_remove' => array(),
                    'warnings' => array(__('Reusing the previously validated field definition; AI analysis was not run again.')),
                    'model' => '',
                    'duration_ms' => 0,
                    'read_only' => false,
                    'auto_apply' => true,
                    'apply_token' => $pendingCreate['token'],
                    'apply_url' => Router::url(array('controller' => 'custom_tables', 'action' => 'ai_rebuild'), true)
                    ));
                }
            }

            // Recover a form definition saved by an earlier generator failure
            // without asking Ollama to regenerate the document fields.
            $savedFormConditions = array('CustomTable.qc_document_id' => $qcDocumentId);
            if ($companyId) $savedFormConditions['CustomTable.company_id'] = $companyId;
            $savedForm = $this->CustomTable->find('first', array(
            'recursive' => -1,
            'conditions' => $savedFormConditions
            ));
            if ($canRebuild && !empty($savedForm['CustomTable']['id'])) {
                $savedFormFields = json_decode($savedForm['CustomTable']['fields'], true);
                $controllerFile = APP.'Controller'.DS.Inflector::pluralize(Inflector::classify($savedForm['CustomTable']['table_name'])).'Controller.php';
                $explicitRetry = preg_match('/\b(retry|finish|repair)\b/i', $prompt);
                $missingMvc = !is_file($controllerFile);
                if (is_array($savedFormFields) && ($explicitRetry || ($missingMvc && preg_match('/\b(create|build|generate|form)\b/i', $prompt)))) {
                    $applyToken = Security::hash(uniqid((string)mt_rand(), true).$userId, 'sha256', true);
                    $pendingRecovery = array(
                    'operation' => 'rebuild_saved_form',
                    'ai_id' => $this->_historyId,
                    'user_id' => $userId,
                    'company_id' => $companyId,
                    'custom_table_id' => $savedForm['CustomTable']['id'],
                    'source_controller' => $savedForm['CustomTable']['table_name'],
                    'base_fields_hash' => hash('sha256', json_encode(array_values($savedFormFields))),
                    'fields' => array_values($savedFormFields),
                    'expires' => time() + 900
                    );
                    if ($this->_writeAiPending($applyToken, $pendingRecovery)) {
                        return $this->_jsonResponse(true, array(
                        'message' => __('The saved form definition was found. FlinkISO is retrying MVC generation through the API.'),
                        'form_name' => $savedForm['CustomTable']['name'],
                        'field_details' => array_values($savedFormFields),
                        'proposed_fields' => array(),
                        'insert_after' => '',
                        'target_field' => '',
                        'new_field_label' => '',
                        'options_to_add' => array(),
                        'fields_to_remove' => array(),
                        'warnings' => array(__('Reusing the previously validated field definition; AI analysis was not run again.')),
                        'model' => '',
                        'duration_ms' => 0,
                        'read_only' => false,
                        'auto_apply' => true,
                        'apply_token' => $applyToken,
                        'apply_url' => Router::url(array('controller' => 'custom_tables', 'action' => 'ai_rebuild'), true)
                        ));
                    }
                }
            }
        }

        // Paid AI execution is owned by API v2. This application supplies only
        // authenticated tenant context and keeps its local request history.
        $aiApiBase = rtrim((string)Configure::read('ApiPath'), '/');
        $model = 'API v2';
        $timeout = 360;
        $documentLength = strlen($documentText);
        $isLongDocument = $documentLength > 12000;
        // A complete form can contain many compact field definitions even
        // when its source PDF is only a few pages.  The former 1,000-token
        // ceiling cut valid JSON off midway through larger forms.
        $documentNumPredict = 4000;
        $requestTimeout = $documentText !== ''
        ? ($isLongDocument ? 600 : 300)
        : ($timeout > 0 ? min($timeout, 180) : 180);
        if ($aiApiBase === '') {
            return $this->_jsonResponse(false, array('message' => __('The FlinkISO AI API is not configured.')), 503);
        }

        $payload = array(
        'request_id' => $this->_historyId,
        'prompt' => $prompt,
        'source_controller' => $sourceController,
        'source_action' => $sourceAction,
        'form_context' => $formContext,
        'document_context' => $documentContext,
        'document_text' => $documentText,
        'document_file_base64' => $documentFileBase64,
        'document_included' => $sendCurrentDocument,
        'ai_config' => array(
            'ai_provider' => Configure::read('AI.ai_provider'),
            'ai_api' => Configure::read('AI.ai_api'),
            'ai_api_key' => Configure::read('AI.ai_api_key'),
            'ai_model' => Configure::read('AI.ai_model'),
            'ai_vision_model' => Configure::read('AI.ai_vision_model'),
            'ai_timeout' => (int)Configure::read('AI.ai_timeout'),
            'ai_vision_context' => (int)Configure::read('AI.ai_vision_context'),
            'vision_pdf_max_pages' => (int)Configure::read('AI.vision_pdf_max_pages'),
            'vision_page_pixels' => (int)Configure::read('AI.vision_page_pixels'),
            'pdf_to_ppm_path' => Configure::read('AI.pdf_to_ppm_path'),
            'libreoffice_path' => Configure::read('AI.libreoffice_path')
        )
        );

        // PHP's file-session handler holds an exclusive lock for the entire
        // request. Ollama can run for several minutes, so release that lock
        // before waiting; SessionComponent will reopen it later only if a
        // rebuild token must be written.
        if (session_id()) @session_write_close();

        // Ollama serializes work for one loaded CPU model. Do not let several
        // browser tabs silently queue expensive generations and make every
        // request appear frozen.
        $generationLock = @fopen(TMP.'flinkiso_ai_generation.lock', 'c+');
        if (!$generationLock || !@flock($generationLock, LOCK_EX | LOCK_NB)) {
            if (is_resource($generationLock)) @fclose($generationLock);
            return $this->_jsonResponse(false, array(
            'message' => __('FlinkISO AI is already processing another request. Stop or wait for that request before starting a new one.'),
            'error_type' => 'busy'
            ), 429);
        }

        // Keep Apache/mod_fastcgi and the browser connection alive while
        // Ollama is processing a large document. MAMP's FastCGI worker can
        // otherwise treat the quiet request as abandoned long before the AI
        // timeout is reached.
        // A closed client connection is also the cancellation signal. The
        // heartbeat below makes PHP notice it promptly and closes the Ollama
        // stream, stopping generation rather than leaving it in background.
        @ignore_user_abort(false);
        // Do not impose a wall-clock limit on a healthy streamed response.
        // Large forms may take longer on a CPU-only model; the UI Stop button
        // closes the client/Ollama stream when the user chooses to cancel.
        @set_time_limit(0);
        @ini_set('zlib.output_compression', '0');
        @ini_set('output_buffering', '0');
        if (function_exists('apache_setenv')) @apache_setenv('no-gzip', '1');
        $this->_debugStream = $debugStream;
        if (!headers_sent()) {
            header('Content-Type: '.($debugStream ? 'application/x-ndjson' : 'application/json').'; charset=UTF-8');
            header('X-Accel-Buffering: no');
            header('Cache-Control: no-cache, no-store, must-revalidate');
        }

        $startedAt = microtime(true);
        $lastHeartbeat = $startedAt;
        $streamBuffer = '';
        $modelContent = '';
        $streamError = '';
        $doneReason = '';
        $generatedTokenCount = 0;
        $apiV2Result = null;
        $cancelRequested = false;
        $cancelPath = $this->_aiCancelPath($this->_historyId, true);
        if ($cancelPath !== '' && is_file($cancelPath)) @unlink($cancelPath);

        $emitStreamEvent = function ($type, $data = array()) use ($debugStream) {
            if (!$debugStream) return;
            echo json_encode(array('type' => $type, 'data' => $data))."\n";
            if (ob_get_level() > 0) @ob_flush();
            @flush();
        };

        $heartbeat = function () use (&$lastHeartbeat, $debugStream, $emitStreamEvent) {
            if ((microtime(true) - $lastHeartbeat) < 5) return;
            $this->_touchAiHistory();
            if ($debugStream) {
                $emitStreamEvent('heartbeat', array('elapsed_ms' => (int)((microtime(true) - $lastHeartbeat) * 1000)));
            } else {
                echo str_repeat(' ', 4096);
            }
            if (ob_get_level() > 0) @ob_flush();
            @flush();
            $lastHeartbeat = microtime(true);
        };

        // Send the first bytes immediately. The transfer callback below then
        // refreshes the connection every five seconds, including during
        // Ollama's prompt-evaluation phase before its first generated token.
        if ($debugStream) {
            $emitStreamEvent('status', array(
            'message' => 'Connected to FlinkISO AI API. Waiting for model output.',
            'model' => $model,
            'request_id' => $this->_historyId,
            'document_characters' => $documentLength,
            'context' => $documentText !== '' ? 8192 : 4096,
            'output_limit' => $documentText !== '' ? $documentNumPredict : 1500
            ));
        } else {
            echo str_repeat(' ', 8192);
        }
        if (ob_get_level() > 0) @ob_flush();
        @flush();
        $lastHeartbeat = microtime(true);

        $consumeLine = function ($line) use (&$modelContent, &$streamError, &$doneReason, &$generatedTokenCount, &$apiV2Result, $emitStreamEvent) {
            $chunk = json_decode(trim($line), true);
            if (!is_array($chunk)) return;
            if (!empty($chunk['api_v2_heartbeat'])) return;
            if (isset($chunk['api_v2_result']) && is_array($chunk['api_v2_result'])) {
                $apiV2Result = $chunk['api_v2_result'];
                return;
            }
            if (!empty($chunk['error'])) {
                if (is_array($chunk['error'])) {
                    $streamError = !empty($chunk['error']['message'])
                        ? (string)$chunk['error']['message']
                        : json_encode($chunk['error']);
                } else {
                    $streamError = (string)$chunk['error'];
                }
            }
            if (isset($chunk['message']['content'])) $modelContent .= $chunk['message']['content'];
            if (!empty($chunk['done_reason'])) $doneReason = (string)$chunk['done_reason'];
            if (isset($chunk['eval_count'])) $generatedTokenCount = (int)$chunk['eval_count'];
            $emitStreamEvent('ai_model', $chunk);
        };

        $curl = curl_init();
        $curlOptions = array(
        CURLOPT_URL => $aiApiBase.'/ai_services/preview/'.$companyId.'/api:true/company_id:'.$companyId,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
        CURLOPT_NOPROGRESS => false,
        CURLOPT_WRITEFUNCTION => function ($handle, $data) use (&$streamBuffer, $consumeLine, $heartbeat) {
            $streamBuffer .= $data;
            while (($newline = strpos($streamBuffer, "\n")) !== false) {
                $line = substr($streamBuffer, 0, $newline);
                $streamBuffer = substr($streamBuffer, $newline + 1);
                $consumeLine($line);
            }
            $heartbeat();
            return strlen($data);
        }
        );
        curl_setopt_array($curl, $curlOptions);
        $progressCallback = function () use ($heartbeat, &$cancelRequested, $cancelPath) {
            $heartbeat();
            if ($cancelPath !== '' && is_file($cancelPath)) {
                $cancelRequested = true;
                return 1;
            }
            return 0;
        };
        if (defined('CURLOPT_XFERINFOFUNCTION')) {
            curl_setopt($curl, CURLOPT_XFERINFOFUNCTION, $progressCallback);
        } else {
            curl_setopt($curl, CURLOPT_PROGRESSFUNCTION, $progressCallback);
        }
        $rawResponse = curl_exec($curl);
        $curlErrno = curl_errno($curl);
        $curlError = curl_error($curl);
        $httpCode = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $durationMs = (int)round((microtime(true) - $startedAt) * 1000);
        if (trim($streamBuffer) !== '') $consumeLine($streamBuffer);
        $this->_rawModelResponse = $modelContent;

        if ($cancelRequested || ($cancelPath !== '' && is_file($cancelPath))) {
            if ($cancelPath !== '') @unlink($cancelPath);
            return $this->_jsonResponse(false, array(
            'message' => __('AI generation was cancelled.'),
            'error_type' => 'cancelled',
            'duration_ms' => $durationMs
            ), 409);
        }

        $completeTimedOutResponse = $curlErrno === CURLE_OPERATION_TIMEDOUT && is_array(json_decode($modelContent, true));
        if (($rawResponse === false || $curlError !== '') && !$completeTimedOutResponse) {
            if ($curlErrno === CURLE_OPERATION_TIMEDOUT) {
                CakeLog::write('error', 'FlinkISO AI API generation timed out: '.$curlError);
                return $this->_jsonResponse(false, array(
                'message' => __('The FlinkISO AI API did not finish generating this form within %s minutes.', round($requestTimeout / 60, 1)),
                'error_type' => 'generation_timeout',
                'duration_ms' => $durationMs
                ), 504);
            }
            CakeLog::write('error', 'FlinkISO AI API connection failed: '.$curlError);
            return $this->_jsonResponse(false, array(
            'message' => __('Could not connect to the FlinkISO AI API.'),
            'error_type' => 'connection',
            'duration_ms' => $durationMs
            ), 503);
        }

        if ($streamError !== '') {
            CakeLog::write('error', 'FlinkISO AI API error: '.$streamError);
            $isBusy = stripos($streamError, 'already processing') !== false;
            $isUnauthorized = stripos($streamError, 'unauthorized') !== false || stripos($streamError, 'invalid api key') !== false;
            return $this->_jsonResponse(false, array(
            'message' => $isBusy
                ? __('FlinkISO AI is processing another request. Wait for it to finish, or stop it from the panel where it was started.')
                : ($isUnauthorized
                    ? __('The AI provider rejected the API key. Create or copy a valid key from the provider, then save it again in AI Setup.')
                    : $streamError),
            'error_type' => $isBusy ? 'busy' : ($isUnauthorized ? 'ai_auth' : 'ai_api'),
            'duration_ms' => $durationMs
            ), $isBusy ? 429 : ($isUnauthorized ? 401 : 502));
        }

        if ($httpCode < 200 || $httpCode >= 300 || ($modelContent === '' && !$apiV2Result)) {
            CakeLog::write('error', 'FlinkISO AI API returned HTTP '.$httpCode.'.');
            return $this->_jsonResponse(false, array(
            'message' => __('The FlinkISO AI API returned an invalid response.'),
            'duration_ms' => $durationMs
            ), 502);
        }

        if ($doneReason === 'length') {
            CakeLog::write('error', 'FlinkISO AI output was truncated after '.$generatedTokenCount.' tokens.');
            return $this->_jsonResponse(false, array(
            'message' => __('The AI service reached its output limit before it finished the form. No partial form was created. Please try again.'),
            'error_type' => 'output_truncated',
            'generated_tokens' => $generatedTokenCount,
            'duration_ms' => $durationMs
            ), 502);
        }

        $modelResponse = $apiV2Result && isset($apiV2Result['model_response']) ? $apiV2Result['model_response'] : null;
        if (!is_array($modelResponse) || !isset($modelResponse['intent'], $modelResponse['operation'], $modelResponse['message'], $modelResponse['fields'], $modelResponse['insert_after'], $modelResponse['target_field'], $modelResponse['new_field_label'], $modelResponse['options_to_add'], $modelResponse['fields_to_remove'])) {
            return $this->_jsonResponse(false, array(
            'message' => __('The AI response did not match the required preview format.'),
            'duration_ms' => $durationMs
            ), 502);
        }

        $operation = in_array($modelResponse['operation'], array('create_form', 'add_fields', 'update_field_options', 'remove_field_options', 'update_field_label', 'update_field_properties', 'reorder_field', 'remove_fields', 'message', 'clarification'), true) ? $modelResponse['operation'] : 'clarification';
        $compiled = isset($apiV2Result['compiled']) && is_array($apiV2Result['compiled']) ? $apiV2Result['compiled'] : array('fields' => array(), 'errors' => array('The API did not return a compiled field definition.'));
        $compiledChildren = isset($apiV2Result['child_tables']) && is_array($apiV2Result['child_tables'])
        ? $apiV2Result['child_tables'] : array('tables' => array(), 'errors' => array());
        if (!empty($apiV2Result['model'])) $model = (string)$apiV2Result['model'];
        $warnings = isset($modelResponse['warnings']) && is_array($modelResponse['warnings']) ? $modelResponse['warnings'] : array();
        $warnings = array_values(array_merge($warnings, $compiled['errors'], !empty($compiledChildren['errors']) ? $compiledChildren['errors'] : array()));
        $intent = in_array($modelResponse['intent'], array('message', 'form_preview', 'clarification'), true) ? $modelResponse['intent'] : 'clarification';
        $message = trim($modelResponse['message']);
        $fieldDetails = $compiled['fields'];
        $insertAfter = trim($modelResponse['insert_after']);
        $targetField = trim($modelResponse['target_field']);
        $newFieldLabel = trim($modelResponse['new_field_label']);
        $optionsToAdd = is_array($modelResponse['options_to_add']) ? $modelResponse['options_to_add'] : array();
        $optionsToRemove = isset($modelResponse['options_to_remove']) && is_array($modelResponse['options_to_remove']) ? $modelResponse['options_to_remove'] : array();
        $fieldChanges = isset($modelResponse['field_changes']) && is_array($modelResponse['field_changes']) ? $modelResponse['field_changes'] : array();
        $fieldsToRemove = is_array($modelResponse['fields_to_remove']) ? $modelResponse['fields_to_remove'] : array();
        $proposedFields = $isGeneratedForm ? $compiled['fields'] : array();
        $applyToken = '';
        $changeReady = false;

        // Small local models can confuse a label rename with an option edit.
        // Resolve the unambiguous "change X to Y" form against the saved schema.
        if ($isGeneratedForm) {
            $inferredLabelChange = $this->_inferLabelChange($prompt, $existingFields);
            $inferredFieldRemoval = $this->_inferFieldRemoval($prompt, $existingFields);
            $inferredPropertyChange = $this->_inferFieldPropertyChange($prompt, $existingFields);
            if ($inferredFieldRemoval) {
                $operation = 'remove_fields';
                $fieldsToRemove = $inferredFieldRemoval;
                $targetField = '';
                $newFieldLabel = '';
                $optionsToAdd = array();
                $optionsToRemove = array();
                $fieldChanges = array();
                $insertAfter = '';
                $proposedFields = array();
            } elseif ($inferredPropertyChange) {
                $operation = 'update_field_properties';
                $targetField = $inferredPropertyChange['target_field'];
                $fieldChanges = $inferredPropertyChange['field_changes'];
                $newFieldLabel = '';
                $optionsToAdd = array();
                $optionsToRemove = array();
                $insertAfter = '';
                $proposedFields = array();
            } elseif ($inferredLabelChange) {
                $operation = 'update_field_label';
                $targetField = $inferredLabelChange['target_field'];
                $newFieldLabel = $inferredLabelChange['new_field_label'];
                $optionsToAdd = array();
                $optionsToRemove = array();
                $fieldChanges = array();
                $insertAfter = '';
                $proposedFields = array();
            }
        }

        if ($isGeneratedForm) {
            if ($operation === 'add_fields') {
                $explicitPlacement = preg_match('/\b(?:after|before|following|below|above|between|next\s+to)\b/i', $prompt) === 1;
                $merged = $this->_mergeExistingFields($existingFields, $compiled['fields'], $insertAfter, $explicitPlacement);
                $warnings = array_values(array_merge($warnings, $merged['errors']));
                if (!empty($merged['warnings'])) $warnings = array_values(array_merge($warnings, $merged['warnings']));
                if (isset($merged['insert_after'])) $insertAfter = $merged['insert_after'];
                if ($merged['errors']) {
                    $intent = 'clarification';
                    $fieldDetails = array();
                    $message = __('I could not safely place the new field in the existing form. Review the notes and clarify the request.');
                } else {
                    $fieldDetails = $merged['fields'];
                    $intent = 'form_preview';
                    $changeReady = true;
                }
            } elseif ($operation === 'update_field_options') {
                $updated = $this->_addFieldOptions($existingFields, $targetField, $optionsToAdd);
                $warnings = array_values(array_merge($warnings, $updated['errors']));
                if ($updated['errors']) {
                    $intent = 'clarification';
                    $fieldDetails = array();
                    $proposedFields = array();
                    $message = __('I could not safely update the choices for that field. Review the notes and clarify the request.');
                } else {
                    $fieldDetails = $updated['fields'];
                    $proposedFields = array($updated['updated_field']);
                    $intent = 'form_preview';
                    $changeReady = true;
                }
            } elseif ($operation === 'update_field_label') {
                $updated = $this->_updateFieldLabel($existingFields, $targetField, $newFieldLabel);
                $warnings = array_values(array_merge($warnings, $updated['errors']));
                if ($updated['errors']) {
                    $intent = 'clarification';
                    $fieldDetails = array();
                    $proposedFields = array();
                    $message = __('I could not safely update that field label. Review the notes and clarify the request.');
                } else {
                    $fieldDetails = $updated['fields'];
                    $proposedFields = array($updated['updated_field']);
                    $intent = 'form_preview';
                    $changeReady = true;
                }
            } elseif ($operation === 'update_field_properties') {
                $updated = $this->_updateFieldProperties($existingFields, $targetField, $fieldChanges);
                $warnings = array_values(array_merge($warnings, $updated['errors']));
                if ($updated['errors']) {
                    $intent = 'clarification';
                    $fieldDetails = array();
                    $proposedFields = array();
                    $message = __('I could not safely update that field configuration. Review the notes and clarify the request.');
                } else {
                    $fieldDetails = $updated['fields'];
                    $proposedFields = array($updated['updated_field']);
                    $intent = 'form_preview';
                    $changeReady = true;
                }
            } elseif ($operation === 'remove_field_options') {
                $updated = $this->_removeFieldOptions($existingFields, $targetField, $optionsToRemove);
                $warnings = array_values(array_merge($warnings, $updated['errors']));
                if ($updated['errors']) {
                    $intent = 'clarification';
                    $fieldDetails = array();
                    $proposedFields = array();
                    $message = __('I could not safely remove those choices. Review the notes and clarify the request.');
                } else {
                    $fieldDetails = $updated['fields'];
                    $proposedFields = array($updated['updated_field']);
                    $intent = 'form_preview';
                    $changeReady = true;
                }
            } elseif ($operation === 'reorder_field') {
                $updated = $this->_reorderField($existingFields, $targetField, $insertAfter);
                $warnings = array_values(array_merge($warnings, $updated['errors']));
                if ($updated['errors']) {
                    $intent = 'clarification';
                    $fieldDetails = array();
                    $proposedFields = array();
                    $message = __('I could not safely move that field. Review the notes and clarify the request.');
                } else {
                    $fieldDetails = $updated['fields'];
                    $proposedFields = array($updated['updated_field']);
                    $intent = 'form_preview';
                    $changeReady = true;
                }
            } elseif ($operation === 'remove_fields') {
                $removed = $this->_removeExistingFields($existingFields, $fieldsToRemove);
                $warnings = array_values(array_merge($warnings, $removed['errors']));
                if ($removed['errors']) {
                    $intent = 'clarification';
                    $fieldDetails = array();
                    $proposedFields = array();
                    $message = __('I could not safely remove that field. Review the notes and clarify the request.');
                } else {
                    $fieldDetails = $removed['fields'];
                    $fieldsToRemove = $removed['removed_field_names'];
                    $proposedFields = array();
                    $intent = 'form_preview';
                    $changeReady = true;
                }
            } else {
                $fieldDetails = array();
                $proposedFields = array();
                if ($intent === 'form_preview') {
                    $intent = 'clarification';
                    $message = __('Specify a supported change to the existing form.');
                }
            }

            if ($changeReady && $canRebuild) {
                $applyToken = Security::hash(uniqid((string)mt_rand(), true).$userId, 'sha256', true);
                $pendingChange = array(
                'ai_id' => $this->_historyId,
                'user_id' => $userId,
                'company_id' => $companyId,
                'custom_table_id' => $customTableId,
                'source_controller' => $sourceController,
                'base_fields_hash' => hash('sha256', json_encode(array_values($existingFields))),
                'fields' => $fieldDetails,
                'expires' => time() + 600
                );
                if ($this->_writeAiPending($applyToken, $pendingChange)) {
                    $message = __('The requested field change was prepared. FlinkISO is rebuilding the form through the API.');
                } else {
                    $applyToken = '';
                    $warnings[] = __('The server could not store the approved AI change for rebuilding.');
                    $message = __('The requested field change is ready, but the rebuild could not be queued.');
                }
            } elseif ($changeReady) {
                $warnings[] = __('Only an MR user can rebuild a generated form.');
                $message = __('The requested field change is ready for review, but your account cannot rebuild this form.');
            }
        } elseif (in_array($operation, array('add_fields', 'update_field_options', 'remove_field_options', 'update_field_label', 'update_field_properties', 'reorder_field', 'remove_fields'), true)) {
            $intent = 'clarification';
            $fieldDetails = array();
            $message = __('Open the generated form that should receive these fields, then submit the request again.');
        } elseif ($operation === 'create_form' && !empty($compiledChildren['errors'])) {
            $intent = 'clarification';
            $message = __('The parent fields were recognized, but one or more detected child tables were invalid. No form was created.');
        } elseif ($operation === 'create_form' && $intent === 'form_preview' && !empty($compiled['fields'])) {
            if ($canRebuild) {
                $applyToken = Security::hash(uniqid((string)mt_rand(), true).$userId, 'sha256', true);
                $pendingCreate = array(
                'operation' => 'create_form',
                'ai_id' => $this->_historyId,
                'user_id' => $userId,
                'company_id' => $companyId,
                'qc_document_id' => $qcDocumentId,
                'form_name' => $qualityDocumentTitle !== '' ? trim($qualityDocumentTitle) : trim($modelResponse['form_name']),
                'fields' => array_values($compiled['fields']),
                'child_tables' => !empty($compiledChildren['tables']) ? array_values($compiledChildren['tables']) : array(),
                'expires' => time() + 900
                );
                if ($this->_writeAiPending($applyToken, $pendingCreate)) {
                    $message = !empty($pendingCreate['child_tables'])
                    ? __('FlinkISO detected repeatable information that should use child tables. Review the structure and confirm creation.')
                    : __('The form fields were prepared. FlinkISO is now creating the form through the API.');
                } else {
                    $applyToken = '';
                    $warnings[] = __('The server could not queue the generated form for creation.');
                    $message = __('The form fields are ready, but form creation could not be queued.');
                }
            } else {
                $warnings[] = __('Only an MR user can create a generated form.');
                $message = __('The form fields are ready for review, but your account cannot create this form.');
            }
        } elseif ($intent === 'form_preview' && !empty($compiled['fields'])) {
            $message = __('I prepared a read-only FlinkISO field preview for your review.');
        } elseif ($intent === 'form_preview') {
            $intent = 'clarification';
            $message = __('I could not prepare a valid field preview. Review the notes and clarify the request.');
        }

        $formName = isset($modelResponse['form_name']) ? trim($modelResponse['form_name']) : '';
        if (!$isGeneratedForm && $qualityDocumentTitle !== '') {
            $formName = trim($qualityDocumentTitle);
        }

        return $this->_jsonResponse(true, array(
        'message' => $message,
        'intent' => $intent,
        'operation' => $operation,
        'form_name' => $formName,
        'field_details' => $fieldDetails,
        'proposed_fields' => $proposedFields,
        'insert_after' => $insertAfter,
        'target_field' => $targetField,
        'new_field_label' => $newFieldLabel,
        'options_to_add' => $optionsToAdd,
        'options_to_remove' => $optionsToRemove,
        'field_changes' => $fieldChanges,
        'fields_to_remove' => $fieldsToRemove,
        'child_tables' => !empty($compiledChildren['tables']) ? array_values($compiledChildren['tables']) : array(),
        'warnings' => $warnings,
        'model' => $model,
        'duration_ms' => $durationMs,
        'read_only' => $applyToken === '',
        'auto_apply' => $applyToken !== '' && empty($compiledChildren['tables']),
        'requires_child_confirmation' => $applyToken !== '' && !empty($compiledChildren['tables']),
        'apply_token' => $applyToken,
        'apply_url' => $applyToken !== '' ? Router::url(array('controller' => 'custom_tables', 'action' => 'ai_rebuild'), true) : ''
        ));
    }

    private function _quickConversationReply($prompt, $isGeneratedForm) {
        $plain = strtolower(trim(preg_replace('/[!?.,]+$/', '', (string)$prompt)));
        if (preg_match('/^(hi|hello|hey|hi there|hello there|good morning|good afternoon|good evening)$/', $plain)) {
            return $isGeneratedForm
            ? __('Hello! I can help you add, remove, rename, reorder, link, or configure fields on this form. What would you like to change?')
            : __('Hello! I can read the current Quality Document and create a FlinkISO form from it. What would you like me to do?');
        }
        if (preg_match('/^(thanks|thank you|thank you very much|ok thanks|okay thanks)$/', $plain)) {
            return __('You are welcome.');
        }
        return null;
    }

    private function _handleGuidedFieldAction($action, $targetField, $value, $options, $existingFields, $customTable, $canRebuild, $userId, $companyId, $customTableId, $sourceController) {
        if ($action === 'add_field') {
            return $this->_handleGuidedFieldAdd($value, $existingFields, $customTable, $canRebuild, $userId, $companyId, $customTableId, $sourceController);
        }
        if ($targetField === '' || $this->_fieldIndex($existingFields, $targetField) === null) {
            return $this->_jsonResponse(false, array('message' => __('The selected field is no longer present in this form. Reload the page and select it again.')), 409);
        }

        $operation = 'update_field_properties';
        $fieldChanges = array();
        $fieldsToRemove = array();
        $optionsToAdd = array();
        $optionsToRemove = array();
        if ($action === 'mandatory') $fieldChanges['mandatory'] = $value === '1';
        elseif ($action === 'default') $fieldChanges['default_field'] = true;
        elseif ($action === 'index') $fieldChanges['index_show'] = $value === '1';
        elseif ($action === 'link') $fieldChanges = array('data_type' => 'dropdown-s', 'linked_to' => $value);
        elseif ($action === 'type') {
            $fieldChanges['data_type'] = $value;
            if (in_array($value, array('radio', 'checkbox'), true)) {
                $fieldChanges['options'] = is_array($options) ? $options : preg_split('/\s*,\s*/', trim((string)$options), -1, PREG_SPLIT_NO_EMPTY);
            }
        } elseif ($action === 'checkbox_layout') {
            $fieldChanges['checkbox_layout'] = $value;
        } elseif ($action === 'add_option') {
            $operation = 'update_field_options';
            $optionsToAdd = is_array($options) ? $options : preg_split('/\s*,\s*/', trim((string)$options), -1, PREG_SPLIT_NO_EMPTY);
        } elseif ($action === 'remove_option') {
            $operation = 'remove_field_options';
            $optionsToRemove = is_array($options) ? $options : preg_split('/\s*,\s*/', trim((string)$options), -1, PREG_SPLIT_NO_EMPTY);
        } elseif ($action === 'rename') {
            $operation = 'update_field_label';
        } elseif ($action === 'tab') {
            $operation = 'update_field_tab';
        } elseif ($action === 'delete') {
            $operation = 'remove_fields';
            $fieldsToRemove = array($targetField);
        } elseif ($action === 'reorder') {
            $operation = 'reorder_field';
        } else {
            return $this->_jsonResponse(false, array('message' => __('That guided field action is not supported.')), 422);
        }

        if ($operation === 'update_field_properties') $updated = $this->_updateFieldProperties($existingFields, $targetField, $fieldChanges);
        elseif ($operation === 'update_field_options') $updated = $this->_addFieldOptions($existingFields, $targetField, $optionsToAdd);
        elseif ($operation === 'remove_field_options') $updated = $this->_removeFieldOptions($existingFields, $targetField, $optionsToRemove);
        elseif ($operation === 'update_field_label') $updated = $this->_updateFieldLabel($existingFields, $targetField, $value);
        elseif ($operation === 'update_field_tab') $updated = $this->_updateFieldTab($existingFields, $targetField, $value);
        elseif ($operation === 'remove_fields') {
            $removed = $this->_removeExistingFields($existingFields, $fieldsToRemove);
            $updated = array('fields' => $removed['fields'], 'updated_field' => array(), 'errors' => $removed['errors']);
        } else {
            $updated = $this->_reorderField($existingFields, $targetField, $value);
        }

        if (!empty($updated['errors'])) {
            return $this->_jsonResponse(true, array(
            'message' => __('I could not safely apply that field action. Review the notes and clarify the request.'),
            'intent' => 'clarification', 'operation' => $operation, 'form_name' => $customTable['CustomTable']['name'],
            'field_details' => array(), 'proposed_fields' => array(), 'insert_after' => $operation === 'reorder_field' ? $value : '',
            'target_field' => $targetField, 'new_field_label' => $operation === 'update_field_label' ? $value : '',
            'options_to_add' => $optionsToAdd, 'options_to_remove' => $optionsToRemove, 'field_changes' => $fieldChanges,
            'fields_to_remove' => $fieldsToRemove, 'warnings' => $updated['errors'], 'model' => '', 'duration_ms' => 0,
            'read_only' => true, 'auto_apply' => false, 'apply_token' => '', 'apply_url' => ''
            ));
        }

        $applyToken = '';
        $warnings = array();
        $message = __('The selected field change was prepared.');
        if ($canRebuild) {
            $applyToken = Security::hash(uniqid((string)mt_rand(), true).$userId, 'sha256', true);
            $pending = array(
            'ai_id' => $this->_historyId, 'user_id' => $userId, 'company_id' => $companyId,
            'custom_table_id' => $customTableId, 'source_controller' => $sourceController,
            'base_fields_hash' => hash('sha256', json_encode(array_values($existingFields))),
            'fields' => array_values($updated['fields']), 'expires' => time() + 600
            );
            if ($this->_writeAiPending($applyToken, $pending)) {
                $message = __('The selected field change was prepared. FlinkISO is rebuilding the form through the API.');
            } else {
                $applyToken = '';
                $warnings[] = __('The server could not queue the selected field change.');
            }
        } else {
            $warnings[] = __('Only an MR user can rebuild a generated form.');
            $message = __('The selected field change is ready for review, but your account cannot rebuild this form.');
        }

        return $this->_jsonResponse(true, array(
        'message' => $message, 'intent' => 'form_preview', 'operation' => $operation,
        'form_name' => $customTable['CustomTable']['name'], 'field_details' => array_values($updated['fields']),
        'proposed_fields' => !empty($updated['updated_field']) ? array($updated['updated_field']) : array(),
        'insert_after' => $operation === 'reorder_field' ? $value : '', 'target_field' => $targetField,
        'new_field_label' => $operation === 'update_field_label' ? $value : '',
        'options_to_add' => $optionsToAdd, 'options_to_remove' => $optionsToRemove, 'field_changes' => $fieldChanges,
        'fields_to_remove' => $fieldsToRemove, 'warnings' => $warnings, 'model' => '', 'duration_ms' => 0,
        'handled_locally' => true,
        'read_only' => $applyToken === '', 'auto_apply' => $applyToken !== '', 'apply_token' => $applyToken,
        'apply_url' => $applyToken !== '' ? Router::url(array('controller' => 'custom_tables', 'action' => 'ai_rebuild'), true) : ''
        ));
    }

    private function _handleGuidedFieldAdd($value, $existingFields, $customTable, $canRebuild, $userId, $companyId, $customTableId, $sourceController) {
        $wizard = is_array($value) ? $value : json_decode((string)$value, true);
        if (!is_array($wizard) || empty($wizard['field']) || !is_array($wizard['field'])) {
            return $this->_jsonResponse(false, array('message' => __('The field wizard result was invalid. Please reopen the wizard and try again.')), 422);
        }

        $compiled = $this->_compileWizardFieldViaApi($companyId, $wizard['field']);
        if (empty($compiled['success']) || empty($compiled['fields'])) {
            return $this->_jsonResponse(true, array(
            'message' => __('The API could not compile the selected field settings.'),
            'intent' => 'clarification', 'operation' => 'add_fields', 'form_name' => $customTable['CustomTable']['name'],
            'field_details' => array(), 'proposed_fields' => array(), 'insert_after' => '', 'target_field' => '',
            'new_field_label' => '', 'options_to_add' => array(), 'options_to_remove' => array(), 'field_changes' => array(),
            'fields_to_remove' => array(), 'warnings' => !empty($compiled['errors']) ? $compiled['errors'] : array(__('The API returned no compiled field.')),
            'model' => '', 'duration_ms' => 0, 'handled_locally' => true, 'read_only' => true, 'auto_apply' => false,
            'apply_token' => '', 'apply_url' => ''
            ));
        }

        $insertAfter = !empty($wizard['insert_after']) ? trim((string)$wizard['insert_after']) : '';
        $merged = $this->_mergeExistingFields($existingFields, $compiled['fields'], $insertAfter, $insertAfter !== '');
        if (!empty($merged['errors'])) {
            return $this->_jsonResponse(true, array(
            'message' => __('The field was valid, but it could not be placed in the current form.'),
            'intent' => 'clarification', 'operation' => 'add_fields', 'form_name' => $customTable['CustomTable']['name'],
            'field_details' => array(), 'proposed_fields' => $compiled['fields'], 'insert_after' => $insertAfter,
            'target_field' => '', 'new_field_label' => '', 'options_to_add' => array(), 'options_to_remove' => array(),
            'field_changes' => array(), 'fields_to_remove' => array(), 'warnings' => $merged['errors'], 'model' => '',
            'duration_ms' => 0, 'handled_locally' => true, 'read_only' => true, 'auto_apply' => false,
            'apply_token' => '', 'apply_url' => ''
            ));
        }

        $applyToken = '';
        $warnings = !empty($merged['warnings']) ? $merged['warnings'] : array();
        $message = __('The field was compiled by API V2 and is ready for review.');
        if ($canRebuild) {
            $applyToken = Security::hash(uniqid((string)mt_rand(), true).$userId, 'sha256', true);
            $pending = array(
            'ai_id' => $this->_historyId, 'user_id' => $userId, 'company_id' => $companyId,
            'custom_table_id' => $customTableId, 'source_controller' => $sourceController,
            'base_fields_hash' => hash('sha256', json_encode(array_values($existingFields))),
            'fields' => array_values($merged['fields']), 'expires' => time() + 600
            );
            if ($this->_writeAiPending($applyToken, $pending)) {
                $message = __('API V2 compiled the field. FlinkISO is rebuilding the form.');
            } else {
                $applyToken = '';
                $warnings[] = __('The server could not queue the compiled field for rebuilding.');
            }
        } else {
            $warnings[] = __('Only an MR user can rebuild a generated form.');
        }

        return $this->_jsonResponse(true, array(
        'message' => $message, 'intent' => 'form_preview', 'operation' => 'add_fields',
        'form_name' => $customTable['CustomTable']['name'], 'field_details' => array_values($merged['fields']),
        'proposed_fields' => array_values($compiled['fields']), 'insert_after' => !empty($merged['insert_after']) ? $merged['insert_after'] : '',
        'target_field' => '', 'new_field_label' => '', 'options_to_add' => array(), 'options_to_remove' => array(),
        'field_changes' => array(), 'fields_to_remove' => array(), 'warnings' => $warnings, 'model' => 'API v2 compiler',
        'duration_ms' => 0, 'handled_locally' => true, 'read_only' => $applyToken === '', 'auto_apply' => $applyToken !== '',
        'apply_token' => $applyToken,
        'apply_url' => $applyToken !== '' ? Router::url(array('controller' => 'custom_tables', 'action' => 'ai_rebuild'), true) : ''
        ));
    }

    private function _compileWizardFieldViaApi($companyId, $field) {
        $base = rtrim((string)Configure::read('ApiPath'), '/');
        if ($base === '' || !$companyId) return array('success' => false, 'fields' => array(), 'errors' => array(__('API V2 is not configured.')));
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => $base.'/ai_services/compile_field/'.$companyId.'/api:true/company_id:'.$companyId,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode(array('field' => $field)),
        CURLOPT_HTTPHEADER => array('Content-Type: application/json')
        ));
        $body = curl_exec($curl);
        $httpStatus = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curlError = curl_error($curl);
        curl_close($curl);
        if ($curlError !== '' || $httpStatus < 200 || $httpStatus >= 300) {
            return array('success' => false, 'fields' => array(), 'errors' => array($curlError !== '' ? $curlError : __('API V2 rejected the field settings.')));
        }
        $decoded = json_decode($body, true);
        return is_array($decoded) ? $decoded : array('success' => false, 'fields' => array(), 'errors' => array(__('API V2 returned an invalid field response.')));
    }

    private function _updateFieldTab($existingFields, $targetField, $tabName) {
        $targetIndex = $this->_fieldIndex($existingFields, $targetField);
        $tabName = trim((string)$tabName);
        if ($targetIndex === null) {
            return array('fields' => array(), 'updated_field' => array(), 'errors' => array('The selected field was not found.'));
        }
        if ($tabName === '' || strlen($tabName) > 80 || strip_tags($tabName) !== $tabName || !preg_match('/^[\pL\pN][\pL\pN _&\/().-]*$/u', $tabName)) {
            return array('fields' => array(), 'updated_field' => array(), 'errors' => array('Enter a valid tab name of 80 characters or fewer.'));
        }
        $currentTab = isset($existingFields[$targetIndex]['tab_name']) ? trim($existingFields[$targetIndex]['tab_name']) : '';
        if (strcasecmp($currentTab, $tabName) === 0) {
            return array('fields' => array(), 'updated_field' => array(), 'errors' => array('That field is already in the selected tab.'));
        }

        $tabGroup = '';
        $tabSequence = '';
        $maxGroup = 0;
        $sequencesByGroup = array();
        foreach ($existingFields as $field) {
            $name = isset($field['tab_name']) ? trim($field['tab_name']) : '';
            $group = isset($field['tab_group']) && $field['tab_group'] !== '' ? (int)$field['tab_group'] : 1;
            $sequence = isset($field['tab_sequence']) && $field['tab_sequence'] !== '' ? (int)$field['tab_sequence'] : 0;
            if ($name !== '' && $name !== '-1') {
                $maxGroup = max($maxGroup, $group);
                if (!isset($sequencesByGroup[$group])) $sequencesByGroup[$group] = 0;
                $sequencesByGroup[$group] = max($sequencesByGroup[$group], $sequence);
                if (strcasecmp($name, $tabName) === 0) {
                    $tabName = $name;
                    $tabGroup = (string)$group;
                    $tabSequence = $sequence ? (string)$sequence : '';
                }
            }
        }
        if ($tabGroup === '') {
            $tabGroupNumber = $maxGroup > 0 ? $maxGroup : 1;
            $nextSequence = isset($sequencesByGroup[$tabGroupNumber]) ? $sequencesByGroup[$tabGroupNumber] + 1 : 1;
            // Existing FlinkISO forms place up to three ordered tabs in one
            // group before starting another tab row.
            if ($nextSequence > 3) {
                $tabGroupNumber++;
                $nextSequence = 1;
            }
            $tabGroup = (string)$tabGroupNumber;
            $tabSequence = (string)$nextSequence;
        }

        $updatedFields = array_values($existingFields);
        $updatedFields[$targetIndex]['tab_name'] = $tabName;
        $updatedFields[$targetIndex]['tab_group'] = $tabGroup;
        $updatedFields[$targetIndex]['tab_sequence'] = $tabSequence;
        return array('fields' => $updatedFields, 'updated_field' => $updatedFields[$targetIndex], 'errors' => array());
    }

    private function _expandModelFields($fields) {
        if (!is_array($fields)) return array();
        $expanded = array();
        foreach ($fields as $field) {
            if (!is_array($field)) {
                $expanded[] = $field;
                continue;
            }
            // Also accept the former verbose shape during deployment so a
            // response from an already-running request can still be compiled.
            if (isset($field['field_name']) || isset($field['data_type'])) {
                $expanded[] = $field;
                continue;
            }
            $item = array(
            'field_name' => isset($field['n']) ? $field['n'] : '',
            'field_label' => isset($field['l']) ? $field['l'] : '',
            'data_type' => isset($field['t']) ? $field['t'] : '',
            'mandatory' => !empty($field['r']),
            'index_show' => !empty($field['i']),
            'linked_to' => isset($field['x']) ? $field['x'] : '-1',
            'options' => isset($field['o']) && is_array($field['o']) ? $field['o'] : array()
            );
            if (isset($field['w'])) $item['size'] = $field['w'];
            $expanded[] = $item;
        }
        return $expanded;
    }

    private function _formContext($customTable, $fields) {
        $contextFields = array();
        foreach ($fields as $field) {
            if (empty($field['field_name'])) continue;
            $label = isset($field['field_label']) ? $this->_decodeFieldLabel($field['field_label']) : '';
            $options = array();
            if (!empty($field['csvoptions']) && in_array(isset($field['data_type']) ? $field['data_type'] : '', array('radio', 'checkbox'), true)) {
                $options = array_values(array_filter(array_map('trim', explode(',', $field['csvoptions'])), 'strlen'));
            }
            $contextFields[] = array(
            'field_name' => $field['field_name'],
            'field_label' => $label !== '' ? $label : Inflector::humanize($field['field_name']),
            'data_type' => isset($field['data_type']) ? $field['data_type'] : '',
            'display_type' => isset($field['display_type']) ? $field['display_type'] : '',
            'linked_to' => isset($field['linked_to']) ? $field['linked_to'] : '-1',
            'size' => isset($field['size']) ? $field['size'] : '',
            'mandatory' => !empty($field['mandatory']) || !empty($field['mandetory']),
            'add_disabled' => !empty($field['add_disabled']),
            'edit_disabled' => !empty($field['edit_disabled']),
            'drop' => !empty($field['drop']),
            'child_tables' => !empty($field['child_tables']) ? $field['child_tables'] : array(),
            'options' => $options
            );
        }
        return array(
        'form_name' => $customTable['CustomTable']['name'],
        'table_name' => $customTable['CustomTable']['table_name'],
        'fields' => $contextFields
        );
    }

    private function _explainFieldVisibility($prompt, $existingFields) {
        if (!preg_match('/\b(?:do\s+not|don[\'’]t|cannot|can[\'’]t)\s+see\b|\bnot\s+visible\b|\bfield\s+is\s+missing\b/i', $prompt)) return null;
        $promptLower = strtolower($prompt);
        $matched = null;
        $matchedLength = 0;
        foreach ((array)$existingFields as $field) {
            if (empty($field['field_name'])) continue;
            $label = !empty($field['field_label']) ? $this->_decodeFieldLabel($field['field_label']) : Inflector::humanize($field['field_name']);
            foreach (array($field['field_name'], Inflector::humanize($field['field_name']), $label) as $candidate) {
                $candidate = strtolower(trim($candidate));
                if ($candidate !== '' && strlen($candidate) > $matchedLength && strpos($promptLower, $candidate) !== false) {
                    $matched = $field;
                    $matchedLength = strlen($candidate);
                }
            }
        }
        if (!$matched) return null;

        $label = !empty($matched['field_label']) ? $this->_decodeFieldLabel($matched['field_label']) : Inflector::humanize($matched['field_name']);
        $dataType = isset($matched['data_type']) ? $matched['data_type'] : '';
        if (!empty($matched['drop'])) return __('%s is marked for removal, so it is not rendered.', $label);
        if ($dataType === 'linked_documents') {
            $children = !empty($matched['child_tables'])
            ? (is_array($matched['child_tables']) ? $matched['child_tables'] : json_decode($matched['child_tables'], true))
            : array();
            if (!is_array($children) || !$children) {
                return __('%s is configured as Linked Documents with no child forms selected, so FlinkISO renders an empty placement block instead of an input. Change it to Text, or link it to the intended model, to make it visible.', $label);
            }
            return __('%s is a Linked Documents placement rather than an input field. Its selected child forms are displayed only when applicable.', $label);
        }
        if ((string)(isset($matched['display_type']) ? $matched['display_type'] : '') === '5' || $dataType === 'break') {
            return __('%s is configured as a section break, not an input field.', $label);
        }
        return __('%s is configured as a %s field and should be rendered. Rebuild the form if its generated view does not contain the input.', $label, $dataType !== '' ? $dataType : 'standard');
    }

    private function _qcDocumentPath($qcDocument) {
        $document = $qcDocument['QcDocument'];
        $companyId = $this->Session->read('User.company_id');
        $folder = WWW_ROOT.'files'.DS.$companyId.DS.'qc_documents'.DS.$document['id'];
        if (!is_dir($folder)) return '';

        $fileType = strtolower(trim((string)$document['file_type']));
        $baseName = $document['document_number'].'-'.$document['title'].'-'.$document['revision_number'];
        $expected = $folder.DS.$this->_clean_table_names($baseName).'.'.$fileType;
        if (is_file($expected)) return $expected;

        $candidates = glob($folder.DS.'*.'.$fileType);
        if (!$candidates) return '';
        usort($candidates, function ($left, $right) {
            return filemtime($right) - filemtime($left);
        });
        return is_file($candidates[0]) ? $candidates[0] : '';
    }

    private function _extractDocumentText($path) {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if ($extension === 'pdf') return $this->_extractPdfText($path);
        if ($extension === 'docx') return $this->_extractDocxText($path);
        if ($extension === 'xlsx') return $this->_extractXlsxText($path);
        if (in_array($extension, array('txt', 'csv'), true)) {
            $text = file_get_contents($path);
            return $this->_cleanExtractedText($text);
        }
        return array('success' => false, 'text' => '', 'message' => __('FlinkISO AI cannot read this document type yet. Use PDF, DOCX, XLSX, TXT, or CSV. Convert legacy XLS files to XLSX first.'));
    }

    private function _extractPdfText($path) {
        $configured = trim((string)Configure::read('PdfToTextPath'));
        $candidates = array_filter(array(
        $configured,
        '/usr/local/bin/pdftotext',
        '/opt/homebrew/bin/pdftotext',
        '/Users/tgs/.cache/codex-runtimes/codex-primary-runtime/dependencies/native/poppler/poppler/bin/pdftotext'
        ));
        $binary = '';
        foreach ($candidates as $candidate) {
            if (is_executable($candidate)) {
                $binary = $candidate;
                break;
            }
        }
        if ($binary === '' || !function_exists('exec')) {
            return array('success' => false, 'text' => '', 'message' => __('PDF text extraction is not installed on the AIS server. Install Poppler/pdftotext and configure PdfToTextPath.'));
        }

        $output = array();
        $status = 1;
        $command = escapeshellarg($binary).' -layout -enc UTF-8 '.escapeshellarg($path).' - 2>&1';
        exec($command, $output, $status);
        if ($status !== 0) {
            CakeLog::write('error', 'FlinkISO AI PDF extraction failed for '.basename($path).': '.implode(' ', $output));
            return array('success' => false, 'text' => '', 'message' => __('AIS could not extract text from the current PDF.'));
        }
        return $this->_cleanExtractedText(implode("\n", $output));
    }

    private function _extractDocxText($path) {
        if (!class_exists('ZipArchive')) {
            return array('success' => false, 'text' => '', 'message' => __('DOCX extraction requires the PHP Zip extension on the AIS server.'));
        }
        $zip = new ZipArchive();
        if ($zip->open($path) !== true) {
            return array('success' => false, 'text' => '', 'message' => __('AIS could not open the current DOCX file.'));
        }
        $xml = $zip->getFromName('word/document.xml');
        $zip->close();
        if ($xml === false) {
            return array('success' => false, 'text' => '', 'message' => __('The current DOCX has no readable document content.'));
        }
        $xml = str_replace(array('</w:p>', '</w:tr>', '</w:tc>', '<w:tab/>'), array("\n", "\n", "\t", "\t"), $xml);
return $this->_cleanExtractedText(html_entity_decode(strip_tags($xml), ENT_QUOTES, 'UTF-8'));
}

private function _extractXlsxText($path) {
    if (!class_exists('ZipArchive') || !class_exists('DOMDocument')) {
        return array('success' => false, 'text' => '', 'message' => __('XLSX extraction requires the PHP Zip and DOM extensions on the FlinkISO server.'));
    }
    $zip = new ZipArchive();
    if ($zip->open($path) !== true) {
        return array('success' => false, 'text' => '', 'message' => __('FlinkISO AI could not open the current XLSX file.'));
    }

    $sharedStrings = array();
    $sharedXml = $zip->getFromName('xl/sharedStrings.xml');
    if ($sharedXml !== false) {
        $dom = new DOMDocument();
        if (@$dom->loadXML($sharedXml, LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING)) {
            $xpath = new DOMXPath($dom);
            foreach ($xpath->query('//*[local-name()="si"]') as $item) {
                $parts = array();
                foreach ($xpath->query('.//*[local-name()="t"]', $item) as $textNode) $parts[] = $textNode->textContent;
                $sharedStrings[] = implode('', $parts);
            }
        }
    }

    $sheetNames = array();
    $workbookXml = $zip->getFromName('xl/workbook.xml');
    if ($workbookXml !== false) {
        $dom = new DOMDocument();
        if (@$dom->loadXML($workbookXml, LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING)) {
            $xpath = new DOMXPath($dom);
            foreach ($xpath->query('//*[local-name()="sheet"]') as $sheet) $sheetNames[] = $sheet->getAttribute('name');
        }
    }

    $sheetFiles = array();
    for ($index = 0; $index < $zip->numFiles; $index++) {
        $name = $zip->getNameIndex($index);
        if (preg_match('#^xl/worksheets/sheet([0-9]+)\.xml$#', $name, $match)) $sheetFiles[(int)$match[1]] = $name;
    }
    ksort($sheetFiles, SORT_NUMERIC);
    $output = array();
    $sheetIndex = 0;
    foreach ($sheetFiles as $sheetFile) {
        $xml = $zip->getFromName($sheetFile);
        if ($xml === false) continue;
        $dom = new DOMDocument();
        if (!@$dom->loadXML($xml, LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING)) continue;
        $xpath = new DOMXPath($dom);
        $title = isset($sheetNames[$sheetIndex]) ? $sheetNames[$sheetIndex] : 'Sheet '.($sheetIndex + 1);
        $output[] = 'WORKSHEET: '.$title;
        foreach ($xpath->query('//*[local-name()="sheetData"]/*[local-name()="row"]') as $row) {
            $values = array();
            foreach ($xpath->query('./*[local-name()="c"]', $row) as $cell) {
                $type = $cell->getAttribute('t');
                $valueNode = $xpath->query('./*[local-name()="v"]', $cell)->item(0);
                $value = $valueNode ? $valueNode->textContent : '';
                if ($type === 's' && isset($sharedStrings[(int)$value])) $value = $sharedStrings[(int)$value];
                elseif ($type === 'inlineStr') {
                    $parts = array();
                    foreach ($xpath->query('.//*[local-name()="t"]', $cell) as $textNode) $parts[] = $textNode->textContent;
                    $value = implode('', $parts);
                } elseif ($type === 'b') $value = $value === '1' ? 'TRUE' : 'FALSE';
                $values[] = trim($value);
            }
            if (array_filter($values, 'strlen')) $output[] = implode("\t", $values);
            if (strlen(implode("\n", $output)) > 45000) break 2;
        }
        $sheetIndex++;
    }
    $zip->close();
    return $this->_cleanExtractedText(implode("\n", $output));
}

private function _cleanExtractedText($text) {
    $text = str_replace("\0", '', (string)$text);
    $text = preg_replace('/[ \t]+\n/', "\n", $text);
    $text = preg_replace('/\n{4,}/', "\n\n\n", $text);
    $text = trim($text);
    if ($text === '') {
        return array('success' => false, 'text' => '', 'message' => __('The current document contains no extractable text. A scanned PDF requires OCR.'));
    }
    if (strlen($text) > 40000) $text = substr($text, 0, 40000);
    return array('success' => true, 'text' => $text, 'message' => '');
}

private function _decodeFieldLabel($value) {
    $value = trim((string)$value);
    if ($value === '') return '';
    $decoded = base64_decode($value, true);
    if ($decoded !== false && rtrim(base64_encode($decoded), '=') === rtrim($value, '=')) return $decoded;
    return $value;
}

private function _mergeExistingFields($existingFields, $newFields, $insertAfter, $requireAnchor = false) {
    $errors = array();
    $warnings = array();
    $appendedForMissingAnchor = false;
    $anchorIndex = null;
    $existingNames = array();
    foreach ($existingFields as $index => $field) {
        if (empty($field['field_name'])) continue;
        $existingNames[$field['field_name']] = true;
        if ($field['field_name'] === $insertAfter) $anchorIndex = $index;
    }
    if ($insertAfter === '') {
        $anchorIndex = count($existingFields) - 1;
    } elseif ($anchorIndex === null) {
        if ($requireAnchor) {
            $errors[] = 'The requested insert_after field was not found in the current form.';
        } else {
            $anchorIndex = count($existingFields) - 1;
            $appendedForMissingAnchor = true;
        }
    }
    if (!$newFields) $errors[] = 'No new field was proposed.';
    foreach ($newFields as $field) {
        if (!empty($field['field_name']) && isset($existingNames[$field['field_name']])) {
            $errors[] = 'Field '.$field['field_name'].' already exists and was not added again.';
        }
    }
    if ($errors) return array('fields' => array(), 'errors' => $errors, 'warnings' => $warnings, 'insert_after' => $insertAfter);
    if ($appendedForMissingAnchor) {
        $warnings[] = 'The suggested position was not found, so the new field was added at the end of the form.';
    }

    $merged = $existingFields;
    array_splice($merged, $anchorIndex + 1, 0, $newFields);
    $effectiveInsertAfter = $anchorIndex >= 0 && !empty($existingFields[$anchorIndex]['field_name'])
    ? $existingFields[$anchorIndex]['field_name']
    : '';
    return array('fields' => array_values($merged), 'errors' => array(), 'warnings' => $warnings, 'insert_after' => $effectiveInsertAfter);
}

private function _addFieldOptions($existingFields, $targetField, $optionsToAdd) {
    $errors = array();
    $targetIndex = null;
    foreach ($existingFields as $index => $field) {
        if (!empty($field['field_name']) && $field['field_name'] === $targetField) {
            $targetIndex = $index;
            break;
        }
    }
    if ($targetField === '' || $targetIndex === null) {
        return array('fields' => array(), 'updated_field' => array(), 'errors' => array('The requested target_field was not found in the current form.'));
    }

    $dataType = isset($existingFields[$targetIndex]['data_type']) ? $existingFields[$targetIndex]['data_type'] : '';
    if (!in_array($dataType, array('radio', 'checkbox'), true)) {
        return array('fields' => array(), 'updated_field' => array(), 'errors' => array('Only radio and checkbox fields currently support editable choices.'));
    }

    $currentOptions = !empty($existingFields[$targetIndex]['csvoptions'])
    ? array_values(array_filter(array_map('trim', explode(',', $existingFields[$targetIndex]['csvoptions'])), 'strlen'))
    : array();
    $known = array();
    foreach ($currentOptions as $option) $known[strtolower($option)] = true;
    $added = 0;
    foreach ((array)$optionsToAdd as $option) {
        if (!is_scalar($option)) continue;
        $option = trim((string)$option);
        if ($option === '') continue;
        if ($option === strtolower($option)) $option = ucwords($option);
        $key = strtolower($option);
        if (isset($known[$key])) continue;
        $currentOptions[] = $option;
        $known[$key] = true;
        $added++;
    }
    if ($added === 0) $errors[] = 'No new option was proposed, or every requested option already exists.';
    if ($errors) return array('fields' => array(), 'updated_field' => array(), 'errors' => $errors);

    $updatedFields = $existingFields;
    $updatedFields[$targetIndex]['csvoptions'] = implode(',', $currentOptions);
    return array(
    'fields' => array_values($updatedFields),
    'updated_field' => $updatedFields[$targetIndex],
    'errors' => array()
    );
}

private function _removeFieldOptions($existingFields, $targetField, $optionsToRemove) {
    $targetIndex = $this->_fieldIndex($existingFields, $targetField);
    if ($targetField === '' || $targetIndex === null) {
        return array('fields' => array(), 'updated_field' => array(), 'errors' => array('The requested target_field was not found in the current form.'));
    }
    $dataType = isset($existingFields[$targetIndex]['data_type']) ? $existingFields[$targetIndex]['data_type'] : '';
    if (!in_array($dataType, array('radio', 'checkbox'), true)) {
        return array('fields' => array(), 'updated_field' => array(), 'errors' => array('Only radio and checkbox fields support editable choices.'));
    }

    $requested = array();
    foreach ((array)$optionsToRemove as $option) {
        if (is_scalar($option) && trim((string)$option) !== '') $requested[strtolower(trim((string)$option))] = true;
    }
    if (!$requested) {
        return array('fields' => array(), 'updated_field' => array(), 'errors' => array('No option was selected for removal.'));
    }

    $current = !empty($existingFields[$targetIndex]['csvoptions'])
    ? array_values(array_filter(array_map('trim', explode(',', $existingFields[$targetIndex]['csvoptions'])), 'strlen'))
    : array();
    $remaining = array();
    $removed = array();
    foreach ($current as $option) {
        $key = strtolower($option);
        if (isset($requested[$key])) $removed[$key] = true;
        else $remaining[] = $option;
    }
    foreach ($requested as $key => $unused) {
        if (!isset($removed[$key])) {
            return array('fields' => array(), 'updated_field' => array(), 'errors' => array('Option '.$key.' was not found on field '.$targetField.'.'));
        }
    }
    if (!$remaining) {
        return array('fields' => array(), 'updated_field' => array(), 'errors' => array('A radio or checkbox field must retain at least one option.'));
    }

    $updatedFields = $existingFields;
    $updatedFields[$targetIndex]['csvoptions'] = implode(',', $remaining);
    return array('fields' => array_values($updatedFields), 'updated_field' => $updatedFields[$targetIndex], 'errors' => array());
}

private function _updateFieldProperties($existingFields, $targetField, $changes) {
    $targetIndex = $this->_fieldIndex($existingFields, $targetField);
    if ($targetField === '' || $targetIndex === null) {
        return array('fields' => array(), 'updated_field' => array(), 'errors' => array('The requested target_field was not found in the current form.'));
    }
    if (!is_array($changes) || !$changes) {
        return array('fields' => array(), 'updated_field' => array(), 'errors' => array('No supported field property change was requested.'));
    }

    $allowedKeys = array('mandatory', 'default_field', 'data_type', 'linked_to', 'size', 'index_show', 'add_disabled', 'edit_disabled', 'checkbox_layout', 'options');
    foreach ($changes as $key => $unused) {
        if (!in_array($key, $allowedKeys, true)) {
            return array('fields' => array(), 'updated_field' => array(), 'errors' => array('Unsupported field property: '.$key.'.'));
        }
    }

    $updatedFields = $existingFields;
    $field =& $updatedFields[$targetIndex];
    $changed = false;
    foreach (array('mandatory', 'index_show', 'add_disabled', 'edit_disabled') as $booleanKey) {
        if (array_key_exists($booleanKey, $changes)) {
            $value = $changes[$booleanKey] ? '1' : '0';
            if (!isset($field[$booleanKey]) || (string)$field[$booleanKey] !== $value) $changed = true;
            $field[$booleanKey] = $value;
            if ($booleanKey === 'mandatory' && array_key_exists('mandetory', $field)) $field['mandetory'] = $value;
        }
    }
    if (array_key_exists('default_field', $changes)) {
        $makeDefault = (bool)$changes['default_field'];
        if (!$makeDefault && !empty($field['default_field'])) {
            return array('fields' => array(), 'updated_field' => array(), 'errors' => array('Select another default field instead of leaving the form without one.'));
        }
        if ($makeDefault) {
            foreach ($updatedFields as $index => $unused) $updatedFields[$index]['default_field'] = '0';
            $field['default_field'] = '1';
            $changed = true;
        }
    }
    if (array_key_exists('size', $changes)) {
        $size = (int)$changes['size'];
        if (!in_array($size, array(4, 6, 12), true)) {
            return array('fields' => array(), 'updated_field' => array(), 'errors' => array('Field size must be 4, 6, or 12.'));
        }
        if (!isset($field['size']) || (int)$field['size'] !== $size) $changed = true;
        $field['size'] = (string)$size;
    }

    $typeMap = array(
    'text' => array('display_type' => '0', 'field_type' => '0', 'length' => '255', 'dummy' => ''),
    'textarea' => array('display_type' => '0', 'field_type' => '1', 'length' => '0', 'dummy' => ''),
    'date' => array('display_type' => '0', 'field_type' => '5', 'length' => '255', 'dummy' => ''),
    'dropdown-s' => array('display_type' => '3', 'field_type' => '0', 'length' => '36', 'dummy' => '-1'),
    'dropdown-m' => array('display_type' => '4', 'field_type' => '1', 'length' => '0', 'dummy' => '0'),
    'radio' => array('display_type' => '1', 'field_type' => '2', 'length' => '1', 'dummy' => '0'),
    'checkbox' => array('display_type' => '2', 'field_type' => '0', 'length' => '50', 'dummy' => '0'),
    'linked_documents' => array('display_type' => '10', 'field_type' => '0', 'length' => '0', 'dummy' => '0')
    );
    $dataType = isset($changes['data_type']) ? strtolower(trim($changes['data_type'])) : (isset($field['data_type']) ? $field['data_type'] : '');
    if (isset($changes['linked_to']) && !isset($changes['data_type']) && !in_array($dataType, array('dropdown-s', 'dropdown-m'), true)) $dataType = 'dropdown-s';
    if (!isset($typeMap[$dataType])) {
        return array('fields' => array(), 'updated_field' => array(), 'errors' => array('The requested data_type is not supported for field updates.'));
    }
    if (!isset($field['data_type']) || $field['data_type'] !== $dataType) $changed = true;
    $field['data_type'] = $dataType;
    foreach ($typeMap[$dataType] as $key => $value) $field[$key] = $value;

    if (array_key_exists('checkbox_layout', $changes)) {
        $layout = strtolower(trim((string)$changes['checkbox_layout']));
        if ($dataType !== 'checkbox') {
            return array('fields' => array(), 'updated_field' => array(), 'errors' => array('Checkbox layout can only be changed on a checkbox field.'));
        }
        if (!in_array($layout, array('inline', 'vertical'), true)) {
            return array('fields' => array(), 'updated_field' => array(), 'errors' => array('Checkbox layout must be inline or vertical.'));
        }
        if (!isset($field['checkbox_layout']) || $field['checkbox_layout'] !== $layout) $changed = true;
        $field['checkbox_layout'] = $layout;
    } elseif ($dataType === 'checkbox' && empty($field['checkbox_layout'])) {
        $field['checkbox_layout'] = 'vertical';
        $changed = true;
    }

    if (in_array($dataType, array('dropdown-s', 'dropdown-m'), true)) {
        $requestedModel = isset($changes['linked_to']) ? $changes['linked_to'] : (isset($field['linked_to']) ? $field['linked_to'] : '');
        $linkedModel = $this->_resolveLinkedModel($requestedModel);
        if ($linkedModel === '') {
            return array('fields' => array(), 'updated_field' => array(), 'errors' => array('The requested linked_to model does not exist in FlinkISO.'));
        }
        if (!isset($field['linked_to']) || $field['linked_to'] !== $linkedModel) $changed = true;
        $field['linked_to'] = $linkedModel;
        unset($field['csvoptions']);
    } else {
        if (isset($field['linked_to']) && $field['linked_to'] !== '-1') $changed = true;
        $field['linked_to'] = '-1';
    }

    if (in_array($dataType, array('radio', 'checkbox'), true)) {
        if (array_key_exists('options', $changes)) {
            $options = $this->_cleanOptions($changes['options']);
            if (!$options) {
                return array('fields' => array(), 'updated_field' => array(), 'errors' => array('A radio or checkbox field requires at least one option.'));
            }
            $csv = implode(',', $options);
            if (!isset($field['csvoptions']) || $field['csvoptions'] !== $csv) $changed = true;
            $field['csvoptions'] = $csv;
        } elseif (empty($field['csvoptions'])) {
            return array('fields' => array(), 'updated_field' => array(), 'errors' => array('Changing to radio or checkbox requires options.'));
        }
        if ($dataType === 'checkbox' && empty($field['checkbox_layout'])) $field['checkbox_layout'] = 'vertical';
    } else {
        unset($field['csvoptions'], $field['checkbox_layout']);
    }

    if (!$changed) {
        return array('fields' => array(), 'updated_field' => array(), 'errors' => array('That field already has the requested configuration.'));
    }
    return array('fields' => array_values($updatedFields), 'updated_field' => $field, 'errors' => array());
}

private function _reorderField($existingFields, $targetField, $insertAfter) {
    $targetIndex = $this->_fieldIndex($existingFields, $targetField);
    if ($targetField === '' || $targetIndex === null) {
        return array('fields' => array(), 'updated_field' => array(), 'errors' => array('The field to move was not found in the current form.'));
    }
    if ($targetField === $insertAfter) {
        return array('fields' => array(), 'updated_field' => array(), 'errors' => array('A field cannot be moved after itself.'));
    }
    if ($insertAfter !== '' && $this->_fieldIndex($existingFields, $insertAfter) === null) {
        return array('fields' => array(), 'updated_field' => array(), 'errors' => array('The requested destination field was not found in the current form.'));
    }

    $updatedFields = array_values($existingFields);
    $moving = $updatedFields[$targetIndex];
    array_splice($updatedFields, $targetIndex, 1);
    $destination = $insertAfter === '' ? -1 : $this->_fieldIndex($updatedFields, $insertAfter);
    array_splice($updatedFields, $destination + 1, 0, array($moving));
    foreach ($updatedFields as $index => $field) $updatedFields[$index]['sequence'] = (string)($index + 1);
    if (json_encode(array_values($existingFields)) === json_encode($updatedFields)) {
        return array('fields' => array(), 'updated_field' => array(), 'errors' => array('That field is already in the requested position.'));
    }
    $newIndex = $this->_fieldIndex($updatedFields, $targetField);
    return array('fields' => $updatedFields, 'updated_field' => $updatedFields[$newIndex], 'errors' => array());
}

private function _fieldIndex($fields, $fieldName) {
    foreach ((array)$fields as $index => $field) {
        if (!empty($field['field_name']) && $field['field_name'] === $fieldName) return $index;
    }
    return null;
}

private function _cleanOptions($options) {
    $result = array();
    $known = array();
    foreach ((array)$options as $option) {
        if (!is_scalar($option)) continue;
        $option = trim((string)$option);
        if ($option === '') continue;
        $key = strtolower($option);
        if (isset($known[$key])) continue;
        $known[$key] = true;
        $result[] = $option === strtolower($option) ? ucwords($option) : $option;
    }
    return $result;
}

private function _resolveLinkedModel($requestedModel) {
    $requestedModel = trim((string)$requestedModel);
    if ($requestedModel === '' || $requestedModel === '-1') return '';
    $candidate = Inflector::classify(Inflector::singularize($requestedModel));
    foreach ((array)App::objects('Model') as $modelName) {
        if (strcasecmp($modelName, $candidate) === 0 || strcasecmp($modelName, $requestedModel) === 0) return $modelName;
    }
    $tables = $this->CustomTable->find('all', array('recursive' => -1, 'fields' => array('CustomTable.name', 'CustomTable.table_name')));
    foreach ($tables as $table) {
        $modelName = Inflector::classify(Inflector::singularize($table['CustomTable']['table_name']));
        if (strcasecmp($requestedModel, $table['CustomTable']['name']) === 0 || strcasecmp($candidate, $modelName) === 0) return $modelName;
    }
    return '';
}

private function _updateFieldLabel($existingFields, $targetField, $newFieldLabel) {
    $targetIndex = null;
    foreach ($existingFields as $index => $field) {
        if (!empty($field['field_name']) && $field['field_name'] === $targetField) {
            $targetIndex = $index;
            break;
        }
    }
    if ($targetField === '' || $targetIndex === null) {
        return array('fields' => array(), 'updated_field' => array(), 'errors' => array('The requested target_field was not found in the current form.'));
    }

    $newFieldLabel = trim((string)$newFieldLabel);
    if ($newFieldLabel === '' || strlen($newFieldLabel) > 120) {
        return array('fields' => array(), 'updated_field' => array(), 'errors' => array('The requested field label is empty or too long.'));
    }
    $currentLabel = isset($existingFields[$targetIndex]['field_label'])
    ? $this->_decodeFieldLabel($existingFields[$targetIndex]['field_label'])
    : '';
    if (strcasecmp($currentLabel, $newFieldLabel) === 0) {
        return array('fields' => array(), 'updated_field' => array(), 'errors' => array('That field already has the requested label.'));
    }

    $updatedFields = $existingFields;
    $updatedFields[$targetIndex]['field_label'] = base64_encode($newFieldLabel);
    return array(
    'fields' => array_values($updatedFields),
    'updated_field' => $updatedFields[$targetIndex],
    'errors' => array()
    );
}

private function _inferLabelChange($prompt, $existingFields) {
    if (!preg_match('/^\s*(?:change|rename|relabel)\s+(.+?)\s+to\s+(.+?)\s*[.!?]*\s*$/i', trim($prompt), $matches)) {
        return null;
    }
    $requestedField = trim($matches[1], " \t\n\r\0\x0B\"'");
    $newLabel = trim($matches[2], " \t\n\r\0\x0B\"'");
    if ($requestedField === '' || $newLabel === '') return null;

    foreach ($existingFields as $field) {
        if (empty($field['field_name'])) continue;
        $fieldName = $field['field_name'];
        $fieldLabel = isset($field['field_label']) ? $this->_decodeFieldLabel($field['field_label']) : '';
        if (strcasecmp($requestedField, $fieldName) === 0 ||
        strcasecmp($requestedField, Inflector::humanize($fieldName)) === 0 ||
        ($fieldLabel !== '' && strcasecmp($requestedField, $fieldLabel) === 0)) {
            return array('target_field' => $fieldName, 'new_field_label' => $newLabel);
        }
    }
    return null;
}

private function _inferFieldRemoval($prompt, $existingFields) {
    if (!preg_match('/^\s*(?:remove|delete)\s+(?:the\s+)?(?:extra\s+)?field(?:\s+(?:called|named))?\s+[\"\']?(.+?)[\"\']?\s*[.!?]*\s*$/i', trim($prompt), $matches)) {
        return null;
    }
    $requestedField = trim($matches[1], " \t\n\r\0\x0B\"'");
    if ($requestedField === '') return null;

    foreach ($existingFields as $field) {
        if (empty($field['field_name'])) continue;
        $fieldName = $field['field_name'];
        $fieldLabel = isset($field['field_label']) ? $this->_decodeFieldLabel($field['field_label']) : '';
        if (strcasecmp($requestedField, $fieldName) === 0 ||
        strcasecmp($requestedField, Inflector::humanize($fieldName)) === 0 ||
        ($fieldLabel !== '' && strcasecmp($requestedField, $fieldLabel) === 0)) {
            return array($fieldName);
        }
    }
    return null;
}

private function _inferFieldPropertyChange($prompt, $existingFields) {
    $prompt = trim($prompt);
    if (preg_match('/^\s*(?:make|set)\s+(?:the\s+)?(.+?)\s+(?:as\s+)?(?:(?:the\s+)?default\s+and\s+(?:mandatory|required)|(?:mandatory|required)\s+and\s+(?:the\s+)?default)(?:\s+field)?\s*[.!?]*\s*$/i', $prompt, $matches)) {
        $targetField = $this->_matchExistingFieldName($matches[1], $existingFields);
        if ($targetField === '') return null;
        return array('target_field' => $targetField, 'field_changes' => array('default_field' => true, 'mandatory' => true));
    }
    if (preg_match('/^\s*(?:make|set)\s+(?:the\s+)?(.+?)\s+(?:as\s+)?(?:the\s+)?default(?:\s+field)?\s*[.!?]*\s*$/i', $prompt, $matches)) {
        $targetField = $this->_matchExistingFieldName($matches[1], $existingFields);
        if ($targetField === '') return null;
        return array('target_field' => $targetField, 'field_changes' => array('default_field' => true));
    }
    if (preg_match('/^\s*(?:make|set)\s+(?:the\s+)?(.+?)\s+(not\s+mandatory|optional|mandatory|required)\s*[.!?]*\s*$/i', $prompt, $matches)) {
        $targetField = $this->_matchExistingFieldName($matches[1], $existingFields);
        if ($targetField === '') return null;
        $state = strtolower(preg_replace('/\s+/', ' ', trim($matches[2])));
        return array(
        'target_field' => $targetField,
        'field_changes' => array('mandatory' => in_array($state, array('mandatory', 'required'), true))
        );
    }

    if (preg_match('/^\s*(?:show|display)\s+(?:the\s+)?(.+?)\s+(?:on|in)\s+(?:the\s+)?(?:index|list)(?:\s+page)?\s*[.!?]*\s*$/i', $prompt, $matches)) {
        $targetField = $this->_matchExistingFieldName($matches[1], $existingFields);
        if ($targetField === '') return null;
        return array('target_field' => $targetField, 'field_changes' => array('index_show' => true));
    }
    if (preg_match('/^\s*(?:hide|remove)\s+(?:the\s+)?(.+?)\s+from\s+(?:the\s+)?(?:index|list)(?:\s+page)?\s*[.!?]*\s*$/i', $prompt, $matches)) {
        $targetField = $this->_matchExistingFieldName($matches[1], $existingFields);
        if ($targetField === '') return null;
        return array('target_field' => $targetField, 'field_changes' => array('index_show' => false));
    }

    if (preg_match('/^\s*(?:make|set|display)\s+(?:the\s+)?(.+?)\s+(?:checkbox(?:es)?\s+)?(?:as\s+)?(inline|vertical)(?:\s+layout)?\s*[.!?]*\s*$/i', $prompt, $matches)) {
        $targetField = $this->_matchExistingFieldName($matches[1], $existingFields);
        if ($targetField === '') return null;
        return array('target_field' => $targetField, 'field_changes' => array('checkbox_layout' => strtolower($matches[2])));
    }

    if (preg_match('/^\s*(?:change|convert|set)\s+(?:the\s+)?(.+?)\s+to\s+(?:a\s+)?(text(?:\s+field)?|textarea|text\s+area|date(?:\s+field)?|radio|checkbox|single(?:-?select)?\s+dropdown|multi(?:ple)?(?:-?select)?\s+dropdown)\s*[.!?]*\s*$/i', $prompt, $matches)) {
        $targetField = $this->_matchExistingFieldName($matches[1], $existingFields);
        if ($targetField === '') return null;
        $requestedType = strtolower(preg_replace('/\s+/', ' ', trim($matches[2])));
        if (strpos($requestedType, 'text area') === 0 || $requestedType === 'textarea') $dataType = 'textarea';
        elseif (strpos($requestedType, 'date') === 0) $dataType = 'date';
        elseif (strpos($requestedType, 'radio') === 0) $dataType = 'radio';
        elseif (strpos($requestedType, 'checkbox') === 0) $dataType = 'checkbox';
        elseif (strpos($requestedType, 'single') === 0) $dataType = 'dropdown-s';
        elseif (strpos($requestedType, 'multi') === 0) $dataType = 'dropdown-m';
        else $dataType = 'text';
        return array('target_field' => $targetField, 'field_changes' => array('data_type' => $dataType));
    }

    if (preg_match('/^\s*(?:link|connect)\s+(?:the\s+)?(?:field\s+)?(.+?)\s+(?:to|with)\s+(?:the\s+)?(.+?)(?:\s+model)?\s*[.!?]*\s*$/i', $prompt, $matches)) {
        $targetField = $this->_matchExistingFieldName($matches[1], $existingFields);
        if ($targetField === '') return null;
        $linkedTo = trim(preg_replace('/\s+model\s*$/i', '', $matches[2]), " \t\n\r\0\x0B\"'");
        return array(
        'target_field' => $targetField,
        'field_changes' => array(
        'data_type' => preg_match('/\b(multiple|multi)\b/i', $prompt) ? 'dropdown-m' : 'dropdown-s',
        'linked_to' => $linkedTo
        )
        );
    }
    return null;
}

private function _clarificationQuestion($prompt, $existingFields) {
    $prompt = trim($prompt);
    if (preg_match('/^\s*(?:make|set|mark)\s+(?:the\s+)?(.+?)\s+(?:as\s+)?(?:important|key|primary)(?:\s+field)?\s*[.!?]*\s*$/i', $prompt, $matches)) {
        $targetField = $this->_matchExistingFieldName($matches[1], $existingFields);
        if ($targetField === '') return null;
        $label = $this->_fieldDisplayLabel($targetField, $existingFields);
        return array(
        'type' => 'choice',
        'question' => __('What should “important” mean for %s?', $label),
        'options' => array(
        array('label' => __('Make mandatory'), 'prompt' => 'Make '.$targetField.' mandatory.'),
        array('label' => __('Make default'), 'prompt' => 'Make '.$targetField.' the default field.'),
        array('label' => __('Mandatory and default'), 'prompt' => 'Make '.$targetField.' the default and mandatory field.'),
        array('label' => __('Show on list'), 'prompt' => 'Show '.$targetField.' on the index page.')
        )
        );
    }

    if (preg_match('/^\s*(?:link|connect)\s+(?:the\s+)?(?:field\s+)?(.+?)\s*[.!?]*\s*$/i', $prompt, $matches)) {
        $targetField = $this->_matchExistingFieldName($matches[1], $existingFields);
        if ($targetField === '') return null;
        $label = $this->_fieldDisplayLabel($targetField, $existingFields);
        $options = array();
        foreach (array('Employee', 'Branch', 'Department', 'Designation', 'QcDocument') as $model) {
            $displayModel = $model === 'QcDocument' ? __('Quality Document') : $model;
            $options[] = array('label' => $displayModel, 'prompt' => 'Link '.$targetField.' to '.$model.' model.');
        }
        return array('type' => 'choice', 'question' => __('Which model should %s link to?', $label), 'options' => $options);
    }

    if (preg_match('/^\s*(?:add|create)\s+(?:a\s+)?(?:new\s+)?field\s*[.!?]*\s*$/i', $prompt)) {
        return array(
        'type' => 'text',
        'question' => __('What should the new field be called?'),
        'placeholder' => __('Field name'),
        'prompt_prefix' => 'Add a field called ',
        'prompt_suffix' => '.'
        );
    }

    if (preg_match('/^\s*(?:add|create)\s+(?:a\s+)?(?:new\s+)?field\s+(?:called|named)\s+["\']?(.+?)["\']?(?:\s+after\s+(?:the\s+)?(?:field\s+)?["\']?(.+?)["\']?)?\s*[.!?]*\s*$/i', $prompt, $matches)) {
        $label = trim($matches[1], " \t\n\r\0\x0B\"'");
        if ($label === '') return null;
        $afterField = '';
        $position = '';
        if (!empty($matches[2])) {
            $afterField = $this->_matchExistingFieldName(trim($matches[2], " \t\n\r\0\x0B\"'"), $existingFields);
            if ($afterField !== '') $position = ' after '.$afterField;
        }
        return array(
        'type' => 'field_wizard',
        'question' => __('Configure the new field “%s”.', $label),
        'label' => $label,
        'insert_after' => !empty($afterField) ? $afterField : '',
        'field_types' => array(
        array('label' => __('Short text'), 'value' => 'text'),
        array('label' => __('Phone'), 'value' => 'phone'),
        array('label' => __('Email'), 'value' => 'email'),
        array('label' => __('Long text'), 'value' => 'textarea'),
        array('label' => __('Date'), 'value' => 'date'),
        array('label' => __('Date and time'), 'value' => 'datetime'),
        array('label' => __('Number'), 'value' => 'number'),
        array('label' => __('Decimal number'), 'value' => 'float'),
        array('label' => __('Dropdown — single'), 'value' => 'dropdown-s'),
        array('label' => __('Dropdown — multiple'), 'value' => 'dropdown-m'),
        array('label' => __('Radio buttons'), 'value' => 'radio'),
        array('label' => __('Checkboxes'), 'value' => 'checkbox'),
        array('label' => __('File upload'), 'value' => 'file'),
        array('label' => __('Comments or notes'), 'value' => 'comments'),
        array('label' => __('Hidden field'), 'value' => 'hidden'),
        array('label' => __('Linked documents'), 'value' => 'linked_documents'),
        array('label' => __('Section break'), 'value' => 'break')
        ),
        'linked_models' => array(
        array('label' => __('Employees'), 'value' => 'Employee'),
        array('label' => __('Branches'), 'value' => 'Branch'),
        array('label' => __('Departments'), 'value' => 'Department'),
        array('label' => __('Designations'), 'value' => 'Designation'),
        array('label' => __('Quality Documents'), 'value' => 'QcDocument')
        ),
        'widths' => array(3, 4, 6, 8, 9, 12)
        );
    }

    if (preg_match('/^\s*(?:add|create)\s+(?:a\s+)?choice\s+field\s+(?:called|named)\s+["\']?(.+?)["\']?(?:\s+after\s+(?:the\s+)?(?:field\s+)?["\']?(.+?)["\']?)?\s*[.!?]*\s*$/i', $prompt, $matches)) {
        $label = trim($matches[1], " \t\n\r\0\x0B\"'");
        if ($label === '') return null;
        $position = '';
        if (!empty($matches[2])) {
            $afterField = $this->_matchExistingFieldName(trim($matches[2], " \t\n\r\0\x0B\"'"), $existingFields);
            if ($afterField !== '') $position = ' after '.$afterField;
        }
        return array(
        'type' => 'choice',
        'question' => __('Should %s allow one selection or multiple selections?', $label),
        'options' => array(
        array('label' => __('One selection'), 'prompt' => 'Add a radio field called '.$label.$position.'.'),
        array('label' => __('Multiple selections'), 'prompt' => 'Add a checkbox field called '.$label.$position.'.')
        )
        );
    }

    if (preg_match('/^\s*(?:add|create)\s+(?:a\s+)?(radio|checkbox)\s+field\s+(?:called|named)\s+["\']?(.+?)["\']?\s*[.!?]*\s*$/i', $prompt, $matches)) {
        $type = strtolower($matches[1]);
        $label = trim($matches[2], " \t\n\r\0\x0B\"'");
        if ($label === '') return null;
        return array(
        'type' => 'text',
        'question' => __('Which choices should %s contain? Enter them separated by commas.', $label),
        'placeholder' => __('For example: Yes, No, Not Applicable'),
        'prompt_prefix' => 'Add a '.$type.' field called '.$label.' with choices ',
        'prompt_suffix' => ' at the end of the form.'
        );
    }

    if (preg_match('/^\s*(?:remove|delete)\s+(?:the\s+)?field\s*[.!?]*\s*$/i', $prompt)) {
        return array(
        'type' => 'text',
        'question' => __('Which field should be removed?'),
        'placeholder' => __('Field name'),
        'prompt_prefix' => 'Remove the field called ',
        'prompt_suffix' => '.'
        );
    }
    return null;
}

private function _fieldDisplayLabel($fieldName, $existingFields) {
    foreach ((array)$existingFields as $field) {
        if (empty($field['field_name']) || $field['field_name'] !== $fieldName) continue;
        $label = !empty($field['field_label']) ? $this->_decodeFieldLabel($field['field_label']) : '';
        return $label !== '' ? $label : Inflector::humanize($fieldName);
    }
    return Inflector::humanize($fieldName);
}

private function _matchExistingFieldName($requestedField, $existingFields) {
    $requestedField = trim((string)$requestedField, " \t\n\r\0\x0B\"'");
    foreach ((array)$existingFields as $field) {
        if (empty($field['field_name'])) continue;
        $fieldName = $field['field_name'];
        $fieldLabel = isset($field['field_label']) ? $this->_decodeFieldLabel($field['field_label']) : '';
        if (strcasecmp($requestedField, $fieldName) === 0 ||
        strcasecmp($requestedField, Inflector::humanize($fieldName)) === 0 ||
        ($fieldLabel !== '' && strcasecmp($requestedField, $fieldLabel) === 0)) return $fieldName;
    }
    return '';
}

private function _removeExistingFields($existingFields, $fieldsToRemove) {
    $requested = array();
    foreach ((array)$fieldsToRemove as $fieldName) {
        if (!is_scalar($fieldName)) continue;
        $fieldName = trim((string)$fieldName);
        if ($fieldName !== '') $requested[$fieldName] = true;
    }
    if (!$requested) {
        return array('fields' => array(), 'removed_field_names' => array(), 'errors' => array('No field was selected for removal.'));
    }

    $found = array();
    $remaining = array();
    $errors = array();
    foreach ($existingFields as $field) {
        $fieldName = !empty($field['field_name']) ? $field['field_name'] : '';
        if ($fieldName !== '' && isset($requested[$fieldName])) {
            $found[$fieldName] = true;
            if (!empty($field['default_field'])) {
                $errors[] = 'The default display field '.$fieldName.' cannot be removed without selecting a replacement.';
            }
            continue;
        }
        $remaining[] = $field;
    }
    foreach ($requested as $fieldName => $unused) {
        if (!isset($found[$fieldName])) $errors[] = 'Field '.$fieldName.' was not found in the current form.';
    }
    if (!$remaining) $errors[] = 'A form cannot have all of its fields removed.';
    if ($errors) return array('fields' => array(), 'removed_field_names' => array(), 'errors' => $errors);

    return array(
    'fields' => array_values($remaining),
    'removed_field_names' => array_keys($found),
    'errors' => array()
    );
}

private function _jsonResponse($success, $data, $statusCode = 200) {
    $body = array_merge(array('success' => (bool)$success), $data);
    $this->_finishAiHistory($success, $body, $statusCode);
    if ($this->_debugStream) {
        // Output has already started, so finish the NDJSON stream with one
        // machine-readable result event. The browser uses this for the
        // normal validated preview/rebuild flow.
        $this->response->statusCode(200);
        $this->response->body(json_encode(array('type' => 'result', 'data' => $body))."\n");
        return $this->response;
    }
    $this->response->type('json');
    // This installation uses an older CakeResponse status table which
    // omits newer codes such as 422. Register the code before setting it
    // so useful validation errors are not replaced by an internal error.
    if ($this->response->httpCodes((int)$statusCode) === null) {
        $this->response->httpCodes(array((int)$statusCode => 'Request Error'));
    }
    $this->response->statusCode($statusCode);
    $this->response->body(json_encode($body));
    return $this->response;
}

public function history() {
    $this->autoRender = false;
    if (!$this->request->is('get')) {
        return $this->_jsonResponse(false, array('message' => __('Only GET requests are accepted.')), 405);
    }
    $sourceController = isset($this->request->query['source_controller']) ? trim($this->request->query['source_controller']) : '';
    $customTableId = isset($this->request->query['custom_table_id']) ? trim($this->request->query['custom_table_id']) : '';
    $qcDocumentId = isset($this->request->query['qc_document_id']) ? trim($this->request->query['qc_document_id']) : '';
    $recordId = isset($this->request->query['record_id']) ? trim($this->request->query['record_id']) : '';
    $before = isset($this->request->query['before']) ? trim($this->request->query['before']) : '';
    $isGeneratedForm = strpos($sourceController, 'tbl_') === 0 || strpos($sourceController, 'chd_') === 0;
    if ($sourceController !== 'qc_documents' && !$isGeneratedForm) {
        return $this->_jsonResponse(false, array('message' => __('AI history is not available in this section.')), 403);
    }
    $companyId = $this->Session->read('User.company_id');
    if ($isGeneratedForm) {
        if ($customTableId === '' || !$this->CustomTable->find('count', array('conditions' => array(
        'CustomTable.id' => $customTableId,
        'CustomTable.table_name' => $sourceController,
        'CustomTable.company_id' => $companyId
        )))) return $this->_jsonResponse(false, array('message' => __('The requested form history is not available.')), 404);
    } else {
        if ($qcDocumentId === '' || !$this->QcDocument->find('count', array('conditions' => array(
        'QcDocument.id' => $qcDocumentId,
        'QcDocument.company_id' => $companyId
        )))) return $this->_jsonResponse(false, array('message' => __('The requested document history is not available.')), 404);
    }
    if (!$this->_ensureAiTable()) {
        return $this->_jsonResponse(false, array('message' => __('AI history storage is not available.')), 503);
    }

    $conditions = array(
    'Ai.company_id' => $companyId,
    'Ai.source_controller' => $sourceController
    );
    if ($customTableId !== '') $conditions['Ai.custom_table_id'] = $customTableId;
    if ($qcDocumentId !== '') $conditions['Ai.qc_document_id'] = $qcDocumentId;
    if ($before !== '' && strtotime($before) !== false) $conditions['Ai.created <'] = date('Y-m-d H:i:s', strtotime($before));

    $rows = $this->Ai->find('all', array(
    'recursive' => -1,
    'conditions' => $conditions,
    'order' => array('Ai.created' => 'DESC', 'Ai.id' => 'DESC'),
    'limit' => 30
    ));
    $hasMore = count($rows) === 30;
    $items = array();
    foreach (array_reverse($rows) as $row) {
        $ai = $row['Ai'];
        $response = !empty($ai['response']) ? json_decode($ai['response'], true) : array();
        $rebuild = !empty($ai['rebuild_response']) ? json_decode($ai['rebuild_response'], true) : array();
        $message = !empty($rebuild['message'])
        ? $rebuild['message']
        : (!empty($response['message'])
        ? $response['message']
        : (!empty($ai['error_details']) ? $ai['error_details'] : ''));
        $items[] = array(
        'id' => $ai['id'],
        'request' => $ai['request'],
        'response_message' => $message,
        'operation' => $ai['operation'],
        'status' => $ai['status'],
        'can_cancel' => $ai['status'] === 'processing' && ($ai['user_id'] === $this->Session->read('User.id') || $this->Session->read('User.is_mr') == true),
        'clarification_question' => !empty($response['clarification_question']) ? $response['clarification_question'] : null,
        'model' => $ai['model'],
        'duration_ms' => (int)$ai['duration_ms'],
        'user_name' => $ai['user_name'],
        'form_url' => !empty($rebuild['form_url']) ? $rebuild['form_url'] : (!empty($response['form_url']) ? $response['form_url'] : ''),
        'created' => $ai['created']
        );
    }
    $nextBefore = $rows ? end($rows)['Ai']['created'] : '';
    return $this->_jsonResponse(true, array('items' => $items, 'has_more' => $hasMore, 'next_before' => $nextBefore));
}

public function status() {
    $this->autoRender = false;
    if (!$this->request->is('get')) {
        return $this->_jsonResponse(false, array('message' => __('Only GET requests are accepted.')), 405);
    }
    if (!$this->_ensureAiTable()) {
        return $this->_jsonResponse(false, array('message' => __('AI status storage is not available.')), 503);
    }
    $companyId = $this->Session->read('User.company_id');
    $trackedRequestId = isset($this->request->query['request_id']) ? trim($this->request->query['request_id']) : '';
    if (!preg_match('/^[a-f0-9-]{36}$/i', $trackedRequestId)) $trackedRequestId = '';
    $apiStatus = $this->_apiAiStatus($companyId);
    if (is_array($apiStatus) && empty($apiStatus['active'])) {
        // Allow a short hand-off window between creating the local history
        // row and ApiV2 acquiring its lock. Older processing rows are
        // abandoned and must not keep the whole instance disabled.
        $staleConditions = array(
        'Ai.company_id' => $companyId,
        'Ai.status' => 'processing',
        // `modified` is refreshed by stream heartbeats, so it can still be
        // recent immediately after a very long request finishes. The API's
        // inactive status is authoritative; only the initial 30-second
        // hand-off is protected here.
        'Ai.created <' => date('Y-m-d H:i:s', time() - 30)
        );
        // Do not exempt the ID supplied by the browser. Once API v2 confirms
        // that this instance has no active request, an old local processing
        // row is stale even when the same browser is still polling that ID.
        // Exempting it here left the completed task card visible forever.
        $this->Ai->updateAll(array(
        'Ai.status' => "'failed'",
        'Ai.error_details' => "'The API is no longer processing this request.'",
        'Ai.modified' => "'".date('Y-m-d H:i:s')."'"
        ), $staleConditions);
    }
    $apiRequestIsActive = is_array($apiStatus) && !empty($apiStatus['active']) && !empty($apiStatus['same_instance']) && !empty($apiStatus['request_id']);
    $activeConditions = array('Ai.company_id' => $companyId);
    if ($apiRequestIsActive) {
        // API v2 owns the generation lock, so it is authoritative here. The
        // local history row may have been marked failed by an earlier status
        // check during a brief network/status hand-off; still expose the real
        // running request and keep the composer disabled.
        $activeConditions['Ai.id'] = $apiStatus['request_id'];
        $this->Ai->updateAll(array(
        'Ai.status' => "'failed'",
        'Ai.error_details' => "'Superseded by the active API request.'",
        'Ai.modified' => "'".date('Y-m-d H:i:s')."'"
        ), array(
        'Ai.company_id' => $companyId,
        'Ai.status' => 'processing',
        'Ai.id !=' => $apiStatus['request_id'],
        'Ai.modified <' => date('Y-m-d H:i:s', time() - 30)
        ));
    } else {
        $activeConditions['Ai.status'] = 'processing';
    }
    $active = $this->Ai->find('first', array(
    'recursive' => -1,
    'conditions' => $activeConditions,
    'fields' => array('Ai.id', 'Ai.user_id', 'Ai.user_name', 'Ai.source_controller', 'Ai.request', 'Ai.status', 'Ai.created'),
    'order' => array('Ai.created' => 'ASC')
    ));
    if (!$active) {
        if ($apiRequestIsActive) {
            return $this->_jsonResponse(true, array(
            'active' => true,
            'request_id' => $apiStatus['request_id'],
            'user_name' => __('Another user'),
            'source' => !empty($apiStatus['source_controller'])
            ? ($apiStatus['source_controller'] === 'qc_documents' ? __('Quality Documents') : Inflector::humanize($apiStatus['source_controller']))
            : __('FlinkISO AI'),
            'started' => !empty($apiStatus['started']) ? $apiStatus['started'] : '',
            'prompt' => '',
            'cancelling' => !empty($apiStatus['cancelling']),
            'can_cancel' => empty($apiStatus['cancelling']) && $this->Session->read('User.is_mr') == true
            ));
        }
        $latestConditions = array('Ai.company_id' => $companyId);
        if ($trackedRequestId !== '') $latestConditions['Ai.id'] = $trackedRequestId;
        $latest = $this->Ai->find('first', array(
        'recursive' => -1,
        'conditions' => $latestConditions,
        'fields' => array('Ai.id', 'Ai.status'),
        'order' => array('Ai.modified' => 'DESC', 'Ai.created' => 'DESC')
        ));
        return $this->_jsonResponse(true, array(
        'active' => false,
        'last_request_id' => $latest ? $latest['Ai']['id'] : '',
        'last_status' => $latest ? $latest['Ai']['status'] : ''
        ));
    }
    $item = $active['Ai'];
    $isOwner = (string)$item['user_id'] === (string)$this->Session->read('User.id');
    $isCancelling = !empty($apiStatus['cancelling']) || $item['status'] === 'cancelled';
    return $this->_jsonResponse(true, array(
    'active' => true,
    'request_id' => $item['id'],
    'user_name' => $item['user_name'],
    'source' => $item['source_controller'] === 'qc_documents' ? __('Quality Documents') : Inflector::humanize($item['source_controller']),
    'started' => $item['created'],
    'prompt' => $isOwner ? $item['request'] : '',
    'cancelling' => $isCancelling,
    'can_cancel' => !$isCancelling && ($isOwner || $this->Session->read('User.is_mr') == true)
    ));
}

public function cancel() {
    $this->autoRender = false;
    if (!$this->request->is('post')) {
        return $this->_jsonResponse(false, array('message' => __('Only POST requests are accepted.')), 405);
    }
    $id = isset($this->request->data['id']) ? trim($this->request->data['id']) : '';
    if (!preg_match('/^[a-f0-9-]{36}$/i', $id) || !$this->_ensureAiTable()) {
        return $this->_jsonResponse(false, array('message' => __('The AI request could not be found.')), 404);
    }
    $conditions = array(
    'Ai.id' => $id,
    'Ai.company_id' => $this->Session->read('User.company_id')
    );
    if ($this->Session->read('User.is_mr') != true) {
        $conditions['Ai.user_id'] = $this->Session->read('User.id');
    }
    $row = $this->Ai->find('first', array('recursive' => -1, 'conditions' => $conditions));
    if (!$row) {
        return $this->_jsonResponse(false, array('message' => __('The AI request could not be found.')), 404);
    }
    if ($row['Ai']['status'] !== 'processing') {
        return $this->_jsonResponse(true, array('message' => __('This AI request has already finished.'), 'status' => $row['Ai']['status']));
    }

    $cancelPath = $this->_aiCancelPath($id, true);
    if ($cancelPath === '' || @file_put_contents($cancelPath, 'cancel', LOCK_EX) === false) {
        return $this->_jsonResponse(false, array('message' => __('FlinkISO could not signal the AI request to stop.')), 500);
    }
    $remoteCancelled = $this->_cancelApiRequest($id, $this->Session->read('User.company_id'));
    $this->Ai->id = $id;
    $this->Ai->save(array('Ai' => array(
    'status' => 'cancelled',
    'response' => json_encode(array('success' => false, 'message' => __('AI generation was cancelled.'), 'error_type' => 'cancelled')),
    'error_details' => __('Cancelled by user.'),
    'http_status' => 409,
    'modified' => date('Y-m-d H:i:s')
    )), false);
    return $this->_jsonResponse(true, array(
    'message' => $remoteCancelled
    ? __('Stop requested. The API is cancelling model generation and releasing the request.')
    : __('Stop was recorded in FlinkISO, but the API cancellation could not be confirmed.'),
    'status' => 'cancelled',
    'api_cancelled' => $remoteCancelled
    ));
}

private function _startAiHistory($prompt, $sourceController, $sourceAction, $customTableId, $qcDocumentId, $recordId) {
    try {
        if (!$this->_ensureAiTable()) return;
        $id = CakeText::uuid();
        $this->Ai->create();
        if ($this->Ai->save(array('Ai' => array(
        'id' => $id,
        'company_id' => $this->Session->read('User.company_id'),
        'user_id' => $this->Session->read('User.id'),
        'user_name' => (string)$this->Session->read('User.name'),
        'source_controller' => $sourceController,
        'source_action' => $sourceAction,
        'custom_table_id' => $customTableId,
        'qc_document_id' => $qcDocumentId,
        'record_id' => $recordId,
        'request' => $prompt,
        'status' => 'processing',
        'model' => 'API v2',
        'created' => date('Y-m-d H:i:s'),
        'modified' => date('Y-m-d H:i:s')
        )), false)) $this->_historyId = $id;
    } catch (Exception $exception) {
        CakeLog::write('error', 'AI history start failed: '.$exception->getMessage());
    }
}

private function _touchAiHistory() {
    if ($this->_historyId === '') return;
    try {
        $this->Ai->updateAll(array(
        'Ai.modified' => "'".date('Y-m-d H:i:s')."'"
        ), array(
        'Ai.id' => $this->_historyId,
        'Ai.status' => 'processing'
        ));
    } catch (Exception $exception) {
        CakeLog::write('error', 'AI history heartbeat failed: '.$exception->getMessage());
    }
}

private function _finishAiHistory($success, $body, $statusCode) {
    if ($this->_historyId === '') return;
    try {
        $status = !empty($body['error_type']) && $body['error_type'] === 'cancelled'
        ? 'cancelled'
        : (!$success ? 'failed' : (!empty($body['auto_apply']) ? 'prepared' : 'completed'));
        // Use one direct UPDATE for the terminal transition. A model save can
        // be affected by application-wide callbacks and previously left an
        // already-returned local clarification row in `processing`, which
        // falsely locked the entire instance until the stale-row timeout.
        $dataSource = $this->Ai->getDataSource();
        $updated = $this->Ai->updateAll(array(
        'Ai.response' => $dataSource->value(json_encode($body)),
        'Ai.raw_response' => $dataSource->value($this->_rawModelResponse),
        'Ai.operation' => $dataSource->value(isset($body['operation']) ? $body['operation'] : ''),
        'Ai.status' => $dataSource->value($status),
        'Ai.duration_ms' => (int)(isset($body['duration_ms']) ? $body['duration_ms'] : 0),
        'Ai.http_status' => (int)$statusCode,
        'Ai.error_details' => $dataSource->value(!$success && !empty($body['message']) ? $body['message'] : ''),
        'Ai.modified' => $dataSource->value(date('Y-m-d H:i:s'))
        ), array(
        'Ai.id' => $this->_historyId,
        'Ai.company_id' => $this->Session->read('User.company_id')
        ));
        if (!$updated) CakeLog::write('error', 'AI history completion update failed for '.$this->_historyId.'.');
        $this->Ai->clear();
    } catch (Exception $exception) {
        CakeLog::write('error', 'AI history completion failed: '.$exception->getMessage());
    }
}

private function _ensureAiTable() {
    try {
        $this->Ai->query("CREATE TABLE IF NOT EXISTS `ais` (
        `id` char(36) NOT NULL,
        `company_id` char(36) NOT NULL,
        `user_id` char(36) NOT NULL,
        `user_name` varchar(255) NOT NULL DEFAULT '',
        `source_controller` varchar(255) NOT NULL,
        `source_action` varchar(100) NOT NULL DEFAULT '',
        `custom_table_id` char(36) DEFAULT NULL,
        `qc_document_id` char(36) DEFAULT NULL,
        `record_id` char(36) DEFAULT NULL,
        `request` text NOT NULL,
        `response` longtext,
        `raw_response` longtext,
        `rebuild_response` longtext,
        `operation` varchar(64) NOT NULL DEFAULT '',
        `status` varchar(32) NOT NULL DEFAULT 'processing',
        `model` varchar(100) NOT NULL DEFAULT '',
        `duration_ms` int unsigned NOT NULL DEFAULT 0,
        `http_status` smallint unsigned NOT NULL DEFAULT 0,
        `error_details` text,
        `created` datetime NOT NULL,
        `modified` datetime NOT NULL,
        PRIMARY KEY (`id`),
        KEY `context_lookup` (`company_id`,`source_controller`,`custom_table_id`,`qc_document_id`,`created`),
        KEY `user_lookup` (`company_id`,`user_id`,`created`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        return true;
    } catch (Exception $exception) {
        CakeLog::write('error', 'AI history table unavailable: '.$exception->getMessage());
        return false;
    }
}

private function _aiCancelPath($id, $createDirectory = false) {
    if (!preg_match('/^[a-f0-9-]{36}$/i', (string)$id)) return '';
    $directory = TMP.'flinkiso_ai_cancel'.DS;
    if ($createDirectory && !is_dir($directory) && !@mkdir($directory, 0700, true)) return '';
    if (!is_dir($directory)) return '';
    return $directory.$id.'.cancel';
}

private function _cancelApiRequest($id, $companyId) {
    $base = rtrim((string)Configure::read('ApiPath'), '/');
    if ($base === '' || !preg_match('/^[a-f0-9-]{36}$/i', (string)$id) || !$companyId) return false;
    $curl = curl_init();
    curl_setopt_array($curl, array(
    CURLOPT_URL => $base.'/ai_services/cancel/'.$companyId.'/api:true/company_id:'.$companyId,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CONNECTTIMEOUT => 3,
    CURLOPT_TIMEOUT => 8,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode(array('request_id' => $id)),
    CURLOPT_HTTPHEADER => array('Content-Type: application/json')
    ));
    $body = curl_exec($curl);
    $status = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $error = curl_errno($curl);
    curl_close($curl);
    if ($error || $status < 200 || $status >= 300) return false;
    $decoded = json_decode($body, true);
    return is_array($decoded) && !empty($decoded['success']);
}

private function _apiAiStatus($companyId) {
    $base = rtrim((string)Configure::read('ApiPath'), '/');
    if ($base === '' || !$companyId) return null;
    $curl = curl_init();
    curl_setopt_array($curl, array(
    CURLOPT_URL => $base.'/ai_services/status/'.$companyId.'/api:true/company_id:'.$companyId,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CONNECTTIMEOUT => 2,
    CURLOPT_TIMEOUT => 5,
    CURLOPT_HTTPGET => true
    ));
    $body = curl_exec($curl);
    $status = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $error = curl_errno($curl);
    curl_close($curl);
    if ($error || $status < 200 || $status >= 300) return null;
    $decoded = json_decode($body, true);
    return is_array($decoded) && !empty($decoded['success']) ? $decoded : null;
}

private function _writeAiPending($token, $pending) {
    $directory = TMP.'flinkiso_ai'.DS;
    if (!is_dir($directory) && !@mkdir($directory, 0700, true)) return false;
    if (!is_writable($directory)) return false;

    // Opportunistically remove abandoned one-time jobs.
    foreach ((array)glob($directory.'*.json') as $oldFile) {
        if (is_file($oldFile) && filemtime($oldFile) < time() - 86400) @unlink($oldFile);
    }

    $json = json_encode($pending);
    if ($json === false) return false;
    return @file_put_contents($directory.$token.'.json', $json, LOCK_EX) !== false;
}

private function _findPendingCreate($userId, $companyId, $qcDocumentId) {
    $directory = TMP.'flinkiso_ai'.DS;
    $matches = array();
    foreach ((array)glob($directory.'*.json') as $path) {
        if (!is_file($path) || !is_readable($path)) continue;
        $pending = json_decode(file_get_contents($path), true);
        if (!is_array($pending) ||
        empty($pending['expires']) || $pending['expires'] < time() - 86400 ||
        empty($pending['operation']) || $pending['operation'] !== 'create_form' ||
        empty($pending['user_id']) || $pending['user_id'] !== $userId ||
        empty($pending['qc_document_id']) || $pending['qc_document_id'] !== $qcDocumentId ||
        (!empty($pending['company_id']) && $pending['company_id'] !== $companyId) ||
        empty($pending['fields']) || !is_array($pending['fields'])) continue;
        // Give an explicitly retried job a fresh window. Identity,
        // company and source-document checks above still apply.
        if ($pending['expires'] < time() + 300) {
            $pending['expires'] = time() + 900;
            @file_put_contents($path, json_encode($pending), LOCK_EX);
        }
        $matches[] = array(
        'token' => basename($path, '.json'),
        'pending' => $pending,
        'modified' => filemtime($path)
        );
    }
    if (!$matches) return null;
    usort($matches, function ($left, $right) { return $right['modified'] - $left['modified']; });
    return $matches[0];
}
}
