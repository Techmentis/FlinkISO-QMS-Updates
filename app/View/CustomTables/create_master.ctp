<div class="modal fade" tabindex="-1" role="dialog" id="myModalCreateMaster">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Create Masters</h4>
    </div>

    <div class="modal-body create-master-modal-body">
     <?php echo $this->Html->script(array('jquery.validate.min', 'jquery-form.min')); ?>
     <?php echo $this->fetch('script'); ?>
     <?php echo $this->Form->create('CustomTable',array('role'=>'form','class'=>'form','default' => false)); ?>
     <div class="row">
        <div class="col-md-12">Table Name must be plural (e.g. employees, users etc). Table Name must not start or end with number.</div>
        <?php echo "<div class='col-md-6'>".$this->Form->input('name',array('class'=>'form-control','required','label'=>'Name : <small>e.g Units/ Currency/ Type etc.</small>')) . '</div>'; ?>	
        <?php echo "<div class='col-md-3'>".$this->Form->input('password',array('type'=>'password', 'class'=>'form-control',)) . '</div>'; ?>
        <?php echo "<div class='col-md-3'>".$this->Form->input('re-password',array('type'=>'password', 'class'=>'form-control',)) . '</div>'; ?>	
        <div class="col-md-12">
           <br /><br />
           <div class="alert alert-warning">Please note : This feature is only available for creating masters. This will create a form with single field viz. Name. You can add required data to it later. Masters are typically datasets like list of states, cities, several types like units, curriencies etc. Use this feature carefully.</div>
       </div>
   </div>
   <?php echo $this->Form->end();?>
</div>
<div class="modal-footer">        
    <div class=""><?php echo $this->Form->submit(__('Submit'), array('div' => false, 'class' => 'btn btn-primary btn-success','id'=>'cust_submit_id')); ?></div>
    <?php echo $this->Html->image('indicator.gif', array('id' => 'cust-submit-indicator')); ?>
</div>
</div>
</div>
</div>

<script type="text/javascript">
	$.validator.setDefaults({
       ignore: null,
       errorPlacement: function(error, element) {
          $(element).after(error);
      },
  });

	$.validator.setDefaults({
        submitHandler: function(form) {
            $(form).ajaxSubmit({                                
                url: "<?php echo Router::url('/', true); ?><?php echo $this->request->params['controller'] ?>/create_master",
                type: 'POST',                
                beforeSend: function(){
                    console.log('ok');
                    $("#cust_submit_id").prop("disabled",true);
                    $("#cust_submit_id").show();
                    $("#cust-submit-indicator").show();
                },error: function(request, status, error) {
                    alert('Action failed!');
                },success: function(data, status, xhr) {
                    
                    if(data == 'Table already exists'){
                        $(".create-master-modal-body").html('Table already exists.Please select another name.');
                        $("#cust-submit-indicator").hide();    
                    }else if(data == 'SQL failed'){                        
                        $(".create-master-modal-body").html('System failed to run SQL successfully. Please try again with different name.');
                        $("#cust-submit-indicator").hide();    
                    }else{
                        $("#linkedTos").val(data);
                        $(".create-master-modal-body").html('Master Table Added.');
                        $("#cust-submit-indicator").hide();    
                    }
                },
                complete: function() {
                 $("#cust_submit_id").removeAttr("disabled");
                 $("#cust_submit_id").hide();
                 $("#cust-submit-indicator").hide();
             }
         });
        }
    });

	$().ready(function(){
		$('#myModalCreateMaster').modal('show');
		$("#cust-submit-indicator").hide();
		$("#cust_submit_id").click(function(){
            if($('#CustomTableCreateMasterForm').valid()){
               $("#cust_submit_id").prop("disabled",true);
               $("#cust-submit-indicator").show();
               $('#CustomTableCreateMasterForm').submit();
           }

       });
	});
</script>
