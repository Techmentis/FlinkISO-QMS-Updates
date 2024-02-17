<?php
	if( isset($doc_not_found) && $doc_not_found == true){
		echo "<div class='alert alert-danger'><i class='fa fa-warning'></i> Document not found for this table</div>";
	}
  echo $this->Html->css(array('code-input-main/code-input.min','prism'));
  echo $this->fetch('css');
  echo $this->Html->script(array(
  	'code-input-main/plugins/prism-core.min',
  	'code-input-main/plugins/prism-autoloader.min',
  	'code-input-main/code-input.min',
  	'code-input-main/plugins/autodetect',
  	'code-input-main/plugins/indent',
	 ));
echo $this->fetch('script');
?>
<script>
	$().ready(function(){
		codeInput.registerTemplate("add", codeInput.templates.prism(Prism, [new codeInput.plugins.Indent()]));	
		codeInput.registerTemplate("edit", codeInput.templates.prism(Prism, [new codeInput.plugins.Indent()]));	
	})
	
</script>

<style type="text/css">
	.btn .badge{
		position: absolute;
		font-size: 8px;
		padding: 3px 5px;
		margin-top: -5px;
		z-index: 2;
	}
	.btn:hover{
	/*	margin-right: -1px;
		margin-left: -1px !important;
		border: 1px solid transparent;*/
	}
	.no-margin-bottom{
		padding: 15px 15px 0 15px;
	}
