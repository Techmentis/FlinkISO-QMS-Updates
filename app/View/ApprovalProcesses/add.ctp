<?php echo $this->Html->script(array('jquery.validate.min', 'jquery-form.min')); ?>
<?php echo $this->fetch('script'); ?><div id="approvalProcesses_ajax">
	<?php echo $this->Session->flash();?>	

	<div class="approvalProcesses ">
		<?php echo $this->element('nav-header-lists',array('postData'=>array('pluralHumanName'=>'Approval Processes','modelClass'=>'ApprovalProcess','options'=>array(),'pluralVar'=>'approvalProcesses'))); ?>

		<div class="nav panel panel-default">
			<div class="approvalProcesses form col-md-12">
				<?php echo $this->Form->create('ApprovalProcess',array('role'=>'form','class'=>'form')); ?>
				<div class="row">
					<?php
					echo "<div class='col-md-12'>".$this->Form->input('title',array('class'=>'form-control',)) . '</div>'; 
					echo "<div class='col-md-12'>".$this->Form->input('process_description',array('class'=>'form-control',)) . '</div>'; 
					echo "<div class='col-md-12'>".$this->Form->input('applicable_to',array('name'=>'data[ApprovalProcess][applicable_to][]', 'class'=>'form-control','multiple','options'=>$customTables)) . '</div>'; 
					?>
				</div>
				<div class="row">
					<div class="col-md-12">
						<h4>Add Steps</h4>
						<div class="table-responsive">
							<table class="table table-bordered table-condenced" id="apptble">
								<thead>
									<tr>
										<th rowspan="2" width="80">#</th>
										<th rowspan="2">Title</th>
										<th rowspan="2">Comment</th>
										<th colspan="7">Send To</th>
										<th colspan="2">Settings</th>
										<th colspan="2">Ignore</th>							
									</tr>
									<tr>
										<th>Reviwer</th>						
										<th>Approver</th>
										<th>HOD</th>
										<th>Admin</th>
										<th>Designation</th>										
										<th>Users</th>
										<th>Publisher</th>
										<th width="220">Mode</th>
										<th width="220">Type</th>
										<th width="180">Department </th>
										<th width="180">Branch </th>
										<th></th>
									</tr>
								</thead>
								<tbody>
									<?php $i = 1 ;
									$required = true;
									?>
									<tr class="colrows thiscol<?php echo $i?>">
										<td><?php echo $this->Form->input('ApprovalStep.steps.'.$i.'.process_step',array('class'=>'form-control','label'=>false,'default'=>$i,'required'=>$required)) ?></td>
										<td><?php echo $this->Form->input('ApprovalStep.steps.'.$i.'.title',array('class'=>'form-control','label'=>false,'default'=>'Step-'.$i,'required'=>$required)) ?></td>
										<td><?php echo $this->Form->input('ApprovalStep.steps.'.$i.'.comments',array('rows'=>2, 'class'=>'form-control','label'=>false,'required'=>$required)) ?></td>
										
										<td><?php echo $this->Form->input('ApprovalStep.steps.'.$i.'.send_to_reviwers',array('checkbox'=>'checkbox','label'=>false, 'checked'=>true, 
										'onchange'=>'udpatecheck('.$i.',"reviewer",this);')) ?></td>										
										<td><?php echo $this->Form->input('ApprovalStep.steps.'.$i.'.send_to_approvers',array('checkbox'=>'checkbox','label'=>false,
										'onchange'=>'udpatecheck('.$i.',"publisher",this);')) ?></td>
										<td><?php echo $this->Form->input('ApprovalStep.steps.'.$i.'.send_to_department_hod',array('checkbox'=>'form-control','label'=>false,
										'onchange'=>'udpatecheck('.$i.',"hods",this);')) ?></td>										
										<td><?php echo $this->Form->input('ApprovalStep.steps.'.$i.'.send_to_admins',array('checkbox'=>'checkbox','label'=>false,
										'onchange'=>'udpatecheck('.$i.',"admins",this);')) ?></td>
										
										<td><?php echo $this->Form->input('ApprovalStep.steps.'.$i.'.send_to_designation',array('class'=>'form-control', 'options'=>$PublishedDesignationList,  'label'=>false,
										'onchange'=>'udpatecheck('.$i.',"designation",this);')) ?></td>										
										<td><?php echo $this->Form->input('ApprovalStep.steps.'.$i.'.send_to_users[]',array('name'=>'data[ApprovalStep][steps]['.$i.'][send_to_users][]', 'class'=>'form-control', 'multiple', 'options'=>$PublishedUserList, 'label'=>false,'onchange'=>'udpatecheck('.$i.',"users",this);')) ?></td>	

										<td><?php echo $this->Form->input('ApprovalStep.steps.'.$i.'.send_to_publishers',array('checkbox'=>'checkbox','label'=>false,
										'onchange'=>'udpatecheck('.$i.',"publisher",this);')) ?></td>			

										<td><?php echo $this->Form->input('ApprovalStep.steps.'.$i.'.approval_mode',array('class'=>'','type'=>'radio', 'legend'=>false, 'options'=>array(0=>'View Only',1=>'Edit'),'default'=>1)) ?></td>
										<td><?php echo $this->Form->input('ApprovalStep.steps.'.$i.'.approval_type',array('class'=>'','type'=>'radio', 'legend'=>false, 'options'=>array(0=>'All',1=>'Any'),'default'=>1));?></td>

										<td><?php echo $this->Form->input('ApprovalStep.steps.'.$i.'.ignore_department',array('class'=>'','type'=>'radio', 'legend'=>false, 'options'=>array(0=>'Yes',1=>'No'),'default'=>1));?></td>
										<td><?php echo $this->Form->input('ApprovalStep.steps.'.$i.'.ignore_branch',array('class'=>'','type'=>'radio', 'legend'=>false, 'options'=>array(0=>'Yes',1=>'No'),'default'=>1));?></td>
									</tr>
									
								</tbody>
								<footer>
									<tr><td colspan="14" class="text-right"><?php echo $this->Html->link('Add','#',array('onClick'=>'addrows('.$i.')'))?></td></tr>
								</footer>
							</table>
						</div>
					</div>
				</div>
				<?php
				echo $this->Form->input('id');
				echo $this->Form->hidden('num_cnt',array('default'=>$i,'type'=>'number'));
				echo $this->Form->hidden('History.pre_post_values', array('value'=>json_encode($this->data)));
				echo $this->Form->input('branchid', array('type' => 'hidden', 'value' => $this->Session->read('User.branch_id')));
				echo $this->Form->input('departmentid', array('type' => 'hidden', 'value' => $this->Session->read('User.department_id')));
				echo $this->Form->input('master_list_of_format_id', array('type' => 'hidden', 'value' => $documentDetails['MasterListOfFormat']['id']));
				?>

			
			<div class="">

				<?php echo $this->Form->submit(__('Submit'), array('div' => false, 'class' => 'btn btn-primary btn-success','id'=>'submit_id')); ?>
				<?php echo $this->Html->image('indicator.gif', array('id' => 'submit-indicator')); ?>
				<?php echo $this->Form->end(); ?>

				<?php echo $this->Js->writeBuffer();?>
			</div>
		</div>
		<script> 
			$("[name*='date']").datepicker({
				changeMonth: true,
				changeYear: true,
				dateFormat:'yy-mm-dd',
			}); </script>
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

		function udpatecheck(i, selectedchoice, el) {
		    var $container = $('.thiscol' + i);

		    // If checkbox triggered
		    if ($(el).is(':checkbox')) {

		        // Uncheck all checkboxes
		        $container.find('input[type="checkbox"]').prop('checked', false);

		        // Reset all selects
		        $container.find('select')
		            .val('')
		            .trigger('chosen:updated');

		        // Optionally re-check current one (if needed)
		        $(el).prop('checked', true);
		    }

		    // If select triggered
		    else if ($(el).is('select')) {

		        var currentVal = $(el).val();

		        // Uncheck all checkboxes
		        $container.find('input[type="checkbox"]').prop('checked', false);

		        // Reset ALL selects first
		        $container.find('select')
		            .not(el)
		            .val('')
		            .trigger('chosen:updated');

		        // Restore current select value
		        $(el)
		            .val(currentVal)
		            .trigger('chosen:updated');
		    }
		}		

		$().ready(function() {
			jQuery.validator.addMethod("greaterThanZero", function(value, element) {
				return this.optional(element) || (parseFloat(value) > 0);
			}, "Please select the value");

			$('#ApprovalProcessAddForm').validate({        	
				rules: {

				}
			}); 
			
			$("#submit-indicator").hide();
			$("#submit_id").click(function(){
				if($('#ApprovalProcessAddForm').valid()){
					$("#submit_id").prop("disabled",true);
					$("#submit-indicator").show();
					$('#ApprovalProcessAddForm').submit();
				}

			});
		});

		function addrows(i){
			var i = parseFloat($("#ApprovalProcessNumCnt").val());
			$.ajax({
	            url: "<?php echo Router::url('/', true); ?><?php echo $this->request->params['controller'] ?>/addrows/"+i,
	            success: function(data, result) {                                   
	                $("#apptble").append(data);	
	                i = i+1;
	                $("#ApprovalProcessNumCnt").val(i);
	                $("select").chosen();
	            },
	        }); 
		}
	</script>