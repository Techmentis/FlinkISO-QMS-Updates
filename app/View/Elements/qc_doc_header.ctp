<?php if($document){ ?>
<div class="box box-info doc-header">
	<div class="box-header"><h3 class="box-title" style="width:100%">Document : <?php echo $document['QcDocument']['name']?> <span class="pull-right"><i class="fa fa-folder-open"></i></span></h3></div>
	<div class="box-body">
		<div class="table-responsive">
			<table class="table table-bordered">
				<tr>
					<th><?php echo h(__('Document Number')); ?></th>
					<td><?php echo $document['QcDocument']['document_number']?></td>
					<th><?php echo h(__('Revision Number')); ?></th>
					<td><?php echo $document['QcDocument']['revision_number']?></td>
					<th><?php echo h(__('Date Of Issue')); ?></th>
					<td><?php echo $document['QcDocument']['date_of_issue']?></td>
				</tr>
				<tr>
					<th><?php echo h(__('Prepared By')); ?></th>
					<td><?php echo $document['PreparedBy']['name']?></td>
					<th><?php echo h(__('Approved By')); ?></th>
					<td><?php echo $document['ApprovedBy']['name']?></td>
					<th><?php echo h(__('Issued By')); ?></th>
					<td><?php echo $document['IssuedBy']['name']?></td>
				</tr>
			</table>
		</div>
	</div>
</div>
<?php } ?>
