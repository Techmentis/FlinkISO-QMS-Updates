<?php echo $this->element('Emails/email_header', array(
    'emailTitle' => __('Invoice'),
    'emailPreheader' => __('Your FlinkISO invoice is available.')
)); ?>
<div><?php echo nl2br($invoice['Invoice']['email_body']); ?></div>
<?php echo $this->element('Emails/email_footer'); ?>
