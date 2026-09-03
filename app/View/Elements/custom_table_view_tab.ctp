<?php
$selectedTab = isset($this->request->params['pass'][1]) ? $this->request->params['pass'][1] : 'main';
$table = $customTable['CustomTable'];
$isMr = (bool)$this->Session->read('User.is_mr');
?>
<?php if($selectedTab === 'main'){ ?>
	<div class="box box-default">
		<div class="box-header with-border"><h3 class="box-title">Main Form <small><?php echo h($table['table_name']); ?></small></h3></div>
		<div class="box-body">
			<table class="table table-bordered">
				<tr><th width="180">Name</th><td><?php echo h($table['name']); ?></td></tr>
				<tr><th>Table Name</th><td><?php echo h($table['table_name']); ?></td></tr>
				<tr><th>Version</th><td><?php echo h($table['table_version']); ?></td></tr>
				<tr><th>Description</th><td><?php echo h($table['description']); ?></td></tr>
				<tr><th>Status</th><td><?php echo $table['table_locked'] ? 'Unlocked' : 'Locked'; ?></td></tr>
			</table>
			<h4>Table Fields</h4>
			<table class="table table-bordered">
				<tr><th>Field Name</th><th>Field Id</th><th>Linked To (Model)</th></tr>
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
				echo $this->Html->link('<i class="fa fa-check-square-o text-success fa-lg"></i>', array('action' => 'hold', $table['id']), array('class' => 'btn btn-sm tooltip1', 'escape' => false, 'title' => 'Unpublish this table'));
			}else{
				echo $this->Html->link('<i class="fa fa-minus-square-o text-danger fa-lg"></i>', array('action' => 'publish', $table['id']), array('class' => 'btn btn-sm tooltip1', 'escape' => false, 'title' => 'Publish this table'));
			} ?>
			<?php if(!$table['table_locked']){
				echo $this->Html->link('<i class="fa fa-lock text-success fa-lg"></i>', array('action' => 'unlock', $table['id']), array('class' => 'btn btn-sm tooltip1', 'escape' => false, 'title' => 'Unlock table'));
			}else{
				echo $this->Html->link('<i class="fa fa-unlock text-danger fa-lg"></i>', array('action' => 'lock', $table['id']), array('class' => 'btn btn-sm tooltip1', 'escape' => false, 'title' => 'Lock table'));
			} ?>
			<?php if($table['table_locked']){
				echo $this->Html->link('<i class="fa fa-refresh text-warning fa-lg"></i>', array('action' => $table['custom_table_id'] ? 'recreate_child' : 'recreate', $table['id'], 'timestamp' => date('ymdhis')), array('class' => 'btn btn-sm tooltip1', 'escape' => false, 'title' => 'Recreate this table'));
				echo $this->Html->link('<i class="fa fa-trash-o text-danger fa-lg"></i>', array('action' => $table['custom_table_id'] ? 'delete_child' : 'delete', $table['id']), array('class' => 'btn btn-sm tooltip1', 'escape' => false, 'title' => 'Delete this table'));
			} ?>
			<?php if(!$table['custom_table_id']){
				echo $this->Html->link('<i class="fa fa-list-ol fa-lg"></i>', array('action' => 'add_child', 'custom_table_id' => $table['id'], 'qc_document_id' => $table['qc_document_id'], 'process_id' => $table['process_id']), array('class' => 'btn btn-sm tooltip1', 'escape' => false, 'title' => 'Link new child table'));
				echo $this->Html->link('<i class="fa fa-chain fa-lg"></i>', array('controller' => 'approval_processes', 'action' => 'add', 'controller_name' => Inflector::classify($table['table_name']), 'timestamp' => date('ymdhis'), 'custom_table_id' => $table['id']), array('class' => 'btn btn-sm tooltip1', 'escape' => false, 'title' => 'Add Auto Approval Process'));
			} ?>
		</div>
	</div>

