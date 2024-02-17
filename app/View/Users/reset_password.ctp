<?php echo $this->Html->script(array('jquery.validate.min', 'jquery-form.min')); ?>
<?php echo $this->fetch('script'); ?>
<script>

    $().ready(function() {
        $('#UserResetPasswordForm').validate({
            rules: {
                "data[User][password]": {
                    required: true,
                    passwordPolicy: true,
                },
                'data[User][temppassword]': {
                    required: true,
                    equalTo: "#UserPassword",
                },
                
            }
        });

    });
</script>
<div  id="users_ajax">
    <?php echo $this->Session->flash(); ?>
    <div class="row">
        <div class="col-md-7">
            <?php echo $this->element('display_policy', array(), array('plugin' => 'PasswordSettingManager'));?>
        </div>
        <div class="col-md-5">
            <div class="panel panel-default">
                <div class="panel-heading"><h4>Reset Password</h4></div>
                <div class="panel-body">
                    <div class="row">
                        <?php echo $this->Form->create('User', array('role' => 'form', 'class' => 'form')); ?>
                        <fieldset>                              
                            <?php
                                // echo $this->Form->input('sr_no');
                            echo "<div class='col-md-12'>" . $this->Form->input('username',array('class'=>'form-control','label'=>'Enter your userame')) . "</div>";
                            echo "<div class='col-md-12'>" . $this->Form->input('password', array('class'=>'form-control', 'label' => __('New Password'), 'type' => 'password', 'div' => false, 'placeholder' => '*******'))."</div>";
                            echo "<div class='col-md-12'>" . $this->Form->input('temppassword', array('class'=>'form-control', 'label' => __('Confirm Password'), 'type' => 'password', 'div' => false, 'placeholder' => '*******'))."</div>";
                            ?>
                            <?php echo "<div class='col-md-12 text-center'><hr />" . $this->Html->link('Click here to generate OTP','#',array('onClick'=>'otp()')) ."<hr /></div>";?>
                            <?php echo "<div class='col-md-12'>" .$this->Form->input('otp',array('label'=>'OTP', 'class'=>'form-control','required'=>'required')). "</div>";
                            echo "<div class='col-md-12'><span class='help-block' id='otp_check'></span></div>";
                            ?>
                        </fieldset>
                        
                        <div class="col-md-12 show" id="toshow">
                            <br />
                            <?php echo $this->Form->submit('Submit',array('div'=>false,'class'=>'btn btn-info btn-sm','update'=>'#main','id'=>'submit_id')); ?>
                            <?php echo $this->Html->image('indicator.gif', array('id' => 'submit-indicator')); ?>
                            <?php echo $this->Form->end(); ?>
                            <?php echo $this->Js->writeBuffer(); ?>
                        </div>
                    </div>
                </div>
            </div>                  
        </div>
    </div>  
</div>
</div>
<script>

    function otp(email){
        var checkEmail = {email: $("#UserUsername").val(),}

        if($('#UserResetPasswordForm').validate()){
           $("#submit_id").prop("disabled",false);             
        }else{            
            $("#submit_id").prop("disabled",true);
            return false;
        }

    $.ajax({
        url:'<?php echo Router::url('/', true);?><?php echo $this->request->params['controller']?>/send_otp/' + $("#UserUsername").val(),
        type: "POST",
        data: checkEmail,
        dataType : "JSON",
        beforeSend:function(){$("#otp_check").show().html('Sending opt... please wait.');},        
        success:function(data, result) 
        {            
            if(data == "Yes"){
                $("#UserOtp").prop('required','required');
                $("#UserOtp").focus();
                $("#otp_check").html('OTP SENT, please check your email').removeClass("help-block").removeClass("text-danger").addClass("text-success");;                
            }else{
              $("#otp_check").html('').addClass(" text-danger").removeClass("help-block").removeClass("text-success").addClass(" text-danger");              
          }
      },
      error: function(jqXHR, textStatus, errorThrown) 
      {
        
      }
  });
}

$("#submit-indicator").hide();
$("#submit_id").click(function(){
   if($('#UserResetPasswordForm').valid()){
       $("#submit_id").prop("disabled",true);
       $("#submit-indicator").show();
       $("#UserResetPasswordForm").submit();
   }
});
</script>

