<?php echo $this->element('checkbox-script'); ?><div  id="main">
	<?php echo $this->Session->flash();?>	
	<div class="templates ">
		<?php echo $this->element('nav-header-lists',array('postData'=>array('pluralHumanName'=>'templates','modelClass'=>'Template','options'=>array("sr_no"=>"Sr No","name"=>"Name","details"=>"Details","departments"=>"Departments"),'pluralVar'=>'templates'))); ?>

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
			<table id="templates" cellpadding="0" cellspacing="0" class="table table-hover index" id="exportcsv">
				<tr>
					<th><?php echo $this->Paginator->sort('name'); ?></th>
					<th>Actions</th>
				</tr>
				<?php if($templates){ ?>
					<?php foreach ($templates as $template): ?>
						<tr class="on_page_src" onclick="addrec('<?php echo $template['Template']['id'];?>')" id="<?php echo $template['Template']['id'];?>_tr">
							<td><?php echo h($template['Template']['name']); ?>&nbsp;</td>
							<td class=" actions">	
								<?php echo $this->element('actions', array('created' => $template['Template']['created_by'], 'postVal' => $template['Template']['id'], 'softDelete' => $template['Template']['soft_delete'])); ?>
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
