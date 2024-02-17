<div class="row">
	<div class="col-md-12"><h4>Select your Change Control table from the dropdown</h4></div>
	<?php echo $this->Form->create('QcDocument',array('action'=>'define_change_history_table'),array('type'=>'post'));?>
	<div class="col-md-12">
		<p class="show_comments">Note : Only text, date and radio fields are allowed. System will automatically add Prepred By & Approved By fields. To link the table, select the table from the dropdown and then select the fields you want to display on "Change Request" tab and click submit.</p>
	</div>
	<?php 
	if(isset($company) && ($company['Company']['change_management_table'] != null || $company['Company']['change_management_table'] != '')){
		$custom_table_id =  $company['Company']['change_management_table'];
		$table_fields = json_decode($company['Company']['change_management_table_fields'],true);		
	}else{
		$custom_table_id = $customTable['CustomTable']['id'];
	}
	echo $this->Form->input('custom_table_id',array('onChange'=>'fetchfields(this.value)', 'default'=>$custom_table_id, 'div'=>array('class'=>'col-md-12')));
		echo "<div class='col-md-12'><br /></div>";
	?>
	<?php
		if(isset($customTable)){			
			$fields = json_decode($customTable['CustomTable']['fields'],true);
			$add = array('text','radio','date','textarea');
			foreach($fields as $field){			
				if(in_array($field['data_type'], $add)){
					$options[$field['field_name']] = base64_decode($field['field_label']);	
				}
				
			}
			echo $this->Form->input('fields',array('multiple'=>'checkbox', 'default'=>$table_fields, 'div'=>array('class'=>'col-md-12'), 'class'=>'checkbox', 'options'=>$options));
		}

	?>	
	<?php
		echo $this->Form->submit('Submit',array('div'=>array('class'=>'col-md-12'), 'class'=>'btn btn-primary btn-sm'));
		echo $this->Form->end();
	?>	
</div>
<script type="text/javascript">
	function fetchfields(val){
		window.location.replace("<?php echo Router::url('/', true); ?>qc_documents/define_change_history_table/custom_table_id:"+ val);
	}
</script>
