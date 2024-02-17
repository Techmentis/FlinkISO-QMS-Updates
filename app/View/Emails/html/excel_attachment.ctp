<?php 
$env = $env;

echo $message = "
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
<h2 style='padding-left:20px'>".$department." Report</h2>  
</div>
<p>
<div style='border:1px solid #ccc'>
<table>
<tr>
<td style='padding:10px'>
Please find the attachment of Excel report of ".$department.".
<br />
$message
<br /><br />
</td>
</tr>
<tr>
<td style='padding:10px'>
<div class='footer' id='footer' style='font-size:10px; color:#ccc'>
<ul style='padding-left: 0px'>
<li style='list-type:none outside none; margin-right:10px;  float:left; '><a target='_blank' href='https://www.flinkiso.com/'>About FlinkISO</a></li>
<li style='list-type:none outside none; margin-right:10px;  float:left; '><a href='/flink-demo/helps/help'>Help Document</a></li>
<li style='list-type:none outside none; margin-right:10px;  float:left; '><a target='_blank' href='https://www.flinkiso.com/terms-and-conditions.html'>Terms &amp; Conditions</a></li>
<li style='list-type:none outside none; margin-right:10px;  float:left; '><a target='_blank' href='https://flinkiso.com/contact-us.html'>Contact us</a></li>
</ul>
</div>
</td>
</tr>
</table>
</div>

</p>

</div>
</body>
</html>";
?>
