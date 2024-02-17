<?php echo $this->element('checkbox-script'); ?><div  id="main">
	<?php echo $this->Session->flash();?>	
	<div class="qcDocumentCategories ">
		<?php echo $this->element('nav-header-lists',array('postData'=>array('pluralHumanName'=>'Qc Document Categories','modelClass'=>'QcDocumentCategory','options'=>array("sr_no"=>"Sr No","name"=>"Name","short_name"=>"Short Name"),'pluralVar'=>'qcDocumentCategories'))); ?>

		<script type="text/javascript">
			$(document).ready(function(){
				$('table th a, .pag_list li span a').on('click', function() {
					var url = $(this).attr("href");
					$('#main').load(url);
					return false;
				});
			});
		</script>	
		<div class="table-responsive">
			<?php echo $this->Form->create(array('class'=>'no-padding no-margin no-background'));?>				
			<table cellpadding="0" cellspacing="0" class="table table-hover index">
				<tr>

					
					<th><?php echo $this->Paginator->sort('name'); ?></th>
					<th><?php echo $this->Paginator->sort('short_name'); ?></th>
					<th><?php echo $this->Paginator->sort('standard_id'); ?></th>
					<th><?php echo $this->Paginator->sort('parent_id'); ?></th>
					<th><?php echo $this->Paginator->sort('prepared_by'); ?></th>		
					<th><?php echo $this->Paginator->sort('approved_by'); ?></th>		
					<th><?php echo $this->Paginator->sort('publish'); ?></th>		

					<th>Actions</th>
				</tr>
				<?php if($qcDocumentCategories){ ?>
					<?php foreach ($qcDocumentCategories as $qcDocumentCategory): ?>
						<tr class="on_page_src" onclick="addrec('<?php echo $qcDocumentCategory['QcDocumentCategory']['id'];?>')" id="<?php echo $qcDocumentCategory['QcDocumentCategory']['id'];?>_tr">
							<td><?php echo h($qcDocumentCategory['QcDocumentCategory']['name']); ?>&nbsp;</td>
							<td><?php echo h($qcDocumentCategory['QcDocumentCategory']['short_name']); ?>&nbsp;</td>
							<td>
								<?php echo $this->Html->link($qcDocumentCategory['Standard']['name'], array('controller' => 'standards', 'action' => 'view', $qcDocumentCategory['Standard']['id'])); ?>
							</td>
							<td>
								<?php echo $this->Html->link($qcDocumentCategory['ParentQcDocumentCategory']['name'], array('controller' => 'qc_document_categories', 'action' => 'view', $qcDocumentCategory['ParentQcDocumentCategory']['id'])); ?>
							</td>
							<td><?php echo h($PublishedEmployeeList[$qcDocumentCategory['QcDocumentCategory']['prepared_by']]); ?>&nbsp;</td>
							<td><?php echo h($PublishedEmployeeList[$qcDocumentCategory['QcDocumentCategory']['approved_by']]); ?>&nbsp;</td>

							<td width="60">
								<?php if($qcDocumentCategory['QcDocumentCategory']['publish'] == 1) { ?>
									<span class="fa fa-check"></span>
								<?php } else { ?>
									<span class="fa fa-close"></span>
									<?php } ?>&nbsp;</td>

									<td class=" actions">	<?php echo $this->element('actions', array('created' => $qcDocumentCategory['QcDocumentCategory']['created_by'], 'postVal' => $qcDocumentCategory['QcDocumentCategory']['id'], 'softDelete' => $qcDocumentCategory['QcDocumentCategory']['soft_delete'])); ?>	</td>	</tr>
								<?php endforeach; ?>
							<?php }else{ ?>
								<tr><td colspan=60>No results found</td></tr>
							<?php } ?>
						</table>
						<?php echo $this->Form->end();?>			
					</div>
					<p>
						<?php
						echo $this->Paginator->options(array(			
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

	<script>$.ajaxSetup({beforeSend:function(){$("#busy-indicator").show();},complete:function(){$("#busy-indicator").hide();}});</script>
