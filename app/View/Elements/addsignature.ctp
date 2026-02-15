<?php echo $this->Session->flash();?>
<style type="text/css">
	.wrapper1,.wrapper2 {border-radius: 4px; border: 2px dashed #ccc;position: relative;width: 250px;height: 152px;-moz-user-select: none;-webkit-user-select: none;-ms-user-select: none;user-select: none;margin: auto; }.signature-pad {position: absolute;left: 0;top: 0;width:250px;height:250px;}
	#signatureModal, .modal-dialog{width: 250px;height: 200px}#clear{width: 250px;border-radius: 0 0 4px 4px;}#fsubmit{margin-bottom: 20px !important}
	.requiredsign{border: 2px dashed red}
	.chosen-container, .chosen-container-single, .chosen-select{min-width: 1px}
</style>
<?php echo $this->fetch('script'); ?>
<div class="modal fade " id="add-sign" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-wide">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" id="close-signature-modal" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title"><?php echo __('Add your signature'); ?></h4>				
			</div>
			<div class="modal-body">
				<div class="hide" id="add-sign-3"></div>
				<div id="add-sign-2">	
					<div class="row">							
						<?php echo $this->Form->create('AddSignature',array('id'=>'addsignform'),array('default'=>false)); ?>
						<div class="col-md-12"> <?php echo $this->Form->input('user_password',array('label'=>'Enter Password for '.$employee['Employee']['name'], 'class'=>'form-control','type'=>'password','required'=>'required'));?>
						<?php echo $this->Form->hidden('fieldid',array('class'=>'form-control','default'=>$fieldid, 'required'=>'required'));?>
						<?php echo $this->Form->hidden('employee_id',array('class'=>'form-control','default'=>$employee_id, 'required'=>'required'));?>
						<i class="fa fa-refresh fa-spin" id="sign-spin"></i>
						</div>
						<div class="col-md-12 text-center">
							<br />
							<?php echo $this->Form->submit(__('Fetch'),array('id'=>'sign-submit', 'async' => 'false', 'class'=>'btn btn-md btn-success')); ?>
							<?php echo $this->Form->end(); ?>
						</div>
					</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div id="loadsign" class="text-center">
								<?php 
									$img = WWW_ROOT. DS. 'img'. DS . $this->Session->read('User.company_id'). DS .'signature'. DS. $employee_id. DS. 'sign.png';
									if(file_exists($img)){
										$imgURL = Router::url('/', true).'/img/' . $this->Session->read('User.company_id') .'/signature/'. $employee_id. '/sign.png';
										echo '<center><img src="'.$imgURL.'" width="100"></center>';
									}else if($employee['Employee']['signature']){
										echo '<img src="' .$employee['Employee']['signature'].'" width="100">';
									}else{
										"Signature not availavle";
									}
								?>
							<br />
							<br />
							<?php echo $this->Form->submit(__('Add'),array('id'=>'sign-add','data-dismiss'=>'modal', 'aria-hidden'=>'true', 'async' => 'false', 'class'=>'btn btn-md btn-success')); ?>
							</div>
							<p id="sign-menu"></p>
						</div>
					</div>
			</div>
			<div class="modal-footer">
				<div class="row">
					<div class="col-md-12 text-center">	
					<div id="response-message"></div>
					</div>
				</div>
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
			}else if(element['context']['className'] == 'radio error'){
				$(element).next('legend').addClass('error');
			}else{
				$(element).after(error); 
			}
		},
		submitHandler: function(form) {
			$("#addsignform").ajaxSubmit({
				url: "<?php echo Router::url('/', true); ?><?php echo $this->request->params['controller'] ?>/addsignature",
				type: 'POST',
				target: '#add-sign-3',
				beforeSend: function(){					
					$("#sign-spin").show();
					$("#add-sign-3").hide();
				},
				complete: function(data) {
					let res = $.trim(data.responseText);
					$("#sign-spin").hide();
					if(res == "Proceed"){
						$("#add-sign-2").hide();
						$("#<?php echo $fieldid;?>").val("<?php echo $employee_id;?>").trigger('chosen:updated');						
						$("#response-message").html('Signature fetched successfully. It will be added in View pages.');
						$("input[type=submit]").show();
						$("#loadsign").show();						
					}
					if(res == "Wrong password"){
						$("#loadsign").hide();
						$("#add-sign-3").show();
						$("#response-message").html('Incorrect Password');
						$("#<?php echo $fieldid;?>").val(-1).trigger('chosen:updated');
					}

					if(res == "Wrong user"){
						$("#loadsign").hide();
						$("#add-sign-3").show();
						$("#response-message").html('User not found');
						$("#<?php echo $fieldid;?>").val(-1).trigger('chosen:updated');
					}

					if(res == "invalid"){
						$("#loadsign").hide();
						$("#add-sign-3").show();
						$("#response-message").html('Invalid Request');
						$("#<?php echo $fieldid;?>").val(-1).trigger('chosen:updated');
					}

					if(res == "Signature not available"){
						$("#loadsign").hide();
						$("#add-sign-3").show();
						$("#response-message").html('Signature not available for this user.');
						$("#<?php echo $fieldid;?>").val(-1).trigger('chosen:updated');
					}
					

					
				},
				error: function(request, status, error) {
					
				},success: function(q, data) {
					$("#sign-spin").hide();					
				}
			});
		}
	});

	$().ready(function(){
		$("select").chosen();
		$("#Approval<?php echo $fieldid;?>").val(-1).trigger('chosen:updated');
		// $('.tooltip1').tooltip();
		$("#sign-spin").hide();
		$('#add-sign').modal('show');        
		$('#addsignform').validate();
		$("#loadsign").hide();

		$("#close-signature-modal").on('click',function(){
			$(".btn").show();
		})
	});
</script>
