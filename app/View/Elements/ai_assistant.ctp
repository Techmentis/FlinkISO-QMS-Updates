<?php
$aiController = $this->request->params['controller'];
$aiAction = $this->request->params['action'];
$aiNamed = isset($this->request->params['named']) ? $this->request->params['named'] : array();
$aiCustomTableId = isset($aiNamed['custom_table_id']) ? $aiNamed['custom_table_id'] : '';
$aiQcDocumentId = isset($aiNamed['qc_document_id']) ? $aiNamed['qc_document_id'] : '';
$aiRecordId = !empty($this->request->params['pass']) ? $this->request->params['pass'][0] : '';
$aiIsGeneratedForm = strpos($aiController, 'tbl_') === 0 || strpos($aiController, 'chd_') === 0;
$aiIsFormContext = in_array($aiController, array('qc_documents', 'custom_tables'), true) || $aiIsGeneratedForm;
$aiProviderHost = strtolower((string)parse_url(trim((string)Configure::read('AI.ai_api')), PHP_URL_HOST));
$aiProviderIsLocal = $aiProviderHost === '' || in_array($aiProviderHost, array('localhost', '::1'), true);
if (!$aiProviderIsLocal && filter_var($aiProviderHost, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
  $aiProviderLong = sprintf('%u', ip2long($aiProviderHost));
  $aiProviderIsLocal = ($aiProviderLong >= sprintf('%u', ip2long('127.0.0.0')) && $aiProviderLong <= sprintf('%u', ip2long('127.255.255.255')))
    || ($aiProviderLong >= sprintf('%u', ip2long('10.0.0.0')) && $aiProviderLong <= sprintf('%u', ip2long('10.255.255.255')))
    || ($aiProviderLong >= sprintf('%u', ip2long('172.16.0.0')) && $aiProviderLong <= sprintf('%u', ip2long('172.31.255.255')))
    || ($aiProviderLong >= sprintf('%u', ip2long('192.168.0.0')) && $aiProviderLong <= sprintf('%u', ip2long('192.168.255.255')));
}
if ($aiController === 'qc_documents' && $aiQcDocumentId === '' && $aiRecordId !== '') {
  $aiQcDocumentId = $aiRecordId;
}
if ($aiController === 'qc_documents') {
  $aiContextLabel = __('Quality Documents');
} elseif (!empty($customTable['CustomTable']['name'])) {
  $aiContextLabel = $customTable['CustomTable']['name'];
} else {
  $aiContextLabel = Inflector::humanize($aiController);
}
$aiFieldContext = array();
$aiTabContext = array();
if ((strpos($aiController, 'tbl_') === 0 || strpos($aiController, 'chd_') === 0) && !empty($customTable['CustomTable']['fields'])) {
  $aiSavedFields = json_decode($customTable['CustomTable']['fields'], true);
  foreach ((array)$aiSavedFields as $aiSavedField) {
    if (empty($aiSavedField['field_name'])) continue;
    $aiLabel = !empty($aiSavedField['field_label']) ? $aiSavedField['field_label'] : Inflector::humanize($aiSavedField['field_name']);
    $aiDecodedLabel = base64_decode($aiLabel, true);
    if ($aiDecodedLabel !== false && base64_encode($aiDecodedLabel) === $aiLabel) $aiLabel = $aiDecodedLabel;
    $aiFieldContext[] = array(
      'field_name' => $aiSavedField['field_name'],
      'field_label' => $aiLabel,
      'data_type' => isset($aiSavedField['data_type']) ? $aiSavedField['data_type'] : '',
      'mandatory' => !empty($aiSavedField['mandatory']) || !empty($aiSavedField['mandetory']),
      'default_field' => !empty($aiSavedField['default_field']),
      'index_show' => !empty($aiSavedField['index_show']),
      'linked_to' => isset($aiSavedField['linked_to']) ? $aiSavedField['linked_to'] : '-1',
      'tab_name' => isset($aiSavedField['tab_name']) ? trim($aiSavedField['tab_name']) : '',
      'checkbox_layout' => isset($aiSavedField['checkbox_layout']) ? $aiSavedField['checkbox_layout'] : 'vertical',
      'options' => !empty($aiSavedField['csvoptions']) ? array_values(array_filter(array_map('trim', explode(',', $aiSavedField['csvoptions'])))) : array()
    );
    $aiTabName = isset($aiSavedField['tab_name']) ? trim($aiSavedField['tab_name']) : '';
    if ($aiTabName !== '' && $aiTabName !== '-1' && !isset($aiTabContext[$aiTabName])) {
      $aiTabContext[$aiTabName] = array(
        'name' => $aiTabName,
        'group' => isset($aiSavedField['tab_group']) ? $aiSavedField['tab_group'] : '',
        'sequence' => isset($aiSavedField['tab_sequence']) ? $aiSavedField['tab_sequence'] : ''
      );
    }
  }
}
?>
<div id="fi-ai-backdrop" class="fi-ai-backdrop" aria-hidden="true"></div>
<aside id="load_ai_container"
       class="fi-ai-sidebar"
       aria-hidden="true"
       aria-labelledby="fi-ai-title"
       data-controller="<?php echo h($aiController); ?>"
       data-action="<?php echo h($aiAction); ?>"
       data-form-ai-context="<?php echo $aiIsFormContext ? '1' : '0'; ?>"
       data-ai-provider-local="<?php echo $aiProviderIsLocal ? '1' : '0'; ?>"
       data-preview-url="<?php echo h(Router::url(array('controller' => 'ais', 'action' => 'preview'), true)); ?>"
       data-status-url="<?php echo h(Router::url(array('controller' => 'ais', 'action' => 'status'), true)); ?>"
       data-history-url="<?php echo h(Router::url(array('controller' => 'ais', 'action' => 'history'), true)); ?>"
       data-cancel-url="<?php echo h(Router::url(array('controller' => 'ais', 'action' => 'cancel'), true)); ?>"
       data-form-builder-url="<?php echo $aiCustomTableId !== '' ? h(Router::url(array('controller' => 'custom_tables', 'action' => 'recreate', $aiCustomTableId), true)) : ''; ?>"
       data-custom-table-id="<?php echo h($aiCustomTableId); ?>"
       data-qc-document-id="<?php echo h($aiQcDocumentId); ?>"
       data-record-id="<?php echo h($aiRecordId); ?>">
  <div class="fi-ai-header">
    <div class="fi-ai-mark" aria-hidden="true"><i class="fa fa-magic"></i></div>
    <div class="fi-ai-heading">
      <h2 id="fi-ai-title"><?php echo __('FlinkISO AI'); ?></h2>
      <span class="fi-ai-context"><i class="fa fa-circle"></i> <?php echo h($aiContextLabel); ?></span>
    </div>
    <div class="fi-ai-window-actions">
      <button type="button" id="fi-ai-expand" class="fi-ai-icon-button hidden-xs" title="<?php echo __('Expand panel'); ?>" aria-label="<?php echo __('Expand panel'); ?>">
        <i class="fa fa-expand"></i>
      </button>
      <button type="button" id="fi-ai-close" class="fi-ai-icon-button" title="<?php echo __('Close'); ?>" aria-label="<?php echo __('Close FlinkISO AI'); ?>">
        <i class="fa fa-times"></i>
      </button>
    </div>
  </div>

  <div class="fi-ai-mode-switch" role="group" aria-label="<?php echo __('AI mode'); ?>">
    <button type="button" class="fi-ai-mode-button is-active" data-ai-mode="chat" aria-pressed="true">
      <i class="fa fa-comments-o" aria-hidden="true"></i> <?php echo __('Chat'); ?>
    </button>
    <button type="button" class="fi-ai-mode-button" data-ai-mode="forms" aria-pressed="false"<?php echo $aiIsFormContext ? '' : ' disabled'; ?>
            title="<?php echo $aiIsFormContext ? __('Create or modify FlinkISO forms through API V2') : __('Forms mode is available only in Quality Documents, Custom Tables, and generated forms'); ?>">
      <i class="fa fa-wpforms" aria-hidden="true"></i> <?php echo __('Forms'); ?>
    </button>
  </div>

  <div id="fi-ai-messages" class="fi-ai-messages" aria-live="polite">
    <div id="fi-ai-welcome" class="fi-ai-message fi-ai-message-assistant">
      <div class="fi-ai-message-icon" aria-hidden="true"><i class="fa fa-magic"></i></div>
      <div class="fi-ai-message-content">
        <strong><?php echo __('How can I help?'); ?></strong>
        <p id="fi-ai-welcome-text"><?php echo __('Ask me a question about QMS or using FlinkISO.'); ?></p>
      </div>
    </div>

    <div id="fi-ai-instance-busy" class="fi-ai-message fi-ai-message-system" style="display:none">
      <div class="fi-ai-message-content">
        <strong><?php echo __('AI task in progress'); ?></strong>
        <p><?php echo __('FlinkISO AI is currently processing a request for this instance.'); ?></p>
        <div class="fi-ai-history-meta" id="fi-ai-instance-busy-meta"></div>
        <button type="button" id="fi-ai-instance-stop" class="fi-ai-history-cancel" style="display:none">
          <i class="fa fa-stop" aria-hidden="true"></i> <?php echo __('Stop'); ?>
        </button>
      </div>
    </div>

    <div class="fi-ai-suggestions" data-ai-mode-content="chat" aria-label="<?php echo __('Suggested chat requests'); ?>">
      <button type="button" class="fi-ai-suggestion" data-prompt="How can FlinkISO help me manage this area?"><i class="fa fa-question-circle"></i><span><?php echo __('Help with this module'); ?></span></button>
      <button type="button" class="fi-ai-suggestion" data-prompt="Explain the QMS requirements related to this area."><i class="fa fa-book"></i><span><?php echo __('Ask a QMS question'); ?></span></button>
      <button type="button" class="fi-ai-suggestion" data-prompt="Give me practical QMS guidance for this activity."><i class="fa fa-lightbulb-o"></i><span><?php echo __('Get practical guidance'); ?></span></button>
    </div>
    <?php if ($aiIsFormContext) { ?>
    <div class="fi-ai-suggestions" data-ai-mode-content="forms" aria-label="<?php echo __('Suggested form requests'); ?>" style="display:none">
      <?php if ($aiController === 'qc_documents') { ?>
        <button type="button" class="fi-ai-suggestion" data-prompt="Create a FlinkISO form from this document."><i class="fa fa-wpforms"></i><span><?php echo __('Create a form from this document'); ?></span></button>
        <button type="button" class="fi-ai-suggestion" data-prompt="Identify the form fields, choices, and validation rules in this document."><i class="fa fa-list-alt"></i><span><?php echo __('Identify fields and rules'); ?></span></button>
        <button type="button" class="fi-ai-suggestion" data-prompt="Summarize the requirements in this document before creating anything."><i class="fa fa-file-text-o"></i><span><?php echo __('Summarize requirements'); ?></span></button>
      <?php } elseif ($aiIsGeneratedForm) { ?>
        <button type="button" class="fi-ai-suggestion" data-prompt="Add a new field."><i class="fa fa-plus-square-o"></i><span><?php echo __('Add a field to this form'); ?></span></button>
        <button type="button" class="fi-ai-suggestion" data-prompt="Review this form and suggest missing fields or validation rules."><i class="fa fa-check-square-o"></i><span><?php echo __('Review this form'); ?></span></button>
        <?php if ($aiCustomTableId !== '') { ?>
          <a class="fi-ai-suggestion fi-ai-builder-suggestion" href="<?php echo h(Router::url(array('controller' => 'custom_tables', 'action' => 'recreate', $aiCustomTableId), true)); ?>"><i class="fa fa-wrench"></i><span><?php echo __('Go to Form Builder'); ?></span></a>
        <?php } ?>
      <?php } else { ?>
        <button type="button" class="fi-ai-suggestion" data-prompt="Create a new FlinkISO form from my requirements."><i class="fa fa-plus-square-o"></i><span><?php echo __('Create a new form'); ?></span></button>
        <button type="button" class="fi-ai-suggestion" data-prompt="Help me define the fields and validation rules for a new form."><i class="fa fa-list-alt"></i><span><?php echo __('Define form requirements'); ?></span></button>
      <?php } ?>
    </div>
    <?php } ?>
  </div>

  <div class="fi-ai-composer-wrap">
    <form id="fi-ai-form" class="fi-ai-form" novalidate>
      <textarea id="fi-ai-prompt" rows="2" maxlength="4000" placeholder="<?php echo __('Ask a question about QMS or using FlinkISO...'); ?>" aria-label="<?php echo __('Message FlinkISO AI'); ?>"></textarea>
      <div class="fi-ai-composer-actions">
        <?php if ($aiQcDocumentId !== '' || $aiIsGeneratedForm) { ?>
          <label class="fi-ai-document-option" for="fi-ai-send-current-document" style="display:none" title="<?php echo $aiController === 'qc_documents'
            ? __('When selected, the current Quality Document is sent to the FlinkISO API for this AI request.')
            : ($aiIsGeneratedForm && !$aiProviderIsLocal
              ? __('When selected, the form field definition and linked Quality Document content are shared with the configured remote AI provider. User names, IDs, and email addresses are not shared.')
              : __('The local AI uses this form field definition and linked Quality Document as read-only reference material.')); ?>">
            <input type="checkbox" id="fi-ai-send-current-document" value="1">
            <span><?php echo $aiController === 'qc_documents'
              ? __('Send current document')
              : ($aiIsGeneratedForm && !$aiProviderIsLocal ? __('Share form context and linked document') : __('Use linked QC document')); ?></span>
          </label>
        <?php } ?>
        <span class="fi-ai-local-note"><i class="fa fa-shield"></i> <?php echo __('Secure AI API'); ?></span>
        <button id="fi-ai-cancel" type="button" class="fi-ai-cancel" aria-label="<?php echo __('Stop AI generation'); ?>" title="<?php echo __('Stop generation'); ?>">
          <i class="fa fa-stop"></i>
        </button>
        <button id="fi-ai-send" type="submit" class="fi-ai-send" disabled aria-label="<?php echo __('Send message'); ?>">
          <i class="fa fa-arrow-up"></i>
        </button>
      </div>
    </form>
    <p class="fi-ai-disclaimer" id="fi-ai-disclaimer"><?php echo __('AI guidance is provided by your configured AI provider.'); ?></p>
  </div>
</aside>
<?php if ($aiFieldContext) { ?>
<script id="fi-ai-field-context" type="application/json"><?php echo json_encode($aiFieldContext, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?></script>
<script id="fi-ai-tab-context" type="application/json"><?php echo json_encode(array_values($aiTabContext), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?></script>
<?php } ?>
