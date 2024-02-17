<?php
	App::uses('AppController', 'Controller');
	App::uses('Folder', 'Utility');
	App::uses('File', 'Utility');


	class AuditCategoriesV0sController extends AppController {

		public $components = array('Paginator');

		public function _get_system_table_id() {
			$this->loadModel('SystemTable');
			$this->SystemTable->recursive = -1;
			$systemTableId = $this->SystemTable->find('first', array('conditions' => array('SystemTable.system_name' => $this->request->params['controller'])));
			return $systemTableId['SystemTable']['id'];
		}

		public function _commons($creator = null){

			if($this->action == 'view' || $this->action == 'edit' )$this->set('approvals',$this->get_approvals());

		    $companies = $this->AuditCategoriesV0->Company->find('list',array('conditions'=>array('Company.publish'=>1,'Company.soft_delete'=>0)));
			$preparedBies = $approvedBies = $this->AuditCategoriesV0->PreparedBy->find('list',array('conditions'=>array('PreparedBy.publish'=>1,'PreparedBy.soft_delete'=>0)));
			$createdBies = $modifiedBies = $this->AuditCategoriesV0->CreatedBy->find('list',array('conditions'=>array('CreatedBy.publish'=>1,'CreatedBy.soft_delete'=>0)));		
			$count = $this->AuditCategoriesV0->find('count');
			$published = $this->AuditCategoriesV0->find('count',array('conditions'=>array('AuditCategoriesV0.publish'=>1)));
			$unpublished = $this->AuditCategoriesV0->find('count',array('conditions'=>array('AuditCategoriesV0.publish'=>0)));

			$this->set(compact('companies', 'preparedBies', 'approvedBies', 'createdBies', 'modifiedBies','count','publish','unpublished'));


	
		if($this->request->params['named']['approval_id']){						
	
			$this->get_approval($this->request->params['named']['approval_id'],$creator);
	
			$this->_get_approval_comnments($this->request->params['named']['approval_id'],$creator);
	
		}
	
		$this->_get_approver_list();
	
		$this->_get_hasMany();


	}


	public function index() {
		
		$conditions = $this->_check_request();
		$this->paginate = array('order'=>array('AuditCategoriesV0.sr_no'=>'DESC'),'conditions'=>array($conditions));
	
		$this->AuditCategoriesV0->recursive = 0;
		$this->set('auditCategoriesV0s', $this->paginate());
		
		$this->_get_count();
			$this->_commons($this->Session->read('User.id'));		
	}

	public function view($id = null) {
		if (!$this->AuditCategoriesV0->exists($id)) {
			throw new NotFoundException(__('Invalid Record'));
		}
		$options = array('conditions' => array('AuditCategoriesV0.' . $this->AuditCategoriesV0->primaryKey => $id));
		$auditCategoriesV0 = $this->AuditCategoriesV0->find('first', $options);
		$this->set('auditCategoriesV0', $this->AuditCategoriesV0->find('first', $options));
		$this->_commons($this->Session->read('User.id'));		
	}

	public function add() {
	
		if ($this->_show_approvals()) {
            $this->set(array('showApprovals' => $this->_show_approvals()));
        }
		
        //check if table is unpublished
        $customTable = $this->AuditCategoriesV0->CustomTable->find('first',array('conditions'=>array('CustomTable.id'=>$this->request->params['named']['custom_table_id']),'recursive'=>-1));        

		if ($this->request->is('post')) {

			foreach($this->request->data['AuditCategoriesV0'] as $key => $value){
				if(is_array($value)){
					if($this->request->data['AuditCategoriesV0'][$key]['name']){
						$this->request->data['AuditCategoriesV0'][$key] = json_encode($value);	
						$this->request->data['Files'][] = $value;
					}else{
						$this->request->data['AuditCategoriesV0'][$key] = json_encode($value);
					}
					
				}
			}

			$this->request->data[$this->modelClass]['publish'] = $this->request->data['Approval'][$this->modelClass]['publish'];
			$this->request->data[$this->modelClass]['prepared_by'] = $this->request->data['Approval'][$this->modelClass]['prepared_by'];
			$this->request->data[$this->modelClass]['approved_by'] = $this->request->data['Approval'][$this->modelClass]['approved_by'];
			$this->request->data['AuditCategoriesV0']['system_table_id'] = $this->_get_system_table_id();
			$this->AuditCategoriesV0->create();
			if ($this->AuditCategoriesV0->save($this->request->data)) {				

				if ($this->_show_approvals()) $this->_save_approvals($this->AuditCategoriesV0->id);
				$this->Session->setFlash(__('Record has been saved'));
				if($this->_show_evidence() == true)$this->redirect(array('action' => 'view',$this->AuditCategoriesV0->id));
				$this->redirect(array('action' => 'index','custom_table_id'=>$this->request->params['named']['custom_table_id'],''=>$this->request->params['named']['']));
			} else {
				$this->Session->setFlash(__('Record could not be saved. Please, try again.'));
			}
		}
		
		$this->_commons($this->Session->read('User.id'));
		
		if(!isset($this->request->params['named'][''])){
			
		}else{
		}
	}
	
	public function edit($id = null) {
		if (!$this->AuditCategoriesV0->exists($id)) {
			throw new NotFoundException(__('Invalid qc document category'));
		}
		
		if ($this->_show_approvals()) {
            $this->set(array('showApprovals' => $this->_show_approvals()));
        }
		
		if (($this->request->is('post') || $this->request->is('put')) &&  isset($this->request->data[$this->modelClass])) {
      
			$this->request->data[$this->modelClass]['publish'] = $this->request->data['Approval'][$this->modelClass]['publish'];
						
			$this->request->data['AuditCategoriesV0']['system_table_id'] = $this->_get_system_table_id();

			foreach($this->request->data['AuditCategoriesV0'] as $key => $value){
				if(is_array($value)){
					if($this->request->data['AuditCategoriesV0'][$key]['name']){
						$this->request->data['AuditCategoriesV0'][$key] = json_encode($value);	
						$this->request->data['Files'][] = $value;
					}else{
						$this->request->data['AuditCategoriesV0'][$key] = json_encode($value);
					}
					
				}
			}

			if ($this->AuditCategoriesV0->save($this->request->data)) {

				if ($this->_show_approvals()) $this->_save_approvals($this->request->data['AuditCategoriesV0']['id']);
				
		 		$this->redirect(array('action' => 'index',''=>$this->request->params['named'][''],'custom_table_id'=>$this->request->params['named']['custom_table_id']));
			} else {
				$this->Session->setFlash(__('Record could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('AuditCategoriesV0.' . $this->AuditCategoriesV0->primaryKey => $id));
			$this->request->data = $this->AuditCategoriesV0->find('first', $options);
		}
		

		$this->_commons($this->Session->read('User.id'));
		}

}