<?php $standard_id =  $this->request->params['pass'][0];?>
<style>
	.fstrong{font-weight: 800; color: #4a4a4a;}
	.panel-group ul { margin: 0px !important; padding: 0px !important}
	.panel-group ul li{ list-style: none; margin: 0; margin-left: 0 !important; padding-left: 0 !important; padding: 5px 0 !important; }	
	.panel-collapse .panel-body { max-height: 450px !important; overflow: scroll !important;}
</style>
<div class="nav">	
	<div class="col-md-3">
		
		<div class="panel-group" id="accordion_<?php echo $standard_id;?>" role="tablist" aria-multiselectable="true">
			<?php foreach ($final as $clause) { ?>
				<div class="panel panel-default">          
					<div class="panel-heading" role="tab" id="heading_<?php echo str_replace('.', '_', $clause['clause']); ?>">
						<?php echo $this->Html->link($clause['clause'] .'-'. $clause['title'],'#collapse_'.$standard_id.'_'.str_replace('.', '_', $clause['clause']),array('class'=>'fstrong', 'role'=>'button','data-toggle'=>'collapse','data-parent'=>'#accordion_'.$standard_id,'aria-expanded'=>'true', 'aria-controls'=>'collapse_'.$clause['clause'])); ?>
						
					</div>
					<div id="collapse_<?php echo $standard_id;?>_<?php echo str_replace('.', '_', $clause['clause']); ?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading_<?php echo str_replace('.', '_', $clause['clause']); ?>">
						<div class="panel-body">
							<ul>
								<?php 
								echo '<li>'. $this->Html->link('<strong>' . $clause['clause'] . '</strong> - '. $clause['title'] ,'#',
									array('escape'=>false, 'class'=>'clauses_'.$standard_id, 'id'=>'clause-'.$clause['id'])) .'</li>';
								if(count($clause['sub'])>0){
									foreach ($clause['sub'] as $subs) {
										echo '<li>'. $this->Html->link('<strong>' . $subs['Clause']['sub-clause'] . '</strong> - '. $subs['Clause']['title'] ,'#',
											array('escape'=>false, 'class'=>'clauses_'.$standard_id, 'id'=>'clause-'.$subs['Clause']['id'])) .'</li>';
									}
								} 
								?>
							</ul>
						</div>
					</div>
				</div>
			<?php } ?>
		</div>
		<div class="btn-group">
			<?php 
			echo $this->Html->link('<i class="fa fa-trash-o fa-lg"></i>',array('action'=>'delete_standard',$standard_id),array('class'=>'btn btn-sm btn-default','style'=>'margin-bottom:20px','escape'=>false));
			echo $this->Html->link('<i class="fa fa-gear fa-lg"></i>',array('action'=>'edit',$standard_id),array('class'=>'btn btn-sm btn-default','style'=>'margin-bottom:20px','escape'=>false));
			?>
		</div>
	</div>
	<div class="col-md-9">
		<div id="load-files-<?php echo $standard_id;?>"></div>
	</div>
</div>
<script type="text/javascript">	
	$('.clauses_<?php echo $standard_id;?>').click(function(){
		$('#load-files-<?php echo $standard_id;?>').load("<?php echo Router::url('/', true); ?>standards/files/" + this.id +"/<?php echo $standard_id;?>");
	});
</script>
