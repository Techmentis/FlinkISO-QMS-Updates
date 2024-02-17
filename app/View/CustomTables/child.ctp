<table cellpadding="0" cellspacing="0" class="table table-striped table-hover" style="margin:0px">
	<?php if($customTables){ ?>
		<?php foreach ($customTables as $customTable): ?>
			<tr class="warning">					
				<td width="20%"><?php echo h($customTable['CustomTable']['name']); ?>&nbsp;</td>
				<td width="20%"><?php echo h($customTable['CustomTable']['table_name']); ?> - <?php echo h($customTable['CustomTable']['table_version']); ?>&nbsp;</td>
				<td width="20%"><?php 
				$doc = $customTable['QcDocument']['document_number'] ."-". $customTable['QcDocument']['title'] ."-". $customTable['QcDocument']['revision_number'] .".". $customTable['QcDocument']['file_type'];?>
				<?php echo $this->Html->link($doc,array('controller'=>'qc_documents','action'=>'view',$customTable['QcDocument']['id']),array('target'=>'_blank')) ?>&nbsp;</td>
				<td>
					<?php if($customTable['CustomTable']['publish'] == 1 && $customTable['CustomTable']['table_locked'] == 0 && $customTable['QcDocument']['publish'] == 1){
						echo "<span class='text-success fa fa-check-circle fa-lg'></span>";	
					}else{
						echo "<span class='text-danger fa fa-exclamation-circle fa-lg'></span>";

					}
				?>&nbsp;</td>
				<td class=" text-right">
					<div class="btn-group">
						<?php 
						if($customTable['CustomTable']['publish'] == 1){
							echo $this->Html->link('<i class="fa fa-minus-square-o fa-lg"></i>',array('action'=>'hold',$customTable['CustomTable']['id']),array('class'=>'btn btn-sm tooltip1 btn-default', 'escape'=>false, 'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'left', 'title'=> 'Ubnpublish this table'));
						}
						if($customTable['CustomTable']['publish'] == 0){
							echo $this->Html->link('<i class="fa fa-check-square-o fa-lg text-success"></i>',array('action'=>'publish',$customTable['CustomTable']['id']),array('class'=>'tooltip1 btn btn-sm btn-default', 'escape'=>false, 'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'left', 'title'=> 'Publish this table'));
						}?>
						
						<?php 
						?>
						<?php  
						if($customTable['CustomTable']['table_locked'] == 0)echo $this->Html->link('<i class="fa fa-lock fa-lg text-danger"></i>',array('action'=>'unlock',$customTable['CustomTable']['id']),array('class'=>'btn btn-sm tooltip1 btn-default', 'escape'=>false, 'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'left', 'title'=> 'Unlock table'));
						else if($customTable['CustomTable']['table_locked'] == 1) echo $this->Html->link('<i class="fa fa-unlock fa-lg text-danger"></i>',array('action'=>'lock',$customTable['CustomTable']['id']),array('class'=>'btn btn-sm tooltip1 btn-default', 'escape'=>false, 'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'left', 'title'=> 'Lock table'));
						?>
						
						<?php 
		// if($customTable['CustomTable']['publish'] == 0){
						if($customTable['CustomTable']['custom_table_id'] == ''){
							echo $this->Html->link('<i class="fa fa-refresh fa-lg text-warning"></i>',array('action'=>'recreate',$customTable['CustomTable']['id'],'qc_document_id'=>$customTable['CustomTable']['qc_document_id'],'process_id'=>$customTable['CustomTable']['process_id']),array('class'=>'tooltip1 btn btn-sm btn-default', 'escape'=>false, 'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'left', 'title'=> 'Recreate this table'));	
						}else{
							echo $this->Html->link('<i class="fa fa-refresh fa-lg text-warning"></i>',array('action'=>'recreate_child',$customTable['CustomTable']['id'],'qc_document_id'=>$customTable['CustomTable']['qc_document_id'],'process_id'=>$customTable['CustomTable']['process_id']),array('class'=>'tooltip1 btn btn-sm btn-default', 'escape'=>false, 'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'left', 'title'=> 'Recreate this table'));
			// }									
						}?>
						
						<?php  if($customTable['CustomTable']['table_locked'] == 1){
							if($customTable['CustomTable']['custom_table_id'] == ''){
								echo $this->Html->link('<i class="fa fa-trash-o fa-lg text-danger"></i>',array('action'=>'delete',$customTable['CustomTable']['id']),array('class'=>'btn btn-sm tooltip1 btn-default', 'escape'=>false, 'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'left', 'title'=> 'Delete this table'));
							}else{
								echo $this->Html->link('<i class="fa fa-trash-o fa-lg text-danger"></i>',array('action'=>'delete_child',$customTable['CustomTable']['id']),array('class'=>'btn btn-sm tooltip1 btn-default', 'escape'=>false, 'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'left', 'title'=> 'Delete this table'));
							}
							
						}?>					

						<?php if($customTable['CustomTable']['custom_table_id'] == '') echo $this->Html->link('<i class="fa fa-link text-info"></i>',array('action'=>'add_child','custom_table_id'=> $customTable['CustomTable']['id'],'qc_document_id'=>$customTable['CustomTable']['qc_document_id']),array('class'=>'btn btn-sm tooltip1 btn-default', 'escape'=>false, 'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'left', 'title'=> 'Link new table to this table'));?>
					</div>
				</td>						
			</tr>
		<?php endforeach; ?>
	<?php }else{ ?>
		<tr><td colspan=48>No results found</td></tr>
	<?php } ?>
</table>
<script>$.ajaxSetup({beforeSend:function(){$("#busy-indicator").show();},complete:function(){$("#busy-indicator").hide();}});</script>
<script type="text/javascript">	$().ready(function(){$(".tooltip1").tooltip();});</script>
