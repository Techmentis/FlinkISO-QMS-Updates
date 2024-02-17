<?php
if(!empty($app_status['invoice_date'])){ ?>
  <style type="text/css">
    .callout-default{background-color:#fbf6d1 !important ;}
    .callout{border-left: 5px solid #f3e791}
  </style>
  <?php 
  if(date('Y-m-d',strtotime('-7 days',strtotime($app_status['invoice_due_date']))) <= date('Y-m-d')){ ?>
    <div class="row">
      <div class="col-md-12">
        <div class="callout callout-default" style="margin-bottom: 0!important;">
          <h4><i class="fa fa-info"></i> Note:</h4>
          <?php if(date('Y-m-d',strtotime($app_status['invoice_date'])) <= date('Y-m-d')){ ?>
            <div class="row">
              <div class="col-md-9">
                Your invoice is ready on <?php echo date('d M y',strtotime($app_status['invoice_date'])) ?>. You will have to clear the invoice by <?php echo date('d M y',strtotime($app_status['invoice_due_date'])) ?> if you wish to continue. Failing which application will be suspennded. 
              </div>
              <div class="col-md-3">
                <?php echo $this->Html->link('View Invoice',array('controller'=>'billing' ,'action'=> 'view_invoice',$app_status['invoice']['Invoice']['id']),array('class'=>'btn btn-xs btn-warning pull-right')); ?>
              </div>
            </div>
          <?php }else{ ?>
            Your invoice will be ready on <?php echo date('d M y',strtotime($app_status['invoice_date'])) ?>. You will have to clear the invoice by <?php echo date('d M y',strtotime($app_status['invoice_due_date'])) ?> if you wish to continue. Failing which application will be suspennded. 
          <?php } ?>
          
        </div>
      </div>      
    </div>
  <?php } ?>
<?php } ?>
