<?php echo $this->element('Emails/email_header', array(
    'emailTitle' => 'Change Request from ' . $companyName,
    'emailPreheader' => 'A change request has been submitted.',
    'environment' => isset($env) ? $env : null,
    'app_url' => isset($app_url) ? $app_url : null
)); ?>
<div style="padding:18px; background-color:#f8f9fa; border:1px solid #e5e5e5;">
    <?php echo $details; ?>
</div>
<?php echo $this->element('Emails/email_footer'); ?>
