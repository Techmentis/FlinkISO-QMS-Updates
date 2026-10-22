<div class="panel panel-default"><div class="panel-heading"><h3><?php echo __('Edit translations'); ?>: <?php echo h($language['name']); ?></h3></div>
<div class="panel-body">
<?php echo $this->Session->flash(); ?>
<p><code><?php echo h('app/Locale/' . $language['code'] . '/LC_MESSAGES/default.po'); ?></code></p>
<p><?php echo __('Leave a translation empty to use English. Keep placeholders such as %s and {:page} unchanged.'); ?></p>
<form method="get" action="<?php echo h($this->Html->url(array('action' => 'edit', $language['id']))); ?>">
<label for="catalog-search"><?php echo __('Search'); ?></label>
<input id="catalog-search" class="form-control" name="q" value="<?php echo h($query); ?>">
<button class="btn btn-default" type="submit"><?php echo __('Search'); ?></button>
</form>
<form method="post" action="<?php echo h($this->Html->url(array('action' => 'edit', $language['id'], '?' => array('q' => $query, 'page' => $page)))); ?>">
<input type="hidden" name="data[token]" value="<?php echo h($languageToken); ?>">
<input type="hidden" name="data[version]" value="<?php echo h($version); ?>">
<div class="form-group"><label for="language-name"><?php echo __('Name'); ?></label>
<input id="language-name" class="form-control" name="data[Language][name]" maxlength="100" required value="<?php echo h($language['name']); ?>"></div>
<div class="form-group"><label for="plural-forms"><?php echo __('Plural forms'); ?></label>
<input id="plural-forms" class="form-control" name="data[Language][plural_forms]" required value="<?php echo h($language['plural_forms']); ?>"></div>
<input type="hidden" name="data[Language][enabled]" value="0">
<label><input type="checkbox" name="data[Language][enabled]" value="1" <?php echo !empty($language['enabled']) ? 'checked' : ''; ?> <?php echo $language['code'] === 'eng' ? 'disabled' : ''; ?>> <?php echo __('Available to users'); ?></label>
<p><?php echo __('The language code and catalog folder remain fixed after creation.'); ?></p>

<?php foreach ($entries as $key => $entry): ?>
<div class="form-group" style="margin-top:20px">
<label for="translation-<?php echo h($key); ?>-0"><?php echo h($entry['msgid']); ?></label>
<?php if (isset($entry['msgctxt'])): ?><p><?php echo h($entry['msgctxt']); ?></p><?php endif; ?>
<?php if (isset($entry['msgid_plural'])): ?><p><?php echo h($entry['msgid_plural']); ?></p><?php endif; ?>
<?php $count = isset($entry['msgid_plural']) ? $pluralCount : 1; for ($i = 0; $i < $count; $i++):
$value = isset($entry['msgid_plural']) ? (isset($entry['msgstr[' . $i . ']']) ? $entry['msgstr[' . $i . ']'] : '') : (isset($entry['msgstr']) ? $entry['msgstr'] : ''); ?>
<?php if ($count > 1): ?><label for="translation-<?php echo h($key); ?>-<?php echo $i; ?>"><?php echo __('Plural form'); ?> <?php echo $i; ?></label><?php endif; ?>
<textarea class="form-control" rows="2" id="translation-<?php echo h($key); ?>-<?php echo $i; ?>" name="data[translations][<?php echo h($key); ?>][<?php echo $i; ?>]"><?php echo h($value); ?></textarea>
<?php endfor; ?>
</div>
<?php endforeach; ?>
<button type="submit" class="btn btn-primary"><?php echo __('Save translations'); ?></button>
</form>
<p><?php echo (int)$page; ?> / <?php echo (int)$pages; ?> (<?php echo (int)$total; ?>)</p>
<?php if ($page > 1) echo $this->Html->link(__('Previous'), array('action' => 'edit', $language['id'], '?' => array('q' => $query, 'page' => $page - 1)), array('class' => 'btn btn-default')); ?>
<?php if ($page < $pages) echo $this->Html->link(__('Next'), array('action' => 'edit', $language['id'], '?' => array('q' => $query, 'page' => $page + 1)), array('class' => 'btn btn-default')); ?>
<?php echo $this->Html->link(__('Languages'), array('action' => 'index'), array('class' => 'btn btn-default')); ?>
</div></div>
