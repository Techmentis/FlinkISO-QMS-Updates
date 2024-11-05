<?php
App::uses('AppController', 'Controller');
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');
/**
 * QcDocuments Controller
 *
 * @property QcDocument $QcDocument
 * @property PaginatorComponent $Paginator
 */
class QcDocumentsController extends AppController {
    /**
     * Components
     *
     * @var array
     */
    public $components = array('Paginator');
    public function _get_system_table_id() {
        $this->loadModel('SystemTable');
        $this->SystemTable->recursive = - 1;
        $systemTableId = $this->SystemTable->find('first', array('conditions' => array('SystemTable.system_name' => $this->request->params['controller'])));
        return $systemTableId['SystemTable']['id'];
    }

    public function _check_document_access(){
        $sharing = $this->QcDocument->find('count',array(
            'conditions'=>array(
                'OR'=>array(
                    'QcDocument.branches LIKE' => '%'.$this->Session->read('User.branch_id').'%',
                    'QcDocument.departments LIKE' => '%'.$this->Session->read('User.department_id').'%',
                    // 'QcDocument.designations LIKE' => '%'.$this->Session->read('User.designation_id').'%',
                    'QcDocument.user_id LIKE' => '%'.$this->Session->read('User.id').'%',
                )
            )
        ));
    }

    public function _doc_access($data = null,$access = null){
        $editors = json_decode($this->request->data['QcDocument']['editors'],true);
        if($editors != null){
            if(!in_array($this->Session->read('User.id'), $editors)){
                if($access['allow_access_user'] == $this->Session->read('User.id')){
                    
                }else{
                    
                    $this->Session->setFlash(__('You do not have permission to edit this document'));
                    $this->redirect(array('controller'=>'users', 'action' => 'access_denied'));    
                }
                
            }        
        }else{
            $this->Session->setFlash(__('You do not have permission to edit this document'));
            $this->redirect(array('controller'=>'users', 'action' => 'access_denied'));    
        }        
    }

    public function _commons($creator = null) {
        $this->set('branches', $this->_get_branch_list());
        $this->set('departments', $this->_get_department_list());
        $this->set('designations', $this->_get_designation_list());
        $this->set('usernames', $this->_get_usernames());
        if ($this->action == 'view' || $this->action == 'edit') $approvals = $this->get_approvals();
        $qcDocumentCategories = $this->QcDocument->QcDocumentCategory->find('list', array('conditions' => array('QcDocumentCategory.publish' => 1, 'QcDocumentCategory.soft_delete' => 0)));

        if($this->request->data['QcDocument']['standard_id'] && $this->request->data['QcDocument']['standard_id'] != -1){
            $clauses = $this->QcDocument->Clause->find('list', array('conditions' => array('Clause.publish' => 1, 'Clause.soft_delete' => 0,'Clause.standard_id'=>$this->request->data['QcDocument']['standard_id'])));    
        }else{
            $clauses = $this->QcDocument->Clause->find('list', array('conditions' => array('Clause.publish' => 1, 'Clause.soft_delete' => 0)));    
        }
        
        $standards = $this->QcDocument->Standard->find('list', array('conditions' => array('Standard.publish' => 1, 'Standard.soft_delete' => 0)));
        $approvedBies = $preparedBies = $issuedBies = $issuingAuthorities = $this->QcDocument->IssuingAuthority->find('list', array('conditions' => array('IssuingAuthority.publish' => 1, 'IssuingAuthority.soft_delete' => 0)));
        $crs = $this->QcDocument->Cr->find('list', array('conditions' => array('Cr.publish' => 1, 'Cr.soft_delete' => 0)));
        $oldCrs = $this->QcDocument->OldCr->find('list', array('conditions' => array('OldCr.publish' => 1, 'OldCr.soft_delete' => 0)));
        $parentQcDocuments = $this->QcDocument->ParentQcDocument->find('list', array('conditions' => array('ParentQcDocument.soft_delete' => 0,'ParentQcDocument.archived'=>0)));
        $parentDocuments = $this->QcDocument->ParentDocument->find('list', array('conditions' => array('ParentDocument.soft_delete' => 0,'ParentDocument.archived'=>0)));
        $users = $this->QcDocument->User->find('list', array('conditions' => array('User.publish' => 1, 'User.soft_delete' => 0)));
        $systemTables = $this->QcDocument->SystemTable->find('list', array('conditions' => array('SystemTable.publish' => 1, 'SystemTable.soft_delete' => 0)));
        // $userSessions = $this->QcDocument->UserSession->find('list',array('conditions'=>array('UserSession.publish'=>1,'UserSession.soft_delete'=>0)));
        $companies = $this->QcDocument->Company->find('list', array('conditions' => array('Company.publish' => 1, 'Company.soft_delete' => 0)));
        $schedules = $this->QcDocument->Schedule->find('list', array('conditions' => array('Schedule.publish' => 1, 'Schedule.soft_delete' => 0)));
        $createdBies = $this->_get_user_list();
        $this->set(compact('qcDocumentCategories', 'clauses', 'standards', 'issuingAuthorities', 'crs', 'oldCrs', 'parentQcDocuments', 'parentDocuments', 'users', 'systemTables', 'userSessions', 'companies', 'preparedBies', 'approvedBies', 'issuedBies', 'schedules', 'approvals','createdBies'));
        $count = $this->QcDocument->find('count');
        $published = $this->QcDocument->find('count', array('conditions' => array('QcDocument.publish' => 1)));
        $unpublished = $this->QcDocument->find('count', array('conditions' => array('QcDocument.publish' => 0)));
        $this->set(compact('count', 'published', 'unpublished'));
        $this->set('customArray', $this->QcDocument->customArray);

        // add creator
        if ($this->request->params['named']['approval_id']) {
            $this->get_approval($this->request->params['named']['approval_id'], $creator);
            $this->_get_approval_comnments($this->request->params['named']['approval_id'], $creator);
        }        

    }
    /**
     * index method
     *
     * @return void
     */
    public function index() {
        $this->QcDocument->virtualFields = array(
            'intdocunumber' => 'CAST(QcDocument.document_number as UNSIGNED)',
            'parent_id'=>'QcDocument.parent_document_id',
            'tables' => 'select count(*) from `custom_tables` where `custom_tables`.`qc_document_id` LIKE QcDocument.id', 
            'active_tables' => 'select count(*) from `custom_tables` where `custom_tables`.`publish` = 1 AND `custom_tables`.`table_locked` = 0 AND `custom_tables`.`qc_document_id` LIKE QcDocument.id',
            'childDoc'=>'select count(*) from qc_documents where qc_documents.parent_document_id LIKE QcDocument.id');
        

        $conditions = $this->_check_request();

        
        if($this->Session->read('User.is_mr') == false){
            $accessConditions = array(

                'OR'=>array(
                    'QcDocument.prepared_by'=>$this->Session->read('User.employee_id'),
                    'QcDocument.branches LIKE' => '%'.$this->Session->read('User.branch_id').'%',
                    'QcDocument.departments LIKE' => '%'.$this->Session->read('User.department_id').'%',
                    // 'QcDocument.designations LIKE' => '%'.$this->Session->read('User.designation_id').'%',
                    'QcDocument.user_id LIKE' => '%'.$this->Session->read('User.id').'%',
                )
            );
        }else{
            $accessConditions = array();
        }

        $this->paginate = array('all', 
            'fields'=>array(
                'QcDocument.name','QcDocument.title','QcDocument.document_number','QcDocument.revision_number', 'QcDocument.document_status','QcDocument.publish','QcDocument.parent_document_id','QcDocument.parent_id','QcDocument.prepared_by','QcDocument.approved_by','QcDocument.tables','QcDocument.active_tables','QcDocument.standard_id','QcDocument.file_type','QcDocument.parent_document_id','QcDocument.childDoc',
                'PreparedBy.id',
                'PreparedBy.name',
                'ApprovedBy.id',
                'ApprovedBy.name',
                'IssuedBy.id',
                'IssuedBy.name',
                'Standard.id',
                'Standard.name'
            ), 
            'order' => array('QcDocument.intdocunumber' => 'ASC','QcDocument.title'=>'ASC'), 
            'conditions' => array(
                'QcDocument.archived !='=>1, 
                'QcDocument.parent_document_id '=>-1, 
                $accessConditions
            ));
        $this->QcDocument->recursive = 0;

        $this->set('qcDocuments', $this->paginate());
        $this->_get_count();
        $this->_commons($this->Session->read('User.id'));
    }

