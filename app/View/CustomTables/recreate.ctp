<?php echo $this->Session->flash();?>	
<?php echo $this->Html->script(array('jquery.validate.min', 'jquery-form.min')); ?>
<?php echo $this->fetch('script'); ?>

<?php echo $this->Html->css(array('dragdrop')); ?>
<?php echo $this->fetch('css'); ?>

<style type="text/css">
	#dropHere {
		padding: 0px;
		width:100%;
		height: 736px;
		border: 1px solid #dddddd;
		margin: 10px 0;
		border-radius: 2px;
		background-image: url('<?php echo Router::url('/', true); ?>img/vline.svg'); 
			background-repeat: repeat-y;
			background-size: 100% 100%;
		}
	</style>

	<div id="customTables_ajax">
		
		<div class="customTables ">
			<?php echo $this->element('nav-header-lists',array('postData'=>array('pluralHumanName'=>'Custom Tables','modelClass'=>'CustomTable','options'=>array(),'pluralVar'=>'customTables'))); ?>

			<div class="nav panel panel-default">
				<div class="customTables form col-md-12">
					<?php echo $this->Form->create('CustomTable',array('role'=>'form','class'=>'form')); ?>
					<div class="row">
						<?php 
						if($this->request->params['named']['qc_document_id'])
							echo "<div class='col-md-12'>".$this->element('qc_doc_header',array('document'=>$qcDocument))."</div>"; 
						else if($this->request->params['named']['process_id'])
							echo "<div class='col-md-12'>".$this->element('process_doc_header',array('document'=>$process))."</div>"; 
						?>	
						
						<?php echo "<div class='col-md-4'>".$this->Form->input('name',array('class'=>'form-control','required')) . '</div>'; ?>
						<?php echo "<div class='col-md-3'>".$this->Form->input('table_name',array('class'=>'form-control', 'readonly', 'default'=>$table_name)) . '</div>'; ?>
						<?php echo "<div class='col-md-1'>".$this->Form->input('table_version',array('label'=>'Version', 'class'=>'form-control', 'readonly', 'default'=>$table_version)) . '</div>'; ?>
						
						<?php unset($this->request->data['CustomTable']['password']);?>
						<?php echo "<div class='col-md-2'>".$this->Form->input('password',array('type'=>'password', 'class'=>'form-control','default'=>false)) . '</div>'; ?>
						<?php echo "<div class='col-md-2'>".$this->Form->input('re-password',array('type'=>'password', 'class'=>'form-control','default'=>false)) . '</div>'; ?>

						<?php echo "<div class='hide'>".$this->Form->hidden('fields',array('class'=>'form-control',)) . '</div>'; ?>
						
						
						<?php echo "<div class='col-md-12'>".$this->Form->input('description',array('class'=>'form-control',)) . '</div>'; ?>	
						
						<?php 
						if(!empty($this->request->data['CustomTable']['qc_document_id'])){
							echo "<div class='col-md-12 hide'>".$this->Form->hidden('qc_document_id',array('class'=>'form-control',)) . '</div>';					
						}
						if(!empty($this->request->data['CustomTable']['process_id'])){
							echo "<div class='col-md-12 hide'>".$this->Form->hidden('process_id',array('class'=>'form-control')) . '</div>';
						}
						?>	
					</div>
					<?php 
					if(!empty($qcDocument['ParentDocument']['title'])){
						echo "<div class='row'><div class='col-md-12'><p><strong>Records in this table will be automatically linked with parent_document_id : ".$qcDocument['ParentDocument']['title'].". Users can add/update these records from ".$qcDocument['ParentDocument']['title']."'s View page only.</strong></p></div>";
						?>
						<?php 
						echo "<div class='col-md-6'>".$this->Form->input('field_name',array('class'=>'form-control',)) . '</div>'; 
						echo "<div class='col-md-6' id='get_data'>".$this->Form->input('field_value',array('class'=>'form-control','options'=>array())) . '</div>';
						?>
						<div class="col-md-12"><p><br />Select field name (from parent table) and field value against which you would like to load this form. This form will be loaded for Adding a record on this value.</p></div>
					</div>
				<?php } ?>	

				<div class="row">
					<?php if($qcDocument)echo "<div class='col-md-12'>".$this->element('qc_doc_header',array('document'=>$qcDocument))."</div>"; ?>
					<?php if($process)echo "<div class='col-md-12'>".$this->element('process_doc_header',array('process'=>$process))."</div>"; ?>
				</div>
				<?php if($qcDocument){ ?>
					<div class="row">
						<div class="col-md-12">
							<?php	
							$key = $key;
							$file_type = $qcDocument['QcDocument']['file_type'];
							$file_name = $qcDocument['QcDocument']['title'];
							$document_number = $qcDocument['QcDocument']['document_number'];
							$document_version = $qcDocument['QcDocument']['revision_number'];

							$file_type = $qcDocument['QcDocument']['file_type'];
							
							if($file_type == 'doc' || $file_type == 'docx'){
								$documentType = 'word';
							}

							if($file_type == 'xls' || $file_type == 'xlsx'){
								$documentType = 'cell';
							}

							$mode = 'edit';

							$file_path = $customTable['CustomTable']['id'];


	        				// $file = $document_number.'-'.$file_name.'-'.$document_version.'.'.$file_type;
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
								'preparedby'=>$this->Session->read('User.name'),
								'filekey'=>$filekey,
								'record_id'=>$customTable['CustomTable']['id'],
								'company_id'=>$this->Session->read('User.company_id'),
								'controller'=>'custom_tables',
								'last_saved'=>$qcDocument['QcDocument']['last_saved'],
								'last_modified' => $qcDocument['QcDocument']['modified'],
								'version_keys' => $qcDocument['QcDocument']['version_keys'],
								'version' => $this->data['QcDocument']['version'],
								'versions' => $this->data['QcDocument']['versions'],
								'docid'=> $qcDocument['QcDocument']['id']
							));
							?>
						</div>
					</div>
				<?php } ?>
				<?php if($process){ ?>
					<div class="row">
						<div class="col-md-12">
							<?php 
							$key = $process['Process']['file_key'];
							$file_type = $process['Process']['file_type'];
							$file_name = $process['Process']['name'];
					        // $document_number = $process['QcDocument']['document_number'];
					        // $document_version = $process['QcDocument']['revision_number'];

							$file_type = $process['Process']['file_type'];
							
							if($file_type == 'doc' || $file_type == 'docx'){
								$documentType = 'word';
							}

							if($file_type == 'xls' || $file_type == 'xlsx'){
								$documentType = 'cell';
							}

							$mode = 'edit';

							$file_path = $process['Process']['id'];


							$file = $file_name.'.'.$file_type;

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
								'record_id'=>$process['Process']['id'],
								'company_id'=>$this->Session->read('User.company_id'),
								'controller'=>$this->request->controller,
							));
							?>
						</div>
					</div>
				<?php } ?>
				<div class="">
					<div class="col-md-12">

						<?php			
						$existingFields = json_decode($this->request->data['CustomTable']['fields'],true);			
						?>
						<h3>Build Custom Form <small>Drag & drop type of fields you want from the left side of the panel to right side. Add field name.</small><?php echo $this->Html->link('<i class="fa fa fa-server fa-sm"></i>','javascript:void(0);',array('onclick'=>'open_master()','escape'=>false,'class'=>'pull-right'));?></h3>
							
						<?php echo $this->element('recreate_custom_forms',array('existingFields'=>$existingFields,'fieldDetails'=>$fieldDetails,'childTables'=>$this->request->data['ChildTables'],'thisTable'=>Inflector::Classify($this->request->data['CustomTable']['table_name'])));?>
					</div>
				</div>
				<?php		
				$f = $f+1;
				echo $this->Form->hidden('count',array('default'=>$f));
				echo $this->Form->hidden('id',array());
				echo $this->Form->hidden('custom_table_id',array('default'=>$this->request->data['CustomTable']['custom_table_id']));
	// echo $this->Form->hidden('qc_document_id',array());
				echo $this->Form->hidden('process_id',array());
				echo $this->Form->hidden('table_type',array());
				echo $this->Form->hidden('pre_fields',array('default'=>$this->request->data['CustomTable']['fields']));
	// echo $this->Form->hidden('table_name',array('default'=>$tableName));
				echo $this->Form->hidden('has_many',array('default'=>$this->request->params['named']['has_many']));
				echo $this->Form->hidden('History.pre_post_values', array('value'=>json_encode($this->data)));
				echo $this->Form->input('branchid', array('type' => 'hidden', 'value' => $this->Session->read('User.branch_id')));
				echo $this->Form->input('departmentid', array('type' => 'hidden', 'value' => $this->Session->read('User.department_id')));		
				?>
			</div>
			<div class=''>
				<div class='col-md-12'>
					<div class="box box-default">
						<div class="box-header with-border">
							<i class="fa fa-share-alt"></i>
							<h3 class="box-title">Table Sharing</h3>
						</div>
						<div class="box-body">
							<div class="row">
								<?php	
								if($this->request->data['CustomTable']['qc_document_id']){
									echo "<div class='col-md-12'>".$this->Form->input('share_with_branches',array('name'=>'data[QcDocument][share_with_branches][]', 'multiple','options'=>$branches, 'selected'=>json_decode($qcDocument["QcDocument"]["branches"],false), 'class'=>'form-control',)) . '</div>'; 
									echo "<div class='col-md-12'>".$this->Form->input('share_with_departments',array('name'=>'data[QcDocument][share_with_departments][]','multiple', 'options'=>$departments, 'selected'=>json_decode($qcDocument["QcDocument"]["departments"],false), 'class'=>'form-control',)) . '</div>'; 
									echo "<div class='col-md-12'>".$this->Form->input('share_with_designations',array('name'=>'data[QcDocument][share_with_designations][]','multiple', 'options'=>$designations, 'selected'=>json_decode($qcDocument["QcDocument"]["designations"],false), 'class'=>'form-control chosen',)) . '</div>'; 
									echo "<div class='col-md-12'>".$this->Form->input('user_id',array('name'=>'data[QcDocument][user_id][]','label'=>'Share with Users','multiple', 'options'=>$usernames, 'selected'=>json_decode($qcDocument["QcDocument"]["user_id"],false), 'class'=>'form-control', 'style'=>'')) . '</div>'; 
								}

								if($this->request->data['CustomTable']['process_id']){

									echo "<div class='col-md-12'>".$this->Form->input('share_with_branches',array('name'=>'data[Process][share_with_branches][]', 'multiple','options'=>$branches, 'selected'=>json_decode($process["Process"]["applicable_to_branches"],false), 'class'=>'form-control',)) . '</div>'; 
									echo "<div class='col-md-12'>".$this->Form->input('share_with_departments',array('name'=>'data[QcDocument][share_with_departments][]','multiple', 'options'=>$departments, 'selected'=>json_decode($process["Process"]["process_owners"],false), 'class'=>'form-control',)) . '</div>'; 
									echo "<div class='col-md-12'>".$this->Form->input('share_with_designations',array('name'=>'data[QcDocument][share_with_designations][]','multiple', 'options'=>$designations,'class'=>'form-control chosen',)) . '</div>'; 
									echo "<div class='col-md-12'>".$this->Form->input('user_id',array('name'=>'data[QcDocument][user_id][]','label'=>'Share with Users','multiple', 'options'=>$usernames, 'class'=>'form-control', 'style'=>'')) . '</div>'; 
								}
								?>

							</div>
						</div>
					</div>
				</div>

			</div>
			<?php if($this->request->data['CustomTable']['process_id']){ ?>
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
									
									echo "<div class='col-md-6'><br /><div class='nomargin-checkbox'><label>Do you want this document to be shared with users for scheduled data enrty? If yes, click YES below. You must define schedule.</label>".$this->Form->input('add_records',array('type'=>'checkbox', 'class'=>'chexkbox', 'label'=>'Yes')) . '</div></div>'; 
									echo "<div class='col-md-3'>".$this->Form->input('schedule_id',array()) . '</div>'; 
									echo "<div class='col-md-3'>".$this->Form->input('data_type',array('options'=>$customArray['dataTypes'])) . '</div>'; 
								?>					</div>
							</div>
						</div>
					</div>
				</div>
			<?php } ?>

			<div class="col-md-12">	
				<div class="btn-group pull-left">
					<?php echo $this->Html->link('Back',array('action'=>'index',$this->request->params['pass'][0]),array('class'=>'btn btn-default btn-sm'));?>
					<?php echo $this->Html->link('Cancel',array('action'=>'recreate',$this->request->params['pass'][0]),array('class'=>'btn btn-warning btn-sm'));?>
				</div>	
				<?php echo $this->Form->submit(__('Submit'), array('div' => false, 'class' => 'btn btn-success pull-right','id'=>'submit_id')); ?>
			</div>
			<?php echo $this->Html->image('indicator.gif', array('id' => 'submit-indicator')); ?>
			<?php echo $this->Form->end(); ?>

			<?php echo $this->Js->writeBuffer();?>
		</div>
	</div>
