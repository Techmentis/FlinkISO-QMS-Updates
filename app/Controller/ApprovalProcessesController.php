<?php
App::uses('AppController', 'Controller');
/**
 * ApprovalProcesses Controller
 *
 * @property ApprovalProcess $ApprovalProcess
 * @property PaginatorComponent $Paginator
 */
class ApprovalProcessesController extends AppController {

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

public function _commons(){	
	$this->set('PublishedUserList',$this->_get_designation_list());	
	$this->set('PublishedDepartmentList',$this->_get_department_list());
	$this->set('PublishedDesignationList',$this->_get_designation_list());
	$this->loadModel('CustomTable');

	$appApprovals = $this->ApprovalProcess->find('all',array('recursive'=>-1, 'fields'=>array('ApprovalProcess.applicable_to')));	
	$tables = array();
	foreach($appApprovals as $appApproval){
		if($appApproval['ApprovalProcess']['applicable_to'] != null && $appApproval['ApprovalProcess']['applicable_to'] != 'null'){
			$tables = array_merge($tables,json_decode($appApproval['ApprovalProcess']['applicable_to'])) ;
		}
	}

	if($this->action == 'add'){
		$existscondition = array('CustomTable.id NOT IN' => $tables);
	}else{
		$existscondition = array();
	}
	
	$customTables = $this->CustomTable->find('list',array(
		'fields'=>array('CustomTable.id','CustomTable.table_name'),
		'conditions'=>array(
			$existscondition,
			// 'OR'=>array(
			// 	'CustomTable.custom_table_id'=> '',
			// 	'CustomTable.custom_table_id'=> NULL
			// )			
	)));

	if(in_array('qc_documents', $tables)){
		if($this->action == 'edit' || $this->action == 'view'){
			$customTables['qc_documents'] = 'QMS Documents';
		}
	}else{
		$customTables['qc_documents'] = 'QMS Documents';	
	}
	$this->set('customTables',$customTables);	
}

/**
 * index method
 *
 * @return void
 */
public function index() {

	// $conditions = $this->_check_request();
	$this->paginate = array('order'=>array('ApprovalProcess.sr_no'=>'DESC'),'conditions'=>array($conditions));
	
	$this->ApprovalProcess->recursive = 0;
	$this->set('approvalProcesses', $this->paginate());

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
	if (!$this->ApprovalProcess->exists($id)) {
		throw new NotFoundException(__('Invalid approval process'));
	}

	$options = array('conditions' => array('ApprovalProcess.' . $this->ApprovalProcess->primaryKey => $id));
	$approvalProcess = $this->ApprovalProcess->find('first', $options);	
	$this->set('approvalProcess', $approvalProcess);	
	$this->_commons();
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
		$this->request->data['ApprovalProcess']['system_table_id'] = $this->_get_system_table_id();
		$this->ApprovalProcess->create();
		
		$this->loadModel('ApprovalStep');
		$this->request->data['ApprovalProcess']['applicable_to'] = json_encode($this->request->data['ApprovalProcess']['applicable_to']);
		if ($this->ApprovalProcess->save($this->request->data)) {
			foreach($this->request->data['ApprovalStep']['steps'] as $approvalStep){
				$approvalStep['approval_process_id'] = $this->ApprovalProcess->id;			
				$this->ApprovalStep->create();
				$this->ApprovalStep->save($approvalStep,false);
			}


			if($this->_show_approvals()){
				$this->loadModel('Approval');
				$this->Approval->create();
				$this->request->data['Approval']['model_name']='ApprovalProcess';
				$this->request->data['Approval']['controller_name']=$this->request->params['controller'];
				$this->request->data['Approval']['user_id']=$this->request->data['Approval']['user_id'];
				$this->request->data['Approval']['from']=$this->Session->read('User.id');
				$this->request->data['Approval']['created_by']=$this->Session->read('User.id');
				$this->request->data['Approval']['modified_by']=$this->Session->read('User.id');
				$this->request->data['Approval']['record']=$this->ApprovalProcess->id;
				$this->Approval->save($this->request->data['Approval']);
			}
			$this->Session->setFlash(__('The approval process has been saved'));
			if($this->_show_evidence() == true)$this->redirect(array('action' => 'view',$this->ApprovalProcess->id));
			else $this->redirect(array('action' => 'index'));
		} else {
			$this->Session->setFlash(__('The approval process could not be saved. Please, try again.'));
		}
	}
	$companies = $this->ApprovalProcess->Company->find('list',array('conditions'=>array('Company.publish'=>1,'Company.soft_delete'=>0)));
	$preparedBies = $this->ApprovalProcess->PreparedBy->find('list',array('conditions'=>array('PreparedBy.publish'=>1,'PreparedBy.soft_delete'=>0)));
	$approvedBies = $this->ApprovalProcess->ApprovedBy->find('list',array('conditions'=>array('ApprovedBy.publish'=>1,'ApprovedBy.soft_delete'=>0)));
	$createdBies = $this->ApprovalProcess->CreatedBy->find('list',array('conditions'=>array('CreatedBy.publish'=>1,'CreatedBy.soft_delete'=>0)));
	$modifiedBies = $this->ApprovalProcess->ModifiedBy->find('list',array('conditions'=>array('ModifiedBy.publish'=>1,'ModifiedBy.soft_delete'=>0)));
	$this->set(compact('companies', 'preparedBies', 'approvedBies', 'createdBies', 'modifiedBies'));
	$count = $this->ApprovalProcess->find('count');
	$published = $this->ApprovalProcess->find('count',array('conditions'=>array('ApprovalProcess.publish'=>1)));
	$unpublished = $this->ApprovalProcess->find('count',array('conditions'=>array('ApprovalProcess.publish'=>0)));

