<?php 
$env = $env;

echo $message = "
<style>
body {font-family: Arial;font-size: 13px;padding: 20px 0px;width: 90%;line-height: 20px;background: #ffffff;}

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
                <h2 style='padding-left:20px'>Performance Review Questions</h2>  
            </div>
        <p>
        <div style='border:1px solid #ccc'>
        <table>
            <tr>
                <td style='padding:10px'>
                    Dear ".$recipientName.", <br /><br />
		    You are receiving this email in response to your appraisal session.
		    <br /><br />
		    To answer your performance review questions, please click the link below.
		    <br /><br />
		    <a href='".$baseurl."'>Appraisal Questions</a>
		    <br /><br />
		    <em><strong>Login to FlinkISO application before you attend the appraisal session. This link will be active till " . $appraisalDate . ".</strong></em>
		    <br /><br />
                    If you need any additional support, please contact " . $appraiserName . " (" . $appraiserEmail . ")<br/><br/>                    
                </td>
            </tr>
            </table>
        </div>
        </p>

</div>

    </body>
</html>";
?>
