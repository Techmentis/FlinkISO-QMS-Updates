<div class="peonav">
	<div id="protabs">
		<ul>                    
			<?php foreach($processes as $process){ ?>
					<li><?php echo $this->Html->link(__($process['Process']['name']), array('controller'=> 'processes', 'action' => 'load_process',$process['Process']['id'])); ?></li>
			<?php } ?>
				<li><?php echo $this->Html->image('indicator.gif', array('id' => 'busy-indicator', 'class' => 'pull-right')); ?></li>
			</ul>
		</div>
	</div>
	<!-- <div id="appraisals_tab_ajax"></div> -->
</div>
<script>
    $(function() {
        $("#protabs").tabs({
            beforeLoad: function(event, ui) {
                ui.jqXHR.error(function() {
                    ui.panel.html(
                            <?php echo json_encode(__('Error Loading ... '), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?> +
                            <?php echo json_encode(__('Please contact administrator.'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>);
                });
            }
        });
    });
</script>