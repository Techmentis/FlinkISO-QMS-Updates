<?php
$docarray = array('doc','docx');
$sheetarray = array('xls','xlsx');
$pdfarray = array('pdf');
$pptarray = array('ppt','pptx');
?>
<style type="text/css">
	.childdoc, .childdoc a{color: #5c5c5c;}
</style>
<?php echo $this->element('checkbox-script'); ?><div  id="main">
	<?php echo $this->Session->flash();?>	
	<div class="qcDocuments ">
		<?php echo $this->element('nav-header-lists',array('postData'=>array('pluralHumanName'=>'Qc Documents','modelClass'=>'QcDocument','options'=>array("sr_no"=>"Sr No","document_number"=>"Document Number","reference_number"=>"Reference Number","issue_number"=>"Issue Number","date_of_next_issue"=>"Date Of Next Issue","date_of_issue"=>"Date Of Issue","effective_from_date"=>"Effective From Date","revision_number"=>"Revision Number","date_of_review"=>"Date Of Review","revision_date"=>"Revision Date","document_type"=>"Document Type","it_categories"=>"It Categories","document_status"=>"Document Status","issued_by"=>"Issued By","archived"=>"Archived","change_history"=>"Change History","cr_status"=>"Cr Status","mark_for_cr_update"=>"Mark For Cr Update","temp_date_of_issue"=>"Temp Date Of Issue","temp_effective_from_date"=>"Temp Effective From Date","linked_formats"=>"Linked Formats","cover_page"=>"Cover Page","page_orientation"=>"Page Orientation"),'pluralVar'=>'qcDocuments'))); ?>

		<script type="text/javascript">
			$(document).ready(function(){
				$('table th a, .pag_list li span a').on('click', function() {
					var url = $(this).attr("href");
					$('#main').load(url);
					return false;
				});
			});
		</script>	
		<div class="table-responsive" style="overflow:scroll">
			<?php echo $this->Form->create(array('class'=>'no-padding no-margin no-background'));?>				
			<table cellpadding="0" cellspacing="0" class="table table-responsive table-hover index">
				<tr>
					<th><?php echo $this->Paginator->sort('standard_id'); ?> : <?php echo $this->Paginator->sort('title'); ?> - <?php echo $this->Paginator->sort('intdocunumber','Document Number'); ?> - Rev.No.</th>
					<th><?php echo $this->Paginator->sort('prepared_by'); ?></th>
					<th><?php echo $this->Paginator->sort('approved_by'); ?></th>		
					<th><?php echo $this->Paginator->sort('issued_by'); ?></th>
					<th><?php echo $this->Paginator->sort('document_status'); ?></th>
					<th><?php echo __('Tables'); ?></th>
					<th><?php echo $this->Paginator->sort('publish'); ?></th>
					<th width="90">Actions</th>
					
				</tr>
				<?php if($qcDocuments){ ?>
					<?php foreach ($qcDocuments as $qcDocument):
						if($qcDocument['QcDocument']['document_status'] ==  6 || $qcDocument['QcDocument']['document_status'] ==  3 ){
							$revisionClasss = ' text-danger';
						}else{
							$revisionClasss = '';
						}
						?>
						<tr class="<?php echo $revisionClasss;?>" onclick="addrec('<?php echo $qcDocument['QcDocument']['id'];?>')" id="<?php echo $qcDocument['QcDocument']['id'];?>_tr">
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
							<?php echo $this->Form->end();?>			
						</div>
						<p>
							<?php
							echo $this->Paginator->options(array(
								'update' => '#main',
								'evalScripts' => true,
								'before' => $this->Js->get('#busy-indicator')->effect('fadeIn', array('buffer' => false)),
								'complete' => $this->Js->get('#busy-indicator')->effect('fadeOut', array('buffer' => false)),
							));

							echo $this->Paginator->counter(array(
								'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
							));
						?>			</p>
						<ul class="pagination">
							<?php
							echo "<li class='previous'>".$this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'))."</li>";
							echo "<li>".$this->Paginator->numbers(array('separator' => ''))."</li>";
							echo "<li class='next'>".$this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'))."</li>";
							?>
						</ul>
					</div>
				</div>
			</div>
		</div>
		<script>
			function changedocumentnumber(id,value){
				$.ajax({
					url: "<?php echo Router::url('/', true); ?><?php echo $this->request->params['controller'] ?>/updatedocumentnumber/"+id+"/"+btoa(value),
					success: function(data, result) {														
						
						$("#"+id+"_dnum").html('');
						if(data == 'Exists'){
							$("#"+id+"_dnum").html('Document Number already exists. Try different number.');
						}

						if(data == 'DocDontExists'){
							$("#"+id+"_dnum").html('Unable to find the document.');
						}

						if(data == 'Toolong'){
							$("#"+id+"_dnum").html('Document Number should not be more than 7 characters long.');
						}

						if(data == 'BkpFoldFailed'){
							$("#"+id+"_dnum").html('Unable to backup existing folder.');
						}

						if(data == 'Success'){
							$("#"+id+"_dnum").html('Document Number Updated.');
						}

						if(data == 'notsame'){
							$("#"+id+"_dnum").html('Old & New Document Numbers must be same length.');
						}						
					},
				});

			}
		</script>
		<script>$.ajaxSetup({beforeSend:function(){$("#busy-indicator").show();},complete:function(){$("#busy-indicator").hide();}});</script>
		<script type="text/javascript">	$().ready(function(){$(".tooltip1").tooltip();});</script>
