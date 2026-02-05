<style>.link{cursor: pointer;font-size:110%;}</style>
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
                } else {
                    $(element).after(error);
                }
            },
        });
        $().ready(function() {
            $('select').chosen();
            jQuery.validator.addMethod("greaterThanZero", function(value, element) {
                return (value != -1);
            }, "Please select the value");

            $('#UserEditForm').validate(
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
                    }
                }
            });
            $("#submit-indicator").hide();
            $("#submit_id").click(function(){
                if($('#UserEditForm').valid()){
                    $("#submit_id").prop("disabled",true);
                    $("#submit-indicator").show();
                    $("#UserEditForm").submit();
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
    <?php echo $this->element('nav-header-lists', array('postData' => array('pluralHumanName' => 'Users', 'modelClass' => 'User', 'options' => array(), 'pluralVar' => 'users'))); ?>
    <?php echo $this->Form->create('User', array('role' => 'form', 'class' => 'form')); ?>
    <?php echo $this->Form->input('id'); ?>
    <div class="nav panel panel-default">
        <div class="users form col-md-12">
            <div class="row">
                <div class="col-md-12"><legend><h5><?php echo __('Login ID: ') . $this->request->data['User']['username']; ?></h5></legend></div>
                <div class="col-md-3"><?php echo $this->Form->input('employee_id', array('style' => 'width:100%', 'class' => 'form-control', 'label' => __('Employee'), 'options' => $PublishedEmployeeList)); ?></div>
                <?php echo $this->Form->input('department_id', array('style' => 'width:100%', 'div' => array('class' => 'col-md-3'), 'label' => __('Department'), 'options' => $PublishedDepartmentList)); ?>
                <?php echo $this->Form->input('branch_id', array('style' => 'width:100%', 'div' => array('class' => 'col-md-3'), 'label' => __('Branch'), 'options' => $PublishedBranchList)); ?>
                <div class="col-md-3"><br /><?php echo $this->Form->input('is_view_all', array('label' => __('This user who can see other user\'s records.'))); ?></div>
            </div>
            <div class="row hide">
                <!-- <div class="col-md-12"><legend><h5><?php echo __('Choose User\'s Privileges'); ?></h5></legend></div> -->
                <div class="col-md-4"><?php echo $this->Form->input('is_mr', array('label' => __('Admin'))); ?></div>
                <div class="col-md-4"><?php echo $this->Form->input('is_approver', array('label' => __('Approver'))); ?></div>                
            </div>
            <div class="row">
                <div class="col-md-12">
                <?php
                echo $this->Form->input('assigned_branches', array('name'=>'data[User][assigned_branches][]', 'type'=>'select', 'label' => 'Select Branches', 'id' => 'AssignedBranches', 'multiple', 'options' => $PublishedBranchList, 'selected' => json_decode($this->request->data['User']['assigned_branches'],true)));
                ?>            
                    <p>If the user is non-admin user, and if "User who can see other user's records" clicked, then user can only see records added by users from his/ her own branch. You can select addtional branched fron "Select Branches" field to give that user access to other braches.</p>
                </div>
            </div>
            <?php echo $this->Form->hidden('benchmark', array('value' => $this->data['User']['benchmark'])); ?>
            <?php
            echo $this->Form->input('publish');
            echo $this->Form->hidden('History.pre_post_values', array('value' => json_encode($this->data)));
            echo $this->Form->submit(__('Submit'), array('div' => false, 'class' => 'btn btn-primary btn-success', 'id' => 'submit_id'));
            echo $this->Html->image('indicator.gif', array('id' => 'submit-indicator'));
            echo $this->Form->end();
            echo $this->Js->writeBuffer(); 
            ?>
    </div>        
</div>
<div id="loaddoclist"></div>
<div id="loadtablelist"></div>
<script>
    $("#loaddoclist").load("<?php echo Router::url('/', true); ?>qc_documents/document_list");
    $("#loadtablelist").load("<?php echo Router::url('/', true); ?>custom_tables/custom_table_list");
$.ajaxSetup({beforeSend: function() {$("#busy-indicator").show();}, complete: function() {$("#busy-indicator").hide();}});
</script>

</div>
