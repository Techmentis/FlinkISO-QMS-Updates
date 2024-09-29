<?php
App::uses('AppController', 'Controller');
App::uses('CakeEmail', 'Network/Email');
/**
 * Users Controller
 *
 * @property User $User
 * @property PaginatorComponent $Paginator
 */
class UsersController extends AppController {
    /**
     * Components
     *
     * @var array
     */
    // public $components = array('Paginator');
    public $components = array('Ctrl');
    public function _commons() {
        $departments = $this->User->Department->find('list', array('conditions' => array('Department.publish' => 1, 'Department.soft_delete' => 0)));
        $branches = $this->User->Branch->find('list', array('conditions' => array('Branch.publish' => 1, 'Branch.soft_delete' => 0)));
        $systemTables = $this->User->SystemTable->find('list', array('conditions' => array('SystemTable.publish' => 1, 'SystemTable.soft_delete' => 0)));
        $companies = $this->User->Company->find('list', array('conditions' => array('Company.publish' => 1, 'Company.soft_delete' => 0)));
        $statusUserIds = $this->User->StatusUserId->find('list', array('conditions' => array('StatusUserId.publish' => 1, 'StatusUserId.soft_delete' => 0)));
        $createdBies = $this->User->CreatedBy->find('list', array('conditions' => array('CreatedBy.publish' => 1, 'CreatedBy.soft_delete' => 0)));
        $modifiedBies = $this->User->ModifiedBy->find('list', array('conditions' => array('ModifiedBy.publish' => 1, 'ModifiedBy.soft_delete' => 0)));
        $this->set(compact('employees', 'departments', 'branches', 'systemTables', 'companies', 'statusUserIds', 'createdBies', 'modifiedBies'));
        $count = $this->User->find('count');
        $published = $this->User->find('count', array('conditions' => array('User.publish' => 1)));
        $unpublished = $this->User->find('count', array('conditions' => array('User.publish' => 0)));
        $this->set(compact('count', 'published', 'unpublished'));
        $employees = $preparedBies = $approvedBies = $this->User->Employee->find('list', array('conditions' => array('Employee.publish' => 1, 'Employee.soft_delete' => 0)));
        $this->set(compact('employees', 'preparedBies', 'approvedBies'));
        $this->_get_branch_list();
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
        $this->paginate = array('order' => array('User.sr_no' => 'DESC'), 'conditions' => array($conditions));
        $this->User->recursive = 0;
        $this->set('users', $this->paginate());
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
        if (!$this->User->exists($id)) {
            throw new NotFoundException(__('Invalid user'));
        }
        $options = array('conditions' => array('User.' . $this->User->primaryKey => $id));
        $this->set('user', $this->User->find('first', $options));

        $branches = $this->User->Branch->find('list', array('conditions' => array('Branch.publish' => 1, 'Branch.soft_delete' => 0)));
        $this->set('branches',$branches);

        if($this->Session->read('User.is_mr') == 1){
            // get all the documents
            $this->loadModel('QcDocument');
            $qcDocuments = $this->QcDocument->find('all',array('conditions'=>array('QcDocument.parent_document_id'=>array(NULL,-1,'')),
                'fields'=>array('QcDocument.id','QcDocument.title','QcDocument.standard_id','QcDocument.user_id','QcDocument.branches','QcDocument.departments','QcDocument.designations','QcDocument.editors')
            ));            
            $this->set('qcDocuments',$qcDocuments);
        }        
    }
    /**
     * add method
     *
     * @return void
     */
    public function add() {

        if ($this->request->is('ajax')) { 
            $this->autoRender = false;
            $employee  = $this->User->Employee->find('first',array('conditions'=>array('Employee.id'=>$this->data['id']),'recursive'=>-1));
            if($employee){

                $user['User']['employee_id'] = $employee['Employee']['id'];
                $user['User']['name'] = $employee['Employee']['name'];
                $user['User']['username'] = $employee['Employee']['office_email'];
                $user['User']['password'] = Security::hash($employee['Employee']['office_email'], 'md5', true);
                $user['User']['branch_id'] = $employee['Employee']['branch_id'];
                $user['User']['department_id'] = $employee['Employee']['department_id'];
                $user['User']['assigned_branches'] = json_encode(array($employee['Employee']['branch_id']));
                $user['User']['is_mr'] = 0;

                if($employee['Employee']['is_approver'] == 1)$user['User']['is_approver'] = 1;
                else $user['User']['is_approver'] = 0;

                if($employee['Employee']['is_hod'] == 1)$user['User']['is_view_all'] = 1;
                else $user['User']['is_view_all'] = 0;

                $user['User']['is_mt'] = 0;
                $user['User']['status'] = 1;
                $user['User']['publish'] = $employee['Employee']['publish'];
                $user['User']['language_id'] = 1;
                $user['User']['allow_multiple_login'] = 0;
                $user['User']['limit_login_attempt'] = 1;
                $user['User']['benchmark'] = 0;
                $user['User']['agree'] = 0;
                $user['User']['prepared_by'] = $this->Session->read('User.employee_id');
                $user['User']['system_table_id'] = '5297b2e7-0a9c-46e3-96a6-2d8f0a000005';

                $this->loadModel('User');
                $this->User->create();
                if($this->User->save($user,false)){
                    
                    if ($employee['Employee']['office_email'] != '') {
                        $email = $employee['Employee']['office_email'];
                    } else if ($employee['Employee']['personal_email'] != '') {
                        $email = $employee['Employee']['personal_email'];
                    }
                    if ($email) {
                        try {
                            
                            $EmailConfig = new CakeEmail("fast");
                            $EmailConfig->to($email);
                            $EmailConfig->subject('FlinkISO: Login Details');
                            $EmailConfig->template('loginDetail');
                            $EmailConfig->viewVars(array(
                                'username' => $user['User']['username'],
                                'password' => $user['User']['username'],
                                'url' => Router::url('/', true).'users/login',
                            ));
                            $EmailConfig->emailFormat('html');
                            $EmailConfig->send();
                        }
                        catch (Exception $e) {
                            $this->Session->setFlash(__('The user has been saved but fail to send email. Please check smtp details.', true), 'smtp');
                            $this->redirect(array(
                                'action' => 'index'
                            ));
                            
                        }
                        
                    }
                }

                return true;
            }
            return true;
        }else{
            $this->Session->setFlash(__('Select employee first.'));
            $this->redirect(array('controller'=>'employees', 'action' => 'index'));
        }
    }

