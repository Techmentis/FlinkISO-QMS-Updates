<?php echo $this->Html->script(array('jquery.validate.min', 'jquery-form.min')); ?>
<?php echo $this->fetch('script'); ?><div id="records_ajax">
	<?php echo $this->Session->flash();?>	

	<div class="records ">
		<?php echo $this->element('nav-header-lists',array('postData'=>array('pluralHumanName'=>'Records','modelClass'=>'Record','options'=>array(),'pluralVar'=>'records'))); ?>

		<div class="nav panel panel-default">
			<div class="records form col-md-12">
				<?php echo $this->Form->create('Record',array('role'=>'form','class'=>'form')); ?>
				<div class="row">
					<div class="col-md-12">
						<?php
						echo $this->Form->input('title',array('class'=>'form-control'))?>
					</div>
					<div class="col-md-12">
						<?php echo $this->element('qc_doc_header',array('document',$this->request->data['Record']['qc_document_id']));?>
					</div>
					<?php
					echo "<div class='col-md-12'>".$this->Form->hidden('qc_document_id',array('class'=>'form-control', 'style'=>'','default'=>$this->request->data['Record']['qc_document_id'])) . '</div>';  ?>
				</div>

				<div class="row">
					<div class="col-md-12">
						<?php 
						$key = $this->data['Record']['file_key'];
						$file_type = $qcDoc['QcDocument']['file_type'];
						$file_name = $qcDoc['QcDocument']['title'];
						$document_number = $qcDoc['QcDocument']['document_number'];
						$document_version = $qcDoc['QcDocument']['revision_number'];

						$file_type = $qcDoc['QcDocument']['file_type'];
						
						if($file_type == 'doc' || $file_type == 'docx'){
							$documentType = 'word';
						}

						if($file_type == 'xls' || $file_type == 'xlsx'){
							$documentType = 'cell';
						}

						$mode = 'edit';

						$file_path = $this->data['Record']['id'];


						$file = $document_number.'-'.$file_name.'-'.$document_version.'.'.$file_type;

						echo $this->element('onlyoffice',array(
							'url'=>$url,
							'placeholderid'=>$placeholderid,
							'panel_title'=>'Document Viewer',
							'mode'=>$mode,
							'path'=>$file_path,
							'file'=>$file,
							'filetype'=>$file_type,
							'documentType'=>$documentType,
							'userid'=>$this->Session->read('User.id'),
							'username'=>$this->Session->read('User.username'),
							'preparedby'=>$masterListOfFormat['PreparedBy']['name'],
							'filekey'=>$key,            
							'record_id'=>$this->data['Record']['id'],
							'company_id'=>$this->Session->read('User.company_id'),
							'controller'=>$this->request->controller,
						));
						?>
					</div>
				</div>	
				<div class="row">
					<div class="hide"></div>
					<?php		
					echo "<div class='col-md-12'>".$this->Form->input('comments',array('class'=>'form-control',)) . '</div>'; 
					?>
					<?php
					echo $this->Form->input('id');
		// echo $this->Form->hidden('created_by',array());
					echo $this->Form->hidden('modified_by',array('default'=>$this->Session->read('User.id')));
		// echo $this->Form->hidden('prepared_by',array('default'=>$this->Session->read('User.employee_id')));
					echo $this->Form->hidden('History.pre_post_values', array('value'=>json_encode($this->data)));
					echo $this->Form->input('branchid', array('type' => 'hidden', 'value' => $this->Session->read('User.branch_id')));
					echo $this->Form->input('departmentid', array('type' => 'hidden', 'value' => $this->Session->read('User.department_id')));
		// echo $this->Form->input('master_list_of_format_id', array('type' => 'hidden', 'value' => $documentDetails['MasterListOfFormat']['id']));
					?>

				</div>
				<div class="">
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
				<div class="row">
					<div class="col-md-12">		
						<?php if($this->request->params['named']['approval_id'])echo $this->element('approval_form',array('approval'=>$approval));?>
						<?php echo $this->element('approval_history',array('approvals'=>$approvals,'current_approval'=>$this->request->params['named']['approval_id'],'approvalComments',$approvalComments));?>
					</div>
				</div>
			</div>

		</div>
	</div>
	<script>
		$.validator.setDefaults({
			ignore: null,
			errorPlacement: function(error, element) {
				
			},
		});
		
		$().ready(function() {
			jQuery.validator.addMethod("greaterThanZero", function(value, element) {
				return this.optional(element) || $("#"+element.id+" option:selected").text() != 'Select';
			}, "Please select the value");

			$('#RecordEditForm').validate({        	
				
			}); 
			
			$("#submit-indicator").hide();
			$("#submit_id").click(function(){
				if($('#RecordEditForm').valid()){
					$("#submit_id").prop("disabled",true);
					$("#submit-indicator").show();
					$('#RecordEditForm').submit();
				}

			});

			
		});
	</script>
