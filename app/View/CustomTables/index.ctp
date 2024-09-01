<?php if($customTables){ ?>
	<?php echo $this->element('checkbox-script'); ?>
	<div  id="main">	
		<?php echo $this->Session->flash();?>	
		<div class="customTables ">
			<?php echo $this->element('nav-header-lists',array('postData'=>array('pluralHumanName'=>'Custom Tables','modelClass'=>'CustomTable','options'=>array("sr_no"=>"Sr No","name"=>"Name","fields"=>"Fields"),'pluralVar'=>'customTables'))); ?>

			<script type="text/javascript">
				$(document).ready(function(){
					$('table th a, .pag_list li span a').on('click', function() {
						var url = $(this).attr("href");
						$('#main').load(url);
						return false;
					});
				});
			</script>	

			<style type="text/css">
				.btn .badge{
					position: absolute;
					font-size: 8px;
					padding: 3px 5px;
					margin-top: -5px;
					z-index: 2;
				}
				.btn:hover{
		/*	margin-right: -1px;
			margin-left: -1px !important;
			border: 1px solid transparent;*/
		}
		.box-title{
			font-size: 14px !important;
		}
	</style>
	<div class="btn-group">
		<?php 

		if($this->request->params['named']['table_type'] == 4){
			echo $this->Html->link('All',array('action'=>'index','table_type'=>4),array('class'=>'btn btn-sm btn-bold  btn-success'));
		}else{
			echo $this->Html->link('All',array('action'=>'index','table_type'=>4),array('class'=>'btn btn-sm btn-bold  btn-default'));
		}

		if($this->request->params['named']['table_type'] == 1){
			echo $this->Html->link('Documents',array('action'=>'index','table_type'=>1),array('class'=>'btn btn-sm btn-bold  btn-success'));
		}else{
			echo $this->Html->link('Documents',array('action'=>'index','table_type'=>1),array('class'=>'btn btn-sm btn-bold  btn-default'));
		}

		// if($this->request->params['named']['table_type'] == 2){
		// 	echo $this->Html->link('Processes',array('action'=>'index','table_type'=>2),array('class'=>'btn btn-sm btn-bold  btn-success'));
		// }else{
		// 	echo $this->Html->link('Processes',array('action'=>'index','table_type'=>2),array('class'=>'btn btn-sm btn-bold  btn-default'));
		// }
		
		if($this->request->params['named']['table_type'] == 3){
			echo $this->Html->link('Masters',array('action'=>'index','table_type'=>3),array('class'=>'btn btn-sm btn-bold  btn-success'));
		}else{
			echo $this->Html->link('Masters',array('action'=>'index','table_type'=>3),array('class'=>'btn btn-sm btn-bold  btn-default'));
		}	
		?>	
	</div>


	<?php echo $this->Form->create(array('class'=>'no-padding no-margin no-background'));?>
	<?php if($customTables){ ?>
		<div class="row">
			<?php foreach ($customTables as $customTable): 
				if($customTable['CustomTable']['childDoc'] == 0 ){
					$textColor = 'text-success';
				}else{
					$textColor = 'text-warning';
				}
			?>
				<div class="col-md-6 col-lg-4">
					<div class="box" style="<?php echo $topBorder;?>">
						<div class="box-header with-border">
							<h4 class="box-title">
								<?php 						

								if($customTable['CustomTable']['publish'] == 1 && $customTable['CustomTable']['table_locked'] == 0){
										// echo "<span class='text-success'><i class='fa fa-check'></i></span>";
									$titleClass = $textColor;
									$btnClass = 'success';	
								}else{
										// echo "<span class='text-danger'><i class='fa fa-exclamation-triangle'></i></span>";	
									$titleClass = 'text-danger';
									$btnClass = 'danger';												

								}
								?>
								<?php 
								if($customTable['CustomTable']['table_type'] == 2)echo $customTable['CustomTable']['name']; 
								else echo $customTable['CustomTable']['name'];?>
								<?php
								if($this->request->params['named']['table_type'] != 3){
									echo "</h4><br /><small>";
									if(!$customTable['Process']['name']){
										if(strlen($customTable['QcDocument']['name'])>45){
											$doc = "<strong class='".$titleClass."' >".substr($customTable['QcDocument']['name'], 0,45) ."...</strong>: ". $customTable['QcDocument']['document_number'] ."-". $customTable['QcDocument']['revision_number'] .".". $customTable['QcDocument']['file_type'];	
										}else{
											$doc = "<strong class='".$titleClass."' >".substr($customTable['QcDocument']['name'], 0,45) ."</strong>: ". $customTable['QcDocument']['document_number'] ."-". $customTable['QcDocument']['revision_number'] .".". $customTable['QcDocument']['file_type'];
										}
										

										echo $this->Html->link($doc,array('controller'=>'qc_documents','action'=>'view',$customTable['QcDocument']['id'],'timestamp'=>date('ymdhis')),array('target'=>'_blank','escape'=>false));	
									}else{
										$doc = "<strong class='".$titleClass."' >".$customTable['Process']['name'] ."</strong> <br /> <small>". $customTable['Process']['file_name'] ."</small>";
										echo $this->Html->link($doc,array('controller'=>'processes','action'=>'view',$customTable['Process']['id'],'timestamp'=>date('ymdhis')),array('target'=>'_blank','escape'=>false));									
									}
								}else{
									echo "</h4>";
								}
								?>
								</small>
							<span class="pull-right badge btn-<?php echo $btnClass;?>"><?php echo $customTable['CustomTable']['linked'];?></span>
						</div>
						<div class="box-body">				
							<?php
							if($customTable['CustomTable']['table_type'] == 2){
								echo "Master Table";
							}else{
								if($customTable['CustomTable']['description'])
									echo h(substr($customTable['CustomTable']['description'], 0,120)) .' ...&nbsp;';	
								else echo "Description not added.";
							}
							?>
						</div>
						<div class="box-footer text-right">	
							<div class="pull-left">
								<p style="padding-top:10px">
									<small>
										<?php 
									// if($customTable['CustomTable']['table_type'] == 0) echo '<i class="fa fa-folder-open  text-default"></i>&nbsp;';
									// if($customTable['CustomTable']['table_type'] == 1) echo '<i class="fa fa-link text-default"></i>&nbsp;';
										?>
										<?php 							
										if(strlen($customTable['CustomTable']['table_name']) > 35){
											echo h(substr($customTable['CustomTable']['table_name'], 0, 35)).'...';
										}else{
											echo h($customTable['CustomTable']['table_name']);
										} ?>
									</small>
								</p>
							</div>
							<div class="btn-group btn-no-border">
								<?php 							

								if($customTable['CustomTable']['publish'] == 1 && $customTable['CustomTable']['table_locked'] == 0 && $customTable['QcDocument']['parent_document_id'] == -1){
									echo $this->Html->link('<i class="fa fa-plus-square-o fa-lg text-info"></i>',array('controller'=>$customTable['CustomTable']['table_name'], 'action'=>'add', 'custom_table_id'=>$customTable['CustomTable']['id'],'qc_document_id'=>$customTable['CustomTable']['qc_document_id']),array('class'=>'tooltip1 btn btn-sm btn-default', 'escape'=>false, 'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'left', 'title'=> 'Goto Add page'));
								}
								if($this->request->params['named']['table_type'] == 3){
									echo $this->Html->link('<i class="fa fa-plus-square-o fa-lg text-info"></i>',array('controller'=>$customTable['CustomTable']['table_name'], 'action'=>'add', 'custom_table_id'=>$customTable['CustomTable']['id'],'qc_document_id'=>$customTable['CustomTable']['qc_document_id']),array('class'=>'tooltip1 btn btn-sm btn-default', 'escape'=>false, 'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'left', 'title'=> 'Goto Add page'));
								}?>
								

								<?php echo $this->Html->link('<i class="fa fa-table fa-lg text-info"></i>',array('controller'=>$customTable['CustomTable']['table_name'],'action'=>'index','custom_table_id'=>$customTable['CustomTable']['id'],'qc_document_id'=>$customTable['CustomTable']['qc_document_id']),array('class'=>'tooltip1 btn btn-sm btn-default', 'escape'=>false, 'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'left', 'title'=> 'Goto index page'));?>
								<?php
								echo $this->Html->link('<i class="fa fa-bar-chart fa-lg text-info"></i>',array('controller'=>$customTable['CustomTable']['table_name'], 'action'=>'reports','custom_table_id'=>$customTable['CustomTable']['id']),
									array('class'=>'tooltip1 btn btn-sm btn-default','escape'=>false,'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'bottom', 'title'=> 'Reports'));
									?>
									
									<?php echo $this->Html->link('<i class="fa fa-gears fa-lg text-success"></i>',array('action'=>'view',$customTable['CustomTable']['id'],'timestamp'=>date('ymdhis')),array('class'=>'tooltip1 btn btn-sm btn-default', 'escape'=>false, 'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'left', 'title'=> 'View/ Recreate'));?>
								</div>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			<?php }else{ ?>
				
			<?php } ?>
		</div>
		<?php echo $this->Form->end();?>			
		
		<p>
			<?php
			echo $this->Paginator->options(array(			
			));
			
			echo $this->Paginator->counter(array(
				'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
			));
		?>			</p>
		<ul class="pagination">
			<?php
			echo "<li class='previous'>".$this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'))."</li>";
			echo "<li>".$this->Paginator->numbers(array('separator' => ''))."</li>";
			echo "<li class='next'>".$this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'))."</li>";
			?>
		</ul>
	</div>
	</div>
	</div>	
	</div>

	<script>$.ajaxSetup({beforeSend:function(){$("#busy-indicator").show();},complete:function(){$("#busy-indicator").hide();}});</script>
	<script type="text/javascript">	$().ready(function(){$(".tooltip1").tooltip();});</script>
<?php }else{ ?>
	<?php echo $this->element('nav-header-lists',array('postData'=>array('pluralHumanName'=>'Custom Tables','modelClass'=>'CustomTable','options'=>array("sr_no"=>"Sr No","name"=>"Name","fields"=>"Fields"),'pluralVar'=>'customTables'))); ?>

			<script type="text/javascript">
				$(document).ready(function(){
					$('table th a, .pag_list li span a').on('click', function() {
						var url = $(this).attr("href");
						$('#main').load(url);
						return false;
					});
				});
			</script>	

	<div class="row">
		<div class="col-md-12">
			<h3>How to Build Custom HTML Forms</h3>
			<p>In order to use this section, you must add Documents to the application first. Once the documents are added, you can build Custom HTML Forms for each document by clicking on Database icon on the document's view page.</p>
			<p>You can use the existing available forms by downloading them. You can edit those forms as per your requirments once you download them.</p>
			<p>If the form/ module you need, does not exist, you can build your Forms.</p>
			<p>Additionally you can also refere to following links & video for more details.
				<ul>
					<li><strong><a href="https://www.flinkiso.com/manual/custom-html-forms.html" target="_blank">https://www.flinkiso.com/manual/custom-html-forms.html</a></strong></li>
					<li><strong><a href="https://www.flinkiso.com/manual/custom-form-layouts.html" target="_blank">https://www.flinkiso.com/manual/custom-form-layouts.html</a></strong></li>
					<li><strong><a href="https://www.flinkiso.com/manual/custom-forms.html" target="_blank">https://www.flinkiso.com/manual/custom-forms.html</a></strong></li>
				</ul>
			</p>
			<p><hr /></p>	
		</div>
		<div class="col-md-7">		
			<div class="embed-responsive embed-responsive-16by9">	
			<iframe class="embed-responsive-item" src="https://www.youtube.com/embed/kvqa19DJzsY?si=6PReZn8PSIKcpoqG" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
			</div>
		</div>
		<div class="col-md-5">
			<h4>Types of HTML Forms You can create</h4> 
                <?php 
                echo $this->Html->link($this->Html->Image('structure.png',array('class'=>'img-responsive')),array('controller'=>'img','action'=>'structure.png'),array('target'=>'_blank', 'class'=>'img-responsive','escape'=>false));?>    
		</div>		
	</div>
<?php } ?>

