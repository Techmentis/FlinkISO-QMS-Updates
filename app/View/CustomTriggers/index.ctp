<?php echo $this->element('checkbox-script'); ?><div  id="main">
	<?php echo $this->Session->flash();?>	
	<div class="customTriggers ">
		<?php echo $this->element('nav-header-lists',array('postData'=>array('pluralHumanName'=>'Custom Triggers','modelClass'=>'CustomTrigger','options'=>array("sr_no"=>"Sr No","name"=>"Name","details"=>"Details","file_name"=>"File Name","changed_field_value"=>"Changed Field Value","notify_user"=>"Notify User","notify_users"=>"Notify Users","if_added"=>"If Added","if_edited"=>"If Edited","if_publish"=>"If Publish","if_approved"=>"If Approved","if_soft_delete"=>"If Soft Delete","recipents"=>"Recipents","cc"=>"Cc","bcc"=>"Bcc","subject"=>"Subject","message"=>"Message"),'pluralVar'=>'customTriggers'))); ?>

		<script type="text/javascript">
			$(document).ready(function(){
				$('table th a, .pag_list li span a').on('click', function() {
					var url = $(this).attr("href");
					$('#main').load(url);
					return false;
				});
			});
		</script>	
		<div class="table-responsive" style="overflow:scroll;">
			<?php echo $this->Form->create(array('class'=>'no-padding no-margin no-background'));?>				
			<table cellpadding="0" cellspacing="0" class="table table-hover">
				<tr>
					<th><?php echo $this->Paginator->sort('custom_table_id'); ?></th>
					<th><?php echo $this->Paginator->sort('name'); ?></th>
					<th><?php echo $this->Paginator->sort('field_name'); ?></th>
					<th><?php echo $this->Paginator->sort('changed_field_value'); ?></th>
					<th><?php echo $this->Paginator->sort('notify_user'); ?></th>
					<th><?php echo $this->Paginator->sort('prepared_by'); ?></th>		
					<th><?php echo $this->Paginator->sort('approved_by'); ?></th>		
					<th>Actions</th>
				</tr>
				<?php if($customTriggers){ ?>
					<?php foreach ($customTriggers as $customTrigger): ?>
						<tr>
							<td><?php echo $this->Html->link($customTrigger['CustomTable']['name'], array('controller' => 'custom_tables', 'action' => 'view', $customTrigger['CustomTable']['id'])); ?></td>
							<td><?php echo h($customTrigger['CustomTrigger']['name']); ?>&nbsp;</td>
							<td><?php echo h($customTrigger['CustomTrigger']['field_name']); ?>&nbsp;</td>
							<td><?php echo h($customTrigger['CustomTrigger']['changed_field_value']); ?>&nbsp;</td>
							<td><?php echo h($customTrigger['CustomTrigger']['notify_user']); ?>&nbsp;</td>		
							<td><?php echo h($PublishedEmployeeList[$customTrigger['CustomTrigger']['prepared_by']]); ?>&nbsp;</td>
							<td><?php echo h($PublishedEmployeeList[$customTrigger['CustomTrigger']['approved_by']]); ?>&nbsp;</td>
							<td class=" actions">	
								<?php echo $this->element('actions', array('created' => $customTrigger['CustomTrigger']['created_by'], 'postVal' => $customTrigger['CustomTrigger']['id'], 'softDelete' => $customTrigger['CustomTrigger']['soft_delete'])); ?>	
							</td>
						</tr>
					<?php endforeach; ?>
				<?php }else{ ?>
					<tr><td colspan="9">No results found</td></tr>
				<?php } ?>
			</table>
			<?php echo $this->Form->end();?>			
		</div>
		<p>
			<?php echo $this->Paginator->options(array());
			
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
