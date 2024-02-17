<?php 
$str = 'type:'.$type;
$response =  $this->requestAction(array('action'=>'curl','post','custom_forms','available_forms','data'=>array('type'=>$type)));
$result = json_decode($response,true);
if($result['error']==1){ ?>
  <div class="alert alert-danger">FlinkISO API is not available due to invalid credentials.</div>
<?php }else{
  echo $response;
}
?>