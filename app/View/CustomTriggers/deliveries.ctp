<div id="main">
	<?php echo $this->Session->flash(); ?>
	<div class="box box-default">
		<div class="box-header">
			<h3 class="box-title"><i class="fa fa-envelope"></i> Email Trigger Deliveries</h3>
			<div class="box-tools pull-right"><?php echo $this->Html->link(__('All rules'), array('action'=>'index'), array('class'=>'btn btn-default btn-sm')); ?></div>
		</div>
		<div class="box-body table-responsive">
			<table class="table table-hover">
				<thead><tr><th>Created</th><th>Event</th><th>Record</th><th>Recipient</th><th>Subject</th><th>Status</th><th>Attempts</th><th>Error</th><th></th></tr></thead>
				<tbody>
				<?php foreach ((array)$deliveries as $delivery): $row = $delivery['EmailTriggerOutbox']; ?>
					<tr>
						<td><?php echo h($row['created']); ?></td>
						<td><?php echo h(Inflector::humanize($row['event_name'])); ?></td>
						<td><?php echo h($row['model_name'].' / '.$row['record_id']); ?></td>
						<td><?php echo h($row['recipient_email']); ?></td>
						<td><?php echo h($row['subject']); ?></td>
						<td><span class="label label-<?php echo $row['status'] === 'sent' ? 'success' : ($row['status'] === 'failed' ? 'danger' : 'warning'); ?>"><?php echo h($row['status']); ?></span></td>
						<td><?php echo (int)$row['attempts']; ?></td>
						<td><?php echo h($row['last_error']); ?></td>
						<td><?php if ($row['status'] === 'failed') echo $this->Form->postLink(__('Retry'), array('action'=>'retry_delivery', $row['id']), array('class'=>'btn btn-xs btn-warning')); ?></td>
					</tr>
				<?php endforeach; ?>
				<?php if (empty($deliveries)): ?><tr><td colspan="9">No deliveries found.</td></tr><?php endif; ?>
				</tbody>
			</table>
			<ul class="pagination">
				<li><?php echo $this->Paginator->prev('&laquo;', array('escape'=>false), null, array('class'=>'disabled','escape'=>false)); ?></li>
				<li><?php echo $this->Paginator->numbers(array('separator'=>'')); ?></li>
				<li><?php echo $this->Paginator->next('&raquo;', array('escape'=>false), null, array('class'=>'disabled','escape'=>false)); ?></li>
			</ul>
		</div>
	</div>
</div>
