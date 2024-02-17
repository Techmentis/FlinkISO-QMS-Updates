<br />
<br />
<table class="table table-responsive">
	<tr>
		<td class="text-center">
			<h1><?php echo $this->Session->read('User.company_name');?></h1>
			<h4><?php echo $this->Session->read('User.branch');?></h4>
			<h4><?php echo $this->Session->read('User.department');?></h4>
			<br /><br />
			<h2><?php echo $qcDocument['QcDocument']['title'];?></h2>
			<h2>
				<small><?php echo $qcDocument['Standard']['name'];?></small><br />
				<small><?php echo $qcDocument['Clause']['title'];?></small><br />
				<small><?php echo $qcDocument['QcDocumentCategory']['name'];?></small>
			</h2>
			<h3><small>Rev. <?php echo $qcDocument['QcDocument']['revision_number'];?></small></h3>
			<br />
			<br />
			<br />
			<div class="row">
				<div class="col-md-3"><p>Prepared By</p><p>
					<?php 
					$sign = $this->requestAction(array('controller'=>'employees','action'=>'get_signatures',$qcDocument['PreparedBy']['id']));				
					?>
					<img src="<?php echo $sign;?>" height="60">
				</p><p><?php echo $qcDocument['PreparedBy']['name'];?></p></div>
				<div class="col-md-3"><p>Approved By</p><p>
					<?php 
					$sign = $this->requestAction(array('controller'=>'employees','action'=>'get_signatures',$qcDocument['ApprovedBy']['id']));				
					?>
					<img src="<?php echo $sign;?>" height="60">
				</p><p><?php echo $qcDocument['ApprovedBy']['name'];?></p></div>
				<div class="col-md-3"><p>Issued By</p><p>
					<?php 
					$sign = $this->requestAction(array('controller'=>'employees','action'=>'get_signatures',$qcDocument['IssuedBy']['id']));				
					?>
					<img src="<?php echo $sign;?>" height="60">
				</p><p><?php echo $qcDocument['IssuedBy']['name'];?></p></div>
				<div class="col-md-3"><p>Downloaded By</p><p>
					<?php 
					$sign = $this->requestAction(array('controller'=>'employees','action'=>'get_signatures',$this->Session->read('User.employee_id')));
					?>
					<img src="<?php echo $sign;?>" height="60">
				</p><p><?php echo $this->Session->read('User.name');?></p></div>
			</div>
		</td>
	</tr>
</table> 

<table class="table table-responsive table-bordered">
	<tr>
		<th colspan="8"><?php echo $qcDocument['QcDocument']['title'];?></th>
	</tr>
	<tr>
		<th>Standard</th><td colspan="2"><?php echo $qcDocument['Standard']['name'];?></td>
		<th>Document Category</th><td colspan="2"><?php echo $qcDocument['QcDocumentCategory']['name'];?></td>
		<th>Clause</th><td colspan="2"><?php echo $qcDocument['Clause']['title'];?></td>
	</tr>
	<tr>
		<th>Document No.</th><td><?php echo $qcDocument['QcDocument']['document_number'];?></td>
		<th>Issue No.</th><td><?php echo $qcDocument['QcDocument']['issue_number'];?></td>
		<th>Date Of Issue</th><td><?php echo $qcDocument['QcDocument']['date_of_issue'];?></td>
		<th>Effective From Date</th><td><?php echo $qcDocument['QcDocument']['effective_from_date'];?></td>
	</tr>
	<tr>
		<th>Revision No.</th><td><?php echo $qcDocument['QcDocument']['revision_number'];?></td>
		<th>Date Created.</th><td><?php echo $qcDocument['QcDocument']['date_created'];?></td>
		<th>Revision Date</th><td><?php echo $qcDocument['QcDocument']['revision_date'];?></td>
		<th>Date Of Review</th><td><?php echo $qcDocument['QcDocument']['date_of_review'];?></td>
	</tr>	
	<tr>
		<th>Prepared By</th><td><?php echo $qcDocument['PreparedBy']['name'];?></td>
		<th>Approved By</th><td><?php echo $qcDocument['ApprovedBy']['name'];?></td>
		<th>Issued By</th><td><?php echo $qcDocument['IssuedBy']['name'];?></td>
		<th>Downloaded By</th><td><?php echo $this->Session->read('User.name');?></td>
	</tr>
