<div id="approvalProcesses_ajax">
	<?php echo $this->Session->flash();?>	
	<div class="approvalProcesses ">
		<?php echo $this->element('nav-header-lists',array('postData'=>array('pluralHumanName'=>'Approval Processes','modelClass'=>'ApprovalProcess','options'=>array(),'pluralVar'=>'approvalProcesses'))); ?>
		<div class="nav panel panel-default">
			<div class="approvalProcesses form col-md-12 table-responsive">
				<table class="table table-responsive">
					<tr><th colspan="2"><?php echo h($approvalProcess['ApprovalProcess']['title']); ?>&nbsp;</th></tr>
					<tr><td colspan="2"><?php echo h($approvalProcess['ApprovalProcess']['process_description']); ?>&nbsp;</td></tr>
					<tr><td>
						<h4>Applicable To</h4>
						<ul class="list-group">
						<?php 
						$applicableTos = json_decode($approvalProcess['ApprovalProcess']['applicable_to'],true);
						foreach($applicableTos as $applicableTo){
							echo '<li class="list-group-item">'.  Inflector::humanize($customTables[$applicableTo]). '</li>';
						}?>
						</ul>
						</td>
						<td>
							<div class="col-md-12 table-responsive">
								<h4>Approval Steps</h4>
								<table class="table table-bordered table-condenced">
									<thead>
										<tr>
											<th rowspan="2" width="80">#</th>
											<th rowspan="2">Title</th>
											<th rowspan="2">Comment</th>
											<th colspan="6">Send To</th>							
										</tr>
										<tr>
											<th>Reviwer</th>							
											<th>Approver</th>
											<th>HOD</th>
											<th>Admin</th>							
											<th>Designation</th>										
											<th>Users</th>
											<th>Publisher</th>
											<th>Action</th>
										</tr>
									</thead>
									<tbody>
										<?php foreach($approvalProcess['ApprovalStep'] as $approvalStep){ ?>
											<tr>
												<td><?php echo $approvalStep['process_step'];?></td>
												<td><?php echo $approvalStep['title'];?></td>
												<td><?php echo $approvalStep['comments'];?></td>
												
												<td><?php if($approvalStep['send_to_reviwers'])echo '<i class="fa fa-check"></i>'; ?></td>								
												<td><?php if($approvalStep['send_to_approvers'])echo '<i class="fa fa-check"></i>'; ?></td>
												<td><?php if($approvalStep['send_to_department_hod'])echo '<i class="fa fa-check"></i>'; ?></td>
												<td><?php if($approvalStep['send_to_admins'])echo '<i class="fa fa-check"></i>'; ?></td>
												
												<td><?php 
												if($approvalStep['send_to_designation']){
													echo $PublishedDesignationList[$approvalStep['send_to_designation']];	
												}
												?></td>
												<td><?php echo $approvalStep['send_to_users'];?></td>	
												<td><?php if($approvalStep['send_to_publishers'])echo '<i class="fa fa-check"></i>'; ?></td>
												<td><?php echo $this->Form->postLink('<i class="fa fa-minus btn-remove-record"></i>',
													array('controller'=>'approval_steps','action'=>'delete',$approvalStep['id']),
													array('confirm'=>'Are you sure you want to delete this record?','class'=>'btn btn-xs btn-default','escape'=>false));
													?>								
												</td>
											</tr>
										<?php }?>

									</tbody>
								</table>
							</div>
						</td>
					</tr>					
				</table>
			</div>						
		</div>
		<?php echo $this->Js->writeBuffer();?>
	</div>	
</div>	
<script>$.ajaxSetup({beforeSend:function(){$("#busy-indicator").show();},complete:function(){$("#busy-indicator").hide();}});</script>
