<div class="row">
	<div class="col-md-12"><h3>Delete record?</h3></div>
	<?php 
	echo $this->Form->create($model,array('action'=>'delete','custom_table_id'=>$this->request->params['named']['custom_table_id'],'qc_document_id'=>$this->request->params['named']['qc_document_id']),array('class'=>'form'));
	echo $this->Form->input('id',array('default'=>$this->request->params['pass'][0]));
	echo $this->Form->hidden('prepared_by',array('default'=>$rec));
	echo '<div class="col-md-12">Record once deleted can not be retrived. Are you sure you want to continue?</div>';
	echo '<div class="col-md-12"> <div class="btn-group">'.$this->Form->submit('Yes, Delete Record?',array('class'=>'btn btn-sm btn-danger','div'=>false));
	echo $this->Html->link('No, go back',array('action'=>'index','custom_table_id'=>$this->request->params['named']['custom_table_id'],'qc_document_id'=>$this->request->params['named']['qc_document_id']), array('class'=>'btn btn-sm btn-success')).'</div>	</div>';
	echo $this->Form->end();
	?>	
</div>
