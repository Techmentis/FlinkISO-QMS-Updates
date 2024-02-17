<?php
$cnt = 0;
if($trial_expired == 0){
  $cnt++;
  if($days <= 3)$calloutclass = 'callout-danger';
  else $calloutclass = 'callout-default';?>
  <a href="#" style="white-space:inherit;">Your free trial will end in <?php echo $days ;?> days and your paid subcription will start. You will not be billied for the free trial preiod. Invoice for the paid preiod will be generated at the end of the month and will be available under billing section. You will have to clear the invoice within 7 days to contine using the application.</a>                 
<?php }else if($days){ 
  $cnt++;    
  ?>
  <a href="#" style="white-space:inherit;">Your paid subcription will be active till <?php echo date('d M Y',strtotime($days));?>.</a></a>
<?php }?>
<?php
if(!empty($app_status['invoice_date'])){
  if(date('Y-m-d',strtotime('-7 days',strtotime($app_status['invoice_due_date']))) <= date('Y-m-d')){ 
    if(date('Y-m-d',strtotime($app_status['invoice_date'])) <= date('Y-m-d')){ 
      $cnt++;
      ?>
      <a href="#" style="white-space:inherit;">Your invoice is ready on <?php echo date('d M y',strtotime($app_status['invoice_date'])) ?>. You will have to clear the invoice by <?php echo date('d M y',strtotime($app_status['invoice_due_date'])) ?> if you wish to continue. Failing which application will be suspennded. 
      </a>
    <?php }else{ 
      $cnt++;
      ?>
      <a href="#" style="white-space:inherit;">
        Your invoice will be ready on <?php echo date('d M y',strtotime($app_status['invoice_date'])) ?>. You will have to clear the invoice by <?php echo date('d M y',strtotime($app_status['invoice_due_date'])) ?> if you wish to continue. Failing which application will be suspennded.
      </a>
    <?php }}
  }else{ 
      // $cnt++;
    ?>
    <a href="#" style="white-space:inherit;">No invoices are pending.</a>
  <?php }?>

  <?php if($cnt > 0){ ?>
    <script type="text/javascript">
      $().ready(function(){
        $("#billingcount").addClass('label label-danger');
        $("#billingcount").html('<?php echo $cnt;?>');
      });
    </script>
  <?php } ?>




