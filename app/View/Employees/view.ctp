<div id="employees_ajax">
<?php echo $this->Session->flash();?>	

	<div class="employees ">
		<?php echo $this->element('nav-header-lists',array('postData'=>array('pluralHumanName'=>'Employees','modelClass'=>'Employee','options'=>array(),'pluralVar'=>'employees'))); ?>


<div class="nav panel panel-default">
<div class="employees form col-md-12">
	<?php if($this->Session->read('User.employee_id') == $this->request->params['pass'][0]){ ?>	
		<table class="table table-responsive no-border">
			<tr>
				<th>Signature</th>
				<th>Add Profile Picture</th>
			</tr>
			<tr>
			<td>
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
			<td>
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
			<tr>
				<td>Image type : png | Max Size : 20kb</td>
				<td>Image type : png | Max Size : 800kb</td>
			</tr>
		</tr>
	</table>
<?php } ?>

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

</div>
<?php echo $this->Js->writeBuffer();?>

</div>
<script>$.ajaxSetup({beforeSend:function(){$("#busy-indicator").show();},complete:function(){$("#busy-indicator").hide();}});</script>
