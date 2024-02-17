<?php if($formatsforissues){ ?> 
  <?php echo $this->Html->script(array('jquery-form.min')); ?>
  <?php echo $this->fetch('script'); ?>
  <div class="row">
    <div class="col-md-12">
      <?php if($this->Session->read('User.is_mr') == false )echo "<h3>". __('Welcome '). $this->Session->read('User.name') ." <small> " . $this->Session->read('User.branch') ." " .__('Branch')." / " .$this->Session->read('User.department'). " ". __('Department')." </small></h3>"; ?>
      <div class="box box-danger box-solid">
        <div class="box-header with-border">
          <h3 class="panel-title"><?php echo __("Pending Documents for Issue"); ?> <span class="badge btn-danger pull-right"><?php echo count($formatsforissues); ?></span></h3>
        </div>
        <div class="box-body">
          <table class="table table-condensed">
            <tr>
              <th><?php echo __("Document Number"); ?></th>
              <th><?php echo __("Document Title"); ?></th>
              <th><?php echo __("Date/Time"); ?></th>
              <th width="42"><?php echo __("Act"); ?></th>
              <th>Issue</th>
            </tr>
            <?php foreach ($formatsforissues as $formatsforissue): ?>
              <tr id="tr-<?php echo $formatsforissue['MasterListOfFormat']['id']?>">
                <td><?php echo $formatsforissue['MasterListOfFormat']['document_number']; ?></td>
                <td><?php echo $formatsforissue['MasterListOfFormat']['title']; ?></td>
                <td><?php echo $formatsforissue['MasterListOfFormat']['modified']; ?></td>
                <td>
                  <?php echo $this->Html->link(__('Act'), array('controller' => 'master_list_of_formats', 'action' => 'issue', 
                  $formatsforissue['MasterListOfFormat']['id']), array('class' => 'badge btn-danger')) ?>
                </td>
                <td>
                  <?php echo $this->Form->create('MasterListOfFormat', array(
                    'action'=>'issue',
                    'id'=>'issue-'.$formatsforissue['MasterListOfFormat']['id'],
                    'default'=> false,
                  ), 
                  array(
                    'role' => 'form', 
                    'class' => 'form' , 
                    
                  )); ?>
                  <?php 
                  echo $this->Form->input('id',array('default'=>$formatsforissue['MasterListOfFormat']['id']));
                  echo $this->Form->submit(__('Issue Document'), 
                    array('label'=>'Issue', 'div' => false, 'class' => 'btn btn-xs btn-primary btn-success',
                      'id'=>'submit_id_'.$formatsforissue['MasterListOfFormat']['id']
                    ));?>
                    <?php echo $this->Html->image('indicator.gif', array('id' => 'submit-indicator-'.$formatsforissue['MasterListOfFormat']['id'])); ?>
                    <?php echo $this->Form->end(); ?>
                    <?php echo $this->Js->writeBuffer(); 
                    ?>
                  </td>
                </tr>
                
                <script type="text/javascript">
                 $.ajaxSetup({beforeSend: function() {
                  $("#submit-indicator-<?php echo $formatsforissue['MasterListOfFormat']['id']?>").show();
                }, complete: function() {
                  $("#submit-indicator-<?php echo $formatsforissue['MasterListOfFormat']['id']?>").hide();
                  // $("#tr-<?php echo $formatsforissue['MasterListOfFormat']['id']?>").html('Document Issued');

                }
              });
                 $().ready(function(){
                  $("#submit_id_<?php echo $formatsforissue['MasterListOfFormat']['id']?>").on('click',function(){
              // alert('a');
                    $("#issue-<?php echo $formatsforissue['MasterListOfFormat']['id']?>").ajaxSubmit({
                      url: "<?php echo Router::url('/', true); ?>master_list_of_formats/issue",
                      type: 'POST',
                      target: '#tr-<?php echo $formatsforissue['MasterListOfFormat']['id']?>',
                      dataType:"html", 
                      evalScripts:true, 
                      async: true,
                      beforeSend: function(){
                       $("#submit_id_<?php echo $formatsforissue['MasterListOfFormat']['id']?>").prop("disabled",true);
                       $("#submit-indicator-<?php echo $formatsforissue['MasterListOfFormat']['id']?>").show();
                     },
                     complete: function() {
                     // $("#submit_id_<?php echo $formatsforissue['MasterListOfFormat']['id']?>").removeAttr("disabled");
                       $("#submit-indicator-<?php echo $formatsforissue['MasterListOfFormat']['id']?>").hide();
                       $("#tr-<?php echo $formatsforissue['MasterListOfFormat']['id']?>").html('<td colspan="4">Document Issued</td>');
                     },
                     error: function(request, status, error) {
                      //alert(request.responseText);
                      alert('Action failed!');
                    }
                  });
                  });
                });
                 $("#submit-indicator-<?php echo $formatsforissue['MasterListOfFormat']['id']?>").hide();
               </script>
             <?php endforeach ?>
           </table>
         </div>
       </div>
     </div>
   </div>
   <?php }?>
