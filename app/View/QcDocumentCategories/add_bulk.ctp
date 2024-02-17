<?php echo $this->Html->script(array('jquery.validate.min', 'jquery-form.min')); ?>
<?php echo $this->fetch('script'); ?><div id="qcDocumentCategories_ajax">
	<?php echo $this->Session->flash();?>	

	<div class="qcDocumentCategories ">
		<?php echo $this->element('nav-header-lists',array('postData'=>array('pluralHumanName'=>'Qc Document Categories','modelClass'=>'QcDocumentCategory','options'=>array(),'pluralVar'=>'qcDocumentCategories'))); ?>
		<?php echo $this->Html->link('Add Bulk',array('action'=>'add_bulk'),array('class'=>'btn btn-sm btn-primary pull-right'));?>
		<div class="nav panel panel-default">
			<div class="qcDocumentCategories form col-md-12">
				<?php echo $this->Form->create('QcDocumentCategory',array('role'=>'form','class'=>'form')); ?>
				<div class="row">
					<?php
					$str =
					"Short Name, Name
					M,Manual
					F,Format
					P,Procedure
					.
					.
					.
					";
		// echo "<div class='col-md-6'>".$this->Form->input('short_name',array('class'=>'form-control',)) . '</div>'; 
					echo "<div class='col-md-6'>".$this->Form->input('standard_id',array('class'=>'form-control', 'style'=>'')) . '</div>'; 
					echo "<div class='col-md-6'>".$this->Form->input('parent_id',array('class'=>'form-control', 'style'=>'')) . '</div>'; 
					echo "<div class='col-md-12'>".$this->Form->input('name',array('type'=>'textarea', 'class'=>'form-control','placeholder'=>$str)) . '</div>'; 
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

					<?php echo $this->element('approval_form',array('approval'=>$approval));?>
					<?php echo $this->element('approval_history',array('approval'=>$approval,'approvals'=>$approvals,'current_approval'=>$this->request->params['named']['approval_id'],'approvalComments',$approvalComments));?>
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
				jQuery.validator.addMethod("greaterThanZero", function(value, element) {
					return this.optional(element) || $("#"+element.id+" option:selected").text() != 'Select';
				}, "Please select the value");

				$('#QcDocumentCategoryAddBulkForm').validate({        	
					rules: {
						"data[QcDocumentCategory][standard_id]": {
							greaterThanZero: true,
						}
					}
				}); 
				
				$("#submit-indicator").hide();
				$("#submit_id").click(function(){
					if($('#QcDocumentCategoryAddBulkForm').valid()){
						$("#submit_id").prop("disabled",true);
						$("#submit-indicator").show();
						$('#QcDocumentCategoryAddBulkForm').submit();
					}

				});

				$('#QcDocumentCategoryStandardId').change(function() {
					if ($(this).val() != -1 && $(this).next().next('label').hasClass("error")) {
						$(this).next().next('label').remove();
					}
				});
				

			});
		</script>
