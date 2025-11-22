<div id="customTriggers_ajax">
	<?php echo $this->Html->script(array('jquery.validate.min', 'jquery-form.min')); ?>
	<?php echo $this->fetch('script'); ?>
	<style type="text/css">
		.chosen-container, .chosen-container-multi{width: 100% !important;}
		#op2{height: 320px;}
	</style> 

	<script> 
		$.validator.setDefaults({
			ignore: null,
			errorPlacement: function(error, element) {    		
				if(element['context']['className'] == 'form-control select error'){
					$(element).next('.chosen-container').addClass('error');
				}else if(element['context']['className'] == 'radio error'){
					$(element).next('legend').addClass('error');
				}else{
					$(element).after(error); 
				}
			},
			submitHandler: function(form) {
				$("#CustomTriggerAddForm").ajaxSubmit({
					url: "<?php echo Router::url('/', true); ?>custom_triggers/add/<?php echo $this->request->params['pass'][0];?>",
					type: 'POST',
					target: '#customTriggers_ajax',
					beforeSend: function(){
						$("#trigger_submit_id").prop("disabled",true);
						$("#trigger_submit_indicator").show();
					},
					complete: function() {
						$("#trigger_submit_id").removeAttr("disabled");
						$("#trigger_submit_indicator").hide();
					},
					error: function(request, status, error) {
                    //alert(request.responseText);
						alert('Action failed!');
					}
				});
			}
		});
		$().ready(function() {

			$(".admin").on('change',function(){
				var admin = $("#CustomTriggerNotifyAdmins").prop('checked');			
				if(admin == false){
					$("#CustomTriggerNotifyHods").prop('checked',true);
					$("#CustomTriggerNotifyAdmins").prop('checked',false);
					
				}			
			});

			$(".hod").on('change',function(){
				var hod = $("#CustomTriggerNotifyHods").prop('checked');
				if(hod == false){
					
					$("#CustomTriggerNotifyAdmins").prop('checked',true);
					$("#CustomTriggerNotifyHods").prop('checked',false);
					
				}
			});

			$('select').chosen();
			$("#triggertask").tabs({
				activate: function (event, ui) {
					var ops = $("#op2").attr('aria-hidden')
					if(ops == 'true'){
						$("#CustomTriggerFieldName").attr('required','required');
						$("#CustomTriggerChangedFieldValue").attr('required','required');
						$("#CustomTriggerNotifyUser").attr('required','required');

						$.validator.setDefaults({
							ignore: null,
							errorPlacement: function(error, element) {    		
								if(element['context']['className'] == 'form-control select error'){
									$(element).next('.chosen-container').addClass('error');
								}else if(element['context']['className'] == 'radio error'){
									$(element).next('legend').addClass('error');
								}else{
									$(element).after(error); 
								}
							}
						});

						$('#CustomTriggerAddForm').validate();

						$('select').each(function() {	
							if($(this).prop('required') == true){
								$(this).rules('add', {
									greaterThanZero: true
								});	
							}
							
						});

						$("#CustomTriggerNotifyAdmins").attr('checked',false);
						$("#CustomTriggerNotifyHods").attr('checked',false);

					}else{    				
						
						$("#CustomTriggerFieldName").removeAttr('required','required');
						$("#CustomTriggerChangedFieldValue").removeAttr('required','required');
						$("#CustomTriggerNotifyUser").removeAttr('required','required');

						$("#CustomTriggerFieldName").rules('remove','greaterThanZero');
						$("#CustomTriggerChangedFieldValue").rules('remove','greaterThanZero');
						$("#CustomTriggerNotifyUser").rules('remove','greaterThanZero');
						$("#CustomTriggerNotifyUser").next('.chosen-container').removeClass('error');


						$("#CustomTriggerFieldName").rules('remove','required');
						$("#CustomTriggerChangedFieldValue").rules('remove','required');

						$("#CustomTriggerNotifyAdmins").prop('checked',true);
						$("#CustomTriggerNotifyHods").prop('checked',true);					

						$("#CustomTriggerFieldName").val('-1').trigger("chosen:updated");
						$("#CustomTriggerChangedFieldValue").val('-1').trigger("chosen:updated");
					}
				}
			}
			);

			$("#CustomTriggerFieldName").on('change',function(){
				$.ajax({
					url: "<?php echo Router::url('/', true); ?><?php echo $this->request->params['controller'] ?>/get_data/"+$("#CustomTriggerFieldName").val()+"/<?php echo $this->request->params['named']['custom_table_id'];?>",
					success: function(data, result) {		        	
						$("#get_data").html(data);	
						$('#CustomTriggerAddForm').validate({        	
							rules: {
								"data[CustomTrigger][changed_field_value]": {
									greaterThanZero: true,
								}
							}
						});
					},
				});	
			});

			jQuery.validator.addMethod("greaterThanZero", function(value, element) {
				return this.optional(element) || (parseFloat(value) != -1);
			}, "Please select the value");

			$('#CustomTriggerAddForm').validate();
			$('select').each(function() {	
				if($(this).prop('required') == true){
					$(this).rules('add', {
						greaterThanZero: true
					});	
				}
				
			});
			$("#trigger_submit_indicator").hide();
			$("#trigger_submit_id").click(function(){
				if($('#CustomTriggerAddForm').valid()){
					$("#trigger_submit_id").prop("disabled",true);
					$("#trigger_submit_indicator").show();
					$('#CustomTriggerAddForm').submit();
				}
			});
		});
	</script>
	<style type="text/css">
		.error, .error .chosen-container{
			border: 1px dotted red;
		}
	</style>

	<div class="customTriggers ">
		<?php echo $this->Session->flash();?>
		<div class="box box-default collapsed-box">
			<div class="box-header data-header" data-widget="collapse">
				<h3 class="box-title"><span class=""><i class="fa fa-bell-o"></i></span>  Email Triggers</h3>
				<div class="box-tools pull-right"><button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button></div>
			</div>
			<div class="box-body">
				<div class="table-responsive" style="overflow:scroll;">						
					<table cellpadding="0" cellspacing="0" class="table table-hover">
						<tr>							
							<th><?php echo __('Subject'); ?></th>
							<th><?php echo __('Message'); ?></th>							
							<th></th>
						</tr>
						<?php if($customTriggers){ ?>
							<?php foreach ($customTriggers as $customTrigger): ?>
								<tr id="<?php echo $customTrigger['CustomTrigger']['id'].'-tr';?>">
									<td><?php echo h($customTrigger['CustomTrigger']['name']); ?>&nbsp;</td>
									<td><?php echo h($customTrigger['CustomTrigger']['message']); ?>&nbsp;</td>
									<td class="text-right">	
										<?php echo $this->Html->link('<i class="fa fa-trash-o"></i>','#',array('id'=>$customTrigger['CustomTrigger']['id'].'-del','escape'=>false));?>

										<script type="text/javascript">
											$("#<?php echo $customTrigger['CustomTrigger']['id'];?>-del").on('click',function(){
												$.ajax({
													url: "<?php echo Router::url('/', true); ?>custom_triggers/delete_trigger",
													type: "POST",
													target: '#customTriggers_ajax',
													dataType: "json",
													contentType: "application/json; charset=utf-8",
													data: JSON.stringify({ id: '<?php echo $customTrigger['CustomTrigger']['id'];?>'}),
													beforeSend: function( xhr ) {										    
													},					
													success: function (result) {
														$("#<?php echo $customTrigger['CustomTrigger']['id'];?>-tr").remove();											
													},
													error: function (err) {
														
													}
												}); 
											});
										</script>

									</td>
								</tr>
							<?php endforeach; ?>
						<?php }else{ ?>
							<tr><td colspan="2">No results found</td></tr>
						<?php } ?>
					</table>
				</div>
				

				<div class="row">
					<div class="customTriggers form col-md-12">
						<?php echo $this->Form->create('CustomTrigger',array('role'=>'form','class'=>'form')); ?>
						<div class="row">							
							<div class="col-md-12">							
								<div class="" id="triggertask">
									<ul>
										<li><a href="#op1">Option 1</a></li>
										<?php if($fieldNames != null){ ?>
											<li><a href="#op2">Option 2</a></li>
										<?php } ?>
										<li></li>
									</ul>
									<div id="op1">
										<div class="row">
											<?php 
											$actions = array('Add','Update','Approved','Delete');
											echo "<div class='col-md-12'>".$this->Form->input('action',array('class'=>'','type'=>'radio', 'default'=>0, 'options'=>$actions)) . '</div>';?>										
										</div>
									</div>
									<?php if($fieldNames != null){ ?>
									<div id="op2">
										<div class="row">
											<?php
											echo "<div class='col-md-6'>".$this->Form->input('field_name',array('class'=>'form-control',)) . '</div>'; 
											echo "<div class='col-md-6' id='get_data'>".$this->Form->input('changed_field_value',array('class'=>'form-control','options'=>array())) . '</div>';
											?>
											<div class="col-md-12"><p><br />Select field name and field value for which you would like to send email.</p></div>
										</div>
									</div>
									<?php } ?>
								</div>	
							</div>
							<div class="col-md-12"><h4>Send Email To</h4></div>
							<?php 
							if(isset($notifyDepartments)){
								echo "<div class='col-md-12'>".$this->Form->input('notify_departments',array('class'=>'nd','default'=>1,'label'=>'Everyone in selected Department')) . '</div>';
							}

							if(isset($notifyBranches)){
								echo "<div class='col-md-12'>".$this->Form->input('notify_branches',array('class'=>'nb','default'=>1,'label'=>'Everyone in selected Branches')) . '</div>';
							}

							if(isset($notifyBranchesDesignations)){
								echo "<div class='col-md-12'>".$this->Form->input('notify_designations',array('class'=>'nb','default'=>1,'label'=>'Everyone with selected designation')) . '</div>';
							}

							echo "<div class='col-md-12'><hr /></div>";

							echo "<div class='col-md-12'>".$this->Form->input('notify_admins',array('class'=>'admin','default'=>1, 'label'=>'Admins')) . '</div>';
							echo "<div class='col-md-12'>".$this->Form->input('notify_hods',array('class'=>'hod','default'=>1,'label'=>'Department HoDs')) . '</div>';
							echo "<div class='col-md-12'>".$this->Form->input('hod_departments',array('label'=>'Select Departments','multiple', 'options'=>array($departments))) . '</div>';							
							?>
							<div class="col-md-12"><p><br /><strong>Note: </strong>If table has a department field, and if you chose Notify HoDs, HoD of that department will automaticaly receive the email. To send email to additional HoDs from different departments, chose thode departments from "Select Departments" field.</p></div>											
							<?php	
							echo "<div class='col-md-12'>".$this->Form->input('notify_user',array('class'=>'form-control',)) . '</div>'; 
							echo "<div class='col-md-12'>".$this->Form->input('notify_users',array('class'=>'form-control','type'=>'select', 'multiple', 'options'=>$employees )) . '</div>';
							?>
							<div class="col-md-12"><h4>Email Subject/ Body</h4></div>
							<?php	
							echo "<div class='col-md-12'>".$this->Form->hidden('custom_table_id',array('class'=>'form-control', 'style'=>'','default'=>$this->request->params['named']['custom_table_id'])) . '</div>';		
							echo "<div class='col-md-12'>".$this->Form->input('name',array('label'=>'Subject', 'required'=>'required', 'class'=>'form-control',)) . '</div>'; 
							
							
							echo "<div class='col-md-12'>".$this->Form->input('message',array('required'=>'required', 'label'=>'Message body to be send to recepient', 'class'=>'form-control')) . '</div>';
							// echo "<div class='col-md-4'>".$this->Form->input('if_edited',array('class'=>'',)) . '</div>'; 
							// echo "<div class='col-md-4'>".$this->Form->input('if_publish',array('class'=>'',)) . '</div>'; 
							// echo "<div class='col-md-4'>".$this->Form->input('if_approved',array('class'=>'',)) . '</div>'; 
							// echo "<div class='col-md-4'>".$this->Form->input('if_soft_delete',array('class'=>'',)) . '</div>';						
							echo $this->Form->input('id');
							echo $this->Form->hidden('History.pre_post_values', array('value'=>json_encode($this->data)));
							echo $this->Form->input('branchid', array('type' => 'hidden', 'value' => $this->Session->read('User.branch_id')));
							echo $this->Form->input('departmentid', array('type' => 'hidden', 'value' => $this->Session->read('User.department_id')));
							echo $this->Form->input('master_list_of_format_id', array('type' => 'hidden', 'value' => $documentDetails['MasterListOfFormat']['id']));
							?>

						</div>
						<div class="">	
							<?php echo $this->Form->submit(__('Submit'), array('div' => false, 'style'=>'margin:10px 0', 'class' => 'btn btn-primary btn-success','id'=>'trigger_submit_id')); ?>
							<?php echo $this->Html->image('indicator.gif', array('id' => 'trigger_submit_indicator')); ?>
							<?php echo $this->Form->end(); ?>
							<?php echo $this->Js->writeBuffer();?>
						</div>
					</div>
				</div>
			</div>
		</div>
