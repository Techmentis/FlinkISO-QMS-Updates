<?php
$accessListUserId = !empty($user['User']['id']) ? $user['User']['id'] : null;
$accessListSearchUrl = Router::url(array('controller'=>'custom_tables', 'action'=>'custom_table_list', $accessListUserId), true);
?>
<?php if($customTables && $this->Session->read('User.is_mr')){ ?>
    <?php if (isset($hide) && $hide == false) { ?>
    <div class="row">
        <div class="col-md-12">
            <h4>Custom HTML Forms Access</h4>
            <p>You can enable/ disable custom HTML Form's access to this user by clicking on the check or remove icon.
            <br />Make sure that the relevent document access is given to a user before allowing the Custom HTML Form access based on that Document.
            <br />If you allow any action for the HTML table, system will automatically add View permission for a linked document if the permission is missng.</p>
        </div>
        
        <div class="col-md-12">
                <?php echo $this->Form->input('src',array('class'=>'form-control','id'=>'quick_ct_src_button', 'autocomplete'=>'off',  'label'=>false,'placeholder'=>'Quick search with sorting.','style'=>'margin-top: -12px'));?>
        </div>
    </div>
    <?php } ?>
<div id="tbllist">
    <div class="row">
        <div class="col-md-12">
            <table class="table table-responsive table-bordered">
                <tr>
                    <th>Table</th>
                    <th>Add</th>
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
                                if(is_array($creators)){
                                    if(in_array($user['User']['id'], $creators)){
                                        $create = true;
                                    }
                                    if($create == true){
                                        echo "<i class='fa fa-check text-success link' id='".$customTable['CustomTable']['id']."-removecreate' onclick=updatetableaccess('".$customTable['CustomTable']['id']."','".$user['User']['id']."','create',this.id,0);></i>";
                                    }else{
                                        echo "<i class='fa fa-remove text-danger link' id='".$customTable['CustomTable']['id']."-addcreate' onclick=updatetableaccess('".$customTable['CustomTable']['id']."','".$user['User']['id']."','create',this.id,1);></i>";
                                    }
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
                                if(is_array($viewers)){
                                    if(in_array($user['User']['id'], $viewers)){
                                        $view = true;
                                    }
                                    if($view == true){
                                        echo "<i class='fa fa-check text-success link' id='".$customTable['CustomTable']['id']."-removeview' onclick=updatetableaccess('".$customTable['CustomTable']['id']."','".$user['User']['id']."','view',this.id,0);></i>";
                                    }else{
                                        echo "<i class='fa fa-remove text-danger link' id='".$customTable['CustomTable']['id']."-addview' onclick=updatetableaccess('".$customTable['CustomTable']['id']."','".$user['User']['id']."','view',this.id,1);></i>";
                                    }
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
                                if(is_array($editors)){
                                    if(in_array($user['User']['id'], $editors)){
                                        $edit = true;
                                    }
                                    if($edit == true){
                                        echo "<i class='fa fa-check text-success link' id='".$customTable['CustomTable']['id']."-removeedit' onclick=updatetableaccess('".$customTable['CustomTable']['id']."','".$user['User']['id']."','edit',this.id,0);></i>";
                                    }else{
                                        echo "<i class='fa fa-remove text-danger link' id='".$customTable['CustomTable']['id']."-addedit' onclick=updatetableaccess('".$customTable['CustomTable']['id']."','".$user['User']['id']."','edit',this.id,1);></i>";
                                    }
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
                                if(is_array($approvers)){
                                    if(in_array($user['User']['id'], $approvers)){
                                        $approver = true;
                                    }
                                    if($approver == true){
                                        echo "<i class='fa fa-check text-success link' id='".$customTable['CustomTable']['id']."-removeapprove' onclick=updatetableaccess('".$customTable['CustomTable']['id']."','".$user['User']['id']."','approve',this.id,0);></i>";
                                    }else{
                                        echo "<i class='fa fa-remove text-danger link' id='".$customTable['CustomTable']['id']."-addapprove' onclick=updatetableaccess('".$customTable['CustomTable']['id']."','".$user['User']['id']."','approve',this.id,1);></i>";
                                    }
                                }                                
                            }else{
                                    echo "<i class='fa fa-remove text-danger link' id='".$customTable['CustomTable']['id']."-addapprove' onclick=updatetableaccess('".$customTable['CustomTable']['id']."','".$user['User']['id']."','approve',this.id,1);></i>";
                                }
                            ?>
                        </td>
                    </tr>
                <?php } ?>
            </table>
            <p>
                <?php
                echo $this->Paginator->options(array(
                    'update' => '#tbllist',
                    'evalScripts' => true,
                    'before' => $this->Js->get('#busy-indicator')->effect('fadeIn', array('buffer' => false)),
                    'complete' => $this->Js->get('#busy-indicator')->effect('fadeOut', array('buffer' => false)),
                ));

                echo $this->Paginator->counter(array(
                    'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
                ));
            ?>          </p>
            <ul class="pagination">
                <?php
                echo "<li class='previous'>".$this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'))."</li>";
                echo "<li>".$this->Paginator->numbers(array('separator' => ''))."</li>";
                echo "<li class='next'>".$this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'))."</li>";
                ?>
            </ul>
        </div>
    </div>
    <script>    
    function setTableAccessIcon($icon,custom_table_id,user_id,action,enabled){
        var nextType = enabled ? 0 : 1;
        $icon
            .removeClass('fa-refresh fa-spin fa-check fa-remove text-success text-danger')
            .addClass(enabled ? 'fa-check text-success link' : 'fa-remove text-danger link')
            .attr('onclick','updatetableaccess(\''+custom_table_id+'\',\''+user_id+'\',\''+action+'\',this.id,'+nextType+')');
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
            var $row = $("#"+thisid).closest('tr');
            if(result && result.success && result.access){
                setTableAccessIcon($row.find('td').eq(1).find('i'),custom_table_id,user_id,'create',result.access.create);
                setTableAccessIcon($row.find('td').eq(2).find('i'),custom_table_id,user_id,'view',result.access.view);
                setTableAccessIcon($row.find('td').eq(3).find('i'),custom_table_id,user_id,'edit',result.access.edit);
                setTableAccessIcon($row.find('td').eq(4).find('i'),custom_table_id,user_id,'approve',result.access.approve);
            }else{
                $("#"+thisid).removeClass('fa-refresh fa-spin').addClass(typ == 1 ? 'fa-remove text-danger' : 'fa-check text-success');
            }
        },
        error: function (err) {
            $("#"+thisid).removeClass('fa-refresh fa-spin').addClass(typ == 1 ? 'fa-remove text-danger' : 'fa-check text-success');
        }
    });
}

</script>
    <?php echo $this->Js->writeBuffer();?>
</div>
<?php } ?>
<script>
    var searchTimer;
    $('#quick_ct_src_button').on('input', function () {
    var search = $(this).val();
    clearTimeout(searchTimer);
    searchTimer = setTimeout(function () {
        if (search.length < 2) {
            $('#tbllist').html('');
            return;
        }

        $.ajax({
            url: '<?php echo $accessListSearchUrl; ?>',
            type: 'POST',
            data: {
                search: search
            },
            beforeSend: function () {
                $('#tbllist').html('Searching...');
            },
            success: function (response) {
                $('#tbllist').html(response);
            },
            error: function () {
                $('#tbllist').html('Error while searching.');
            }
        });

    }, 300); // wait 300ms after user stops typing
});
</script>
