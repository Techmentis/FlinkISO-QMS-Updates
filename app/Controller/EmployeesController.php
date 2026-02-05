<?php
App::uses('AppController', 'Controller');
/**
 * Employees Controller
 *
 * @property Employee $Employee
 * @property PaginatorComponent $Paginator
 */
class EmployeesController extends AppController {
    /**
     * Components
     *
     * @var array
     */
    public $components = array('Paginator');

    public function _commons(){
        // $parentEmployees = $this->Employee->ParentEmployee->find('list', array('conditions' => array('ParentEmployee.publish' => 1, 'ParentEmployee.soft_delete' => 0)));
        $branches = $this->Employee->Branch->find('list', array('conditions' => array('Branch.publish' => 1, 'Branch.soft_delete' => 0)));
        $departments = $this->Employee->Department->find('list', array('conditions' => array('Department.publish' => 1, 'Department.soft_delete' => 0)));
        $designations = $this->Employee->Designation->find('list', array('conditions' => array('Designation.publish' => 1, 'Designation.soft_delete' => 0)));
        $systemTables = $this->Employee->SystemTable->find('list', array('conditions' => array('SystemTable.publish' => 1, 'SystemTable.soft_delete' => 0)));
        $companies = $this->Employee->Company->find('list', array('conditions' => array('Company.publish' => 1, 'Company.soft_delete' => 0)));
        $parents = $preparedBies = $this->Employee->PreparedBy->find('list', array('conditions' => array('PreparedBy.publish' => 1, 'PreparedBy.soft_delete' => 0)));
        $approvedBies = $this->Employee->ApprovedBy->find('list', array('conditions' => array('ApprovedBy.publish' => 1, 'ApprovedBy.soft_delete' => 0)));
        $createdBies = $this->Employee->CreatedBy->find('list', array('conditions' => array('CreatedBy.publish' => 1, 'CreatedBy.soft_delete' => 0)));
        $modifiedBies = $this->Employee->ModifiedBy->find('list', array('conditions' => array('ModifiedBy.publish' => 1, 'ModifiedBy.soft_delete' => 0)));
        $this->set(compact('parents', 'branches', 'departments', 'designations', 'systemTables', 'companies', 'preparedBies', 'approvedBies', 'createdBies', 'modifiedBies'));
        $count = $this->Employee->find('count');
        $published = $this->Employee->find('count', array('conditions' => array('Employee.publish' => 1)));
        $unpublished = $this->Employee->find('count', array('conditions' => array('Employee.publish' => 0)));
        $this->set(compact('count', 'published', 'unpublished'));
        $this->set('customArray',$this->Employee->customArray);
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
        $this->Employee->virtualFields = array(
            'user'=>'select `users.id` from `users` where `users`.`employee_id` LIKE Employee.id'
        );
        $this->paginate = array('order' => array('Employee.sr_no' => 'DESC'), 'conditions' => array($conditions));
        $this->Employee->recursive = 0;
        $this->set('employees', $this->paginate());
        $this->_get_count();
        $this->set('customArray',$this->Employee->customArray);
        $this->_commons();
    }
    /**    
    /**
     * view method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function view($id = null) {
        if (!$this->Employee->exists($id)) {
            throw new NotFoundException(__('Invalid employee'));
        }
        $options = array('conditions' => array('Employee.' . $this->Employee->primaryKey => $id));
        $this->set('employee', $this->Employee->find('first', $options));
        $this->set('customArray',$this->Employee->customArray);
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
            $this->request->data['Employee']['system_table_id'] = $this->_get_system_table_id();
            $this->Employee->create();
            if ($this->Employee->save($this->request->data)) {
                if ($this->_show_approvals()) {
                    $this->loadModel('Approval');
                    $this->Approval->create();
                    $this->request->data['Approval']['model_name'] = 'Employee';
                    $this->request->data['Approval']['controller_name'] = $this->request->params['controller'];
                    $this->request->data['Approval']['user_id'] = $this->request->data['Approval']['user_id'];
                    $this->request->data['Approval']['from'] = $this->Session->read('User.id');
                    $this->request->data['Approval']['created_by'] = $this->Session->read('User.id');
                    $this->request->data['Approval']['modified_by'] = $this->Session->read('User.id');
                    $this->request->data['Approval']['record'] = $this->Employee->id;
                    $this->Approval->save($this->request->data['Approval']);
                }
                $this->Session->setFlash(__('The employee has been saved'));
                if ($this->_show_evidence() == true) $this->redirect(array('action' => 'view', $this->Employee->id));
                else $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The employee could not be saved. Please, try again.'));
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
            $this->request->data['Employee']['system_table_id'] = $this->_get_system_table_id();
            
            $this->request->data['Employee']['name'] = str_replace('"','',$this->request->data['Employee']['name']);
            if(strpos($this->request->data['Employee']['name'], '\r\n') !== false){
                $employees = explode('\r\n',$this->request->data['Employee']['name']);    
            }else if(strpos($this->request->data['Employee']['name'], '\n') !== false){
                $employees = explode('\n',$this->request->data['Employee']['name']);    
            }else if(strpos($this->request->data['Employee']['name'], PHP_EOL) !== false){                
                $employees = explode(PHP_EOL,$this->request->data['Employee']['name']);    
            }else{
                
            }

            
            if($employees){
                foreach($employees as $employee){
                    $data['Employee'] = $this->request->data['Employee'];
                    $rec = explode(',',trim($employee));
                    
                    if(count($rec) >= 3){
                        $data['Employee']['employee_number'] = trim($rec[0]);
                        $data['Employee']['name'] = trim($rec[1]);
                        $data['Employee']['office_email'] = trim($rec[2]);

                        if(trim($rec[3]) && (trim($rec[3]) == 'Yes' || trim($rec[3]) == 'yes'))$data['Employee']['is_hod'] = 1;
                        if(trim($rec[4]) && (trim($rec[4]) == 'Yes' || trim($rec[4]) == 'yes'))$data['Employee']['is_approver'] = 1;
                    }
                    if(filter_var($data['Employee']['office_email'], FILTER_VALIDATE_EMAIL)) {
                        $exists = $this->Employee->find('count',array('conditions'=>array(
                        'Employee.office_email'=>$data['Employee']['office_email'],

                        )));
                        $data['Employee']['publish'] = $this->request->data['Employee']['publish'];
                        $data['Employee']['prepared_by'] = $data['Employee']['approved_by'] = $this->Session->read('User.employee_id');
                        $data['Employee']['soft_delete'] = 0;
                        if($exists == 0){                    
                            try {
                                $this->Employee->create();
                                $this->Employee->save($data['Employee'],false);
                                if($this->request->data['Employee']['create_users'] == 1)$this->_add_user($data,$this->Employee->id);
                            } catch (Exception $e) {
                                
                            }        
                        }   
                    }
                    else {
                        $this->Session->setFlash(__('Employees could not be saved. Invalid Email'));
                        $this->redirect(array('action' => 'add_bulk'));
                    }                    
                }

                $this->Session->setFlash(__('Employees saved.'));
                $this->redirect(array('action' => 'index'));
            }else{
                $this->Session->setFlash(__('Employees could not be saved. Please, try again.'));
                $this->redirect(array('action' => 'add_bulk'));
            }
            
        }        
        $this->_commons();
    }


    public function _add_user($data = null, $employee_id = null){

        if($this->Session->read('User.is_mr') == true){
            $this->loadModel('User');
            $user['User']['employee_id'] = $employee_id;
            $user['User']['name'] = $data['Employee']['name'];
            $user['User']['username'] = $data['Employee']['office_email'];
            $user['User']['password'] = Security::hash($data['Employee']['office_email'], 'md5', true);
            $user['User']['branch_id'] = $data['Employee']['branch_id'];
            $user['User']['department_id'] = $data['Employee']['department_id'];
            $user['User']['assigned_branches'] = json_encode(array($data['Employee']['branch_id']));
            $user['User']['is_mr'] = 0;
            $user['User']['is_approver'] = 1;
            $user['User']['is_view_all'] = 1;
            $user['User']['is_mt'] = 0;
            $user['User']['status'] = 1;
            $user['User']['publish'] = $data['Employee']['publish'];
            $user['User']['language_id'] = 1;
            $user['User']['allow_multiple_login'] = 0;
            $user['User']['limit_login_attempt'] = 1;
            $user['User']['benchmark'] = 0;
            $user['User']['agree'] = 0;
            $user['User']['prepared_by'] = $this->Session->read('User.employee_id');
            $user['User']['system_table_id'] = '5297b2e7-0a9c-46e3-96a6-2d8f0a000005';

            try {
                $this->User->create();
                $this->User->save($user['User'],false);
            } catch (Exception $e) {
                
            }
        }
    }


    /**
     * edit method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function edit($id = null) {
        if (!$this->Employee->exists($id)) {
            throw new NotFoundException(__('Invalid employee'));
        }
        if ($this->_show_approvals()) {
            $this->set(array('showApprovals' => $this->_show_approvals()));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            
            $this->request->data['Employee']['system_table_id'] = $this->_get_system_table_id();
            if ($this->Employee->save($this->request->data)) {



                //update user- emaployee name if user exists
                $user = $this->Employee->User->find('first',array('recursive'=>-1, 'conditions'=>array('User.employee_id'=>$id)));
                if($user){
                    $user['User']['name'] = $this->request->data['Employee']['name'];
                    $user['User']['branch_id'] =  $this->request->data['Employee']['branch_id'];
                    $user['User']['department_id'] =  $this->request->data['Employee']['department_id'];
                    $this->Employee->User->create();
                    $this->Employee->User->save($user,false);
                }
                
                if ($this->_show_approvals()) $this->_save_approvals();
                if ($this->_show_evidence() == true) $this->redirect(array('action' => 'view', $id));
                else $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The employee could not be saved. Please, try again.'));
            }
        } else {
            $options = array('conditions' => array('Employee.' . $this->Employee->primaryKey => $id));
            $this->request->data = $this->Employee->find('first', $options);
        }
        $this->_commons();
    }
    
    public function org_chart($result = array()) {
        $employees = $this->Employee->find('threaded', array('conditions' => array('Employee.publish' => 1), 'fields' => array('Employee.id', 'Employee.name', 'ParentEmployee.id', 'ParentEmployee.name', 'Employee.designation_id', 'Employee.parent_id', 'Designation.id', 'Designation.name'),));
        
        foreach ($employees as $employee) {
            if (file_exists(WWW_ROOT . 'img' . DS . $this->Session->read('User.company_id') . DS . 'avatar' . DS . $child_employee['Employee']['id'] . DS . 'avatar.png')) {
                $imagepath = Router::url('/', true) . "img" . DS . $this->Session->read("User.company_id") . DS . "avatar/". $child_employee['Employee']['id'] ."/avatar.png";
            } else {
                $imagepath = Router::url('/', true) . "img/img/avatar.png";
            }

            if (!empty($employee['children'])) {
                $result[] = array(
                    'id' => $employee['Employee']['id'], 
                    'name' => $employee['Employee']['name'], 
                    'title' => $employee['Designation']['name'], 
                    'imagepath'=>$imagepath,
                    'className' => 'top-level', 
                    'children' => $this->renderPosts($employee['children'], 
                $result));
            
            } else {
                $result[] = array(
                    'id'=>$employee['Employee']['id'], 
                    'name' => $employee['Employee']['name'], 
                    'title' => $employee['Designation']['name'],
                    'imagepath'=>$imagepath,
                    'className' => 'top-level',                     
                );
            }            
        }        
        $this->set('employees_orgchart', $result);
    }
    public function renderPosts($employeesArray, $tmpModel) {
        if (!isset($return)) {
            $return = array();
        }
        foreach ($employeesArray as $child_employee) {
            if (file_exists(WWW_ROOT . 'img' . DS . $this->Session->read('User.company_id') . DS . 'avatar' . DS . $child_employee['Employee']['id'] . DS . 'avatar.png')) {
                $imagepath = Router::url('/', true) . "img" . DS . $this->Session->read("User.company_id") . DS . "avatar/". $child_employee['Employee']['id'] ."/avatar.png";
            } else {
                $imagepath = Router::url('/', true) . "img/img/avatar.png";              
            }
            if (!empty($child_employee['children'])) {
                $children['id'] = $child_employee['Employee']['id'];
                $children['className'] = 'middle-level';
                $children['name'] = $child_employee['Employee']['name'];
                $children['title'] = $child_employee['Designation']['name'];
                $children['imagepath'] = $imagepath;
                $children['children'] = $this->renderPosts($child_employee['children'], $tmpModel);
                $return[] = $children;
            } else {
                $return[] = array('id' => $child_employee['Employee']['id'], 'name' => $child_employee['Employee']['name'], 'title' => $child_employee['Designation']['name'], 'className' => 'middle-level','imagepath'=>$imagepath);
            }
        }
        return $return;
    }

    public function upload(){
        if ($this->request->is('post')) {
            if($this->request->data && !empty($this->request->data['Employee']['signature']) && $this->request->data['Employee']['signature']['type'] != 'image/png' ){
                $this->Session->setFlash(__('Incorrect signature file.'));
                $this->redirect(array('action' => 'view',$this->Session->read('User.employee_id')));
            }else{

                if($this->request->data['Employee']['signature']['size'] > 20000){
                    $this->Session->setFlash(__('Signature file is too large'));
                    $this->redirect(array('action' => 'view',$this->Session->read('User.employee_id')));
                }

                $folder = new Folder();
                $path = WWW_ROOT . 'img' . DS . $this->Session->read('User.company_id');
                echo $path;
                if ($folder->create($path)) {
                } else {
                    echo "Folder creation failed";
                    exit;
                }

                $folder = new Folder();
                $path = WWW_ROOT . 'img' . DS . $this->Session->read('User.company_id') . DS . 'signature';
                echo $path;
                if ($folder->create($path)) {
                } else {
                    echo "Folder creation failed";
                    exit;
                }

                 $folder = new Folder();
                $path = WWW_ROOT . 'img' . DS . $this->Session->read('User.company_id') . DS . 'signature' . DS . $this->Session->read('User.employee_id');
                echo $path;
                if ($folder->create($path)) {
                } else {
                    echo "Folder creation failed";
                    exit;
                }
                $to = $path . DS . 'sign.png';
                if(move_uploaded_file($this->request->data['Employee']['signature']['tmp_name'], $to)){
                    $this->Session->setFlash(__('Signature file added'));
                    $this->redirect(array('action' => 'view',$this->Session->read('User.employee_id')));
                }else{
                    $this->Session->setFlash(__('Failed to add signature file.'));
                    $this->redirect(array('action' => 'view',$this->Session->read('User.employee_id')));
                }
            }
        }
    }

    public function profile(){
        if ($this->request->is('post')) {            
            if($this->request->data && !empty($this->request->data['Employee']['profile']) && $this->request->data['Employee']['profile']['type'] != 'image/png' ){
                $this->Session->setFlash(__('Incorrect Profile Picture.'));
                $this->redirect(array('action' => 'view',$this->Session->read('User.employee_id')));
            }else{
                if($this->request->data['Employee']['profile']['size'] > 800000){
                    $this->Session->setFlash(__('Profile Picture is too large'));
                    $this->redirect(array('action' => 'view',$this->Session->read('User.employee_id')));
                }

                $folder = new Folder();
                $path = WWW_ROOT . 'img' . DS . $this->Session->read('User.company_id') . DS . 'profile' . DS . $this->Session->read('User.employee_id');
                if ($folder->create($path)) {
                } else {
                    echo "Folder creation failed";
                    exit;
                }
                $to = $path . DS . 'profile.png';
                if(move_uploaded_file($this->request->data['Employee']['profile']['tmp_name'], $to)){
                    $this->Session->setFlash(__('Profile Picture added'));
                    $this->redirect(array('action' => 'view',$this->Session->read('User.employee_id')));
                }else{
                    $this->Session->setFlash(__('Failed to add Profile Picture.'));
                    $this->redirect(array('action' => 'view',$this->Session->read('User.employee_id')));
                }
            }
        }
    }

    public function update_parent($id = null, $value = null){
        $this->autoRender = false;
        if($this->Session->read('User.is_mr') == true){
            $this->Employee->read(null,$id);
            $this->Employee->set('parent_id',$value);
            $this->Employee->save();
            return true;
        }else{
            return false;
        }
    }

    public function save_signature(){
        $this->autoRender = false;
        $employee = $this->Employee->find('first',array('conditions'=>array('Employee.id'=>$this->Session->read('User.employee_id')),'recursive'=>-1));
        if(!empty($employee)){
            $employee['Employee']['signature'] = $this->request->data['str'];
            $this->Employee->create();
            $this->Employee->save($employee,false);
            return true;
        }else{
            return false;
        }
    }

    public function get_signature(){
        $this->autoRender = false;
        $employee = $this->Employee->find('first',array('conditions'=>array('Employee.id'=>$this->Session->read('User.employee_id')),'recursive'=>-1));
        if(!empty($employee)){
            return $employee['Employee']['signature'];
        }else{
            return false;
        }
    }

    public function get_signatures($id = null){
        $this->autoRender = false;
        $employee = $this->Employee->find('first',array('conditions'=>array('Employee.id'=>$id),'recursive'=>-1));
        if(!empty($employee)){
            return $employee['Employee']['signature'];
        }else{
            return false;
        }
    }
}
