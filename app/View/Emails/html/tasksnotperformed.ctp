<?php 
$env = $env;

echo "
<style>
body {
	font-family: Arial;font-size: 13px;padding: 20px 0px;width: 90%;line-height: 20px;background: #ffffff;
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
<h2 style='padding-left:20px'>You have not sent in an update for a task you own.</h2>  
</div>

<div style='border:1px solid #ccc'>
<p>";        

echo "<p><br /><table>
<tr>
<td style='padding:10px'>";
echo "Dear " . $employee;
echo "<br />This email is to inform you that an update is required for your task <b>" . $task_name ."</b> .";
echo "<br /></td>
</tr>
</table></p>
</div>
</p>
</div>
</body>
</html>";
// echo $message;
?>
