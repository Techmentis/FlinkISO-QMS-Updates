<?php
App::uses('AppController', 'Controller');
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');
class ProcessesController extends AppController {
    public $components = array('Paginator');
    public function _get_system_table_id() {
        $this->loadModel('SystemTable');
        $this->SystemTable->recursive = - 1;
        $systemTableId = $this->SystemTable->find('first', array('conditions' => array('SystemTable.system_name' => $this->request->params['controller'])));
        return $systemTableId['SystemTable']['id'];
    }
    public function _commons($creator = null) {
        if ($this->action == 'view' || $this->action == 'edit') $this->set('approvals', $this->get_approvals());
        $companies = $this->Process->Company->find('list', array('conditions' => array('Company.publish' => 1, 'Company.soft_delete' => 0)));
        $preparedBies = $approvedBies = $this->Process->PreparedBy->find('list', array('conditions' => array('PreparedBy.publish' => 1, 'PreparedBy.soft_delete' => 0)));
        $createdBies = $modifiedBies = $this->Process->CreatedBy->find('list', array('conditions' => array('CreatedBy.publish' => 1, 'CreatedBy.soft_delete' => 0)));
        $count = $this->Process->find('count');
        $published = $this->Process->find('count', array('conditions' => array('Process.publish' => 1)));
        $unpublished = $this->Process->find('count', array('conditions' => array('Process.publish' => 0)));
        $this->set(compact('companies', 'preparedBies', 'approvedBies', 'createdBies', 'modifiedBies', 'count', 'publish', 'unpublished'));
        $processOwners = $this->Process->ProcessOwner->find('list', array('conditions' => array('ProcessOwner.publish' => 1, 'ProcessOwner.soft_delete' => 0)));
        $applicableToBranches = $this->Process->ApplicableToBranch->find('list', array('conditions' => array('ApplicableToBranch.publish' => 1, 'ApplicableToBranch.soft_delete' => 0)));
        $inputProcesses = $outputProcesses = $this->Process->InputProcess->find('list', array('conditions' => array('InputProcess.publish' => 1, 'InputProcess.soft_delete' => 0)));
        
        $qcDocuments = $this->Process->QcDocument->find('list', array('conditions' => array('QcDocument.document_type'=>2, 'QcDocument.publish' => 1, 'QcDocument.soft_delete' => 0)));
        $standards = $this->Process->Standard->find('list', array('conditions' => array('Standard.publish' => 1, 'Standard.soft_delete' => 0)));
        $clauses = $this->Process->Clause->find('list', array('conditions' => array('Clause.publish' => 1, 'Clause.soft_delete' => 0)));
        $schedules = $this->Process->Schedule->find('list', array('conditions' => array('Schedule.publish' => 1, 'Schedule.soft_delete' => 0)));
        
        $this->set('customArray', $this->Process->customArray);
        $this->set(compact('processOwners', 'applicableToBranches', 'inputProcesses', 'outputProcesses', 'qcDocuments', 'standards', 'clauses','schedules'));
        if ($this->request->params['named']['approval_id']) {
            $this->get_approval($this->request->params['named']['approval_id'], $creator);
            $this->_get_approval_comnments($this->request->params['named']['approval_id'], $creator);
        }
        $this->_get_approver_list();
        $this->_get_hasMany();
        // get hasMay
        $thisTable = $this->Process->CustomTable->find('first', array('recursive' => - 1, 'conditions' => array('CustomTable.id' => $this->request->params['named']['custom_table_id'])));
        if ($thisTable) {
            // find linked tables
            $linkedTables = $this->Process->CustomTable->find('all', array('recursive' => - 1, 'conditions' => array(
            
            'CustomTable.custom_table_id' => $this->request->params['named']['custom_table_id'])));
            $this->set('linkedTables', $linkedTables);
            $this->set('customArray', $this->Process->customArray);
        }
    }
    public function index() {

        $this->Process->virtualFields = array(
            'tables' => 'select count(*) from `custom_tables` where `custom_tables`.`process_id` LIKE Process.id', 
            'active_tables' => 'select count(*) from `custom_tables` where `custom_tables`.`publish` = 1 AND `custom_tables`.`table_locked` = 0 AND `custom_tables`.`process_id` LIKE Process.id'
        );

        // $conditions = $this->_check_request();
        $this->paginate = array('order' => array('Process.sr_no' => 'DESC'), 'conditions' => array($conditions));
        $this->Process->recursive = 0;
        $this->set('processes', $this->paginate());
        $this->_get_count();
        $this->_commons($this->Session->read('User.id'));
    }
    
