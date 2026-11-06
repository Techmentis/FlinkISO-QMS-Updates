<script>
(function(){
    if(window.FlinkisoAvailabilitySummary){ window.FlinkisoAvailabilitySummary.refresh(); return; }
    var words = {
        add: <?php echo json_encode(__('Add'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>,
        edit: <?php echo json_encode(__('Edit'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>,
        shown: <?php echo json_encode(__('Shown.'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>,
        hidden: <?php echo json_encode(__('Hidden.'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>,
        hiddenWithRule: <?php echo json_encode(__('Hidden, even when the condition matches.'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>,
        readonly: <?php echo json_encode(__('Read-only.'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>,
        childDocumentAdd: <?php echo json_encode(__('Not shown. Linked child documents are available on Edit only.'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>,
        shownWhen: <?php echo json_encode(__('Shown only when {field} is {values}; otherwise hidden.'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>,
        readonlyWhen: <?php echo json_encode(__('Read-only when {field} is {values}; otherwise hidden.'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>,
        or: <?php echo json_encode(__(' or '), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>,
        chooseValues: <?php echo json_encode(__('Choose at least one value to activate this condition.'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>,
        noChoices: <?php echo json_encode(__('No available values. This condition is not active.'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>
    };
    function renderRow(row){
        var mode = row.find('.tab-action-visibility, .child-action-availability').val() || 'always';
        var field = row.find('.tab-visibility-field, .child-visibility-field');
        var values = row.find('.tab-visibility-values, .child-visibility-values');
        var conditionMode = row.find('.tab-visibility-mode');
        var wantsRule = !!field.val() && (!conditionMode.length || conditionMode.val() === 'field');
        var labels = values.find('option:selected').map(function(){ return $.trim($(this).text()); }).get();
        var hasRule = wantsRule && labels.length > 0;
        var fieldLabel = $.trim(field.find('option:selected').text());
        var summary = row.find('.availability-rule-summary');
        if(!summary.length){
            summary = $('<div class="availability-rule-summary" aria-live="polite"></div>');
            values.closest('td').append(summary);
        }
        summary.empty();
        $.each(['add','edit'], function(_, action){
            var hidden = mode === 'hide_both' || mode === 'hide_' + action;
            var readonly = mode === 'readonly_both' || mode === 'readonly_' + action;
            var text = hidden ? (hasRule ? words.hiddenWithRule : words.hidden)
                : hasRule ? (readonly ? words.readonlyWhen : words.shownWhen).replace('{field}', fieldLabel).replace('{values}', labels.join(words.or))
                : readonly ? words.readonly : words.shown;
            if(action === 'add' && row.attr('data-tab-type') === 'child_document') text = words.childDocumentAdd;
            $('<div></div>').append($('<strong></strong>').text(words[action] + ': ')).append($('<span></span>').text(text)).appendTo(summary);
        });
        if(wantsRule && !labels.length) $('<div class="availability-rule-hint"></div>').text(values.find('option').length ? words.chooseValues : words.noChoices).appendTo(summary);
    }
    function refresh(){ $('#tab-configuration-table tbody tr, #child-form-availability-table tbody tr').each(function(){ renderRow($(this)); }); }
    window.FlinkisoAvailabilitySummary = {refresh:refresh};
    $(document).on('change.availabilityRuleSummary', '#tab-configuration-table select, #child-form-availability-table select', function(){ renderRow($(this).closest('tr')); });
    if(!$('#availability-rule-summary-style').length) $('<style id="availability-rule-summary-style">.availability-rule-summary{font-size:13px;line-height:1.5;color:#444;margin-top:8px}.availability-rule-hint{color:#8a6d3b;margin-top:4px}</style>').appendTo('head');
    $(refresh);
})();
</script>
