
<?php echo $this->Html->script(array('jquery.validate.min', 'jquery-form.min')); ?>
<?php echo $this->fetch('script'); ?>
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
		$("#ProcessFileType").val(filetype);	
		$("#filetypediv").hide();
	}

	$().ready(function(){
		$("#filetypediv").hide();
		$("#ProcessFile").on('change',function(){
			
			let pathext = this.value;
			const pathfile = pathext.split(".");
			const arr = pathfile.length;
			const ext = pathfile[arr-1];
			
			let path = pathfile[arr-2];
			const file = path.split("\\");
			const filename  = file[file.length - 1];
			
			$("#ProcessName").val(filename);
			addfiletype(ext);
		});
	})

</script>
<div id="Process_div">	
	<?php echo $this->Session->flash();?>
	<div class="Process">
		<?php echo $this->element('nav-header-lists',array('postData'=>array('friendlyName'=>'Processes','pluralHumanName'=>'Process','modelClass'=>'Process','options'=>array(),'pluralVar'=>'processes'))); ?>

		<?php echo $this->Form->create('Process',array('role'=>'form','class'=>'form','type'=>'file')); ?>		

		<div class="panel panel-default">
			<div class="panel-body">				
				<div class="row">
					<?php echo "<div class='col-md-12'>". $this->Form->input('qc_document_id',array('class'=>'form-control')) . "</div>";?>
					<?php echo "<div class='col-md-12'>". $this->Form->input('name',array('class'=>'form-control')) . "</div>";?>
					<?php echo "<div class='col-md-12'>". $this->Form->input('process_definition',array('class'=>'form-control','required' , )) . "</div>";?>
					<?php echo "<div class='col-md-12'>". $this->Form->input('process_objective_and_metrics',array('class'=>'form-control','required' , )) . "</div>";?>
					<?php echo "<div class='col-md-12'>". $this->Form->input('process_owners',array(
						'name'=>'data[Process][process_owners][]',
						'type'=>'select',
						'selected'=>json_decode($this->data['Process']['process_owners']),
						'options'=> $departments , 'class'=>'form-control select','multiple','required' , )) . "</div>";?>
					<?php echo "<div class='col-md-12'>". $this->Form->input('applicable_to_branches',array(
						'name'=>'data[Process][applicable_to_branches][]',
						'type'=>'select',
						'selected'=>json_decode($this->data['Process']['applicable_to_branches']),
						'options'=> $branches , 'class'=>'form-control select','multiple','required' , )) . "</div>";?>
						<?php echo "<div class='col-md-12'>". $this->Form->input('additional_responsibilities',array('class'=>'form-control',)) . "</div>";?>
						<?php echo "<div class='col-md-12'>". $this->Form->input('input_processes',array(
							'name'=>'data[Process][input_processes][]',
							'type'=>'select',
							'selected'=>json_decode($this->data['Process']['input_processes']),
							'options'=> $Processs , 'class'=>'form-control select','multiple',)) . "</div>";?>
						<?php echo "<div class='col-md-12'>". $this->Form->input('output_processes',array(
							'name'=>'data[Process][output_processes][]',
							'type'=>'select',
							'selected'=>json_decode($this->data['Process']['output_processes']),
							'options'=> $Processs , 'class'=>'form-control select','multiple',)) . "</div>";?>
							<?php echo "<div class='col-md-12'>". $this->Form->input('process_output',array('class'=>'form-control','required' , )) . "</div>";?>
							<?php echo "<div class='col-md-12'>". $this->Form->input('risks_and_opportunities',array('class'=>'form-control',)) . "</div>";?>
							<?php echo "<div class='col-md-12'>". $this->Form->input('standards',array(
								'name'=>'data[Process][standards][]',
								'type'=>'select',
								'selected'=>json_decode($this->data['Process']['standards']),
								'options'=> $standards , 'class'=>'form-control select','multiple','required' , )) . "</div>";?>
							<?php echo "<div class='col-md-12'>". $this->Form->input('clauses',array(
								'name'=>'data[Process][clauses][]',
								'type'=>'select',
								'selected'=>json_decode($this->data['Process']['clauses']),
								'options'=> $clauses , 'class'=>'form-control select','multiple','required' , )) . "</div>";?>
							<?php
							echo $this->Form->input('id');
							// echo $this->Form->hidden('qc_document_id',array('default'=>$this->request->params['named']['qc_document_id']));
							echo $this->Form->hidden('History.pre_post_values', array('value'=>json_encode($this->data)));
							echo $this->Form->input('branchid', array('type' => 'hidden', 'value' => $this->Session->read('User.branch_id')));
							echo $this->Form->input('departmentid', array('type' => 'hidden', 'value' => $this->Session->read('User.department_id')));						
							?>
						</div>
					</div>
				</div>
				<?php  $i = 0; foreach($linkedTables as $linkedTable){ ?>
					<div><h4><?php echo Inflector::humanize($linkedTable['CustomTable']['name']);?> <small><?php echo $linkedTable['CustomTable']['name'];?></small></h4></div>	
					<?php 
					if($this->request->data[Inflector::classify($linkedTable['CustomTable']['table_name'])]){
						if($linkedTable['CustomTable']['form_layout'] == 2){
							echo "<div class='panel panel-default'><div class='panel-body no-padding'>";
							echo "<table class='table table-responsive table-bordered'><tr><th>Name/title</th>";
							foreach(json_decode($linkedTable['CustomTable']['fields'],true) as $field){
								echo "<th>" . Inflector::humanize($field['field_name']) . "</th>";
							}
							echo "</tr>";
						}
						foreach($this->request->data[Inflector::classify($linkedTable['CustomTable']['table_name'])] as $cdata){ 
							if($linkedTable['CustomTable']['form_layout'] == 2){				
								echo "<tr id='".$cdata['id']."'></tr>";
							}else{
								echo "<div class='panel panel-default'><div class='panel-body'>";
								echo "<div id='".$cdata['id']."'></div>";
								echo "</div></div>";
							}
							?>
							<script type="text/javascript">
								$("#<?php echo $cdata['id']?>").load("<?php echo Router::url('/', true); ?><?php echo $linkedTable['CustomTable']['table_name']?>/edit/<?php echo $cdata['id'];?>/<?php echo $i;?>",function(response,status,xhr){
									if(status == 'success'){
										$('select').chosen();
									}
								});
							</script>
							<?php $i++;
							if($linkedTable['CustomTable']['form_layout'] == 2){ 
								echo "</table>";			
							}		
						}?>
						<div id="<?php echo $linkedTable['CustomTable']['table_name']?>"></div>
						<script type="text/javascript">
							$("#<?php echo $linkedTable['CustomTable']['table_name']?>").load("<?php echo Router::url('/', true); ?><?php echo $linkedTable['CustomTable']['table_name']?>/add/<?php echo $i;?>");
						</script>
					<?php } else{ ?>
						<div id="<?php echo $linkedTable['CustomTable']['table_name']?>"></div>
						<script type="text/javascript">
							$("#<?php echo $linkedTable['CustomTable']['table_name']?>").load("<?php echo Router::url('/', true); ?><?php echo $linkedTable['CustomTable']['table_name']?>/add/<?php echo $i;?>");
						</script>
					<?php	} } ?>
					<?php echo "</div></div>"; ?>					

					<div class="">
						<?php echo $this->element('approval_form',array('approval'=>$approval));?>
						<?php echo $this->element('approval_history',array('approval'=>$approval,'approvals'=>$approvals,'current_approval'=>$this->request->params['named']['approval_id'],'approvalComments',$approvalComments));?>
						<?php echo $this->Form->submit(__('Submit'), array('div' => false, 'class' => 'btn btn-primary btn-success','id'=>'submit_id')); ?>
						<?php echo $this->Html->image('indicator.gif', array('id' => 'submit-indicator')); ?>
						<?php echo $this->Form->end(); ?>
						<?php echo $this->Js->writeBuffer();?>						
					</div>
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
					}
				});
								
				$().ready(function() {

					$('select').chosen();

					jQuery.validator.addMethod("greaterThanZero", function(value, element) {
						return this.optional(element) || $("#"+element.id+" option:selected").text() != 'Select';
					}, "Please select the value");


					$('#ProcessAddForm').validate();

					$('select').each(function() {	
						if($(this).prop('required') == true){
							$(this).rules('add', {
								greaterThanZero: true
							});	
						}
						
					});        
					
					$("#submit-indicator").hide();
					$("#submit_id").click(function(){
						if($('#ProcessAddForm').valid()){
							$("#submit_id").prop("disabled",true);
							$("#submit-indicator").show();
							$('#ProcessAddForm').submit();
						}
					});


					$("#ProcessQcDocumentId").on('change',function(){
						$.ajax({
							url: "<?php echo Router::url('/', true); ?>processes/get_document_details/"+$("#ProcessQcDocumentId").val(),
							type: "GET",
							beforeSend: function( xhr ) {
								console.log(xhr);
							},                    
							success: function (data) {
								var obj = jQuery.parseJSON(data);
								console.log(obj.branches);
								$("#ProcessName").val(obj.title);
								$("#ProcessApplicableToBranches").val(jQuery.parseJSON(obj.branches)).trigger("chosen:updated");
								$("#ProcessProcessOwners").val(jQuery.parseJSON(obj.departments)).trigger("chosen:updated");
								$("#ProcessStandards").val(obj.standard_id).trigger("chosen:updated");
								$("#ProcessClauses").val(obj.clause_id).trigger("chosen:updated");
							},
							error: function (err) {
								console.log(err);
							}
						}); 
					})
				});
			</script>
