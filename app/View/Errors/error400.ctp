<?php
/**
 * @link          https://cakephp.org CakePHP(tm) Project
 * @package       app.View.Errors
 * @since         CakePHP(tm) v 0.10.0.1076
 */
?>
<?php echo $this->element('nav-header-lists',array('postData'=>array('pluralHumanName'=>'Error','modelClass'=>'Error','options'=>array(),'pluralVar'=>'errors'))); ?>
<!-- <h2><?php echo $message; ?></h2> -->
<p class="error">
	<strong><?php echo __d('cake', 'Error'); ?>: </strong>
	<?php printf(
		__d('cake', 'The requested address %s was not found on this server.'),
		""
	); ?>
</p>
<hr />
<p>
	<?php
	if($this->request->params['named']['approval_id']){
		if(empty($this->request->params['named']['approval_id'][0])){
			echo $this->Html->link('Delete This approval',array('controller'=>'approvals','action'=> 'delete_approval',$this->request->params['named']['approval_id']),array('class'=>'btn btn-danger btn-xl'));

		}else if(!empty($this->request->params['named']['approval_id'])){
			if($this->request->params['named']['approval_comment_id']){
				echo $this->Html->link('Delete This approval',array('controller'=>'approvals','action'=> 'delete_approval',$this->request->params['named']['approval_comment_id']),array('class'=>'btn btn-danger btn-xl'));
			}else{
				echo $this->Html->link('Delete This approval',array('controller'=>'approvals','action'=> 'delete_approval',$this->request->params['named']['approval_id']),array('class'=>'btn btn-danger btn-xl'));	
			}
		}else{
			echo $this->Html->link('Delete This approval',array('controller'=>'approvals','action'=> 'delete_approval',$this->request->params['named']['approval_id']),array('class'=>'btn btn-danger btn-xl'));
		}		
	}
	?>
</p>
<?php
if (Configure::read('debug') > 0):
	// echo $this->element('exception_stack_trace');
endif;
