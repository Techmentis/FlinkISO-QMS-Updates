<?php echo $this->Html->script(array('jquery.validate.min', 'jquery-form.min')); ?>
<?php echo $this->fetch('script'); ?>

<script>
    
    $().ready(function() {
        $("#submit-indicator").hide();
        
        $('#UserOptCheckForm').validate({
            rules: {
                "data[User][opt_code]": {
                    required: true,
                    
                }
            }
        });
        $("#submit-indicator").hide();
        $("#submit_id").click(function () {
            if ($('#UserOptCheckForm').valid()) {
                $("#submit_id").prop("disabled", true);
                $("#submit-indicator").show();
                $("#UserOptCheckForm").submit();
            }
        });
    });
</script>

<div id="users_ajax">
    <?php echo $this->Session->flash(); ?>
    <div class="nav">
        <div class="nav panel">
            <div class="col-md-12">
                <h4><?php echo __("Two way authentication"); ?></h4>
                <p><?php echo __('Please enter the OTP code which you get in your email'); ?></p>
                <?php echo $this->Form->create('User', array('role' => 'form', 'class' => 'form')); ?>
                <div class="row">
                    <div class="col-md-6"><?php   echo $this->Form->password('opt_code', array('label' => __('OPT Code'))); ?>
                    
                    
                    <?php
                    echo $this->Form->input('branchid', array('type' => 'hidden', 'value' => $this->Session->read('User.branch_id')));
                    echo $this->Form->input('departmentid', array('type' => 'hidden', 'value' => $this->Session->read('User.department_id')));
                    echo $this->Form->input('master_list_of_format_id', array('type' => 'hidden', 'value' => $documentDetails['MasterListOfFormat']['id']));
                    ?>
                    
                    
                    <?php echo $this->Form->submit(__('Submit'), array('div' => false, 'class' => 'btn btn-primary btn-success', 'id'=>'submit_id')); ?>
                    <?php echo $this->Html->image('indicator.gif', array('id' => 'submit-indicator')); ?>
                </div>
            </div>
            
            <?php echo $this->Form->end(); ?>
            <?php echo $this->Js->writeBuffer(); ?>
        </div>
        
    </div>
</div>
</div>
<script>
    $.ajaxSetup({beforeSend: function() {
        $("#busy-indicator").show();
    }, complete: function() {
        $("#busy-indicator").hide();
    }});
</script>
