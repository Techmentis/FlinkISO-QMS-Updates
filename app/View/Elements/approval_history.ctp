<?php
echo $this->Form->hidden('Access.skip_access_check',array('default'=>1));
echo $this->Form->hidden('Access.allow_access_user',array('default'=>$this->Session->read('User.id')));
?>
<?php if($approvals){ ?>
	<style type="text/css">
		.font-weight-bold{font-weight: 600;color: #3566b1 !important;}.table-border-dark{border: 1px solid #000;}.btn .badge{position: absolute;top: -10px;}
	</style>
	<?php
	$approvalStatuses = array(0=>'Pending...',1=>'Approved',2=>'Not Approved');
	?>
	<div class="row">
		<div class="col-md-12">
			<div class="box box-warning">
				<div class="box-header with-border">
					<?php
						if(isset($customTable) && $customTable['CustomTable']['name'])$approvalPanelTitle = $customTable['CustomTable']['name'];
						else $approvalPanelTitle = Inflector::humanize($this->request->controller);
					?>
					<h3 class="box-title">Approval History for <?php echo $approvalPanelTitle;?>
					<?php if($this->action == 'edit'){ ?><small>Prepared By : <?php echo $this->request->data['PreparedBy']['name'];?></small><?php } ?></h3>
					<i class="fa  fa-clock-o pull-right"></i>
				</div>
				<div class="box-body" style="padding: 0;">
					<table cellpadding="0" cellspacing="0" class="table table-responsive">
						<tr >
							<th>Date/time</th>
							<th>From</th>
							<th>To</th>
							<th class="text-right">Approve / Reject</th>
						</tr>
						
						<?php foreach ($approvals as $approval):
							if($approval['Approval']['approval_mode'] == 1 && $approval['Approval']['approval_status'] == 0){ 
								if($this->viewVars['customTable']['CustomTable']['table_name']){
								?>
								<script type="text/javascript">
									$().ready(function(){
										$(".approval_checkbox_div").html('<div class="text-danger">You can not Publish this record unless everyone approves it as Approval Type is ALL.<br /></div>');
										console.log("<?php echo Inflector::Classify($this->viewVars['customTable']['CustomTable']['table_name']);?>Publish");	
									})
									
								</script>
							<?php } }

							$appClass = '';
							if($approval['Approval']['id'] == $current_approval)$class = ' font-weight-bold';
							else $class = '';

							if($approval['Approval']['status'] == 1)$appClass = ' text-success';
							elseif($approval['Approval']['status'] == 2)$appClass = ' text-danger';

							if($approval['Approval']['status'] == 1)$badgeClass = ' bg-green';
							elseif($approval['Approval']['status'] == 2)$badgeClass = ' bg-red';
							else $badgeClass = ' bg-red';
							?>
							<?php if($class == ' font-weight-bold'){ ?>					
								<script type="text/javascript">
									$().ready(function(){
										$('#<?php echo $approval['Approval']['id'];?>_div').load('<?php echo Router::url('/', true); ?>approval_comments/approval_comments/approval_id:<?php echo $approval['Approval']['id'];?>/action:<?php echo $this->action;?>');
									});
								</script>
							<?php } ?>				
							<tr class="<?php echo $class;?> <?php echo $appClass;?>" onclick="showcomments('<?php echo $approval['Approval']['id']?>','<?php echo $approval['Approval']['id']?>_i');" style="cursor: pointer;">
								<td><?php echo h($approval['Approval']['created']); ?>&nbsp;</td>
								<td><?php echo h($approval['From']['name']); ?>&nbsp;</td>
								<td><?php echo h($approval['Employee']['name']); ?></td>
								<td>
									<?php if($approval['Approval']['user_id'] == $this->Session->read('User.id') && $approval['Approval']['status'] != 1){ ?>
										<div class="btn-group pull-right">
											<?php if(count($approval['ApprovalComment']) == 0)$cnt = 1;
											else $cnt = count($approval['ApprovalComment']);
											?>									
											<span class="badge <?php echo $badgeClass;?>"><?php echo $cnt;?></span>					                
										</div>
									<?php } else { ?>
										<div class="btn-group pull-right">
											<?php if(count($approval['ApprovalComment']) == 0)$cnt = 1;
											else $cnt = count($approval['ApprovalComment']);?>
											<span class="badge <?php echo $badgeClass;?>"><?php echo $cnt;?></span>					                
										</div>
									<?php } ?>
								</td>
							</tr>
							<tr>
								<td colspan="6" style="padding:0" id="<?php echo $approval['Approval']['id'];?>_tr">
									<div id="<?php echo $approval['Approval']['id'];?>_div"></div>
								</td>
							</tr>					
							
						<?php endforeach; ?>				
					</table>
				</div>
			</div>
		</div>
	</div>
<?php $approval = array();?>	
	<script type="text/javascript">	
		function approve(approval_id,comments,status){
			var txt;
			var r = confirm("Are you sure you want to approve this record?");
			if (r == true) {
				$('#'+approval_id+'_div').load('<?php echo Router::url('/', true); ?>approvals/action/id:'+approval_id+'/comments:'+comments);		  	
			} else {
				return false;
			}
		}

		function reject(approval_id,comments){
			
		}

		function showcomments(approval_id,fa_id){
			$("#"+fa_id).removeClass('fa-list').addClass('fa-refresh fa-spin');
			
			if($('#'+approval_id+'_div').html().length == 0){			
				$('#'+approval_id+'_div').load('<?php echo Router::url('/', true); ?>approval_comments/approval_comments/approval_id:'+approval_id +'/custom_table_id:<?php echo $customTable["CustomTable"]["id"];?>', function(responseTxt, statusTxt, xhr){			
					if(statusTxt == 'success'){
						$("#"+fa_id).removeClass('fa-refresh fa-spin').addClass('fa-check');	
					}
				});	
			}else{
				$('#'+approval_id+'_div').html('');
			}

			
		}							
	</script>
<?php }else{ ?> 
	<!-- add 1st response -->
<?php } ?>
