<div class="table-responsive">
	<table cellpadding="0" cellspacing="0" class="table table-hover">
		<tr>		
			<th><?php echo __('Title'); ?></th>
			<th><?php echo Inflector::humanize('document_number'); ?></th>
			<th><?php echo Inflector::humanize('date_of_issue'); ?></th>				
			<th><?php echo Inflector::humanize('document_status'); ?></th>
			<th><?php echo Inflector::humanize('prepared_by'); ?></th>		
			<th><?php echo Inflector::humanize('approved_by'); ?></th>		
			<th><?php echo Inflector::humanize('issued_by'); ?></th>
			<th><?php echo Inflector::humanize('publish'); ?></th>
			<th></th>				
		</tr>
		<?php if($childDocs){ ?>
			<?php foreach ($childDocs as $qcDocument): ?>
				<tr>		
					<td><?php echo $this->Html->link($qcDocument['QcDocument']['title'],array('action'=>'view',$qcDocument['QcDocument']['id'])); ?>&nbsp;</td>
					<td><?php echo h($qcDocument['QcDocument']['document_number']); ?>&nbsp;</td>
					<td><?php echo h($qcDocument['QcDocument']['date_of_issue']); ?>&nbsp;</td>
					<td><?php echo h($qcDocument['QcDocument']['document_status']); ?>&nbsp;</td>
					<td><?php echo h($qcDocument['PreparedBy']['name']); ?>&nbsp;</td>
					<td><?php echo h($qcDocument['ApprovedBy']['name']); ?>&nbsp;</td>
					<td><?php echo h($qcDocument['IssuedBy']['name']); ?>&nbsp;</td>

					<td width="60">
						<?php if($qcDocument['QcDocument']['publish'] == 1) { ?>
							<span class="fa fa-check"></span>
						<?php } else { ?>
							<span class="fa fa-close"></span>
							<?php } ?>&nbsp;
						</td>
						
						<td class=" actions">	
							<?php echo $this->element('actions', array('created' => $qcDocument['QcDocument']['created_by'], 'postVal' => $qcDocument['QcDocument']['id'], 'softDelete' => $qcDocument['QcDocument']['soft_delete'])); ?>			
						</td>
					</tr>
				<?php endforeach; ?>
			<?php }else{ ?>
				<tr><td colspan="9">No child document found</td></tr>
			<?php } ?>
		</table>
	</div>
