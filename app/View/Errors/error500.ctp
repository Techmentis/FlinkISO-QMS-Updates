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

<hr />
<h4><?php echo h(__('Possible Causes:')); ?></h4>
<p><?php echo h(__('Check the message above first. The following checks are for an administrator; they do not all apply to every error.')); ?></p>
<ul>
    <li><strong><?php echo h(__('Access permissions')); ?></strong> — <?php echo h(__('The signed-in account may not have permission for this action. Creating or rebuilding application forms requires a Super User.')); ?></li>
    <li><strong><?php echo h(__('Database connection or schema')); ?></strong> — <?php echo h(__('Verify the connection settings in app/Config/database.php and check that the required database updates have been applied.')); ?></li>
    <li><strong><?php echo h(__('Incomplete application update')); ?></strong> — <?php echo h(__('Check that all files required by the installed version were copied, including application libraries, helpers, language files and assets.')); ?></li>
    <li><strong><?php echo h(__('File permissions')); ?></strong> — <?php echo h(__('The web server must be able to write to app/tmp and any files or folders used by the failed operation. Form rebuilding also needs write access to the generated controllers, models and views.')); ?></li>
    <li><strong><?php echo h(__('Generated form files')); ?></strong> — <?php echo h(__('If the error started after changing a form, check for missing or outdated generated files. A Super User can rebuild the parent module after correcting the underlying error.')); ?></li>
</ul>
<p><?php echo h(__('For the exact cause, ask an administrator to check app/tmp/logs/error.log and the web server error log at the time of this request.')); ?></p>
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
