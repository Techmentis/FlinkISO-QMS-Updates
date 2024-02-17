<div id="loadhere<?php echo $ccount;?>">
  <?php 
  echo $this->Html->script(array('jquery-form.min', 'chartjs/dist/chart.min')); 
  echo $this->fetch('script'); ?>

  <?php echo $this->Form->create('Reports',array('role' => 'form', 'class' => 'form', 'default' => false, 'id'=>'ReportsGenerateChartsForm'.$ccount));?>
  <div class="row">
    <div class="col-md-12">
      <?php if($this->request->data['Reports'][$ccount]['chart_type'] == 0 || $this->request->data['Reports'][$ccount]['chart_type'] == 1){ ?>        
        <div class="row">
          <div class="col-md-12">
            <div class="pad">
              <?php                
              if(isset($this->request->data) && isset($formChart)){
                echo $this->element('linechart',array(
                  'title'=> Inflector::Humanize($labelFields[$this->request->data['Reports'][$ccount]['lables']]),
                  'type'=>$chartTypes[$this->request->data['Reports'][$ccount]['chart_type']], 
                  'count'=>$ccount,
                  'label'=>'Employee Data Entry', 
                  'labels'=>json_encode($formChart['labels']),
                  'data'=>json_encode($formChart['data'],JSON_NUMERIC_CHECK),
                  'backgroundColor'=>json_encode($formChart['backgroundColor']),
                  'borderColor'=>json_encode($formChart['borderColor']),
                ));
              }
              ?>

            </div>
          </div>
          <div class="col-md-12">
            <div class="pad box-pane-right" style="min-height: 280px">
              <div class="row">
                <div class="col-md-3"><?php echo $this->Form->input('Reports.'.$ccount.'.lables',array('class'=>'form-control','options'=>$labelFields));?></div>
                <div class="col-md-3"><?php echo $this->Form->input('Reports.'.$ccount.'.data_field',array('class'=>'form-control','options'=>$dataFields));?></div>
                <div class="col-md-3"><?php echo $this->Form->input('Reports.'.$ccount.'.result_type',array('class'=>'form-control','options'=>$resultTypes));?></div>
                <div class="col-md-3"><?php echo $this->Form->input('Reports.'.$ccount.'.date_sort',array('class'=>'form-control','options'=>$datefields));?></div>
                <div class="col-md-3"><?php echo $this->Form->input('Reports.'.$ccount.'.date_range',array('class'=>'form-control'));?></div>
                <div class="col-md-6"><?php echo $this->Form->input('Reports.'.$ccount.'.chart_type',array('class'=>'','type'=>'radio', 'options'=>$chartTypes));?></div>
                <div class="col-md-3"><br /><?php echo $this->Form->submit(__('Submit'), array('div' => false, 'class' => 'btn btn-primary btn-success', 'id'=>'submit_id_chart' . $ccount)); ?></div>
              </div>
            </div>
          </div>

        <?php }elseif($this->request->data['Reports'][$ccount]['chart_type'] == 2 || $this->request->data['Reports'][$ccount]['chart_type'] == 3){ ?>
          <div class="col-md-6">
            <div class="pad">

             <?php  echo $this->element('piechart',array(
              'title'=> $pieChartKey,
              'type'=>$chartTypes[$this->request->data['Reports'][$ccount]['chart_type']], 
              'count'=>$ccount,
              'label'=>'Employee Data Entry', 
              'labels'=>json_encode($formChart['labels']),
              'data'=>json_encode($formChart['data'],JSON_NUMERIC_CHECK),
              'backgroundColor'=>json_encode($formChart['backgroundColor']),
              'borderColor'=>json_encode($formChart['borderColor']),
            ));?>

          </div>
        </div>
        <div class="col-md-6">
          <div class="pad box-pane-right" style="min-height: 280px">
            <div class="row">
              <div class="col-md-12"><?php echo $this->Form->input('Reports.'.$ccount.'.lables',array('class'=>'form-control','options'=>$labelFields));?></div>
              <div class="col-md-12"><?php echo $this->Form->input('Reports.'.$ccount.'.data_field',array('class'=>'form-control','options'=>$dataFields));?></div>
              <div class="col-md-12"><?php echo $this->Form->input('Reports.'.$ccount.'.result_type',array('class'=>'form-control','options'=>$resultTypes));?></div>
              <div class="col-md-12"><?php echo $this->Form->input('Reports.'.$ccount.'.date_sort',array('class'=>'form-control','options'=>$datefields));?></div>
              <div class="col-md-12"><?php echo $this->Form->input('Reports.'.$ccount.'.date_range',array('class'=>'form-control'));?></div>
              <div class="col-md-12"><?php echo $this->Form->input('Reports.'.$ccount.'.chart_type',array('class'=>'', 'type'=>'radio', 'options'=>$chartTypes));?></div>
              <div class="col-md-12"><br /><?php echo $this->Form->submit(__('Submit'), array('div' => false, 'class' => 'btn btn-primary btn-success', 'id'=>'submit_id_chart' . $ccount)); ?></div>
            </div>
          </div>
        </div>
      <?php } else{ ?>
        <div class="row">
          <div class="col-md-6">
            <div class="pad">

             <?php  echo $this->element('piechart',array(
              'title'=> $pieChartKey,
              'type'=>$chartTypes[$this->request->data['Reports'][$ccount]['chart_type']], 
              'count'=>$ccount,
              'label'=>'Employee Data Entry', 
              'labels'=>json_encode($formChart['labels']),
              'data'=>json_encode($formChart['data'],JSON_NUMERIC_CHECK),
              'backgroundColor'=>json_encode($formChart['backgroundColor']),
              'borderColor'=>json_encode($formChart['borderColor']),
            ));?>

          </div>
          <div class="col-md-6">
            <div class="pad box-pane-right" style="min-height: 280px">
              <div class="row">
                <div class="col-md-12"><?php echo $this->Form->input('Reports.'.$ccount.'.lables',array('class'=>'form-control','options'=>$labelFields));?></div>
                <div class="col-md-12"><?php echo $this->Form->input('Reports.'.$ccount.'.data_field',array('class'=>'form-control','options'=>$dataFields));?></div>
                <div class="col-md-12"><?php echo $this->Form->input('Reports.'.$ccount.'.result_type',array('class'=>'form-control','options'=>$resultTypes));?></div>
                <div class="col-md-12"><?php echo $this->Form->input('Reports.'.$ccount.'.date_sort',array('class'=>'form-control','options'=>$datefields));?></div>
                <div class="col-md-12"><?php echo $this->Form->input('Reports.'.$ccount.'.date_range',array('class'=>'form-control'));?></div>
                <div class="col-md-12"><?php echo $this->Form->input('Reports.'.$ccount.'.chart_type',array('class'=>'','type'=>'radio','options'=>$chartTypes));?></div>
                <div class="col-md-12"><br /><?php echo $this->Form->submit(__('Submit'), array('div' => false, 'class' => 'btn btn-primary btn-success', 'id'=>'submit_id_chart' . $ccount)); ?></div>
              </div>
            </div>
          </div>
        <?php } ?>
      </div>
    </div>      
    
  </div>
</div>
</div>
<?php echo $this->Form->end(); ?>
</div>
<script type="text/javascript">

  $().ready(function(){
    $('select').chosen();    
    $(".chosen-select").chosen({"width":"100%"});
    $("#Reports<?php echo $ccount;?>DateRange").daterangepicker();
  });

  $("#submit_id_chart<?php echo $ccount;?>").on('click',function(){

    $("#ReportsGenerateChartsForm<?php echo $ccount;?>").ajaxSubmit({
      url: "<?php echo Router::url('/', true); ?><?php echo $this->request->params['controller'] ?>/generate_charts/<?php echo $ccount;?>/custom_table_id:<?php echo $this->request->params['named']['custom_table_id']?>",
      type: 'POST',
      target: '#loadhere<?php echo $ccount;?>',
      beforeSend: function(){           
      },
      complete: function() {           
      },
      error: function (request, status, error) {          
        alert('Action failed!');
      }
    });
  });

</script>
