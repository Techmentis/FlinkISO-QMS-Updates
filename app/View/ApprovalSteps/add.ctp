<?php echo $this->Html->script(array('jquery.validate.min', 'jquery-form.min')); ?>
<?php echo $this->fetch('script'); ?><div id="approvalSteps_ajax">
<?php echo $this->Session->flash();?>	

	<div class="approvalSteps ">
		<?php echo $this->element('nav-header-lists',array('postData'=>array('pluralHumanName'=>'Approval Steps','modelClass'=>'ApprovalStep','options'=>array(),'pluralVar'=>'approvalSteps'))); ?>

<div class="nav panel panel-default">
<div class="approvalSteps form col-md-12">
	<?php echo $this->Form->create('ApprovalStep',array('role'=>'form','class'=>'form')); ?>
	<div class="row">
			<?php
		echo "<div class='col-md-4'>".$this->Form->input('approval_process_id',array('class'=>'form-control', 'style'=>'')) . '</div>'; 
		echo "<div class='col-md-4'>".$this->Form->input('title',array('class'=>'form-control',)) . '</div>'; 
		echo "<div class='col-md-4'>".$this->Form->input('process_step',array('class'=>'form-control',)) . '</div>'; 
		echo "<div class='col-md-4'>".$this->Form->input('send_to_department_hod',array('class'=>'form-control',)) . '</div>'; 
		echo "<div class='col-md-4'>".$this->Form->input('send_to_designation',array('class'=>'form-control',)) . '</div>'; 
		echo "<div class='col-md-4'>".$this->Form->input('send_to_admins',array('class'=>'form-control',)) . '</div>'; 
		echo "<div class='col-md-4'>".$this->Form->input('send_to_reviwers',array('class'=>'form-control',)) . '</div>'; 
		echo "<div class='col-md-4'>".$this->Form->input('send_to_publishers',array('class'=>'form-control',)) . '</div>'; 
		echo "<div class='col-md-12'>".$this->Form->input('send_to_users',array('class'=>'form-control',)) . '</div>'; 
		echo "<div class='col-md-12'>".$this->Form->input('comments',array('class'=>'form-control',)) . '</div>'; 
		echo "<div class='col-md-4'>".$this->Form->input('approval_mode',array('class'=>'form-control',)) . '</div>'; 
		echo "<div class='col-md-4'>".$this->Form->input('approval_type',array('class'=>'form-control',)) . '</div>'; 
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
            return this.optional(element) || (parseFloat(value) > 0);
        }, "Please select the value");

        $('#ApprovalStepAddForm').validate({        	
            rules: {
				"data[ApprovalStep][approval_process_id]": {
                		greaterThanZero: true,
					},
                
            }
        }); 
			
        $("#submit-indicator").hide();
        $("#submit_id").click(function(){
            if($('#ApprovalStepAddForm').valid()){
                 $("#submit_id").prop("disabled",true);
                 $("#submit-indicator").show();
                $('#ApprovalStepAddForm').submit();
            }

        });

		$('#ApprovalStepApprovalProcessId').change(function() {
			if ($(this).val() != -1 && $(this).next().next('label').hasClass("error")) {
				$(this).next().next('label').remove();
			}
		});	

    });
</script>