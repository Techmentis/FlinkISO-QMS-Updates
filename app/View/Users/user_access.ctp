<style type="text/css">
    form div[class^="col-md-"]{
        min-height: 1px !important;
    }

    form div[class^="col-md-"] .error{
        font-size: 12px;
    }

    form div[class^="col-md-"] label{
        margin-top: 0px;
        margin-bottom: 2px;
    }
    .checkbox label { font-weight: normal;}
</style>
<?php
echo $this->Html->css(array('bootstrap-chosen.min'));
echo $this->fetch('css');
echo $this->Html->script(array('chosen.min',));
echo $this->fetch('script');
?>
<style type="text/css">

</style>
<div><?php echo $this->element('nav-header-lists', array('postData' => array('pluralHumanName' => 'Users', 'modelClass' => 'User', 'options' => array(), 'pluralVar' => 'users'))); ?>
<div class="panel">
    <div class="panel-heading">
        <h4><small><?php echo $this->Html->link('Users', array('action' => 'index')); ?> / </small> Set User Access for : <?php echo $this->Html->link($this->request->data['User']['username'],array(
            'action'=>'view',
            $this->request->data['User']['id'],
            'qc_document_id'=>$qc_document_id,'
            custom_table_id'=>$custom_table_id)); 
        ?></h4>
    </div>

    <div class="panel-body">
        <div class="panel-group" id="accordion">
            <?php echo $this->Form->create('User', array('role' => 'form', 'class' => 'form')); ?>
            <div class="row">                
                <?php
                $abs = json_decode($this->request->data['User']['assigned_branches'], true);
                $this->request->data['User']['assigned_branches'] = $assigned_branches_user;
                echo $this->Form->hidden('assigned_branches1', array('name' => 'User.assigned_branches1[]', 'value' => $this->request->data['User']['assigned_branches']));
                ?>
            </div>
            <?php foreach ($forms as $value => $data):?>
                <div class="box box-default file-box">
                    <div class="box-header">
                        <div class="panel-title">
                            <h6 class="user-access">
                                <a data-toggle="collapse" data-parent="#accordion" href="#<?php echo str_replace(' ', '', ucfirst($value)); ?>" style="float:none">
                                    <?php echo $value ?>
                                </a>
                                <?php
                                echo "<span style='float:left;margin-top:-20px;margin-right:10px'>" . $this->Form->input('select_all_' . str_replace(' ', '', ucfirst($value)), array('type' => 'checkbox', 'default' => 0, 'style' => 'float:left', 'label' => FALSE)) . "</span>";?>
                            </h6>
                        </div>
                    </div>
                </div>
                <div id="<?php echo str_replace(' ', '', ucfirst($value)); ?>" class="panel panel-collapse collapse">
                    <div class="box-body">
                        <script>
                            $('#UserSelectAll<?php echo str_replace(' ', '', ucfirst($value)); ?>').on('click', function() {

                                $("#<?php echo str_replace(' ', '', ucfirst($value)); ?>").find(':checkbox').prop('checked', this.checked);
                                if (this.checked) {
                                    $("#<?php echo str_replace(' ', '', ucfirst($value)); ?>").find(':hidden').attr('value', 1);
                                } else {
                                    $("#<?php echo str_replace(' ', '', ucfirst($value)); ?>").find(':hidden').attr('value', 0);
                                }
                            });
                        </script>
                        <?php
                        foreach ($forms[$value] as $fkey => $fvalue):
                            if ($fvalue) {?>
                                <div class="row <?php echo $fkey; ?>">
                                    <div class="col-md-12" >
                                        <table>
                                            <tr><td width="25" class="chk-bk">
                                                <h4><?php
                                                echo $this->Form->input('select_all_' . str_replace(' ', '', ucfirst($value)) . Inflector::Humanize($fkey), array('type' => 'checkbox', 'default' => 0, 'class' => $fkey . '_view', 'style' => 'float:left;', 'label' => false));
                                                echo "</h4></td><td><h4><strong>" . Inflector::Humanize($fkey) . "</strong></h4></td>";?>
                                            </tr>
                                        </table>
                                    </div>
                                    <script>
                                        $('#UserSelectAll<?php echo str_replace(' ', '', ucfirst($value)); ?><?php echo str_replace(' ', '', Inflector::Humanize($fkey)); ?>').on('click', function() {
                                            $("div.<?php echo $fkey; ?>").find(':checkbox').prop('checked', this.checked);
                                            if (this.checked) {
                                                $("div.<?php echo $fkey; ?>").find(':hidden').attr('value', 1);
                                            } else {
                                                $("div.<?php echo $fkey; ?>").find(':hidden').attr('value', 0);
                                            }
                                        });
                                    </script>
                                    <?php
                                    foreach ($fvalue['actions'] as $act):
                                        if ($act == 'index') {
                                            echo "<div class='col-md-2'>";
                                            echo $this->Form->input('ACL.user_access.' . $fkey . '.index', array('type' => 'checkbox', 'default' => 0, 'class' => $fkey . '_view'));
                                            echo $this->Form->hidden('ACL.user_access.' . $fkey . '.advanced_search', array('type' => 'checkbox', 'default' => 0, 'class' => $fkey . '_view'));
                                            
                                            echo "</div>";
                                            ?>
                                            <script>
                                                if($('#ACLUserAccess<?php echo str_replace(' ', '', Inflector::Humanize($fkey)); ?>View').is(':checked')){
                                                    $('#UserSelectAll<?php echo str_replace(' ', '', ucfirst($value)); ?><?php echo str_replace(' ', '', Inflector::Humanize($fkey)); ?>').prop('checked',true);
                                                    $('#UserSelectAll<?php echo str_replace(' ', '', ucfirst($value)); ?>').prop('checked',true);

                                                }
                                                $('#ACLUserAccess<?php echo str_replace(' ', '', Inflector::Humanize($fkey)); ?>View').on('click', function() {
                                                    if(($('#ACLUserAccess<?php echo str_replace(' ', '', Inflector::Humanize($fkey)); ?>Add').prop('checked')) || ($('#ACLUserAccess<?php echo str_replace(' ', '', Inflector::Humanize($fkey)); ?>Edit').prop('checked'))){
                                                        return false;
                                                    }else{
                                                        if (this.checked) {
                                                            $(".<?php echo $fkey; ?>_view").attr('value', 1);
                                                        } else {
                                                            $(".<?php echo $fkey; ?>_view").attr('value', 0);
                                                        }
                                                    }
                                                });
                                            </script>

                                            <?php
                                        } elseif ($act == 'add' || $act == 'plan_add_ajax') {
                                            echo "<div class='col-md-2'>";
                                            echo $this->Form->input('ACL.user_access.' . $fkey . '.add', array('type' => 'checkbox', 'default' => 0));
                                            if (strtolower($fkey) == 'meetings') {
                                                echo $this->Form->hidden('ACL.user_access.' . $fkey . '.add_ajax', array('type' => 'checkbox', 'default' => 0, 'class' => $fkey . '_add'));
                                                echo $this->Form->hidden('ACL.user_access.' . $fkey . '.add_meeting_topics', array('type' => 'checkbox', 'default' => 0, 'class' => $fkey . '_add'));
                                                echo $this->Form->hidden('ACL.user_access.' . $fkey . '.after_meeting', array('type' => 'checkbox', 'default' => 0, 'class' => $fkey . '_add'));
                                                echo $this->Form->hidden('ACL.user_access.' . $fkey . '.meeting_detail_lists', array('type' => 'checkbox', 'default' => 0, 'class' => $fkey . '_add'));
                                                echo $this->Form->hidden('ACL.user_access.' . $fkey . '.before_meeting_view', array('type' => 'checkbox', 'default' => 0, 'class' => $fkey . '_add'));
                                            } elseif (strtolower($fkey) == 'internal_audit_plans') {
                                                echo $this->Form->hidden('ACL.user_access.' . $fkey . '.plan_add_ajax', array('type' => 'checkbox', 'default' => 0, 'class' => $fkey . '_add'));
                                            } else {
                                                echo $this->Form->hidden('ACL.user_access.' . $fkey . '.add_ajax', array('type' => 'checkbox', 'default' => 0, 'class' => $fkey . '_add'));
                                            }
                                            echo $this->Form->hidden('ACL.user_access.' . $fkey . '.lists', array('type' => 'checkbox', 'default' => 0, 'class' => $fkey . '_add'));
                                            echo "</div>";
                                            ?>

                                            <script>
                                                if($('#ACLUserAccess<?php echo str_replace(' ', '', Inflector::Humanize($fkey)); ?>Add').is(':checked')){
                                                    $('#UserSelectAll<?php echo str_replace(' ', '', ucfirst($value)); ?><?php echo str_replace(' ', '', Inflector::Humanize($fkey)); ?>').prop('checked',true);
                                                    $('#UserSelectAll<?php echo str_replace(' ', '', ucfirst($value)); ?>').prop('checked',true);
                                                }
                                                $('#ACLUserAccess<?php echo str_replace(' ', '', Inflector::Humanize($fkey)); ?>Add').on('click', function() {
                                                    if (this.checked) {
                                                        $(".<?php echo $fkey; ?>_add").attr('value', 1);
                                                        $('#ACLUserAccess<?php echo str_replace(' ', '', Inflector::Humanize($fkey)); ?>Edit').prop('checked',true);
                                                        $('#ACLUserAccess<?php echo str_replace(' ', '', Inflector::Humanize($fkey)); ?>Edit').prop('readonly',true);
                                                        $('#ACLUserAccess<?php echo str_replace(' ', '', Inflector::Humanize($fkey)); ?>View').prop('checked',true);
                                                        $('#ACLUserAccess<?php echo str_replace(' ', '', Inflector::Humanize($fkey)); ?>View').prop('readonly',true);                                                                   $(".<?php echo $fkey; ?>_edit").attr('value', 1);
                                                        $(".<?php echo $fkey; ?>_view").attr('value', 1);
                                                    } else {
                                                        $(".<?php echo $fkey; ?>_add").attr('value', 0);
                                                        $('#ACLUserAccess<?php echo str_replace(' ', '', Inflector::Humanize($fkey)); ?>Edit').prop('checked',false);
                                                        $('#ACLUserAccess<?php echo str_replace(' ', '', Inflector::Humanize($fkey)); ?>Edit').prop('readonly',false);
                                                        $('#ACLUserAccess<?php echo str_replace(' ', '', Inflector::Humanize($fkey)); ?>View').prop('checked',false);
                                                        $('#ACLUserAccess<?php echo str_replace(' ', '', Inflector::Humanize($fkey)); ?>View').prop('readonly',false);
                                                        $(".<?php echo $fkey; ?>_edit").attr('value', 0);
                                                        $(".<?php echo $fkey; ?>_view").attr('value', 0);
                                                    }
                                                });
                                            </script>

                                            <?php
                                        } elseif ($act == 'task') {
                                            echo "<div class='col-md-2'>";
                                            echo $this->Form->input('ACL.user_access.' . $fkey . '.task', array('type' => 'checkbox', 'default' => 0));
                                            if (strtolower($fkey) == 'users') {
                                                echo $this->Form->hidden('ACL.user_access.' . $fkey . '.task', array('type' => 'checkbox', 'default' => 0, 'class' => $fkey . '_task'));
                                            }
                                            echo "</div>";
                                            ?>
                                            <script>
                                                if($('#ACLUserAccess<?php echo str_replace(' ', '', Inflector::Humanize($fkey)); ?>Task').is(':checked')){
                                                    $('#UserSelectAll<?php echo str_replace(' ', '', ucfirst($value)); ?><?php echo str_replace(' ', '', Inflector::Humanize($fkey)); ?>').prop('checked',true);
                                                    $('#UserSelectAll<?php echo str_replace(' ', '', ucfirst($value)); ?>').prop('checked',true);
                                                }
                                                $('#ACLUserAccess<?php echo str_replace(' ', '', Inflector::Humanize($fkey)); ?>Task').on('click', function() {
                                                    if (this.checked) {
                                                        $(".<?php echo $fkey; ?>_task").attr('value', 1);
                                                    } else {
                                                        $(".<?php echo $fkey; ?>_task").attr('value', 0);
                                                    }
                                                });
                                            </script>

                                            <?php
                                        } elseif ($act == 'edit') {
                                            echo "<div class='col-md-2'>";
                                            echo $this->Form->input('ACL.user_access.' . $fkey . '.edit', array('type' => 'checkbox', 'default' => 0));
                                            
                                            echo "</div>";
                                            ?>
                                            <script>
                                                if($('#ACLUserAccess<?php echo str_replace(' ', '', Inflector::Humanize($fkey)); ?>Edit').is(':checked')){
                                                    $('#UserSelectAll<?php echo str_replace(' ', '', ucfirst($value)); ?><?php echo str_replace(' ', '', Inflector::Humanize($fkey)); ?>').prop('checked',true);
                                                    $('#UserSelectAll<?php echo str_replace(' ', '', ucfirst($value)); ?>').prop('checked',true);
                                                }
                                                $('#ACLUserAccess<?php echo str_replace(' ', '', Inflector::Humanize($fkey)); ?>Edit').on('click', function() {
                                                 if($('#ACLUserAccess<?php echo str_replace(' ', '', Inflector::Humanize($fkey)); ?>Add').prop('checked')){
                                                    return false;
                                                }else{
                                                    if (this.checked) {
                                                        $(".<?php echo $fkey; ?>_edit").attr('value', 1);
                                                        $('#ACLUserAccess<?php echo str_replace(' ', '', Inflector::Humanize($fkey)); ?>View').prop('checked',true);
                                                        $('#ACLUserAccess<?php echo str_replace(' ', '', Inflector::Humanize($fkey)); ?>View').prop('readonly',true);                                                                   $(".<?php echo $fkey; ?>_view").attr('value', 1);
                                                    } else {
                                                        $(".<?php echo $fkey; ?>_edit").attr('value', 0);
                                                        $('#ACLUserAccess<?php echo str_replace(' ', '', Inflector::Humanize($fkey)); ?>View').prop('checked',false);
                                                        $('#ACLUserAccess<?php echo str_replace(' ', '', Inflector::Humanize($fkey)); ?>View').prop('readonly',false);
                                                        $(".<?php echo $fkey; ?>_view").attr('value', 0);
                                                    }
                                                }
                                            });
                                        </script>

                                        <?php
                                    } elseif ($act == 'delete') {
                                        echo "<div class='col-md-2'>";
                                        echo $this->Form->input('ACL.user_access.' . $fkey . '.delete', array('type' => 'checkbox', 'default' => 0, 'class' => $fkey . '_delete'));
                                        echo $this->Form->hidden('ACL.user_access.' . $fkey . '.purge', array('type' => 'checkbox', 'default' => 0, 'class' => $fkey . '_delete'));
                                        echo $this->Form->hidden('ACL.user_access.' . $fkey . '.delete_all', array('type' => 'checkbox', 'default' => 0, 'class' => $fkey . '_delete'));
                                        echo $this->Form->hidden('ACL.user_access.' . $fkey . '.purge_all', array('type' => 'checkbox', 'default' => 0, 'class' => $fkey . '_delete'));
                                        echo $this->Form->hidden('ACL.user_access.' . $fkey . '.restore', array('type' => 'checkbox', 'default' => 0, 'class' => $fkey . '_delete'));
                                        echo $this->Form->hidden('ACL.user_access.' . $fkey . '.restore_all', array('type' => 'checkbox', 'default' => 0, 'class' => $fkey . '_delete'));
                                        echo "</div>";
                                        ?>
                                        <script>
                                            if($('#ACLUserAccess<?php echo str_replace(' ', '', Inflector::Humanize($fkey)); ?>Delete').is(':checked')){
                                                $('#UserSelectAll<?php echo str_replace(' ', '', ucfirst($value)); ?><?php echo str_replace(' ', '', Inflector::Humanize($fkey)); ?>').prop('checked',true);
                                                $('#UserSelectAll<?php echo str_replace(' ', '', ucfirst($value)); ?>').prop('checked',true);
                                            }
                                            $('#ACLUserAccess<?php echo str_replace(' ', '', Inflector::Humanize($fkey)); ?>Delete').on('click', function() {
                                                if (this.checked) {
                                                    $(".<?php echo $fkey; ?>_delete").attr('value', 1);
                                                } else {
                                                    $(".<?php echo $fkey; ?>_delete").attr('value', 0);
                                                }
                                            });
                                        </script>

                                        <?php
                                    } elseif ($act == 'report') {
                                        echo "<div class='col-md-2'>";
                                        echo $this->Form->input('ACL.user_access.' . $fkey . '.report', array('type' => 'checkbox', 'default' => 0, 'class' => $fkey . '_report'));
                                        if (strtolower($fkey) == 'internal_audits') {
                                            echo $this->Form->hidden('ACL.user_access.' . $fkey . '.audit_report', array('type' => 'checkbox', 'default' => 0, 'class' => $fkey . '_report'));
                                        } elseif (strtolower($fkey) == 'internal_audit_plans') {
                                            echo $this->Form->hidden('ACL.user_access.' . $fkey . '.plan_report', array('type' => 'checkbox', 'default' => 0, 'class' => $fkey . '_report'));
                                        }
                                        echo "</div>";
                                    } else {
                                        echo "<div class='col-md-2'>";
                                        echo $this->Form->input('ACL.user_access.' . $fkey . '.' . $act, array('type' => 'checkbox', 'default' => 0));
                                        echo "</div>";
                                    } ?>
                                    
                                    <?php
                                endforeach; ?>
                            </div>
                            <hr />
                            <?php
                        }
                    endforeach; ?>
                </div>
            </div>
            <?php
        endforeach; ?>
        <br />
        <?php
        echo $this->Form->hidden('History.pre_post_values', array('value' => json_encode($this->data)));
        echo $this->Form->submit(__('Submit'), array('div' => false, 'class' => 'btn btn-primary btn-success', 'id' => 'submit_id'));
        ?>
        <?php echo $this->Html->image('indicator.gif', array('id' => 'submit-indicator')); ?>
        <?php echo $this->Form->end(); ?>
        <?php echo $this->Js->writeBuffer(); ?>
    </div>
</div>
</div>
</div>
<script>

    $().ready(function() {
        $('#AssignedBranches').chosen();   
    });
    $("#submit-indicator").hide();
    $("#submit_id").click(function(){
        
       $("#submit_id").prop("disabled",true);
       $("#submit-indicator").show();
       $("#UserUserAccessForm").submit();
       
   });
</script>

