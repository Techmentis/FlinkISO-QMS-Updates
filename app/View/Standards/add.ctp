<div id="standards_ajax">
	<?php $placeholder = 
	"1,,Scope;
	2,,Normative references;
	3,,Terms and Definitions;
	4,,Context of the organization;
	4,4.1, Understanding Context of the Organization;
	4,4.2, Understanding the needs and expectations of interested parties;
	4,4.3 Determining the scope of the quality management system;
	4,4.4, Quality management system and its processes;
	";
	?>

	<?php echo $this->Session->flash();?>	
	<?php echo $this->element('nav-header-lists',array('postData'=>array('pluralHumanName'=>'Standards','modelClass'=>'Standard','options'=>array("sr_no"=>"Sr No","name"=>"Name","details"=>"Details"),'pluralVar'=>'standards'))); ?>
	<div class="nav panel panel-default">
		<div class="standards form col-md-12">
			<?php echo $this->Form->create('Standard',array('role'=>'form','class'=>'form')); ?>
			<div class="row">
				<?php
				echo "<div class='col-md-12'>".$this->Form->input('name',array('class'=>'form-control')) . '</div>'; 
				echo "<div class='col-md-12'>".$this->Form->input('details',array('class'=>'form-control','label'=>'Brief Description')) . '</div>';
				echo "<div class='col-md-12'>".$this->Form->input('clauses',array('type'=>'textarea', 'placeholder'=>$placeholder, 'class'=>'form-control','label'=>'Enter clauses in CSV format')) . '</div>';
				echo "<div class='col-md-12'>CSV : Comma Seperated Values. <br />Clause No.<strong>,</strong>Sub-Clause<strong>,</strong>Title/Name of the clause<strong>,</strong> Details(textual Description) < enter ><br /> Please avoid additional comma's inbetween at this stage. You can always edit these from Standard/Clauses page later.</div>"
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
		$('#StandardAddForm').validate();
		$("#submit-indicator").hide();
		$("#submit_id").click(function(){
			if($('#StandardAddForm').valid()){
				$("#submit_id").prop("disabled",true);
				$("#submit-indicator").show();
				$('#StandardAddForm').submit();
			}

		});
	});
</script>
<?php echo $this->Html->script(array('jquery.validate.min', 'jquery-form.min')); ?>
<?php echo $this->fetch('script'); ?>
