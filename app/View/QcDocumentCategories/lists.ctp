<div  id="main">
	<?php echo $this->Session->flash();?>	
	<div class="qcDocumentCategories ">
		<?php echo $this->element('nav-header-lists',array('postData'=>array('pluralHumanName'=>__('Qc Document Categories'),'modelClass'=>'QcDocumentCategory','options'=>array("sr_no"=>__("Sr No"),"name"=>__("Name"),"short_name"=>__("Short Name"),"record_status"=>__("Record Status"),"approved_by"=>__("Approved By"),"prepared_by"=>__("Prepared By")),'pluralVar'=>'qcDocumentCategories'))); ?>
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
							<?php echo json_encode(__('Error Loading ... '), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?> +
							<?php echo json_encode(__('Please contact administrator.'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?> );
					});
				}
			});
		});
	</script>

	<?php echo $this->element('export'); ?>
	<?php echo $this->element('advanced-search',array('postData'=>array("sr_no"=>__("Sr No"),"name"=>__("Name"),"short_name"=>__("Short Name"),"record_status"=>__("Record Status"),"approved_by"=>__("Approved By"),"prepared_by"=>__("Prepared By")),'PublishedBranchList'=>array($PublishedBranchList))); ?>
	<?php echo $this->element('import',array('postData'=>array("sr_no"=>"Sr No","name"=>"Name","short_name"=>"Short Name","record_status"=>"Record Status","approved_by"=>"Approved By","prepared_by"=>"Prepared By"))); ?>
</div>
<script>$.ajaxSetup({beforeSend:function(){$("#busy-indicator").show();},complete:function(){$("#busy-indicator").hide();}});</script>
