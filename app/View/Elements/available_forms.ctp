<?php 
$str = 'type:'.$type;
$response =  $this->requestAction(array('action'=>'curl','post','custom_forms','available_forms','data'=>array('type'=>$type)));
$result = json_decode($response,true);
if($result['error']==1){ ?>
  
<?php }else{
  echo $response;
}
?>