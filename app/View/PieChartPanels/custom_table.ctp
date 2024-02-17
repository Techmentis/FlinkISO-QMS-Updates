<div class="box box-default">
	<div class="box-header with-border"><h3 class="box-title" style="width: 100%">Show Piechart on dashboard <span class="pull-right"><i class="fa fa-pie-chart"></i></span> </h3></div>
	<div class="box-body">
		<table class="table table-striped">
			<tr>
				<th>Field Name</th>
				<th width="12"></th>
			</tr>
			<?php foreach($radioaFields as $field => $name){ ?>
				<tr>
					<td><?php echo $field; ?></td>
					<td>
						<?php 
						$str = array($field,$custom_table_id,$name[1]);
						$str = base64_encode(json_encode($str));
						
						if($name[1] == 0){
							echo $this->Html->link('<i class="fa fa-close"></i>','javascript:void(0)', array(
								'class'=>'btn btn-sm text-danger',
								'onClick'=>'reset_display(\''.$custom_table_id.'\', \''.$str.'\',this.id)',
								'id'=>$field.'_i',
								'escape'=>false
							));
						}else{
							echo $this->Html->link('<i class="fa fa-check"></i>','javascript:void(0)', array(
								'class'=>'btn btn-sm text-success',
								'onClick'=>'reset_display(\''.$custom_table_id.'\', \''.$str.'\',this.id)',
								'id'=>$field.'_i',
								'escape'=>false
							));
						}
						?>
					</td>
				</tr>
			<?php } ?>
		</table>
	</div>
</div>
<script>
	// add class updates to by-pass sending ajax again .. do this in users index page as well.
	function reset_display(custom_table_id,str, id){
		$.ajax({
			url: "<?php echo Router::url('/', true); ?>pie_chart_panels/reset_display",
			type: "POST",
			dataType: "json",
			contentType: "application/json; charset=utf-8",
			data: JSON.stringify({str:str}),
			beforeSend: function( xhr ) {
				$("#"+id+" i").removeClass('fa-check').removeClass('fa-remove').addClass('fa-refresh fa-spin');
			},                    
			success: function (result) {
				if(result == 1){
					$("#"+id+" i").removeClass('fa-refresh fa-spin fa-close').addClass('fa-check');
					$("#"+id+" i").removeClass('text-danger').addClass('text-success');
				}
				if(result == 2){            		
					$("#"+id+" i").removeClass('fa-refresh fa-spin fa-check').addClass('fa-close');
					$("#"+id+" i").removeClass('text-success').addClass('text-danger');
				}                
			},
			error: function (err) {
				
			}
		}); 

	}


	$.ajaxSetup({
		beforeSend: function () {
			$("#busy-indicator").show();
		},
		complete: function () {
			$("#busy-indicator").hide();
		}
	});
</script>
