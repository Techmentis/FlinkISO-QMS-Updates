<?php    
echo $this->Html->css(array('code-input-main/code-input.min','prism'));
echo $this->fetch('css');
echo $this->Html->script(array(
    'code-input-main/plugins/prism-core.min',
    'code-input-main/plugins/prism-autoloader.min',
    'code-input-main/code-input.min',
    'code-input-main/plugins/autodetect',
    'code-input-main/plugins/indent',
));
echo $this->fetch('script');
?>
<script>
    $().ready(function(){
        codeInput.registerTemplate("add", codeInput.templates.prism(Prism, [new codeInput.plugins.Indent()]));  
        <?php if(isset($error)){ ?>codeInput.registerTemplate("error", codeInput.templates.prism(Prism, [new codeInput.plugins.Indent()]));<?php } ?>
    })
    
</script>
<div class="content">
    <?php echo $this->Session->flash();?>   
    <div class="row">
        <div class="col-md-12"><p><h4>How to Configure Email?</h4></p>
            <p>Please refer to instructions below. Once you update and save the file. Try sending email from the form below by entering your email address:</p>
        </div>        
        <div class="col-md-12">            
            <?php echo $this->Form->create('Settings',array('action'=>'smtp_details',$this->Session->read('User.company_id')),array('class'=>'form-control','id'=>'SmtpDetails','type'=>'post'));?>
            <div class="row">
                <div class="col-md-12"><?php echo $this->Form->input('email_address',array('required'=>true,'type'=>'email', 'class'=>'form-control'));?></div>
                <div class="col-md-12"><br /><?php echo $this->Form->submit('Submit',array('class'=>'btn btn-success btn-sm'));?></div>
            </div>
            <?php echo $this->Form->end();?>
            <?php if(isset($error)){ ?>
                <code-input style="height: 550px;" name="return_value_for_dropdown" required id="return_value_for_dropdown" style="resize: both; overflow: hidden; " lang="JavaScript" placeholder="Write some JavaScript!" template="error"><?php echo $error;?>
            </code-input>
        <?php } ?>

    </div>

    <div class="col-md-12">        
        <p><h4>Email Configuration:</h4></p>
        <p>Go to your_directory/flinkiso/app/Config/Email.php</p>
        <p>Update class EmailConfig { } with your email's SMTP credentials. Provide values for all three options viz: $default, $smtp & $fast.</p>
        <p>
            <code-input style="height: 1120px;" name="return_value_for_dropdown" required id="return_value_for_dropdown" style="resize: both; overflow: hidden; " lang="JavaScript" placeholder="Write some JavaScript!" template="add">
                class EmailConfig {
                    public $default = array(
                    "transport" => "Mail",
                    "from" => array("noreply@flinkiso.com" => "FlinkISO"),
                    //"charset" => "utf-8",
                    //"headerCharset" => "utf-8",
                    );

                    public $smtp = array(

                    "transport" => "Smtp",
                    "from" => array("your_email@your_domain.com" => "FlinkISO"),
                    "host" => "your_host",
                    "port" => 465,
                    "timeout" => 30,
                    "username" => "username",
                    "password" => "********",
                    "client" => null,
                    "log" => false,
                    );

                    public $fast = array(
                    "from" => "your_email@your_domain",
                    "sender" => null,
                    "to" => null,
                    "cc" => null,
                    "bcc" => null,
                    "replyTo" => null,
                    "readReceipt" => null,
                    "returnPath" => null,
                    "messageId" => true,
                    "subject" => null,
                    "message" => null,
                    "headers" => null,
                    "viewRender" => null,
                    "template" => false,
                    "layout" => false,
                    "viewVars" => null,
                    "attachments" => null,
                    "emailFormat" => null,
                    "transport" => "Smtp",
                    "host" => "your_host",
                    "port" => 465,
                    "timeout" => 30,
                    "username" => "username",
                    "password" => "********",
                    "client" => null,
                    "log" => true,
                    //"charset" => "utf-8",
                    //"headerCharset" => "utf-8",
                    );
                }
            </code-input>
        </p>
    </div>
</div>
</div>
