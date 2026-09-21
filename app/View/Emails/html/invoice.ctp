<?php echo $this->element('Emails/email_header', array(
    'emailTitle' => 'Invoice',
    'emailPreheader' => 'Your FlinkISO invoice is available.'
)); ?>
<div><?php echo nl2br($invoice['Invoice']['email_body']); ?></div>
<?php echo $this->element('Emails/email_footer'); ?>