</table>
<h4>Record</h4>
<table class="table table-responsive table-bordered">
	<?php foreach(json_decode($customTable['CustomTable']['fields'],true) as $field){ ?>
		<tr>
			<th><?php echo Inflector::humanize($field['field_name']);?></th>
			<td>
				<?php 
				if($field['linked_to'] != -1 && $field['display_type'] == 3){
					echo $record[Inflector::classify($field['field_name'])]['name'];
					echo $record[Inflector::classify($field['field_name'])]['title'];
				}

				if($field['linked_to'] != -1 && $field['display_type'] == 4){
					$options = json_decode($record[$mainModel][$field['field_name']],true);
					foreach($options as $option){
						$val = $this->requestAction(array('action'=>'get_val','model'=>$field['linked_to'],'record'=>$option));
						echo $val.', ';						
					}
				}

				if($field['display_type'] == 1){
					$options = explode(',',$field['csvoptions']);
					echo $options[$record[$mainModel][$field['field_name']]];
				}

				if($field['display_type'] == 0){
					echo $record[$mainModel][$field['field_name']];
				}
				

				?>
			</td>
		</tr>
	<?php } ?>
	<tr>
		<th>Prepared By</th>
		<td><?php echo $record['PreparedBy']['name'];?></td>
	</tr>
	<tr>
		<th>Approved By</th>
		<td><?php echo $record['ApprovedBy']['name'];?></td>
	</tr>
	<tr>
		<th>Publish</th>
		<td><?php echo $record[$mainModel]['publish']?'Yes':'No';?></td>
	</tr>
</table>
<h4>Approvals</h4>
<?php if($approvals){ ?>
	<table class="table table-responsive ">
		<tr>
			<th>Date/Time</th>
			<th>From</th>
			<th>To</th>
			<th>Comments</th>
			<th>Approver Comments/ Response</th>
			<th>Status</th>
		</tr>
		<?php foreach($approvals as $approval){ ?>
			<tr style="font-weight: 500;">
				<td><?php echo $approval['Approval']['created'];?></td>
				<td><?php echo $approval['From']['name'];?></td>
				<td><?php echo $approval['User']['name'];?></td>
				<td><?php echo $approval['Approval']['comments'];?></td>
				<td><?php echo $approval['Approval']['approver_comments'];?></td>
				<td><?php echo $approval['Approval']['status'];?></td>
			</tr>
			<?php foreach($approval['ApprovalComment'] as $comments){ ?>
				<tr>
					<td><?php echo $comments['created'];?></td>
					<td>
						<?php 
						$val = $this->requestAction(array('action'=>'get_val','model'=>'User','record'=>$comments['from']));
						echo $val.', ';
						?>
					</td>
					<td>
						<?php 
						$val = $this->requestAction(array('action'=>'get_val','model'=>'User','record'=>$comments['user_id']));
						echo $val.', ';
						?>
					</td>
					<td><?php echo $comments['comments'];?></td>
					<td><?php echo $comments['response'];?></td>
					<td><?php echo $comments['response_status'];?></td>
				</tr>
			<?php } ?>
		<?php } ?>
	</table>
<?php } ?>
<?php if($linkedRecords){ ?>
	<?php foreach($linkedRecords as $form => $records){ ?>
		<h4><?php echo $form;?> Record</h4> 
		<?php foreach($records as $record){
			$linkedModel = array_keys($record);
			$fields = $this->requestAction(array('action'=>'get_fields',$record[$linkedModel[0]]['custom_table_id']));
			?>
			<table class="table table-responsive table-bordered">
				<?php foreach(json_decode($fields,true) as $field){ ?>				
					<tr>
						<th><?php echo Inflector::humanize($field['field_name']);?></th>
						<td>
							<?php 
							if($field['linked_to'] != -1 && $field['display_type'] == 3){
								echo $record[Inflector::classify($field['field_name'])]['name'];
								echo $record[Inflector::classify($field['field_name'])]['title'];
							}

							if($field['linked_to'] != -1 && $field['display_type'] == 4){
								$options = json_decode($record[$linkedModel[0]][$field['field_name']],true);
								foreach($options as $option){
									$val = $this->requestAction(array('action'=>'get_val','model'=>$field['linked_to'],'record'=>$option));
									echo $val.', ';						
								}
							}

							if($field['display_type'] == 1){
								$options = explode(',',$field['csvoptions']);
								echo $options[$record[$linkedModel[0]][$field['field_name']]];
							}

							if($field['display_type'] == 0){
								echo $record[$linkedModel[0]][$field['field_name']];
							}
							

							?>
						</td>
					</tr>
				<?php } ?>
				<tr>
					<th>Prepared By</th>
					<td><?php echo $record['PreparedBy']['name'];?></td>
				</tr>
				<tr>
					<th>Approved By</th>
					<td><?php echo $record['ApprovedBy']['name'];?></td>
				</tr>
				<tr>
					<th>Publish</th>
					<td><?php echo $record[$linkedModel[0]]['publish']?'Yes':'No';?></td>
				</tr>
			</table>
		<?php }
	}?>	
<?php } ?>

