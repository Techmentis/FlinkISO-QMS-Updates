<div class="box collapsed-box" id="tipsbox">
	<div class="box-header with-border data-header" data-widget="collapse">
		<h3 class="box-title"><i class="fa fa-question-circle fa-lg>"></i>&nbsp;&nbsp;<?php echo h(__('Document Management Tips')); ?>&nbsp;&nbsp;<small><?php echo h(__('Important tips about Document Management.')); ?></small></h3>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus" id="tipsboxbtn"></i></button>
		</div>            
	</div>          
	<div class="box-body">
		<p>
			<strong><?php echo h(__('Document Version:')); ?></strong>
			<ul>
				<li><?php echo h(__('Documents are auto saved. Every time a document is saved, it creates a new version of the document. You can access those versions by clicking on')); ?> <strong><?php echo h(__('Version Numbers')); ?></strong> <?php echo h(__('displayed at the bottom of the document.')); ?></li>
				<li><?php echo h(__('To navigate to a previous version of the document, click on that version number at the bottom.')); ?></li>
				<li><?php echo h(__('By default, it will load a selected previous version below the current document.')); ?></li>
				<li><?php echo h(__('To load the document alongside, click on')); ?> <i class="fa fa-toggle-on fa-lg"></i> <?php echo h(__('icon. This will reload the page and after clicking on the version number, a selected previous version will load next to the current document.')); ?></li>
			</ul>
			<strong><?php echo h(__('Document Revision:')); ?></strong>
			<ul>
				<li><?php echo h(__('Document revision is different than document versions. To add a revision, you must add')); ?> <a href="https://www.flinkiso.com/quality-management-software/document-version-control.html" class="link" style="color:#017ec6; font-weight: 600;" target="_blank"><?php echo h(__('Change Control Table')); ?></a> <?php echo h(__('and follow the change control process. Also refere to')); ?> <a href="https://www.flinkiso.com/manual/building-document-change-request-form.html" class="link" style="color:#017ec6; font-weight: 600;" target="_blank"><?php echo h(__('How to build Change Request Form')); ?></a> <?php echo h(__('to learn how to build your')); ?> <strong><?php echo h(__('Document Change Request HTML Form using FlinkISO APIs.')); ?></strong></li>
			</ul>
			<strong><?php echo h(__('Track Changes')); ?></strong>
			<ul>	
				<li><?php echo h(__('If the document is edited with')); ?> <strong><?php echo h(__('Track Changes ON')); ?></strong> <?php echo h(__('under')); ?> <strong><?php echo h(__('Collaboration')); ?></strong><?php echo h(__(', latest version of the document may be available when the changes are accepted.')); ?></li>
				<li><?php echo h(__('To compare changes from a revision, you must download the document manually and use')); ?> <strong><?php echo h(__('Collaboration')); ?></strong> -> <strong><?php echo h(__('Compare')); ?></strong> <?php echo h(__('option. This option is only available in Edit mode.')); ?></li>
			</ul>
		</p>
	</div>
</div>
<script type="text/javascript">
	$().ready(function(){
		$("#tipsbox").on('click',function(){
			if($('#tipsbox').hasClass('collapsed-box') == true){
				$("#tipsboxbtn").removeClass('fa-plus');
				$("#tipsboxbtn").addClass('fa-minus');                                            
			}else{
				$("#tipsboxbtn").removeClass('fa-minus');
				$("#tipsboxbtn").addClass('fa-plus');
			}
		});
	})
</script>