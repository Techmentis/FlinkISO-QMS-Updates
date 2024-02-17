<?php echo $this->element('checkbox-script'); ?>

<div id="main"> <?php echo $this->Session->flash(); ?>
<div class="branches ">
	<h4>Approvals (Approved records) <small class="pull-right"><?php echo $this->Html->link('View Pending Records',array('action'=>'index')); ?></small></h4>		
	<script type="text/javascript">
		$(document).ready(function() {
			$('table th a, .pag_list li span a').on('click', function() {
				var url = $(this).attr("href");
				$('#main').load(url);
				return false;
			});
		});
	</script>
	<div class="alert alert-info"><strong>Note : </strong>If you are a creator of the record, you can direclty access those records from that record's listing page. This page will only list records which are not created by you.</div>
	<div class="table-responsive"> <?php echo $this->Form->create(array('class' => 'no-padding no-margin no-background')); ?>
	<table cellpadding="0" cellspacing="0" class="table table-bordered table table-striped table-hover">
		<tr>
			<th >Action</th>
			<th><?php echo $this->Paginator->sort('name', __('Name')); ?></th>
			<th>Created</th>
			<th><?php echo $this->Paginator->sort('From'); ?></th>
			<th><?php echo $this->Paginator->sort('To'); ?></th>
			<th><?php echo $this->Paginator->sort('comments'); ?></th>
			<th><?php echo $this->Paginator->sort('Status'); ?></th>
		</tr>
		<?php
		if ($approvals) {
			$x = 0;
			foreach ($approvals as $approval):
				?>
				<tr>
					<td width="100"><div class="btn-group"> 						
						<?php echo $this->Html->link("view", array('controller' => $approval['Approval']['controller_name'], 'action' => 'view', $approval['Approval']['record']), array( 'class'=>'btn btn-xs btn-info')); ?> 						
						<script>
							$().ready(function(){$('#countdiv<?php echo $approval['Approval']['id'];?>').load('<?php echo Router::url('/', true); ?>file_uploads/approval_ajax_file_count/<?php echo $approval['Approval']['id'] ?>', function(response, status, xhr){});});</script>
							<div id="countdiv<?php echo $approval['Approval']['id'];?>" class="btn-xs btn btn-primary"></div>
						</div></td>
						<td><?php echo $approval['Approval']['model_name']." (".$approval['Approval']['title'].")"; ?>&nbsp;</td>
						<td><?php echo $approval['Approval']['created'];?>&nbsp;</td>
						<td><?php echo $userList[$approval['Approval']['from']]; ?>&nbsp;</td>
						<td><?php echo $PublishedEmployeeList[$approval['Approval']['user_id']]; ?>&nbsp;</td>
						<td><?php echo $approval['Approval']['comments']; ?>&nbsp;</td>
						<td>
							<?php if($approval['Approval']['status'] == 'Approved'){ echo "Approved"; }else{?>
								<?php if($this->Session->read('User.is_mr'== true)){ ?>
									<div class="dropdown">
										<button class="btn btn-default btn-xs dropdown-toggle" type="button" id="dropdownMenu<?php echo $approval['Approval']['id'];?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
											Pending ...
											<span class="caret"></span>
										</button>
										<ul class="dropdown-menu pull-right" aria-labelledby="dropdownMenu<?php echo $approval['Approval']['id'];?>">
											<li><?php echo $this->Html->Link('Send Reminder to ' . $userList[$approval['Approval']['user_id']],array('controller'=>'approvals','action'=>'send_reminder',$approval['Approval']['id'])); ?></li>
											<li><?php echo $this->Html->Link('Unlock Record',array('controller'=>'approvals','action'=>'unlock_record',$approval['Approval']['id'])); ?></li>						    
										</ul>
									</div>
								<?php } } ?>
							</td>				
						</tr>
						<?php
						$x++;
					endforeach;
				} else {
					?>
					<tr>
						<td colspan=13><?php echo __('No results found'); ?></td>
					</tr>
				<?php } ?>
			</table>
			<?php echo $this->Form->end(); ?> </div>
			<p>
				<?php
				echo $this->Paginator->options(array(
					'update' => '#main',
					'evalScripts' => true,
					'before' => $this->Js->get('#busy-indicator')->effect('fadeIn', array('buffer' => false)),
					'complete' => $this->Js->get('#busy-indicator')->effect('fadeOut', array('buffer' => false)),
				));

				echo $this->Paginator->counter(array(
					'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
				));
				?>
			</p>
			<ul class="pagination">
				<?php
				echo "<li class='previous'>" . $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled')) . "</li>";
				echo "<li>" . $this->Paginator->numbers(array('separator' => '')) . "</li>";
				echo "<li class='next'>" . $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled')) . "</li>";
				?>
			</ul>
		</div>
	</div>

	<script>
		$.ajaxSetup({beforeSend: function() {
			$("#busy-indicator").show();
		}, complete: function() {
			$("#busy-indicator").hide();
		}});
	</script>
