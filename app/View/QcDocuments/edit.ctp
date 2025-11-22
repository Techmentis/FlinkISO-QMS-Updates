<?php echo $this->Html->script(array('jquery.validate.min', 'jquery-form.min')); ?>
<?php echo $this->fetch('script'); ?><div id="qcDocuments_ajax">
	<?php echo $this->Session->flash();?>	
	<?php echo $this->element('nav-header-lists',array('postData'=>array('pluralHumanName'=>'Edit Document','modelClass'=>'QcDocument','options'=>array("sr_no"=>"Sr No","document_number"=>"Document Number","reference_number"=>"Reference Number","issue_number"=>"Issue Number","date_of_next_issue"=>"Date Of Next Issue","date_of_issue"=>"Date Of Issue","effective_from_date"=>"Effective From Date","revision_number"=>"Revision Number","date_of_review"=>"Date Of Review","revision_date"=>"Revision Date","document_type"=>"Document Type","it_categories"=>"It Categories","document_status"=>"Document Status","issued_by"=>"Issued By","archived"=>"Archived","change_history"=>"Change History","cr_status"=>"Cr Status","mark_for_cr_update"=>"Mark For Cr Update","temp_date_of_issue"=>"Temp Date Of Issue","temp_effective_from_date"=>"Temp Effective From Date","linked_formats"=>"Linked Formats","cover_page"=>"Cover Page","page_orientation"=>"Page Orientation"),'pluralVar'=>'qcDocuments'))); 
	?>

	<div class="nav panel panel-default">

		<div class="qcDocuments form col-md-12">
			<?php echo $this->Form->create('QcDocument',array('role'=>'form','class'=>'form')); ?>
			<div class="row">
				<div class="col-md-6"><h3><small>Document Name:</small><br /><?php echo $this->data['QcDocument']['name'] ?></h3></div>
				<div class="col-md-3"><h3><small>Number:</small><br /><?php echo $this->data['QcDocument']['document_number'] ?></h3></div>
				<div class="col-md-3"><h3><small>Revision:</small><br /><?php echo $this->data['QcDocument']['revision_number'] ?></h3></div>

			</div>
			<div class="row onlyofficedivshow">
				<div class="col-md-12">
					<?php 
					$key = $this->data['QcDocument']['file_key'];
					$file_type = $this->data['QcDocument']['file_type'];
					$file_name = $this->data['QcDocument']['title'];
					$document_number = $this->data['QcDocument']['document_number'];
					$document_version = $this->data['QcDocument']['revision_number'];

					$file_type = $this->data['QcDocument']['file_type'];

					if($file_type == 'doc' || $file_type == 'docx'){
						$documentType = 'word';
					}

					if($file_type == 'xls' || $file_type == 'xlsx'){
						$documentType = 'cell';
					}
					if($file_type == 'pdf'){
						$documentType = 'pdf';
					}

					if($file_type == 'pptx' || $file_type == 'ppt'){
						$documentType = 'presentation';
					}


					$editors = json_decode($this->request->data['QcDocument']['editors'],true);
					if($editors){
						if(in_array($this->Session->read('User.id'), $editors)){
							$mode = 'edit';	
						}else{
							$mode = 'view';
						}	
					}




					$file_path = $this->data['QcDocument']['id'];

					$file = $document_number.'-'.$file_name.'-'.$document_version;
					$file = ltrim(rtrim($file));
					$file = str_replace('-', '_', $file);
					$file = ltrim(rtrim(strtolower($file)));
					$file = preg_replace('/[\@\.\;\" "-]+/', '_', $file);
					$file = preg_replace('/  */', '_', $file);
					$file = preg_replace('/\\s+/', '_', $file);        
					$file = preg_replace('/-*-/', '_', $file);
					$file = preg_replace('/_*_/', '_', $file);
					$file = $this->requestAction(array('action'=>'clean_table_names',$file));
					$file = $file.'.'.$file_type;

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
						'record_id'=>$this->data['QcDocument']['id'],
						'company_id'=>$this->Session->read('User.company_id'),
						'controller'=>$this->request->controller,
						'last_saved' => $this->data['QcDocument']['last_saved'],
						'last_modified' => $this->data['QcDocument']['modified'],
						'version_keys' => $this->data['QcDocument']['version_keys'],
						'version' => $this->data['QcDocument']['version'],
						'versions' => $this->data['QcDocument']['versions'],
						'docid'=> $this->data['QcDocument']['id']
					));
					?>
				</div>
				<div class="col-md-12">
					<?php echo $this->element('dmtips');?>
				</div>
			</div>
			<div class="row">
				<?php				
				echo "<div class='col-md-12'>".$this->Form->input('parent_document_id',array('class'=>'form-control', 'style'=>'')) . '</div>'; 

				if($this->data['QcDocument']['name']){
					echo "<div class='col-md-12'>".$this->Form->input('name',
						array(
							'label'=>'<strong>Name:</strong> Do not add document extension (doc,docx,elxs etc) in this field',
							'class'=>'form-control', 'readonly'=>'readonly')) . '</div>'; 	
				}else{
					echo "<div class='col-md-12'>".$this->Form->input('name',
						array(
							'label'=>'<strong>Name:</strong> Do not add document extension (doc,docx,elxs etc) in this field',
							'class'=>'form-control', 'value'=>$this->data['QcDocument']['title'])) . '</div>'; 	
				}
				
				echo "<div class='col-md-6 hide'>".$this->Form->hidden('title',array('class'=>'form-control', 'style'=>'')) . '</div>'; 
				echo "<div class='col-md-3 hide'>".$this->Form->hidden('document_number',array('class'=>'form-control','label'=>'Document Number')) . '</div>'; 
				echo "<div class='col-md-3 hide'>".$this->Form->hidden('revision_number',array('class'=>'form-control','label'=>'Revision/Version')) . '</div>'; 
				
				echo "</div><div class='row'>";
				
				echo "<div class='col-md-4'>".$this->Form->input('date_created',array('class'=>'form-control',)) . '</div>'; 
				echo "<div class='col-md-4'>".$this->Form->input('date_of_issue',array('class'=>'form-control',)) . '</div>'; 
				echo "<div class='col-md-4'>".$this->Form->input('effective_from_date',array('class'=>'form-control',)) . '</div>'; 		

				echo "</div><div class='row'>";

				echo "<div class='col-md-4'>".$this->Form->input('date_of_review',array('class'=>'form-control',)) . '</div>'; 
				echo "<div class='col-md-4'>".$this->Form->input('revision_date',array('class'=>'form-control',)) . '</div>'; 
				echo "<div class='col-md-4'>".$this->Form->input('date_of_next_issue',array('class'=>'form-control',)) . '</div>'; 
				
				echo "</div><div class='row'>";

				echo "<div class='col-md-4'>".$this->Form->input('standard_id',array('class'=>'form-control', 'style'=>'','required')) . '</div>'; 
				echo "<div class='col-md-4'>".$this->Form->input('clause_id',array('class'=>'form-control', 'style'=>'','required')) . '</div>'; 		
				echo "<div class='col-md-4'>".$this->Form->input('qc_document_category_id',array('class'=>'form-control', 'style'=>'','required')) . '</div>'; 

				echo "<div class='col-md-12'>".$this->Form->input('additional_clauses',array('name'=>'data[QcDocument][additional_clauses][]', 'class'=>'form-control', 'multiple', 'options'=>$clauses, 'style'=>'','selected'=>json_decode($this->request->data['QcDocument']['additional_clauses']))) . '</div>'; 
				
				echo "</div><div class='row'>";
				
				echo "<div class='col-md-12'>".$this->Form->input('document_type',array('default'=>0, 'type'=>'radio', 'class'=>'','options'=>$customArray['documentTypes'])) . '</div>'; 
				echo "<div class='col-md-12'>".$this->Form->input('it_categories',array('legend'=>'Document Security','type'=>'radio', 'class'=>'','options'=>$customArray['itCategories'])) . '</div>';
				
				$updateCustomTableDocuments = array(
					0=>'Yes, Update Custom HTML Form Document when any changes are made to this document',
					1=>'No, Custom HTML Form Document will be updated manually',
				);
				echo "<div class='col-md-12 hide'>".$this->Form->input('update_custom_table_document',array('legend'=>'Update Custom HTML From Document','type'=>'radio', 'class'=>'','options'=>$updateCustomTableDocuments)) . '</div>'; 

				echo "</div><div class='row'>";
				
				echo "<div class='col-md-4'>".$this->Form->input('prepared_by',array('class'=>'form-control',)) . '</div>'; 
				echo "<div class='col-md-4'>".$this->Form->input('issued_by',array('class'=>'form-control',)) . '</div>'; 
				echo "<div class='col-md-4'>".$this->Form->input('issuing_authority_id',array('class'=>'form-control', 'style'=>'')) . '</div>'; 		
				
				
				echo "</div><div class='row'>";

				echo "<div class='col-md-12'>".$this->Form->input('change_history',array('class'=>'form-control',)) . '</div>'; 
				echo $this->Form->hidden('file_key',array());
				
				echo "</div>";
				?>				
				<div class='row'>
					<div class='col-md-12'>
						<div class="box box-default">
							<div class="box-header with-border">
								<i class="fa fa-share-alt"></i>
								<h3 class="box-title">Document Sharing  
								</h3>
									<br />
									<p><?php echo $this->Form->input('and_or_condition',array('label'=>'Check for strict sharing','class'=>'checkbox'));?> </p>									
									<small>If strict shareing is checked, only users beloging to selected Branches/ Departments/ Designations will be able to view the document. It not checked, then users belonging to either of any will be able to view the documents.</small>
							</div>
							<div class="box-body">
								<div class="">
									<?php	
									echo "<div class='row'>";
									echo "<div class='col-md-10'>".$this->Form->input('share_with_branches',array('name'=>'data[QcDocument][share_with_branches][]', 'multiple','options'=>$branches, 'class'=>'form-control','selected'=>json_decode($this->request->data['QcDocument']['branches']))) . '</div>'; 
									echo "<div class='col-md-2'><br />".$this->Form->input('select_all_branches',array('type'=>'checkbox','class'=>'checkbox','onClick'=>'selectall("QcDocumentShareWithBranches",this)'))."</div>";
									echo "</div>";
									
									echo "<div class='row'>";
									echo "<div class='col-md-10'>".$this->Form->input('share_with_departments',array('name'=>'data[QcDocument][share_with_departments][]','multiple', 'options'=>$departments, 'class'=>'form-control','selected'=>json_decode($this->request->data['QcDocument']['departments']))) . '</div>'; 
									echo "<div class='col-md-2'><br />".$this->Form->input('select_all_departments',array('type'=>'checkbox','class'=>'checkbox','onClick'=>'selectall("QcDocumentShareWithDepartments",this)'))."</div>";
									echo "</div>";

									echo "<div class='row'>";
									echo "<div class='col-md-10'>".$this->Form->input('share_with_designations',array('name'=>'data[QcDocument][share_with_designations][]','multiple', 'options'=>$designations, 'class'=>'form-control chosen','selected'=>json_decode($this->request->data['QcDocument']['designations']))) . '</div>'; 
									echo "<div class='col-md-2'><br />".$this->Form->input('select_all_designations',array('type'=>'checkbox','class'=>'checkbox','onClick'=>'selectall("QcDocumentShareWithDesignations",this)'))."</div>";
									echo "</div>";
									
									echo "<div class='row'>";
									echo "<div class='col-md-10'>".$this->Form->input('user_id',array('name'=>'data[QcDocument][user_id][]','label'=>'Share with Users','multiple', 'options'=>$usernames, 'class'=>'form-control', 'style'=>'','selected'=>json_decode($this->request->data['QcDocument']['user_id'],true))) . '</div>'; 
									echo "<div class='col-md-2'><br />".$this->Form->input('select_all_users',array('type'=>'checkbox','class'=>'checkbox','onClick'=>'selectall("QcDocumentUserId",this)'))."</div>";
									echo "</div>";

									echo "<div class='row'>";
									echo "<div class='col-md-12'><hr /></div>";
									echo "<div class='col-md-12'>".$this->Form->input('editors',array('name'=>'data[QcDocument][editors][]','label'=>'Who can edit this document?','multiple', 'options'=>$usernames, 'class'=>'form-control', 'required',  'style'=>'','selected'=>json_decode($this->request->data['QcDocument']['editors']))) . '</div>'; 
									echo "<div class='hide'><br />".$this->Form->input('select_all_editors',array('type'=>'checkbox','class'=>'checkbox','onClick'=>'selectall("QcDocumentEditors",this)'))."</div>";
									echo "</div>";

									?>

								</div>
							</div>
							<div class="box-footer">
								<p>Checking <strong>Select All</strong> will disable the field & exiting values and will override these values on save.</p>
							</div>
						</div>
					</div>
				</div>				
				<?php 		echo "<div class='row'>";
				unset($customArray['documentStatuses'][3]);
				unset($customArray['documentStatuses'][6]);
				echo "<div class='col-md-6'>".$this->Form->input('document_status',array('default'=>0,'type'=>'radio', 'class'=>'','options'=>$customArray['documentStatuses'])) . '</div>';
				echo "<div class='col-md-3'><br />" . $this->Form->input('allow_download',array('type'=>'checkbox','class'=>'checkbox')) ."</div>";
				echo "<div class='col-md-3'><br />" . $this->Form->input('allow_print',array('type'=>'checkbox','class'=>'checkbox')) ."</div>";

				echo "<div class='hide'>".$this->Form->hidden('user_session_id',array('class'=>'form-control', 'style'=>'')) . '</div>'; 
				echo "<div class='hide'>".$this->Form->hidden('cr_status',array('class'=>'form-control',)) . '</div>'; 
				echo "<div class='hide'>".$this->Form->hidden('mark_for_cr_update',array('class'=>'form-control',)) . '</div>'; 
				echo "<div class='hide'>".$this->Form->hidden('temp_date_of_issue',array('class'=>'form-control',)) . '</div>'; 
				echo "<div class='hide'>".$this->Form->hidden('temp_effective_from_date',array('class'=>'form-control',)) . '</div>'; 
				echo "<div class='hide'>".$this->Form->hidden('cr_id',array('class'=>'form-control', 'style'=>'')) . '</div>'; 
				echo "<div class='hide'>".$this->Form->hidden('old_cr_i`d',array('class'=>'form-control', 'style'=>'')) . '</div>'; 
				echo "<div class='hide'>".$this->Form->hidden('parent_id',array('class'=>'form-control', 'style'=>'')) . '</div>'; 
				// echo "<div class='hide'>".$this->Form->hidden('parent_document_id',array('class'=>'form-control', 'style'=>'')) . '</div>'; 
				echo "<div class='col-md-12'>".$this->Form->input('linked_document_ids',array('label'=>'Linked Documents', 'name'=>'data[QcDocument][linked_document_id][]','class'=>'form-control','multiple','options'=>$parentQcDocuments)) . '</div>'; 
				
				
				?>
				<?php
				echo $this->Form->input('id');
				echo $this->Form->hidden('History.pre_post_values', array('value'=>json_encode($this->data)));
				echo $this->Form->input('branchid', array('type' => 'hidden', 'value' => $this->Session->read('User.branch_id')));
				echo $this->Form->input('departmentid', array('type' => 'hidden', 'value' => $this->Session->read('User.department_id')));
				echo $this->Form->input('master_list_of_format_id', array('type' => 'hidden', 'value' => $documentDetails['MasterListOfFormat']['id']));
				?>

			</div>
			<div class="row hide">
				<div class="col-md-12">
					<?php echo $this->Form->input('update_version',array('options'=>array(0=>'No',1=>'Yes'),'type'=>'radio','class'=>0,'default'=>0));?>
				</div>
				<div class="col-md-12">
					<p><strong>Note:</strong> By default, update version in set to NO. If you want to update the version, click yes. It will create a copy of this document and store it as a older version. This may affect your billing.</p>		
				</div>
			</div>
			<div class="">
				<?php
				if ($showApprovals && $showApprovals['show_panel'] == true) {		
					if($this->request->params['named']['approval_id'])echo $this->element('approval_form',array('approval'=>$approval));
					else echo $this->element('approval_form');
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
			if(element['context']['className'] == 'form-control select error'){
				$(element).next('.chosen-container').addClass('error');
			}else if(element.attr("fieldset") != ''){						
				$(element).parent('fieldset').addClass('error-radio');
			}else{			
				$(element).after(error); 
			}
		},
	});

	function selectall(selecttype,chk){
		var c = chk.checked;
		if(c == true){
			$("#"+selecttype).prop('disabled',true).trigger('chosen:updated').prop('disabled',false)
		}else{
			$("#"+selecttype).prop('disabled',false).trigger('chosen:updated').prop('disabled',false)
		}
	}

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

		$("select").chosen();

		jQuery.validator.addMethod("greaterThanZero", function(value, element) {
			return this.optional(element) || (parseFloat(value) != -1);
		}, "Please select the value");

		$('#QcDocumentEditForm').validate({}); 

		$('select').each(function() {	
			if($(this).prop('required') == true){
				$(this).rules('add', {
					greaterThanZero: true
				});	
			}
			
		}); 
		
		$("#submit-indicator").hide();
		$("#submit_id").click(function(){
			if($('#QcDocumentEditForm').valid()){
				$("#submit_id").prop("disabled",true);
				$("#submit-indicator").show();
				$('#QcDocumentEditForm').submit();
			}

		});

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

		$("#ApprovalQcDocumentPublish").on('change',function(){
			
			if(this.checked == true){    			
				$("#QcDocumentDocumentStatus1").attr('checked',true);
			}else{
				$("[name='data[QcDocument][document_status]']:checked").attr('checked', false);;
			}
		});    	
	});
</script>

