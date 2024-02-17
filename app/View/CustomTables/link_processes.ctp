<div   id="linkedProcessesDiv">
<div class="box box-default">
	<div class="box-header data-header" data-widget="collapse">
		<h3 class="box-title"><span class=""><i class="fa fa-chain"></i></span> Linked Processes</h3>
		<div class="box-tools pull-right"><button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button></div>
	</div>
	<div class="box-body">
		<?php if($linkedProcesses){ ?>
			<table class="table table-bordered table-responsive" id="sortable">
				<tr><th width="20px">#</th><th>Process</th><th width="20px"></th></tr>
				    <tbody>
						<?php 
						$i = 1;
						foreach($linkedProcesses as $linkedProcess){ 
							$default[] = $linkedProcess['Process']['id'];
						?>
							<tr class="ui-state-default">
								<td><?php echo $linkedProcess['CustomTableProcess']['sequence'];?></td>
								<td><?php echo $linkedProcess['Process']['name'];?>
									<?php echo $this->Form->hidden('seq'.$i,array('default'=>$i,'class'=>'cnt'));?>
									<?php echo $this->Form->hidden('pro'.$i,array('default'=>$linkedProcess['Process']['id']));?>
								</td>
								<td><i class="fa fa-trash" onClick="deleteprocess('<?php echo $id;?>','<?php echo $linkedProcess['Process']['id'];?>')"></i></td>
							</tr>
						<?php $i++; } ?>
					</tbody>
			</table>
			<input type="hidden" name="seqs" id="seqs" value="">
		<?php } ?>
		<?php echo $this->Form->create('CustomTable',array('#'),array('class'=>'form-control', 'id'=>'CustomTableLinkProcessesForm', 'role'=>'form', 'default'=>false)); ?>
		<div class="row">
			<div class="col-md-12">
				<?php echo $this->Form->input('id',array('default'=>$id));?>
				<?php echo $this->Form->input('processes',array('multiple','options'=>$perocesses, 'default'=>$default, 'class'=>'chosen'));?></div>
			<div class="col-md-12"><br /><?php echo $this->Form->submit('Submit',array('onclick'=>'add_process()', 'class'=>'btn btn-sm btn-success'));?></div>
		</div>
		<?php echo $this->Form->end();?>
	</div>
	<div class="box-footer">
		<!-- <small>You can check any of the following fields from here and default chart will start displaying on the dashboard. For more options goto Graph Panels from main menu.</small>  -->
	</div>
</div>
<script>
  $( function() {    	
    $( "#sortable tbody" ).sortable({
	  stop: function( event, ui ) {
	  	let str = {};
	  	var i = 1;
	  	$('#sortable tbody .cnt').each(function(){
	  		var x = this.value;
	  		str[i] = $("#pro"+x).val();
	  		$("#"+this.id).val(i);
	  		i++;	  		
	  	});	  	
	  	var myJsonString = JSON.stringify(str);
	  	$("#seqs").val(myJsonString);	 
	  	$("#linkedProcessesDiv").load("<?php echo Router::url('/', true); ?>/custom_tables/process_sequence/id:<?php echo $id;?>/processes:"+$("#seqs").val()); 		  	
	  },
	  // update: function(event, ui){
	  // 	$("linkedProcessesDiv").load("<?php echo Router::url('/', true); ?>/custom_tables/process_sequence/<?php echo $id;?>/"+$("#seqs").val());
	  // }
	});	
  });
  </script>
<script>

	function deleteprocess(id,process_id){
		$("#linkedProcessesDiv").load("<?php echo Router::url('/', true); ?>custom_tables/delete_link_processes/"+id+"/"+process_id);
	}
	// add class updates to by-pass sending ajax again .. do this in users index page as well.
	function add_process(){
		$.ajax({
			url: "<?php echo Router::url('/', true); ?>custom_tables/link_processes/<?php echo $id;?>",
			type: "POST",
			dataType: "json",
			contentType: "application/json; charset=utf-8",
			data: JSON.stringify({
				id: $("#CustomTableId").val(),
				str:$("#CustomTableProcesses").val()
			}),
			beforeSend: function( xhr ) {
				$("#linkedProcessesDiv").html('sending...');
			},                    
			success: function (result) {
				$("#linkedProcessesDiv").load("<?php echo Router::url('/', true); ?>custom_tables/link_processes/<?php echo $id;?>");
			},
			error: function (err) {
				$("#linkedProcessesDiv").load("<?php echo Router::url('/', true); ?>custom_tables/link_processes/<?php echo $id;?>");
			}
		}); 

	}

	$().ready(function(){
		$("#CustomTableProcesses").chosen();
	})

	$.ajaxSetup({
		beforeSend: function () {
			$("#busy-indicator").show();
		},
		complete: function () {
			$("#busy-indicator").hide();
		}
	});
</script>
</div>