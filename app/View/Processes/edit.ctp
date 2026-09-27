
<?php echo $this->Html->script(array('jquery.validate.min', 'jquery-form.min')); ?>
<?php echo $this->fetch('script'); ?>
<div id="Process_div">	
	<?php echo $this->Session->flash();?>	
	<div class="Process">
		<?php echo $this->element('nav-header-lists',array('postData'=>array('friendlyName'=>'Processes','pluralHumanName'=>'Processes','modelClass'=>'Process','options'=>array(),'pluralVar'=>'processes'))); ?>

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
									return this.optional(element) || (parseFloat(value) != -1);
								}, "Please select the value");

								$('#ProcessEditForm').validate();

								$('select').each(function() {	
									if($(this).prop('required') == true){
										$(this).rules('add', {
											greaterThanZero: true
										});	
									}
									
								});        
								
								$("#submit-indicator").hide();
								$("#submit_id").click(function(){
									if($('#ProcessEditForm').valid()){
										$("#submit_id").prop("disabled",true);
										$("#submit-indicator").show();
										$('#ProcessEditForm').submit();
									}
								});
							});
						</script>
						<?php if($this->data['CustomTable']){ ?>	
							<div class="row">
								<?php foreach ($this->data['CustomTable'] as $customTable): 
			// Configure::write('debug',1);
									debug($customTable);
									?>
									<div class="col-md-6">
										<div class="box box-default">
											<div class="box-header">
												<h4 class="box-title">
													<?php if($customTable['publish'] == 1 && $customTable['table_locked'] == 0 && $customTable['QcDocument']['publish'] == 1){
														echo "<span class='text-success'><i class='fa fa-check'></i></span>";
														$titleClass = 'text-success';
														$btnClass = 'success';	
													}else{
														echo "<span class='text-danger'><i class='fa fa-exclamation-triangle'></i></span>";	
														$titleClass = 'text-danger';
														$btnClass = 'danger';												

													}
												?>&nbsp;
												<?php 
												
												$doc = "<strong class='".$titleClass."' >".$this->data['Process']['name'] ."</strong> <br /> <small>". $this->data['Process']['file_name'] ."</small>";
												echo $this->Html->link($doc,array('controller'=>'processes','action'=>'view',$this->data['Process']['id']),array('target'=>'_blank','escape'=>false));	
							// echo h($customTable['Process']['name']);
											?>&nbsp;							
										</h4>
										<span class="pull-right"><span class="badge btn-<?php echo $btnClass;?>"><?php echo $customTable['linked'];?></span></span>
									</div>
									<div class="box-body">
										
										<?php echo h(substr($customTable['description'], 0,120)); ?> ...&nbsp;

									</div>
									<div class="box-footer text-right">	
										<div class="pull-left"><p style="padding-top:10px"><small><?php echo h($customTable['table_name']) ?></small></p></div>					
										<div class="btn-group">
											<?php 
											if($customTable['publish'] == 1 && $customTable['table_locked'] == 0){
												echo $this->Html->link('<i class="fa fa-plus-square-o fa-lg text-info"></i>',array('controller'=>$customTable['table_name'], 'action'=>'add', 'custom_table_id'=>$customTable['id'],'qc_document_id'=>$customTable['qc_document_id'],'process_id'=>$customTable['process_id']),array('class'=>'tooltip1 btn btn-sm btn-default', 'escape'=>false, 'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'left', 'title'=> 'Goto Add page'));
											}?>

											<?php echo $this->Html->link('<i class="fa fa-table fa-lg text-info"></i>',array('controller'=>$customTable['table_name'],'action'=>'index','custom_table_id'=>$customTable['id'],'qc_document_id'=>$customTable['qc_document_id'],'process_id'=>$customTable['process_id']),array('class'=>'tooltip1 btn btn-sm btn-default', 'escape'=>false, 'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'left', 'title'=> 'Goto index page'));?>
											
											
											<?php echo $this->Html->link('<i class="fa fa-gears fa-lg text-success"></i>',array('action'=>'view',$customTable['id']),array('class'=>'tooltip1 btn btn-sm btn-default', 'escape'=>false, 'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'left', 'title'=> 'View'));?>
											
											
											<?php
											echo $this->Html->link('<i class="fa fa-bar-chart fa-lg text-info"></i>',array('controller'=>$customTable['table_name'], 'action'=>'reports','custom_table_id'=>$customTable['id']),
												array('class'=>'tooltip1 btn btn-sm btn-default','escape'=>false,'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'bottom', 'title'=> 'Reports'));
												?>
											</div>
											
											
											
											
										</div>
									</div>
								</div>
							<?php endforeach; ?>
						<?php }else{ ?>
							
						<?php } ?>	
