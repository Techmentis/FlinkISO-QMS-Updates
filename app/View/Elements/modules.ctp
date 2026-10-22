<div class="row">
	<div class="col-md-6">
		<div class="box box-default">
			<div class="box-header">
				<h4 class="box-title">
					<?php echo h(__('Audits')); ?>
				</h4>
				<span class="pull-right"><span class="badge btn-<?php echo $btnClass;?>"></span></span>
			</div>
			<div class="box-body"><?php echo h(__('Install Audit module costs of following ...')); ?></div>
			<div class="box-footer text-right">	
				<div class="pull-left"><?php echo h(__('INR.200 Per Month')); ?></div>
				<?php echo $this->Html->link('<i class="fa fa-cloud-download"></i>','#',array('class'=>'tooltip1', 'escape'=>false, 'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'left', 'title'=> __('Download')));
				?>			
			</div>
		</div>
	</div>

	<div class="col-md-6">
		<div class="box box-default">
			<div class="box-header">
				<h4 class="box-title">
					<?php echo h(__('Meetings')); ?>
				</h4>
				<span class="pull-right"><span class="badge btn-<?php echo $btnClass;?>"></span></span>
			</div>
			<div class="box-body"><?php echo h(__('Install meetings module costs of following ...')); ?></div>
			<div class="box-footer text-right">
				<div class="pull-left"><?php echo h(__('INR.200 Per Month')); ?></div>
				<?php echo $this->Html->link('<i class="fa fa-cloud-download"></i>','#',array('class'=>'tooltip1', 'escape'=>false, 'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'left', 'title'=> __('Download')));
				?>			
			</div>
		</div>
	</div>
</div>
