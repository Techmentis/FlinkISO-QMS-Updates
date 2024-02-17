<?php echo $this->Html->script(array('jquery.validate.min', 'jquery-form.min')); ?>
<?php echo $this->fetch('script'); ?>
<div id="records_ajax">
	<?php echo $this->Session->flash();?>	

	<div class="records ">
		<?php echo $this->element('nav-header-lists',array('postData'=>array('pluralHumanName'=>'Records','modelClass'=>'Record','options'=>array(),'pluralVar'=>'records'))); ?>

		<div class="nav panel panel-default">
			<div class="records form col-md-12">
				<?php echo $this->Form->create('Record',array('role'=>'form','class'=>'form')); ?>
				<div class="row">
					<div class="col-md-12">
						<?php echo $this->element('qc_doc_header',array('document',$document));?>
					</div>
					<?php
					echo "<div class='col-md-12'>".$this->Form->hidden('qc_document_id',array('class'=>'form-control', 'style'=>'','default'=>$this->request->params['pass'][0])) . '</div>';  ?>
				</div>

				<div class="row">
					<?php
					echo $this->Form->input('id');
					echo $this->Form->hidden('created_by',array('default'=>$this->Session->read('User.id')));
					echo $this->Form->hidden('modified_by',array('default'=>$this->Session->read('User.id')));
					echo $this->Form->hidden('prepared_by',array('default'=>$this->Session->read('User.employee_id')));
					echo $this->Form->hidden('History.pre_post_values', array('value'=>json_encode($this->data)));
					echo $this->Form->input('branchid', array('type' => 'hidden', 'value' => $this->Session->read('User.branch_id')));
					echo $this->Form->input('departmentid', array('type' => 'hidden', 'value' => $this->Session->read('User.department_id')));		
					?>

				</div>
				<div class="hide"></div>
				<div class="">
					<?php
					if ($showApprovals && $showApprovals['show_panel'] == true) {
						echo $this->element('approval_form');
					} else {
						echo $this->Form->input('publish', array('label' => __('Publish')));
					}
					?>
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
				
			},
		});
		
		$().ready(function() {
			jQuery.validator.addMethod("greaterThanZero", function(value, element) {
				return this.optional(element) || $("#"+element.id+" option:selected").text() != 'Select';
			}, "Please select the value");

			$('#RecordAddForm').validate({        	
				rules: {
					
				}
			}); 
			
			$("#submit-indicator").hide();
			$("#submit_id").click(function(){
				if($('#RecordAddForm').valid()){
					$("#submit_id").prop("disabled",true);
					$("#submit-indicator").show();
					$('#RecordAddForm').submit();
				}

			});
		});
	</script>
