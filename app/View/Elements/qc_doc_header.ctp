<div class="box box-info doc-header">
	<div class="box-header"><h3 class="box-title" style="width:100%">Document : <?php echo $document['QcDocument']['name']?> <span class="pull-right"><i class="fa fa-folder-open"></i></span></h3></div>
	<div class="box-body">
		<div class="table-responsive">
			<table class="table table-bordered">
				<tr>
					<th>Document Number</th>
					<td><?php echo $document['QcDocument']['document_number']?></td>
					<th>Revision Number</th>
					<td><?php echo $document['QcDocument']['revision_number']?></td>
					<th>Date Of Issue</th>
					<td><?php echo $document['QcDocument']['date_of_issue']?></td>
				</tr>
				<tr>
					<th>Prepared By</th>
					<td><?php echo $document['PreparedBy']['name']?></td>
					<th>Approved By</th>
					<td><?php echo $document['ApprovedBy']['name']?></td>
					<th>Issued By</th>
					<td><?php echo $document['IssuedBy']['name']?></td>
				</tr>
			</table>
		</div>
	</div>
</div>
