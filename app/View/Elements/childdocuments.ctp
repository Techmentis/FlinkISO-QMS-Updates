<?php
$docarray = array('doc','docx');
$sheetarray = array('xls','xlsx');
$pdfarray = array('pdf');
$pptarray = array('ppt','pptx');
?>
<style type="text/css">
	.childdoc, .childdoc a{color: #5c5c5c;}
</style>

<div class="table-responsive">
	<table cellpadding="0" cellspacing="0" class="table table-hover">
		<tr>		
			<th><?php echo Inflector::humanize('standard_id'); ?> : <?php echo Inflector::humanize('title'); ?> - <?php echo Inflector::humanize('intdocunumber','Document Number'); ?> - Rev.No.</th>
			<th><?php echo Inflector::humanize('prepared_by'); ?></th>
			<th><?php echo Inflector::humanize('approved_by'); ?></th>		
			<th><?php echo Inflector::humanize('issued_by'); ?></th>
			<th><?php echo Inflector::humanize('document_status'); ?></th>
			<th><?php echo __('Tables'); ?></th>
			<th><?php echo Inflector::humanize('publish'); ?></th>
					<th width="90">Actions</th>
			<th></th>				
		</tr>
		<?php if($childDocs){ ?>
			<?php foreach ($childDocs as $qcDocument): ?>
				<tr class="">
				<td>
					<?php
					if(in_array($qcDocument['QcDocument']['file_type'],$docarray)){ ?>
						<i class="fa fa-file-word-o" aria-hidden="true"></i>
					<?php }elseif(in_array($qcDocument['QcDocument']['file_type'],$sheetarray)){ ?>
						<i class="fa fa-file-excel-o" aria-hidden="true"></i>
					<?php }elseif(in_array($qcDocument['QcDocument']['file_type'],$pdfarray)){ ?>
						<i class="fa fa-file-pdf-o" aria-hidden="true"></i>
					<?php }elseif(in_array($qcDocument['QcDocument']['file_type'],$pptarray)){ ?>
						<i class="fa fa-file-powerpoint-o" aria-hidden="true"></i>
					<?php }else{ ?>
						<i class="fa fa-exclamation-triangle text-danger" aria-hidden="true"></i>
					<?php }?>
					&nbsp;
					<strong><?php echo $this->Html->link($qcDocument['Standard']['name'], array('controller' => 'standards', 'action' => 'view', $qcDocument['Standard']['id']),array('class'=>$revisionClasss)); ?></strong>: 
					<strong><?php echo $this->Html->link($qcDocument['QcDocument']['name'],array('action'=>'view',$qcDocument['QcDocument']['id']),array('class'=>$revisionClasss)); ?>&nbsp; </strong> - <small><?php echo h($qcDocument['QcDocument']['document_number']); ?>&nbsp;-Rev.No.<?php echo $qcDocument['QcDocument']['revision_number']; ?></small></td>								
						<td><?php echo h($qcDocument['PreparedBy']['name']); ?>&nbsp;</td>
						<td><?php echo h($qcDocument['ApprovedBy']['name']); ?>&nbsp;</td>
						<td><?php echo h($qcDocument['IssuedBy']['name']); ?>&nbsp;</td>
						<td><?php echo h($customArray['documentStatuses'][$qcDocument['QcDocument']['document_status']]); ?>&nbsp;</td>
						<td>
							<div class="btn-group">
								<div class="btn btn-xs btn-default"><?php echo h($qcDocument['QcDocument']['tables']); ?></div>
								<div class="btn btn-xs btn-success"><?php echo h($qcDocument['QcDocument']['active_tables']); ?></div>
							</div>
						&nbsp;</td>

						<td width="60">
							<?php if($qcDocument['QcDocument']['publish'] == 1) { ?>
								<span class="btn btn-sm fa fa-check"></span>
							<?php } else { ?>
								<span class="btn btn-sm fa fa-cross"></span>
								<?php } ?>&nbsp;</td>

								<td class="" width="120">	
									<div class="btn-group btn-no-border">
										<?php echo $this->Html->link('<i class="fa fa-television"></i>',array('controller'=>'qc_documents','action'=>'view',$qcDocument['QcDocument']['id']),array('class'=>'tooltip1 btn btn-sm btn-default','escape'=>false, 'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'left', 'title'=> 'View'));?>

										<?php echo $this->Html->link('<i class="fa fa-edit"></i>',array('controller'=>'qc_documents','action'=>'edit',$qcDocument['QcDocument']['id']),array('class'=>'tooltip1 btn btn-sm btn-default','escape'=>false, 'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'left', 'title'=> 'Edit'));?>

										<?php  
										if($qcDocument['QcDocument']['tables'] == 0){
											echo $this->Html->link('<i class="fa fa-database"></i>',array('controller'=>'custom_tables','action'=>'add','qc_document_id'=>$qcDocument['QcDocument']['id']),array('class'=>'tooltip1 btn btn-sm btn-default','escape'=>false, 'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'left', 'title'=> 'Create Table'));
										}else{
											echo $this->Html->link('<i class="fa fa-database"></i>',array('controller'=>'custom_tables','action'=>'index','qc_document_id'=>$qcDocument['QcDocument']['id']),array('class'=>'tooltip1 btn btn-sm btn-default','escape'=>false, 'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'left', 'title'=> 'View Table'));
										}
										
										?>
									</div>
								</td>
							</tr>
							<?php if($qcDocument['QcDocument']['childDoc'] > 0){?>
								<script type="text/javascript">
									$.ajax({
										url: "<?php echo Router::url('/', true); ?><?php echo $this->request->params['controller'] ?>/child_docs/<?php echo $qcDocument['QcDocument']['id']?>",
										success: function(data, result) {
											$("#<?php echo $qcDocument['QcDocument']['id'];?>_tr").after(data);
										},
									});
								</script>
							<?php } ?>
						<?php endforeach; ?>
					<?php }else{ ?>
						<tr><td colspan=144>No results found</td></tr>
					<?php } ?>
		</table>
	</div>
