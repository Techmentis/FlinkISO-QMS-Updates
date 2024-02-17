<?php 
if(FULL_BASE_URL == 'https://sit-qms.tranzport.com')$env =  "SIT";
elseif(FULL_BASE_URL == 'https://qa-qms.tranzport.com')$env =  "QA";
elseif(FULL_BASE_URL == 'https://dev-qms.tranzport.com')$env = "DEV";
else $env = "None";

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
    <h2 style='padding-left:20px'>Training Need Identification</h2>  
</div>

             <div style='border:1px solid #ccc'>
                <table>
                    <tr>
                        <td style='padding:10px'>
                            <h2>".$course. "</h2><br /><p>" .$details."</p>
                        </td>
                    </tr>
                </table>

                <table width='100%'>
                    <tr><td>By User</td><td>".$by_user."</td></tr>
                    <tr><td>Date/Time</td><td>".$date_time."</td></tr>
        </table>
        
            </div>

        </div>        
    </body>
</html>";
?>
