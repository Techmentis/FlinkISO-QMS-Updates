<?php
App::uses('AppController', 'Controller');
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');
/**
 * Files Controller
 *
 * @property File $File
 * @property PaginatorComponent $Paginator
 */
class TemplatesController extends AppController {

    public function index(){
        $conditions = $this->_check_request();        
        $this->paginate = array('order' => array('Template.sr_no' => 'DESC'), 'conditions' => array('Template.model'=>'Template'));
        $this->Template->recursive = 0;
        $this->set('templates', $this->paginate());
        $this->_get_count();
    }

    public function add(){
        if ($this->request->is('post') || $this->request->is('put')) {
            $filekey = $this->_generate_onlyoffice_key($this->request->data['Template']['name']);
            $this->request->data['Template']['file_key'] = $filekey;
            $this->request->data['Template']['user_id'] = $this->Session->read('User.id');
            $this->request->data['Template']['custom_table_id'] = 'templates';
            $this->Template->create();
            //check duplicate
            $templ = $this->Template->find('count',array('conditions'=>array('Template.name'=>$this->request->data['Template']['name'])));
            if($templ > 0){
                $this->Session->setFlash(__('Duplicate Name.'));
                $this->redirect(array('action' => 'add'));
            }
            
            $accessptedExtentions = array(
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.oasis.opendocument.spreadsheet',
                'application/vnd.oasis.opendocument.text',
                'application/rtf',
                'text/plain',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/pdf',
                'application/vnd.openxmlformats-officedocument.presentationml.presentation'

            );

            if(is_uploaded_file($this->request->data['Template']['file']['tmp_name'])){
                if(!in_array($this->request->data['Template']['file']['type'], $accessptedExtentions)){
                    $this->Session->setFlash(__('Incorrect File Type'));
                    $this->redirect(array('action' => 'add'));
                }
                $file_name = explode('.',$this->request->data['Template']['file']['name']);
                $ext = $file_name[count($file_name)-1];
                if($ext == 'doc' || $ext == 'docx' || $ext == 'txt' || $ext == 'odt')$this->request->data['Template']['file_type'] = 0;
                if($ext == 'xlx' || $ext == 'xlsx')$this->request->data['Template']['file_type'] = 1;
                if($ext == 'ppt' || $ext == 'pptx')$this->request->data['Template']['file_type'] = 2;

                $this->request->data['Template']['file_type'] = $ext;
            }else{
                if($this->request->data['Template']['file_type'] == 0)$this->request->data['Template']['file_type'] = 'docx';
                else if($this->request->data['Template']['file_type'] == 1)$this->request->data['Template']['file_type'] = 'xlsx';
                else if($this->request->data['Template']['file_type'] == 2)$this->request->data['Template']['file_type'] = 'pptx';
            }
            
            if($this->Template->save($this->request->data)){
                $id = $this->Template->id;
                $file_name = $this->_clean_table_names($this->request->data['Template']['name']);
                $file_name = $file_name . '.' . $this->request->data['Template']['file_type'];

                if (!file_exists(Configure::read('path') . DS . $id)) {
                    $pathfolder = new Folder();
                    if ($pathfolder->create(Configure::read('path') . DS . $id)) {
                        chmod(Configure::read('path') . DS . $id,0777);
                    } else {
                        echo "failed folder" . Configure::read('path');
                        exit;
                    }
                }

                if(is_uploaded_file($this->request->data['Template']['file']['tmp_name'])){
                    $file = Configure::read('path') . DS . $id . DS . $file_name;
                    if (move_uploaded_file($this->request->data['Template']['file']['tmp_name'], $file)) {

                    }else{
                        $this->Session->setFlash(__('Unable to save file'));
                        $this->redirect(array('action' => 'add'));
                    }
                }else{
                    $this->_load_blank_file($this->request->data['Template']['file_type'],$this->request->data['Template']['name'],$this->Template->id);
                    $this->Session->setFlash(__('Add template content'));
                    $this->redirect(array('action' => 'edit',$this->Template->id));
                }
                

                $this->Session->setFlash(__('Add template content'));
                $this->redirect(array('action' => 'edit',$this->Template->id));
            }else{
                $this->Session->setFlash(__('Something went wrong.'));
                $this->redirect(array('action' => 'add'));
            }
        }
    }

    public function view($id = null){
        $this->redirect(array('action' => 'edit',$id));
    }

    public function edit($id = null){
        if ($this->request->is('post') || $this->request->is('put')) {

        }
        $this->request->data = $this->Template->find('first',array('conditions'=>array('Template.id'=>$id)));
        $this->request->data['Template']['file_key'] = $this->_generate_onlyoffice_key($id. date('Ymdhis')); 
        $this->set('file_name',$this->_clean_table_names($this->request->data['Template']['name']));
    }

