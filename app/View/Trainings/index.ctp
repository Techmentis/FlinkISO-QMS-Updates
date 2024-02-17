<?php echo $this->element('checkbox-script'); ?><div  id="main">
	<?php echo $this->Session->flash();?>	
	<div class="Trainings ">
		<?php echo $this->element('billing_header_lists',array('header'=>'Traning'));?>

		<style type="text/css">
			.video-container{
				position: relative;
				width: 100%;
				height: 0;
				padding-bottom: 56.25%;
				border: 10px solid #fff;
				box-shadow: 0 0 10px 2px #b3b3b3;
			}
			.video{

				position: absolute;
				top: 0;
				left: 0;
				width: 100%;
				height: 100%;
			}
		</style>
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
					<th><?php echo $this->Paginator->sort('schedule_date'); ?></th>
					<th><?php echo $this->Paginator->sort('employee_name'); ?></th>
					<th><?php echo $this->Paginator->sort('notes'); ?></th>
					<th><?php echo $this->Paginator->sort('inoice_id'); ?></th>
					<th><?php echo $this->Paginator->sort('training_status'); ?></th>
					<th width="240">Actions</th>
				</tr>
				<?php if($trainings){ ?>
					<?php foreach ($trainings as $training): ?>
						<tr class="on_page_src" onclick="addrec('<?php echo $training['Training']['id'];?>')" id="<?php echo $training['Training']['id'];?>_tr">
							<td><?php echo h($training['Training']['schedule_date']); ?>&nbsp;</td>
							<td><?php echo h($training['Training']['employee_name']); ?>&nbsp;</td>
							<td><?php echo h($training['Training']['notes']); ?>&nbsp;</td>
							<td><?php echo h($training['Invoice']['status']); ?>&nbsp;</td>
							<td><?php echo h($trainingStatus[$training['Training']['training_status']]); ?>&nbsp;</td>
							<td class=" actions">
								<div class="btn-group">
									<?php
									if($training['Invoice']['status'] == 'draft'){
										echo $this->Html->link('View Invoice',array('controller'=>'billing','action'=>'view_invoice',$training['Invoice']['id']),array('class'=>'btn btn-xs btn-success'));
										echo $this->Html->link('Re-schedule',array('controller'=>'trainings','action'=>'edit',$training['Training']['id']),array('class'=>'btn btn-xs btn-info'));
										echo $this->Html->link('Cancle',array('controller'=>'trainings','action'=>'edit',$training['Training']['id']),array('class'=>'btn btn-xs btn-warning'));
										;
									}
									?>
								</div>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php }else{ ?>
					<tr><td colspan="7">No results found</td></tr>
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
