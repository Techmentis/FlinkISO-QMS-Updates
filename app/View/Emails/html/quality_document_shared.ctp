<?php echo $this->element('Emails/email_header', array(
    'emailTitle' => $title,
    'emailPreheader' => 'A quality document has been shared with you.'
)); ?>
<p style="margin:0 0 20px 0;">Dear User,</p>
<div><?php echo $html; ?></div>
<?php echo $this->element('Emails/email_footer'); ?>