    public function check_username($username = null)
    {
        $this->layout   = 'ajax';
        $resultUsername = $this->User->find('count', array(
            'recursive' => -1,
            'conditions' => array(
                'User.username' => $username
            )
        ));
        if ($resultUsername != 0)
            $this->set('username_response', 'Username already exists. Please enter a different username');
        else
            return false;
    }
    /**
     * edit method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    
    public function edit($id = null) {
        if (!$this->User->exists($id)) {
            throw new NotFoundException(__('Invalid user'));
        }
        // if ($this->_show_approvals()) {
        //     $this->set(array('showApprovals' => $this->_show_approvals()));
        // }
        if ($this->request->is('post') || $this->request->is('put')) {
            $this->request->data['User']['system_table_id'] = $this->_get_system_table_id();
            $this->request->data['User']['assigned_branches'] = json_encode($this->request->data['User']['assigned_branches']);
            if ($this->User->save($this->request->data)) {
                if ($this->_show_approvals()) $this->_save_approvals();
                // if ($this->_show_evidence() == true)
                $this->redirect(array('action' => 'view', $id));
                // else $this->redirect(array('action' => 'index'));
                
            } else {
                $this->Session->setFlash(__('The user could not be saved. Please, try again.'));
            }
        } else {
            $options = array('conditions' => array('User.' . $this->User->primaryKey => $id));
            $this->request->data = $this->User->find('first', $options);
        }        
        $this->_commons();
    }

    public function reset_access($id = null) {
        $this->autoRender = false;
        if ($this->request->is('ajax')) { 
            $str = base64_decode($this->request->data['str']);
            $values = explode(',',$str);            

            $user= $this->User->find('first',array('recursive'=>-1,'conditions'=>array('User.id'=>$values[0])));
            if($user){
                if($values[1] == 'is_mr'){                
                    $user['User']['is_mr'] = $values[2];
                    $user['User']['is_view_all'] = $values[2];
                    $user['User']['is_approver'] = $values[2];
                }
                if($values[1] == 'is_view_all'){                
                    $user['User']['is_view_all'] = $values[2];
                }
                if($values[1] == 'is_approver'){                
                    $user['User']['is_approver'] = $values[2];
                }
                if($values[1] == 'status'){                
                    $user['User']['status'] = $values[2];
                }
            }
            $this->User->create();
            if($this->User->save($user,false)){
                if($values[1] == 'is_mr'){                
                    return $user['User']['is_mr'];
                }
                if($values[1] == 'is_view_all'){                
                    return $user['User']['is_view_all'];
                }
                if($values[1] == 'is_approver'){                
                    return $user['User']['is_approver'];
                }
                if($values[1] == 'status'){                
                    return $user['User']['status'];
                }
            }else{
                return 10;
            }
            
            
        }else{
            
        }        
    }
    public function change_password() {
        if ($this->request->is('post')) {

            $userData = $this->User->find('first', array('conditions' => array('id' => $this->Session->read('User.id')), 'recursive' => - 1));
            if ($userData['User']['password'] == Security::hash($this->request->data['User']['current_password'], 'md5', true)) {
                $newData = array();
                $newData['User']['id'] = $userData['User']['id'];
                $newData['User']['password'] = Security::hash($this->request->data['User']['new_password'], 'md5', true);                
                $old_pwd = $userData['User']['password'];
                
                $companyData = $this->User->Company->find('first', array(
                    'fields'=>array('Company.id','Company.activate_password_setting'),
                    'conditions' => array('id' => $this->Session->read('User.company_id')), 'recursive' => - 1));

                if($companyData['Company']['activate_password_setting']){
                    $result = $this->requestAction(array('plugin' => 'password_setting_manager', 'controller' => 'password_settings', 'action' => 'check_password_validation', 
                        $this->request->data['User']['new_password'], 
                        $userData['User']['password'], 
                        $userData['User']['username']
                    ));
                    if (!$result['valid']) {
                        $this->Session->setFlash(__($result['message']), 'default', array('class' => 'alert-danger'));
                        $this->redirect(array('action' => 'change_password'));
                    }
                }                
                
                if (isset($old_pwd) && is_array($old_pwd) && count($old_pwd) > 0) {
                    if (!in_array($newData['User']['password'], $old_pwd)) {
                        array_unshift($old_pwd, $newData['User']['password']);
                        $pwd_repeat_cnt = $this->requestAction(array('plugin' => 'password_setting_manager', 'controller' => 'password_settings', 'action' => 'get_password_repeat_len'));
                        if (count($old_pwd) > $pwd_repeat_cnt) {
                            $splited_arr = array_chunk($old_pwd, $pwd_repeat_cnt);
                            $old_pwd = $splited_arr[0];
                        }
                        $newData['User']['current_password'] = json_encode($old_pwd);
                    }
                } else {
                    $newData['User']['current_password'] = json_encode(array($newData['User']['password']));
                }
                
                $newData['User']['pwd_last_modified'] = date('Y-m-d H:i:s');
                if ($this->User->save($newData, false)) {
                    $this->Session->setFlash(__('Your password has been change'));
                    $this->redirect(array('action' => 'logout'));
                }
            } else {
                $this->Session->setFlash(__('Your current password does not match with the password you have entered. please try again.'), 'default', array('class' => 'alert-danger'));
                $this->redirect(array('action' => 'change_password'));
            }
        }

        $this->loadModel('PasswordSetting');
        $this->loadModel('Company');
        $this->PasswordSetting->recursive = - 1;
        $this->Company->recursive = - 1;
        $password_setting = $this->PasswordSetting->find('first');
        $companies = $this->Company->find('first');
        $password_setting['PasswordSetting']['activate_password_setting'] = $companies['Company']['activate_password_setting'];
        $opt_code = $companies['Company']['two_way_authentication'];
        $this->set('password_setting', $password_setting['PasswordSetting']);
        $this->set('opt_code', $opt_code);
    }
    /**
     /**
     * Bake Following Methods ONLY for USERS model - By TGS
     *
     */
     public function reset_password($params = null, $user = null) {
        $this->layout ='login';
        if ($this->request->is('post') || $this->request->is('put')) {            
            if($this->request->data['User']['username'] && $this->request->data['User']['temppassword'] && $this->request->data['User']['otp']){
                
                $user = $this->User->find('first',array('recursive'=>-1,'conditions'=>array(
                    'User.username'=>$this->request->data['User']['username'],
                    'User.password_token'=>$this->request->data['User']['otp'],
                    'UNIX_TIMESTAMP(User.email_token_expires) >'=> strtotime(date('Y-m-d H:i:s')),
                )));

                if($user){
                    $user['User']['password'] = Security::hash($this->request->data['User']['temppassword'], 'md5', true);                    
                    $this->User->create();
                    $this->User->save($user,false);
                    $this->Session->setFlash(__('Your password has been change'));
                    $this->redirect(array('action' => 'login'));
                }else{
                    $this->Session->setFlash(__('Incorrect details or User does not exist or OTP expired.'));
                    $this->redirect(array('action' => 'reset_password'));
                }                
            }else{
                $this->Session->setFlash(__('Incorrect details'));
                $this->redirect(array('action' => 'reset_password'));
            }
            exit;
        }
    }

