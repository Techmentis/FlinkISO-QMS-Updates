<?php echo $this->Html->script(array('jquery.validate.min', 'jquery-form.min')); ?>
<?php echo $this->fetch('script'); ?><div id="clauses_ajax">
	<?php echo $this->Session->flash();?>	

	<div class="clauses ">
		<?php echo $this->element('nav-header-lists',array('postData'=>array('pluralHumanName'=>'Clauses','modelClass'=>'Clause','options'=>array(),'pluralVar'=>'clauses'))); ?>

		<div class="nav panel panel-default panel-body">
			<div class="clauses">
				<?php echo $this->Form->create('Clause',array('role'=>'form','class'=>'form')); ?>
				<div class="row">
					<?php
					echo "<div class='col-md-6'>".$this->Form->input('title',array('class'=>'form-control',)) . '</div>'; 
					echo "<div class='col-md-6'>".$this->Form->input('standard_id',array('class'=>'form-control', 'style'=>'')) . '</div>'; 
					echo "<div class='col-md-6'>".$this->Form->input('clause',array('class'=>'form-control',)) . '</div>'; 
					echo "<div class='col-md-6'>".$this->Form->input('sub-clause',array('class'=>'form-control',)) . '</div>'; 
					echo "<div class='col-md-12'>".$this->Form->input('details',array('class'=>'form-control',)) . '</div>'; 
					echo "<div class='col-md-12'>".$this->Form->input('additional_details',array('class'=>'form-control',)) . '</div>'; 
					echo "<div class='col-md-12'>".$this->Form->input('tabs',array('class'=>'form-control',)) . '</div>'; 
					echo "<div class='col-md-12'>".$this->Form->input('external_link_1',array('class'=>'form-control',)) . '</div>'; 
					echo "<div class='col-md-12'>".$this->Form->input('external_link_2',array('class'=>'form-control',)) . '</div>'; 
					echo "<div class='col-md-12'>".$this->Form->input('external_link_3',array('class'=>'form-control',)) . '</div>'; 
					echo "<div class='col-md-12'>".$this->Form->input('external_link_4',array('class'=>'form-control',)) . '</div>'; 
					echo "<div class='col-md-12'>".$this->Form->input('external_link_5',array('class'=>'form-control',)) . '</div>'; 
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
		</div>
		<script>
			$.validator.setDefaults({
				ignore: null,
				errorPlacement: function(error, element) {
					if (
						
						$(element).attr('name') == 'data[Clause][standard_id]')
					{	
						$(element).next().after(error);
					} else {
						$(element).after(error);
					}
				},
			});
			
			$().ready(function() {
				jQuery.validator.addMethod("greaterThanZero", function(value, element) {
					return this.optional(element) || $("#"+element.id+" option:selected").text() != 'Select';
				}, "Please select the value");

				$('#ClauseAddForm').validate({        	
					rules: {
						"data[Clause][standard_id]": {
							greaterThanZero: true,
						},
						
					}
				}); 
				
				$("#submit-indicator").hide();
				$("#submit_id").click(function(){
					if($('#ClauseAddForm').valid()){
						$("#submit_id").prop("disabled",true);
						$("#submit-indicator").show();
						$('#ClauseAddForm').submit();
					}

				});

				$('#ClauseStandardId').change(function() {
					if ($(this).val() != -1 && $(this).next().next('label').hasClass("error")) {
						$(this).next().next('label').remove();
					}
				});	

			});
		</script>