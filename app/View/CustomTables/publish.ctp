<?php echo $this->Session->flash();?>
<div class="row">
	<div class="col-md-12"><h3>Publish Table</h3></div>
	<?php 
	echo $this->Form->create('CustomTable',array('action'=>'publish'),array('class'=>'form'));
	echo $this->Form->input('id',array('default'=>$this->request->params['pass'][0]));
	echo '<div class="col-md-4">'.$this->Form->input('password',array('type'=>'password','class'=>'form-control')).'</div>';
	echo '<div class="col-md-2"><br />'.$this->Form->submit('Submit',array('class'=>'btn btn-sm btn-success')).'</div>';
	echo $this->Form->end();
	?>	
</div>
