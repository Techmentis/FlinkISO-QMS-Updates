<?php echo $this->Form->input('CustomTable.field_value',array('id'=>'CustomTableFieldValue', 'class'=>'form-control','options'=>$result,'default'=>$this->request->params['named']['value'])); ?>
<script type="text/javascript">
	$("#CustomTableFieldValue").chosen();
</script>