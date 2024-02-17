<div class="box box-default box-solid doc-header">	
	<div class="box-header with-border"><h3 class="box-title" style="width:100%">Process : <?php echo $process['Process']['name']?> <span class="pull-right"><i class="fa fa-chain"></i></span></h3></div>
	<div class="box-body">
		<table class="table table-bordered table-responsive">
			<tr>
				<th>Standards</th>
				<td><?php 
				$stans = json_decode($process['Process']['standards'],true);
				foreach($stans as $s){
					echo $standards[$s].', ';
				}
			?></td>
			<th>Clauses</th>
			<td><?php 
			$ccs = json_decode($process['Process']['clauses'],true);
			foreach($ccs as $c){
				echo $clauses[$c].', ';
			}
		?></td>				
	</tr>
	<tr>
		<th>Process Owners</th>
		<td><?php 
		$powns = json_decode($process['Process']['process_owners'],true);
		foreach($powns as $emps){
			echo $PublishedDepartmentList[$emps].', ';
		}				
	?></td>
	<th>Applicable To Branches</th>
	<td><?php 
	$brans = json_decode($process['Process']['applicable_to_branches'],true);
	foreach($brans as $bs){					
		echo $PublishedBranchList[$bs].', ';
	}				
?></td>
</tr>
</table>
</div>
</div>
