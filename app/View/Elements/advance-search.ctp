<?php if($this->action != 'recreate'){ ?>
	<script type="text/javascript">
		$().ready(function() {
		if($(".control-sidebar").hasClass('control-sidebar-open') == true){
			$("[name*='date']").datepicker({
				changeMonth: true,
				changeYear: true,
				format: 'yyyy-mm-dd',
				autoclose:true,
				'showTimepicker': false,
			}).attr('readonly', 'readonly');
			$('select').chosen();	 
		}	 
	 });
	</script>
<style>
	.chosen-container, .chosen-container-single, .chosen-select
	{min-width: 120px; width:100% !important;}	
</style>

<div class="">
	<?php echo $this->Form->create($this->name, array('action' => __('advance_search/custom_table_id:').$this->request->params['named']['custom_table_id'].'/qc_document_id:'.$this->request->params['named']['qc_document_id'].'/process_id:'.$this->request->params['named']['process_id'], 'role' => __('form'), 'class' => __('advanced-search-form'), 'id' => __('advance-search-form'), 'type' => __('post'))); ?>
	<div class="panel">

			<div class="panel-heading">
				<h4 class="modal-title"><?php echo __('Advanced Search'); ?></h4>
			<small><?php echo h(__('Set your required conditions and click submit to search.')); ?></small>
		</div>
		<div class="panel-body">
			<div>
				<ul class="nav nav-tabs" role="tablist">
					<li role="presentation" class=""  ><a href="#sectionone" aria-controls="sectionone" role="tab" data-toggle="tab"><?php echo h(__('Basic')); ?></a></li>
					<li role="presentation" class="active"><a href="#sectiontwo" aria-controls="sectiontwo" role="tab" data-toggle="tab"><?php echo h(__('Advanced')); ?></a></li>
					<li role="presentation" class=""><a href="#sectionthree" aria-controls="sectionthree" role="tab" data-toggle="tab"><?php echo h(__('Order')); ?></a></li>                        
				</ul>
				<div class="tab-content">
					<div role="tabpanel" class="tab-pane" id="sectionone">
						<table class="table table-responsive table-bordered table-stripped">
							<tr>
								<th><?php echo __('Field');?></th>
								<th class="conwidth"><?php echo __('Condition');?></th>
								<th><?php echo __('Value');?></th>
							</tr>
							<?php foreach ($src as $fieldName => $oprators) { ?>
								<tr>
									<td><?php echo Inflector::Humanize($fieldName);?></td>
									<td><?php echo $this->Form->input($fieldName,array('id'=>Inflector::Classify($modal.$fieldName), 'name'=>__('data[basic][').$modal.']['.$fieldName.'][oprator]', 'options'=>$oprators,'default'=>'==', 'label'=>false,'onChange'=>__('checkdate(this)'),'class'=>__('form-control')));?></td>
									<td><?php echo $this->Form->input($fieldName.'_value',array('id'=>Inflector::Classify($modal.$fieldName).'_Value','name'=>'data[basic]['.$modal.']['.$fieldName.'][value]','label'=>false,'class'=>'form-control'));?></td>
								</tr>
							<?php } ?>
						</table>
					</div>
					<div role="tabpanel" class="tab-pane active" id="sectiontwo">
						<table class="table table-responsive table-bordered table-stripped">
							<tr>
								<th><?php echo __('Field');?></th>
								<th><?php echo __('Condition');?></th>                        
							</tr>
							<?php $oprators = array('=='=>__('Equal To'),'!='=>__('Not Equal To'));?>
							<?php foreach ($belongsToModels as $fieldName => $records) { ?>
								<tr>
									<td><?php echo Inflector::Humanize($fieldName);?></td>
									<td><?php echo $this->Form->input($fieldName,array('name'=>__('data[advance][').$modal.']['.$records['field_name'].'][oprator]','options'=>$oprators,'default'=>'==', 'label'=>false,'class'=>__('form-control')));?></td>
									<td><?php echo $this->Form->input($fieldName.'_value',array('name'=>'data[advance]['.$modal.']['.$records['field_name'].'][value][]','options'=>$records['records'],'multiple', 'label'=>false,'class'=>'form-control'));?></td>
								</tr>
							<?php } ?>
						</table>
					</div>
					<div role="tabpanel" class="tab-pane" id="sectionthree">
						<table class="table table-responsive table-bordered table-stripped">
							<tr>
								<th><?php echo __('Field');?></th>                                    
								<th><?php echo __('Asending / Desending');?></th>
							</tr>
							<?php foreach ($src as $fieldName => $oprators) { ?>
								<tr>
									<td><?php echo Inflector::Humanize($fieldName);?></td>
									<!-- <td><?php echo $this->Form->input($fieldName,array('name'=>__('data[basic][').$modal.']['.$fieldName.'][oprator]', 'options'=>$oprators,'default'=>'==', 'label'=>false,'onChange'=>__('checkdate(this)')));?></td> -->
									<td><?php 
									$oredrOptions = array(0=>__('Ascending Order'),1=>__('Desending Order'));
									echo $this->Form->input($fieldName.'_value',array('name'=>__('data[order][').$modal.']['.$fieldName.'][value]','label'=>false, 'options'=>$oredrOptions,'class'=>__('form-control')));?></td>
								</tr>
							<?php } ?>
						</table>
					</div>
					
				</div>
				<div class="panel-footer tex-left">
					<?php echo $this->Form->submit(__('Submit'), array('div' => false, 'class' => 'btn btn-primary btn-success', 'id'=>'search_submit_id')); ?>                    
					<?php echo $this->Form->end(); ?>                
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	function datePicker() {
	}
</script>
<script>

	function checkdate(n){
		if(n.value == 'between'){

			i = n.id + "_Value";
			$("#"+i).datepicker("destroy");
			
			$("#"+i).daterangepicker({
				format: 'MM/DD/YYYY',
				locale: {
					format: 'MM/DD/YYYY'
				},
				autoclose:true,
			}); 
		}
	}

</script>
<?php } ?>
