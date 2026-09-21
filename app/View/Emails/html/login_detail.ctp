<?php echo $this->element('Emails/email_header', array(
    'emailTitle' => 'Login Details',
    'emailPreheader' => 'Your FlinkISO login details are ready.'
)); ?>
<p style="margin:0 0 20px 0;">Dear User,</p>
<p style="margin:0 0 20px 0;">Welcome to <strong>FlinkISO Quality Management Software</strong>.</p>
<p style="margin:0 0 20px 0;">Please find your login details below:</p>
<table role="presentation" width="100%" cellpadding="10" cellspacing="0" border="0" style="background-color:#f8f9fa; border:1px solid #e5e5e5; margin-bottom:25px;">
    <tr><td width="120" style="font-weight:bold; border-bottom:1px solid #e5e5e5;">URL</td><td style="border-bottom:1px solid #e5e5e5; word-break:break-all;"><a href="<?php echo h($url); ?>" style="color:#1769aa; text-decoration:none;"><?php echo h($url); ?></a></td></tr>
    <tr><td style="font-weight:bold; border-bottom:1px solid #e5e5e5;">Login ID</td><td style="border-bottom:1px solid #e5e5e5;"><?php echo h($username); ?></td></tr>
    <tr><td style="font-weight:bold;">Password</td><td><?php echo h($password); ?></td></tr>
</table>
<p style="margin:0 0 20px 0;">For security, we recommend changing your password after your first login using the <strong>Change Password</strong> option in FlinkISO.</p>
<p style="margin:0;">If you have difficulty accessing the system, please contact your FlinkISO administrator.</p>
<?php echo $this->element('Emails/email_footer'); ?>
