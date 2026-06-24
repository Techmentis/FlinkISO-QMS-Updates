<?php echo $this->element('checkbox-script'); ?><div  id="main">
<?php echo $this->Session->flash();?>	
<div class="approvalProcesses ">
	<?php echo $this->element('nav-header-lists',array('postData'=>array('pluralHumanName'=>'Approval Processes','modelClass'=>'ApprovalProcess','options'=>array("sr_no"=>"Sr No","title"=>"Title","process_description"=>"Process Description"),'pluralVar'=>'approvalProcesses'))); ?>

	<script type="text/javascript">
		$(document).ready(function(){
		$('table th a, .pag_list li span a').on('click', function() {
			var url = $(this).attr("href");
			$('#main').load(url);
			return false;
		});
	});
	</script>	
	<p class="text-right"><?php echo $this->Html->link('View All Approvals',array('controller'=>'approvals','action'=>'index','timestamp'=>date('Ymdhis')));?></p>
	<div class="table-responsive">
		<?php echo $this->Form->create(array('class'=>'no-padding no-margin no-background'));?>				
			<table cellpadding="0" cellspacing="0" class="table table-bordered table table-striped table-hover">
				<tr>					
					<th><?php echo $this->Paginator->sort('title'); ?></th>
					<th><?php echo $this->Paginator->sort('process_description'); ?></th>					
					<th>Actions</th>
				</tr>
				<?php if($approvalProcesses){ ?>
			<?php foreach ($approvalProcesses as $approvalProcess): ?>
				<tr>
					<td><?php echo h($approvalProcess['ApprovalProcess']['title']); ?>&nbsp;</td>
					<td><?php echo h($approvalProcess['ApprovalProcess']['process_description']); ?>&nbsp;</td>
					<td class=" actions">	<?php echo $this->element('actions', array('created' => $approvalProcess['ApprovalProcess']['created_by'], 'postVal' => $approvalProcess['ApprovalProcess']['id'], 'softDelete' => $approvalProcess['ApprovalProcess']['soft_delete'])); ?>	</td>	
				</tr>
			<?php endforeach; ?>
		<?php }else{ ?>
			<tr><td colspan=51>No results found</td></tr>
		<?php } ?>
			</table>
		<?php echo $this->Form->end();?>			
	</div>
	<p>
	<?php echo $this->Paginator->options(array());
			echo $this->Paginator->counter(array(
				'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
				));
			?>
	</p>
	<ul class="pagination">
		<?php
			echo "<li class='previous'>".$this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'))."</li>";
			echo "<li>".$this->Paginator->numbers(array('separator' => ''))."</li>";
			echo "<li class='next'>".$this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'))."</li>";
		?>
	</ul>
</div>
<script>$.ajaxSetup({beforeSend:function(){$("#busy-indicator").show();},complete:function(){$("#busy-indicator").hide();}});</script>
