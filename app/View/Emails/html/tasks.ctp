<?php 
$env = $env;

echo "
<style>
body {
	font-family: Arial;font-size: 13px;padding: 20px 0px;width: 100%;line-height: 20px;background: #ffffff;
}

label{ }

</style> 
<html>
<body>
<div style='float:left;width:99%; display:block;padding:2px;margin:2px;'>
<div style='background:#fff; margin:0px; float:left; padding:0; border-bottom:1px dashed #FFFFFF; width:100%;'> 
<a href='".$app_url."' target='_blank' style='padding:0px; margin:0px; float:left'><h3>FlinkISO</h3></a>
<span style='float:left; color:#cccccc; margin-top:5px; padding-left:15px; font-size:20px'>".$env."</span>
</div>
<div style='background:#16AFDD;margin:0px; float:left; padding:0; border-bottom:1px dashed #FFFFFF; color:#fff; width:100%; text-shadow:0px 0px 1px #000000; line-height:19px'>
<h2 style='padding-left:20px'>Daily Tasks Reminders</h2>  
</div>
<p>
<div style='border:1px solid #ccc'>";

echo "<table border='0' cellspacing='1' cellpadding='2' bgcolor='#666666' style='width:100%',float:left;margin:0px; padding:5px 20px;>";
echo "<tr bgcolor='#333'><th style='color:#fff;text-align:left'>Name</th>
<th style='color:#fff;text-align:left'>Task Completion</th>
<th style='color:#fff;text-align:left'>Task Type</th>
<th style='color:#fff;text-align:left'>Task Status</th>
<th style='color:#fff;text-align:left'>Assigned To</th>
<th style='color:#fff;text-align:left'>From-To</th></tr>";
if ($tasks) {
    $x = 0;
    foreach ($tasks as $task): 
        echo  "<tr>";
        echo  "<td bgcolor='#FFFFFF'><b>".h($task['Task']['name']) ."</b>&nbsp;(".$schedules[$task['Task']['schedule_id']].")</td>";
        echo  "<td bgcolor='#FFFFFF' style='text-align:center'>".$task['Task']['task_completion'] . "%&nbsp;</td>";
        
        if($task['Task']['task_type'] == 0)echo  "<td bgcolor='#FFFFFF'><small>General&nbsp;</small></td>";
        elseif($task['Task']['task_type'] == 1)echo  "<td bgcolor='#FFFFFF'><small>Process Related&nbsp;</small></td>";
        elseif($task['Task']['task_type'] == 2)echo  "<td bgcolor='#FFFFFF'><small>Project Related&nbsp;</small></td>";
        else echo "<td bgcolor='#FFFFFF'>".$task['Task']['type']."</td>";

        echo  ($task['Task']['task_status'])? "<td bgcolor='#FFFFFF'>Completed&nbsp;</td>":"<td bgcolor='#FFFFFF'>On Going&nbsp;</td>";
        echo  "<td bgcolor='#FFFFFF'>". $task['User']['name']."&nbsp;</td>";
        echo  "<td bgcolor='#FFFFFF'>". $task['Task']['start_date'] ."-".$task['Task']['end_date']."&nbsp;</td>";
        echo "</tr>";
        $x++;
    endforeach;
} else {

    echo  "<tr><td colspan=16 bgcolor='#FFFFFF'><?php echo __('No results found'); ?></td></tr>";
} 
echo  "</table>
</div>
</p>
</div>
</body>
</html>";
?>