	$this->set(compact('count','published','unpublished'));
	$this->_commons();

}


/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
public function edit($id = null) {
	if (!$this->ApprovalProcess->exists($id)) {
		throw new NotFoundException(__('Invalid approval process'));
	}

	if ($this->_show_approvals()) {
		$this->set(array('showApprovals' => $this->_show_approvals()));
	}

	if ($this->request->is('post') || $this->request->is('put')) {
		// if(!isset($this->request->data['Approval']['ApprovalProcess']['publish'])){
			$this->request->data[$this->modelClass]['publish'] = $this->request->data['Approval']['ApprovalProcess']['publish'];
		// }

		$this->request->data['ApprovalProcess']['system_table_id'] = $this->_get_system_table_id();
		$addedTables = $this->request->data['ApprovalProcess']['applicable_to'];
		$this->request->data['ApprovalProcess']['applicable_to'] = json_encode($this->request->data['ApprovalProcess']['applicable_to']);
		
		// check if table has alredy process
		$allProcesses = $this->ApprovalProcess->find('all',array('recursive'=>-1, 'conditions'=>array('ApprovalProcess.id != '=>$id), 'fields'=>array('ApprovalProcess.id','ApprovalProcess.applicable_to')));
		$tables = array();
		foreach($allProcesses as $allProcess){
			$tables = array_merge($tables,json_decode($allProcess['ApprovalProcess']['applicable_to'],true)) ;
		}
		$this->_commons();
		
		foreach($addedTables as $addedTable){
			if( in_array($addedTable, $tables) ){		
				$this->Session->setFlash(__('The approval process for : ' . $this->viewVars['customTables'][$addedTable] . ' already exists'));
				$this->redirect($this->referer());
			}
		}
		
		if ($this->ApprovalProcess->save($this->request->data)) {
			$this->loadModel('ApprovalStep');
			foreach($this->request->data['ApprovalStep']['steps'] as $approvalStep){
				if(!isset($approvalStep['send_to_designation']))$approvalStep['send_to_designation'] = '-1';
				$approvalStep['approval_process_id'] = $this->ApprovalProcess->id;			
				$this->ApprovalStep->create();
				$this->ApprovalStep->save($approvalStep,false);
			}

			if ($this->_show_approvals()) $this->_save_approvals();

			$this->redirect(array('action' => 'view', $id));
		} else {
			$this->Session->setFlash(__('The approval process could not be saved. Please, try again.'));
		}
	} else {
		$options = array('conditions' => array('ApprovalProcess.' . $this->ApprovalProcess->primaryKey => $id));
		$this->request->data = $this->ApprovalProcess->find('first', $options);
	}
	$companies = $this->ApprovalProcess->Company->find('list',array('conditions'=>array('Company.publish'=>1,'Company.soft_delete'=>0)));
	$preparedBies = $this->ApprovalProcess->PreparedBy->find('list',array('conditions'=>array('PreparedBy.publish'=>1,'PreparedBy.soft_delete'=>0)));
	$approvedBies = $this->ApprovalProcess->ApprovedBy->find('list',array('conditions'=>array('ApprovedBy.publish'=>1,'ApprovedBy.soft_delete'=>0)));
	$createdBies = $this->ApprovalProcess->CreatedBy->find('list',array('conditions'=>array('CreatedBy.publish'=>1,'CreatedBy.soft_delete'=>0)));
	$modifiedBies = $this->ApprovalProcess->ModifiedBy->find('list',array('conditions'=>array('ModifiedBy.publish'=>1,'ModifiedBy.soft_delete'=>0)));
	$this->set(compact('companies', 'preparedBies', 'approvedBies', 'createdBies', 'modifiedBies'));
	$count = $this->ApprovalProcess->find('count');
	$published = $this->ApprovalProcess->find('count',array('conditions'=>array('ApprovalProcess.publish'=>1)));
	$unpublished = $this->ApprovalProcess->find('count',array('conditions'=>array('ApprovalProcess.publish'=>0)));

	$this->set(compact('count','published','unpublished'));	

	$this->_commons();
}

public function addrows($i = 0){
	$i = $i+1;
	$this->set('i',$i);
	$this->_commons();
}
}
