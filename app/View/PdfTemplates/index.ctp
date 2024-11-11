<?php echo $this->element('checkbox-script'); ?><div  id="main">
	<?php echo $this->Session->flash();?>	
	<div class="pdfTempates ">
		<?php echo $this->element('nav-header-lists',array('postData'=>array('pluralHumanName'=>'PDF Templates','modelClass'=>'PdfTemplate','options'=>array("sr_no"=>"Sr No","name"=>"Name"),'pluralVar'=>'pdfDepartments'))); ?>

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
			<?php
			if($headerFileExists == true)echo $this->Html->link('Update Default Header File',array('action'=>'add','HeaderTemplate'),array('class'=>'btn btn-sm btn-success pull-right'));
			else echo $this->Html->link('Create Default Header File',array('action'=>'add','HeaderTemplate'),array('class'=>'btn btn-sm btn-warning pull-right'));

			echo $this->Html->link('Create QC Document Cover File',array('action'=>'add_cover','CoverTemplate'),array('class'=>'btn btn-sm btn-info pull-right')).'<br /><br />';	
			?>				
			<table id="pdfTempates" cellpadding="0" cellspacing="0" class="table table-hover index" id="exportcsv">
				<tr>
					<th><?php echo $this->Paginator->sort('name'); ?></th>
					<th><?php echo $this->Paginator->sort('template_type'); ?></th>
					<th><?php echo $this->Paginator->sort('custom_table_id'); ?></th>
					<th><?php echo $this->Paginator->sort('modified_by'); ?></th>
					<th><?php echo $this->Paginator->sort('modified'); ?></th>
					<th>Actions</th>
				</tr>
				<?php if($pdfTemplates){ ?>
					<?php foreach ($pdfTemplates as $pdfTemplate): ?>
						<tr class="on_page_src" onclick="addrec('<?php echo $pdfTemplate['PdfTemplate']['id'];?>')" id="<?php echo $pdfTemplate['PdfTemplate']['id'];?>_tr">
							<td><?php echo h($pdfTemplate['PdfTemplate']['name']); ?>&nbsp;</td>
							<!-- <td><?php echo h($pdfTemplate['PdfTemplate']['template_type']?'Header':'Content'); ?>&nbsp;</td> -->
							<td>
								<?php 
									if($pdfTemplate['PdfTemplate']['template_type'] == 3)echo 'Cover';
									if($pdfTemplate['PdfTemplate']['template_type'] == 0)echo 'Content';
									if($pdfTemplate['PdfTemplate']['template_type'] == 1)echo 'Header';
								?>
							</td>
							<td><?php echo h($pdfTemplate['CustomTable']['name']); ?>&nbsp;</td>														
							<td><?php echo h($pdfTemplate['ModifiedBy']['name']); ?>&nbsp;</td>
							<td><?php echo h($pdfTemplate['PdfTemplate']['modified']); ?>&nbsp;</td>
								<td class=" actions">	
									<?php echo $this->element('actions', array('created' => $pdfTemplate['PdfTemplate']['created_by'], 'postVal' => $pdfTemplate['PdfTemplate']['id'], 'softDelete' => $pdfTemplate['PdfTemplate']['soft_delete'])); ?>
								</td>							
							</tr>
						<?php endforeach; ?>
					<?php }else{ ?>
						<tr><td colspan="7">No results found</td></tr>
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
