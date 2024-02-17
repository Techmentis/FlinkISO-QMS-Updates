<script type="text/javascript">
	$(document).ready(function(){
		$("#UserSessionUserId").chosen();

		$("#submit-indicator").hide();
		$("#submit_id").click(function(){
			$("#submit_id").prop("disabled",true);
			$("#submit-indicator").show();
			$("#UserSessionIndexForm").submit();
		});
	});
</script>
<div  id="main"  id="users_ajax">
	<?php echo $this->Session->flash();?>
	<?php echo $this->element('nav-header-lists',array('postData'=>array('pluralHumanName'=>'User Audit Trail','modelClass'=>'UserSession','options'=>array(),'pluralVar'=>'user_sessions'))); ?>

	
	<?php
	echo '<div class="row">'. $this->Form->create('UserSession',array('action'=>'index','type'=>'get'),array('role'=>'form','class'=>'form'));
	echo '<div class="col-md-8">'.$this->Form->input('user_id',array( 'value'=>$userId)).'</div>';
	echo '<div class="col-md-4">'.$this->Form->submit('Submit',array('class'=>'btn btn-info pull-left', 'id'=>'submit_id', 'div'=>array('style'=>array('margin:30px 0 0 10px')))).'</div>';
	echo $this->Html->image('indicator.gif', array('id' => 'submit-indicator', 'class'=>'pull-right'));
	echo $this->Form->end();
	echo '</div>';
	?>		
	<div class="userSessions row panel">

		<script type="text/javascript">
// $(document).ready(function(){
// $('table th a, .pag_list li span a').on('click', function() {
// 	var url = $(this).attr("href");
// 	$('#main').load(url);
// 	return false;
// });
// });
		</script>
		<?php


		?>

		<table class="table table-bordered table table-hover">

			<?php
			foreach ($userSession as $history):
				?>
				<?php
				if($history['History']['model_name'] != 'CakeError'){


					if($history['History']['action'] == 'index' or $history['History']['action'] == 'lists' or $history['History']['action'] == 'view')
					{
						echo "<tr><td class='text-warning'> Accessed ". Inflector::humanize(Inflector::underscore($history['History']['model_name'])) ."<span class='pull-right'><span class='pull-right'> " . $this->Time->nice($history['History']['created'])."</span></td></tr>";
					}elseif(($history['History']['post_values'] != "[[],[]]") && ($history['History']['action'] == 'add' or $history['History']['action'] == 'add_ajax' or $history['History']['action'] == 'add_new_ajax'))
					{
						
						echo "<tr><td class='text-success'> Added new record to <b> ". $this->Html->link(Inflector::humanize(Inflector::underscore($history['History']['model_name'])),array('controller'=>$history['History']['controller_name'],'action'=>'view',$history['History']['record_id']))."</b> <span class='pull-right'>" . $this->Time->nice($history['History']['created'])."</span></td></tr>";
					}elseif($history['History']['action'] == 'edit' && $history['History'] != "[[],[]]")
					{
						echo "<tr><td class='text-info'> Changed record in <b> ". $this->Html->link(Inflector::humanize(Inflector::underscore($history['History']['model_name'])), "#".$history['History']['record_id'], array('escape'=>false,'class'=>'getEdits','id'=>$history['History']['record_id'])) ."</b> <span class='pull-right'>" . $this->Time->nice($history['History']['created'])."</span></td></tr>";
					}else{
						echo "<tr><td class='text-warning'> Accessed <b>". Inflector::humanize(Inflector::underscore($history['History']['model_name'])) .'</b> /'. $history['History']['action']." <span class='pull-right'>" . $this->Time->nice($history['History']['created'])."</span></td></tr>";
					}

				//echo "</td><td>".$this->Html->link(__('View Details'), array('controller' => 'histories', 'action' => 'view', $history['id']))."</td></tr>";
				}
				?>
			<?php endforeach; ?>
		</table>

		<?php echo $this->Form->end();?>
	</div>
</div>
</div>
</div>

</div>
<?php echo $this->Js->writeBuffer();?>
<script>$.ajaxSetup({beforeSend:function(){$("#busy-indicator").show();},complete:function(){$("#busy-indicator").hide();}});</script>
<div id="display_full_history"><?php echo $this->Html->image('indicator.gif', array('id' => 'showModal-indicator', 'style' => 'display:none;')); ?></div>
<script>
	$('.getEdits').click(function(){
		$("#showModal-indicator").show();
		$('#display_full_history').load('<?php echo Router::url('/', true); ?>histories/view/'+ this.id);
		$('#crs').on('hidden.bs.modal', function (e) {$("#showModal-indicator").hide();});
	});
</script>