    public function view($id = null) {
        if (!$this->Process->exists($id)) {
            throw new NotFoundException(__('Invalid qc document category'));
        }
        $options = array('conditions' => array('Process.' . $this->Process->primaryKey => $id));
        $process = $this->Process->find('first', $options);
        $this->set('process', $this->Process->find('first', $options));

        $key = $process['QcDocument']['file_key'];
        $file_type = $process['QcDocument']['file_type'];
        $file_name = $process['QcDocument']['title'];
        $document_number = $process['QcDocument']['document_number'];
        $document_version = $process['QcDocument']['revision_number'];

        $file_type = $process['QcDocument']['file_type'];
        
        if($file_type == 'doc' || $file_type == 'docx'){
            $documentType = 'word';
        }

        if($file_type == 'xls' || $file_type == 'xlsx'){
            $documentType = 'cell';
        }


        if($process['QcDocument']['id']){
            $mode = 'view';
            $file_path = $process['QcDocument']['id'];
            // $file = $document_number.'-'.$file_name.'-'.$document_version.'.'.$file_type;
            $file = $document_number.'-'.$file_name.'-'.$document_version;
            $file = ltrim(rtrim($file));
            $file = str_replace('-', '_', $file);
            $file = ltrim(rtrim(strtolower($file)));
            $file = preg_replace('/[\@\.\;\" "-]+/', '_', $file);
            $file = preg_replace('/  */', '_', $file);
            $file = preg_replace('/\\s+/', '_', $file);        
            $file = preg_replace('/-*-/', '_', $file);
            $file = preg_replace('/_*_/', '_', $file);
            $file = $this->_clean_table_names($file);
            $file = $file.'.'.$file_type;
            $path = Router::url('/',true) . '/files/' . $this->Session->read('User.company_id') . '/qc_documents/' . $file_path . '/' . $file;
            
            $htmlFile = WWW_ROOT .'files' . DS . $this->Session->read('User.company_id')  . DS . 'processes' . DS . $id . DS .  'template.html';
            if(file_exists($htmlFile)){
                $file = new File($htmlFile);
                $html = $file->read();
                $file->close();
                $this->set('html',$html);    
            }else{
                $html = $this->_convert_to_html($path,$file_type,'html',null,$process['QcDocument']['title'],$id,$this->Session->read('User.company_id'));
                $this->set('html',$html);    
            }
            
            $images = $this->_fetch_images($html);
            $this->set('images',$images);
        }else{
            $this->Session->setFlash(__('This process is not linked with any Document'));
        }       

        $this->_commons($this->Session->read('User.id'));
        $linkedTables = $this->Process->CustomTable->find('list', array('recursive' => - 1, 'conditions' => array('CustomTable.process_id' => $Process['Process']['id'])));
        $this->set('linkedTables', $linkedTables);
        $process_loop = $this->_process_loop($process);
        $this->set('ips', $process_loop[0]);
        $this->set('ops', $process_loop[1]);

    }

