<?php echo $this->Html->script(array('jquery.validate.min', 'jquery-form.min')); ?>
<?php echo $this->fetch('script'); ?><div id="designations_ajax">
	<?php echo $this->Session->flash();?>	

	<div class="designations ">
		<?php echo $this->element('nav-header-lists',array('postData'=>array('pluralHumanName'=>'Designations','modelClass'=>'Designation','options'=>array(),'pluralVar'=>'designations'))); ?>		
		<div class="nav panel panel-default">
			<div class="designations form col-md-12">
				<?php echo $this->Form->create('Designation',array('role'=>'form','class'=>'form')); ?>
				<div class="row">
					<?php
$str =
"Designation 1
Designation 2
Designation 3
Designation 4
.
.
.
";
					echo "<div class='col-md-12'>".$this->Form->input('parent_id',array('class'=>'form-control', 'style'=>'','options'=>$parentDesignations)) . '</div>';
					echo "<div class='col-md-12'>".$this->Form->input('name',array('type'=>'textarea' ,'class'=>'form-control', 'placeholder'=>$str )) . '</div>'; 		
					echo "<div class='col-md-12 help-text'>Add departments with < enter > as new record.</div>";
					?>
					<?php
					echo $this->Form->input('id');
					echo $this->Form->hidden('History.pre_post_values', array('value'=>json_encode($this->data)));
					echo $this->Form->input('branchid', array('type' => 'hidden', 'value' => $this->Session->read('User.branch_id')));
					echo $this->Form->input('departmentid', array('type' => 'hidden', 'value' => $this->Session->read('User.department_id')));
					echo $this->Form->input('master_list_of_format_id', array('type' => 'hidden', 'value' => $documentDetails['MasterListOfFormat']['id']));
					?>

				</div>
				<div class="">
					<?php echo $this->Form->input('publish');?>
					<?php echo $this->Form->submit(__('Submit'), array('div' => false, 'class' => 'btn btn-primary btn-success','id'=>'submit_id')); ?>
					<?php echo $this->Html->image('indicator.gif', array('id' => 'submit-indicator')); ?>
					<?php echo $this->Form->end(); ?>

					<?php echo $this->Js->writeBuffer();?>
				</div>
			</div>
			<script> 
				$("[name*='date']").datepicker({
					changeMonth: true,
					changeYear: true,
					dateFormat:'yy-mm-dd',
				}); </script>
			</div>
		</div>
		<script>
			$.validator.setDefaults({
				ignore: null,
				errorPlacement: function(error, element) {
					if(element['context']['className'] == 'form-control select error'){
						$(element).next().after(error); 		
					}else{
						$(element).after(error); 
					}
				},
			});
			
			$().ready(function() {
				
				$('#DesignationAddBulkForm').validate({        	
					
				}); 
				
				$("#submit-indicator").hide();
				$("#submit_id").click(function(){
					if($('#DesignationAddBulkForm').valid()){
						$("#submit_id").prop("disabled",true);
						$("#submit-indicator").show();
						$('#DesignationAddBulkForm').submit();
					}

				});		

			});
		</script>
