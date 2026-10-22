<?php
App::uses('AppController', 'Controller');
App::uses('TranslationCatalog', 'Lib');
App::uses('AiCatalogTranslator', 'Lib');
App::uses('CatalogTranslationJob', 'Lib');
App::uses('ApplicationPotExtractor', 'Lib');
class LanguagesController extends AppController {
    public function beforeFilter() {
        $this->_check_login();
        $this->_applyUserLanguage();
        if (!$this->Session->read('User.id') || (!$this->Session->read('User.is_mr') && !$this->Session->read('User.is_super_user'))) throw new ForbiddenException(__('Only administrators and super users can manage languages.'));
        $this->_loadAiConfiguration();
        if (!$this->Language->available()) throw new RuntimeException(__('Run the language database update first.'));
        $this->set('aiTranslationLanguages', $this->Language->find('list', array('recursive' => -1, 'order' => array('Language.name' => 'ASC'))));
        if (!$this->Session->read('Languages.token')) $this->Session->write('Languages.token', bin2hex(openssl_random_pseudo_bytes(32)));
        $this->set('languageToken', $this->Session->read('Languages.token'));
        if ($this->request->is('post')) {
            $token = isset($this->request->data['token']) ? $this->request->data['token'] : '';
            if (!is_string($token) || !hash_equals($this->Session->read('Languages.token'), $token)) throw new ForbiddenException(__('Reload the page and try again.'));
        }
    }
    public function beforeRender() { $this->set('menus', array()); }
    private function _translationOwner() {
        return (string)$this->Session->read('User.company_id') . ':' . (string)$this->Session->read('User.id');
    }
    private function _translationReply($data, $error = null) {
        $this->autoRender = false;
        $this->response->type('json');
        $this->response->header('Cache-Control', 'no-store');
        if ($error !== null) $this->response->statusCode(422);
        $this->response->body(json_encode($error === null ? array('success' => true, 'data' => $data) : array('success' => false, 'message' => $error)));
        return $this->response;
    }
    public function translation_start() {
        $this->request->onlyAllow('post');
        try {
            if (Configure::read('AI.ai_enabled') !== true) throw new RuntimeException(__('AI functions are disabled.'));
            $id = isset($this->request->data['language_id']) ? $this->request->data['language_id'] : '';
            $language = is_scalar($id) ? $this->Language->find('first', array('recursive' => -1, 'conditions' => array('Language.id' => $id))) : array();
            if (!$language) throw new RuntimeException(__('Select an available language.'));
            if (empty($this->request->data['share_pot']) || $this->request->data['share_pot'] !== '1') throw new RuntimeException(__('Select Share application POT file with AI.'));
            $owner = $this->_translationOwner();
            if (session_id()) session_write_close();
            set_time_limit(0);
            $extractor = new ApplicationPotExtractor();
            $path = $extractor->generate();
            $jobs = new CatalogTranslationJob();
            $progress = $jobs->start($path, $language['Language'], $owner);
            $progress['extraction_warnings'] = count($extractor->warnings);
            return $this->_translationReply($progress);
        } catch (Exception $e) { return $this->_translationReply(null, $e->getMessage()); }
    }
    public function translation_step() {
        $this->request->onlyAllow('post');
        $owner = $this->_translationOwner();
        $id = isset($this->request->data['job']) ? $this->request->data['job'] : '';
        $jobs = new CatalogTranslationJob();
        try {
            if (Configure::read('AI.ai_enabled') !== true) throw new RuntimeException(__('AI functions are disabled.'));
            // Release the session while the configured model works so other requests can proceed.
            if (session_id()) session_write_close();
            set_time_limit(0);
            $data = $jobs->step($id, $owner);
            $data['download'] = Router::url(array('controller' => 'languages', 'action' => 'translation_download', $data['job']));
            return $this->_translationReply($data);
        } catch (Exception $e) {
            try {
                $jobs->resultPath($id, $owner);
                $this->autoRender = false;
                $this->response->type('json'); $this->response->statusCode(422);
                $this->response->body(json_encode(array('success' => false, 'message' => $e->getMessage(),
                    'download' => Router::url(array('controller' => 'languages', 'action' => 'translation_download', $id)))));
                return $this->response;
            } catch (Exception $notReady) { return $this->_translationReply(null, $e->getMessage()); }
        }
    }
    public function translation_download($id = null) {
        $jobs = new CatalogTranslationJob();
        $path = $jobs->resultPath($id, $this->_translationOwner());
        $this->response->file($path, array('download' => true, 'name' => 'translated-default.po'));
        return $this->response;
    }

