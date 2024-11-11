<?php echo $this->Session->flash();?>
<?php echo $this->Html->script(array('jquery.validate.min', 'jquery-form.min', 'sign')); ?>
<style type="text/css">
	.wrapper1,.wrapper2 {border-radius: 4px; border: 2px dashed #ccc;position: relative;width: 352px;height: 152px;-moz-user-select: none;-webkit-user-select: none;-ms-user-select: none;user-select: none;margin: auto; }.signature-pad {position: absolute;left: 0;top: 0;width:350px;height:150px;}
	#signatureModal, .modal-dialog{width: 390px;height: 200px}#clear{width: 350px;border-radius: 0 0 4px 4px;}#fsubmit{margin-bottom: 20px !important}
	.requiredsign{border: 2px dashed red}
	.chosen-container, .chosen-container-single, .chosen-select{min-width: 1px}
</style>
<?php echo $this->fetch('script'); ?>
<?php echo $this->Form->create('DocumentDownload',array(),array('default'=>false)); ?>
<div class="modal fade " id="pdf-download" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-wide">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title"><?php echo __('Download PDF'); ?></h4>
				<small>Securly generate and download PDF</small>
			</div>
			<div class="modal-body" id="show-download">
				<div class="col-md-12 text-center hide">
					<?php 
					$signAvailable = false; 
					$sign = WWW_ROOT . 'img' . DS . $this->Session->read('User.company_id') . DS . 'signature' . DS . $this->Session->read('User.employee_id') . DS . 'sign.png';
					if(file_exists($sign)){
						$signAvailable = true;
						echo $this->Html->Image($this->Session->read('User.company_id') . DS . 'signature' . DS . $this->Session->read('User.employee_id') . DS . 'sign.png',array('height'=>'60'));						
					}?>
				</div>
				<div class="documentDownloads form row">
					<div class="col-md-6 hide"><?php echo $this->Form->input('add_document',array('class'=>'checkbox'));?></div>
					<div class="col-md-6 hide"><?php echo $this->Form->input('add_cover_page',array('class'=>'checkbox'));?></div>
					<div class="col-md-6 hide"><?php echo $this->Form->input('add_parent_records',array('class'=>'checkbox'));?></div>
					<div class="col-md-6 hide"><?php echo $this->Form->input('add_child_records',array('class'=>'checkbox'));?></div>
					<div class="col-md-6 hide"><?php echo $this->Form->input('add_linked_form_records',array('class'=>'checkbox'));?></div>
					<div class="col-md-12">
						<?php
						if(isset($qcDocument) && $qcDocument['QcDocument']['it_categories'] == 3 || $qcDocument['QcDocument']['it_categories'] == null){
							echo "Add password to protect your PDF file. Leave blank if you do not wish to. This password will not be stored.";
						}else{
							echo "This document requires password to be added before generating the PDF.";
						}
						?>
					</div>
					<div class="col-md-12"><?php
					if(isset($qcDocument) && $qcDocument['QcDocument']['it_categories'] == 3){
						echo $this->Form->input('password',array('class'=>'form-control'));		
					}else{
						echo $this->Form->input('password',array('class'=>'form-control','required'=>'required'));							
					}?></div>
					<div class="col-md-12 text-center">						
						<label>Draw Your Signature Below</label></div>
						<div class="col-md-12 text-center">
							<div id="signed"></div>
							<div class="wrapper1">
								<canvas id="signature-pad" class="signature-pad" width=350 height=150></canvas>					        
							</div>
							<?php echo $this->Form->hidden('signature',array('id'=>'digital-signature'));?>
						</div>
						<div class="col-md-12 text-center ">
							<div class="btn-group ">
								<div class="btn tooltip1" onclick="copysign();" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title = "Copy Signature"><i class="fa fa-copy "></i></div>
								<div class="btn tooltip1" onclick="getsignature();" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title = "Fetch Saved Signature"><i class="fa fa-folder-open"></i></div>
								<div class="btn tooltip1" onclick="savesignature();" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title = "Save Signature"><i class="fa fa-save"></i></div>
								<div class="btn tooltip1" onclick="clearCanvas();" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title = "Clear Signature"><i class="fa fa-eraser"></i></div>							
							</div>
						</div>
						<div class="col-md-12">
							<ul class="list-group">
								<li class="list-group-item"><i class="fa fa-copy"></i> : Copy signature from earlier uploaded png file.</li>
								<li class="list-group-item"><i class="fa fa-folder-open"></i> : Copy signature from earlier saved image.</li>
								<li class="list-group-item"><i class="fa fa-save"></i> : Save current signature image.</li>
								<li class="list-group-item"><i class="fa fa-eraser"></i> : Clear Canvas.</li>
							</ul>
						</div>
						<?php 
						$cover = Configure::read('files') . DS . 'pdf_template' . DS . 'cover' . DS . 'template.html';
						if(file_exists($cover)){
							$cover_page_options = array(
								0=>'No',
								1=>'Yes',
								2=>'QC Documents Only',
								3=>'All'
							);
							
							if(isset($this->request->params['named']['custom_table_id'])){
								unset($cover_page_options[1]);
							}

							if(isset($this->request->params['named']['controller_name']) && $this->request->params['named']['controller_name'] == 'qc_documents'){								
								unset($cover_page_options[2]);
								unset($cover_page_options[3]);
							}
							echo '<div class="col-md-12">'.$this->Form->input('add_cover',array('type'=>'radio', 'default'=>0, 'options'=>$cover_page_options)).'</div>';
						}
						?>
						<?php
						if($this->request->params['pass'][0]){ ?>
							<?php 					
							$font_face = array('Arial'=>'Arial','Times New Roman'=>'Times New Roman','Tahoma'=>'Tahoma','Helvetica'=>'Helvetica');
							?>
							<div class="col-md-6 "><?php echo $this->Form->input('font_size',array('default'=> 11 , 'class'=>'form-control'));?></div>
							<div class="col-md-6 "><?php echo $this->Form->input('font_face',array('options'=> $font_face, 'default'=>'Arial', 'class'=>'form-control'));?></div>
							<div class="col-md-12"><?php echo $this->Form->input('pdf_header_id',array('options'=>$pdfTemplateHeaders,'class'=>'select'));?></div>
							<div class="col-md-12"><?php echo $this->Form->input('pdf_template_id',array('options'=>$pdfTemplates,'class'=>'select'));?></div>
							<div class="col-md-12 text-center"><p><br /><?php 
							if($pdfTemplates){
								echo $this->Html->link('View Templates',array('controller'=>'pdf_templates','action'=>'index',$this->request->params['named']['custom_table_id'])) . ' | ';
								echo $this->Html->link('Add New Template',array('controller'=>'pdf_templates','action'=>'add',$this->request->params['named']['custom_table_id']));
							}else{
								echo $this->Html->link('Add Template',array('controller'=>'pdf_templates','action'=>'add',$this->request->params['named']['custom_table_id']));	
							} 
						?></p>
					<?php } ?>
				</div>

				<?php echo $this->Form->hidden('record_id',array('default'=>$this->request->params['pass'][0]));?>
				<?php echo $this->Form->hidden('custom_table_id',array('default'=>$this->request->params['named']['custom_table_id']));?>
				<?php echo $this->Form->hidden('qc_document_id',array('default'=>$this->request->params['named']['qc_document_id']));?>
				<?php echo $this->Form->hidden('process_id',array('default'=>$this->request->params['named']['process_id']));?>
			</div>
			<div class="modal-footer">
				<div class="row">
					<div class="col-md-12 text-center">
						<i class="fa fa-refresh fa-spin" id="pdf-spin"></i>
						<?php echo $this->Form->submit(__('Download PDF'),array('id'=>'pdf-submit', 'async' => 'false', 'class'=>'btn btn-md btn-success')); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php echo $this->Form->end(); ?>
<script type="text/javascript">
	var signaturePad = new SignaturePad(document.getElementById('signature-pad'), {
		backgroundColor: 'rgba(255, 255, 255, 0)',
		penColor: 'rgb(17,26,201)'
	});	

	function copysign(){
		clearCanvas();
		var c = document.getElementById("signature-pad");
		var ctx = c.getContext("2d");
		var img = new Image();
		img.onload = function() {
			ctx.drawImage(img, 0, 0);
		};
		img.src = "<?php echo Router::url('/', true); ?>img/<?php echo $this->Session->read('User.company_id');?>/signature/<?php echo $this->Session->read('User.employee_id');?>/sign.png";

		$(".wrapper1").removeClass(' requiredsign');

	}
	
	function getsignature(){
		clearCanvas();
		$.ajax({
			url: "<?php echo Router::url('/', true); ?>employees/get_signature",
			type: "POST",
			dataType: "json",
			contentType: "application/json; charset=utf-8",
			data: JSON.stringify({str:'<?php echo $this->Session->read('User.employee_id');?>'}),
			beforeSend: function( xhr ) {
				
			},                    
			complete: function (data,result) {
            	// console.log(data.responseText);
				var canvas = document.getElementById("signature-pad");
				var ctx = canvas.getContext("2d");

				var image = new Image();
				image.onload = function() {
					ctx.drawImage(image, 0, 0);
				};
				image.src = data.responseText;
				$(".wrapper1").removeClass(' requiredsign');
			},
			error: function (err) {
				
			}
		});
	}

	function savesignature(){
		const blank = isCanvasBlank(document.getElementById('signature-pad'));
		if(blank == true){
			$(".wrapper1").addClass(' requiredsign');
			return false;
		}else{
			$(".wrapper1").removeClass(' requiredsign');
		}
		var signature = signaturePad.toDataURL('image/png');
		$.ajax({
			url: "<?php echo Router::url('/', true); ?>employees/save_signature",
			type: "POST",
			dataType: "json",
			contentType: "application/json; charset=utf-8",
			data: JSON.stringify({str:signature}),
			beforeSend: function( xhr ) {
                // $("#"+id+" i").removeClass('fa-check').removeClass('fa-remove').addClass('fa-refresh fa-spin');
			},                    
			success: function (result) {                
			},
			error: function (err) {
				
			}
		}); 
	}

	$.validator.setDefaults({
		ignore: null,
		errorPlacement: function(error, element) {    		
			if(element['context']['className'] == 'form-control select error'){
				$(element).next('.chosen-container').addClass('error');
			}else if(element['context']['className'] == 'radio error'){
				$(element).next('legend').addClass('error');
			}else{
				$(element).after(error); 
			}
		},
		submitHandler: function(form) {
			var signature = signaturePad.toDataURL('image/png');
			$("#digital-signature").val(signature);

			$(form).ajaxSubmit({
				url: "<?php echo Router::url('/', true); ?><?php echo $this->request->params['controller'] ?>/record_list/<?php echo $this->request->params['pass'][0];?>",
				type: 'POST',
				target: '#show-download',
				beforeSend: function(){
					const blank = isCanvasBlank(document.getElementById('signature-pad'));
					if(blank == true){
						$(".wrapper1").addClass(' requiredsign');
						return false;
					}else{
						$(".wrapper1").removeClass(' requiredsign');
					}
					$("#pdf-submit").hide();
					$("#pdf-spin").show();
				},
				complete: function(data) {
					$("#pdf-spin").hide();
					$(".modal-dialog").animate({width:'60%'});
					$(".modal-title").html("Your PDFs files are ready for download.");
				},
				error: function(request, status, error) {
					alert('Action failed!');
				}
			});
		}
	});

	function isCanvasBlank(canvas) {
		const context = canvas.getContext('2d');
		const pixelBuffer = new Uint32Array(
			context.getImageData(0, 0, canvas.width, canvas.height).data.buffer
			);
		return !pixelBuffer.some(color => color !== 0);
	}

	function clearCanvas() {
		var canvas = document.getElementById('signature-pad');
		var ctx = canvas.getContext('2d');
		event.preventDefault();
		ctx.clearRect(0, 0, canvas.width, canvas.height);
	}

	$().ready(function(){
		$("select").chosen();

		<?php if($signAvailable == true){ ?>
			copysign();
		<?php } ?>

		$('.tooltip1').tooltip();
		$("#pdf-spin").hide();
		$('#pdf-download').modal('show');        
		$('#DocumentDownloadAddForm').validate();
	});
</script>