    public function update_process($id = null) {
        $process = $this->Process->find('first', array('conditions'=>array('Process.id'=>$id)));
        $key = $process['QcDocument']['file_key'];
        $file_type = $process['QcDocument']['file_type'];
        $file_name = $process['QcDocument']['title'];
        $document_number = $process['QcDocument']['document_number'];
        $document_version = $process['QcDocument']['revision_number'];

        $file_type = $process['QcDocument']['file_type'];
        
        if($file_type == 'doc' || $file_type == 'docx'){
            $documentType = 'word';
        }

        if($file_type == 'xls' || $file_type == 'xlsx'){
            $documentType = 'cell';
        }

        $mode = 'view';

        $file_path = $process['QcDocument']['id'];

        $file_path = $process['QcDocument']['id'];
        // $file = $document_number.'-'.$file_name.'-'.$document_version.'.'.$file_type;
        $file = $document_number.'-'.$file_name.'-'.$document_version;
        $file = ltrim(rtrim($file));
        $file = str_replace('-', '_', $file);
        $file = ltrim(rtrim(strtolower($file)));
        $file = preg_replace('/[\@\.\;\" "-]+/', '_', $file);
        $file = preg_replace('/  */', '_', $file);
        $file = preg_replace('/\\s+/', '_', $file);        
        $file = preg_replace('/-*-/', '_', $file);
        $file = preg_replace('/_*_/', '_', $file);
        $file = $this->_clean_table_names($file);

        $file = $file.'.'.$file_type;
        $path = Router::url('/',true) . '/files/' . $process['Process']['company_id'] . '/qc_documents/' . $file_path . '/' . $file;

        $htmlFile = WWW_ROOT .'files' . DS . $process['Process']['company_id']  . DS . 'processes' . DS . $id . DS .  'template.html';
        
        $html = $this->_convert_to_html($path,$file_type,'html',null,$process['QcDocument']['title'],$id,$process['Process']['company_id']);
        // $this->set('html',$html);    
        
    }


    public function _convert_to_html($url = null,$filetype = null,$outputtype = null, $password = null, $title = null,$record_id = null,$company_id = null){

        $path = Configure::read('OnlyofficeConversionApi'). '/ConvertService.ashx';
        $key = $this->_generate_onlyoffice_key($record_id . date('Ymdhis'));
        $payload = array(
            'async'=>false,
            'url'=>$url,
            'outputtype'=>$outputtype,
            'filetype'=>$filetype,
            'title'=>$title,
            'key'=>$key,            
        );
        $token = $this->jwtencode(json_encode($payload));
        $arr = [
            'async'=>false,
            'url'=>$url,
            'outputtype'=>$outputtype,
            'filetype'=>$filetype,
            'title'=>$title,
            'key'=>$key,                 
        ];

        // add header token
        $headerToken = "";
        $jwtHeader = Configure::read('onlyofficesecret');

        $headerToken = $this->jwtEncode([ "payload" => $arr ]);
        $arr["token"] = $this->jwtEncode($arr);     

        $data = json_encode($arr);
        // request parameters   
        $opts = array('http' => array(
            'method'  => 'POST',
            'timeout' => 30,
            'header'=> "Content-type: application/json\r\n" . 
            "Accept: application/json\r\n" .
            (empty($headerToken) ? "" : $jwtHeader.": Bearer $headerToken\r\n"),
            'content' => $data
        )
    );
        
        $context = stream_context_create($opts);
        $response_data = file_get_contents($path, FALSE, $context);
        $downloadUri = json_decode($response_data,true);
        $downloadUri = $downloadUri['fileUrl'];        
        if($downloadUri){
            $new_data = file_get_contents($downloadUri);    
        }        

        if ($new_data === FALSE) {
            echo "error";
        } else {
            
             $savepath = WWW_ROOT .'files' . DS . $company_id   . DS . 'processes' . DS . $record_id;
                Cakelog::write('debug',$savepath);
            if(file_exists($savepath)){

            }else{
                $folder = new Folder();
                if ($folder->create($savepath,0777)) {
                } else {
                    echo "Folder creation failed";
                    exit;
                }    
            }
            
            $new_data = str_replace('&quot;','"',$new_data);
            $file_for_save = WWW_ROOT .'files' . DS . $company_id   . DS . 'processes' . DS . $record_id . DS .  'template.'.$outputtype;
            if (file_put_contents($file_for_save, $new_data)) {                             
                return $new_data;
            } else {
                
            }
        }
        
    }

