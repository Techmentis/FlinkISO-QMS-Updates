<div id="approvals_ajax">
<?php echo $this->Session->flash();?>	

	<div class="approvals ">
		<?php echo $this->element('nav-header-lists',array('postData'=>array('pluralHumanName'=>'Approvals','modelClass'=>'Approval','options'=>array(),'pluralVar'=>'approvals'))); ?>


<div class="nav panel panel-default">
<div class="approvals form col-md-12">


<table class="table table-responsive">
		<tr><td><?php echo __('Model Name'); ?></td>
		<td>
			<?php echo h($approval['Approval']['model_name']); ?>
			&nbsp;
		</td></tr>
		<tr><td><?php echo __('Controller Name'); ?></td>
		<td>
			<?php echo h($approval['Approval']['controller_name']); ?>
			&nbsp;
		</td></tr>
		<tr><td><?php echo __('Record'); ?></td>
		<td>
			<?php echo h($approval['Approval']['record']); ?>
			&nbsp;
		</td></tr>
		<tr><td><?php echo __('From'); ?></td>
		<td>
			<?php echo h($approval['Approval']['from']); ?>
			&nbsp;
		</td></tr>
		<tr><td><?php echo __('User'); ?></td>
		<td>
			<?php echo $this->Html->link($approval['User']['name'], array('controller' => 'users', 'action' => 'view', $approval['User']['id'])); ?>
			&nbsp;
		</td></tr>
		<tr><td><?php echo __('Comments'); ?></td>
		<td>
			<?php echo h($approval['Approval']['comments']); ?>
			&nbsp;
		</td></tr>
		<tr><td><?php echo __('Approval Step'); ?></td>
		<td>
			<?php echo h($approval['Approval']['approval_step']); ?>
			&nbsp;
		</td></tr>
		<tr><td><?php echo __('Status'); ?></td>
		<td>
			<?php echo h($approval['Approval']['status']); ?>
			&nbsp;
		</td></tr>
		<tr><td><?php echo __('Approval Status'); ?></td>
		<td>
			<?php echo h($approval['Approval']['approval_status']); ?>
			&nbsp;
		</td></tr>
		<tr><td><?php echo __('Approver Comments'); ?></td>
		<td>
			<?php echo h($approval['Approval']['approver_comments']); ?>
			&nbsp;
		</td></tr>
		<tr><td><?php echo __('Prepared By'); ?></td>

	<td><?php echo h($approval['ApprovedBy']['name']); ?>&nbsp;</td></tr>
		<tr><td><?php echo __('Approved By'); ?></td>

	<td><?php echo h($approval['ApprovedBy']['name']); ?>&nbsp;</td></tr>
	<tr><td><?php echo __('Publish'); ?></td>

	<td>
	<?php if($approval['Approval']['publish'] == 1) { ?>
	<span class="fa fa-check"></span>
	<?php } else { ?>
	<span class="fa fa-close"></span>
	<?php } ?>&nbsp;</td>
&nbsp;</td></tr>
	<tr><td><?php echo __('Soft Delete'); ?></td>

	<td>
	<?php if($approval['Approval']['soft_delete'] == 1) { ?>
	<span class="fa fa-check"></span>
	<?php } else { ?>
	<span class="fa fa-close"></span>
	<?php } ?>&nbsp;</td>
&nbsp;</td></tr>
<tr>
	<td colspan="2">
			<?php echo $this->Html->link('Delete This Record?', array('controller' => 'users', 'action' => 'delete', $approval['ModifiedBy']['id']),array('class'=>'btn btn-sm btn-danger')); ?>
	<?php echo $this->Html->link('Edit This Record?', array('controller' => 'users', 'action' => 'delete', $approval['ModifiedBy']['id']),array('class'=> 'btn btn-sm btn-warning')); ?>
	</td>
</tr>
</table>

</div>

</div>
<?php echo $this->Js->writeBuffer();?>

</div>
<script>$.ajaxSetup({beforeSend:function(){$("#busy-indicator").show();},complete:function(){$("#busy-indicator").hide();}});</script>
