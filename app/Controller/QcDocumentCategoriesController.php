<?php
App::uses('AppController', 'Controller');
/**
 * QcDocumentCategories Controller
 *
 * @property QcDocumentCategory $QcDocumentCategory
 * @property PaginatorComponent $Paginator
 */
class QcDocumentCategoriesController extends AppController {

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
	$standards = $this->QcDocumentCategory->Standard->find('list',array('conditions'=>array('Standard.publish'=>1,'Standard.soft_delete'=>0)));
	$parentQcDocumentCategories = $this->QcDocumentCategory->ParentQcDocumentCategory->find('list',array('conditions'=>array('ParentQcDocumentCategory.publish'=>1,'ParentQcDocumentCategory.soft_delete'=>0)));
	$systemTables = $this->QcDocumentCategory->SystemTable->find('list',array('conditions'=>array('SystemTable.publish'=>1,'SystemTable.soft_delete'=>0)));
	$companies = $this->QcDocumentCategory->Company->find('list',array('conditions'=>array('Company.publish'=>1,'Company.soft_delete'=>0)));
	$approvedBies = $preparedBies = $this->QcDocumentCategory->PreparedBy->find('list',array('conditions'=>array('PreparedBy.publish'=>1,'PreparedBy.soft_delete'=>0)));
	
	$modifiedBies = $createdBies = $this->QcDocumentCategory->CreatedBy->find('list',array('conditions'=>array('CreatedBy.publish'=>1,'CreatedBy.soft_delete'=>0)));
	
	$count = $this->QcDocumentCategory->find('count');
	$published = $this->QcDocumentCategory->find('count',array('conditions'=>array('QcDocumentCategory.publish'=>1)));
	$unpublished = $this->QcDocumentCategory->find('count',array('conditions'=>array('QcDocumentCategory.publish'=>0)));

	$this->set(compact('count','published','unpublished','standards','parentQcDocumentCategories','systemTables','companies','preparedBies', 'approvedBies','modifiedBies','createdBies'));
}

/**
 * index method
 *
 * @return void
 */
public function index() {
	
	$conditions = $this->_check_request();
	$this->paginate = array('order'=>array('QcDocumentCategory.sr_no'=>'DESC'),'conditions'=>array($conditions));
	
	$this->QcDocumentCategory->recursive = 0;
	$this->set('qcDocumentCategories', $this->paginate());
	
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
	if (!$this->QcDocumentCategory->exists($id)) {
		throw new NotFoundException(__('Invalid qc document category'));
	}
	$options = array('conditions' => array('QcDocumentCategory.' . $this->QcDocumentCategory->primaryKey => $id));
	$this->set('qcDocumentCategory', $this->QcDocumentCategory->find('first', $options));
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
		$userids = $this->User->find('list',array('order'=>array('User.name'=>'ASC'),'conditions'=>array('User.publish'=>1,'User.soft_delete'=>0,'User.is_approver'=>1)));
		$this->set(array('userids'=>$userids,'show_approvals'=>$this->_show_approvals()));
	}
	
	if ($this->request->is('post')) {
		$this->request->data['QcDocumentCategory']['system_table_id'] = $this->_get_system_table_id();
		$this->request->data[$this->modelClass]['publish'] = $this->request->data['Approval']['QcDocumentCategory']['publish'];
		$this->QcDocumentCategory->create();
		if ($this->QcDocumentCategory->save($this->request->data)) {

			if($this->_show_approvals()){
				$this->loadModel('Approval');
				$this->Approval->create();
				$this->request->data['Approval']['model_name']='QcDocumentCategory';
				$this->request->data['Approval']['controller_name']=$this->request->params['controller'];
				$this->request->data['Approval']['user_id']=$this->request->data['Approval']['user_id'];
				$this->request->data['Approval']['from']=$this->Session->read('User.id');
				$this->request->data['Approval']['created_by']=$this->Session->read('User.id');
				$this->request->data['Approval']['modified_by']=$this->Session->read('User.id');
				$this->request->data['Approval']['record']=$this->QcDocumentCategory->id;
				$this->Approval->save($this->request->data['Approval']);
			}
			$this->Session->setFlash(__('The qc document category has been saved'));
			if($this->_show_evidence() == true)$this->redirect(array('action' => 'view',$this->QcDocumentCategory->id));
			else $this->redirect(array('action' => 'index'));
		} else {
			$this->Session->setFlash(__('The qc document category could not be saved. Please, try again.'));
		}
	}
	$this->_commons();

}


