<div id="users_ajax">
    <?php echo $this->Session->flash(); ?>
    <?php echo $this->Html->script(array('jquery.validate.min', 'jquery-form.min')); ?>
    <?php echo $this->fetch('script'); ?>

    <script>
        $.validator.setDefaults({
            ignore: null,
            errorPlacement: function(error, element) {

                if ($(element).attr('name') == 'data[User][employee_id]')
                    $(element).next().after(error);
                else if ($(element).attr('name') == 'data[User][branch_id]') {
                    $(element).next().after(error);
                } else if ($(element).attr('name') == 'data[User][department_id]') {
                    $(element).next().after(error);
                } else if ($(element).attr('name') == 'data[User][language_id]') {
                    $(element).next().after(error);
                } else {
                    $(element).after(error);
                }
            },
        });
        $().ready(function() {
            jQuery.validator.addMethod("greaterThanZero", function(value, element) {
                return (value != -1);
            }, "Please select the value");

            $('#UserApproveForm').validate(
            {
                rules: {
                    "data[User][office_email]": {
                        required: true,
                        email: true
                    },
                    "data[User][password]": {
                        required: true,
                        passwordPolicy: true,
                    },
                    "data[User][employee_id]": {
                        greaterThanZero: true,
                    },
                    "data[User][branch_id]": {
                        greaterThanZero: true,
                    },
                    "data[User][department_id]": {
                        greaterThanZero: true,
                    },
                    "data[User][language_id]": {
                        greaterThanZero: true,
                    },
                }

            });
            $("#submit-indicator").hide();
            $("#submit_id").click(function(){
              if($('#UserApproveForm').valid()){
                  $("#submit_id").prop("disabled",true);
                  $("#submit-indicator").show();
                  $("#UserApproveForm").submit();
              }
          });
            $('#UserEmployeeId').change(function() {
                if ($(this).val() != -1 && $(this).next().next('label').hasClass("error")) {
                    $(this).next().next('label').remove();
                }
            });
            $('#UserBranchId').change(function() {
                if ($(this).val() != -1 && $(this).next().next('label').hasClass("error")) {
                    $(this).next().next('label').remove();
                }
            });
            $('#UserLanguageId').change(function() {
                if ($(this).val() != -1 && $(this).next().next('label').hasClass("error")) {
                    $(this).next().next('label').remove();
                }
            });
            $('#UserDepartmentId').change(function() {
                if ($(this).val() != -1 && $(this).next().next('label').hasClass("error")) {
                    $(this).next().next('label').remove();
                }
            });        

            if ($("#UserIsMr").is(':checked')) {
                $("#UserIsViewAll").prop('checked', true);
                $("#UserIsViewAll").attr("disabled", true);
                $("#UserIsApprovar").prop('checked', true);
                $("#UserIsApprovar").attr("disabled", true);
            }

            $('#UserIsMr').change(function() {
                userPrivilegesCheck()
            });
            function userPrivilegesCheck(){
                if ($("#UserIsMr").is(':checked')) {
                    $("#UserIsViewAll").prop('checked', true);
                    $("#UserIsViewAll").attr("disabled", true);
                    $("#UserIsApprovar").prop('checked', true);
                    $("#UserIsApprovar").attr("disabled", true);

                } else {
                    $("#UserIsViewAll").prop('checked', false);
                    $("#UserIsApprovar").prop('checked', false);
                    $("#UserIsApprovar").removeAttr("disabled");
                    $("#UserIsViewAll").removeAttr("disabled");
                }
            }
        });
    </script>

    <div class="nav panel panel-default">
        <div class="users form col-md-12">
            <h4><?php echo $this->element('breadcrumbs') . __('Approve User'); ?>
            <?php echo $this->Html->link(__('List'), array('action' => 'index'), array('id' => 'list', 'class' => 'label btn-info')); ?>
            <?php echo $this->Html->link(__('Download PDF'), array('action' => 'view',$this->request->params['pass'][0].'.pdf'), array('id' => 'pdf', 'class' => 'label btn-info')); ?>

        </h4>
        <?php echo $this->Form->create('User', array('role' => 'form', 'class' => 'form')); ?>
        <?php echo $this->Form->input('id'); ?>

        <div class="row">
            <fieldset><legend><h5><?php echo __('Login ID: ') . $this->request->data['User']['username']; ?></h5></legend>
                <div class="col-md-6"><?php echo $this->Form->input('employee_id', array('style' => 'width:100%', 'class' => 'form-control', 'label' => __('Employee'), 'options' => $PublishedEmployeeList)); ?></div>
                <div class="col-md-6"><?php echo $this->Form->input('language_id', array('style' => 'width:100%', 'label' => __('Language'))); ?></div>
            </fieldset>
        </div>
        <div class="row"><br />
            <fieldset><legend><h5><?php echo __('Choose Other Details'); ?></h5></legend>
                <?php echo $this->Form->input('department_id', array('style' => 'width:100%', 'div' => array('class' => 'col-md-4'), 'label' => __('Department'), 'options' => $PublishedDepartmentList)); ?>
                <?php echo $this->Form->input('branch_id', array('style' => 'width:100%', 'div' => array('class' => 'col-md-4'), 'label' => __('Branch'), 'options' => $PublishedBranchList)); ?>
                <?php echo $this->Form->input('division_id', array('style' => 'width:100%', 'div' => array('class' => 'col-md-4'), 'label' => __('Division'))); ?>
            </fieldset>
        </div>
        <div class="row"><br />
            <fieldset><legend><h5><?php echo __('Choose User\'s Privileges'); ?></h5></legend>
                <div class="col-md-12"><?php echo $this->Form->input('is_mr', array('label' => __('New MR User'))); ?></div>
                <div class="col-md-12"><?php echo $this->Form->input('is_mt', array('label' => __('New user as management team member'))); ?></div>
                <div class="col-md-12"><?php echo $this->Form->input('is_view_all', array('label' => __('User who can see other user\'s records'))); ?></div>
                <div class="col-md-12"><?php echo $this->Form->input('is_approver', array('label' => __('User who can approve data added by other users'))); ?></div>
            </fieldset>
        </div>
        <div class="row"><br />
            <fieldset><legend><h5><?php echo __('Benchmark:'); ?></h5></legend>
                <div class="col-md-12">
                    <strong>Note</strong>
                    <br />
                    <p>Benchmarking helps you to track your day-to-day activities linked with each user, department and branch.
                        <br />System prepares its's reports based on the benchmarks you have added for each user & department.
                    </p>
                </div>

                <script>
                    $(function() {
                        $("#slider").slider({
                            range: "min",
                            min: 1,
                            max: 500,
                            value: <?php echo $this->data['User']['benchmark']; ?>,
                            slide: function(event, ui) {
                                $("#vals-slider").html(ui.value);
                                $("#UserBenchmark").val(ui.value);

                            }
                        });

                    });
                </script>

                <div class="col-md-12">
                    <div class="row">
                        <h6 class="col-md-10 no-padding"><?php echo __("Estimated Data Entry Per Day From This User"); ?></h6>
                        <h5><div id="vals-slider" class="col-md-2 pull-right"><?php echo $this->data['User']['benchmark']; ?></div></h5>
                    </div>
                </div>
                <?php echo $this->Form->hidden('benchmark', array('value' => $this->data['User']['benchmark'])); ?>
                <div class="col-md-1"></div><div class="col-md-10" id="slider"></div><div class="col-md-1"></div>
            </fieldset>
        </div>

        <div class="row"><br />
            <fieldset><legend><h5><?php echo __('Copy permission from available user'); ?></h5></legend>
                <div class="col-md-12">
                    <strong>Note:</strong>
                    <br />
                    <ul>
                        <li>Each user created has permissions to access the system with specified limitations.</li>
                        <li>You will need to exclusively provide access to each user based on his/her role.</li>
                        <li>If you have already a user to whom you have provided access, and if you are creating a user with identical access you can select that user from the drop-down and the new user will automatically have those access.</li>
                        <li>You can later either assign more access or limit the new user by manually changing the access.</li>
                        <li>You can manually add user access from Users/View page by clicking <span class="label btn-danger">Manage Access Control</span>.</li>
                        <li><strong>MR users are excluded from following list as MRs have complete access to application.</strong></li>
                    </ul>
                </div>

                <?php echo $this->Form->input('copy_acl_from', array('style' => 'width:100%','label' => __('Copy ACL form User'), 'options' => $aclUsers, 'div' => array('class' => 'col-md-6'))); ?>
                <?php echo $this->Form->input('copy_acl_from_acl', array('style' => 'width:100%', 'label' => __('Select From Group'), 'options' => $userAccessControls, 'div' => array('class' => 'col-md-6'))); ?>
            </fieldset>
        </div>

        <?php
        if ($showApprovals && $showApprovals['show_panel'] == true) {
            echo $this->element('approval_form');
        } else {
            echo $this->Form->input('publish', array('label' => __('Publish')));
        }
        ?>
        <?php echo $this->Form->hidden('History.pre_post_values', array('value'=>json_encode($this->data)));
        echo $this->Form->submit(__('Submit'), array('div' => false, 'class' => 'btn btn-primary btn-success','id'=>'submit_id'));?>
        <?php echo $this->Html->image('indicator.gif', array('id' => 'submit-indicator')); ?>
        <?php echo $this->Form->end(); ?>
        <?php echo $this->Js->writeBuffer(); ?>
    </div>    
</div>
<?php $this->Js->get('#list'); ?>
<?php echo $this->Js->event('click', $this->Js->request(array('action' => 'index', 'ajax'), array('async' => true, 'update' => '#users_ajax'))); ?>
<?php echo $this->Js->writeBuffer(); ?>
</div>
