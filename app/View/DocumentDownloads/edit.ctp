<div class="documentDownloads form">
	<?php echo $this->Form->create('DocumentDownload'); ?>
	<fieldset>
		<legend><?php echo __('Edit Document Download'); ?></legend>
		<?php
		echo $this->Form->input('id');
		echo $this->Form->input('sr_no');
		echo $this->Form->input('qc_document_id');
		echo $this->Form->input('custom_table_id');
		echo $this->Form->input('reccord_id');
		echo $this->Form->input('download_by');
		echo $this->Form->input('process_definition');
		echo $this->Form->input('signature');
		echo $this->Form->input('digital_signature');
		echo $this->Form->input('add_cover_page');
		echo $this->Form->input('add_parent_records');
		echo $this->Form->input('add_child_records');
		echo $this->Form->input('add_linked_form_records');
		echo $this->Form->input('created_by');
		echo $this->Form->input('modified_by');
		echo $this->Form->input('approved_by');
		echo $this->Form->input('prepared_by');
		echo $this->Form->input('soft_delete');
		echo $this->Form->input('branchid');
		echo $this->Form->input('departmentid');
		echo $this->Form->input('company_id');
		?>
	</fieldset>
	<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $this->Form->value('DocumentDownload.id')), array('confirm' => __('Are you sure you want to delete # %s?', $this->Form->value('DocumentDownload.id')))); ?></li>
		<li><?php echo $this->Html->link(__('List Document Downloads'), array('action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('List Qc Documents'), array('controller' => 'qc_documents', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Qc Document'), array('controller' => 'qc_documents', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Custom Tables'), array('controller' => 'custom_tables', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Custom Table'), array('controller' => 'custom_tables', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Companies'), array('controller' => 'companies', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Company'), array('controller' => 'companies', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Users'), array('controller' => 'users', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Created By'), array('controller' => 'users', 'action' => 'add')); ?> </li>
	</ul>
</div>
