<?php
if(($user != null && $user == $this->Session->read('User.id')) || $showform == true){ ?>
<style>
	.chosen-container-multi .chosen-choices .search-field, .active-result{
		border: none !important;
	}
	.approvalli{
		/*padding: 12px !important;*/
	}
</style>
<div class="row">
	<div class="col-md-12">
		<?php 	

		if($approval['id']){
			$reType = 'ApprovalComment';
		}else{
			$reType = 'Approval';
		}
		
		$toRequired = $commentRequired =  'required';
		$approversList[$from['id']] = $from['name'];
		unset($approversList[$this->Session->read('User.id')]);
		if($reType == 'ApprovalComment'){
			echo $this->Form->input($reType.'.'.$approvalModel.'.user_id', array('id'=>'Approval'.Inflector::Classify($this->request->controller).'UserId','label'=>'Add your response/ comments',
			'name'=>'data['.$reType.']['.$approvalModel.'][user_id]', 'class'=>'form-control select', $toRequired, 'options' => $approversList,'selected'=>$from['id']));
			
			echo $this->Form->hidden($reType.'.'.$approvalModel.'.approval_id', array('id'=>'Approval'.Inflector::Classify($this->request->controller).'Id','default'=>$approval['id']));
			echo $this->Form->hidden($reType.'.'.$approvalModel.'.id', array('id'=>'Approval'.Inflector::Classify($this->request->controller).'Id','default'=>$approvalComment['id']));
			echo $this->Form->hidden($reType.'.'.$approvalModel.'.approval_id', array('id'=>false,'default'=>$approval['id']));
		}else{
			echo "here";
			echo $this->Form->input($reType.'.'.$approvalModel.'.user_id', array('id'=>'Approval'.Inflector::Classify($this->request->controller).'UserId','label'=>'Select user',
			'name'=>'data['.$reType.']['.$approvalModel.'][user_id][]', 'class'=>'form-control select', $toRequired, 'options' => $approversList,'selected'=>$from['id']));

			echo $this->Form->hidden($reType.'.'.$approvalModel.'.id', array('id'=>'Approval'.Inflector::Classify($this->request->controller).'Id','default'=>$approval['id']));
		}		
		echo $this->Form->hidden($reType.'.'.$approvalModel.'.approval_step_id', array('id'=>'Approval'.Inflector::Classify($this->request->controller).'ApprovalStepId','default'=>$step['ApprovalStep']['id']));
		echo $this->Form->hidden($reType.'.'.$approvalModel.'.approval_process_id', array('id'=>'Approval'.Inflector::Classify($this->request->controller).'ApprovalProcessId','default'=>$step['ApprovalStep']['approval_process_id']));


		echo $this->Form->hidden($reType.'.'.$approvalModel.'.approval_step_id', array('id'=>'Approval'.Inflector::Classify($this->request->controller).'From','default'=>$current_step_id));
		echo $this->Form->hidden($reType.'.'.$approvalModel.'.from', array('id'=>'Approval'.Inflector::Classify($this->request->controller).'From','default'=>$this->Session->read('User.id')));
		echo $this->Form->hidden($reType.'.'.$approvalModel.'.record', array('id'=>'Approval'.Inflector::Classify($this->request->controller).'Record','default'=>$this->request->params['pass'][0]));
		echo $this->Form->hidden($reType.'.'.$approvalModel.'.controller_name', array('id'=>'Approval'.Inflector::Classify($this->request->controller).'ControllerName','default'=>$this->request->controller));
		echo $this->Form->hidden($reType.'.'.$approvalModel.'.model_name', array('id'=>'Approval'.Inflector::Classify($this->request->controller).'ModelName','default'=>Inflector::classify($this->request->controller)));
		?>
	</div>
	<div class="col-md-12">
		<?php
		if($reType == 'ApprovalComment'){			
			if($approvalModel == 'ApprovalComment' && empty($approvalComment['comments'])){
				echo 1;
				echo $this->Form->input($reType.'.'.$approvalModel.'.comments',array('id'=>'Approval'.Inflector::Classify($this->request->controller).'Comments','type'=>'textarea', $commentRequired, 'rows'=>4, 'class'=>'form-control'));	
			}else{
				echo 2;
				echo $this->Form->hidden($reType.'.'.$approvalModel.'.comments',array('default'=>$approvalComment['comments']));	

				echo $this->Form->input($reType.'.'.$approvalModel.'.response',array('id'=>'Approval'.Inflector::Classify($this->request->controller).'Response','type'=>'textarea', $commentRequired, 'rows'=>4, 'class'=>'form-control'));
			}
		}else{
			echo 3;
			echo $this->Form->input($reType.'.'.$approvalModel.'.comments',array('id'=>'Approval'.Inflector::Classify($this->request->controller).'Comments','type'=>'textarea', $commentRequired, 'rows'=>4, 'class'=>'form-control'));	
		}
		
		
		?>						
</div>
<div class="col-md-4">
<?php
if($approval['from'] == $this->Session->read('User.id')){
	$approvalModeStatus = 'readonly';
}
 echo $this->Form->input($reType.'.'.$approvalModel.'.approval_mode',array(
	'id'=>'Approval'.Inflector::Classify($this->request->controller).'ApprovalMode',
	'type'=>'radio',
	'class'=>'',
	'options'=>array(0=>'View Only',1=>'Edit'),
	'default'=>$approvalMode,
	$approvalModeStatus
));?>						
</div>
<div class="col-md-4"><?php 
if($approval['from'] == $this->Session->read('User.id')){
	$approvalTypeStatus = 'readonly';
}
echo $this->Form->input($reType.'.'.$approvalModel.'.approval_type',array(
	'id'=>'Approval'.Inflector::Classify($this->request->controller).'ApprovalType',
	'type'=>'radio',
	'class'=>'',
	'options'=>array(0=>'All',1=>'Any'),
	'default'=>$approvalType,
	$approvalTypeStatus
));?>						
</div>
<div class="col-md-4"><?php 
$approvalStatuses = array(0=>'Pending',1=>'Approved',2=>'Not Approved');

if($approval['from'] == $this->Session->read('User.id')){
	unset($approvalStatuses[1]);
}



if($this->action == 'add'){
	unset($approvalStatuses[1]);
	unset($approvalStatuses[2]);

	echo $this->Form->input($reType.'.'.$approvalModel.'.stauts',array(
		'id'=>'Approval'.Inflector::Classify($this->request->controller).'stauts',
		'type'=>'radio',
		'legend'=>'Status',
		'class'=>'',
		'options'=>$approvalStatuses,'default'=>0));	
}else{
	if($user == $this->Session->read('User.id'))	echo $this->Form->input($reType.'.'.$approvalModel.'.stauts',array('id'=>'Approval'.Inflector::Classify($this->request->controller).'stauts','type'=>'radio','class'=>'','options'=>$approvalStatuses,'default'=>0));
}

?>
</div>
<?php } ?>
<?php echo $this->Form->hidden($reType.'.'.$approvalModel.'.approval_step_id',array('default'=>$current_step_id)); ?>

