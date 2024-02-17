<?php echo $this->Html->script(array('jquery.validate.min', 'jquery-form.min')); ?>
<?php echo $this->fetch('script'); ?><div id="employees_ajax">
	<?php echo $this->Session->flash();?>	

	<div class="employees ">
		<?php echo $this->element('nav-header-lists',array('postData'=>array('pluralHumanName'=>'Employees','modelClass'=>'Employee','options'=>array(),'pluralVar'=>'employees'))); ?>

		<div class="nav panel panel-default">
			<div class="employees form col-md-12">
				<?php echo $this->Form->create('Employee',array('role'=>'form','class'=>'form')); ?>
				<div class="row">
					<?php
					$str =
					"Employee Number,Employee Name,Employee Email,Hod,Approver
					001,Alex John,alex@demo.com,yes,yes
					002,Erina D,erina@demo.com,yes,no
					.
					.
					.
					";

					echo "<div class='col-md-3'>".$this->Form->input('branch_id',array('class'=>'form-control', 'style'=>'')) . '</div>'; 
					echo "<div class='col-md-3'>".$this->Form->input('department_id',array('class'=>'form-control', 'style'=>'')) . '</div>'; 
					echo "<div class='col-md-3'>".$this->Form->input('designation_id',array('class'=>'form-control', 'style'=>'')) . '</div>'; 
					echo "<div class='col-md-3'>".$this->Form->input('parent_id',array('class'=>'form-control', 'style'=>'')) . '</div>';

					echo "<div class='col-md-12'>".$this->Form->input('name',array('class'=>'form-control','type'=>'textarea','placeholder'=>$str)) . '</div>'; 
					?>
					<?php
					echo $this->Form->input('id');
					echo $this->Form->hidden('History.pre_post_values', array('value'=>json_encode($this->data)));
					echo $this->Form->input('branchid', array('type' => 'hidden', 'value' => $this->Session->read('User.branch_id')));
					echo $this->Form->input('departmentid', array('type' => 'hidden', 'value' => $this->Session->read('User.department_id')));
					echo $this->Form->input('master_list_of_format_id', array('type' => 'hidden', 'value' => $documentDetalis['MasterListOfFormat']['id']));
					?>

				</div>
				<div class="">
					<?php echo $this->Form->input('publish');?>
					<?php echo $this->Form->input('create_users',array('type'=>'checkbox'));?>
					<?php echo $this->Form->submit(__('Submit'), array('div' => false, 'class' => 'btn btn-primary btn-success','id'=>'submit_id')); ?>
					<?php echo $this->Html->image('indicator.gif', array('id' => 'submit-indicator')); ?>
					<?php echo $this->Form->end(); ?>

					<?php echo $this->Js->writeBuffer();?>
				</div>

				<div class="row">
					<div class="col-md-12">
						<p><strong>Adding Users:</strong></p>
						<p>When you add users from this page, users wlil be automatically inherit following:</p>
						<p>
							<ul>
								<li>Selected Branch</li>
								<li>Selected Department</li>
								<li>Username : Email Address</li>
								<li>Password : Email Addresss</li>
								<li>Admin : No</li>
								<li>Approver : Yes</li>
								<li>Can create custom tables : No</li>
								<li>User who can see other user's records : Yes</li>
							</ul>
						</p>
						<p>You can always change these user settings from user's page</p>
					</div>
				</div>

			</div>

		</div>
	</div>
	<script>
		$.validator.setDefaults({
			ignore: null,
			errorPlacement: function(error, element) {
				if (
					
					$(element).attr('name') == 'data[Employee][branch_id]' ||
					$(element).attr('name') == 'data[Employee][department_id]'
					)
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

			$('#EmployeeAddBulkForm').validate({        	
				rules: {
					"data[Employee][branch_id]": {
						greaterThanZero: true,
					},
					"data[Employee][department_id]": {
						greaterThanZero: true,
					}
					
				}
			}); 
			
			$("#submit-indicator").hide();
			$("#submit_id").click(function(){
				if($('#EmployeeAddBulkForm').valid()){
					$("#submit_id").prop("disabled",true);
					$("#submit-indicator").show();
					$('#EmployeeAddBulkForm').submit();
				}

			});

			$('#EmployeeBranchId').change(function() {
				if ($(this).val() != -1 && $(this).next().next('label').hasClass("error")) {
					$(this).next().next('label').remove();
				}
			});
			$('#EmployeeDepartmentId').change(function() {
				if ($(this).val() != -1 && $(this).next().next('label').hasClass("error")) {
					$(this).next().next('label').remove();
				}
			});
			
		});
	</script>
