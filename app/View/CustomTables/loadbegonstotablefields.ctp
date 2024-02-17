<?php 
$skip = array('id','sr_no','created','created_by','modified','modified_by','custom_table_id','soft_delete','system_table_id','devision_id','branchid','departmentid','record_status','status_user_id','company_id');
foreach($allfields as $field){
	if(!in_array($field, $skip))echo '<li class="list-group-item" data="'.$belogsToModel.'" id="'.$field.'" field="'.$field.'">'.$field.' <i class="fa pull-right text-muted"></i></li>';
}?>