    function _crypto_rand_secure($min, $max)
    {
        $range = $max - $min;
            if ($range < 1) return $min; // not so random...
            $log = ceil(log($range, 2));
            $bytes = (int) ($log / 8) + 1; // length in bytes
            $bits = (int) $log + 1; // length in bits
            $filter = (int) (1 << $bits) - 1; // set all lower bits to 1
            do {
                $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
                $rnd = $rnd & $filter; // discard irrelevant bits
            } while ($rnd > $range);
            return $min + $rnd;
        }

        
        public function _getToken($length)
        {
            $token = "";
            $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
            $codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
            $codeAlphabet.= "0123456789";
            $max = strlen($codeAlphabet); // edited

            for ($i=0; $i < $length; $i++) {
                $token .= $codeAlphabet[$this->_crypto_rand_secure(0, $max-1)];
            }

            return $token;
        }
        

        public function send_otp(){
            $this->autoRender = false;
            $user = $this->User->find('first',array('conditions'=>array('User.username'=>$this->request->data['email'])));
            if(!$user){
                return json_encode('User does not exist');
            }else{
                $to = $user['Employee']['office_email'];
                $otp = $this->_getToken(10);
                $user['User']['password_token'] = $otp;
                $user['User']['email_token_expires'] = date('Y-m-d H:i:s',strtotime('+1 hour'));
                $this->User->create();
                $this->User->save($user,false);

                App::uses('CakeEmail', 'Network/Email');
                
                $EmailConfig = new CakeEmail("fast");
                
                $EmailConfig->to($to);
                $EmailConfig->subject('FlinkISO: Password reset OTP');
                $EmailConfig->template('password_reset_request');
                $EmailConfig->viewVars(array(                
                    'otp' => $otp,
                ));
                $EmailConfig->emailFormat('html');
                if($EmailConfig->send()){
                    return json_encode('Yes');        
                }else{
                    return json_encode('no');
                }
            }
        }
        
