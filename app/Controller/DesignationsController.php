<?php
App::uses('AppController', 'Controller');
/**
 * Designations Controller
 *
 * @property Designation $Designation
 * @property PaginatorComponent $Paginator
 */
class DesignationsController extends AppController {
    /**
     * Components
     *
     * @var array
     */
    public $components = array('Paginator');


    public function _commons(){        
    	$parentDesignations = $this->Designation->ParentDesignation->find('list', array('conditions' => array('ParentDesignation.publish' => 1)));
        $systemTables = $this->Designation->SystemTable->find('list', array('conditions' => array('SystemTable.publish' => 1, 'SystemTable.soft_delete' => 0)));        
        // $divisions = $this->Designation->Division->find('list', array('conditions' => array('Division.publish' => 1, 'Division.soft_delete' => 0)));
        $preparedBies = $this->Designation->PreparedBy->find('list', array('conditions' => array('PreparedBy.publish' => 1, 'PreparedBy.soft_delete' => 0)));
        $approvedBies = $this->Designation->ApprovedBy->find('list', array('conditions' => array('ApprovedBy.publish' => 1, 'ApprovedBy.soft_delete' => 0)));
        $createdBies = $this->Designation->CreatedBy->find('list', array('conditions' => array('CreatedBy.publish' => 1, 'CreatedBy.soft_delete' => 0)));
        $modifiedBies = $this->Designation->ModifiedBy->find('list', array('conditions' => array('ModifiedBy.publish' => 1, 'ModifiedBy.soft_delete' => 0)));
        $this->set(compact('parentDesignations', 'systemTables', 'masterListOfFormats', 'divisions', 'preparedBies', 'approvedBies', 'createdBies', 'modifiedBies'));
        $count = $this->Designation->find('count');
        $published = $this->Designation->find('count', array('conditions' => array('Designation.publish' => 1)));
        $unpublished = $this->Designation->find('count', array('conditions' => array('Designation.publish' => 0)));
        $this->set(compact('count', 'published', 'unpublished'));

        $parentDesignations = $this->Designation->ParentDesignation->find('list', array('conditions' => array('ParentDesignation.publish' => 1)));
        $this->set('parentDesignations',$parentDesignations);        
        
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

        $this->Designation->virtualFields = array(
            'employees' => 'select count(*) from employees where employees.designation_id LIKE Designation.id',            
        );
        $this->paginate = array('order' => array('Designation.sr_no' => 'DESC'), 'conditions' => array($conditions));
        $this->Designation->recursive = 0;
        $this->set('designations', $this->paginate());
        $this->_get_count();
        
        $parentDesignations = $this->Designation->ParentDesignation->find('list', array('conditions' => array('ParentDesignation.publish' => 1, 'ParentDesignation.soft_delete' => 0)));
        $this->set('parentDesignations',$parentDesignations);


    }
    /**
     * view method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function view($id = null) {
        if (!$this->Designation->exists($id)) {
            throw new NotFoundException(__('Invalid designation'));
        }
        $options = array('conditions' => array('Designation.' . $this->Designation->primaryKey => $id));
        $this->set('designation', $this->Designation->find('first', $options));
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
            $this->request->data['Designation']['system_table_id'] = $this->_get_system_table_id();
            $this->Designation->create();
            if ($this->Designation->save($this->request->data)) {
                if ($this->_show_approvals()) {
                    $this->loadModel('Approval');
                    $this->Approval->create();
                    $this->request->data['Approval']['model_name'] = 'Designation';
                    $this->request->data['Approval']['controller_name'] = $this->request->params['controller'];
                    $this->request->data['Approval']['user_id'] = $this->request->data['Approval']['user_id'];
                    $this->request->data['Approval']['from'] = $this->Session->read('User.id');
                    $this->request->data['Approval']['created_by'] = $this->Session->read('User.id');
                    $this->request->data['Approval']['modified_by'] = $this->Session->read('User.id');
                    $this->request->data['Approval']['record'] = $this->Designation->id;
                    $this->Approval->save($this->request->data['Approval']);
                }
                $this->Session->setFlash(__('The designation has been saved'));
                if ($this->_show_evidence() == true) $this->redirect(array('action' => 'view', $this->Designation->id));
                else $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The designation could not be saved. Please, try again.'));
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

            $this->request->data['Desigation']['system_table_id'] = $this->_get_system_table_id();
            
            $this->request->data['Designation']['name'] = str_replace('"','',$this->request->data['Designation']['name']);
            if(strpos($this->request->data['Designation']['name'], '\r\n') !== false){
                $designations = explode('\r\n',$this->request->data['Designation']['name']);    
            }else if(strpos($this->request->data['Designation']['name'], '\n') !== false){
                $designations = explode('\n',$this->request->data['Designation']['name']);    
            }else if(strpos($this->request->data['Designation']['name'], PHP_EOL) !== false){                
                $designations = explode(PHP_EOL,$this->request->data['Designation']['name']);    
            }else{

            }
            $parent_id = $this->request->data['Designation']['parent_id'];
            if($designations){
                foreach($designations as $designation){

                    $data['Designation'] = $this->request->data['Designation'];
                    $data['Designation']['name'] = trim($designation);
                    $data['Designation']['parent_id'] = $parent_id;
                    $data['Designation']['details'] = 'Added as bulk';                    
                    $data['Designation']['soft_delete'] = 0;
                    $data['Designation']['prepared_by'] = $data['Designation']['approved_by'] = $this->Session->read('User.employee_id');
                    $exists = $this->Designation->find('count',array('conditions'=>array('Designation.name'=>trim($designation))));

                    if($exists == 0){                    
                        try {
                            $this->Designation->create();
                            $this->Designation->save($data['Designation'],false);
                            
                        } catch (Exception $e) {

                        }
                    }                    
                }
                $this->Session->setFlash(__('The designations saved.'));
                $this->redirect(array('action' => 'index'));
            }else{
                $this->Session->setFlash(__('The designations could not be saved. Please, try again.'));
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
        if (!$this->Designation->exists($id)) {
            throw new NotFoundException(__('Invalid designation'));
        }
        if ($this->_show_approvals()) {
            $this->set(array('showApprovals' => $this->_show_approvals()));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            if (!isset($this->request->data[$this->modelClass]['publish']) && $this->request->data['Approval']['user_id'] != - 1) {
                $this->request->data[$this->modelClass]['publish'] = 0;
            }            
            $this->request->data['Designation']['system_table_id'] = $this->_get_system_table_id();
            if ($this->Designation->save($this->request->data)) {
                if ($this->_show_approvals()) $this->_save_approvals();
                if ($this->_show_evidence() == true) $this->redirect(array('action' => 'view', $id));
                else $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The designation could not be saved. Please, try again.'));
            }
        } else {
            $options = array('conditions' => array('Designation.' . $this->Designation->primaryKey => $id));
            $this->request->data = $this->Designation->find('first', $options);
        }
        $this->_commons();
    }

    public function update_parent($id = null, $value = null){
        $this->autoRender = false;
        $this->Designation->read(null,$id);
        $this->Designation->set('parent_id',$value);
        $this->Designation->save();
        return true;
    }

    public function org_chart($result = array()) {
        $designations = $this->Designation->find('threaded', array(            
            'conditions' => array('Designation.publish' => 1),
            'fields'=>array(              
            )
        ));
        foreach ($designations as $designation) {            
            $imagepath = Router::url('/', true) . "img/img/avatar.png";
            
            if ($designation['children']){
                $result[] = array('id' => $designation['Designation']['id'], 'name' => $designation['Designation']['name'], 'title' => $designation['Designation']['name'], 'className' => 'top-level', 'children' => $this->renderPosts($designation['children'], $result));
            } else {
                $result[] = array($designation['Designation']['name'], 'name' => $designation['Designation']['name'], 'title' => $designation['Designation']['name'], 'className' => 'top-level','imagepath'=>$imagepath);
            }
        }        
        $this->set('employees_orgchart', $result);
    }
    public function renderPosts($designationsArray, $tmpModel) {
        if (!isset($return)) {
            $return = array();
        }
        foreach ($designationsArray as $child_designation) {

            $imagepath = Router::url('/', true) . "img/img/avatar.png";              
            
            if (!empty($child_designation['children'])) {
                $children['id'] = $child_designation['Designation']['id'];
                $children['className'] = 'middle-level';
                $children['name'] = $child_designation['Designation']['name'];
                $children['title'] = $child_designation['Designation']['name'];
                $children['children'] = $this->renderPosts($child_designation['children'], $tmpModel);
                $return[] = $children;
            } else {
                $return[] = array('id' => $child_designation['Designation']['id'], 'name' => $child_designation['Designation']['name'], 'title' => $child_designation['Designation']['name'], 'className' => 'middle-level','imagepath'=>$imagepath);
            }
        }
        return $return;
    }
}
