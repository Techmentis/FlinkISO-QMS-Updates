
<?php 
$percent = round($appsize * 100 / 1,2);    
if($percent <= 50)$dirclass = 'success';
else if($percent > 50 && $percent <= 85)$dirclass = 'warning';
else $dirclass = 'danger';

$dbpercent = round($dbsize * 100 / 1,2);    
if($dbpercent <= 50)$dbdirclass = 'success';
else if($dbpercent > 50 && $dbpercent <= 85)$dbdirclass = 'warning';
else $dbdirclass = 'danger';
?>

<div class="row">
    <div class="col-md-6">
        <div class="box box-<?php echo $dirclass;?>">
            <div class="box-header with-border">
                <h3 class="box-title">Storage Usage</h3>
                <i class="glyphicon glyphicon-hdd pull-right text-<?php echo $dirclass;?>" style="font-size:15px"></i>
            </div>
            <div class="box-body">
                <div class="progress progress-sm active" style="margin-bottom:0px">
                    <div class="progress-bar progress-bar-<?php echo $dirclass;?> progress-bar-striped" role="progressbar" aria-valuenow="<?php echo $percent;?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $percent;?>%">
                        <span class="sr-only"><?php echo $percent;?>% Used out of 1GB</span>
                    </div>
                </div>                
            </div>
            <div class="box-footer">
                <?php echo $percent;?>% Used out of 1GB
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="box box-<?php echo $dbdirclass;?>">
            <div class="box-header with-border">
                <h3 class="box-title">Database Usage</h3>
                <i class="glyphicon glyphicon-hdd pull-right text-<?php echo $dbdirclass;?>" style="font-size:15px"></i>
            </div>
            <div class="box-body">
                <div class="progress progress-sm active" style="margin-bottom:0px">
                    <div class="progress-bar progress-bar-<?php echo $dbdirclass;?> progress-bar-striped" role="progressbar" aria-valuenow="<?php echo $dbpercent;?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $dbpercent;?>%">
                        <span class="sr-only"><?php echo $dbpercent;?>% Used out of 1GB</span>
                    </div>
                </div>                
            </div>
            <div class="box-footer">
                <?php echo $dbpercent;?>% Used out of 1GB
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div id="monthlyusage"></div>
        <script type="text/javascript">
            $("#monthlyusage").load("<?php echo Router::url('/', true); ?>billing/monthly_usage");
        </script>
    </div>
</div>