public function add_bulk() {
	if ($this->_show_approvals()) {
		$this->loadModel('User');
		$this->User->recursive = 0;
		$userids = $this->User->find('list', array('order' => array('User.name' => 'ASC'), 'conditions' => array('User.publish' => 1, 'User.soft_delete' => 0, 'User.is_approver' => 1)));
		$this->set(array('userids' => $userids, 'show_approvals' => $this->_show_approvals()));
	}
	if ($this->request->is('post')) {
		
		$this->request->data['QcDocumentCategory']['system_table_id'] = $this->_get_system_table_id();
		
		$this->request->data['QcDocumentCategory']['name'] = str_replace('"','',$this->request->data['QcDocumentCategory']['name']);
		if(strpos($this->request->data['QcDocumentCategory']['name'], '\r\n') !== false){
			$qcDocumentCategories = explode('\r\n',$this->request->data['QcDocumentCategory']['name']);    
		}else if(strpos($this->request->data['QcDocumentCategory']['name'], '\n') !== false){
			$qcDocumentCategories = explode('\n',$this->request->data['QcDocumentCategory']['name']);    
		}else if(strpos($this->request->data['QcDocumentCategory']['name'], PHP_EOL) !== false){                
			$qcDocumentCategories = explode(PHP_EOL,$this->request->data['QcDocumentCategory']['name']);    
		}else{
			
		}

		if($qcDocumentCategories){
			foreach($qcDocumentCategories as $qcDocumentCategory){
				
				$data['QcDocumentCategory'] = $this->request->data['QcDocumentCategory'];
				$rec = explode(',',trim($qcDocumentCategory));
				

				if(count($rec) ==2){
					$data['QcDocumentCategory']['short_name'] = trim($rec[0]);
					$data['QcDocumentCategory']['name'] = trim($rec[1]);
				}
				
				$exists = $this->QcDocumentCategory->find('count',array('conditions'=>array(
					'QcDocumentCategory.name'=>$data['QcDocumentCategory']['name'],
					'QcDocumentCategory.standard_id'=>$data['QcDocumentCategory']['standard_id'],
					'QcDocumentCategory.parent_id'=>$data['QcDocumentCategory']['parent_id'],

				)));
				$data['QcDocumentCategory']['publish'] = $this->request->data['QcDocumentCategory']['publish'];
				
				if($exists == 0){                    
					try {
						$this->QcDocumentCategory->create();
						$this->QcDocumentCategory->save($data['QcDocumentCategory'],false);
						
					} catch (Exception $e) {
						
					}        
				}                    
			}

			$this->Session->setFlash(__('Categories saved.'));
			$this->redirect(array('action' => 'index'));
                    // exit;
		}else{
			$this->Session->setFlash(__('Categories could not be saved. Please, try again.'));
			$this->redirect(array('action' => 'add_bulk'));
		}
		
	}
        // exit;
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
	if (!$this->QcDocumentCategory->exists($id)) {
		throw new NotFoundException(__('Invalid qc document category'));
	}
	
	if ($this->_show_approvals()) {
		$this->set(array('showApprovals' => $this->_show_approvals()));
	}
	
	if ($this->request->is('post') || $this->request->is('put')) {
		
		$this->request->data[$this->modelClass]['publish'] = $this->request->data['Approval']['QcDocumentCategory']['publish'];
		
		$this->request->data['QcDocumentCategory']['system_table_id'] = $this->_get_system_table_id();
		if ($this->QcDocumentCategory->save($this->request->data)) {

			if ($this->_show_approvals()) $this->_save_approvals();
			
			if ($this->_show_evidence() == true)
				$this->redirect(array('action' => 'view', $id));
			else
				$this->redirect(array('action' => 'index'));
		} else {
			$this->Session->setFlash(__('The qc document category could not be saved. Please, try again.'));
		}
	} else {
		$options = array('conditions' => array('QcDocumentCategory.' . $this->QcDocumentCategory->primaryKey => $id));
		$this->request->data = $this->QcDocumentCategory->find('first', $options);
	}
	
	$this->_commons();

}
}
