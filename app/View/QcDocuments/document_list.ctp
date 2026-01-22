<?php if($qcDocuments && $this->Session->read('User.is_mr')){ ?>
    <div  id="doclist">
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
                                $branches = $departments = $designations = $users = array();
                                
                                $branches = json_decode($qcDocument['QcDocument']['branches'],true);
                                if(is_array($branches)){
                                    if(in_array($user['User']['branch_id'], $branches)){
                                        $view = true;
                                        $class = 'disabled';
                                    }
                                }                            

                                $departments = json_decode($qcDocument['QcDocument']['departments'],true);
                                if(is_array($departments)){
                                        if(in_array($user['User']['department_id'], $departments)){
                                        $view = true;
                                        $class = 'disabled';
                                    }
                                }                            
                                
                                $designations = json_decode($qcDocument['QcDocument']['designations'],true);
                                if(is_array($designations)){
                                    if(in_array($user['User']['designation_id'], $designations)){
                                        $view = true;
                                        $class = 'disabled';
                                    }
                                }

                                $users = json_decode($qcDocument['QcDocument']['user_id'],true);
                                if(is_array($users)){
                                    if(in_array($user['User']['id'], $users)){
                                        $view = true;
                                    }    
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
                                if(is_array($editors)){
                                    if(in_array($user['User']['id'], $editors)){
                                        $edit = true;
                                    }
                                    if($edit == true){
                                        echo "<i class='fa fa-check text-success link' onclick=updateaccess('".$qcDocument['QcDocument']['id']."','".$user['User']['id']."','edit',this.id,0); id='qc".$qcDocument['QcDocument']['id']."-removeedit'></i>";
                                    }else{
                                        echo "<i class='fa fa-remove text-danger link' onclick=updateaccess('".$qcDocument['QcDocument']['id']."','".$user['User']['id']."','edit',this.id,1); id='qc".$qcDocument['QcDocument']['id']."-addedit'></i>";
                                    }
                                }else{
                                    echo "<i class='fa fa-remove text-danger link' onclick=updateaccess('".$qcDocument['QcDocument']['id']."','".$user['User']['id']."','edit',this.id,1); id='qc".$qcDocument['QcDocument']['id']."-addedit'></i>";
                                }
                                ?>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
                    <p>
                    <?php
                    echo $this->Paginator->options(array(
                        'update' => '#doclist',
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

        </script>
    <?php echo $this->Js->writeBuffer();?>
</div>
<?php } ?>
