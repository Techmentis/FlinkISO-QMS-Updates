<?php
$isApproved = ((int)$message === 1);
echo $this->element('Emails/email_header', array(
    'emailTitle' => $isApproved ? 'Record Approved' : 'Approval Update',
    'emailPreheader' => 'You have an approval-related update in FlinkISO.'
));
?>
<p style="margin:0 0 20px 0;">Dear <?php echo h($to_name); ?>,</p>
<?php if ($isApproved): ?>
<p style="margin:0 0 20px 0;">A record submitted for approval has been approved by <strong><?php echo h($by); ?></strong>. Log in to FlinkISO QMS for more details.</p>
<?php else: ?>
<p style="margin:0 0 20px 0;">You have an approval-related update from <strong><?php echo h($by); ?></strong>. Log in to FlinkISO QMS and check the approval section on your dashboard.</p>
<?php endif; ?>
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f8f9fa; border:1px solid #e5e5e5;">
    <tr><td style="padding:16px 18px;"><strong>Comment / response</strong><br><?php echo nl2br(h($response)); ?></td></tr>
</table>
<?php echo $this->element('Emails/email_footer'); ?>