        public function login() {  
            if($this->User->find('count') == 0 && $this->action !='register'){
                $this->Session->setFlash(__('Register your application before you begin'), 'default', array('class' => 'alert alert-danger'));
                $this->redirect(array('controller' => 'users', 'action' => 'register'));
            }            
            if ($this->request->is('ajax') == true) {
                $str = "Your session has expired, please login to continue";
                $this->Session->setFlash(__($str, true), 'default', array('class' => 'alert-danger'));
                $this->layout = 'ajax';
            } else {
                $this->layout = 'login';            
            }

            if ($this->Session->read('User.id')) {
                $this->redirect(array('controller' => 'users', 'action' => 'dashboard'));
            }

            if ($this->request->is('post')) {
                $this->loadModel('Company');
                $user = $this->User->find('first', array('conditions' => array('User.status' => 1, 'User.soft_delete' => 0, 'User.publish' => 1, 'User.username' => $this->data['User']['username'])));
                if ($user) {

                    if(!$user['User']['assigned_branches']){
                        $user['User']['assigned_branches'] = json_encode(array($user['Employee']['branch_id']));
                    }

                    $allUsers = $this->User->find('all', array('conditions' => array('User.login_status' => 1, 'User.id <>' => $user['User']['id'])));
                    $currentTime = date('Y-m-d H:i:s');
                    foreach ($allUsers as $user) {
                        $lastActTime = date('Y-m-d H:i:s', strtotime('+10 mins', strtotime($user['User']['last_activity'])));
                        if ($lastActTime < $currentTime) {
                            $this->User->read(null, $user['User']['id']);
                            $data['User']['last_activity'] = date('Y-m-d H:i:s');
                            $data['User']['login_status'] = 0;
                            $this->User->save($data, false);
                        }
                    }
                    $companyId = $user['User']['company_id'];
                    $companyData = $this->Company->find('first', array('conditions' => array('id' => $companyId), 'recursive' => - 1));
                    $currentTime = date('Y-m-d H:i:s');
                    if ($companyData && $companyData['Company']['allow_multiple_login'] == 0 && $user['User']['login_status'] == 1) {
                        $this->Session->setFlash(__('Already Logged in. Please wait while your earlier session expires.', true), 'default', array('class' => 'alert-danger'));
                        $this->redirect(array('controller' => 'users', 'action' => 'login'));
                    }
                    if (trim($user['User']['password']) != trim(Security::hash($this->data['User']['password'], 'md5', true))) {
                        if ($this->Session->read('Login.username') == $this->data['User']['username'] && $companyData['Company']['limit_login_attempt']) {
                            $this->Session->write('Login.count', $this->Session->read('Login.count') + 1);
                        } else {
                            $this->Session->write('Login.count', 1);
                        }
                        $this->Session->write('Login.username', $this->data['User']['username']);
                        if (3 <= ($this->Session->read('Login.count'))) {
                            $this->User->read(null, $user['User']['id']);
                            $data['User']['status'] = 3;
                            $this->User->save($data, false);
                            $this->Session->destroy();
                            $this->Session->setFlash(__('Your account is locked', true), 'default', array('class' => 'alert-danger'));
                            $this->redirect(array('controller' => 'users', 'action' => 'login'));
                        } else {
                            $this->Session->write('Login.username', $user['User']['username']);
                        }
                        if ($companyData['Company']['limit_login_attempt']) $this->Session->setFlash(__('Incorrect login credential : You have ' . (3 - $this->Session->read('Login.count')) . ' attempts left', true), 'default', array('class' => 'alert-danger'));
                        else $this->Session->setFlash(__('Incorrect login credential', true), 'default', array('class' => 'alert-danger'));
                        $this->redirect(array('controller' => 'users', 'action' => 'login'));
                        
                    } else {
                        if ($user['User']['last_login'] == '0000-00-00 00:00:00' && $user['User']['last_activity'] == '0000-00-00 00:00:00') {
                            $this->loadModel('Company');
                            $companyUsers = $this->User->find('all', array('conditions' => array('User.company_id' => $companyId, 'User.last_login !=' => '0000-00-00 00:00:00', 'User.last_activity !=' => '0000-00-00 00:00:00'), 'recursive' => - 1));
                            if (count($companyUsers) == 0) {
                                $CompanyData['Company']['id'] = $companyId;
                                $CompanyData['Company']['flinkiso_start_date'] = date('Y-m-d H:i:s');
                                $CompanyData['Company']['flinkiso_end_date'] = date('Y-m-d H:i:s', strtotime('+15 days', strtotime(date('Y-m-d H:i:s'))));
                                $this->Company->save($CompanyData, false);
                            }
                        }
                    }
                    
                    if($companyData['Company']['activate_password_setting'] == 1){
                        $result = $this->requestAction(array('plugin' => 'password_setting_manager', 'controller' => 'password_settings', 'action' => 'get_password_change_remind', urlencode($user['User']['pwd_last_modified'])));
                    
                        if (!$result['valid']) {
                            $this->Session->setFlash(__($result['msg']), 'default', array('class' => 'alert-danger'));
                            $this->redirect(array('controller' => 'users', 'action' => 'reset_password'));
                        }    
                    }
                    
                    $this->User->read(null, $user['User']['id']);
                    
                    if ($companyData['Company']['two_way_authentication'] == 1) {
                        $officeEmailId = $user['Employee']['office_email'];
                        $personalEmailId = $user['Employee']['personal_email'];
                        if ($officeEmailId != '') {
                            $email = $officeEmailId;
                        } else if ($personalEmailId != '') {
                            $email = $personalEmailId;
                        }
                        $otp_code = $this->User->generateToken(6);
                        if ($email) {
                            if (Configure::read('evnt') == 'Dev') $env = 'DEV';
                            elseif (Configure::read('evnt') == 'Qa') $env = 'QA';
                            else $env = "";
                            try {
                                App::uses('CakeEmail', 'Network/Email');
                                
                                $EmailConfig = new CakeEmail("fast");                                
                                $EmailConfig->to($email);
                                $EmailConfig->subject('One time OTP Code');
                                $EmailConfig->template('otpCode');
                                $EmailConfig->viewVars(array('otp_code' => $otp_code, 'env' => $env));
                                $EmailConfig->emailFormat('html');
                                $EmailConfig->send();
                            }
                            catch(Exception $e) {
                                $this->Session->setFlash(__('Can not email OTP Code. Please check SMTP details and email address is correct.'));
                                $this->redirect(array('controller' => 'users', 'action' => 'login'));
                            }
                        }
                        if (isset($otp_code)) {
                            $this->Session->write('OPTCode', $otp_code);
                            $this->Session->write('UserIdentity', $user['User']['id']);
                        }
                    }
                    if (isset($otp_code)) {
                        $this->redirect(array('controller' => 'users', 'action' => 'opt_check'));
                    } else {
                        $_SESSION['User']['id'] = $user['User']['id'];
                        $data['User']['last_login'] = date('Y-m-d H:i:s');
                        $data['User']['last_activity'] = date('Y-m-d H:i:s');
                    $data['User']['login_status'] = 0; //1
                    $this->User->save($data, false);
                    $this->Session->write('User.id', $user['User']['id']);
                    $this->Session->write('User.employee_id', $user['Employee']['id']);
                    $this->Session->write('User.branch_id', $user['User']['branch_id']);
                    $this->Session->write('User.department_id', $user['User']['department_id']);
                    $this->Session->write('User.designation_id', $user['Employee']['designation_id']);
                    $this->Session->write('User.branch', $user['Branch']['name']);
                    $this->Session->write('User.department', $user['Department']['name']);
                    $this->Session->write('User.name', $user['Employee']['name']);
                    $this->Session->write('User.username', $user['User']['username']);
                    $this->Session->write('User.lastLogin', $user['User']['last_login']);
                    $this->Session->write('User.is_mr', $user['User']['is_mr']);
                    $this->Session->write('User.is_mt', $user['User']['is_mt']);
                    $this->Session->write('User.hod', $user['Employee']['is_hod']);
                    $this->Session->write('User.company_id', $user['User']['company_id']);
                    $this->Session->write('User.is_smtp', $user['Company']['is_smtp']);
                    $this->Session->write('User.division_id', $user['User']['division_id']);
                    $this->Session->write('User.company_name', $companyData['Company']['name']);
                    $this->Session->write('User.dir_name', $companyData['Company']['dir_name']);
                    $this->Session->write('User.timezone', $companyData['Company']['timezone']);
                    $this->Session->write('User.version', $companyData['Company']['version']);

                    if ($user['User']['is_mr'] == 1) $this->Session->write('User.is_view_all', 1);
                    else $this->Session->write('User.is_view_all', $user['User']['is_view_all']);
                    $this->Session->write('User.is_approver', $user['User']['is_approver']);
                    
                    if ($user['User']['agree'] && $user['User']['agree'] != 0) {
                        $this->Session->write('TANDC', 1);
                        $this->loadModel('UserSession');
                        $this->UserSession->create();
                        $data['UserSession']['ip_address'] = $_SERVER['REMOTE_ADDR'];
                        $data['UserSession']['browser_details'] = json_encode($_SERVER);
                        $data['UserSession']['start_time'] = date('Y-m-d H:i:s');
                        $data['UserSession']['end_time'] = date('Y-m-d H:i:s');
                        $data['UserSession']['user_id'] = $this->Session->read('User.id');
                        $data['UserSession']['employee_id'] = $this->Session->read('User.employee_id');
                        $data['UserSession']['company_id'] = $this->Session->read('User.company_id');
                        $data['UserSession']['division_id'] = $this->Session->read('User.division_id');
                        $this->Session->write('User.assigned_branches', $user['User']['assigned_branches']);
                        $this->UserSession->save($data, false);
                        $this->Session->write('User.user_session_id', $this->UserSession->id);
                        $this->redirect(array('controller' => 'users', 'action' => 'terms_and_conditions'));
                    } else {
                        $this->loadModel('UserSession');
                        $this->UserSession->create();
                        $data['UserSession']['ip_address'] = $_SERVER['REMOTE_ADDR'];
                        $data['UserSession']['browser_details'] = json_encode($_SERVER);
                        $data['UserSession']['start_time'] = date('Y-m-d H:i:s');
                        $data['UserSession']['end_time'] = date('Y-m-d H:i:s');
                        $data['UserSession']['user_id'] = $this->Session->read('User.id');
                        $data['UserSession']['employee_id'] = $this->Session->read('User.employee_id');
                        $data['UserSession']['company_id'] = $this->Session->read('User.company_id');
                        $this->Session->write('User.assigned_branches', $user['User']['assigned_branches']);
                        $data['UserSession']['division_id'] = $this->Session->read('User.division_id');
                        $this->UserSession->save($data, false);
                        $this->Session->write('User.user_session_id', $this->UserSession->id);
                        $this->Session->write('User.expired',0);

                        $this->_check_updates();

                        $this->redirect(array('action' => 'dashboard'));                        
                    }
                }
            }
            $this->Session->setFlash(__('Incorrect Login Credentials or your account is locked or already logged in', true), 'default', array('class' => 'alert-danger'));
            $this->redirect(array('controller' => 'users', 'action' => 'login'));
        }

        $this->loadModel('PasswordSetting');
        $this->loadModel('Company');
        $this->PasswordSetting->recursive = - 1;
        $this->Company->recursive = - 1;
        $password_setting = $this->PasswordSetting->find('first');
        $companies = $this->Company->find('first',array('fields'=>array('Company.id','Company.two_way_authentication','Company.activate_password_setting')));        
        $password_setting['PasswordSetting']['activate_password_setting'] = $companies['Company']['activate_password_setting'];
        $opt_code = $companies['Company']['two_way_authentication'];
        $this->set('password_setting', $password_setting['PasswordSetting']);
        $this->set('opt_code', $opt_code);
    }
    public function logout() {
        if ($this->Session->read('User.id')) {
            $this->User->read(null, $this->Session->read('User.id'));
            $this->User->set('login_status', 0);
            $this->User->save();


            $this->loadModel('UserSession');
            $this->UserSession->read(null,$this->Session->read('User.user_session_id'));
            $this->UserSession->set('end_time',date('Y-m-d H:i:d'));
            $this->UserSession->save();
            
            $this->Session->write('User.id', NULL);
            $this->Session->destroy('User');


        }
        $this->Session->setFlash(__('You have been logged out' . $this->Session->read('User.id'), true));
        $this->redirect(array('controller' => 'users', 'action' => 'login'));
    }

