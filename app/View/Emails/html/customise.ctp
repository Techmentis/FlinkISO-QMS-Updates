<?php echo $this->element('Emails/email_header', array(
    'emailTitle' => 'Customization Request',
    'emailPreheader' => 'A new FlinkISO customization request was submitted.',
    'environment' => isset($env) ? $env : null,
    'app_url' => isset($app_url) ? $app_url : null
)); ?>
<table role="presentation" width="100%" cellpadding="10" cellspacing="0" border="0" style="background-color:#f8f9fa; border:1px solid #e5e5e5;">
    <tr><td width="120" style="font-weight:bold; border-bottom:1px solid #e5e5e5;">From</td><td style="border-bottom:1px solid #e5e5e5;"><?php echo h($viewVars['employee']); ?></td></tr>
    <tr><td style="font-weight:bold; border-bottom:1px solid #e5e5e5;">Branch</td><td style="border-bottom:1px solid #e5e5e5;"><?php echo h($viewVars['branch_name']); ?></td></tr>
    <tr><td style="font-weight:bold; border-bottom:1px solid #e5e5e5;">URL</td><td style="border-bottom:1px solid #e5e5e5; word-break:break-all;"><?php echo h($viewVars['request_for']); ?></td></tr>
    <tr><td style="font-weight:bold; vertical-align:top;">Details</td><td><?php echo nl2br(h($viewVars['customization_details'])); ?></td></tr>
</table>
<?php echo $this->element('Emails/email_footer'); ?>
