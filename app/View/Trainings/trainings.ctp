<?php echo $this->element('checkbox-script'); ?>
<div  id="main">
	<?php echo $this->Session->flash();?>	
	<div class="usageDetails panel-body ">
		<?php echo $this->element('billing_header_lists',array('header'=>'Traning'));?>
		<?php echo $this->Form->create('Training',array('action'=>'add_training'),array('class'=>'form-control'),array('default'=>false));?>
		<div class="row">
			<div class="col-md-12"><h4><?php echo h(__('Schedule New Training')); ?></h4></div>
			<div class="col-md-6"><?php echo $this->Form->input('timezone',array('default'=>$this->Session->read('User.timezone'), 'class'=>'form-control','required'));?></div>
			<div class="col-md-6"><?php echo $this->Form->input('schedule_date',array('class'=>'form-control','type'=>'datetime-local','required'));?></div>
			<div class="col-md-12"><?php echo $this->Form->input('notes',array('class'=>'form-control','required','type'=>'textarea'));?></div>
			<div class="col-md-12">
				<?php 
				$traning_checklist = array(
					0 => __('Informed all the participents / users'),
					1 => __('Checked & confirmed availibility for all the participents'),
					2 => __('Checked and confirmed requiements/infrastructure like laptops, conference room, mics etc')
				);

				echo $this->Form->input('checklist',array('options'=>$traning_checklist,'multiple'=>'checkbox'));

				?>
			</div>			
			<div class="col-md-12">
				<?php echo $this->Form->submit(__('Schedule'),array('id'=>'submit_id','class'=>'btn btn-sm btn-success')); ?>
			</div>				
		</div>
		<?php echo $this->Form->end();?>
		<div class="row">
			<div class="col-md-12">
				<h5><?php echo h(__('Note:')); ?></h5>
				<p>
					<ul>
						<li><?php echo h(__('You can schedule training by adding Schedule Date')); ?></li>
						<li><?php echo h(__('Training duration will be 2 Hours')); ?></li>
						<li><?php echo h(__('We can conduct training in multiple sessions, spaning multiple days')); ?></li>
						<li><?php echo h(__('When you add new schedule, system will generate the inoice')); ?></li>
						<li><?php echo h(__('Training request will be accepted once the invoice is paid')); ?></li>
						<li><?php echo h(__('You can cancle the schedule 3 days before the schedule date for refund')); ?></li>
						<li><?php echo h(__('You can reschedule the training for the paid invoice')); ?></li>
						<li><?php echo h(__('All traiings will be conducted online via screen share tools')); ?></li>
					</ul>
				</p>
			</div>	
		</div>
	</div>
