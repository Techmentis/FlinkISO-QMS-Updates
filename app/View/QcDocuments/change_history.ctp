<?php if($prompt == false){ ?>
	<div class="row">
		<div class="col-md-12"><h4><?php echo "Fetching data from " . $table;?></h4></div>
		<div class="col-md-12">
			<div class="table-responsive">
				<table class="table">
					<tr>
					<?php foreach($fields as $field){ ?>
						<th><?php echo Inflector::humanize($field);?></th>
					<?php }?>
					<th>Prepared By</th>
					<th>Approved By</th>
					<th>Action</th>
					</tr>
					<?php 				
					foreach($records as $record){ ?>
						<tr>
						<?php foreach($fields as $field){							
						
							foreach(json_decode($table_fields,true) as $table_field){
								if($table_field['field_name'] == $field){
									if($table_field['data_type'] == 'radio'){
										$values = explode(',', $table_field['csvoptions']);
										$value = $values[$record[$model][$field]];								
									}else{
										$value = $record[$model][$field];
									}
								}
							}

							echo "<td>".$value."</td>";
						}?>
						<td><?php echo $record['PreparedBy']['name'];?></td>
						<td><?php echo $record['ApprovedBy']['name'];?></td>
						<td><?php echo $this->Html->link('<i class="fa fa-television"></fa>',array('controller'=>$table_name,'action'=>'view',$record[$model]['id']),array('target'=>'_blank', 'escape'=>false));?></td>
						</tr>
					<?php }?>				
				</table>
			</div>
		</div>
	</div>
<?php } else {?>
	<div class="row">
		<div class="col-md-12"><h4 class="text-danger">You have not defined Change Management Custom Table.</h4></div>
		<div class="col-md-12">Click <?php echo $this->Html->link('Here',array('action'=>'define_change_history_table'));?> to link you change manahement custom table.</div>
	</div>
<?php }?>