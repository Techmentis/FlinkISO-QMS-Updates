<?php echo $this->element('checkbox-script'); ?>
<div  id="main">
	<?php echo $this->Session->flash();?>	
	<div class="employees ">
		<?php echo $this->element('nav-header-lists',array('postData'=>array('pluralHumanName'=>'Employees','modelClass'=>'Employee','options'=>array("sr_no"=>"Sr No","name"=>"Name","employee_number"=>"Employee Number","identification_number"=>"Identification Number","qualification"=>"Qualification","joining_date"=>"Joining Date","date_of_birth"=>"Date Of Birth","pancard_number"=>"Pancard Number","personal_telephone"=>"Personal Telephone","office_telephone"=>"Office Telephone","mobile"=>"Mobile","personal_email"=>"Personal Email","office_email"=>"Office Email","residence_address"=>"Residence Address","permenant_address"=>"Permenant Address","maritial_status"=>"Maritial Status","driving_license"=>"Driving License","employment_status"=>"Employment Status","is_approver"=>"Is Approvar"),'pluralVar'=>'employees'))); ?>


		<div class="table-responsive" style="overflow:scroll">
			<?php echo $this->Form->create(array('class'=>'no-padding no-margin no-background'));?>				
			<table cellpadding="0" cellspacing="0" class="table table-responsive table-hover index" id="exportcsv">
				<tr>										
					<th><?php echo $this->Paginator->sort('name'); ?></th>
					<th><?php echo $this->Paginator->sort('employee_number'); ?></th>
					<th><?php echo $this->Paginator->sort('parent_id'); ?></th>					
					<th><?php echo $this->Paginator->sort('branch_id'); ?></th>
					<th><?php echo $this->Paginator->sort('department_id'); ?></th>
					<th><?php echo $this->Paginator->sort('designation_id'); ?></th>					
					<th><?php echo $this->Paginator->sort('employment_status'); ?></th>
					<th><?php echo $this->Paginator->sort('is_hod','HOD'); ?></th>
					<th><?php echo $this->Paginator->sort('is_approver','Approver'); ?></th>
					<th><?php echo $this->Paginator->sort('publish'); ?></th>
					<th>Action</th>				
				</tr>
				<?php if($employees){ ?>
					<?php foreach ($employees as $employee): ?>
						<tr class="on_page_src" onclick="addrec('<?php echo $employee['Employee']['id'];?>')" id="<?php echo $employee['Employee']['id'];?>_tr">
							<td><?php echo h($employee['Employee']['name']); ?>&nbsp;</td>
							<td><?php echo h($employee['Employee']['employee_number']); ?>&nbsp;</td>
							<td>

								<?php 
								if($employee['ParentEmployee']['name']){
									echo $employee['ParentEmployee']['name'];
								}else{
									echo $this->form->input('parent_id',array('label'=>false,'style'=>'width:100%','id'=>'ParentEmployee'.$x, 'onChange'=>'update("'.$employee['Employee']['id'].'",this.value)', 'options'=>$parents));
								}?>
								
							</td>
							<td>
								<?php echo $this->Html->link($employee['Branch']['name'], array('controller' => 'branches', 'action' => 'view', $employee['Branch']['id'])); ?>
							</td>
							<td>
								<?php echo $this->Html->link($employee['Department']['name'], array('controller' => 'departments', 'action' => 'view', $employee['Department']['id'])); ?>
							</td>
							<td>
								<?php echo $this->Html->link($employee['Designation']['name'], array('controller' => 'designations', 'action' => 'view', $employee['Designation']['id'])); ?>
							</td>		
							<td><?php echo h($customArray['employmentStatuses'][$employee['Employee']['employment_status']]); ?>&nbsp;</td>
							<td><?php echo ($employee['Employee']['is_hod']?'<span class="fa fa-check text-success"></span>':'<span class="fa fa-close text-danger">');?></td>
								<td><?php echo ($employee['Employee']['is_approver']?'<span class="fa fa-check text-success"></span>':'<span class="fa fa-close text-danger">');?></td>
									<td width="60"><?php echo ($employee['Employee']['publish']?'<span class="fa fa-check text-success"></span>':'<span class="fa fa-close text-danger">');?></td>
										<td class=" actions" style="width: 160px;">
											<?php echo $this->element('actions', array('user'=>$employee['Employee']['user'], 'created' => $employee['Employee']['created_by'], 'postVal' => $employee['Employee']['id'], 'softDelete' => $employee['Employee']['soft_delete'])); ?>
										</td>
									</tr>
								<?php endforeach; ?>
							<?php }else{ ?>
								<tr><td colspan=114>No results found</td></tr>
							<?php } ?>
						</table>
						<?php echo $this->Form->end();?>			
					</div>
					<p>
						<?php
						echo $this->Paginator->options(array(
						));
						
						echo $this->Paginator->counter(array(
							'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
						));
					?>			</p>
					<ul class="pagination">
						<?php
						echo "<li class='previous'>".$this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'))."</li>";
						echo "<li>".$this->Paginator->numbers(array('separator' => ''))."</li>";
						echo "<li class='next'>".$this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'))."</li>";
						?>
					</ul>
				</div>
			</div>
		</div>	

	</div>

	<script type="text/javascript">
		$().ready(function(){
			$('select').chosen({width:'100%'});
		});
		function update(id,value){
			$.ajax({
				url: "<?php echo Router::url('/', true); ?><?php echo $this->request->params['controller'] ?>/update_parent/"+id+"/"+value,
				success: function(data, result) {
	        	// $(".fa-spin").removeClass('show').addClass('hide');
	        	// $("#downloadpdf").html(data);
				},						        
			});	
		}
	</script>

	<script>$.ajaxSetup({beforeSend:function(){$("#busy-indicator").show();},complete:function(){$("#busy-indicator").hide();}});</script>
