<?php
$docarray = array('doc','docx');
$sheetarray = array('xls','xlsx');
$pdfarray = array('pdf');
$pptarray = array('ppt','pptx');
?>
<div  id="main">	
		<?php echo $this->Session->flash();?>	
		<div class="customTables ">
			<h4>Forms</h4>
			<style type="text/css">
				.btn .badge{
					position: absolute;
					font-size: 8px;
					padding: 3px 5px;
					margin-top: -5px;
					z-index: 2;
				}
				.box-title{
					font-size: 14px !important;
				}
			</style>
			<div class="btn-group">
			<?php 
				if($this->request->params['named']['table_type'] == 4 && !isset($this->request->params['named']['standard_id'])){
					echo $this->Html->link('All',array('action'=>'index','table_type'=>4),array('class'=>'btn btn-sm btn-bold  btn-info'));
				}else{
					echo $this->Html->link('All',array('action'=>'index','table_type'=>4),array('class'=>'btn btn-sm btn-bold  btn-default'));
				}

				if($this->request->params['named']['table_type'] == 1){
					echo $this->Html->link('Documents',array('action'=>'index','table_type'=>1),array('class'=>'btn btn-sm btn-bold  btn-info'));
				}else{
					echo $this->Html->link('Documents',array('action'=>'index','table_type'=>1),array('class'=>'btn btn-sm btn-bold  btn-default'));
				}
				
				if($this->request->params['named']['table_type'] == 5){
					echo $this->Html->link('Child Documents',array('action'=>'index','table_type'=>5),array('class'=>'btn btn-sm btn-bold  btn-info'));
				}else{
					echo $this->Html->link('Child Documents',array('action'=>'index','table_type'=>5),array('class'=>'btn btn-sm btn-bold  btn-default'));
				}

				if($this->request->params['named']['table_type'] == 3){
					echo $this->Html->link('Masters',array('action'=>'index','table_type'=>3),array('class'=>'btn btn-sm btn-bold  btn-info'));
				}else{
					echo $this->Html->link('Masters',array('action'=>'index','table_type'=>3),array('class'=>'btn btn-sm btn-bold  btn-default'));
				}

				foreach($standards as $standard_id => $standard){
					$standardTitle = $standard .'&nbsp;&nbsp;<div class="badge">'.$cTableCount[$standard_id].'</div>';
					if(isset($this->request->params['named']['standard_id']) && $this->request->params['named']['standard_id'] == $standard_id){
						echo $this->Html->link($standardTitle,array('action'=>'index','standard_id'=>$standard_id),array('class'=>'btn btn-sm btn-bold  btn-info', 'escape'=>false));
					}else{
						echo $this->Html->link($standardTitle,array('action'=>'index','standard_id'=>$standard_id),array('class'=>'btn btn-sm btn-bold  btn-default', 'escape'=>false));
					}					
				}
			?>
		</div>
