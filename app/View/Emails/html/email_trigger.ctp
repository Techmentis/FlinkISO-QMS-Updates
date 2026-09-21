<?php echo $this->element('Emails/email_header', array(
    'emailTitle' => $h2tag,
    'emailPreheader' => 'A FlinkISO record event requires your attention.'
)); ?>
<p style="margin:0 0 20px 0;">Dear User,</p>
<div style="margin:0 0 22px 0;"><?php echo $msg_content; ?></div>
<table role="presentation" width="100%" cellpadding="10" cellspacing="0" border="0" style="background-color:#f8f9fa; border:1px solid #e5e5e5;">
    <tr>
        <td width="110" style="font-weight:bold; border-bottom:1px solid #e5e5e5;">Record</td>
        <td style="border-bottom:1px solid #e5e5e5;"><?php echo h($record); ?></td>
    </tr>
    <tr>
        <td style="font-weight:bold;">By</td>
        <td><?php echo h($employee); ?></td>
    </tr>
</table>
<?php echo $this->element('Emails/email_footer'); ?>
