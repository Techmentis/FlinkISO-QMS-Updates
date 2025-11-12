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
	/*#ui-datepicker-div,.ui-datepicker,.datepicker{z-index:9999 !important}{z-index: 999999 !important}*/
/*.modal-footer{text-align: left}*/
</style>

<div class="">
	<?php echo $this->Form->create($this->name, array('action' => 'advance_search/custom_table_id:'.$this->request->params['named']['custom_table_id'].'/qc_document_id:'.$this->request->params['named']['qc_document_id'].'/process_id:'.$this->request->params['named']['process_id'], 'role' => 'form', 'class' => 'advanced-search-form', 'id' => 'advance-search-form', 'type' => 'post')); ?>
	<div class="panel">

		<div class="panel-heading">                                
			<h4 class="modal-title"><?php echo __('Advanced Search'); ?>
			<?php echo $this->Html->link('<i class="fa fa-close"></i>','#',
    			array('class'=>'btn-app btn-sm btn-default pull-right','escape'=>false,
        		'data-toggle'=>'control-sidebar', 'id'=>'close_sidebar'
    		)); ?>
    		</h4>
			<small>Set your required conditions and click submit to search.</small>
		</div>
		<div class="panel-body">
			<div>
				<ul class="nav nav-tabs" role="tablist">
					<li role="presentation" class=""  ><a href="#sectionone" aria-controls="sectionone" role="tab" data-toggle="tab">Basic</a></li>
					<li role="presentation" class="active"><a href="#sectiontwo" aria-controls="sectiontwo" role="tab" data-toggle="tab">Advanced</a></li>
					<li role="presentation" class=""><a href="#sectionthree" aria-controls="sectionthree" role="tab" data-toggle="tab">Order</a></li>                        
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
									<td><?php echo $this->Form->input($fieldName,array('id'=>Inflector::Classify($modal.$fieldName), 'name'=>'data[basic]['.$modal.']['.$fieldName.'][oprator]', 'options'=>$oprators,'default'=>'==', 'label'=>false,'onChange'=>'checkdate(this)','class'=>'form-control'));?></td>
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
							<?php $oprators = array('=='=>'Equal To','!='=>'Not Equal To');?>
							<?php foreach ($belongsToModels as $fieldName => $records) { ?>
								<tr>
									<td><?php echo Inflector::Humanize($fieldName);?></td>
									<td><?php echo $this->Form->input($fieldName,array('name'=>'data[advance]['.$modal.']['.$records['field_name'].'][oprator]','options'=>$oprators,'default'=>'==', 'label'=>false,'class'=>'form-control'));?></td>
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
									<!-- <td><?php echo $this->Form->input($fieldName,array('name'=>'data[basic]['.$modal.']['.$fieldName.'][oprator]', 'options'=>$oprators,'default'=>'==', 'label'=>false,'onChange'=>'checkdate(this)'));?></td> -->
									<td><?php 
									$oredrOptions = array(0=>'Ascending Order',1=>'Desending Order');
									echo $this->Form->input($fieldName.'_value',array('name'=>'data[order]['.$modal.']['.$fieldName.'][value]','label'=>false, 'options'=>$oredrOptions,'class'=>'form-control'));?></td>
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
	$().ready(function(){
		$("#close_sidebar").on('click',function(){
			$(".control-sidebar").removeClass('control-sidebar-open');
		})
	})

	function datePicker() {   
	}
</script>
<script>

	function checkdate(n){
		if(n.value == 'between'){

			i = n.id + "_Value";
			// $("#"+i).data('datepicker').remove();			
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
