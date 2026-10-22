<?php
$selectedTab = isset($this->request->params['pass'][1]) ? $this->request->params['pass'][1] : 'main';
$table = $customTable['CustomTable'];
$isMr = (bool)$this->Session->read('User.is_mr') || (bool)$this->Session->read('User.is_super_user');
?>
<?php if($selectedTab === 'main'){ ?>
	<div class="box box-default">
		<div class="box-header with-border"><h3 class="box-title"><?php echo h(__('Main Form')); ?> <small><?php echo h($table['table_name']); ?></small></h3></div>
		<div class="box-body">
			<table class="table table-bordered">
				<tr><th width="180"><?php echo h(__('Name')); ?></th><td><?php echo h($table['name']); ?></td></tr>
				<tr><th><?php echo h(__('Table Name')); ?></th><td><?php echo h($table['table_name']); ?></td></tr>
				<tr><th><?php echo h(__('Version')); ?></th><td><?php echo h($table['table_version']); ?></td></tr>
				<tr><th><?php echo h(__('Description')); ?></th><td><?php echo h($table['description']); ?></td></tr>
				<tr><th><?php echo h(__('Status')); ?></th><td><?php echo $table['table_locked'] ? 'Unlocked' : 'Locked'; ?></td></tr>
			</table>
			<h4><?php echo h(__('Table Fields')); ?></h4>
			<table class="table table-bordered">
				<tr><th><?php echo h(__('Field Name')); ?></th><th><?php echo h(__('Field Id')); ?></th><th><?php echo h(__('Linked To (Model)')); ?></th></tr>
				<?php foreach((array)json_decode($table['fields'], true) as $field){
					if(isset($field['data_type']) && $field['data_type'] === 'belogsTos') continue;
					$fieldName = isset($field['field_name']) ? $field['field_name'] : '';
					$linkedTo = isset($field['linked_to']) ? $field['linked_to'] : -1;
				?>
					<tr><td><?php echo h($fieldName); ?></td><td><?php echo h(Inflector::classify($table['table_name']).Inflector::classify($fieldName)); ?></td><td><?php echo $linkedTo !== -1 && $linkedTo !== '-1' ? '<strong>'.h(Inflector::classify($linkedTo)).'</strong>' : '-'; ?></td></tr>
				<?php } ?>
			</table>
		</div>
		<div class="box-footer text-right">
			<?php if($table['publish']){
				echo $this->Html->link('<i class="fa fa-check-square-o text-success fa-lg"></i>', array('action' => 'hold', $table['id']), array('class' => 'btn btn-sm tooltip1', 'escape' => false, 'title' => __('Unpublish this table')));
			}else{
				echo $this->Html->link('<i class="fa fa-minus-square-o text-danger fa-lg"></i>', array('action' => 'publish', $table['id']), array('class' => 'btn btn-sm tooltip1', 'escape' => false, 'title' => __('Publish this table')));
			} ?>
			<?php if(!$table['table_locked']){
				echo $this->Html->link('<i class="fa fa-lock text-success fa-lg"></i>', array('action' => 'unlock', $table['id']), array('class' => 'btn btn-sm tooltip1', 'escape' => false, 'title' => __('Unlock table')));
			}else{
				echo $this->Html->link('<i class="fa fa-unlock text-danger fa-lg"></i>', array('action' => 'lock', $table['id']), array('class' => 'btn btn-sm tooltip1', 'escape' => false, 'title' => __('Lock table')));
			} ?>
			<?php if($table['table_locked']){
				echo $this->Html->link('<i class="fa fa-refresh text-warning fa-lg"></i>', array('action' => $table['custom_table_id'] ? 'recreate_child' : 'recreate', $table['id'], 'timestamp' => date('ymdhis')), array('class' => 'btn btn-sm tooltip1', 'escape' => false, 'title' => __('Recreate this table')));
				echo $this->Html->link('<i class="fa fa-trash-o text-danger fa-lg"></i>', array('action' => $table['custom_table_id'] ? 'delete_child' : 'delete', $table['id']), array('class' => 'btn btn-sm tooltip1', 'escape' => false, 'title' => __('Delete this table')));
			} ?>
			<?php if(!$table['custom_table_id']){
				echo $this->Html->link('<i class="fa fa-list-ol fa-lg"></i>', array('action' => 'add_child', 'custom_table_id' => $table['id'], 'qc_document_id' => $table['qc_document_id'], 'process_id' => $table['process_id']), array('class' => 'btn btn-sm tooltip1', 'escape' => false, 'title' => __('Link new child table')));
				echo $this->Html->link('<i class="fa fa-chain fa-lg"></i>', array('controller' => 'approval_processes', 'action' => 'add', 'controller_name' => Inflector::classify($table['table_name']), 'timestamp' => date('ymdhis'), 'custom_table_id' => $table['id']), array('class' => 'btn btn-sm tooltip1', 'escape' => false, 'title' => __('Add Auto Approval Process')));
			} ?>
		</div>
	</div>

<?php }elseif($selectedTab === 'child_tables'){ ?>
	<div class="box box-default">
		<div class="box-header with-border"><h3 class="box-title"><?php echo h(__('Child Forms')); ?></h3></div>
		<div class="box-body table-responsive">
			<table class="table table-bordered table-hover">
				<thead><tr><th><?php echo h(__('Name')); ?></th><th><?php echo h(__('Table Name')); ?></th><th><?php echo h(__('Version')); ?></th><th><?php echo h(__('Description')); ?></th><th><?php echo h(__('Status')); ?></th><th class="text-right"><?php echo h(__('Actions')); ?></th></tr></thead>
				<tbody><?php foreach((array)$childs as $child){ $childTable = $child['CustomTable']; ?>
					<tr>
						<td><?php echo h($childTable['name']); ?></td><td><?php echo h($childTable['table_name']); ?></td><td><?php echo h($childTable['table_version']); ?></td><td><?php echo h($childTable['description']); ?></td><td><?php echo $childTable['publish'] ? 'Published' : 'Unpublished'; ?></td>
						<td class="text-right">
							<?php echo $this->Html->link('<i class="fa fa-cogs text-warning"></i>', array('action' => 'view', $childTable['id'], 'timestamp' => date('ymdhis')), array('class' => 'btn btn-sm tooltip1', 'escape' => false, 'title' => __('Open child table'))); ?>
							<?php echo $this->Html->link('<i class="fa fa-refresh text-warning"></i>', array('action' => 'unlock', $childTable['id'], 'next_action' => 'recreate_child', 'timestamp' => date('ymdhis')), array('class' => 'btn btn-sm tooltip1', 'escape' => false, 'title' => __('Recreate child form'))); ?>
							<?php if($childTable['table_locked']) echo $this->Html->link('<i class="fa fa-trash-o text-danger"></i>', array('action' => 'delete_child', $childTable['id']), array('class' => 'btn btn-sm tooltip1', 'escape' => false, 'title' => __('Delete child table'))); ?>
						</td>
					</tr>
				<?php } ?></tbody>
			</table>
		</div>
	</div>

<?php }elseif($selectedTab === 'rebuild_module'){ ?>
	<div class="box box-default" id="rebuild-module-panel">
		<div class="box-header with-border"><h3 class="box-title"><?php echo h(__('Rebuild Module')); ?></h3></div>
		<div class="box-body">
			<p class="text-muted"><?php echo h(__('Rebuilds the generated controller, model and views from the saved JSON. It does not change fields, table settings or existing records.')); ?></p>
			<table class="table table-bordered">
				<thead><tr><th><?php echo h(__('Type')); ?></th><th><?php echo h(__('Name')); ?></th><th><?php echo h(__('Table name')); ?></th></tr></thead>
				<tbody>
					<tr><td><?php echo h(__('Main table')); ?></td><td><?php echo h($table['name']); ?></td><td><?php echo h($table['table_name']); ?></td></tr>
					<?php foreach((array)$childs as $childForm){ ?><tr><td><?php echo h(__('Child form')); ?></td><td><?php echo h($childForm['CustomTable']['name']); ?></td><td><?php echo h($childForm['CustomTable']['table_name']); ?></td></tr><?php } ?>
					<?php foreach((array)$childDocumentForms as $childDocumentForm){ ?><tr><td><?php echo h(__('Child document form')); ?></td><td><?php echo h($childDocumentForm['CustomTable']['name']); ?></td><td><?php echo h($childDocumentForm['CustomTable']['table_name']); ?></td></tr><?php } ?>
				</tbody>
			</table>
		</div>
		<div class="box-footer text-right">
			<?php echo $this->Form->create('CustomTable', array('url' => array('action' => 'rebuild_module', $table['id']), 'id' => 'rebuild-module-form', 'class' => 'form-inline')); ?>
			<?php echo $this->Form->submit(__('Rebuild Module'), array('class' => 'btn btn-sm btn-success', 'id' => 'rebuild-module-submit')); ?>
			<?php echo $this->Form->end(); ?>
			<div id="rebuild-module-result" class="text-right" style="margin-top:8px"></div>
		</div>
	</div>
	<script type="text/javascript">
	(function(){
		$('#rebuild-module-form').on('submit', function(event){
			event.preventDefault();
			var form = $(this), button = $('#rebuild-module-submit'), result = $('#rebuild-module-result');
			button.prop('disabled', true).text(<?php echo json_encode(__('Rebuilding...'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>);
			result.html(<?php echo json_encode(__('<p class="text-muted"><i class="fa fa-refresh fa-spin"></i> Rebuilding generated files...</p>'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>);
			$.ajax({url: form.attr('action'), type: 'POST', data: form.serialize(), dataType: 'json'})
				.done(function(response){
					if(response.success){ result.html('<span class="text-success">' + $('<div>').text(response.message).html() + ' ' + (response.rebuilt || []).length + ' form(s) rebuilt.</span>'); }
					else result.html('<div class="alert alert-danger">' + $('<div>').text(response.message || <?php echo json_encode(__('Module rebuild failed.'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>).html() + '</div>');
				})
				.fail(function(){ result.html(<?php echo json_encode(__('<div class="alert alert-danger">Module rebuild failed. Please check the API connection and try again.</div>'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>); })
				.always(function(){ button.prop('disabled', false).text(<?php echo json_encode(__('Rebuild Module'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>); });
		});
	})();
	</script>

<?php }elseif($selectedTab === 'child_document_forms'){ ?>
	<div class="box box-default">
		<div class="box-header with-border"><h3 class="box-title"><?php echo h(__('Child Document Forms')); ?></h3></div>
		<div class="box-body table-responsive">
			<table class="table table-bordered table-hover">
				<thead><tr><th><?php echo h(__('Name')); ?></th><th><?php echo h(__('Table Name')); ?></th><th><?php echo h(__('Version')); ?></th><th><?php echo h(__('Description')); ?></th><th><?php echo h(__('Status')); ?></th><th class="text-right"><?php echo h(__('Actions')); ?></th></tr></thead>
				<tbody><?php foreach((array)$childDocumentForms as $childDocumentForm){ $childDocumentTable = $childDocumentForm['CustomTable']; ?>
					<tr>
						<td><?php echo h($childDocumentTable['name']); ?></td><td><?php echo h($childDocumentTable['table_name']); ?></td><td><?php echo h($childDocumentTable['table_version']); ?></td><td><?php echo h($childDocumentTable['description']); ?></td><td><?php echo $childDocumentTable['publish'] ? 'Published' : 'Unpublished'; ?></td>
						<td class="text-right">
							<?php echo $this->Html->link('<i class="fa fa-cogs text-warning"></i>', array('action' => 'view', $childDocumentTable['id'], 'timestamp' => date('ymdhis')), array('class' => 'btn btn-sm tooltip1', 'escape' => false, 'title' => __('Open child document form'))); ?>
							<?php echo $this->Html->link('<i class="fa fa-refresh text-warning"></i>', array('action' => 'unlock', $childDocumentTable['id'], 'next_action' => 'recreate', 'timestamp' => date('ymdhis')), array('class' => 'btn btn-sm tooltip1', 'escape' => false, 'title' => __('Recreate child document form'))); ?>
						</td>
					</tr>
				<?php } ?></tbody>
			</table>
		</div>
	</div>

<?php }elseif($selectedTab === 'tab_configuration'){ ?>
	<div class="box box-primary" id="tab-configuration-panel">
		<div class="box-header with-border"><h3 class="box-title"><?php echo h(__('Tab Configuration')); ?></h3></div>
		<?php echo $this->Form->create('CustomTable', array('url' => array('action' => 'update_tab_settings', $table['id']), 'id' => 'update-tab-settings', 'class' => 'form')); ?>
		<?php echo $this->Form->hidden('tab_settings', array('id' => 'CustomTableTabSettings')); ?>
		<div class="box-body">
			<p class="text-muted"><?php echo h(__('Choose when each main-form tab or child document is available. Normal child forms are rendered independently and are not controlled here.')); ?></p>
			<table class="table table-bordered tab-configuration-table" id="tab-configuration-table"><thead><tr><th><?php echo h(__('Tab')); ?></th><th><?php echo h(__('Position')); ?></th><th><?php echo h(__('Action visibility')); ?></th><th><?php echo h(__('Visibility rule')); ?></th></tr></thead><tbody>
			<?php foreach((array)$tabConfigurationRows as $formTab){
				$settingsGroup = $formTab['type'] === 'child_document' ? (array)$childDocumentSettings : (array)$tabSettings;
				$setting = isset($settingsGroup[$formTab['key']]) ? $settingsGroup[$formTab['key']] : array();
				$action = isset($setting['action_visibility']) ? $setting['action_visibility'] : 'always';
				$fieldName = isset($setting['visibility_field']) ? $setting['visibility_field'] : '';
				$values = isset($setting['visible_when']) ? (array)$setting['visible_when'] : array();
			?>
				<tr class="tab-configuration-row" data-tab-name="<?php echo h($formTab['key']); ?>" data-tab-type="<?php echo h($formTab['type']); ?>" data-selected-values="<?php echo h(json_encode($values)); ?>">
					<td><strong><?php echo h($formTab['name']); ?></strong></td><td><?php echo h($formTab['position']); ?></td>
					<td><select class="form-control input-sm tab-action-visibility"><option value="always"<?php echo $action === 'always' ? ' selected' : ''; ?>><?php echo h(__('Always visible')); ?></option><option value="hide_add"<?php echo $action === 'hide_add' ? ' selected' : ''; ?>><?php echo h(__('Hide on Add')); ?></option><option value="hide_edit"<?php echo $action === 'hide_edit' ? ' selected' : ''; ?>><?php echo h(__('Hide on Edit')); ?></option><option value="hide_both"<?php echo $action === 'hide_both' ? ' selected' : ''; ?>><?php echo h(__('Hide on Add')); ?> &amp; <?php echo h(__('Edit')); ?></option></select></td>
					<td><select class="form-control input-sm tab-visibility-field"><option value=""><?php echo h(__('No field-value rule')); ?></option><?php foreach((array)$visibilityFields as $visibilityField){ ?><option value="<?php echo h($visibilityField['name']); ?>" data-options="<?php echo h(json_encode($visibilityField['options'])); ?>"<?php echo $fieldName === $visibilityField['name'] ? ' selected' : ''; ?>><?php echo h($visibilityField['label']); ?></option><?php } ?></select><select class="form-control input-sm tab-visibility-values" multiple style="margin-top:6px"></select></td>
				</tr>
			<?php } ?></tbody></table>
		</div>
		<div class="box-footer text-right"><?php echo $this->Form->submit(__('Save Tab Configuration'), array('class' => 'btn btn-sm btn-success')); ?><?php echo $this->Form->end(); ?></div>
	</div>
	<script>
	(function(){
		function loadValues(row){ var field=row.find('.tab-visibility-field option:selected'), values=row.find('.tab-visibility-values'), options=[], selected=[]; try{options=JSON.parse(field.attr('data-options')||'[]'); selected=JSON.parse(row.attr('data-selected-values')||'[]');}catch(e){} values.empty(); $.each(options,function(i,item){ $('<option/>',{value:item,text:item,selected:($.inArray(item,selected)>-1)}).appendTo(values); }); values.prop('disabled',!options.length).trigger('chosen:updated'); }
		$('#tab-configuration-table .tab-configuration-row').each(function(){ loadValues($(this)); });
		$('#tab-configuration-table').on('change','.tab-visibility-field',function(){ loadValues($(this).closest('tr')); });
		$('#update-tab-settings').on('submit',function(){ var settings={tabs:{},child_documents:{}}; $('#tab-configuration-table .tab-configuration-row').each(function(){ var row=$(this), setting={action_visibility:row.find('.tab-action-visibility').val(),visibility_field:row.find('.tab-visibility-field').val()||'',visible_when:row.find('.tab-visibility-values').val()||[]}; var group=row.attr('data-tab-type')==='child_document'?settings.child_documents:settings.tabs; group[row.attr('data-tab-name')]=setting; }); $('#CustomTableTabSettings').val(JSON.stringify(settings)); });
	})();
	</script>

<?php }elseif($selectedTab === 'data_entry'){ ?>
	<?php if($isMr){ echo $this->Form->create('CustomTable', array('url' => 'updatedataentry/'.$table['qc_document_id'].'/'.$table['id'], 'class' => 'form')); ?>
	<div class="box box-default"><div class="box-header with-border"><h3 class="box-title"><?php echo h(__('Data Entry')); ?></h3></div><div class="box-body">
		<div class="col-md-4"><?php echo $this->Form->input('QcDocument.schedule_id', array('required' => 'required', 'default' => $customTable['QcDocument']['schedule_id'])); ?></div>
		<div class="col-md-4"><?php echo $this->Form->input('QcDocument.data_type', array('required' => 'required', 'options' => $customArray['dataTypes'], 'default' => $customTable['QcDocument']['data_type'])); ?></div>
		<div class="col-md-4"><?php echo $this->Form->input('QcDocument.data_update_type', array('required' => 'required', 'options' => $customArray['dataUpdateTypes'], 'default' => $customTable['QcDocument']['data_update_type'])); ?></div>
	</div><div class="box-footer text-right"><?php echo $this->Form->submit(__('Update Data Entry'), array('class' => 'btn btn-sm btn-success')); ?></div></div><?php echo $this->Form->end(); } ?>

<?php }elseif($selectedTab === 'permissions'){ ?>
	<?php if($isMr){ echo $this->Form->create('CustomTable', array('url' => 'updateaccess/'.$table['id'])); ?>
	<div class="box box-default"><div class="box-header with-border"><h3 class="box-title"><?php echo h(__('Table Permissions')); ?></h3></div><div class="box-body table-responsive"><table class="table table-bordered"><tr><th><?php echo h(__('Creators')); ?></th><th><?php echo h(__('Viewers')); ?></th><th><?php echo h(__('Editors')); ?></th><th><?php echo h(__('Approvers')); ?></th></tr><tr><td height="300"><?php echo $this->Form->input('creators',array('label'=>false,'multiple'=>true,'options'=>$users,'default'=>$creators)); ?></td><td><?php echo $this->Form->input('viewers',array('label'=>false,'multiple'=>true,'options'=>$users,'default'=>$viewers)); ?></td><td><?php echo $this->Form->input('editors',array('label'=>false,'multiple'=>true,'options'=>$users,'default'=>$editors)); ?></td><td><?php echo $this->Form->input('approvers',array('label'=>false,'multiple'=>true,'options'=>$users,'default'=>$approvers)); ?></td></tr></table></div><div class="box-footer text-right"><?php echo $this->Form->submit(__('Update Access'),array('class'=>'btn btn-sm btn-success')); ?></div></div><?php echo $this->Form->end(); } ?>

<?php }elseif($selectedTab === 'charts_panels'){ ?>
	<div class="configuration-plain-panel ajax-tab-content" data-load-url="<?php echo Router::url('/', true); ?>graph_panels/custom_table/<?php echo h($table['id']); ?>"><i class="fa fa-refresh fa-spin"></i> <?php echo h(__('Loading chart and panel configuration...')); ?></div>

<?php }elseif($selectedTab === 'email_triggers'){ ?>
	<div class="configuration-plain-panel ajax-tab-content" data-load-url="<?php echo Router::url('/', true); ?>custom_triggers/add/custom_table_id:<?php echo h($table['id']); ?>"><i class="fa fa-refresh fa-spin"></i> <?php echo h(__('Loading email triggers...')); ?></div>

<?php }elseif($selectedTab === 'create_tasks'){ ?>
	<div class="configuration-plain-panel ajax-tab-content" data-load-url="<?php echo Router::url('/', true); ?>custom_table_tasks/add/<?php echo h($table['id']); ?>"><i class="fa fa-refresh fa-spin"></i> <?php echo h(__('Loading task settings...')); ?></div>

<?php }elseif($selectedTab === 'javascript'){ ?>
	<div class="configuration-plain-panel ajax-tab-content" data-load-url="<?php echo Router::url('/', true); ?>custom_tables/code_input_main/<?php echo h($table['id']); ?>"><i class="fa fa-refresh fa-spin"></i> <?php echo h(__('Loading JavaScript settings...')); ?></div>
<?php } ?>
