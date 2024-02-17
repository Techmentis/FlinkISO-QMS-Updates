<?php echo $this->Html->script(array('chartjs/dist/chart.min')); ?>
<?php echo $this->fetch('script'); ?>
<div class="row" style="margin-bottom:0; margin-top:0">
	<?php foreach($panels as $panel){ ?>
		<div class="col-md-<?php echo $panel['GraphPanel']['size']?>" id="<?php echo $panel['GraphPanel']['id']?>_div">
			<div id="<?php echo $panel['GraphPanel']['id']?>"><i class="fa fa-spin fa-refresh"></i></div>
			<script type="text/javascript">
				$("#<?php echo $panel['GraphPanel']['id']?>").load("<?php echo Router::url('/', true); ?>graph_panels/panel/<?php echo $panel['GraphPanel']['id'];?>")
			</script>				
		</div>
	<?php } ?>
	
</div>

<div class="row" style="margin-bottom:0; margin-top:0">
	<?php foreach($graphs as $graph){ ?>
		<div class="col-md-<?php echo $graph['GraphPanel']['size'];?>" id="<?php echo $graph['GraphPanel']['id']?>_div">			
			<div id="<?php echo $graph['GraphPanel']['id']?>"><i class="fa fa-spin fa-refresh"></i></div>
			<script type="text/javascript">
				$("#<?php echo $graph['GraphPanel']['id']?>").load("<?php echo Router::url('/', true); ?>graph_panels/graph/<?php echo $graph['GraphPanel']['id'];?>")
			</script>				
		</div>
	<?php } ?>
	
</div>
