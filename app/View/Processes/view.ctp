<style type="text/css">
	.process_div{
		padding: 15px;
		border: 1px solid #ccc;
		border-radius: 10px;
		text-align: center;
		font-weight: 500;
		margin: 4px;
	}
	.input_div{
		background-color: #eef7ff;
	}
	.output_div{
		background-color: #f4ffee;
	}
	.current_div{
		background-color: #fffbee;
	}
</style>
<div  id="main">
	<?php echo $this->Session->flash();?>
	<?php echo $this->element('nav-header-lists',array('postData'=>array('friendlyName'=>'Processes','pluralHumanName'=>'processes','modelClass'=>'Process','options'=>array("process_definition"=>"Process Definition","process_objective_and_metrics"=>"Process Objecttive And Metrics","process_owners"=>"Process Owners","applicable_to_branches"=>"Applicable To Branches","additional_responsibilities"=>"Additional Responsibilities","input_processes"=>"Input Processes","output_processes"=>"Outout Processes","process_output"=>"Process Output","risks_and_opportunities"=>"Risks And Opportunities","standards"=>"Standards","clauses"=>"Clauses","process_definition"=>"Process Definition",""=>"Process Objecttive And Metrics","process_owners"=>"Process Owners","applicable_to_branches"=>"Applicable To Branches","additional_responsibilities"=>"Additional Responsibilities","input_processes"=>"Input Processes","output_processes"=>"Outout Processes","process_output"=>"Process Output","risks_and_opportunities"=>"Risks And Opportunities","standards"=>"Standards","clauses"=>"Clauses"),'pluralVar'=>'Processs'))); ?><div class="row"><div class="col-md-12">
	</div></div>
	<div id="newdoctitle<?php echo $docid;?>"></div>        
        <div class="box collapsed-box" id="ofdcontainer<?php echo $placeholderid;?>">
          <div class="box-header with-border data-header" data-widget="collapse">
            <h3 class="box-title"><i class="fa fa-chain"></i>&nbsp;&nbsp;<?php echo $process['QcDocument']['title'];?>&nbsp;&nbsp;<small></small></h3>
            <div class="box-tools pull-right">
              <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus" id="process_html"></i></button>
          </div>            
      </div>          
      <div class="box-body no-padding"> 
      	<div style="padding:20px"><?php echo $html;?></div>
      </div>
  </div>
	<div class="panel panel-default"><div class="panel-body">
		<div class="table-responsive">
			<table class="table table-bordered table table-striped table-hover" id="exportcsv">
				<tr>
					<th>Qc Document</th>
					<td><?php echo $process['QcDocument']['title'];?></td>
				</tr>
				<tr>
					<th> Process Definition</th>
					<td><?php echo $process['Process']['process_definition'];?></td>
				</tr>
				<tr>
					<th> Process Objecttive And Metrics</th>
					<td><?php echo $process['Process']['process_objective_and_metrics'];?></td>
				</tr>
				<tr>
					<th>Process Owners</th>
					<td><?php foreach(json_decode($process['Process']['process_owners'],true) as $field ){
						echo $processOwners[$field] .", ";
					};?></td>
				</tr>
				<tr>
					<th>Applicable To Branches</th>
					<td><?php foreach(json_decode($process['Process']['applicable_to_branches'],true) as $field ){
						echo $applicableToBranches[$field] .", ";
					};?></td>
				</tr>
				<tr>
					<th> Additional Responsibilities</th>
					<td><?php echo $process['Process']['additional_responsibilities'];?></td>
				</tr>
				<tr>
					<th>Input Processes</th>
					<td><?php foreach(json_decode($process['Process']['input_processes'],true) as $field ){
						echo $inputProcesses[$field] .", ";
					};?></td>
				</tr>
				<tr>
					<th>Outout Processes</th>
					<td><?php foreach(json_decode($process['Process']['output_processes'],true) as $field ){
						echo $outputProcesses[$field] .", ";
					};?></td>
				</tr>
				<tr>
					<th> Process Output</th>
					<td><?php echo $process['Process']['process_output'];?></td>
				</tr>
				<tr>
					<th> Risks And Opportunities</th>
					<td><?php echo $process['Process']['risks_and_opportunities'];?></td>
				</tr>
				<tr>
					<th>Standards</th>
					<td><?php foreach(json_decode($process['Process']['standards'],true) as $field ){
						echo $standards[$field] .", ";
					};?></td>
				</tr>
				<tr>
					<th>Clauses</th>
					<td><?php foreach(json_decode($process['Process']['clauses'],true) as $field ){
						echo $clauses[$field] .", ";
					};?></td>
				</tr>
				<tr>
					<th>Prepared By</th>
					<td><?php echo $process['PreparedBy']['name'];?></td>
				</tr>
				<tr>
					<th>Approved By</th>
					<td><?php echo $process['ApprovedBy']['name'];?></td>
				</tr>
			</table>
		</div>
	</div></div>
	<div class="row">
		<div class="col-md-4">
			<div class="row">
				<div class="col-md-12 text-center">Input</div>
			<?php foreach($ips as $id => $name){ ?>
				<div class="col-md-12"><div class="process_div input_div">
					<?php echo $this->Html->link($name,array('action'=>'view',$id))?>
				</div></div>
			<?php }?>
			</div>
		</div>
		<div class="col-md-4">
			<div class="row">
				<div class="col-md-12 text-center">Current</div>
					<div class="col-md-12">
						<div class="process_div current_div"><?php echo $process['Process']['name'];?></div>
					</div>
				</div>
			</div>			
		<div class="col-md-4">
			<div class="col-md-12 text-center">Output</div>
			<?php foreach($ops as $id => $name){ ?>
				<div class="col-md-12"><div class="process_div output_div">
					<?php echo $this->Html->link($name,array('action'=>'view',$id))?>
				</div></div>
			<?php }?>
			</div>
		</div>
	</div>
	<div class="row">
		<?php foreach($images as $image){ ?>
			<div class="col-md-4"><div style="padding:10px"><?php echo $image?></div></div>
		<?php } ?>
	</div>
	<div class="row">
		<div class="col-md-12">
			<h4>Linked Tables</h4>
			<?php 
			foreach($linkedTables as $id => $linkedTable){
				echo "<div class='col-md-6'><ul>";
				echo "<li>" .$this->Html->link($linkedTable, array('controller'=>'custom_tables','action'=>'view',$id), array('class'=>'link')). "</li>";
				echo "</ul></div>";
			} ?>
		</div>
	</div>

	<div class="row">
		<div class="col-md-12">
			<?php echo $this->element('approval_form',array('approval'=>$approval));?>
			<?php echo $this->element('approval_history',array('approval'=>$approval,'approvals'=>$approvals,'current_approval'=>$this->request->params['named']['approval_id'],'approvalComments',$approvalComments));?>
		</div>
	</div>
</div>
