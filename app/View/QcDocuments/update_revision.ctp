<?php echo $this->Session->flash();?>   
    <?php echo $this->element('nav-header-lists',array('postData'=>array('pluralHumanName'=>__('Update Qc Document Revision'),'modelClass'=>'QcDocument','options'=>array("sr_no"=>__("Sr No"),"document_number"=>__("Document Number"),"reference_number"=>__("Reference Number"),"issue_number"=>__("Issue Number"),"date_of_next_issue"=>__("Date Of Next Issue"),"date_of_issue"=>__("Date Of Issue"),"effective_from_date"=>__("Effective From Date"),"revision_number"=>__("Revision Number"),"date_of_review"=>__("Date Of Review"),"revision_date"=>__("Revision Date"),"document_type"=>__("Document Type"),"it_categories"=>__("It Categories"),"document_status"=>__("Document Status"),"issued_by"=>__("Issued By"),"archived"=>__("Archived"),"change_history"=>__("Change History"),"cr_status"=>__("Cr Status"),"mark_for_cr_update"=>__("Mark For Cr Update"),"temp_date_of_issue"=>__("Temp Date Of Issue"),"temp_effective_from_date"=>__("Temp Effective From Date"),"linked_formats"=>__("Linked Formats"),"cover_page"=>__("Cover Page"),"page_orientation"=>__("Page Orientation")),'pluralVar'=>'qcDocuments'))); ?>

<h3><?php echo h($qcDocument['QcDocument']['document_number']); ?>-<?php echo h($qcDocument['QcDocument']['title']); ?>-<?php echo h($qcDocument['QcDocument']['revision_number']); ?><br />
                    <small><?php echo $qcDocument['Standard']['name']; ?> - <?php echo h($qcDocument['QcDocument']['document_status']); ?></small></h3>

<div class="box box-info">
        <div class="box-header">
          <h3 class="box-title"><?php echo h(__('Change Revision Process')); ?></h3>

          <div class="box-tools">
            <!-- <a href="/cloud/medrun/custom_tables"><i class="fa fa-database fa-lg"></i></a> -->
          </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
                <ol>
                    <li><?php echo h(__('System Will Archive The Current Document')); ?></li>
                    <li>System will copy Current Document in Archives Folder with revision number as <?php echo $qcDocument['QcDocument']['revision_number']?></li>
                    <li><?php echo h(__('System will link the New Updated Document with Current Document')); ?></li>
                    <li><?php echo h(__('System will mark document status as')); ?> <strong><?php echo h(__('Awaiting Issue')); ?></strong> </li>
                    <li><?php echo h(__('User must then goto edit file and update rest of the details like Prepared By, Approved By, Issued By etc.')); ?></li>
                </ol>
                <p><?php echo h(__('You can update rest of the details from Edit field once the document updation is done.')); ?></p>

            </div>
            <!-- /.box-body -->
          </div>