    public function child_docs($id = null) {        
        $this->QcDocument->virtualFields = array(
            'intdocunumber' => 'CAST(QcDocument.document_number as UNSIGNED)',
            'parent_id'=>'QcDocument.parent_document_id',
            'tables' => 'select count(*) from `custom_tables` where `custom_tables`.`qc_document_id` LIKE QcDocument.id', 
            'active_tables' => 'select count(*) from `custom_tables` where `custom_tables`.`publish` = 1 AND `custom_tables`.`table_locked` = 0 AND `custom_tables`.`qc_document_id` LIKE QcDocument.id',
            'childDoc'=>'select count(*) from qc_documents where qc_documents.parent_document_id LIKE QcDocument.id');
        

        $conditions = $this->_check_request();

        
        if($this->Session->read('User.is_mr') == false){
            $accessConditions = array(

                'OR'=>array(
                    'QcDocument.prepared_by'=>$this->Session->read('User.employee_id'),
                    'QcDocument.branches LIKE' => '%'.$this->Session->read('User.branch_id').'%',
                    'QcDocument.departments LIKE' => '%'.$this->Session->read('User.department_id').'%',
                    // 'QcDocument.designations LIKE' => '%'.$this->Session->read('User.designation_id').'%',
                    'QcDocument.user_id LIKE' => '%'.$this->Session->read('User.id').'%',
                )
            );
        }else{
            $accessConditions = array();
        }

        $childDocArray = array();
        
        $qcDocuments = $this->QcDocument->find('all', array(
            'fields'=>array(
                'QcDocument.name','QcDocument.title','QcDocument.document_number','QcDocument.revision_number','QcDocument.document_status','QcDocument.publish','QcDocument.parent_document_id','QcDocument.parent_id','QcDocument.prepared_by','QcDocument.approved_by','QcDocument.tables','QcDocument.active_tables','QcDocument.standard_id','QcDocument.file_type','QcDocument.parent_document_id','QcDocument.childDoc',
                'PreparedBy.id',
                'PreparedBy.name',
                'ApprovedBy.id',
                'ApprovedBy.name',
                'IssuedBy.id',
                'IssuedBy.name',
                'Standard.id',
                'Standard.name'
            ), 
            'recursive'=>0,
            'order' => array('QcDocument.intdocunumber' => 'ASC','QcDocument.title'=>'ASC'), 
            'conditions' => array(
                'QcDocument.archived !='=>1, 
                'QcDocument.parent_document_id'=>$id, 
                $accessConditions
            )
            )
        );        
        $this->set('qcDocuments',$qcDocuments);
        $this->_commons($this->Session->read('User.id'));
    }
    /**
     * view method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function view($id = null) {
        if (!$this->QcDocument->exists($id)) {
            throw new NotFoundException(__('Invalid qc document'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            $this->Session->setFlash(__('Approval Saved'));
            if ($this->request->data['Approval']['user_id']) $this->_save_approvals();
            if ($this->request->data['ApprovalComment']['user_id']) $this->_save_approval_comments();
            $this->redirect(array('action' => 'view', $id));
        }
        $options = array('conditions' => array('QcDocument.' . $this->QcDocument->primaryKey => $id));
        
        $qcDocument = $this->QcDocument->find('first', $options);
        $this->set('qcDocument', $qcDocument);
        $this->_commons($qcDocument['QcDocument']['created_by']);
        $this->set('childDocs', $this->_get_child_docs($id));

        if($this->request->data['QcDocument']['archived'] == 1){
            $this->Session->setFlash(__('You can not edit this document, its archived.'));
            $this->redirect(array('action' => 'view', $id,'timestamp'=>date('ymdhis')));
        }

        // redirect to update_revision of document_status == 6
        if($qcDocument['QcDocument']['document_status'] == 6 && $qcDocument['QcDocument']['archived'] == 0){
            $this->redirect(array('action' => 'update_revision', $id));   
        }


        // get CR record count
        $this->set('crs',$this->change_history($id));
    }

    public function view_archived($id = null) {
        if (!$this->QcDocument->exists($id)) {
            throw new NotFoundException(__('Invalid qc document'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {         
            $this->Session->setFlash(__('Approval Saved'));
            if ($this->request->data['Approval']['user_id']) $this->_save_approvals();
            if ($this->request->data['ApprovalComment']['user_id']) $this->_save_approval_comments();
            $this->redirect(array('action' => 'view', $id));
        }
        $options = array('conditions' => array('QcDocument.' . $this->QcDocument->primaryKey => $id));
        
        $qcDocument = $this->QcDocument->find('first', $options);

        $qcDocument['QcDocument']['file_key'] = $this->_generate_onlyoffice_key($this->request->data['QcDocument']['id'] . date('Ymdhis'));
        $this->QcDocument->create();
        $this->QcDocument->save($qcDocument,false);        

        $this->set('qcDocument', $qcDocument);
        $this->_commons($qcDocument['QcDocument']['created_by']);
        $this->set('childDocs', $this->_get_child_docs($id));

    }
    /**
     * view method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function mini_view($id = null) {
        if (!$this->QcDocument->exists($id)) {
            throw new NotFoundException(__('Invalid qc document'));
        }
        $options = array('conditions' => array('QcDocument.' . $this->QcDocument->primaryKey => $id));
        $qcDocument = $this->QcDocument->find('first', $options);
        $this->set('qcDocument', $qcDocument);
        $this->set('document', $qcDocument);
        $this->_commons($qcDocument['QcDocument']['created_by']);
        // $this->set('childDocs',$this->_get_child_docs($id));
        // $this->_qc_document_header($qcDocument['QcDocument']['qc_document_id']);
        
    }
    public function _get_child_docs($id = null) {
        $childDocs = $this->QcDocument->find('all', array('recursive' => 0, 'conditions' => array('QcDocument.parent_document_id' => $id)));
        return $childDocs;
    }
    /**
     * list method
     *
     * @return void
     */
    public function lists() {
        $this->_get_count();
    }
    /**
     * add method
     *
     * @return void
     */
    public function add() {

        if ( ! is_writable(dirname(WWW_ROOT . DS . 'files' . DS . $this->Session->read('User.company_id')))) {
            $this->Session->setFlash(__('Files directory is not writable. Make sure to add 0777 permission (recursive) to "'.WWW_ROOT . DS . 'files" folder'));
            $this->redirect(array('action' => 'index'));
        }

        if(file_exists(Configure::read('path'))){
            
        }else{
            $newFolder = New Folder();
            $newFolder->create(Configure::read('path'));
        }
        
        chmod(Configure::read('path'),0777);

        if ($this->request->is('post') || $this->request->is('put')) {
            
            $accessptedExtentions = array(
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.oasis.opendocument.spreadsheet',
                'application/vnd.oasis.opendocument.text',
                'application/rtf',
                'text/plain',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/pdf'

            );

            if($this->request->data['QcDocument']['file']['type']){
                if(!in_array($this->request->data['QcDocument']['file']['type'], $accessptedExtentions)){
                    $this->Session->setFlash(__('Incorrect File Type'));
                    $this->redirect(array('action' => 'add'));
                }
            }

            if(!$this->request->data['QcDocument']['file']['name']){
                unset($this->request->data['QcDocument']['file']);
            }

            $this->request->data['QcDocument']['system_table_id'] = $this->_get_system_table_id();
            $this->QcDocument->create();
            $this->request->data['QcDocument']['title'] = $this->_clean_table_names(ltrim(rtrim($this->request->data['QcDocument']['title'])));
            $this->request->data['QcDocument']['document_number'] = ltrim(rtrim($this->request->data['QcDocument']['document_number']));
            $this->request->data['QcDocument']['revision_number'] = ltrim(rtrim($this->request->data['QcDocument']['revision_number']));
            $this->request->data['QcDocument']['branches'] = json_encode($this->request->data['QcDocument']['branches']);
            $this->request->data['QcDocument']['departments'] = json_encode($this->request->data['QcDocument']['departments']);
            $this->request->data['QcDocument']['designations'] = json_encode($this->request->data['QcDocument']['designations']);
            $this->request->data['QcDocument']['user_id'] = json_encode($this->request->data['QcDocument']['user_id']);
            $this->request->data['QcDocument']['editors'] = json_encode($this->request->data['QcDocument']['editors']);
            $this->request->data['QcDocument']['linked_document_ids'] = json_encode($this->request->data['QcDocument']['linked_document_ids']);
            $this->request->data['QcDocument']['additional_clauses'] = json_encode($this->request->data['QcDocument']['additional_clauses']);
            // $this->request->data[$this->modelClass]['publish'] = $this->request->data['Approval']['publish'];
            $this->request->data['QcDocument']['version'] = 1;

            if($this->request->data['QcDocument']['select_all_branches'] == 1){
                $this->request->data['QcDocument']['branches'] = json_encode(array_keys($this->_get_branch_list()));
            }
            if($this->request->data['QcDocument']['select_all_departments'] == 1){
                $this->request->data['QcDocument']['departments'] = json_encode(array_keys($this->_get_department_list()));
            }
            if($this->request->data['QcDocument']['select_all_designations'] == 1){
                $this->request->data['QcDocument']['designations'] = json_encode(array_keys($this->_get_designation_list()));
            }
            if($this->request->data['QcDocument']['select_all_users'] == 1){
                $this->request->data['QcDocument']['user_id'] = json_encode(array_keys($this->_get_user_list()));
            }

            if($this->request->data['Approval']['QcDocument']['publish'] == 1){
                $this->request->data['QcDocument']['publish'] = 1;
                $this->request->data['QcDocument']['approved_by'] = $this->request->data['Approval']['QcDocument']['approved_by'];
            }            

            if ($this->QcDocument->save($this->request->data)) {
                if (!empty($this->request->data['QcDocument']['file']['name'])) $this->_step1($this->request->data, $this->QcDocument->id);
                else{                    
                    // check if the tmp file is available
                    // if yes copy that file here
                    if(!empty($this->request->data['QcDocument']['template_id'])){
                        // load template file details
                        $this->loadModel('Template');
                        $templateFile = $this->Template->find('first',array('recursive'=>-1, 'fields'=>array('Template.id','Template.name','Template.file_type'), 'conditions'=>array('Template.id'=>$this->request->data['QcDocument']['template_id'])));

                        if($templateFile){
                            $file_type = $templateFile['Template']['file_type'];
                            $file_name = $this->_clean_table_names($templateFile['Template']['name']);
                            $from = WWW_ROOT . DS . 'files' . DS . $this->Session->read('User.company_id') . DS . 'templates' .DS . $templateFile['Template']['id'] . DS . $file_name. '.'.$file_type ;


                        }else{
                            $this->Session->setFlash(__('Unable to add template.'));
                            $this->redirect(array('action' => 'add'));
                        }

                     
                        $file_name = $this->request->data['QcDocument']['title'];
                        $document_number = $this->request->data['QcDocument']['document_number'];
                        $document_version = $this->request->data['QcDocument']['revision_number'];
                        $file_name = $document_number . '-' . $file_name . '-' . $document_version;
                        $file_name = $this->_clean_table_names($file_name);
                        $file_name = $file_name. '.' . $file_type;

                        $id = $this->QcDocument->id;
                        $file = Configure::read('path') . DS . $id . DS . $file_name;

                        $to = Configure::read('path') . DS . $id;
                        $history_file_for_save = $file;

                        if(!file_exists($to)){
                            $testfolder = new Folder($to);
                            $testfolder->create($to);
                            chmod($to, 0777);
                        }

                        $to = $file;
                        $fromFile = new File($from);
                        $fromFile->copy($to,true);
                        if(copy($from,$to)){
                            $key = $this->_generate_onlyoffice_key($this->request->data['QcDocument']['id'] . date('Ymdhis'));
                            $this->set('file_key', $key);
                            $qcdoc = $this->QcDocument->find('first', array('recursive' => - 1, 'conditions' => array('QcDocument.id' => $id)));
                            $qcdoc['QcDocument']['file_key'] = $key;
                            $qcdoc['QcDocument']['file_type'] = $file_type;
                            $qcdoc['QcDocument']['file_key'] = $key;
                            $this->QcDocument->create();
                            $this->QcDocument->save($qcdoc['QcDocument']);

                            $json = [
                                "created" => date("Y-m-d H:i:s"),
                                'uid'=>$this->Session->read('User.id'),
                                'name'=>$this->Session->read('User.name'),
                            ];

                            $version = 1;
                            $history_file_for_save = $history_file_for_save.'-hist' . DS . $version;
                            $historyFolder = new Folder($history_file_for_save);
                            $historyFolder->create($history_file_for_save);
                            chmod($history_file_for_save, 0777);
                            file_put_contents($history_file_for_save . DS . "createdInfo.json", json_encode($json, JSON_PRETTY_PRINT));

                        }else{
                            $this->Session->setFlash(__('Unable to copy template.'));
                            $this->redirect(array('action' => 'add'));
                        }

                    }
                }
                if($this->request->data['QcDocument']['document_type'] == 2)$this->_add_process($this->request->data,$this->QcDocument->id);

                $this->Session->setFlash(__('The qc document has been saved'));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The qc document could not be saved. Please, try again.'));
            }
        }
        $this->_commons();
        if (isset($this->request->params['named']['parent_id'])) {
            $document = $this->QcDocument->find('first', array('recursive' => 0, 'conditions' => array('QcDocument.id' => $this->request->params['named']['parent_id'])));
            if ($document) {
                $this->set('document', $document);
            }
        }
        $this->loadModel('Template');
        $templates = $this->Template->find('all',array('recursive'=>-1, 'conditions'=>array('Template.model'=>'Template')));
        $this->set('templates',$templates);

        $document_number = $this->_get_last_number();
        $this->set('document_number',$document_number);
    }
    

