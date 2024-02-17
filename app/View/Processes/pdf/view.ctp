<div  id="main">
<?php echo $this->Session->flash();?>
<?php echo $this->element('nav-header-lists',array('postData'=>array('friendlyName'=>'Process Definition template','pluralHumanName'=>'cust_pro_00_10_v1s','modelClass'=>'CustPro0010V1','options'=>array("process_definition"=>"Process Definition","process_objective_and_metrics"=>"Process Objecttive And Metrics","process_owners"=>"Process Owners","applicable_to_branches"=>"Applicable To Branches","additional_responsibilities"=>"Additional Responsibilities","input_processes"=>"Input Processes","output_processes"=>"Outout Processes","process_output"=>"Process Output","risks_and_opportunities"=>"Risks And Opportunities","standards"=>"Standards","clauses"=>"Clauses","process_definition"=>"Process Definition",""=>"Process Objecttive And Metrics","process_owners"=>"Process Owners","applicable_to_branches"=>"Applicable To Branches","additional_responsibilities"=>"Additional Responsibilities","input_processes"=>"Input Processes","output_processes"=>"Outout Processes","process_output"=>"Process Output","risks_and_opportunities"=>"Risks And Opportunities","standards"=>"Standards","clauses"=>"Clauses"),'pluralVar'=>'custPro0010V1s'))); ?><div class="row"><div class="col-md-12"><?php echo $this->element('qc_doc_header',array('document',$document));?></div></div>
<div class="panel panel-default"><div class="panel-body">
	<div class="table-responsive">
		<table class="table table-bordered table table-striped table-hover" id="exportcsv">
			<tr>
				<th> Process Definition</th>
                <td><?php echo $custPro0010V1['CustPro0010V1']['process_definition'];?></td>
			</tr>
			<tr>
				<th> Process Objecttive And Metrics</th>
                <td><?php echo $custPro0010V1['CustPro0010V1']['process_objective_and_metrics'];?></td>
			</tr>
			<tr>
				<th>Process Owners</th>
				<td><?php foreach(json_decode($custPro0010V1['CustPro0010V1']['process_owners'],true) as $field ){
                            echo $processOwners[$field] .", ";
                        };?></td>
			</tr>
			<tr>
				<th>Applicable To Branches</th>
				<td><?php foreach(json_decode($custPro0010V1['CustPro0010V1']['applicable_to_branches'],true) as $field ){
                            echo $applicableToBranches[$field] .", ";
                        };?></td>
			</tr>
			<tr>
				<th> Additional Responsibilities</th>
                <td><?php echo $custPro0010V1['CustPro0010V1']['additional_responsibilities'];?></td>
			</tr>
			<tr>
				<th>Input Processes</th>
				<td><?php foreach(json_decode($custPro0010V1['CustPro0010V1']['input_processes'],true) as $field ){
                            echo $inputProcesses[$field] .", ";
                        };?></td>
			</tr>
			<tr>
				<th>Outout Processes</th>
				<td><?php foreach(json_decode($custPro0010V1['CustPro0010V1']['output_processes'],true) as $field ){
                            echo $outoutProcesses[$field] .", ";
                        };?></td>
			</tr>
			<tr>
				<th> Process Output</th>
                <td><?php echo $custPro0010V1['CustPro0010V1']['process_output'];?></td>
			</tr>
			<tr>
				<th> Risks And Opportunities</th>
                <td><?php echo $custPro0010V1['CustPro0010V1']['risks_and_opportunities'];?></td>
			</tr>
			<tr>
				<th>Standards</th>
				<td><?php foreach(json_decode($custPro0010V1['CustPro0010V1']['standards'],true) as $field ){
                            echo $standards[$field] .", ";
                        };?></td>
			</tr>
			<tr>
				<th>Clauses</th>
				<td><?php foreach(json_decode($custPro0010V1['CustPro0010V1']['clauses'],true) as $field ){
                            echo $clauses[$field] .", ";
                        };?></td>
			</tr>
			<tr>
				<th>Prepared By</th>
                <td><?php echo $custPro0010V1['PreparedBy']['name'];?></td>
			</tr>
			<tr>
				<th>Approved By</th>
                <td><?php echo $custPro0010V1['ApprovedBy']['name'];?></td>
			</tr>
		</table>
	</div>
</div></div>
    <div class="row">
    	<div class="col-md-12">
    		<?php 
    			foreach($linkedTables as $linkedTable){
    				?>
    				<div><h4><?php echo Inflector::humanize($linkedTable['CustomTable']['name']);?></h4></div>
    				<div id="<?php echo $linkedTable['CustomTable']['table_name']?>"></div>
    				<script type="text/javascript">
    					$("#<?php echo $linkedTable['CustomTable']['table_name']?>").load("<?php echo Router::url('/', true); ?><?php echo $linkedTable['CustomTable']['table_name']?>/index/<?php echo $this->request->params['pass'][0];?>");
    				</script>
    			<?php } ?>
    	</div>
    </div>
    
    <div class="row">
    	<div class="col-md-12">
    		<?php echo $this->element('approval_form',array('approval'=>$approval));?>
    		<?php echo $this->element('approval_history',array('approval'=>$approval,'approvals'=>$approvals,'current_approval'=>$this->request->params['named']['approval_id'],'approvalComments',$approvalComments));?>
    	</div>
    </div>
</div>
