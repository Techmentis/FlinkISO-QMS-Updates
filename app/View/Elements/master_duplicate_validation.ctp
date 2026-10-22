<?php
$duplicateFields = isset($fields) && is_array($fields) ? $fields : array();
$duplicateRecordSelector = !empty($recordIdSelector) ? $recordIdSelector : '';
$duplicateCheckUrl = Router::url(array(
    'controller' => $this->request->params['controller'],
    'action' => 'check_duplicate'
), true);
?>
<script>
$(function () {
    var fields = <?php echo json_encode($duplicateFields); ?>;
    var recordSelector = <?php echo json_encode($duplicateRecordSelector); ?>;

    $.each(fields, function (selector, options) {
        var $field = $(selector);
        if (!$field.length || !$field.closest('form').data('validator')) return;
        var originalPlaceholder = $field.attr('placeholder');
        var checkedValue;

        $field.on('input.masterDuplicate', function () {
            if ($field.data('duplicatePlaceholder')) {
                if (originalPlaceholder === undefined) $field.removeAttr('placeholder');
                else $field.attr('placeholder', originalPlaceholder);
                $field.removeData('duplicatePlaceholder');
            }
        });

        $field.rules('add', {
            remote: {
                url: <?php echo json_encode($duplicateCheckUrl); ?>,
                type: 'post',
                dataType: 'json',
                dataFilter: function (response) {
                    // Show the warning inside text fields without storing the
                    // warning as a value that could be submitted as record data.
                    if ($.trim(response) === 'false' && $field.is('input, textarea') &&
                        $.trim($field.val() || '') === checkedValue) {
                        $field.val('').attr('placeholder', checkedValue + <?php echo json_encode(__(' already exist.'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>)
                            .data('duplicatePlaceholder', true);
                    }
                    return response;
                },
                data: {
                    field: function () { return options.field; },
                    value: function () {
                        checkedValue = $.trim($field.val() || '');
                        return checkedValue;
                    },
                    id: function () { return recordSelector ? ($(recordSelector).val() || '') : ''; }
                }
            },
            messages: {
                remote: options.message || <?php echo json_encode(__('This value already exists.'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>
            }
        });
    });
});
</script>
