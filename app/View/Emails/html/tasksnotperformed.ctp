<?php echo $this->element('Emails/email_header', array(
    'emailTitle' => 'Task Update Required',
    'emailPreheader' => 'A task you own needs an update.',
    'environment' => isset($env) ? $env : null,
    'app_url' => isset($app_url) ? $app_url : null
)); ?>
<p style="margin:0 0 20px 0;">Dear <?php echo h($employee); ?>,</p>
<div style="padding:18px; background-color:#f8f9fa; border:1px solid #e5e5e5;">
    You have not submitted an update for the task <strong><?php echo h($task_name); ?></strong>. Please log in to FlinkISO and provide the required update.
</div>
<?php echo $this->element('Emails/email_footer'); ?>