    public function index() {
        App::uses('LanguageReference', 'Lib');
        $languageCodes = LanguageReference::all();
        $this->set('languageCodes', $languageCodes);
        if ($this->request->is('post')) {
            $row = isset($this->request->data['Language']) ? $this->request->data['Language'] : array();
            if (!is_array($row)) throw new BadRequestException(__('Invalid language.'));
            $row = array_intersect_key($row, array_flip(array('name', 'code', 'plural_forms')));
            if ((isset($row['name']) && !is_string($row['name'])) || (isset($row['code']) && !is_string($row['code']))) throw new BadRequestException(__('Invalid language.'));
            $row['code'] = isset($row['code']) ? strtolower(trim($row['code'])) : '';
            $row['enabled'] = 1;
            $row['plural_forms'] = isset($row['plural_forms']) && is_string($row['plural_forms']) ? trim($row['plural_forms']) : '';
            if ($row['plural_forms'] === '') $row['plural_forms'] = LanguageReference::pluralForms($row['code']);
            $catalog = new TranslationCatalog();
            $this->Language->create(); $this->Language->set($row);
            if ($this->Language->validates()) {
                try {
                    $entries = $catalog->entries($row['code']);
                    $catalog->save($row['code'], $entries, $row['plural_forms']);
                    if (!$this->Language->save(null, array('validate' => false))) throw new RuntimeException(__('Could not save language.'));
                    return $this->redirect(array('action' => 'edit', $this->Language->id));
                } catch (Exception $e) { $this->Session->setFlash($e->getMessage()); }
            }
        }
        $this->set('languages', $this->Language->find('all', array('recursive' => -1, 'order' => array('Language.name' => 'ASC'))));
    }
    public function edit($id = null) {
        $record = $this->Language->find('first', array('recursive' => -1, 'conditions' => array('Language.id' => $id)));
        if (!$record) throw new NotFoundException(__('Invalid language.'));
        $language = $record['Language']; $catalog = new TranslationCatalog();
        $entries = $catalog->entries($language['code']);
        preg_match('/nplurals=([1-6])/', $language['plural_forms'], $pluralMatch);
        $pluralCount = isset($pluralMatch[1]) ? (int)$pluralMatch[1] : 2;
        if ($this->request->is('post')) {
            try {
                if (!isset($this->request->data['version']) || $this->request->data['version'] !== $catalog->version($language['code'])) throw new RuntimeException(__('The catalog changed. Reload before saving.'));
                $settings = isset($this->request->data['Language']) ? $this->request->data['Language'] : array();
                $forms = isset($settings['plural_forms']) && is_string($settings['plural_forms']) ? trim($settings['plural_forms']) : $language['plural_forms'];
                if (!preg_match('/^nplurals=([1-6]);\s*plural=[n0-9\s()!<>=&|%?:+*\/\-]+;$/D', $forms, $pluralMatch)) throw new RuntimeException(__('Invalid plural forms.'));
                $pluralCount = (int)$pluralMatch[1];
                $name = isset($settings['name']) && is_string($settings['name']) ? trim($settings['name']) : $language['name'];
                if ($name === '' || strlen($name) > 100) throw new RuntimeException(__('Enter a language name.'));
                $changes = isset($this->request->data['catalog_changes']) ? $this->request->data['catalog_changes'] : null;
                if (!is_string($changes)) throw new RuntimeException(__('Invalid translation values.'));
                $changes = json_decode($changes, true);
                if (json_last_error() !== JSON_ERROR_NONE || !is_array($changes)) throw new RuntimeException(__('Invalid translation values.'));
                $entries = $catalog->update($entries, $changes, $pluralCount);
                $catalog->save($language['code'], $entries, $forms);
                $enabled = $language['code'] === 'eng' || (array_key_exists('enabled', $settings) ? !empty($settings['enabled']) : !empty($language['enabled'])) ? 1 : 0;
                $this->Language->id = $language['id'];
                if (!$this->Language->save(array('Language' => array('id' => $language['id'], 'name' => $name, 'enabled' => $enabled, 'plural_forms' => $forms)), array('validate' => false, 'fieldList' => array('name', 'enabled', 'plural_forms')))) throw new RuntimeException(__('Could not save language.'));
                $this->Session->setFlash(__('Translations saved.'));
                return $this->redirect(array('action' => 'edit', $id, '?' => $this->request->query));
            } catch (Exception $e) { $this->Session->setFlash($e->getMessage()); }
        }
        $total = count($entries);
        $version = $catalog->version($language['code']);
        $this->set(compact('language', 'entries', 'version', 'total', 'pluralCount'));
    }
}
