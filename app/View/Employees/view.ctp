<div id="employees_ajax">
<?php echo $this->Session->flash();?>	
<?php echo $this->Html->script(array('jquery.validate.min', 'jquery-form.min', 'sign')); ?>
<style type="text/css">
	.wrapper1,.wrapper2 {border-radius: 4px; border: 2px dashed #ccc;position: relative;width: 352px;height: 152px;-moz-user-select: none;-webkit-user-select: none;-ms-user-select: none;user-select: none;margin: auto; }.signature-pad {position: absolute;left: 0;top: 0;width:350px;height:150px;}
	#signatureModal, .modal-dialog{width: 825px;height: 200px}#clear{width: 350px;border-radius: 0 0 4px 4px;}#fsubmit{margin-bottom: 20px !important}
	.requiredsign{border: 2px dashed red}
	.chosen-container, .chosen-container-single, .chosen-select{min-width: 1px}
</style>
<?php echo $this->fetch('script'); ?>
	<div class="employees ">
		<?php echo $this->element('nav-header-lists',array('postData'=>array('pluralHumanName'=>'Employees','modelClass'=>'Employee','options'=>array(),'pluralVar'=>'employees'))); ?>


<div class="nav panel panel-default">
<div class="employees form col-md-12">	
		<div class="row">
			<div class="col-md-7">
				<table class="table table-responsive">
					<tr><td><?php echo __('Name'); ?></td>
					<td>
						<?php echo h($employee['Employee']['name']); ?>
						&nbsp;
					</td></tr>
					<tr><td><?php echo __('Parent Employee'); ?></td>
					<td>
						<?php echo $this->Html->link($employee['ParentEmployee']['name'], array('controller' => 'employees', 'action' => 'view', $employee['ParentEmployee']['id'])); ?>
						&nbsp;
					</td></tr>
					<tr><td><?php echo __('Employee Number'); ?></td>
					<td>
						<?php echo h($employee['Employee']['employee_number']); ?>
						&nbsp;
					</td></tr>
					<tr><td><?php echo __('Identification Number'); ?></td>
					<td>
						<?php echo h($employee['Employee']['identification_number']); ?>
						&nbsp;
					</td></tr>
					<tr><td><?php echo __('Branch'); ?></td>
					<td>
						<?php echo $this->Html->link($employee['Branch']['name'], array('controller' => 'branches', 'action' => 'view', $employee['Branch']['id'])); ?>
						&nbsp;
					</td></tr>
					<tr><td><?php echo __('Department'); ?></td>
					<td>
						<?php echo $this->Html->link($employee['Department']['name'], array('controller' => 'departments', 'action' => 'view', $employee['Department']['id'])); ?>
						&nbsp;
					</td></tr>
					<tr><td><?php echo __('Designation'); ?></td>
					<td>
						<?php echo $this->Html->link($employee['Designation']['name'], array('controller' => 'designations', 'action' => 'view', $employee['Designation']['id'])); ?>
						&nbsp;
					</td></tr>
					<tr><td><?php echo __('Qualification'); ?></td>
					<td>
						<?php echo h($employee['Employee']['qualification']); ?>
						&nbsp;
					</td></tr>
					<tr><td><?php echo __('Joining Date'); ?></td>
					<td>
						<?php echo h($employee['Employee']['joining_date']); ?>
						&nbsp;
					</td></tr>
					<tr><td><?php echo __('Date Of Birth'); ?></td>
					<td>
						<?php echo h($employee['Employee']['date_of_birth']); ?>
						&nbsp;
					</td></tr>
					<tr><td><?php echo __('Pancard Number'); ?></td>
					<td>
						<?php echo h($employee['Employee']['pancard_number']); ?>
						&nbsp;
					</td></tr>
					<tr><td><?php echo __('Personal Telephone'); ?></td>
					<td>
						<?php echo h($employee['Employee']['personal_telephone']); ?>
						&nbsp;
					</td></tr>
					<tr><td><?php echo __('Office Telephone'); ?></td>
					<td>
						<?php echo h($employee['Employee']['office_telephone']); ?>
						&nbsp;
					</td></tr>
					<tr><td><?php echo __('Mobile'); ?></td>
					<td>
						<?php echo h($employee['Employee']['mobile']); ?>
						&nbsp;
					</td></tr>
					<tr><td><?php echo __('Personal Email'); ?></td>
					<td>
						<?php echo h($employee['Employee']['personal_email']); ?>
						&nbsp;
					</td></tr>
					<tr><td><?php echo __('Office Email'); ?></td>
					<td>
						<?php echo h($employee['Employee']['office_email']); ?>
						&nbsp;
					</td></tr>
					<tr><td><?php echo __('Residence Address'); ?></td>
					<td>
						<?php echo h($employee['Employee']['residence_address']); ?>
						&nbsp;
					</td></tr>
					<tr><td><?php echo __('Permenant Address'); ?></td>
					<td>
						<?php echo h($employee['Employee']['permenant_address']); ?>
						&nbsp;
					</td></tr>
					<tr><td><?php echo __('Maritial Status'); ?></td>
					<td>
						<?php echo h($employee['Employee']['maritial_status']); ?>
						&nbsp;
					</td></tr>
					<tr><td><?php echo __('Driving License'); ?></td>
					<td>
						<?php echo h($employee['Employee']['driving_license']); ?>
						&nbsp;
					</td></tr>
					<tr><td><?php echo __('Employment Status'); ?></td>
					<td>
						<?php echo h($employee['Employee']['employment_status']); ?>
						&nbsp;
					</td></tr>
					<tr><td><?php echo __('Is Approvar'); ?></td>
					<td>
						<?php echo h($employee['Employee']['is_approver']); ?>
						&nbsp;
					</td></tr>
					<tr><td><?php echo __('Prepared By'); ?></td>

				<td><?php echo h($employee['ApprovedBy']['name']); ?>&nbsp;</td></tr>
					<tr><td><?php echo __('Approved By'); ?></td>

				<td><?php echo h($employee['ApprovedBy']['name']); ?>&nbsp;</td></tr>
				<tr><td><?php echo __('Publish'); ?></td>

				<td>
				<?php if($employee['Employee']['publish'] == 1) { ?>
				<span class="fa fa-check"></span>
				<?php } else { ?>
				<span class="fa fa-close"></span>
				<?php } ?>&nbsp;</td>
			&nbsp;</td></tr>
				<tr><td><?php echo __('Soft Delete'); ?></td>

				<td>
				<?php if($employee['Employee']['soft_delete'] == 1) { ?>
				<span class="fa fa-check"></span>
				<?php } else { ?>
				<span class="fa fa-close"></span>
				<?php } ?>&nbsp;</td>
			&nbsp;</td></tr>
			<tr>
				<td colspan="2">
					<?php echo $this->Html->link('Delete This Record?', array('controller' => 'employees', 'action' => 'delete', $employee['Employee']['id']),array('class'=>'btn btn-sm btn-danger')); ?>
					<?php echo $this->Html->link('Edit This Record?', array('controller' => 'employees', 'action' => 'edit', $employee['Employee']['id']),array('class'=> 'btn btn-sm btn-warning')); ?>
				</td>
			</tr>

			</table>
			</div>
			<div class="col-md-5">
				<?php if($this->Session->read('User.employee_id') == $this->request->params['pass'][0]){ ?>	
					<table class="table table-responsive no-border">
						<tr>
							<td>
								<h4>Upload signature</h4>
								<?php 
								$sign = WWW_ROOT . 'img' . DS . $this->Session->read('User.company_id') . DS . 'signature' . DS . $this->Session->read('User.employee_id') . DS . 'sign.png';
								if(file_exists($sign)){
									echo "<div class='row'>";
									echo $this->Form->create('Employee',array('action'=>'upload','type'=>'file'),array('class'=>'form-control'));
									echo "<div class='col-md-12'>". $this->Html->Image($this->Session->read('User.company_id') . DS . 'signature' . DS . $this->Session->read('User.employee_id') . DS . 'sign.png',array('height'=>'60')) . "</div>";
									
									echo "<div class='col-md-8'><span class='control-fileupload'><i class='fa fa-file-o'></i>". $this->Form->input('signature',array('class'=>$block_class ,  'type'=>'file', 'label'=>'Update Your Signature', 'div'=>false , 'onchange'=>'$(this).prev("label").html($(this).val().substr(12));')) ."</span></div>";
									echo "<div class='col-md-4'>" . $this->Form->submit('Add',array('class'=>'btn btn-sm btn-success','style'=>'margin-top:15px')) ."</div>";
									echo $this->Form->end();
									echo "</div>";
								}else{
									echo "<div class='row'>";
									echo $this->Form->create('Employee',array('action'=>'upload','type'=>'file'),array('class'=>'form-control'));
									echo "<div class='col-md-8'><span class='control-fileupload'><i class='fa fa-file-o'></i>". $this->Form->input('signature',array('class'=>$block_class ,  'type'=>'file', 'label'=>'Upload Your Signature', 'div'=>false , 'onchange'=>'$(this).prev("label").html($(this).val().substr(12));')) ."</span></div>";
									echo "<div class='col-md-4'>" . $this->Form->submit('Add',array('class'=>'btn btn-sm btn-success','style'=>'margin-top:15px')) ."</div>";
									echo $this->Form->end();
									echo "</div>";
								}
								?>
							</td>
						</tr>
						<tr><td><h4>OR</h4></td></tr>
						<tr>
							<td>			
								<h4>Draw Signature</h4>	
								<div class="row">
									<div class="col-md-12 ">
										<label style="margin:8px 0px" >Draw/ fetch Your Signature Below</label>
										<div class="row">
											<div class="col-md-12">
												<div id="signed"></div>
												<div class="wrapper1" style="margin:0px">
													<canvas id="signature-pad" class="signature-pad" width=350 height=150></canvas>					        
												</div>
												<div class="btn-group ">
													<div class="btn tooltip1" onclick="copysign();" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title = "Copy Signature"><i class="fa fa-copy "></i></div>
													<div class="btn tooltip1" onclick="getsignature();" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title = "Fetch Saved Signature"><i class="fa fa-folder-open"></i></div>
													<div class="btn tooltip1" onclick="savesignature();" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title = "Save Signature"><i class="fa fa-save"></i></div>
													<div class="btn tooltip1" onclick="clearCanvas();" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title = "Clear Signature"><i class="fa fa-eraser"></i></div>							
												</div>
												<?php echo $this->Form->hidden('signature',array('id'=>'digital-signature'));?>
											</div>
											<div class="col-md-12">
												<ul class="list-group">
													<li class="list-group-item"><i class="fa fa-copy"></i> : Copy signature from earlier uploaded png file.</li>
													<li class="list-group-item"><i class="fa fa-folder-open"></i> : Copy signature from earlier saved image.</li>
													<li class="list-group-item"><i class="fa fa-save"></i> : Save current signature image.</li>
													<li class="list-group-item"><i class="fa fa-eraser"></i> : Clear Canvas.</li>
												</ul>
											</div>
										</div>
									</div>
								</div>
							</td>
						</tr>
						<tr>
							<td>
								<h4>Upload Profile Picture</h4>
								<?php 
								$profile = WWW_ROOT . 'img' . DS . $this->Session->read('User.company_id') . DS . 'profile' . DS . $this->Session->read('User.employee_id') . DS . 'profile.png';
								if(file_exists($profile)){
									echo "<div class='row'>";
									echo $this->Form->create('Employee',array('action'=>'profile','type'=>'file'),array('class'=>'form-control'));
									echo "<div class='col-md-12'>". $this->Html->Image($this->Session->read('User.company_id') . DS . 'profile' . DS . $this->Session->read('User.employee_id') . DS . 'profile.png',array('height'=>'60')) . "</div>";
									
									echo "<div class='col-md-8'><span class='control-fileupload'><i class='fa fa-file-o'></i>". $this->Form->input('profile',array('class'=>$block_class ,  'type'=>'file', 'label'=>'Update Your Profile Profile', 'div'=>false , 'onchange'=>'$(this).prev("label").html($(this).val().substr(12));')) ."</span></div>";
									echo "<div class='col-md-4'>" . $this->Form->submit('Add',array('class'=>'btn btn-sm btn-success','style'=>'margin-top:15px')) ."</div>";
									echo $this->Form->end();
									echo "</div>";
								}else{
									echo "<div class='row'>";
									echo $this->Form->create('Employee',array('action'=>'profile','type'=>'file'),array('class'=>'form-control'));
									echo "<div class='col-md-8'><span class='control-fileupload'><i class='fa fa-file-o'></i>". $this->Form->input('profile',array('class'=>$block_class ,  'type'=>'file', 'label'=>'Upload Your profile Profile', 'div'=>false , 'onchange'=>'$(this).prev("label").html($(this).val().substr(12));')) ."</span></div>";
									echo "<div class='col-md-4'>" . $this->Form->submit('Add',array('class'=>'btn btn-sm btn-success','style'=>'margin-top:15px')) ."</div>";
									echo $this->Form->end();
									echo "</div>";
								}
								?>
							</td>
						</tr>
						<tr><td>Image type : png | Max Size : 20kb</td></tr>
						<tr><td>Image type : png | Max Size : 800kb</td></tr>					
				</table>
				<?php } ?>
			</div>
</div>

</div>
<?php echo $this->Js->writeBuffer();?>

</div>
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
		getsignature();
		<?php if($signAvailable == true){ ?>
			copysign();
		<?php } ?>

		$('.tooltip1').tooltip();
		$("#pdf-spin").hide();
		$('#pdf-download').modal('show');        
		$('#DocumentDownloadAddForm').validate();
	});
</script>

<script>$.ajaxSetup({beforeSend:function(){$("#busy-indicator").show();},complete:function(){$("#busy-indicator").hide();}});</script>

