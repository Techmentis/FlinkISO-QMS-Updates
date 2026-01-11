<?php 
	$disabledPublish = false;
	if($customTable){
		if(is_array(json_decode($customTable['CustomTable']['approvers'],true)) && !in_array($this->Session->read('User.id'), json_decode($customTable['CustomTable']['approvers'],true))){
			$disabledPublish = true;
		}			
	}
?>
<script type="text/javascript">
	$(document).ready(function() {
		$('select').chosen( { width: '100%' } );
	});
</script>
<?php
echo $this->Form->hidden('Access.skip_access_check',array('default'=>1));
echo $this->Form->hidden('Access.allow_access_user',array('default'=>$this->Session->read('User.id')));
?>
<?php 
$approvalModel = Inflector::classify($this->request->controller);
$pubshow == false;
$approvedByList = $approversList;

// foreach($approvedByList as $key => $value){
// 	if($key != $this->Session->read('User.employee_id')){
// 		// unset($approvedByList[$key]);
// 	}
// }


if($this->action == 'view'){
	$preparer = $this->viewVars[Inflector::singularize(Inflector::variable($this->request->controller))][Inflector::classify($this->request->controller)]['prepared_by'];
}

if($this->action == 'edit'){
	$preparer = $this->request->data[Inflector::classify($this->request->controller)]['prepared_by'];	
}

if($this->action == 'add'){
	$preparer = $this->Session->read('User.employee_id');
}

