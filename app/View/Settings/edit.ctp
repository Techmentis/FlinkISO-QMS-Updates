<script>
    $().ready(function() {
        $("#submit-indicator").hide();
        $("#submit_id").click(function() {
            if ($('#SettingEditForm').valid()) {
                $("#submit_id").prop("disabled", true);
                $("#submit-indicator").show();
                $("#SettingEditForm").submit();
            }
        });
        <?php if ($this->data['Setting']['logo'] == 0){ ?>
            $("#customLogo").hide();
        <?php } else { ?>
           $("#customLogo").show();
       <?php } ?>
       $('input:radio[name="data[Setting][logo]"]').change(function(){
        var logoType = $('input:radio[name="data[Setting][logo]"]:checked').val();
        if(logoType == 0){
            $("#customLogo").hide();
        }else{
            $("#customLogo").show();
        }
    });
   });

</script>
<div id="companies_ajax">
    <?php echo $this->Session->flash(); ?>
    <div class="nav panel panel-default">
        <div class="companies form col-md-12 panel">
            <h4><?php echo __('Edit Setting'); ?></h4>
            <?php 
            $logo = WWW_ROOT . DS . 'img' . DS . 'logo'. DS . $this->request->data['Setting']['company_logo'];
            if(file_exists($logo)){
                echo $this->Html->image('logo'. DS . $this->request->data['Setting']['company_logo']);
            }

            ?>

            <?php echo $this->Form->create('Setting', array('role' => 'form', 'class' => 'form', 'type' => 'file')); ?>
            <?php echo $this->Form->input('id'); ?>
            <div class="row">
                <div class="col-md-12"><?php echo $this->Form->input('name', array('disabled','class'=>'form-control')); ?></div>
            </div>
            <div class="row">
                <div class="col-md-12">   
                    <?php echo $this->Form->input('logo', array('type' => 'radio', 'options' => array(0 => 'Default', 1 => 'Custom Logo'))); ?>
                </div>                
                <div id="customLogo" class="col-md-12">                                        
                    <span class='control-fileupload'><i class='fa fa-file-o'></i><?php echo $this->Form->input('company_logo',array('class'=>$block_class ,  'type'=>'file', 'label'=>'Upload Company Logo', 'div'=>false , 'onchange'=>'$(this).prev("label").html($(this).val().substr(12));'));?></span>
                    <p class="help-text">Acceptable image formats are 'jpg/jpeg,png'</p>
                </div>
            </div>
            <?php echo $this->Form->hidden('History.pre_post_values', array('value'=>json_encode($this->data)));
            echo $this->Form->submit(__('Submit'), array('div' => false, 'class' => 'btn btn-primary btn-success','id'=>'submit_id'));?>
            <?php echo $this->Html->image('indicator.gif', array('id' => 'submit-indicator')); ?>
            <?php echo $this->Form->end(); ?>
            <?php echo $this->Js->writeBuffer(); ?>
        </div>        
    </div>
</div>
