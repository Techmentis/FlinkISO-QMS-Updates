<div  id="main">
	<?php echo $this->Session->flash();?>

	<?php echo $this->element('nav-header-lists',array('postData'=>array('friendlyName'=>__('Processes'), 'pluralHumanName'=>__('processes'),'modelClass'=>'Processes','options'=>array("process_definition"=>__("Process Definition"),"process_objective_and_metrics"=>__("Process Objecttive And Metrics"),"process_owners"=>__("Process Owners"),"applicable_to_branches"=>__("Applicable To Branches"),"additional_responsibilities"=>__("Additional Responsibilities"),"input_processes"=>__("Input Processes"),"output_processes"=>__("Outout Processes"),"process_output"=>__("Process Output"),"risks_and_opportunities"=>__("Risks And Opportunities"),"standards"=>__("Standards"),"clauses"=>__("Clauses")),'pluralVar'=>'processes'))); ?><div class="row"><div class="col-md-12">
	</div></div>
	<?php if($processes){ ?>
		<div class="panel panel-default"><div class="panel-body no-padding">
			<div class="table-responsive" style="overflow:scroll">
				<?php echo $this->Form->create('Processes',array('class'=>'no-padding no-margin no-background'));?>
				<table cellpadding="0" cellspacing="0" class="table table-responsive table-hover index">
					<tr>
						<th><?php echo $this->Paginator->sort('name'); ?></th>
						<th><?php echo $this->Paginator->sort('qc_document_id',__('Document')); ?></th>
						<th><?php echo $this->Paginator->sort('process_owners'); ?></th>
						<th><?php echo $this->Paginator->sort('standards'); ?></th>
						<th><?php echo $this->Paginator->sort('clauses'); ?></th>						
						<th width="100"><?php echo h(__('Actions')); ?></th>
					</tr>
					<?php foreach ($processes as $process): ?>
						<tr class="" onclick="addrec('<?php echo $process['Process']['id'];?>')" id="<?php echo $process['Process']['id'];?>_tr">
							<td><?php echo $process['Process']['name'];?></td>
							<td><?php echo $process['QcDocument']['name'];?></td>
							<td><?php foreach(json_decode($process['Process']['process_owners'],true) as $field ){
								echo $processOwners[$field] .", ";
							};?></td>
							<td><?php foreach(json_decode($process['Process']['standards'],true) as $field ){
								echo $standards[$field] .", ";
							};?></td>
							<td><?php foreach(json_decode($process['Process']['clauses'],true) as $field ){
								echo $clauses[$field] .", ";
							};?></td>							
							<td class=" actions">	
								<?php echo $this->element('actions', array('created' => $process['Process']['created_by'], 'postVal' => $process['Process']['id'],'qc_document_id' => $process['Process']['qc_document_id'], 'softDelete' => $process['Process']['soft_delete'],'custom_table_id'=>$this->request->params['named']['custom_table_id'])); ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</table>
				<?php echo $this->Form->end();?>
			<?php } ?>

			<?php if(!$processes){ ?>
				<p class='text-center'><span class='text-warning'><i class='fa fa-exclamation-triangle';></i> <?php echo h(__('No records found.')); ?></span></p>
			<?php } ?>
			<?php echo $this->element('paging');?>
		</div>
	</div>
</div>

</div></div>
