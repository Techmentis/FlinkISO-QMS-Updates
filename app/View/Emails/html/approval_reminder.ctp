<?php echo $this->element('Emails/email_header', array(
    'emailTitle' => __('Approval Reminder'),
    'emailPreheader' => __('You have a pending approval request.')
)); ?>
<p style="margin:0 0 20px 0;"><?php echo h(__('Dear User,')); ?></p>
<p style="margin:0 0 20px 0;"><?php echo h(__('You have had an approval request pending since')); ?> <strong><?php echo h($date); ?></strong>.</p>
<p style="margin:0;"><?php echo h(__('Please log in to FlinkISO QMS and process the request.')); ?></p>
<?php echo $this->element('Emails/email_footer'); ?>
