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
			
			<div class="row">
				<?php				
				if($this->data['QcDocument']['name']){
					echo "<div class='col-md-6'>".$this->Form->input('name',
						array(
							'label'=>'<strong>Name:</strong> Do not add document extension (doc,docx,elxs etc) in this field',
							'class'=>'form-control')) . '</div>'; 	
				}
				if($this->data['QcDocument']['document_number']){
					echo "<div class='col-md-3'>".$this->Form->input('document_number',
						array(
							'label'=>'<strong>Document Numner:</strong>',
							'class'=>'form-control')) . '</div>'; 	
				}
				if(isset($this->data['QcDocument']['revision_number'])){
					echo "<div class='col-md-3'>".$this->Form->input('revision_number',
						array(
							'label'=>'<strong>Revision Numner:</strong>',
							'class'=>'form-control')) . '</div>'; 	
				}

				echo "</div><div class='row'>";
				
				echo $this->Form->hidden('file_key',array());				
				echo "<br /><br /></div>";
				?>				
				
				</div></div>
				
				<?php
				echo $this->Form->input('id');
				echo $this->Form->hidden('History.pre_post_values', array('value'=>json_encode($this->data)));
				echo $this->Form->input('branchid', array('type' => 'hidden', 'value' => $this->Session->read('User.branch_id')));
				echo $this->Form->input('departmentid', array('type' => 'hidden', 'value' => $this->Session->read('User.department_id')));
				echo $this->Form->input('master_list_of_format_id', array('type' => 'hidden', 'value' => $documentDetails['MasterListOfFormat']['id']));
				?>

			</div>
			<div class="">
				<?php echo $this->Form->submit(__('Submit'), array('div' => false, 'class' => 'btn btn-primary btn-success','id'=>'submit_id')); ?>
				<?php echo $this->Html->image('indicator.gif', array('id' => 'submit-indicator')); ?>
				<?php echo $this->Form->end(); ?>

				<?php echo $this->Js->writeBuffer();?>
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

