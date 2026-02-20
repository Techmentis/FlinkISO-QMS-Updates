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
<div id="loaddoclist"></div>
<div id="loadtablelist"></div>
<script>
    $("#loaddoclist").load("<?php echo Router::url('/', true); ?>qc_documents/document_list/<?php echo $user['User']['id']?>");
    $("#loadtablelist").load("<?php echo Router::url('/', true); ?>custom_tables/custom_table_list/<?php echo $user['User']['id']?>");
$.ajaxSetup({beforeSend: function() {$("#busy-indicator").show();}, complete: function() {$("#busy-indicator").hide();}});
</script>