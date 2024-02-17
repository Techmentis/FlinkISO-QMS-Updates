<?php echo $this->Html->script(array('sign')); ?>
<style type="text/css">
    #submit-indicator-add{position: relative !important;}#signature-alert{/*border-radius: 5px;*/margin: 30px 15px 0 -15px;float: left;padding: 7px;/*width: 96%;*//*background-color: #f9f9f9 !important;border: 1px solid #ccc !important;color : #000 !important;*/}.wrapper1,.wrapper2 {border: 1px solid #ccc;position: relative;width: 352px;height: 152px;-moz-user-select: none;-webkit-user-select: none;-ms-user-select: none;user-select: none;}img {position: absolute;left: 0;top: 0;}.signature-pad {position: absolute;left: 0;top: 0;width:350px;height:150px;}#signatureModal .modal-dialog{width: 380px;height: 200px}#clear{width: 350px;border-radius: 0 0 4px 4px;}#fsubmit{margin-bottom: 20px !important}
</style>
<?php echo $this->fetch('script'); ?>
<div class="main">
    <?php echo $this->Form->create($this->request->controller,array('controller'=>$this->request->controller, 'action'=>'sign_pdf'),array('class'=>'form-control'));?>
    <div class="row">
        <div class="col-md-12"><h4>Add your signature here before downloading the PDF</h4></div>
        <div class="col-md-12"><?php echo $this->Form->input('add_cover_page',array('class'=>'form-control'));?></div>
        <div class="col-md-12"><?php echo $this->Form->input('add_parent_records',array('class'=>'form-control'));?></div>
        <div class="col-md-12"><?php echo $this->Form->input('add_child_records',array('class'=>'form-control'));?></div>
        <div class="col-md-12"><?php echo $this->Form->input('add_linked_form_records',array('class'=>'form-control'));?></div>
        <div class="col-md-12">
            <div id="signed">
                
            </div>
            <div class="wrapper1">
                <canvas id="signature-pad" class="signature-pad" width=350 height=150></canvas>     
                <?php echo $this->Form->hidden('DigitalSignature.signature',array('id'=>'digital-signature'));?>            
            </div>
            <div class="pull-left" ><?php echo $this->Form->input('save_sign',array('type'=>'checkbox'))?></div>
            <div class="pull-right" ><input type="button" value="Clear Sketchpad" id="clearbutton" onclick="clearCanvas();"></div>          
        </div>
    </div>
    <?php echo $this->Form->submit('Create and Doenload PDF',array('class'=>'btn btn-sm btn-success'));?>
    <?php echo $this->Form->end();?>
</div>
<script type="text/javascript">
    function clearCanvas() {
        // canvas = new SignaturePad(document.getElementById('signature-pad');
        var canvas = document.getElementById('signature-pad');
        var ctx = canvas.getContext('2d');
        event.preventDefault();
        ctx.clearRect(0, 0, canvas.width, canvas.height);
    }

    $().ready(function(){
        var signaturePad = new SignaturePad(document.getElementById('signature-pad'), {
          backgroundColor: 'rgba(255, 255, 255, 0)',
          penColor: 'rgb(17,26,201)'
      });
    });

    // var signature = signaturePad.toDataURL('image/png');
    // $("#digital-signature").val(signature);
</script>
