<?php echo $this->element('Emails/email_header', array(
    'emailTitle' => 'Summary of Open Tasks',
    'emailPreheader' => 'Your weekly FlinkISO open-task summary is ready.',
    'environment' => isset($env) ? $env : null,
    'app_url' => isset($app_url) ? $app_url : null
)); ?>
<table role="presentation" width="100%" cellpadding="6" cellspacing="0" border="0" style="width:100%; border:1px solid #d9d9d9; border-collapse:collapse; font-size:11px; line-height:1.35;">
    <thead>
        <tr style="background-color:#1769aa; color:#ffffff;">
            <th align="left">Task</th>
            <th align="center">Completion</th>
            <?php $headerDate = $start_date; ?>
            <?php while (strtotime($headerDate) <= strtotime($end_date)): ?>
            <th align="center">W<?php echo date('W', strtotime($headerDate)); ?><br><span style="font-weight:normal;"><?php echo date('d M', strtotime($headerDate)); ?>&ndash;<?php echo date('d M', strtotime('+7 days', strtotime($headerDate))); ?></span></th>
            <?php $headerDate = date('Y-m-d', strtotime('+7 days', strtotime($headerDate))); ?>
            <?php endwhile; ?>
            <th align="center">RAG</th>
            <th align="left">Assigned To</th>
        </tr>
    </thead>
    <tbody>
    <?php if (!empty($tasks)): ?>
        <?php foreach ($tasks as $task): ?>
        <tr>
            <td style="border-top:1px solid #e5e5e5;"><?php echo h($task['Task']['name']); ?></td>
            <td align="center" style="border-top:1px solid #e5e5e5;"><?php echo h($task['Task']['task_completion']); ?>%</td>
            <?php $weekDate = $start_date; ?>
            <?php while (strtotime($weekDate) <= strtotime($end_date)): ?>
                <?php
                $performed = false;
                foreach ((array)$task['TaskStatus'] as $taskStatus) {
                    if (date('o-W', strtotime($taskStatus['task_date'])) === date('o-W', strtotime($weekDate))) {
                        $performed = ((int)$taskStatus['task_performed'] === 1);
                        break;
                    }
                }
                ?>
                <td align="center" style="border-top:1px solid #e5e5e5; color:<?php echo $performed ? '#398439' : '#c9302c'; ?>;"><?php echo $performed ? 'Performed' : 'Not performed'; ?></td>
                <?php $weekDate = date('Y-m-d', strtotime('+7 days', strtotime($weekDate))); ?>
            <?php endwhile; ?>
            <?php
            $ragColors = array(0 => '#d9534f', 1 => '#f0ad4e', 2 => '#5cb85c');
            $ragColor = isset($ragColors[(int)$task['Task']['rag_status']]) ? $ragColors[(int)$task['Task']['rag_status']] : '#dddddd';
            $assignedUser = isset($users[$task['Task']['user_id']]) ? $users[$task['Task']['user_id']] : '';
            ?>
            <td align="center" style="border-top:1px solid #e5e5e5;"><span style="display:inline-block; width:14px; height:14px; border-radius:7px; background-color:<?php echo $ragColor; ?>;">&nbsp;</span></td>
            <td style="border-top:1px solid #e5e5e5;"><?php echo h($assignedUser); ?></td>
        </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr><td colspan="20" style="padding:18px; text-align:center; color:#777777;">No open tasks found.</td></tr>
    <?php endif; ?>
    </tbody>
</table>
<?php echo $this->element('Emails/email_footer'); ?>
