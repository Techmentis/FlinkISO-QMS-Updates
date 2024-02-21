<?php
$docarray = array('doc','docx');
$sheetarray = array('xls','xlsx');
$pdfarray = array('pdf');
$pptarray = array('ppt','pptx');
?>
<style type="text/css">
  .callout-default{background-color:#fbf6d1 !important ;}
  .callout{border-left: 5px solid #f3e791}
  .box-header>.box-tools{top: 10px;}
  .small-box .icon{font-size: 60px; top:10px}
  .small-box:hover .icon {font-size: 70px;}
</style>
<?php echo $this->Session->flash();?>
<div class="row">
  <div class="col-xs-12">
    <h3>Dashboard</h3>
    <div class="">
      <div class="row">
        <div class="col-md-7 text-left">
          <?php
          echo $this->Html->link('<i class="fa fa-sitemap"></i>',array('controller'=>'branches', 'action'=>'index','timestamp'=>date('ymdhis')),
            array('class'=>'tooltip1 btn btn btn-app btn-sm btn-info','escape'=>false,
              'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'bottom', 'title'=> 'List of Branches'
            ));
          echo $this->Html->link('<i class="fa fa-industry"></i>',array('controller'=>'departments', 'action'=>'index','timestamp'=>date('ymdhis')),
            array('class'=>'tooltip1 btn btn btn-app btn-sm btn-info','escape'=>false ,
              'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'bottom', 'title'=> 'List of Departments'
            ));
          echo $this->Html->link('<i class="fa fa-users"></i>',array('controller'=>'employees', 'action'=>'index','timestamp'=>date('ymdhis')),
            array('class'=>'tooltip1 btn btn btn-app btn-sm btn-info','escape'=>false,'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 
              'data-placement'=>'bottom', 'title'=> 'List of Employees'
            ));
          echo $this->Html->link('<i class="fa fa-user"></i>',array('controller'=>'users', 'action'=>'index','timestamp'=>date('ymdhis')),
            array('class'=>'tooltip1 btn btn btn-app btn-sm btn-info','escape'=>false,
              'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'bottom', 'title'=> 'List of Users'
            ));

          echo $this->Html->link('<i class="fa fa-user-secret"></i>',array('controller'=>'user_sessions', 'action'=>'index','timestamp'=>date('ymdhis')),
            array('class'=>'tooltip1 btn btn btn-app btn-sm btn-info','escape'=>false,
              'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'bottom', 'title'=> 'System User Audit Trail'
            ));

          echo $this->Html->link('<i class="fa fa-check-square-o"></i>',array('controller'=>'approvals', 'action'=>'index','timestamp'=>date('ymdhis')),
            array('class'=>'tooltip1 btn btn btn-app btn-sm btn-info','escape'=>false,
              'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'bottom', 'title'=> 'View all approvals'
            ));

          
            ?>
          </div>
        </div>    
      </div>
    </div>
  </div>

  <?php echo $this->element('subscription');?>
  <br />
  <div class="row">
    <div class="col-lg-3 col-xs-3">

      <div class="small-box bg-aqua">
        <div class="inner">
          <h3><?php echo $totalDocs;?></h3>
          <p>Documents Created</p>
        </div>
        <div class="icon">
          <i class="fa fa-folder-open"></i>
        </div>        
      </div>
    </div>

    <div class="col-lg-3 col-xs-3">

      <div class="small-box bg-green">
        <div class="inner">
          <h3><?php echo $totalTables;?></h3>
          <p>HTML Forms Created</p>
        </div>
        <div class="icon">
          <i class="fa fa-database"></i>
        </div>        
      </div>
    </div>

    <div class="col-lg-3 col-xs-3">

      <div class="small-box bg-yellow">
        <div class="inner">
          <h3><?php echo $totalFiles;?></h3>
          <p>Total Files Created</p>
        </div>
        <div class="icon">
          <i class="fa fa-file-o"></i>
        </div>        
      </div>
    </div>   
    <div class="col-lg-3 col-xs-3">

      <div class="small-box bg-red">
        <div class="inner">
          <h3><?php echo $totalFileSize;?> <sup style="font-size: 20px">mb</sup></h3>
          <p>Total Files Size</p>
        </div>
        <div class="icon">
          <i class="fa fa-folder-open"></i>
        </div>        
      </div>
    </div>   
  </div>
  <div class="pie" id="pie" style="margin-bottom:0; padding: 0;">  
    <script type="text/javascript">
      $("#pie").load("<?php echo Router::url('/', true); ?>graph_panels/index");
    </script>
  </div>

  <div class="row">  
    <div class="col-xs-12">
      <div class="box box-info">
        <div class="box-header">
          <h3 class="box-title">Shared Custom Forms</h3> <i class="fa fa-database pull-right"></i>

          <div class="box-tools">
            <!-- <?php echo $this->Html->link('<i class="fa fa-database fa-lg"></i>',array('controller'=>'custom_tables','action'=>'index'),array('escape'=>false));?> -->
          </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body table-responsive no-padding">
          <table class="table table-hover">
            <tbody><tr>
              <th>Document</th>
              <th>Last Updated</th>
              <th>Status</th>
              <th>Schedule</th>
              <th class="text-right"></th>
            </tr>
            <?php foreach($customTables as $customTable){ 

              $last_updated = $this->requestAction(array('controller'=>'custom_tables', 'action'=>'last_updated_record',$customTable['CustomTable']['table_name']));

              switch ($schedules[$customTable['QcDocument']['schedule_id']]){
                case 'Daily':
                if(date('Y-m-d',strtotime($last_updated['created'])) != date('Y-m-d')){
                  $label = 'Pending';
                  $class = "label-warning";
                }else{
                  $label = 'Updated';
                  $class = "label-success";
                }
                break;
                case 'Weekly':
                if(date('Y-m-d',strtotime($last_updated['created'])) < date('Y-m-d',strtotime('-1 week'))){
                  $label = 'Pending';
                  $class = "label-warning";
                }else{
                  $label = 'Updated';
                  $class = "label-success";
                }
                break;
                case 'Monthly':
                if(date('Y-m-d',strtotime($last_updated['created'])) < date('Y-m-d',strtotime('-1 month'))){
                  $label = 'Pending';
                  $class = "label-warning";
                }else{
                  $label = 'Updated';
                  $class = "label-success";
                }
                break;
                case 'None':
                $label = 'Add';
                $class = "label-warning";    
                break;
              }
              ?>
              <tr>
                <td>
                  <?php   
                  if(in_array($customTable['QcDocument']['file_type'],$docarray)){ ?>
                    <i class="fa fa-file-word-o" aria-hidden="true"></i>
                  <?php }elseif(in_array($customTable['QcDocument']['file_type'],$sheetarray)){ ?>
                    <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                  <?php }elseif(in_array($customTable['QcDocument']['file_type'],$pdfarray)){ ?>
                    <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                  <?php }elseif(in_array($customTable['QcDocument']['file_type'],$pptarray)){ ?>
                    <i class="fa fa-file-ppt-o" aria-hidden="true"></i>
                    <?php }?>&nbsp;

                    <strong><?php echo $customTable['CustomTable']['name'];?></strong> : <small><?php echo $customTable['QcDocument']['name'];?> : <?php echo $customTable['QcDocument']['document_number'];?>-<?php echo $customTable['QcDocument']['revision_number'];?>.<?php echo $customTable['QcDocument']['file_type'];?></small></td>
                    <td><?php 
                    if($last_updated)echo $last_updated['created'];
                    else echo "NIL";
                // echo $customTable['QcDocument']['last_updated'];
                  ?></td>
                  <td>                
                    <span class="label <?php echo $class;?>"><?php echo $label;?></span>
                  </td>
                  <td><?php echo $schedules[$customTable['QcDocument']['schedule_id']];?></td>
                  <th class="text-right">
                    <div class="btn-group btn-light btn-no-border" style="width:105px">
                      <?php 
                      if($label == 'Updated'){
                        echo $this->Html->link('<i class="fa fa-edit fa-lg"></i>',array(
                          'controller'=>$customTable['CustomTable']['table_name'],
                          'action'=>'edit',$last_updated['id'],
                          'qc_document_id'=>$customTable['CustomTable']['qc_document_id'],
                          'custom_table_id'=>$customTable['CustomTable']['id']
                        ),array('escape'=>false,'class'=>'btn btn-default btn-xs tooltip1','data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'bottom', 'title'=> 'Update Record'));

                        echo $this->Html->link('<i class="fa fa-desktop fa-lg"></i>',array(
                          'controller'=>$customTable['CustomTable']['table_name'],
                          'action'=>'view',$last_updated['id'],
                          'qc_document_id'=>$customTable['CustomTable']['qc_document_id'],
                          'custom_table_id'=>$customTable['CustomTable']['id'],
                        ),array('escape'=>false,'class'=>'btn btn-default btn-xs tooltip1','data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'bottom', 'title'=> 'View Record'));
                      }                  
                      echo $this->Html->link('<i class="fa fa-table fa-lg"></i>',array('controller'=>''.$customTable['CustomTable']['table_name'].'','action'=>'index',
                        'custom_table_id'=> $customTable['CustomTable']['id'],'qc_document_id'=> $customTable['CustomTable']['qc_document_id'],'custom_table_id'=>$customTable['CustomTable']['id']),array('escape'=>false,'class'=>'btn btn-default btn-xs tooltip1','data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'bottom', 'title'=> 'Goto Index Page'));
                      
                      echo $this->Html->link('<i class="fa fa-plus fa-lg"></i>',array('controller'=>''.$customTable['CustomTable']['table_name'].'','action'=>'add',
                        'custom_table_id'=> $customTable['CustomTable']['id'],'qc_document_id'=> $customTable['CustomTable']['qc_document_id'],'custom_table_id'=>$customTable['CustomTable']['id']),array('escape'=>false,'class'=>'btn btn-default btn-xs tooltip1','data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'bottom', 'title'=> 'Add New Record'));

                        ?>

                      </th>
                    </tr>
                  <?php } ?>          
                  <tr>
                    <td colspan="6"><small>To add new record click <i class="fa fa-plus"></i> button. To Updated last added record click <i class="fa fa-edit"></i>  button or to view the record click <i class="fa fa-desktop"></i> button. 
                      <!-- To see all records added by you click <?php echo $this->Html->link('here',array('controller'=>'records','action'=>'index'));?> -->
                    </small></td>
                  </tr>
                </tbody>
              </table>

            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
      </div>
      <?php if($approvals){ ?>
        <div class="row">
          <div class="col-md-12">
            <div class="box box-default">
              <div class="box-header ">
                <h3 class="box-title">Approvals</h3>
                <?php echo $this->Html->link('<i class="fa fa-list"></i>',array('controller'=>'approvals','action'=>'index'),array('escape'=>false,'class'=>'pull-right'));?>
              </div>
              <div class="box-body" style="padding:0">
                <div class="table-responsive">
                  <table cellpadding="0" cellspacing="0" class="table table-hover">
                    <tr>
                      <th>Table</th>
                      <th>Date/time</th>
                      <th>Record</th>
                      <th>From</th>
                      <th>Comments</th>                
                      <th width="90" class="text-right"></th>
                    </tr>

                    <?php foreach ($approvals as $approval): 
                      if(!$approval['ApprovalComment'] && $approval['ApprovalComment'][0]['user_id'] != $this->Session->read('User.id')){


                        ?>
                        <tr>
                          <td><?php echo h($approval['Approval']['model_name']); ?>&nbsp;</td>
                          <td><?php echo h($approval['Approval']['created']); ?>&nbsp;</td>
                          <td><?php echo h($approval['Approval']['title']); ?>&nbsp;</td>
                          <td><?php echo h($approval['From']['name']); ?>&nbsp;</td>            
                          <td><?php echo h($approval['Approval']['comments']); ?>&nbsp;</td>
                          <td class="text-right">
                            <?php
                            if($approval['Approval']['approval_mode'] == 1 ){$action = 'edit';$aClass = "success";}
                            else {$action = 'view';$aClass = "warning";}

                            $names = '';
                            $names .= '/approval_id:'.$approval['Approval']['id'];
                            $names .= '/qc_document_id:'.$approval['Approval']['qc_document_id'];
                            $names .= '/custom_table_id:'.$approval['Approval']['custom_table_id'];

                            echo $this->Form->create($approval['Approval']['controller_name'],array('controller'=>$approval['Approval']['controller_name'],'action'=>$action. '/'.$approval['Approval']['record'].$names));
                            echo $this->Form->hidden('Access.skip_access_check',array('default'=>1));
                            echo $this->Form->hidden('Access.allow_access_user',array('default'=>$this->Session->read('User.id')));
                            echo $this->Form->submit($action,array('class'=>'btn btn-xs btn-'.$aClass.''));
                            echo $this->Form->end();
                            ?>
                          </td>
                        </tr>
                      <?php } endforeach; ?>
                    </table>
                  </div>       
                </div>
                <div class="box-footer"><small>List of approvals sent/received by you.</small></div>
              </div>
            <?php }else{ ?>              
            <?php } ?>
            <?php if($approvalComments){ ?>
              <div class="row">
                <div class="col-md-12">
                  <div class="box box-danger">
                    <div class="box-header ">
                      <h3 class="box-title">Approval Responses</h3>
                      <div class="box-tools">
                        <!-- <i class="fa fa-mail-reply fa-sm pull-right"></i> -->
                      </div>
                    </div>
                    <div class="box-body" style="padding:0">
                      <div class="table-responsive">            
                        <table cellpadding="0" cellspacing="0" class="table table-hover">
                          <tr>
                            <th>Table</th>
                            <th>Date/time</th>
                            <th>From</th>                
                            <th>Comments</th>               
                            <!-- <th>Approval Status</th> -->
                            <th width="90" class="text-right"></th>
                          </tr>

                          <?php foreach ($approvalComments as $approvalComment): ?>
                            <tr>
                              <td><?php echo h($approvalComment['Approval']['model_name']); ?>&nbsp;</td>
                              <td><?php echo h($approvalComment['ApprovalComment']['created']); ?>&nbsp;</td>
                              <td><?php echo h($approvalComment['From']['name']); ?>&nbsp;</td>
                              <td><?php echo h($approvalComment['ApprovalComment']['comments']); ?>&nbsp;</td>
                              <!-- <td><?php echo h($approvalComment['Approval']['approval_status']); ?>&nbsp;</td> -->

                              <td class="text-right">
                                <?php
                                if($approvalComment['Approval']['approval_mode'] == 1 ){$action = 'edit';$aClass = "danger";}
                                else {$action = 'view';$aClass = "danger";}

                // if($approvalComment['Approval']['approval_mode'] == 1 )$action = 'edit';
                // else $action = 'view';

                                $names = '';

                                $names = '';
                                if($recs[Inflector::classify($approvalComment['Approval']['controller_name'])]['qc_document_id'])$names .= '/qc_document_id:'.$recs[Inflector::classify($data['controller'])][
                                  'qc_document_id'];
                                  if($recs[Inflector::classify($approvalComment['Approval']['controller_name'])]['process_id'])$names .= '/process_id:'.$recs[Inflector::classify($data['controller'])][
                                    'process_id'];

                                    $names .= '/approval_id:'.$approvalComment['Approval']['id'].'/approval_comment_id:'.$approvalComment['ApprovalComment']['id'];
                                    $names .= '/approval_id:'.$approvalComment['Approval']['id'];
                                    $names .= '/qc_document_id:'.$approvalComment['ApprovalComment']['qc_document_id'];
                                    $names .= '/custom_table_id:'.$approvalComment['ApprovalComment']['custom_table_id'];
                // debug($approvalComment['Approval']['record']);
                                    echo $this->Form->create($approvalComment['Approval']['controller_name'],array('controller'=>$approvalComment['Approval']['controller_name'],'action'=>$action.'/'.$approvalComment['Approval']['record'].$names,'id'=>false),array('class'=>'form-control'));
                                    echo $this->Form->hidden('Access.skip_access_check',array('default'=>1));
                                    echo $this->Form->hidden('Access.allow_access_user',array('default'=>$this->Session->read('User.id')));
                                    echo $this->Form->submit($action,array('class'=>'btn btn-xs btn-'.$aClass.''));
                                    echo $this->Form->end();
                                    ?>
                                  </td>
                                </tr>
                              <?php endforeach; ?>
                            </table>
                          </div>
                          <div class="box-footer"><small>List of approvals responses sent/received by you.</small></div>
                        </div>
                      </div>
                    </div>
                  </div>
                <?php }else{ ?>

                <?php } ?>

                <div id="customtabletasks"><i class="fa fa-refresh fa-spin"></i></div>
                <script type="text/javascript">
                  $("#customtabletasks").load("<?php echo Router::url('/', true); ?>custom_table_tasks/assigned_tasks/");
                </script>

                <?php if($qcDocForCoEdit){ ?>
                  <div class="row">
                    <div class="col-md-12">
                      <div class="box box-info">
                        <div class="box-header ">
                          <h3 class="box-title">Documents shared with you for co-authoring and in Draft mode.</h3>
                          <div class="box-tools">
                            <i class="fa fa-share-alt fa-lg pull-right"></i>
                          </div>
                        </div>
                        <div class="box-body" style="padding:0">
                          <div class="table-responsive">
                            <table class="table">
                              <tr>
                                <th>Document Name</th>
                                <th>Number</th>
                                <th>Standard</th>
                                <th>Clause</th>
                                <th>Prepared By</th>
                                <th>Created</th>
                                <th>Last Modified</th>
                                <th width="120"></th>
                              </tr>
                              <?php              
                              foreach($qcDocForCoEdit as $qcDocument){ ?>
                                <tr>
                                  <td><?php echo $qcDocument['QcDocument']['name']?></td>
                                  <td><?php echo $qcDocument['QcDocument']['document_number']?></td>
                                  <td><?php echo $qcDocument['Standard']['name']?></td>
                                  <td><?php echo $qcDocument['Clause']['title']?></td>
                                  <td><?php echo $qcDocument['PreparedBy']['name']?></td>
                                  <td><?php echo $qcDocument['QcDocument']['created']?></td>
                                  <td><?php echo $qcDocument['QcDocument']['modified']?></td>
                                  <td><?php echo $this->Html->link('Collaborate',array('controller'=>'qc_documents','action'=>'edit',$qcDocument['QcDocument']['id'],'timestamp'=>date('ymdhis')),array('class'=>'btn btn-xs btn-info'))?></td>
                                </tr>
                              <?php } ?>             
                            </table>
                          </div>
                        </div>
                        <div class="box-footer"><small>To turn off Co-authring for a document, change document status to "Published/Issued".</small></div>
                      </div>
                    </div>
                  </div>
                  <?php } ?>                  
                  <div class="row"><div class="col-md-12">
                    <?php echo $this->element('miles',array('masters'=>$masters));?>
                  </div>
                </div>
                <?php // echo $this->element('dashboard_help');?>
                <?php echo $this->element('custom_triggers',array('customTrigers'=>$customTrigers));?>
