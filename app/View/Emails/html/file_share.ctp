<?php echo $this->element('Emails/email_header', array(
    'emailTitle' => $h2tag,
    'emailPreheader' => 'A file has been shared through FlinkISO.'
)); ?>
<p style="margin:0 0 20px 0;">Dear User,</p>
<div style="margin:0 0 22px 0; padding:16px 18px; background-color:#f8f9fa; border:1px solid #e5e5e5;"><?php echo $msg_content; ?></div>
<table role="presentation" width="100%" cellpadding="10" cellspacing="0" border="0" style="border:1px solid #e5e5e5;">
    <tr><td width="120" style="font-weight:bold; border-bottom:1px solid #e5e5e5;">By User</td><td style="border-bottom:1px solid #e5e5e5;"><?php echo h($by_user); ?></td></tr>
    <tr><td style="font-weight:bold; border-bottom:1px solid #e5e5e5;">Employee</td><td style="border-bottom:1px solid #e5e5e5;"><?php echo h($employee); ?></td></tr>
    <tr><td style="font-weight:bold; border-bottom:1px solid #e5e5e5;">Branch</td><td style="border-bottom:1px solid #e5e5e5;"><?php echo h($branch); ?></td></tr>
    <tr><td style="font-weight:bold; border-bottom:1px solid #e5e5e5;">Department</td><td style="border-bottom:1px solid #e5e5e5;"><?php echo h($department); ?></td></tr>
    <tr><td style="font-weight:bold;">Date / Time</td><td><?php echo h($date_time); ?></td></tr>
</table>
<?php echo $this->element('Emails/email_footer'); ?>
