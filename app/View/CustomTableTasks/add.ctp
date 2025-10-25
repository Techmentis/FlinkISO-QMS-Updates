<div id="create_tasks">
	<?php echo $this->Html->script(array('jquery.validate.min', 'jquery-form.min')); ?>
	<?php echo $this->fetch('script'); ?>
	<?php 
	$fields = json_decode($customTable['Table']['fields'],true);
	foreach($fields as $field){
		if($field['data_type'] == 'radio'){
			$lockRecordField[$field['field_name']]= $field['field_name'];
		}

		if($field['linked_to'] != -1){
			$linkedToTables[$field['linked_to']] = $field['linked_to'];
		}
	}
	?>
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
				$(form).ajaxSubmit({
					url: "<?php echo Router::url('/', true); ?><?php echo $this->request->params['controller'] ?>/add/<?php echo $this->request->params['pass'][0];?>",
					type: 'POST',
					target: '#create_tasks',
					beforeSend: function(){
						$("#task_submit_id").prop("disabled",true);
						$("#task_submit_indicator").show();
					},
					complete: function() {
						$("#task_submit_id").removeAttr("disabled");
						$("#task_submit_indicator").hide();
					},
					error: function(request, status, error) {
                    //alert(request.responseText);
						alert('Action failed!');
					}
				});
			}
		});
		
		$().ready(function() {

			$('select').chosen();

			jQuery.validator.addMethod("greaterThanZero", function(value, element) {
				return this.optional(element) || (parseFloat(value) != -1);
			}, "Please select the value");


			$('#CustomTableTaskAddForm').validate();

			$('select').each(function() {	
				if($(this).prop('required') == true){
					$(this).rules('add', {
						greaterThanZero: true
					});	
				}
				
			});
			
			$("#task_submit_indicator").hide();
			$("#task_submit_id").click(function(){
				if($('#CustomTableTaskAddForm').valid()){
					$("#task_submit_id").prop("disabled",true);
					$("#task_submit_indicator").show();
					$('#CustomTableTaskAddForm').submit();
				}
			});
		});
	</script>
	<style type="text/css">
		.error, .error .chosen-container{
			border: 1px dotted red;
		}
	</style>
	<!-- <div class="col-md-6"> -->
		<div class="box box-default collapsed-box">
			<div class="box-header data-header" data-widget="collapse"><h3 class="box-title"><span class=""><i class="fa fa-calendar-check-o"></i></span> Create Tasks </h3>
				<div class="box-tools pull-right"><button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button></div>
			</div>

			<div class="box-body">

				<div class="table-responsive" style="overflow:scroll;">
					<table cellpadding="0" cellspacing="0" class="table table-hover">
						<tr>
							<th><?php echo __('Field Name'); ?></th>
							<th><?php echo __('Condition'); ?></th>
							<th><?php echo __('Date'); ?></th>
							<th><?php echo __('Message'); ?></th>
							<th></th>
						</tr>
						<?php if($customTableTasks){ ?>
							<?php foreach ($customTableTasks as $customTableTask): ?>
								<tr id="<?php echo $customTableTask['CustomTableTask']['id'].'-tr';?>">
									<td><?php echo h($customTableTask['CustomTableTask']['employee_field']); ?>&nbsp;</td>
									<td><?php 
									foreach(json_decode($customTableTask['CustomTable']['fields'],true) as $field){
										if($field['field_name'] == $customTableTask['CustomTableTask']['condition_field']){
											$csvoptions = explode(',',$field['csvoptions']);								
										}
									}
									echo h($customTableTask['CustomTableTask']['condition_field']) . ' ' . $customTableTask['CustomTableTask']['condition'] .' ' . $csvoptions[$customTableTask['CustomTableTask']['csvoption']] ; ?>&nbsp;</td>
									<td><?php echo h($customTableTask['CustomTableTask']['date_field']); ?>&nbsp;</td>
									<td><?php echo h($customTableTask['CustomTableTask']['message']); ?>&nbsp;</td>
									<td class="text-right">	
										<?php echo $this->Html->link('<i class="fa fa-trash-o"></i>','#',array('id'=>$customTableTask['CustomTableTask']['id'].'-del','escape'=>false));?>

										<script type="text/javascript">
											$("#<?php echo $customTableTask['CustomTableTask']['id'];?>-del").on('click',function(){
												$.ajax({
													url: "<?php echo Router::url('/', true); ?>custom_table_tasks/delete_task/<?php echo $this->request->params['pass']['0']?>",
													type: "POST",
													target: '#loadlocks',
													dataType: "json",
													contentType: "application/json; charset=utf-8",
													data: JSON.stringify({ id: '<?php echo $customTableTask['CustomTableTask']['id'];?>'}),
													beforeSend: function( xhr ) {
									    // $("#<?php echo $postVal;?>-user i").removeClass('fa-user').addClass('fa-refresh fa-spin');
									    // $("#<?php echo $postVal;?>-user").next().find('.tooltip-inner').html('Adding');
									    // $("#<?php echo $postVal;?>-user").tooltip().attr({'data-toggle':'tooltip', 'data-original-title':'Adding','data-placement':'left','data-trigger':'hover'}).tooltip('show');
									    // $("#<?php echo $customTableTask['CustomTableTask']['id'];?>-tr").remove();
													},					
													success: function (result) {
														$("#<?php echo $customTableTask['CustomTableTask']['id'];?>-tr").remove();
										// $("#<?php echo $postVal;?>-user").removeClass('btn-danger').addClass('btn-success');
										// $("#<?php echo $postVal;?>-user i").removeClass('fa-refresh fa-spin').addClass('fa-check');
										// $("#<?php echo $postVal;?>-user").next().find('.tooltip-inner').html('User added');
										// $("#<?php echo $postVal;?>-user").tooltip().attr({'data-toggle':'tooltip', 'data-original-title':'User added','data-placement':'left','data-trigger':'hover'}).tooltip('show');
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
							<tr><td colspan=102>No results found</td></tr>
						<?php } ?>
					</table>
				</div>
				<div class="row">
					<div class="CustomTableTasks form col-md-12">
						<?php echo $this->Form->create('CustomTableTask', array('controller'=>'custom_table_tasks','action'=>'add'), array('role'=>'form','class'=>'form')); ?>
						<div class="row">
							<?php					
							echo "<div class='col-md-6 hide'>" . $this->Form->hidden('custom_table_id',array('default'=>$this->request->params['pass'][0], 'class'=>'form-control')) . "</div>";
							echo "<div class='col-md-6'>" . $this->Form->input('employee_field',array('required'=>'required', 'options'=>$result['emp'], 'class'=>'form-control')) . "</div>";
							echo "<div class='col-md-6'>" . $this->Form->input('condition_field',array('required'=>'required', 'options'=>$result['radio'], 'class'=>'form-control')) . "</div>";						
							echo "<div class='col-md-4'>" . $this->Form->input('condition',array('required'=>'required', 'options'=>$customTaskConditions, 'class'=>'form-control')) . "</div>";
							echo "<div class='col-md-4'>" . $this->Form->input('csvoption',array('required'=>'required', 'class'=>'form-control','options'=>array())) . "</div>";
							echo "<div class='col-md-4'>" . $this->Form->input('date_field',array('required'=>'required', 'options'=>$result['date'], 'class'=>'form-control')) . "</div>";;
							echo "<div class='col-md-12'>" . $this->Form->input('message',array('required'=>'required', 'class'=>'form-control')) . "</div>";
							?>
							<?php
							echo $this->Form->input('id');
							echo $this->Form->hidden('History.pre_post_values', array('value'=>json_encode($this->data)));
							echo $this->Form->input('branchid', array('type' => 'hidden', 'value' => $this->Session->read('User.branch_id')));
							echo $this->Form->input('departmentid', array('type' => 'hidden', 'value' => $this->Session->read('User.department_id')));										
							?>

						</div>
						<div class="">
							<?php echo $this->Form->input('publish');?>
							<?php echo $this->Form->submit(__('Submit'), array('div' => false, 'style'=>'margin-bottom:10px', 'class' => 'btn btn-primary btn-success','async' => 'false', 'update'=>'create_tasks', 'id'=>'task_submit_id')); ?>
							<?php echo $this->Html->image('indicator.gif', array('id' => 'task_submit_indicator')); ?>
							<?php echo $this->Form->end(); ?>
							<?php echo $this->Js->writeBuffer();?>
							<script type="text/javascript">
								$("#CustomTableTaskConditionField").on('change',function(){
									$.ajax({
										url: "<?php echo Router::url('/', true); ?><?php echo $this->request->params['controller'] ?>/getcsvs/<?php echo $this->request->params['pass'][0];?>/"+$("#CustomTableTaskConditionField").val(),
										success: function(data, result) {
											$("#CustomTableTaskCsvoption").html(data).trigger("chosen:updated");
										},
									});
								});
							</script>									
						</div>
					</div>			
					
				</div>
				<div class="box-footer"><strong>Note: </strong>When you add task for employee, basis on the condition set by you, emplyee will automatically receive an email when those conditions are met. Task will also be displayed on employees dashboard for action. There is no need to add additional email triggers for such conditions.</div>
			</div>
		</div>
		
