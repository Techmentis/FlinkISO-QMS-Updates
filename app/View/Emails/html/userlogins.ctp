<?php echo $this->element('Emails/email_header', array(
    'emailTitle' => 'User Login Notification',
    'emailPreheader' => 'A FlinkISO user login event was recorded.'
)); ?>
<?php if (!empty($content)): ?>
<div><?php echo $content; ?></div>
<?php elseif (!empty($message)): ?>
<div><?php echo $message; ?></div>
<?php else: ?>
<p style="margin:0;">A user login event has been recorded in FlinkISO.</p>
<?php endif; ?>
<?php echo $this->element('Emails/email_footer'); ?>
