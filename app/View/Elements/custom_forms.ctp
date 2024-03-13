<?php 
$str = 'company_id:'.$this->Session->read('User.company_id');
$response =  $this->requestAction(array('action'=>'curl','post','custom_forms','elements','data'=>json_encode(array($existingFields,$fieldDetails))));
$result = json_decode($response,true);
if($result['error']==1 or $response == null){ ?>
  <div class="row">
    <div class="col-md-12">
      <div class="alert alert-danger">FlinkISO API is not available.</div>
      <div class="panel">
        <div class="panel-body">
          <p>Check your internet connection.</p>
          <?php if($result['end_date']){
            echo "<p>Make sure that you have renewed your API subscription which expired on ".date('d M Y',strtotime($result['end_date'])).". Visit <a href='https://www.flinkiso.com/pricing/qms-api.html' target='_blank'>https://www.flinkiso.com/pricing/qms-api.html</a> to learm more or contact sale@flinkiso.com for renewal.</p>";
          }?>
        </div>
      </div>
    </div>
  </div>
<?php }else{
  echo $response;
} ?>


