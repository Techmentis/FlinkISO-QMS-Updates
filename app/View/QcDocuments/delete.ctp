<?php echo $this->Session->flash();?>
<div class="row">
	<div class="col-md-12"><h3>Enter Password To Delete File</h3></div>
	<?php 
	echo $this->Form->create('QcDocument',array('action'=>'delete'),array('class'=>'form'));
	echo $this->Form->hidden('id',array('default'=>$id));
	echo '<div class="col-md-4">'.$this->Form->input('password',array('type'=>'password','class'=>'form-control')).'</div>';
	
	if($customTables){
		echo '<div class="col-md-12"><br /><p><h4 class="text-danger">Following custom tables found.</h4></p></div>';	
		echo '<div class="col-md-12"><ul class="list-group">';	
		foreach($customTables as $custom_table_id => $custom_table_name){
			echo '<li class="list-group-item">'. $custom_table_name . '</li>';	
		}
		echo '</ul>';	
		echo '<p>You must delete custom tables linked to this document separately from custom tables section.</p></div>';
	}
	
	echo '<div class="col-md-12"><br /><br />'.$this->Form->submit('Submit',array('class'=>'btn btn-sm btn-success')).'</div>';
	echo $this->Form->end();
	?>	
</div>
