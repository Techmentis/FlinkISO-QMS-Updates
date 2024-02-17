<td><?php echo h($graphPanel['CustomTable']['name']); ?>&nbsp;</td>
<td><?php echo h($graphPanel['GraphPanel']['field_name']); ?>&nbsp;</td>
<td>
	<div class="dropdown dropdown-sm">
		<button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
			<?php echo h($dateConditions[$graphPanel['GraphPanel']['date_condition']]); ?>
			<span class="caret"></span>
		</button>
		<ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
			<?php foreach($dateConditions as $num => $dateCondition){
				$str = base64_encode(json_encode(array($graphPanel['CustomTable']['id'],$graphPanel['GraphPanel']['id'],'date_condition',$num)));
				echo "<li>" . $this->Html->link($dateCondition,'#',array('onClick'=>'update("'.$str.'","'.$graphPanel['GraphPanel']['id'].'")')) ."</li>";
			}?>							    
		</ul>
	</div>

</td>							
<td><?php
if($graphPanel['GraphPanel']['graph_type'] == 0 )$color = "#0094ff";
else $color = "#ccc";
$str = base64_encode(json_encode(array($graphPanel['CustomTable']['id'],$graphPanel['GraphPanel']['id'],'graph_type',0)));
echo '<svg xmlns="http://www.w3.org/2000/svg" height="18px" viewBox="0 0 24 24" width="18px" fill="'.$color.'" onClick=update("'.$str.'","'.$graphPanel['GraphPanel']['id'].'");><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm7.93 9H13V4.07c3.61.45 6.48 3.32 6.93 6.93zM4 12c0-4.07 3.06-7.44 7-7.93v15.86c-3.94-.49-7-3.86-7-7.93zm9 7.93V13h6.93c-.45 3.61-3.32 6.48-6.93 6.93z"/></svg>';

if($graphPanel['GraphPanel']['graph_type'] == 1 )$color = "#0094ff";
else $color = "#ccc";

$str = base64_encode(json_encode(array($graphPanel['CustomTable']['id'],$graphPanel['GraphPanel']['id'],'graph_type',1)));
echo '<svg xmlns="http://www.w3.org/2000/svg" height="18px" viewBox="0 0 24 24" width="18px" fill="'.$color.'" onClick=update("'.$str.'","'.$graphPanel['GraphPanel']['id'].'");><path d="M0 0h24v24H0V0z" fill="none"/><path d="M13 5.08c3.06.44 5.48 2.86 5.92 5.92h3.03c-.47-4.72-4.23-8.48-8.95-8.95v3.03zM18.92 13c-.44 3.06-2.86 5.48-5.92 5.92v3.03c4.72-.47 8.48-4.23 8.95-8.95h-3.03zM11 18.92c-3.39-.49-6-3.4-6-6.92s2.61-6.43 6-6.92V2.05c-5.05.5-9 4.76-9 9.95 0 5.19 3.95 9.45 9 9.95v-3.03z"/></svg>';

if($graphPanel['GraphPanel']['graph_type'] == 2 )$color = "#0094ff";
else $color = "#ccc";

$str = base64_encode(json_encode(array($graphPanel['CustomTable']['id'],$graphPanel['GraphPanel']['id'],'graph_type',2)));
echo '<svg xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 24 24" height="18px" viewBox="0 0 24 24" width="18px" fill="'.$color.'" onClick=update("'.$str.'","'.$graphPanel['GraphPanel']['id'].'");><rect fill="none" height="24" width="24"/><g><path d="M16,11V3H8v6H2v12h20V11H16z M10,5h4v14h-4V5z M4,11h4v8H4V11z M20,19h-4v-6h4V19z"/></g></svg>';

if($graphPanel['GraphPanel']['graph_type'] == 3 )$color = "#0094ff";
else $color = "#ccc";

