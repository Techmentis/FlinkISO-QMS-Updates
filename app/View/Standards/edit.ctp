<div id="standards_ajax">
	<?php echo $this->Session->flash();?>	
	<?php echo $this->element('nav-header-lists',array('postData'=>array('pluralHumanName'=>'Standards','modelClass'=>'Standard','options'=>array("sr_no"=>"Sr No","name"=>"Name","details"=>"Details"),'pluralVar'=>'standards'))); ?>
	<div class="nav panel panel-default">
		<div class="standards form col-md-12">
			<?php echo $this->Form->create('Standard',array('role'=>'form','class'=>'form')); ?>
			<div class="row">
				<?php
				echo "<div class='col-md-12'>".$this->Form->input('name',array('class'=>'form-control')) . '</div>'; 
				echo "<div class='col-md-12'>".$this->Form->input('details',array('class'=>'form-control','label'=>'Brief Description')) . '</div>'; 	
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
	</div>
</div>
<script>
	$.validator.setDefaults();
	$().ready(function() {
		$('#StandardEditForm').validate();
		$("#submit-indicator").hide();
		$("#submit_id").click(function(){
			if($('#StandardEditForm').valid()){
				$("#submit_id").prop("disabled",true);
				$("#submit-indicator").show();
				$('#StandardEditForm').submit();
			}

		});
	});
</script>
<?php echo $this->Html->script(array('jquery.validate.min', 'jquery-form.min')); ?>
<?php echo $this->fetch('script'); ?>
