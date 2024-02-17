<?php echo $this->Html->script(array('jquery.validate.min', 'jquery-form.min')); ?>
<?php echo $this->fetch('script'); ?>
<script>
    $.validator.setDefaults();
    $().ready(function () {
        $("#submit-indicator").hide();
        var isSmtp = <?php echo $isSmtp; ?>;
        if (isSmtp) {
            $("#smtp").show();
        } else {
            $("#default").show();
        }
        $("#SettingSmtpDetailsForm").validate({
            rules: {
                "data[Setting][smtp_user]": {
                    required: true,
                    email: true
                },
                "data[Setting][default_user]": {
                    required: true,
                    email: true
                }
            }
        });
        
        $("#submit_id").click(function () {
            if ($('#SettingSmtpDetailsForm').valid()) {
                $("#submit_id").prop("disabled", true);
                $("#submit-indicator").show();
                $("#SettingSmtpDetailsForm").submit();
            }
        });
    });
    function Showdiv(value) {
        if (value === '0') {
            $("#smtp").hide();
            $("#default").show();
        } else if (value === '1') {
            $("#default").hide();
            $("#smtp").show();
        }
    }
</script>

<div id="smtp_ajax">
    <?php echo $this->Session->flash(); ?>
    <div class="nav panel panel-default">

        <?php if (!$this->Session->read('User.id')) { ?>
            <div class="col-md-12">
                <h5><strong>Installation Progress</strong></h5>
                <div class="progress progress-striped active" style="background: #CCC">
                    <div class="progress-bar"  role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 90%">
                    </div>
                </div>
            </div>
        <?php } ?>

        <div class="installations form col-md-12">

            <?php
            if (isset($isSmtp)) {
                echo $this->Form->input('is_smtp', array('type' => 'radio', 'legend' => '<h4>' . __('Email Server Setting') . '</h4>', 'options' => array(0 => 'Use Default', 1 => 'Use SMTP Server'), 'default' => $isSmtp, 'onClick' => 'Showdiv(this.value)'));
            } else {
                echo $this->Form->input('is_smtp', array('type' => 'radio', 'legend' => __('Email Server Setting'), 'options' => array(0 => 'Use Default', 1 => 'Use SMTP Server'), 'onClick' => 'Showdiv(this.value)'));
            }
            ?>

            <div class="installations form" id="smtp" style="display: none">
                <div class="text-info">Set this values if you are working on localhost that does not have an smtp server(email server) installed.</div>
                <?php echo $this->Form->create(array('role' => 'form', 'class' => 'form')); ?>
                <?php if ($transport != null) { ?>
                    <div class="row">
                        <div class="col-md-6"><?php echo $this->Form->input('smtp_host', array('value' => $transport['host'], 'style' => 'width:100%', 'label' => __('Email Host Name'), 'required' => 'required', 'class'=>'form-control')); ?></div>
                        <div class="col-md-6"><?php echo $this->Form->input('port', array('value' => $transport['port'], 'style' => 'width:100%', 'label' => __('Email Port'), 'required' => 'required', 'class'=>'form-control')); ?></div>
                        <div class="col-md-6 hidden"><?php echo $this->Form->input('is_smtp', array('default' => 1, 'id' => 'smtpfield')); ?></div>
                    </div>

                    <div class="row">
                        <div class="col-md-6"><?php echo $this->Form->input('smtp_user', array('value' => $transport['username'], 'style' => 'width:100%', 'label' => __('Email Id'), 'required' => 'required', 'type' => 'email', 'class'=>'form-control')); ?></div>
                        <div class="col-md-6"><?php echo $this->Form->input('smtp_password', array('value' => $transport['password'], 'type' => 'password', 'style' => 'width:100%', 'label' => __('Password'), 'required' => 'required', 'class'=>'form-control')); ?></div>
                    </div>

                <?php } else { ?>

                    <div class="row">
                        <div class="col-md-6"><?php echo $this->Form->input('smtp_host', array('placeholder' => 'ssl://smtp.gmail.com (if using SSL) or smtp.gmail.com', 'style' => 'width:100%', 'label' => __('Email Host Name'), 'required' => 'required', 'class'=>'form-control')); ?></div>
                        <div class="col-md-6"><?php echo $this->Form->input('port', array('placeholder' => '465 (if using SSL) or 587', 'style' => 'width:100%', 'label' => __('Email Port'), 'required' => 'required', 'class'=>'form-control')); ?></div>
                        <div class="col-md-6 hidden"><?php echo $this->Form->input('is_smtp', array('default' => 1, 'id' => 'smtpfield', 'class'=>'form-control')); ?></div>
                    </div>

                    <div class="row">
                        <div class="col-md-6"><?php echo $this->Form->input('smtp_user', array('style' => 'width:100%', 'label' => __('Email Id'), 'required' => 'required', 'type' => 'email', 'class'=>'form-control')); ?></div>
                        <div class="col-md-6"><?php echo $this->Form->input('smtp_password', array('type' => 'password', 'style' => 'width:100%', 'label' => __('Password'), 'required' => 'required', 'class'=>'form-control')); ?></div>
                    </div>

                <?php } ?>
                <?php echo $this->Form->submit(__('Submit'), array('div' => false, 'class' => 'btn btn-primary btn-success', 'id' => 'submit_id')); ?>
                <?php echo $this->Html->image('indicator.gif', array('id' => 'submit-indicator')); ?>
                
                <?php echo $this->Form->end(); ?>
                <?php echo $this->Js->writeBuffer(); ?>
            </div>

            

        </div>        
    </div>
</div>
<script>$.ajaxSetup({beforeSend: function () {
    $("#submit-indicator").show();
}, complete: function () {
    $("#submit-indicator").hide();
}});</script>
