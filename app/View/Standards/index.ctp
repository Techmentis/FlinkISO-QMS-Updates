<style type="text/css">
	.ui-tabs .ui-tabs-panel{padding: 0 !important;}	
</style>
<?php echo $this->Session->flash();?>
<div><?php echo $this->element('nav-header-lists',array('postData'=>array('pluralHumanName'=>'Standard-wise Documents & tables','modelClass'=>'Standard','options'=>array("sr_no"=>"Sr No","name"=>"Name","details"=>"Details"),'pluralVar'=>'standards'))); ?>
<div class="main">
	<div class="row">
		<div class="col-md-12">
			<div id="standards_tabs">	
				<ul>
					<?php
					foreach ($standards as $key => $value) {
						echo "<li>". $this->Html->link($value, array('action' => 'documents',$key,'jqload'=>0),array('id'=>'document-standard-'.$key))."</li>";
					}
					?>
					<li><?php echo $this->Html->image('indicator.gif', array('id' => 'busy-indicator','class'=>'pull-right')); ?></li>
				</ul>
			</div>
			<div id="documents_tab_ajax"></div>
		</div>
	</div>
</div>
<script>
	$(function() {
		$( "#standards_tabs" ).tabs({
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
<script>$.ajaxSetup({beforeSend:function(){$("#busy-indicator").show();},complete:function(){$("#busy-indicator").hide();}});</script>
