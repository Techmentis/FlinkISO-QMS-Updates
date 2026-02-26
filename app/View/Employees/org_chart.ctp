<?php echo $this->Html->script(array('html5','html2canvas')); ?>
<?php echo $this->fetch('script'); ?>
<div  id="main">
    <?php echo $this->Session->flash(); ?>
    <div class="employees ">
        <?php echo $this->element('nav-header-lists', array('postData' => array('pluralHumanName' => 'Employees', 'modelClass' => 'Employee', 'options' => array("sr_no" => "Sr No", "name" => "Name", "employee_number" => "Employee Number", "qualification" => "Qualification", "joining_date" => "Joining Date", "date_of_birth" => "Date Of Birth", "pancard_number" => "Pancard Number", "personal_telephone" => "Personal Telephone", "office_telephone" => "Office Telephone", "mobile" => "Mobile", "personal_email" => "Personal Email", "office_email" => "Office Email", "residence_address" => "Residence Address", "permenant_address" => "Permanent Address", "maritial_status" => "Marital Status", "driving_license" => "Driving License"), 'pluralVar' => 'employees'))); ?>

        
        <style type="text/css">
            #orgc{background-color: #fff;}
            .orgchart{background-image: none !important}
            .orgchart .node .title{height: auto !important}
            .pagination {margin-bottom: 10px !important;}
            .h4class{text-align: center;}
            .orgchart .node{width: auto !important; }
            .orgchart table{width: 100% !important; }
            .chart-container{float: left;}
            .chart-container .content{min-height: 0px !important; float: left;}
            .orgchartcontainer{width: 100%;}
            #print-this{display: block; overflow: auto}

        </style>
        <?php
        echo $this->Html->css(array('jquery.orgchart'));
        echo $this->fetch('css');
        echo $this->Html->script(array('jquery.orgchart'));
        echo $this->fetch('script');
        ?>
        <h4 class="h4class">Employee Organizational Chart <br />
            <small><?php echo $this->Html->link(__('Employee'), array('controller' => 'employees', 'action' => 'org_chart', 'render'=>'h', 'timestamp'=>date('ymdhis'))); ?> | 
            <?php echo $this->Html->link(__('Designation'), array('controller' => 'designations', 'action' => 'org_chart', 'render'=>'h', 'timestamp'=>date('ymdhis'))); ?></small> 
            <br />
            <small>
                <div class="text-center btn-group">
                  <?php 
                  if(isset($this->request->params['named']['render']) && $this->request->params['named']['render'] == 'v'){
                      $vclass = ' btn-success';
                  }else{
                      $vclass = ' btn-default';
                  }

                  if(isset($this->request->params['named']['render']) && $this->request->params['named']['render'] == 'h'){
                      $hclass = ' btn-success';
                  }else{
                      $hclass = ' btn-default';
                  }
                  ?>                  
                  <?php echo $this->Html->link('Verticle',array('action'=>'org_chart','render'=>'v'),array('class'=>'btn' . $vclass));?>
                  <?php echo $this->Html->link('Horizontal',array('action'=>'org_chart','render'=>'h'),array('class'=>'btn'. $hclass));?>
                  <?php echo $this->Html->link('Download PNG', '#',array('class'=>'btn btn-default','id'=>'printBtn'));?>
              </div>
          </small>
      </h4>
      <div class="orgchartcontainer" style="overflow:auto" id="orgc">

       <center>    
        <?php 
        $i = 0;
        $w = count($employees_orgchart);
        foreach ($employees_orgchart as $orgchart) { 
           $t = $t + count($orgchart);
       }

       foreach ($employees_orgchart as $orgchart) { 
          $p = (100 * (count($orgchart)) / $t);
		// echo $p;	
          ?>
          <div id="chart-container-<?php echo $i;?>" class="chart-container" style="overflow:auto;"></div>
          <script type="text/javascript">
            (function($){
                $(function() {
                    var datascource = <?php echo json_encode($orgchart)?>;
                    $('#chart-container-<?php echo $i;?>').orgchart({
                        'data' : datascource,
                        'toggleSiblingsResp': false,
                        'nodeContent': 'title',
                        'nodeID': 'id',                      
                        <?php                           
                        if(isset($this->request->params['named']['render']) && $this->request->params['named']['render'] == 'v'){ ?>
                          'verticalDepth': 3,
                      <?php }else{ ?>
                          
                      <?php } 
                      ?>                        
                      'createNode': function($node, data) {
                        var secondMenuIcon = $('<i>', {
                          'class': 'fa fa-info-circle second-menu-icon',
                          click: function() {
                            $(this).siblings('.second-menu').toggle();
                        }
                    });
                        var secondMenu = '<div class="second-menu"><img class="avatar" src="'+data.imagepath+'"></div>';
                        $node.append(secondMenuIcon).append(secondMenu);
                    }
                })
                });
            })(jQuery);
        </script>    
        <?php $i++; } ?>
    </center>
</div>
</div>
<div id="print-this" style="width: 2480px">
  <img id="screenshot-img" width="100%"/>
</div>

<script>
    $("#printBtn").on("click", function () {
        const element = document.getElementById("orgc");
        html2canvas(element, {
            allowTaint: true,
            useCORS: true
        }).then(function (canvas) {
            const link = document.createElement("a");
            link.download = "emloyee-chart.png";
            link.href = canvas.toDataURL("image/png");
            link.click();
        });
    });
    $.ajaxSetup({beforeSend: function() {
            $("#busy-indicator").show();
        }, complete: function() {
            $("#busy-indicator").hide();
        }
    });
</script>
</div>