$str = base64_encode(json_encode(array($graphPanel['CustomTable']['id'],$graphPanel['GraphPanel']['id'],'graph_type',3)));
echo '<svg xmlns="http://www.w3.org/2000/svg" height="18px" viewBox="0 0 24 24" width="18px" fill="'.$color.'" onClick=update("'.$str.'","'.$graphPanel['GraphPanel']['id'].'");><path d="M0 0h24v24H0V0z" fill="none"/><path d="M3.5 18.49l6-6.01 4 4L22 6.92l-1.41-1.41-7.09 7.97-4-4L2 16.99l1.5 1.5z"/></svg>';

if($graphPanel['GraphPanel']['graph_type'] == 4 )$color = "#0094ff";
else $color = "#ccc";
$str = base64_encode(json_encode(array($graphPanel['CustomTable']['id'],$graphPanel['GraphPanel']['id'],'graph_type',4)));
echo '<svg xmlns="http://www.w3.org/2000/svg" height="18px" viewBox="0 0 24 24" width="18px" fill="'.$color.'" onClick=update("'.$str.'","'.$graphPanel['GraphPanel']['id'].'");><g fill="none"><path d="M0 0h24v24H0V0z"/><path d="M0 0h24v24H0V0z" opacity=".87"/></g><path d="M3 13h2v-2H3v2zm0 4h2v-2H3v2zm0-8h2V7H3v2zm4 4h14v-2H7v2zm0 4h14v-2H7v2zM7 7v2h14V7H7zm-4 6h2v-2H3v2zm0 4h2v-2H3v2zm0-8h2V7H3v2zm4 4h14v-2H7v2zm0 4h14v-2H7v2zM7 7v2h14V7H7z"/></svg>';

?>&nbsp;</td>
<td>
	<div class="dropdown dropdown-sm">
		<?php if($graphPanel['GraphPanel']['data_type'] == 0)$graphPanel['GraphPanel']['data_type'] = 0;?>
		<button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu2<?php echo $cnt;?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
			<?php echo h(Inflector::humanize($dataTypes[$graphPanel['GraphPanel']['data_type']])); ?>
			<span class="caret"></span>
		</button>
		<ul class="dropdown-menu" aria-labelledby="dropdownMenu2<?php echo $cnt;?>">
			<?php foreach($dataTypes as $key => $data_type){
				$str = base64_encode(json_encode(array($graphPanel['CustomTable']['id'],$graphPanel['GraphPanel']['id'],'data_type',$key)));
				echo "<li>" . $this->Html->link($dataTypes[$key],'#',array('onClick'=>'update("'.$str.'","'.$graphPanel['GraphPanel']['id'].'")')) ."</li>";
			}?>							    
		</ul>
	</div>

</td>
<td>
	<?php
	$fields = array();
	
	if(is_array(json_decode($graphPanel['CustomTable']['fields'],true))){
		foreach(json_decode($graphPanel['CustomTable']['fields'],true) as $field){
			if($field['data_type'] == 'float' || $field['data_type'] == 'number'){
				$fields[$field['field_name']] = base64_decode($field['field_label']);
			}
		}
	}
	
	?>
	<div class="dropdown dropdown-sm">
		<button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu3<?php echo $cnt;?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
			<?php echo h(Inflector::humanize($fields[$graphPanel['GraphPanel']['value_field']])); ?>
			<span class="caret"></span>
		</button>
		<ul class="dropdown-menu" aria-labelledby="dropdownMenu3<?php echo $cnt;?>">
			<?php foreach($fields as $field_name => $label){
				$str = base64_encode(json_encode(array($graphPanel['CustomTable']['id'],$graphPanel['GraphPanel']['id'],'value_field',$field_name)));
				echo "<li>" . $this->Html->link($label,'#',array('onClick'=>'update("'.$str.'","'.$graphPanel['GraphPanel']['id'].'")')) ."</li>";
			}?>							    
		</ul>
	</div>