</style>
<?php echo $this->Session->flash();?>
<div class="row">
	<div class="col-md-12">
		<?php echo $this->element('nav-header-lists',array('postData'=>array('pluralHumanName'=>'Custom Tables','modelClass'=>'CustomTable','options'=>array(),'pluralVar'=>'customTables'))); ?>
		

		<div class="row">
			<div class="col-md-12">		
				
			</div>
		</div>

		<div class="row">
			<?php if($qcDocument)echo "<div class='col-md-12'>".$this->element('qc_doc_header',array('document'=>$qcDocument))."</div>"; ?>
			<?php if($process)echo "<div class='col-md-12'>".$this->element('process_doc_header',array('process'=>$process))."</div>"; ?>
		</div>
		<?php if($qcDocument){ ?>
			<div class="row">
				<div class="col-md-12">
					<?php	
					$key = $key;
					$file_type = $qcDocument['QcDocument']['file_type'];
					$file_name = $qcDocument['QcDocument']['title'];
					$document_number = $qcDocument['QcDocument']['document_number'];
					$document_version = $qcDocument['QcDocument']['revision_number'];

					$file_type = $qcDocument['QcDocument']['file_type'];
					
					if($file_type == 'doc' || $file_type == 'docx'){
						$documentType = 'word';
					}

					if($file_type == 'xls' || $file_type == 'xlsx'){
						$documentType = 'cell';
					}

					$mode = 'view';

					$file_path = $customTable['CustomTable']['id'];


	        // $file = $document_number.'-'.$file_name.'-'.$document_version.'.'.$file_type;
					$file = $document_number.'-'.$file_name.'-'.$document_version;
					$file = ltrim(rtrim($file));
					$file = str_replace('-', '_', $file);
					$file = ltrim(rtrim(strtolower($file)));
					$file = preg_replace('/[\@\.\;\" "-]+/', '_', $file);
					$file = preg_replace('/  */', '_', $file);
					$file = preg_replace('/\\s+/', '_', $file);        
					$file = preg_replace('/-*-/', '_', $file);
					$file = preg_replace('/_*_/', '_', $file);
					$file = $this->requestAction(array('action'=>'clean_table_names',$file));
					$file = $file .'.'.$file_type;
					
					if($customTable['CustomTable']['custom_table_id'])$record_id = $customTable['CustomTable']['custom_table_id'];
					else $record_id = $customTable['CustomTable']['id'];
					

					echo $this->element('onlyoffice',array(
						'url'=>$url,
						'placeholderid'=>$placeholderid,
						'panel_title'=>'Document Viewer',
						'mode'=>$mode,
						'path'=>$file_path,
						'file'=>$file,
						'filetype'=>$file_type,
						'documentType'=>$documentType,
						'userid'=>$this->Session->read('User.id'),
						'username'=>$this->Session->read('User.username'),
						'preparedby'=>$this->Session->read('User.name'),
						'filekey'=>$filekey,
						'record_id'=>$record_id,
						'company_id'=>$this->Session->read('User.company_id'),
						'controller'=>'custom_tables',
						'version_keys'=>$customTable['CustomTable']['version_keys'],
					));
					?>
				</div>
			</div>
		<?php } ?>
		<?php if($process){ ?>
			<div class="row">
				<div class="col-md-12">
					<?php 
					$key = $process['Process']['file_key'];
					$file_type = $process['Process']['file_type'];
					$file_name = $process['Process']['name'];
			        // $document_number = $process['QcDocument']['document_number'];
			        // $document_version = $process['QcDocument']['revision_number'];

					$file_type = $process['Process']['file_type'];
					
					if($file_type == 'doc' || $file_type == 'docx'){
						$documentType = 'word';
					}

					if($file_type == 'xls' || $file_type == 'xlsx'){
						$documentType = 'cell';
					}

					$mode = 'edit';

					$file_path = $process['Process']['id'];


					$file = $file_name.'.'.$file_type;

					echo $this->element('onlyoffice',array(
						'url'=>$url,
						'placeholderid'=>$placeholderid,
						'panel_title'=>'Document Viewer',
						'mode'=>$mode,
						'path'=>$file_path,
						'file'=>$file,
						'filetype'=>$file_type,
						'documentType'=>$documentType,
						'userid'=>$this->Session->read('User.id'),
						'username'=>$this->Session->read('User.username'),
						'preparedby'=>$masterListOfFormat['PreparedBy']['name'],
						'filekey'=>$key,            
						'record_id'=>$process['Process']['id'],
						'company_id'=>$this->Session->read('User.company_id'),
						'controller'=>$this->request->controller,
					));
					?>
				</div>
			</div>
		<?php } ?>
		<div class="row">
			<div class="col-md-6 no-margin-bottom">
				<div class="box box-default">
					<div class="box-header"><h3 class="box-title" style="width:100%">Table Details <span class="pull-right"><i class="fa fa-database"></i></span></h3></div>
					<div class="box-body" id="table-body-div">

							<table class="table">
								<tr><th width="120">Name</th><td><?php echo $customTable['CustomTable']['name']?></td></tr>
								<tr><th>Table Name</th><td><?php echo $customTable['CustomTable']['table_name']?></td></tr>
								<tr><th>Version</th><td><?php echo $customTable['CustomTable']['table_version']?></td></tr>
								<tr><th>Description</th><td><?php echo $customTable['CustomTable']['description']?></td></tr>
								<tr><th>Status</th><td><?php echo $customTable['CustomTable']['table_locked']? 'Unlocked':'Locked';?></td></tr>
								<tr><th>Schedule</th><td><?php echo $schedules[$customTable['QcDocument']['schedule_id']];?></td></tr>
								<tr><td colspan="2"><strong>Shared With</strong></td></tr>
								<tr><th>Departments</th><td>
									<?php
									if($process){
										$users = json_decode($customTable['Process']['process_owners']);
									}
									foreach($users as $user){
										echo $departments[$user].', ';
									}
									?>
								</td></tr>
								<tr><th>Branches</th><td>
									<?php 
									if($process){
										$branchess = json_decode($customTable['Process']['applicable_to_branches']);
									}
									foreach($branchess as $branch){						
										echo $branches[$branch].', ';
									}
									?>
								</td></tr>
								<tr><th>Users</th><td></td></tr>
								<tr>
									<th>Class</th><td><?php echo Inflector::classify($customTable['CustomTable']['table_name']);?> (_tbl/ _div)</td>
								</tr>								
							</table>
							
							<div class="table-body-div">
								<h4>Table Fields</h4>
								<table class="table table-bordered">
									<tr>
										<th>Field Name</th>
										<th>Field Id</th>
										<th>Linked To (Model)</th>
									</tr>									
										<?php 
											$fields = json_decode($customTable['CustomTable']['fields'],true);
												foreach($fields as $field){
													if($field['data_type'] != 'belogsTos'){
														echo "<tr>";
															echo "<td>".$field['field_name']."</td>";
															echo "<td>".Inflector::classify($customTable['CustomTable']['table_name']).Inflector::classify($field['field_name'])."</td>";
															if($field['linked_to'] != -1)echo "<td><strong>".Inflector::classify($field['linked_to'])."</strong></td>";
															else echo "<td>-</td>";
														echo "</tr>";
													}else{
														echo "<tr>";
															echo "<td>".base64_decode($field['field_label']).".".$field['linked_to_field_name']."</td>";
															echo "<td>belongsTos".Inflector::classify(base64_decode($field['field_label']).Inflector::classify($field['linked_to_field_name']))."</td>";
															if($field['linked_to'] != -1)echo "<td><strong>".Inflector::classify($field['linked_to_field_name'])."</strong></td>";
															else echo "<td>-</td>";
														echo "</tr>";
													}														
												}

										?>
										<tr><th colspan="2">Default Fields</th></tr>
	<tr><td>qc_document_id</td><td><?php echo Inflector::classify($customTable['CustomTable']['table_name']).Inflector::classify('qc_document_id');?></td><td><strong>Document</strong></td></tr>
	<tr><td>process_id</td><td><?php echo Inflector::classify($customTable['CustomTable']['table_name']).Inflector::classify('process_id');?></td><td><strong>Process</strong></td></tr>
	<tr><td>custom_table_id</td><td><?php echo Inflector::classify($customTable['CustomTable']['table_name']).Inflector::classify('custom_table_id');?></td><td><strong>Table</strong></td></tr>
	<tr><td>file_id</td><td><?php echo Inflector::classify($customTable['CustomTable']['table_name']).Inflector::classify('file_id');?></td><td><strong>Files</strong></td></tr>
	<tr><td>prepared_by</td><td><?php echo Inflector::classify($customTable['CustomTable']['table_name']).Inflector::classify('prepared_by');?></td><td><strong>Employee</strong></td></tr>
	<tr><td>approved_by</td><td><?php echo Inflector::classify($customTable['CustomTable']['table_name']).Inflector::classify('approved_by');?></td><td><strong>Employee</strong></td></tr>
	<tr><td>created_by</td><td><?php echo Inflector::classify($customTable['CustomTable']['table_name']).Inflector::classify('created_by');?></td><td><strong>User</strong></td></tr>
	<tr><td>modified_by</td><td><?php echo Inflector::classify($customTable['CustomTable']['table_name']).Inflector::classify('modified_by');?></td><td><strong>User</strong></td></tr>
	<tr><td>created</td><td><?php echo Inflector::classify($customTable['CustomTable']['table_name']).Inflector::classify('created');?></td><td></td></tr>
	<tr><td>modified</td><td><?php echo Inflector::classify($customTable['CustomTable']['table_name']).Inflector::classify('modified');?></td><td></td></tr>
	<tr><td>publish</td><td><?php echo Inflector::classify($customTable['CustomTable']['table_name']).Inflector::classify('publish');?></td><td></td></tr>
										<tr><th colspan="2">Custom Fields</th></tr>
										<tr>
											<th>Field Name</th>
											<th>Field Id</th>
											<th>Linked To</th>
										</tr>
									</table>
									<br /><br />
								</div>
