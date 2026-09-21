<?php echo $this->element('Emails/email_header', array(
    'emailTitle' => 'Two-Factor Authentication',
    'emailPreheader' => 'Your FlinkISO verification code is ready.'
)); ?>
<p style="margin:0 0 20px 0;">Dear User,</p>
<p style="margin:0 0 18px 0;">Use the following one-time verification code:</p>
<div style="padding:18px; background-color:#f8f9fa; border:1px solid #e5e5e5; color:#1769aa; font-size:28px; line-height:1.2; font-weight:bold; letter-spacing:4px; text-align:center;">
    <?php echo h($otp_code); ?>
</div>
<p style="margin:18px 0 0 0; color:#777777; font-size:13px;">If you did not request this code, you can safely ignore this email.</p>
<?php echo $this->element('Emails/email_footer'); ?>
