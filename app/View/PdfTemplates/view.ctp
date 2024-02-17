<?php echo $this->element('nav-header-lists',array('postData'=>array('pluralHumanName'=>'PDF Templates','modelClass'=>'PdfTemplate','options'=>array("sr_no"=>"Sr No","name"=>"Name"),'pluralVar'=>'pdfDepartments'))); ?>
<style type="text/css">
	.txtfld{width: 96%; border: none;}
	.childtables ul { list-style-type: none; margin: 0; padding: 0; margin-bottom: 10px; }
  	.childtables li { margin: 5px; padding: 5px; width: 150px; border:1px solid #ccc; width:100% }
    body{font-family: "<?php echo $fontface;?>" font-size:"<?php echo $fontsize;?>."px}
    table{background-color: #ccc; border-color: #ccc; font-size:"<?php echo $fontsize;?>"px}
    tr{background-color: #fff; text-align: left;}
    td,th{background-color: #fff; text-align: left;}
 </style>
<?php echo $this->Form->create('PdfTemplate',array('action'=>'edit'),array('class'=>'form-control')) ?>
<div class="main">
	<div class="row">
		<div class="col-md-12">
			<?php echo $html;?>
		</div>
	</div>
</div>
