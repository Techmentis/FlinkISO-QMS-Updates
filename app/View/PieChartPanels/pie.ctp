<?php if($result['data']){ ?>
	<?php $id = $this->request->params['pass'][0] ;?>
	<div class="box box-default" style="background-color: #fff;"> 
		<div class="box-header">
			<h4 class=" text-center"><?php echo $field;?>
			<br /><small><?php echo $table;?></small>
		</h4>
	</div>	
	<div class="box-body">
		<canvas id="pieChart<?php echo $id;?>" width="400" height="400"></canvas>
		<script>
			const ctx = document.getElementById('pieChart<?php echo $id;?>').getContext('2d');
			const pieChart = new Chart(ctx, {
				type: 'doughnut',
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
							position: 'bottom',
						},
					},
					scales: {
						
					}
				}
			});
		</script>
	</div>	
</div>
<?php }else{ ?>
	<script type="text/javascript">
		$("#<?php echo $this->request->params['pass'][0]?>_div").remove();
	</script>
<?php } ?>
