<div class="box box-info">
  <div class="box-header " style="background-color: #fefefe; border-bottom: 1px dotted #ccc;">
    <h3 class="box-title"><?php echo h(__('What Next?')); ?> </h3>
    <div class="pull-right">        
      <?php if($masters['User']['qc_documents'] == 0)echo "<strong>".$this->Html->link(__('Add Initial Documents'),array('controller'=>'qc_documents','action'=>'add_bulk'),array('class'=>'btn btn-sm btn-info'))."</strong>";?>
    </div>
  </div>
  <div class="box-body" >
    <p><?php echo h(__('We have already imported our existing dummy documents')); ?> & <?php echo h(__('HTML forms to the system. You can update those documents by uploading your own documents or editing those documents using ONLYOFFICE')); ?>&trade; <?php echo h(__('Editors. You may also want to re-create existing HTML forms using FlinkISO Drag-n-Drop APIs. Note that the existing HTML forms are completely customisable. You can add/ edit/ remove fields, change their placements, size or labels.')); ?> </p>

    <?php 
    
    $txt2 = $txt3 = $txt4 = '';
    $show = true;
    if($masters['User']['branches'] == 20 || $masters['User']['departments'] == 20 || $masters['User']['designations'] == 1 || $masters['User']['employees'] == 20 || $masters['User']['users'] == 20){
      $class2 = 'danger';
      $class3 = 'danger';
      $class4 = 'danger';  
      $txt2 = __('Pending : Add Masters | You have not added all the masters yet.');  
      $txt3 = __('Pending : Add Documents / Processes');  
      $txt4 = __('Pending : Build Custom Tables');  
    }else if($masters['User']['qc_documents'] < 20){
      $class2 = 'success';
      $class3 = 'danger';
      $class4 = 'danger';  
      $txt2 = __('Masters are ready.');  
      $txt3 = __('Pending : Add Documents / Processes | You have not added any document yet.');  
      $txt4 = __('Pending : Build Custom Tables.');  
    }else if($masters['User']['custom_tables'] < 20){
      $class2 = 'success';
      $class3 = 'success';
      $class4 = 'danger';
      $txt2 = __('Masters are ready.'); 
      $txt3 = __('Documents are added.');  
      $txt4 = __('Pending : Build Custom Tables | You have not added any custom forms yet.');  
    }else{
      $class2 = 'success';
      $class3 = 'success';
      $class4 = 'success';
      $txt2 = __('Masters added');
      $txt3 = __('Documents / Proceses added');
      $txt4 = __('Forms are created.');
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
                <li class="tooltip1" data-toggle = "tooltip" data-trigger = "hover" data-placement = "bottom" title = "<?php echo h(__('Sign-up')); ?>">1</li>
                <li class="<?php echo $class2;?> tooltip1" data-toggle = "tooltip" data-trigger = "hover" data-placement = "bottom" title = "<?php echo $txt2;?>">2</li>
                <li class="<?php echo $class3;?> tooltip1" data-toggle = "tooltip" data-trigger = "hover" data-placement = "bottom" title = "<?php echo $txt3;?>">3</li>
                <li class="last <?php echo $class4;?> tooltip1" data-toggle = "tooltip" data-trigger = "hover" data-placement = "bottom" title = "<?php echo $txt4;?>">4</li>
              </ul>    
            </div>
          </div>    
          <div class="col-md-12">
            <div class="row">
              <div class="col-md-5">
                <p><h4><?php echo h(__('Getting started')); ?></h4></p>
                <p><?php echo h(__('In order to setup and start using the application, you must add branches/ departments/ employees/ designations/ standards')); ?> & <?php echo h(__('clauses/ documents etc. Follow these step-by-step guide below to kick start your QMS implementation.')); ?></p>
                <ul>          
                  <li><a href="https://www.flinkiso.com/videos/qms-application/qms-introduction.html" target="_blank" title="<?php echo h(__('QMS Introduction')); ?>"><?php echo h(__('QMS Introduction')); ?></a></li>
                  <li><a href="https://www.flinkiso.com/videos/qms-application/adding-masters-part-1.html" target="_blank" title="<?php echo h(__('Adding masters to QMS')); ?>"><?php echo h(__('Adding Masters Part -1')); ?></a></li>
                  <li><a href="https://www.flinkiso.com/videos/qms-application/adding-masters-part-2.html" target="_blank" title="<?php echo h(__('Adding employees to QMS')); ?>"><?php echo h(__('Adding Masters Part -2')); ?></a></li>
                  <li><a href="https://www.flinkiso.com/videos/qms-application/user-creation.html" target="_blank" title="<?php echo h(__('User Creation')); ?>"><?php echo h(__('Adding Users')); ?></a></li>
                  <li><a href="https://www.flinkiso.com/videos/qms-application/adding-standards-clauses.html" target="_blank" title="<?php echo h(__('Adding Standards and Clauses')); ?>"><?php echo h(__('Standards and Clauses')); ?></a></li>            
                  <li><a href="https://www.flinkiso.com/videos/document-management/upload-document.html" target="_blank" title="<?php echo h(__('Upload QMS Document or Spreadsheet')); ?>"><?php echo h(__('Upload Document or Spreadsheet')); ?></a></li>
                  <li><a href="https://www.flinkiso.com/videos/document-management/document-creation-from-template.html" target="_blank" title="<?php echo h(__('QMS Document Creation From Template')); ?>"><?php echo h(__('Document Creation From Template')); ?></a></li>
                  <li><a href="https://www.flinkiso.com/videos/document-management/document-version-control.html" target="_blank" title="<?php echo h(__('QMS Document Version Control')); ?>"><?php echo h(__('Document Version Control')); ?></a></li>
                  <li><a href="https://www.flinkiso.com/videos/document-management/generate-digitally-signed-pdf.html" target="_blank" title="<?php echo h(__('Digitally Signed PDF')); ?>"><?php echo h(__('Digitally Signed PDFs')); ?></a></li>            
                  <li><a href="https://www.flinkiso.com/videos/build-html-form/basic-form-creation.html" target="_blank" title="<?php echo h(__('Build Custom HTML Form')); ?>"><?php echo h(__('Build HTML Form')); ?></a></li>
                  <li><a href="https://www.flinkiso.com/videos/build-html-form/create-and-link-masters-with-html-form.html" target="_blank" title="<?php echo h(__('Create and Link Masters with HTML Form')); ?>"><?php echo h(__('Link Masters')); ?></a></li>

                  
                </ul>
              </div>
              <div class="col-md-3">
                <h4><?php echo h(__('Ready Modules')); ?></h4>  
                <p><?php echo h(__('Some of these modules are already installed. You can click on the links below to learn how to use them or customise them. Note that,Non Conformity,Corrective Actions, Document Change Control module needs to be installed manually.')); ?></p>              
                <ul>
                  <li><a href="https://www.flinkiso.com/quality-management-software/audits.html" title="<?php echo h(__('QMS Audits')); ?>" target="_blank"><?php echo h(__('Audit Management')); ?></a></li>
                  <li><a href="https://www.flinkiso.com/quality-management-software/management-review.html" title="<?php echo h(__('Management Review')); ?>" target="_blank"><?php echo h(__('Management Review')); ?></a></li>
                  <li><a href="https://www.flinkiso.com/quality-management-software/change-control.html" title="<?php echo h(__('qms change requests')); ?>" target="_blank"><?php echo h(__('Change Control')); ?></a></li>
                  <li><a href="https://www.flinkiso.com/quality-management-software/customer-complaints.html" title="<?php echo h(__('Customer Complaints')); ?>" target="_blank"><?php echo h(__('Customer Complaints')); ?></a></li>
                  <li><a href="https://www.flinkiso.com/quality-management-software/non-conformities.html" title="<?php echo h(__('Non Conformities')); ?>" target="_blank"><?php echo h(__('Non Conformity')); ?></a></li>
                  <li><a href="https://www.flinkiso.com/quality-management-software/corrective-actions.html" title="<?php echo h(__('Corrective Actions')); ?>" target="_blank"><?php echo h(__('Corrective Actions')); ?></a></li>
                  <li><a href="https://www.flinkiso.com/quality-management-software/employee-training-records.html" title="<?php echo h(__('Employee Training Records')); ?>" target="_blank"><?php echo h(__('Employee Training Records')); ?></a></li>
                  <li><a href="https://www.flinkiso.com/quality-management-software/supplier-details.html" title="<?php echo h(__('Supplier Detail Records')); ?>" target="_blank"><?php echo h(__('Supplier Details')); ?></a></li>
                  <li><a href="https://www.flinkiso.com/quality-management-software/device-equipment.html" title="<?php echo h(__('Device Equipment Records')); ?>" target="_blank"><?php echo h(__('Device/Equipment Records')); ?></a></li>
                  <li><a href="https://www.flinkiso.com/quality-management-software/device-calibration.html" title="<?php echo h(__('Device Equipment Records')); ?>" target="_blank"><?php echo h(__('Device/Equipment Calibration')); ?></a></li>
                  <li><a href="https://www.flinkiso.com/manual/building-document-change-request-form.html" title="<?php echo h(__('Document Change Control')); ?>" target="_blank"><?php echo h(__('Document Change Control')); ?></a></li>
                </ul>
              </div>
              <div class="col-md-4">
                <h4><?php echo h(__('Types of HTML Forms You can create')); ?></h4> 
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
      <small class="pull-left"><?php echo h(__('Contact help@flinkiso.com or')); ?> <br /><a href="https://forum.flinkiso.com/" target="_blank">https://forum.flinkiso.com/</a> <?php echo h(__('for help.')); ?></small>
      <a href="https://www.flinkiso.com/services/startup-plan.html" target="_blank" class="text-blue">
        <h4 class="pull-right"><i class="fa fa-bolt fa-lg"></i> <?php echo h(__('Try New Start-Up Plan For Assured Implementation!')); ?></h4></a>
    </div>
  </div>
</div>