<div class="row">
    <div class="col-md-12">
        <div class="box box-default">
            <div class="box-header"><h4><?php echo h(__('Current Document')); ?></h4></div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tr>
                            <th><?php echo h(__('Document Number')); ?></th>
                            <td><?php echo $document['QcDocument']['document_number']?></td>
                            <th><?php echo h(__('Revision Number')); ?></th>
                            <td><?php echo $document['QcDocument']['revision_number']?></td>
                            <th><?php echo h(__('Date Of Issue')); ?></th>
                            <td><?php echo $document['QcDocument']['date_of_issue']?></td>
                        </tr>
                        <tr>
                            <th><?php echo h(__('Prepared By')); ?></th>
                            <td><?php echo $document['PreparedBy']['name']?></td>
                            <th><?php echo h(__('Approved By')); ?></th>
                            <td><?php echo $document['ApprovedBy']['name']?></td>
                            <th><?php echo h(__('Issued By')); ?></th>
                            <td><?php echo $document['IssuedBy']['name']?></td>
                        </tr>
                    </table>
                </div>
                <div class="row">
                    
                    <?php 
                            if($qcDocument['QcDocument']['document_status'] == 6){
                                
                            }

                            $key = $qcDocument['QcDocument']['file_key'];
                            $file_type = $qcDocument['QcDocument']['file_type'];
                            $file_name = $qcDocument['QcDocument']['title'];
                            $document_number = $qcDocument['QcDocument']['document_number'];
                            $document_version = $qcDocument['QcDocument']['revision_number'];

                            $file_type = $qcDocument['QcDocument']['file_type'];
                            
                            if($file_type == 'doc' || $file_type == 'docx'){
                                $documentType = 'word';
                            }

                            if($file_type == 'xls' || $file_type == 'xlsx'){
                                $documentType = 'cell';
                            }

                            $mode = 'view';

                            $file_path = $qcDocument['QcDocument']['id'];


                            // $file = $document_number.'-'.$file_name.'-'.$document_version.'.'.$file_type;
                            $file = $document_number.'-'.$file_name.'-'.$document_version;
                            $file = ltrim(rtrim($file));
                            $file = str_replace('-', '_', $file);
                            $file = ltrim(rtrim(strtolower($file)));
                            $file = preg_replace('/[\@\.\;\" "-]+/', '_', $file);
                            $file = preg_replace('/  */', '_', $file);
                            $file = preg_replace('/\\s+/', '_', $file);        
                            $file = preg_replace('/-*-/', '_', $file);
                            $file = preg_replace('/_*_/', '_', $file);

                            $file = $file.'.'.$file_type;
                            
                            if(file_exists(WWW_ROOT . DS . 'files' . DS . $this->Session->read('User.company_id') . DS . 'qc_documents' . DS . $file_path . DS . $file)){
                                echo '<div class="col-md-12">'. $this->element('onlyoffice',array(
                                    'url'=>$url,
                                    'placeholderid'=>0,
                                    'panel_title'=>__('Document Viewer'),
                                    'mode'=>$mode,
                                    'path'=>$file_path,
                                    'file'=>$file,
                                    'filetype'=>$file_type,
                                    'documentType'=>$documentType,
                                    'userid'=>$this->Session->read('User.id'),
                                    'username'=>$this->Session->read('User.username'),
                                    'preparedby'=>$qcDocument['PreparedBy']['name'],
                                    'filekey'=>$key,            
                                    'record_id'=>$qcDocument['QcDocument']['id'],
                                    'company_id'=>$this->Session->read('User.company_id'),
                                    'controller'=>$this->request->controller,
                                    'last_saved'=>$qcDocument['QcDocument']['last_saved'],
                                    'last_modified' => $qcDocument['QcDocument']['modified'],
                                    'version_keys' => $qcDocument['QcDocument']['version_keys'],
                                    'version' => $qcDocument['QcDocument']['version'],
                                    'versions' => $qcDocument['QcDocument']['versions'],
                                    'docid'=> $qcDocument['QcDocument']['id']
                                )) .'</div>';
                            }else{
                                echo __("Something went wrong");
                            } ?>
                        
                </div>

            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="box box-default">
            <div class="box-header"><h4><?php echo h(__('New Updated Document')); ?></h4></div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tr>
                            <th><?php echo h(__('Document Number')); ?></th>
                            <td><?php echo $document['QcDocument']['document_number']?></td>
                            <th><?php echo h(__('Revision Number')); ?></th>
                            <td><?php echo $document['QcDocument']['revision_number'] + 1?></td>
                            <th><?php echo h(__('Date Of Issue')); ?></th>
                            <td><?php echo $document['QcDocument']['date_of_issue']?></td>
                        </tr>
                        <tr>
                            <th><?php echo h(__('Prepared By')); ?></th>
                            <td><?php echo $document['PreparedBy']['name']?></td>
                            <th><?php echo h(__('Approved By')); ?></th>
                            <td><?php echo $document['ApprovedBy']['name']?></td>
                            <th><?php echo h(__('Issued By')); ?></th>
                            <td>--</td>
                        </tr>
                    </table>
                </div>
                <?php if($prompt == false){ ?>
                <div class="row">
                    <div class="col-md-12"></h4>
                        <?php    
                            $key = $fileEdit['File']['file_key'];
                            $file_type = $fileEdit['File']['file_type'];
                            $file_name = $fileEdit['File']['name'];
                            
                            if($file_type == 'doc' || $file_type == 'docx'){
                                $documentType = 'word';
                            }

                            if($file_type == 'xls' || $file_type == 'xlsx'){
                                $documentType = 'cell';
                            }
                            
                            $mode = 'view';
                            $file_path = $fileEdit['File']['id'];
                            $file = $file_name.'.'.$file_type;
                                                        
                            echo $this->element('onlyoffice',array(
                                'url'=>$url,                       
                                'user_id'=>$user_id,               
                                'placeholderid'=>$placeholderid,
                                'panel_title'=>__('Document Viewer'),
                                'mode'=>$mode,                     
                                'path'=>$file_path,                
                                'file'=>$file,
                                'filetype'=>$file_type,
                                'documentType'=>$documentType,
                                'userid'=>$this->Session->read('User.id'),
                                'username'=>$this->Session->read('User.username'),
                                'preparedby'=>$this->Session->read('User.name'),
                                'filekey'=>$key,            
                                'record_id'=>$fileEdit['File']['id'],
                                'company_id'=>$this->Session->read('User.company_id'),
                                'controller'=>'files',
                                'last_saved' => $fileEdit['File']['last_saved'],
                                'last_modified' => $fileEdit['File']['modified'],
                                'version_keys' => $fileEdit['File']['version_keys'],
                                'version' => $fileEdit['File']['version'],
                                'versions' => $fileEdit['File']['versions'],
                                'docid'=> $fileEdit['File']['id']
                            ));
                        ?>
                    </div>
                </div>
                <div class="row">                
                    <div class="col-md-12">
                        <h4><?php echo h(__('Changed Request Details')); ?></h4>
                        <div class="table-responsive">
                            <table class="table">
                                <tr>
                                <?php foreach($fields as $field){ ?>
                                    <th><?php echo Inflector::humanize($field);?></th>
                                <?php }?>
                                <th><?php echo h(__('Prepared By')); ?></th>
                                <th><?php echo h(__('Approved By')); ?></th>
                                <th><?php echo h(__('Action')); ?></th>
                                </tr>
                                <?php               
                                foreach($records as $record){
                                    foreach($fields as $field){
                                        

                                        foreach(json_decode($table_fields,true) as $table_field){
                                            if($table_field['field_name'] == $field){
                                                if($table_field['data_type'] == 'radio'){
                                                    $values = explode(',', $table_field['csvoptions']);
                                                    $value = $values[$record[$model][$field]];                              
                                                }else{
                                                    $value = $record[$model][$field];
                                                }
                                            }
                                        }

                                        echo "<td>".$value."</td>";
                                    }?>
                                    <td><?php echo $record['PreparedBy']['name'];?></td>
                                    <td><?php echo $record['ApprovedBy']['name'];?></td>
                                    <td><?php echo $this->Html->link('<i class="fa fa-television"></fa>',array('controller'=>$table_name,'action'=>'view',$record[$model]['id']),array('target'=>'_blank', 'escape'=>false));?></td>
                                <?php }?>               
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    
    
    <!-- update from -->
    <!-- 
    1. Create copy of existing record -1 (revision_number), and add as a new record
    2. Copy existind document to archived folder
    3. Copy new document to a new folder
    4. Add previouse document's parent_id will be existing id
    5. Previous documents archived will be set to 1
    6. add cr_id to as cr id

    -->    

    <?php echo $this->Form->create('QcDocument',array('action'=>'update_revision'),array('class'=>'form-control','default'=>true));?>

    <?php 
    echo $this->Form->hidden('id',array('default'=>$qcDocument['QcDocument']['id']));
    echo $this->Form->hidden('cr_id',array('default'=>$record[$model]['id']));
    echo $this->Form->hidden('replace_file',array('default'=>WWW_ROOT . DS . 'files' . DS . $this->Session->read('User.company_id') . DS . 'qc_documents' . DS . $qcDocument['QcDocument']['id'] . DS . $file));
    echo $this->Form->hidden('new_file_id',array('default'=>$fileEdit['File']['id']));

    ?>
    <div class="row">
        <div class="col-md-6"><?php echo $this->Form->input('revision_number',array('class'=>'form-control','default'=>$qcDocument['QcDocument']['revision_number']+1));?></div>
        <div class="col-md-6"><?php echo $this->Form->input('revision_date',array('class'=>'form-control','default'=>date('Y-m-d')));?></div>
    
        <div class="col-md-12"><br /><?php echo $this->Form->submit(__('Update Document'),array('class'=>'btn btn-lg btn-info'));
        echo $this->Form->end();?></div>
    <!-- update from ends-->
    </div>

<?php } else {?>
    <div class="row">
        <div class="col-md-12"><h4 class="text-danger"><?php echo h(__('You have not defined Change Management Custom Table.')); ?></h4></div>
        <div class="col-md-12">Click <?php echo $this->Html->link(__('Here'),array('action'=>'define_change_history_table'));?> to link you change manahement custom table.</div>
    </div>
<?php }?>