<?php if($customTables){ ?>
	<?php echo $this->element('checkbox-script'); ?>	
		<?php echo $this->Form->create(array('class'=>'no-padding no-margin no-background'));?>
		<?php if($customTables){ 
			$tblcount = 0; ?>
			<div class="row">
				<div class="col-md-12">
					<h4>HTML Forms Shared With You</h4>
					<table cellpadding="0" cellspacing="0" class="table table-striped table-hover index" id="customTablesTable">					
						<thead>
							<tr>
								<th><?php echo $this->Paginator->sort('name'); ?></th>
								<th><?php echo $this->Paginator->sort('table_name'); ?></th>								
								<th><?php echo $this->Paginator->sort('qc_document_id'); ?></th>
								<th><?php echo $this->Paginator->sort('table_version'); ?></th>
								<th><?php echo $this->Paginator->sort('publish'); ?></th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
						<?php foreach ($customTables as $customTable): ?>							
							<tr>
								<td><?php echo $customTable['CustomTable']['name'];?></td>
								<td><?php echo $customTable['CustomTable']['table_name'];?></td>
								<td><?php echo $customTable['QcDocument']['name'];?></td>
								<td><?php echo $customTable['CustomTable']['table_version'];?></td>
								<td><?php echo $customTable['CustomTable']['publish'];?></td>
								<td>
									<div class="btn-group btn-no-border">
										<?php
										if($customTable['CustomTable']['publish'] == 1 && $customTable['CustomTable']['table_locked'] == 0 && $customTable['QcDocument']['parent_document_id'] == -1){
											echo $this->Html->link('<i class="fa fa-plus-square-o fa-lg text-default"></i>',array('controller'=>$customTable['CustomTable']['table_name'], 'action'=>'add', 'custom_table_id'=>$customTable['CustomTable']['id'],'qc_document_id'=>$customTable['CustomTable']['qc_document_id']),array('class'=>'tooltip1 btn btn-sm btn-default', 'escape'=>false, 'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'left', 'title'=> 'Goto Add page'));
										}
										if($this->request->params['named']['table_type'] == 3){
											echo $this->Html->link('<i class="fa fa-plus-square-o fa-lg text-default"></i>',array('controller'=>$customTable['CustomTable']['table_name'], 'action'=>'add', 'custom_table_id'=>$customTable['CustomTable']['id'],'qc_document_id'=>$customTable['CustomTable']['qc_document_id']),array('class'=>'tooltip1 btn btn-sm btn-default', 'escape'=>false, 'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'left', 'title'=> 'Goto Add page'));
										}?>												
										
										<?php echo $this->Html->link('<i class="fa fa-table fa-lg text-default"></i>',array('controller'=>$customTable['CustomTable']['table_name'],'action'=>'index','custom_table_id'=>$customTable['CustomTable']['id'],'qc_document_id'=>$customTable['CustomTable']['qc_document_id']),array('class'=>'tooltip1 btn btn-sm btn-default', 'escape'=>false, 'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'left', 'title'=> 'Goto index page'));?>
										
										<?php
										if($this->Session->read('User.is_hod') || $this->Session->read('User.is_mr') || ($customTable['CustomTable']['created_by'] == $this->Session->read('User.id')))echo $this->Html->link('<i class="fa fa-bar-chart fa-lg text-default"></i>',array('controller'=>$customTable['CustomTable']['table_name'], 'action'=>'reports','custom_table_id'=>$customTable['CustomTable']['id']),
										array('class'=>'tooltip1 btn btn-sm btn-default','escape'=>false,'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'bottom', 'title'=> 'Reports'));
										?>
										
										<?php 
										if($customTable['CustomTable']['created_by'] == $this->Session->read('User.id'))echo $this->Html->link('<i class="fa fa-television fa-lg text-default"></i>',array('action'=>'view',$customTable['CustomTable']['id'],'timestamp'=>date('ymdhis')),array('class'=>'tooltip1 btn btn-sm btn-default', 'escape'=>false, 'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'left', 'title'=> 'View/ Recreate'));?>

										
										<?php 
											if($customTable['CustomTable']['created_by'] == $this->Session->read('User.id'))echo $this->Html->link('<i class="fa fa-refresh fa-lg text-default"></i>',array('action'=>'unlock' ,'next_action'=>'recreate', $customTable['CustomTable']['id'],'timestamp'=>date('ymdhis')),array('class'=>'tooltip1 btn btn-sm btn-default', 'escape'=>false, 'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'left', 'title'=> 'View/ Recreate'));?>
										
										<?php 
											if($customTable['CustomTable']['created_by'] == $this->Session->read('User.id'))echo $this->Html->link('<i class="fa fa-file-pdf-o fa-lg text-default"></i>',array('controller'=>'pdf_templates', 'action'=>'add' ,$customTable['CustomTable']['id'],'timestamp'=>date('ymdhis')),array('class'=>'tooltip1 btn btn-sm btn-default', 'escape'=>false, 'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'left', 'title'=> 'Create PDF Template'));?>

										<?php if($customTable['CustomTable']['custom_table_id'] == '' && ($customTable['CustomTable']['created_by'] == $this->Session->read('User.id'))){
											echo $this->Html->link('<i class="fa fa-chain fa-lg "></i>',array('action'=>'add_child','custom_table_id'=> $customTable['CustomTable']['id'],'qc_document_id'=>$customTable['CustomTable']['qc_document_id'],'process_id'=>$customTable['CustomTable']['process_id']),array('class'=>'btn btn-sm tooltip1 ', 'escape'=>false, 'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'bottom',  'title'=> 'Link new table to this table'));
											
										} 	
										?>
										</div>
								</td>
							</tr>					
						<?php endforeach; ?>
						</tbody>
					</table>
				</div>
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
	<div class="row">
		<div class="col-md-12">
			<h3>How to Build Custom HTML Forms</h3>
			<p>In order to use this section, you must add Documents to the application first. Once the documents are added, you can build Custom HTML Forms for each document by clicking on Database icon on the document's view page.</p>
			<p>You can use the existing available forms by downloading them. You can edit those forms as per your requirements once you download them.</p>
			<p>If the form/ module you need, does not exist, you can build your Forms.</p>
			<p>Additionally you can also refer to following links & video for more details.
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

