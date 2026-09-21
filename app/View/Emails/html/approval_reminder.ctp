<?php echo $this->element('Emails/email_header', array(
    'emailTitle' => 'Approval Reminder',
    'emailPreheader' => 'You have a pending approval request.'
)); ?>
<p style="margin:0 0 20px 0;">Dear User,</p>
<p style="margin:0 0 20px 0;">You have had an approval request pending since <strong><?php echo h($date); ?></strong>.</p>
<p style="margin:0;">Please log in to FlinkISO QMS and process the request.</p>
<?php echo $this->element('Emails/email_footer'); ?>
