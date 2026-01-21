<div  id="main">
    <style>
        .fa{font-size: 14px}
    </style>
    <?php echo $this->Session->flash(); ?>
    <?php echo $this->element('nav-header-lists',array('postData'=>array('pluralHumanName'=>'Users','modelClass'=>'User','options'=>array(),'pluralVar'=>'users'))); ?>
    <div class="users ">
        <div class="table-responsive" style="min-height: 450px;">
            <?php echo $this->Form->create(array('class' => 'no-padding no-margin no-background')); ?>
            <table cellpadding="0" cellspacing="0" class="table table-responsive table-hover index">
                <tr>
                    <th><?php echo $this->Paginator->sort('employee_id', __('Employee')); ?>/ <?php echo $this->Paginator->sort('username', __('Username')); ?></th>
                    <th><?php echo $this->Paginator->sort('branch_id', __('Branch')); ?>/ <?php echo $this->Paginator->sort('department_id', __('Department')); ?></th>                    
                    <th><?php echo $this->Paginator->sort('last_login', __('Last Login')); ?></th>
                    <?php if($this->Session->read('User.is_mr') == true){ ?>
                        <th><?php echo $this->Paginator->sort('is_mr', __('Admin')); ?></th>
                        <th><?php echo $this->Paginator->sort('is_view_all', __('View')); ?></th>
                        <th><?php echo $this->Paginator->sort('is_creator', __('Creator')); ?></th>                    
                        <th><?php echo $this->Paginator->sort('is_approver', __('Approver')); ?></th>
                        <th><?php echo $this->Paginator->sort('is_publisher', __('Publisher')); ?></th>                        
                        <th>Block/Release</th>
                        <th><?php echo __('Copy Access From');?></th>
                    <?php } ?>                                        
                    <th><?php echo $this->Paginator->sort('publish', __('Publish')); ?></th>
                    <?php if($this->Session->read('User.is_mr') == true){ ?>
                        <th width="120">Actions</th>
                    <?php } ?>
                </tr>
                <?php if ($users) {
                    $x = 0;
                    foreach ($users as $user):?>                
                        <tr class="on_page_src" onclick="addrec('<?php echo $user['User']['id'];?>')" id="<?php echo $user['User']['id'];?>_tr">
                            <td><?php echo $user['User']['name']; ?>&nbsp;<br /><?php echo $user['User']['username']; ?>&nbsp;</td>
                            <td>
                                <?php echo $this->Html->link($user['Branch']['name'], array('controller' => 'branches', 'action' => 'view', $user['Branch']['id'])); ?><br />
                                <?php echo $this->Html->link($user['Department']['name'], array('controller' => 'departments', 'action' => 'view', $user['Department']['id'])); ?>
                            </td>
                            <td><small><?php echo $user['User']['last_login']; ?>&nbsp;</small></td>
                            <?php if($this->Session->read('User.is_mr') == true){ ?>
                            <td>
                                <?php 
                                if($user['User']['is_mr']){
                                    $str = base64_encode($user['User']['id'].',is_mr,0');
                                    echo $this->Html->link('<i class="fa fa-check"></i>','javascript:void(0)', array(
                                        'class'=>'btn btn-sm text-success',
                                        'onClick'=>'reset_access(\''.$user['User']['id'].'\', \''.$str.'\',this.id,\'is_mr\', 0)',
                                        'id'=>$user['User']['id'].'_is_mr',
                                        'escape'=>false
                                    ));
                                }else{
                                    $str = base64_encode($user['User']['id'].',is_mr,1');
                                    echo $this->Html->link('<i class="fa fa-remove"></i>','#', array(
                                        'class'=>'btn btn-sm text-danger',
                                        'onClick'=>'reset_access(\''.$user['User']['id'].'\', \''.$str.'\',this.id,\'is_mr\', 1)',
                                        'id'=>$user['User']['id'].'_is_mr',
                                        'escape'=>false
                                    ));
                                }
                            ?>&nbsp;
                        </td>                        
                        <td>
                            <?php
                            if($user['User']['is_view_all']){
                                $str = base64_encode($user['User']['id'].',is_view_all,0');
                                echo $this->Html->link('<i class="fa fa-check"></i>','javascript:void(0)', array(
                                    'class'=>'btn btn-sm text-success',
                                    'onClick'=>'reset_access(\''.$user['User']['id'].'\', \''.$str.'\',this.id,\'is_view_all\', 0)',
                                    'id'=>$user['User']['id'].'_is_view_all',
                                    'escape'=>false
                                ));
                            }else{
                                $str = base64_encode($user['User']['id'].',is_view_all,1');
                                echo $this->Html->link('<i class="fa fa-remove"></i>','#', array(
                                    'class'=>'btn btn-sm text-danger',
                                    'onClick'=>'reset_access(\''.$user['User']['id'].'\', \''.$str.'\',this.id,\'is_view_all\', 1)',
                                    'id'=>$user['User']['id'].'_is_view_all',
                                    'escape'=>false
                                ));
                            }
                            ?>&nbsp;
                        </td>
                        <td>
                            <?php
                            if($user['User']['is_creator']){
                                $str = base64_encode($user['User']['id'].',is_creator,0');
                                echo $this->Html->link('<i class="fa fa-check"></i>','javascript:void(0)', array(
                                    'class'=>'btn btn-sm text-success',
                                    'onClick'=>'reset_access(\''.$user['User']['id'].'\', \''.$str.'\',this.id,\'is_creator\', 0)',
                                    'id'=>$user['User']['id'].'is_creator',
                                    'escape'=>false
                                ));
                            }else{
                                $str = base64_encode($user['User']['id'].',is_creator,1');
                                echo $this->Html->link('<i class="fa fa-remove"></i>','#', array(
                                    'class'=>'btn btn-sm text-danger',
                                    'onClick'=>'reset_access(\''.$user['User']['id'].'\', \''.$str.'\',this.id,\'is_creator\', 1)',
                                    'id'=>$user['User']['id'].'is_creator',
                                    'escape'=>false
                                ));
                            }
                            ?>&nbsp;
                        </td>
                        <td>
                            <?php 
                                if($user['User']['is_approver']){
                                    $str = base64_encode($user['User']['id'].',is_approver,0');
                                    echo $this->Html->link('<i class="fa fa-check"></i>','javascript:void(0)', array(
                                        'class'=>'btn btn-sm text-success',
                                        'onClick'=>'reset_access(\''.$user['User']['id'].'\', \''.$str.'\',this.id,\'is_approver\', 0)',
                                        'id'=>$user['User']['id'].'_is_approver',
                                        'escape'=>false
                                    ));
                                }else{
                                    $str = base64_encode($user['User']['id'].',is_approver,1');
                                    echo $this->Html->link('<i class="fa fa-remove"></i>','#', array(
                                        'class'=>'btn btn-sm text-danger',
                                        'onClick'=>'reset_access(\''.$user['User']['id'].'\', \''.$str.'\',this.id,\'is_approver\', 1)',
                                        'id'=>$user['User']['id'].'_is_approver',
                                        'escape'=>false
                                    ));
                                }
                            ?>&nbsp;
                        </td>
                        <td>
                            <?php 
                            if($user['User']['is_publisher']){
                                $str = base64_encode($user['User']['id'].',is_publisher,0');
                                echo $this->Html->link('<i class="fa fa-check"></i>','javascript:void(0)', array(
                                    'class'=>'btn btn-sm text-success',
                                    'onClick'=>'reset_access(\''.$user['User']['id'].'\', \''.$str.'\',this.id,\'is_publisher\', 0)',
                                    'id'=>$user['User']['id'].'is_publisher',
                                    'escape'=>false
                                ));
                            }else{
                                $str = base64_encode($user['User']['id'].',is_publisher,1');
                                echo $this->Html->link('<i class="fa fa-remove"></i>','#', array(
                                    'class'=>'btn btn-sm text-danger',
                                    'onClick'=>'reset_access(\''.$user['User']['id'].'\', \''.$str.'\',this.id,\'is_publisher\', 1)',
                                    'id'=>$user['User']['id'].'is_publisher',
                                    'escape'=>false
                                ));
                            }
                            ?>&nbsp;
                        </td>
                        <td>
                            <?php
                            if($user['User']['status'] == 1){
                                $str = base64_encode($user['User']['id'].',status,0');
                                echo $this->Html->link('<i class="fa fa-check"></i>','javascript:void(0)', array(
                                    'class'=>'text-success',
                                    'onClick'=>'reset_access(\''.$user['User']['id'].'\', \''.$str.'\',this.id,\'status\', 0)',
                                    'id'=>$user['User']['id'].'_status',
                                    'escape'=>false
                                ));
                            }else{
                                $str = base64_encode($user['User']['id'].',status,1');
                                echo $this->Html->link('<i class="fa fa-ban"></i>','#', array(
                                    'class'=>'text-danger',
                                    'onClick'=>'reset_access(\''.$user['User']['id'].'\', \''.$str.'\',this.id,\'status\', 1)',
                                    'id'=>$user['User']['id'].'_status',
                                    'escape'=>false
                                ));
                            }
                        ?>&nbsp;
                        </td> 
                        <td>                            
                            <?php echo $this->Form->input('copy_acl_from',array('options'=>$PublishedUserList, 'default'=>$user['User']['copy_acl_from'], 'id'=>false,'label'=>false,'onChange'=>'copyaccess(this.value,\''.$user['User']['id'].'\')'));?>
                        </td>                        
                    <?php } ?>                    
                    <td width="60">
                        <?php if ($user['User']['publish'] == 1) { ?>
                            <span class="fa fa-check"></span>
                        <?php } else { ?>
                            <span class="fa fa-close"></span>
                        <?php } ?>&nbsp;
                    </td>
                    <?php if($this->Session->read('User.is_mr') == true){ ?>         
                        <td class=" actions" style="width:120px">
                            <?php echo $this->element('actions', array('created' => $user['User']['created_by'], 'postVal' => $user['User']['id'], 'softDelete' => $user['User']['soft_delete'],'is_mr'=>$user['User']['is_mr'])); ?>                        
                        </td>
                    <?php } ?>
                </tr>
            <?php
                $x++;
                endforeach;
            } else {?>
                <tr><td colspan="10"><?php echo __('No results found'); ?></td></tr>
            <?php } ?>            
        </table>
        <?php echo $this->Form->end(); ?>
    </div>
    <p>
        <?php
        echo $this->Paginator->options(array(
            'update' => '#main',
            'evalScripts' => true,
            'before' => $this->Js->get('#busy-indicator')->effect('fadeIn', array('buffer' => false)),
            'complete' => $this->Js->get('#busy-indicator')->effect('fadeOut', array('buffer' => false)),
        ));

        echo $this->Paginator->counter(array(
            'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
        ));
    ?></p>
    <ul class="pagination">
        <?php
        echo "<li class='previous'>" . $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled')) . "</li>";
        echo "<li>" . $this->Paginator->numbers(array('separator' => '')) . "</li>";
        echo "<li class='next'>" . $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled')) . "</li>";
        ?>
    </ul>
