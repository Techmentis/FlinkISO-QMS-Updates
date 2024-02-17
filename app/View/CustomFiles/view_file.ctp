<?php echo $this->element('checkbox-script'); ?><div  id="main">
	<?php echo $this->Session->flash();?>	
	<div class="customFiles ">
		<?php echo $this->element('nav-header-lists',array('postData'=>array('pluralHumanName'=>'Custom Files','modelClass'=>'CustomFile','options'=>array("sr_no"=>"Sr No","model"=>"Model","controller"=>"Controller","record"=>"Record","file_name"=>"File Name","file_type"=>"File Type","action"=>"Action"),'pluralVar'=>'customFiles'))); ?>

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
			<table cellpadding="0" cellspacing="0" class="table table-hover">
				<tr>
					<th><?php echo $this->Paginator->sort('user_id'); ?></th>
					<th><?php echo $this->Paginator->sort('employee_id'); ?></th>
					<th><?php echo __('Date/time') ?></th>
					<th>Actions</th>
				</tr>
				<?php if($customFiles){ ?>
					<?php foreach ($customFiles as $customFile): ?>
						<tr>
							<td><?php echo $this->Html->link($customFile['User']['name'], array('controller' => 'users', 'action' => 'view', $customFile['User']['id'])); ?></td>
							<td><?php echo $this->Html->link($customFile['Employee']['name'], array('controller' => 'employees', 'action' => 'view', $customFile['Employee']['id'])); ?></td>
							<td><?php echo h($customFile['CustomFile']['action']?'Delete':'Download'); ?>&nbsp;</td>
							<td><?php echo h($customFile['CustomFile']['created']); ?>&nbsp;</td>
						<?php endforeach; ?>
					<?php }else{ ?>
						<tr><td colspan="4">No results found</td></tr>
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
