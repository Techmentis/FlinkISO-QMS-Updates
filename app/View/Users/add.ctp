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
            } else {
                $(element).after(error);
            }
        }
    });

    $().ready(function() {
        $("select").chosen();
        $("#submit-indicator").hide();
        jQuery.validator.addMethod("greaterThanZero", function(value, element) {
            return (value != -1);
        }, "Please select the value");

        $('#UserAddForm').validate({
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
                }
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
        

        $('#UserIsMr').change(function() {
            if ($("#UserIsMr").is(':checked')) {
                $("#UserIsViewAll").prop('checked', true);
                $("#UserIsViewAll").attr('disabled', true);

                $("#UserIsApprovar").prop('checked', true);
                $("#UserIsApprovar").attr('disabled', true);

            } else {
                $("#UserIsViewAll").prop('checked', false);
                $("#UserIsApprovar").prop('checked', false);
                $("#UserIsApprovar").removeAttr('disabled');
                $("#UserIsViewAll").removeAttr('disabled');
            }
        });

        function checkUsername() {
            $('#usernameCheckResult').load('<?php echo Router::url('/', true); ?><?php echo $this->request->params['controller'] ?>/check_username/' + $('#UserUsername').val(), function(response, status, xhr) {
                if (response !== '') {
                    $('#usernameCheckResult').show();
                    $('#UserUsername').val('');
                    $('#UserUsername').addClass('error');
                } else {
                    $('#UserUsername').removeClass('error');
                    $('#usernameCheckResult').hide();
                }
            });

        }
        $('#UserUsername').blur(function() {
            checkUsername();
        });
        checkUsername();
    });
</script>

<div id="users_ajax">
    <?php echo $this->Session->flash(); ?>
    <?php echo $this->element('nav-header-lists',array('postData'=>array('pluralHumanName'=>'Users','modelClass'=>'User','options'=>array(),'pluralVar'=>'users'))); ?>    
    <?php echo $this->Form->create('User', array('role' => 'form', 'class' => 'form')); ?>
    <div class="panel panel-default">
        <div class="panel-body">    
            <div class="nav">
                <div class="users form col-md-12">                
                    <div class="row">
                        <fieldset><legend><h5><?php echo __('Login Details'); ?></h5></legend>
                            <div class="col-md-4"><?php echo $this->Form->input('employee_id', array('class'=>'form-control select','style' => 'width:100%', 'label' => __('Employee'), 
                                'options' => $PublishedEmployeeList, 'default' => $this->request->params['pass'][0]
                                )); ?></div>                    
                                <div class="col-md-4"><?php echo $this->Form->input('username', array('class'=>'form-control', 'label' => __('Username'))); ?></div>
                                <div class="col-md-4"><?php echo $this->Form->input('password', array('class'=>'form-control','label' => __('Password'))); ?></div>
                                <div id="usernameCheckResult"></div>
                            </fieldset>
                        </div>
                        <?php echo $this->element('display_policy', array(), array('plugin' => 'PasswordSettingManager'));?>
                        <div class="row"><br />
                            
                            <?php echo $this->Form->input('department_id', array('style' => 'width:100%', 'div' => array('class' => 'col-md-6'), 'label' => __('Department'), 'options' => $PublishedDepartmentList )); ?>
                            <?php echo $this->Form->input('branch_id', array('style' => 'width:100%', 'div' => array('class' => 'col-md-6'), 'label' => __('Branch'), 'options' => $PublishedBranchList )); ?>
                            <!-- <?php echo $this->Form->input('division_id', array('style' => 'width:100%', 'div' => array('class' => 'col-md-4'), 'label' => __('Division'))); ?> -->
                        </fieldset>
                    </div>

                    <div class="row"><br />
                        <div class="col-md-12"><legend><h5><?php echo __('Choose User\'s Privileges'); ?></h5></legend></div>
                        <div class="col-md-6"><?php echo $this->Form->input('is_mr', array('label' => __('Admin'))); ?></div>
                        <div class="col-md-6"><?php echo $this->Form->input('is_mt', array('label' => __('Can create custom tables'))); ?></div>
                        <div class="col-md-6"><?php echo $this->Form->input('is_approver', array('label' => __('Approver'))); ?></div>  
                        <div class="col-md-6"><?php echo $this->Form->input('is_view_all', array('label' => __('User who can see other user\'s records'))); ?></div>
                    </div>
                    
                    <?php echo $this->Form->hidden('benchmark', array('value' => 0)); ?>
                    <div class="row hide"><br />
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

                    <?php echo $this->Form->input('branchid', array('type' => 'hidden', 'value' => $this->Session->read('User.branch_id'))); ?>
                    <?php echo $this->Form->input('departmentid', array('type' => 'hidden', 'value' => $this->Session->read('User.department_id'))); ?>
                </div>        
            </div>
            <?php                      
            echo $this->Form->input('assigned_branches', array('name'=>'data[User][assigned_branches][]', 'type'=>'select', 'label' => 'Select Branches', 'id' => 'AssignedBranches', 'multiple', 'options' => $PublishedBranchList, 'selected' => json_decode($this->request->data['User']['assigned_branches'],true)));
            ?>            
            <?php echo $this->Form->input('publish');?>
            <?php echo $this->Form->submit(__('Submit'), array('div' => false, 'class' => 'btn btn-primary btn-success', 'update' => '#users_ajax', 'async' => 'false','id'=>'submit_id')); ?>
            <?php echo $this->Html->image('indicator.gif', array('id' => 'submit-indicator')); ?>
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
    }
});
</script>
