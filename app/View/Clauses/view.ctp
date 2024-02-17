<div id="clauses_ajax">
<?php echo $this->Session->flash();?>
	<div class="clauses ">
		<?php echo $this->element('nav-header-lists',array('postData'=>array('pluralHumanName'=>'Clauses','modelClass'=>'Clause','options'=>array(),'pluralVar'=>'clauses'))); ?>
			<div class="nav panel panel-default">
				<div class="clauses form col-md-12">
					<table class="table table-responsive">
						<tr><td><?php echo __('Title'); ?></td>
						<td>
							<?php echo h($clause['Clause']['title']); ?>
							&nbsp;
						</td></tr>
						<tr><td><?php echo __('Standard'); ?></td>
						<td>
							<?php echo h($clause['Clause']['standard']); ?>
							&nbsp;
						</td></tr>
						<tr><td><?php echo __('Standard'); ?></td>
						<td>
							<?php echo $this->Html->link($clause['Standard']['name'], array('controller' => 'standards', 'action' => 'view', $clause['Standard']['id'])); ?>
							&nbsp;
						</td></tr>
						<tr><td><?php echo __('Clause'); ?></td>
						<td>
							<?php echo h($clause['Clause']['clause']); ?>
							&nbsp;
						</td></tr>
						<tr><td><?php echo __('Sub-clause'); ?></td>
						<td>
							<?php echo h($clause['Clause']['sub-clause']); ?>
							&nbsp;
						</td></tr>
						<tr><td><?php echo __('Details'); ?></td>
						<td>
							<?php echo h($clause['Clause']['details']); ?>
							&nbsp;
						</td></tr>
						<tr><td><?php echo __('Additional Details'); ?></td>
						<td>
							<?php echo h($clause['Clause']['additional_details']); ?>
							&nbsp;
						</td></tr>
						<tr><td><?php echo __('Tabs'); ?></td>
						<td>
							<?php echo h($clause['Clause']['tabs']); ?>
							&nbsp;
						</td></tr>
						<tr><td><?php echo __('External Link 1'); ?></td>
						<td>
							<?php echo h($clause['Clause']['external_link_1']); ?>
							&nbsp;
						</td></tr>
						<tr><td><?php echo __('External Link 2'); ?></td>
						<td>
							<?php echo h($clause['Clause']['external_link_2']); ?>
							&nbsp;
						</td></tr>
						<tr><td><?php echo __('External Link 3'); ?></td>
						<td>
							<?php echo h($clause['Clause']['external_link_3']); ?>
							&nbsp;
						</td></tr>
						<tr><td><?php echo __('External Link 4'); ?></td>
						<td>
							<?php echo h($clause['Clause']['external_link_4']); ?>
							&nbsp;
						</td></tr>
						<tr><td><?php echo __('External Link 5'); ?></td>
						<td>
							<?php echo h($clause['Clause']['external_link_5']); ?>
							&nbsp;
						</td></tr>
						<tr><td><?php echo __('System Tables'); ?></td>
						<td>
							<?php echo h($clause['Clause']['system_tables']); ?>
							&nbsp;
						</td></tr>
						<tr><td><?php echo __('Prepared By'); ?></td>

						<td><?php echo h($clause['ApprovedBy']['name']); ?>&nbsp;</td></tr>
						<tr><td><?php echo __('Approved By'); ?></td>

						<td><?php echo h($clause['ApprovedBy']['name']); ?>&nbsp;</td></tr>
						<tr><td><?php echo __('Publish'); ?></td>

						<td>
						<?php if($clause['Clause']['publish'] == 1) { ?>
						<span class="fa fa-check"></span>
						<?php } else { ?>
						<span class="fa fa-close"></span>
						<?php } ?>&nbsp;</td>
						&nbsp;</td></tr>
						<tr><td><?php echo __('Soft Delete'); ?></td>

						<td>
						<?php if($clause['Clause']['soft_delete'] == 1) { ?>
						<span class="fa fa-check"></span>
						<?php } else { ?>
						<span class="fa fa-close"></span>
						<?php } ?>&nbsp;</td>
						&nbsp;</td></tr>
						<tr>
						<td colspan="2">
							<?php echo $this->Html->link('Delete This Record?', array('controller' => 'clauses', 'action' => 'delete', $clause['Clause']['id']),array('class'=>'btn btn-sm btn-danger')); ?>
							<?php echo $this->Html->link('Edit This Record?', array('controller' => 'clauses', 'action' => 'edit', $clause['Clause']['id']),array('class'=> 'btn btn-sm btn-warning')); ?>
						</td>
						</tr>
					</table>
				</div>
			</div>
			<?php echo $this->Js->writeBuffer();?>
		</div>
</div>