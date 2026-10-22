<style type="text/css">
  #myModal .modal-dialog{width: 90%;}
  /*.chosen{width: 100%;}*/
</style>
<div class="modal fade" tabindex="-1" role="dialog" id="myModal">

  <?php echo $this->Html->script(array('jquery.validate.min', 'jquery-form.min')); ?>
  <?php echo $this->fetch('script'); ?>
  
  <?php 
  $f = 0;
  echo $this->Form->create('CustomTable',array('role'=>'form','class'=>'form')); ?>
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="<?php echo h(__('Close')); ?>"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?php echo h(__('Update Field')); ?></h4>
      </div>
      <div class="modal-body">

        <table class="table table-bordered" id="fieldstable">
          <tbody>
            <tr><th colspan="8"><?php echo h(__('Password')); ?></th></tr>
            <tr><th colspan="8"><?php echo $this->Form->input('CustomTableFields.'.$f.'.password',array('required', 'type'=>'password','class'=>'form-control','label'=>false));?></th></tr>
            <tr>
              <th><?php echo h(__('Field Name')); ?></th>
              <th><?php echo h(__('Linked To')); ?></th>
              <th><?php echo h(__('Display Type')); ?></th>
              <th><?php echo h(__('Field Type')); ?></th>          
              <th><?php echo h(__('Length')); ?></th>
              <th><?php echo h(__('Display Size')); ?></th>
              <th><?php echo h(__('Mandetory?')); ?></th>
              <th><?php echo h(__('Options')); ?></th>
              <th><?php echo h(__('Index')); ?></th>
            </tr>         
            <?php 
        
            $existingField = array_keys($existingField);
            $existingField = json_decode($existingField[0],true);
        
            if($existingField['field_name']){         
              if($existingField['linked_to'] != -1)$key = $existingField['linked_to'];
              else $key = -1;

              echo $this->Form->hidden('CustomTableFields.'.$f.'.pre_fields',array('default'=>json_encode($existingField)));?>

              <tr id="<?php echo $f;?>_tr">
                <td><?php echo $this->Form->input('CustomTableFields.'.$f.'.field_name',array('default'=>$existingField['field_name'], 'label'=>false,'class'=>'form-control','onChange'=>'cleanname(this.value,this.id)'));?></td>
                <td><?php echo $this->Form->input('CustomTableFields.'.$f.'.linked_to',array('disabled','selected'=>$key,'label'=>false,'class'=>'form-control','options'=>$linkedTos));?></td> 
                <td><?php echo $this->Form->input('CustomTableFields.'.$f.'.display_type',array('disabled','selected'=>$existingField['display_type'],'label'=>false,'class'=>'form-control','options'=>$displayTypes,'default'=>0));?></td>
                <td><?php echo $this->Form->input('CustomTableFields.'.$f.'.field_type',array('disabled','default'=>$existingField['field_type'],'label'=>false,'class'=>'form-control','options'=>$fiedTypes,'onChange'=>'checklengths(this.value,this.id,'.$f.')'));?></td>  
                <td><?php echo $this->Form->input('CustomTableFields.'.$f.'.length',array('default'=>$existingField['length'],'label'=>false,'class'=>'form-control'));?></td>  
                <td><?php echo $this->Form->input('CustomTableFields.'.$f.'.size',array('selected'=>$existingField['size'],'label'=>false,'class'=>'form-control','options'=>$bootstrapSizes,'default'=>11));?></td>  
                <td><?php echo $this->Form->input('CustomTableFields.'.$f.'.mandetory',array('selected'=>$existingField['mandetory'],'label'=>false,'class'=>'form-control','options'=>array(0=>__('Yes'),1=>__('No')),'default'=>0));?></td> 
                <td><?php echo $this->Form->input('CustomTableFields.'.$f.'.csvoptions',array('default'=>$existingField['csvoptions'],'placeholder'=>__('yes,no,etc,etc,'), 'label'=>false,'class'=>'validate form-control'));?></td>
                <td><?php echo $this->Form->input('CustomTableFields.'.$f.'.index_show',array('default'=>$existingField['index_show'],'options'=>array(__('Yes'),__('No')), 'label'=>false,'class'=>'validate form-control'));?></td>
              </tr>
              <?php
              $f++;
            }       
            
            ?>
          </tbody>
        </table>

        
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo h(__('Close')); ?></button>
        <?php echo $this->Form->submit(__('Submit'), array('div' => false, 'class' => 'btn btn-primary btn-success','id'=>'submit_id_1')); ?>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
  <?php echo $this->Form->end(); ?>  
</div><!-- /.modal -->
<script type="text/javascript">
  $().ready(function(){
    $('#myModal').modal('show')
    $('select').chosen({"width":"100%"});
  });
</script>

<script>

  $.validator.setDefaults({
    ignore: null,
    errorPlacement: function(error, element) {
      $(element).after(error);
   //     var t = $("#"+id).attr('class');    
      // if(t.includes('select')){
      //  alert('yes');
      // }
    },
  });
  
  $().ready(function() {    
    


    $('#CustomTableUpdateFieldForm').validate();

    $('select').each(function() { 
      if($(this).prop('required') == true){
        $(this).rules('add', {
          greaterThanZero: true
        });
      }
      
    });   

    
    $("#submit-indicator").hide();
    $("#submit_id").click(function(){
      if($('#CustomTableUpdateFieldForm').valid()){
       $("#submit_id").prop("disabled",true);
       $("#submit-indicator").show();
       $('#CustomTableUpdateFieldForm').submit();
     }

   });
    

  });
</script>


