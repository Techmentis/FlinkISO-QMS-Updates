<style type="text/css">
	.response-table{margin-left: 10px;}.response-table div{margin: 0 !important;padding: 0;}.timeline{margin-top: 20px;}
/*.timeline>li>.timeline-item{box-shadow: 0px 0px 3px 0px rgb(0 0 0 / 40%);}*/

</style>
<div class="row">

	<?php if($approvalComments){ ?>
		<div class="col-md-12">
			<ul class="timeline">
				<?php foreach ($approvalComments as $approvalComment): 

					if($approvalComment['ApprovalComment']['response_status'] == 2){ ?>
						<li>
							<i class="fa fa-check bg-green"></i>
							<div class="timeline-item">
								<span class="time">
									<?php
									if($approvalComment['Approval']['status'] != 1 && empty($approvalComment['ApprovalComment']['response']) && $approvalComment['ApprovalComment']['response_status'] != 1){
										echo "<span class='text-danger'><strong><i>Response pending from ".$approvalComment['User']['name']."</i>.</strong></span>";
									}else{ ?>
										<i class="fa fa-clock-o"></i> <?php echo h($approvalComment['ApprovalComment']['created']); ?>
										<?php } ?>&nbsp;
									</span>
									<!-- <h3 class="timeline-header"><?php echo $approvalComment['User']['name'];?>&nbsp;</h3> -->
									<div class="timeline-body" id="<?php echo $approvalComment['ApprovalComment']['id']?>_td_to_update">
										<p>
											<?php echo $approvalComment['ApprovalComment']['comments'] ?>
											<br />-<?php echo $approvalComment['From']['name'];?>&nbsp;</strong>											
										</p>
										<hr />
										<?php echo $approvalComment['ApprovalComment']['response'] ?> <span class="pull-right"><i class="fa fa-check text-green"></i>&nbsp;<small><?php echo date('Y-m-d H:i:s',strtotime($approvalComment['ApprovalComment']['created'])) ?>&nbsp;&nbsp;</small></span>
										<br />-<?php echo $approvalComment['User']['name'];?>										
									</div>
								</div>
							</li>
						<?php }else{							
							if($approvalComment['ApprovalComment']['user_id'] == $this->Session->read('User.id') || $approvalComment['ApprovalComment']['user_id'] == $this->Session->read('User.employee_id')){
								$replayClass = 'fa-mail-forward';
								$iClass = 'yellow';
							}else{
								$replayClass = 'fa-mail-reply';
								$iClass = 'green';
							}

							if($approvalComment['Approval']['status'] != 1 && empty($approvalComment['ApprovalComment']['response']) && $approvalComment['ApprovalComment']['response_status'] != 1){
								$replayClass = $replayClass .' bg-yellow';
							}
							?>
							
							<li>
								<i class="fa <?php echo $replayClass;?>"></i>
								<div class="timeline-item timeline-item-<?php echo $iClass;?>">
									<span class="time">
										<?php
										if($approvalComment['Approval']['status'] != 1 && empty($approvalComment['ApprovalComment']['response']) && $approvalComment['ApprovalComment']['response_status'] != 1){
											echo "<span class='text-danger'><strong><i>Response pending from ".$approvalComment['User']['name']."</i>.</strong></span>";
										}else{ ?>
											<i class="fa fa-clock-o"></i> <?php echo h($approvalComment['ApprovalComment']['created']); ?>
											<?php } ?>&nbsp;
										</span>
										<h3 class="timeline-header"><?php echo $approvalComment['From']['name'];?>&nbsp;</h3>

										<div class="timeline-body" id="<?php echo $approvalComment['ApprovalComment']['id']?>_td_to_update">
											<div class="row">
												<div class="col-md-12">
													<?php echo h($approvalComment['ApprovalComment']['comments']); ?>&nbsp;
													
													<?php 
													if($approvalComment['Approval']['status'] != 1 && $approvalComment['ApprovalComment']['response_status'] == 0  && ($approvalComment['ApprovalComment']['user_id'] == $this->Session->read('User.id') || $approvalComment['ApprovalComment']['user_id'] == $this->Session->read('User.employee_id'))){
														
														echo $this->Form->input('ApprovalComment.'.$approvalComment['ApprovalComment']['id'].'.response',array(
															'id'=>$approvalComment['ApprovalComment']['id'].'ResponseTxt',
													// 'label'=>false,
															'type'=>'textarea','class'=>'form-control'));
													}else{
														if($approvalComment['ApprovalComment']['response']){
												// echo $approvalComment['ApprovalComment']['response'];
														}else{
												// echo "<br /><span class='text-danger'>Response pending...</span>";
														}
													}?>
												</div>
												<?php if($approvalComment['Approval']['status'] != 1 && $approvalComment['ApprovalComment']['response_status'] == 0  && $this->Session->read('User.is_approver') && ($approvalComment['ApprovalComment']['user_id'] == $this->Session->read('User.id') || $approvalComment['ApprovalComment']['user_id'] == $this->Session->read('User.employee_id'))){ ?>
													<div class="col-md-12">
														<?php echo $this->Form->hidden('comments',
															array(
																'div'=>false, 
																'placeholder'=>'add comments for accept/reject', 
																'class'=>'form-control',
																'label'=>false,
																'type'=>'text',
																'id'=>'Approval'.$approval['Approval']['id'].'Comments',

															)
														);?>
														<?php											
														if($this->Session->read('User.is_approver') == true){
															$approvalStatuses = array(0=>'Pending',1=>'Approved',2=>'Not Approved');
															echo $this->Form->input('ApprovalComment.'.$approvalComment['ApprovalComment']['id'].'.approval_status',array(
														// 'id'=>$approvalComment['ApprovalComment']['id'].'ResponseTxt',
																'type'=>'radio',
																'options'=>$approvalStatuses,
																'default'=>0
															));
														}else{
															echo "<br />";
														}
														?>
													</div>
												<?php } ?>

												<div class="col-md-12 hide">
													<?php 

													if($approvalComment['Approval']['status'] != 1 && $approvalComment['ApprovalComment']['response_status'] == 0  && ($approvalComment['ApprovalComment']['user_id'] == $this->Session->read('User.id') || $approvalComment['ApprovalComment']['user_id'] == $this->Session->read('User.employee_id'))){
														echo '<br />'.$this->Form->input('ApprovalComment.'.$approvalComment['ApprovalComment']['id'].'.user_id',array(
															'id'=>$approvalComment['ApprovalComment']['id'].'UserTo',
															'label'=>false,'options'=>$approversLists,'default'=>$approvalComment['ApprovalComment']['from'])); 
														
													}else{

														if($approvalComment['ApprovalComment']['response']){
															echo '- <strong>'.$approvalComment['User']['name'].'</strong>';
															echo ' | <small><i class="fa fa-clock-o"></i> <span class="time">'.$approvalComment['ApprovalComment']['modified'].'</span></small>';	
														}else{
											// echo "<span class='text-danger'>Response pending...</span>";
														}																	
													}?>&nbsp;

													<?php 
													if($approvalComment['ApprovalComment']['response_status'] == 0  && ($approvalComment['ApprovalComment']['user_id'] == $this->Session->read('User.id') || $approvalComment['ApprovalComment']['user_id'] == $this->Session->read('User.employee_id'))){
														echo '</div><div class="col-md-12">'. $this->Html->link('Respond','javascript:void(0);',array('id'=>$approvalComment['ApprovalComment']['id'].'_link','class'=>'btn btn-sm btn-success','style'=>'margin-top:15px', 'escape'=>false)); 

														echo "<i class='' id='".$approvalComment['ApprovalComment']['id']."_fa'></i>";
													}else{
														echo '<l class="fa fa-check pull-right"></i>';
													}?>&nbsp;	
												</div>
												<script type="text/javascript">
													$("#<?php echo $approvalComment['ApprovalComment']['id'];?>_link").on('click',function(){
														if($("#<?php echo $approvalComment['ApprovalComment']['id']?>ResponseTxt").val() ==''){
															alert('Add Response');
															return false;
														}

														$.ajax({
															url: "<?php echo Router::url('/', true); ?>approval_comments/add_response/id:<?php echo $approvalComment['ApprovalComment']['id']?>/response:" + $("#<?php echo $approvalComment['ApprovalComment']['id'];?>ResponseTxt").val()+"/to:" + $("#<?php echo $approvalComment['ApprovalComment']['id'];?>UserTo").val(),
															type : "POST",
															data : {
																'id':'<?php echo $approvalComment['ApprovalComment']['id']?>',
																'response': $("#<?php echo $approvalComment['ApprovalComment']['id'];?>ResponseTxt").val(),
																'to':$("#<?php echo $approvalComment['ApprovalComment']['id'];?>UserTo").val(),
																'approval_id':'<?php echo h($approvalComment['ApprovalComment']['approval_id']); ?>',
																'approval_status':$("input[name='data[ApprovalComment][<?php echo $approvalComment['ApprovalComment']['id'] ;?>][approval_status]']:checked").val()
															},
															beforeSend: function( xhr ) {
																$("#<?php echo $approvalComment['ApprovalComment']['id'];?>_link").remove();
																$("#<?php echo $approvalComment['ApprovalComment']['id'];?>_fa").addClass('fa fa-refresh fa-spin');
															},                    
															error: function (err) {
																
															},
															success: function(data, result) {
																$("#<?php echo $approvalComment['ApprovalComment']['id']?>_td_to_update").html(data);
																$("#<?php echo $approvalComment['ApprovalComment']['id'];?>_link").removeClass('fa fa-refresh fa-spin');
															},						        
														});	
													})
												</script>
											</div>
										</div>
									</div>
								</li>
							<?php } ?>							
						<?php endforeach; ?>

						<?php 
						if($approvalComment['Approval']['status'] != 1 && empty($approvalComment['ApprovalComment']['response']) && $approvalComment['ApprovalComment']['response_status'] != 1){
							$replayClass = ' fa-exclamation-triangle bg-red';
						}else{
							$replayClass = ' fa-trophy bg-green';
						}	?>
						<li>
							<i class="fa <?php echo $replayClass;?>"></i>
						</li>
					</ul>					
				</div>
			<?php }else{ ?>			
				<!-- Add first response  -->
				<?php if($approval['Approval']['approval_status'] == 0){ ?>
					<div class="col-md-12">
						<ul class="timeline">
							<li>
								<i class="fa fa-mail-reply"></i>
								<div class="timeline-item">
									<span class="time"><i class="fa fa-clock-o"></i> <?php echo h($approval['Approval']['created']); ?></span>
									<h3 class="timeline-header"><?php echo h($approval['From']['name']); ?></h3>
									<div class="timeline-body"><?php echo h($approval['Approval']['comments']); ?></div>
									<div class="timeline-footer">
										<div class="row">
											<div class="col-md-12">
												<?php 
												if($approval['Approval']['user_id'] == $this->Session->read('User.id') || $approval['Approval']['user_id'] == $this->Session->read('User.employee_id')){
													echo $this->Form->input('approval.'.$approval['Approval']['id'].'.user_id',array(
														'id'=>$approval['Approval']['id'].'UserTo',
														'label'=>false,'options'=>$approversLists,'default'=>$approval['Approval']['from'])); 
												}else{
													echo $approval['User']['name'];
												}
											?>&nbsp;
											<?php
											if($approval['Approval']['user_id'] == $this->Session->read('User.id') || $approval['Approval']['user_id'] == $this->Session->read('User.employee_id')){
												echo $this->Form->input('approval.'.$approval['Approval']['id'].'.response',array(
													'id'=>$approval['Approval']['id'].'ResponseTxt',
													'label'=>false,'type'=>'textarea','class'=>'form-control')
											);
											}else{
												
											}?>
										</div>
										<div class="col-md-12">
											<?php if(empty($approval['Approval']['response']) && $approval['Approval']['response_status'] != 1)echo "<small><span class='text-danger'>pending ...</span></small>";?>&nbsp;		
										</div>
										<div class="col-md-12">
											<?php											
											if($this->Session->read('User.id') != $approvalComment['Approval']['from']){
												$approvalStatuses = array(0=>'Pending',1=>'Approved',2=>'Not Approved');
												echo $this->Form->input('ApprovalComment.'.$approvalComment['ApprovalComment']['id'].'.approval_status',array(
													// 'id'=>$approvalComment['ApprovalComment']['id'].'ResponseTxt',
													'type'=>'radio',
													'options'=>$approvalStatuses,
													'default'=>0
												));
											}else{
												echo "<br />";
											}
											?>
										</div>
										<div class="col-md-12">
											<div id="<?php echo $approval['Approval']['id']?>_td_to_update"><?php 
											if($approval['Approval']['user_id'] == $this->Session->read('User.id') || $approval['Approval']['user_id'] == $this->Session->read('User.employee_id')){
												echo $this->Html->link('Reply','javascript:void(0);',array('id'=>$approval['Approval']['id'].'_link_new','class'=>'btn btn-sm btn-success','escape'=>false)); 

												echo "<i class='' id='".$approval['Approval']['id']."_fa'></i>";
											}else{
												echo '<l class="fa fa-check"></i>';
											}?>&nbsp;
										</div>	
									</div>
									<!-- need to pass comment id, to and response only  -->
									<script type="text/javascript">
										$("#<?php echo $approval['Approval']['id'];?>_link_new").on('click',function(){
											if($("#<?php echo $approval['Approval']['id']?>ResponseTxt").val() ==''){
												alert('Add Response');
												return false;
											}

											$.ajax({
												url: "<?php echo Router::url('/', true); ?>approval_comments/add_response/approval_id:<?php echo $approval['Approval']['id']?>/response:" + $("#<?php echo $approval['Approval']['id'];?>ResponseTxt").val()+"/to:" + $("#<?php echo $approval['Approval']['id'];?>UserTo").val(),
												type : "POST",
												data : {															
													'response': $("#<?php echo $approval['Approval']['id'];?>ResponseTxt").val(),
													'to':$("#<?php echo $approval['Approval']['id'];?>UserTo").val(),
													'approval_id':'<?php echo $approval['Approval']['id']?>',
													'approval_status':$("input[name='data[ApprovalComment][<?php echo $approvalComment['ApprovalComment']['id'] ;?>][approval_status]']:checked").val()
												},
												beforeSend: function( xhr ) {
													$("#<?php echo $approval['Approval']['id']?>_link_new").remove();
													$("#<?php echo $approval['Approval']['id']?>_fa").addClass('fa fa-refresh fa-spin');
												},                    
												error: function (err) {
													
												},
												success: function(data, result) {
													$("#<?php echo $approval['Approval']['id']?>_td_to_update").html(data);
												},						        
											});	
										})
									</script>
								</div>
							</div>
						</li>
					</div>
				</div>
			<?php }else{ ?>								
				<div class="col-md-12">
					<ul class="timeline">
						<li>
							<i class="fa fa-check bg-green"></i>
							<div class="timeline-item">
								<span class="time"><i class="fa fa-clock-o"></i> <?php echo h($approval['Approval']['created']); ?></span>
								<h3 class="timeline-header"><?php echo h($approval['From']['name']); ?></h3>
								<div class="timeline-body">
									<?php echo h($approval['Approval']['comments']); ?>
									<br />-<?php echo h($approval['From']['name']); ?>
								</div>
								<div class="timeline-footer">
									<div class="row">
										<div class="col-md-12">																
										</div>
										<div class="col-md-12">
											<?php echo $approval['Approval']['approver_comments'];?>&nbsp;		
											<br />-<?php echo $approval['Employee']['name'];?>&nbsp;											
										</div>
										<div class="col-md-12">
											
										</div>
										<div class="col-md-12">
											
										</div>
										<!-- need to pass comment id, to and response only  -->
										
									</div>
								</li>											
								<li><i class="fa fa-trophy bg-green"></i></li>									
							</ul>
						</div>
					<?php } ?>
				<?php } ?>
				<script type="text/javascript">
					$().ready(function(){				
						$('select').chosen();
					});
				</script>
