<table class="table table-responsive">
		<tr>
			<th>Field</th>
			<th>Value</th>
		</tr>
		<tr><td width="25%"><?php echo __('Qc Document Category'); ?></td>
		<td>
			<?php echo $this->Html->link($qcDocument['QcDocumentCategory']['name'], array('controller' => 'qc_document_categories', 'action' => 'view', $qcDocument['QcDocumentCategory']['id'])); ?>
			&nbsp;
		</td></tr>
		<tr><td><?php echo __('Clause'); ?></td>
		<td>
			<?php echo $this->Html->link($qcDocument['Clause']['title'], array('controller' => 'clauses', 'action' => 'view', $qcDocument['Clause']['id'])); ?>
			&nbsp;
		</td></tr>		
		<tr><td><?php echo __('Issue Number'); ?></td>
		<td>
			<?php echo h($qcDocument['QcDocument']['issue_number']); ?>
			&nbsp;
		</td></tr>
		<tr><td><?php echo __('Date Of Next Issue'); ?></td>
		<td>
			<?php echo h($qcDocument['QcDocument']['date_of_next_issue']); ?>
			&nbsp;
		</td></tr>
		<tr><td><?php echo __('Date Of Issue'); ?></td>
		<td>
			<?php echo h($qcDocument['QcDocument']['date_of_issue']); ?>
			&nbsp;
		</td></tr>
		<tr><td><?php echo __('Effective From Date'); ?></td>
		<td>
			<?php echo h($qcDocument['QcDocument']['effective_from_date']); ?>
			&nbsp;
		</td></tr>
		<tr><td><?php echo __('Date Of Review'); ?></td>
		<td>
			<?php echo h($qcDocument['QcDocument']['date_of_review']); ?>
			&nbsp;
		</td></tr>
		<tr><td><?php echo __('Revision Number'); ?></td>
		<td>
			<?php echo h($qcDocument['QcDocument']['revision_number']); ?>
			&nbsp;
		</td></tr>
		<tr><td><?php echo __('Revision Date'); ?></td>
		<td>
			<?php echo h($qcDocument['QcDocument']['revision_date']); ?>
			&nbsp;
		</td></tr>
		<tr><td><?php echo __('Document Type'); ?></td>
		<td>
			<?php echo h($customArray['documentTypes'][$qcDocument['QcDocument']['document_type']]); ?>
			&nbsp;
		</td></tr>
		<tr><td><?php echo __('Document Security'); ?></td>
		<td>
			<?php echo h($customArray['itCategories'][$qcDocument['QcDocument']['it_categories']]); ?>
			&nbsp;
		</td></tr>
		<tr><td><?php echo __('Issued By'); ?></td>
		<td>
			<?php echo h($qcDocument['IssuedBy']['name']); ?>
			&nbsp;
		</td></tr>
		<tr><td><?php echo __('Issuing Authority'); ?></td>
		<td>
			<?php echo $this->Html->link($qcDocument['IssuingAuthority']['name'], array('controller' => 'employees', 'action' => 'view', $qcDocument['IssuingAuthority']['id'])); ?>
			&nbsp;
		</td></tr>
		<tr><td><?php echo __('Archived'); ?></td>
		<td>
			<?php echo h($qcDocument['QcDocument']['archived']?'Yes':'No'); ?>
			&nbsp;
		</td></tr>
		<tr><td><?php echo __('Qc Document'); ?></td>
		<td>
			<?php echo $this->Html->link($parentDocuments[$qcDocument['ParentQcDocument']['id']], array('controller' => 'qc_documents', 'action' => 'view', $qcDocument['ParentQcDocument']['id'])); ?>
			&nbsp;
		</td></tr>
		<tr><td><?php echo __('Parent Document'); ?></td>
		<td>
			<?php echo $this->Html->link($qcDocument['ParentDocument']['id'], array('controller' => 'qc_documents', 'action' => 'view', $qcDocument['ParentDocument']['id'])); ?>
			&nbsp;
		</td></tr>
		<tr><td><?php echo __('Linked Formats'); ?></td>
		<td>
			<?php echo h($qcDocument['QcDocument']['linked_formats']); ?>
			&nbsp;
		</td></tr>		
		<tr><td><?php echo __('Prepared By'); ?></td>

	<td><?php echo h($qcDocument['PreparedBy']['name']); ?>&nbsp;</td></tr>
		<tr><td><?php echo __('Approved By'); ?></td>

	<td><?php echo h($qcDocument['ApprovedBy']['name']); ?>&nbsp;</td></tr>
	<tr><td><?php echo __('Publish'); ?></td>

	<td>
	<?php if($qcDocument['QcDocument']['publish'] == 1) { ?>
	<span class="fa fa-check"></span>
	<?php } else { ?>
	<span class="fa fa-close"></span>
	<?php } ?>&nbsp;</td>
&nbsp;</td></tr>	
<tr>
	<td colspan="2">
		<div class="btn-group1">
        <?php     
        echo $this->Html->link('<i class="fa fa-trash-o fa-lg"></i>',array('action'=>'delete',$qcDocument['QcDocument']['id']),array('confirm'=>'This action can not be revered. Do you want to procced?', 'class'=>'btn btn-lg btn-danger','escape'=>false));
        echo "&nbsp;" . $this->Html->link('<i class="fa fa-edit fa-lg"></i>',array('action'=>'edit',$qcDocument['QcDocument']['id']),array('class'=>'btn btn-lg btn-warning','escape'=>false));
        echo "&nbsp;" . $this->Html->link('<i class="fa fa-database fa-lg"></i>',array('controller'=>'custom_tables', 'action'=>'add', 'qc_document_id'=> $qcDocument['QcDocument']['id']),array('class'=>'btn btn-lg btn-info','escape'=>false));        
    ?>
    </div>
	</td>
</tr>
</table>
