<?php echo $this->element('Emails/email_header', array(
    'emailTitle' => 'File Access Request',
    'emailPreheader' => 'A user needs permission to access a file.'
)); ?>
<p style="margin:0 0 20px 0;">Dear User,</p>
<p style="margin:0;"><strong><?php echo h($employee); ?></strong> tried to access <strong><?php echo h($file_name); ?></strong>. Log in to FlinkISO and grant the required permission if appropriate.</p>
<?php echo $this->element('Emails/email_footer'); ?>