if($approval){ ?>
	<?php if($preparer && ($preparer == $this->Session->read('User.id') || $preparer == $this->Session->read('User.employee_id'))){ ?>		
		<div class="box box-warning">
			<div class="box-header with-border data-header" data-widget="collapse"><h3 class="box-title">Approvals / Sharing / Collaboration</h3>
				<div class="box-tools pull-right"><button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button></div>
			</div>
			<div class="box-body">
				<div class="row">
					<div class="col-md-12">
						<?php 
						echo $this->Form->input('Approval.'.$approvalModel.'.'.$approvalModel.'.user_id', array('id'=>'Approval'.Inflector::Classify($this->request->controller).'UserId', 'label'=>'Select user you want to send this record for approval',
							'name'=>'data[Approval]['.$approvalModel.'][user_id][]', 'options' => $approversList,'multiple'));
						echo $this->Form->hidden('Approval.'.$approvalModel.'.'.$approvalModel.'.from', array('id'=>'Approval'.Inflector::Classify($this->request->controller).'From', 'default'=>$this->Session->read('User.id')));
						echo $this->Form->hidden('Approval.'.$approvalModel.'.record', array('id'=>'Approval'.Inflector::Classify($this->request->controller).'Record','default'=>$this->request->params['pass'][0]));
						echo $this->Form->hidden('Approval.'.$approvalModel.'.controller_name', array('id'=>'Approval'.Inflector::Classify($this->request->controller).'ControllerName','default'=>$this->request->controller));
						echo $this->Form->hidden('Approval.'.$approvalModel.'.model_name', array('id'=>'Approval'.Inflector::Classify($this->request->controller).'ModelName','default'=>Inflector::classify($this->request->controller)));

						?>
					</div>
					<div class="col-md-12"><?php echo $this->Form->input('Approval.'.$approvalModel.'.comments',array('id'=>'Approval'.Inflector::Classify($this->request->controller).'Comments','type'=>'textarea', 'rows'=>2, 'class'=>'form-control'));?></div>
					<div class="col-md-4"><?php echo $this->Form->input('Approval.'.$approvalModel.'.approval_mode',array('id'=>'Approval'.Inflector::Classify($this->request->controller).'ApprovalMode','type'=>'radio','class'=>'','options'=>array(0=>'View Only',1=>'Edit'),'default'=>1));?></div>
					<div class="col-md-4"><?php echo $this->Form->input('Approval.'.$approvalModel.'.approval_type',array('id'=>'Approval'.Inflector::Classify($this->request->controller).'ApprovalType','type'=>'radio','class'=>'','options'=>array(0=>'All',1=>'Any'),'default'=>0));?></div>			
					<div class="col-md-4"><?php 
					if($approval['Approval']['user_id'] == $this->Session->read('User.id'))	echo $this->Form->input('ApprovalComment.stauts',array('id'=>'Approval'.Inflector::Classify($this->request->controller).'Stauts','type'=>'radio','class'=>'','options'=>array(0=>'Pending',1=>'Approved',2=>'Not Approved'),'default'=>0));
				?></div>
			</div>
		</div>	 
	</div>
<?php } ?>
<?php
// if($this->Session->read('User.is_mr') == 1 || $this->Session->read('User.is_approver') == 1 || $this->Session->read('User.is_hod') == 1){
if($this->Session->read('User.is_publisher') == 0){	
	echo "<div class='row'><div class='col-md-12 approval_checkbox_div'>" . $this->Form->input('Approval.'.$approvalModel.'.publish',array('id'=>Inflector::Classify($this->request->controller).'Publish','class'=>'checkbox'))."</div></div>"; ?>
<?php }
?>
<?php }else{ 	
	echo $this->Form->create('Approval',array('role'=>'form','class'=>'form')); ?>
	<div class="box box-warning">
		<div class="box-header with-border data-header" data-widget="collapse"><h3 class="box-title">Approvals / Sharing / Collaboration</h3>
			<div class="box-tools pull-right"><button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button></div>
		</div>
		<div class="box-body">
			<?php if($approvalComments){ ?>
				<div class="row">
					<div class="col-md-12">
						<table cellpadding="0" cellspacing="0" class="table table-hover">
							<tr>
								<th><?php echo $this->Paginator->sort('user_id'); ?></th>
								<th><?php echo $this->Paginator->sort('comments'); ?></th>
							</tr>			
							<?php foreach ($approvalComments as $approvalComment): ?>
								<tr>
									<td><?php echo h($approvalComment['User']['name']); ?></td>
									<td><?php echo h($approvalComment['ApprovalComment']['comments']); ?>&nbsp;</td>						
								</tr>
							<?php endforeach; ?>					
						</table>
					</div>
				</div>
			<?php }else{ ?>
				
			<?php } ?>		
			<div class="row">
				<div class="col-md-12">
					<?php 
					echo $this->Form->input('Approval.'.$approvalModel.'.user_id', array('id'=>'Approval'.Inflector::Classify($this->request->controller).'UserId','label'=>'Select user you want to send this record for approval',
						'name'=>'data[Approval]['.$approvalModel.'][user_id][]', 'options' => $approversList,'multiple'));
					echo $this->Form->hidden('Approval.'.$approvalModel.'.from', array('id'=>'Approval'.Inflector::Classify($this->request->controller).'From','default'=>$this->Session->read('User.id')));
					echo $this->Form->hidden('Approval.'.$approvalModel.'.record', array('id'=>'Approval'.Inflector::Classify($this->request->controller).'Record','default'=>$this->request->params['pass'][0]));
					echo $this->Form->hidden('Approval.'.$approvalModel.'.controller_name', array('id'=>'Approval'.Inflector::Classify($this->request->controller).'ControllerName','default'=>$this->request->controller));
					echo $this->Form->hidden('Approval.'.$approvalModel.'.model_name', array('id'=>'Approval'.Inflector::Classify($this->request->controller).'ModelName','default'=>Inflector::classify($this->request->controller)));

					?>
				</div>
				<div class="col-md-12"><?php echo $this->Form->input('Approval.'.$approvalModel.'.comments',array('id'=>'Approval'.Inflector::Classify($this->request->controller).'Comments','type'=>'textarea',  'rows'=>2, 'class'=>'form-control'));?></div>
				<div class="col-md-4"><?php echo $this->Form->input('Approval.'.$approvalModel.'.approval_mode',array('id'=>'Approval'.Inflector::Classify($this->request->controller).'ApprovalMode','type'=>'radio','class'=>'','options'=>array(0=>'View Only',1=>'Edit'),'default'=>1));?></div>
				<div class="col-md-4"><?php echo $this->Form->input('Approval.'.$approvalModel.'.approval_type',array('id'=>'Approval'.Inflector::Classify($this->request->controller).'ApprovalType','type'=>'radio','class'=>'','options'=>array(0=>'All',1=>'Any'),'default'=>0));?></div>			
				<div class="col-md-4"><?php 
				if($approval['Approval']['user_id'] == $this->Session->read('User.id'))	echo $this->Form->input('ApprovalComment.Stauts',array('id'=>'Approval'.Inflector::Classify($this->request->controller).'stauts','type'=>'radio','class'=>'','options'=>array(0=>'Pending',1=>'Approved',2=>'Not Approved'),'default'=>0));
			?></div>
		</div>	
		
	</div>
	<div class="box-footer">		
		<?php if($this->action == 'view'){
			echo "<div class='row'>";
			// if($this->Session->read('User.is_mr') == 1 || $this->Session->read('User.is_approver') == 1 || $this->Session->read('User.is_hod') == 1){
			if($this->Session->read('User.is_publisher') == 1 && $disabledPublish == false){
				echo "<div class='col-md-12 approval_checkbox_div'>" . $this->Form->input('Approval.'.$approvalModel.'.publish',array('id'=>'Approval'.Inflector::Classify($this->request->controller).'Publish','class'=>'checkbox'))."</div>";
				$pubshow = true;
			}else{
				echo "<div class='col-md-12 text-warning'><strong>Note:</strong> You are not an Approver, you must send this record for approval and approvers will publish the record after varification. Record will not reflect in any reports or dropsowns unless its approved and published.</div>";
			}

			echo "<div class='col-md-12'><br />" . $this->Form->submit('Submit',array('id'=>'Approval'.Inflector::Classify($this->request->controller).'Submit','class'=>'btn btn-md btn-success')) . "</div>";
			echo "</div>";
			echo $this->Form->end();
		}else{			
			if($this->request->data[Inflector::classify($this->request->controller)]['prepared_by']){
				$prepared_by = $this->request->data[Inflector::classify($this->request->controller)]['prepared_by'];
				$dis = '\'readonly\' => \'readonly\'';
			}else{
				$prepared_by = $this->Session->read('User.employee_id');
				$dis = '';
			} 
			
			echo "<div class='row'>";
			
			if($disabledPublish == false){
				// if($this->Session->read('User.is_approver') == 1){
				echo "<div class='col-md-2 approval_checkbox_div'><br />".$this->Form->input('Approval.'.$approvalModel.'.publish',array('id'=>'Approval'.Inflector::Classify($this->request->controller).'Publish','class'=>'checkbox','onClick'=>'addapp();'))."</div>";
				$pubshow = true;
				echo "<div class='col-md-5'>".$this->Form->input('Approval.'.$approvalModel.'.prepared_by',array('id'=>'Approval'.Inflector::Classify($this->request->controller).'PreparedBy','selected'=>$prepared_by, 'readonly'=>'readonly' , 'class'=>'form-control select'))."</div>";			
				echo "<div class='col-md-5'>".$this->Form->input('Approval.'.$approvalModel.'.approved_by',array('id'=>'Approval'.Inflector::Classify($this->request->controller).'ApprovedBy', 'onchange'=>'addsignature(this.value,this.id)', 'options'=>$approvedByList, 'class'=>'form-control select'))."</div>";
			}else{
				echo "<div class='col-md-12 hide'>".$this->Form->input('Approval.'.$approvalModel.'.prepared_by',array('id'=>'Approval'.Inflector::Classify($this->request->controller).'PreparedBy','selected'=>$prepared_by, 'readonly'=>'readonly' , 'class'=>'form-control select'))."</div>";			
				echo "<div class='col-md-12 text-warning'><strong>Note:</strong> You are not an Approver, you must send this record for approval and approvers will publish the record after varification. Record will not reflect in any reports or dropsowns unless its approved and published.</div>";				
			}			
			
			echo "</div>";
		}?>
	</div>
</div>	
<?php if($preparer && ($preparer == $this->Session->read('User.id') || $preparer == $this->Session->read('User.employee_id'))){ ?>
	
<?php }else{
	// if(($this->Session->read('User.is_mr') == 1 || $this->Session->read('User.is_approver') == 1 || $this->Session->read('User.is_hod') == 1) && $pubshow != 1){
	if($this->Session->read('User.is_publisher') == 1  && $pubshow != 1){
		echo $this->Form->input('Approval.'.$approvalModel.'.publish',array('id'=>'Approval'.Inflector::Classify($this->request->controller).'Publish','class'=>'checkbox'));
	}
} ?>	
<?php } ?>
<script type="text/javascript">
	$().ready(function(){

		$("#Approval<?php echo Inflector::Classify($this->request->controller);?>Comments").prop('required',false);

		$("#Approval<?php echo Inflector::Classify($this->request->controller);?>UserId").on('change',function(){			
			if($("#Approval<?php echo Inflector::Classify($this->request->controller);?>UserId option:selected").text() == 'Select'){
				$("#Approval<?php echo Inflector::Classify($this->request->controller);?>UserId option:selected").html('').trigger("chosen:updated");				
			}

			if($("#Approval<?php echo Inflector::Classify($this->request->controller);?>UserId option:selected").text() != 'Select' && $("#Approval<?php echo Inflector::Classify($this->request->controller);?>UserId option:selected").text() != ''){
				$('#Approval<?php echo Inflector::Classify($this->request->controller);?>Publish').prop('checked', false);
				$('#Approval<?php echo Inflector::Classify($this->request->controller);?>Publish').prop('disabled',true);
				$("#Approval<?php echo Inflector::Classify($this->request->controller);?>Comments").prop('required',true);
				$("#Approval<?php echo Inflector::Classify($this->request->controller);?>ApprovedBy").val("-1").trigger('chosen:updated').prop('disabled',true).trigger('chosen:updated').prop('disabled',false);
			}else{
				$('#Approval<?php echo Inflector::Classify($this->request->controller);?>Publish').prop('checked', false);
				$('#Approval<?php echo Inflector::Classify($this->request->controller);?>Publish').prop('disabled',false);
				$("#Approval<?php echo Inflector::Classify($this->request->controller);?>Comments").prop('required',false);
				$("#Approval<?php echo Inflector::Classify($this->request->controller);?>ApprovedBy").val("-1").trigger('chosen:updated').prop('disabled',false).trigger('chosen:updated').prop('disabled',false);
			}
		})

		$('select').chosen();
	});
	
	function addapp(){

		if ($('#Approval<?php echo Inflector::Classify($this->request->controller);?>Publish').is(':checked') == true) {
			$("#Approval<?php echo Inflector::Classify($this->request->controller);?>ApprovedBy").val("<?php echo $this->Session->read('User.employee_id')?>").trigger('chosen:updated');
		}else{		
			$("#Approval<?php echo Inflector::Classify($this->request->controller);?>ApprovedBy").val("-1").trigger('chosen:updated');
		}
	}
</script>
<script type="text/javascript">
	function addappedit(){

				// if ($('#<?php echo Inflector::Classify($this->request->controller);?>Publish').is(':checked') == true) {
		$("#<?php echo Inflector::Classify($this->request->controller);?>ApprovedBy").val("<?php echo $this->Session->read('User.employee_id')?>").trigger('chosen:updated');
				// }else{		
				// 	$("#<?php echo Inflector::Classify($this->request->controller);?>ApprovedBy").val("-1").trigger('chosen:updated');
				// }
	}
	function approveedit(val){
		if (val == 1) {
			$('#<?php echo Inflector::Classify($this->request->controller);?>Publish').prop('checked',true);
			$("#<?php echo Inflector::Classify($this->request->controller);?>ApprovedBy").val("<?php echo $this->Session->read('User.employee_id')?>").trigger('chosen:updated');
		}else{		
			$('#<?php echo Inflector::Classify($this->request->controller);?>Publish').prop('checked',false);
			$("#<?php echo Inflector::Classify($this->request->controller);?>ApprovedBy").val("-1").trigger('chosen:updated');
		}
	}
</script>
