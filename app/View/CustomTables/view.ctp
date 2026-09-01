<?php echo $this->Html->script(array('jquery.validate.min', 'jquery-form.min')); ?>
<?php echo $this->fetch('script'); ?>
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

/*
 * Tab settings belong to the form as a whole, while the field JSON remains the
 * source of truth for which tabs already exist.  This panel is deliberately a
 * front-end preview for now; persistence and generated-form behaviour will be
 * added after the workflow is approved.
 */
$customTableFields = json_decode($customTable['CustomTable']['fields'], true);
if (!is_array($customTableFields)) $customTableFields = array();
$savedTabSettings = isset($customTable['CustomTable']['tab_settings']) ? json_decode($customTable['CustomTable']['tab_settings'], true) : array();
if (!is_array($savedTabSettings)) $savedTabSettings = array();
// Older saved records used the tab name directly as the key. Preserve them
// while allowing child-form tab rules to live alongside ordinary form tabs.
$tabSettings = isset($savedTabSettings['tabs']) && is_array($savedTabSettings['tabs']) ? $savedTabSettings['tabs'] : $savedTabSettings;
$childFormSettings = isset($savedTabSettings['child_forms']) && is_array($savedTabSettings['child_forms']) ? $savedTabSettings['child_forms'] : array();

$formTabs = array();
$visibilityFields = array();
foreach ($customTableFields as $customTableField) {
	$tabName = isset($customTableField['tab_name']) ? trim($customTableField['tab_name']) : '';
	if ($tabName !== '' && $tabName !== '-1' && !isset($formTabs[$tabName])) {
		$formTabs[$tabName] = array(
			'name' => $tabName,
			'group' => isset($customTableField['tab_group']) ? $customTableField['tab_group'] : '',
			'sequence' => isset($customTableField['tab_sequence']) ? $customTableField['tab_sequence'] : '',
		);
	}

	if (
		!empty($customTableField['field_name']) &&
		in_array(isset($customTableField['data_type']) ? $customTableField['data_type'] : '', array('radio', 'dropdown-s'))
	) {
		$options = array();
		if (!empty($customTableField['csvoptions'])) {
			$options = array_values(array_filter(array_map('trim', explode(',', $customTableField['csvoptions']))));
		}
		$visibilityFields[] = array(
			'name' => $customTableField['field_name'],
			'label' => Inflector::humanize($customTableField['field_name']),
			'options' => $options,
		);
	}
}

$tabConfigurationRows = array();
foreach ($formTabs as $formTab) {
	$tabConfigurationRows[] = array(
		'type' => 'tab',
		'key' => $formTab['name'],
		'name' => $formTab['name'],
		'position' => 'Group '.($formTab['group'] !== '' ? $formTab['group'] : '-').' / #'.($formTab['sequence'] !== '' ? $formTab['sequence'] : '-'),
	);
}
foreach ((array)$childs as $child) {
	if (empty($child['CustomTable']['table_name'])) continue;
	$tabConfigurationRows[] = array(
		'type' => 'child_form',
		'key' => $child['CustomTable']['table_name'],
		'name' => $child['CustomTable']['name'],
		'position' => 'Child form tab',
	);
}
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
	.error, .error .chosen-container{
			border: 1px dotted red;
		}
	.tab-configuration-table th, .tab-configuration-table td{
		vertical-align: middle !important;
		color: #3c3c3c !important;
		font-size: 13px !important;
		line-height: 1.4 !important;
	}
	.tab-configuration-table th{
		font-weight: 700 !important;
		background: #f7f7f7 !important;
	}
	.tab-configuration-table .tab-name,
	.tab-configuration-table .tab-position{
		color: #3c3c3c !important;
		display: inline !important;
	}
	.tab-configuration-table .form-group{
		margin-bottom: 0;
	}
	.tab-configuration-rule{
		display: none;
	}
	.tab-configuration-note{
		color: #777;
		font-size: 12px;
	}
	/* Tab labels already identify each panel; do not repeat the old collapsible
	 * box/accordion chrome inside the new configuration tabs. */
	.custom-table-configuration-tabs .ui-tabs-panel > .box,
	.configuration-plain-panel .box{
		border: 0;
		box-shadow: none;
		margin: 0;
	}
	.custom-table-configuration-tabs .ui-tabs-panel > .box > .box-header,
	.configuration-plain-panel .box > .box-header{
		display: none;
	}
	.custom-table-configuration-tabs .ui-tabs-panel > .box > .box-body,
	.configuration-plain-panel .box > .box-body{
		padding: 15px 0;
	}
	.configuration-plain-panel .box.collapsed-box > .box-body{
		display: block !important;
	}
	/* Chosen option lists must escape the jQuery UI tab panel and sit above
	 * adjacent fields/footers. */
	.custom-table-configuration-tabs,
	.custom-table-configuration-tabs .ui-tabs-panel{
		overflow: visible !important;
	}
	.custom-table-configuration-tabs .chosen-container.chosen-with-drop,
	.custom-table-configuration-tabs .chosen-container .chosen-drop{
		z-index: 1060 !important;
	}
	.custom-table-configuration-tabs .ui-tabs-panel > .box > .box-footer,
	.configuration-plain-panel .box > .box-footer{
		border-left: 0;
		border-right: 0;
		border-bottom: 0;
		padding-right: 0;
	}
