<?php 
$env = $env;


echo "
<style>
body {
	font-family: Arial;font-size: 13px;padding: 20px 0px;width: 90%;line-height: 20px;background: #ffffff;
}
.circle {
  width: 20px;
  height: 20px;
  -webkit-border-radius: 25px;
  -moz-border-radius: 25px;
  border-radius: 10px;      
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
<h2 style='padding-left:20px'>Summary of Open Tasks</h2>	
</div>";

echo "<table border='0' cellspacing='1' cellpadding='2' bgcolor='#666666' style='width:100%',float:left;margin:0px; padding:5px 20px;>";
echo "<tr bgcolor='#333'><th style='color:#fff;text-align:left'>Task</th>";
echo "<th style='color:#fff; text-align:center'>Task Completion</th>";
$date     = $start_date;
$end_date = $end_date;
while (strtotime($date) <= strtotime($end_date)) { 
    echo "<th style='color:#fff'>" . 
    date('W',strtotime($date)) ." <br /> <small>". date('d M',strtotime($date)) . " - ". date("d M", strtotime("+7 days", strtotime($date))) . "
    </small></th>
    ";
    $date = date("Y-m-d", strtotime("+7 days", strtotime($date)));
};
echo "<th style='color:#fff'>RAG Status</th>";
echo "<th style='color:#fff;text-align:left'>Assigned To</th>";
echo "</tr>";
foreach ($tasks as $task){
    echo "<tr>
    <td bgcolor='#FFFFFF'>
    ".$task['Task']['name']."
    </td>
    <td bgcolor='#FFFFFF' align='center' >
    ".$task['Task']['task_completion']."%
    </td>";
    
    $date     = $start_date;
    $end_date = $end_date;
    while (strtotime($date) <= strtotime($end_date)) { ?>
        <?php 
        if(!$task['TaskStatus']){
            echo "<td bgcolor='#FFFFFF' style='color:#d9534f'>Not Performed</td>";
        }else{
            foreach ($task['TaskStatus'] as $taskStatus) {
                if(date('W',strtotime($taskStatus['task_date'])) == date('W',strtotime($date)) && $taskStatus['task_performed'] == 1){
                    echo "<td bgcolor='#FFFFFF' style='color:#5cb85c'>Perfoemed</td>";
                }elseif(date('W',strtotime($taskStatus['task_date'])) == date('W',strtotime($date)) && $taskStatus['task_performed'] != 1){
                    echo "<td bgcolor='#FFFFFF' style='color:#d9534f'> Not Performed</td>";
                }else{
                    echo "<td bgcolor='#FFFFFF' style='color:#d9534f'> Not Performed</td>";
                }
            }   
        }                                    
        $date = date("Y-m-d", strtotime("+7 days", strtotime($date)));
    };
    echo        "<td bgcolor='#FFFFFF' align='center'>";
    if($task['Task']['rag_status'] == 0)echo "<div class='circle' style='background:#d9534f;width:16px'>&nbsp;</div>";
    elseif($task['Task']['rag_status'] == 1)echo "<div class='circle' style='background:#f0ad4e;width:16px'>&nbsp;</div>";
    elseif($task['Task']['rag_status'] == 2)echo "<div class='circle' style='background:#5cb85c;width:16px'>&nbsp;</div>";
    else echo "<div class='circle' style='background:#ddd;width:16px'></div>";
    "</td>";
    echo            "<td bgcolor='#FFFFFF'>".$users[$task['Task']['user_id']] ."</td>";
    echo        "</tr>";
}
echo            "</table>";

echo  "</div>
</body>
</html>";
// echo $message;
?>
