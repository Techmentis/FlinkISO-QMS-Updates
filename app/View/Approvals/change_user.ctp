<?php echo $this->Html->script(array('jquery.validate.min', 'jquery-form.min')); ?>
<?php echo $this->fetch('script'); ?>

<script>
    $.validator.setDefaults({
        submitHandler: function(form) {
            $(form).ajaxSubmit({
                url: "<?php echo Router::url('/', true); ?><?php echo $this->request->params['controller'] ?>/change_user",
                type: 'POST',
                target: '#main',
                beforeSend: function(){
                 $("#submit_id").prop("disabled",true);
                 $("#submit-indicator").show();
             },
             complete: function() {
                 $("#submit_id").removeAttr("disabled");
                 $("#submit-indicator").hide();
             },
             error: function(request, status, error) {
                    //alert(request.responseText);
                alert('Action failed!');
            }
        });
        }
    });

    $().ready(function() {
        $("#submit-indicator").hide();
        $('#ApprovalChangeUserForm').validate();
    });
</script>

<div id="main">
    <?php echo $this->Session->flash(); ?>
    <div class="nav">
        <div class="branches form col-md-8">
            <h4><?php echo __("Change Approver"); ?></h4>
            <?php echo $this->Form->create('Approval', array('role' => 'form', 'class' => 'form', 'default' => false)); ?>
            <div class="row">
                <div class="col-md-12"><?php echo $this->Form->input('approver_id', array('options'=>$approversList)); ?></div>
                
                <?php
                echo $this->Form->hidden('id', array('value' => $approval['Approval']['id']));
                echo $this->Form->hidden('controller_name', array('value' => $approval['Approval']['controller_name']));
                echo $this->Form->hidden('record', array('value' => $approval['Approval']['record']));                    
                ?>
            </div>
            
            <?php echo $this->Form->submit(__('Submit'), array('div' => false, 'class' => 'btn btn-primary btn-success', 'update' => '#branches_ajax', 'async' => 'false','id'=>'submit_id')); ?>
            <?php echo $this->Html->image('indicator.gif', array('id' => 'submit-indicator')); ?>
            <?php echo $this->Form->end(); ?>
            <?php echo $this->Js->writeBuffer(); ?>
        </div>
        <div class="col-md-4">
            
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