    public function add() {
        if ($this->_show_approvals()) {
            $this->set(array('showApprovals' => $this->_show_approvals()));
        }
        if ($this->request->is('post')) {
            
            foreach ($this->request->data['Process'] as $key => $value) {
                if (is_array($value) && $key != 'file') {
                    $this->request->data['Process'][$key] = json_encode($value);
                }
            }
            
            if($this->request->data['Approval']['Process']['publish'] == 1){
                $this->request->data['Process']['publish'] = 1;
                $this->request->data['Process']['approved_by'] = $this->request->data['Approval']['approved_by'];
            }else{
                $this->request->data[$this->modelClass]['publish'] = $this->request->data['Approval']['publish'];
                $this->request->data[$this->modelClass]['prepared_by'] = $this->request->data['Approval']['prepared_by'];
                $this->request->data[$this->modelClass]['approved_by'] = $this->request->data['Approval']['approved_by'];    
            }
            
            $this->request->data['Process']['system_table_id'] = $this->_get_system_table_id();
            $this->Process->create();



            if ($this->Process->save($this->request->data)) {
                // get hasMany
                if ($this->request->data['Process']['file']) $this->_step1($this->request->data, $this->Process->id);
                $hasManies = $this->Process->hasMany;
                foreach ($hasManies as $model => $fields) {
                    if ($this->request->data[$model]) {
                        $this->loadModel($model);
                        foreach ($this->request->data[$model] as $cdata) {
                            $this->$model->create();
                            $cdata['parent_id'] = $this->Process->id;
                            $cdata['created'] = date('Y-m-d H:i:s');
                            $cdata['modified'] = date('Y-m-d H:i:s');
                            $cdata['prepared_by'] = $this->Session->read('User.employee_id');
                            $cdata['created_by'] = $this->Session->read('User.id');
                            $cdata['branchid'] = $this->Session->read('User.branch_id');
                            $cdata['departmentid'] = $this->Session->read('User.department_id');
                            $cdata['qc_document_id'] = $this->request->data['Process']['qc_document_id'];
                            try {
                                $this->$model->save($cdata, false);
                            }
                            catch(Exception $e) {
                                // do nothing;
                                
                            }
                        }
                    }
                }
                if ($this->_show_approvals()) $this->_save_approvals($this->Process->id);
                $this->Session->setFlash(__('Record has been saved'));
                if ($this->_show_evidence() == true) $this->redirect(array('action' => 'view', $this->Process->id));
                $this->redirect(array('action' => 'index', 'custom_table_id' => $this->request->params['named']['custom_table_id'], 'qc_document_id' => $this->request->params['named']['qc_document_id']));
            } else {
                $this->Session->setFlash(__('Record could not be saved. Please, try again.'));
            }
        }
        $this->_commons($this->Session->read('User.id'));
    }
    public function _step1($data = null, $id = null) {
        $full_name = explode('.', $data['Process']['file']['name']);
        $file_type = $full_name[1];
        $file_name = $data['Process']['file']['name'];
        $file = Configure::read('path') . DS . $id . DS . $file_name;
        if (!file_exists(Configure::read('path') . DS . $id)) {
            $pathfolder = new Folder();
            if ($pathfolder->create(Configure::read('path') . DS . $id)) {
                chmod(Configure::read('path') . DS . $id,0777);
            } else {
                echo "failed folder" . Configure::read('path');
                $this->Session->setFlash(__('Folder creation failed. Please, try again.'));
            }
        }
        if (move_uploaded_file($data['Process']['file']['tmp_name'], $file)) {
            // copy($from,$file);
            $key = $this->_generate_onlyoffice_key($this->data['Process']['id'] . date('Ymdhis'));
            $this->set('file_key', $key);
            $process = $this->Process->find('first', array('recursive' => - 1, 'conditions' => array('Process.id' => $id)));
            $process['Process']['file_key'] = $key;
            $process['Process']['file_key'] = $key;
            $process['Process']['file_name'] = $file_name;
            $this->Process->create();
            $this->Process->save($process['Process']);
        } else {
            echo "File move failed!";
        }
        if ($this->data['Process']['file_key']) {
        } else {
            $key = $this->_generate_onlyoffice_key($this->data['Process']['id'] . date('Ymdhis'));
            $this->data['Process']['file_key'] = $key;
            $this->set('file_key', $key);
            $process = $this->Process->find('first', array('recursive' => - 1, 'conditions' => array('Process.id' => $id)));
            $process['Process']['file_key'] = $key;
            $process['Process']['file_key'] = $key;
            $process['Process']['file_name'] = $file_name;
            $this->Process->create();
            $this->Process->save($process['Process']);
        }
    }
    public function edit($id = null) {
        if (!$this->Process->exists($id)) {
            throw new NotFoundException(__('Invalid Process'));
        }        

        if ($this->_show_approvals()) {
            $this->set(array('showApprovals' => $this->_show_approvals()));
        }
        if (($this->request->is('post') || $this->request->is('put')) && !empty($this->request->data['Process'])) {
            $this->request->data[$this->modelClass]['publish'] = $this->request->data['Approval']['publish'];
            $this->request->data['Process']['system_table_id'] = $this->_get_system_table_id();
            foreach ($this->request->data['Process'] as $key => $value) {
                if (is_array($value) && $key != 'file') {
                    $this->request->data['Process'][$key] = json_encode($value);
                }
            }


            if($this->request->data['Approval']['Process']['publish'] == 1){
                $this->request->data['Process']['publish'] = 1;
                $this->request->data['Process']['approved_by'] = $this->request->data['Approval']['approved_by'];
            }

            if ($this->Process->save($this->request->data)) {
                if ($this->request->data['Process']['file']) $this->_step1($this->request->data, $this->request->data['Process']['id']);
                // get hasMany
                $hasManies = $this->Process->hasMany;
                foreach ($hasManies as $model => $fields) {
                    if ($this->request->data[$model]) {
                        $this->loadModel($model);
                        foreach ($this->request->data[$model] as $cdata) {
                            foreach ($cdata as $ckey => $c) {
                                if (is_array($c)) {
                                    $c[$key] = json_encode($c);
                                }
                            }
                            $this->$model->create();
                            $cdata['parent_id'] = $this->Process->id;
                            $cdata['created'] = date('Y-m-d H:i:s');
                            $cdata['modified'] = date('Y-m-d H:i:s');
                            $cdata['created_by'] = $this->Session->read('User.id');
                            $cdata['branchid'] = $this->Session->read('User.branch_id');
                            $cdata['departmentid'] = $this->Session->read('User.department_id');
                            $cdata['qc_document_id'] = $this->request->data['Process']['qc_document_id'];
                            try {
                                $this->$model->save($cdata, false);
                            }
                            catch(Exception $e) {
                                // do nothing;
                                
                            }
                        }
                    }
                }
                if ($this->_show_approvals()) $this->_save_approvals($this->request->data['Process']['id']);
                $this->redirect(array('action' => 'index', 'process_id' => $this->request->params['named']['process_id'], 'custom_table_id' => $this->request->params['named']['custom_table_id']));
            } else {
                $this->Session->setFlash(__('Record could not be saved. Please, try again.'));
            }
        } else {
            $options = array('conditions' => array('Process.' . $this->Process->primaryKey => $id));
            $this->request->data = $this->Process->find('first', $options);

            if($this->Session->read('User.is_mr') == false && $this->request->data['Process']['created_by'] != $this->Session->read('User.id')){
                $this->Session->setFlash(__('You can not edit this process.'));
                $this->redirect(array('action' => 'index', 'process_id' => $this->request->params['named']['process_id'], 'custom_table_id' => $this->request->params['named']['custom_table_id']));
            }
        }
        $this->_commons($this->Session->read('User.id'));
    }

