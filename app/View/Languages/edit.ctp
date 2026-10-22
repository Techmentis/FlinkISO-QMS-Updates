<style>
#po-editor .po-toolbar { display:flex; gap:12px; align-items:center; flex-wrap:wrap; margin-bottom:15px; }
#po-editor .po-toolbar input { flex:1; min-width:220px; }
#po-editor .po-scroll { max-height:65vh; overflow:auto; border:1px solid #ddd; }
#po-editor table { width:100%; table-layout:fixed; margin:0; }
#po-editor th { position:sticky; top:0; z-index:1; background:#f5f5f5; }
#po-editor th, #po-editor td { width:50%; padding:12px; vertical-align:top; }
#po-editor .po-source { white-space:pre-wrap; overflow-wrap:anywhere; }
#po-editor textarea { resize:vertical; min-height:60px; }
#po-editor .po-footer { display:flex; gap:12px; align-items:center; margin-top:15px; flex-wrap:wrap; }
#po-editor tr.po-changed td { background:#fff9e8; }
</style>
<div class="panel panel-default" id="po-editor">
<div class="panel-heading"><h3><?php echo __('Update PO file'); ?>: <?php echo h($language['name']); ?></h3></div>
<div class="panel-body">
<?php echo $this->Session->flash(); ?>
<p><?php echo __('Leave a translation empty to use English. Keep placeholders such as %s and {:page} unchanged.'); ?></p>
<div class="po-toolbar">
<label for="catalog-search"><?php echo __('Search'); ?></label>
<input id="catalog-search" class="form-control" type="search" value="" placeholder="<?php echo h(__('Search original text or translation')); ?>">
<span id="po-visible-count" aria-live="polite"><?php echo (int)$total; ?> / <?php echo (int)$total; ?></span>
</div>
<form id="po-file-form" method="post" action="<?php echo h($this->Html->url(array('action' => 'edit', $language['id']))); ?>">
<input type="hidden" name="data[token]" value="<?php echo h($languageToken); ?>">
<input type="hidden" name="data[version]" value="<?php echo h($version); ?>">
<input type="hidden" id="po-changes" name="data[catalog_changes]" value="{}">
<div class="po-scroll">
<table class="table table-bordered">
<thead><tr><th scope="col"><?php echo __('Original text'); ?></th><th scope="col"><?php echo __('Translation'); ?> — <?php echo h($language['name']); ?></th></tr></thead>
<tbody>
<?php foreach ($entries as $key => $entry): ?>
<tr class="po-entry">
<td class="po-source"><label for="translation-<?php echo h($key); ?>-0"><?php echo h($entry['msgid']); ?></label><?php if (isset($entry['msgctxt'])): ?><div class="text-muted"><?php echo h($entry['msgctxt']); ?></div><?php endif; ?><?php if (isset($entry['msgid_plural'])): ?><div><?php echo h($entry['msgid_plural']); ?></div><?php endif; ?></td>
<td>
<?php $count = isset($entry['msgid_plural']) ? $pluralCount : 1; for ($i = 0; $i < $count; $i++):
$value = isset($entry['msgid_plural']) ? (isset($entry['msgstr[' . $i . ']']) ? $entry['msgstr[' . $i . ']'] : '') : (isset($entry['msgstr']) ? $entry['msgstr'] : ''); ?>
<?php if ($count > 1): ?><label for="translation-<?php echo h($key); ?>-<?php echo $i; ?>"><?php echo __('Plural form'); ?> <?php echo $i; ?></label><?php endif; ?>
<textarea class="form-control po-translation" rows="2" id="translation-<?php echo h($key); ?>-<?php echo $i; ?>" data-key="<?php echo h($key); ?>" data-plural="<?php echo $i; ?>" aria-label="<?php echo h(__('Translation') . ': ' . $entry['msgid'] . ($count > 1 ? ' (' . $i . ')' : '')); ?>"><?php echo h($value); ?></textarea>
<?php endfor; ?>
</td>
</tr>
<?php endforeach; ?>
</tbody></table>
<p id="po-no-results" class="text-center" style="display:none;padding:20px"><?php echo __('No results found'); ?></p>
</div>
<div class="po-footer">
<button id="po-save" type="submit" class="btn btn-primary" disabled><?php echo __('Save'); ?></button>
<span id="po-unsaved" role="status" aria-live="polite"></span>
<?php echo $this->Html->link(__('Languages'), array('action' => 'index'), array('class' => 'btn btn-default')); ?>
</div>
<noscript><p><?php echo __('Enable JavaScript to edit this catalog.'); ?></p></noscript>
</form>
</div></div>
<script>
(function () {
    var form = document.getElementById('po-file-form');
    var search = document.getElementById('catalog-search');
    var fields = Array.prototype.slice.call(form.querySelectorAll('.po-translation'));
    var rows = Array.prototype.slice.call(form.querySelectorAll('.po-entry'));
    var initial = fields.map(function (field) { return field.value; });
    var dirty = false;
    var warning = <?php echo json_encode(__('You have unsaved translations. Leave this page?')); ?>;
    var changedMessage = <?php echo json_encode(__('Unsaved changes')); ?>;
    function filter() {
        var query = search.value.toLocaleLowerCase().trim(), visible = 0;
        rows.forEach(function (row) {
            var text = row.querySelector('.po-source').textContent;
            Array.prototype.forEach.call(row.querySelectorAll('textarea'), function (field) { text += ' ' + field.value; });
            var matches = text.toLocaleLowerCase().indexOf(query) !== -1;
            row.hidden = !matches;
            if (matches) visible++;
        });
        document.getElementById('po-visible-count').textContent = visible + ' / ' + rows.length;
        document.getElementById('po-no-results').style.display = visible ? 'none' : 'block';
    }
    function collect() {
        var changes = {}, count = 0;
        rows.forEach(function (row) { row.classList.remove('po-changed'); });
        fields.forEach(function (field, index) {
            if (field.value === initial[index]) return;
            var key = field.getAttribute('data-key');
            if (!changes[key]) changes[key] = {};
            changes[key][field.getAttribute('data-plural')] = field.value;
            field.closest('tr').classList.add('po-changed');
            count++;
        });
        dirty = count > 0;
        document.getElementById('po-unsaved').textContent = dirty ? changedMessage + ': ' + count : '';
        return changes;
    }
    form.addEventListener('input', function (event) {
        if (event.target.classList.contains('po-translation')) collect();
    });
    search.addEventListener('input', filter);
    form.addEventListener('submit', function () {
        // One JSON field avoids PHP's max_input_vars limit on large catalogs.
        document.getElementById('po-changes').value = JSON.stringify(collect());
        dirty = false;
    });
    window.addEventListener('beforeunload', function (event) {
        if (dirty) { event.preventDefault(); event.returnValue = warning; }
    });
    document.getElementById('po-save').disabled = false;
}());
</script>
