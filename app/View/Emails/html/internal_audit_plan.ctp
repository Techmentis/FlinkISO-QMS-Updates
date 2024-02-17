<?php 
$env = $env;
echo $message = "
<html>
<head>
<style>
body {
    font-family: Arial;
    font-size: 13px;
    padding: 20px 10px;
    width: 100%;
    line-height: 20px;
    background: #ffffff;
}
label{ }
</style>
</head>
<body>
<div style='float:left;width:99%; display:block;padding:2px;margin:2px;'>
<div style='background:#fff; margin:0px; float:left; padding:0; border-bottom:1px dashed #FFFFFF; width:100%;'> 
<a href='".$app_url."' target='_blank' style='padding:0px; margin:0px; float:left'><h3>FlinkISO</h3></a>
<span style='float:left; color:#cccccc; margin-top:5px; padding-left:15px; font-size:20px'>".$env."</span>
</div>
<div style='background:#16AFDD;margin:0px; float:left; padding:0; border-bottom:1px dashed #FFFFFF; color:#fff; width:100%; text-shadow:0px 0px 1px #000000; line-height:19px'>
<h2 style='padding-left:20px'>Internal Audit Plan</h2>  
</div>

<div style='border:1px solid #ccc'>
<table>

<tr>
<td style='padding:10px'>
<table style='width:100%; padding:2px'>

<tr><td>".__('Internal Audit Plan')."</td>
<td> : ".$internalAuditPlan['InternalAuditPlan']['title']."
&nbsp;
</td></tr>
<tr><td>".__('Schedule Date From')."</td>
<td> : ".$internalAuditPlan['InternalAuditPlan']['schedule_date_from']."
&nbsp;
</td></tr>

<tr><td>".__('Schedule Date To')."</td>
<td> : ".$internalAuditPlan['InternalAuditPlan']['schedule_date_to']."
&nbsp;
</td></tr>

<tr><td>".__('Note')."</td>
<td> : ".nl2br($internalAuditPlan['InternalAuditPlan']['note'])."
&nbsp;
</td></tr>

</table>
</td>
</tr>

</table>
</div>

</div>
</body>
</html>";
?>
