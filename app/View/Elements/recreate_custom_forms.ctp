<script  src="https://www.paypal.com/sdk/js?client-id=BAABcB51IbtCT0Yb0sCwfVdbeHIwxjR0kjU2_3xL3gntGNF99TxO3zuuY7fRWbcg2_jd12CpQgpJZlib4E&components=hosted-buttons&disable-funding=venmo&currency=USD"></script>
<style>#checkout-button{display: none;}</style>
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
            <p><strong>Alert:</strong> Your FlinkISO API Subcription has expired. Click here to renew the subscription.</p>
            <p>You can renew the subscription by purchasing the API access from the links below.</p>
            <p>Please use our registred email address while making the purchase.</p>
            <div class="row">
              <div class="col-md-12">
                <table class="table table-bordered">
                  <tr>
                    <th>1 Month: USD.88</th>
                    <th>3 Months: USD.231</th>
                    <th>6 Months: USD.440</th>
                    <th>12 Months: USD.750</th>
                </tr>
                  <tr>
                    <td>                    
                      <div id="paypal-container-HUWKVKPWAQPH2"></div>
                        <script>
                          paypal.HostedButtons({
                            hostedButtonId: "HUWKVKPWAQPH2",
                          }).render("#paypal-container-HUWKVKPWAQPH2")
                        </script>            
                    </td>
                    <td>
                      <div id="paypal-container-7ZD6TR45T67JL"></div>
                        <script>
                          paypal.HostedButtons({
                            hostedButtonId: "7ZD6TR45T67JL",
                          }).render("#paypal-container-7ZD6TR45T67JL")
                        </script>
                    </td>
                    <td>
                        <div id="paypal-container-CYH6VJZ5E4CYN"></div>
                        <script>
                          paypal.HostedButtons({
                            hostedButtonId: "CYH6VJZ5E4CYN",
                          }).render("#paypal-container-CYH6VJZ5E4CYN")
                        </script>
                    </td>
                    <td>
                      <div id="paypal-container-AAHKYH69UMYTL"></div>
                        <script>
                          paypal.HostedButtons({
                            hostedButtonId: "AAHKYH69UMYTL",
                          }).render("#paypal-container-AAHKYH69UMYTL")
                        </script>
                    </td>
                  </tr>                
              </table>
            </div>
          </div>
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
          <p><a href="https://www.paypal.com/ncp/payment/HUWKVKPWAQPH2" target="_blank" class="btn btn-success btn-lg">Pay Now!</a></p>
          <p>Please make sure to use the same registered email address while making the payment.</p>
          <p>To know more about FlinkISO API: <a href="https://www.flinkiso.com/pricing/qms-api.html" target="_blank">Click Here.</a></p>
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
