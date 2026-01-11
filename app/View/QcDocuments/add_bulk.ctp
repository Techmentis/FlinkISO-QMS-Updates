<?php echo $this->Html->script(array('jquery.validate.min', 'jquery-form.min')); ?>
<?php echo $this->fetch('script'); ?>

<?php
$docarray = array('doc','docx');
$sheetarray = array('xls','xlsx');
$pdfarray = array('pdf');
$pptarray = array('ppt','pptx');


$fields = array('title','document_number','revision_number','date_created','date_of_issue','effective_from_date','document_type','prepared_by','approved_by');
$values = array('Audit Schedule','Audit Checklist','Audit Findings','MRM','Customer Details','Customer Complaint','Suppliers','Device','Calibrations','Corrective Actions');
?>
<style type="text/css">
	.childdoc, .childdoc a{color: #5c5c5c;}
	.chosen-container, .chosen-container-single, .chosen-select{min-width: 50px}
</style>
<?php echo $this->element('checkbox-script'); ?><div  id="main">
	<?php echo $this->Session->flash();?>	
	<div class="qcDocuments ">
		<?php echo $this->element('nav-header-lists',array('postData'=>array('pluralHumanName'=>'Qc Documents','modelClass'=>'QcDocument','options'=>array("sr_no"=>"Sr No","document_number"=>"Document Number","reference_number"=>"Reference Number","issue_number"=>"Issue Number","date_of_next_issue"=>"Date Of Next Issue","date_of_issue"=>"Date Of Issue","effective_from_date"=>"Effective From Date","revision_number"=>"Revision Number","date_of_review"=>"Date Of Review","revision_date"=>"Revision Date","document_type"=>"Document Type","it_categories"=>"It Categories","document_status"=>"Document Status","issued_by"=>"Issued By","archived"=>"Archived","change_history"=>"Change History","cr_status"=>"Cr Status","mark_for_cr_update"=>"Mark For Cr Update","temp_date_of_issue"=>"Temp Date Of Issue","temp_effective_from_date"=>"Temp Effective From Date","linked_formats"=>"Linked Formats","cover_page"=>"Cover Page","page_orientation"=>"Page Orientation"),'pluralVar'=>'qcDocuments'))); ?>

		
		<div class="table-responsive" style="overflow:scroll">
			<div class="row">
				<div class="col-md-12">
					<h4>Add Initial Documents & Data.</h4>
					<p>Forms for following documents are available to download through our APIs. <strong>You can use this option to add basic QMS documents to understand how system works.</strong>. When you click Submit, system will add all these documents and will copy a blank ".DOCX" files against each document. You can edit these documents to add your content.To edit, goto each document and click edit icon. To add HTML Forms, goto each document's view page and click on Database icon at the bottom. It will take you to the HTML Form Builder. Based on the document, select the HTML Form and click download and save the form. System will then create HTML form for that document. Repeat this step for each document.</p>
					<p class="text-warning">You can change document name, number, version from here. Once added these fields can not be changed. You can change all other fields from Edit page of the document. If you wish to continue with these documents, add existing name, number and version and proceed.</p>
				</div>
			</div>
			<?php echo $this->Form->create('QcDocument',array('role'=>'form','class'=>'form','type'=>'file')); ?>
			<div class="row">
				<div class="col-md-4"><?php echo $this->Form->input('standard_id',array('class'=>'form-control','required'=>'required','default'=>'58511238-fba8-4db9-aad0-833fc20b8995'));?></div>
				<div class="col-md-4"><?php echo $this->Form->input('clause_id',array('class'=>'form-control','required'=>'required','default'=>'33457b87-110e-4bba-bf46-24f34952d44b'));?></div>
				<div class="col-md-4"><?php echo $this->Form->input('qc_document_category_id',array('class'=>'form-control','required'=>'required','default'=>'584dbb5d-f880-44a3-8b0d-2e7ec20b8995'));?></div>
				<div class="col-md-12"><hr /></div>				
			</div>
			<table cellpadding="0" cellspacing="0" class="table table-responsive table-hover index">
				<tr>
					<th width="200">Document Name</th>
					<th>Number</th>
					<th>Version</th>
					<th>Date Created</th>
					<th>Date Of Issue</th>
					<th>Effective From Date</th>
					
					<th>Document Type</th>
					<th>Prepared By</th>
					<th>Approved By</th>
				</tr>
				
				<?php 
				echo "<tr>";
				$documents = array(
					0=>'QMS Manual',
					1=>'Audit Schedule',
					2=>'Audit Checklist',
					3=>'Audit Findings',
					4=>'MRM',
					5=>'Customer Details',
					6=>'Customer Complaints',
					7=>'Supplier Details',
					8=>'Device/ Equipment',
					9=>'Calibration',
					
				);
				for($x = 0; $x <= 9; $x++){
					foreach($fields as $field){
						
						if($field == 'title')$default =  $documents[$x];
						else if($field == 'document_number')$default = str_pad($x+1 ,3, '0',STR_PAD_LEFT);
						else if($field == 'revision_number')$default = 0;
						else if($field == 'date_created')$default = date('Y-m-d');
						else if($field == 'date_of_issue')$default = date('Y-m-d');
						else if($field == 'effective_from_date')$default = date('Y-m-d');
						else if($field == 'prepared_by')$default = $this->Session->read('User.employee_id');
						else if($field == 'approved_by')$default = $this->Session->read('User.employee_id');
						else if($field == 'document_type')$default = 6;
						else $default = '';							

						if($field != 'document_number' && $field != 'revision_number' && $field != 'title')$readonly = '';
						else $readonly = '';
						
						echo "<td>" . $this->Form->input('Docs.'.$x.'.QcDocument.'.$field,array('class'=>'form-control', $readonly , 'label'=>false,'div'=>false,'required'=>'required','value'=>$default)) . "</td>";
						$default = '';
					}
					echo "</tr>";
				}?>
				
			</table>
		</div>
		<div class="row">
			<div class="col-md-12">
				<h5>Note:</h5>
				<p>After you add these document, make sure to update "Audit" & "Audit Findings" as child documents to "Audit Schedule". To do this, goto each document and select "Audit Schedule" as a parent document from the drop down.</p>
				<p>After your initial testing, delete all these documents and HTML Tables, and add your actual QMS documents with correct name,number etc and create new custom HTML tables.</p>
			</div>
		</div>
		<div class="">
			<?php echo $this->Form->submit(__('Submit'), array('div' => false, 'class' => 'btn btn-primary btn-success','id'=>'submit_id')); ?>
			<?php echo $this->Html->image('indicator.gif', array('id' => 'submit-indicator','class'=>'hide')); ?>
			<?php echo $this->Form->end(); ?>
		</div>
	</div>
</div>
</div>
<script type="text/javascript">
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

	$().ready(function(){

		jQuery.validator.addMethod("greaterThanZero", function(value, element) {
			return this.optional(element) || (parseFloat(value) != -1);
		}, "Please select the value");


		$('#QcDocumentAddBulkForm').validate(); 

		$('select').each(function() {	
			if($(this).prop('required') == true){
				$(this).rules('add', {
		        	greaterThanZero: true
		    	});	
			}
		});  
		
	});
</script>
<script>$.ajaxSetup({beforeSend:function(){$("#busy-indicator").show();},complete:function(){$("#busy-indicator").hide();}});</script>
<script type="text/javascript">	$().ready(function(){$(".tooltip1").tooltip();});</script>
