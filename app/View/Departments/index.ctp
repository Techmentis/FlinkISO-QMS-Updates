<?php echo $this->element('checkbox-script'); ?><div  id="main">
	<?php echo $this->Session->flash();?>	
	<div class="departments ">
		<?php echo $this->element('nav-header-lists',array('postData'=>array('pluralHumanName'=>'Departments','modelClass'=>'Department','options'=>array("sr_no"=>"Sr No","name"=>"Name","clauses"=>"Clauses","details"=>"Details"),'pluralVar'=>'departments'))); ?>

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
			<table cellpadding="0" cellspacing="0" class="table table-hover index"  id="exportcsv">
				<tr>					
					<th><?php echo $this->Paginator->sort('name'); ?></th>
					<!-- <th><?php echo $this->Paginator->sort('clauses'); ?></th> -->
					<th><?php echo $this->Paginator->sort('details'); ?></th>
					<th><?php echo $this->Paginator->sort('employees'); ?></th>		
					<th><?php echo $this->Paginator->sort('users'); ?></th>		
					<th><?php echo $this->Paginator->sort('publish'); ?></th>
					<th>Actions</th>
				</tr>
				<?php if($departments){ ?>
					<?php foreach ($departments as $department): ?>
						<tr class="on_page_src" onclick="addrec('<?php echo $department['Department']['id'];?>')" id="<?php echo $department['Department']['id'];?>_tr">
							<td><?php echo h($department['Department']['name']); ?>&nbsp;</td>
							<!-- <td><?php echo h($department['Department']['clauses']); ?>&nbsp;</td> -->
							<td><?php echo h($department['Department']['details']); ?>&nbsp;</td>
		<!-- <td><?php echo h($PublishedEmployeeList[$department['Department']['prepared_by']]); ?>&nbsp;</td>
			<td><?php echo h($PublishedEmployeeList[$department['Department']['approved_by']]); ?>&nbsp;</td> -->
			<td><?php echo h($department['Department']['employees']); ?>&nbsp;</td>
			<td><?php echo h($department['Department']['users']); ?>&nbsp;</td>

			<td width="60">
				<?php if($department['Department']['publish'] == 1) { ?>
					<span class="fa fa-check"></span>
				<?php } else { ?>
					<span class="fa fa-close"></span>
					<?php } ?>&nbsp;</td>

					<td class=" actions">	<?php echo $this->element('actions', array('created' => $department['Department']['created_by'], 'postVal' => $department['Department']['id'], 'softDelete' => $department['Department']['soft_delete'])); ?>	</td>	</tr>
				<?php endforeach; ?>
			<?php }else{ ?>
				<tr><td colspan=54>No results found</td></tr>
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
