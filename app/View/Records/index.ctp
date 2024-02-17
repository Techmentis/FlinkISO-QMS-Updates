<?php echo $this->element('checkbox-script'); ?><div  id="main">
	<?php echo $this->Session->flash();?>	
	<div class="records ">
		<?php echo $this->element('nav-header-lists',array('postData'=>array('pluralHumanName'=>'Records','modelClass'=>'Record','options'=>array("sr_no"=>"Sr No","comments"=>"Comments"),'pluralVar'=>'records'))); ?>

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
					<th><?php echo $this->Paginator->sort('title'); ?></th>
					<th><?php echo $this->Paginator->sort('qc_document_id'); ?></th>
					<th><?php echo $this->Paginator->sort('created'); ?></th>
					<th><?php echo $this->Paginator->sort('comments'); ?></th>
					<th><?php echo $this->Paginator->sort('prepared_by'); ?></th>		
					<th><?php echo $this->Paginator->sort('approved_by'); ?></th>		
					<th><?php echo $this->Paginator->sort('publish'); ?></th>
					<th></th>
				</tr>
				<?php if($records){ ?>
					<?php foreach ($records as $record): ?>
						<tr>
							<td><?php echo $this->Html->link($record['Record']['title'], array('controller' => 'records', 'action' => 'view', $record['Record']['id'])); ?></td>
							<td><?php echo $this->Html->link($record['QcDocument']['title'], array('controller' => 'qc_documents', 'action' => 'view', $record['QcDocument']['id'])); ?></td>
							<td><?php echo h($record['Record']['created']); ?>&nbsp;</td>
							<td><?php echo h($record['Record']['comments']); ?>&nbsp;</td>
							<td><?php echo h($record['PreparedBy']['name']); ?>&nbsp;</td>
							<td><?php echo h($PublishedEmployeeList[$record['Record']['approved_by']]); ?>&nbsp;</td>

							<td width="60">
								<?php if($record['Record']['publish'] == 1) { ?>
									<span class="fa fa-check"></span>
								<?php } else { ?>
									<span class="fa fa-close"></span>
									<?php } ?>&nbsp;
								</td>
								<td class=" actions">	
									<?php echo $this->element('actions', array('created' => $record['Record']['created_by'], 'postVal' => $record['Record']['id'], 'softDelete' => $record['Record']['soft_delete'])); ?>	</td>		
								</tr>
							<?php endforeach; ?>
						<?php }else{ ?>
							<tr><td colspan=54>No results found</td></tr>
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