</div>
</div>
<div id="open_master_model"></div>
<script>

	$(function() {
		$(".sortable").sortable({
			start: function(ui,event) {
				$("#"+event.helper.context.id).css({"background":"#ccc"});			
			},
			stop: function(ui,event) {
				$("#"+event.item.context.id).css({"background":"#f3f3f3"});
			},
			revert: true
		});
	});

	function addrow(){
		var f = parseInt($("#CustomTableCount").val());
		$.ajax({
			url: "<?php echo Router::url('/', true); ?><?php echo $this->request->params['controller'] ?>/add_fields/"+f,
			success: function(data, result) {
				$("#CustomTableCount").val(f+1);
				$("#fieldstable").append(data);
				
				var rowCount = $('#fieldstable tr').length;
				if(rowCount > 1){
					$("#1stbtn").hide()
				}
			},
		});	
	}

	function delrow(f){
		$("#"+f+"_tr").remove();
		var rowCount = $('#fieldstable tr').length;		
		if(rowCount == 2){
			$("#1stbtn").show()
		}
	}

	var escapeRegExp = function(strToEscape) {
	    // Escape special characters for use in a regular expression
		return strToEscape.replace(/[\-\[\]\/\{\}\(\)\*\+\?\\\^\$\|]/g, "\\$&");
	};

	function cleanname(name,id,f){

		$("#newID"+f).removeClass(' fieldholder').addClass(' fieldholderok');
		var path = name;
		path1 = path.trim();
		str1 = path1.replace('//g','_');		
		str2 = str1.replace('.','');
		str3 = str2.replace(/[` ~!@#$%^&*()|+\-=?;:'",.<>\{\}\[\]\\]/gi,'_');		
		str4 = str3.replace(/__/g,'');
		str5 = str4.replace(/\s/g,'');		
		str6 = str5.replace(/\//g,'');
		str7 = str6.replace(/--/g,'-');		
		str8 = escapeRegExp(str7);
		

		let str = str8;
		if(str.charAt(str.length-1) == "_"){
			str = str.substring(0, str.length - 1);
		}

		str = str.toLowerCase();
		
		$("#"+id).val(str);

		if($("#"+id).val != ''){
			$("#CustomTableFields"+f+"FieldType").prop("greaterThanZero", function(value, element) {
			}, "Please select the value");


			$("#CustomTableFields"+f+"Length").prop('required',true);			
		}




		let llabel = $.camelCase(str8);
		label = llabel.replace(/_/g,' ');

		$("#CustomTableFields"+f+"FieldLabel").val(label).capitalize();
		
		$("label[for='CustomTableFields"+f+"Dummy']").text(label).capitalize();
		$(".radio"+f).find('legend').text(label).capitalize();
		$(".date"+f).prev('label').text(label).capitalize();
		$(".datetime"+f).prev('label').text(label).capitalize();
		$(".checkbox"+f).find('legend').text(label).capitalize();

	}


	$.fn.capitalize = function() {
	    $(this[0]).blur(function(event) {
	        var box = event.target;
	        var txt = $(this).val();	        	        
	        var stringStart = box.selectionStart;
	        var stringEnd = box.selectionEnd;
	        $(this).val(txt.replace(/^(.)|(\s|\-)(.)/g, function($word) {
	            return $word.toUpperCase();
	        }));
	        box.setSelectionRange(stringStart , stringEnd);
	    });

	   return this;
	}	

	function dropalert(f){
		var r = confirm("This change will delete the existing database table, resulting in data loss.\n Are you sure you wish to proceed with this change?");
		if (r == true) {
		  // proceed
			$("#CustomTableDropTable").prop("checked",true);
		} else {
		  // revert changes, 
			$("#CustomTableDropTable").prop("checked",false);
			
			$("#CustomTableFields"+f+"FieldName").val($("#CustomTableFields"+f+"FieldNamePre").val());
			$("#CustomTableFields"+f+"DisplayType").val($("#CustomTableFields"+f+"DisplayTypePre").val()).trigger('chosen:updated');
			$("#CustomTableFields"+f+"FieldType").val($("#CustomTableFields"+f+"FieldTypePre").val()).trigger('chosen:updated');
			$("#CustomTableFields"+f+"LinkedTo").val($("#CustomTableFields"+f+"LinkedToPre").val()).trigger('chosen:updated');
			$("#CustomTableFields"+f+"Length").val($("#CustomTableFields"+f+"LengthPre").val());
			$("#CustomTableFields"+f+"Size").val($("#CustomTableFields"+f+"SizePre").val()).trigger('chosen:updated');
			$("#CustomTableFields"+f+"Mandetory").val($("#CustomTableFields"+f+"MandetoryPre").val()).trigger('chosen:updated');
			$("#CustomTableFields"+f+"Csvoptions").val($("#CustomTableFields"+f+"CsvoptionsPre").val());
			$("#CustomTableFields"+f+"IndexShow").val($("#CustomTableFields"+f+"IndexShow").val()).trigger('chosen:updated');
			return false;
		}
	}

	function setoptions(val,f){

		var drop = dropalert(f);
		if(drop == false){
			return false;
		}

		
		if(val == 1){ // radio 
			$("#CustomTableFields"+f+"LinkedTo").val(-1).trigger('chosen:updated').attr({'disabled':true}).trigger('chosen:updated');
			$("#CustomTableFields"+f+"FieldType").val(2).trigger('chosen:updated');
			$("#CustomTableFields"+f+"Length").val(1);
			$("#CustomTableFields"+f+"Csvoptions").rules('add', {
				required: true,
			});				
		}else if(val == 2){ // checkbox
			$("#CustomTableFields"+f+"LinkedTo").val(-1).trigger('chosen:updated');
			$("#CustomTableFields"+f+"FieldType").val(2).trigger('chosen:updated');
			$("#CustomTableFields"+f+"Length").val(1);
			$("#CustomTableFields"+f+"Csvoptions").rules('add', {
				required: true,
			});
		}else if(val == 3){ // dropdowns		
			$("#CustomTableFields"+f+"LinkedTo").val(-1).trigger('chosen:updated');
			$("#CustomTableFields"+f+"LinkedTo").rules('add', {
				greaterThanZero: true,
			});

			$("#CustomTableFields"+f+"LinkedTo").val(-1).trigger('chosen:updated').attr({'disabled':false}).trigger('chosen:updated');
			$("#CustomTableFields"+f+"FieldType").val(0).trigger('chosen:updated');
			$("#CustomTableFields"+f+"Length").val(36);
			$("#CustomTableFields"+f+"FieldType").val(0).trigger('chosen:updated');

		}else if(val == 4){ // dropdowns			
			$("#CustomTableFields"+f+"LinkedTo").val(-1).trigger('chosen:updated');
			$("#CustomTableFields"+f+"LinkedTo").rules('add', {
				greaterThanZero: true,
			});
			$("#CustomTableFields"+f+"LinkedTo").val(-1).trigger('chosen:updated').attr({'disabled':false}).trigger('chosen:updated');
			$("#CustomTableFields"+f+"FieldType").val(1).trigger('chosen:updated');
			$("#CustomTableFields"+f+"Length").val("");			

		}else if(val == 5){ // dropdowns			
			$("#CustomTableFields"+f+"FieldName").val("Break");
			$("#CustomTableFields"+f+"FieldType").val(-1).trigger('chosen:updated');
			$("#CustomTableFields"+f+"Csvoptions").rules('remove');	
			$("#CustomTableFields"+f+"LinkedTo").val(-1).trigger('chosen:updated');
			$("#CustomTableFields"+f+"LinkedTo").rules('remove');
			$("#CustomTableFields"+f+"Length").rules('remove');	
			$("#CustomTableFields"+f+"Length").val(0)
			$("#CustomTableFields"+f+"LinkedTo").val(-1).trigger('chosen:updated').attr({'disabled':false}).trigger('chosen:updated');

		}else{
			$("#CustomTableFields"+f+"FieldType").val(-1).trigger('chosen:updated');
			$("#CustomTableFields"+f+"Length").val("");
			$("#CustomTableFields"+f+"Csvoptions").rules('remove');	
			$("#CustomTableFields"+f+"LinkedTo").val(-1).trigger('chosen:updated');
			$("#CustomTableFields"+f+"LinkedTo").rules('remove');
			$("#CustomTableFields"+f+"LinkedTo").val(-1).trigger('chosen:updated').attr({'disabled':false}).trigger('chosen:updated');
		}
	}

	function getfields(model,f){		
		
	}

	function typelength(val,f){	

		var drop = dropalert(f);
		if(drop == false){
			return false;
		}

		if($("#CustomTableFields"+f+"DisplayType").val() != 4){
			$("#CustomTableFields"+f+"Length").val(36);			
			$("#CustomTableFields"+f+"FieldType").val(0).trigger('chosen:updated');	
		}else{
			$("#CustomTableFields"+f+"Length").val("");			
			$("#CustomTableFields"+f+"FieldType").val(1).trigger('chosen:updated');	
		}
		
	}


	function setdefaultfield(val){
		if(val == 0){
			$("#CustomTableDefaultFieldOther").val('name');
		}

		if(val == 1){
			$("#CustomTableDefaultFieldOther").val('title');
		}

		if(val == 2){
			$("#CustomTableDefaultFieldOther").prop('required',true);
		}

	}

	// for field types
	function checklengths(val,id,f){

		var drop = dropalert(f);
		if(drop == false){
			return false;
		}

		if(val == 0){	//varchar
			if($("#CustomTableFields"+f+"Length").val() != 36){
				// $("#CustomTableFields"+f+"Length").val(255);
				$("#CustomTableFields"+f+"Length").attr('maxlength',3);
				$("#CustomTableFields"+f+"Length").rules('add', {
					varchar: true,
					required: true,
				});
				
			}
		}
		if(val == 1 || val == 5 || val == 6){ // date/textarea
			$("#CustomTableFields"+f+"Length").rules('add',{
				required: false,
			});
			$("#CustomTableFields"+f+"Length").val('');	
		}
		if(val == 2){
			// $("#CustomTableFields"+f+"Length").val(11);
			$("#CustomTableFields"+f+"Length").rules('add', {
				required: true,
				int: true,
			});
			$("#CustomTableFields"+f+"LinkedTo").val(-1).trigger('chosen:updated');	
			$("#CustomTableFields"+f+"Length").val(11);
			$("#CustomTableFields"+f+"DisplayType").val(-1).trigger('chosen:updated');	

		}
		if(val == 3){ // tinyint
			$("#CustomTableFields"+f+"Length").val(1);
			$("#CustomTableFields"+f+"Length").rules('add', {
				required: true,		        
			});
			$("#CustomTableFields"+f+"LinkedTo").val(-1).trigger('chosen:updated');
			$("#CustomTableFields"+f+"DisplayType").val(-1).trigger('chosen:updated');
		}
		if(val == 4){
			$("#CustomTableFields"+f+"Length").val("11,2");
			$("#CustomTableFields"+f+"Length").rules('add', {
				required: true,
				float: true,
			});
			$("#CustomTableFields"+f+"LinkedTo").val(-1).trigger('chosen:updated');
			$("#CustomTableFields"+f+"DisplayType").val(-1).trigger('chosen:updated');
		}
		
	}	

	$.validator.setDefaults({
		ignore: null,
		errorPlacement: function(error, element) {
			$(element).after(error);
			
		},
	});
	

	function open_master(){
		$("#open_master_model").load("<?php echo Router::url('/', true); ?>custom_tables/create_master");
	}

	function check_multiple_defaults(ckid){
		var i = 0;
		$(".default_field input[type=checkbox]").each(function(val){        		        
			var id;
			var ck;
			id = this.id;
			ck = $("#"+id).prop('checked');		        
			if(ck == true){
				i++;
			}

		}); 		      
		

		if(i > 0 && i != 1){
			alert('You must add one default field. Only one default field is allowed.');
			$("#"+ckid).prop('checked',false);
			return false;		        	  
		}else{
			
		}
		
	}
	
	$(document).ajaxStop(function(){
		check_multiple_defaults();
		
	});



	$().ready(function() {    
		
		<?php if(!empty($qcDocument['ParentDocument']['title'])){ ?>
			$.ajax({
				url: "<?php echo Router::url('/', true); ?>custom_tables/get_data/"+$("#CustomTableFieldName").val()+"/<?php echo $parent['CustomTable']['id'];?>/value:<?php echo $this->request->data['CustomTable']['field_value'];?>",
				success: function(data, result) {		        	
					$("#get_data").html(data);	
					$('#CustomTableAddForm').validate({        	
						rules: {
							"data[CustomTable][field_value]": {
								greaterThanZero: true,
							}
						}
					});
				},
			});	
		<?php } ?>
		addrow();
		<?php if(!empty($qcDocument['ParentDocument']['title'])){ ?>
			$("#CustomTableFieldName").on('change',function(){
				$.ajax({
					url: "<?php echo Router::url('/', true); ?>custom_tables/get_data/"+$("#CustomTableFieldName").val()+"/<?php echo $parent['CustomTable']['id'];?>",
					success: function(data, result) {		        	
						$("#get_data").html(data);
						$('#CustomTableAddForm').validate({        	
							rules: {
								"data[CustomTable][field_value]": {
									greaterThanZero: true,
								}
							}
						});
					},
				});	
			});
		<?php } ?>
		jQuery.validator.addMethod("greaterThanZero", function(value, element) {
			return this.optional(element) || (parseInt(value) != -1);
		}, "This field is required.");

		jQuery.validator.addMethod("varchar", function(value, element) {
			return this.optional(element) || (parseFloat(value) < 256);
		}, "Max 255");

		jQuery.validator.addMethod("int", function(value, element) {
			return this.optional(element) || (parseFloat(value) < 12);
		}, "Max 11");

		jQuery.validator.addMethod("float", function(value, element) {
			return this.optional(element) || (parseFloat(value) != "11,2");
		}, "Only 11,2");

		jQuery.validator.addMethod("tinyint", function(value, element) {
			return this.optional(element) || (parseFloat(value) == 1);
		}, "Max 1");        


		$('#CustomTableRecreateForm').validate();

		$('select').each(function() {	
			if($(this).prop('required') == true){
				$(this).rules('add', {
					greaterThanZero: true
				});
			}
			
		});		


		
		$("#submit-indicator").hide();
		$("#submit_id").click(function(e){
			var result;
			result = check_multiple_defaults();
			if(result == false){
				return false;
				e.preventDefault();
			}
			
			if($('#CustomTableRecreateForm').valid()){
				$("#submit_id").prop("disabled",true);
				$("#submit-indicator").show();
				$('#CustomTableRecreateForm').submit();
			}

		});
		

	});
</script>