</td>
<td>
	<div class="dropdown dropdown-sm">
		
		<button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu4<?php echo $cnt;?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
			<?php echo h(Inflector::humanize($graphPanel['GraphPanel']['position'])); ?>/<?php echo $graphPanels; ?>
			<span class="caret"></span>
		</button>
		<ul class="dropdown-menu" aria-labelledby="dropdownMenu4<?php echo $cnt;?>">
			<?php for($position = 0; $position <= $graphPanels; $position++){									
				$str = base64_encode(json_encode(array($graphPanel['CustomTable']['id'],$graphPanel['GraphPanel']['id'],'position',$position)));
				echo "<li>" . $this->Html->link($position,'#',array('onClick'=>'update("'.$str.'","'.$graphPanel['GraphPanel']['id'].'")')) ."</li>";
			}?>
		</ul>
	</div>
</td>
<td>
	<div class="dropdown dropdown-sm">
		
		<button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu5<?php echo $cnt;?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
			<?php echo h(Inflector::humanize($graphPanel['GraphPanel']['size'])); ?>/12
			<span class="caret"></span>
		</button>
		<ul class="dropdown-menu" aria-labelledby="dropdownMenu5<?php echo $cnt;?>">
			<?php for($size = 0; $size <= 12; $size++){									
				$str = base64_encode(json_encode(array($graphPanel['CustomTable']['id'],$graphPanel['GraphPanel']['id'],'size',$size)));
				echo "<li>" . $this->Html->link($size,'#',array('onClick'=>'update("'.$str.'","'.$graphPanel['GraphPanel']['id'].'")')) ."</li>";
			}?>
		</ul>
	</div>
</td>

<!-- <td>							
	<?php 
	if($graphPanel['GraphPanel']['color'] == '')$graphPanel['GraphPanel']['color'] = 'blue';

	$colors = array('aqua','red','green','yellow');?>
	<div class="dropdown dropdown-sm">
		<button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1<?php echo $cnt;?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
			<?php echo h(Inflector::humanize($graphPanel['GraphPanel']['color'])); ?>
			<span class="caret"></span>
		</button>
		<ul class="dropdown-menu" aria-labelledby="dropdownMenu1<?php echo $cnt;?>">
			<?php foreach($colors as $panel_color){
				$str = base64_encode(json_encode(array($graphPanel['CustomTable']['id'],$graphPanel['GraphPanel']['id'],'color',$panel_color)));
				echo "<li>" . $this->Html->link($panel_color,'#',array('onClick'=>'update("'.$str.'","'.$graphPanel['GraphPanel']['id'].'")')) ."</li>";
			}?>							    
		</ul>
	</div>
</td>
<td><?php echo ($graphPanel['GraphPanel']['admin_only']?'<i class="fa fa-check"></i>':'<i class="fa fa-remove"></i>');?></td>						
<td>
	<?php
	foreach(json_decode($graphPanel['GraphPanel']['branches']) as $branch_id){
		echo $branches[$branch_id].', ';
	}
	?>
</td>
<td>
	<?php
	foreach(json_decode($graphPanel['GraphPanel']['departments']) as $department_id){
		echo $departments[$department_id].', ';
	}
	?>
</td>
<td>
	<?php
	foreach(json_decode($graphPanel['GraphPanel']['designations']) as $designation_id){
		echo $designations[$designation_id].', ';
	}
	?>
</td> -->
<td>
	<i class="fa fa-cog" onclick="more('<?php echo $graphPanel['GraphPanel']['id'];?>');"></i>&nbsp;&nbsp;
	<?php echo $this->Html->link('<i class="fa fa-trash-o"></i>',array('action'=>'delete',$graphPanel['GraphPanel']['id']),array('escape'=>false,'class'=>'pull-right'));?>
	<div id="<?php echo $graphPanel['GraphPanel']['id'];?>_more"></div>
	
</td>