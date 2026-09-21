<?php
$aiController = $this->request->params['controller'];
$aiAction = $this->request->params['action'];
$aiNamed = isset($this->request->params['named']) ? $this->request->params['named'] : array();
$aiCustomTableId = isset($aiNamed['custom_table_id']) ? $aiNamed['custom_table_id'] : '';
$aiQcDocumentId = isset($aiNamed['qc_document_id']) ? $aiNamed['qc_document_id'] : '';
$aiRecordId = !empty($this->request->params['pass']) ? $this->request->params['pass'][0] : '';
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

  <div id="fi-ai-messages" class="fi-ai-messages" aria-live="polite">
    <div id="fi-ai-welcome" class="fi-ai-message fi-ai-message-assistant">
      <div class="fi-ai-message-icon" aria-hidden="true"><i class="fa fa-magic"></i></div>
      <div class="fi-ai-message-content">
        <strong><?php echo __('How can I help?'); ?></strong>
        <p><?php echo $aiController === 'qc_documents'
          ? __('Ask me to understand a document and prepare a FlinkISO form or module.')
          : __('Ask me to create, review, or update fields for this form.'); ?></p>
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

    <div class="fi-ai-suggestions" aria-label="<?php echo __('Suggested requests'); ?>">
      <?php if ($aiController === 'qc_documents') { ?>
        <button type="button" class="fi-ai-suggestion" data-prompt="Create a FlinkISO form from this document."><i class="fa fa-wpforms"></i><span><?php echo __('Create a form from this document'); ?></span></button>
        <button type="button" class="fi-ai-suggestion" data-prompt="Identify the form fields, choices, and validation rules in this document."><i class="fa fa-list-alt"></i><span><?php echo __('Identify fields and rules'); ?></span></button>
        <button type="button" class="fi-ai-suggestion" data-prompt="Summarize the requirements in this document before creating anything."><i class="fa fa-file-text-o"></i><span><?php echo __('Summarize requirements'); ?></span></button>
      <?php } else { ?>
        <button type="button" class="fi-ai-suggestion" data-prompt="Add a new field."><i class="fa fa-plus-square-o"></i><span><?php echo __('Add a field to this form'); ?></span></button>
        <button type="button" class="fi-ai-suggestion" data-prompt="Review this form and suggest missing fields or validation rules."><i class="fa fa-check-square-o"></i><span><?php echo __('Review this form'); ?></span></button>
        <?php if ($aiCustomTableId !== '') { ?>
          <a class="fi-ai-suggestion fi-ai-builder-suggestion" href="<?php echo h(Router::url(array('controller' => 'custom_tables', 'action' => 'recreate', $aiCustomTableId), true)); ?>"><i class="fa fa-wrench"></i><span><?php echo __('Go to Form Builder'); ?></span></a>
        <?php } ?>
      <?php } ?>
    </div>
  </div>

  <div class="fi-ai-composer-wrap">
    <form id="fi-ai-form" class="fi-ai-form" novalidate>
      <textarea id="fi-ai-prompt" rows="2" maxlength="4000" placeholder="<?php echo __('Describe what you want FlinkISO to create or update...'); ?>" aria-label="<?php echo __('Message FlinkISO AI'); ?>"></textarea>
      <div class="fi-ai-composer-actions">
        <?php if ($aiQcDocumentId !== '') { ?>
          <label class="fi-ai-document-option" for="fi-ai-send-current-document" title="<?php echo $aiController === 'qc_documents'
            ? __('When selected, the current Quality Document is sent to the FlinkISO API for this AI request.')
            : __('When selected, the Quality Document linked to this form is sent to the FlinkISO API as reference material.'); ?>">
            <input type="checkbox" id="fi-ai-send-current-document" value="1">
            <span><?php echo $aiController === 'qc_documents'
              ? __('Send current document')
              : __('Use linked QC document'); ?></span>
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
    <p class="fi-ai-disclaimer"><?php echo __('AI changes are validated before FlinkISO rebuilds the form.'); ?></p>
  </div>
</aside>
<?php if ($aiFieldContext) { ?>
<script id="fi-ai-field-context" type="application/json"><?php echo json_encode($aiFieldContext, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?></script>
<script id="fi-ai-tab-context" type="application/json"><?php echo json_encode(array_values($aiTabContext), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?></script>
<?php } ?>
