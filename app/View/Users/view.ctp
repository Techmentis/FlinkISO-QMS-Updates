<div id="main">
    <style type="text/css">
        .link:hover{cursor: pointer;}
    </style>
    <?php echo $this->element('nav-header-lists',array('postData'=>array('pluralHumanName'=>'Users','modelClass'=>'User','options'=>array(),'pluralVar'=>'users'))); ?>
    <?php echo $this->Session->flash(); ?>
    <div class="nav panel panel-default">
        <div class="users form col-md-12">
           
            <table class="table table-responsive">
                <tr><td><?php echo __('Sr. No'); ?></td>
                    <td>
                        <?php echo $user['User']['sr_no']; ?>
                        &nbsp;
                    </td>
                </tr>
                <tr><td><?php echo __('Employee'); ?></td>
                    <td>
                        <?php echo $this->Html->link($user['Employee']['name'], array('controller' => 'employees', 'action' => 'view', $user['Employee']['id'])); ?>
                        &nbsp;
                    </td>
                </tr>
                <tr><td><?php echo __('Username'); ?></td>
                    <td>
                        <?php echo $user['User']['username']; ?>
                        &nbsp;
                    </td>
                </tr>
                <tr><td><?php echo __('Password'); ?></td>
                    <td>
                        <?php echo "************"; ?>
                        &nbsp;
                    </td>
                </tr>
                <tr><td><?php echo __('Admin?'); ?></td>
                    <td>
                        <?php echo $user['User']['is_mr'] ? 'Yes' : 'No'; ?>
                        &nbsp;
                    </td>
                </tr>
                <tr><td><?php echo __('Is View All?'); ?></td>
                    <td>
                        <?php echo $user['User']['is_view_all'] ? 'Yes' : 'No'; ?>
                        &nbsp;
                    </td>
                </tr>
                <tr><td><?php echo __('Is Approver?'); ?></td>
                    <td>
                        <?php echo $user['User']['is_approver'] ? 'Yes' : 'No'; ?>
                        &nbsp;
                    </td>
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
                    <td>
                        <?php echo $this->Html->link($user['Department']['name'], array('controller' => 'departments', 'action' => 'view', $user['Department']['id'])); ?>
                        &nbsp;
                    </td>
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
                        }
                        
                        ?>
                    </td>
                </tr>
                <tr><td><?php echo __('Login Status'); ?></td>
                    <td>
                        <?php echo $user['User']['login_status'] ? 'Online' : 'Offline'; ?>
                        &nbsp;
                    </td>
                </tr>
                <tr><td><?php echo __('Last Login'); ?></td>
                    <td>
                        <?php echo $user['User']['last_login']; ?>
                        &nbsp;
                    </td>
                </tr>
                <tr><td><?php echo __('Last Activity'); ?></td>
                    <td>
                        <?php echo $user['User']['last_activity']; ?>
                        &nbsp;
                    </td>
                </tr>
                <!-- <tr><td><?php echo __('Prepared By'); ?></td>
                    <td>
                        <?php echo h($user['PreparedBy']['name']); ?>
                        &nbsp;
                    </td>
                </tr>
                <tr><td><?php echo __('Approved By'); ?></td>
                    <td>
                        <?php echo h($user['ApprovedBy']['name']); ?>
                        &nbsp;
                    </td>
                </tr> -->
                <tr><td><?php echo __('Publish'); ?></td>
                    <td>
                        <?php if ($user['User']['publish'] == 1) { ?>
                            <span class="fa fa-check"></span>
                        <?php } else { ?>
                            <span class="fa fa-close"></span>
                            <?php } ?>&nbsp;</td>
                            &nbsp;
                        </tr>
                    </table>
                </div>        
            </div>    
        </div>
        <?php if($qcDocuments){ ?>
            <div class="row">
                <div class="col-md-12"><h4>Document Access</h4></div>
                <div class="col-md-12">
                    <table class="table table-responsive table-bordered">
                        <tr>
                            <th>Document</th>
                            <th>View</th>
                            <th>Edit</th>
                        </tr>
                        <?php foreach($qcDocuments as $qcDocument){ ?>
                            <tr>
                                <td><?php echo $qcDocument['QcDocument']['title'];?></td>
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
                                            echo "<i class='fa fa-check text-success link' onclick=updateaccess('".$qcDocument['QcDocument']['id']."','".$user['User']['id']."','removeview',this); id='".$qcDocument['QcDocument']['id']."-removeview'></i>";    
                                        }else{
                                            echo "<i class='fa fa-check text-warning'></i>";
                                        }
                                        
                                    }else{
                                        echo "<i class='fa fa-remove text-danger link' onclick=updateaccess('".$qcDocument['QcDocument']['id']."','".$user['User']['id']."','addview',this); id='".$qcDocument['QcDocument']['id']."-addview'></i>";
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
                                        echo "<i class='fa fa-check text-success link' onclick=updateaccess('".$qcDocument['QcDocument']['id']."','".$user['User']['id']."','removeedit',this);></i>";
                                    }else{
                                        echo "<i class='fa fa-remove text-danger link' onclick=updateaccess('".$qcDocument['QcDocument']['id']."','".$user['User']['id']."','addedit',this);></i>";
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

            function updateaccess(qc_document_id,user_id,action,id){
                $.ajax({
                 url: "<?php echo Router::url('/', true); ?>qc_documents/update_access",
                 type: "POST",
                 dataType: "json",
                 contentType: "application/json; charset=utf-8",
                 data: JSON.stringify({qc_document_id:qc_document_id,user_id:user_id,action:action}),
                 beforeSend: function( xhr ) {                
                    $("#"+id.id).removeClass('fa-check').removeClass('fa-remove').addClass('fa-refresh fa-spin');
                },
                success: function (result) {                
                    $("#"+id.id).removeClass('fa-refresh fa-spin');
                    if(action == 'removeview'){
                        $("#"+id.id).removeClass('text-success').addClass('fa-remove').addClass('text-danger');
                        $("#"+id.id).prop('onclick',null).removeClass('link');
                    }
                    if(action == 'addview'){
                        $("#"+id.id).removeClass('text-danger').addClass('fa-check').addClass('text-success');
                        $("#"+id.id).prop('onclick',null).removeClass('link');
                    }
                },
                error: function (err) {            
                }
            });
            }

            $.ajaxSetup({beforeSend: function() {
                $("#busy-indicator").show();
            }, complete: function() {
                $("#busy-indicator").hide();
            }
        });
    </script>
