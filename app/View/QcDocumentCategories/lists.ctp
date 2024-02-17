<div  id="main">
	<?php echo $this->Session->flash();?>	
	<div class="qcDocumentCategories ">
		<?php echo $this->element('nav-header-lists',array('postData'=>array('pluralHumanName'=>'Qc Document Categories','modelClass'=>'QcDocumentCategory','options'=>array("sr_no"=>"Sr No","name"=>"Name","short_name"=>"Short Name","record_status"=>"Record Status","approved_by"=>"Approved By","prepared_by"=>"Prepared By"),'pluralVar'=>'qcDocumentCategories'))); ?>
		<div class="nav">
			<div id="tabs">	
				<ul>
					<li><?php echo $this->Html->link(__('New Qc Document Category'), array('action' => 'add_ajax')); ?></li>
					<li><?php // echo $this->Html->link(__('Add Standard'), array('controller' => 'standards', 'action' => 'add_ajax')); ?> </li>
					<li><?php // echo $this->Html->link(__('Add Parent Qc Document Category'), array('controller' => 'qc_document_categories', 'action' => 'add_ajax')); ?> </li>
					<li><?php // echo $this->Html->link(__('Add Company'), array('controller' => 'companies', 'action' => 'add_ajax')); ?> </li>
					<li><?php // echo $this->Html->link(__('Add Prepared By'), array('controller' => 'employees', 'action' => 'add_ajax')); ?> </li>
					<li><?php // echo $this->Html->link(__('Add Qc Document'), array('controller' => 'qc_documents', 'action' => 'add_ajax')); ?> </li>
					<li><?php echo $this->Html->image('indicator.gif', array('id' => 'busy-indicator','class'=>'pull-right')); ?></li>
				</ul>
			</div>
		</div>
		<div id="qcDocumentCategories_tab_ajax"></div>
	</div>

	<script>
		$(function() {
			$( "#tabs" ).tabs({
				beforeLoad: function( event, ui ) {
					ui.jqXHR.error(function() {
						ui.panel.html(
							"Error Loading ... " +
							"Please contact administrator." );
					});
				}
			});
		});
	</script>

	<?php echo $this->element('export'); ?>
	<?php echo $this->element('advanced-search',array('postData'=>array("sr_no"=>"Sr No","name"=>"Name","short_name"=>"Short Name","record_status"=>"Record Status","approved_by"=>"Approved By","prepared_by"=>"Prepared By"),'PublishedBranchList'=>array($PublishedBranchList))); ?>
	<?php echo $this->element('import',array('postData'=>array("sr_no"=>"Sr No","name"=>"Name","short_name"=>"Short Name","record_status"=>"Record Status","approved_by"=>"Approved By","prepared_by"=>"Prepared By"))); ?>
</div>
<script>$.ajaxSetup({beforeSend:function(){$("#busy-indicator").show();},complete:function(){$("#busy-indicator").hide();}});</script>
