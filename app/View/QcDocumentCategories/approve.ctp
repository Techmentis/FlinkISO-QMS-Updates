 <div id="qcDocumentCategories_ajax">
 	<?php echo $this->Session->flash();?>	
 	<div class="nav panel panel-default">
 		<div class="qcDocumentCategories form col-md-12">
 			<h4><?php echo __('Approve Qc Document Category'); ?>		
 			<?php echo $this->Html->link(__('List'), array('action' => 'index'),array('id'=>'list','class'=>'label btn-info')); ?>
 			
 		</h4>
 		<?php echo $this->Form->create('QcDocumentCategory',array('role'=>'form','class'=>'form')); ?>
 		<div class="row">
 			<?php
 			echo "<div class='col-md-6'>".$this->Form->input('name') . '</div>'; 
 			echo "<div class='col-md-6'>".$this->Form->input('short_name') . '</div>'; 
 			echo "<div class='col-md-6'>".$this->Form->input('standard_id',array('style'=>'')) . '</div>'; 
 			echo "<div class='col-md-6'>".$this->Form->input('parent_id',array('style'=>'')) . '</div>'; 
 			?>
 			<?php
 			echo $this->Form->input('id');
 			echo $this->Form->hidden('History.pre_post_values', array('value'=>json_encode($this->data)));
 			echo $this->Form->input('branchid', array('type' => 'hidden', 'value' => $this->Session->read('User.branch_id')));
 			echo $this->Form->input('departmentid', array('type' => 'hidden', 'value' => $this->Session->read('User.department_id')));
 			echo $this->Form->input('master_list_of_format_id', array('type' => 'hidden', 'value' => $documentDetails['MasterListOfFormat']['id']));
 			?>

 		</div>
 		<div class="row">
 			<?php
 			if ($showApprovals && $showApprovals['show_panel'] == true) {
 				echo $this->element('approval_form');
 			} else {
 				echo $this->Form->input('publish', array('label' => __('Publish')));
 			}
 			?>
 			<?php echo $this->Form->submit(__('Submit'), array('div' => false, 'class' => 'btn btn-primary btn-success','id'=>'submit_id')); ?>
 			<?php echo $this->Html->image('indicator.gif', array('id' => 'submit-indicator')); ?>
 			<?php echo $this->Form->end(); ?>

 			<?php echo $this->Js->writeBuffer();?>
 		</div>
 	</div>
 	<script> $("[name*='date']").datepicker({
 		changeMonth: true,
 		changeYear: true,
 		dateFormat:'yy-mm-dd',
 	}); </script> 	
 </div>
 <?php echo $this->Js->get('#list');?>
 <?php echo $this->Js->event('click',$this->Js->request(array('action' => 'index', 'ajax'),array('async' => true, 'update' => '#qcDocumentCategories_ajax')));?>

 <?php echo $this->Js->writeBuffer();?>
</div>
<script>
	$.validator.setDefaults({
		submitHandler: function(form) {
			$(form).ajaxSubmit({
				url: "<?php echo Router::url('/', true); ?><?php echo $this->request->params['controller'] ?>/approve/<?php echo $this->request->params['pass'][0] ?>/<?php echo $this->request->params['pass'][1] ?>",
				type: 'POST',
				target: '#qcDocumentCategories_ajax',
				beforeSend: function(){
					$("#submit_id").prop("disabled",true);
					$("#submit-indicator").show();
				},
				complete: function() {
					$("#submit_id").removeAttr("disabled");
					$("#submit-indicator").hide();
				},
				error: function(request, status, error) {                    
					alert('Action failed!');
				}
			});
		}
	});
	$().ready(function() {
		$("#submit-indicator").hide();
		$('#QcDocumentCategoryApproveForm').validate();        
	});
</script>
<?php echo $this->Html->script(array('jquery.validate.min', 'jquery-form.min')); ?>
<?php echo $this->fetch('script'); ?>
