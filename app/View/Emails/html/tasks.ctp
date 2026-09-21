<?php echo $this->element('Emails/email_header', array(
    'emailTitle' => 'Daily Task Reminders',
    'emailPreheader' => 'Your FlinkISO task summary is ready.',
    'environment' => isset($env) ? $env : null,
    'app_url' => isset($app_url) ? $app_url : null
)); ?>
<table role="presentation" width="100%" cellpadding="7" cellspacing="0" border="0" style="width:100%; border:1px solid #d9d9d9; border-collapse:collapse; font-size:12px; line-height:1.4;">
    <thead>
        <tr style="background-color:#1769aa; color:#ffffff;">
            <th align="left">Name</th>
            <th align="center">Completion</th>
            <th align="left">Type</th>
            <th align="left">Status</th>
            <th align="left">Assigned To</th>
            <th align="left">From&ndash;To</th>
        </tr>
    </thead>
    <tbody>
    <?php if (!empty($tasks)): ?>
        <?php foreach ($tasks as $task): ?>
        <?php
        $taskType = isset($task['Task']['type']) ? $task['Task']['type'] : '';
        if ((int)$task['Task']['task_type'] === 0) $taskType = 'General';
        elseif ((int)$task['Task']['task_type'] === 1) $taskType = 'Process Related';
        elseif ((int)$task['Task']['task_type'] === 2) $taskType = 'Project Related';
        $schedule = isset($schedules[$task['Task']['schedule_id']]) ? $schedules[$task['Task']['schedule_id']] : '';
        ?>
        <tr>
            <td style="border-top:1px solid #e5e5e5;"><strong><?php echo h($task['Task']['name']); ?></strong><?php if ($schedule !== ''): ?><br><span style="color:#777777;">(<?php echo h($schedule); ?>)</span><?php endif; ?></td>
            <td align="center" style="border-top:1px solid #e5e5e5;"><?php echo h($task['Task']['task_completion']); ?>%</td>
            <td style="border-top:1px solid #e5e5e5;"><?php echo h($taskType); ?></td>
            <td style="border-top:1px solid #e5e5e5;"><?php echo !empty($task['Task']['task_status']) ? 'Completed' : 'Ongoing'; ?></td>
            <td style="border-top:1px solid #e5e5e5;"><?php echo h($task['User']['name']); ?></td>
            <td style="border-top:1px solid #e5e5e5;"><?php echo h($task['Task']['start_date']); ?>&ndash;<?php echo h($task['Task']['end_date']); ?></td>
        </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr><td colspan="6" style="padding:18px; text-align:center; color:#777777;">No results found.</td></tr>
    <?php endif; ?>
    </tbody>
</table>
<?php echo $this->element('Emails/email_footer'); ?>