    public function dashboard() {

        if ( ! is_writable(WWW_ROOT . DS . 'files')) {
            $this->Session->setFlash(__('Files directory is not writable. Make sure to add 0777 permission (recursive) to "'.WWW_ROOT . DS . 'files" folder'));
        }

        // check masters
        $this->User->virtualFields = array(
            'branches'=>'select COUNT(*) from branches',
            'departments'=>'select COUNT(*) from departments',
            'employees'=>'select COUNT(*) from employees',
            'users'=>'select COUNT(*) from users',
            'designations'=>'select COUNT(*) from designations',
            'qc_documents'=>'select COUNT(*) from qc_documents',
            'custom_tables'=>'select COUNT(*) from custom_tables',
        );
        $masters = $this->User->find('first',array(
            'recursive'=>-1,
            'fields'=>array(
                'User.branches',
                'User.departments',
                'User.employees',
                'User.users',
                'User.designations',
                'User.qc_documents',
                'User.custom_tables',
                'User.id'    
            )
        ));

        $this->set('masters',$masters);
        
        
        $this->loadModel('Approval');
        $this->loadModel('CustomTable');
        $this->CustomTable->virtualFields = array(
            'qc_parent' => 'select `qc_documents`.`parent_document_id` from `   qc_documents` where `qc_documents`.`id` LIKE CustomTable.qc_document_id'
        );
        $customTables = $this->CustomTable->find('all', array(
            'fields'=>array(
                'CustomTable.id',
                'CustomTable.name',
                'CustomTable.table_name',
                'CustomTable.table_version',
                'CustomTable.qc_document_id',
                'QcDocument.id',
                'QcDocument.title',
                'QcDocument.name',
                'QcDocument.document_number',
                'QcDocument.revision_number',
                'QcDocument.file_type',
                'QcDocument.schedule_id',
                'CustomTable.qc_parent',
            ),
            'order'=>array('CustomTable.name'=>'ASC'),
            'conditions' => array('CustomTable.qc_parent' => -1,  'CustomTable.publish' => 1, 'QcDocument.add_records' => 1, 'CustomTable.table_locked' => 0, 'CustomTable.table_name NOT LIKE' => '%_child_%', 'OR' => array('QcDocument.departments LIKE ' => '%' . $this->Session->read('User.department_id') . '%', 'QcDocument.branches LIKE ' => '%' . $this->Session->read('User.branch_id') . '%', 'QcDocument.user_id LIKE ' => '%' . $this->Session->read('User.id') . '%',))));
        
        $this->set('customTables', $customTables);
        $schedules = $this->CustomTable->QcDocument->Schedule->find('list');
        $this->set(compact('schedules'));


        $this->Approval->virtualFields = array(
            'custom_table_id'=>'select `custom_tables`.`id` from `custom_tables` where `custom_tables`.`table_name` = Approval.controller_name',
            'qc_document_id'=>'select `custom_tables`.`qc_document_id` from `custom_tables` where `custom_tables`.`table_name` = Approval.controller_name',
            'process_id'=>'select `custom_tables`.`process_id` from `custom_tables` where `custom_tables`.`table_name` = Approval.controller_name'
        );
        $approvals = $this->Approval->find('all', array('order' => array('Approval.created' => 'desc'), 'conditions' => array(
            'OR' => array('Approval.status is NULL', 'Approval.status' => 0), 
            // 'OR'=> array(
            'Approval.user_id' => $this->Session->read('User.id'),
            'Approval.user_id' => $this->Session->read('User.employee_id')
                // )
        )));

        $this->set('approvals', $approvals);
        $this->loadModel('ApprovalComment');
        

        $this->ApprovalComment->virtualFields = array(
            'custom_table_id'=>'select `custom_tables`.`id` from `custom_tables` where `custom_tables`.`table_name` = Approval.controller_name',
            'qc_document_id'=>'select `custom_tables`.`qc_document_id` from `custom_tables` where `custom_tables`.`table_name` = Approval.controller_name',
            'process_id'=>'select `custom_tables`.`process_id` from `custom_tables` where `custom_tables`.`table_name` = Approval.controller_name'
        );
        $approvalComments = $this->ApprovalComment->find('all', array('conditions' => array(
        // 'ApprovalComment.approval_statuses >'=> 0,
            'OR' => array('Approval.status is NULL', 'Approval.status' => 0),
        // 'Approval.status'=>0,
            'ApprovalComment.response_status' => 0, 
            'ApprovalComment.user_id' => $this->Session->read('User.id')
        ), 
            'group' => array('ApprovalComment.user_id','ApprovalComment.approval_id'), 
            'order' => array('ApprovalComment.sr_no' => 'DESC'),
        ));
       
        $this->set('approvalComments', $approvalComments);
        $this->_get_user_list();        
        
        $this->set('customTrigers',$this->_check_triggers());
        $this->set('qcDocForCoEdit',$this->_qc_doc_co_edit());

        $this->set('totalDocs',$this->QcDocument->find('count'));
        $this->set('totalTables',$this->CustomTable->find('count'));
        $this->loadModel('File');
        $this->set('totalFiles',$this->File->find('count'));

        // file storage size
        $folder = new Folder(WWW_ROOT . DS . 'files');
        $size = ($folder->dirsize() / 1000000);
        $this->set('totalFileSize',round($size));

    }

