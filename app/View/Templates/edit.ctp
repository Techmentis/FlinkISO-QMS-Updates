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
<script type="text/javascript">
	function addfiletype(filetype){
		$("#QcDocumentFileType").val(filetype);	
		$("#filetypediv").hide();
	}

	$().ready(function(){
		$("#TemplateName").on('change',function(){			
			$.ajax({
				url: "<?php echo Router::url('/', true); ?><?php echo $this->request->params['controller'] ?>/check_duplicates/"+btoa($("#TemplateName").val()),
				success: function(data, result) {						    		
					if(data == 1){
						$("#TemplateName").val('');
						$("#duplicate_name").html('Template with this name already exists. Chose another name');
					}else{
						$("#duplicate_name").html('');
					}
				},						        
			});	
			// var obj = jQuery.parseJSON(data);
		});
	})

</script>				
<div id="qcDocuments_ajax">
	<?php echo $this->element('nav-header-lists',array('postData'=>array('pluralHumanName'=>'Template','modelClass'=>'Template','options'=>array(),'pluralVar'=>'templates'))); ?>
	<?php echo $this->Form->create('Template',array('role'=>'form','class'=>'form','type'=>'file')); ?>
	<div class="nav panel panel-default">
		<div class="qcDocuments form col-md-12">			
			<div class="row">		
				<?php
				$filesTypes = array('docx'=>'Document','xlsx'=>'Spreadsheet');
				echo $this->Form->input('id');
				echo "<div class='col-md-8'>".$this->Form->input('name',array('readonly'=>'readonly', 'label'=>'Template Name', 'class'=>'form-control',)) . '</div>';
				echo "<div class='col-md-4'>".$this->Form->input('file_type',array('readonly'=>'readonly','options'=>$filesTypes, 'type'=>'radio','default'=>'docx', 'required'=>'required','class'=>'',)) . '</div>';
				echo "<div class='col-md-4'>".$this->Form->hidden('model',array('default'=>'Template',)) . '</div>';
				echo "<div class='col-md-4'>".$this->Form->hidden('controller',array('default'=>'templates',)) . '</div>';
				echo "<div class='col-md-4'>".$this->Form->hidden('record_id',array('default'=>'template',)) . '</div>';
				?>
			</div>
			<br />
		</div>			
	</div>		
</div>
<div class="row">
	<div class="col-md-12">
		<?php		
		$key = $this->request->data['Template']['file_key'];
		$file_type = $this->request->data['Template']['file_type'];
		// $file_name = $this->request->data['Template']['name'];
		
		if($file_type == 'doc' || $file_type == 'docx'){
			$documentType = 'word';
		}

		if($file_type == 'xls' || $file_type == 'xlsx'){
			$documentType = 'cell';
		}
		$mode = 'edit';
		$file_path = $this->request->data['Template']['id'];
		$file = $file_name.'.'.$file_type;

		echo $this->element('onlyoffice',array(
			'url'=>$url,						
			'user_id'=>$user_id,				
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
			'filekey'=>$key,            
			'record_id'=>$this->request->data['Template']['id'],
			'company_id'=>$this->Session->read('User.company_id'),
			'controller'=>$this->request->controller,
		));
		?>
	</div>
</div>

<div class="">
	<?php echo $this->Form->submit(__('Submit'), array('div' => false, 'class' => 'btn btn-primary btn-success','id'=>'submit_id')); ?>
	<?php echo $this->Html->image('indicator.gif', array('id' => 'submit-indicator','class'=>'hide')); ?>
	<?php echo $this->Form->end(); ?>

	<?php echo $this->Js->writeBuffer();?>
</div>	
</div>
<script>
	$.validator.setDefaults({
		ignore: null,
		errorPlacement: function(error, element) {
			if(element['context']['className'] == 'form-control select error'){
				$(element).next().after(error); 		
			}else{
				$(element).after(error); 
			}
		},
	});
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

	$().ready(function() {	    	

		jQuery.validator.addMethod("greaterThanZero", function(value, element) {
			return this.optional(element) || (parseFloat(value) != -1);
		}, "Please select the value");

		$('#TemplateEditForm').validate(); 
		
		$("#submit-indicator").hide();
		$("#submit_id").click(function(e){	   
			
			if($('#TemplateEditForm').valid()){
				$("#submit_id").prop("disabled",true);
				$("#submit-indicator").show();
				$('#TemplateEditForm').submit();
			}
		});

		
	});
</script>

