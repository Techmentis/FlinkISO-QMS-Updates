<div  id="main">
	<?php echo $this->Session->flash();?>

	<?php echo $this->element('nav-header-lists',array('postData'=>array('friendlyName'=>'Processes', 'pluralHumanName'=>'processes','modelClass'=>'Processes','options'=>array("process_definition"=>"Process Definition","process_objective_and_metrics"=>"Process Objecttive And Metrics","process_owners"=>"Process Owners","applicable_to_branches"=>"Applicable To Branches","additional_responsibilities"=>"Additional Responsibilities","input_processes"=>"Input Processes","output_processes"=>"Outout Processes","process_output"=>"Process Output","risks_and_opportunities"=>"Risks And Opportunities","standards"=>"Standards","clauses"=>"Clauses"),'pluralVar'=>'processes'))); ?><div class="row"><div class="col-md-12">
	</div></div>
	<?php if($processes){ ?>
		<div class="panel panel-default"><div class="panel-body no-padding">
			<div class="table-responsive" style="overflow:scroll">
				<?php echo $this->Form->create('Processes',array('class'=>'no-padding no-margin no-background'));?>
				<table cellpadding="0" cellspacing="0" class="table table-responsive table-hover index">
					<tr>
						<th><?php echo $this->Paginator->sort('name'); ?></th>
						<th><?php echo $this->Paginator->sort('qc_document_id','Document'); ?></th>
						<th><?php echo $this->Paginator->sort('process_owners'); ?></th>
						<th><?php echo $this->Paginator->sort('standards'); ?></th>
						<th><?php echo $this->Paginator->sort('clauses'); ?></th>
						<th>Tables</th>
						<th width="100">Actions</th>
					</tr>
					<?php foreach ($processes as $process): ?>
						<tr class="" onclick="addrec('<?php echo $process['Process']['id'];?>')" id="<?php echo $process['Process']['id'];?>_tr">
							<td><?php echo $process['Process']['name'];?></td>
							<td><?php echo $process['QcDocument']['title'];?></td>
							<td><?php foreach(json_decode($process['Process']['process_owners'],true) as $field ){
								echo $processOwners[$field] .", ";
							};?></td>
							<td><?php foreach(json_decode($process['Process']['standards'],true) as $field ){
								echo $standards[$field] .", ";
							};?></td>
							<td><?php foreach(json_decode($process['Process']['clauses'],true) as $field ){
								echo $clauses[$field] .", ";
							};?></td>
							<td>
								<div class="btn-group">
									<div class="btn btn-xs btn-default"><?php echo h($process['Process']['tables']); ?></div>
									<div class="btn btn-xs btn-success"><?php echo h($process['Process']['active_tables']); ?></div>
								</div>
							&nbsp;</td>
							<td class=" actions">	
								<?php echo $this->element('actions', array('created' => $process['Process']['created_by'], 'postVal' => $process['Process']['id'],'qc_document_id' => $process['Process']['qc_document_id'], 'softDelete' => $process['Process']['soft_delete'],'custom_table_id'=>$this->request->params['named']['custom_table_id'])); ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</table>
				<?php echo $this->Form->end();?>
			<?php } ?>

			<?php if(!$processes){ ?>
				<p class='text-center'><span class='text-warning'><i class='fa fa-exclamation-triangle';></i> No records found.</span></p>
			<?php } ?>
			<?php echo $this->element('paging');?>
		</div>
	</div>
</div>

</div></div>
