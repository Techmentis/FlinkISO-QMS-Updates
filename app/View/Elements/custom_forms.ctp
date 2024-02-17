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
          <p>Make sure that your API server is accesible from your server.</p>
          <p>Make sure that you have renewed your API subscription.</p>
        </div>
      </div>
    </div>
  </div>
<?php }else{
  echo $response;
} ?>


