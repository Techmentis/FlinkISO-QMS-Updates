<div class="row">
	<div class="col-md-12">
		<div class="table-responsive">
			<table class="table">
				<tr>
					<th><?php echo h(__('Document Revision Number')); ?></th>
					<th><?php echo h(__('Document Revision Date')); ?></th>
					<th><?php echo h(__('Effective From Date')); ?></th>					
					<th><?php echo h(__('Last Modified Date')); ?></th>
					<th><?php echo h(__('Last Modified By')); ?></th>					
					<th><?php echo h(__('View')); ?></th>
				</tr>
				<?php if($qcDocuments){
					foreach($qcDocuments as $qcDocument){ ?>
						<tr>
							<td><?php echo $qcDocument['QcDocument']['revision_number'];?></td>
							<td><?php echo $qcDocument['QcDocument']['revision_date'];?></td>
							<td><?php echo $qcDocument['QcDocument']['effective_from_date'];?></td>							
							<td><?php echo $qcDocument['QcDocument']['modified'];?></td>
							<td><?php echo $qcDocument['ModifiedBy']['name'];?></td>
							<td><?php echo $this->Html->link(__('View'),array('action'=>'view_archived',$qcDocument['QcDocument']['id']),array('class'=>'btn btn-xs btn-success','target'=>'_blank'));?></td>
						</tr>
					<?php } ?>

				<?php }else{ ?>
					<tr>
						<td colspan="7"><?php echo h(__('No records found.')); ?></td>
					</tr>
				<?php }?>
			</table>
		</div>
	</div>
</div>