<div id="branches_ajax">
	<?php echo $this->Session->flash();?>	
	<div class="branches ">
		<?php echo $this->element('nav-header-lists',array('postData'=>array('pluralHumanName'=>'Branches','modelClass'=>'Branch','options'=>array(),'pluralVar'=>'branches'))); ?>
		<div class="nav panel panel-default">
			<div class="branches form col-md-12">
				<table class="table table-responsive">
					<tr>
						<td><?php echo __('Name'); ?></td>
						<td><?php echo h($branch['Branch']['name']); ?>&nbsp;</td>
					</tr>
					<tr>
						<td><?php echo __('Details'); ?></td>
						<td><?php echo h($branch['Branch']['details']); ?>&nbsp;</td>
					</tr>
					<tr>
						<td><?php echo __('Departments'); ?></td>
						<td><?php foreach(json_decode($branch['Branch']['departments'],true) as $departments){
							echo $PublishedDepartmentList[$departments].', ';
						} ?>&nbsp;
					</td>
				</tr>
				<tr>
					<td><?php echo __('Prepared By'); ?></td>
					<td><?php echo h($branch['ApprovedBy']['name']); ?>&nbsp;</td>
				</tr>
				<tr>
					<td><?php echo __('Approved By'); ?></td>
					<td><?php echo h($branch['ApprovedBy']['name']); ?>&nbsp;</td>
				</tr>
				<tr>
					<td><?php echo __('Publish'); ?></td>
					<td>
						<?php if($branch['Branch']['publish'] == 1) { ?>
							<span class="fa fa-check"></span>
						<?php } else { ?>
							<span class="fa fa-close"></span>
							<?php } ?>&nbsp;
						</td>
					</tr>
					<tr>
						<td><?php echo __('Soft Delete'); ?></td>
						<td>
							<?php if($branch['Branch']['soft_delete'] == 1) { ?>
								<span class="fa fa-check"></span>
							<?php } else { ?>
								<span class="fa fa-close"></span>
								<?php } ?>&nbsp;
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<?php echo $this->Html->link('Delete This Record?', array('controller' => 'users', 'action' => 'delete', $branch['ModifiedBy']['id']),array('class'=>'btn btn-sm btn-danger')); ?>
								<?php echo $this->Html->link('Edit This Record?', array('controller' => 'users', 'action' => 'delete', $branch['ModifiedBy']['id']),array('class'=> 'btn btn-sm btn-warning')); ?>
							</td>
						</tr>
					</table>
				</div>				
			</div>
			<?php echo $this->Js->writeBuffer();?>
		</div>
		<script>$.ajaxSetup({beforeSend:function(){$("#busy-indicator").show();},complete:function(){$("#busy-indicator").hide();}});</script>
