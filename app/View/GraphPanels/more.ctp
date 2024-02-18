<div class="modal fade" id="<?php echo $graphPanel['GraphPanel']['id'];?>_modal" tabindex="-1" role="dialog" aria-labelledby="<?php echo $graphPanel['GraphPanel']['id'];?>_modal">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="<?php echo $graphPanel['GraphPanel']['id'];?>_modal"><?php echo $graphPanel['CustomTable']['name'];?>
				<br /><small><?php echo $graphPanel['GraphPanel']['field_name'];?></small>
			</h4>
		</div>
		<div class="modal-body" id="<?php echo $graphPanel['GraphPanel']['id'];?>_body">
			<?php echo $this->Form->create('GraphPanel',array('role'=>'form','class'=>'form','default'=>false));?>
			<?php echo $this->Form->input('id',array('default'=>$graphPanel['GraphPanel']['id']))?>
			<style type="text/css">
				.chosen-container, .chosen-container-multi{width: 100%;}
			</style> 
			<div class="row">
				<div class="col-md-12"><p>Show graphs/panels on following conditions:</p></div>
				<div class="col-md-12"><?php echo $this->Form->input('title',array('class'=>'form-control','default'=>$graphPanel['GraphPanel']['title']));?></div>
				<div class="col-md-12"><?php echo $this->Form->input('admin_only',array('class'=>'checkbox','type'=>'checkbox'));?></div>
				<div class="col-md-12"><?php echo $this->Form->input('branches',array('name'=>'data[GraphPanel][branches][]', 'options'=>$branches, 'default'=>json_decode($graphPanel['GraphPanel']['branches'],true), 'class'=>'form-control','multiple'));?></div>
				<div class="col-md-12"><?php echo $this->Form->input('departments',array('name'=>'data[GraphPanel][departments][]','options'=>$departments, 'default'=>json_decode($graphPanel['GraphPanel']['departments'],true), 'class'=>'form-control','multiple'));?></div>
				<div class="col-md-12"><?php echo $this->Form->input('designations',array('name'=>'data[GraphPanel][designations][]','options'=>$designations,'default'=>json_decode($graphPanel['GraphPanel']['designations'],true), 'class'=>'form-control','multiple'));?></div>
				<?php if($graphPanel['GraphPanel']['graph_type'] == 4){
					$colors = array('aqua'=>'Aqua','green'=>'Green','yellow'=>'Yellow','red'=>'Red'); ?>
					<div class="col-md-12"><?php echo $this->Form->input('color',array('options'=>$colors,'default'=>json_decode($graphPanel['GraphPanel']['color'],true), 'class'=>'form-control'));?></div>
				<?php } ?>
			</div>
		</div>
		<div class="modal-footer">      	
			<button type="button" class="btn btn-default" data-dismiss="modal" id="close_btn">Close</button>        
			<?php echo $this->Form->submit(__('Submit'), array('div' => array('id'=>'submit_id_div'), 'class' => 'btn btn-primary btn-success','id'=>'panel_submit_id')); ?>
			<?php echo $this->Form->end();?>
		</div>
	</div>
</div>
</div>
<?php echo $this->Js->writeBuffer();?>
<script type="text/javascript">
	$("#panel_submit_id").on('click',function(e){		
		e.preventDefault();
		formdata = $("#GraphPanelMoreForm").serializeArray();
		$.ajax({
			url: "<?php echo Router::url('/', true); ?>graph_panels/more",
			type: "POST",
			data: formdata,
			
			complete: function(){
				$("#panel_submit_id").hide();
				$("#close_btn").show();            	
			}
		});
	});

	$().ready(function(){
		$("#close_btn").hide();
		$("#<?php echo $graphPanel['GraphPanel']['id'];?>_modal").modal('show');	
		$("select").chosen();
		$(".chosen-container-multi").width('100%');
	});
	
</script>
