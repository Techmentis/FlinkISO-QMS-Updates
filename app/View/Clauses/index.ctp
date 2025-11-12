<?php echo $this->element('checkbox-script'); ?><div  id="main">
	<?php echo $this->Session->flash();?>	
	<div class="clauses ">
		<?php echo $this->element('nav-header-lists',array('postData'=>array('pluralHumanName'=>'Clauses','modelClass'=>'Clause','options'=>array("sr_no"=>"Sr No","title"=>"Title","standard"=>"Standard","clause"=>"Clause","sub-clause"=>"Sub-clause","details"=>"Details","additional_details"=>"Additional Details","tabs"=>"Tabs","external_link_1"=>"External Link 1","external_link_2"=>"External Link 2","external_link_3"=>"External Link 3","external_link_4"=>"External Link 4","external_link_5"=>"External Link 5","system_tables"=>"System Tables"),'pluralVar'=>'clauses'))); ?>

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
			<table cellpadding="0" cellspacing="0" class="table table-hover">
				<tr>					
					<th><?php echo $this->Paginator->sort('title'); ?></th>
					<!-- <th><?php echo $this->Paginator->sort('standard'); ?></th> -->
					<th><?php echo $this->Paginator->sort('standard_id'); ?></th>
					<th><?php echo $this->Paginator->sort('clause'); ?></th>
					<th><?php echo $this->Paginator->sort('sub-clause'); ?></th>
					<th><?php echo $this->Paginator->sort('prepared_by'); ?></th>		
					<th><?php echo $this->Paginator->sort('approved_by'); ?></th>		
					<th><?php echo $this->Paginator->sort('publish'); ?></th>	
					<th>Action</th>			
				</tr>
				<?php if($clauses){ ?>
					<?php foreach ($clauses as $clause): ?>
						<tr class="on_page_src" onclick="addrec('<?php echo $clause['Clause']['id'];?>')" id="<?php echo $clause['Clause']['id'];?>_tr">
							<td><?php echo h($clause['Clause']['title']); ?>&nbsp;</td>
							<!-- <td><?php echo h($clause['Standard']['name']); ?>&nbsp;</td> -->
							<td><?php echo $this->Html->link($clause['Standard']['name'], array('controller' => 'standards', 'action' => 'view', $clause['Standard']['id'])); ?></td>
							<td><?php echo h($clause['Clause']['clause']); ?>&nbsp;</td>
							<td><?php echo h($clause['Clause']['sub-clause']); ?>&nbsp;</td>		
							<td><?php echo h($PublishedEmployeeList[$clause['Clause']['prepared_by']]); ?>&nbsp;</td>
							<td><?php echo h($PublishedEmployeeList[$clause['Clause']['approved_by']]); ?>&nbsp;</td>
							<td width="60">
								<?php if($clause['Clause']['publish'] == 1) { ?>
									<span class="fa fa-check"></span>
								<?php } else { ?>
									<span class="fa fa-close"></span>
									<?php } ?>&nbsp;
								</td>
								<td class=" actions">
									<?php echo $this->element('actions', array('created' => $clause['Clause']['created_by'], 'postVal' => $clause['Clause']['id'], 'softDelete' => $clause['Clause']['soft_delete'])); ?>
								</td>
							</tr>
						<?php endforeach; ?>
					<?php }else{ ?>
						<tr><td colspan=90>No results found</td></tr>
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
