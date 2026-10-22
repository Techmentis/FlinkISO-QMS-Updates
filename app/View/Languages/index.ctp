<div class="panel panel-default"><div class="panel-heading"><h3><?php echo __('Languages'); ?></h3></div>
<div class="panel-body">
<?php echo $this->Session->flash(); ?>
<?php if (Configure::read('AI.ai_enabled') === true): ?>
<p><button type="button" class="btn btn-primary" id="open-language-ai"><i class="fa fa-language" aria-hidden="true"></i> <?php echo __('Translate PO file with AI'); ?></button></p>
<script>
document.addEventListener('click', function (event) {
    var button = event.target.closest('#open-language-ai');
    if (button) { var ai = document.getElementById('ask_ai_icon'); if (ai) ai.click(); }
});
</script>
<?php endif; ?>

<p><?php echo __('Add a language, then translate its catalog. The POT file is the shared source template; each language has its own PO file.'); ?></p>
<table class="table"><thead><tr><th><?php echo __('Name'); ?></th><th><?php echo __('Language code'); ?></th><th><?php echo __('Actions'); ?></th></tr></thead><tbody>
<?php foreach ($languages as $record): $item = $record['Language']; ?>
<tr><td><?php echo h($item['name']); ?></td><td><?php echo h($item['code']); ?></td><td><?php echo $this->Html->link(__('Update PO file'), array('action' => 'edit', $item['id'])); ?></td></tr>
<?php endforeach; ?>
</tbody></table>
<h4><?php echo __('Add language'); ?></h4>
<?php echo $this->Form->create('Language', array('url' => array('controller' => 'languages', 'action' => 'index'), 'id' => 'add-language')); ?>
<input type="hidden" name="data[token]" value="<?php echo h($languageToken); ?>">
<?php echo $this->Form->input('name', array('label' => __('Name'), 'class' => 'form-control', 'id' => 'language-name')); ?>
<?php echo $this->Form->input('code', array('label' => __('Language code'), 'class' => 'form-control', 'maxlength' => 3, 'id' => 'language-code')); ?>
<p><?php echo __('Use a unique three-letter language code, for example deu or mar.'); ?> <a href="#language-code-reference"><?php echo __('Choose from the language-code table below.'); ?></a></p>
<div class="help-block" id="plural-forms-help">
<p><?php echo __('Plural forms tells the application which translation to use for a number, such as 1 document or 2 documents.'); ?></p>
<p><?php echo __('For English and German, keep the default rule: two forms, singular when the number is 1 and plural otherwise. Other languages may need a different rule.'); ?></p>
<p><code>nplurals=2; plural=(n != 1);</code> — <?php echo __('nplurals is the number of forms; plural selects the form using the number n. This applies only to messages that contain singular and plural translations.'); ?></p>
</div>
<?php echo $this->Form->input('plural_forms', array('label' => __('Plural forms'), 'class' => 'form-control', 'default' => 'nplurals=2; plural=(n != 1);', 'aria-describedby' => 'plural-forms-help', 'id' => 'language-plural-forms', 'required' => true)); ?>
<p id="language-plural-status" class="help-block" role="status"></p>
<?php echo $this->Form->end(array('label' => __('Add language'), 'class' => 'btn btn-primary')); ?>
<section id="language-code-reference" style="margin-top:24px">
<h4><?php echo __('Language-code reference'); ?></h4>
<p><?php echo __('This table is a reference. You can also enter another unique three-letter code. Use this code fills the language name, code and available plural rule above.'); ?></p>
<label for="language-code-search"><?php echo __('Search languages or codes'); ?></label>
<input type="search" id="language-code-search" class="form-control" style="margin-bottom:12px">
<div class="table-responsive" style="max-height:360px;overflow:auto">
<table class="table table-bordered table-striped" id="language-code-table">
<thead><tr><th scope="col"><?php echo __('Language'); ?></th><th scope="col"><?php echo __('Language code'); ?></th><th scope="col"><?php echo __('Plural forms'); ?></th><th scope="col"><?php echo __('Actions'); ?></th></tr></thead>
<tbody>
<?php foreach ($languageCodes as $code => $reference): $name = $reference['name']; $rule = $reference['plural_forms']; ?>
<tr><td><?php echo h($name); ?></td><td><code><?php echo h($code); ?></code></td><td><?php if ($rule !== ''): ?><code><?php echo h($rule); ?></code><?php else: ?><?php echo __('Enter a plural rule manually'); ?><?php endif; ?></td><td><button type="button" class="btn btn-default btn-sm language-code-select" data-code="<?php echo h($code); ?>" data-name="<?php echo h($name); ?>" data-plural-forms="<?php echo h($rule); ?>"><?php echo __('Use this code'); ?></button></td></tr>
<?php endforeach; ?>
</tbody></table>
</div>
</section>
<script>
(function () {
    var table = document.getElementById('language-code-table');
    var codeField = document.getElementById('language-code');
    var pluralField = document.getElementById('language-plural-forms');
    var rules = <?php echo json_encode(array_map(function ($row) { return $row['plural_forms']; }, $languageCodes), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
    var automaticText = <?php echo json_encode(__('The plural rule was filled automatically. You can edit it if needed.')); ?>;
    var manualText = <?php echo json_encode(__('No plural rule is listed for this code. Enter the rule for your language.')); ?>;
    function fillPlural(rule) {
        pluralField.value = rule;
        document.getElementById('language-plural-status').textContent = rule ? automaticText : manualText;
    }
    codeField.addEventListener('input', function () {
        var code = this.value.toLowerCase().trim();
        fillPlural(Object.prototype.hasOwnProperty.call(rules, code) ? rules[code] : '');
    });
    document.getElementById('language-code-search').addEventListener('input', function () {
        var query = this.value.toLocaleLowerCase().trim();
        Array.prototype.forEach.call(table.querySelectorAll('tbody tr'), function (row) {
            row.hidden = row.cells[0].textContent.toLocaleLowerCase().indexOf(query) === -1 && row.cells[1].textContent.toLocaleLowerCase().indexOf(query) === -1;
        });
    });
    table.addEventListener('click', function (event) {
        var button = event.target.closest('.language-code-select');
        if (!button) return;
        codeField.value = button.getAttribute('data-code');
        fillPlural(button.getAttribute('data-plural-forms'));
        var name = document.getElementById('language-name');
        name.value = button.getAttribute('data-name');
        name.focus();
        document.getElementById('add-language').scrollIntoView({block: 'start'});
    });
}());
</script>
</div></div>
