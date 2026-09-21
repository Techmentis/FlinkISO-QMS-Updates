<?php
App::uses('AppController', 'Controller');
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');
App::uses('CakeEmail', 'Network/Email');
App::uses('CakeText', 'Utility');
App::uses('Security', 'Utility');

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

public function ai_setup()
{
    if ($this->Session->read('User.is_mr') != true) {
        throw new ForbiddenException(__('Only an administrator can configure AI.'));
    }
    $this->loadModel('AiSetting');
    if (!$this->_ensureAiSettingsTable()) {
        throw new InternalErrorException(__('AI settings storage could not be prepared.'));
    }
    $companyId = (string)$this->Session->read('User.company_id');
    $saved = $this->AiSetting->find('first', array('recursive' => -1, 'conditions' => array('AiSetting.company_id' => $companyId)));
    if ($this->request->is('post') || $this->request->is('put')) {
        $input = isset($this->request->data['AiSetting']) ? $this->request->data['AiSetting'] : array();
        $providers = array('ollama', 'openai_compatible', 'flinkiso_subscription');
        $provider = isset($input['ai_provider']) ? trim($input['ai_provider']) : '';
        $api = isset($input['ai_api']) ? rtrim(trim($input['ai_api']), '/') : '';
        $model = isset($input['ai_model']) ? trim($input['ai_model']) : '';
        $enabled = !empty($input['ai_enabled']);
        $newApiKey = isset($input['ai_api_key_plain']) ? trim($input['ai_api_key_plain']) : '';
        $hasSavedKey = !empty($saved['AiSetting']['ai_api_key']);
        $error = '';
        if (!in_array($provider, $providers, true)) $error = __('Select a valid AI provider.');
        if ($enabled && $provider !== 'flinkiso_subscription') {
            if ($api === '' || !filter_var($api, FILTER_VALIDATE_URL) || !in_array(strtolower(parse_url($api, PHP_URL_SCHEME)), array('http', 'https'), true)) {
                $error = __('Enter a valid HTTP or HTTPS AI API URL.');
            } elseif ($model === '') {
                $error = __('Enter an AI model.');
            } elseif (empty($input['ai_vision_model'])) {
                $error = __('Enter an AI vision model.');
            } elseif (empty($input['pdf_to_ppm_path']) || empty($input['libreoffice_path'])) {
                $error = __('Enter the PDF to PPM and LibreOffice paths.');
            } elseif ($provider === 'openai_compatible' && $newApiKey === '' && !$hasSavedKey) {
                $error = __('Enter the cloud AI API key.');
            }
        }
        if ($error !== '') {
            $this->Session->setFlash($error, 'default', array('class' => 'alert alert-danger'));
        } else {
            $row = array('AiSetting' => array(
                'id' => !empty($saved['AiSetting']['id']) ? $saved['AiSetting']['id'] : CakeText::uuid(),
                'company_id' => $companyId,
                'ai_enabled' => $enabled ? 1 : 0,
                'ai_provider' => $provider,
                'ai_api' => $api,
                'ai_model' => $model,
                'ai_vision_model' => isset($input['ai_vision_model']) ? trim($input['ai_vision_model']) : '',
                'ai_timeout' => max(30, min(1800, (int)$input['ai_timeout'])),
                'ai_vision_context' => max(4096, min(131072, (int)$input['ai_vision_context'])),
                'vision_pdf_max_pages' => max(1, min(50, (int)$input['vision_pdf_max_pages'])),
                'vision_page_pixels' => max(600, min(2400, (int)$input['vision_page_pixels'])),
                'pdf_to_ppm_path' => trim($input['pdf_to_ppm_path']),
                'libreoffice_path' => trim($input['libreoffice_path']),
                'modified' => date('Y-m-d H:i:s')
            ));
            if (empty($saved['AiSetting']['id'])) $row['AiSetting']['created'] = date('Y-m-d H:i:s');
            if ($newApiKey !== '') {
                try {
                    $row['AiSetting']['ai_api_key'] = $this->_encryptAiSecret($newApiKey);
                } catch (Exception $exception) {
                    CakeLog::write('error', 'AI API key encryption failed: '.$exception->getMessage());
                    $this->Session->setFlash(__('The AI API key could not be encrypted on this server.'), 'default', array('class' => 'alert alert-danger'));
                    $this->request->data['AiSetting']['ai_api_key_plain'] = '';
                    $this->set('hasApiKey', $hasSavedKey);
                    return;
                }
            } elseif ($hasSavedKey) {
                $row['AiSetting']['ai_api_key'] = $saved['AiSetting']['ai_api_key'];
            }
            $this->AiSetting->create();
            if ($this->AiSetting->save($row, false)) {
                $this->Session->setFlash(__('AI setup saved.'), 'default', array('class' => 'alert alert-success'));
                return $this->redirect(array('action' => 'ai_setup'));
            }
            $this->Session->setFlash(__('AI setup could not be saved.'), 'default', array('class' => 'alert alert-danger'));
        }
        $this->request->data['AiSetting']['ai_api_key_plain'] = '';
    } else {
        $this->request->data = $saved;
        if (empty($this->request->data['AiSetting'])) {
            $this->request->data['AiSetting'] = array(
                'ai_enabled' => 0, 'ai_provider' => 'ollama', 'ai_timeout' => 360,
                'ai_vision_context' => 32768, 'vision_pdf_max_pages' => 8,
                'vision_page_pixels' => 1200, 'pdf_to_ppm_path' => '/usr/bin/pdftoppm',
                'libreoffice_path' => '/usr/bin/libreoffice'
            );
        }
    }
    $this->set('hasApiKey', $hasSavedKey = !empty($saved['AiSetting']['ai_api_key']));
}

private function _ensureAiSettingsTable()
{
    try {
        $this->AiSetting->query("CREATE TABLE IF NOT EXISTS `ai_settings` (
            `id` char(36) NOT NULL, `company_id` char(36) NOT NULL,
            `ai_enabled` tinyint(1) NOT NULL DEFAULT 0,
            `ai_provider` varchar(40) NOT NULL DEFAULT '', `ai_api` varchar(1000) NOT NULL DEFAULT '',
            `ai_api_key` text, `ai_model` varchar(255) NOT NULL DEFAULT '', `ai_vision_model` varchar(255) NOT NULL DEFAULT '',
            `ai_timeout` int NOT NULL DEFAULT 360, `ai_vision_context` int NOT NULL DEFAULT 32768,
            `vision_pdf_max_pages` int NOT NULL DEFAULT 8, `vision_page_pixels` int NOT NULL DEFAULT 1200,
            `pdf_to_ppm_path` varchar(1000) NOT NULL DEFAULT '/usr/bin/pdftoppm',
            `libreoffice_path` varchar(1000) NOT NULL DEFAULT '/usr/bin/libreoffice',
            `created` datetime NOT NULL, `modified` datetime NOT NULL,
            PRIMARY KEY (`id`), UNIQUE KEY `company_ai_setting` (`company_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        $this->AiSetting->schema(true);
        return true;
    } catch (Exception $exception) {
        CakeLog::write('error', 'AI settings table unavailable: '.$exception->getMessage());
        return false;
    }
}
}
