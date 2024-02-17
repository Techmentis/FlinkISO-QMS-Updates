<?php echo $this->element('checkbox-script'); ?><div  id="main">
	<?php echo $this->Session->flash();?>	
	<div class="designations ">
		<?php echo $this->element('nav-header-lists',array('postData'=>array('pluralHumanName'=>'Designations','modelClass'=>'Designation','options'=>array("sr_no"=>"Sr No","name"=>"Name","level"=>"Level"),'pluralVar'=>'designations'))); ?>

		<script type="text/javascript">
			$(document).ready(function(){
				$("select").chosen();
				$('table th a, .pag_list li span a').on('click', function() {
					var url = $(this).attr("href");
					$('#main').load(url);
					return false;
				});
			});
		</script>
		<?php $x = 0;?>
		<div class="table-responsive">
			<?php echo $this->Form->create(array('class'=>'no-padding no-margin no-background'));?>				
			<table cellpadding="0" cellspacing="0" class="table table-hover index" id="exportcsv">
				<tr>
					<th><?php echo $this->Paginator->sort('name'); ?></th>
					<th><?php echo $this->Paginator->sort('employees'); ?></th>
					<th width="280"><?php echo $this->Paginator->sort('parent_id'); ?></th>
					<th><?php echo $this->Paginator->sort('publish'); ?></th>
					<th>Actions</th>
				</tr>
				<?php if($designations){ ?>
					<?php foreach ($designations as $designation): ?>
						<tr class="on_page_src" onclick="addrec('<?php echo $designation['Designation']['id'];?>')" id="<?php echo $designation['Designation']['id'];?>_tr">
							<td><?php echo h($designation['Designation']['name']); ?>&nbsp;</td>
							<td><?php echo h($designation['Designation']['employees']); ?>&nbsp;</td>
							<td>
								<?php 
								if($designation['ParentDesignation']['name']){
									echo $this->Html->link($designation['ParentDesignation']['name'], array('controller' => 'designations', 'action' => 'view', $designation['ParentDesignation']['id']));	
								}else{
									echo $this->form->input('parent_designation_id',array('label'=>false,'style'=>'width:100%','id'=>'ParentDesignation'.$x, 'onChange'=>'update("'.$designation['Designation']['id'].'",this.value)', 'options'=>$parentDesignations));
								}?>
							</td>
							<td width="60">
								<?php if($designation['Designation']['publish'] == 1) { ?>
									<span class="fa fa-check"></span>
								<?php } else { ?>
									<span class="fa fa-close"></span>
									<?php } ?>&nbsp;</td>
									<td class=" actions">	
										<?php echo $this->element('actions', array('created' => $designation['Designation']['created_by'], 'postVal' => $designation['Designation']['id'], 'softDelete' => $designation['Designation']['soft_delete'])); ?>	
									</td>	
								</tr>
								<?php $x++;?>			
							<?php endforeach; ?>
						<?php }else{ ?>
							<tr><td colspan=57>No results found</td></tr>
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
<script type="text/javascript">
	function update(id,value){
		$.ajax({
			url: "<?php echo Router::url('/', true); ?><?php echo $this->request->params['controller'] ?>/update_parent/"+id+"/"+value,
			success: function(data, result) {
	        	// $(".fa-spin").removeClass('show').addClass('hide');
	        	// $("#downloadpdf").html(data);
			},						        
		});	
	}
</script>
<script>$.ajaxSetup({beforeSend:function(){$("#busy-indicator").show();},complete:function(){$("#busy-indicator").hide();}});</script>
