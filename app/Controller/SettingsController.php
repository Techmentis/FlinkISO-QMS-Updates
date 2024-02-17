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
    $this->loadModel('Company');
    if (!$this->Session->read('User.id')) {
        
        $record = $this->Company->find('first', array(
            'fields' => 'Company.smtp_setup',
            'recursive' => -1
        ));
        if ($record['Company']['smtp_setup'] == 1) {
            
            $this->Session->setFlash(__('Please login to setup SMTP details'), 'default', array(
                'class' => 'alert-danger'
            ));
            $this->redirect(array(
                'action' => 'login',
                $username
            ));
        }
        $this->layout = "login";
    }
    
    $isSmtp     = 0;
    $transport  = null;
    $SmtpDetail = $this->Company->find('first', array(
        'fields' => array(
            'Company.is_smtp',
            'Company.smtp_setup'
        ),
        'recursive' => -1
    ));
    if ($SmtpDetail['Company']['smtp_setup'] == 1) {
        try{
            $Email = new CakeEmail();
            $Email->config('smtp');
            $transport = $Email->transport('smtp')->config();    
        }catch(Exception $e){

        }
        
    }
    
    if ($SmtpDetail['Company']['is_smtp'] == 1) {
        $isSmtp = 1;
    }
    $this->set(compact('isSmtp', 'transport'));
    
    if ($this->request->is('post') || $this->request->is('put')) {

        if ($this->request->data['User']['is_smtp'] == 0) {
            $this->loadModel('Company');
            $id                         = $this->Company->find('first', array(
                'fields' => 'Company.id',
                'recursive' => -1
            ));
            $this->Company->id          = $id;
            $data['Company']['is_smtp'] = 0;
            $this->Company->id;
            $this->Company->save($data);
            $string = '<?php
            /**
 * This is email configuration file.
 *
 * Use it to configure email transports of Cake.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Config
 * @since         CakePHP(tm) v 2.0.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 *
 * Email configuration class.
 * You can specify multiple configurations for production, development and testing.
 *
 * transport => The name of a supported transport; valid options are as follows:
 *      Mail        - Send using PHP mail function
 *      Smtp        - Send using SMTP
 *      Debug       - Do not send the email, just return the result
 *
 * You can add custom transports (or override existing transports) by adding the
 * appropriate file to app/Network/Email. Transports should be named "YourTransport.php",
 * where "Your" is the name of the transport.
 *
 * from =>
 * The origin email. See CakeEmail::from() about the valid values
 *
 */
            class EmailConfig {

            public $default = array(
                "transport" => "Smtp",
                "from" => array("' . $this->request->data['Setting']['smtp_user'] . '" => "FlinkISO"),
                "host" => "' . $this->request->data['Setting']['smtp_host'] . '",
                "port" => ' . $this->request->data['Setting']['port'] . ',
                "timeout" => 30,
                "username" => "' . $this->request->data['Setting']['smtp_user'] . '",
                "password" => "' . $this->request->data['Setting']['smtp_password'] . '",
                "client" => null,
                "log" => false,
                );

            public $fast = array(
                "from" => "'.$this->request->data['Setting']['smtp_user'].'",
                "sender" => null,
                "to" => null,
                "cc" => null,
                "bcc" => null,
                "replyTo" => null,
                "readReceipt" => null,
                "returnPath" => null,
                "messageId" => true,
                "subject" => null,
                "message" => null,
                "headers" => null,
                "viewRender" => null,
                "template" => false,
                "layout" => false,
                "viewVars" => null,
                "attachments" => null,
                "emailFormat" => null,
                "transport" => "Smtp",
                "host" => "' . $this->request->data['Setting']['smtp_host'] . '",
                "port" => ' . $this->request->data['Setting']['port'] . ',
                "timeout" => 30,
                "username" =>  "' . $this->request->data['Setting']['smtp_user'] . '",
                "password" => "' . $this->request->data['Setting']['smtp_password'] . '",
                "client" => null,
                "log" => true,
                //"charset" => "utf-8",
                //"headerCharset" => "utf-8",
            );

            }';
            $fp = fopen(APP . "Config/email.php", "w");
            
            if(fwrite($fp, $string)){
                
            }else{
                $this->Session->setFlash(__('Unbale to write to the file. Make sure app/Config/email.php has 0777 permission.'), 'default', array(
                    'class' => 'alert-success'
                ));    

                $this->redirect(array(
                'controller' => 'settings',
                'action' => 'smtp_details',$this->Session->read('User.company_id')                
            ));
                
            }
            fclose($fp);
            
            $this->Session->setFlash(__('Default email setup done successfully.'), 'default', array(
                'class' => 'alert-success'
            ));
            $this->redirect(array(
                'controller' => 'users',
                'action' => 'login',
                $username
            ));
        } else {
            
            $this->loadModel('Company');
            $id                         = $this->Company->find('first', array(
                'fields' => 'Company.id',
                'recursive' => -1
            ));
            $this->Company->id          = $id;
            $data['Company']['is_smtp'] = 1;
            $this->Company->id;
            $this->Company->save($data);
            $string = '<?php
            /**
 * This is email configuration file.
 *
 * Use it to configure email transports of Cake.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Config
 * @since         CakePHP(tm) v 2.0.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 *
 * Email configuration class.
 * You can specify multiple configurations for production, development and testing.
 *
 * transport => The name of a supported transport; valid options are as follows:
 *      Mail        - Send using PHP mail function
 *      Smtp        - Send using SMTP
 *      Debug       - Do not send the email, just return the result
 *
 * You can add custom transports (or override existing transports) by adding the
 * appropriate file to app/Network/Email. Transports should be named "YourTransport.php",
 * where "Your" is the name of the transport.
 *
 * from =>
 * The origin email. See CakeEmail::from() about the valid values
 *
 */
            class EmailConfig {

                public $default = array(
                "transport" => "Mail",
                "from" => array("noreply@flinkiso.com" => "FlinkISO"),
        //"charset" => "utf-8",
        //"headerCharset" => "utf-8",
                );

                public $smtp = array(

                "transport" => "Smtp",
                "from" => array("' . $this->request->data['User']['smtp_user'] . '" => "FlinkISO"),
                "host" => "' . $this->request->data['User']['smtp_host'] . '",
                "port" => ' . $this->request->data['User']['port'] . ',
                "timeout" => 30,
                "username" => "' . $this->request->data['User']['smtp_user'] . '",
                "password" => "' . $this->request->data['User']['smtp_password'] . '",
                "client" => null,
                "log" => false,
                );

                public $fast = array(
                "from" => "you@localhost",
                "sender" => null,
                "to" => null,
                "cc" => null,
                "bcc" => null,
                "replyTo" => null,
                "readReceipt" => null,
                "returnPath" => null,
                "messageId" => true,
                "subject" => null,
                "message" => null,
                "headers" => null,
                "viewRender" => null,
                "template" => false,
                "layout" => false,
                "viewVars" => null,
                "attachments" => null,
                "emailFormat" => null,
                "transport" => "Smtp",
                "host" => "localhost",
                "port" => 25,
                "timeout" => 30,
                "username" => "user",
                "password" => "secret",
                "client" => null,
                "log" => true,
        //"charset" => "utf-8",
        //"headerCharset" => "utf-8",
                );



            }';
            
            $fp = fopen(APP . "Config/email.php", "w");
            if(fwrite($fp, $string)){

            }else{
                $this->Session->setFlash(__('Unbale to write to the file. Make sure app/Config/email.php has 0777 permission.'), 'default', array(
                    'class' => 'alert-success'
                ));    

                $this->redirect(array(
                'controller' => 'settings',
                'action' => 'smtp_details',$this->Session->read('User.company_id')                
            ));
                
            }
            fclose($fp);
            
            $this->loadModel('Employee');
            $userData = $this->Employee->find('first', array(
                'recursive' => -1
            ));
            
            if ($userData['Employee']['office_email'] != '') {
                $email = $userData['Employee']['office_email'];
            } else if ($userData['Employee']['personal_email'] != '') {
                $email = $userData['Employee']['personal_email'];
            }
            if ($email) {
                
                
                try {
                    
                    if(Configure::read('evnt') == 'Dev')$env = 'DEV';
                    elseif(Configure::read('evnt') == 'Qa')$env = 'QA';
                    else $env = "";

                    App::uses('CakeEmail', 'Network/Email');
                    $EmailConfig = new CakeEmail("smtp");
                    $EmailConfig->to($email);
                    $EmailConfig->subject('FlinkISO: Smtp Setup details');
                    $EmailConfig->template('smtpSetup');
                    $EmailConfig->viewVars(array(
                        'name' => $userData['Employee']['name'],
                        'env' => $env, 'app_url' => FULL_BASE_URL
                    ));
                    $EmailConfig->emailFormat('html');
                    
                    $EmailConfig->send();
                    $companyData               = array();
                    $companyData['id']         = $userData['Employee']['company_id'];
                    $companyData['smtp_setup'] = 1;
                    $this->loadModel('Company');
                    $this->Company->save($companyData, false);
                    $this->Session->setFlash(__('SMTP setup done successfully.'), 'default', array(
                        'class' => 'alert-success'
                    ));
                    
                    $this->redirect(array(
                        'controller' => 'users',
                        'action' => 'login',
                        $username
                    ));
                }
                catch (Exception $e) {
                    $exceptionMessage          = $e->getMessage();
                    $invalidPass               = substr($exceptionMessage, 0, 15);
                    $companyData               = array();
                    $companyData['id']         = $userData['Employee']['company_id'];
                    $companyData['smtp_setup'] = 0;
                    $this->loadModel('Company');
                    $this->Company->save($companyData, false);
                    if (($invalidPass === 'SMTP Error: 535') == 1) {
                        $this->Session->setFlash(__('Can not connect with SMTP server: Invalid password'), 'default', array(
                            'class' => 'alert-danger'
                        ));
                    } else {
                        $this->Session->setFlash(__('Can not connect with SMTP server: ' . $e->getMessage()), 'default', array(
                            'class' => 'alert-danger'
                        ));
                    }
                }
            }
        }
    }
    $isSmtp            = 0;
    $transport         = null;
    $default_transport = null;
    $SmtpDetail        = $this->Company->find('first', array(
        'fields' => array(
            'Company.is_smtp',
            'Company.smtp_setup'
        ),
        'recursive' => -1
    ));
    $Email             = new CakeEmail();
    if ($SmtpDetail['Company']['is_smtp'] == 1) {
        try{
            $Email->config('smtp');
            $transport = $Email->transport('smtp')->config();    
        }catch(Exception $e){
            
        } 
        
    } else {
        $Email->config('default');
        $default_transport = $Email->transport('default')->config();
    }
    
    if ($SmtpDetail['Company']['is_smtp'] == 1) {
        $isSmtp = 1;
    }
    $this->set(compact('isSmtp', 'transport', 'default_transport', 'username'));
}
}
