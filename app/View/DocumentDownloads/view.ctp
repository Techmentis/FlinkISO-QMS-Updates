<div class="documentDownloads view">
	<h2><?php echo __('Document Download'); ?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($documentDownload['DocumentDownload']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Sr No'); ?></dt>
		<dd>
			<?php echo h($documentDownload['DocumentDownload']['sr_no']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Qc Document'); ?></dt>
		<dd>
			<?php echo $this->Html->link($documentDownload['QcDocument']['title'], array('controller' => 'qc_documents', 'action' => 'view', $documentDownload['QcDocument']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Custom Table'); ?></dt>
		<dd>
			<?php echo $this->Html->link($documentDownload['CustomTable']['name'], array('controller' => 'custom_tables', 'action' => 'view', $documentDownload['CustomTable']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Reccord Id'); ?></dt>
		<dd>
			<?php echo h($documentDownload['DocumentDownload']['reccord_id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Download By'); ?></dt>
		<dd>
			<?php echo h($documentDownload['DocumentDownload']['download_by']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Process Definition'); ?></dt>
		<dd>
			<?php echo h($documentDownload['DocumentDownload']['process_definition']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Signature'); ?></dt>
		<dd>
			<?php echo h($documentDownload['DocumentDownload']['signature']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Digital Signature'); ?></dt>
		<dd>
			<?php echo h($documentDownload['DocumentDownload']['digital_signature']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Add Cover Page'); ?></dt>
		<dd>
			<?php echo h($documentDownload['DocumentDownload']['add_cover_page']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Add Parent Records'); ?></dt>
		<dd>
			<?php echo h($documentDownload['DocumentDownload']['add_parent_records']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Add Child Records'); ?></dt>
		<dd>
			<?php echo h($documentDownload['DocumentDownload']['add_child_records']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Add Linked Form Records'); ?></dt>
		<dd>
			<?php echo h($documentDownload['DocumentDownload']['add_linked_form_records']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Created By'); ?></dt>
		<dd>
			<?php echo $this->Html->link($documentDownload['CreatedBy']['name'], array('controller' => 'users', 'action' => 'view', $documentDownload['CreatedBy']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Created'); ?></dt>
		<dd>
			<?php echo h($documentDownload['DocumentDownload']['created']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Modified By'); ?></dt>
		<dd>
			<?php echo $this->Html->link($documentDownload['ModifiedBy']['name'], array('controller' => 'users', 'action' => 'view', $documentDownload['ModifiedBy']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Approved By'); ?></dt>
		<dd>
			<?php echo h($documentDownload['DocumentDownload']['approved_by']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Prepared By'); ?></dt>
		<dd>
			<?php echo h($documentDownload['DocumentDownload']['prepared_by']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Modified'); ?></dt>
		<dd>
			<?php echo h($documentDownload['DocumentDownload']['modified']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Soft Delete'); ?></dt>
		<dd>
			<?php echo h($documentDownload['DocumentDownload']['soft_delete']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Branchid'); ?></dt>
		<dd>
			<?php echo h($documentDownload['DocumentDownload']['branchid']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Departmentid'); ?></dt>
		<dd>
			<?php echo h($documentDownload['DocumentDownload']['departmentid']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Company'); ?></dt>
		<dd>
			<?php echo $this->Html->link($documentDownload['Company']['name'], array('controller' => 'companies', 'action' => 'view', $documentDownload['Company']['id'])); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Document Download'), array('action' => 'edit', $documentDownload['DocumentDownload']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete Document Download'), array('action' => 'delete', $documentDownload['DocumentDownload']['id']), array('confirm' => __('Are you sure you want to delete # %s?', $documentDownload['DocumentDownload']['id']))); ?> </li>
		<li><?php echo $this->Html->link(__('List Document Downloads'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Document Download'), array('action' => 'add')); ?> </li>
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
