<?php if($dcndocs){ ?>
  <div class="row">
    <div class="col-md-12">
      <div class="box box-info">
        <div class="box-header ">
          <h3 class="box-title"><?php echo $title;?></h3>
          <div class="box-tools">
            <i class="fa fa-share-alt fa-lg pull-right"></i>
          </div>
        </div>
        <div class="box-body" style="padding:0">
          <div class="table-responsive">
            <table class="table">
              <tr>
                <th><?php echo $this->Paginator->sort('dcn_no','DCN No'); ?></th>
                <th><?php echo $this->Paginator->sort('change_type','Type'); ?></th>
                <th><?php echo $this->Paginator->sort('dcn_date','Date'); ?></th>
                <th><?php echo $this->Paginator->sort('document','Document'); ?></th>
                <th><?php echo $this->Paginator->sort('prepared_by'); ?></th>
                <th><?php echo $this->Paginator->sort('reviewed_by'); ?></th>
                <th><?php echo $this->Paginator->sort('approved_by'); ?></th>    
                <th>Act</th>            
              </tr>
              <?php              
              foreach($dcndocs as $tblDocumentChangeControl0V1){ ?>
                <tr>
                  <td><?php echo $this->Html->link( $tblDocumentChangeControl0V1['TblDocumentChangeControl0V1']['dcn_no'],array('action'=>'view',$tblDocumentChangeControl0V1['TblDocumentChangeControl0V1']['id'],'qc_document_id'=>$this->request->params['named']['qc_document_id'],'custom_table_id'=>$this->request->params['named']['custom_table_id'],'process_id'=>$this->request->params['named']['process_id']));?></td>
                  <td><?php 
                  $chartTypes = array(0=>'Delete',1=>'Revise');
                  echo $chartTypes[$tblDocumentChangeControl0V1['TblDocumentChangeControl0V1']['change_type']];?></td>
                  <td>
                    <?php 
                      if($tblDocumentChangeControl0V1['TblDocumentChangeControl0V1']['dcn_date'])
                        echo  date(Configure::read('dateFormat'),strtotime($tblDocumentChangeControl0V1['TblDocumentChangeControl0V1']['dcn_date'])) ;
                        else echo "--";
                  ?></td>                  
                  <td><?php echo $tblDocumentChangeControl0V1['Document']['name'];?>                    
                  </td>
                  <?php if($tblDocumentChangeControl0V1['TblDocumentChangeControl0V1']['change_type'] == 2 ){ ?>
                    <td><?php echo $tblDocumentChangeControl0V1['PreparedBy']['name'];?></td>
                    <td><?php echo $tblDocumentChangeControl0V1['ReviewedBy']['name'];?></td>
                    <td><?php echo $tblDocumentChangeControl0V1['ApprovedBy']['name'];?></td>                  
                  <?php }else{ ?>
                    <td><?php if($tblDocumentChangeControl0V1['TblDocumentChangeControl0V1']['prepared_date']) echo $tblDocumentChangeControl0V1['PreparedBy']['name'];?></td>
                    <td><?php if($tblDocumentChangeControl0V1['TblDocumentChangeControl0V1']['review_date'] && $tblDocumentChangeControl0V1['TblDocumentChangeControl0V1']['review_status'] == 1)echo $tblDocumentChangeControl0V1['ReviewedBy']['name'];?></td>
                    <td><?php if($tblDocumentChangeControl0V1['TblDocumentChangeControl0V1']['approval_date'] && $tblDocumentChangeControl0V1['TblDocumentChangeControl0V1']['approve_status'] == 1)echo $tblDocumentChangeControl0V1['ApprovedBy']['name'];?></td>                  
                  <?php } ?>                  
                  <td><?php echo $this->Html->link($btn,array(
                    'controller'=>'tbl_document_change_control_0_v1s',
                    'action'=>'edit',$tblDocumentChangeControl0V1['TblDocumentChangeControl0V1']['id'],
                    'custom_table_id'=>$tblDocumentChangeControl0V1['CustomTable']['id'],
                    'qc_document_id'=>$tblDocumentChangeControl0V1['QcDocument']['id'],
                    'timestamp'=>date('ymdhis')),array('class'=>'btn btn-xs btn-info'))?></td>
                </tr>
              <?php } ?>             
            </table>
          </div>
        </div>        
      </div>
    </div>
  </div>
<?php } ?> 
