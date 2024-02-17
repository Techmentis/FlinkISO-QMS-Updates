<?php if($finalCustomTableTask){ ?>
  <div class="row">
    <div class="col-md-12">
      <div class="box box-warning">
        <div class="box-header with-border">
          <h3 class="box-title">Tasks Assigned To You</h3>
          <div class="box-tools">
            
          </div>
        </div>
        <div class="box-body" style="padding:0">
          <div class="table-responsive">            
            <table cellpadding="0" cellspacing="0" class="table table-responsive table-hover">
              <tbody><tr>
                <th>Custom Form</th>
                <th>Record</th>
                <th>Message</th>                
                <th>From</th>
                <th>Target Date</th>
                <th></th>
              </tr>
              <?php 
              foreach($finalCustomTableTask as $result){
                if($result['parent']){
                  $formName = $result['parent']['CustomTable']['table_name'];
                  $controllerName = $result['parent']['CustomTable']['table_name'];
                  $table = $result['parent']['CustomTable']['id'];
                }else{
                  $formName = $result['table']['CustomTable']['table_name'];
                  $controllerName = $result['table']['CustomTable']['table_name'];
                  $table = $result['table']['CustomTable']['id'];
                }
                foreach($result['records'] as $rec){

                  if($rec[Inflector::classify($result['table']['CustomTable']['table_name'])]['parent_id']){
                    $recid = $rec[Inflector::classify($result['table']['CustomTable']['table_name'])]['parent_id'];
                  }else{
                    $recid = $rec[Inflector::classify($result['table']['CustomTable']['table_name'])]['id'];
                  } 
                  ?>
                  <tr>
                    <td><?php echo $result['table']['CustomTable']['name'];?></td>
                    <td><?php echo $rec[Inflector::classify($result['table']['CustomTable']['table_name'])][$result['table']['CustomTable']['display_field']];?></td>
                    <td><?php echo $result['table']['CustomTableTask']['message'];?></td>
                    <td><?php echo $PublishedEmployeeList[$rec[Inflector::classify($result['table']['CustomTable']['table_name'])]['prepared_by']];?></td>
                    <td><?php echo $rec[Inflector::classify($result['table']['CustomTable']['table_name'])][$result['table']['CustomTableTask']['date_field']];?></td>
                    <td class="text-right">
                      <?php
                      $names = '';
                      $names .= '/qc_document_id:'.$result['table']['CustomTable']['qc_document_id'].'/process_id:'.$result['table']['CustomTable']['process_id'].'/custom_table_id:'.$table;

                      echo $this->Form->create($formName,array('controller'=>$controllerName,'action'=>'edit'. '/'.$recid.$names));
                      echo $this->Form->hidden('Access.skip_access_check',array('default'=>1));
                      echo $this->Form->hidden('Access.allow_access_user',array('default'=>$this->Session->read('User.id')));
                      echo $this->Form->submit('Act',array('class'=>'btn btn-xs btn-danger'));
                      echo $this->Form->end();
                      ?>
                    </td>
                  </tr>
                <?php } ?>
              <?php } ?>                  
            </tbody></table>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php } ?>
