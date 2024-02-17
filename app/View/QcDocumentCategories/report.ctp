<?php echo $this->Html->script(array('openwysiwyg/scripts/wysiwyg','openwysiwyg/scripts/wysiwyg-settings','jquery-ui'));
?><?php echo $this->Html->css(array('openwysiwyg/styles/wysiwyg'));
?><script type="text/javascript">
	WYSIWYG.attach('ReportDetails',full);
</script>
<div id="reports_ajax">
	<?php echo $this->Session->flash();?>
	
	<div class="nav">
		<div class="reports form col-md-12">
			<h4>Add Report            <?php echo $this->Html->link(__('List'), array('action' => 'index'),array('id'=>'list','class'=>'label btn-info'));
			?>	    <?php echo $this->Html->link('', '#advanced_search',array('class'=>'glyphicon glyphicon-search h4-title','data-toggle'=>'modal')); ?>
			<?php echo $this->Html->image('indicator.gif', array('id' => 'busy-indicator')); ?>
		</h4>
		<?php echo $this->Form->create('Report',array('controller'=>'reports','action'=>'add'),array('role'=>'form','class'=>'form'));
	?>	<fieldset>
		<div class="panel panel-default">
			<div class="panel-body">
				Below is the system generated report. You can modify this report and save it under report center. <br/>
				Report is rendered in a Text Editor so that you can make the required changes.<br/>
				You can either publish this report as it is or keep it ready in reports center.
			</div>
		</div>	
		<textarea id="ReportDetails" name="data[Report][details]" style="float:left; clear:both">        
			<table cellpadding="2" cellspacing="2" border="1" width="100%" style="font-size:10px">
				<tr>
					<td colspan="4"><h2>Company Name (ISO CERTFICATION DOCUMENTS) :</h2>Powered by : FlinkISO ™</td>
				</tr>
				<tr>
					<td>Document Title </td><td>&nbsp;<?php echo h($qcDocumentCategory['MasterListOfFormat']['title']); ?></td>
					<td>Document Number</td><td>&nbsp;<?php echo h($qcDocumentCategory['MasterListOfFormat']['document_number']); ?></td>
				</tr>
				<tr>
					<td>Revision Number </td><td>&nbsp;<?php echo h($qcDocumentCategory['MasterListOfFormat']['revision_number']); ?></td>
					<td>Revision Date</td><td>&nbsp;<?php echo h($qcDocumentCategory['MasterListOfFormat']['revision_date']); ?></td>
				</tr>
			</table>
			<table cellpadding="2" cellspacing="2" border="1" width="100%" style="font-size:10px">
				<tr>
					<th><?php echo h(Inflector::humanize("sr_no"));?></th>
					<th><?php echo h(Inflector::humanize("name"));?></th>
					<th><?php echo h(Inflector::humanize("short_name"));?></th>
					<th><?php echo h(Inflector::humanize("standard_id"));?></th>
					<th><?php echo h(Inflector::humanize("parent_id"));?></th>
					<th><?php echo h(Inflector::humanize("publish"));?></th>
					<th><?php echo h(Inflector::humanize("record_status"));?></th>
					<th><?php echo h(Inflector::humanize("status_user_id"));?></th>
					<th><?php echo h(Inflector::humanize("branchid"));?></th>
					<th><?php echo h(Inflector::humanize("departmentid"));?></th>
					<th><?php echo h(Inflector::humanize("approved_by"));?></th>
					<th><?php echo h(Inflector::humanize("prepared_by"));?></th>
					<th><?php echo h(Inflector::humanize("company_id"));?></th>
					
				</tr>
				<tr>
					<?php foreach ($qcDocumentCategories as $qcDocumentCategory): ?>
						<tr>
							<td width="50"><?php echo h($qcDocumentCategory['QcDocumentCategory']['sr_no']); ?>&nbsp;</td>
							<td><?php echo h($qcDocumentCategory['QcDocumentCategory']['name']); ?>&nbsp;</td>
							<td><?php echo h($qcDocumentCategory['QcDocumentCategory']['short_name']); ?>&nbsp;</td>
							<td width="50"><?php echo h($qcDocumentCategory['Standard']['name']); ?>&nbsp;</td>
							<td width="50"><?php echo h($qcDocumentCategory['ParentQcDocumentCategory']['name']); ?>&nbsp;</td>
							<td><?php echo h($qcDocumentCategory['QcDocumentCategory']['publish']); ?>&nbsp;</td>
							<td><?php echo h($qcDocumentCategory['QcDocumentCategory']['record_status']); ?>&nbsp;</td>
							<td><?php echo h($qcDocumentCategory['QcDocumentCategory']['status_user_id']); ?>&nbsp;</td>
							<td width="50"><?php echo h($qcDocumentCategory['BranchIds']['name']); ?>&nbsp;</td>
							<td width="50"><?php echo h($qcDocumentCategory['DepartmentIds']['name']); ?>&nbsp;</td>
							<td width="50"><?php echo h($qcDocumentCategory['ApprovedBy']['name']); ?>&nbsp;</td>
							<td width="50"><?php echo h($qcDocumentCategory['PreparedBy']['name']); ?>&nbsp;</td>
							<td width="50"><?php echo h($qcDocumentCategory['Company']['name']); ?>&nbsp;</td>
						</tr>
					<?php endforeach; ?>
				</table>
				<table cellpadding="2" cellspacing="2" border="1" width="100%" style="font-size:10px">
					<tr>
						<td>Prepared By </td><td>&nbsp;</td>
						<td>Approved By</td><td>&nbsp;</td>
					</tr>
				</table>
			</textarea>
			<?php echo $this->Form->input('title');?>
			<?php echo $this->Form->input('branch_id',array('style'=>'width:90%','options'=>$PublishedBranchList));?>
			<?php echo $this->Js->link('Add New',array('controller'=>'branchs','action'=>'add'),array('class'=>'label btn-info','update'=>'#reports_ajax'));?>
			<?php echo $this->Form->input('department_id',array('style'=>'width:90%','options'=>$PublishedDepartmentList));?>
			<?php echo $this->Js->link('Add New',array('controller'=>'departments','action'=>'add'),array('class'=>'label btn-info','update'=>'#reports_ajax'));?>
			<?php echo $this->Form->input('master_list_of_format_id',array('style'=>'width:90%'));?>
			<?php echo $this->Js->link('Add New',array('controller'=>'master_list_of_formats','action'=>'add'),array('class'=>'label btn-info','update'=>'#reports_ajax'));?>
			<?php echo $this->Form->input('description');?>
			<?php echo $this->Form->input('report_date');?>
			<?php echo $this->Form->input('publish');?>
			<?php echo $this->Form->submit('Submit',array('div'=>false,'class'=>'btn btn-primary btn-success'));?>
			<?php echo $this->Form->end();?>
			<?php echo $this->Js->writeBuffer();?>

		</fieldset>
	</div>
	<script> $("#ReportReportDate").datepicker({
		changeMonth: true,
		changeYear: true,
		dateFormat:'yy-mm-dd',
	}); </script>
</div>
<?php echo $this->Js->get('#list');
?><?php echo $this->Js->event('click',$this->Js->request(array('action' => 'index', 'ajax'),array('async' => true, 'update' => '#reports_ajax')));
?>
<?php echo $this->Js->writeBuffer();
?>
<div class="modal fade" id="advanced_search" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Advanced Search</h4>
			</div>
			<div class="modal-body"><?php echo $this->element('advanced-search',array('postData'=>array("sr_no"=>"Sr No","name"=>"Name","short_name"=>"Short Name","record_status"=>"Record Status","branchid"=>"Branchid","departmentid"=>"Departmentid","approved_by"=>"Approved By","prepared_by"=>"Prepared By"),'PublishedBranchList'=>array($PublishedBranchList))); ?></div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div></div></div></div>


		</div></div></div>
	</div>
	<script>$.ajaxSetup({beforeSend:function(){$("#busy-indicator").show();},complete:function(){$("#busy-indicator").hide();}});</script>
	
