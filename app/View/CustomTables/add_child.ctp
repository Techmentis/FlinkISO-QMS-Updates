<?php echo $this->Html->script(array('jquery.validate.min', 'jquery-form.min')); ?>
<?php echo $this->fetch('script'); ?>

<?php echo $this->Html->css(array('dragdrop')); ?>
<?php echo $this->fetch('css'); ?>

<style type="text/css">
	#dropHere {padding: 0px;width:100%;height: 736px;border: 1px solid #dddddd;margin: 10px 0;border-radius: 2px;background-image: url('<?php echo Router::url('/', true); ?>img/vline.svg'); background-repeat: repeat-y;background-size: 100% 100%;
	}
</style>
<div id="customTables_ajax">
	<?php echo $this->Session->flash();?>
	<div class="customTables ">
		<?php echo $this->element('nav-header-lists',array('postData'=>array('pluralHumanName'=>'Custom Tables','modelClass'=>'CustomTable','options'=>array(),'pluralVar'=>'customTables'))); ?>

		<div class="row">
			<div class="col-md-12">
				<div class="box box-default collapsed-box">
					<div class="box-header with-border"><h3 class="box-title" style="width:100%">Parent Table Details</h3>
						<div class="box-tools pull-right">
							<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
						</div>
					</div>
					<div class="box-body">		
						<table class="table table-responsive">
							<tr><th>Name</th><td><?php echo $customTable['CustomTable']['name']?></td></tr>
							<tr><th>Table Name</th><td><?php echo $customTable['CustomTable']['table_name']?></td></tr>
							<tr><th>Table Version</th><td><?php echo $customTable['CustomTable']['table_version']?></td></tr>
							<tr><th>Fields</th><td>
								<ul><?php 
								$fields = json_decode($customTable['CustomTable']['fields'],true);
								foreach($fields as $field){
									echo "<li>" . $field['field_name'] . "</li>";
								}
							?></ul></td></tr>
							<tr><th>Linked To</th><td><ul><?php 
							
							$fields = json_decode($customTable['CustomTable']['fields'],true);
							foreach($fields as $field){
								if($field['linked_to'] != -1){
									echo "<li>".$field['field_name'] . " : <strong>".$field['linked_to']."</strong> </li>";
								}
								
							}				
						?></ul></td></tr>
						<tr><th>Status</th><td><?php echo $customTable['CustomTable']['table_locked']? 'Unlocked':'Locked';?></td></tr>					
					</table>
				</div>
			</div>
		</div>
	</div>

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
				
				<?php echo "<div class='col-md-4'>".$this->Form->input('name',array('class'=>'form-control','required','default'=>$customTable['CustomTable']['name'].' Child '.$table_version)) . '</div>'; ?>
				<?php echo "<div class='col-md-3'>".$this->Form->input('table_name',array('class'=>'form-control', 'readonly', 'default'=>$table_name)) . '</div>'; ?>
				<?php echo "<div class='col-md-1'>".$this->Form->input('table_version',array('label'=>'Version', 'class'=>'form-control', 'readonly', 'default'=>$table_version)) . '</div>'; ?>
				<?php echo "<div class='col-md-12 hide'>".$this->Form->hidden('fields',array('class'=>'form-control',)) . '</div>'; ?>
				
				<?php echo "<div class='col-md-2'>".$this->Form->input('password',array('type'=>'password', 'class'=>'form-control',)) . '</div>'; ?>
				<?php echo "<div class='col-md-2'>".$this->Form->input('re-password',array('type'=>'password', 'class'=>'form-control',)) . '</div>'; ?>	

				<?php echo "<div class='col-md-12'>".$this->Form->input('description',array('class'=>'form-control',)) . '</div>'; ?>	
				<?php 
				if(!empty($this->request->params['named']['qc_document_id'])){
					echo "<div class='col-md-12'>".$this->Form->input('qc_document_id',array('class'=>'form-control','default'=> $this->request->params['named']['qc_document_id'])) . '</div>';					
				}
				if(!empty($this->request->params['named']['process_id'])){
					echo "<div class='col-md-12'>".$this->Form->input('process_id',array('class'=>'form-control','default'=>$process['Process']['id'])) . '</div>';					
				}
				?>
			</div>
			<div class="row">
				<div class="col-md-12">
					<?php
					if($this->request->params['named']['qc_document_id'] != ''){
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

						$file_path = $qcDocument['QcDocument']['id'];



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
						$file = $file .'.'.$file_type;

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
							'filekey'=>$key.'1',            
							'record_id'=>$qcDocument['QcDocument']['id'],
							'company_id'=>$this->Session->read('User.company_id'),
							'controller'=>'custom_tables'
						));
					}else if($this->request->params['named']['process_id'] != ''){
						$key = $key;
						$file_type = $process['Process']['file_type'];
						$file_name = $process['Process']['file_name'];
						
	        // $file_type = $qcDocument['QcDocument']['file_type'];
						
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
							'preparedby'=>$this->Session->read('User.name'),
							'filekey'=>$key.'1',            
							'record_id'=>$process['Process']['id'],
							'company_id'=>$this->Session->read('User.company_id'),
							'controller'=>'custom_tables'
						));
					}	
					?>
				</div>
				<div class="">
					<div class="col-md-12">
						<?php echo $this->element('available_forms',array('availableForms'=>$availableForms,'type'=>'child')); ?>
					</div>
				</div>
				<div class="">
					<div class="col-md-12">
						<?php echo $this->Form->input('form_layout',array('type'=>'radio','options'=>array(1=>'Regular',2=>'Table'),'default'=>2));?>
						<h3>Build Custom Form <small>Drag & drop type of fields you want from the left side of the panel to right side. Add field name.</small><?php echo $this->Html->link('<i class="fa fa fa-server fa-sm"></i>','javascript:void(0);',array('onclick'=>'open_master()','escape'=>false,'class'=>'pull-right'));?></h3>
						<?php // echo $this->element('fieldshelp');?>			
						<?php echo $this->element('custom_forms');?>
					</div>
				</div>
			</div>

			<?php		
			if($this->request->params['named']['qc_document_id']){
				echo $this->Form->hidden('qc_document_id',array('default'=>$this->request->params['named']['qc_document_id']));	
				echo $this->Form->hidden('table_type',array('default'=>0));	
			}else if($this->request->params['named']['process_id']){
				echo $this->Form->hidden('process_id',array('default'=>$this->request->params['named']['process_id']));
				echo $this->Form->hidden('table_type',array('default'=>1));	
			}
			echo $this->Form->hidden('count',array('default'=>1));
			echo $this->Form->hidden('custom_table_id',array('default'=>$this->request->params['named']['custom_table_id']));
			echo $this->Form->hidden('qc_document_id',array('default'=>$this->request->params['named']['qc_document_id']));
			echo $this->Form->hidden('process_id',array('default'=>$this->request->params['named']['process_id']));
			echo $this->Form->hidden('table_name',array('default'=>$tableName));
			echo $this->Form->hidden('History.pre_post_values', array('value'=>json_encode($this->data)));
			echo $this->Form->input('branchid', array('type' => 'hidden', 'value' => $this->Session->read('User.branch_id')));
			echo $this->Form->input('departmentid', array('type' => 'hidden', 'value' => $this->Session->read('User.department_id')));		
			?>


			<div class="">
				<?php
				if ($showApprovals && $showApprovals['show_panel'] == true) {
					echo $this->element('approval_form');
				} else {
					echo $this->Form->input('publish', array('label' => __('Publish')));
				}
				?>
				<div class=""><?php echo $this->Form->submit(__('Submit'), array('div' => false, 'class' => 'btn btn-primary btn-success','id'=>'submit_id')); ?></div>
				<?php echo $this->Html->image('indicator.gif', array('id' => 'submit-indicator')); ?>
				<?php echo $this->Form->end(); ?>

				<?php echo $this->Js->writeBuffer();?>
			</div>
		</div>
	</div>
