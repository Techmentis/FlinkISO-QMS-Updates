<?php echo $this->Html->script(array('chartjs/dist/chart.min')); ?>
<?php echo $this->fetch('script'); ?>

<div class="row">	
	<?php foreach($records as $record){ ?>
		<div class="col-md-3" id="<?php echo $record['PieChartPanel']['id']?>_div">
			<div id="<?php echo $record['PieChartPanel']['id']?>"><i class="fa fa-spin fa-refresh"></i></div>
			<script type="text/javascript">
				$("#<?php echo $record['PieChartPanel']['id']?>").load("<?php echo Router::url('/', true); ?>pie_chart_panels/pie/<?php echo $record['PieChartPanel']['id'];?>")
			</script>				
		</div>
	<?php } ?>
	
</div>
