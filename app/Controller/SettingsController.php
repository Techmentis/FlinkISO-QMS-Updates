<?php
App::uses('AppController', 'Controller');
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');
App::uses('CakeEmail', 'Network/Email');

class SettingsController extends AppController {

    public function _get_system_table_id($controller = NULL) {
        $this->loadModel('SystemTable');
        $this->SystemTable->recursive = -1;
        $systemTableId = $this->SystemTable->find('first', array('conditions' => array('SystemTable.system_name' => $controller)));
        return $systemTableId['SystemTable']['id'];
    }

    public function view($id = null) {
        if (!$this->Setting->exists($id)) {
            throw new NotFoundException(__('Invalid company'));
        }
        $options = array('conditions' => array('Setting.' . $this->Setting->primaryKey => $id));
        $this->set('company', $this->Setting->find('first', $options));


        $document_types = $this->_get_specials()['Dashboard Files'];
        $this->set('document_types', $document_types);        
    }

    public function edit($id = null) {
        if (!$this->Setting->exists($id)) {
            throw new NotFoundException(__('Invalid company'));
        }
        if($this->_show_approvals()){
            $this->loadModel('User');
            $this->User->recursive = 0;
            $userids = $this->User->find('list',array('order'=>array('User.name'=>'ASC'),'conditions'=>array('User.publish'=>1,'User.soft_delete'=>0)));
            $this->set(array('userids'=>$userids,'show_approvals'=>$this->_show_approvals()));
        }


        if ($this->request->is('post') || $this->request->is('put')) {

            if(($this->request->data['Setting']['logo'] == 1) && isset($this->request->data['Setting']['company_logo']['error']) && $this->request->data['Setting']['company_logo']['error'] == 0){
                $file = new File($this->request->data['Setting']['company_logo']['name'], FALSE);
                $fileinfo = $file->info();

                if (filesize($this->request->data['Setting']['company_logo']['tmp_name']) > 5000000){
                    $this->Session->setFlash(__('Uploaded file exceeds maximum upload size limit. Please try again.'), 'default', array('class' => 'alert alert-danger'));
                    $this->redirect(array('action' => 'edit', $id));
                }

                if (mb_strlen($fileinfo['basename'],"UTF-8") > 225){
                    $nameLengthCheck = false;
                    $this->Session->setFlash(__('Logo file name is too long. Please try again.'), 'default', array('class' => 'alert alert-danger'));
                    $this->redirect(array('action' => 'edit', $id));
                }

                if(!in_array($fileinfo['extension'], array('jpg','jpe','jpeg','png'))){
                    $this->Session->setFlash(__('Logo file type is invalid. Please try again.'), 'default', array('class' => 'alert alert-danger'));
                    $this->redirect(array('action' => 'edit', $id));
                }

                if(!file_exists(WWW_ROOT . DS . 'img' . DS . 'logo')){
                    new Folder(WWW_ROOT . DS . 'img' . DS . 'logo', TRUE, 0777);
                }

             $moveLogo = move_uploaded_file($this->request->data['Setting']['company_logo']['tmp_name'], WWW_ROOT . DS . 'img' . DS . 'logo' . DS . $fileinfo['basename']); //die;
             if($moveLogo){

             }else{

             }
             
             if($moveLogo){
                    // $dir_name = WWW_ROOT . DS . 'img' . DS . 'logo' ;
                    // $dir = opendir($dir_name);
                    // chdir($dir_name);

                    // $imgFile = getimagesize($fileinfo['basename']);
                    // $format = $imgFile['mime'];

                    // if ($format != '') {
                    //     list($width, $height) = $imgFile;
                    //     $ratio = $width / $height;
                    //     $newheight = 80;
                    //     $newwidth = 80 * $ratio;
                    //     switch ($format) {
                    //         case 'image/jpeg':
                    //             $source = imagecreatefromjpeg($fileinfo['basename']);
                    //             break;
                    //         case 'image/png';
                    //             $source = imagecreatefrompng($fileinfo['basename']);
                    //             break;
                    //     }
                    //     $dest = imagecreatetruecolor($newwidth, $newheight);
                    //     imagealphablending($dest, false);
                    //     imagesavealpha($dest, true);
                    //     imagecopyresampled($dest, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

                    //     switch ($format) {
                    //         case 'image/jpeg':
                    //             imagedestroy($source);
                    //             @imagejpeg($dest, $fileinfo['basename'], 100);
                    //             imagedestroy($dest);
                    //             break;
                    //         case 'image/png';
                    //             imagedestroy($source);
                    //             @imagepng($dest, $fileinfo['basename'], 9);
                    //             imagedestroy($dest);
                    //             break;
                    //     }
                    // }
                    // $oldLogo = $this->Setting->find('first', array('conditions' => array('Setting.id' => $id), 'fields' => array('Setting.company_logo')));
                    // if(!empty($oldLogo)){
                    //     $oldLogoFile = new File(WWW_ROOT . DS . 'img' . DS . 'logo'. DS . $oldLogo['Company']['company_logo']);                                    
                    // }
                $this->request->data['Setting']['company_logo'] = $fileinfo['basename'];
                $this->Session->setFlash(__('Logo uploaded.'), 'default', array('class' => 'alert alert-succes'));
            } else {
                $this->Session->setFlash(__('Logo upload was not successful. Please try again.'), 'default', array('class' => 'alert alert-danger'));
                $this->redirect(array('action' => 'edit', $id));
            }
        } else if(($this->request->data['Setting']['logo'] == 1) && isset($this->request->data['Setting']['company_logo']['error']) && $this->request->data['Setting']['company_logo']['error'] == 1){
            $this->Session->setFlash(__('The uploaded file exceeds specified maximum file size. Contact your system administrator and try again.'), 'default', array('class' => 'alert alert-danger'));
            $this->redirect(array('action' => 'edit', $id));
        } else if($this->request->data['Setting']['logo'] == 0) {
            $oldLogo = $this->Setting->find('first', array('conditions' => array('Setting.id' => $id), 'fields' => array('Setting.company_logo')));
            if(!empty($oldLogo)){
                $oldLogoFile = new File(WWW_ROOT . DS . 'img' . DS . 'logo'. DS . $oldLogo['Company']['company_logo']);
                $oldLogoFile->delete();
            }
            $this->request->data['Setting']['company_logo'] = '';
        }else{
            unset( $this->request->data['Setting']['company_logo'] );
        }

        if ($this->Setting->save($this->request->data)) {
            $this->redirect(array('action' => 'edit',$id));
        }
        
    } else {
        $options = array('conditions' => array('Setting.' . $this->Setting->primaryKey => $id));
        $this->request->data = $this->Setting->find('first', $options);
    }

}

public function password_setting()
{

}

function smtp_details($username = null)
{    
    if ($this->request->is('post') || $this->request->is('put')) {
            
        $email = $this->request->data['Settings']['email_address'];
        if ($email) {
            $subject = "FlinkISO Email Setting Test";
        try {
            App::uses('CakeEmail', 'Network/Email');            
            $EmailConfig = new CakeEmail("fast");
            $EmailConfig->to($email);
            $EmailConfig->subject($subject);
            $EmailConfig->emailFormat('text');
            $EmailConfig->send("Email test successfull");
            $this->Session->setFlash(__('Email sent successfully'), 'default', array('class' => 'alert alert-succes'));
        }
        catch (Exception $e) {
            $this->Session->setFlash(__('Email configuration failed!'), 'default', array('class' => 'alert alert-succes'));
            $this->set('error',$e);
        }

    }
    }
}
}
