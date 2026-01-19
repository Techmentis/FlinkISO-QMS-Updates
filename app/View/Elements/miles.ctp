<div class="box box-info">
  <div class="box-header " style="background-color: #fefefe; border-bottom: 1px dotted #ccc;">
    <h3 class="box-title">What Next? </h3>
    <div class="pull-right">        
      <?php if($masters['User']['qc_documents'] == 0)echo "<strong>".$this->Html->link('Add Initial Documents',array('controller'=>'qc_documents','action'=>'add_bulk'),array('class'=>'btn btn-sm btn-info'))."</strong>";?>
    </div>
  </div>
  <div class="box-body" >
    <p>We have already imported our existing dummy documents & HTML forms to the system. You can update those documents by uploading your own documents or editing those documents using ONLYOFFICE&trade; Editors. You may also want to re-create existing HTML forms using FlinkISO Drag-n-Drop APIs. Note that the existing HTML forms are completely customisable. You can add/ edit/ remove fields, change their placements, size or labels. </p>

    <?php 
    
    $txt2 = $txt3 = $txt4 = '';
    $show = true;
    if($masters['User']['branches'] == 20 || $masters['User']['departments'] == 20 || $masters['User']['designations'] == 1 || $masters['User']['employees'] == 20 || $masters['User']['users'] == 20){
      $class2 = 'danger';
      $class3 = 'danger';
      $class4 = 'danger';  
      $txt2 = 'Pending : Add Masters | You have not added all the masters yet.';  
      $txt3 = 'Pending : Add Documents / Processes';  
      $txt4 = 'Pending : Build Custom Tables';  
    }else if($masters['User']['qc_documents'] < 20){
      $class2 = 'success';
      $class3 = 'danger';
      $class4 = 'danger';  
      $txt2 = 'Masters are ready.';  
      $txt3 = 'Pending : Add Documents / Processes | You have not added any document yet.';  
      $txt4 = 'Pending : Build Custom Tables.';  
    }else if($masters['User']['custom_tables'] < 20){
      $class2 = 'success';
      $class3 = 'success';
      $class4 = 'danger';
      $txt2 = 'Masters are ready.'; 
      $txt3 = 'Documents are added.';  
      $txt4 = 'Pending : Build Custom Tables | You have not added any custom forms yet.';  
    }else{
      $class2 = 'success';
      $class3 = 'success';
      $class4 = 'success';
      $txt2 = 'Masters added';
      $txt3 = 'Documents / Proceses added';
      $txt4 = 'Forms are created.';
      $show = false;
    }

    ?>
    <?php if($show){ ?>
      <style type="text/css">
        .miles{
          background-image: url('<?php echo Router::url('/', true); ?>img/hline.svg'); background-repeat: repeat-x;
          }
          .well{
            float: left;
            width: 100%;
          }
        </style>
        <div class="row" style="margin-top:25px">          
          <?php if($txt){ ?>
            <div class="col-md-12 hide">
              <div class="callout callout-default"><?php echo $txt;?></div>
            </div>
          <?php } ?>
          <div class="col-md-12">    
            <div class="">
              <ul class="miles">
                <li class="tooltip1" data-toggle = "tooltip" data-trigger = "hover" data-placement = "bottom" title = "Sign-up">1</li>
                <li class="<?php echo $class2;?> tooltip1" data-toggle = "tooltip" data-trigger = "hover" data-placement = "bottom" title = "<?php echo $txt2;?>">2</li>
                <li class="<?php echo $class3;?> tooltip1" data-toggle = "tooltip" data-trigger = "hover" data-placement = "bottom" title = "<?php echo $txt3;?>">3</li>
                <li class="last <?php echo $class4;?> tooltip1" data-toggle = "tooltip" data-trigger = "hover" data-placement = "bottom" title = "<?php echo $txt4;?>">4</li>
              </ul>    
            </div>
          </div>    
          <div class="col-md-12">
            <div class="row">
              <div class="col-md-5">
                <p><h4>Getting started</h4></p>
                <p>In order to setup and start using the application, you must add branches/ departments/ employees/ designations/ standards & clauses/ documents etc. Follow these step-by-step guide below to kick start your QMS implementation.</p>
                <ul>          
                  <li><a href="https://www.flinkiso.com/videos/qms-application/qms-introduction.html" target="_blank" title="QMS Introduction">QMS Introduction</a></li>
                  <li><a href="https://www.flinkiso.com/videos/qms-application/adding-masters-part-1.html" target="_blank" title="Adding masters to QMS">Adding Masters Part -1</a></li>
                  <li><a href="https://www.flinkiso.com/videos/qms-application/adding-masters-part-2.html" target="_blank" title="Adding employees to QMS">Adding Masters Part -2</a></li>
                  <li><a href="https://www.flinkiso.com/videos/qms-application/user-creation.html" target="_blank" title="User Creation">Adding Users</a></li>
                  <li><a href="https://www.flinkiso.com/videos/qms-application/adding-standards-clauses.html" target="_blank" title="Adding Standards and Clauses">Standards and Clauses</a></li>            
                  <li><a href="https://www.flinkiso.com/videos/document-management/upload-document.html" target="_blank" title="Upload QMS Document or Spreadsheet">Upload Document or Spreadsheet</a></li>
                  <li><a href="https://www.flinkiso.com/videos/document-management/document-creation-from-template.html" target="_blank" title="QMS Document Creation From Template">Document Creation From Template</a></li>
                  <li><a href="https://www.flinkiso.com/videos/document-management/document-version-control.html" target="_blank" title="QMS Document Version Control">Document Version Control</a></li>
                  <li><a href="https://www.flinkiso.com/videos/document-management/generate-digitally-signed-pdf.html" target="_blank" title="Digitally Signed PDF">Digitally Signed PDFs</a></li>            
                  <li><a href="https://www.flinkiso.com/videos/build-html-form/basic-form-creation.html" target="_blank" title="Build Custom HTML Form">Build HTML Form</a></li>
                  <li><a href="https://www.flinkiso.com/videos/build-html-form/create-and-link-masters-with-html-form.html" target="_blank" title="Create and Link Masters with HTML Form">Link Masters</a></li>

                  
                </ul>
              </div>
              <div class="col-md-3">
                <h4>Ready Modules</h4>  
                <p>Some of these modules are already installed. You can click on the links below to learn how to use them or customise them. Note that,Non Conformity,Corrective Actions, Document Change Control module needs to be installed manually.</p>              
                <ul>
                  <li><a href="https://www.flinkiso.com/quality-management-software/audits.html" title="QMS Audits" target="_blank">Audit Management</a></li>
                  <li><a href="https://www.flinkiso.com/quality-management-software/management-review.html" title="Management Review" target="_blank">Management Review</a></li>
                  <li><a href="https://www.flinkiso.com/quality-management-software/change-control.html" title="qms change requests" target="_blank">Change Control</a></li>
                  <li><a href="https://www.flinkiso.com/quality-management-software/customer-complaints.html" title="Customer Complaints" target="_blank">Customer Complaints</a></li>
                  <li><a href="https://www.flinkiso.com/quality-management-software/non-conformities.html" title="Non Conformities" target="_blank">Non Conformity</a></li>
                  <li><a href="https://www.flinkiso.com/quality-management-software/corrective-actions.html" title="Corrective Actions" target="_blank">Corrective Actions</a></li>
                  <li><a href="https://www.flinkiso.com/quality-management-software/employee-training-records.html" title="Employee Training Records" target="_blank">Employee Training Records</a></li>
                  <li><a href="https://www.flinkiso.com/quality-management-software/supplier-details.html" title="Supplier Detail Records" target="_blank">Supplier Details</a></li>
                  <li><a href="https://www.flinkiso.com/quality-management-software/device-equipment.html" title="Device Equipment Records" target="_blank">Device/Equipment Records</a></li>
                  <li><a href="https://www.flinkiso.com/quality-management-software/device-calibration.html" title="Device Equipment Records" target="_blank">Device/Equipment Calibration</a></li>
                  <li><a href="https://www.flinkiso.com/manual/building-document-change-request-form.html" title="Document Change Control" target="_blank">Document Change Control</a></li>
                </ul>
              </div>
              <div class="col-md-4">
                <h4>Types of HTML Forms You can create</h4> 
                <?php 
                echo $this->Html->link($this->Html->Image('structure.png',array('class'=>'img-responsive')),array('controller'=>'img','action'=>'structure.png'),array('target'=>'_blank', 'class'=>'img-responsive','escape'=>false));?>    
              </div>
            </div>
            
          </div>
        </div>
        

        <script type="text/javascript">

          $(window).on('resize', function(){
            resize();
          });

          $().ready(function(){
            resize();
          });

          function resize(){
            var w = $(".miles").width();
            $(".miles li").attr('style','margin-right:'+ Number(((w/3) - 55))+'px !important');
            $(".last").attr('style','margin-right:0px !important');
          }
        </script>
      <?php } ?>
    </div>  
    <div class="box-footer">
      <small class="pull-left">Contact help@flinkiso.com or <br /><a href="https://forum.flinkiso.com/" target="_blank">https://forum.flinkiso.com/</a> for help.</small>
      <a href="https://www.flinkiso.com/services/startup-plan.html" target="_blank" class="text-blue">
        <h4 class="pull-right"><i class="fa fa-bolt fa-lg"></i> Try New Start-Up Plan For Assured Implementation!</h4></a>
    </div>
  </div>
</div>
