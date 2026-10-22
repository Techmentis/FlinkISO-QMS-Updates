<?php echo $this->Html->script(array('jquery.validate.min', 'jquery-form.min')); ?>
<?php echo $this->fetch('script'); ?><div id="employees_ajax">
	<?php echo $this->Session->flash();?>	

	<div class="employees ">
		<?php echo $this->element('nav-header-lists',array('postData'=>array('pluralHumanName'=>__('Employees'),'modelClass'=>'Employee','options'=>array(),'pluralVar'=>'employees'))); ?>

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

					echo "<div class='col-md-3'>".$this->Form->input('branch_id',array('class'=>'form-control', 'style'=>'','required'=>'required')) . '</div>'; 
					echo "<div class='col-md-3'>".$this->Form->input('department_id',array('class'=>'form-control', 'style'=>'','required'=>'required')) . '</div>'; 
					echo "<div class='col-md-3'>".$this->Form->input('designation_id',array('class'=>'form-control', 'style'=>'','required'=>'required')) . '</div>'; 
					echo "<div class='col-md-3'>".$this->Form->input('parent_id',array('class'=>'form-control', 'style'=>'')) . '</div>';
					echo "<div class='col-md-12'>".$this->Form->input('name',array('class'=>'form-control','type'=>'textarea','placeholder'=>$str)) . '</div>'; 
					echo '<div class=\'col-md-12\'>' . h(__('Employee Number,Employee Name,Employee Email,Hod,Approver')) . '</div>';
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
						<p><strong><?php echo h(__('Adding Users:')); ?></strong></p>
						<p><?php echo h(__('When you add users from this page, users wlil be automatically inherit following:')); ?></p>
						<p>
							<ul>
								<li><?php echo h(__('Selected Branch')); ?></li>
								<li><?php echo h(__('Selected Department')); ?></li>
								<li><?php echo h(__('Username : Email Address')); ?></li>
								<li><?php echo h(__('Password : Email Addresss')); ?></li>
								<li><?php echo h(__('Admin : No')); ?></li>
								<li><?php echo h(__('Approver : Yes')); ?></li>
								<li><?php echo h(__('Can create custom tables : No')); ?></li>
								<li><?php echo h(__('User who can see other user\'s records : Yes')); ?></li>
							</ul>
						</p>
						<p><?php echo h(__('You can always change these user settings from user\'s page')); ?></p>
					</div>
				</div>

			</div>

		</div>
	</div>
	<script>
		$.validator.setDefaults({
			ignore: null,
			errorPlacement: function(error, element) {				
				if(element['context']['className'] == 'form-control select error'){					
					$(element).next('.chosen-container').addClass('error');
				}else if(element.attr("fieldset") != ''){						
					$(element).parent('fieldset').addClass('error-radio');
				}else{			
					$(element).after(error); 
				}
			},
		});
		
		$().ready(function() {
			$('select').chosen();

			$('select').each(function(){
				var id = this.id;		
				$("#"+id).on('change',function(){			
					$("#"+id).next('.chosen-container').removeClass('error').addClass('success');			
				});
			});


			jQuery.validator.addMethod("greaterThanZero", function(value, element) {				
				return this.optional(element) || $("#"+element.id+" option:selected").text() != 'Select';
			}, <?php echo json_encode(__('Please select the value'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>);

			
			$('#EmployeeAddBulkForm').validate();
			$('select').each(function() {				
				if($(this).prop('required') == true){
					$(this).rules('add', {
						greaterThanZero: true
					});	
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

			
		});
	</script>