</div>
<script>
	$(function() {
		$(".sortable").sortable({
			revert: true
		});
	});

	function addrow(f){
		var f = parseInt($("#CustomTableCount").val());
		f = f + 1;
		$.ajax({
			url: "<?php echo Router::url('/', true); ?><?php echo $this->request->params['controller'] ?>/add_fields/"+f,
			success: function(data, result) {						    		
				$("#fieldstable").append(data);
				$("#CustomTableCount").val(f);
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
		return strToEscape.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&");
	};

	function cleanname(name,id,f){

		$("#newID"+f).removeClass(' fieldholder').addClass(' fieldholderok');

		var path = name;
		path1 = path.trim();
		str1 = path1.replace('/','_');		
		str2 = str1.replace('.','');
		str3 = str2.replace(/[` ~!@#$%^&*()|+\-=?;:'",.<>\{\}\[\]\\]/gi,'_');		
		str4 = str3.replace(/__/g,'_');
		str5 = str4.replace(/\s/g,'');		
		str6 = str5.replace(/\//g,'/');
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

		let llabel = str8;
		label = llabel.replace(/_/g,' ');
		
		$("#CustomTableFields"+f+"FieldLabel").val(label);		
		
		$("label[for='CustomTableFields"+f+"Dummy']").text(label);
		$(".radio"+f).find('legend').text(label);
		$(".date"+f).prev('label').text(label);
		$(".datetime"+f).prev('label').text(label);
		$(".checkbox"+f).find('legend').text(label);

	}

	
	function getfields(model,f){		
		// $('#'+f+'_div').load('<?php echo Router::url('/', true); ?>custom_tables/get_linked_to_fields/model:'+ model+'/f:'+f);
	}

	function typelength(val,f){			
		if($("#CustomTableFields"+f+"DisplayType").val() != 4){
			$("#CustomTableFields"+f+"Length").val(36);			
			$("#CustomTableFields"+f+"FieldType").val(0).trigger('chosen:updated');	
		}else{
			$("#CustomTableFields"+f+"Length").val("");			
			$("#CustomTableFields"+f+"FieldType").val(1).trigger('chosen:updated');	
		}
		
	}
	
	$.validator.setDefaults({
		ignore: null,
		errorPlacement: function(error, element) {
			$(element).after(error);
		},
	});
	
	$().ready(function() {     	

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

		$('#CustomTableAddChildForm').validate();	

		$('select').each(function() {	
			if($(this).prop('required') == true){
				$(this).rules('add', {
					greaterThanZero: true
				});
			}
			
		});

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
			

			if(i != 1){
				alert('You must add one default field. Only one default field is allowed.');
				$("#"+ckid).prop('checked',false);
				return false;
			}else{
				
			}
			
		}


		$("#submit-indicator").hide();
		$("#submit_id").click(function(){        	
			var result;
			result = check_multiple_defaults();
			if(result == false){        		
				return false;        	
				e.preventDefault();	
			}

			if($('#CustomTableAddChildForm').valid()){
				$("#submit_id").prop("disabled",true);
				$("#submit-indicator").show();
				$('#CustomTableAddChildForm').submit();
			}else{
				
			}

		});
	});    

</script>
