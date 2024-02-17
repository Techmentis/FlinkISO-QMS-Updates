<?php echo $this->Html->script(array('jquery.validate.min', 'jquery-form.min')); ?>
<?php echo $this->fetch('script'); ?>

<script>
    
    $().ready(function() {
        $("#submit-indicator").hide();
        $('#UserTwoWayAuthenticationForm').validate();
        $("#submit-indicator").hide();
        $("#submit_id").click(function () {
            if ($('#UserTwoWayAuthenticationForm').valid()) {
                $("#submit_id").prop("disabled", true);
                $("#submit-indicator").show();
                $("#UserTwoWayAuthenticationForm").submit();
            }
        });
    });
</script>

<div id="users_ajax">
    <?php echo $this->Session->flash(); ?>
    <div class="nav panel panel-default">
        <div class="users form col-md-12">
            <h4><?php //echo __("Add Branch"); ?></h4>
            <?php echo $this->Form->create('User', array('role' => 'form', 'class' => 'form')); ?>
            <div class="row">
                <div class="col-md-12"><?php   echo $this->Form->input('two_way_authentication', array('type' => 'radio', 'legend' => '<h4>' . __('Two way authentication') . '</h4>', 'options' => array(0 => 'Disable', 1 => 'Enable'), 'default' => $authentication['Company']['two_way_authentication'])); ?>
                
            </div>
            <div class="col-md-12">
                <div class="alert alert-danger">
                    <p><strong>Note: </strong>Do not enable Two Way Authentication unless your emails are working. We recomment setting up 
                        <strong><?php echo $this->Html->link(__('SMTP Details'),array('controller'=>'users','action'=>'smtp_details')); ?></strong>
                    first.</p>
                </div>
            </div>
            <?php
            echo $this->Form->input('branchid', array('type' => 'hidden', 'value' => $this->Session->read('User.branch_id')));
            echo $this->Form->input('departmentid', array('type' => 'hidden', 'value' => $this->Session->read('User.department_id')));
            echo $this->Form->input('master_list_of_format_id', array('type' => 'hidden', 'value' => $documentDetails['MasterListOfFormat']['id']));
            ?>
            <div class="col-md-12" style="padding-left:40px;">
             
                <?php echo $this->Form->submit(__('Submit'), array('div' => false, 'class' => 'btn btn-primary btn-success', 'id'=>'submit_id')); ?>
                <?php echo $this->Html->image('indicator.gif', array('id' => 'submit-indicator')); ?>
            </div>
        </div>
        
        <?php echo $this->Form->end(); ?>
        <?php echo $this->Js->writeBuffer(); ?>
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
