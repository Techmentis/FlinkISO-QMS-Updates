<head>
    <style type="text/css">
        body{margin:0; padding: 0; background-color: #24303e;}
        .top{width: 100%;  display: block; text-align: left; background-color: #24303e;}
        .logo{background-color:#0094ff; width:75px; padding: 12px 12px; font-size: 16px; font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; color:#fff; display: block;}
        .content{width: 96%; background-color: #f8f8f8; border-bottom: 2px dashed #666; margin: 0; padding: 2%; display: block; text-align: left; font-family: arial; font-size: 14px;}
    </style>
</head>
<body>
    <center>
        <div class="top">
            <div class="logo">Flink<strong>ISO</strong></div>
        </div>
        <div class="content">
            <?php if($message == 1){ ?>
                <h3>FlinkISO : Record Approved</h3>
                <p>Dear <?php echo $username;?></p>            
                <p>
                    One of the records sent for the approval has been approved by <?php echo $by;?><br />. 
                Login to FlinkISO QMS for more details.</p><br />Approver's response : <br /><?php echo $response;?><br /></p>
            <?php }else{ ?>
              <p>One of the records sent to <?php echo $by;?> for the approval has updates.<br /> 
                Login to FlinkISO QMS application for more details on your Dashboard.
                <br /><br /><strong>Approver's response </strong>: <?php echo $response;?><br /></p></div>
            <?php } ?>
            
        </div>
    </center>
</body>
