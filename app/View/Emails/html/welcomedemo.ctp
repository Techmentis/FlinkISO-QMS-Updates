<?php $date = date('Y-m-d', strtotime('+16 days')); ?>
<?php echo $this->element('Emails/email_header', array(
    'emailTitle' => 'Welcome to FlinkISO',
    'emailPreheader' => 'Your FlinkISO On-Cloud trial is ready.'
)); ?>
<p style="margin:0 0 20px 0;">Dear User,</p>
<p style="margin:0 0 20px 0;">Thank you for registering for the FlinkISO&trade; On-Cloud edition.</p>
<p style="margin:0 0 20px 0;">Your free trial will remain active until <strong><?php echo date('d M Y', strtotime('+15 days')); ?></strong>. Your paid monthly subscription begins on that date and can be renewed after <?php echo date('d M Y', strtotime('+1 month', strtotime($date))); ?>.</p>
<p style="margin:0 0 20px 0;">You can ignore an automatically generated invoice if you do not wish to continue using the application.</p>
<table role="presentation" width="100%" cellpadding="10" cellspacing="0" border="0" style="background-color:#f8f9fa; border:1px solid #e5e5e5; margin-bottom:22px;">
    <tr><td width="130" style="font-weight:bold; border-bottom:1px solid #e5e5e5;">URL</td><td style="border-bottom:1px solid #e5e5e5; word-break:break-all;"><a href="<?php echo h($url); ?>" style="color:#1769aa;"><?php echo h($url); ?></a></td></tr>
    <tr><td style="font-weight:bold;">Login &amp; password</td><td><?php echo h($username); ?></td></tr>
</table>
<p style="margin:0 0 16px 0;">You can reset your password after logging in.</p>
<p style="margin:0 0 16px 0;"><a href="https://www.flinkiso.com/manual/introduction.html" style="color:#1769aa;">Visit the tutorial section</a> to get started.</p>
<p style="margin:0;">For additional help, contact <a href="mailto:help@flinkiso.com" style="color:#1769aa;">help@flinkiso.com</a>.</p>
<?php echo $this->element('Emails/email_footer'); ?>