    public function _step1($data = null, $id = null) {
        $full_name = explode('.', $data['QcDocument']['file']['name']);
        $file_type = $data['QcDocument']['file_type'];
        $file_name = $data['QcDocument']['title'];
        $document_number = $data['QcDocument']['document_number'];
        $document_version = (int)$data['QcDocument']['revision_number'];
        
        $title = $file_name;
        
        $file_name = $document_number . '-' . $file_name . '-' . $document_version;
        $file_name = $this->_clean_table_names($file_name);
        
        $file_name = $file_name . '.' . $file_type;     
        
        $history_file_for_save = $file = Configure::read('path') . DS . $id . DS . $file_name;
        if (!file_exists(Configure::read('path') . DS . $id)) {
            $pathfolder = new Folder();
            if ($pathfolder->create(Configure::read('path') . DS . $id)) {
                chmod(Configure::read('path') . DS . $id,0777);
            } else {
                echo "failed folder" . Configure::read('path');
            }
        }
        if (move_uploaded_file($data['QcDocument']['file']['tmp_name'], $file)) {            
            $key = $this->_generate_onlyoffice_key($this->data['QcDocument']['id'] . date('Ymdhis'));
            $this->set('file_key', $key);
            $qcdoc = $this->QcDocument->find('first', array('recursive' => - 1, 'conditions' => array('QcDocument.id' => $id)));
            $qcdoc['QcDocument']['title'] = $title;
            $qcdoc['QcDocument']['file_key'] = $key;
            $data['QcDocument']['file_key'] = $key;
            $data['QcDocument']['file_status'] = 0;
            $data['QcDocument']['version'] = 1;
            $data['QcDocument']['versions'] = json_encode(array());
            $this->QcDocument->create();
            $this->QcDocument->save($qcdoc['QcDocument']);

            $json = [
                "created" => date("Y-m-d H:i:s"),
                'uid'=>$this->Session->read('User.id'),
                'name'=>$this->Session->read('User.name'),
            ];

            $version = 1;
            $history_file_for_save = $history_file_for_save.'-hist' . DS . $version;
            $historyFolder = new Folder($history_file_for_save);
            $historyFolder->create($history_file_for_save);
            chmod($history_file_for_save, 0777);
            file_put_contents($history_file_for_save . DS . "createdInfo.json", json_encode($json, JSON_PRETTY_PRINT));
        } else {
            echo "File move failed!";
        }
        if ($this->data['QcDocument']['file_key']) {
        } else {
            $key = $this->_generate_onlyoffice_key($this->data['QcDocument']['id'] . date('Ymdhis'));
            $this->data['QcDocument']['file_key'] = $key;
            $this->set('file_key', $key);
            $qcdoc = $this->QcDocument->find('first', array('recursive' => - 1, 'conditions' => array('QcDocument.id' => $id)));
            $qcdoc['QcDocument']['title'] = $title;
            $data['QcDocument']['file_key'] = $key;
            $qcdoc['QcDocument']['file_key'] = $key;
            $data['QcDocument']['file_status'] = 0;
            $this->QcDocument->create();
            $this->QcDocument->save($qcdoc['QcDocument']);

            $json = [
                "created" => date("Y-m-d H:i:s"),
                'uid'=>$this->Session->read('User.id'),
                'name'=>$this->Session->read('User.name'),
            ];

            $version = 1;
            $history_file_for_save = $history_file_for_save.'-hist' . DS . $version;
            $historyFolder = new Folder($history_file_for_save);
            $historyFolder->create($history_file_for_save);
            chmod($history_file_for_save, 0777);
            file_put_contents($history_file_for_save . DS . "createdInfo.json", json_encode($json, JSON_PRETTY_PRINT));
        }
    }


