<div id="designations_ajax">
<?php echo $this->Session->flash();?>	

	<div class="designations ">
		<?php echo $this->element('nav-header-lists',array('postData'=>array('pluralHumanName'=>'Designations','modelClass'=>'Designation','options'=>array(),'pluralVar'=>'designations'))); ?>


<div class="nav panel panel-default">
<div class="designations form col-md-12">


<table class="table table-responsive">
		<tr><td><?php echo __('Name'); ?></td>
		<td>
			<?php echo h($designation['Designation']['name']); ?>
			&nbsp;
		</td></tr>
		<tr><td><?php echo __('Parent Designation'); ?></td>
		<td>
			<?php echo $this->Html->link($designation['ParentDesignation']['name'], array('controller' => 'designations', 'action' => 'view', $designation['ParentDesignation']['id'])); ?>
			&nbsp;
		</td></tr>
		<tr><td><?php echo __('Level'); ?></td>
		<td>
			<?php echo h($designation['Designation']['level']); ?>
			&nbsp;
		</td></tr>
		<tr><td><?php echo __('Division Id'); ?></td>
		<td>
			<?php echo h($designation['Designation']['division_id']); ?>
			&nbsp;
		</td></tr>
		<tr><td><?php echo __('Prepared By'); ?></td>

	<td><?php echo h($designation['ApprovedBy']['name']); ?>&nbsp;</td></tr>
		<tr><td><?php echo __('Approved By'); ?></td>

	<td><?php echo h($designation['ApprovedBy']['name']); ?>&nbsp;</td></tr>
	<tr><td><?php echo __('Publish'); ?></td>

	<td>
	<?php if($designation['Designation']['publish'] == 1) { ?>
	<span class="fa fa-check"></span>
	<?php } else { ?>
	<span class="fa fa-close"></span>
	<?php } ?>&nbsp;</td>
&nbsp;</td></tr>
	<tr><td><?php echo __('Soft Delete'); ?></td>

	<td>
	<?php if($designation['Designation']['soft_delete'] == 1) { ?>
	<span class="fa fa-check"></span>
	<?php } else { ?>
	<span class="fa fa-close"></span>
	<?php } ?>&nbsp;</td>
&nbsp;</td></tr>
<tr>
	<td colspan="2">
			<?php echo $this->Html->link('Delete This Record?', array('controller' => 'users', 'action' => 'delete', $designation['ModifiedBy']['id']),array('class'=>'btn btn-sm btn-danger')); ?>
	<?php echo $this->Html->link('Edit This Record?', array('controller' => 'users', 'action' => 'delete', $designation['ModifiedBy']['id']),array('class'=> 'btn btn-sm btn-warning')); ?>
	</td>
</tr>
</table>

</div>
</div>
<?php echo $this->Js->writeBuffer();?>

</div>
<script>$.ajaxSetup({beforeSend:function(){$("#busy-indicator").show();},complete:function(){$("#busy-indicator").hide();}});</script>
