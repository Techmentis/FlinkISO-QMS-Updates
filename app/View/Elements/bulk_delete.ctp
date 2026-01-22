<?php echo $this->Session->flash();?>	
<div class="row">
	<div class="col-md-12"><h3>Bulk Delete record?</h3></div>
	<?php 	
	if($this->request->data[Inflector::classify($this->request->controller)]['bulk_delete_ids']){
		$data = explode(',',$this->request->data[Inflector::classify($this->request->controller)]['bulk_delete_ids']);
		
		foreach($data as $ids){
			$id[] = str_replace('_tr','',$ids);
		}
		$ids = json_encode($id);
	}

	echo $this->Form->create($model,array('action'=>'bulk_delete/custom_table_id:'.$this->request->params['named']['custom_table_id'].'/qc_document_id:'.$this->request->params['named']['qc_document_id']),array('class'=>'form'));
	echo $this->Form->hidden('ids',array('default'=>$ids));
	echo "<div class='col-md-4'>" . $this->Form->input('password',array('type'=>'password','class'=>'form-control')) . "</div>";
	
	$skipModels = array('Branch','Department','Designation','QcDocument','Process','Approval','ApprovalComment','Clause','Company','CustomTable','QcDocumentCategory','Standard','UserAccess','PasswordSetting','SystemTable');
	
	$options = array();
	foreach($hasManies as $modelName => $fields){
		if(!in_array($modelName, $skipModels))$options[$modelName] = $modelName;
	}
	
	if(!empty($options))echo '<div class="col-md-12">' . $this->Form->input('has_many',array('options'=>$options,'multiple'=>'checkbox','label'=>'Also delete linked records in following tables')) . '</div>';
	

	echo '<div class="col-md-12">Record once deleted can not be retrieved. Are you sure you want to continue?</div>';
	echo '<div class="col-md-12"> <div class="btn-group">'.$this->Form->submit('Yes, Delete Record?',array('class'=>'btn btn-sm btn-danger','div'=>false));
	echo $this->Html->link('No, go back',array('action'=>'index','custom_table_id'=>$this->request->params['named']['custom_table_id'],'qc_document_id'=>$this->request->params['named']['qc_document_id']), array('class'=>'btn btn-sm btn-success')).'</div>	</div>';
	echo $this->Form->end();
	?>	
</div>
