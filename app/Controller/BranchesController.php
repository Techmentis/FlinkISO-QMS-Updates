<?php
App::uses('AppController', 'Controller');
/**
 * Branches Controller
 *
 * @property Branch $Branch
 * @property PaginatorComponent $Paginator
 */
class BranchesController extends AppController {
    /**
     * Components
     *
     * @var array
     */
    public $components = array('Paginator');
    
    public function _commons(){
        $systemTables = $this->Branch->SystemTable->find('list', array('conditions' => array('SystemTable.publish' => 1, 'SystemTable.soft_delete' => 0)));
        $companies = $this->Branch->Company->find('list', array('conditions' => array('Company.publish' => 1, 'Company.soft_delete' => 0)));
        $preparedBies = $this->Branch->PreparedBy->find('list', array('conditions' => array('PreparedBy.publish' => 1, 'PreparedBy.soft_delete' => 0)));
        $approvedBies = $this->Branch->ApprovedBy->find('list', array('conditions' => array('ApprovedBy.publish' => 1, 'ApprovedBy.soft_delete' => 0)));
        $createdBies = $this->Branch->CreatedBy->find('list', array('conditions' => array('CreatedBy.publish' => 1, 'CreatedBy.soft_delete' => 0)));
        $modifiedBies = $this->Branch->ModifiedBy->find('list', array('conditions' => array('ModifiedBy.publish' => 1, 'ModifiedBy.soft_delete' => 0)));
        $this->set(compact('systemTables', 'companies', 'preparedBies', 'approvedBies', 'createdBies', 'modifiedBies'));
        $count = $this->Branch->find('count');
        $published = $this->Branch->find('count', array('conditions' => array('Branch.publish' => 1)));
        $unpublished = $this->Branch->find('count', array('conditions' => array('Branch.publish' => 0)));
        $this->set(compact('count', 'published', 'unpublished'));
        $this->_get_department_list();
    }

    public function _get_system_table_id() {
        $this->loadModel('SystemTable');
        $this->SystemTable->recursive = - 1;
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

        $this->Branch->virtualFields = array(
            'employees'=>'select count(*) from employees where employees.branch_id LIKE Branch.id',
            'users'=>'select count(*) from users where users.branch_id LIKE Branch.id',
        );

        $this->paginate = array('order' => array('Branch.sr_no' => 'DESC'), 'conditions' => array($conditions));
        $this->Branch->recursive = 0;
        $this->set('branches', $this->paginate());
        $this->_get_count();
        $this->_get_department_list();
    }
    /**
     * view method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function view($id = null) {
        if (!$this->Branch->exists($id)) {
            throw new NotFoundException(__('Invalid branch'));
        }
        $options = array('conditions' => array('Branch.' . $this->Branch->primaryKey => $id));
        $this->set('branch', $this->Branch->find('first', $options));
        $this->_get_department_list();
    }
    /**
     * add method
     *
     * @return void
     */
    public function add() {
        if ($this->_show_approvals()) {
            $this->loadModel('User');
            $this->User->recursive = 0;
            $userids = $this->User->find('list', array('order' => array('User.name' => 'ASC'), 'conditions' => array('User.publish' => 1, 'User.soft_delete' => 0, 'User.is_approver' => 1)));
            $this->set(array('userids' => $userids, 'show_approvals' => $this->_show_approvals()));
        }
        if ($this->request->is('post')) {
            $this->request->data['Branch']['system_table_id'] = $this->_get_system_table_id();
            $this->request->data['Branch']['departments'] = json_encode($this->request->data['Branch']['departments']);
            
            $this->Branch->create();
            if ($this->Branch->save($this->request->data)) {
                if ($this->_show_approvals()) {
                    $this->loadModel('Approval');
                    $this->Approval->create();
                    $this->request->data['Approval']['model_name'] = 'Branch';
                    $this->request->data['Approval']['controller_name'] = $this->request->params['controller'];
                    $this->request->data['Approval']['user_id'] = $this->request->data['Approval']['user_id'];
                    $this->request->data['Approval']['from'] = $this->Session->read('User.id');
                    $this->request->data['Approval']['created_by'] = $this->Session->read('User.id');
                    $this->request->data['Approval']['modified_by'] = $this->Session->read('User.id');
                    $this->request->data['Approval']['record'] = $this->Branch->id;
                    $this->Approval->save($this->request->data['Approval']);
                }
                $this->Session->setFlash(__('The branch has been saved'));
                if ($this->_show_evidence() == true) $this->redirect(array('action' => 'view', $this->Branch->id));
                else $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The branch could not be saved. Please, try again.'));
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
            
            $this->request->data['Branch']['system_table_id'] = $this->_get_system_table_id();
            
            $this->request->data['Branch']['name'] = str_replace('"','',$this->request->data['Branch']['name']);
            if(strpos($this->request->data['Branch']['name'], '\r\n') !== false){
                $branches = explode('\r\n',$this->request->data['Branch']['name']);    
            }else if(strpos($this->request->data['Branch']['name'], '\n') !== false){
                $branches = explode('\n',$this->request->data['Branch']['name']);    
            }else if(strpos($this->request->data['Branch']['name'], PHP_EOL) !== false){                
                $branches = explode(PHP_EOL,$this->request->data['Branch']['name']);    
            }else{
                
            }

            if($branches){
                foreach($branches as $branch){
                    
                    $data['Branch'] = $this->request->data['Branch'];
                    $data['Branch']['name'] = trim($branch);
                    $data['Branch']['soft_delete'] = 0;
                    $data['Branch']['prepared_by'] = $data['Branch']['approved_by'] = $this->Session->read('User.employee_id');
                    
                    $exists = $this->Branch->find('count',array('conditions'=>array('Branch.name'=>trim($branch))));
                    if($exists == 0){                    
                        try {
                            $this->Branch->create();
                            $this->Branch->save($data['Branch'],false);
                            
                        } catch (Exception $e) {
                            
                        }        
                    }                    
                }
                $this->Session->setFlash(__('The branches saved.'));
                $this->redirect(array('action' => 'index'));
            }else{
                $this->Session->setFlash(__('The branches could not be saved. Please, try again.'));
                $this->redirect(array('action' => 'add_bulk'));
            }
            
        }

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
        if (!$this->Branch->exists($id)) {
            throw new NotFoundException(__('Invalid branch'));
        }
        if ($this->_show_approvals()) {
            $this->set(array('showApprovals' => $this->_show_approvals()));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            
            $this->request->data['Branch']['system_table_id'] = $this->_get_system_table_id();
            $this->request->data['Branch']['departments'] = json_encode($this->request->data['Branch']['departments']);
            if ($this->Branch->save($this->request->data)) {
                if ($this->_show_approvals()) $this->_save_approvals();
                if ($this->_show_evidence() == true) $this->redirect(array('action' => 'view', $id));
                else $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The branch could not be saved. Please, try again.'));
            }
        } else {
            $options = array('recursive'=>-1, 'conditions' => array('Branch.id'=>$id));
            $this->request->data = $this->Branch->find('first', $options);
        }
        $this->_commons();
    }
}