</style>
<?php echo $this->Session->flash();?>
<div class="row">
	<div class="col-md-12">
		<?php echo $this->element('nav-header-lists', array('postData' => array('pluralHumanName' => 'Custom Tables', 'modelClass' => 'CustomTable', 'options' => array(), 'pluralVar' => 'customTables'))); ?>
	</div>
</div>
<?php if($qcDocument){
	$fileType = $qcDocument['QcDocument']['file_type'];
	$documentType = in_array($fileType, array('doc', 'docx')) ? 'word' : (in_array($fileType, array('xls', 'xlsx')) ? 'cell' : 'pdf');
	$documentFile = $qcDocument['QcDocument']['document_number'].'-'.$qcDocument['QcDocument']['title'].'-'.$qcDocument['QcDocument']['revision_number'];
	$documentFile = $this->requestAction(array('action' => 'clean_table_names', $documentFile)).'.'.$fileType;
	$documentRecordId = $customTable['CustomTable']['custom_table_id'] ? $customTable['CustomTable']['custom_table_id'] : $customTable['CustomTable']['id'];
?>
	<div class="row">
		<div class="col-md-12"><?php echo $this->element('qc_doc_header', array('document' => $qcDocument)); ?></div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<?php echo $this->element('onlyoffice', array(
				'url' => $url,
				'placeholderid' => $placeholderid,
				'panel_title' => 'Document: '.$qcDocument['QcDocument']['title'],
				'mode' => 'view',
				'path' => $customTable['CustomTable']['id'],
				'file' => $documentFile,
				'filetype' => $fileType,
				'documentType' => $documentType,
				'userid' => $this->Session->read('User.id'),
				'username' => $this->Session->read('User.username'),
				'preparedby' => $this->Session->read('User.name'),
				'filekey' => $filekey,
				'record_id' => $documentRecordId,
				'company_id' => $this->Session->read('User.company_id'),
				'controller' => 'custom_tables',
				'version_keys' => $customTable['CustomTable']['version_keys'],
			)); ?>
		</div>
	</div>
<?php } ?>
<div class="row">
	<div class="col-md-12">
		<div id="custom-table-configuration-tabs" class="custom-table-configuration-tabs">
			<ul>
				<li><a href="<?php echo Router::url(array('action' => 'view_tab', $customTable['CustomTable']['id'], 'main')); ?>">Main Form</a></li>
				<?php if($formTabs || $childs){ ?><li><a href="<?php echo Router::url(array('action' => 'view_tab', $customTable['CustomTable']['id'], 'tab_configuration')); ?>">Tab Configuration</a></li><?php } ?>
				<?php if($childs){ ?><li><a href="<?php echo Router::url(array('action' => 'view_tab', $customTable['CustomTable']['id'], 'child_tables')); ?>">Child Forms</a></li><?php } ?>
				<?php if($childDocumentForms){ ?><li><a href="<?php echo Router::url(array('action' => 'view_tab', $customTable['CustomTable']['id'], 'child_document_forms')); ?>">Child Document Forms</a></li><?php } ?>
				<li><a href="<?php echo Router::url(array('action' => 'view_tab', $customTable['CustomTable']['id'], 'rebuild_module')); ?>">Rebuild Module</a></li>
				<li><a href="<?php echo Router::url(array('action' => 'view_tab', $customTable['CustomTable']['id'], 'data_entry')); ?>">Data Entry</a></li>
				<li><a href="<?php echo Router::url(array('action' => 'view_tab', $customTable['CustomTable']['id'], 'permissions')); ?>">Table Permissions</a></li>
				<li><a href="<?php echo Router::url(array('action' => 'view_tab', $customTable['CustomTable']['id'], 'charts_panels')); ?>">Charts and Panels</a></li>
				<li><a href="<?php echo Router::url(array('action' => 'view_tab', $customTable['CustomTable']['id'], 'email_triggers')); ?>">Email Triggers</a></li>
				<li><a href="<?php echo Router::url(array('action' => 'view_tab', $customTable['CustomTable']['id'], 'create_tasks')); ?>">Create Tasks</a></li>
				<li><a href="<?php echo Router::url(array('action' => 'view_tab', $customTable['CustomTable']['id'], 'javascript')); ?>">Add JavaScript</a></li>
			</ul>
		</div>
	</div>
