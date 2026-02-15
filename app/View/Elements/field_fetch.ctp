<?php 	
$field_details = json_decode(base64_decode($this->request->params['named']['field_details']),true);
	// add rules
	// mandatory
	if($field_details['mandatory'] == 0){
		$required = false;
	}

	if($field_details['add_disabled'] == 1){		
		if($this->request->params['named']['action'] == 'add'){
			$disabled = true;	
		}
		
	}
	if(!$label == false)$label = Inflector::Humanize($fieldTobeChanged);

	if($type == 'checkbox'){
		echo '<label>'.Inflector::humanize(Inflector::underscore($selectedModelName)).'</label>';
		echo $this->Form->input('belongsTos.'.$model.'.'.$fieldTobeChanged,array(
			'default'=>$record_value,
			'disabled'=>$disabled, 
			'required'=>$required, 
			'type'=>'checkbox',
			'class'=>'',
			// 'label'=>Inflector::humanize(Inflector::underscore($selectedModelName)).': '. Inflector::Humanize($fieldTobeChanged)
			)
		);		
	}else{
		// render text fields
		if(($fieldDetails['type'] == 'string' || $fieldDetails['type'] == 'integer') && ($fieldDetails['length'] != 36 && $fieldDetails['length'] != 1)){	
			// echo '<label>'.Inflector::humanize(Inflector::underscore($selectedModelName)).'</label>';	
			echo $this->Form->input('belongsTos.'.$model.'.'.$fieldTobeChanged,array(
			'default'=>$record_value,
			'disabled'=>$disabled, 
			'required'=>$required, 
			'class'=>'form-control',
			'label'=> $label
		));
			return;
		}

		if($fieldDetails['type'] == 'boolean'){
			// load radio
			echo '<label>'.Inflector::humanize(Inflector::underscore($selectedModelName)).'</label>';
			echo $this->Form->input('belongsTos.'.$model.'.'.$fieldTobeChanged,array(
				'default'=>$record_value,
				'disabled'=>$disabled, 
				'required'=>$required, 
				'type'=>'radio','class'=>'',
				'legend'=> $label,
				'options'=>$values));
			return;
		}

		if($fieldDetails['type'] == 'integer' && ($fieldDetails['length'] == null  || $fieldDetails['length'] == 1 )){
			// load radio
			echo '<label>'.Inflector::humanize(Inflector::underscore($selectedModelName)).'</label>';		
			echo $this->Form->input('belongsTos.'.$model.'.'.$fieldTobeChanged,array(
				'default'=>$record_value,
				'disabled'=>$disabled, 
				'required'=>$required, 
				'type'=>'radio','class'=>'',
				'legend'=>$label,
				'options'=>$values));
			return;
		}	

		if($fieldDetails['type'] == 'date'){
			echo '<label>'.Inflector::humanize(Inflector::underscore($selectedModelName)).'</label>';
			echo $this->Form->input('belongsTos.'.$model.'.'.$fieldTobeChanged,array(
				'default'=>$record_value,
				'disabled'=>$disabled, 
				'required'=>$required, 
				'class'=>'form-control',
				'label'=>$label
			));
			return;
		}

		if($fieldDetails['type'] == 'datetime'){
			// load add cal
			echo '<label>'.Inflector::humanize(Inflector::underscore($selectedModelName)).'</label>';
			echo $this->Form->input('belongsTos.'.$model.'.'.$fieldTobeChanged,array(
				'default'=>$record_value,
				'disabled'=>$disabled, 
				'required'=>$required, 
				'class'=>'form-control',
				'label'=> $label
			));
			return;
		}

		if($fieldDetails['type'] == 'string' && $fieldDetails['length'] == 36){
			// load drop down
			echo '<label>'.Inflector::humanize(Inflector::underscore($selectedModelName)).'</label>';
			echo $this->Form->input('belongsTos.'.$model.'.'.$fieldTobeChanged,array(
				'default'=>$record_value,
				'disabled'=>$disabled, 
				'required'=>$required, 
				'class'=>'form-control',
				'label'=> $label,
				'options'=>$values));		
			return;
		}
	}

?>

<script type="text/javascript">
	$().ready(function(){$("select").chosen()});
</script>