<div class="box-footer text-right">								
<?php 
if($customTable['CustomTable']['publish'] == 1){

	echo $this->Html->link('<i class="fa fa fa-check-square-o text-success fa-lg "></i>',array('action'=>'hold',$customTable['CustomTable']['id']),array('class'=>'btn btn-sm tooltip1 ', 'escape'=>false, 'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'bottom',  'title'=> 'Ubnpublish this table'));
}
if($customTable['CustomTable']['publish'] == 0){

	echo $this->Html->link('<i class="fa fa-minus-square-o text-danger fa-lg "></i>',array('action'=>'publish',$customTable['CustomTable']['id']),
		array(
			'class'=>'btn btn-sm tooltip1 ', 
			'escape'=>false, 
			'data-toggle'=>'tooltip', 
			'data-trigger'=>'hover', 
			'data-placement'=>'bottom',  
			'title'=> 'Publish this table')
	);
}

if($customTable['CustomTable']['table_locked'] == 0)echo $this->Html->link('<i class="fa fa-lock text-success fa-lg "></i>',array('action'=>'unlock',$customTable['CustomTable']['id']),array('class'=>'btn btn-sm tooltip1 ', 'escape'=>false, 'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'bottom',  'title'=> 'Unlock table'));

else if($customTable['CustomTable']['table_locked'] == 1) echo $this->Html->link('<i class="fa fa-unlock text-danger fa-lg "></i>',array('action'=>'lock',$customTable['CustomTable']['id']),array('class'=>'btn btn-sm tooltip1 ', 'escape'=>false, 'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'bottom',  'title'=> 'Lock table'));


if($customTable['CustomTable']['table_locked'] == 1){

	if($customTable['CustomTable']['custom_table_id'] == ''){

		echo $this->Html->link('<i class="fa fa-refresh text-warning fa-lg "></i>',array('action'=>'recreate','timestamp'=>date('ymdhis'), $customTable['CustomTable']['id']),array('class'=>'btn btn-sm tooltip1 ', 'escape'=>false, 'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'bottom',  'title'=> 'Recreate this table'));
	}else{

		echo $this->Html->link('<i class="fa fa-refresh text-warning fa-lg "></i>',array('action'=>'recreate_child',$customTable['CustomTable']['id'],'timestamp'=>date('ymdhis')),array('class'=>'btn btn-sm tooltip1 ', 'escape'=>false, 'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'bottom',  'title'=> 'Recreate this table'));
	}	

	if($customTable['CustomTable']['custom_table_id'] == ''){

		echo $this->Html->link('<i class="fa fa-trash-o text-danger fa-lg "></i>',array('action'=>'delete',$customTable['CustomTable']['id']),array('class'=>'btn btn-sm tooltip1 ', 'escape'=>false, 'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'bottom',  'title'=> 'Delete this table'));
	}else{

		echo $this->Html->link('<i class="fa fa-trash-o text-danger fa-lg "></i>',array('action'=>'delete_child',$customTable['CustomTable']['id']),array('class'=>'btn btn-sm tooltip1 ', 'escape'=>false, 'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'bottom',  'title'=> 'Delete this table'));
	}
}

if($customTable['CustomTable']['custom_table_id'] == '') 	echo $this->Html->link('<i class="fa fa-link text-info fa-lg "></i>',array('action'=>'add_child','custom_table_id'=> $customTable['CustomTable']['id'],'qc_document_id'=>$customTable['CustomTable']['qc_document_id'],'process_id'=>$customTable['CustomTable']['process_id']),array('class'=>'btn btn-sm tooltip1 ', 'escape'=>false, 'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'bottom',  'title'=> 'Link new table to this table'));
?>
</div>

				</div>
			</div>
		</div>
		<div class="col-md-6 no-margin-bottom">
			<div id="processpanel"><i class="fa fa-refresh fa-spin"></i></div>
			<script type="text/javascript">
				$("#processpanel").load("<?php echo Router::url('/', true); ?>/custom_tables/link_processes/<?php echo $customTable['CustomTable']['id'];?>");
			</script>
			<div id="piechartpanels"><i class="fa fa-refresh fa-spin"></i></div>
			<script type="text/javascript">
				$("#piechartpanels").load("<?php echo Router::url('/', true); ?>/graph_panels/custom_table/<?php echo $customTable['CustomTable']['id'];?>");
			</script>
			<div id="emailTriggers"><i class="fa fa-refresh fa-spin"></i></div>
			<script type="text/javascript">
				$("#emailTriggers").load("<?php echo Router::url('/', true); ?>/custom_triggers/add/custom_table_id:<?php echo $customTable['CustomTable']['id'];?>");
			</script>
			<div id="loadlocks"><i class="fa fa-refresh fa-spin"></i></div>
			<script type="text/javascript">
				$("#loadlocks").load("<?php echo Router::url('/', true); ?>/record_locks/add/<?php echo $customTable['CustomTable']['id'];?>");
			</script>
			<div id="create_tasks"><i class="fa fa-refresh fa-spin"></i></div>
			<script type="text/javascript">
				$("#create_tasks").load("<?php echo Router::url('/', true); ?>/custom_table_tasks/add/<?php echo $customTable['CustomTable']['id'];?>");
			</script>
			<div id="add_scripts"><i class="fa fa-refresh fa-spin"></i></div>
			<script type="text/javascript">
				$("#add_scripts").load("<?php echo Router::url('/', true); ?>/custom_tables/code_input_main/<?php echo $customTable['CustomTable']['id'];?>");
			</script>
			<div class="row">
				<div class="col-md-12">
					<h4>Available JS APIs (Local APIs)</h4>
					<p><strong>API Function: return_value_for_dropdown:</strong><br/>Sample Js Code<br />

<code-input name="return_value_for_dropdown" required id="return_value_for_dropdown" style="resize: both; overflow: hidden; " lang="JavaScript" placeholder="Write some JavaScript!" template="add">$().ready(function(){
	$("#fieldIdforWhichonChangeFunctionToBeCalled").on("change",function(){
		$.ajax({
			url: "http(s)://your_local_address/current_table_name/return_value_for_dropdown/ModelForWhichValueIsToBeFetched/ModelFieldName/"+this.value,
				success: function(data, result) {
					$('select[id="DropDownFieldToBeChanged"]').find('option[value='+data+']').attr("selected",true).trigger('chosen:updated');
			},			
		});
	});
});</code-input>
					</p>
					<p><strong>API Function: return_options_for_dropdown:</strong><br/>Sample Js Code<br />
<code-input name="return_options_for_dropdown" required id="return_options_for_dropdown" style="resize: both; overflow: hidden; " lang="JavaScript" placeholder="Write some JavaScript!" template="add">$().ready(function(){
	$("#fieldIdforWhichonChangeFunctionToBeCalled").on("change",function(){
		$.ajax({
			url: "http(s)://your_local_address/current_table_name/return_options_for_dropdown/ModelForWhichValueIsToBeFetched/ModelFieldName/"+this.value,
				success: function(data, result) {
					$('select[id="DropDownFieldToBeChanged"]').find('option[value='+data+']').attr("selected",true).trigger('chosen:updated');
			},			
		});
	});
});</code-input>
					</p>
					<p><strong>API Function: fetch_record:</strong><br/>Sample Js Code<br />
<code-input name="fetch_record" required id="fetch_record" style="resize: both; overflow: hidden; " lang="JavaScript" placeholder="Write some JavaScript!" template="add">$().ready(function(){
	$("#fieldIdforWhichonChangeFunctionToBeCalled").on("change",function(){
		$.ajax({
			url: "http(s)://your_local_address/current_table_name/fetch_record/TableNameForDataToBeFetched/FieldFromTheTable/OtherFieldNameToBeFetched/last/"+this.value,
				success: function(data, result) {
				$("#FieldForWhichValueIsToBeUpdated").val(data);
			}
		});
	});
});</code-input>
					</p>
				</div>
			</div>
		</div>
	</div>


	<?php if($childs){ ?>
		<div class="row">	
			<div class="col-md-12"><h4>Child Tables <small><i class="fa fa-chain"></i></small></h4></div>
			<?php foreach($childs as $child){ ?>
				<div class="col-md-6">
					<div class="box box-default">
						<div class="box-header with-border"><h3 class="box-title" style="width:100%"><?php echo $child['CustomTable']['name']?><span class="pull-right"><i class="fa fa-database"></i></span></h3></div>
						<div class="box-body">
							<table class="table table-responsive">
								<tr>
									<th width="120">Table Name</th>
									<td><?php echo $child['CustomTable']['table_name']?></td>
								</tr>
								<tr>
									<th>Version</th>
									<td><?php echo $child['CustomTable']['table_version']?></td>
								</tr>
								<tr>
									<th>Description</th>
									<td><?php echo $child['CustomTable']['description']?></td>
								</tr>
								<tr><th>Fields</th><td>
									<ul><?php 						
									$fields = json_decode($child['CustomTable']['fields'],true);						
									foreach($fields as $field){
										echo "<li>" . $field['field_name'] . "</li>";							
									}
								?></td></tr>
								<tr><th>Linked To</th><td><ul><?php 						
								$fields = json_decode($child['CustomTable']['fields'],true);
								foreach($fields as $field){
									if($field['linked_to'] != -1){
										echo "<li>".$field['field_name'] . " : <strong>".$field['linked_to']."</strong> </li>";
									}
								}				
							?></td></tr>
							<tr>
								<td colspan="2" class="text-right">
									<?php 
									if($child['CustomTable']['publish'] == 1){
										echo $this->Html->link('<i class="fa fa-minus-square-o fa-lg"></i>',array('action'=>'hold',$child['CustomTable']['id']),array('class'=>'btn btn-sm tooltip1 ', 'escape'=>false, 'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'left', 'title'=> 'Ubnpublish this table'));
									}
									if($child['CustomTable']['publish'] == 0){
										echo $this->Html->link('<i class="fa fa-check-square-o fa-lg text-success"></i>',array('action'=>'publish',$child['CustomTable']['id']),array('class'=>'tooltip1 btn btn-sm ', 'escape'=>false, 'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'left', 'title'=> 'Publish this table'));
									}?>

									<?php  
									if($child['CustomTable']['table_locked'] == 0)echo $this->Html->link('<i class="fa fa-lock fa-lg text-danger"></i>',array('action'=>'unlock',$child['CustomTable']['id']),array('class'=>'btn btn-sm tooltip1 ', 'escape'=>false, 'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'left', 'title'=> 'Unlock table'));
									else if($child['CustomTable']['table_locked'] == 1) echo $this->Html->link('<i class="fa fa-unlock fa-lg text-danger"></i>',array('action'=>'lock',$child['CustomTable']['id']),array('class'=>'btn btn-sm tooltip1 ', 'escape'=>false, 'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'left', 'title'=> 'Lock table'));
									?>

									<?php 
									// if($child['CustomTable']['publish'] == 0){
									if($child['CustomTable']['custom_table_id'] == ''){
										echo $this->Html->link('<i class="fa fa-refresh fa-lg text-warning"></i>',array('action'=>'recreate',$child['CustomTable']['id'],'qc_document_id'=>$child['CustomTable']['qc_document_id'],'process_id'=>$child['CustomTable']['process_id'],'timestamp'=>date('ymdhis')),array('class'=>'tooltip1 btn btn-sm ', 'escape'=>false, 'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'left', 'title'=> 'Recreate this table'));	
									}else{
										echo $this->Html->link('<i class="fa fa-cogs fa-lg text-warning"></i>',array('action'=>'view',$child['CustomTable']['id'],'qc_document_id'=>$child['CustomTable']['qc_document_id'],'process_id'=>$child['CustomTable']['process_id'],'timestamp'=>date('ymdhis')),array('class'=>'tooltip1 btn btn-sm ', 'escape'=>false, 'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'left', 'title'=> 'Recreate this table'));
										// }									
									}?>

									<?php  if($child['CustomTable']['table_locked'] == 1){
										if($child['CustomTable']['custom_table_id'] == ''){
											echo $this->Html->link('<i class="fa fa-trash-o fa-lg text-danger"></i>',array('action'=>'delete',$child['CustomTable']['id']),array('class'=>'btn btn-sm tooltip1 ', 'escape'=>false, 'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'left', 'title'=> 'Delete this table'));
										}else{
											echo $this->Html->link('<i class="fa fa-trash-o fa-lg text-danger"></i>',array('action'=>'delete_child',$child['CustomTable']['id']),array('class'=>'btn btn-sm tooltip1 ', 'escape'=>false, 'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'left', 'title'=> 'Delete this table'));
										}
									}?>
									<?php									
									if($child['CustomTable']['custom_table_id'] == '') echo $this->Html->link('<i class="fa fa-link text-info"></i>',array('action'=>'add_child','custom_table_id'=> $child['CustomTable']['id'],'qc_document_id'=>$child['CustomTable']['qc_document_id']),array('class'=>'btn btn-sm tooltip1 ', 'escape'=>false, 'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'left', 'title'=> 'Link new table to this table'));
									?>	
								</td>
							</tr>
						</table>
					</div>
				</div>
			</div>
		<?php } ?>	
	</div>	
</div>
<?php } ?>
<script type="text/javascript">
	$().ready(function(){
		$(".table-body-div").width($("#table-body-div").width()-20).css('overflow-y','scroll');
	})
</script>