</div>
</div>
<script>
    function reset_access(userid,str, id, field, t){        
        $.ajax({
         url: "<?php echo Router::url('/', true); ?>users/reset_access",
         type: "POST",
         dataType: "json",
         contentType: "application/json; charset=utf-8",
         data: JSON.stringify({str:str}),
         beforeSend: function( xhr ) {
            $("#"+id+" i").removeClass('fa-check').removeClass('fa-remove').addClass('fa-refresh fa-spin');
        },                    
        success: function (result) {

            if(field == 'is_mr'){
                if(result == 1){
                    $("#"+id+" i").removeClass('fa-refresh fa-spin').addClass('fa-check');
                    $("#"+id).removeClass('text-danger').addClass('text-success');

                    $("#"+userid+"_is_view_all").removeClass('text-danger').addClass('text-success');
                    $("#"+userid+"_is_approver").removeClass('text-danger').addClass('text-success');

                    $("#"+userid+"_is_view_all i").removeClass('fa-check').removeClass('fa-remove').addClass('fa-check');
                    $("#"+userid+"_is_approver i").removeClass('fa-check').removeClass('fa-remove').addClass('fa-check');

                }else{
                    $("#"+id+" i").removeClass('fa-refresh fa-spin').addClass('fa-remove');
                    $("#"+id).removeClass('text-success').addClass('text-danger');

                    $("#"+userid+"_is_view_all").removeClass('text-success').addClass('text-danger');
                    $("#"+userid+"_is_approver").removeClass('text-success').addClass('text-danger');

                    $("#"+userid+"_is_view_all i").removeClass('fa-check').removeClass('fa-remove').addClass('fa-remove');
                    $("#"+userid+"_is_approver i").removeClass('fa-check').removeClass('fa-remove').addClass('fa-remove');
                }
            }else if(field == 'status'){
                if(result == 1){
                    
                    $("#"+id+" i").removeClass('fa-refresh fa-spin fa-ban').addClass(' fa-check');
                    
                    $("#"+id).removeClass('text-danger').addClass('text-success');
                }else{
                    
                    $("#"+id+" i").removeClass('fa-refresh fa-spin').addClass('fa-ban');
                    $("#"+id).removeClass('text-success').addClass('text-danger');
                }

            }else{
                if(result == 1){
                    $("#"+id+" i").removeClass('fa-refresh fa-spin').addClass('fa-check');
                    $("#"+id).removeClass('text-danger').addClass('text-success');
                }else{
                    $("#"+id+" i").removeClass('fa-refresh fa-spin').addClass('fa-remove');
                    $("#"+id).removeClass('text-success').addClass('text-danger');
                }
            }
        },
        error: function (err) {
            
        }
    }); 

    }

    function copyaccess(from,to){
        if(from == to){
            alert('Same user.')
            return false;
        }else{
            $.ajax({
                url: "<?php echo Router::url('/', true); ?><?php echo $this->request->params['controller'] ?>/copy_access_from/"+from+"/"+to,
                success: function(data, result) {
                    console.log('done');
                },
            }); 
        }
    }
    
    $.ajaxSetup({beforeSend: function () {$("#busy-indicator").show();},complete: function () {$("#busy-indicator").hide();}});
</script>