    public function _qc_doc_co_edit(){
        $this->loadModel('QcDocument');
        $qcDocs = $this->QcDocument->find('all',array(
            'fields'=>array(
                'QcDocument.id',
                'QcDocument.title',
                'QcDocument.name',
                'QcDocument.document_number',
                'QcDocument.standard_id',
                'QcDocument.clause_id',
                'QcDocument.document_status',
                'QcDocument.prepared_by',
                'QcDocument.qc_document_category_id',
                'PreparedBy.id',
                'PreparedBy.name',
                'QcDocumentCategory.id',
                'QcDocumentCategory.name',
                'QcDocument.created',
                'QcDocument.modified',
                'Standard.id',
                'Standard.name',
                'Clause.id',
                'Clause.title',
            ),
            'recursive'=>0,
            'conditions'=>array(
                'QcDocument.document_status'=>0,
                'OR'=>array(
                    'QcDocument.user_id'=> '%'. $this->Session->read('User.id'),
                    'QcDocument.prepared_by'=> '%'. $this->Session->read('User.employee_id'),
                    'QcDocument.editors LIKE ' => '%' . $this->Session->read('User.id') . '%'
                ),
                
                
            )
        ));
        return $qcDocs;
    }


    public function _getDbSize() {
        return 0;
    }
    public function _foldersize($dir) {
        $count_size = 0;
        $count = 0;
        $dir_array = scandir($dir);
        foreach ($dir_array as $key => $filename) {
            if ($filename != ".." && $filename != ".") {
                if (is_dir($dir . "/" . $filename)) {
                    $new_foldersize = $this->_foldersize($dir . "/" . $filename);
                    $count_size = $count_size + $new_foldersize[0];
                    $count = $count + $new_foldersize[1];
                } else if (is_file($dir . "/" . $filename)) {
                    $count_size = $count_size + filesize($dir . "/" . $filename);
                    $count++;
                }
            }
        }
        return array($count_size, $count);
    }
    public function user_access($id = null) {
        
    }
    public function access_denied() {
    }

    public function _check_triggers(){
    }

