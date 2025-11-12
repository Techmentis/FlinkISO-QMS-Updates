<?php if($lockRecordField){?>
<div id="loadlocks">
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
				console.log(element);
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
					target: '#loadlocks',
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
		<div class="box box-default  collapsed-box">
			<div class="box-header data-header" data-widget="collapse">
				<h3 class="box-title"><span class=""><i class="fa fa-random"></i></span> Locks </h3>
				<div class="box-tools pull-right"><button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button></div>
			</div>
			<div class="box-body">

				<div class="table-responsive" style="overflow:scroll;">		
					<table cellpadding="0" cellspacing="0" class="table table-hover">
						<tr>
							<th><?php echo __('Tables'); ?></th>
							<th><?php echo __('Field Name'); ?></th>
							<th><?php echo __('Message'); ?></th>
							<th></th>
						</tr>
						<?php if($recordLocks){ ?>
							<?php foreach ($recordLocks as $recordLock): ?>
								<tr id="<?php echo $recordLock['RecordLock']['id'].'-tr';?>">
									<td><?php echo h($recordLock['RecordLock']['lock_table_id']); ?>&nbsp;</td>
									<td><?php echo h($recordLock['RecordLock']['table_field']); ?>&nbsp;</td>
									<td><?php echo h($recordLock['RecordLock']['message']); ?>&nbsp;</td>
									<td class="text-right">	
										<?php echo $this->Html->link('<i class="fa fa-trash-o"></i>','#',array('id'=>$recordLock['RecordLock']['id'].'-del','escape'=>false));?>

										<script type="text/javascript">
											$("#<?php echo $recordLock['RecordLock']['id'];?>-del").on('click',function(){
												$.ajax({
													url: "<?php echo Router::url('/', true); ?>record_locks/delete_lock/<?php echo $this->request->params['pass']['0']?>",
													type: "POST",
													target: '#loadlocks',
													dataType: "json",
													contentType: "application/json; charset=utf-8",
													data: JSON.stringify({ id: '<?php echo $recordLock['RecordLock']['id'];?>'}),
													beforeSend: function( xhr ) {
													    // $("#<?php echo $postVal;?>-user i").removeClass('fa-user').addClass('fa-refresh fa-spin');
													    // $("#<?php echo $postVal;?>-user").next().find('.tooltip-inner').html('Adding');
													    // $("#<?php echo $postVal;?>-user").tooltip().attr({'data-toggle':'tooltip', 'data-original-title':'Adding','data-placement':'left','data-trigger':'hover'}).tooltip('show');
													    // $("#<?php echo $recordLock['RecordLock']['id'];?>-tr").remove();
													},					
													success: function (result) {
														$("#<?php echo $recordLock['RecordLock']['id'];?>-tr").remove();
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
					<div class="recordLocks form col-md-12">
						<?php echo $this->Form->create('RecordLock', array('controller'=>'record_locks','action'=>'add'), array('role'=>'form','class'=>'form')); ?>
						<div class="row">
							<?php
							$linkedToTables[Inflector::classify($customTable['Table']['table_name'])] = Inflector::classify($customTable['Table']['table_name']);							
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
							<?php echo $this->Form->submit(__('Submit'), array('div' => false, 'class' => 'btn btn-primary btn-success','async' => 'false', 'update'=>'loadlocks', 'id'=>'submit_id')); ?>
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
				<div class="panel-footer">At certiain point you may want to disable a record which is dependent on current record you are adding. E.g, if you are adding Document Change Request, you may restrict the Document for which you are adding the chage request for. In such scenario, you can use this option. Select which table you want to lock and the add the condition. Once you add and save these details, a linked record for that table will be locked for editing.</div>
			</div>
		</div>
<?php } ?>		
