<?php echo $this->Html->script(array('jquery.validate.min', 'jquery-form.min')); ?>
<?php echo $this->fetch('script'); ?>
<?php echo $this->Session->flash();?>
<style type="text/css">
	.info-box-number{font-size: 14px !important; font-weight: 400;}
	.info-box-text{font-weight: 600;}
	.nomargin-checkbox .checkbox{
		margin-top: 0px !important;
	}
	.nomargin-checkbox label{
		margin: 0 0 0 0 !important;
	}
	.chosen-container { 
		width: 100% !important; /* desired width */
	} 
	#qcDocuments_ajax_add_child .row{
		margin: 0;
	}
</style>
<script type="text/javascript">
	function addfiletype(filetype){
		$("#QcDocumentFileType").val(filetype);	
		$("#filetypediv").hide();
	}
</script>				
<div id="qcDocuments_ajax_add_child">
	<?php echo $this->Form->Create('QcDocument', array('action'=>'add'), array('id'=>'QcDocumentAddChildForm', 'role'=>'form','class'=>'form')); ?>
	<div class="nav panel panel-default">	
		<div class="row">
			<div class="col-md-12"><h4>Add Child Document : Step - I</h4></div>
			<div class="col-md-12">
				<div class="box box-default">
					<div class="box-header with-border">
						<i class="fa fa-question-circle"></i>
						<h3 class="box-title">Select Document Type</h3>
					</div>
					<div class="box-body">
						<div class="row">
							<div class="col-md-3 col-sm-6 col-12">
								<div class="info-box">
									<span class="info-box-icon bg-yellow"><i class="fa fa-file-word-o" aria-hidden="true"></i></span>

									<div class="info-box-content">
										<span class="info-box-text">Word</span>
										<span class="info-box-number">
											<?php echo $this->Html->link('.doc','#',array('onClick'=>'addfiletype("doc")')); ?>,
											<?php echo $this->Html->link('.docx','#',array('onClick'=>'addfiletype("docx")')); ?>,
											otd,txt
										</span>
									</div>
								</div>
							</div>
							
							<div class="col-md-3 col-sm-6 col-12">
								<div class="info-box">
									<span class="info-box-icon bg-yellow"><i class="fa fa-file-excel-o" aria-hidden="true"></i></span>
									<div class="info-box-content">
										<span class="info-box-text">Spreed Sheet</span>
										<span class="info-box-number">
											<?php echo $this->Html->link('.xls','#',array('onClick'=>'addfiletype("xls")')); ?>,
											<?php echo $this->Html->link('.xlsx','#',array('onClick'=>'addfiletype("xlsx")')); ?>,
											ods,csv,
										</span>
									</div>
								</div>					    
							</div>
							
							<div class="col-md-3 col-sm-6 col-12">
								<div class="info-box">
									<span class="info-box-icon bg-yellow"><i class="fa fa-file-powerpoint-o" aria-hidden="true"></i></span>
									<div class="info-box-content">
										<span class="info-box-text">Presentation</span>
										<span class="info-box-number">ppt</span>
									</div>
								</div>
							</div>					  
							

							<div class="col-md-3 col-sm-6 col-12">
								<div class="info-box">
									<span class="info-box-icon bg-disabled"><i class="fa fa-external-link" aria-hidden="true"></i></span>

									<div class="info-box-content">
										<span class="info-box-text">Other</span>
										<span class="info-box-number">
											These documents can not be created with in syetem. Upload them directly.
										</span>
									</div>
								</div>
							</div>

						</div>
					</div>
				</div>			
			</div>
			<dlv class="col-md-12">
				<?php echo "<div class='col-md-12'>".$this->Form->hidden('file_type',array('class'=>'form-control')) . '</div>'; ?>
				<div id="filetypediv" class="text-danger">Select file type.</div>
			</dlv>
		</div>
		<div class="row">		
			<?php
			echo "<div class='col-md-12'>".$this->Form->input('parent_document_id',array('class'=>'form-control', 'style'=>'','default'=>$parent_document_id)) . '</div>'; 
			echo "<div class='col-md-6'>".$this->Form->input('title',array('class'=>'form-control', 'style'=>'')) . '</div>'; 
			echo "<div class='col-md-3'>".$this->Form->input('document_number',array('class'=>'form-control','label'=>'Document Number')) . '</div>'; 
			echo "<div class='col-md-3'>".$this->Form->input('revision_number',array('class'=>'form-control','label'=>'Rivision/Version')) . '</div>'; 			
			?>
		</div>			
		<div class="row">
			<?php
			
			echo "<div class='col-md-4'>".$this->Form->input('date_created',array('class'=>'form-control',)) . '</div>'; 
			echo "<div class='col-md-4'>".$this->Form->input('date_of_issue',array('class'=>'form-control',)) . '</div>'; 
			echo "<div class='col-md-4'>".$this->Form->input('effective_from_date',array('class'=>'form-control',)) . '</div>'; 		

			echo "</div><div class='row'>";

			echo "<div class='col-md-4'>".$this->Form->input('date_of_review',array('class'=>'form-control',)) . '</div>'; 
			echo "<div class='col-md-4'>".$this->Form->input('revision_date',array('class'=>'form-control',)) . '</div>'; 
			echo "<div class='col-md-4'>".$this->Form->input('date_of_next_issue',array('class'=>'form-control',)) . '</div>'; 
			
			echo "</div><div class='row'>";

			echo "<div class='col-md-4'>".$this->Form->input('standard_id',array('class'=>'form-control', 'style'=>'','default'=>$standard_id)) . '</div>'; 
			echo "<div class='col-md-4'>".$this->Form->input('clause_id',array('class'=>'form-control', 'style'=>'','default'=>$clause_id)) . '</div>'; 		
			echo "<div class='col-md-4'>".$this->Form->input('qc_document_category_id',array('class'=>'form-control', 'style'=>'',$default=>$qc_document_category_id)) . '</div>'; 
			
			echo "</div><div class='row'>";
			
			echo "<div class='col-md-12'>".$this->Form->input('document_type',array('default'=>0, 'type'=>'radio', 'class'=>'','options'=>$customArray['documentTypes'])) . '</div>'; 
			echo "<div class='col-md-12'>".$this->Form->input('it_categories',array('legend'=>'Document Security', 'default'=>0,'type'=>'radio', 'class'=>'','options'=>$customArray['itCategories'])) . '</div>'; 

			echo "</div><div class='row'>";
			
			echo "<div class='col-md-4'>".$this->Form->input('prepared_by',array('class'=>'form-control',)) . '</div>'; 
			echo "<div class='col-md-4'>".$this->Form->input('issued_by',array('class'=>'form-control',)) . '</div>'; 
			echo "<div class='col-md-4'>".$this->Form->input('issuing_authority_id',array('class'=>'form-control', 'style'=>'')) . '</div>'; 		
			
			echo "</div><div class='row'>";

			echo "<div class='col-md-12'>".$this->Form->input('change_history',array('class'=>'form-control',)) . '</div>';
			
			echo "</div>";
			?>
			
			


			<div class='row'>
				<div class='col-md-12'>
					<div class="box box-default">
						<div class="box-header with-border">
							<i class="fa fa-share-alt"></i>
							<h3 class="box-title">Document Sharing</h3>
						</div>
						<div class="box-body">
							<div class="row">
								<?php		
								echo "<div class='col-md-12'>".$this->Form->input('share_with_branches',array('name'=>'data[QcDocument][share_with_branches][]', 'multiple','options'=>$branches, 'class'=>'form-control',)) . '</div>'; 
								echo "<div class='col-md-12'>".$this->Form->input('share_with_departments',array('name'=>'data[QcDocument][share_with_departments][]','multiple', 'options'=>$departments, 'class'=>'form-control',)) . '</div>'; 
								echo "<div class='col-md-12'>".$this->Form->input('share_with_designations',array('name'=>'data[QcDocument][share_with_designations][]','multiple', 'options'=>$designations, 'class'=>'form-control chosen',)) . '</div>'; 
								echo "<div class='col-md-12'>".$this->Form->input('user_id',array('name'=>'data[QcDocument][user_id][]','label'=>'Share with Users','multiple', 'options'=>$usernames, 'class'=>'form-control', 'style'=>'')) . '</div>'; 
								?>

							</div>
						</div>
					</div>
				</div>
			</div>
			
			<div class='row'>
				<div class='col-md-12'>
					<div class="box box-default">
						<div class="box-header with-border">
							<i class="fa fa-database"></i>
							<h3 class="box-title">Document Data Entry</h3>
						</div>
						<div class="box-body">
							<div class="row">
								<?php 
								
								echo "<div class='col-md-9'><br /><div class='nomargin-checkbox'><label>Do you want this document to be shared with users for scheduled data enrty? If yes, click YES below. You must define schedule.</label>".$this->Form->input('add_records',array('type'=>'checkbox','label'=>'Yes')) . '</div></div>'; 
								echo "<div class='col-md-3'>".$this->Form->input('schedule_id',array()) . '</div>'; 
							?>					</div>
						</div>
					</div>
				</div>
			</div>
			<?php 		echo "<div class='row'>";
			echo "<div class='col-md-12'>".$this->Form->input('document_status',array('default'=>0,'type'=>'radio', 'class'=>'','options'=>$customArray['documentStatuses'])) . '</div>';
			echo "<div class='col-md-4'>".$this->Form->hidden('user_session_id',array('class'=>'form-control', 'style'=>'')) . '</div>'; 
			echo "<div class='col-md-4'>".$this->Form->hidden('cr_status',array('class'=>'form-control',)) . '</div>'; 
			echo "<div class='col-md-4'>".$this->Form->hidden('mark_for_cr_update',array('class'=>'form-control',)) . '</div>'; 
			echo "<div class='col-md-4'>".$this->Form->hidden('temp_date_of_issue',array('class'=>'form-control',)) . '</div>'; 
			echo "<div class='col-md-4'>".$this->Form->hidden('temp_effective_from_date',array('class'=>'form-control',)) . '</div>'; 
			echo "<div class='col-md-4'>".$this->Form->hidden('cr_id',array('class'=>'form-control', 'style'=>'')) . '</div>'; 
			echo "<div class='col-md-4'>".$this->Form->hidden('old_cr_id',array('class'=>'form-control', 'style'=>'')) . '</div>'; 
			echo "<div class='col-md-4'>".$this->Form->hidden('parent_id',array('class'=>'form-control', 'style'=>'')) . '</div>'; 
			echo "<div class='col-md-4'>".$this->Form->hidden('parent_document_id',array('class'=>'form-control', 'style'=>'')) . '</div>'; 
			echo "<div class='col-md-12'>".$this->Form->input('linked_document_ids',array('label'=>'Linked Documents', 'name'=>'data[QcDocument][linked_document_id][]','class'=>'form-control','multiple','options'=>$parentQcDocuments)) . '</div>'; 
			
			echo $this->Form->hidden('step',array('default'=>2));						
			echo $this->Form->input('id');
			echo $this->Form->hidden('History.pre_post_values', array('value'=>json_encode($this->data)));
			echo $this->Form->input('branchid', array('type' => 'hidden', 'value' => $this->Session->read('User.branch_id')));
			echo $this->Form->input('departmentid', array('type' => 'hidden', 'value' => $this->Session->read('User.department_id')));			
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
			<?php echo $this->Html->image('indicator.gif', array('id' => 'submit-indicator','class'=>'hide')); ?>	
		</div>

	</div>
	<?php echo $this->Form->end(); ?>	
