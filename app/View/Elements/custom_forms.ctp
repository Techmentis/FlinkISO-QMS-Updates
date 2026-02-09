<?php 
$str = 'company_id:'.$this->Session->read('User.company_id');
$response =  $this->requestAction(array('action'=>'curl','post','custom_forms','elements','data'=>json_encode(array($existingFields,$fieldDetails))));
$result = json_decode($response,true);
if($result['error']==1 or $response == null){ ?>
  <div class="row">
    <div class="col-md-12">
      <div class="panel">
        <div class="panel-body">
          <?php if($result['end_date']){
            echo "<p><i class='fa fa-exclamation-triangle'></i> Make sure that you have renewed your API subscription which expired on ".date('d M Y',strtotime($result['end_date'])).". Visit <a href='https://www.flinkiso.com/pricing/qms-api.html' target='_blank'>https://www.flinkiso.com/pricing/qms-api.html</a> to learm more or contact sale@flinkiso.com for renewal.</p>";
          }else{ ?>
            <p>Check your internet connection.</p>
          <?php }?>
        </div>
      </div>
    </div>
  </div>
<?php }else{
  echo $response;
} ?>