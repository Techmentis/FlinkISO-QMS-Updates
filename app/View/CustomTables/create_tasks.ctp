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
						$("#submit_id").prop("disabled",true);
						$("#submit-indicator").show();
					},
					complete: function() {
						$("#submit_id").removeAttr("disabled");
						$("#submit-indicator").hide();
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


			$('#RecordLockAddForm').validate();

			$('select').each(function() {	
				if($(this).prop('required') == true){
					$(this).rules('add', {
						greaterThanZero: true
					});	
				}
				
			});
			
			$("#submit-indicator").hide();
			$("#submit_id").click(function(){
				if($('#RecordLockAddForm').valid()){
					$("#submit_id").prop("disabled",true);
					$("#submit-indicator").show();
					$('#RecordLockAddForm').submit();
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
		<div class="box box-default">
			<div class="box-header with-border"><h3 class="box-title" style="width: 100%">Create Tasks <span class="pull-right"><i class="fa fa-calendar-check-o"></i></span> </h3></div>
			<div class="box-body">

				<div class="table-responsive" style="overflow:scroll;">			
					<div class="nav panel panel-default">
						<div class="recordLocks form col-md-12">
							<?php echo $this->Form->create('RecordLock', array('controller'=>'record_locks','action'=>'add'), array('role'=>'form','class'=>'form')); ?>
							<div class="row">
								<?php
								echo "<div class='col-md-6 hide'>" . $this->Form->hidden('table_id',array('default'=>$this->request->params['pass'][0], 'class'=>'form-control')) . "</div>";
								echo "<div class='col-md-12'>" . $this->Form->input('lock_table_id',array('required'=>'required', 'options'=>$linkedToTables, 'class'=>'form-control')) . "</div>";
								echo "<div class='col-md-4'>" . $this->Form->input('table_field',array('required'=>'required', 'class'=>'form-control','options'=>$lockRecordField)) . "</div>";
								echo "<div class='col-md-4'>" . $this->Form->input('condition',array('required'=>'required', 'options'=>$lockRecordConditions, 'class'=>'form-control')) . "</div>";
								echo "<div class='col-md-4'>" . $this->Form->input('csvoption',array('required'=>'required', 'class'=>'form-control','options'=>array())) . "</div>";
								echo "<div class='col-md-12'>" . $this->Form->input('action',array('required'=>'required', 'options'=>$lockRecordActions,'default'=>0,  'type'=>'radio')) . "</div>";
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
								<?php echo $this->Form->submit(__('Submit'), array('div' => false, 'class' => 'btn btn-primary btn-success','async' => 'false', 'update'=>'create_tasks', 'id'=>'submit_id')); ?>
								<?php echo $this->Html->image('indicator.gif', array('id' => 'submit-indicator')); ?>
								<?php echo $this->Form->end(); ?>

								<?php echo $this->Js->writeBuffer();?>
								<script type="text/javascript">
									$("#RecordLockTableField").on('change',function(){
										$.ajax({
											url: "<?php echo Router::url('/', true); ?><?php echo $this->request->params['controller'] ?>/getcsvs/<?php echo $this->request->params['pass'][0];?>/"+$("#RecordLockTableField").val(),
											success: function(data, result) {
												$("#RecordLockCsvoption").html(data).trigger("chosen:updated");
											},
										});
									});
								</script>									
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		
