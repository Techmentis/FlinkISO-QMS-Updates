<div  id="main">
    <?php echo $this->Session->flash(); ?>
    <div class="employees ">
        <?php echo $this->element('nav-header-lists',array('postData'=>array('pluralHumanName'=>'Designations','modelClass'=>'Designation','options'=>array("sr_no"=>"Sr No","name"=>"Name","level"=>"Level"),'pluralVar'=>'designations'))); ?>


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
        <h4 class="h4class">Organizational Chart <br />
            <small>
                <div class="text-center btn-group">                  
                  <?php // echo $this->Html->link('Export PDF','#',array('class'=>'btn btn-success','onClick'=>'getpdf();','escape'=>false));?>
                  <?php echo $this->Html->link('Designation',array('controller'=>'designations', 'action'=>'org_chart','render'=>'h'),array('class'=>'btn btn-primary'));?>
                  <?php echo $this->Html->link('Employee',array('controller'=>'employees','action'=>'org_chart','render'=>'h'),array('class'=>'btn btn-default'));?>
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
    function takeHighResScreenshot(srcEl, destIMG, scaleFactor) {
        // Save original size of element
        var originalWidth = srcEl.offsetWidth;
        var originalHeight = srcEl.offsetHeight;
        // Force px size (no %, EMs, etc)
        srcEl.style.width = originalWidth + "px";
        srcEl.style.height = originalHeight + "px";

        // Position the element at the top left of the document because of bugs in html2canvas. The bug exists when supplying a custom canvas, and offsets the rendering on the custom canvas based on the offset of the source element on the page; thus the source element MUST be at 0, 0.
        // See html2canvas issues #790, #820, #893, #922
        srcEl.style.position = "absolute";
        srcEl.style.top = "0";
        srcEl.style.left = "0";

        // Create scaled canvas
        var scaledCanvas = document.createElement("canvas");
        scaledCanvas.width = originalWidth * scaleFactor;
        scaledCanvas.height = originalHeight * scaleFactor;
        scaledCanvas.style.width = originalWidth + "px";
        scaledCanvas.style.height = originalHeight + "px";
        var scaledContext = scaledCanvas.getContext("2d");
        scaledContext.scale(scaleFactor, scaleFactor);

        html2canvas(srcEl, { canvas: scaledCanvas })
        .then(function(canvas) {
            destIMG.src = canvas.toDataURL("image/png");
            srcEl.style.display = "none";
        });
    }
    
    // function getpdf(){
    //   var src = document.getElementById("orgc");
    //   var img = document.getElementById("screenshot-img");
    //   canvas = takeHighResScreenshot(src, img, 2);
    //   // print_png(); 
    
    // }

    function getpdf(){
      // alert('hey');
      // var blob = new Blob(["Hello, world!"], {type: "text/plain;charset=utf-8"});
      // saveAs(blob, "hello world.txt");
      
      var src = document.getElementById("orgc");
      var img = document.getElementById("screenshot-img");
      canvas = takeHighResScreenshot(src, img, 4);
      $("#screenshot-img").load(function(){
        $("#print-this").width = $("#screenshot-img").width();
        $("#print-this").height = $("#screenshot-img").height();
        
        html2canvas($("#print-this"), {
          width : $("#screenshot-img").width(),
          height : $("#screenshot-img").height(),
          background : '#fff',
          onrendered: function(canvas) {
            theCanvas = canvas;
                // document.body.appendChild(canvas);

            canvas.toBlob(function(blob) {
                saveAs(blob, "org_chart.png"); 
            });
        }
    });
    });
      
  }

  $.ajaxSetup({beforeSend: function() {
    $("#busy-indicator").show();
}, complete: function() {
    $("#busy-indicator").hide();
}
});
</script>
</div>
