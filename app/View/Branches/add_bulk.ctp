<?php echo $this->Html->script(array('jquery.validate.min', 'jquery-form.min')); ?>
<?php echo $this->fetch('script'); ?><div id="branches_ajax">
    <?php echo $this->Session->flash(); ?>	

    <div class="branches ">
      <?php echo $this->element('nav-header-lists', array('postData' => array('pluralHumanName' => 'Branches', 'modelClass' => 'Branch', 'options' => array(), 'pluralVar' => 'branches'))); ?>            
      <div class="nav panel panel-default">
        <div class="branches form col-md-12">
           <?php echo $this->Form->create('Branch', array('role' => 'form', 'class' => 'form')); ?>
           <div class="row">
             <?php
             $str =
             "Factory
             Branch 2
             Branch 3
             Branch 4
             .
             .
             .
             ";
             echo "<div class='col-md-12'>" . $this->Form->input('name', array('type'=>'textarea', 'class' => 'form-control','label'=>'Add multiple branches/locations','placeholder'=>$str)) . '</div>';
             echo "<div class='col-md-12 help-text'>Add branch/loction with < enter > as new record.</div>";
             ?>
             <?php
             echo $this->Form->input('id');
             echo $this->Form->hidden('History.pre_post_values', array('value' => json_encode($this->data)));
             echo $this->Form->input('branchid', array('type' => 'hidden', 'value' => $this->Session->read('User.branch_id')));
             echo $this->Form->input('departmentid', array('type' => 'hidden', 'value' => $this->Session->read('User.department_id')));
             echo $this->Form->input('master_list_of_format_id', array('type' => 'hidden', 'value' => $documentDetails['MasterListOfFormat']['id']));
             ?>

         </div>
         <div class="">
            <?php echo $this->Form->input('publish');?>
            <?php echo $this->Form->submit(__('Submit'), array('div' => false, 'class' => 'btn btn-primary btn-success', 'id' => 'submit_id')); ?>
            <?php echo $this->Html->image('indicator.gif', array('id' => 'submit-indicator')); ?>
            <?php echo $this->Form->end(); ?>

            <?php echo $this->Js->writeBuffer(); ?>
        </div>
    </div>

</div>
</div>
<script>
    $.validator.setDefaults({
    	ignore: null,
    	errorPlacement: function(error, element) {
            if(element['context']['className'] == 'form-control select error'){
                $(element).next().after(error); 		
            }else{
                $(element).after(error); 
            }
        },
    });
    
    $().ready(function() {

        $('select').chosen();

        jQuery.validator.addMethod("greaterThanZero", function(value, element) {
            return this.optional(element) || $("#"+element.id+" option:selected").text() != 'Select';
        }, "Please select the value");

        $('#BranchAddBulkForm').validate({        	
            rules: {
                
            }
        }); 
        
        $("#submit-indicator").hide();
        $("#submit_id").click(function(){
            if($('#BranchAddBulkForm').valid()){
               $("#submit_id").prop("disabled",true);
               $("#submit-indicator").show();
               $('#BranchAddBulkForm').submit();
           }

       });
        

    });
</script>
