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
</style>

<style>
    .chosen-choices{
        max-height:80px;
        overflow: auto;
    }
</style>
<script type="text/javascript">
	function addfiletype(filetype){
		$("#QcDocumentFileType").val(filetype);	
		$("#filetypediv").hide();
	}

	$().ready(function(){
		$("#QcDocumentFile").on('change',function(){			
			let pathext = this.value;
			const pathfile = pathext.split(".");
			const arr = pathfile.length;
			const ext = pathfile[arr-1];
			
			var earr = ["doc","docx","xls","xlsx","pdf","ppt","odt","txt"];
			
			if($.inArray(ext,earr) > -1){
				
			}else{
				alert('This file type is not allowed : ' + ext);
				location.reload(true);
			}
			let name = pathext.split('.' + ext);
			console.log(name);
			let path = name[0];
			const file = path.split("\\");			
			const filename  = file[file.length - 1];

			var str = filename;
			
			// if (!isNaN(str[0]) && !isNaN(str[str.length - 1])) {
			// 	this.value = "";
			// 	alert('File starting or ending with numbers is not allowed.');
			// 	location.reload(true);
			// } else {
				
			// }
			$("#QcDocumentName").val(filename);
			// $("#QcDocumentTitle").val(filename);
			add_title(filename);
			addfiletype(ext);
		});
	})

