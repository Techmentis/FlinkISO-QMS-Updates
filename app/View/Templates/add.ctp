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
		if(filetype == 'doc' || filetype == 'docx' || filetype == 'odt' || filetype == 'txt'){
			$("#TemplateFileType0").prop('checked',true);	
		}else if(filetype == 'xls' || filetype == 'xlsx'){
			$("#TemplateFileType1").prop('checked',true);
		}else if (filetype == 'ppt' || filetype == 'pptx'){
			alert(<?php echo json_encode(__('Only doc, docx, xls & xlsx formats are allowed'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>);
			$("#TemplateFileType2").prop('checked',true);
		}		
	}

	$().ready(function(){
		$("#TemplateFile").on('change',function(){			
			let pathext = this.value;
			const pathfile = pathext.split(".");
			const arr = pathfile.length;
			const ext = pathfile[arr-1];
			
			var earr = ["doc","docx","xls","xlsx","pdf","ppt","odt","txt","pptx","pptm"];
			
			if($.inArray(ext,earr) > -1){
				
			}else{
				alert(<?php echo json_encode(__('This file type is not allowed : '), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?> + ext + <?php echo json_encode(__('
Allowed file types are "doc","docx","xls","xlsx","pdf","ppt","odt","txt","pptx","pptm"'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>);
				location.reload(true);
			}
			let name = pathext.split('.' + ext);
			
			let path = name[0];
			const file = path.split("\\");			
			const filename  = file[file.length - 1];

			var str = filename;
			$("#TemplateName").val(filename);
			addfiletype(ext);
		});
	})

	$().ready(function(){
		$("#TemplateName").on('change',function(){			
			$.ajax({
				url: "<?php echo Router::url('/', true); ?><?php echo $this->request->params['controller'] ?>/check_duplicates/"+btoa($("#TemplateName").val()),
				success: function(data, result) {						    		
					if(data == 1){
						$("#TemplateName").val('');
						$("#duplicate_name").html(<?php echo json_encode(__('Template with this name already exists. Chose another name'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>);
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
	<?php echo $this->element('nav-header-lists',array('postData'=>array('pluralHumanName'=>__('Template'),'modelClass'=>'Template','options'=>array(),'pluralVar'=>'templates'))); ?>
	<?php echo $this->Form->create('Template',array('role'=>'form','class'=>'form','type'=>'file')); ?>
	<div class="nav panel panel-default">
		<div class="qcDocuments form col-md-12">			
			<div class="row">		
				<?php
				$filesTypes = array('docx'=>'Document','xlsx'=>'Spreadsheet');
				echo "<div class='col-md-4'><span class='control-fileupload' style='margin-top:28px'><i class='fa fa-file'></i>". $this->Form->input('file',array('class'=>$block_class ,  'type'=>'file', 'label'=>__('Upload Document'), 'div'=>false , 'onchange'=>'$(this).prev("label").html($(this).val().substr(12));')) . "</div>";
				echo "<div class='col-md-4'>".$this->Form->input('name',array('required'=>'required', 'label'=>__('Template Name'), 'class'=>'form-control',)) . '</div>';
				echo "<div class='col-md-4'>".$this->Form->input('file_type',array('required'=>'required','class'=>'', 'type'=>'radio', 'options'=>array(0=>__('Document'),1=>__('Spreadsheet'),2=>__('Presentation')))) . '</div>';
				echo "<div class='col-md-4'>".$this->Form->hidden('model',array('default'=>'Template',)) . '</div>';
				echo "<div class='col-md-4'>".$this->Form->hidden('controller',array('default'=>'templates',)) . '</div>';
				echo "<div class='col-md-4'>".$this->Form->hidden('record_id',array('default'=>'template',)) . '</div>';
				?>
				<div class="col-md-12" id="duplicate_name"></div>
				<div class="col-md-12"><div class="show_comments">
					<i class="fa hide fa-exclamation-triangle"></i>
					<strong><?php echo h(__('Option-1:')); ?> </strong><?php echo h(__('Add template name')); ?> & <?php echo h(__('file type and save the record, it will redirect you to the template creation page. You can then create a template from scratch and save it for future use. If you select Document, system will load blank document, if you select Spreadsheet, system will load blank spreadsheet. You can then add/ edit containt to the newly created document.')); ?><br />
					<strong><?php echo h(__('Option-2:')); ?> </strong><?php echo h(__('Upload exising template from your local machine (allowed file types: "doc","docx","xls","xlsx","pdf","ppt","odt","txt","pptx","pptm" and click submit. (Do not change File Type).')); ?><br />
				</div></div>
			</div>
			<br />
		</div>			
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
			name = <?php echo json_encode(__('Document Number'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
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
						name = <?php echo json_encode(__('Document Number'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
							// clean_document_number(value);
					}

					$("#"+id).val('');
					$("#duplicate_errors").html(name + <?php echo json_encode(__(' already exists'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>);
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
		}, <?php echo json_encode(__('Please select the value'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>);

		$('#TemplateAddForm').validate(); 
		
		$("#submit-indicator").hide();
		$("#submit_id").click(function(e){	   
			
			if($('#TemplateAddForm').valid()){
				$("#submit_id").prop("disabled",true);
				$("#submit-indicator").show();
				$('#TemplateAddForm').submit();
			}
		});

		
	});
</script>