<?php }elseif($selectedTab === 'child_tables'){ ?>
	<div class="box box-default">
		<div class="box-header with-border"><h3 class="box-title">Child Forms</h3></div>
		<div class="box-body table-responsive">
			<table class="table table-bordered table-hover">
				<thead><tr><th>Name</th><th>Table Name</th><th>Version</th><th>Description</th><th>Status</th><th class="text-right">Actions</th></tr></thead>
				<tbody><?php foreach((array)$childs as $child){ $childTable = $child['CustomTable']; ?>
					<tr>
						<td><?php echo h($childTable['name']); ?></td><td><?php echo h($childTable['table_name']); ?></td><td><?php echo h($childTable['table_version']); ?></td><td><?php echo h($childTable['description']); ?></td><td><?php echo $childTable['publish'] ? 'Published' : 'Unpublished'; ?></td>
						<td class="text-right">
							<?php echo $this->Html->link('<i class="fa fa-cogs text-warning"></i>', array('action' => 'view', $childTable['id'], 'timestamp' => date('ymdhis')), array('class' => 'btn btn-sm tooltip1', 'escape' => false, 'title' => 'Open child table')); ?>
							<?php echo $this->Html->link('<i class="fa fa-refresh text-warning"></i>', array('action' => 'unlock', $childTable['id'], 'next_action' => 'recreate_child', 'timestamp' => date('ymdhis')), array('class' => 'btn btn-sm tooltip1', 'escape' => false, 'title' => 'Recreate child form')); ?>
							<?php if($childTable['table_locked']) echo $this->Html->link('<i class="fa fa-trash-o text-danger"></i>', array('action' => 'delete_child', $childTable['id']), array('class' => 'btn btn-sm tooltip1', 'escape' => false, 'title' => 'Delete child table')); ?>
						</td>
					</tr>
				<?php } ?></tbody>
			</table>
		</div>
	</div>

<?php }elseif($selectedTab === 'rebuild_module'){ ?>
	<div class="box box-default" id="rebuild-module-panel">
		<div class="box-header with-border"><h3 class="box-title">Rebuild Module</h3></div>
		<div class="box-body">
			<p class="text-muted">Rebuilds the generated controller, model and views from the saved JSON. It does not change fields, table settings or existing records.</p>
			<table class="table table-bordered">
				<thead><tr><th>Type</th><th>Name</th><th>Table name</th></tr></thead>
				<tbody>
					<tr><td>Main table</td><td><?php echo h($table['name']); ?></td><td><?php echo h($table['table_name']); ?></td></tr>
					<?php foreach((array)$childs as $childForm){ ?><tr><td>Child form</td><td><?php echo h($childForm['CustomTable']['name']); ?></td><td><?php echo h($childForm['CustomTable']['table_name']); ?></td></tr><?php } ?>
					<?php foreach((array)$childDocumentForms as $childDocumentForm){ ?><tr><td>Child document form</td><td><?php echo h($childDocumentForm['CustomTable']['name']); ?></td><td><?php echo h($childDocumentForm['CustomTable']['table_name']); ?></td></tr><?php } ?>
				</tbody>
			</table>
		</div>
		<div class="box-footer text-right">
			<?php echo $this->Form->create('CustomTable', array('url' => array('action' => 'rebuild_module', $table['id']), 'id' => 'rebuild-module-form', 'class' => 'form-inline')); ?>
			<?php echo $this->Form->submit('Rebuild Module', array('class' => 'btn btn-sm btn-success', 'id' => 'rebuild-module-submit')); ?>
			<?php echo $this->Form->end(); ?>
			<div id="rebuild-module-result" class="text-right" style="margin-top:8px"></div>
		</div>
	</div>
	<script type="text/javascript">
	(function(){
		$('#rebuild-module-form').on('submit', function(event){
			event.preventDefault();
			var form = $(this), button = $('#rebuild-module-submit'), result = $('#rebuild-module-result');
			button.prop('disabled', true).text('Rebuilding...');
			result.html('<p class="text-muted"><i class="fa fa-refresh fa-spin"></i> Rebuilding generated files...</p>');
			$.ajax({url: form.attr('action'), type: 'POST', data: form.serialize(), dataType: 'json'})
				.done(function(response){
					if(response.success){ result.html('<span class="text-success">' + $('<div>').text(response.message).html() + ' ' + (response.rebuilt || []).length + ' form(s) rebuilt.</span>'); }
					else result.html('<div class="alert alert-danger">' + $('<div>').text(response.message || 'Module rebuild failed.').html() + '</div>');
				})
				.fail(function(){ result.html('<div class="alert alert-danger">Module rebuild failed. Please check the API connection and try again.</div>'); })
				.always(function(){ button.prop('disabled', false).text('Rebuild Module'); });
		});
	})();
	</script>

<?php }elseif($selectedTab === 'child_document_forms'){ ?>
	<div class="box box-default">
		<div class="box-header with-border"><h3 class="box-title">Child Document Forms</h3></div>
		<div class="box-body table-responsive">
			<table class="table table-bordered table-hover">
				<thead><tr><th>Name</th><th>Table Name</th><th>Version</th><th>Description</th><th>Status</th><th class="text-right">Actions</th></tr></thead>
				<tbody><?php foreach((array)$childDocumentForms as $childDocumentForm){ $childDocumentTable = $childDocumentForm['CustomTable']; ?>
					<tr>
						<td><?php echo h($childDocumentTable['name']); ?></td><td><?php echo h($childDocumentTable['table_name']); ?></td><td><?php echo h($childDocumentTable['table_version']); ?></td><td><?php echo h($childDocumentTable['description']); ?></td><td><?php echo $childDocumentTable['publish'] ? 'Published' : 'Unpublished'; ?></td>
						<td class="text-right">
							<?php echo $this->Html->link('<i class="fa fa-cogs text-warning"></i>', array('action' => 'view', $childDocumentTable['id'], 'timestamp' => date('ymdhis')), array('class' => 'btn btn-sm tooltip1', 'escape' => false, 'title' => 'Open child document form')); ?>
							<?php echo $this->Html->link('<i class="fa fa-refresh text-warning"></i>', array('action' => 'unlock', $childDocumentTable['id'], 'next_action' => 'recreate_child', 'timestamp' => date('ymdhis')), array('class' => 'btn btn-sm tooltip1', 'escape' => false, 'title' => 'Recreate child document form')); ?>
						</td>
					</tr>
				<?php } ?></tbody>
			</table>
		</div>
	</div>

<?php }elseif($selectedTab === 'tab_configuration'){ ?>
	<div class="box box-primary" id="tab-configuration-panel">
		<div class="box-header with-border"><h3 class="box-title">Tab Configuration</h3></div>
		<?php echo $this->Form->create('CustomTable', array('url' => array('action' => 'update_tab_settings', $table['id']), 'id' => 'update-tab-settings', 'class' => 'form')); ?>
		<?php echo $this->Form->hidden('tab_settings', array('id' => 'CustomTableTabSettings')); ?>
		<div class="box-body">
			<p class="text-muted">Choose when each main-form tab and child-form tab is available. Leave a visibility rule as Always visible when no field-value rule is required.</p>
			<table class="table table-bordered tab-configuration-table" id="tab-configuration-table"><thead><tr><th>Tab</th><th>Position</th><th>Action visibility</th><th>Visibility rule</th></tr></thead><tbody>
			<?php foreach((array)$tabConfigurationRows as $formTab){
				$settingsForRow = $formTab['type'] === 'child_form' ? $childFormSettings : $tabSettings;
				$setting = isset($settingsForRow[$formTab['key']]) ? $settingsForRow[$formTab['key']] : array();
				$action = isset($setting['action_visibility']) ? $setting['action_visibility'] : 'always';
				$fieldName = isset($setting['visibility_field']) ? $setting['visibility_field'] : '';
				$values = isset($setting['visible_when']) ? (array)$setting['visible_when'] : array();
			?>
				<tr class="tab-configuration-row" data-tab-name="<?php echo h($formTab['key']); ?>" data-tab-type="<?php echo h($formTab['type']); ?>" data-selected-values="<?php echo h(json_encode($values)); ?>">
					<td><strong><?php echo h($formTab['name']); ?></strong><?php echo $formTab['type'] === 'child_form' ? ' <small>(Child form)</small>' : ''; ?></td><td><?php echo h($formTab['position']); ?></td>
					<td><select class="form-control input-sm tab-action-visibility"><option value="always"<?php echo $action === 'always' ? ' selected' : ''; ?>>Always visible</option><option value="hide_add"<?php echo $action === 'hide_add' ? ' selected' : ''; ?>>Hide on Add</option><option value="hide_edit"<?php echo $action === 'hide_edit' ? ' selected' : ''; ?>>Hide on Edit</option><option value="hide_both"<?php echo $action === 'hide_both' ? ' selected' : ''; ?>>Hide on Add &amp; Edit</option></select></td>
					<td><select class="form-control input-sm tab-visibility-field"><option value="">No field-value rule</option><?php foreach((array)$visibilityFields as $visibilityField){ ?><option value="<?php echo h($visibilityField['name']); ?>" data-options="<?php echo h(json_encode($visibilityField['options'])); ?>"<?php echo $fieldName === $visibilityField['name'] ? ' selected' : ''; ?>><?php echo h($visibilityField['label']); ?></option><?php } ?></select><select class="form-control input-sm tab-visibility-values" multiple style="margin-top:6px"></select></td>
				</tr>
			<?php } ?></tbody></table>
		</div>
		<div class="box-footer text-right"><?php echo $this->Form->submit('Save Tab Configuration', array('class' => 'btn btn-sm btn-success')); ?><?php echo $this->Form->end(); ?></div>
	</div>
	<script>
	(function(){
		function loadValues(row){ var field=row.find('.tab-visibility-field option:selected'), values=row.find('.tab-visibility-values'), options=[], selected=[]; try{options=JSON.parse(field.attr('data-options')||'[]'); selected=JSON.parse(row.attr('data-selected-values')||'[]');}catch(e){} values.empty(); $.each(options,function(i,item){ $('<option/>',{value:item,text:item,selected:($.inArray(item,selected)>-1)}).appendTo(values); }); values.prop('disabled',!options.length).trigger('chosen:updated'); }
		$('#tab-configuration-table .tab-configuration-row').each(function(){ loadValues($(this)); });
		$('#tab-configuration-table').on('change','.tab-visibility-field',function(){ loadValues($(this).closest('tr')); });
		$('#update-tab-settings').on('submit',function(){ var settings={tabs:{},child_forms:{}}; $('#tab-configuration-table .tab-configuration-row').each(function(){ var row=$(this), setting={action_visibility:row.find('.tab-action-visibility').val(),visibility_field:row.find('.tab-visibility-field').val()||'',visible_when:row.find('.tab-visibility-values').val()||[]}; if(row.attr('data-tab-type')==='child_form') settings.child_forms[row.attr('data-tab-name')]=setting; else settings.tabs[row.attr('data-tab-name')]=setting; }); $('#CustomTableTabSettings').val(JSON.stringify(settings)); });
	})();
	</script>

<?php }elseif($selectedTab === 'data_entry'){ ?>
	<?php if($isMr){ echo $this->Form->create('CustomTable', array('url' => 'updatedataentry/'.$table['qc_document_id'].'/'.$table['id'], 'class' => 'form')); ?>
	<div class="box box-default"><div class="box-header with-border"><h3 class="box-title">Data Entry</h3></div><div class="box-body">
		<div class="col-md-4"><?php echo $this->Form->input('QcDocument.schedule_id', array('required' => 'required', 'default' => $customTable['QcDocument']['schedule_id'])); ?></div>
		<div class="col-md-4"><?php echo $this->Form->input('QcDocument.data_type', array('required' => 'required', 'options' => $customArray['dataTypes'], 'default' => $customTable['QcDocument']['data_type'])); ?></div>
		<div class="col-md-4"><?php echo $this->Form->input('QcDocument.data_update_type', array('required' => 'required', 'options' => $customArray['dataUpdateTypes'], 'default' => $customTable['QcDocument']['data_update_type'])); ?></div>
	</div><div class="box-footer text-right"><?php echo $this->Form->submit('Update Data Entry', array('class' => 'btn btn-sm btn-success')); ?></div></div><?php echo $this->Form->end(); } ?>

<?php }elseif($selectedTab === 'permissions'){ ?>
	<?php if($isMr){ echo $this->Form->create('CustomTable', array('url' => 'updateaccess/'.$table['id'])); ?>
	<div class="box box-default"><div class="box-header with-border"><h3 class="box-title">Table Permissions</h3></div><div class="box-body table-responsive"><table class="table table-bordered"><tr><th>Creators</th><th>Viewers</th><th>Editors</th><th>Approvers</th></tr><tr><td><?php echo $this->Form->input('creators',array('label'=>false,'multiple'=>true,'options'=>$users,'default'=>$creators)); ?></td><td><?php echo $this->Form->input('viewers',array('label'=>false,'multiple'=>true,'options'=>$users,'default'=>$viewers)); ?></td><td><?php echo $this->Form->input('editors',array('label'=>false,'multiple'=>true,'options'=>$users,'default'=>$editors)); ?></td><td><?php echo $this->Form->input('approvers',array('label'=>false,'multiple'=>true,'options'=>$users,'default'=>$approvers)); ?></td></tr></table></div><div class="box-footer text-right"><?php echo $this->Form->submit('Update Access',array('class'=>'btn btn-sm btn-success')); ?></div></div><?php echo $this->Form->end(); } ?>

<?php }elseif($selectedTab === 'charts_panels'){ ?>
	<div class="configuration-plain-panel ajax-tab-content" data-load-url="<?php echo Router::url('/', true); ?>graph_panels/custom_table/<?php echo h($table['id']); ?>"><i class="fa fa-refresh fa-spin"></i> Loading chart and panel configuration...</div>

<?php }elseif($selectedTab === 'email_triggers'){ ?>
	<div class="configuration-plain-panel ajax-tab-content" data-load-url="<?php echo Router::url('/', true); ?>custom_triggers/add/custom_table_id:<?php echo h($table['id']); ?>"><i class="fa fa-refresh fa-spin"></i> Loading email triggers...</div>

<?php }elseif($selectedTab === 'create_tasks'){ ?>
	<div class="configuration-plain-panel ajax-tab-content" data-load-url="<?php echo Router::url('/', true); ?>custom_table_tasks/add/<?php echo h($table['id']); ?>"><i class="fa fa-refresh fa-spin"></i> Loading task settings...</div>

<?php }elseif($selectedTab === 'javascript'){ ?>
	<div class="configuration-plain-panel ajax-tab-content" data-load-url="<?php echo Router::url('/', true); ?>custom_tables/code_input_main/<?php echo h($table['id']); ?>"><i class="fa fa-refresh fa-spin"></i> Loading JavaScript settings...</div>
<?php } ?>
