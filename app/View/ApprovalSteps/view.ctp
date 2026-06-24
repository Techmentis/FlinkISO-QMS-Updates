<div id="approvalSteps_ajax">
<?php echo $this->Session->flash();?>	

	<div class="approvalSteps ">
		<?php echo $this->element('nav-header-lists',array('postData'=>array('pluralHumanName'=>'Approval Steps','modelClass'=>'ApprovalStep','options'=>array(),'pluralVar'=>'approvalSteps'))); ?>


<div class="nav panel panel-default">
<div class="approvalSteps form col-md-12">


<table class="table table-responsive">
		<tr><td><?php echo __('Approval Process'); ?></td>
		<td>
			<?php echo $this->Html->link($approvalStep['ApprovalProcess']['title'], array('controller' => 'approval_processes', 'action' => 'view', $approvalStep['ApprovalProcess']['id'])); ?>
			&nbsp;
		</td></tr>
		<tr><td><?php echo __('Title'); ?></td>
		<td>
			<?php echo h($approvalStep['ApprovalStep']['title']); ?>
			&nbsp;
		</td></tr>
		<tr><td><?php echo __('Process Step'); ?></td>
		<td>
			<?php echo h($approvalStep['ApprovalStep']['process_step']); ?>
			&nbsp;
		</td></tr>
		<tr><td><?php echo __('Send To Department Hod'); ?></td>
		<td>
			<?php echo h($approvalStep['ApprovalStep']['send_to_department_hod']); ?>
			&nbsp;
		</td></tr>
		<tr><td><?php echo __('Send To Designation'); ?></td>
		<td>
			<?php echo h($approvalStep['ApprovalStep']['send_to_designation']); ?>
			&nbsp;
		</td></tr>
		<tr><td><?php echo __('Send To Admins'); ?></td>
		<td>
			<?php echo h($approvalStep['ApprovalStep']['send_to_admins']); ?>
			&nbsp;
		</td></tr>
		<tr><td><?php echo __('Send To Reviwers'); ?></td>
		<td>
			<?php echo h($approvalStep['ApprovalStep']['send_to_reviwers']); ?>
			&nbsp;
		</td></tr>
		<tr><td><?php echo __('Send To Publishers'); ?></td>
		<td>
			<?php echo h($approvalStep['ApprovalStep']['send_to_publishers']); ?>
			&nbsp;
		</td></tr>
		<tr><td><?php echo __('Send To Users'); ?></td>
		<td>
			<?php echo h($approvalStep['ApprovalStep']['send_to_users']); ?>
			&nbsp;
		</td></tr>
		<tr><td><?php echo __('Comments'); ?></td>
		<td>
			<?php echo h($approvalStep['ApprovalStep']['comments']); ?>
			&nbsp;
		</td></tr>
		<tr><td><?php echo __('Approval Mode'); ?></td>
		<td>
			<?php echo h($approvalStep['ApprovalStep']['approval_mode']); ?>
			&nbsp;
		</td></tr>
		<tr><td><?php echo __('Approval Type'); ?></td>
		<td>
			<?php echo h($approvalStep['ApprovalStep']['approval_type']); ?>
			&nbsp;
		</td></tr>
		<tr><td><?php echo __('Prepared By'); ?></td>

	<td><?php echo h($approvalStep['ApprovedBy']['name']); ?>&nbsp;</td></tr>
		<tr><td><?php echo __('Approved By'); ?></td>

	<td><?php echo h($approvalStep['ApprovedBy']['name']); ?>&nbsp;</td></tr>
	<tr><td><?php echo __('Publish'); ?></td>

	<td>
	<?php if($approvalStep['ApprovalStep']['publish'] == 1) { ?>
	<span class="glyphicon glyphicon-ok-sign"></span>
	<?php } else { ?>
	<span class="glyphicon glyphicon-remove-circle"></span>
	<?php } ?>&nbsp;</td>
&nbsp;</td></tr>
	<tr><td><?php echo __('Soft Delete'); ?></td>

	<td>
	<?php if($approvalStep['ApprovalStep']['soft_delete'] == 1) { ?>
	<span class="glyphicon glyphicon-ok-sign"></span>
	<?php } else { ?>
	<span class="glyphicon glyphicon-remove-circle"></span>
	<?php } ?>&nbsp;</td>
&nbsp;</td></tr>
<tr>
	<td colspan="2">
			<?php echo $this->Html->link('Delete This Record?', array('controller' => 'users', 'action' => 'delete', $approvalStep['ModifiedBy']['id']),array('class'=>'btn btn-sm btn-danger')); ?>
	<?php echo $this->Html->link('Edit This Record?', array('controller' => 'users', 'action' => 'delete', $approvalStep['ModifiedBy']['id']),array('class'=> 'btn btn-sm btn-warning')); ?>
	</td>
</tr>
</table>

</div>
<div class="col-md-12 hide">
	<p><?php echo $this->element('helps'); ?></p>
</div>
</div>
<?php echo $this->Js->writeBuffer();?>

</div>
<script>$.ajaxSetup({beforeSend:function(){$("#busy-indicator").show();},complete:function(){$("#busy-indicator").hide();}});</script>
