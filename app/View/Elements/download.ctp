<?php
$url = Configure::read('url') . DS . $data['id'] . DS . 'pdf' . DS . $data['pdffilename'];
if($data['result'] == 'success'){
	echo $this->Html->link('<i class="fa fa-cloud-download"></i>&nbsp;&nbsp;&nbsp;Download PDF',$url,array('escape'=>false,'class'=>'btn btn-sm btn-success'));	
}else{
	echo $this->Html->link('<i class="fa fa-exclamation-triangle"></i>&nbsp;&nbsp;&nbsp;File Generation Faild!','#',array('escape'=>false,'class'=>'btn btn-sm btn-danger'));	
}
?>

