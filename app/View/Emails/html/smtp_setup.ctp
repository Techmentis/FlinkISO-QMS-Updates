<?php echo $this->element('Emails/email_header', array(
    'emailTitle' => __('SMTP Setup Details'),
    'emailPreheader' => __('Your SMTP setup was completed successfully.'),
    'environment' => isset($env) ? $env : null,
    'app_url' => isset($app_url) ? $app_url : null
)); ?>
<p style="margin:0 0 20px 0;"><?php echo h(__('Dear User,')); ?></p>
<div style="padding:18px; background-color:#f8f9fa; border:1px solid #e5e5e5;">
    <strong style="color:#1769aa;"><?php echo h(__('Congratulations!')); ?></strong><br><?php echo h(__('Your SMTP setup has been completed successfully.')); ?>
</div>
<?php echo $this->element('Emails/email_footer'); ?>