    public function _fetch_images($str = null){
        $tag_close = '"/>';
        foreach (explode('<img', $str) as $key => $value) {
           if(strpos($value, $tag_close) !== FALSE){
                $images[] = '<img'. substr($value, 0, strpos($value, $tag_close)).'"/>';
           }
       }
        return $images;
    }

    public function _process_loop($process = null){

        $input_processes = $this->Process->find('list',array('recursive'=>-1, 'conditions'=>array('Process.id'=>json_decode($process['Process']['input_processes'],true))));
        $output_processes = $this->Process->find('list',array('recursive'=>-1, 'conditions'=>array('Process.id'=>json_decode($process['Process']['output_processes'],true))));
        return array($input_processes,$output_processes);
    }

    public function load_process($id = null) {
        if (!$this->Process->exists($id)) {
            throw new NotFoundException(__('Invalid qc document category'));
        }
        $options = array('conditions' => array('Process.' . $this->Process->primaryKey => $id));
        $process = $this->Process->find('first', $options);
        $this->set('process', $this->Process->find('first', $options));

        $key = $process['QcDocument']['file_key'];
        $file_type = $process['QcDocument']['file_type'];
        $file_name = $process['QcDocument']['title'];
        $document_number = $process['QcDocument']['document_number'];
        $document_version = $process['QcDocument']['revision_number'];

        $file_type = $process['QcDocument']['file_type'];
        
        if($file_type == 'doc' || $file_type == 'docx'){
            $documentType = 'word';
        }

        if($file_type == 'xls' || $file_type == 'xlsx'){
            $documentType = 'cell';
        }

        $mode = 'view';

        $file_path = $process['QcDocument']['id'];

        $file_path = $process['QcDocument']['id'];
        // $file = $document_number.'-'.$file_name.'-'.$document_version.'.'.$file_type;
        $file = $document_number.'-'.$file_name.'-'.$document_version;
        $file = ltrim(rtrim($file));
        $file = str_replace('-', '_', $file);
        $file = ltrim(rtrim(strtolower($file)));
        $file = preg_replace('/[\@\.\;\" "-]+/', '_', $file);
        $file = preg_replace('/  */', '_', $file);
        $file = preg_replace('/\\s+/', '_', $file);        
        $file = preg_replace('/-*-/', '_', $file);
        $file = preg_replace('/_*_/', '_', $file);
        $file = $this->_clean_table_names($file);
        $file = $file.'.'.$file_type;
        $path = Router::url('/',true) . '/files/' . $this->Session->read('User.company_id') . '/qc_documents/' . $file_path . '/' . $file;

        $htmlFile = WWW_ROOT .'files' . DS . $this->Session->read('User.company_id')  . DS . 'processes' . DS . $id . DS .  'template.html';
        if(file_exists($htmlFile)){
            $file = new File($htmlFile);
            $html = $file->read();
            $file->close();
            $this->set('html',$html);    
        }else{
            $html = $this->_convert_to_html($path,$file_type,'html',null,$process['QcDocument']['title'],$id,$this->Session->read('User.company_id'));
            $this->set('html',$html);    
        }
        
        $images = $this->_fetch_images($html);
        $this->set('images',$images);

        $this->_commons($this->Session->read('User.id'));
        $linkedTables = $this->Process->CustomTable->find('list', array('recursive' => - 1, 'conditions' => array('CustomTable.process_id' => $Process['Process']['id'])));
        $this->set('linkedTables', $linkedTables);
        $process_loop = $this->_process_loop($process);
        $this->set('ips', $process_loop[0]);
        $this->set('ops', $process_loop[1]);

    }

    public function get_document_details($qc_document_id = null){
        $this->autoRender = false;
        $qcDocument = $this->Process->QcDocument->find('first',array(
            'fields'=>array('QcDocument.id','QcDocument.title','QcDocument.branches','QcDocument.departments','QcDocument.standard_id','QcDocument.clause_id','QcDocument.schedule_id'),
            'recursive'=>-1,
            'conditions'=>array('QcDocument.id'=>$qc_document_id)));

        if($qcDocument){
            return json_encode($qcDocument['QcDocument']);
        }else{
            return false;
        }

    }
}