</script>				
<div id="qcDocuments_ajax">
	<?php echo $this->element('nav-header-lists',array('postData'=>array('pluralHumanName'=>'Qc Documents','modelClass'=>'QcDocument','options'=>array("sr_no"=>"Sr No","document_number"=>"Document Number","reference_number"=>"Reference Number","issue_number"=>"Issue Number","date_of_next_issue"=>"Date Of Next Issue","date_of_issue"=>"Date Of Issue","effective_from_date"=>"Effective From Date","revision_number"=>"Revision Number","date_of_review"=>"Date Of Review","revision_date"=>"Revision Date","document_type"=>"Document Type","it_categories"=>"It Categories","document_status"=>"Document Status","issued_by"=>"Issued By","archived"=>"Archived","change_history"=>"Change History","cr_status"=>"Cr Status","mark_for_cr_update"=>"Mark For Cr Update","temp_date_of_issue"=>"Temp Date Of Issue","temp_effective_from_date"=>"Temp Effective From Date","linked_formats"=>"Linked Formats","cover_page"=>"Cover Page","page_orientation"=>"Page Orientation"),'pluralVar'=>'qcDocuments'))); ?>
	<div class="nav panel panel-default">

		<div class="qcDocuments form col-md-12">
			
			<?php echo $this->Form->create('QcDocument',array('role'=>'form','class'=>'form','type'=>'file')); ?>
			<?php if(isset($document)){ ?>
				<div class="row">
					<div class="col-md-12">
						<?php echo $this->element('qc_doc_header',array('document',$document));?>
					</div>					
				</div>
			<?php } ?>
			
			<div class="row" style="margin-top:20px">

				<div class="col-md-12">
					<div class="docs">
						<ul class="nav nav-tabs docs" role="tablist">
							<li role="presentation" class="active"><a href="#upload" aria-controls="upload" role="tab" data-toggle="tab">Upload</a></li>
							<li role="presentation"><a href="#add_blank" aria-controls="later" role="tab" data-toggle="tab">Select From Template</a></li>					
							<li role="presentation"><a href="#later" aria-controls="later" role="tab" data-toggle="tab">Add Later</a></li>					
						</ul>
						<div class="tab-content">
							<div role="tabpanel" class="tab-pane active" id="upload">
								<div class="row">
									<div class="col-md-12">
										<div class="box box-default">
											<div class="box-body">
												<div class="row">
													<div class="col-md-6 col-sm-6 col-12">
														<div class="info-box">
															<div class="box-content">
																<!-- <span class="info-box-text">Upload Document</span> -->
																<span class="info-box-number">
																	<?php echo "<div class='col-md-12 '><span class='control-fileupload'><i class='fa fa-file'></i>". $this->Form->input('file',array('class'=>$block_class ,  'type'=>'file', 'label'=>'Upload Document', 'div'=>false , 'onchange'=>'$(this).prev("label").html($(this).val().substr(12));')) . "</div>";?>

																	<!-- <span class='control-fileupload'><i class='fa fa-file'></i><?php echo $this->Form->input('file',array('type'=>'file'));?> -->
																	</span>
																</div>
															</div>
														</div>
														<div class="col-md-6 col-sm-6 col-12">
															<div class="info-box">
																<div class="box-content" style="padding:10px 10px 0 10px">
																	<p class="text-left">
																		<i class="fa fa-file-word-o fa-lg" style="color:#3f628a; font-size:40px; margin:2px 10px 10px 2px;"></i> 
																		<i class="fa fa-file-excel-o fa-lg" style="color:#387951; font-size:40px; margin:2px 10px 10px 2px;"></i> 
																		<i class="fa fa-file-powerpoint-o fa-lg" style="color:#a5494a; font-size:40px; margin:2px 10px 10px 2px;"></i>
																		<i class="fa fa-file-pdf-o fa-lg" style="color:#a5494a; font-size:40px; margin:2px 10px 10px 2px;"></i></p>
																		<p>You can upload doc, docx, txt, odt, xls, xlsx, ppt, pdf files</p>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>			
											</div>
											<dlv class="col-md-12" style="min-height: auto;"><?php echo "<div class='hide'>".$this->Form->hidden('file_type',array('class'=>'form-control')) . '</div>'; ?></dlv>
										</div>
									</div>
									<div role="tabpanel" class="tab-pane" id="later">
										<div class="row">
											<div class="col-md-12">
												<div class="box box-default">
													<div class="box-body">
														<div class="row">
															<div class="col-md-12 col-sm-12 col-12">
																<p class="text-warning">You can skip the upload document action for now. You can add doucument later from edit page, after adding details below..</p>
															</div>
														</div>
													</div>
												</div>								
											</div>
										</div>
									</div>
									<div role="tabpanel" class="tab-pane" id="add_blank">
										<div class="box box-default" style="padding:0"  id="loadblankfile">
											<div class="box-body">
												<div class="box-content">
													<div class="row">										
														<?php foreach($templates as $template){
															if($template['Template']['file_type'] == 'docx'){ ?>
																<div class="col-md-2 text-center templates">
																	<div class="panel panel-body templates" id="tmp_<?php echo $template['Template']['id'];?>">
																		<a href="javascript:void(0);" onclick="loadtemplate('<?php echo $template['Template']['id'];?>');"><i class="fa fa-file-word-o fa-lg" style="color:#3f628a; font-size:40px;margin: 10px 0;"></i></a><p><?php echo $template['Template']['name'];?></p>
																	</div>
																</div>
															<?php }?>										  					
															<?php if($template['Template']['file_type'] == 'xlsx'){ ?>
																<div class="col-md-2 text-center">
																	<div class="panel panel-body templates" id="tmp_<?php echo $template['Template']['id'];?>">
																		<a href="javascript:void(0);" onclick="loadtemplate('<?php echo $template['Template']['id'];?>');"><i class="fa fa-file-excel-o fa-lg" style="color:#387951; font-size:40px;margin: 10px 0;"></i></a> <p><?php echo $template['Template']['name'];?></p>
																	</div>
																</div>
															<?php } ?>
														<?php }?>
														<div class="col-md-12">						        	
															<div class="alert alert-default"><strong>Note:</strong> Click on template you want, then, fill in rest of the form and submit the form. You can then add actualt content to the file from Edit view. <?php echo $this->Html->link('Add New Template',array('controller'=>'templates','action'=>'add'),array('style'=>'color:#333a40'));?></div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div id="filetypediv" class="text-danger">Select file first.</div>
						</div>

					</div>
					<div class="row">						
						<?php
						if(isset($this->request->params['named']['parent_id'])){
							echo "<div class='col-md-12'>".$this->Form->hidden('parent_document_id',array('default'=>$this->request->params['named']['parent_id'], 'class'=>'form-control', 'style'=>'')) . '</div>'; 
						}else{
							echo "<div class='col-md-12'>".$this->Form->input('parent_document_id',array('class'=>'form-control', 'style'=>'')) . '</div>'; 
						}
						echo "<div class='col-md-6'>".$this->Form->input('name',
							array(
								'label'=>'<strong>Name:</strong> Do not add document extension (doc,docx,xlsx,pdf etc) in this field',
								'class'=>'form-control', 'style'=>'','onChange'=>'add_title(this.value);')) . '</div>'; 
						echo "<div class='col-md-6 hide'>".$this->Form->hidden('title',
							array('class'=>'form-control', 'style'=>'','onChange'=>'check_duplicates("t",this.value,this.id);')) . '</div>'; 
						echo "<div class='col-md-3'>".$this->Form->input('document_number',
							array('class'=>'form-control','label'=>'Document Number','default'=>$document_number, 'onChange'=>'check_duplicates("n",this.value,this.id);')) . '</div>'; 
						echo "<div class='col-md-3'>".$this->Form->input('revision_number',
							array('class'=>'form-control','label'=>'Revision/Version',)) . '</div>'; 			
							?>
							<div class="col-md-12 text-danger" style="min-height: auto;" id="duplicate_errors"></div>
						</div>			
						<div class="row">
							<?php
							
							echo "<div class='col-md-4'>".$this->Form->input('date_created',array('class'=>'form-control','value'=>date('Y-m-d'))) . '</div>';
							echo "<div class='col-md-4'>".$this->Form->input('date_of_issue',array('class'=>'form-control','value'=>date('Y-m-d',strtotime('+1 week')))) . '</div>';
							echo "<div class='col-md-4'>".$this->Form->input('effective_from_date',array('class'=>'form-control','value'=>date('Y-m-d',strtotime('+2 week')))) . '</div>';

							echo "</div><div class='row'>";

							echo "<div class='col-md-4'>".$this->Form->input('date_of_review',array('class'=>'form-control','value'=>date('Y-m-d',strtotime('+3 year')))) . '</div>';
							echo "<div class='col-md-4'>".$this->Form->input('revision_date',array('class'=>'form-control','value'=>date('Y-m-d',strtotime('+3 year 1 week')))) . '</div>'; 
							echo "<div class='col-md-4'>".$this->Form->input('date_of_next_issue',array('class'=>'form-control','value'=>date('Y-m-d',strtotime('+3 year +2 week')))) . '</div>';
							
							echo "</div><div class='row'>";
							if(isset($this->request->params['named']['parent_id'])){
								echo "<div class='col-md-4'>".$this->Form->input('standard_id',array('default'=>$document['QcDocument']['standard_id'], 'class'=>'select2-drop-mask', 'style'=>'')) . '</div>'; 
								echo "<div class='col-md-4'>".$this->Form->input('clause_id',array('default'=>$document['QcDocument']['clause_id'],'class'=>'form-control', 'style'=>'')) . '</div>'; 		
								echo "<div class='col-md-4'>".$this->Form->input('qc_document_category_id',array('default'=>$document['QcDocument']['qc_document_category_id'],'class'=>'form-control', 'style'=>'')) . '</div>'; 
								echo "<div class='col-md-12'>".$this->Form->input('additional_clauses',array('name'=>'data[QcDocument][additional_clauses][]', 'class'=>'form-control', 'multiple', 'options'=>$clauses, 'style'=>'','selected'=>json_decode($document['QcDocument']['additional_clauses']))) . '</div>'; 
							}else{
								echo "<div class='col-md-4'>".$this->Form->input('standard_id',array('class'=>'select2-drop-mask', 'style'=>'')) . '</div>'; 
								echo "<div class='col-md-4'>".$this->Form->input('clause_id',array('class'=>'form-control', 'style'=>'')) . '</div>'; 		
								echo "<div class='col-md-4'>".$this->Form->input('qc_document_category_id',array('class'=>'form-control', 'style'=>'')) . '</div>'; 
								echo "<div class='col-md-12'>".$this->Form->input('additional_clauses',array('name'=>'data[QcDocument][additional_clauses][]', 'class'=>'form-control', 'multiple', 'options'=>$clauses, 'style'=>'')) . '</div>'; 
							}
							
							echo "</div><div class='row'>";
							
							echo "<div class='col-md-12'>".$this->Form->input('document_type',array('default'=>0, 'type'=>'radio', 'class'=>'','options'=>$customArray['documentTypes'])) . '</div>'; 
							echo "<div class='col-md-12'>".$this->Form->input('it_categories',array('legend'=>'Document Security','type'=>'radio', 'class'=>'','options'=>$customArray['itCategories'],'default'=>0)) . '</div>';
							
							$updateCustomTableDocuments = array(
								0=>'Yes, Update Custom HTML Form Document when any changes are made to this document',
								1=>'No, Custom HTML Form Document will be updated manually',
							);
							echo "<div class='col-md-12'>".$this->Form->input('update_custom_table_document',array('legend'=>'Update Custom HTML From Document','type'=>'radio', 'class'=>'','options'=>$updateCustomTableDocuments,'default'=>0)) . '</div>'; 

							echo "</div><div class='row'>";
							
							echo "<div class='col-md-4'>".$this->Form->input('prepared_by',array('default'=>$this->Session->read('User.employee_id'), 'class'=>'form-control',)) . '</div>'; 
							echo "<div class='col-md-4'>".$this->Form->input('issued_by',array('class'=>'form-control',)) . '</div>'; 
							echo "<div class='col-md-4'>".$this->Form->input('issuing_authority_id',array('class'=>'form-control', 'style'=>'')) . '</div>'; 		
				// echo "<div class='col-md-4'>".$this->Form->input('archived',array('type'=>'checkbox', 'class'=>'',)) . '</div>'; 
							
							echo "</div><div class='row'>";

							echo "<div class='col-md-12'>".$this->Form->input('change_history',array('class'=>'form-control',)) . '</div>';
							
							echo "<div class='col-md-6'>".$this->Form->hidden('cover_page',array('default' => 0)) . '</div>'; 
							echo "<div class='col-md-6'>".$this->Form->hidden('page_orientation',array('default' => 0)) . '</div>'; 

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
											<div class="">
												<?php
												if(isset($this->request->params['named']['parent_id'])){

													echo "<div class='col-md-8'>".$this->Form->input('share_with_branches',array('name'=>'data[QcDocument][share_with_branches][]', 'multiple','options'=>$branches, 'class'=>'form-control','selected'=>json_decode($document['QcDocument']['share_with_branches'],true) )) . '</div>'; 
													echo "<div class='col-md-12'>".$this->Form->input('share_with_departments',array('name'=>'data[QcDocument][share_with_departments][]','multiple', 'options'=>$departments, 'class'=>'form-control','selected'=>json_decode($document['QcDocument']['share_with_departments'],true) )) . '</div>'; 
													echo "<div class='col-md-12'>".$this->Form->input('share_with_designations',array('name'=>'data[QcDocument][share_with_designations][]','multiple', 'options'=>$designations, 'class'=>'form-control chosen','selected'=>json_decode($document['QcDocument']['share_with_designations'],true)  )) . '</div>'; 
													echo "<div class='col-md-12'>".$this->Form->input('user_id',array('name'=>'data[QcDocument][user_id][]','label'=>'Share with Users','multiple', 'options'=>$usernames, 'class'=>'form-control', 'style'=>'', 'selected'=>json_decode($document['QcDocument']['user_id'],true))) . '</div>'; 
													echo "<div class='col-md-12'>".$this->Form->input('editors',array('name'=>'data[QcDocument][editors][]','label'=>'Who can edit this document?','multiple', 'options'=>$usernames, 'class'=>'form-control', 'required'=>'required',  'style'=>'','selected'=>json_decode($document['QcDocument']['editors'],true))) . '</div>'; 
												}else{
													echo "<div class='row'>";
													echo "<div class='col-md-10'>".$this->Form->input('share_with_branches',array('name'=>'data[QcDocument][share_with_branches][]', 'multiple','options'=>$branches, 'class'=>'form-control',)) . '</div>'; 
													echo "<div class='col-md-2'><br />".$this->Form->input('select_all_branches',array('type'=>'checkbox','class'=>'checkbox','onClick'=>'selectall("QcDocumentShareWithBranches",this)'))."</div>";
													echo "</div>";
													
													echo "<div class='row'>";
													echo "<div class='col-md-10'>".$this->Form->input('share_with_departments',array('name'=>'data[QcDocument][share_with_departments][]','multiple', 'options'=>$departments, 'class'=>'form-control',)) . '</div>'; 
													echo "<div class='col-md-2'><br />".$this->Form->input('select_all_departments',array('type'=>'checkbox','class'=>'checkbox','onClick'=>'selectall("QcDocumentShareWithDepartments",this)'))."</div>";
													echo "</div>";

													echo "<div class='row'>";
													echo "<div class='col-md-10'>".$this->Form->input('share_with_designations',array('name'=>'data[QcDocument][share_with_designations][]','multiple', 'options'=>$designations, 'class'=>'form-control chosen',)) . '</div>'; 
													echo "<div class='col-md-2'><br />".$this->Form->input('select_all_designations',array('type'=>'checkbox','class'=>'checkbox','onClick'=>'selectall("QcDocumentShareWithDesignations",this)'))."</div>";
													echo "</div>";

													echo "<div class='row'>";
													echo "<div class='col-md-10'>".$this->Form->input('user_id',array('name'=>'data[QcDocument][user_id][]','label'=>'Share with Users','multiple', 'options'=>$usernames, 'class'=>'form-control', 'style'=>'')) . '</div>'; 
													echo "<div class='col-md-2'><br />".$this->Form->input('select_all_users',array('type'=>'checkbox','class'=>'checkbox','onClick'=>'selectall("QcDocumentUserId",this)'))."</div>";
													echo "</div>";

													echo "<div class='row'>";
													echo "<div class='col-md-12'><hr /></div>";
													echo "<div class='col-md-12'>".$this->Form->input('editors',array('name'=>'data[QcDocument][editors][]','label'=>'Who can edit this document?','multiple', 'options'=>$usernames, 'class'=>'form-control', 'required'=>'required',  'style'=>'')) . '</div>'; 
													echo "<div class='hide'><br />".$this->Form->input('select_all_editors',array('type'=>'checkbox','class'=>'checkbox','onClick'=>'selectall("QcDocumentEditors",this)'))."</div>";
													echo "</div>";
												}
												?>

											</div>
										</div>
										<div class="box-footer">
											<p>Checking <strong>Select All</strong> will disable the field & exiting values and will override these values on save.</p>
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
												
												echo "<div class='col-md-6'><br /><div class='nomargin-checkbox'><label>Do you want this document to be shared with users for scheduled data enrty? If yes, click YES below. You must define schedule & data type.</label>".$this->Form->input('add_records',array('type'=>'checkbox','label'=>'Yes')) . '</div></div>'; 
												echo "<div class='col-md-3'>".$this->Form->input('schedule_id',array()) . '</div>'; 
												echo "<div class='col-md-3'>".$this->Form->input('data_type',array('required', 'options'=>$customArray['dataTypes'])) . '</div>'; 
											?>
											</div>
										</div>
									</div>
								</div>
							</div>
							<?php 		echo "<div class='row'>";
							echo "<div class='col-md-12'>".$this->Form->input('document_status',array('default'=>0,'type'=>'radio', 'class'=>'','options'=>$customArray['documentStatuses'])) . '</div>';
							echo "<div class='hide'>".$this->Form->hidden('template_id',array('class'=>'form-control', 'style'=>'')) . '</div>'; 

							echo "<div class='hide'>".$this->Form->hidden('user_session_id',array('class'=>'form-control', 'style'=>'')) . '</div>'; 
							echo "<div class='hide'>".$this->Form->hidden('cr_status',array('class'=>'form-control',)) . '</div>'; 
							echo "<div class='hide'>".$this->Form->hidden('mark_for_cr_update',array('class'=>'form-control',)) . '</div>'; 
							echo "<div class='hide'>".$this->Form->hidden('temp_date_of_issue',array('class'=>'form-control',)) . '</div>'; 
							echo "<div class='hide'>".$this->Form->hidden('temp_effective_from_date',array('class'=>'form-control',)) . '</div>'; 
							echo "<div class='hide'>".$this->Form->hidden('cr_id',array('class'=>'form-control', 'style'=>'')) . '</div>'; 
							echo "<div class='hide'>".$this->Form->hidden('old_cr_id',array('class'=>'form-control', 'style'=>'')) . '</div>'; 
							echo "<div class='hide'>".$this->Form->hidden('parent_id',array('class'=>'form-control', 'style'=>'')) . '</div>'; 
							// echo "<div class='col-md-4'>".$this->Form->hidden('parent_document_id',array('class'=>'form-control', 'style'=>'')) . '</div>'; 
							echo "<div class='col-md-12'>".$this->Form->input('linked_document_ids',array('label'=>'Linked Documents', 'name'=>'data[QcDocument][linked_document_id][]','class'=>'form-control','multiple','options'=>$parentQcDocuments)) . '</div>'; 
							
							
							?>
							<?php
							
							echo $this->Form->hidden('step',array('default'=>2));
							echo $this->Form->input('id');
							echo $this->Form->hidden('History.pre_post_values', array('value'=>json_encode($this->data)));
							echo $this->Form->input('branchid', array('type' => 'hidden', 'value' => $this->Session->read('User.branch_id')));
							echo $this->Form->input('departmentid', array('type' => 'hidden', 'value' => $this->Session->read('User.department_id')));
							// echo $this->Form->input('master_list_of_format_id', array('type' => 'hidden', 'value' => $documentDetails['MasterListOfFormat']['id']));
							?>

						</div>

						<div class="">
							<?php echo $this->element('approval_form',array('approval'=>$approval));?>
							<?php echo $this->element('approval_history',array('approval'=>$approval,'approvals'=>$approvals,'current_approval'=>$this->request->params['named']['approval_id'],'approvalComments',$approvalComments));?>
							<?php echo $this->Form->submit(__('Submit'), array('div' => false, 'class' => 'btn btn-primary btn-success','id'=>'submit_id')); ?>
							<?php echo $this->Html->image('indicator.gif', array('id' => 'submit-indicator','class'=>'hide')); ?>
							<?php echo $this->Form->end(); ?>

							<?php echo $this->Js->writeBuffer();?>
						</div>
					</div>
					<script> 

					</script>
				</div>
			</div>
			<script>

				function selectall(selecttype,chk){
					var c = chk.checked;
					if(c == true){
						$("#"+selecttype).prop('disabled',true).trigger('chosen:updated').prop('disabled',false)
					}else{
						$("#"+selecttype).prop('disabled',false).trigger('chosen:updated').prop('disabled',false)
					}
				}

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
				
				function loadtemplate(id){			
					$(".templates").css('background-color','#fff');
					$("#tmp_"+id).css('background-color','#c8e5fd');
					$("#QcDocumentTemplateId").val(id);
					$("#add_blank .box").removeClass(' box-danger').addClass(' box-default');
				}

				function add_title(name){
					
					str2 = name.replace(/[`~!@#$%^&*()|+\=?;:'",.<>\{\}\[\]\\]/gi,'');
					// str2 = str1.replace(' ','_');
					str3 = str2.replace(/\s/g,'_');
					str = str3.replace('/\//','/');
					$("#QcDocumentTitle").val(str);
				}
	    // check duplication
				function check_duplicates(field,value,id){
					if(field == "t"){
						name = "Title";

						var path = value;
						str1 = path.replace(/[`~!@#$%^&*()|+\=?;:'",.<>\{\}\[\]\\]/gi,'');
						// str2 = str1.replace(' ','_');
						str2 = str1.replace(/\s/g,' ');
						str = str2.replace('/\//','/');
					}else if(field == "n"){
						name = "Document Number";
						// clean_document_number(value);
						var path = value;
						str1 = path.replace(/[`~!@#$%^&*()|+\=?;:'",.<>\{\}\[\]\\]/gi,'');
						str2 = str1.replace(' ','_');
						str3 = str2.replace(/\s/g,'');
						str = str3.replace('/\//','/');
					}
					
					
					$("#"+id).val(str);    		
					$.ajax({
						url: "<?php echo Router::url('/', true); ?><?php echo $this->request->params['controller'] ?>/check_duplicates/field:"+field+"/value:"+$("#"+id).val(),
						success: function(data, result) {						    		
							if(data == 1){
								if(field == "t"){
									name = "Title";
								}else if(field == "n"){
									name = "Document Number";
							// clean_document_number(value);
								}

								$("#"+id).val('');
								$("#duplicate_errors").html(name + " already exists");
							}else{
								$("#duplicate_errors").html("");
						// clean_document_number(value);
							}
						},
					});	
				};

				function clean_document_number(value){
					const regex = /^[\w-]+$/;
					var check = value;
					if (check.search(regex) === -1){
						$("#QcDocumentDocumentNumber").val("");
					}else{
						$.ajax({
							url: "<?php echo Router::url('/', true); ?><?php echo $this->request->params['controller'] ?>/clean_document_number/clean_document_number:"+value,
							success: function(data, result) {						    		
								$("#QcDocumentDocumentNumber").val(data);
							},
						});	 
					}    		
				};

				$("#QcDocumentStandardId").on('change',function(){
					$.get("<?php echo Router::url('/', true); ?><?php echo $this->request->params['controller'];?>/get_child_select/parent_id:" + $("#QcDocumentStandardId").val() +"/model:Clause/field:standard_id", function(data) {
						$("#QcDocumentClauseId").html(data).trigger("chosen:updated");
						$("#QcDocumentAdditionalClauses").html(data).trigger("chosen:updated");
					});

					$.get("<?php echo Router::url('/', true); ?><?php echo $this->request->params['controller'];?>/get_child_select/parent_id:" + $("#QcDocumentStandardId").val() +"/model:QcDocumentCategory/field:standard_id", function(data) {
						$("#QcDocumentQcDocumentCategoryId").html(data).trigger("chosen:updated");
					});
				});

				

				$().ready(function() {	    	

	    			// $(".select").chosen();					
					$("#QcDocumentDateCreated").on('change',function(){
						var date = new Date($("#QcDocumentDateCreated").val());
						var newdate = new Date(date);


						// date of issue + 7 days
						newdate.setDate(newdate.getDate() + 7);
						var dd = newdate.getDate();				
						var mm = newdate.getMonth() + 1;
						var yyyy = newdate.getFullYear();

						if(dd < 10)dd = '0'+dd;
						if(mm < 10)mm = '0'+mm;

						var someFormattedDate = yyyy + '-' + mm + '-' + dd;
						$("#QcDocumentDateOfIssue").val(someFormattedDate);

						someFormattedDate = '';
						// effective from date + 14 days
						newdate.setDate(newdate.getDate() + 7);
						var dd = newdate.getDate();				
						var mm = newdate.getMonth() + 1;
						var yyyy = newdate.getFullYear();

						if(dd < 10)dd = '0'+dd;
						if(mm < 10)mm = '0'+mm;

						var someFormattedDate = yyyy + '-' + mm + '-' + dd;
						$("#QcDocumentEffectiveFromDate").val(someFormattedDate);

				// ----------------------
						
				// var date1 = new Date($("#QcDocumentDateCreated").val());
						var newdate1 = new Date(date);
						newdate1 = new Date(newdate1);

						var dd1 = newdate1.getDate();				
						var mm1 = newdate1.getMonth() + 1;
						var yyyy1 = newdate1.getFullYear() + 3;

						if(dd1 < 10)dd1 = '0'+dd1;
						if(mm1 < 10)mm1 = '0'+mm1;

						var someFormattedDate1 = yyyy1 + '-' + mm1 + '-' + dd1;
						$("#QcDocumentDateOfReview").val(someFormattedDate1);

				// ---------------------
						var date = new Date($("#QcDocumentDateCreated").val());
						var newdate2 = new Date(date);
						newdate2.setDate(newdate2.getDate() + 7);
				// newdate2 = new Date(newdate1);

						var dd2 = newdate2.getDate();				
						var mm2 = newdate2.getMonth() + 1;
						var yyyy2 = newdate2.getFullYear() + 3;

						if(dd2 < 10)dd2 = '0'+dd2;
						if(mm2 < 10)mm2 = '0'+mm2;

						var someFormattedDate2 = yyyy2 + '-' + mm2 + '-' + dd2;
						

						$("#QcDocumentRevisionDate").val(someFormattedDate2);

				// ------------

						newdate3 = new Date(someFormattedDate2);
						newdate3.setDate(newdate3.getDate() + 7);

						var dd3 = newdate3.getDate();				
						var mm3 = newdate3.getMonth() + 1;
						var yyyy3 = newdate3.getFullYear();

						if(dd3 < 10)dd3 = '0'+dd3;
						if(mm3 < 10)mm3 = '0'+mm3;

						var someFormattedDate3 = yyyy3 + '-' + mm3 + '-' + dd3;
						$("#QcDocumentDateOfNextIssue").val(someFormattedDate3);

					});

					$("#filetypediv").hide();

					$("select").chosen();
					
					$("#QcDocumentShareWithBranches").chosen();
					$("#QcDocumentShareWithDepartments").chosen();
					$("#QcDocumentShareWithDesignations").chosen();
					$("#QcDocumentUserId").chosen();
					$("#QcDocumentLinkedDocumentIds").chosen();

					jQuery.validator.addMethod("greaterThanZero", function(value, element) {
						return this.optional(element) || (parseFloat(value) != -1);
					}, "Please select the value");

					$('#QcDocumentAddForm').validate({        	
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
							"data[QcDocument][prepared_by]": {
								greaterThanZero: true,
							},
							"data[QcDocument][data_type]": {
								greaterThanZero: true,
							} 
						}
					}); 
					
					$("#submit-indicator").hide();
					$("#submit_id").click(function(e){	   
						
						if($("#upload").hasClass('tab-pane active') == true){
							$("#QcDocumentTemplateId").val('');
							if($("#QcDocumentFileType").val() == ''){
								$("#upload .box").removeClass(' box-default').addClass(' box-danger');
								$("#add_blank .box").removeClass(' box-danger').addClass(' box-default');
								$(".templates").css('background-color','#fff');
								$("#filetypediv").show();
								return false;
							}else{
								$("#upload .box").removeClass(' box-danger').addClass(' box-default');
								$("#filetypediv").hide();
								return true;
							}
							if($('#QcDocumentAddForm').valid()){
								$("#submit_id").prop("disabled",true);
								$("#submit-indicator").show();
								$('#QcDocumentAddForm').submit();
							}
						}else if($("#add_blank").hasClass('tab-pane active') == true){
							if($("#QcDocumentTemplateId").val() == ''){
								$("#upload .box").removeClass(' box-danger').addClass(' box-default');
								$("#add_blank .box").removeClass(' box-default').addClass(' box-danger');
								$("#filetypediv").show();
								return false;
							}else{
								$("#add_blank .box").removeClass(' box-danger').addClass(' box-default');
								$("#filetypediv").hide();
								return true;
							}
							if($('#QcDocumentAddForm').valid()){
								$("#submit_id").prop("disabled",true);
								$("#submit-indicator").show();
								$('#QcDocumentAddForm').submit();
							}
						}else{
							if($('#QcDocumentAddForm').valid()){
								$("#submit_id").prop("disabled",true);
								$("#submit-indicator").show();
								$('#QcDocumentAddForm').submit();
							}
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
					$('#QcDocumentPreparedBy').change(function() {
						if ($(this).val() != -1 && $(this).next().next('label').hasClass("error")) {
							$(this).next().next('label').remove();
						}
					});	

					$('#QcDocumentDataType').change(function() {
						if ($(this).val() != -1 && $(this).next().next('label').hasClass("error")) {
							$(this).next().next('label').remove();
						}
					});			
					
				});
			</script>