</div>

<script>
	$.validator.setDefaults({
		ignore: null,
		errorPlacement: function(error, element) {
			if (
				
				$(element).attr('name') == 'data[QcDocument][qc_document_category_id]' ||
				$(element).attr('name') == 'data[QcDocument][clause_id]' ||
				$(element).attr('name') == 'data[QcDocument][standard_id]' ||
				$(element).attr('name') == 'data[QcDocument][issuing_authority_id]')
			{	
				$(element).next().after(error);
			} else {
				$(element).after(error);
			}
		},
	});
	
	$().ready(function() {
		
		$("#filetypediv").hide();

		$("#QcDocumentShareWithBranches").chosen().width();
		$("#QcDocumentShareWithDepartments").chosen();
		$("#QcDocumentShareWithDesignations").chosen();
		$("#QcDocumentUserId").chosen();
		$("#QcDocumentLinkedDocumentIds").chosen();

		jQuery.validator.addMethod("greaterThanZero", function(value, element) {
			return this.optional(element) || $("#"+element.id+" option:selected").text() != 'Select';
		}, "Please select the value");

		$('#QcDocumentAddChildForm').validate({        	
			rules: {
				"data[QcDocument][qc_document_category_id]": {
					greaterThanZero: true,
				},
				"data[QcDocument][clause_id]": {
					greaterThanZero: true,
				},
				"data[QcDocument][standard_id]": {
					greaterThanZero: true,
				},
				"data[QcDocument][issuing_authority_id]": {
					greaterThanZero: true,
				}	                
			}
		}); 
		
		$("#submit-indicator").hide();
		$("#submit_id").click(function(e){	   
			if($("#QcDocumentFileType").val() == ''){	        		
				e.preventDefault();
				$("#filetypediv").show();
				return false;
			}else{
				$("#filetypediv").hide();
				return true;
			}
			if($('#QcDocumentAddChildForm').valid()){
				$("#submit_id").prop("disabled",true);
				$("#submit-indicator").show();
				$('#QcDocumentAddChildForm').submit();
			}

		});

		$('#QcDocumentQcDocumentCategoryId').change(function() {
			if ($(this).val() != -1 && $(this).next().next('label').hasClass("error")) {
				$(this).next().next('label').remove();
			}
		});
		$('#QcDocumentClauseId').change(function() {
			if ($(this).val() != -1 && $(this).next().next('label').hasClass("error")) {
				$(this).next().next('label').remove();
			}
		});
		$('#QcDocumentStandardId').change(function() {
			if ($(this).val() != -1 && $(this).next().next('label').hasClass("error")) {
				$(this).next().next('label').remove();
			}
		});
		$('#QcDocumentIssuingAuthorityId').change(function() {
			if ($(this).val() != -1 && $(this).next().next('label').hasClass("error")) {
				$(this).next().next('label').remove();
			}
		});
		
	});
</script>

