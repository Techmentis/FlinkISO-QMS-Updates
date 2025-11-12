<style>
/*input[type="checkbox"] {
	margin-bottom: 0px;
	margin-top : 1px !important
}*/
/*.label, .badge{ font-size: 70%}
.h4-title {font-size: 22px !important;}
.pdf-btn{padding: 3px 20px}
.pdf-btn a{color: #777777 !important;}*/
</style>
<div class="btn-group btn-no-border" style="padding-top:0px" >

	<?php $controller = $this->request->controller;?>
	<?php if($this->request->params['named']['parent_record_id']){
		echo $this->Html->link('<i class="fa fa-file-o"></i>',array('controller'=>$controller, 'action'=>'view',$postVal,'qc_document_id'=>$qc_document_id,'process_id'=>$process_id,'custom_table_id'=>$custom_table_id, 'compare' => 'yes','timestamp'=>date('ymdhis')),array('target'=>'_blank','escape'=>false,'class'=>'tooltip1 btn btn-sm btn-default','data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'left', 'title'=> 'View File'));
	}else{
		echo $this->Html->link('<i class="fa fa-television"></i>',array('controller'=>$controller, 'action'=>'view',$postVal,'qc_document_id'=>$qc_document_id,'process_id'=>$process_id,'custom_table_id'=>$custom_table_id,'compare' => 'yes','timestamp'=>date('ymdhis')),array('class'=>'tooltip1 btn btn-sm btn-default','escape'=>false, 'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'left', 'title'=> 'View','timestamp'=>date('ymdhis')));
	}
	
	?>
	
	<?php echo $this->Html->link('<i class="fa fa-edit"></i>',array('controller'=>$controller,'action'=>'edit',$postVal,'qc_document_id'=>$qc_document_id,'process_id'=>$process_id,'custom_table_id'=>$custom_table_id,'timestamp'=>date('ymdhis')),array('class'=>'tooltip1 btn btn-sm btn-default','escape'=>false, 'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'left', 'title'=> 'Edit'));?>

	<?php echo $this->Html->link('<i class="fa fa-trash-o"></i>',array('controller'=>$controller,'action'=>'delete',$postVal,'qc_document_id'=>$qc_document_id,'process_id'=>$process_id,'custom_table_id'=>$custom_table_id,'timestamp'=>date('ymdhis')),array('class'=>'tooltip1 btn btn-sm btn-default','escape'=>false, 'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'left', 'title'=> 'Delete'));?>

	<?php 
	// if($this->request->controller == 'processes'){
	// 	echo $this->Html->link('<i class="fa fa-database"></i>',array('controller'=>'custom_tables', 'action'=>'add', 'process_id'=> $postVal,'qc_document_id'=>$qc_document_id,'custom_table_id'=>$custom_table_id),array('class'=>'tooltip1 btn btn-sm btn-default','escape'=>false, 'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'left', 'title'=> 'Add Table'));		
	// }

	if($this->request->controller == 'employees'){
		if($this->Session->read('User.is_mr') == true){

			if($user == null){
				echo $this->Html->link('<i class="fa fa-user"></i>','javascript:void(0);',array('id'=>$postVal.'-user', 'class'=>'tooltip1 btn btn-sm btn-danger empaction','escape'=>false, 'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'left', 'title'=> 'Add User'));			
			}else{
				echo $this->Html->link('<i class="fa fa-gears"></i>',array('controller'=>'users','action'=>'edit',$user,'timestamp'=>date('ymdhis')),array('class'=>'tooltip1 btn btn-sm btn-success empaction','escape'=>false, 'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'left', 'title'=> 'Edit User'));			
			} ?>
			<script type="text/javascript">
				$("#<?php echo $postVal;?>-user").on('click',function(){
					$.ajax({
						url: "<?php echo Router::url('/', true); ?>users/add",
						type: "POST",
						dataType: "json",
						contentType: "application/json; charset=utf-8",
						data: JSON.stringify({ id: '<?php echo $postVal;?>'}),
						beforeSend: function( xhr ) {
							$("#<?php echo $postVal;?>-user i").removeClass('fa-user').addClass('fa-refresh fa-spin');
							$("#<?php echo $postVal;?>-user").next().find('.tooltip-inner').html('Adding');
							$("#<?php echo $postVal;?>-user").tooltip().attr({'data-toggle':'tooltip', 'data-original-title':'Adding','data-placement':'left','data-trigger':'hover'}).tooltip('show');
						},					
						success: function (result) {
							$("#<?php echo $postVal;?>-user").removeClass('btn-danger').addClass('btn-success');
							$("#<?php echo $postVal;?>-user i").removeClass('fa-refresh fa-spin').addClass('fa-check');
							$("#<?php echo $postVal;?>-user").next().find('.tooltip-inner').html('User added');
							$("#<?php echo $postVal;?>-user").tooltip().attr({'data-toggle':'tooltip', 'data-original-title':'User added','data-placement':'left','data-trigger':'hover'}).tooltip('show');
						},
						error: function (err) {
							
						}
					}); 
				});
			<?php } ?>
		</script>

		
	<?php } ?>	
</div>
