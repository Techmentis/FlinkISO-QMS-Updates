<?php $thisTableBelognsTos = json_decode($customTable['CustomTable']['belongs_to'],true); ?>
<div class="panel-group" id="belongsToaccordion" role="tablist" aria-multiselectable="true">
  <?php 
  foreach($thisTableBelognsTos as $thisTableBelognsToField => $thisTableBelognsTosModel){ 
    if($thisTableBelognsTosModel != -1){ ?>
      <div class="panel panel-default">
        <div class="panel-heading" role="tab" id=false>
          <h5 class="panel-title" style="font-size: 14px;">
            <a role="button" data-toggle="collapse" data-parent="#belongsToaccordion" href="#<?php echo $thisTableBelognsToField;?>" aria-expanded="true" aria-controls="<?php echo $thisTableBelognsToField;?>">
              <?php echo $thisTableBelognsTosModel;?>
            </a>
          </h5>
        </div>
        <div id="<?php echo $thisTableBelognsToField;?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="<?php echo $thisTableBelognsToField;?>">
          <div class="panel-body">
            <div id="<?php echo $thisTableBelognsToField;?>_belongs_div"></div>
            <script type="text/javascript">
              
                $("#<?php echo $thisTableBelognsToField;?>").load("<?php echo Router::url('/', true); ?>custom_tables/loadbegonstotablefields/<?php echo $thisTableBelognsTosModel;?>")
              
            </script>

          </div>
        </div>
      </div>
    <?php } ?>
  <?php } ?>
</div>
