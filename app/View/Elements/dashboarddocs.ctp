<?php if($qcDocs){ ?>
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
                <th>Document Name</th>
                <th>Number</th>
                <th>Standard</th>
                <th>Clause</th>
                <th>Prepared By</th>
                <th>Reviewed By</th>
                <th>Approved By</th>
                <th>Created</th>
                <th>Last Modified</th>
                <th width="120"></th>
              </tr>
              <?php              
              foreach($qcDocs as $qcDocument){ ?>
                <tr>
                  <td><?php echo $qcDocument['QcDocument']['name']?></td>
                  <td><?php echo $qcDocument['QcDocument']['document_number']?></td>
                  <td><?php echo $qcDocument['Standard']['name']?></td>
                  <td><?php echo $qcDocument['Clause']['title']?></td>
                  <td><?php echo $qcDocument['PreparedBy']['name']?></td>
                  <td><?php echo $qcDocument['ReviewedBy']['name']?></td>
                  <td><?php echo $qcDocument['ApprovedBy']['name']?></td>
                  <td><?php echo $qcDocument['QcDocument']['created']?></td>
                  <td><?php echo $qcDocument['QcDocument']['modified']?></td>
                  <td><?php echo $this->Html->link($btn,array('controller'=>'qc_documents','action'=>'edit',$qcDocument['QcDocument']['id'],'timestamp'=>date('ymdhis')),array('class'=>'btn btn-xs btn-info'))?></td>
                </tr>
              <?php } ?>             
            </table>
          </div>
        </div>        
      </div>
    </div>
  </div>
<?php } ?> 
