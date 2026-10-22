<div class="panel panel-default"><div class="panel-heading"><h3><?php echo __('Languages'); ?></h3></div>
<div class="panel-body">
<?php echo $this->Session->flash(); ?>
<p><?php echo __('Add a language, then translate its catalog. The POT file is the shared source template; each language has its own PO file.'); ?></p>
<table class="table"><thead><tr><th><?php echo __('Name'); ?></th><th><?php echo __('Language code'); ?></th><th><?php echo __('Actions'); ?></th></tr></thead><tbody>
<?php foreach ($languages as $record): $item = $record['Language']; ?>
<tr><td><?php echo h($item['name']); ?></td><td><?php echo h($item['code']); ?></td><td><?php echo $this->Html->link(__('Edit translations'), array('action' => 'edit', $item['id'])); ?></td></tr>
<?php endforeach; ?>
</tbody></table>
<h4><?php echo __('Add language'); ?></h4>
<?php echo $this->Form->create('Language', array('url' => array('controller' => 'languages', 'action' => 'index'))); ?>
<input type="hidden" name="data[token]" value="<?php echo h($languageToken); ?>">
<?php echo $this->Form->input('name', array('label' => __('Name'), 'class' => 'form-control')); ?>
<?php echo $this->Form->input('code', array('label' => __('Language code'), 'class' => 'form-control', 'maxlength' => 3)); ?>
<p><?php echo __('Use a three-letter CakePHP language code, for example deu.'); ?></p>
<?php echo $this->Form->input('plural_forms', array('label' => __('Plural forms'), 'class' => 'form-control', 'default' => 'nplurals=2; plural=(n != 1);')); ?>
<?php echo $this->Form->end(array('label' => __('Add language'), 'class' => 'btn btn-primary')); ?>
</div></div>
