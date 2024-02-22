<?php 
$docarray = array('doc','docx');
$sheetarray = array('xls','xlsx');
$pdfarray = array('pdf');
$pptarray = array('ppt','pptx');
if($qcDocuments){
	// echo '<table cellpadding="0" cellspacing="0" class="table table-responsive table-hover index">';
	foreach($qcDocuments as $childDoc){ ?>
		<tr class="childdoc" onclick="addrec('<?php echo $childDoc['QcDocument']['id'];?>')" id="<?php echo $childDoc['QcDocument']['id'];?>_tr">
			<td><i class="fa fa-angle-right" style="margin: 0px 10px;"></i>

				<?php   
				if(in_array($childDoc['QcDocument']['file_type'],$docarray)){ ?>
					<i class="fa fa-file-word-o" aria-hidden="true"></i>
				<?php }elseif(in_array($childDoc['QcDocument']['file_type'],$sheetarray)){ ?>
					<i class="fa fa-file-excel-o" aria-hidden="true"></i>
				<?php }elseif(in_array($childDoc['QcDocument']['file_type'],$pdfarray)){ ?>
					<i class="fa fa-file-pdf-o" aria-hidden="true"></i>
				<?php }elseif(in_array($childDoc['QcDocument']['file_type'],$pptarray)){ ?>
					<i class="fa fa-file-ppt-o" aria-hidden="true"></i>
				<?php }else{ ?>
					<i class="fa fa-exclamation-triangle text-danger" aria-hidden="true"></i>
				<?php }?>

				<strong><?php echo $this->Html->link($childDoc['Standard']['name'], array('controller' => 'standards', 'action' => 'view', $childDoc['Standard']['id'])); ?></strong>: <strong><?php echo $this->Html->link($childDoc['QcDocument']['name'],array('action'=>'view',$childDoc['QcDocument']['id'])); ?>&nbsp; </strong> - <small><?php echo h($childDoc['QcDocument']['document_number']); ?>&nbsp;</small></td>
				<td><?php echo h($childDoc['PreparedBy']['name']); ?>&nbsp;</td>
				<td><?php echo h($childDoc['ApprovedBy']['name']); ?>&nbsp;</td>
				<td><?php echo h($childDoc['IssuedBy']['name']); ?>&nbsp;</td>
				<td><?php echo h($customArray['documentStatuses'][$childDoc['QcDocument']['document_status']]); ?>&nbsp;</td>
				<td>
					<div class="btn-group">
						<div class="btn btn-xs btn-default"><?php echo h($childDoc['QcDocument']['tables']); ?></div>
						<div class="btn btn-xs btn-success"><?php echo h($childDoc['QcDocument']['active_tables']); ?></div>
					</div>
				&nbsp;</td>

				<td width="60">
					<?php if($childDoc['QcDocument']['publish'] == 1) { ?>
						<span class="btn btn-sm fa fa-check"></span>
					<?php } else { ?>
						<span class="btn btn-sm fa fa-close"></span>
						<?php } ?>&nbsp;</td>

						<td class="" >	
							<div class="btn-group btn-no-border">
								<?php echo $this->Html->link('<i class="fa fa-television"></i>',array('controller'=>'qc_documents','action'=>'view',$childDoc['QcDocument']['id']),array('class'=>'tooltip1 btn btn-sm btn-default','escape'=>false, 'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'left', 'title'=> 'View'));?>

								<?php echo $this->Html->link('<i class="fa fa-edit"></i>',array('controller'=>'qc_documents','action'=>'edit',$childDoc['QcDocument']['id']),array('class'=>'tooltip1 btn btn-sm btn-default','escape'=>false, 'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'left', 'title'=> 'Edit'));?>

								<?php //echo $this->Html->link('<i class="fa fa-database"></i>',array('controller'=>'custom_tables','action'=>'add','qc_document_id'=>$childDoc['QcDocument']['id']),array('class'=>'tooltip1 btn btn-sm btn-default','escape'=>false, 'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'left', 'title'=> 'Create Table'));?>
							</div>
						</td>
					</tr>
				<?php }
				// echo "</table>";
			} ?>