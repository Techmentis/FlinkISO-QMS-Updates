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
<?php $user = $this->request->data;?>
<?php if($qcDocuments && $this->Session->read('User.is_mr')){ ?>
    <div class="row">
        <div class="col-md-12">
            <h4>Document Access</h4>
            <p>You can enable/ disable document access to this user by clicking on the check or remove icon.</p>
        </div>
        <div class="col-md-12">
            <table class="table table-responsive table-bordered">
                <tr>
                    <th>Document</th>
                    <th>View</th>
                    <th>Edit</th>
                </tr>
                <?php foreach($qcDocuments as $qcDocument){ ?>
                    <tr>
                        <td><?php echo $qcDocument['QcDocument']['name'];?></td>
                        <td>
                            <?php
                            $view = false;
                            $class = 'link';
                            $branches = json_decode($qcDocument['QcDocument']['branches'],true);
                            if($branches && in_array($user['User']['branch_id'], $branches)){
                                $view = true;
                                $class = 'disabled';
                            }

                            $departments = json_decode($qcDocument['QcDocument']['departments'],true);
                            if($departments && in_array($user['User']['department_id'], $departments)){
                                $view = true;
                                $class = 'disabled';
                            }
                            
                            $designations = json_decode($qcDocument['QcDocument']['designations'],true);
                            if($designations && in_array($user['User']['designation_id'], $designations)){
                                $view = true;
                                $class = 'disabled';
                            }

                            $users = json_decode($qcDocument['QcDocument']['user_id'],true);
                            if($users && in_array($user['User']['id'], $users)){
                                $view = true;
                            }
                            
                            if($view == true){
                                if($class != 'disabled'){
                                    echo "<i class='fa fa-check text-success link' onclick=updateaccess('".$qcDocument['QcDocument']['id']."','".$user['User']['id']."','view',this.id,0); id='qc".$qcDocument['QcDocument']['id']."-removeview'></i>";    
                                }else{
                                    echo "<i class='fa fa-check text-gray'></i>";
                                }                                        
                            }else{
                                echo "<i class='fa fa-remove text-danger link' onclick=updateaccess('".$qcDocument['QcDocument']['id']."','".$user['User']['id']."','view',this.id,1); id='qc".$qcDocument['QcDocument']['id']."-addview'></i>";
                            }
                            ?>
                        </td>
                        <td>
                            <?php
                            $edit = false;                                    
                            $editors = json_decode($qcDocument['QcDocument']['editors'],true);
                            if(in_array($user['User']['id'], $editors)){
                                $edit = true;
                            }
                            if($edit == true){
                                echo "<i class='fa fa-check text-success link' onclick=updateaccess('".$qcDocument['QcDocument']['id']."','".$user['User']['id']."','edit',this.id,0); id='qc".$qcDocument['QcDocument']['id']."-removeedit'></i>";
                            }else{
                                echo "<i class='fa fa-remove text-danger link' onclick=updateaccess('".$qcDocument['QcDocument']['id']."','".$user['User']['id']."','edit',this.id,1); id='qc".$qcDocument['QcDocument']['id']."-addedit'></i>";
                            }
                            ?>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>
<?php } ?>
<?php if($customTables && $this->Session->read('User.is_mr')){ ?>
    <div class="row">
        <div class="col-md-12">
            <h4>Custom HTML Forms Access</h4>
            <p>You can enable/ disable custom HTML Form's access to this user by clicking on the check or remove icon.
            <br />Make sure that the relevent document access is given to a user before allowing the Custom HTML Form access based on that Document.
            <br />If you allow any action for the HTML table, system will automatically add View permission for a linked document if the permission is missng.</p>
        </div>
        <div class="col-md-12">
            <table class="table table-responsive table-bordered">
                <tr>
                    <th>Table</th>
                    <th>Creator</th>
                    <th>Viewer</th>
                    <th>Editor</th>
                    <th>Approver</th>
                </tr>
                <?php foreach($customTables as $customTable){ ?>
                    <tr>
                        <td><?php echo $customTable['CustomTable']['name'];?></td>
                        <td>
                            <?php
                            $create = false;
                            if($customTable['CustomTable']['creators']){
                                $creators = json_decode($customTable['CustomTable']['creators'],true);
                                if(in_array($user['User']['id'], $creators)){
                                    $create = true;
                                }
                                if($create == true){
                                    echo "<i class='fa fa-check text-success link' id='".$customTable['CustomTable']['id']."-removecreate' onclick=updatetableaccess('".$customTable['CustomTable']['id']."','".$user['User']['id']."','create',this.id,0);></i>";
                                }else{
                                    echo "<i class='fa fa-remove text-danger link' id='".$customTable['CustomTable']['id']."-addcreate' onclick=updatetableaccess('".$customTable['CustomTable']['id']."','".$user['User']['id']."','create',this.id,1);></i>";
                                }
                            }
                            else{
                                    echo "<i class='fa fa-remove text-danger link' id='".$customTable['CustomTable']['id']."-addcreate' onclick=updatetableaccess('".$customTable['CustomTable']['id']."','".$user['User']['id']."','create',this.id,1);></i>";
                                }
                            ?>
                        </td>
                        <td>
                            <?php
                            $view = false;
                            if($customTable['CustomTable']['viewers']){
                                $viewers = json_decode($customTable['CustomTable']['viewers'],true);
                                if(in_array($user['User']['id'], $viewers)){
                                    $view = true;
                                }
                                if($view == true){
                                    echo "<i class='fa fa-check text-success link' id='".$customTable['CustomTable']['id']."-removeview' onclick=updatetableaccess('".$customTable['CustomTable']['id']."','".$user['User']['id']."','view',this.id,0);></i>";
                                }else{
                                    echo "<i class='fa fa-remove text-danger link' id='".$customTable['CustomTable']['id']."-addview' onclick=updatetableaccess('".$customTable['CustomTable']['id']."','".$user['User']['id']."','view',this.id,1);></i>";
                                }
                            }else{
                                    echo "<i class='fa fa-remove text-danger link' id='".$customTable['CustomTable']['id']."-addview' onclick=updatetableaccess('".$customTable['CustomTable']['id']."','".$user['User']['id']."','view',this.id,1);></i>";
                                }
                            ?>
                        </td>
                        <td>
                            <?php
                            $edit = false;
                            if($customTable['CustomTable']['editors']){
                                $editors = json_decode($customTable['CustomTable']['editors'],true);
                                if(in_array($user['User']['id'], $editors)){
                                    $edit = true;
                                }
                                if($edit == true){
                                    echo "<i class='fa fa-check text-success link' id='".$customTable['CustomTable']['id']."-removeedit' onclick=updatetableaccess('".$customTable['CustomTable']['id']."','".$user['User']['id']."','edit',this.id,0);></i>";
                                }else{
                                    echo "<i class='fa fa-remove text-danger link' id='".$customTable['CustomTable']['id']."-addedit' onclick=updatetableaccess('".$customTable['CustomTable']['id']."','".$user['User']['id']."','edit',this.id,1);></i>";
                                }
                            }
                            else{
                                    echo "<i class='fa fa-remove text-danger link' id='".$customTable['CustomTable']['id']."-addedit' onclick=updatetableaccess('".$customTable['CustomTable']['id']."','".$user['User']['id']."','edit',this.id,1);></i>";
                                }
                            ?>
                        </td>
                        <td>
                            <?php
                            $approver = false;
                            if($customTable['CustomTable']['approvers']){
                                $approvers = json_decode($customTable['CustomTable']['approvers'],true);
                                if(in_array($user['User']['id'], $approvers)){
                                    $approver = true;
                                }
                                if($approver == true){
                                    echo "<i class='fa fa-check text-success link' id='".$customTable['CustomTable']['id']."-removeapprove' onclick=updatetableaccess('".$customTable['CustomTable']['id']."','".$user['User']['id']."','approve',this.id,0);></i>";
                                }else{
                                    echo "<i class='fa fa-remove text-danger link' id='".$customTable['CustomTable']['id']."-addapprove' onclick=updatetableaccess('".$customTable['CustomTable']['id']."','".$user['User']['id']."','approve',this.id,1);></i>";
                                }
                            }else{
                                    echo "<i class='fa fa-remove text-danger link' id='".$customTable['CustomTable']['id']."-addapprove' onclick=updatetableaccess('".$customTable['CustomTable']['id']."','".$user['User']['id']."','approve',this.id,1);></i>";
                                }
                            ?>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>
<?php } ?>
        <script>
            function updateaccess(qc_document_id,user_id,action,thisid,typ){
                $.ajax({
                 url: "<?php echo Router::url('/', true); ?>qc_documents/update_access",
                 type: "POST",
                 dataType: "json",
                 contentType: "application/json; charset=utf-8",
                 data: JSON.stringify({qc_document_id:qc_document_id,user_id:user_id,action:action,typ:typ}),
                 beforeSend: function( xhr ) {
                    $("#"+thisid).removeClass('fa-check').removeClass('fa-remove').addClass('fa-refresh fa-spin');
                },
                success: function (result) {
                    $("#"+thisid).removeClass('fa-refresh fa-spin');
                    if(typ == 0){                    
                        $("#"+thisid).removeClass('text-success fa-check ').addClass('fa-remove text-danger link');
                        $("#"+thisid).attr('onclick','updateaccess(\''+qc_document_id+'\',\''+user_id+'\',\''+action+'\',this.id,1)');
                    }
                    if(typ == 1){                        
                        $("#"+thisid).removeClass('text-danger ').addClass('fa-check text-success link');
                        $("#"+thisid).attr('onclick','updateaccess(\''+qc_document_id+'\',\''+user_id+'\',\''+action+'\',this.id,0)');
                    }
                    
                },
                error: function (err) {
                }
            });
            }

            function updatetableaccess(custom_table_id,user_id,action,thisid,typ){
                $.ajax({
                 url: "<?php echo Router::url('/', true); ?>custom_tables/update_access",
                 type: "POST",
                 dataType: "json",
                 contentType: "application/json; charset=utf-8",
                 data: JSON.stringify({custom_table_id:custom_table_id,user_id:user_id,action:action,typ:typ}),
                 beforeSend: function( xhr ) {
                    $("#"+thisid).removeClass('fa-check').removeClass('fa-remove').addClass('fa-refresh fa-spin');
                },
                success: function (result) {
                    $("#"+thisid).removeClass('fa-refresh fa-spin');
                    if(typ == 0){
                        $("#"+thisid).removeClass('text-success fa-check ').addClass('fa-remove text-danger link');
                        $("#"+thisid).attr('onclick','updatetableaccess(\''+custom_table_id+'\',\''+user_id+'\',\''+action+'\',this.id,1)');
                    }
                    if(typ == 1){
                        $("#"+thisid).removeClass('text-danger ').addClass('fa-check text-success link');
                        $("#"+thisid).attr('onclick','updatetableaccess(\''+custom_table_id+'\',\''+user_id+'\',\''+action+'\',this.id,0)');
                    }
                },
                error: function (err) {
                }
            });
        }

        $.ajaxSetup({beforeSend: function() {$("#busy-indicator").show();}, complete: function() {$("#busy-indicator").hide();}});
    </script>
</div>