<?php echo $this->element('checkbox-script'); ?><div  id="main">
<?php echo $this->Session->flash();?>	
	<div class="approvalSteps ">
		<?php echo $this->element('nav-header-lists',array('postData'=>array('pluralHumanName'=>'Approval Steps','modelClass'=>'ApprovalStep','options'=>array("sr_no"=>"Sr No","title"=>"Title","process_step"=>"Process Step","send_to_department_hod"=>"Send To Department Hod","send_to_designation"=>"Send To Designation","send_to_admins"=>"Send To Admins","send_to_reviwers"=>"Send To Reviwers","send_to_publishers"=>"Send To Publishers","send_to_users"=>"Send To Users","comments"=>"Comments","approval_mode"=>"Approval Mode","approval_type"=>"Approval Type"),'pluralVar'=>'approvalSteps'))); ?>

<script type="text/javascript">
$(document).ready(function(){
$('table th a, .pag_list li span a').on('click', function() {
	var url = $(this).attr("href");
	$('#main').load(url);
	return false;
});
});
</script>	
		<div class="table-responsive">
		<?php echo $this->Form->create(array('class'=>'no-padding no-margin no-background'));?>				
			<table cellpadding="0" cellspacing="0" class="table table-bordered table table-striped table-hover">
				<tr>

					
				<th><?php echo $this->Paginator->sort('approval_process_id'); ?></th>
				<th><?php echo $this->Paginator->sort('title'); ?></th>
				<th><?php echo $this->Paginator->sort('process_step'); ?></th>
				<th><?php echo $this->Paginator->sort('send_to_department_hod'); ?></th>
				<th><?php echo $this->Paginator->sort('send_to_designation'); ?></th>
				<th><?php echo $this->Paginator->sort('send_to_admins'); ?></th>
				<th><?php echo $this->Paginator->sort('send_to_reviwers'); ?></th>
				<th><?php echo $this->Paginator->sort('send_to_publishers'); ?></th>
				<th><?php echo $this->Paginator->sort('send_to_users'); ?></th>
				<th><?php echo $this->Paginator->sort('comments'); ?></th>
				<th><?php echo $this->Paginator->sort('approval_mode'); ?></th>
				<th><?php echo $this->Paginator->sort('approval_type'); ?></th>
					<th><?php echo $this->Paginator->sort('prepared_by'); ?></th>		
					<th><?php echo $this->Paginator->sort('approved_by'); ?></th>		
					<th><?php echo $this->Paginator->sort('publish'); ?></th>		

				<th>Actions</th>
				</tr>
				<?php if($approvalSteps){ ?>
<?php foreach ($approvalSteps as $approvalStep): ?>
	<tr>
		<td>
			<?php echo $this->Html->link($approvalStep['ApprovalProcess']['title'], array('controller' => 'approval_processes', 'action' => 'view', $approvalStep['ApprovalProcess']['id'])); ?>
		</td>
		<td><?php echo h($approvalStep['ApprovalStep']['title']); ?>&nbsp;</td>
		<td><?php echo h($approvalStep['ApprovalStep']['process_step']); ?>&nbsp;</td>
		<td><?php echo h($approvalStep['ApprovalStep']['send_to_department_hod']); ?>&nbsp;</td>
		<td><?php echo h($approvalStep['ApprovalStep']['send_to_designation']); ?>&nbsp;</td>
		<td><?php echo h($approvalStep['ApprovalStep']['send_to_admins']); ?>&nbsp;</td>
		<td><?php echo h($approvalStep['ApprovalStep']['send_to_reviwers']); ?>&nbsp;</td>
		<td><?php echo h($approvalStep['ApprovalStep']['send_to_publishers']); ?>&nbsp;</td>
		<td><?php echo h($approvalStep['ApprovalStep']['send_to_users']); ?>&nbsp;</td>
		<td><?php echo h($approvalStep['ApprovalStep']['comments']); ?>&nbsp;</td>
		<td><?php echo h($approvalStep['ApprovalStep']['approval_mode']); ?>&nbsp;</td>
		<td><?php echo h($approvalStep['ApprovalStep']['approval_type']); ?>&nbsp;</td>
		<td><?php echo h($PublishedEmployeeList[$approvalStep['ApprovalStep']['prepared_by']]); ?>&nbsp;</td>
		<td><?php echo h($PublishedEmployeeList[$approvalStep['ApprovalStep']['approved_by']]); ?>&nbsp;</td>

		<td width="60">
			<?php if($approvalStep['ApprovalStep']['publish'] == 1) { ?>
			<span class="glyphicon glyphicon-ok-sign"></span>
			<?php } else { ?>
			<span class="glyphicon glyphicon-remove-circle"></span>
			<?php } ?>&nbsp;</td>

					<td class=" actions">	<?php echo $this->element('actions', array('created' => $approvalStep['ApprovalStep']['created_by'], 'postVal' => $approvalStep['ApprovalStep']['id'], 'softDelete' => $approvalStep['ApprovalStep']['soft_delete'])); ?>	</td>	</tr>
<?php endforeach; ?>
<?php }else{ ?>
	<tr><td colspan=81>No results found</td></tr>
<?php } ?>
			</table>
<?php echo $this->Form->end();?>			
		</div>
			<p>
			<?php
			echo $this->Paginator->options(array(			
			));
			
			echo $this->Paginator->counter(array(
			'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
			));
			?>			</p>
			<ul class="pagination">
			<?php
		echo "<li class='previous'>".$this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'))."</li>";
		echo "<li>".$this->Paginator->numbers(array('separator' => ''))."</li>";
		echo "<li class='next'>".$this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'))."</li>";
	?>
			</ul>
		</div>
	</div>
	</div>	

</div>

<script>$.ajaxSetup({beforeSend:function(){$("#busy-indicator").show();},complete:function(){$("#busy-indicator").hide();}});</script>
