<?php
$docarray = array('doc','docx');
$sheetarray = array('xls','xlsx');
$pdfarray = array('pdf');
$pptarray = array('ppt','pptx');

if (!$this->request->is('post')) {
	echo $this->Form->input('form_search',array('class'=>'form-control pull-right'));
}
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
				if($this->request->params['named']['table_type'] == 1 || !isset($this->request->params['named']['table_type'])){
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
				if($this->request->params['named']['table_type'] == 4 && !isset($this->request->params['named']['standard_id'])){
					echo $this->Html->link('All',array('action'=>'index','table_type'=>4),array('class'=>'btn btn-sm btn-bold  btn-info'));
				}else{
					echo $this->Html->link('All',array('action'=>'index','table_type'=>4),array('class'=>'btn btn-sm btn-bold  btn-default'));
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
<div id="searchResults">		
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
								<!-- <th><?php echo $this->Paginator->sort('table_version'); ?></th> -->
								<!-- <th><?php echo $this->Paginator->sort('publish'); ?></th> -->
								<th width="280">Action</th>
							</tr>
						</thead>
						<tbody>
						<?php foreach ($customTables as $customTable): ?>							
							<tr>
								<td><?php echo $customTable['CustomTable']['name'];?></td>
								<td><?php echo $customTable['CustomTable']['table_name'];?></td>
								<td><?php echo $customTable['QcDocument']['document_number'];?> - <?php echo $customTable['QcDocument']['name'];?></td>
								<!-- <td><?php echo $customTable['CustomTable']['table_version'];?></td> -->
								<!-- <td><?php echo $customTable['CustomTable']['publish'];?></td> -->
								<td style="text-align:right">
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
	<p>Forms are not added for this section.</p>
<?php } ?>
</div>
<script>
	var searchTimer;
	$('#form_search').on('input', function () {
    var search = $(this).val();
    clearTimeout(searchTimer);
    searchTimer = setTimeout(function () {
        if (search.length < 2) {
            $('#main').html('');
            return;
        }

        $.ajax({
            url: '<?php echo Router::url('/', true); ?>/custom_tables/index/',
            type: 'POST',
            data: {
                search: search
            },
            beforeSend: function () {
                $('#main').html('Searching...');
            },
            success: function (response) {
                $('#main').html(response);
            },
            error: function () {
                $('#main').html('Error while searching.');
            }
        });

    }, 300); // wait 300ms after user stops typing
});
</script>