    public function register($downloadkey = NULL)
    {
        $this->layout = 'login';
        $usersExists = $this->User->find('count');
        if($usersExists && $usersExists > 0){
            $this->Session->setFlash(__('Already Registred.'));
            $this->redirect(array('controller' => 'users', 'action' => 'dashboard'));
        }

        $data = $this->request->params['pass'][0];
        $data = base64_decode($data);
        $this->request->data['User'] = json_decode($data,TRUE);
        
        if(!$this->request->data['User'] && $this->request->data['register']['email']){
            echo "make curl call";
            $curl = curl_init();
            $auth = base64_encode('abc:123');
            $head ='Authorization: Basic '. $auth;

            $path = Configure::read('ApiPath') . 'on_premise_users/register/' . $this->request->data['register']['email'];
            
            curl_setopt_array($curl, array(
              CURLOPT_URL => $path,
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 30,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "GET",
              CURLOPT_HTTPHEADER => array(            
              ),
          ));

            $response = curl_exec($curl);
            $user = json_decode($response,true);
            $user = json_decode($user['user'],true);
            $err = curl_error($curl);
            curl_close($curl);
            

            $this->request->data['User'] = $user;

            if($err){

            }       

            if($response){
                // return $response;
            }
        }

        if ($this->request->data['User']) {            

            //create Company
            $description = '<p>FlinkISO is a web based application (available On-Cloud well as On-Premise) which automates the entire ISO documentation process and facilitates customers to electronically store &amp;
            maintain all the relevant documents, quality &amp; procedure manuals and data at single source on cloud. From this extensive information,
            system can generate &ldquo;eye to detail reports&rdquo;, YOY performance analysis for the management to gauge, scale the organization growth and productivity,
            and move forward to take corrective actions.</p>';
            
            $welcomeMessage = '<p>TECHMENTIS GLOBAL SERVICES PVT. LTD offers an array of web business solutions through e-commerce, B2B, B2C and mobile applications.
            Our young and dynamic team not only thrives to create and innovate, but also focuses on building a sustainable business model which enables our clients to remain
            competitive and profitable in the versatile global market.</p>';
            
            $company['Company']['name']                = $this->request->data['User']['OnPremiseUser']['company'];
            $company['Company']['id']                  = $this->request->data['User']['OnPremiseUser']['id'];
            $company['Company']['dir_name']            = $this->request->data['User']['OnPremiseUser']['company'];
            $company['Company']['sample_data']         = 0;
            $company['Company']['description']         = $description;
            $company['Company']['welcome_message']     = $welcomeMessage;
            $company['Company']['number_of_branches']  = 1;
            $company['Company']['number_of_users']     = $this->request->data['User']['OnPremiseUser']['number_of_users'];
            $company['Company']['timezone']            = $this->request->data['User']['OnPremiseUser']['timezone'];
            $company['Company']['limit_login_attempt'] = 100;
            $company['Company']['flinkiso_start_date'] = date('Y-m-d');
            $company['Company']['flinkiso_end_date']   = date('Y-m-d',strtotime('+1 month'));
            $company['Company']['publish']             = 1;
            $company['Company']['soft_delete']         = 0;
            $company['Company']['branchid']            = '0';
            $company['Company']['departmentid']        = '0';
            $company['Company']['created_by']          = '0';
            $company['Company']['modified_by']         = '0';
            $company['Company']['smtp_setup']          = '1';
            $company['Company']['created']             = date('Y-m-d h:i:s');
            $company['Company']['created']             = date('Y-m-d h:i:s');
            $company['Company']['liscence_key']        = $this->request->data['User']['OnPremiseUser']['liscence_key_installed'];
            $this->loadModel('Company');
            $this->Company->create();


            //check if company exist
            $companyFind = $this->Company->find('first', array(
                'conditions' => array(
                    'Company.name' => $this->request->data['User']['OnPremiseUser']['company']
                ),
                'recursive' => -1
            ));
            if ($companyFind) {
                $companyId     = $companyFind['Company']['id'];
                $alreadyExists = true;
            } else {                
                if (($this->Company->save($company['Company'], false))) {
                    $companyId     = $this->Company->id;
                    $alreadyExists = false;
                }
            }            
            
            if ($companyId != null) {
                $branch_name                      = $this->request->data['User']['Customer']['city'] ? $this->request->data['User']['Customer']['city'] : 'Default';
                $branch['Branch']['name']         = $branch_name;
                $branch['Branch']['publish']      = 1;
                $branch['Branch']['soft_delete']  = 0;
                $branch['Branch']['branchid']     = '0';
                $branch['Branch']['departmentid'] = '0';
                $branch['Branch']['created_by']   = '0';
                $branch['Branch']['modified_by']  = '0';
                $branch['Branch']['created']      = date('Y-m-d h:i:s');
                $branch['Branch']['created']      = date('Y-m-d h:i:s');
                $this->loadModel('Branch');
                
                $findBranch = $this->Branch->find('first', array(
                    'conditions' => array(
                        'Branch.name' => $branch_name,
                        'Branch.company_id' => $companyId
                    )
                ));
                if ($findBranch) {
                    $branchId = $findBranch['Branch']['id'];
                } else {
                    $this->Branch->create();
                    if ($this->Branch->save($branch, false)) {
                        $branchId = $this->Branch->id;
                    }
                }
                
                if ($branchId != null) {
                    
                    $department['Department']['name']         = 'Quality Management';
                    $department['Department']['publish']      = 1;
                    $department['Department']['soft_delete']  = 0;
                    $department['Department']['branchid']     = '0';
                    $department['Department']['departmentid'] = '0';
                    $department['Department']['created_by']   = '0';
                    $department['Department']['modified_by']  = '0';
                    $department['Department']['created']      = date('Y-m-d h:i:s');
                    $department['Department']['created']      = date('Y-m-d h:i:s');
                    $this->loadModel('Department');
                    $this->Department->create();
                    if ($this->Department->save($department, false)) {
                        $departmentId = $this->Department->id;
                    }
                    
                    
                    $designation['Designation']['name']         = 'QA Manager';
                    $designation['Designation']['level']        = 0;
                    $designation['Designation']['publish']      = 1;
                    $designation['Designation']['soft_delete']  = 0;
                    $designation['Designation']['branchid']     = '0';
                    $designation['Designation']['departmentid'] = '0';
                    $designation['Designation']['created_by']   = '0';
                    $designation['Designation']['modified_by']  = '0';
                    $designation['Designation']['created']      = date('Y-m-d h:i:s');
                    $designation['Designation']['created']      = date('Y-m-d h:i:s');
                    $this->loadModel('Designation');
                    
                    
                    $this->Designation->create();
                    if ($this->Designation->save($designation, false)) {
                        $designationId = $this->Designation->id;
                    }
                    
                    
                    
                    
                    
                    $this->loadModel('Employee');
                    $employeeCount = $this->Employee->find('count', array(
                        'conditions' => array(
                            'Employee.company_id' => $companyId
                        )
                    ));
                    
                    
                    $employee['Employee']['name']               = $this->request->data['User']['OnPremiseUser']['name'];
                    $employee['Employee']['employee_number']    = substr(strtoupper($this->request->data['User']['OnPremiseUser']['company']), 0, 3) . '00' . ($employeeCount + 1);
                    $employee['Employee']['department_id']      = $departmentId;
                    $employee['Employee']['branch_id']          = $branchId;
                    $employee['Employee']['designation_id']     = $$departmentId;
                    $employee['Employee']['company_id']         = $companyId;
                    $employee['Employee']['joining_date']       = date('Y-m-d');
                    $employee['Employee']['publish']            = 1;
                    $employee['Employee']['soft_delete']        = 0;
                    $employee['Employee']['personal_telephone'] = $this->request->data['User']['OnPremiseUser']['phone'];
                    $employee['Employee']['office_telephone']   = $this->request->data['User']['OnPremiseUser']['phone'];
                    $employee['Employee']['mobile']             = $this->request->data['User']['OnPremiseUser']['phone'];
                    $employee['Employee']['personal_email']     = $this->request->data['User']['OnPremiseUser']['email'];
                    $employee['Employee']['office_email']       = $this->request->data['User']['OnPremiseUser']['email'];
                    $employee['Employee']['branchid']           = '0';
                    $employee['Employee']['departmentid']       = '0';
                    $employee['Employee']['created_by']         = '0';
                    $employee['Employee']['modified_by']        = '0';
                    $employee['Employee']['created']            = date('Y-m-d h:i:s');
                    $employee['Employee']['created']            = date('Y-m-d h:i:s');
                    
                    $employee['Employee']['system_table_id']          = '5297b2e7-959c-4892-b073-2d8f0a000005';
                    $employee['Employee']['master_list_of_format_id'] = '523ab4b6-cf7c-4de5-918b-6f22c6c3268c';
                    
                    
                    $this->Employee->create();
                    if ($this->Employee->save($employee, false)) {
                        
                        //create User
                        $encrypt                                  = $this->User->generateToken();
                        $user['User']['employee_id']              = $this->Employee->id;
                        $user['User']['company_id']               = $companyId;
                        $user['User']['name']                     = $this->request->data['User']['OnPremiseUser']['name'];
                        $user['User']['username']                 = $this->request->data['User']['OnPremiseUser']['email'];
                        $user['User']['password']                 = Security::hash($this->request->data['User']['OnPremiseUser']['email'], 'md5', true);
                        $user['User']['is_mr']                    = true;
                        $user['User']['is_mt']                    = true;
                        $user['User']['is_view_all']              = true;
                        $user['User']['is_approver']              = true;
                        $user['User']['status']                   = 1;
                        $user['User']['agree']                    = 0;
                        $user['User']['department_id']            = $departmentId;
                        $user['User']['branch_id']                = $branchId;                        
                        $user['User']['publish']                  = 1;
                        $user['User']['soft_delete']              = 0;
                        $user['User']['master_list_of_format_id'] = '523ae34c-bcc0-4c7d-b7aa-75cec6c3268c';
                        $user['User']['system_table_id']          = '5297b2e7-0a9c-46e3-96a6-2d8f0a000005';
                        $user['User']['allow_multiple_login']     = 1;
                        $user['User']['password_token']           = $encrypt;
                        $user['User']['branchid']                 = $branchId;
                        $user['User']['departmentid']             = $departmentId;;
                        $user['User']['created_by']               = '0';
                        $user['User']['modified_by']              = '0';
                        $user['User']['created']                  = date('Y-m-d h:i:s');
                        $user['User']['created']                  = date('Y-m-d h:i:s');
                        $user['User']['last_login']               = date('Y-m-d H:i:s');
                        $user['User']['last_activity']            = date('Y-m-d H:i:s');
                        $user['User']['copy_acl_from']            = '';
                        $user['User']['user_access']              = $this->Ctrl->get_defaults();
                        $this->User->create();
                        
                        if ($this->User->save($user, false)) {
                            
                            
                            
                            $data                              = null;
                            $data['Employee']['id']            = $this->Employee->id;
                            $data['Employee']['branch_id']     = $branchId;
                            $data['Employee']['department_id'] = $departmentId;
                            $data['Employee']['created_by']    = $this->User->id;
                            $data['Employee']['company_id']    = $companyId;
                            $this->Employee->save($data, false);
                            
                            $data = null;
                            
                            $data['Branch']['id']         = $branchId;
                            $data['Branch']['branchid']   = $branchId;
                            $data['Branch']['created_by'] = $this->User->id;
                            $data['Branch']['company_id'] = $companyId;
                            $this->Branch->save($data, false);
                            
                            $data                       = null;
                            $data['User']['id']         = $this->User->id;
                            $data['User']['created_by'] = $this->User->id;
                            $data['User']['company_id'] = $companyId;
                            $this->User->save($data, false);
                            
                            $data                          = null;
                            $data['Company']['id']         = $companyId;
                            $data['Company']['created_by'] = $this->User->id;
                            $data['Company']['company_id'] = $companyId;
                            $data['Company']['flinkiso_start_date'] = date('Y-m-d');
                            $data['Company']['flinkiso_end_date']   = date('Y-m-d',strtotime('1 month'));
                            $this->Company->save($data['Company'], false);                                                        

                            $EmailConfig = null;
                            
                            // curl_close($ch);
                            
                            if ($ret) {
                                $data                           = null;
                                $data['Employee']['id']         = $this->Employee->id;
                                $data['Employee']['registered'] = 1;
                                $this->Employee->save($data, false);
                            }
                            
                            $this->_update_sql($companyId);
                            if ($this->request->data['User']['sample_data'] == 1) {
                                $this->insert_sample_data();
                                $this->_update_sql($companyId);
                            }
                            
                            $this->Session->setFlash(__('Account created'));
                            file_put_contents(APP . 'Config/installed.txt', date('Y-m-d, H:i:s'));
                            unlink(APP . 'Config/installed_db.txt');
                            // $this->set('url',$this->request->data['User']['dir_name']);
                            echo "here";
                            $this->redirect(array('action' => 'login'));
                            
                        } else {
                            $this->Session->setFlash(__('Failed to create account. Error while creating user'), 'default', array(
                                'class' => 'alert-danger'
                            ));
                            $this->redirect(array(
                                'action' => 'register'
                            ));
                        }
                    } else {
                        $this->Session->setFlash(__('Failed to create account. Error while creating employee'), 'default', array(
                            'class' => 'alert-danger'
                        ));
                        $this->redirect(array(
                            'action' => 'register'
                        ));
                    }
                } else {
                    $this->Session->setFlash(__('Failed to create account. Error while creating branch'), 'default', array(
                        'class' => 'alert-danger'
                    ));
                    $this->redirect(array(
                        'action' => 'register'
                    ));
                }
            } else {
                $this->Session->setFlash(__('Failed to create account. Error while creating company'), 'default', array(
                    'class' => 'alert-danger'
                ));
                $this->redirect(array(
                    'action' => 'register'
                ));
            }
            
        } else{
            // later add to check if any user exits; if not run this function.
            // $this->set('register',true);


        }       
        // $this->set('url',$this->request->data['User']['dir_name']);
        // $this->redirect(array('action' => 'register'));
    }


    public function _update_sql($companyId = null)
    {        
        
    }    

    public function _check_updates(){
        try {
            $updates = Xml::build('http://www.flinkiso.com/flinkiso-updates/application-updates.xml', array(
                'return' => 'simplexml'
            ));
            if ($updates) {
                foreach ($updates as $update):  
                    if($update->number > $this->Session->read('User.version')){ 
                        $this->Session->write('Update.update',1);
                    }else if($update->number == $this->Session->read('User.version')){
                        $this->Session->write('Update.update',0);
                    }else{
                        $this->Session->write('Update.update',0);
                    }
                endforeach;
                
            } else {
                $this->Session->write('Update.update',0);
            }
            
        }
        catch (Exception $e) {
            $this->Session->write('Update.update',0);
        }
    }

    public function install_updates(){
        $this->autoRender = false;
        $this->loadModel('Company');
        // check if user is on-premise or on-clould (may not require because of below step)
        // check if user is eligible for update by API call and check if billing etc is OK
        // if TRUE
        // Download MVC from fixed location to user's DIR
        // Download & RUN SQL on the server with the $updates->number.sql name
        // Once the SQL is run, update version in companies table with new version number.
        // redirect to login.

    }    
}
