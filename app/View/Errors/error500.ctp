<?php
/**
 * @link          https://cakephp.org CakePHP(tm) Project
 * @package       app.View.Errors
 * @since         CakePHP(tm) v 0.10.0.1076
 */
?>
<?php if($this->Session->read('User'))echo $this->element('nav-header-lists',array('postData'=>array('pluralHumanName'=>__('Error'),'modelClass'=>'Error','options'=>array(),'pluralVar'=>'errors'))); ?>
<h2><?php echo $message; ?></h2>

<p class="text-danger">
	<strong><?php echo __d('cake', 'Error'); ?>: </strong>
	<?php echo __d('cake', 'An Internal Error Has Occurred.'); ?>
</p>

<p><hr /></p>
<h4><?php echo h(__('Possible Causes:')); ?></h4>
<p>
	<ul>
		<li><?php echo h(__('Database is not connected.')); ?>
			<ul>
				<li><?php echo h(__('Make sure that you have imported app/webroot/schema/flinkiso-on-premise.sql')); ?></li>
				<li><?php echo h(__('Make sure to add database configuration at app/Config/Database.php under default config.')); ?></li>
			</ul>
		</li>
		<li><?php echo h(__('Directory permissions are not correct')); ?>
			<ul>
				<li><?php echo h(__('Following folders must have read-write-execute permissions (recursive)')); ?>
					<ul>
						<li><?php echo h(__('app/webtoot')); ?></li>
						<li><?php echo h(__('app/tmp')); ?></li>
						<li><?php echo h(__('lib/Cake/Cache')); ?></li>						
					</ul>
				</li>
				
			</ul>
		</li>
		<li><?php echo h(__('Missing tables or incorrect models.')); ?></li>
	</ul>
</p>
<p><?php echo h(__('If this error is occuring during the installation, visit:')); ?> <a href="https://www.flinkiso.com/manual/installation.html">https://www.flinkiso.com/manual/installation.html</a> <?php echo h(__('and follow the installation instructions.')); ?></p>
<p><?php echo h(__('You can also use FlinkISO Forum :')); ?> <a href="https://forum.flinkiso.com/">https://forum.flinkiso.com/</a> <?php echo h(__('for possible resolution.')); ?></p>

<p><?php echo h(__('If you have successfully installed the application and error is occuring after creating a custom HTML form, this error can be resolved by recreating the form.')); ?></p>
<p><?php echo h(__('If this error is coming after creating a child form or a form linked to a child document,')); ?> <strong class="text-warning"><?php echo h(__('recreate')); ?></strong> <?php echo h(__('the parent form.')); ?></p>
<p><strong><?php echo h(__('Additional Info:')); ?></strong><?php echo h(__('Remove "STRICT_TRANS_TABLES" for sql_mode from MySQL configuration file.')); ?></p>
<hr />
<p>
	<?php
	if($this->request->params['named']['approval_id']){
		if(empty($this->request->params['named']['approval_id'][0])){
			echo $this->Html->link(__('Delete This approval'),array('controller'=>'approvals','action'=> 'delete_approval',$this->request->params['named']['approval_id']),array('class'=>'btn btn-danger btn-xl'));

		}else if(!empty($this->request->params['named']['approval_id'])){
			if($this->request->params['named']['approval_comment_id']){
				echo $this->Html->link(__('Delete This approval'),array('controller'=>'approvals','action'=> 'delete_approval',$this->request->params['named']['approval_comment_id']),array('class'=>'btn btn-danger btn-xl'));
			}else{
				echo $this->Html->link(__('Delete This approval'),array('controller'=>'approvals','action'=> 'delete_approval',$this->request->params['named']['approval_id']),array('class'=>'btn btn-danger btn-xl'));	
			}
		}else{
			echo $this->Html->link(__('Delete This approval'),array('controller'=>'approvals','action'=> 'delete_approval',$this->request->params['named']['approval_id']),array('class'=>'btn btn-danger btn-xl'));
		}		
	}
	?>
</p>
<hr />
<?php
if (Configure::read('debug') > 0):
	echo $this->element('exception_stack_trace');
endif;
