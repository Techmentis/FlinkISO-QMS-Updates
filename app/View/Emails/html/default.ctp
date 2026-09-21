<?php echo $this->element('Emails/email_header', array('emailTitle' => 'FlinkISO Notification')); ?>
<?php foreach (explode("\n", $content) as $line): ?>
<p style="margin:0 0 16px 0;"><?php echo $line; ?></p>
<?php endforeach; ?>
<?php echo $this->element('Emails/email_footer'); ?>
