<?php echo $this->Session->flash();?>
<div class="row">
	<div class="col-md-12"><h3>Enter Password To Download File</h3></div>
	<?php 
	echo $this->Form->create('Files',array('action'=>'download_file'),array('class'=>'form'));
	echo $this->Form->hidden('id',array('default'=>$this->request->params['named']['id']));
	echo $this->Form->hidden('file',array('default'=>$this->request->params['named']['file']));
	echo $this->Form->hidden('ref',array('default'=>$ref));
	echo '<div class="col-md-4">'.$this->Form->input('password',array('type'=>'password','class'=>'form-control')).'</div>';
	echo '<div class="col-md-2"><br /><br />'.$this->Form->submit('Submit',array('class'=>'btn btn-sm btn-success')).'</div>';
	echo $this->Form->end();
	?>	
</div>
