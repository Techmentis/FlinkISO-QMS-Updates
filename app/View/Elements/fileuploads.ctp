<?php
if(!$id){
	$id = $qcDocument['QcDocument']['id'] = $this->request['data']['id'];
}
?>
<style type="text/css">
	#myUL, #myUL li {list-style-type: none;padding: 5px 0 5px 5px !important; }		
	#myUL {margin: 0;padding: 0;}
	#myUL strong{color: #000000;}
	#myUL li a{color: #262525}
	#myUL li a .fa{padding-left: 12px}
	#myUL .file{width: auto !important;padding: 1px 5px 0 0 !important; }
	#myUL .caret {cursor: pointer;user-select: none;border:none; width: 100% !important; margin-bottom: 25px}
	#myUL .caret::before {content: "\25B6";display: inline-block;margin-right: 6px; font-size: 8px}
	#myUL .caret-down::before {transform: rotate(90deg);}
	#myUL .nested {display: none;}
	#myUL .active {display: block;}
	.nli:hover, .caret:hover{background-color: #ccc}		
/*	.folder:hover{background-color: #ccc !important;}*/
</style>
<?php echo $this->Html->script(array('jquery.validate.min', 'jquery-form.min')); ?>
<?php echo $this->fetch('script'); ?>
<div id="upload">
	<h4>Add New Files</h4>

	<div class="panel panel-default">
		<div class="panel-body">
			<?php  echo $this->Form->create(false,array('url'=>array('controller'=>$this->request->controller, 'action'=>'upload_document'),'id'=>'uploadform','role'=>'form','default'=>false, 'class'=>'form','type'=>'file'));?>	
			<div class="row">		
				<?php echo "<div class=''>".$this->Form->hidden('id',array('default'=>$id))."</div>";
				echo "<div class='col-md-5'><label>Define path: <small>Seperated by '/' and without spaces.</small></lable>".$this->Form->input('path',array('class'=>'form-control','div'=>array('class'=>'input-group'),'before'=>'<span class="input-group-addon"><i class="fa fa-folder"></i></span>','label'=>false))."</div>";
				?>				
				<div class="col-md-5"><br />
					<?php 
					echo "<span class='control-fileupload'><i class='fa fa-file-o'></i>". $this->Form->input('file',array('class'=>$block_class ,  'type'=>'file', 'label'=>'Upload File', 'div'=>false , 'onchange'=>'$(this).prev("label").html($(this).val().substr(12));'))					
					?>
				</div>
				<div class="col-md-2">
					<br />
					<?php echo $this->Form->submit('Upload File',array('class'=>'btn btn-sm btn-info','id'=>'submit_id'));?>
				</div>		
				<div class="col-md-12">
					<div class="progress progress-sm active">
						<div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
							<span class="sr-only">20% Complete</span>
						</div>
					</div>
				</div>		
			</div>
		</div>
		<div class="panel-footer">
			<span class="help-text text-warning">
				<small>Allowed file types : Images/Documents/PDF. Max Size: 5MB.
					<br />Note : These documents will not be version controlled and will be available to download as it is.
					<br />These documents will be available for download to everyone who has access to this record.
				</small>
			</span>
		</div>
	</div>
</div>
<?php echo $this->Form->end(); ?>
<script>
	$.validator.setDefaults({
		ignore: null,
		errorPlacement: function(error, element) {            
		},
		submitHandler: function(form) {
			$("#uploadform").ajaxSubmit({
				url: "<?php echo Router::url('/', true); ?><?php echo $this->request->params['controller'] ?>/upload_document",
				type: 'POST',
				target: '#tab_4',
				beforeSend: function(){
					$("#submit_id").prop("disabled",true);
					$(".progress").show();
                    // $("#submit-indicator").show();
				},
				complete: function() {
					$("#submit_id").removeAttr("disabled");
					$(".progress").hide();
                   // $("#submit-indicator").hide();
				},
				error: function(request, status, error) {                    
					alert('Action failed!');
				}
			});
		}
	});
	$().ready(function() {
		$(".progress").hide();
		jQuery.validator.addMethod("greaterThanZero", function(value, element) {
			return this.optional(element) || $("#"+element.id+" option:selected").text() != 'Select';
		}, "Please select the value");
		
		$('#uploadform').validate({    
		}); 
	});
</script><script>$.ajaxSetup({beforeSend:function(){$("#submit-indicator").show();},complete:function(){$("#busy-indicator").hide();}});</script>

<h4>Available files:</h4>
<div class="panel panel-default">
	<div class="panel-body">
		<div class="row">
			<div class="col-md-12">
				<?php 
				$aurl = array(
					'controller' => 'qc_documents',
					'action' => 'get_directory_tree',
					$qcDocument['QcDocument']['id']
				);
				$tokan =$this->requestAction($aurl);  
				echo $tokan;
				?>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">

	var toggler = document.getElementsByClassName("caret");
	var i;

	for (i = 0; i < toggler.length; i++) {
		toggler[i].addEventListener("click", function() {
			this.parentElement.querySelector(".nested").classList.toggle("active");
			this.classList.toggle("caret-down");
		});
	}


	$().ready(function(){
		$("#path").on('change',function(){
			var path = $("#path").val();		
			str1 = path.replace(/[`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\]/gi,'_');
			str2 = str1.replace(' ','_');
			str3 = str2.replace(/\s/g,'');
			str = str3.replace('/\//','/');
			$("#path").val(str);			
		})
	});

	function deletefile(file,id){
		var txt;
		var r = confirm("Delete file? This action can not be reversed.");
		if (r == true) {
			$.ajax({
				url: "<?php echo Router::url('/', true); ?><?php echo $this->request->params['controller'] ?>/delete_uploaded_file/" + file,
				success: function(data, result) {
					$("#id_"+id).hide();        
				}
			});
			
		} else {
			
		}
	}

	function selectfolder(folderpath,id){
		$("#path").val('');
		folderpath = atob(folderpath);
		let folder = folderpath.replace("<?php echo Configure::read('url')?>", "");
		folder = folder.replace("<?php echo $id?>", "");
		folder = folder.replace("addtional_documents", "");
		folder = folder.replace("//", "");
		$("#path").val(folder);
	}

	function deletefolder(folderpath,id){
		var txt;
		var r = confirm("Delete folder? This action can not be reversed.");
		if (r == true) {
			$.ajax({
				url: "<?php echo Router::url('/', true); ?><?php echo $this->request->params['controller'] ?>/delete_uploaded_folder/" + folderpath,
				success: function(data, result) {
					$("#fid_"+id).hide();        
				}
			});
			
		} else {
			
		}
	}

</script>
