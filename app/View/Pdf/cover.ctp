<style type="text/css">
	body{font-family: "Helvetica Neue",Helvetica,Arial,sans-serif; font-size: 12px;text-align: center;}
	.row {margin-right: -15px;margin-left: -15px;}
	.col-md-3 {width: 20%;float:left ;position: relative;min-height: 1px;padding-right: 15px;padding-left: 15px; display: block;}
	small{font-size: 80%; font-weight: 500;}
	h1,h2,h3,h4,h5{display: block; float:left; text-align: center; width: 100%;}
</style>
<?php

if(!empty($logo)){
	$path = WWW_ROOT . DS . 'img' . DS . 'logo' . DS . $logo;
	echo '<img src="'.$path.'" height="120">';
}?>
<h1 style="margin-top:35%;"><?php echo $this->Session->read('User.company_name');?></h1>
<h3><?php echo $this->Session->read('User.branch');?> | <?php echo $this->Session->read('User.department');?></h3>
<br /><br />
<h2><?php echo $qcDocument['QcDocument']['title'];?></h2>
<h2>
	<small><?php echo $qcDocument['Standard']['name'];?> | <?php echo $qcDocument['Clause']['title'];?></small><br />
	<small><?php echo $qcDocument['QcDocumentCategory']['name'];?></small>
</h2>
<h3><small>Rev. <?php echo $qcDocument['QcDocument']['revision_number'];?></small></h3>
<h3><small>Issue No. <?php echo $issue;?></small></h3>
<br />
<br />
<br />

<div class="row" style="margin-top:30%; float: left; width:100%">
	<div class="col-md-3">
		<p>
			<?php
			if($qcDocument['PreparedBy']['id'] != $this->Session->read('User.employee_id')){
				$sign = $this->requestAction(array('controller'=>'employees','action'=>'get_signatures',$qcDocument['PreparedBy']['id']));					
			}else{
				$sign = $signature;
			}
			
			?>
			<img src="<?php echo $sign;?>" height="60">			
		</p>
		<p>Prepared By</p>
		<p><?php echo $qcDocument['PreparedBy']['name'];?></p>
	</div>
	<div class="col-md-3">
		<p>
			<?php 
			if($qcDocument['ApprovedBy']['id'] != $this->Session->read('User.employee_id')){
				$sign = $this->requestAction(array('controller'=>'employees','action'=>'get_signatures',$qcDocument['ApprovedBy']['id']));					
			}else{
				$sign = $signature;
			}
			?>
			<img src="<?php echo $sign;?>" height="60">
		</p>
		<p>Approved By</p>
		<p><?php echo $qcDocument['ApprovedBy']['name'];?></p>
	</div>
	<div class="col-md-3">
		<p>
			<?php 
			if($qcDocument['IssuedBy']['id'] != $this->Session->read('User.employee_id')){
				$sign = $this->requestAction(array('controller'=>'employees','action'=>'get_signatures',$qcDocument['IssuedBy']['id']));					
			}else{
				$sign = $signature;
			}
			?>
			<img src="<?php echo $sign;?>" height="60">
		</p>
		<p>Issued By</p>
		<p><?php echo $qcDocument['IssuedBy']['name'];?></p>
	</div>
	<div class="col-md-3">
		<p>
			<?php 
			
			$sign = $signature;
			
			?>
			<img src="<?php echo $sign;?>" height="60">
		</p>
		<p>Downloaded By</p>
		<p><?php echo $this->Session->read('User.name');?></p>
	</div>
</div>
