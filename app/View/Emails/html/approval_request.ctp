<head>
<style type="text/css">
    body{margin:0; padding: 10px; background-color: #fff; width:100%; font-family:arial;text-align:left;}
</style>
</head>
<body>
<div class="content">
    <?php if($message == 1){ ?>
        <h3>FlinkISO : Record Approved</h3>
        <p>Dear <?php echo $to_name;?></p>        
        <p>One of the records sent for the approval has been approved by <?php echo $by;?>.<br /> 
        Login to FlinkISO QMS for more details.</p><br />Comment/ response : <?php echo $response;?><br /></p>
    <?php }else{ ?>
        <p>Dear <?php echo $to_name;?></p>
        <p>You have Approval related updates from <?php echo $by;?>.<br /> 
        For more details, login to FlinkISO QMS and check your dashboard's approval section.
        <br /><br /><strong>Comment/ response </strong>: <?php echo $response;?><br /></p></div>
    <?php } ?>        
</div>
</body>
