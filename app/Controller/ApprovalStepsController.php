<?php
App::uses('AppController', 'Controller');
/**
 * ApprovalSteps Controller
 *
 * @property ApprovalStep $ApprovalStep
 * @property PaginatorComponent $Paginator
 */
class ApprovalStepsController extends AppController {

/**
 * Components
 *
 * @var array
 */
public $components = array('Paginator');

public function _get_system_table_id() {
	$this->loadModel('SystemTable');
	$this->SystemTable->recursive = -1;
	$systemTableId = $this->SystemTable->find('first', array('conditions' => array('SystemTable.system_name' => $this->request->params['controller'])));
	return $systemTableId['SystemTable']['id'];
}



/**
 * index method
 *
 * @return void
 */
public function index() {

	$conditions = $this->_check_request();
	$this->paginate = array('order'=>array('ApprovalStep.sr_no'=>'DESC'),'conditions'=>array($conditions));
	
	$this->ApprovalStep->recursive = 0;
	$this->set('approvalSteps', $this->paginate());

	$this->_get_count();
}



/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
public function view($id = null) {
	if (!$this->ApprovalStep->exists($id)) {
		throw new NotFoundException(__('Invalid approval step'));
	}
	$options = array('conditions' => array('ApprovalStep.' . $this->ApprovalStep->primaryKey => $id));
	$this->set('approvalStep', $this->ApprovalStep->find('first', $options));
}




/**
 * add method
 *
 * @return void
 */
public function add() {
	
	if($this->_show_approvals()){
		$this->loadModel('User');
		$this->User->recursive = 0;
		$userids = $this->User->find('list',array('order'=>array('User.name'=>'ASC'),'conditions'=>array('User.publish'=>1,'User.soft_delete'=>0,'User.is_approvar'=>1)));
		$this->set(array('userids'=>$userids,'show_approvals'=>$this->_show_approvals()));
	}

	if ($this->request->is('post')) {
		$this->request->data['ApprovalStep']['system_table_id'] = $this->_get_system_table_id();
		$this->ApprovalStep->create();
		if ($this->ApprovalStep->save($this->request->data)) {

			if($this->_show_approvals()){
				$this->loadModel('Approval');
				$this->Approval->create();
				$this->request->data['Approval']['model_name']='ApprovalStep';
				$this->request->data['Approval']['controller_name']=$this->request->params['controller'];
				$this->request->data['Approval']['user_id']=$this->request->data['Approval']['user_id'];
				$this->request->data['Approval']['from']=$this->Session->read('User.id');
				$this->request->data['Approval']['created_by']=$this->Session->read('User.id');
				$this->request->data['Approval']['modified_by']=$this->Session->read('User.id');
				$this->request->data['Approval']['record']=$this->ApprovalStep->id;
				$this->Approval->save($this->request->data['Approval']);
			}
			$this->Session->setFlash(__('The approval step has been saved'));
			if($this->_show_evidence() == true)$this->redirect(array('action' => 'view',$this->ApprovalStep->id));
			else $this->redirect(array('action' => 'index'));
		} else {
			$this->Session->setFlash(__('The approval step could not be saved. Please, try again.'));
		}
	}
	$approvalProcesses = $this->ApprovalStep->ApprovalProcess->find('list',array('conditions'=>array('ApprovalProcess.publish'=>1,'ApprovalProcess.soft_delete'=>0)));
	$companies = $this->ApprovalStep->Company->find('list',array('conditions'=>array('Company.publish'=>1,'Company.soft_delete'=>0)));
	$preparedBies = $this->ApprovalStep->PreparedBy->find('list',array('conditions'=>array('PreparedBy.publish'=>1,'PreparedBy.soft_delete'=>0)));
	$approvedBies = $this->ApprovalStep->ApprovedBy->find('list',array('conditions'=>array('ApprovedBy.publish'=>1,'ApprovedBy.soft_delete'=>0)));
	$createdBies = $this->ApprovalStep->CreatedBy->find('list',array('conditions'=>array('CreatedBy.publish'=>1,'CreatedBy.soft_delete'=>0)));
	$modifiedBies = $this->ApprovalStep->ModifiedBy->find('list',array('conditions'=>array('ModifiedBy.publish'=>1,'ModifiedBy.soft_delete'=>0)));
	$this->set(compact('approvalProcesses', 'companies', 'preparedBies', 'approvedBies', 'createdBies', 'modifiedBies'));
	$count = $this->ApprovalStep->find('count');
	$published = $this->ApprovalStep->find('count',array('conditions'=>array('ApprovalStep.publish'=>1)));
	$unpublished = $this->ApprovalStep->find('count',array('conditions'=>array('ApprovalStep.publish'=>0)));

	$this->set(compact('count','published','unpublished'));

}


/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
public function edit($id = null) {
	if (!$this->ApprovalStep->exists($id)) {
		throw new NotFoundException(__('Invalid approval step'));
	}

	if ($this->_show_approvals()) {
		$this->set(array('showApprovals' => $this->_show_approvals()));
	}

	if ($this->request->is('post') || $this->request->is('put')) {

		if(!isset($this->request->data[$this->modelClass]['publish']) && $this->request->data['Approval']['user_id'] != -1){
			$this->request->data[$this->modelClass]['publish'] = 0;
		}

		$this->request->data['ApprovalStep']['system_table_id'] = $this->_get_system_table_id();
		if ($this->ApprovalStep->save($this->request->data)) {

			if ($this->_show_approvals()) $this->_save_approvals();

			if ($this->_show_evidence() == true)
				$this->redirect(array('action' => 'view', $id));
			else
				$this->redirect(array('action' => 'index'));
		} else {
			$this->Session->setFlash(__('The approval step could not be saved. Please, try again.'));
		}
	} else {
		$options = array('conditions' => array('ApprovalStep.' . $this->ApprovalStep->primaryKey => $id));
		$this->request->data = $this->ApprovalStep->find('first', $options);
	}
	$approvalProcesses = $this->ApprovalStep->ApprovalProcess->find('list',array('conditions'=>array('ApprovalProcess.publish'=>1,'ApprovalProcess.soft_delete'=>0)));
	$companies = $this->ApprovalStep->Company->find('list',array('conditions'=>array('Company.publish'=>1,'Company.soft_delete'=>0)));
	$preparedBies = $this->ApprovalStep->PreparedBy->find('list',array('conditions'=>array('PreparedBy.publish'=>1,'PreparedBy.soft_delete'=>0)));
	$approvedBies = $this->ApprovalStep->ApprovedBy->find('list',array('conditions'=>array('ApprovedBy.publish'=>1,'ApprovedBy.soft_delete'=>0)));
	$createdBies = $this->ApprovalStep->CreatedBy->find('list',array('conditions'=>array('CreatedBy.publish'=>1,'CreatedBy.soft_delete'=>0)));
	$modifiedBies = $this->ApprovalStep->ModifiedBy->find('list',array('conditions'=>array('ModifiedBy.publish'=>1,'ModifiedBy.soft_delete'=>0)));
	$this->set(compact('approvalProcesses', 'companies', 'preparedBies', 'approvedBies', 'createdBies', 'modifiedBies'));
	$count = $this->ApprovalStep->find('count');
	$published = $this->ApprovalStep->find('count',array('conditions'=>array('ApprovalStep.publish'=>1)));
	$unpublished = $this->ApprovalStep->find('count',array('conditions'=>array('ApprovalStep.publish'=>0)));

	$this->set(compact('count','published','unpublished'));
}
	public function delete($id = null){
		$step =  $this->ApprovalStep->find('first',array('conditions'=>array('ApprovalStep.id'=>$id)));
		if($step){
			$this->ApprovalStep->delete($id);
			$this->Session->setFlash(__('The approval step deleted.'));
			// $this->redirect(array('controller'=>'approval_processes', 'action' => 'view', $step['ApprovalStep']['approval_process_id'],'timestamp'=>date('Ymdhis'))); 
			$this->redirect($this->referer());
		}else{
			$this->Session->setFlash(__('The approval step could not be deleted. Please, try again.'));
			// $this->redirect(array('controller'=>'approval_processes', 'action' => 'view', $step['ApprovalStep']['approval_process_id'],'timestamp'=>date('Ymdhis'))); 
			$this->redirect($this->referer());
		}

		$this->redirect($this->referer());
	}
}
