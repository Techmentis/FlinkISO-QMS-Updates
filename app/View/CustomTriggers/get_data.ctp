<?php
$options = array('id'=>'CustomTriggerChangedFieldValue', 'class'=>'form-control');
if (!empty($hasOptions)) $options['options'] = $result;
else $options['type'] = 'text';
echo $this->Form->input('CustomTrigger.changed_field_value', $options);
?>
<script type="text/javascript">
	if ($('#CustomTriggerChangedFieldValue').is('select')) $("#CustomTriggerChangedFieldValue").chosen();
</script>
