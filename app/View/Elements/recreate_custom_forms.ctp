<?php 
$str = 'company_id:'.$this->Session->read('User.company_id');
$response =  $this->requestAction(array('action'=>'curl','post','custom_forms','recreate',
  'data'=>json_encode(array(
    $existingFields,
    $fieldDetails,
    $childTables,
    $thisTable,
    $linkedTosWithDisplay,
    $hasMany
  )),
  'linkedTosWithDisplay'=>json_encode($linkedTosWithDisplay)));
$result = json_decode($response,true);
if($result['error']==1 or $response == null){ 
  if($result['message'] == 'Incorrect credentials'){ ?>
    <div class="row">
      <div class="col-md-12">
        <!-- <div class="alert alert-danger">FlinkISO API Subscription Has Expired.</div> -->
        <div class="panel panel-default">
          <div class="panel-body">
            <p>Your FlinkISO API Subcription has expired. Click here to renew the subscription.</p>
            <p class="text-danger">If you are seeing this alert, Do not click Submit button. Click Back to goback to previouse page.</p>
          </div>
        </div>
      </div>
    </div>
  <?php }else{ ?>
    <div class="row">
      <div class="col-md-12">
        <div class="alert alert-danger">FlinkISO API is not available.</div>
        <div class="panel panel-default">
          <div class="panel-body">
            <p>Make sure that our API server is accesible from your server.</p>
            <p>Make sure that you have renewed your API subscription. Contact <strong>sales@flinkiso.com</strong> for renewal.</p>
            <p class="text-danger">If you are seeing this alert, Do not click Submit button. Click Back to goback to previouse page.</p>
          </div>
        </div>
      </div>
    </div>
  <?php }?>
  <script>
    $().ready(function(){
      $("#submit_id").hide();
    })
  </script>
<?php }else{
  echo $response; ?>
<script type="text/javascript">
    $("#loadbelongstos").load("<?php echo Router::url('/', true); ?>custom_tables/loadbelongstos/<?php echo $customTable['CustomTable']['id'];?>");
  </script>
<?php }?>
