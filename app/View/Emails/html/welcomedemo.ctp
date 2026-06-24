<?php 
$date = date('Y-m-d',strtotime('+16 days'));
?>
<head>
    <style type="text/css">
        body{margin:0; padding: 10px; background-color: #fff; width:100%; font-family:arial;text-align:left;}
        
        
        
    </style>
</head>
<body>
    
        <div class="top">
            <div class="logo">Flink<strong>ISO</strong></div>
        </div>
        <div class="content">
            <h3>Welcome to FlinkISO</h3><br />
            <p>Dear User,<br /><br /></p>            
            <p>Thank you for registering with FlinkISO&trade; On-Cloud edition.</p>
            <p>Your free trial will be active till <?php echo date('d M Y',strtotime('+15 days')) ;?></p>
            <p>Your paid subscription  will start on <?php echo date('d M Y',strtotime('+15 days')); ?> and will be active for month.</p>
            <p>You will have to renew your subscription after <?php echo date('d M Y',strtotime('+1 month',strtotime($date))) ;?></p>
            <p>You will have to renew your subscription in 7 days once the invoice is generated.</p>
            <p>You can ignore the automatically generated invoice if you do not wish to continue using the application.</p>


            <p><strong>Your email address is your username & password.</strong></p> 
            <p>You can login to your instance from the link below :</p>
            <table>
                <tr>
                    <td style='padding:10px; font-weight:600'>
                        URL : <?php echo$url;?><br/>
                        Login and Password : <?php echo $username;?><br/>
                    </td>
                </tr>
            </table>
            <p>You can reset your password after login.</p>
            <p>Visit : <a href="https://www.flinkiso.com/manual/introduction.html">Tutorial Section to get stated.</a></p>
            <p>You can also Visit : <a href="https://www.flinkiso.com/videos/qms-application/qms-introduction">Viodes Section</a> for step by step set up.</p>
            <p>Feel free to contact us at help@flinkiso.com for any additional help.</p> 
            <p>Thank you!<br />FlinkISO Team.</p>
        </div>
    
</body>
