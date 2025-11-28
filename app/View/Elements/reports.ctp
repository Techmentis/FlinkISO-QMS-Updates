<?php echo $this->Html->script(array('chartjs/dist/chart.min'));
echo $this->fetch('script');
echo $this->Session->flash();?>
<style type="text/css">
    canvas{width: 100%;min-height: 150px;max-height: 250px;}
</style>
<div class="custom_tables">
    <?php echo $this->element('nav-header-lists',array('postData'=>array('pluralHumanName'=>'Reports','modelClass'=>'CustomTable','options'=>array("sr_no"=>"Sr No","name"=>"Name","fields"=>"Fields"),'pluralVar'=>'customTables'))); ?>    
    <div id='manualcharttabs'>
        <ul>
            <li><a href="#employee-compliance">Employee Compliance</a></li>
            <li><a href="#intro">Charts</a></li>            
            <li class="pull-right"><a href="#" onclick="loadTabs($('#manualcharttabs ul li .ui-tabs-anchor').length);">Add Chart</a></li>
        </ul>
        <div id="intro">
            <div class="row">
                <div class="col-md-12">
                    <i class="fa fa-pie-chart fa-5x pull-left " style="color:#0094ff"></i>
                    <h3>Click on <strong>Add Chart</strong> to create new chart</h3>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                     <?php echo $this->Form->create('CustomTable');?>
                    <div class="row">
                        <?php        
                        foreach($pieCharts as $pieChartKey => $pieChartValues){ ?>
                            <div class="col-md-4">
                                <div class="box box-default">
                                    <div class="box-header with-border text-center"><h3 class="box-title"><?php echo Inflector::humanize($pieChartKey);?></h3></div>
                                    <div class="box-body with-border">
                                        <?php echo $this->element('piechart',array(
                                            'title'=> $pieChartKey,
                                            'type'=>'pie',
                                            'count'=>$ccount,
                                            'label'=>'Employee Data Entry',
                                            'labels'=>json_encode($pieChartValues['labels']),
                                            'data'=>json_encode($pieChartValues['data'],JSON_NUMERIC_CHECK),
                                            'backgroundColor'=>json_encode($pieChartValues['backgroundColor']),
                                            'borderColor'=>json_encode($pieChartValues['borderColor']),
                                        ));?>
                                    </div>
                                </div>
                            </div>
                            <?php $ccount++; } ?>
                        </div>
                        <div class="box box-default">
                            <div class="box-header"><h3 class="box-title">Employee Data Entry Chart</h3></div>
                            <div class="box-body with-border">
                                <?php echo $this->element('linechart',array(
                                    'title'=>'Employee Data Entry',
                                    'type'=>'bar',
                                    'count'=>$ccount,
                                    'label'=>'Employee Data Entry',
                                    'labels'=>json_encode($employeeDataEntry['labels']),
                                    'data'=>json_encode($employeeDataEntry['data'],JSON_NUMERIC_CHECK),
                                    'backgroundColor'=>json_encode($employeeDataEntry['backgroundColor']),
                                    'borderColor'=>json_encode($employeeDataEntry['borderColor']),
                                ));$ccount++;?>
                            </div>
                        </div>
                        <div class="box box-default">
                            <div class="box-header"><h3 class="box-title">Department Chart</h3></div>
                            <div class="box-body with-border">
                                <?php echo $this->element('linechart',array(
                                    'title'=>'Department Data Entry',
                                    'type'=>'bar',
                                    'count'=>$ccount,
                                    'label'=>'Department Data Entry',
                                    'labels'=>json_encode($departmentDataEntry['labels']),
                                    'data'=>json_encode($departmentDataEntry['data'],JSON_NUMERIC_CHECK),
                                    'backgroundColor'=>json_encode($departmentDataEntry['backgroundColor']),
                                    'borderColor'=>json_encode($departmentDataEntry['borderColor']),
                                ));$ccount++;?>
                            </div>
                        </div>
                        <div class="box box-default">
                            <div class="box-header"><h3 class="box-title">Branch Chart</h3></div>
                            <div class="box-body with-border">
                                <?php echo $this->element('linechart',array(
                                    'title'=>'Branch Data Entry',
                                    'type'=>'bar',
                                    'count'=>$ccount,
                                    'label'=>'Branch Data Entry',
                                    'labels'=>json_encode($branchDataEntry['labels']),
                                    'data'=>json_encode($branchDataEntry['data'],JSON_NUMERIC_CHECK),
                                    'backgroundColor'=>json_encode($branchDataEntry['borderColor']),
                                    'borderColor'=>json_encode($branchDataEntry['borderColor']),
                                ));$ccount++;?>
                            </div>
                        </div>
                        <?php echo $this->Form->end();?>
                </div>
            </div>
        </div>
        <div id="employee-compliance">
            <div id="employee-comp"></div>
        </div>
    </div>
    <script type="text/javascript">
        function loadTabs(num_tabs){
            $("div#manualcharttabs ul").append(
                "<li><a href='#manualcharttabs" + num_tabs + "'>Chart #" + num_tabs + "</a></li>"
                );            
            $("#manualcharttabs").append("<div id='manualcharttabs"+num_tabs+"'></div>");
            $("#manualcharttabs").tabs("refresh");
            $("#manualcharttabs"+num_tabs).load("<?php echo Router::url('/', true); ?><?php echo $this->request->params['controller'] ?>/generate_charts/"+num_tabs+"/custom_table_id:<?php echo $this->request->params['named']['custom_table_id']?>");
        }
        $(document).ready(function() {
            $("#manualcharttabs").tabs({
                
            });
        });
    </script>           
    <script type="text/javascript">
     $().ready(function() {
        $("#CustomTableDateRange").daterangepicker();
        $('select').chosen({"width":"100%"});
        $("#employee-comp").load("<?php echo Router::url('/', true); ?><?php echo $this->request->params['controller'] ?>/employee_compliance/custom_table_id:<?php echo $this->request->params['named']['custom_table_id']?>")
    });
</script>