    public function upload_qc_document(){
        if ($this->request->is('post') || $this->request->is('put')) {

            $accessptedExtentions = array(
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.oasis.opendocument.spreadsheet',
                'application/vnd.oasis.opendocument.text',
                'application/rtf',
                'text/plain',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/pdf'
            );

            if($this->request->data['QcDocument']['file']['type']){
                if(!in_array($this->request->data['QcDocument']['file']['type'], $accessptedExtentions)){
                    $this->Session->setFlash(__('Incorrect File Type'));
                    $this->redirect(array('action' => 'view',$this->request->data['QcDocument']['id']));
                }
            }            
            
            $full_name = explode('.', $this->request->data['QcDocument']['file']['name']);

            $file_type = $this->request->data['QcDocument']['file_type'];
            
            //get qc doc
            $qcDocument = $this->QcDocument->find('first',array(
                'recursive'=>-1,
                'fields'=>array('QcDocument.id','QcDocument.document_number','QcDocument.revision_number','QcDocument.title'),
                'conditions'=>array('QcDocument.id'=>$this->request->data['QcDocument']['id'])));

            if(!$qcDocument){
                $this->Session->setFlash(__('Incorrrect qc document'));
                $this->redirect(array('action' => 'view',$this->request->data['QcDocument']['id']));
            }

            $file_name = $qcDocument['QcDocument']['title'];
            $document_number = $qcDocument['QcDocument']['document_number'];
            $document_version = $qcDocument['QcDocument']['revision_number'];
            $file_name = $document_number . '-' . $qcDocument['QcDocument']['title'] . '-' . $document_version;
            $file_name = $file_name_for_folder = $this->_clean_table_names($file_name);
            $file_name = $file_name. '.' . $file_type;
            
            $id = $this->request->data['QcDocument']['id'];            

            $file = Configure::read('path') . DS . $id . DS . $file_name;

            if (!file_exists(Configure::read('path') . DS . $id)) {
                $pathfolder = new Folder();
                if ($pathfolder->create(Configure::read('path') . DS . $id,false,0777)) {                    
                } else {

                    $this->Session->setFlash(__('failed folder' . Configure::read('path')));
                    $this->redirect(array('action' => 'view',$this->request->data['QcDocument']['id']));                
                }
            }else{
                
                $path = Configure::read('path') . DS . $id ;
                $dir = new Folder();
                $dir->create($path);
                chmod($path, 0777);
                
                // if(!$dir){
                //     $this->Session->setFlash(__('Failed folder creation' . Configure::read('path')));
                //     $this->redirect(array('action' => 'view',$this->request->data['QcDocument']['id']));                   
                // }
            }

            $folder_new_file = New Folder();
            $folder_new_file->create(Configure::read('path') . DS . $id . DS . $file_name_for_folder, false, 0777);

            if (move_uploaded_file($this->request->data['QcDocument']['file']['tmp_name'], $file)) { 
                $key = $this->_generate_onlyoffice_key($this->request->data['QcDocument']['id'] . date('Ymdhis'));
                $this->set('file_key', $key);
                $qcdoc = $this->QcDocument->find('first', array('recursive' => - 1, 'conditions' => array('QcDocument.id' => $id)));
                $qcdoc['QcDocument']['file_key'] = $key;
                $qcod['QcDocument']['file_status'] = 0;
                $qcdoc['QcDocument']['file_type'] = $file_type;
                $this->request->data['QcDocument']['file_key'] = $key;
                $this->QcDocument->create();
                $this->QcDocument->save($qcdoc['QcDocument']);
                $this->Session->setFlash(__('File Uploaded'));

                $json = [
                    "created" => date("Y-m-d H:i:s"),
                    'uid'=>$this->Session->read('User.id'),
                    'name'=>$this->Session->read('User.name'),
                ];

                $version = 1;
                $history_file_for_save = $history_file_for_save.'-hist' . DS . $version;
                $historyFolder = new Folder($history_file_for_save);
                $historyFolder->create($history_file_for_save);
                chmod($history_file_for_save, 0777);
                file_put_contents($history_file_for_save . DS . "createdInfo.json", json_encode($json, JSON_PRETTY_PRINT));


            // now copy new file to custom tables

                $customTables = $this->QcDocument->CustomTable->find('all',array('recursive'=>-1,
                // 'fields'=>array('CustomTable.id','CustomTable.file_key','CustomTable.version_keys','CustomTable.file_status'),
                    'conditions'=>array('CustomTable.qc_document_id'=>$this->request->data['QcDocument']['id'])));
                if($customTables){
                    foreach($customTables as $customTable){                    
                        $this->QcDocument->CustomTable->create();
                        $key = $this->_generate_onlyoffice_key($customTable['CustomTable']['id'] . date('Ymdhis'));
                        $customTable['CustomTable']['file_key'] = $key;
                        $customTable['CustomTable']['version_keys'] = $key;
                        $this->QcDocument->CustomTable->save($customTable,false);

                    // delete old file
                        $customTableFolder = Configure::read('path') . DS . $customTable['CustomTable']['id'];
                        $customTableFolder = str_replace('qc_documents','custom_tables',Configure::read('path') . DS . $customTable['CustomTable']['id']);
                        $delFolder = new Folder($customTableFolder);
                        
                        $dir = new Folder();
                        $dir->create($customTableFolder, false, 0777);
                        $to = $customTableFolder . DS . $file_name;
                        copy($file,$to);
                    }
                }                

            } else {
                $this->Session->setFlash(__('File move failed....'));
                $this->redirect(array('action' => 'view',$this->request->data['QcDocument']['id']));
            }
            
        // if ($this->data['QcDocument']['file_key']) {
        // } else {
        //     $key = $this->_generate_onlyoffice_key($this->request->data['QcDocument']['id'] . date('Ymdhis'));
        //     $this->request->data['QcDocument']['file_key'] = $key;
        //     $this->set('file_key', $key);
        //     $qcdoc = $this->QcDocument->find('first', array('recursive' => - 1, 'conditions' => array('QcDocument.id' => $id)));
        //     $this->request->data['QcDocument']['file_key'] = $key;
        //     $qcdoc['QcDocument']['file_key'] = $key;
        //     $qcdoc['QcDocument']['file_type'] = $file_type;
        //     $qcdoc['QcDocument']['file_status'] = 0;
        //     $this->QcDocument->create();
        //     $this->QcDocument->save($qcdoc['QcDocument']);
        //     $this->Session->setFlash(__('File Uploaded'));

        //     $json = [
        //         "created" => date("Y-m-d H:i:s"),
        //         'uid'=>$this->Session->read('User.id'),
        //         'name'=>$this->Session->read('User.name'),
        //     ];

        //     $version = 1;
        //     $history_file_for_save = $history_file_for_save.'-hist' . DS . $version;
        //     $historyFolder = new Folder($history_file_for_save);
        //     $historyFolder->create($history_file_for_save);
        //     chmod($history_file_for_save, 0777);
        //     file_put_contents($history_file_for_save . DS . "createdInfo.json", json_encode($json, JSON_PRETTY_PRINT));
        // }

            $this->redirect(array('action' => 'view',$this->request->data['QcDocument']['id']));
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
        if (!$this->QcDocument->exists($id)) {
            throw new NotFoundException(__('Invalid qc document'));
        }

        if ($this->_show_approvals()) {           
            $this->set(array('showApprovals' => $this->_show_approvals()));
        }
        
        if (($this->request->is('post') || $this->request->is('put')) && !empty($this->request->data['QcDocument'])) {
            if($this->request->data['QcDocument']['document_type'] == 2)$this->_update_process($this->request->data,$this->request->data['QcDocument']['id']);
         
            $this->request->data['QcDocument']['system_table_id'] = $this->_get_system_table_id();
            $this->request->data['QcDocument']['branches'] = json_encode($this->request->data['QcDocument']['share_with_branches']);
            $this->request->data['QcDocument']['departments'] = json_encode($this->request->data['QcDocument']['share_with_departments']);
            $this->request->data['QcDocument']['designations'] = json_encode($this->request->data['QcDocument']['share_with_designations']);
            $this->request->data['QcDocument']['user_id'] = json_encode($this->request->data['QcDocument']['user_id']);
            $this->request->data['QcDocument']['editors'] = json_encode($this->request->data['QcDocument']['editors']);
            $this->request->data['QcDocument']['linked_document_ids'] = json_encode($this->request->data['QcDocument']['linked_document_ids']);
            $this->request->data['QcDocument']['additional_clauses'] = json_encode($this->request->data['QcDocument']['additional_clauses']);
            $this->request->data['QcDocument']['publish'] = $this->request->data['Approval']['QcDocument']['publish'];

            if($this->request->data['QcDocument']['select_all_branches'] == 1){
                $this->request->data['QcDocument']['branches'] = json_encode(array_keys($this->_get_branch_list()));
            }
            if($this->request->data['QcDocument']['select_all_departments'] == 1){
                $this->request->data['QcDocument']['departments'] = json_encode(array_keys($this->_get_department_list()));
            }
            if($this->request->data['QcDocument']['select_all_designations'] == 1){
                // $this->request->data['QcDocument']['designations'] = json_encode(array_keys($this->_get_designation_list()));
            }
            if($this->request->data['QcDocument']['select_all_users'] == 1){
                $this->request->data['QcDocument']['user_id'] = json_encode(array_keys($this->_get_user_list()));
            }
            
            if($this->request->data['Approval']['QcDocument']['publish'] == 1){
                $this->request->data['QcDocument']['publish'] = 1;
                $this->request->data['QcDocument']['approved_by'] = $this->request->data['Approval']['QcDocument']['approved_by'];
            }

            if ($this->QcDocument->save($this->request->data)) {

                // if ($this->_show_approvals())
                if ($this->request->data['Approval']['QcDocument']['user_id']) $this->_save_approvals($this->QcDocument->id);
                if ($this->request->data['ApprovalComment']['user_id']) $this->_save_approval_comments();
                if ($this->_show_evidence() == true) $this->redirect(array('action' => 'view', $id));
                else                
                    $this->redirect(array('action' => 'view', $id,'timestamp'=>date('ymdhis')));
            } else {
                $this->Session->setFlash(__('The qc document could not be saved. Please, try again.'));
            }
        } else {

            $access = $this->request->data['Access'];
            $options = array('conditions' => array('QcDocument.' . $this->QcDocument->primaryKey => $id));
            $this->request->data = $this->QcDocument->find('first', $options);

            if($this->request->data['QcDocument']['document_status'] == 3){
                $this->Session->setFlash(__('Can not edit this document. This document is under revision.'));
                $this->redirect(array('action' => 'view', $id,'timestamp'=>date('ymdhis')));
            }
            
            if($this->request->data['QcDocument']['document_status'] == 6){
                $this->Session->setFlash(__('You must update this document.'));
                $this->redirect(array('action' => 'update_revision', $id,'timestamp'=>date('ymdhis')));
            }

            if($this->request->data['QcDocument']['archived'] == 1){
                $this->Session->setFlash(__('You can not edit this document, its archived.'));
                $this->redirect(array('action' => 'view', $id,'timestamp'=>date('ymdhis')));
            }
            
            $file_type = $this->request->data['QcDocument']['file_type'];
            $file_name = $this->request->data['QcDocument']['title'];
            $document_number = $this->request->data['QcDocument']['document_number'];
            $document_version = $this->request->data['QcDocument']['revision_number'];
            $file_name = $document_number . '-' . $file_name . '-' . $document_version;
            $file_name = $this->_clean_table_names($file_name);
            $file_name = $file_name. '.' . $file_type;

            $file = Configure::read('path') . DS . $this->request->data['QcDocument']['id'] . DS . $file_name;
            if (!file_exists(Configure::read('path') . DS . $this->request->data['QcDocument']['id'])) {
                $pathfolder = new Folder();
                if ($pathfolder->create(Configure::read('path') . DS . $this->request->data['QcDocument']['id'])) {
                    chmod(Configure::read('path') . DS . $this->request->data['QcDocument']['id'],0777);
                } else {
                    echo "failed folder" . Configure::read('path');
                }
            }
            if (!file_exists($file)) {
                $from = WWW_ROOT . 'files/samples/sample.' . $file_type;
                if (file_exists($from)) {
                    copy($from, $file);
                    $key = $this->_generate_onlyoffice_key($this->data['QcDocument']['id'] . date('Ymdhis'));
                    $this->set('file_key', $key);
                    $qcdoc = $this->QcDocument->find('first', array('recursive' => - 1, 'conditions' => array('QcDocument.id' => $this->request->data['QcDocument']['id'])));
                    $qcdoc['QcDocument']['file_key'] = $key;
                    $qcdoc['QcDocument']['file_status'] = 0;
                    $this->request->data['QcDocument']['file_key'] = $key;
                    $this->QcDocument->create();
                    $this->QcDocument->save($qcdoc['QcDocument']);
                }
            }
            if ($this->data['QcDocument']['file_key']) {

            } else {
                $key = $this->_generate_onlyoffice_key($this->data['QcDocument']['id'] . date('Ymdhis'));
                $this->data['QcDocument']['file_key'] = $key;
                $this->set('file_key', $key);
                $qcdoc = $this->QcDocument->find('first', array('recursive' => - 1, 'conditions' => array('QcDocument.id' => $this->request->data['QcDocument']['id'])));
                $this->request->data['QcDocument']['file_key'] = $key;
                $qcdoc['QcDocument']['file_key'] = $key;
                $qcdoc['QcDocument']['file_status'] = 0;
                $this->QcDocument->create();
                $this->QcDocument->save($qcdoc['QcDocument']);
            }
        }
        $this->_commons($this->request->data['QcDocument']['created_by']);
        if($this->Session->read('User.id') != $this->request->data['QcDocument']['created_by'])$this->_doc_access($this->request->data,$access);
    }
    /**
     * approve method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function approve($id = null, $approvalId = null) {
        if (!$this->QcDocument->exists($id)) {
            throw new NotFoundException(__('Invalid qc document'));
        }
        $this->loadModel('Approval');
        if (!$this->Approval->exists($approvalId)) {
            throw new NotFoundException(__('Invalid approval id'));
        }
        $approval = $this->Approval->read(null, $approvalId);
        $this->set('same', $approval['Approval']['user_id']);
        if ($this->_show_approvals()) {
            $this->set(array('showApprovals' => $this->_show_approvals()));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->QcDocument->save($this->request->data)) {
                if (!isset($this->request->data[$this->modelClass]['publish']) && $this->request->data['Approval']['user_id'] != - 1) {
                    $this->request->data[$this->modelClass]['publish'] = 0;
                }
                if ($this->QcDocument->save($this->request->data)) {
                    $this->Session->setFlash(__('The qc document has been saved.'));
                    if ($this->_show_approvals()) $this->_save_approvals();
                } else {
                    $this->Session->setFlash(__('The qc document could not be saved. Please, try again.'));
                }
            } else {
                $this->Session->setFlash(__('The qc document could not be saved. Please, try again.'));
            }
        } else {
            $options = array('conditions' => array('QcDocument.' . $this->QcDocument->primaryKey => $id));
            $this->request->data = $this->QcDocument->find('first', $options);
        }
        $this->_commons();
    }
    /**
     * purge method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function purge($id = null) {
        $this->QcDocument->id = $id;
        if (!$this->QcDocument->exists()) {
            throw new NotFoundException(__('Invalid qc document'));
        }
        $this->request->onlyAllow('post', 'delete');
        if ($this->QcDocument->delete()) {
            $this->Session->setFlash(__('Qc document deleted'));
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('Qc document was not deleted'));
        $this->redirect(array('action' => 'index'));
    }
    /**
     * delete method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function delete($id = null, $parent_id = NULL) {
        if ($this->request->is('post')) {

            $this->loadModel('User');
            $user = $this->User->find('first', array('conditions' => array('User.status' => 1, 'User.soft_delete' => 0, 'User.publish' => 1, 'User.username' => $this->Session->read('User.username'))));
            if ($user) {
                if (trim($user['User']['password']) != trim(Security::hash($this->request->data['QcDocument']['password'], 'md5', true))) {
                    $this->Session->setFlash(__('Incorrect password', true), 'default', array('class' => 'alert-danger'));                    
                } else {

                    $doc = $this->QcDocument->find('first',array(
                        'recursive'=>-1, 
                        // 'fields'=>array('QcDocument.id','QcDocument.created_by'), 
                        'conditions'=>array('QcDocument.id'=>$this->request->data['QcDocument']['id']
                    )));
                        
                    $editors = json_decode($doc['QcDocument']['editors'],true);
                    if($editors != null){
                        if(!in_array($this->Session->read('User.id'), $editors)){
                                
                                $this->Session->setFlash(__('You do not have permission to edit this document'));
                                $this->redirect(array('action' => 'index')); 
                        }        
                    }else{
                        $this->Session->setFlash(__('You do not have permission to edit this document'));
                        $this->redirect(array('action' => 'index'));  
                    }                         

                    // find all custom tables 
                    $id = $doc['QcDocument']['id'];
                    if($doc){
                        if($this->Session->read('User.id') == $doc['QcDocument']['created_by']){
                            if($id){
                                $this->QcDocument->delete($id, true);
                                $delFolder = new Folder(Configure::read('path') . DS . $id);
                                $delFolder->delete();
                            }   

                            // delete archived documents
                            $archived = $this->QcDocument->find('all',array('conditions'=>array('QcDocument.parent_id'=>$id),'fields'=>array('QcDocument.id','QcDocument.parent_id')));
                            foreach($archived as $archive){
                                $this->QcDocument->delete($id, true);
                                $delFolder = new Folder(Configure::read('files') . DS . 'archive' . DS . $id);
                                $delFolder->delete();

                            }

                            $this->redirect(array('action' => 'index'));    
                        }else{
                            $this->Session->setFlash(__('Qc document was not deleted as you are not the creator of the document'));
                            $this->redirect(array('action' => 'index'));
                        }    
                    }else{
                        $this->Session->setFlash(__('Document does not exist'));
                        $this->redirect(array('action' => 'index'));
                    }
                }
            }
        }else{
            $this->set('id',$id);
            $this->set('parent_id',$parent_id);
            $customTables = $this->QcDocument->CustomTable->find('list',array('conditions'=>array('CustomTable.qc_document_id'=>$id)));
            $this->set('customTables',$customTables);
        }
        
                
    } 

    public function delete_document() {
        if ($this->request->is('post')) {
            $this->loadModel('User');
            $user = $this->User->find('first', array('conditions' => array('User.status' => 1, 'User.soft_delete' => 0, 'User.publish' => 1, 'User.username' => $this->Session->read('User.username'))));
            if ($user) {
                if (trim($user['User']['password']) != trim(Security::hash($this->request->data['QcDocument']['password'], 'md5', true))) {
                    $this->Session->setFlash(__('Incorrect password', true), 'default', array('class' => 'alert-danger'));                    
                } else {
                    $this->autoRender = false;
                    $file_path = base64_decode($this->request->data['QcDocument']['url']);
                    $path1 = explode(DS , $file_path);
                    $qcDocument = $this->QcDocument->find('first',array('conditions'=>array('QcDocument.id'=>$path1[count($path1)-2]),'recursive'=>-1));
                    
                    if(!in_array($this->Session->read('User.id'),json_decode($qcDocument['QcDocument']['editors'],true))){
                        $this->Session->setFlash(__('You are not authorized to delete this document'));
                        $this->redirect(array('action' => 'view',$qcDocument['QcDocument']['id'],'timestamp'=>date('Ymdhis')));
                    }
                    
                    if($qcDocument){
                        $folder = Configure::read('path') . DS . $path1[count($path1)-2];

                        if($path1){
                            $delFolder = new Folder($folder);
                            $delFolder->delete();    
                        }
                        
                        
                        $qcDocument['QcDocument']['file_key'] = '';
                        $qcDocument['QcDocument']['version'] = '';
                        $qcDocument['QcDocument']['versions'] = '';
                        $qcDocument['QcDocument']['version_keys'] = '';
                        $qcDocument['QcDocument']['file_status'] = 0;
                        $qcDocument['QcDocument']['file_type'] = '';
                        $qcDocument['QcDocument']['last_saved'] = '';
                        $this->QcDocument->create();
                        $this->QcDocument->save($qcDocument,false);
                        // must also change custome_table_document for tables linked with this document
                        $customTables = $this->QcDocument->CustomTable->find('all',array('recursive'=>-1,
                            // 'fields'=>array('CustomTable.id','CustomTable.file_key','CustomTable.version_keys','CustomTable.file_status'),
                            'conditions'=>array('CustomTable.qc_document_id'=>$qcDocument['QcDocument']['id'])));

                        if($customTables){
                            foreach($customTables as $customTable){
                                $this->QcDocument->CustomTable->create();
                                $customTable['CustomTable']['file_key'] = '';
                                $customTable['CustomTable']['version_keys'] = '';
                                $this->QcDocument->CustomTable->save($customTable,false);

                                // delete old file
                                $customTableFolder = Configure::read('path') . DS . 'custome_tables' . DS . $customTable['CustomTable']['id'];
                                if($customTable['CustomTable']['id']){
                                    $delFolder = new Folder($customTableFolder);
                                    $delFolder->delete();    
                                }
                                
                            }
                        }

                    }
                    $this->Session->setFlash(__('File Deleted', true), 'default', array('class' => 'alert-success'));
                    $this->redirect($this->request->data['QcDocument']['ref']);
                }
            }
        } else {            
            $this->set('ref', $this->referer());
        }
    }

    public function check_duplicates($field = null, $val = null, $id = null) {
        $this->autoRender = false;
        $field = $this->request->params['named']['field'];
        $val = $this->request->params['named']['value'];
        if ($field == 't') {
            $rec = $this->QcDocument->find('count', array('conditions' => array('QcDocument.title' => $val)));
            // echo $rec;
            if ($rec > 0) {
                return 1;
            } else {
                return 0;
            }
        }
        if ($field == 'n') {
            $rec = $this->QcDocument->find('count', array('conditions' => array('QcDocument.document_number' => $val)));
            // echo $rec;
            if ($rec > 0) {
                return 1;
            } else {
                return 0;
            }
        }
    }
    public function clean_document_number($value = null) {
        $this->autoRender = false;        
        return preg_replace('/[^a-zA-Z0-9]/', '-', $this->request->params->name['document_number']);
        return $value;
    } 

    public function load_blank_file($type = null){

        $filetype  = $this->request->params['named']['type'];        
        
        $to = WWW_ROOT . DS . 'files' . DS . $this->Session->read('User.company_id') . DS . 'qc_documents' .DS . 'tmp' . DS . $this->Session->read('User.id');
        if(!file_exists($to)){
            $testfolder = new Folder($to);
            $testfolder->create($to);
            chmod($to, 0777);
        }
        
        $from = WWW_ROOT . 'files' . DS . 'samples' .DS . 'sample.'.$filetype;
        
        $to = $to . DS . 'blank.'.$filetype;
        
        if(file_exists($from)){
            $fromFile = new File($from);
            $fromFile->copy($to,true);
            if(copy($from,$to)){
                $file = 'blank.'.$filetype;
                $url = Router::url('/', true) . 'files/'.$this->Session->read('User.company_id').'/qc_documents/tmp/'.$this->Session->read('User.id').'/blank.'.$filetype;
                $filekey = $this->_generate_onlyoffice_key($to);
                $this->set(array('file'=>$file,'url'=>$url,'filekey'=>$filekey,'filetype'=>$filetype));
            }else{
                echo "Unable to copy file";
            }

        }else{
            echo "File does not exists";
        }
    }   

    public function update_access(){
        $this->autoRender = false;
        $qcDocument = $this->QcDocument->find('first',array('conditions'=>array('QcDocument.id'=>$this->request->data['qc_document_id'])));
        
        if($qcDocument){
            if($this->request->data['action'] == 'addview'){
                $users = json_decode($qcDocument['QcDocument']['user_id'],true);
                $users[] = $this->request->data['user_id'];
                $qcDocument['QcDocument']['user_id'] = json_encode($users);
            }            
            if($this->request->data['action'] == 'removeview'){
                $qcDocument['QcDocument']['user_id'] = str_replace($this->request->data['user_id'], '', $qcDocument['QcDocument']['user_id']);
                $users = json_decode($qcDocument['QcDocument']['user_id'],true);
                foreach($users as $user){
                    if($user)$newView[] = $user;
                }
                $qcDocument['QcDocument']['user_id'] = json_encode($newView);        
            }
            if($this->request->data['action'] == 'removeedit'){
                $qcDocument['QcDocument']['editors'] = str_replace($this->request->data['user_id'], '', $qcDocument['QcDocument']['editors']);
                $users = json_decode($qcDocument['QcDocument']['editors'],true);
                foreach($users as $user){
                    if($user)$newEdit[] = $user;
                }
                $qcDocument['QcDocument']['editors'] = json_encode($newEdit);
            }
            if($this->request->data['action'] == 'addedit'){
                $users = json_decode($qcDocument['QcDocument']['editors'],true);
                $users[] = $this->request->data['user_id'];
                $qcDocument['QcDocument']['editors'] = json_encode($users);                
            }

            $this->QcDocument->create();
            if($this->QcDocument->save($qcDocument,false)){
                return true;
            }else{
                return false;
            }

        }else{
            return false;
        }        
    }

    public function reloaddocument($docid = null){        
        if($docid){
            $qcDocument = $this->QcDocument->find('first',array('conditions'=>array('QcDocument.id'=>$docid)));
            $this->set('qcDocument',$qcDocument);
        }
    }

    public function _get_last_number(){
        $docs = $this->QcDocument->find('count',array());
        $docs = $docs + 1;
        $number = "QMS-".str_pad($docs, 3, 0, STR_PAD_LEFT);
        return $number;        
    }
    // change history functionality

    public function define_change_history_table(){
        $this->loadModel('Company');
        $customTables = $this->QcDocument->CustomTable->find('list');
        $this->set('customTables',$customTables);
        if($this->request->params['named']['custom_table_id']){
            $customTable = $this->QcDocument->CustomTable->find('first',array('conditions'=>array('CustomTable.id'=>$this->request->params['named']['custom_table_id']),
                'fields'=>array('CustomTable.id','CustomTable.fields'),
                'recursive'=>-1));
            $this->set('customTable',$customTable);
        }else{
            if ($this->request->is('post') || $this->request->is('put')) {         
                $company = $this->Company->find('first',array('conditions'=>array('Company.id'=>$this->Session->read('User.company_id'))));
                if($company){
                    $company['Company']['change_management_table'] = $this->request->data['QcDocument']['custom_table_id'];
                    $company['Company']['change_management_table_fields'] = json_encode($this->request->data['QcDocument']['fields']);
                    $this->Company->create();
                    $this->Company->save($company,false);

                    $this->Session->setFlash(__('Form linked.'));
                    $this->redirect(array('action' => 'index','timestamp'=>date('ymdhis')));
                }
            }else{                
                $company = $this->Company->find('first',array('fields'=>array(
                    'Company.id',
                    'Company.change_management_table',
                    'Company.change_management_table_fields',
                ), 'conditions'=>array('Company.id'=>$this->Session->read('User.company_id'))));

                if($company['Company']['change_management_table']){
                    $customTable = $this->QcDocument->CustomTable->find('first',array('conditions'=>array('CustomTable.id'=>$company['Company']['change_management_table']),
                        'fields'=>array('CustomTable.id','CustomTable.fields'),
                        'recursive'=>-1));
                }
                $this->set('customTable',$customTable);
                $this->set('company',$company);
            }
        }
                
    }

    public function change_history($id = null){  
        if(!$id)$id = $this->request->params['named']['id'];   
        else $this->request->params['named']['id'] = $id;
        $this->loadModel('Company');
        $company = $this->Company->find('first',array('conditions'=>array('Company.id'=>$this->Session->read('User.company_id')),'fields'=>array('Company.id','Company.change_management_table','Company.change_management_table_fields')));
        if($company['Company']['change_management_table']){
            $fields = json_decode($company['Company']['change_management_table_fields'],true);
            $this->set('fields',$fields);
            $customTable = $this->QcDocument->CustomTable->find('first',array(
                'conditions'=>array('CustomTable.id'=>$company['Company']['change_management_table']),
                'fields'=>array(
                    'CustomTable.id',
                    'CustomTable.name',
                    'CustomTable.table_name',
                    'CustomTable.fields',
                    'CustomTable.belongs_to',                    
                ),
                'recursive'=>-1
            ));

            if($customTable){
                // load model
                $model = Inflector::Classify($customTable['CustomTable']['table_name']);
                $this->loadModel($model);
                $records = $this->$model->find('all',array('conditions'=>array($model.'.document_for_change'=>$this->request->params['named']['id'])));
                $this->set('records',$records);
                $this->set('table',$customTable['CustomTable']['name']);
                $this->set('table_name',$customTable['CustomTable']['table_name']);
                $this->set('model',$model);
                $this->set('table_fields',$customTable['CustomTable']['fields']);
                $this->set('belogsTos',$customTable['CustomTable']['belongs_to']);
                $this->set('prompt',false);
                return count($records);
            }
        }else{
            $this->set('prompt',true);
        }
    }


    public function update_revision($id = null){
        if ($this->request->is('post') || $this->request->is('put')) {
            
            $qcDocument = $this->QcDocument->find('first',array('conditions'=>array('QcDocument.id'=>$this->request->data['QcDocument']['id']),'recursive'=>-1));
            
            $archiveQcDocument = $qcDocument;
            unset($archiveQcDocument['QcDocument']['id']);
            unset($archiveQcDocument['QcDocument']['sr_no']);
            //adjust revision number 
            // $archiveQcDocument['QcDocument']['revision_number'] = $qcDocument['QcDocument']['revision_number'];
            $archiveQcDocument['QcDocument']['publish'] = 0;
            $archiveQcDocument['QcDocument']['archived'] = 1;
            $archiveQcDocument['QcDocument']['cr_id'] = $this->request->data['QcDocument']['cr_id'];
            $archiveQcDocument['QcDocument']['parent_id'] = $qcDocument['QcDocument']['id'];            
            $archiveQcDocument['QcDocument']['modified'] = date('Y-m-d H:i:s');
            $archiveQcDocument['QcDocument']['modified_by'] = $this->Session->read('User.id');
            $archiveQcDocument['QcDocument']['file_key'] = $this->_generate_onlyoffice_key($qcDocument['QcDocument']['id'] . date('Ymdhis'));
            $qcDocument['QcDocument']['document_status'] = 4;

            
            $this->QcDocument->create();
            $this->QcDocument->Save($archiveQcDocument,false);

            //after saving archive record, update current record
            // set publish = 1
            // set document_status = 5
            $qcDocument['QcDocument']['revision_number'] = $archiveQcDocument['QcDocument']['revision_number'] + 1;
            $qcDocument['QcDocument']['revision_date'] = $this->request->data['QcDocument']['revision_date'];
            $qcDocument['QcDocument']['publish'] = 1;
            $qcDocument['QcDocument']['document_status'] = 5;
            $qcDocument['QcDocument']['modified'] = date('Y-m-d H:i:s');
            $qcDocument['QcDocument']['modified_by'] = $this->Session->read('User.id');
            $qcDocument['QcDocument']['file_key'] = $this->_generate_onlyoffice_key($qcDocument['QcDocument']['id'] . date('Ymdhis'));
            $this->QcDocument->create();
            $this->QcDocument->Save($qcDocument,false);

            // after saving this record, create folder - files/company_id/archive/qc_document_id/revision_number/cr_id/
            $archiveFolder = Configure::read('files') . DS . 'archive' . DS . $this->request->data['QcDocument']['id'] . DS .$archiveQcDocument['QcDocument']['revision_number'] . DS . $this->request->data['QcDocument']['cr_id'] ;
            
            
            $createFolder = New Folder();
            $createFolder->create($archiveFolder, 0777);


            $from = $this->request->data['QcDocument']['replace_file'];
            if(file_exists($from)){
                // echo "yes";
            }else{
                // echo "No";
            }
            $file = $archiveFolder. DS . 'archived.'.$qcDocument['QcDocument']['file_type'];
            $to = $file;


            $fromFile = new File($from);
            $fromFile->copy($to,false);
            if(copy($from,$to)){

            }else{
                echo "Something went wrong. System cound not find the original document";
            }

            // move new files from files folder to qc document folder
            // rename that file with new version number
            $file_name = $qcDocument['QcDocument']['title'];
            $document_number = $qcDocument['QcDocument']['document_number'];
            $document_version = $qcDocument['QcDocument']['revision_number']-1;
            $file_name = $document_number . '-' . $file_name . '-' . $document_version;
            $file_name = $this->_clean_table_names($file_name);
            $file_name = $file_name. '.' . $qcDocument['QcDocument']['file_type'];
            

            // $id = $this->QcDocument->id;
            $oldFilePath = Configure::read('path') . DS . $qcDocument['QcDocument']['id'];
            // $oldFile = Configure::read('path') . DS . $qcDocument['QcDocument']['id'] . DS . $file_name;


            // ---- 
            if($qcDocument['QcDocument']['id']){
                $createFolder = New Folder();
                $createFolder->delete($oldFilePath);    
            }
            

            $createFolder = New Folder();
            $createFolder->create($oldFilePath, 0777);

            // -----
            $this->loadModel('DownloadFile');
            $newFile = $this->DownloadFile->find('first',array('conditions'=>array('DownloadFile.id'=>$this->request->data['QcDocument']['new_file_id'])));
            
            $new_file_path = Configure::read('files')  . DS . 'files' . DS . $newFile['DownloadFile']['id'] . DS . $newFile['DownloadFile']['name'].'.'.$newFile['DownloadFile']['file_type'];
            

            $file_name = $qcDocument['QcDocument']['title'];
            $document_number = $qcDocument['QcDocument']['document_number'];
            $document_version = $qcDocument['QcDocument']['revision_number'];
            $file_name = $document_number . '-' . $file_name . '-' . $document_version;
            $file_name = $this->_clean_table_names($file_name);
            $file_name = $file_name. '.' . $qcDocument['QcDocument']['file_type'];
            
            
            //final copy

            $from = $new_file_path;
            $to = $oldFilePath. DS .$file_name;
            
            $fromFile = new File($from);
            $fromFile->copy($to,false);
            if(copy($from,$to)){
                $this->Session->setFlash(__('Change request successfully completed. Document successfully updated.'));
                $this->redirect(array('action' => 'view', $qcDocument['QcDocument']['id']));  
            }else{
                echo "Something went wrong. System cound not find the original document";
            }
            
            

        }else{
            // get CR table
            $this->loadModel('Company');
            $company = $this->Company->find('first',array('conditions'=>array('Company.id'=>$this->Session->read('User.company_id')),'fields'=>array('Company.id','Company.change_management_table','Company.change_management_table_fields')));
            if($company['Company']['change_management_table']){
                $fields = json_decode($company['Company']['change_management_table_fields'],true);
                $this->set('fields',$fields);
                $customTable = $this->QcDocument->CustomTable->find('first',array(
                    'conditions'=>array('CustomTable.id'=>$company['Company']['change_management_table']),
                    'fields'=>array(
                        'CustomTable.id',
                        'CustomTable.name',
                        'CustomTable.table_name',
                        'CustomTable.fields',
                        'CustomTable.belongs_to',                    
                    ),
                    'recursive'=>-1
                ));

                if($customTable){
                    // load model
                    $model = Inflector::Classify($customTable['CustomTable']['table_name']);
                    $this->loadModel($model);
                    $records = $this->$model->find('all',array(
                        'limit'=>1,
                        'order'=>array($model.'.sr_no'=>'DESC'),
                        'conditions'=>array($model.'.document_for_change'=>$id)));
                    $this->set('records',$records);
                    $this->set('table',$customTable['CustomTable']['name']);
                    $this->set('table_name',$customTable['CustomTable']['table_name']);
                    $this->set('model',$model);
                    $this->set('table_fields',$customTable['CustomTable']['fields']);
                    $this->set('belogsTos',$customTable['CustomTable']['belongs_to']);
                    $this->set('prompt',false);


                    /// also load existing document
                    $qcDocument = $this->QcDocument->find('first',array('conditions'=>array('QcDocument.id'=>$id)));
                    $this->set('document',$qcDocument);
                    $this->set('qcDocument',$qcDocument);
                    
                    if($qcDocument['QcDocument']['archived'] == true){
                        $this->Session->setFlash(__('Document is already archived.'));
                        $this->redirect(array('action' => 'view', $qcDocument['QcDocument']['parent_id'], 'timestamp'=>date('ymdhis')));
                    }
        
                    
                    // find changed document
                    $additionalFiles = json_decode($records[0][$model]['additional_files'],true);
                    
                    $this->loadModel('File');
                    foreach($additionalFiles as $additionalFile){
                        $file = $this->File->find('first',array('conditions'=>array(
                            'File.id'=>$additionalFile)));                             
                        $this->set('fileEdit',$file);                        
                    }
                }
            }else{
                $this->set('prompt',true);
            }
        }        

    }

    public function load_revisions($id = null){
        $qcDocuments = $this->QcDocument->find('all',array('conditions'=>array('QcDocument.parent_id'=>$this->request->params['named']['id'])));
        $this->set('qcDocuments',$qcDocuments);    
    }

    public function _add_process($data = null,$id = null){
        $data['Process']['name'] = $data['QcDocument']['title'];
        $data['Process']['qc_document_id'] = $id;
        $data['Process']['applicable_to_branches'] = json_encode(array($data['QcDocument']['share_with_branches']));
        $data['Process']['process_owners'] = json_encode(array($data['QcDocument']['share_with_departments']));
        $data['Process']['standards'] = json_encode(array($data['QcDocument']['standard_id']));
        $data['Process']['clauses'] = json_encode(array($data['QcDocument']['clause_id']));
        $data['Process']['schedule_id'] = $data['QcDocument']['schedule_id'];
        $data['Process']['prepared_by'] = $data['QcDocument']['prepared_by'];
        $data['Process']['approved_by'] = $data['QcDocument']['approved_by'];
        $data['Process']['created'] = $data['QcDocument']['created'];
        $data['Process']['created_by'] = $data['QcDocument']['created_by'];
        $data['Process']['modified'] = $data['QcDocument']['modified'];
        $data['Process']['modified_by'] = $data['QcDocument']['modified_by'];
        $this->loadModel('Process');
        $this->Process->save($data['Process'],false);
        return true;
    }

    public function _update_process($data = null,$id = null){
        $this->loadModel('Process');
        if($processes){
            foreach($processes as $process){

                $process['Process']['name'] = $data['QcDocument']['title'];
                $process['Process']['qc_document_id'] = $id;
                $process['Process']['applicable_to_branches'] = json_encode($data['QcDocument']['share_with_branches']);
                $process['Process']['process_owners'] = json_encode($data['QcDocument']['share_with_departments']);
                $process['Process']['standards'] = json_encode(array($data['QcDocument']['standard_id']));
                $process['Process']['clauses'] = json_encode(array($data['QcDocument']['clause_id']));
                $process['Process']['schedule_id'] = $data['QcDocument']['schedule_id'];
                $process['Process']['prepared_by'] = $data['QcDocument']['prepared_by'];
                $process['Process']['approved_by'] = $data['QcDocument']['approved_by'];
                $this->Process->create();
                $this->Process->save($process['Process'],false);
            }
        }
        return true;
    }

    public function add_bulk(){
        if ($this->request->is('post') || $this->request->is('put')) {
            foreach($this->request->data['Docs'] as $qcDocument){                
                $qcDocument['QcDocument']['name'] = ltrim(rtrim($qcDocument['QcDocument']['title']));
                $qcDocument['QcDocument']['title'] =ltrim(rtrim($this->_clean_table_names($qcDocument['QcDocument']['title'])));
                $qcDocument['QcDocument']['document_number'] = ltrim(rtrim($qcDocument['QcDocument']['document_number']));
                $qcDocument['QcDocument']['revision_number'] = ltrim(rtrim($qcDocument['QcDocument']['revision_number']));
                $qcDocument['QcDocument']['standard_id'] = $this->request->data['QcDocument']['standard_id'];
                $qcDocument['QcDocument']['clause_id'] = $this->request->data['QcDocument']['clause_id'];
                $qcDocument['QcDocument']['qc_document_category_id'] = $this->request->data['QcDocument']['qc_document_category_id'];
                $qcDocument['QcDocument']['branches'] = json_encode(array_keys($this->_get_branch_list()));
                $qcDocument['QcDocument']['departments'] = json_encode(array_keys($this->_get_department_list()));
                $qcDocument['QcDocument']['designations'] = json_encode(array_keys($this->_get_designation_list()));                
                $qcDocument['QcDocument']['user_id'] = json_encode(array_keys($this->_get_user_list()));
                $qcDocument['QcDocument']['editors'] = json_encode(array_keys($this->_get_user_list()));
                $qcDocument['QcDocument']['file_type'] = 'docx';
                $qcDocument['QcDocument']['data_type'] = 2;
                $qcDocument['QcDocument']['it_categories'] = 0;
                $qcDocument['QcDocument']['archived'] = 0;
                $qcDocument['QcDocument']['document_status'] = 1;
                $qcDocument['QcDocument']['add_records'] = 1;
                $qcDocument['QcDocument']['publish'] = 1;
                $qcDocument['QcDocument']['soft_delete'] = 0;
                $qcDocument['QcDocument']['parent_document_id'] = -1;
                $qcDocument['QcDocument']['undate_custom_table_document'] = 0;
                $qcDocument['QcDocument']['schedule_id'] = '56d1564b-0acc-48f6-9beb-03a7db1e6cf9';
                $qcDocument['QcDocument']['created'] = $qcDocument['QcDocument']['modified'] = date('Y-m-d H:i:s');
                $qcDocument['QcDocument']['issued_by'] = $qcDocument['QcDocument']['issuing_authority_id'] = $qcDocument['QcDocument']['approved_by'];
                $qcDocument['QcDocument']['created_by'] = $qcDocument['QcDocument']['modified_by'] = $this->Session->read('User.id');
                $qcDocument['QcDocument']['company_id'] =  $this->Session->read('User.company_id');



                $file_name = $qcDocument['QcDocument']['title'];
                $document_number = $qcDocument['QcDocument']['document_number'];
                $document_version = $qcDocument['QcDocument']['revision_number'];
                $file_name = $document_number . '-' . $file_name . '-' . $document_version;
                $file_name = $this->_clean_table_names($file_name);
                $file_name = $file_name. '.docx';

                $this->QcDocument->create();
                if($this->QcDocument->save($qcDocument['QcDocument'],false)){
                    $id = $this->QcDocument->id;
                    $from = WWW_ROOT  . 'files' . DS . 'samples' . DS . 'sample.docx';
                    
                    $file = Configure::read('path') . DS . $id . DS . $file_name;                    
                    $to = Configure::read('path') . DS . $id;
                    $history_file_for_save = $file;

                    if(!file_exists($to)){
                        $testfolder = new Folder($to);
                        $testfolder->create($to);
                        chmod($to, 0777);
                    }

                    $to = $file;

                    $fromFile = new File($from);
                    $fromFile->copy($to,true);
                    if(copy($from,$to)){
                        $key = $this->_generate_onlyoffice_key($qcDocument['QcDocument']['id'] . date('Ymdhis'));
                        $this->set('file_key', $key);
                        $qcdoc = $this->QcDocument->find('first', array('recursive' => - 1, 'conditions' => array('QcDocument.id' => $id)));
                        $qcdoc['QcDocument']['file_key'] = $key;
                        $qcdoc['QcDocument']['file_type'] = 'docx';
                        $qcdoc['QcDocument']['file_key'] = $key;
                        $this->QcDocument->create();
                        $this->QcDocument->save($qcdoc['QcDocument']);

                        $json = [
                            "created" => date("Y-m-d H:i:s"),
                            'uid'=>$this->Session->read('User.id'),
                            'name'=>$this->Session->read('User.name'),
                        ];

                        $version = 1;
                        $history_file_for_save = $history_file_for_save.'-hist' . DS . $version;
                        $historyFolder = new Folder($history_file_for_save);
                        $historyFolder->create($history_file_for_save);
                        chmod($history_file_for_save, 0777);
                        file_put_contents($history_file_for_save . DS . "createdInfo.json", json_encode($json, JSON_PRETTY_PRINT));

                    }else{
                        
                    }  
                }
            }
            $this->Session->setFlash(__('Document Added`'));
            $this->redirect(array('action' => 'index'));  
        }

        $this->_commons();  
        $this->set('documentTypes',$this->QcDocument->customArray['documentTypes']);
    }


    public function fix_names(){
        Configure::write('debug',1);
        $qcDocuments = $this->QcDocument->find('all',array('conditions'=>array(),'recursive'=>-1,'fields'=>array('QcDocument.id','QcDocument.name','QcDocument.title','QcDocument.version','QcDocument.file_type','QcDocument.issue_number','QcDocument.document_number','QcDocument.revision_number')));
        foreach($qcDocuments as $qcDocument){
            debug($qcDocument);
            $qcDocument['QcDocument']['name'] = Inflector::humanize($qcDocument['QcDocument']['title']);
            $qcDocument['QcDocument']['title'] = $this->_clean_table_names($qcDocument['QcDocument']['title']);
            debug($qcDocument);
            $this->QcDocument->create();
            $this->QcDocument->save($qcDocument,false);            
        }
        exit;
    }
}
