<style type="text/css">
	canvas{
		width: 100%;
		min-height: 150px;
		max-height: 250px;
	}
</style>
<?php if($result['data']){ 
	$graph = $graphTypes[$record['GraphPanel']['graph_type']];
	?>
	<?php $id = $this->request->params['pass'][0] ;?>
	<div class="box box-default" style="background-image:none ;">  
		<div class="box-header">
			<i class="fa fa-plus pull-right" id="fa<?php echo str_replace('-', '', $this->request->params['pass'][0]);?>" onclick="showhidetable<?php echo str_replace('-', '', $this->request->params['pass'][0]);?>()"></i>
			<h4 class=" text-center">
				<?php if(!$record['GraphPanel']['title']){ ?>
					<?php echo $table;?>
					<br /><small>By <?php echo $field;?></small>
				<?php }else{ ?>
					<?php echo $record['GraphPanel']['title'];?>
				<?php } ?>
			<small><?php echo $dateConditions[$record['GraphPanel']['date_condition']];?></small>			
		</h4>
		<hr />
	</div>	
	<div class="box-body text-center" style="width:100%; margin:auto;">
		<center>
			<div class="canvas_div" id="canvas_div_<?php echo $id;?>">
				<canvas id="pieChart<?php echo $id;?>" width="100%" style="float: left;clear: both;"></canvas>
			</div>
		</center>
		<script>
			const ctx = document.getElementById('pieChart<?php echo $id;?>').getContext('2d');
			const pieChart = new Chart(ctx, {
				type: '<?php echo $graph;?>',
				data: {
					labels: <?php echo json_encode($result['labels']);?>,
					datasets: [{
						label: '<?php echo $field_name;?>',
						data: <?php echo json_encode($result['data']);?>,
						backgroundColor: <?php echo json_encode($result['color']);?>,
						borderColor: <?php echo json_encode($result['color']);?>,
						borderWidth: 1
					}]
				},
				options: {
					responsive: true,
					plugins:{
						legend: {
							display : false
						},
					},
					scales: {
						
					}
				}
			});
		</script>
	</div>
	<div class="box-footer box-footer-panel text-center  hide" id="table-<?php echo str_replace('-', '', $this->request->params['pass'][0]);?>">
		<div class="table-responsive">
			<table class="table table-bordered">
				<?php for($cnt = 0; $cnt < count($result['labels']); $cnt++){ ?>
					<tr><th><i class="fa fa-circle" aria-hidden="true" style="color:<?php echo $result['color'][$cnt];?>90;"></i>
						&nbsp;<?php echo $result['labels'][$cnt];?></th><td><?php echo $result['data'][$cnt];?></td></tr>
					<?php } ?>
				</table>
			</div>
		</div>	
	</div>
<?php }else{ ?>
	<script type="text/javascript">
		$("#<?php echo $this->request->params['pass'][0]?>_div").remove();		
	</script>
<?php } ?>

<script type="text/javascript">
	function showhidetable<?php echo str_replace('-', '', $this->request->params['pass'][0]);?>(){
		$("#table-<?php echo str_replace('-', '', $this->request->params['pass'][0]);?>").toggleClass('hide');
		$("#fa<?php echo str_replace('-', '', $this->request->params['pass'][0]);?>").toggleClass('fa-plus fa-minus');
	}
</script>