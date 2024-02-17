<style type="text/css">
	body{font-family: "Helvetica Neue",Helvetica,Arial,sans-serif; font-size: 12px;}
	.table-responsive{overflow-x: visible!important;min-height:.01%}
	.table {width: 100%;max-width: 100%;margin-bottom: 20px;border-spacing:0;border-collapse:collapse;display: table;box-sizing: border-box;text-indent: initial;border-spacing: 2px;border-color: gray;}
	tr {display: table-row;vertical-align: inherit;border-color: inherit;}
	.table-bordered, .table-bordered>tbody>tr>td, .table-bordered>tbody>tr>th, .table-bordered>tfoot>tr>td, .table-bordered>tfoot>tr>th, .table-bordered>thead>tr>td, .table-bordered>thead>tr>th {border: 1px solid #ddd;}
	.table-bordered>tbody>tr>td, .table-bordered>tbody>tr>th, .table-bordered>tfoot>tr>td, .table-bordered>tfoot>tr>th, .table-bordered>thead>tr>td, .table-bordered>thead>tr>th {border: 1px solid #f4f4f4;}
	caption, th {text-align: left;}.table-bordered, .table-bordered>tbody>tr>td, .table-bordered>tbody>tr>th, .table-bordered>tfoot>tr>td, .table-bordered>tfoot>tr>th, .table-bordered>thead>tr>td, .table-bordered>thead>tr>th {border: 1px solid #ddd;}.table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, 
	.table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {padding: 4px 6px;line-height: 1.42857143;vertical-align: top;border-top: 1px solid #ddd;font-size: 12px;}
</style>
<br />

<h4><?php echo $record['CustomTable']['name'];?> Record</h4> 

<?php 	
$linkedModel = array_keys($record);
$fields = $this->requestAction(array('action'=>'get_fields',$record[$linkedModel[0]]['custom_table_id']));
?>
<table class="table table-responsive table-bordered">
	<?php foreach(json_decode($fields,true) as $field){ ?>				
		<tr>
			<th><?php echo Inflector::humanize($field['field_name']);?></th>
			<td>
				<?php 
				if($field['linked_to'] != -1 && $field['display_type'] == 3){
					echo $record[Inflector::classify($field['field_name'])]['name'];
					echo $record[Inflector::classify($field['field_name'])]['title'];
				}

				if($field['linked_to'] != -1 && $field['display_type'] == 4){
					$options = json_decode($record[$linkedModel[0]][$field['field_name']],true);
					foreach($options as $option){
						$val = $this->requestAction(array('action'=>'get_val','model'=>$field['linked_to'],'record'=>$option));
						echo $val.', ';						
					}
				}

				if($field['display_type'] == 1){
					$options = explode(',',$field['csvoptions']);
					echo $options[$record[$linkedModel[0]][$field['field_name']]];
				}

				if($field['display_type'] == 0){
					echo $record[$linkedModel[0]][$field['field_name']];
				}
				

				?>
			</td>
		</tr>
	<?php } ?>
	<tr>
		<th>Prepared By</th>
		<td><?php echo $record['PreparedBy']['name'];?></td>
	</tr>
	<tr>
		<th>Approved By</th>
		<td><?php echo $record['ApprovedBy']['name'];?></td>
	</tr>
	<tr>
		<th>Publish</th>
		<td><?php echo $record[$linkedModel[0]]['publish']?'Yes':'No';?></td>
	</tr>
</table>
