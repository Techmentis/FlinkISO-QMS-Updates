<?php
App::uses('AppController', 'Controller');
/**
 * CustomFiles Controller
 *
 * @property CustomFile $CustomFile
 * @property PaginatorComponent $Paginator
 */
class CustomFilesController extends AppController {
    public function view_file() {
        $file = base64_decode($this->request->params['named']['file'], true);
        $file = explode(DS, $file);
        $file_name = $file[count($file) - 1];
        $customFiles = $this->CustomFile->find('all', array('conditions' => array('CustomFile.record' => $this->request->params['named']['id'], 'CustomFile.controller' => $this->request->controller, 'CustomFile.file_name LIKE ' => $file_name,)));
        $this->set('customFiles', $customFiles);
    }
    public function download_file() {
        if ($this->request->is('post')) {
            $this->loadModel('User');
            $user = $this->User->find('first', array('conditions' => array('User.status' => 1, 'User.soft_delete' => 0, 'User.publish' => 1, 'User.username' => $this->Session->read('User.username'))));
            if ($user) {
                if (trim($user['User']['password']) != trim(Security::hash($this->request->data['CustomFile']['password'], 'md5', true))) {
                    $this->Session->setFlash(__('Incorrect password', true), 'default', array('class' => 'alert-danger'));
                    // $this->redirect(array('controller' => 'CustomFile', 'action' => 'download_file','file'=>$this->request->data['CustomFile']['file']));
                    $this->redirect($this->request->data['CustomFile']['ref']);
                } else {
                    $file_path = base64_decode($this->request->data['CustomFile']['file']);
                    // $file_data = $this->FileUpload->find('first',array('conditions'=>array('FileUpload.id' =>$file_path)));
                    // //check for permissions
                    // $permissions = $this->FileUpload->FileShare->find('first',array(
                    // 'fields'=>array('FileShare.id','FileShare.users','FileShare.everyone','FileShare.file_upload_id'),
                    // 'recursive'=>-1,
                    // 'conditions'=>
                    // array('FileShare.file_upload_id' => $file_path,
                    //         'FileShare.branch_id' => $this->Session->read('User.branch_id')
                    //         )));
                    // if($permissions['FileShare']['everyone'] != 1 ){
                    //     if(in_array($this->Session->read('User.id'), json_decode($permissions['FileShare']['users'])) == false)
                    //     $this->redirect(array('controller'=>'file_uploads', 'action'=>'request_access',$file_path));
                    // }
                    // $full_path = Configure::read('MediaPath').'CustomFile'. DS . $this->Session->read('User.company_id'). DS .  $file_data['FileUpload']['file_dir'];
                    
                    $file = base64_decode($this->request->data['CustomFile']['file'], true);
                    $file = explode(DS, $file);
                    $file_name = $file[count($file) - 1];
                    
                    $this->_update_view($this->request->data['CustomFile']['id'], $file_name, 1, 0);
                    $this->autoRender = false;
                    $this->response->file($file_path, array('download' => true, 'name' => $file_data['FileUpload']['file_details'] . '.' . $file_data['FileUpload']['file_type']));
                    $this->response->send();
                }
            }
        } else {            
            $this->set('ref', $this->referer());
        }
    }
    public function delete_file() {
        if ($this->request->is('post')) {
            $this->loadModel('User');
            $user = $this->User->find('first', array('conditions' => array('User.status' => 1, 'User.soft_delete' => 0, 'User.publish' => 1, 'User.username' => $this->Session->read('User.username'))));
            if ($user) {
                if (trim($user['User']['password']) != trim(Security::hash($this->request->data['CustomFile']['password'], 'md5', true))) {
                    $this->Session->setFlash(__('Incorrect password', true), 'default', array('class' => 'alert-danger'));
                    // $this->redirect(array('controller' => 'CustomFile', 'action' => 'download_file','file'=>$this->request->data['CustomFile']['file']));
                    $this->redirect($this->request->data['CustomFile']['ref']);
                } else {
                    $this->autoRender = false;
                    $file_path = base64_decode($this->request->data['CustomFile']['file']);
                    unlink($file_path);
                    $this->_update_view($file_data['FileUpload']['id'], $_SESSION['User']['id'], 'Delete');
                    $this->Session->setFlash(__('File Deleted', true), 'default', array('class' => 'alert-success'));
                    $this->redirect($this->request->data['CustomFile']['ref']);
                }
            }
        } else {
            $this->set('ref', $this->referer());
        }
    }
    public function request_access($id = null) {
        $file = $this->FileUpload->find('first', array('conditions' => array('FileUpload.id' => $id)));
        $file_name = $file['FileUpload']['file_details'] . '.' . $file['FileUpload']['file_type'];
        $user = $this->FileUpload->User->find('first', array('conditions' => array('User.id' => $file['FileUpload']['user_id']), 'recursive' => - 1));
        $employee = $this->FileUpload->User->Employee->find('first', array('conditions' => array('Employee.id' => $user['User']['employee_id']), 'recursive' => - 1));
        if ($employee['Employee']['office_email']) {
            $email = $employee['Employee']['office_email'];
        } else {
            $email = $employee['Employee']['personal_email'];
        }
        if ($email) {
            try {
                App::uses('CakeEmail', 'Network/Email');
                
                $EmailConfig = new CakeEmail("fast");
                
                $EmailConfig->to($email);
                $EmailConfig->subject($employee['Employee']['name'] . ' is requesting file access');
                $EmailConfig->template('file_access');
                $EmailConfig->viewVars(array('employee' => $employee['Employee']['name'], 'file_name' => $file_name));
                // $EmailConfig->attachments(array($path . DS . $fileName));
                $EmailConfig->emailFormat('html');
                $EmailConfig->send();
                $this->Session->setFlash(__('Email sent succeefully.', true), 'smtp');
            }
            catch(Exception $e) {
                $this->Session->setFlash(__('Failed to send email. Please check smtp details.', true), 'smtp');
            }
        }
    }
    public function _update_view($id = null, $file_name = null, $type = null, $action = null) {
        $data['record'] = $id;
        $data['user_id'] = $this->Session->read('User.id');
        $data['employee_id'] = $this->Session->read('User.employee_id');
        $data['model'] = $this->modelClass;
        $data['controller'] = $this->request->controller;
        $data['file_name'] = $file_name;
        $data['file_type'] = $type;
        $data['action'] = $action;
        $data['user_sessions_id'] = $_SESSION['User']['user_session_id'];
        $data['publish'] = 1;
        $data['soft_delete'] = 0;        
        $this->CustomFile->create();
        $this->CustomFile->save($data, false);
        return true;
    }
}
