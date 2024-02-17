<?php echo $this->element('checkbox-script'); ?>
<style type="text/css">
	.highlights{
		border: 2px dashed #e81e1e;
		border-collapse: collapse;
	}
</style>
<div id="main"> <?php echo $this->Session->flash(); ?>
<div class="branches ">
	<h4>Approvals (Pending for approvals) <small class="pull-right"><?php echo $this->Html->link('View Approved Records',array('action'=>'approved')); ?></small></h4>		
	<script type="text/javascript">
		$(document).ready(function() {
			$("#ApprovalDates").daterangepicker({
				startDate : '<?php echo date("m/d/Y",strtotime($startDate))?>',
				endDate : '<?php echo date("m/d/Y",strtotime($endDate))?>',
			});

			$('table th a, .pag_list li span a').on('click', function() {
				var url = $(this).attr("href");
				$('#main').load(url);
				return false;
			});
		});
		
		$(document).ready(function(){    
			$("#filter").keyup(function(){
				var filter = $(this).val(), count = 0;
				$(".table .on_page_src").each(function(){
					if ($(this).text().search(new RegExp(filter, "i")) < 0) {
						$(this).hide();
					} else {             
						$(this).show();
						count++;
					}
				});
				var numberItems = count;
				$("#filter-count").text(""+count);
			});
		});
	</script>		
	<div class="row">
		<?php echo $this->Form->create('Approval');?>
		<div class="col-md-4"><?php echo $this->Form->input('model_name',array('class'=>''))?></div>
		<div class="col-md-4"><?php echo $this->Form->input('from',array('class'=>''))?></div>
		<div class="col-md-4"><?php echo $this->Form->input('to',array('class'=>''))?></div>
		<div class="col-md-4"><?php echo $this->Form->input('status',array('class'=>''))?></div>
		<div class="col-md-4"><?php echo $this->Form->input('record_status',array('class'=>''))?></div>
		<div class="col-md-4"><?php echo $this->Form->input('publish_status',array('class'=>''))?></div>
		<div class="col-md-4"><?php echo $this->Form->input('dates',array('class'=>'form-control'))?></div>        		
		<div class="col-md-3"><br /><?php echo $this->Form->submit('Submit',array('class'=>'btn btn-sm btn-success'));?></div>
		<?php echo $this->Form->end();?>
	</div>
	<div class="row">
		<div class="col-md-12"><h2>Result</h2></div>
		<div class="col-md-12">
			<div class="table table-responsive"> <?php echo $this->Form->create(array('class' => 'no-padding no-margin no-background')); ?>
			<table cellpadding="0" cellspacing="0" class="table table-responsive table-striped table-hover">
				<tr>
					<th><?php echo $this->Paginator->sort('name', __('Name')); ?></th>
					<th><?php echo $this->Paginator->sort('created'); ?></th>
					<th><?php echo $this->Paginator->sort('from'); ?></th>
					<th><?php echo $this->Paginator->sort('to'); ?></th>						
					<th><?php echo $this->Paginator->sort('comments'); ?></th>
					<th><?php echo $this->Paginator->sort('approval_status'); ?></th>
					<th><?php echo $this->Paginator->sort('lock_status'); ?></th>
					<th><?php echo $this->Paginator->sort('record_status'); ?></th>
					<th width="110px"><?php echo $this->Paginator->sort('action'); ?></th>
					<th><?php echo $this->Paginator->sort('count'); ?></th>
				</tr>
				<?php
				if ($approvals) {
					$x = 0;
					foreach ($approvals as $model => $datas): ?>
						<tr><th colspan="10"><h3><?php echo $model?></h3></th></tr>
						<?php foreach ($datas as $rec => $apps) { 
							$x = 0;
							foreach ($apps as $approval) { ?>
								<?php if($approval['Approval']['status'] == 'Approved')$class="text-success";
								if($approval['Approval']['status'] == 'Sent Back')$class="text-danger";
								if($approval['Approval']['status'] == 'Forwarded')$class="text-warning";
								if($x > 0)$class = 'hide ' . $rec;
								?>
								<tr class="on_page_src <?php echo $class ?> <?php echo $rec?>-all">
									<!-- <td width="100"></td> -->
									<td><?php 
									echo $this->Html->link($approval['Approval']['model_name']." (".$approval['Approval']['title'].")",array('controller'=>$approval['Approval']['controller_name'],'action'=>'view',$approval['Approval']['record']),array('target'=>'_blank')) ; ?>&nbsp;</td>
									<td><?php echo $approval['Approval']['created'];?>&nbsp;</td>
									<td><?php echo $froms[$approval['Approval']['from']]; ?>&nbsp;</td>
									<td><?php echo $froms[$approval['Approval']['user_id']]; ?>&nbsp;</td>										
									<td><?php echo $approval['Approval']['comments']; ?>&nbsp;</td>
									<td><?php 						
									echo $approval['Approval']['record_status']?'Locked':'Unlocked'; ?>&nbsp;</td>
									<td><?php echo $approval['Approval']['status'];?></td>
									<td><?php echo $approval['Approval']['record_published']?'Published':'Unbublished';?></td>
									<td>
										<!-- <div class="btn-group"> 						 -->
											
											<?php if($approval['Approval']['app_record_status'] == 1){ ?> 
												<!-- <span class="btn btn-xs btn-danger"><span class=" glyphicon glyphicon-remove"></span></span> -->
											<?php } else { ?> 
												
											<?php } ?>
											
											
											<?php						
											
											if($approval['Approval']['status'] == 'Approved'){ echo "Approved"; }else{
												?>
												<?php if($this->Session->read('User.is_mr') == true){ ?>
													<div class="dropdown">
														<?php 

														if(!$approval['Approval']['app_record_status'] or !$approval['Approval']['record_published'] or $approval['Approval']['app_record_status'] == 1 or $approval['Approval']['record_published'] == 1){ ?> 
															<button class="btn btn-default btn-xs dropdown-toggle" type="button" id="dropdownMenu<?php echo $approval['Approval']['id'];?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
																Pending...
																<span class="caret"></span>
															</button>
															<ul class="dropdown-menu pull-right" aria-labelledby="dropdownMenu<?php echo $approval['Approval']['id'];?>">
																<li>
																	<?php echo $this->Html->link("view", array('controller' => $approval['Approval']['controller_name'], 'action' => 'view', $approval['Approval']['record']), array('target'=>'_blank')); ?></li>
																	<li>
																		<?php echo $this->Html->Link('Send Reminder to ' . $froms[$approval['Approval']['user_id']],array('controller'=>'approvals','action'=>'send_reminder',$approval['Approval']['id']), array('target'=>'_blank')); ?></li>
																		<li><?php 
																		echo $this->Html->Link('Unlock Record',array('controller'=>'approvals','action'=>'unlock_record',$approval['Approval']['id']), array('target'=>'_blank')); ?></li>
																		<li><?php echo $this->Html->Link('Assign to Another User',array('controller'=>'approvals','action'=>'change_user',$approval['Approval']['id']), array('target'=>'_blank')); ?></li>
																		<li><?php echo $this->Html->Link('Delete Record',array('controller'=>'approvals','action'=>'delete_record',$approval['Approval']['id']), array('target'=>'_blank')); ?></li>
																	</ul>	
																<?php }else { ?>				    
																	<button class="btn btn-default btn-xs dropdown-toggle" type="button" id="dropdownMenu<?php echo $approval['Approval']['id'];?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
																		Approved
																		<span class="caret"></span>
																	</button>
																	<ul class="dropdown-menu pull-right" aria-labelledby="dropdownMenu<?php echo $approval['Approval']['id'];?>">
																		<li><?php echo $this->Html->link("view", array('controller' => $approval['Approval']['controller_name'], 'action' => 'view', $approval['Approval']['record']), array('target'=>'_blank', 'class'=>'')); ?></li>
																	</ul>
																	
																	
																<?php } ?>
																
															</div>
														<?php } } ?>
														<td width="120">
															<span class="badge"><?php if($x == 0) echo count($apps)?></span>
															<?php if(count($apps) > 1 && $x == 0){ ?>
																<div class="btn-group">
																	<span class="btn btn-xs btn-success" id="<?php echo $rec?>">Show</span> 
																	<span class="btn btn-xs btn-danger" id="<?php echo $rec?>hide">Hide</span>
																</div>
																<script type="text/javascript">
																	$("#<?php echo $rec?>").on('click',function(){
																		$(".<?php echo $rec?>").removeClass('hide');
																		$(".<?php echo $rec?>-all").addClass('highlights');
																	});
																	$("#<?php echo $rec?>hide").on('click',function(){
																		$(".<?php echo $rec?>").removeClass('show').addClass('hide');
																		$(".<?php echo $rec?>-all").removeClass('highlights');
																	});
																</script>
															<?php } ?>
														</td>
														<!-- </div> -->
													</td>				
												</tr>									
												<?php $x++;}?>
												
											<?php } ?>	
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
							</div>
							<?php echo $this->Form->end(); ?> </div>
							<p>
								<?php
                // echo $this->Paginator->options(array(
                //     'update' => '#main',
                //     'evalScripts' => true,
                //     'before' => $this->Js->get('#busy-indicator')->effect('fadeIn', array('buffer' => false)),
                //     'complete' => $this->Js->get('#busy-indicator')->effect('fadeOut', array('buffer' => false)),
                // ));

                // echo $this->Paginator->counter(array(
                //     'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
                // ));
								?>
							</p>
							<ul class="pagination">
								<?php
                // echo "<li class='previous'>" . $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled')) . "</li>";
                // echo "<li>" . $this->Paginator->numbers(array('separator' => '')) . "</li>";
                // echo "<li class='next'>" . $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled')) . "</li>";
								?>
							</ul>
						</div>
					</div>
				</div>
				<?php echo $this->Js->writeBuffer(); ?> 
				<script>


					$.ajaxSetup({beforeSend: function() {
						$("#busy-indicator").show();
					}, complete: function() {
						$("#busy-indicator").hide();
					}});
				</script>
