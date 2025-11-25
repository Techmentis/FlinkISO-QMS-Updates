<div id="main">
    <style type="text/css">
        .link:hover{cursor: pointer;}
    </style>
    <?php echo $this->element('nav-header-lists',array('postData'=>array('pluralHumanName'=>'Users','modelClass'=>'User','options'=>array(),'pluralVar'=>'users'))); ?>
    <?php echo $this->Session->flash(); ?>
    <div class="nav panel panel-default">
        <div class="users form col-md-12">
            <h3><?php echo $this->Html->link($user['Employee']['name'], array('controller' => 'employees', 'action' => 'view', $user['Employee']['id'])); ?>
                        &nbsp; | <?php echo $user['User']['username']; ?></h3>
            <table class="table table-responsive">
                <tr><td><?php echo __('Admin User?'); ?></td>
                    <td><?php echo $user['User']['is_mr'] ? 'Yes' : 'No'; ?>&nbsp;</td>
                </tr>
                <tr><td><?php echo __('Can this user see records created by other users?'); ?></td>
                    <td><?php echo $user['User']['is_view_all'] ? 'Yes' : 'No'; ?>&nbsp;</td>
                </tr>
                <tr><td><?php echo __('Is this user Approver?'); ?></td>
                    <td><?php echo $user['User']['is_approver'] ? 'Yes' : 'No'; ?>&nbsp;</td>
                </tr>
                <tr><td><?php echo __('Status'); ?></td>
                    <td>
                        <?php
                        $status = $user['User']['status'];
                        switch ($status){
                            case 0: echo 'Inactive'; break;
                            case 1: echo 'Active'; break;
                            case 2: echo 'Blocked';break;
                        }
                        ?>
                        &nbsp;
                    </td>
                </tr>
                <tr><td><?php echo __('Department'); ?></td>
                    <td><?php echo $this->Html->link($user['Department']['name'], array('controller' => 'departments', 'action' => 'view', $user['Department']['id'])); ?>&nbsp;</td>
                </tr>
                <tr><td><?php echo __('Branch'); ?></td>
                    <td>
                        <?php echo $this->Html->link($user['Branch']['name'], array('controller' => 'branches', 'action' => 'view', $user['Branch']['id'])); ?>
                        &nbsp;
                        <?php 
                        if(!$user['Branch']['name']){
                            if(!$user['User']['assigned_branches']){
                                echo $branches[$user['Employee']['branch_id']];
                            }    
                        }?>
                    </td>
                </tr>
                <tr><td><?php echo __('Last Login'); ?></td>
                    <td><?php echo $user['User']['last_login']; ?>&nbsp;</td>
                </tr>
                <tr><td><?php echo __('Published?'); ?></td>
                    <td>
                        <?php if ($user['User']['publish'] == 1) { ?>
                            <span class="fa fa-check"></span>
                        <?php } else { ?>
                            <span class="fa fa-close"></span>
                            <?php } ?>&nbsp;
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
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