<h3><small><?php echo $this->Html->link('Dashboard',array('controller'=>'users','action'=>'dashboard'));?> / </small><?php echo __('Pending Tasks');?></h3>
<div id="pending-task-tabs" class="nav-tabs-info">
	<ul class="nav nav-tabs">
		<?php foreach ($results as $model_name => $value) { ?>
			<li><?php echo $this->Html->link(Inflector::humanize($model_name) .' <span class="label label-danger">'.$value.'</span>',array('controller'=>Inflector::tableize($model_name),'action'=>'pending_tasks'),array('escape'=>false));?></li>	
		<?php } ?>
     <li><?php echo $this->Html->image('indicator.gif', array('id' => 'pt-busy-indicator', 'class' => 'pull-right')); ?></li>
 </ul>
</div>

<script>
    $(document).ready(function () {

        $.ajaxSetup({
            cache: false,
            success: function() {$("#pt-busy-indicator").hide();}
        });
        $("#pending-task-tabs").tabs({
            load: function (event, ui) {
                $("#file-busy-indicator").hide();
            },
            ajaxOptions: {
                error: function (xhr, status, index, anchor) {
                    $(anchor.hash).html(
                        "<?php echo __('Error loading resource.')?> " +
                        "<?php echo __('Contact Administrator.')?>" );
                }
            }
        });

        $("#pending-task-tabs li").click(function () {
            $("#pt-busy-indicator").show();
        });
    });
</script>