    public function check_duplicates($name = null){
        $this->autoRender = false;
        $name = base64_decode($name);
        $this->loadModel('File');
        $count = $this->File->find('count',array('conditions'=>array('File.name'=>$name)));
        if($count == 0){
            return 0;
        }else{
            return 1;
        }
        exit;
    }

    public function initial_add($model = null, $controller = null, $user_id = null, $qc_document_id = null, $name = null, $type = null, $file_key = null, $custom_table_id = null, $record_id = null) {
        $data['File']['name'] = $this->request->params['named']['name'];
        $data['File']['file_type'] = $this->request->params['named']['file_type'];
        $data['File']['file_key'] = $this->request->params['named']['file_key'];
        $data['File']['qc_document_id'] = $this->request->params['named']['qc_document_id'];
        $data['File']['custom_table_id'] = $this->request->params['named']['custom_table_id'];
        $data['File']['model'] = $this->request->params['named']['file_model'];
        $data['File']['controller'] = $this->request->params['named']['file_controller'];
        $data['File']['prepared_by'] = $data['File']['modified_by'] = $this->Session->read('User.employee_id');
        $data['File']['created'] = date('Y-m-d h:i:s');
        $data['File']['user_id'] = $this->request->params['named']['user_id'];
        $data['File']['record_id'] = $this->request->params['named']['record_id'];
        $this->File->create();
        $this->File->save($data, false);
        return $this->File->id;
    }    


    public function _load_blank_file($type = null, $name = null, $id = null){
        $name = $this->_clean_table_names($name);
        $to = WWW_ROOT . DS . 'files' . DS . $this->Session->read('User.company_id') . DS . 'templates' . DS . $id;
        if(!file_exists($to)){
            $testfolder = new Folder($to, TRUE, 0777);
            $testfolder->create($to);
            chmod($to, 0777);
        }
        
        $from = WWW_ROOT . 'files' . DS . 'samples' .DS . 'sample.'.$type;
        
        $to = $to . DS . $name .'.'.$type;
        
        if(file_exists($from)){
            if(copy($from,$to)){
                echo "File Copied";
            }else{
                echo "Unable to copy file";
            }

        }else{
            echo "File does not exists";
        }
    } 

    public function save_doc() {
        $this->autoRender = false;
        $local = $this->request->params['named'];
        
        if (($body_stream = file_get_contents("php://input")) === FALSE) {
            echo "Bad Request";
        }
        $data = json_decode($body_stream, TRUE);
        $this->log('data received : ' . json_encode($data));
        if ($data["status"] == 2) {
            $data = json_decode($body_stream, TRUE);
            $record_id = $this->request->params['named']['record_id'];
            $path_for_save = WWW_ROOT . 'files' . DS . $local['company_id'] . DS . 'templates' . DS . $local['record_id'];
            
            $this->loadModel('File');
            $file = $this->File->find('first',array('conditions'=>array('File.id'=>$local['record_id'])));
            
            $this->log('File details => ' . json_encode($file));
            if($file){            
                $file_type = $file['File']['file_type'];
                $file_name = $this->_clean_table_names($file['File']['name']);
                
                $file = $file_name . '.' . $file_type;
                $file_for_save = $path_for_save . DS . $file;
                $testfolder = new Folder($path_for_save);
                $testfolder->create($path_for_save);
                chmod($path_for_save, 0777);
                chmod($file_for_save, 0777);
                $downloadUri = $data["url"];
                if (($new_data = file_get_contents($downloadUri)) === FALSE) {
                } else {
                    if (file_put_contents($file_for_save, $new_data)) {
                        
                        $json = [
                            "created" => date("Y-m-d H:i:s"),
                            'uid'=>$data['history']['changes'][0]['user']['id'],
                            'name'=>$data['history']['changes'][0]['user']['name'],
                        ];
                        
                        // write the encoded file information to the createdInfo.json file
                        $version = 1;
                        $history_file_for_save = $path_for_save . DS .$file.'-hist' . DS . $version;
                        $historyFolder = new Folder($history_file_for_save);
                        $historyFolder->create($history_file_for_save);
                        chmod($history_file_for_save, 0777);
                        file_put_contents($history_file_for_save . DS . "createdInfo.json", json_encode($json, JSON_PRETTY_PRINT));


                    } else {
                       
                    }
                    
                }
                
                $key = $this->_generate_onlyoffice_key($record_id . date('Ymdhis'));
                $file['File']['file_key'] = $key;
                $this->File->create();
                $this->File->save($file,false);
            }
            
        }
        echo "{\"error\":0}";  
    }
}
