<script  src="https://www.paypal.com/sdk/js?client-id=BAABcB51IbtCT0Yb0sCwfVdbeHIwxjR0kjU2_3xL3gntGNF99TxO3zuuY7fRWbcg2_jd12CpQgpJZlib4E&components=hosted-buttons&disable-funding=venmo&currency=USD"></script>
<style>#checkout-button{display: none;}</style>
<?php 
$str = 'company_id:'.$this->Session->read('User.company_id');
$builderChildTables = array();
foreach ((array)$childTables as $childTable) {
  if (empty($childTable['CustomTable']['id'])) continue;
  $childTable['CustomTable']['edit_url'] = Router::url(array(
    'controller' => 'custom_tables',
    'action' => 'unlock',
    $childTable['CustomTable']['id'],
    'next_action' => 'recreate_child'
  ), true);
  $builderChildTables[] = $childTable;
}
$recreatePayload = json_encode(array(
    $existingFields,
    $fieldDetails,
    $builderChildTables,
    $thisTable,
    $linkedTosWithDisplay,
    $hasMany,
    array('has_type_ii_forms' => !empty($hasTypeIIForms), 'departments' => isset($departments) ? $departments : array()),
    isset($approvalSteps) ? $approvalSteps : array(),
    isset($designations) ? $designations : array()
  ));
$response = $this->requestAction(
  array('action'=>'curl','post','custom_forms','recreate'),
  array('data'=>array(
    'payload'=>$recreatePayload,
    'linkedTosWithDisplay'=>json_encode($linkedTosWithDisplay)
  ))
);
$result = is_string($response) ? json_decode($response, true) : null;
$hasBuilder = is_string($response) && strpos($response, 'id="fieldSettingsPanelBody"') !== false;
if (!$hasBuilder) {
  $apiMessage = is_array($result) && isset($result['message']) && is_string($result['message'])
    ? $result['message'] : '';
  if ($apiMessage === 'Incorrect credentials') { ?>
    <div class="row">
      <div class="col-md-12">
        <!-- <div class="alert alert-danger">FlinkISO API Subscription Has Expired.</div> -->
        <div class="panel panel-default">
          <div class="panel-body">
            <p><strong><?php echo h(__('Alert:')); ?></strong> <?php echo h(__('Your FlinkISO API Subcription has expired. Click here to renew the subscription.')); ?></p>
            <p><?php echo h(__('You can renew the subscription by purchasing the API access from the links below.')); ?></p>
            <p><?php echo h(__('Please use our registred email address while making the purchase.')); ?></p>
            <div class="row">
              <div class="col-md-12">
                <table class="table table-bordered">
                  <tr>
                    <th><?php echo h(__('1 Month: USD.88')); ?></th>
                    <th><?php echo h(__('3 Months: USD.231')); ?></th>
                    <th><?php echo h(__('6 Months: USD.440')); ?></th>
                    <th><?php echo h(__('12 Months: USD.750')); ?></th>
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
          <p class="text-danger"><?php echo h(__('If you are seeing this alert, Do not click Submit button. Click Back to goback to previouse page.')); ?></p>
        </div>
      </div>
    </div>
  </div>
<?php } else { ?>
  <div class="alert alert-danger" role="alert">
    <strong><?php echo h(__('The form builder could not load.')); ?></strong>
    <?php echo h($apiMessage !== '' ? $apiMessage : __('The configured FlinkISO API did not return a form builder. Reload this page to retry.')); ?>
  </div>
<?php }?>
<script>
  $().ready(function(){
    $("#submit_id").prop("disabled", true).hide();
  })
</script>
<?php }else{
  echo $response; ?>
  <script type="text/javascript">
    $("#loadbelongstos").load("<?php echo Router::url('/', true); ?>custom_tables/loadbelongstos/<?php echo $customTable['CustomTable']['id'];?>");
  </script>
<?php }?>
