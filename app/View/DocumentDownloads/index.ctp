<div  id="document_download">
	<div class="panel panel-body">
		<div class="document_download ">		
			<h4>PDF Download Receipts</h4>
			<?php $x = 0;?>
			<script type="text/javascript">
				$(document).ready(function() {
					$('table th a, .pag_list li span a').on('click', function() {
						var url = $(this).attr("href");
						$('#document_download').load(url);
						return false;
					});
				});
			</script>
			<div class="table-responsive">		
				<table cellpadding="0" cellspacing="0" class="table table-hover">
					<tr>
						<th><?php echo "Document";?></th>
						<th><?php echo $this->Paginator->sort('created_by','Downloaded By'); ?></th>
						<th><?php echo $this->Paginator->sort('time','Download Date/ Time'); ?></th>						
						<th>Signature</th>
					</tr>
					<?php if($documentDownloads){ ?>
						<?php foreach ($documentDownloads as $documentDownload): ?>
							<tr>
								<td><?php echo h($documentDownload['DocumentDownload']['name']); ?>&nbsp;</td>
								<td><?php echo h($documentDownload['CreatedBy']['name']); ?>&nbsp;</td>
								<td><?php echo h($documentDownload['DocumentDownload']['created']); ?>&nbsp;</td>								
								<td><img src="<?php echo $documentDownload['DocumentDownload']['signature'];?>" height="30">&nbsp;</td>
							</tr>
							<?php $x++;?>			
						<?php endforeach; ?>
					<?php }else{ ?>
						<tr><td colspan="4">No results found</td></tr>
					<?php } ?>
				</table>			
			</div>
			<p>
				<?php
				echo $this->Paginator->options(array(
					'update' => '#document_download',
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
	<?php echo $this->Js->writeBuffer(); ?>
	</div>
	<script type="text/javascript">
		function update(id,value){
			$.ajax({
				url: "<?php echo Router::url('/', true); ?><?php echo $this->request->params['controller'] ?>/update_parent/"+id+"/"+value,
				success: function(data, result) {
					
				},						        
			});	
		}
	</script>
	<script>$.ajaxSetup({beforeSend:function(){$("#busy-indicator").show();},complete:function(){$("#busy-indicator").hide();}});</script>
</div>