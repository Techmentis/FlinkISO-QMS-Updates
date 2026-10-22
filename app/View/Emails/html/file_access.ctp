<?php echo $this->element('Emails/email_header', array(
    'emailTitle' => __('File Access Request'),
    'emailPreheader' => __('A user needs permission to access a file.')
)); ?>
<p style="margin:0 0 20px 0;"><?php echo h(__('Dear User,')); ?></p>
<p style="margin:0;"><strong><?php echo h($employee); ?></strong> <?php echo h(__('tried to access')); ?> <strong><?php echo h($file_name); ?></strong><?php echo h(__('. Log in to FlinkISO and grant the required permission if appropriate.')); ?></p>
<?php echo $this->element('Emails/email_footer'); ?>
