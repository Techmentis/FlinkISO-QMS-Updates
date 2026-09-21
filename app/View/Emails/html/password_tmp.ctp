<?php echo $this->element('Emails/email_header', array(
    'emailTitle' => 'Password Reset',
    'emailPreheader' => 'Complete your FlinkISO password reset.'
)); ?>
<p style="margin:0 0 20px 0;">Dear User,</p>
<p style="margin:0 0 20px 0;">You are receiving this email in response to your recent password reset request.</p>
<table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 20px 0;">
    <tr><td style="background-color:#1769aa;"><a href="<?php echo h($baseurl); ?>" style="display:inline-block; padding:12px 22px; color:#ffffff; text-decoration:none; font-weight:bold;">Reset Password</a></td></tr>
</table>
<p style="margin:0; color:#777777; font-size:13px; word-break:break-all;">If the button does not work, open this link:<br><a href="<?php echo h($baseurl); ?>" style="color:#1769aa;"><?php echo h($baseurl); ?></a></p>
<?php echo $this->element('Emails/email_footer'); ?>
