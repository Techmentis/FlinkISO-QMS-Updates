<h2><?php  echo __('User Session'); ?></h2>
	<table cellpadding="2" cellspacing="1" bgcolor="#D4D4D4">
		<tr  bgcolor="#FFFFFF"><td><?php echo __('Ip Address'); ?></td>
		<td>
			<?php echo h($userSession['UserSession']['ip_address']); ?>
			&nbsp;
		</td></tr>
		<tr  bgcolor="#FFFFFF"><td><?php echo __('Start Time'); ?></td>
		<td>
			<?php echo h($userSession['UserSession']['start_time']); ?>
			&nbsp;
		</td></tr>
		<tr  bgcolor="#FFFFFF"><td><?php echo __('End Time'); ?></td>
		<td>
			<?php echo h($userSession['UserSession']['end_time']); ?>
			&nbsp;
		</td></tr>
		<tr bgcolor="#FFFFFF"><td><?php echo __('User'); ?></td>
		<td>
			<?php echo $userSession['User']['name']; ?>
			&nbsp;
		</td></tr>
		<tr bgcolor="#FFFFFF"><td><?php echo __('Employee'); ?></td>
		<td>
			<?php echo $userSession['Employee']['name']; ?>
			&nbsp;
		</td></tr>
		<tr  bgcolor="#FFFFFF"><td><?php echo __('Created'); ?></td>
		<td>
			<?php echo h($userSession['UserSession']['created']); ?>
			&nbsp;
		</td></tr>
		<tr  bgcolor="#FFFFFF"><td><?php echo __('Modified'); ?></td>
		<td>
			<?php echo h($userSession['UserSession']['modified']); ?>
			&nbsp;
		</td></tr>
	</table>
	<br />

	<h3><?php echo __('Related Evidences'); ?></h3>
	<?php if (!empty($userSession['Evidence'])): ?>
	<table cellpadding="2" cellspacing="1" bgcolor="#D4D4D4">
	<tr bgcolor="#FFFFFF">
		<th><?php echo __('File Upload Id'); ?></th>
		<th><?php echo __('Description'); ?></th>
		<th><?php echo __('Model Name'); ?></th>
		<th><?php echo __('Record'); ?></th>
		<th><?php echo __('User Session Id'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Approved By'); ?></th>
		<th><?php echo __('Prepared By'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th><?php echo __('Master List Of Format Id'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($userSession['Evidence'] as $evidence): ?>
		<tr bgcolor="#FFFFFF">
			<td><?php echo $evidence['file_upload_id']; ?></td>
			<td><?php echo $evidence['description']; ?></td>
			<td><?php echo $evidence['model_name']; ?></td>
			<td><?php echo $evidence['record']; ?></td>
			<td><?php echo $evidence['user_session_id']; ?></td>
			<td><?php echo $evidence['created']; ?></td>
			<td><?php echo $evidence['approved_by']; ?></td>
			<td><?php echo $evidence['prepared_by']; ?></td>
			<td><?php echo $evidence['modified']; ?></td>
			<td><?php echo $evidence['master_list_of_format_id']; ?></td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	

	<h3><?php echo __('Related File Uploads'); ?></h3>
	<?php if (!empty($userSession['FileUpload'])): ?>
	<table cellpadding="2" cellspacing="1" bgcolor="#D4D4D4">
	<tr bgcolor="#FFFFFF">
		<th><?php echo __('Record'); ?></th>
		<th><?php echo __('File Dir'); ?></th>
		<th><?php echo __('File Details'); ?></th>
		<th><?php echo __('User Id'); ?></th>
		<th><?php echo __('File Type'); ?></th>
		<th><?php echo __('File Status'); ?></th>
		<th><?php echo __('Archived'); ?></th>
		<th><?php echo __('Result'); ?></th>
		<th><?php echo __('Version'); ?></th>
		<th><?php echo __('Comment'); ?></th>
		<th><?php echo __('User Session Id'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Approved By'); ?></th>
		<th><?php echo __('Prepared By'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th><?php echo __('Master List Of Format Id'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($userSession['FileUpload'] as $fileUpload): ?>
		<tr bgcolor="#FFFFFF">
			<td><?php echo $fileUpload['record']; ?></td>
			<td><?php echo $fileUpload['file_dir']; ?></td>
			<td><?php echo $fileUpload['file_details']; ?></td>
			<td><?php echo $fileUpload['user_id']; ?></td>
			<td><?php echo $fileUpload['file_type']; ?></td>
			<td><?php echo $fileUpload['file_status']; ?></td>
			<td><?php echo $fileUpload['archived']; ?></td>
			<td><?php echo $fileUpload['result']; ?></td>
			<td><?php echo $fileUpload['version']; ?></td>
			<td><?php echo $fileUpload['comment']; ?></td>
			<td><?php echo $fileUpload['user_session_id']; ?></td>
			<td><?php echo $fileUpload['created']; ?></td>
			<td><?php echo $fileUpload['approved_by']; ?></td>
			<td><?php echo $fileUpload['prepared_by']; ?></td>
			<td><?php echo $fileUpload['modified']; ?></td>
			<td><?php echo $fileUpload['master_list_of_format_id']; ?></td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	

	<h3><?php echo __('Related Histories'); ?></h3>
	<?php if (!empty($userSession['History'])): ?>
	<table cellpadding="2" cellspacing="1" bgcolor="#D4D4D4">
	<tr bgcolor="#FFFFFF">
		<th><?php echo __('Model Name'); ?></th>
		<th><?php echo __('Controller Name'); ?></th>
		<th><?php echo __('Action'); ?></th>
		<th><?php echo __('Record Id'); ?></th>
		<th><?php echo __('Get Values'); ?></th>
		<th><?php echo __('Pre Post Values'); ?></th>
		<th><?php echo __('Post Values'); ?></th>
		<th><?php echo __('User Session Id'); ?></th>
		<th><?php echo __('Branch Id'); ?></th>
		<th><?php echo __('Department Id'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Approved By'); ?></th>
		<th><?php echo __('Prepared By'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th><?php echo __('Master List Of Format Id'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($userSession['History'] as $history): ?>
		<tr bgcolor="#FFFFFF">
			<td><?php echo $history['model_name']; ?></td>
			<td><?php echo $history['controller_name']; ?></td>
			<td><?php echo $history['action']; ?></td>
			<td><?php echo $history['record_id']; ?></td>
			<td><?php echo $history['get_values']; ?></td>
			<td><?php echo $history['pre_post_values']; ?></td>
			<td><?php echo $history['post_values']; ?></td>
			<td><?php echo $history['user_session_id']; ?></td>
			<td><?php echo $history['branch_id']; ?></td>
			<td><?php echo $history['department_id']; ?></td>
			<td><?php echo $history['created']; ?></td>
			<td><?php echo $history['approved_by']; ?></td>
			<td><?php echo $history['prepared_by']; ?></td>
			<td><?php echo $history['modified']; ?></td>
			<td><?php echo $history['master_list_of_format_id']; ?></td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	
