<table class="table table-responsive table-bordered">
	<tr>
		<th><?php echo __('Clause'); ?></th>
		<td><?php echo $qcDocument['Clause']['title']; ?>&nbsp;</td>
		<th><?php echo __('Additional Clauses'); ?></th>
		<td><?php if($qcDocument['QcDocument']['additional_clauses']){
			$additionalClauses = json_decode($qcDocument['QcDocument']['additional_clauses']);
			foreach($additionalClauses as $additionalClause){
				echo $clauses[$additionalClause].', ';	
			}

		}?>&nbsp;</td>
		<th><?php echo __('Qc Document Category'); ?></th>
		<td><?php echo $this->Html->link($qcDocument['QcDocumentCategory']['name'], array('controller' => 'qc_document_categories', 'action' => 'view', $qcDocument['QcDocumentCategory']['id'])); ?>&nbsp;</td>
	</tr>	
	<tr>
		<th><?php echo __('Issue Number'); ?></th>
		<td><?php echo h($qcDocument['QcDocument']['issue_number']); ?>&nbsp;</td>
		<th><?php echo __('Date Of Issue'); ?></th>
		<td><?php echo h($qcDocument['QcDocument']['date_of_issue']); ?>&nbsp;</td>
		<th><?php echo __('Date Of Next Issue'); ?></th>
		<td><?php echo h($qcDocument['QcDocument']['date_of_next_issue']); ?>&nbsp;</td>
	</tr>
	<tr>
		<th><?php echo __('Effective From Date'); ?></th>
		<td><?php echo h($qcDocument['QcDocument']['effective_from_date']); ?>&nbsp;</td>
		<th><?php echo __('Date Of Review'); ?></th>
		<td><?php echo h($qcDocument['QcDocument']['date_of_review']); ?>&nbsp;</td>
		<th><?php echo __('Revision Date'); ?></th>
		<td><?php echo h($qcDocument['QcDocument']['revision_date']); ?>&nbsp;</td>
	</tr>
	<tr>
		<th><?php echo __('Document Type'); ?></th>
		<td><?php echo h($customArray['documentTypes'][$qcDocument['QcDocument']['document_type']]); ?>&nbsp;</td>
		<th><?php echo __('Document Security'); ?></th>
		<td><?php echo h($customArray['itCategories'][$qcDocument['QcDocument']['it_categories']]); ?>&nbsp;</td>
		<th><?php echo __('Issued By'); ?></th>
		<td><?php echo h($qcDocument['IssuedBy']['name']); ?>&nbsp;</td>		
	</tr>
	<tr>
		<th><?php echo __('Issuing Authority'); ?></th>
		<td><?php echo $this->Html->link($qcDocument['IssuingAuthority']['name'], array('controller' => 'employees', 'action' => 'view', $qcDocument['IssuingAuthority']['id'])); ?>&nbsp;</td>
		<th><?php echo __('Prepared By'); ?></th>
		<td><?php echo h($qcDocument['PreparedBy']['name']); ?>&nbsp;</td>
		<th><?php echo __('Approved By'); ?></th>
		<td><?php echo h($qcDocument['ApprovedBy']['name']); ?>&nbsp;</td>
	</tr>	
	<tr>
		<td colspan="6">
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
