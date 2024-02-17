<?php echo $this->Session->flash();?>
<div class="row">
	<div class="col-md-12"><h3>Enter Password To Delete File</h3></div>
	<?php 
	echo $this->Form->create(Inflector::classify($this->request->controller),array('action'=>'delete_document'),array('class'=>'form'));
	echo $this->Form->hidden('url',array('default'=>$this->request->params['named']['url']));
	echo $this->Form->hidden('ref',array('default'=>$ref));
	echo '<div class="col-md-4">'.$this->Form->input('password',array('type'=>'password','class'=>'form-control')).'</div>';
	echo '<div class="col-md-2"><br /><br />'.$this->Form->submit('Submit',array('class'=>'btn btn-sm btn-success')).'</div>';
	echo $this->Form->end();
	?>	
</div>
