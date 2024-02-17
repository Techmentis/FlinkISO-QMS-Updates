<?php 
echo $this->Html->script(array('jquery.validate.min', 'jquery-form.min')); 
echo $this->fetch('script');
?>
<script>
    $().ready(function() {
        $('#UserChangePasswordForm').validate({
            rules: {
                'data[User][current_password]': {
                    required: true,
                },
                'data[User][new_password]': {
                    required: true,
                    passwordPolicy: true,
                },
                'data[User][confirm_password]': {
                    required: true,
                    equalTo: "#UserNewPassword",
                },
            }
        });

    });
</script>
<?php echo $this->Session->flash(); ?>
<?php echo $this->element('nav-header-lists',array('postData'=>array('pluralHumanName'=>'Users','modelClass'=>'User','options'=>array(),'pluralVar'=>'users'))); ?>
<div class="nav panel panel-default">
    <?php echo $this->Session->flash(); ?>
    <div class="nav">
        <div class="change_password form col-md-12">
            <h4><?php echo __("Reset your Password"); ?></h4>
            <?php echo $this->Form->create(array('role' => 'form', 'class' => 'form')); ?>
            <div class="row">
                <div class="col-md-4"><?php echo $this->Form->input('current_password', array('label' => __('Current Password'), 'type' => 'password', 'class' => 'form-control', 'div' => false)); ?></div>
                <div class="col-md-4"><?php echo $this->Form->input('new_password', array('label' => __('New Password'), 'type' => 'password', 'class' => 'form-control', 'div' => false)); ?></div>
                <div class="col-md-4"><?php echo $this->Form->input('confirm_password', array('label' => __('Confirm Password'), 'type' => 'password', 'class' => 'form-control', 'div' => false)); ?></div>
                <?php
                echo $this->Form->input('branchid', array('type' => 'hidden', 'value' => $this->Session->read('User.branch_id')));
                echo $this->Form->input('departmentid', array('type' => 'hidden', 'value' => $this->Session->read('User.department_id')));
                ?>
            </div>
            <?php 
            echo $this->Form->hidden('History.pre_post_values', array('value' => json_encode($this->data)));
            echo "<br />" . $this->Form->submit(__('Submit'), array('class' => 'btn btn-primary btn-success', 'id' => 'submit_id'));
            echo $this->Html->image('indicator.gif', array('id' => 'submit-indicator'));
            echo $this->Form->end();
            echo $this->Js->writeBuffer();
            echo $this->element('display_policy', array(), array('plugin' => 'PasswordSettingManager'));?>
        </div>        
    </div>
</div>

<script>
    $.ajaxSetup({beforeSend: function() {
        $("#busy-indicator").show();
    }, complete: function() {
        $("#busy-indicator").hide();
    }
});
    $("#submit-indicator").hide();
    $("#submit_id").click(function(){
        if($('#UserChangePasswordForm').valid()){
           $("#submit_id").prop("disabled",true);
           $("#submit-indicator").show();
           $("#UserChangePasswordForm").submit();
       }
   });
</script>
