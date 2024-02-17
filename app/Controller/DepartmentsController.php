<?php
App::uses('AppController', 'Controller');
/**
 * Departments Controller
 *
 * @property Department $Department
 * @property PaginatorComponent $Paginator
 */
class DepartmentsController extends AppController {
    /**
     * Components
     *
     * @var array
     */
    public $components = array('Paginator');

    public function _commons(){
        $systemTables = $this->Department->SystemTable->find('list', array('conditions' => array('SystemTable.publish' => 1, 'SystemTable.soft_delete' => 0)));
        $preparedBies = $this->Department->PreparedBy->find('list', array('conditions' => array('PreparedBy.publish' => 1, 'PreparedBy.soft_delete' => 0)));
        $approvedBies = $this->Department->ApprovedBy->find('list', array('conditions' => array('ApprovedBy.publish' => 1, 'ApprovedBy.soft_delete' => 0)));
        $createdBies = $this->Department->CreatedBy->find('list', array('conditions' => array('CreatedBy.publish' => 1, 'CreatedBy.soft_delete' => 0)));
        $modifiedBies = $this->Department->ModifiedBy->find('list', array('conditions' => array('ModifiedBy.publish' => 1, 'ModifiedBy.soft_delete' => 0)));
        $this->set(compact('systemTables', 'preparedBies', 'approvedBies', 'createdBies', 'modifiedBies'));
        $count = $this->Department->find('count');
        $published = $this->Department->find('count', array('conditions' => array('Department.publish' => 1)));
        $unpublished = $this->Department->find('count', array('conditions' => array('Department.publish' => 0)));
        $this->set(compact('count', 'published', 'unpublished'));
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
        $this->Department->virtualFields = array(
            'employees'=>'select count(*) from employees where employees.department_id LIKE Department.id',
            'users'=>'select count(*) from users where users.department_id LIKE Department.id',
        );
        $this->paginate = array('order' => array('Department.sr_no' => 'DESC'), 'conditions' => array($conditions));
        $this->Department->recursive = 0;
        $this->set('departments', $this->paginate());
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
        if (!$this->Department->exists($id)) {
            throw new NotFoundException(__('Invalid department'));
        }
        $options = array('conditions' => array('Department.' . $this->Department->primaryKey => $id));
        $this->set('department', $this->Department->find('first', $options));
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
            $this->request->data['Department']['system_table_id'] = $this->_get_system_table_id();
            $this->Department->create();
            if ($this->Department->save($this->request->data)) {
                if ($this->_show_approvals()) {
                    $this->loadModel('Approval');
                    $this->Approval->create();
                    $this->request->data['Approval']['model_name'] = 'Department';
                    $this->request->data['Approval']['controller_name'] = $this->request->params['controller'];
                    $this->request->data['Approval']['user_id'] = $this->request->data['Approval']['user_id'];
                    $this->request->data['Approval']['from'] = $this->Session->read('User.id');
                    $this->request->data['Approval']['created_by'] = $this->Session->read('User.id');
                    $this->request->data['Approval']['modified_by'] = $this->Session->read('User.id');
                    $this->request->data['Approval']['record'] = $this->Department->id;
                    $this->Approval->save($this->request->data['Approval']);
                }
                $this->Session->setFlash(__('The department has been saved'));
                if ($this->_show_evidence() == true) $this->redirect(array('action' => 'view', $this->Department->id));
                else $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The department could not be saved. Please, try again.'));
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
            
            $this->request->data['Department']['system_table_id'] = $this->_get_system_table_id();
            
            $this->request->data['Department']['name'] = str_replace('"','',$this->request->data['Department']['name']);
            if(strpos($this->request->data['Department']['name'], '\r\n') !== false){
                $departments = explode('\r\n',$this->request->data['Department']['name']);    
            }else if(strpos($this->request->data['Department']['name'], '\n') !== false){
                $departments = explode('\n',$this->request->data['Department']['name']);    
            }else if(strpos($this->request->data['Department']['name'], PHP_EOL) !== false){                
                $departments = explode(PHP_EOL,$this->request->data['Department']['name']);    
            }else{
                
            }
            if($departments){
                foreach($departments as $department){
                    
                    $data['Department'] = $this->request->data['Department'];
                    $data['Department']['name'] = trim($department);
                    $data['Department']['details'] = 'Added as bulk';
                    $data['Department']['soft_delete'] = 0;
                    $data['Department']['prepared_by'] = $data['Department']['approved_by'] = $this->Session->read('User.employee_id');
                    
                    $exists = $this->Department->find('count',array('conditions'=>array('Department.name'=>trim($department))));
                    if($exists == 0){                    
                        try {
                            $this->Department->create();
                            $this->Department->save($data['Department'],false);
                            
                        } catch (Exception $e) {
                            
                        }
                    }                    
                }
                $this->Session->setFlash(__('The departments saved'));
                $this->redirect(array('action' => 'index'));
            }else{
                $this->Session->setFlash(__('The departments could not be saved. Please, try again.'));
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
        if (!$this->Department->exists($id)) {
            throw new NotFoundException(__('Invalid department'));
        }
        if ($this->_show_approvals()) {
            $this->set(array('showApprovals' => $this->_show_approvals()));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            
            $this->request->data['Department']['system_table_id'] = $this->_get_system_table_id();
            if ($this->Department->save($this->request->data)) {
                if ($this->_show_approvals()) $this->_save_approvals();
                if ($this->_show_evidence() == true) $this->redirect(array('action' => 'view', $id));
                else $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The department could not be saved. Please, try again.'));
            }
        } else {
            $options = array('conditions' => array('Department.' . $this->Department->primaryKey => $id));
            $this->request->data = $this->Department->find('first', $options);
        }
        $this->_commons();
    }
}
