<?php echo $this->element('checkbox-script'); ?><div  id="main">
	<?php echo $this->Session->flash();?>	
	<div class="branches ">
		<?php echo $this->element('nav-header-lists',array('postData'=>array('pluralHumanName'=>'Branches','modelClass'=>'Branch','options'=>array("sr_no"=>"Sr No","name"=>"Name","details"=>"Details","departments"=>"Departments"),'pluralVar'=>'branches'))); ?>

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
			<table id="branches" cellpadding="0" cellspacing="0" class="table table-hover index" id="exportcsv">
				<tr>
					<th><?php echo $this->Paginator->sort('name'); ?></th>
					<th><?php echo $this->Paginator->sort('details'); ?></th>
					<th><?php echo $this->Paginator->sort('departments'); ?></th>
					<th><?php echo $this->Paginator->sort('employees'); ?></th>
					<th><?php echo $this->Paginator->sort('users'); ?></th>
					<th><?php echo $this->Paginator->sort('publish'); ?></th>
					<th>Actions</th>
				</tr>
				<?php if($branches){ ?>
					<?php foreach ($branches as $branch): ?>
						<tr class="on_page_src" onclick="addrec('<?php echo $branch['Branch']['id'];?>')" id="<?php echo $branch['Branch']['id'];?>_tr">
							<td><?php echo h($branch['Branch']['name']); ?>&nbsp;</td>
							<td><?php echo h($branch['Branch']['details']); ?>&nbsp;</td>
							<td><?php foreach(json_decode($branch['Branch']['departments'],true) as $departments){
								echo $PublishedDepartmentList[$departments].', ';
							} ?>&nbsp;</td>
						<!-- <td><?php echo h($branch['PreparedBy']['name']); ?>&nbsp;</td>
							<td><?php echo h($branch['ApprovedBy']['name']); ?>&nbsp;</td> -->
							<td><?php echo h($branch['Branch']['employees']); ?>&nbsp;</td>
							<td><?php echo h($branch['Branch']['users']); ?>&nbsp;</td>
							<td width="60">
								<?php if($branch['Branch']['publish'] == 1) { ?>
									<span class="fa fa-check"></span>
								<?php } else { ?>
									<span class="fa fa-close"></span>
									<?php } ?>&nbsp;
								</td>
								<td class=" actions">	
									<?php echo $this->element('actions', array('created' => $branch['Branch']['created_by'], 'postVal' => $branch['Branch']['id'], 'softDelete' => $branch['Branch']['soft_delete'])); ?>
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