</div>
<script type="text/javascript">
	$().ready(function(){
		function initialiseChosen(panel){
			panel.find('select').each(function(){
				var select = $(this);
				if(select.data('chosen')) select.trigger('chosen:updated');
				else select.chosen({width: '100%'});
			});
			panel.find('.tooltip1').tooltip();
		}
		function loadNestedTabContent(panel){
			panel.find('.ajax-tab-content[data-load-url]').each(function(){
				var target = $(this);
				if(target.data('nested-loaded')) return;
				target.data('nested-loaded', true).load(target.attr('data-load-url'), function(response, status){
					if(status === 'error') target.html('<div class="alert alert-danger">Unable to load this panel. Please refresh the page and try again.</div>');
					else initialiseChosen(target);
				});
			});
		}
		$('#custom-table-configuration-tabs').tabs({
			beforeLoad: function(event, ui){
				ui.jqXHR.fail(function(){
					ui.panel.html('<div class="alert alert-danger">Unable to load this configuration panel. Please refresh the page and try again.</div>');
				});
			},
			load: function(event, ui){
				initialiseChosen(ui.panel);
				loadNestedTabContent(ui.panel);
			}
		});
	});
</script>
<?php return; ?>
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
							<tr><th>Class</th><td><?php echo Inflector::classify($customTable['CustomTable']['table_name']);?> (_tbl/ _div)</td></tr>								
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

							if($customTable['CustomTable']['custom_table_id'] == ''){
								echo $this->Html->link('<i class="fa fa-list-ol fa-lg "></i>',array('action'=>'add_child','custom_table_id'=> $customTable['CustomTable']['id'],'qc_document_id'=>$customTable['CustomTable']['qc_document_id'],'process_id'=>$customTable['CustomTable']['process_id']),array('class'=>'btn btn-sm tooltip1 ', 'escape'=>false, 'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'bottom',  'title'=> 'Link new table to this table'));
								
								echo $this->Html->link('<i class="fa fa-chain fa-lg"></i>',array('controller'=>'approval_processes','action'=>'add','controller_name'=>Inflector::classify($customTable['CustomTable']['table_name']), 'timestamp'=>date('ymdhis'),'custom_table_id'=>$customTable['CustomTable']['id']),array('escape'=>false,'class'=>'tooltip1 btn btn-sm','data-toggle'=>'tooltip', 'data-trigger'=>'hover','data-placement'=>'bottom', 'title'=> 'Add Auto Approval Process'));    	
							} 	
							?>
						</div>

					</div>
				</div>
			</div>
			<div class="col-md-6 no-margin-bottom">
				<div class="box box-primary" id="tab-configuration-panel">
					<div class="box-header with-border">
						<h3 class="box-title" style="width:100%">Tab Configuration <span class="pull-right"><i class="fa fa-folder-o"></i></span></h3>
					</div>
					<div class="box-body">
						<p class="tab-configuration-note">Define when each form tab is available. Leave the visibility rule as <strong>Always visible</strong>, or leave its values unselected, to apply no field-value restriction.</p>
						<?php echo $this->Form->create('CustomTable', array('url' => array('action' => 'update_tab_settings', $customTable['CustomTable']['id']), 'id' => 'update-tab-settings', 'class' => 'form')); ?>
						<?php echo $this->Form->hidden('tab_settings', array('id' => 'CustomTableTabSettings')); ?>
						<?php if($tabConfigurationRows){ ?>
							<div class="table-responsive">
								<table class="table table-bordered tab-configuration-table" id="tab-configuration-table">
									<thead>
										<tr>
											<th>Tab</th>
											<th>Position</th>
											<th>Action visibility</th>
											<th>Visibility rule</th>
										</tr>
										</thead>
									<tbody>
										<?php foreach($tabConfigurationRows as $formTab){
											$settingsForRow = $formTab['type'] === 'child_form' ? $childFormSettings : $tabSettings;
											$formTabSetting = isset($settingsForRow[$formTab['key']]) && is_array($settingsForRow[$formTab['key']]) ? $settingsForRow[$formTab['key']] : array();
											$actionVisibility = isset($formTabSetting['action_visibility']) ? $formTabSetting['action_visibility'] : 'always';
											$visibilityFieldName = isset($formTabSetting['visibility_field']) ? $formTabSetting['visibility_field'] : '';
											$visibilityValues = isset($formTabSetting['visible_when']) && is_array($formTabSetting['visible_when']) ? $formTabSetting['visible_when'] : array();
											$visibilityMode = ($visibilityFieldName !== '' && $visibilityValues) ? 'field' : 'always';
											$visibilityOptions = array();
											foreach($visibilityFields as $visibilityFieldOption){
												if($visibilityFieldOption['name'] === $visibilityFieldName){
													$visibilityOptions = $visibilityFieldOption['options'];
													break;
												}
											}
										?>
											<tr class="tab-configuration-row" data-tab-name="<?php echo h($formTab['key']); ?>" data-tab-type="<?php echo h($formTab['type']); ?>">
												<td><strong class="tab-name"><?php echo h($formTab['name']); ?><?php echo $formTab['type'] === 'child_form' ? ' <small>(Child form)</small>' : ''; ?></strong></td>
												<td><small class="tab-position"><?php echo h($formTab['position']); ?></small></td>
												<td>
													<select class="form-control input-sm tab-action-visibility">
														<option value="always"<?php echo $actionVisibility === 'always' ? ' selected' : ''; ?>>Always visible</option>
														<option value="hide_add"<?php echo $actionVisibility === 'hide_add' ? ' selected' : ''; ?>>Hide on Add</option>
														<option value="hide_edit"<?php echo $actionVisibility === 'hide_edit' ? ' selected' : ''; ?>>Hide on Edit</option>
														<option value="hide_both"<?php echo $actionVisibility === 'hide_both' ? ' selected' : ''; ?>>Hide on Add &amp; Edit</option>
													</select>
												</td>
												<td>
													<div class="form-group">
														<select class="form-control input-sm tab-visibility-mode">
															<option value="always"<?php echo $visibilityMode === 'always' ? ' selected' : ''; ?>>Always visible</option>
															<option value="field"<?php echo $visibilityMode === 'field' ? ' selected' : ''; ?>>Only for a field value</option>
														</select>
													</div>
													<div class="tab-configuration-rule row" style="margin-top:8px;<?php echo $visibilityMode === 'field' ? 'display:block' : ''; ?>">
														<div class="col-xs-6">
															<select class="form-control input-sm tab-visibility-field">
																<option value="">Choose field</option>
																<?php foreach($visibilityFields as $visibilityField){ ?>
																	<option value="<?php echo h($visibilityField['name']); ?>" data-options="<?php echo h(json_encode($visibilityField['options'])); ?>"<?php echo $visibilityFieldName === $visibilityField['name'] ? ' selected' : ''; ?>><?php echo h($visibilityField['label']); ?></option>
																<?php } ?>
															</select>
														</div>
														<div class="col-xs-6">
															<select class="form-control input-sm tab-visibility-values" multiple<?php echo $visibilityOptions ? '' : ' disabled'; ?>>
																<?php foreach($visibilityOptions as $visibilityOption){ ?>
																	<option value="<?php echo h($visibilityOption); ?>"<?php echo in_array($visibilityOption, $visibilityValues) ? ' selected' : ''; ?>><?php echo h($visibilityOption); ?></option>
																<?php } ?>
															</select>
														</div>
													</div>
												</td>
											</tr>
										<?php } ?>
									</tbody>
								</table>
							</div>
							<?php if(!$visibilityFields){ ?>
								<p class="help-block">Add a radio or single-select field, such as Audit Status, before configuring a field-value rule.</p>
							<?php } ?>
						<?php }else{ ?>
							<div class="alert alert-info" style="margin-bottom:0">No tabs or child forms have been defined. Assign a Tab Name to fields or link a child form first.</div>
						<?php } ?>
					</div>
					<div class="box-footer text-right">
						<?php echo $this->Form->submit('Save Tab Configuration', array('class' => 'btn btn-sm btn-success', 'id' => 'save-tab-settings')); ?>
						<?php echo $this->Form->end(); ?>
					</div>
				</div>
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
<?php } ?>
<script type="text/javascript">
	$().ready(function(){
		$(".table-body-div").width($("#table-body-div").width()-20).css('overflow-y','scroll');

		$('#tab-configuration-panel').on('change', '.tab-visibility-mode', function(){
			var rule = $(this).closest('.tab-configuration-row').find('.tab-configuration-rule');
			rule.toggle($(this).val() === 'field');
		});

		$('#tab-configuration-panel').on('change', '.tab-visibility-field', function(){
			var values = $(this).closest('.tab-configuration-row').find('.tab-visibility-values');
			var selected = $(this).find('option:selected');
			var options = [];

			try {
				options = JSON.parse(selected.attr('data-options') || '[]');
			} catch (error) {
				options = [];
			}

			values.empty();
			$.each(options, function(index, option){
				$('<option/>', { value: option, text: option }).appendTo(values);
			});
			values.prop('disabled', options.length === 0);
			values.trigger('chosen:updated');
		});

		$('#update-tab-settings').on('submit', function(){
			var settings = {tabs: {}, child_forms: {}};

			$('#tab-configuration-table .tab-configuration-row').each(function(){
				var row = $(this);
				var tabName = row.attr('data-tab-name');
				var visibilityMode = row.find('.tab-visibility-mode').val();
				var visibilityField = visibilityMode === 'field' ? row.find('.tab-visibility-field').val() : '';
				var visibleWhen = visibilityMode === 'field' ? (row.find('.tab-visibility-values').val() || []) : [];

				var setting = {
					action_visibility: row.find('.tab-action-visibility').val(),
					visibility_field: visibilityField || '',
					visible_when: visibleWhen
				};
				if(row.attr('data-tab-type') === 'child_form') settings.child_forms[tabName] = setting;
				else settings.tabs[tabName] = setting;
			});

			$('#CustomTableTabSettings').val(JSON.stringify(settings));
		});
	})
</script>
<?php

		if($customTable['CustomTable']['creators'])$creators = json_decode($customTable['CustomTable']['creators'],true);
		else $creators = json_decode($qcDocument['QcDocument']['editors'],true);

		if($customTable['CustomTable']['viewers'])$viewers = json_decode($customTable['CustomTable']['viewers'],true);
		else $viewers = json_decode($qcDocument['QcDocument']['user_id'],true);

		if($customTable['CustomTable']['editors'])$editors = json_decode($customTable['CustomTable']['editors'],true);
		else $editors = json_decode($qcDocument['QcDocument']['editors'],true);

		if($customTable['CustomTable']['approvers'])$approvers = json_decode($customTable['CustomTable']['approvers'],true);
		else $approvers = json_decode($qcDocument['QcDocument']['editors'],true);
		?>
		
		<?php if($this->Session->read('User.is_mr') == true){ ?>
		<?php echo $this->Form->create('CustomTable',array('url'=>'updatedataentry/'.$customTable['CustomTable']['qc_document_id'].'/'.$customTable['CustomTable']['id'],'id'=>'updatedataentry','class'=>'form','role' => 'form', ),array('class' => 'form'));?>
		<div class='row'>
			<div class='col-md-12'>
				
				<div class="box box-default">
					<div class="box-header with-border">
						<i class="fa fa-database"></i>
						<h3 class="box-title">Data Entry</h3>
					</div>
					<div class="box-body">
						<div class="row">
							<?php 
							
							// echo "<div class='col-md-6'><br /><div class='nomargin-checkbox'><label>Do you want this document to be shared with users for scheduled data enrty? If yes, click YES below. You must define schedule & data type.</label>".$this->Form->input('add_records',array('type'=>'checkbox','label'=>'Yes')) . '</div></div>'; 
							echo "<div class='col-md-2'>".$this->Form->input('QcDocument.schedule_id',array(
								'required'=>'required',
								'default'=>$customTable['QcDocument']['schedule_id'])) . '</div>'; 
							
							echo "<div class='col-md-5'>".$this->Form->input('QcDocument.data_type',array('required'=>'required', 'options'=>$customArray['dataTypes'],
								'default'=>$customTable['QcDocument']['data_type'])) . '</div>'; 
							
							echo "<div class='col-md-5'>".$this->Form->input('QcDocument.data_update_type',array('required'=>'required','options'=>$customArray['dataUpdateTypes'],'default'=>$customTable['QcDocument']['data_update_type'])) . '</div>';
						?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php echo $this->Form->submit('Update Dataentry',array('class'=>'btn btn-sm btn-success','id'=>'updatedataentrybutton'));
		echo $this->Form->end();?>

<script>
	$.validator.setDefaults({
    	ignore: null,
    	errorPlacement: function(error, element) {    		
			if(element['context']['className'] == 'form-control select error'){
				$(element).next('.chosen-container').addClass('error');
			}else if(element['context']['className'] == 'radio error'){
				$(element).next('legend').addClass('error');
			}else{
				$(element).after(error); 
			}
		}
    });

    $("#updatedataentry").validate({
    	"data[QcDocument][schedule_id]": {
			greaterThanZero: true,
		},
		"data[QcDocument][data_type]": {
			greaterThanZero: true,
		},
		"data[QcDocument][data_update_type]": {
			greaterThanZero: true,
		}
    });

	$().ready(function(){	
		$('select').chosen();
		$("#updatedataentrybutton").click(function(){			
            if($('#updatedataentry').valid()){
               // $("#submit_id").prop("disabled",true);
               // $("#submit-indicator").show();
               $('#updatedataentry').submit();
           }else{
           	console.log('sas');
           }

       });
	});
</script>		


		<?php } ?>

		<?php if($this->Session->read('User.is_mr') == true){ ?>
		<?php echo $this->Form->create('CustomTable',array('url'=>'updateaccess/'.$customTable['CustomTable']['id'],'id'=>'updateaccess'),array());?>
			<div class="row">
				<div class="col-md-12">
					<div class="box box-default">
						<div class="box-header">
							<h4 class="box-title">Table Permissions</h4>
						</div>
						<div class="box-body">
							<div class="row">
								<div class="col-md-12">
									<table class="table table-bordered">
										<tr>
											<th>Creators</th>
											<th>Viewers</th>
											<th>Editors</th>
											<th>Approvers</th>
										</tr>
										<tr>
											<tr>
												<td><?php echo $this->Form->input('creators',array(
													'label'=>false,
													'multiple'=>true, 
													'options'=>$users,
													'default'=>$creators,
												));?>												
											</td>
												<td><?php echo $this->Form->input('viewers',array(
													'label'=>false,
													'multiple'=>true, 
													'options'=>$users,
													'default'=>$viewers,
												));?>							
											</td>
											<td><?php echo $this->Form->input('editors',array(
													'label'=>false,
													'multiple'=>true, 
													'options'=>$users,
													'default'=>$editors,
												));?>												
											</td>
											<td><?php echo $this->Form->input('approvers',array(
													'label'=>false,
													'multiple'=>true, 
													'options'=>$users,
													'default'=>$approvers,												
												));?>												
											</td>
										</tr>
									</tr>
									<tr>
										<td>Create, View, Edit, Delete</td>
										<td>View Only</td>
										<td>View, Edit</td>
										<td>View, Edit, Approve</td>
									</tr>
								</table>
							</div>							
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php echo $this->Form->submit('Update Access',array('class'=>'btn btn-sm btn-success','id'=>'updateaccessbutton'));
		echo $this->Form->end();?>
		<?php } ?>
</div>
