<?php echo $this->element('Emails/email_header', array(
    'emailTitle' => __('Login Details'),
    'emailPreheader' => __('Your FlinkISO login details are ready.')
)); ?>
<p style="margin:0 0 20px 0;"><?php echo h(__('Dear User,')); ?></p>
<p style="margin:0 0 20px 0;"><?php echo h(__('Welcome to')); ?> <strong><?php echo h(__('FlinkISO Quality Management Software')); ?></strong>.</p>
<p style="margin:0 0 20px 0;"><?php echo h(__('Please find your login details below:')); ?></p>
<table role="presentation" width="100%" cellpadding="10" cellspacing="0" border="0" style="background-color:#f8f9fa; border:1px solid #e5e5e5; margin-bottom:25px;">
    <tr><td width="120" style="font-weight:bold; border-bottom:1px solid #e5e5e5;"><?php echo h(__('URL')); ?></td><td style="border-bottom:1px solid #e5e5e5; word-break:break-all;"><a href="<?php echo h($url); ?>" style="color:#1769aa; text-decoration:none;"><?php echo h($url); ?></a></td></tr>
    <tr><td style="font-weight:bold; border-bottom:1px solid #e5e5e5;"><?php echo h(__('Login ID')); ?></td><td style="border-bottom:1px solid #e5e5e5;"><?php echo h($username); ?></td></tr>
    <tr><td style="font-weight:bold;"><?php echo h(__('Password')); ?></td><td><?php echo h($password); ?></td></tr>
</table>
<p style="margin:0 0 20px 0;"><?php echo h(__('For security, we recommend changing your password after your first login using the')); ?> <strong><?php echo h(__('Change Password')); ?></strong> <?php echo h(__('option in FlinkISO.')); ?></p>
<p style="margin:0;"><?php echo h(__('If you have difficulty accessing the system, please contact your FlinkISO administrator.')); ?></p>
<?php echo $this->element('Emails/email_footer'); ?>
