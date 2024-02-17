<div id="departments_ajax">
<?php echo $this->Session->flash();?>	

	<div class="departments ">
		<?php echo $this->element('nav-header-lists',array('postData'=>array('pluralHumanName'=>'Departments','modelClass'=>'Department','options'=>array(),'pluralVar'=>'departments'))); ?>


<div class="nav panel panel-default">
<div class="departments form col-md-12">


<table class="table table-responsive">
		<tr><td><?php echo __('Name'); ?></td>
		<td>
			<?php echo h($department['Department']['name']); ?>
			&nbsp;
		</td></tr>
		<tr><td><?php echo __('Clauses'); ?></td>
		<td>
			<?php echo h($department['Department']['clauses']); ?>
			&nbsp;
		</td></tr>
		<tr><td><?php echo __('Details'); ?></td>
		<td>
			<?php echo h($department['Department']['details']); ?>
			&nbsp;
		</td></tr>
		<tr><td><?php echo __('Prepared By'); ?></td>

	<td><?php echo h($department['ApprovedBy']['name']); ?>&nbsp;</td></tr>
		<tr><td><?php echo __('Approved By'); ?></td>

	<td><?php echo h($department['ApprovedBy']['name']); ?>&nbsp;</td></tr>
	<tr><td><?php echo __('Publish'); ?></td>

	<td>
	<?php if($department['Department']['publish'] == 1) { ?>
	<span class="fa fa-check"></span>
	<?php } else { ?>
	<span class="fa fa-close"></span>
	<?php } ?>&nbsp;</td>
&nbsp;</td></tr>
	<tr><td><?php echo __('Soft Delete'); ?></td>

	<td>
	<?php if($department['Department']['soft_delete'] == 1) { ?>
	<span class="fa fa-check"></span>
	<?php } else { ?>
	<span class="fa fa-close"></span>
	<?php } ?>&nbsp;</td>
&nbsp;</td></tr>
<tr>
	<td colspan="2">
			<?php echo $this->Html->link('Delete This Record?', array('controller' => 'users', 'action' => 'delete', $department['ModifiedBy']['id']),array('class'=>'btn btn-sm btn-danger')); ?>
	<?php echo $this->Html->link('Edit This Record?', array('controller' => 'users', 'action' => 'delete', $department['ModifiedBy']['id']),array('class'=> 'btn btn-sm btn-warning')); ?>
	</td>
</tr>
</table>

</div>
</div>
<?php echo $this->Js->writeBuffer();?>

</div>
<script>$.ajaxSetup({beforeSend:function(){$("#busy-indicator").show();},complete:function(){$("#busy-indicator").hide();}});</script>
