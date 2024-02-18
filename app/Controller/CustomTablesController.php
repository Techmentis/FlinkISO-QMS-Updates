<?php
App::uses('AppController', 'Controller');
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');
/**
 * CustomTables Controller
 *
 * @property CustomTable $CustomTable
 * @property PaginatorComponent $Paginator
 */
class CustomTablesController extends AppController {
    /**
     * Components
     *
     * @var array
     */
    public $components = array('Paginator','AvailableForms','CreateModel', 'CreateView', 'CreateChildModel');

    public $reserved_fields = array("id", "prepared_by", "approved_by", "created", "modified", "sr_no", "qc_document_id", "file_id", "file_key" ,"created_by" ,"modified_by" ,"status_user_id" ,"record_status" ,"branchid" ,"departmentid" ,"company_id" ,"soft_delete" ,"process_id","publish","custom_table_id");     
    
    public function _get_system_table_id() {
        $this->loadModel('SystemTable');
        $this->SystemTable->recursive = - 1;
        $systemTableId = $this->SystemTable->find('first', array('conditions' => array('SystemTable.system_name' => $this->request->params['controller'])));
        return $systemTableId['SystemTable']['id'];
    }
    public function _commons($creator = null) {        
        if ($this->action == 'view' || $this->action == 'recreate') $this->set('approvals', $this->get_approvals());

        $this->set('branches', $this->_get_branch_list());
        $this->set('departments', $this->_get_department_list());
        $this->set('designations', $this->_get_designation_list());
        $this->set('usernames', $this->_get_usernames());
        
        // $companies = $this->CustomTable->Company->find('list',array('conditions'=>array('Company.publish'=>1,'Company.soft_delete'=>0)));
        $preparedBies = $approvedBies = $this->CustomTable->PreparedBy->find('list', array('conditions' => array('PreparedBy.publish' => 1, 'PreparedBy.soft_delete' => 0)));
        $createdBies = $modifiedBies = $this->CustomTable->CreatedBy->find('list', array('conditions' => array('CreatedBy.publish' => 1, 'CreatedBy.soft_delete' => 0)));
        $qcDocuments = $this->CustomTable->QcDocument->find('list', array('conditions' => array('QcDocument.publish' => 1, 'QcDocument.soft_delete' => 0)));
        $this->set(compact('companies', 'preparedBies', 'approvedBies', 'createdBies', 'modifiedBies'));
        $count = $this->CustomTable->find('count');
        $publish = $this->CustomTable->find('count', array('conditions' => array('CustomTable.publish' => 1)));
        $unpublish = $this->CustomTable->find('count', array('conditions' => array('CustomTable.publish' => 0)));
        // $fieldTypes = array(0 => 'varchar', 1 => 'text', 2 => 'int', 3 => 'tinyint', 4 => 'float', 5 => 'date', 6 => 'datetime',);
        $fieldTypes = $this->CustomTable->customArray['fieldTypes'];
        // $displayTypes = array(0 => 'text', 1 => 'radio', 2 => 'checkbox', 3 => 'dropdown', 4 => 'multiple', 5 => 'break');
        $displayTypes = $this->CustomTable->customArray['displayTypes'];
        //get all tables
        $controllers = array();
        $aCtrlClasses = App::objects('controller');
        $skip = array('AppController', 'ApprovalsController', 'ApprovalCommentsController', 'CustomTablesController', 'FilesController', 'RecordsController', 'UserSessionsController');
        foreach ($aCtrlClasses as $controller) {
            if (!in_array($controller, $skip)) {
                $controller = str_replace('Controller', '', $controller);
                $name = $this->CustomTable->find('first', array('recursive' => 1, 'conditions' => array('CustomTable.table_name LIKE' => "%" . Inflector::underscore($controller)), 'fields' => array('CustomTable.id', 'CustomTable.name', 'CustomTable.table_version')));
                if ($name) {
                    $controller = Inflector::classify($controller);
                    $linkedTos[$controller] = $name['CustomTable']['name'] . " ver " . $name['CustomTable']['table_version'];
                } else {
                    $linkedTos[$controller] = $controller;
                }
            }
        }

        $customArray = $this->CustomTable->customArray;

        $dataTypes = array('text','phone','email','textarea','checkbox','radio','dropdown-s','dropdown-m','date','datetime','number','file');


        $bootstrapSizes = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12);
        $this->set(compact('count', 'publish', 'unpublish', 'fieldTypes', 'bootstrapSizes', 'linkedTos', 'displayTypes', 'qcDocuments','dataTypes','customArray'));
        // add creator
        if ($this->request->params['named']['approval_id']) {
            $this->get_approval($this->request->params['named']['approval_id'], $creator);
            $this->_get_approval_comnments($this->request->params['named']['approval_id'], $creator);
        }
        $this->_get_approver_list($creator);


        $processes = $this->CustomTable->Process->find('list', array('recursive' => -1, 'conditions' => array()));
        $standards = $this->CustomTable->QcDocument->Standard->find('list', array('recursive' => -1, 'conditions' => array()));
        $clauses = $this->CustomTable->QcDocument->Standard->Clause->find('list', array('recursive' => -1, 'conditions' => array()));
        $departments = $this->CustomTable->QcDocument->CreatedBy->Department->find('list');
        $branches = $this->CustomTable->QcDocument->CreatedBy->Branch->find('list');
        $schedules = $this->CustomTable->QcDocument->Schedule->find('list');
        $this->set(compact('processes','standards','clauses','departments','branches','schedules'));

        $reserved_fields = array("id", "name", "prepared_by", "approved_by", "created", "modified", "sr_no", "qd_document_id", "file_id", "file_key" ,"created_by" ,"modified_by" ,"status_user_id" ,"record_status" ,"branchid" ,"departmentid" ,"company_id" ,"soft_delete" ,"process_id");     

        
    }
    /**
     * index method
     *
     * @return void
     */
    public function index() {

        if($this->Session->read('User.is_mr') == false){
            $accessConditions = array(
                'OR'=>array(
                    'QcDocument.branches LIKE' => '%'.$this->Session->read('User.branch_id').'%',
                    'QcDocument.departments LIKE' => '%'.$this->Session->read('User.department_id').'%',
                    // 'QcDocument.designations LIKE' => '%'.$this->Session->read('User.designation_id').'%',
                    'QcDocument.user_id LIKE' => '%'.$this->Session->read('User.id').'%',
                )
            );
        }else{
            $accessConditions = array();
        }
        
        $conditions = $this->_check_request();
        if($this->request->params['named']['table_type'] == 1)$accessConditions[] = array('CustomTable.table_type'=>0);
        else if($this->request->params['named']['table_type'] == 2)$accessConditions[] = array('CustomTable.table_type'=>1);
        else if($this->request->params['named']['table_type'] == 3)$accessConditions[] = array('CustomTable.table_type'=>2);
        else {$accessConditions[] = array('CustomTable.table_type'=>array(0,1));$this->request->params['named']['table_type'] = 4;}
        
        $this->CustomTable->virtualFields = array(
            'linked' => 'select count(*) from `custom_tables` where `custom_tables`.`custom_table_id` LIKE CustomTable.id ',
            'childDoc' => 'select count(*) from `qc_documents` where QcDocument.parent_document_id LIKE `qc_documents`.id ',
        );
        $this->paginate = array(
            'order' => array('CustomTable.childDoc'=>'ASC','CustomTable.sr_no' => 'DESC'), 
            'conditions' => array($accessConditions, 'OR' => array('ltrim(rtrim(CustomTable.custom_table_id))' => "", 'CustomTable.custom_table_id' => null, 'CustomTable.linked >' => 0)));
        $this->CustomTable->recursive = 0;
        $customTables = $this->paginate();

        $this->set('customTables', $customTables);
        $this->_get_count();
    }
    public function child($custom_table_id = null) {
        $conditions = $this->_check_request();
        $this->paginate = array('order' => array('CustomTable.sr_no' => 'DESC'), 'conditions' => array($conditions, 'CustomTable.custom_table_id' => $this->request->params['named']['custom_table_id']));
        $this->CustomTable->recursive = 0;
        $customTables = $this->paginate();
        $this->set('customTables', $customTables);
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
        if (!$this->CustomTable->exists($id)) {
            throw new NotFoundException(__('Invalid Table'));
        }        

        if($this->Session->read('User.is_mr') == false){
            if($this->request->is('ajax') == false){
                $this->Session->setFlash(__('You are not authorized to view this section'), 'default', array('class' => 'alert alert-danger'));
                $this->redirect(array('controller' => 'users', 'action' => 'access_denied',$n));
            }else{
                echo "<br />You are not authorized to view this form<br /><br />";
                exit;
            }
        }

        if ($this->request->params['named']['approval_id']) {
            $this->redirect(array('action' => 'recreate', $id, 'approval_id' => $this->request->params['named']['approval_id']));
        }
        $this->CustomTable->virtualFields = array('linked' => 'select count(*) from `custom_tables` where `custom_tables`.`custom_table_id` LIKE CustomTable.id ',);
        $options = array('conditions' => array('CustomTable.' . $this->CustomTable->primaryKey => $id));
        $customTable = $this->CustomTable->find('first', $options);
        $this->set('customTable', $this->CustomTable->find('first', $options));
        if ($this->_show_approvals()) {
            $this->loadModel('User');
            $this->User->recursive = 0;
            $userids = $this->User->find('list', array('order' => array('User.name' => 'ASC'), 'conditions' => array('User.publish' => 1, 'User.soft_delete' => 0, 'User.is_approver' => 1)));
            $this->set(array('userids' => $userids));
            $this->set(array('showApprovals' => $this->_show_approvals()));
        }


        // check if file exists, if not, add from latest qc file

        $qcDocument = $this->_qc_document_header($customTable['CustomTable']['qc_document_id']);
        
        $file_type = $qcDocument['QcDocument']['file_type'];
        $file_name = $qcDocument['QcDocument']['title'];
        $document_number = $qcDocument['QcDocument']['document_number'];
        $document_version = $qcDocument['QcDocument']['revision_number'];
        // $file_name = $document_number . '-' . $file_name . '-' . $document_version . '.' . $file_type;

        $file_name = $document_number . '-' . $file_name . '-' . $document_version;
        $file_name = $this->_clean_table_names($file_name);
        $file_name = $file_name . '.' . $file_type;

        $tableFile = WWW_ROOT .  'files' . DS . $this->Session->read('User.company_id') . DS . 'custom_tables' . DS . $id . DS . $file_name;

        if(!file_exists($tableFile)){            
            $qcfile = WWW_ROOT .  'files' . DS . $this->Session->read('User.company_id') . DS . 'qc_documents' . DS . $qcDocument['QcDocument']['id'] . DS . $file_name;;
            if(file_exists($qcfile)){
                // copy file
                copy($qcfile,$tableFile);
            }else{
                $this->set('doc_not_found',true);
            }
        }

        
        $this->set('qcDocument', $qcDocument);
        $this->set('process', $this->_process_header($customTable['CustomTable']['process_id']));
        $this->_commons($this->Session->read('User.id'));

        // get child 
        $childs = $this->CustomTable->find('all',array('conditions'=>array('CustomTable.custom_table_id'=>$id)));
        
        $this->set('childs',$childs);

        $customTriggers = $this->CustomTable->CustomTrigger->find('all',array('conditions'=>array('CustomTrigger.custom_table_id'=>$customTable['CustomTable']['id'])));
        $this->set('customTriggers',$customTriggers);

        $key = $this->_generate_onlyoffice_key($customTable['CustomTable']['id'] . date('Ymdhis'));
        $this->set('filekey', $key);

    }
    public function edit($id = null) {
        if($this->Session->read('User.is_mt') == false){
            $this->Session->setFlash(__('You are not authorized to view this section'), 'default', array('class' => 'alert alert-danger'));
            $this->redirect(array('controller' => 'users', 'action' => 'access_denied',$n));
        }

        $this->redirect(array('action' => 'view', $id));
    }
    public function _make_table_name($qc_doc_id = null,$type = null) {        
        if($type == 'qc_documents'){
            $qcDocument = $this->CustomTable->QcDocument->find('first', array('recursive' => 0, 'conditions' => array('QcDocument.id' => $qc_doc_id),));
            $tableName = $qcDocument['QcDocument']['title'] . '_' . $qcDocument['QcDocument']['revision_number'];
            $tableName = $this->_clean_table_names($tableName);
            $tableName = $tableName . '_v';
            // check if any other tables is already created.
            $version = $this->CustomTable->find('count', array('conditions' => array('CustomTable.table_name LIKE' => '%' . $tableName . '%')));
            if($version)$version = $version + 1;
            else $version = 0;
            $tableName = $qcDocument['QcDocument']['title'] . '_' . $qcDocument['QcDocument']['revision_number'];
            $tableName = ltrim(rtrim($tableName));
            $tableName = 'tbl_'.$this->_clean_table_names($tableName);
            $tableName = Inflector::pluralize($tableName . '_v' . $version);
            return array($tableName, $version);
        }else if($type == 'processes'){
            $process = $this->CustomTable->Process->find('first', array('recursive' => 0, 'conditions' => array('Process.id' => $qc_doc_id),));
            $tableName = $process['Process']['name'];
            $tableName = ltrim(rtrim($tableName));
            $tableName = $this->_clean_table_names($tableName);
            $tableName = $tableName . '_v';
            // check if any other tables is already created.
            $version = $this->CustomTable->find('count', array('conditions' => array('CustomTable.table_name LIKE' => '%' . $tableName . '%')));
            if($version)$version = $version + 1;
            else $version = 0;            
            $tableName = $tableName = $process['Process']['name'].'_'. $version;
            $tableName = 'tbl_'.$this->_clean_table_names($tableName);
            $tableName = Inflector::pluralize($tableName . '_v' . $version);
            return array($tableName, $version);
        }else if($type == 'masters'){
            // $process = $this->CustomTable->Process->find('first', array('recursive' => 0, 'conditions' => array('Process.id' => $qc_doc_id),));
            // $tableName = $process['Process']['name'];
            $tableName = $this->request->data['CustomTable']['name'];
            $tableName = ltrim(rtrim($tableName));
            $tableName = $this->_clean_table_names($tableName);
            $tableName = $tableName . '_v';
            // check if any other tables is already created.
            $version = $this->CustomTable->find('count', array('conditions' => array('CustomTable.table_name LIKE' => '%' . $tableName . '%')));
            $version = 0;            
            // $tableName = $tableName = $process['Process']['name'].'_'. $version;
            $tableName = 'tbl_'.$this->_clean_table_names($tableName);
            $tableName = Inflector::pluralize($tableName .$version);
            return array($tableName, $version);
        }
        
    }
    public function _make_child_table_name($qc_doc_id = null, $type = null, $linked = null) {        
        if($type == 'qc_documents'){
            $qcDocument = $this->CustomTable->QcDocument->find('first', array('recursive' => 0, 'conditions' => array('QcDocument.id' => $qc_doc_id),));
            $linked = $linked + 1;
            $tableName = $qcDocument['QcDocument']['title'] . '' . $qcDocument['QcDocument']['revision_number'];
            $tableName = $this->_clean_table_names(ltrim(rtrim($tableName)));
            $tableName = $tableName . '_child_' . $linked . '_v';
            // check if any other tables is already created.
            $version = $this->CustomTable->find('count', array('conditions' => array('CustomTable.table_name LIKE' => '%' . $tableName . '%')));
            $version = $version + 1;
            $tableName = $qcDocument['QcDocument']['title'] . '' . $qcDocument['QcDocument']['revision_number'];
            $tableName = 'chd_'.$this->_clean_table_names($tableName);
            $tableName = Inflector::pluralize($tableName . '_child_' . $linked . '_v' . $version);
        }else if($type == 'processes'){    
            $process = $this->CustomTable->Process->find('first', array('recursive' => 0, 'conditions' => array('Process.id' => $qc_doc_id),));
            $tableName = 'chd_'.$this->_clean_table_names(trim($process['Process']['name']));
            $tableName = $tableName;
            
            $version = $this->CustomTable->find('count', array('conditions' => array('CustomTable.custom_table_id !='=>'', 'CustomTable.table_name LIKE' => '%' . $tableName .'%' )));

            $version = $version + 1;
            $tableName = 'chd_'.$this->_clean_table_names($process['Process']['name'].'_'. $version);
            $tableName = Inflector::pluralize($tableName . '_child_' . $linked . '_v' . $version);
            // return array($tableName, $version);
        }

        return array($tableName, $version);
    }
    public function add_fields($type = null, $id = null, $f = null) {
        
    }

    public function existing_fields($type = null, $id = null, $f = null,$field = null,$custom_table_id = null) {    
        $this->set('f', $f);
        $this->set('id', $id);
        $this->set('type', $type);
        $field_name = base64_decode($field);
        
        $table = $this->CustomTable->find('first',array('conditions'=>array('CustomTable.id'=>$custom_table_id),'recursive'=>-1,'fields'=>array('CustomTable.id','CustomTable.fields')));
        $fields = json_decode($table['CustomTable']['fields'],true);
        foreach($fields as $field){            
            if($field['field_name'] == $field_name){
                $fieldDetails = $field;
            }
        }
        
        $this->set('fieldDetails', $fieldDetails);
        $this->_commons($this->Session->read('User.id'));
    }


    // 1st step add new table
    public function _add_new_table($table_name = null,$defaultfield = null,$sqld = null,$fields = null){
        
        foreach($fields as $field){
            if($field['default_field'] != 1)$newFields[] = $field;
        }

        $fields = $newFields;


        $sql = "CREATE TABLE IF NOT EXISTS `" . $table_name . "` ( ";
        $sql .= "`id` varchar(36) NOT NULL ,";
        $sql .= "`sr_no` int(11) NOT NULL AUTO_INCREMENT,";
        $sql .= "`".$defaultfield."` varchar(255) NOT NULL,";
        $sql .= $sqld;
        $sql .= "            
        `file_id` varchar(36) NULL,
        `file_key` varchar(50) NULL,
        `parent_id` varchar(36) NULL,
        `additional_files` text NULL,
        `publish` tinyint(1) DEFAULT '1' COMMENT '0=Un 1=Pub',
        `record_status` tinyint(1) DEFAULT '0' COMMENT '0=Un-locked, 1=Locked',
        `status_user_id` varchar(36) DEFAULT NULL,
        `created_by` varchar(36) NOT NULL,
        `created` datetime NOT NULL,
        `modified_by` varchar(36) NOT NULL,
        `approved_by` varchar(36) DEFAULT NULL,
        `prepared_by` varchar(36) DEFAULT NULL,
        `modified` datetime NOT NULL,
        `soft_delete` tinyint(1) NOT NULL DEFAULT '0',
        `branchid` varchar(36) DEFAULT NULL,
        `departmentid` varchar(36) DEFAULT NULL,
        `company_id` varchar(36) DEFAULT NULL,
        PRIMARY KEY (`id`),
        KEY `sr_no` (`sr_no`)
        ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
        ";



        if($this->CustomTable->query($sql)){

        }else{
            // echo "Something went wrong";
        }

    // run above sql first and then add alter table commands one by one
        foreach ($fields as $chkd) {

            
            if($chkd['new'] == 0 && $chkd['field_name'] != $chkd['old_field_name']){

                switch ($chkd['field_type']) {
                    case 0: // varchar
                $type = 'varchar('.$chkd['length'].')';
                break;
                    case 1: // text
                $type = 'text';
                break;
                    case 2: // int
                $type = 'int('.$chkd['length'].')';
                break;
                    case 3: // tinyint
                $type = 'tinyint('.$chkd['length'].')';
                break;
                    case 4: // float
                $type = 'float('.$chkd['length'].')';
                break;
                    case 5: // date
                $type = 'date';
                break;
                    case 6: // datetime
                $type = 'datetime';
                break;
                default: // text
                    $type = 'text';
                break;
        }

        if($chkd['mandetory'] == 1)$m = ' NOT ';
        else $m = '';

        if(!in_array($chkd['field_name'],$this->reserved_fields))$updatesql .= 'ALTER TABLE `'.$this->request->data['CustomTable']['table_name'].'` CHANGE `'.$chkd['old_field_name'].'` `'.$chkd['field_name'].'` '.$type. ' '. $m.' NULL;';

    }

    if($chkd['drop'] == 1){
        if(!in_array($chkd['field_name'],$this->reserved_fields))$dropsql .= 'ALTER TABLE `'.$this->request->data['CustomTable']['table_name'].'` DROP `'.$chkd['field_name'].'`;';
    }

    if($chkd['new'] == 1){

        switch ($chkd['field_type']) {
            case 0: // varchar
            $type = 'varchar('.$chkd['length'].')';
            break;
            case 1: // text
            $type = 'text';
            break;
            case 2: // int
            $type = 'int('.$chkd['length'].')';
            break;
            case 3: // tinyint
            $type = 'tinyint('.$chkd['length'].')';
            break;
            case 4: // float
            $type = 'float('.$chkd['length'].')';
            break;
            case 5: // date
            $type = 'date';
            break;
            case 6: // datetime
            $type = 'datetime';
            break;
            default: // text
            $type = 'text';
            break;
        }

        if($chkd['mandetory'] == 1)$m = ' NOT ';
        else $m = '';
        if($chkd['display_type'] != 7 && $chkd['display_type'] != 5){
            if(!in_array($chkd['field_name'],$this->reserved_fields)){
                $addsql .=  'ALTER TABLE `'.$this->request->data['CustomTable']['table_name'].'` ADD `'.$chkd['field_name'].'` '.$type.' '. $m .' NULL AFTER `sr_no`;';
            }
        }
    }
}

if($updatesql){
    try{
        $this->CustomTable->query($updatesql);
    }catch(Exception $e){

    }
}

if($dropsql){
    try{
        $this->CustomTable->query($dropsql);
    }catch(Exception $e){
        
    }
}

if($addsql){
    try{
        $this->CustomTable->query($addsql);
    }catch(Exception $e){
        
    }
}

return true;

}
    /**
     * add method
     *
     * @return void
     */
    public function add() {        
        if($this->Session->read('User.is_mr') == false){
            $this->Session->setFlash(__('You are not authorized to view this section'), 'default', array('class' => 'alert alert-danger'));
            $this->redirect(array('controller' => 'users', 'action' => 'access_denied',$n));
        }

        if(empty($this->request->params['named']['qc_document_id']) && empty($this->request->params['named']['process_id'])){
            $this->Session->setFlash(__('Select Document/Process first'), 'default', array('class' => 'alert alert-danger'));
            $this->redirect(array('controller' => 'custom_tables', 'action' => 'index',$n));   
        }

        if ($this->_show_approvals()) {
            $this->loadModel('User');
            $this->User->recursive = 0;
            $userids = $this->User->find('list', array('order' => array('User.name' => 'ASC'), 'conditions' => array('User.publish' => 1, 'User.soft_delete' => 0, 'User.is_approver' => 1)));
            $this->set(array('userids' => $userids));
            $this->set(array('showApprovals' => $this->_show_approvals()));
        }

        // block adding table if document is not available

        if($this->request->params['named']['qc_document_id']){
            $options = array('recursive' => - 1, 'conditions' => array('QcDocument.id' => $this->request->params['named']['qc_document_id']));
            $qcDoc = $this->CustomTable->QcDocument->find('first', $options);
            $this->set('qcDoc', $qcDoc);
            $file_type = $qcDoc['QcDocument']['file_type'];
            $file_name = $qcDoc['QcDocument']['title'];
            $document_number = $qcDoc['QcDocument']['document_number'];
            $document_version = $qcDoc['QcDocument']['revision_number'];
            
            // $file_name = $document_number . '-' . $file_name . '-' . $document_version . '.' . $file_type;

            $file_name = $document_number . '-' . $file_name . '-' . $document_version;
            $file_name = $this->_clean_table_names($file_name);
            $file_name = $file_name . '.' . $file_type;

            $qcpfile = WWW_ROOT .  'files' . DS . $this->Session->read('User.company_id') . DS . 'qc_documents' . DS . $this->request->params['named']['qc_document_id'] . DS . $file_name;
            
            if (!file_exists($qcpfile)) {
                $this->Session->setFlash(__('You are creating HTML form without any document.'));                
            }    
        }else{
            $this->Session->setFlash(__('You are creating HTML form without any document.'));            
        }

        // else if($this->request->params['named']['process_id']){
        //     $options = array('recursive' => - 1, 'conditions' => array('Process.id' => $this->request->params['named']['process_id']));
        //     $proDoc = $this->CustomTable->Process->find('first', $options);
        //     $this->set('proDoc', $proDoc);
        //     $file_type = $proDoc['Process']['file_type'];
        //     $file_name = $proDoc['Process']['file_name'];
        
        //     $file_name = $file_name;
        //     $proFile = WWW_ROOT .  'files' . DS . $this->Session->read('User.company_id') . DS . 'processes' . DS . $this->request->params['named']['process_id'] . DS . $file_name;
        
        //     if (!file_exists($proFile)) {                    
        //         $this->Session->setFlash(__('Can not add HTML form without Process Document. Please add document first.'));
        //         $this->redirect(array('controller'=>'processes', 'action' => 'view', $this->request->params['named']['process_id']));
        //     }
        // }

        $this->_commons($this->Session->read('User.id'));
        
        // after form submit
        if ($this->request->is('post')) {
            
            $this->_commons($this->Session->read('User.id'));
            
            $this->request->data['CustomTable']['password'] = Security::hash($this->request->data['CustomTable']['password'], 'md5', true);
            
            foreach($this->request->data['CustomTableFields'] as $fields){
                if($fields['default_field'] == 1){
                    $defaultfield = $this->_clean_table_names($fields['field_name']);
                }
            }

            if ($defaultfield == '') {
                $this->Session->setFlash(__('Default field missing'));
                $this->redirect(array('action' => 'add', $id, 'qc_document_id' => $this->request->params['named']['qc_document_id']));
            }

            $fieldTypes = $this->CustomTable->customArray['fieldTypes'];
            
            if(isset($this->request->params['named']['qc_document_id'])){
                $table_name_version = $this->_make_table_name($this->request->data['CustomTable']['qc_document_id'],'qc_documents');

            // for processes                
            }else if(isset($this->request->params['named']['process_id'])){                
                $table_name_version = $this->_make_table_name($this->request->data['CustomTable']['process_id'],'processes');
            // if none
            }else{
                
            }

            $this->request->data['CustomTable']['table_name'] = $table_name_version[0];
            $this->request->data['CustomTable']['table_version'] = $table_name_version[1];
            $table_name = $table_name_version[0];
            //prepare sql
            $this->request->data['CustomTable']['fields'] = json_encode($this->request->data['CustomTableFields']);
            $this->request->data['CustomTable']['system_table_id'] = $this->_get_system_table_id();

            $this->CustomTable->create();
            // move new onlyoffice document to new location
            if($this->request->data['CustomTable']['qc_document_id']){
                $options = array('recursive' => - 1, 'conditions' => array('QcDocument.id' => $this->request->data['CustomTable']['qc_document_id']));
                $qcDoc = $this->CustomTable->QcDocument->find('first', $options);
                $this->set('qcDoc', $qcDoc);
                $file_type = $qcDoc['QcDocument']['file_type'];
                $file_name = $qcDoc['QcDocument']['title'];
                $document_number = $qcDoc['QcDocument']['document_number'];
                $document_version = $qcDoc['QcDocument']['revision_number'];
                // $file_name = $document_number . '-' . $file_name . '-' . $document_version . '.' . $file_type;

                $file_name = $document_number . '-' . $file_name . '-' . $document_version;
                $file_name = $this->_clean_table_names($file_name);
                $file_name = $file_name . '.' . $file_type;

                $qcpfile = WWW_ROOT .  'files' . DS . $this->Session->read('User.company_id') . DS . 'custom_tables' . DS . $this->request->data['CustomTable']['qc_document_id'] . DS . $file_name;
                if (!file_exists($qcpfile)) {
                    // $this->Session->setFlash(__('This document does not exists.'));
                    // $this->redirect(array('action' => 'index', 'qc_document_id' => $this->request->params['named']['qc_document_id']));
                }    
            }else if($this->request->data['CustomTable']['process_id']){
                $options = array('recursive' => - 1, 'conditions' => array('Process.id' => $this->request->data['CustomTable']['process_id']));
                $proDoc = $this->CustomTable->Process->find('first', $options);
                $this->set('proDoc', $proDoc);
                $file_type = $proDoc['Process']['file_type'];
                $file_name = $proDoc['Process']['file_name'];
                
                $file_name = $file_name;
                $qcpfile = WWW_ROOT .  'files' . DS . $this->Session->read('User.company_id') . DS . 'custom_tables' . DS . $this->request->data['CustomTable']['process_id'] . DS . $file_name;
                if (!file_exists($qcpfile)) {                    
                    // $this->Session->setFlash(__('This document does not exists.'));
                    // $this->redirect(array('action' => 'index', 'qc_document_id' => $this->request->params['named']['qc_document_id']));
                }
            }
            
            $newFields = array();
            foreach($this->request->data['CustomTableFields'] as $fields){
                if($fields['drop'] == 1){
                    unset($fields);
                }else{
                    $fields['who_can_edit'] = json_encode($fields['who_can_edit']);
                    if($fields['display_type'] == 7)$fields['show_comments'] = base64_encode($fields['show_comments']);
                    // $fields['field_label'] = Inflector::humanize($this->_clean_table_names($fields['field_label']));
                    $fields['field_label'] = base64_encode($fields['field_label']);
                    $newFields[] = $fields;        
                }                
            }
            
            if(is_array($newFields)){
                $this->request->data['CustomTableFields'] = $newFields;
                $this->request->data['CustomTable']['fields'] = json_encode($newFields);    
            }

            if ($this->CustomTable->save($this->request->data)) {
                
                $file = Configure::read('path') . DS . $this->CustomTable->id . DS . $file_name;
                
                if (!file_exists(Configure::read('path') . DS . $this->CustomTable->id)) {
                    $pathfolder = new Folder();
                    // create folder
                    if ($pathfolder->create(Configure::read('path') . DS . $this->CustomTable->id)) {
                        chmod(Configure::read('path') . DS . $this->CustomTable->id,0777);
                        
                        if(file_exists($qcpfile)){
                            if (copy($qcpfile, $file)) {
                                $key = $this->_generate_onlyoffice_key($this->CustomTable->id . date('Ymdhis'));
                                $this->set('file_key', $key);

                            } else {
                                $this->Session->setFlash(__('Copy file failed'));
                                $this->redirect(array('action' => 'index', 'qc_document_id' => $this->request->params['named']['qc_document_id'],'process_id' => $this->request->params['named']['process_id']));
                            }    
                        }

                        
                        
                        $rec = $this->CustomTable->find('first', array('recursive' => - 1, 'conditions' => array('CustomTable.id' => $this->CustomTable->id)));
                        $rec['CustomTable']['file_key'] = $key;
                        $this->CustomTable->create();
                        $this->CustomTable->save($rec['CustomTable']);
                        
                        if($this->request->data['CustomTable']['qc_document_id']){
                            $sqld.= "`qc_document_id` varchar(36) NOT NULL DEFAULT '" . $this->request->data['CustomTable']['qc_document_id'] . "',
                            ";
                        }else if($this->request->data['CustomTable']['process_id']){
                            $sqld.= "`process_id` varchar(36) NOT NULL DEFAULT '" . $this->request->data['CustomTable']['process_id'] . "',
                            ";
                            $sqld.= "`qc_document_id` varchar(36) NULL',
                            ";
                        }
                        
                        $sqld .= "`custom_table_id` varchar(36) NOT NULL DEFAULT '" . $this->CustomTable->id . "',
                        ";
                        
                        $friendlyName = $this->request->data['CustomTable']['name'];
                        
                        unset($this->request->data['History']);
                        $data = array($this->request->data,$table_name,$friendlyName,$defaultfield);

                        $result = $this->curl('post','custom_forms', 'create',$data);
                        $result = json_decode($result,true);
                        
                        if($result['error'] == 1){
                            echo "Something went wrong. Please try again";
                        }else{
                            $result = json_decode($result['response']['finalResult'],true);
                            
                            if($result['controller']){
                                $controller_file_name = Inflector::pluralize(Inflector::Classify($table_name)) . 'Controller.php';
                                $folder = APP . DS . 'Controller';
                                $controllerFolder = new Folder();
                                $controllerFolder->create($folder);
                                $file = $folder . DS . $controller_file_name;
                                $controllerFile = new File($file);
                                $controllerFile->create();
                                $controllerFile->open();
                                $controllerFile->write($result['controller'], 'w', true);
                                $controllerFile->close();                                    
                            }

                            if($result['model']){

                                $name = Inflector::Classify($table_name) . '.php';

                                $folder = APP . DS . 'Model';

                                $file = $folder . DS . $name;
                                $modelFile = new File($file);
                                $modelFile->create();
                                $modelFile->open();
                                $modelFile->write($result['model'], 'w', true);
                                $modelFile->close();                                    
                            }

                            $viewCode = json_decode($result['viewFile'],true);

                            if($viewCode['index']){
                                $folder = APP . DS . 'View' . DS . Inflector::pluralize(Inflector::classify($table_name));        
                                $modelFolder = new Folder();
                                $modelFolder->create($folder);
                                $file = $folder . DS . 'index.ctp';
                                $modelFile = new File($file);
                                $modelFile->create();
                                $modelFile->open();
                                $modelFile->write($viewCode['index'], 'w', true);
                                $modelFile->close();                                    
                            }

                            if($viewCode['index']){
                                $folder = APP . DS . 'View' . DS . Inflector::pluralize(Inflector::classify($table_name));        
                                $modelFolder = new Folder();
                                $modelFolder->create($folder);
                                $file = $folder . DS . 'view.ctp';
                                $modelFile = new File($file);
                                $modelFile->create();
                                $modelFile->open();
                                $modelFile->write($viewCode['view'], 'w', true);
                                $modelFile->close();
                            }

                            $addCode = json_decode($result['formFile'],true);

                            if($addCode['add']){
                                $folder = APP . DS . 'View' . DS . Inflector::pluralize(Inflector::classify($table_name));        
                                $modelFolder = new Folder();
                                $modelFolder->create($folder);
                                $file = $folder . DS . 'add.ctp';
                                $modelFile = new File($file);
                                $modelFile->create();
                                $modelFile->open();
                                $modelFile->write($addCode['add'], 'w', true);
                                $modelFile->close();
                                
                            }

                            if($addCode['edit']){
                                $folder = APP . DS . 'View' . DS . Inflector::pluralize(Inflector::classify($table_name));        
                                $modelFolder = new Folder();
                                $modelFolder->create($folder);
                                $file = $folder . DS . 'edit.ctp';
                                $modelFile = new File($file);
                                $modelFile->create();
                                $modelFile->open();
                                $modelFile->write($addCode['edit'], 'w', true);
                                $modelFile->close();
                                
                            }
                        }

                        $sqlresult = $this->_add_new_table($table_name,$defaultfield,$sqld,$this->request->data['CustomTableFields']);

                            // $this->_clear_cake_cache();
                        
                            // everything run properly -- now create table
                        if ($this->_show_approvals()) $this->_save_approvals($this->CustomTable->id);
                        $this->Session->setFlash(__('Form created'));
                        $this->redirect(array('action' => 'recreate',$this->CustomTable->id, 'qc_document_id' => $this->request->params['named']['qc_document_id'],'process_id' => $this->request->params['named']['process_id']));
                        
                    } else {
                        echo "failed folder - 1" . Configure::read('path');
                        $this->Session->setFlash(__('Custom Table id folder could not be created'));
                        $this->redirect(array('action' => 'index', 'qc_document_id' => $this->request->params['named']['qc_document_id'],'process_id' => $this->request->params['named']['process_id']));
                    }
                } else {
                    $this->Session->setFlash(__('Custom Table id folder already exixts'));
                    $this->redirect(array('action' => 'index', 'qc_document_id' => $this->request->params['named']['qc_document_id'],'process_id' => $this->request->params['named']['process_id']));
                }
                $this->redirect(array('action' => 'index', 'qc_document_id' => $this->request->params['named']['qc_document_id'],'process_id' => $this->request->params['named']['process_id']));
            } else {
                $this->Session->setFlash(__('The custom table could not be saved. Please, try again.'));
            }
        }
        if ($this->request->params['named']['qc_document_id'] || $this->request->params['named']['process_id']) {
            
            // generate table name
            // for documets
            if(isset($this->request->params['named']['qc_document_id'])){
                $qcDocument = $this->CustomTable->QcDocument->find('first', array('recursive' => 0, 'conditions' => array('QcDocument.id' => $this->request->params['named']['qc_document_id']),));
                $table_name_version = $this->_make_table_name($this->request->params['named']['qc_document_id'],'qc_documents');
                $this->set('qcDocument', $qcDocument);
                $this->set('table_name', $table_name_version[0]);
                $this->set('table_version', $table_name_version[1]);
                $this->_set_file($this->request->params['named']['qc_document_id'],'qc_documents');

            // for processes                
            }else if(isset($this->request->params['named']['process_id'])){
                $process = $this->CustomTable->Process->find('first', array('recursive' => 0, 'conditions' => array('Process.id' => $this->request->params['named']['process_id']),));
                $table_name_version = $this->_make_table_name($this->request->params['named']['process_id'],'processes');
                $this->set('process', $process);
                $this->set('table_name', $table_name_version[0]);
                $this->set('table_version', $table_name_version[1]);
                $this->_set_file($this->request->params['named']['process_id'],'processes');
            // if none
            }else{
                
            }
            
            if($qcDocument['QcDocument']['parent_document_id']){
                $parent = $this->CustomTable->find('first',array(
                    'recursive'=>-1,
                    'fields'=>array('CustomTable.id','CustomTable.fields'),
                    'conditions'=>array('CustomTable.qc_document_id'=>$qcDocument['QcDocument']['parent_document_id'])));

                if($parent){
                    $parentFields = json_decode($parent['CustomTable']['fields'],true);                    
                    foreach ($parentFields as $f) {                        
                        if($f["data_type"] == 'radio')$fieldNames[$f["field_name"]] = $f["field_name"];
                        $fields[$f["field_name"]] = $f;
                        $this->set("fieldNames", $fieldNames);
                        $this->set("fields", $fields);   
                        $this->set("parent",$parent);                     
                    }
                }

            }

            if(!$qcDocument && !$process){
                $this->Session->setFlash(__('Select Document/Process first'), 'default', array('class' => 'alert alert-warning'));
                $this->redirect(array('controller' => 'custom_tables', 'action' => 'index',$n));   
            }
            
        } else {
            $this->Session->setFlash(__('Chose document first to create table. Tables are linked with documents and document must be present before adding table.'));
            $this->redirect(array('controller' => 'qc_documents', 'action' => 'index'));
        }        
    }

    public function _set_file($id = null,$type = null){        
        // before form submit
        // create call back folder
        $folder = WWW_ROOT . 'files' . DS . $this->Session->read('company_id') . DS . 'save_doc' . DS . $id;
        $callbackFolder = new Folder();
        $callbackFolder->create($folder);
        // generate key
        // move new onlyoffice document to new location
        if($type  == 'qc_documents'){
            $options = array('recursive' => - 1, 'conditions' => array('QcDocument.id' => $this->request->params['named']['qc_document_id']));
            $qcDoc = $this->CustomTable->QcDocument->find('first', $options);
            $this->set('qcDoc', $qcDoc);    

            $file_type = $qcDoc['QcDocument']['file_type'];
            $file_name = $qcDoc['QcDocument']['title'];
            $document_number = $qcDoc['QcDocument']['document_number'];
            $document_version = $qcDoc['QcDocument']['revision_number'];
            // $file_name = $document_number . '-' . $file_name . '-' . $document_version . '.' . $file_type;

            $file_name = $document_number . '-' . $file_name . '-' . $document_version;
            $file_name = $this->_clean_table_names($file_name);
            $file_name = $file_name . '.' . $file_type;

        }else if($type == 'processes'){
            $options = array('recursive' => - 1, 'conditions' => array('Process.id' => $id));
            $process = $this->CustomTable->Process->find('first', $options);
            $this->set('process', $process);    

            $file_type = $process['Process']['file_type'];
            $file_name = $process['Process']['file_name'];
        }
        
        
        
        $qcpfile = WWW_ROOT .  'files' . DS . $this->Session->read('User.company_id') . DS . $type . DS . $id . DS . $file_name;
        $tofile = WWW_ROOT .  'files' . DS . $this->Session->read('User.company_id') . DS . 'custom_tables' . DS . $id . DS . $file_name;

        if (file_exists($qcpfile)) {
            $folder = WWW_ROOT .  'files' . DS . $this->Session->read('User.company_id') . DS . 'custom_tables' . DS . $id;
            $tmpfolder = new Folder();
            if ($tmpfolder->create($folder)) {
                if (copy($qcpfile, $tofile)) {
                } else {
                    $this->Session->setFlash(__('File copy failed'));
                    $this->redirect(array('action' => 'index', 'qc_document_id' => $this->request->params['named']['qc_document_id'],'process_id' => $this->request->params['named']['process_id']));
                }
            } else {
                $this->Session->setFlash(__('Folder creation failed'));
                $this->redirect(array('action' => 'index', 'qc_document_id' => $this->request->params['named']['qc_document_id'],'process_id' => $this->request->params['named']['process_id']));
            }
        } else {        
            $this->Session->setFlash(__('You are creating HTML form without any document.'));            
        }
        $this->set('key', $this->_generate_onlyoffice_key($id));
    }
    /**
     * add method
     *
     * @return void
     */
    public function add_child() {

        if($this->Session->read('User.is_mt') == false){
            $this->Session->setFlash(__('You are not authorized to view this section'), 'default', array('class' => 'alert alert-danger'));
            $this->redirect(array('controller' => 'users', 'action' => 'access_denied',$n));
        }

        if(empty($this->request->params['named']['qc_document_id']) && empty($this->request->params['named']['process_id'])){
            $this->Session->setFlash(__('Select Document/Process first'), 'default', array('class' => 'alert alert-warning'));
            $this->redirect(array('controller' => 'custom_tables', 'action' => 'index',$n));   
        }

        if ($this->_show_approvals()) {
            $this->loadModel('User');
            $this->User->recursive = 0;
            $userids = $this->User->find('list', array('order' => array('User.name' => 'ASC'), 'conditions' => array('User.publish' => 1, 'User.soft_delete' => 0, 'User.is_approver' => 1)));
            $this->set(array('userids' => $userids));
            $this->set(array('showApprovals' => $this->_show_approvals()));
        }
        $this->_commons($this->Session->read('User.id'));
        // afert form submit
        if ($this->request->is('post')) {            

            $this->request->data['CustomTable']['password'] = Security::hash($this->request->data['CustomTable']['password'], 'md5', true);

            foreach($this->request->data['CustomTableFields'] as $fields){
                if($fields['default_field'] == 1){
                    $defaultfield = $this->_clean_table_names($fields['field_name']);
                }
            }

            if ($defaultfield == '') {
                $this->Session->setFlash(__('Default field missing'));
                $this->redirect(array('action' => 'add_child', $id,'custom_table_id'=>$this->request->params['named']['custom_table_id'],  'qc_document_id' => $this->request->params['named']['qc_document_id']));

            }

            $fieldTypes = $this->CustomTable->customArray['fieldTypes'];
            $friendlyName = $this->request->data['CustomTable']['name'];

            if($this->request->params['named']['qc_document_id'] != ''){
                $qcDocument = $this->CustomTable->QcDocument->find('first', array('recursive' => 0, 'conditions' => array('QcDocument.id' => $this->request->params['named']['qc_document_id']),));
                $table_name_version = $this->_make_child_table_name($this->request->params['named']['qc_document_id'],'qc_documents',$customTable['CustomTable']['linked']);
            // for processes                
            }else if($this->request->params['named']['process_id'] != ''){
                $process = $this->CustomTable->Process->find('first', array('recursive' => 0, 'conditions' => array('Process.id' => $this->request->params['named']['process_id']),));
                $table_name_version = $this->_make_child_table_name($this->request->params['named']['process_id'],'processes',$customTable['CustomTable']['linked']);                
            }else{
                
            }

            
            $this->request->data['CustomTable']['table_name'] = $table_name_version[0];
            $this->request->data['CustomTable']['table_version'] = $table_name_version[1];
            $table_name = $table_name_version[0];
            //prepare sql
            $this->request->data['CustomTable']['fields'] = json_encode($this->request->data['CustomTableFields']);
            $this->request->data['CustomTable']['system_table_id'] = $this->_get_system_table_id();
            
            $this->CustomTable->create();
            
            $customTable = $this->CustomTable->find('first', array('conditions' => array('CustomTable.id' => $this->request->data['CustomTable']['custom_table_id'])));
            $hasMany = json_decode($customTable['CustomTable']['has_many'], true);
            $hasMany[] = array('table_name' => $this->request->data['CustomTable']['table_name'], 'friendly_name' => $this->request->data['CustomTable']['name'], 'table_version' => $this->request->data['CustomTable']['table_version']);
            

            $customTable['CustomTable']['has_many'] = json_encode($hasMany);            

            $newFields = array();
            foreach($this->request->data['CustomTableFields'] as $fields){
                if($fields['drop'] == 1){
                    unset($fields);
                }else{
                    $fields['who_can_edit'] = json_encode($fields['who_can_edit']);
                    $fields['show_comments'] = base64_encode($fields['show_comments']);
                    $fields['field_label'] = Inflector::humanize($this->_clean_table_names($fields['field_label']));
                    $fields['field_label'] = base64_encode($fields['field_label']);
                    $newFields[] = $fields;                    
                }                
            }
            
            if(is_array($newFields)){
                $this->request->data['CustomTable']['fields'] = json_encode($newFields);    
                $this->request->data['CustomTableFields'] = $newFields;
            }
            
            $customTable['CustomTable']['has_many_existing'] = json_encode($hasMany);
            
            if ($this->CustomTable->save($this->request->data)) {
                
                $customTable['CustomTable']['has_many_existing'] = json_encode($hasMany);
                $data = array($this->request->data,$table_name,$friendlyName,$defaultfield,$customTable);
                $result = $this->curl('post','custom_forms','create_child',$data);
                $result = json_decode($result,true);
                

                if($result['error'] == 1){
                    echo "Something went wrong. Please try again";
                }else{
                    $result = json_decode($result['response']['finalResult'],true);
                    
                    if($result['controller']){
                        $controller_file_name = Inflector::pluralize(Inflector::Classify($table_name)) . 'Controller.php';
                        $folder = APP . DS . 'Controller';
                        $controllerFolder = new Folder();
                        $controllerFolder->create($folder);
                        $file = $folder . DS . $controller_file_name;
                        $controllerFile = new File($file);
                        $controllerFile->create();
                        $controllerFile->open();
                        $controllerFile->write($result['controller'], 'w', true);
                        $controllerFile->close();                                    
                    }

                    if($result['model']){

                        $name = Inflector::Classify($table_name) . '.php';

                        $folder = APP . DS . 'Model';

                        $file = $folder . DS . $name;
                        $modelFile = new File($file);
                        $modelFile->create();
                        $modelFile->open();
                        $modelFile->write($result['model'], 'w', true);
                        $modelFile->close();                                    
                    }

                    // $viewCode = json_decode($result['viewFile'],true);

                    if($result['index']){
                        $folder = APP . DS . 'View' . DS . Inflector::pluralize(Inflector::classify($table_name));        
                        $modelFolder = new Folder();
                        $modelFolder->create($folder);
                        $file = $folder . DS . 'index.ctp';
                        $modelFile = new File($file);
                        $modelFile->create();
                        $modelFile->open();
                        $modelFile->write($result['index'], 'w', true);
                        $modelFile->close();                                    
                    }

                    $addCode = json_decode($result['formFile'],true);
                    
                    foreach($addCode as $file => $code){
                        $folder = APP . DS . 'View' . DS . Inflector::pluralize(Inflector::classify($table_name));        
                        $modelFolder = new Folder();
                        $modelFolder->create($folder);
                        $file = $folder . DS . $file.'.ctp';
                        $modelFile = new File($file);
                        $modelFile->create();
                        $modelFile->open();
                        $modelFile->write($code, 'w', true);
                        $modelFile->close();
                    }
                    
                    if($result['parentModelFile']){

                        $name = Inflector::Classify($customTable['CustomTable']['table_name']) . '.php';

                        $folder = APP . DS . 'Model';

                        $file = $folder . DS . $name;
                        $modelFile = new File($file);
                        $modelFile->create();
                        $modelFile->open();
                        $modelFile->write($result['parentModelFile'], 'w', true);
                        $modelFile->close();                                    
                    }                    
                }



                if($this->request->data['CustomTable']['qc_document_id']){
                    $sqld.= "`qc_document_id` varchar(36) NOT NULL DEFAULT '" . $this->request->data['CustomTable']['qc_document_id'] . "',
                    ";
                }else if($this->request->data['CustomTable']['process_id']){
                    $sqld.= "`process_id` varchar(36) NOT NULL DEFAULT '" . $this->request->data['CustomTable']['process_id'] . "',
                    ";
                    $sqld.= "`qc_document_id` varchar(36) NULL',
                    ";
                }
                
                $sqld.= "`custom_table_id` varchar(36) NOT NULL DEFAULT '" . $this->CustomTable->id . "',";

                $sqlresult = $this->_add_new_table($table_name,$defaultfield,$sqld,$this->request->data['CustomTableFields']);

                if ($this->_show_approvals()) $this->_save_approvals($this->CustomTable->id);
                $this->Session->setFlash(__('Form created'));
                $this->redirect(array('action' => 'index', 'qc_document_id' => $this->request->params['named']['qc_document_id']));
            } else {
                $this->Session->setFlash(__('The custom table could not be saved. Please, try again.'));
            }
        }
        

        if ($this->request->params['named']['qc_document_id'] || $this->request->params['named']['process_id']) {
            
            // generate table name
            // for documets
            if($this->request->params['named']['qc_document_id'] != ''){
                $qcDocument = $this->CustomTable->QcDocument->find('first', array('recursive' => 0, 'conditions' => array('QcDocument.id' => $this->request->params['named']['qc_document_id']),));
                $table_name_version = $this->_make_child_table_name($this->request->params['named']['qc_document_id'],'qc_documents',$customTable['CustomTable']['linked']);
                $this->set('qcDocument', $qcDocument);
                $this->set('table_name', $table_name_version[0]);
                $this->set('table_version', $table_name_version[1]);
                $this->_set_file($this->request->params['named']['qc_document_id'],'qc_documents');

            // for processes                
            }else if($this->request->params['named']['process_id'] != ''){
                $process = $this->CustomTable->Process->find('first', array('recursive' => 0, 'conditions' => array('Process.id' => $this->request->params['named']['process_id']),));
                $table_name_version = $this->_make_child_table_name($this->request->params['named']['process_id'],'processes',$customTable['CustomTable']['linked']);
                $this->set('process', $process);
                $this->set('table_name', $table_name_version[0]);
                $this->set('table_version', $table_name_version[1]);
                $this->_set_file($this->request->params['named']['process_id'],'processes');
            // if none
            }else{
                
            }

            if(!$qcDocument && !$process){
                $this->Session->setFlash(__('Select Document/Process first'), 'default', array('class' => 'alert alert-warning'));
                $this->redirect(array('controller' => 'custom_tables', 'action' => 'index',$n));   
            }
            
        } else {
            $this->Session->setFlash(__('Chose document first to create table. Tables are linked with documents and document must be present before adding table.'));
            $this->redirect(array('controller' => 'qc_documents', 'action' => 'index'));
        }
        $this->set('key', $this->_generate_onlyoffice_key($qcDocument['QcDocument']['id']));
        $customTable = $this->CustomTable->find('first',array('conditions'=>array('CustomTable.id'=>$this->request->params['named']['custom_table_id']),'recursive'=>-1));
        $this->set('customTable',$customTable);
    }
    /**
     * edit method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function get_linked_to_fields($model = null) {
        $model = $this->request->params['named']['model'];
        $this->loadModel($model);
        $this->$model->schema();
        $this->set('f', $f);
        $this->set('fields', array_keys($this->$model->schema()));
    }
    public function delete($id = null) {
        if($this->Session->read('User.is_mt') == false){
            $this->Session->setFlash(__('You are not authorized to view this section'), 'default', array('class' => 'alert alert-danger'));
            $this->redirect(array('controller' => 'users', 'action' => 'access_denied',$n));
        }

        if ($this->request->is('post') || $this->request->is('put')) {
            if (empty($this->request->data['CustomTable']['password'])) {
                $this->Session->setFlash(__('Enter password'));
                $this->redirect(array('action' => 'index'));
            } else {                
                $found = $this->check_before_delete($this->data['CustomTable']['id']);
                if(!$found){            
                    $customTable = $this->CustomTable->find('first', array('conditions' => array('CustomTable.password' => trim(Security::hash($this->data['CustomTable']['password'], 'md5', true)), 'CustomTable.id' => $this->data['CustomTable']['id'])));

                    if ($customTable) {                            
                            // check if this field exists in any other tables
                            $allTables = $this->CustomTable->find('all',array('recursive'=>-1));
                            foreach($allTables as $allTable){
                                $model = $allTable['CustomTable']['table_name'];
                                $model = Inflector::classify($model);
                                try{
                                    $this->loadModel($model);                    
                                    if(in_array(Inflector::classify($customTable['CustomTable']['name']),array_keys($this->$model->belongsTo) )){
                                        $this->Session->setFlash(__('Can not delete this table due to dependency on '. $allTable['CustomTable']['name'] . ' table'));
                                        $this->redirect(array('action' => 'view',$this->data['CustomTable']['id']));    
                                    }    
                                }catch(Exception $e){

                                }
                            }

                            if ($customTable['CustomTable']['table_locked'] == 0) {
                                $this->Session->setFlash(__('Can not recreate/delete locked table'));
                                $this->redirect(array('action' => 'index'));
                            }

                            // also delete child
                            foreach (json_decode($customTable['CustomTable']['has_many'], true) as $childs) {                        
                                // delete files
                                $folder = APP . 'Controller';
                                $name = Inflector::pluralize(Inflector::Classify($childs['table_name'])) . 'Controller.php';
                                if ($name) {
                                    unlink($folder . DS . $name);
                                }
                                $folder = APP . 'Model';
                                $name = Inflector::singularize(Inflector::Classify($childs['CustomTable']['table_name'])) . '.php';
                                if ($name) {
                                    unlink($folder . DS . $name);
                                }
                                $folder = APP . 'View';
                                $name = Inflector::pluralize(Inflector::Classify($childs['table_name']));
                                $folder = APP . 'View' . DS . Inflector::pluralize(Inflector::classify($childs['table_name']));
                                if ($folder != APP . 'View') {
                                    $modelFolder = new Folder();
                                    $modelFolder->delete($folder);
                                    $this->CustomTable->delete(array('CustomTable.id' => $id));
                                }
                                $sql = 'DROP TABLE `' . $childs['table_name'] . '`';
                                try {
                                    $q = $this->CustomTable->query($sql);
                                }
                                catch(Exception $e) {
                                }
                                $this->CustomTable->delete(array('CustomTable.table_name' => $childs['table_name']));
                            }   

                            
                            if ($customTable['CustomTable']['name']) {
                                // delete files
                                $folder = APP . 'Controller';
                                $name = Inflector::pluralize(Inflector::Classify($customTable['CustomTable']['table_name'])) . 'Controller.php';
                                if ($name) {
                                    unlink($folder . DS . $name);
                                }
                                $folder = APP . 'Model';
                                $name = Inflector::singularize(Inflector::Classify($customTable['CustomTable']['table_name'])) . '.php';
                                if ($name) {
                                    unlink($folder . DS . $name);
                                }
                                $folder = APP . 'View';
                                $name = Inflector::pluralize(Inflector::Classify($customTable['CustomTable']['table_name']));
                                $folder = APP . 'View' . DS . Inflector::pluralize(Inflector::classify($customTable['CustomTable']['table_name']));
                                if ($folder != APP . 'View') {
                                    $modelFolder = new Folder();
                                    $modelFolder->delete($folder);
                                    $this->CustomTable->delete(array('CustomTable.id' => $id));
                                }
                                
                                $path = Configure::read('files') .  DS . 'custom_tables' . DS . $customTable['CustomTable']['id'];
                                $filesFolder = new Folder($path);
                                $filesFolder->delete();

                                // delete all records from files table
                                $this->loadModel('File');
                                $this->File->delete(array('File.controller' => $customTable['CustomTable']['table_name']));
                                
                                $sql = 'DROP TABLE `' . $customTable['CustomTable']['table_name'] . '`';

                                try {
                                    $q = $this->CustomTable->query($sql);
                                }
                                catch(Exception $e) {
                                    
                                }

                                // must also delete approvals
                                $this->loadModel('Approval');
                                
                                // get child tables
                                $child_tables = $this->CustomTable->find('all',array('conditions'=>array('CustomTable.custom_table_id'=>$this->data['CustomTable']['id'])));
                                foreach($child_tables as $child_table){
                                    // delete files
                                    $folder = APP . 'Controller';
                                    $name = Inflector::pluralize(Inflector::Classify($child_table['CustomTable']['table_name'])) . 'Controller.php';
                                    if ($name) {
                                        unlink($folder . DS . $name);
                                    }
                                    $folder = APP . 'Model';
                                    $name = Inflector::singularize(Inflector::Classify($child_table['CustomTable']['table_name'])) . '.php';
                                    if ($name) {
                                        unlink($folder . DS . $name);
                                    }
                                    $folder = APP . 'View';
                                    $name = Inflector::pluralize(Inflector::Classify($child_table['CustomTable']['table_name']));
                                    $folder = APP . 'View' . DS . Inflector::pluralize(Inflector::classify($child_table['CustomTable']['table_name']));
                                    if ($folder != APP . 'View') {
                                        $modelFolder = new Folder();
                                        $modelFolder->delete($folder);
                                        $this->CustomTable->delete(array('CustomTable.id' => $id));
                                    }
                                    // delete all folders in files folder
                                    $path = Configure::read('files') . DS . 'custom_tables' . DS . $child_table['CustomTable']['id'];
                                    $filesFolder = new Folder($path);                                    
                                    $filesFolder->delete();
                                    
                                    $path = Configure::read('files') . DS . 'custom_tables' . DS . $child_table['CustomTable']['table_name'];
                                    $filesFolder = new Folder($path);
                                    $filesFolder->delete();
                                    // delete all records from files table
                                    $this->loadModel('File');
                                    $this->File->delete(array('File.controller' => $child_table['CustomTable']['table_name']));
                                    
                                    $sql = 'DROP TABLE `' . $child_table['CustomTable']['table_name'] . '`';
                                    try {                                
                                        $q = $this->CustomTable->query($sql);
                                    }
                                    catch(Exception $e) {                                        
                                    }
                                    
                                    $this->Approval->deleteAll(array('Approval.controller_name'=>$child_table['CustomTable']['table_name']),true);
                                }

                                // delete approvals

                                // delete tables from custom_tables
                                $this->CustomTable->deleteAll(array('CustomTable.custom_table_id'=>$this->data['CustomTable']['id']));


                                $this->CustomTable->delete($customTable['CustomTable']['id']);
                                $this->Approval->deleteAll(array('Approval.controller_name'=>$customTable['CustomTable']['table_name']),true);

                                $this->CustomTable->CustomTrigger->deleteAll(array('CustomTrigger.custom_table_id'=>$customTable['CustomTable']['id']));
                                $this->CustomTable->RecordLock->deleteAll(array('RecordLock.lock_table_id'=>$customTable['CustomTable']['id']));
                                $this->CustomTable->CustomTableTask->deleteAll(array('CustomTableTask.custom_table_id'=>$customTable['CustomTable']['id']));
                                $this->CustomTable->GraphPanel->deleteAll(array('GraphPanel.custom_table_id'=>$customTable['CustomTable']['id']));

                                $this->Session->setFlash(__('Table Deleted'));
                                $this->redirect(array('action' => 'index'));
                            }

                        } else {
                            $this->Session->setFlash(__('Incorrect Password'));
                            $this->redirect(array('action' => 'delete', $this->request->data['CustomTable']['id']));
                        }
                     }else{
                        $this->Session->setFlash(__('Can not delete this table.'));
                        $this->Set('found',$found);
                    }
            }            
        }        
        
    }
    public function recreate($id = null) {
        $this->_clear_cake_cache();
        if($this->Session->read('User.is_mr') == false){
            $this->Session->setFlash(__('You are not authorized to view this section'), 'default', array('class' => 'alert alert-danger'));
            $this->redirect(array('controller' => 'users', 'action' => 'access_denied',$n));
        }

        
        if ($this->request->is('post') || $this->request->is('put')) {

            
            $updatesql = '';
            $dropsql = '';
            $addsql = '';
            
            foreach($this->request->data['CustomTableFields'] as $fields){
                if($fields['default_field'] == 1){
                    $defaultfield = $this->_clean_table_names($fields['field_name']);                  
                }
            }

            if ($defaultfield == '') {
                $this->Session->setFlash(__('Default field missing'));
                $this->redirect(array('action' => 'recreate', $id, 'qc_document_id' => $this->request->params['named']['qc_document_id']));
            }

            $fieldTypes = $this->CustomTable->customArray['fieldTypes'];
            $table_name = $this->request->data['CustomTable']['table_name'];

            
            $newFields = array();
            foreach($this->request->data['CustomTableFields'] as $fields){
                if($fields['drop'] == 1){
                    $toDrop[] = $fields;
                    $sqlresult = $this->_add_new_table($table_name,$defaultfield,null,$toDrop);
                    unset($fields);
                }else{
                    if($fields['who_can_edit'])$fields['who_can_edit'] = json_encode($fields['who_can_edit']);
                    if($fields['show_comments'])$fields['show_comments'] = base64_encode($fields['show_comments']);
                    $fields['field_name'] = $this->_clean_table_names($fields['field_name']);
                    // $fields['field_label'] = Inflector::humanize($this->_clean_table_names($fields['field_label']));
                    if($fields['field_label'])$fields['field_label'] = base64_encode($fields['field_label']);
                    $newFields[] = $fields;                    
                }

                if($fields['display_type'] == 3 || $fields['display_type'] == 4){
                    $belongsTo[$fields['field_name']] = $fields['linked_to'];
                }
            }
            
            if(is_array($newFields)){
                $this->request->data['CustomTable']['fields'] = json_encode($newFields);    
                $this->request->data['CustomTableFields'] = $newFields;
            }
            

            $this->request->data['CustomTable']['system_table_id'] = $this->_get_system_table_id();
            $this->request->data['CustomTable']['password'] = Security::hash($this->request->data['CustomTable']['password'], 'md5', true);
            $this->CustomTable->create();

            $this->request->data['CustomTable']['belongs_to'] = json_encode($belongsTo);
            $belongsTo = null;            
            

            

            if ($this->CustomTable->save($this->request->data)) {

                unset($this->request->data['History']);
                unset($this->request->data['CustomTable']['pre_fields']);
                
                $friendlyName = $this->request->data['CustomTable']['name'];
                $hasMany = $this->request->data['CustomTable']['has_many'];

                //add permissions
                chmod(APP . 'Controller', 0777);
                chmod(APP . 'Model', 0777);
                chmod(APP . 'View', 0777);
                
               $dir_writable_controller = substr(sprintf('%o', fileperms(APP . 'Controller')), -4) == "0777"  ? true : false;
               $dir_writable_model = substr(sprintf('%o', fileperms(APP . 'Model')), -4) == "0777"  ? true : false;
               $dir_writable_view = substr(sprintf('%o', fileperms(APP . 'View')), -4) == "0777"  ? true : false;

               if($dir_writable_controller == false || $dir_writable_model == false || $dir_writable_view == false){
                    $this->Session->setFlash(__('Unable to change directory permissions. Please manually change app/Controller, app/Model & app/View directories to writable.(0777)'));
                    $this->redirect(array('action' => 'recreate',$this->request->data['CustomTable']['id']));
               }else{

               }

                // run update quieries
                $sqlresult = $this->_add_new_table($table_name,$defaultfield,null,$this->request->data['CustomTableFields']);
                $thisModel = Inflector::classify($table_name);
                
                $this->loadModel($thisModel);
                $findChilds = $this->CustomTable->find('all',array(
                    'conditions'=>array(
                        'CustomTable.custom_table_id'=>$this->CustomTable->id
                    ),
                    'recursive'=>-1,
                    'fields'=>array(
                        'CustomTable.id',
                        'CustomTable.table_name',
                        'CustomTable.name',
                        'CustomTable.custom_table_id',
                        'CustomTable.table_version',
                    ))
                );

                if($findChilds){
                    $hasMany = array();
                    foreach($findChilds as $findChild){                        
                        $hasMany[] = array('table_name'=>$findChild['CustomTable']['table_name'],'friendly_name'=>$findChild['CustomTable']['name'],'table_version'=>$findChild['CustomTable']['table_version']);
                    }
                    
                }
                
                $data = array($this->request->data,$table_name,$friendlyName,$defaultfield,json_encode($hasMany));
                $result = $this->curl('post','custom_forms','create',$data);
                $result = json_decode($result,true);
                
                if(!$result || $result['error'] == 1){
                    $this->Session->setFlash(__('Something went wrong. Please try again'));
                    $this->redirect(array('action' => 'recreate',$this->request->data['CustomTable']['id']));
                }else{
                    $result = json_decode($result['response']['finalResult'],true);
                    
                    if($result['controller']){
                        $controller_file_name = Inflector::pluralize(Inflector::Classify($table_name)) . 'Controller.php';
                        $folder = APP . DS . 'Controller';
                        $controllerFolder = new Folder();
                        $controllerFolder->create($folder);
                        $file = $folder . DS . $controller_file_name;
                        $controllerFile = new File($file);
                        $controllerFile->create();
                        $controllerFile->open();
                        $controllerFile->write($result['controller'], 'w', true);
                        $controllerFile->close();                                    
                    }

                    if($result['model']){

                        $name = Inflector::Classify($table_name) . '.php';

                        $folder = APP . DS . 'Model';

                        $file = $folder . DS . $name;
                        $modelFile = new File($file);
                        $modelFile->create();
                        $modelFile->open();
                        $modelFile->write($result['model'], 'w', true);
                        $modelFile->close();                                    
                    }

                    $viewCode = json_decode($result['viewFile'],true);

                    if($viewCode['index']){
                        $folder = APP . DS . 'View' . DS . Inflector::pluralize(Inflector::classify($table_name));        
                        $modelFolder = new Folder();
                        $modelFolder->create($folder);
                        $file = $folder . DS . 'index.ctp';
                        $modelFile = new File($file);
                        $modelFile->create();
                        $modelFile->open();
                        $modelFile->write($viewCode['index'], 'w', true);
                        $modelFile->close();                                    
                    }

                    if($viewCode['index']){
                        $folder = APP . DS . 'View' . DS . Inflector::pluralize(Inflector::classify($table_name));        
                        $modelFolder = new Folder();
                        $modelFolder->create($folder);
                        $file = $folder . DS . 'view.ctp';
                        $modelFile = new File($file);
                        $modelFile->create();
                        $modelFile->open();
                        $modelFile->write($viewCode['view'], 'w', true);
                        $modelFile->close();
                    }

                    $addCode = json_decode($result['formFile'],true);

                    if($addCode['add']){
                        $folder = APP . DS . 'View' . DS . Inflector::pluralize(Inflector::classify($table_name));        
                        $modelFolder = new Folder();
                        $modelFolder->create($folder);
                        $file = $folder . DS . 'add.ctp';
                        $modelFile = new File($file);
                        $modelFile->create();
                        $modelFile->open();
                        $modelFile->write($addCode['add'], 'w', true);    
                        $modelFile->close();
                        
                    }

                    if($addCode['edit']){
                        $folder = APP . DS . 'View' . DS . Inflector::pluralize(Inflector::classify($table_name));        
                        $modelFolder = new Folder();
                        $modelFolder->create($folder);
                        $file = $folder . DS . 'edit.ctp';
                        $modelFile = new File($file);
                        $modelFile->create();
                        $modelFile->open();
                        $modelFile->write($addCode['edit'], 'w', true);
                        $modelFile->close();
                        
                    }
                }

                $this->_clear_cake_cache();
                if ($this->_show_approvals()) $this->_save_approvals($this->CustomTable->id);
                
                $this->redirect(array('action' => 'recreate',$this->request->data['CustomTable']['id']));
            } else {
                $this->Session->setFlash(__('The custom table could not be saved. Please, try again.'));
            }
        }
        
        $customTable = $this->CustomTable->find('first', array('recursive' => 0, 'conditions' => array('CustomTable.id' => $id)));
        $customTable['CustomTable']['fields'] = str_replace('\/','',$customTable['CustomTable']['fields']);
        
        // find child tables
        $customTable['ChildTables'] = $this->CustomTable->find('all',array(
            'conditions'=>array('CustomTable.custom_table_id'=>$customTable['CustomTable']['id']),
            'fields'=>array('CustomTable.id','CustomTable.name','CustomTable.fields','CustomTable.table_name'),
            'recursive'=>-1)
    );
        
        $this->set('customTable', $customTable);
        $this->request->data = $customTable;
        
        $qcDocument = $this->CustomTable->QcDocument->find('first', array('recursive' => 0, 'conditions' => array('QcDocument.id' => $customTable['CustomTable']['qc_document_id']),));
        $process = $this->CustomTable->Process->find('first', array('recursive' => 0, 'conditions' => array('Process.id' => $customTable['CustomTable']['process_id']),));
        
        $this->set('qcDocument', $qcDocument);
        $this->set('process', $process);
        if ($this->_show_approvals()) {
            $this->loadModel('User');
            $this->User->recursive = 0;
            $userids = $this->User->find('list', array('order' => array('User.name' => 'ASC'), 'conditions' => array('User.publish' => 1, 'User.soft_delete' => 0, 'User.is_approver' => 1)));
            $this->set(array('userids' => $userids));
            $this->set(array('showApprovals' => $this->_show_approvals()));
        }

        $fields = json_decode($customTable['CustomTable']['fields'],true);
        foreach($fields as $field){
            $fieldDetails[] = $field;
        }        
        
        $this->set('fieldDetails', $fieldDetails);

        $key = $this->_generate_onlyoffice_key($customTable['CustomTable']['id'] . date('Ymdhis'));
        $this->set('filekey', $customTable['CustomTable']['file_key']);

        $this->_commons($this->Session->read('User.id'));  
        $this->set('linkedTosWithDisplay',$this->_returnDetaultField($fields));

        if($qcDocument['QcDocument']['parent_document_id']){
            $parent = $this->CustomTable->find('first',array(
                'recursive'=>-1,
                'fields'=>array('CustomTable.id','CustomTable.fields'),
                'conditions'=>array('CustomTable.qc_document_id'=>$qcDocument['QcDocument']['parent_document_id'])));

            if($parent){
                $parentFields = json_decode($parent['CustomTable']['fields'],true);                    
                foreach ($parentFields as $f) {
                    
                    if($f["data_type"] == 'radio')$fieldNames[$f["field_name"]] = $f["field_name"];
                    $fields[$f["field_name"]] = $f;
                    $this->set("fieldNames", $fieldNames);
                    $this->set("fields", $fields);   
                    $this->set("parent",$parent);                     
                }
            }

        }         
        

    }
    public function _get_hasManies($data = null) {
        $parent = $this->CustomTable->find('first', array('conditions' => array('CustomTable.id' => $data['CustomTable']['custom_table_id'])));
        if ($parent) {
            
            $childs = $this->CustomTable->find('all', array('recursive' => - 1, 'fields' => array('CustomTable.id', 'CustomTable.table_name', 'CustomTable.table_version', 'CustomTable.name'), 'conditions' => array('CustomTable.custom_table_id' => $parent['CustomTable']['id'])));
        }
        foreach ($childs as $child) {
            $existingHasManies[] = array('table_name' => $child['CustomTable']['table_name'], 'friendly_name' => $child['CustomTable']['name'], 'table_version' => $child['CustomTable']['table_version']);
        }
        $existingHasManies[] = array('table_name' => $data['CustomTable']['table_name'], 'friendly_name' => $data['CustomTable']['name'], 'table_version' => $data['CustomTable']['table_version']);
        $existingHasManies = array_unique($existingHasManies, SORT_REGULAR);
        $this->CustomTable->read(null, $parent['CustomTable']['id']);
        $this->CustomTable->set('has_many', json_encode($existingHasManies));
        $this->CustomTable->save();        
        return $existingHasManies;
    }
    public function recreate_child($id = null) {
        if($this->Session->read('User.is_mt') == false){
            $this->Session->setFlash(__('You are not authorized to view this section'), 'default', array('class' => 'alert alert-danger'));
            $this->redirect(array('controller' => 'users', 'action' => 'access_denied',$n));
        }

        if ($this->request->is('post') || $this->request->is('put')) {
            
            foreach ($this->request->data['CustomTableFields'] as $chkd) {
                if ($chkd['field_name']) $chkarray[] = $chkd['field_name'];
            }

            foreach($this->request->data['CustomTableFields'] as $fields){
                if($fields['default_field'] == 1){
                    $defaultfield = $this->_clean_table_names($fields['field_name']);
                    // $sqld = "`".$defaultfield."` varchar(255) NOT NULL,";
                }
            }

            if ($defaultfield == '') {
                $this->Session->setFlash(__('Default field missing'));
                $this->redirect(array('action' => 'recreate_child', $id, 'qc_document_id' => $this->request->params['named']['qc_document_id']));
            }

            $fieldTypes = $this->CustomTable->customArray['fieldTypes'];
            $table_name = $this->request->data['CustomTable']['table_name'];

            $newFields = array();            
            foreach($this->request->data['CustomTableFields'] as $fields){
                if($fields['drop'] == 1){
                    unset($fields);
                }else{
                    $fields['who_can_edit'] = json_encode($fields['who_can_edit']);
                    $fields['show_comments'] = base64_encode($fields['show_comments']);
                    $fields['field_label'] = Inflector::humanize($this->_clean_table_names($fields['field_label']));
                    // $fields['formula'] = base64_encode($fields['formula']);
                    $fields['field_label'] = base64_encode($fields['field_label']);
                    $newFields[] = $fields;
                }                
            }            

            if(is_array($newFields)){
                $this->request->data['CustomTable']['fields'] = json_encode($newFields);    
                $this->request->data['CustomTableFields'] = $newFields;
            }            
            
            $this->request->data['CustomTable']['system_table_id'] = $this->_get_system_table_id();
            $this->request->data['CustomTable']['password'] = Security::hash($this->request->data['CustomTable']['password'], 'md5', true);
            $customTable = $this->CustomTable->find('first', array('recursive'=>-1, 'conditions' => array('CustomTable.id' => $this->request->data['CustomTable']['custom_table_id'])));
            
            $hasMany = json_decode($customTable['CustomTable']['has_many'], true);
            $model = Inflector::classify($customTable['CustomTable']['table_name']);
            
            $this->loadModel($model);
            $hasManyExisting = $this->$model->hasMany;
            
            $hasMany[] = array('table_name' => $this->request->data['CustomTable']['table_name'], 'friendly_name' => $this->request->data['CustomTable']['name'], 'table_version' => $this->request->data['CustomTable']['table_version']);

            
            $this->CustomTable->create();
            if ($this->CustomTable->save($this->request->data)) {

                unset($this->request->data['History']);
                unset($this->request->data['CustomTable']['pre_fields']);
                
                $friendlyName = $this->request->data['CustomTable']['name'];
                $table_name_version = $this->request->data['CustomTable']['table_name'];
                
                $hasManyExisting = $this->_get_hasManies($this->request->data);
                
                // recreate                
                $customTable['CustomTable']['has_many_existing'] = json_encode($hasManyExisting);
                $data = array($this->request->data,$table_name,$friendlyName,$defaultfield,$customTable);
                
                $result = $this->curl('post','custom_forms','create_child',$data);
                $result = json_decode($result,true);

                //add permissions
                chmod(APP . 'Controller', 0777);
                chmod(APP . 'Model', 0777);
                chmod(APP . 'View', 0777);
                mkdir(APP . 'View' . DS . Inflector::pluralize(Inflector::classify($table_name)));
                chmod(APP . 'View' . DS . Inflector::pluralize(Inflector::classify($table_name)),0777);
                
                
                $dir_writable_controller = substr(sprintf('%o', fileperms(APP . 'Controller')), -4) == "0777"  ? true : false;
                $dir_writable_model = substr(sprintf('%o', fileperms(APP . 'Model')), -4) == "0777"  ? true : false;
                $dir_writable_view = substr(sprintf('%o', fileperms(APP . 'View' . DS . Inflector::pluralize(Inflector::classify($table_name)))), -4) == "0777"  ? true : false;

               if($dir_writable_controller == false || $dir_writable_model == false || $dir_writable_view == false){
                    $this->Session->setFlash(__('Unable to change directory permissions. Please manually change app/Controller, app/Model & app/View directories to writable.(0777)'));
                    $this->redirect(array('action' => 'recreate_child',$this->request->data['CustomTable']['id']));
               }else{

               }

                if($result['error'] == 1){
                    echo "Something went wrong. Please try again";
                }else{
                    $result = json_decode($result['response']['finalResult'],true);
                    
                    if($result['controller']){
                        $controller_file_name = Inflector::pluralize(Inflector::Classify($table_name)) . 'Controller.php';
                        $folder = APP . DS . 'Controller';
                        $controllerFolder = new Folder();
                        $controllerFolder->create($folder);
                        $file = $folder . DS . $controller_file_name;
                        $controllerFile = new File($file);
                        $controllerFile->create();
                        $controllerFile->open();
                        $controllerFile->write($result['controller'], 'w', true);
                        $controllerFile->close();                                    
                    }

                    if($result['model']){

                        $name = Inflector::Classify($table_name) . '.php';

                        $folder = APP . DS . 'Model';

                        $file = $folder . DS . $name;
                        $modelFile = new File($file);
                        $modelFile->create();
                        $modelFile->open();
                        $modelFile->write($result['model'], 'w', true);
                        $modelFile->close();                                    
                    }

                    $viewCode = json_decode($result['viewFile'],true);

                    if($result['index']){
                        $folder = APP . DS . 'View' . DS . Inflector::pluralize(Inflector::classify($table_name));        
                        $modelFolder = new Folder();
                        $modelFolder->create($folder);
                        $file = $folder . DS . 'index.ctp';
                        $modelFile = new File($file);
                        $modelFile->create();
                        $modelFile->open();
                        $modelFile->write($result['index'], 'w', true);
                        $modelFile->close();                                    
                    }

                    $addCode = json_decode($result['formFile'],true);

                    foreach($addCode as $file => $code){
                        $folder = APP . DS . 'View' . DS . Inflector::pluralize(Inflector::classify($table_name));        
                        $modelFolder = new Folder();
                        $modelFolder->create($folder);
                        $file = $folder . DS . $file.'.ctp';
                        $modelFile = new File($file);
                        $modelFile->create();
                        $modelFile->open();
                        $modelFile->write($code, 'w', true);
                        $modelFile->close();
                    }                    

                    if($result['parentModelFile']){

                        $name = Inflector::Classify($customTable['CustomTable']['table_name']) . '.php';

                        $folder = APP . DS . 'Model';

                        $file = $folder . DS . $name;
                        $modelFile = new File($file);
                        $modelFile->create();
                        $modelFile->open();
                        $modelFile->write($result['parentModelFile'], 'w', true);
                        $modelFile->close();                                    
                    }                    
                }

                $this->CustomTable->create();
                $this->CustomTable->save($customTable);


                $sqlresult = $this->_add_new_table($customTable['CustomTable']['table_name'],$defaultfield,$sqld,$this->request->data['CustomTableFields']);

                
                if ($this->_show_approvals()) $this->_save_approvals($this->CustomTable->id);
                $this->redirect(array('action' => 'recreate_child',$this->request->params['pass'][0]));
            } else {
                $this->Session->setFlash(__('The custom table could not be saved. Please, try again.'));
            }
        }
        $customTable = $this->CustomTable->find('first', array('recursive' => 0, 'conditions' => array('CustomTable.id' => $id)));
        $this->set('customTable', $customTable);
        $this->request->data = $customTable;
        
        if ($this->request->params['named']['qc_document_id'] || $this->request->params['named']['process_id']) {
            
            // generate table name
            // for documets
            if($this->request->params['named']['qc_document_id'] != ''){
                $qcDocument = $this->CustomTable->QcDocument->find('first', array('recursive' => 0, 'conditions' => array('QcDocument.id' => $this->request->params['named']['qc_document_id']),));
                $this->set('qcDocument', $qcDocument);
            // for processes                
            }else if($this->request->params['named']['process_id'] != ''){
                $process = $this->CustomTable->Process->find('first', array('recursive' => 0, 'conditions' => array('Process.id' => $this->request->params['named']['process_id']),));
                $this->set('process', $process);                
            }else{
                
            }
            
        } else {
            // $this->Session->setFlash(__('Chose document first to create table. Tables are linked with documents and document must be present before adding table.'));
            // $this->redirect(array('controller' => 'qc_documents', 'action' => 'index'));
        }

        $qcDocument = $this->CustomTable->QcDocument->find('first', array('recursive' => 0, 'conditions' => array('QcDocument.id' => $customTable['CustomTable']['qc_document_id']),));
        $this->set('qcDocument', $qcDocument);
        if ($this->_show_approvals()) {
            $this->loadModel('User');
            $this->User->recursive = 0;
            $userids = $this->User->find('list', array('order' => array('User.name' => 'ASC'), 'conditions' => array('User.publish' => 1, 'User.soft_delete' => 0, 'User.is_approver' => 1)));
            $this->set(array('userids' => $userids));
            $this->set(array('showApprovals' => $this->_show_approvals()));
        }

        $key = $this->_generate_onlyoffice_key($customTable['CustomTable']['id'] . date('Ymdhis'));
        $this->set('filekey', $key);

        $this->_commons($this->Session->read('User.id'));
    }
    
    public function save_rec_doc() {
        $this->autoRender = false;
        $local = $this->request->params['named'];
        
        if (($body_stream = file_get_contents("php://input")) === FALSE) {
            echo "Bad Request";
        }
        $data = json_decode($body_stream, TRUE);
        
        if ($data["status"] == 2) {
            
            $data = json_decode($body_stream, TRUE);
            $record_id = $this->request->params['named']['record_id'];
            $path_for_save = WWW_ROOT . 'files' . DS . $local['company_id'] . DS . 'files' . DS . $local['record_id'];
            
            // $qcdoc = $this->CustomTable->QcDocument->find('first', array('recursive' => - 1, 'conditions' => array('QcDocument.id' => $this->request->params['named']['record_id'])));
            $this->loadModel('File');
            $file = $this->File->find('first',array('conditions'=>array('File.id'=>$local['record_id'])));
            if($file){            
                $file_type = $file['File']['file_type'];
                $file_name = $file['File']['name'];
                
                $file_name = $this->_clean_table_names($file_name);
                $file_name = $file_name . '.' . $file_type;

                $file_for_save = $path_for_save . DS . $file_name;
                $testfolder = new Folder($path_for_save,true, 0777);
                $testfolder->create($path_for_save);                
                try{
                    chmod($path_for_save, 0777);    
                }catch (Exception $e){
                    
                }

                try{
                    chmod($file_for_save, 0777);
                }catch (Exception $e){
                    
                }
                
                
                $downloadUri = $data["url"];

                if (file_get_contents($downloadUri) === FALSE) {
                    
                } else {
                    $new_data = file_get_contents($downloadUri);
                    if (file_put_contents($file_for_save, $new_data)) {
                        $key = $this->_generate_onlyoffice_key($record_id . date('Ymdhis'));

                        $updates = json_decode($file['File']['version_keys'],true);
                        if(is_array($updates)){
                            $last_modified = date('Y-m-d H:i:s');
                            $updates[] = array('version_key'=>$key,'modified'=>$last_modified,'by'=>$local['user']);
                        }else{
                            $last_modified = date('Y-m-d H:i:s');
                            $updates[] = array('version_key'=>$key,'modified'=>$last_modified,'by'=>$local['user']);
                        }

                        $file['File']['version_keys'] = json_encode($updates);

                        $file['File']['data_received'] = 'from save_doc custom_tables';
                        $file['File']['file_key'] = $key;
                        $file['File']['file_status'] = 1;  
                        $file['File']['last_saved'] = $last_modified;
                        $this->File->create();
                        $this->File->save($file,false);

                    } else {
                        
                    }
                    
                }
                
            }
            
        }
        echo "{\"error\":0}";
    }
    public function save_custom_table_doc() {
        $this->autoRender = false;
        $local = $this->request->params['named'];
        
        if (($body_stream = file_get_contents("php://input")) === FALSE) {
            echo "Bad Request";
        }
        $data = json_decode($body_stream, TRUE);
        
        if ($data["status"] == 2) {
            $data = json_decode($body_stream, TRUE);
            $record_id = $this->request->params['named']['record_id'];
            $path_for_save = WWW_ROOT . 'files' . DS . $local['company_id'] . DS . 'custom_tables' . DS . $local['record_id'];
            
            // $qcdoc = $this->CustomTable->QcDocument->find('first', array('recursive' => - 1, 'conditions' => array('QcDocument.id' => $this->request->params['named']['record_id'])));
            $this->loadModel('CustomTable');
            $customTable = $this->CustomTable->find('first',array('conditions'=>array('CustomTable.id'=>$local['record_id'])));
            if($customTable){

                $file_type = $customTable['QcDocument']['file_type'];
                $file_name = $customTable['QcDocument']['title'];
                $document_number = $customTable['QcDocument']['document_number'];
                $document_version = $customTable['QcDocument']['revision_number'];

                // $fileName = $document_number.'-'.$file_name.'-'.$document_version.'.'.$file_type;

                $file_name = $document_number . '-' . $file_name . '-' . $document_version;
                $file_name = $this->_clean_table_names($file_name);
                $fileName = $file_name . '.' . $file_type;

                $file_for_save = $path_for_save . DS . $fileName;
                $testfolder = new Folder($path_for_save);
                $testfolder->create($path_for_save);
                chmod($path_for_save, 0777);
                chmod($file_for_save, 0777);
                $downloadUri = $data["url"];


                if (file_get_contents($downloadUri) === FALSE) {
                    
                } else {
                    $new_data = file_get_contents($downloadUri);
                    if (file_put_contents($file_for_save, $new_data)) {
                        
                        $key = $this->_generate_onlyoffice_key($record_id . date('Ymdhis'));

                        $updates = json_decode($customTable['CustomTable']['version_keys'],true);
                        if(is_array($updates)){
                            $last_modified = date('Y-m-d H:i:s');
                            $updates[] = array('version_key'=>$key,'modified'=>$last_modified,'by'=>$local['user']);
                        }else{
                            $last_modified = date('Y-m-d H:i:s');
                            $updates[] = array('version_key'=>$key,'modified'=>$last_modified,'by'=>$local['user']);
                        }

                        $customTable['CustomTable']['version_keys'] = json_encode($updates);

                        $customTable['CustomTable']['file_key'] = $key;
                        $customTable['CustomTable']['file_status'] = 1;  
                        $customTable['CustomTable']['last_saved'] = $last_modified;                        
                        $this->CustomTable->create();
                        $this->CustomTable->save($customTable,false);

                    } else {
                        
                    }
                    
                }
                
            }
            
        }
        echo "{\"error\":0}";
    }
    public function unlock($id = null) {
        if ($this->request->is('post') || $this->request->is('put')) {
            if (empty($this->request->data['CustomTable']['password'])) {
                $this->Session->setFlash(__('Enter password'));
                $this->redirect(array('action' => 'index'));
            } else {
                $customTable = $this->CustomTable->find('first', array('conditions' => array('CustomTable.password' => trim(Security::hash($this->data['CustomTable']['password'], 'md5', true)), 'CustomTable.id' => $this->data['CustomTable']['id'])));
                if ($customTable) {
                    $this->CustomTable->read(null, $this->data['CustomTable']['id']);
                    $this->CustomTable->set('table_locked', 1);
                    $this->CustomTable->save();
                    $this->Session->setFlash(__('Table unlocked'));
                    $this->redirect(array('action' => 'view',$this->data['CustomTable']['id']));
                } else {
                    $this->Session->setFlash(__('Incorrect Password'));
                    $this->redirect(array('action' => 'view',$this->data['CustomTable']['id']));
                }
            }
        } else {
            $customTable = $this->CustomTable->find('first', array('conditions' => array('CustomTable.id' => $id)));
            if ($customTable) {
            }
        }
    }
    public function lock($id = null) {
        if ($this->request->is('post') || $this->request->is('put')) {
            if (empty($this->request->data['CustomTable']['password'])) {
                $this->Session->setFlash(__('Enter password'));
                $this->redirect(array('action' => 'index'));
            } else {
                $customTable = $this->CustomTable->find('first', array('conditions' => array('CustomTable.password' => trim(Security::hash($this->data['CustomTable']['password'], 'md5', true)), 'CustomTable.id' => $this->data['CustomTable']['id'])));
                if ($customTable) {
                    $this->CustomTable->read(null, $this->data['CustomTable']['id']);
                    $this->CustomTable->set('table_locked', 0);
                    $this->CustomTable->save();
                    $this->Session->setFlash(__('Table locked'));
                    $this->redirect(array('action' => 'view',$this->data['CustomTable']['id']));
                } else {
                    $this->Session->setFlash(__('Incorrect Password'));
                    $this->redirect(array('action' => 'view',$this->data['CustomTable']['id']));
                }
            }
        } else {
            $customTable = $this->CustomTable->find('first', array('conditions' => array('CustomTable.id' => $id)));
            if ($customTable) {
            }
        }
    }
    public function publish($id = null) {
        if($this->Session->read('User.is_mt') == false){
            $this->Session->setFlash(__('You are not authorized to view this section'), 'default', array('class' => 'alert alert-danger'));
            $this->redirect(array('controller' => 'users', 'action' => 'access_denied',$n));
        }

        if ($this->request->is('post') || $this->request->is('put')) {
            if (empty($this->request->data['CustomTable']['password'])) {
                $this->Session->setFlash(__('Enter password'));
                $this->redirect(array('action' => 'index'));
            } else {
                $customTable = $this->CustomTable->find('first', array('conditions' => array('CustomTable.password' => trim(Security::hash($this->data['CustomTable']['password'], 'md5', true)), 'CustomTable.id' => $this->data['CustomTable']['id'])));
                if ($customTable) {
                    $this->CustomTable->read(null, $this->data['CustomTable']['id']);
                    $this->CustomTable->set('publish', 1);
                    $this->CustomTable->save();
                    $this->Session->setFlash(__('Table published'));
                    $this->redirect(array('action' => 'view',$this->data['CustomTable']['id']));
                } else {
                    $this->Session->setFlash(__('Incorrect Password'));
                    $this->redirect(array('action' => 'view',$this->data['CustomTable']['id']));
                }
            }
        } else {
            $customTable = $this->CustomTable->find('first', array('conditions' => array('CustomTable.id' => $id)));
            if ($customTable) {
            }
        }
    }
    public function hold($id = null) {
        if($this->Session->read('User.is_mt') == false){
            $this->Session->setFlash(__('You are not authorized to view this section'), 'default', array('class' => 'alert alert-danger'));
            $this->redirect(array('controller' => 'users', 'action' => 'access_denied',$n));
        }

        if ($this->request->is('post') || $this->request->is('put')) {
            if (empty($this->request->data['CustomTable']['password'])) {
                $this->Session->setFlash(__('Enter password'));
                $this->redirect(array('action' => 'index'));
            } else {
                $customTable = $this->CustomTable->find('first', array('conditions' => array('CustomTable.password' => trim(Security::hash($this->data['CustomTable']['password'], 'md5', true)), 'CustomTable.id' => $this->data['CustomTable']['id'])));
                if ($customTable) {
                    $this->CustomTable->read(null, $this->data['CustomTable']['id']);
                    $this->CustomTable->set('publish', 0);
                    $this->CustomTable->save();
                    $this->Session->setFlash(__('Table unpublished'));
                    $this->redirect(array('action' => 'view',$this->data['CustomTable']['id']));
                } else {
                    $this->Session->setFlash(__('Incorrect Password'));
                    $this->redirect(array('action' => 'view',$this->data['CustomTable']['id']));
                }
            }
        } else {
            $customTable = $this->CustomTable->find('first', array('conditions' => array('CustomTable.id' => $id)));
            if ($customTable) {
            }
        }
    }
    public function delete_child($id = null) {
        if($this->Session->read('User.is_mt') == false){
            $this->Session->setFlash(__('You are not authorized to view this section'), 'default', array('class' => 'alert alert-danger'));
            $this->redirect(array('controller' => 'users', 'action' => 'access_denied',$n));
        }

        if ($this->request->is('post') || $this->request->is('put')) {
            if (empty($this->request->data['CustomTable']['password'])) {
                $this->Session->setFlash(__('Enter password'));
                $this->redirect(array('action' => 'index'));
            } else {
                $customTable = $this->CustomTable->find('first', array('conditions' => array('CustomTable.password' => trim(Security::hash($this->data['CustomTable']['password'], 'md5', true)), 'CustomTable.id' => $this->data['CustomTable']['id'])));
                if ($customTable) {
                    if(isset($customTable['ParentTable']['has_many']) && is_array($customTable['ParentTable']['has_many'])){
                        $hasManies = array_unique(json_decode($customTable['ParentTable']['has_many'], true), SORT_REGULAR);
                        
                        foreach ($hasManies as $hasMany) {
                            if ($hasMany['table_name'] != $customTable['CustomTable']['table_name']) $newHasMany[] = $hasMany;
                        }
                    }
                    
                    // first remove hasMany from parent table
                    $this->CustomTable->read(null, $customTable['CustomTable']['custom_table_id']);
                    $this->CustomTable->set('has_many', json_encode($newHasMany));
                    $this->CustomTable->save();
                    
                    $parentTable = $this->CustomTable->find('first', array('recursive' => - 1, 'conditions' => array('CustomTable.id' => $customTable['CustomTable']['custom_table_id'])));
                    
                    $this->CreateChildModel->_create_model($parentTable, $parentTable['CustomTable']['table_name'], $parentTable['CustomTable']['name'], $parentTable['CustomTable']['default_field'], $newHasMany, null);
                    if ($customTable['CustomTable']['table_locked'] == 0) {
                        $this->Session->setFlash(__('Can not recreate/delete locked table'));
                        $this->redirect(array('action' => 'index'));
                    }
                    if ($customTable['CustomTable']['name']) {
                        // delete files
                        $folder = APP . 'Controller';
                        $name = Inflector::pluralize(Inflector::Classify($customTable['CustomTable']['table_name'])) . 'Controller.php';
                        if ($name) {
                            unlink($folder . DS . $name);
                        }
                        $folder = APP . 'Model';
                        $name = Inflector::singularize(Inflector::Classify($customTable['CustomTable']['table_name'])) . '.php';
                        if ($name) {
                            unlink($folder . DS . $name);
                        }
                        $folder = APP . 'View';
                        $name = Inflector::pluralize(Inflector::Classify($customTable['CustomTable']['table_name']));
                        $folder = APP . 'View' . DS . Inflector::pluralize(Inflector::classify($customTable['CustomTable']['table_name']));
                        if ($folder != APP . 'View') {
                            $modelFolder = new Folder();
                            $modelFolder->delete($folder);
                            $this->CustomTable->delete(array('CustomTable.id' => $id));
                        }
                        // delete all folders in files folder
                        $filesFolder = new Folder();
                        $path = WWW_ROOT .  'files' . DS . $this->Session->read('User.company_id') . DS . $customTable['CustomTable']['id'];
                        $filesFolder->delete($path);
                        $filesFolder = new Folder();
                        $path = WWW_ROOT .  'files' . DS . $this->Session->read('User.company_id') . DS . $customTable['CustomTable']['table_name'];
                        $filesFolder->delete($path);
                        // delete all records from files table
                        $this->loadModel('File');
                        $this->File->delete(array('File.controller' => $customTable['CustomTable']['table_name']));
                        $sql = 'DROP TABLE IF EXISTS  `' . $customTable['CustomTable']['table_name'] . '`';
                        
                        $q = $this->CustomTable->query($sql);
                        $this->CustomTable->delete($customTable['CustomTable']['id']);
                        $this->Session->setFlash(__('Table Deleted'));
                        $this->redirect(array('action' => 'index'));
                    }
                } else {
                    $this->Session->setFlash(__('Incorrect Password'));
                    $this->redirect(array('action' => 'delete', $this->data['CustomTable']['id']));
                }
            }
        }        
    }
    public function update_field() {
        if($this->Session->read('User.is_mt') == false){
            $this->Session->setFlash(__('You are not authorized to view this section'), 'default', array('class' => 'alert alert-danger'));
            $this->redirect(array('controller' => 'users', 'action' => 'access_denied',$n));
        }

        if ($this->data['CustomTableFields'][0]) {
            $this->_commons($this->Session->read('User.id'));
            $customTable = $this->CustomTable->find('first', array('conditions' => array(
                
                'CustomTable.id' => $this->request->params['named']['custom_table_id'])));
            if (!$customTable) {
                $this->Session->setFlash(__('Incorrect Password'));
                $this->redirect(array('action' => 'recreate', $this->request->params['named']['custom_table_id']));
            }
            $preFields = json_decode($this->data['CustomTableFields'][0]['pre_fields'], true);
            
            $fieldTypes = $this->CustomTable->customArray['fieldTypes'];
            if ($this->data['CustomTableFields'][0]['mandetory'] == 1) $n = " NULL";
            else if ($this->data['CustomTableFields'][0]['mandetory'] == 0) $n = " NOT NULL";
            $friendlyName = $customTable['CustomTable']['name'];
            $hasMany = $customTable['CustomTable']['has_many'];
            $table_name = $customTable['CustomTable']['table_name'];
            $defaultfield = $defaultfield;
            if ($preFields['field_type'] == 0 or $preFields['field_type'] == 2 or $preFields['field_type'] == 3 or $preFields['field_type'] == 4) {
                $sql = "ALTER TABLE `" . $table_name . "` CHANGE `" . $preFields['field_name'] . "` `" . $this->data['CustomTableFields'][0]['field_name'] . "` " . $fieldTypes[$preFields['field_type']] . "(" . $this->data['CustomTableFields'][0]['length'] . ")" . $n;
            } else {
                $sql = "ALTER TABLE `" . $table_name . "` CHANGE `" . $preFields['field_name'] . "` `" . $this->data['CustomTableFields'][0]['field_name'] . "` " . $fieldTypes[$preFields['field_type']] . " " . $n;
            }
            $existingFields = json_decode($customTable['CustomTable']['fields'], true);
            $x = 0;
            $newFields = array();
            foreach ($existingFields as $existingField) {
                if ($existingField['field_name'] == $this->request->params['named']['field']) {
                    // change this field
                    $newFields[$x] = $existingField;
                    $newFields[$x]['field_name'] = $this->data['CustomTableFields'][0]['field_name'];
                    $newFields[$x]['length'] = $this->data['CustomTableFields'][0]['length'];
                    $newFields[$x]['size'] = $this->data['CustomTableFields'][0]['size'];
                    $newFields[$x]['mandetory'] = $this->data['CustomTableFields'][0]['mandetory'];
                    $newFields[$x]['csvoptions'] = $this->data['CustomTableFields'][0]['csvoptions'];
                } else {
                    $newFields[$x] = $existingField;
                }
                $x++;
            }
            // save json and then update views and model and controller
            $customTable['CustomTable']['fields'] = json_encode($newFields);
            $this->CustomTable->create();
            $this->CustomTable->save($customTable, true);
            if ($customTable['CustomTable']['default_field'] == 0) {
                $defaultfield = 'name';
                foreach ($newFields as $chkd) {
                    if (in_array('name', $chkarray)) {
                        $this->Session->setFlash(__('There are duplicate fields'));
                        $this->redirect(array('action' => 'add', $id, 'qc_document_id' => $this->request->params['named']['qc_document_id']));
                    }
                }
            }
            if ($customTable['CustomTable']['default_field'] == 1) {
                $defaultfield = 'title';
                foreach ($newFields as $chkd) {
                    if (in_array('title', $chkarray)) {
                        $this->Session->setFlash(__('There are duplicate fields'));
                        $this->redirect(array('action' => 'add', $id, 'qc_document_id' => $this->request->params['named']['qc_document_id']));
                    }
                }
            }

            if ($customTable['CustomTable']['default_field'] == 2) {
                $defaultfield = $customTable['CustomTable']['default_field_other'];
                $defaultfield = $this->_clean_table_names($defaultfield);
                
                foreach ($newFields as $chkd) {
                    if (in_array($defaultfield, $chkarray)) {
                        $this->Session->setFlash(__('There are duplicate fields'));
                        $this->redirect(array('action' => 'recreate', $id, 'qc_document_id' => $this->request->params['named']['qc_document_id']));
                    }
                }                
            }

            $customTable['CustomTableFields'] = $newFields;

            $this->CustomTable->query($sql);
            $this->Session->setFlash(__('Changes Saved.'));
            $this->redirect(array('action' => 'recreate', $customTable['CustomTable']['id']));
        } else {
            $this->set('existingField', $this->data);
            $this->_commons($this->Session->read('User.id'));
        }
    }
    public function update_child_field() {
        if($this->Session->read('User.is_mt') == false){
            $this->Session->setFlash(__('You are not authorized to view this section'), 'default', array('class' => 'alert alert-danger'));
            $this->redirect(array('controller' => 'users', 'action' => 'access_denied',$n));
        }
        
        if ($this->data['CustomTableFields'][0]) {
            $this->_commons($this->Session->read('User.id'));
            $customTable = $this->CustomTable->find('first', array('conditions' => array('CustomTable.password' => trim(Security::hash($this->data['CustomTableFields'][0]['password'], 'md5', true)), 'CustomTable.id' => $this->request->params['named']['custom_table_id'])));
            if (!$customTable) {
                $this->Session->setFlash(__('Incorrect Password'));
                $this->redirect(array('action' => 'recreate_child', $this->request->params['named']['custom_table_id']));
            }
            $preFields = json_decode($this->data['CustomTableFields'][0]['pre_fields'], true);
            
            $fieldTypes = $this->CustomTable->customArray['fieldTypes'];
            if ($this->data['CustomTableFields'][0]['mandetory'] == 1) $n = " NULL";
            else if ($this->data['CustomTableFields'][0]['mandetory'] == 0) $n = " NOT NULL";
            $friendlyName = $customTable['CustomTable']['name'];
            $hasMany = $customTable['CustomTable']['has_many'];
            $table_name = $customTable['CustomTable']['table_name'];
            $defaultfield = $defaultfield;
            if ($preFields['field_type'] == 0 or $preFields['field_type'] == 2 or $preFields['field_type'] == 3 or $preFields['field_type'] == 4) {
                $sql = "ALTER TABLE `" . $table_name . "` CHANGE `" . $preFields['field_name'] . "` `" . $this->data['CustomTableFields'][0]['field_name'] . "` " . $fieldTypes[$preFields['field_type']] . "(" . $this->data['CustomTableFields'][0]['length'] . ")" . $n;
            } else {
                $sql = "ALTER TABLE `" . $table_name . "` CHANGE `" . $preFields['field_name'] . "` `" . $this->data['CustomTableFields'][0]['field_name'] . "` " . $fieldTypes[$preFields['field_type']] . " " . $n;
            }
            $existingFields = json_decode($customTable['CustomTable']['fields'], true);
            $x = 0;
            $newFields = array();
            foreach ($existingFields as $existingField) {
                if ($existingField['field_name'] == $this->request->params['named']['field']) {
                    // change this field
                    $newFields[$x] = $existingField;
                    $newFields[$x]['field_name'] = $this->data['CustomTableFields'][0]['field_name'];
                    $newFields[$x]['length'] = $this->data['CustomTableFields'][0]['length'];
                    $newFields[$x]['size'] = $this->data['CustomTableFields'][0]['size'];
                    $newFields[$x]['mandetory'] = $this->data['CustomTableFields'][0]['mandetory'];
                    $newFields[$x]['csvoptions'] = $this->data['CustomTableFields'][0]['csvoptions'];
                } else {
                    $newFields[$x] = $existingField;
                }
                $x++;
            }
            // save json and then update views and model and controller
            $customTable['CustomTable']['fields'] = json_encode($newFields);
            $this->CustomTable->create();
            $this->CustomTable->save($customTable, true);
            if ($customTable['CustomTable']['default_field'] == 0) {
                $defaultfield = 'name';
                foreach ($newFields as $chkd) {
                    if (in_array('name', $chkarray)) {
                        $this->Session->setFlash(__('There are duplicate fields'));
                        $this->redirect(array('action' => 'add', $id, 'qc_document_id' => $this->request->params['named']['qc_document_id']));
                    }
                }
            }
            if ($customTable['CustomTable']['default_field'] == 1) {
                $defaultfield = 'title';
                foreach ($newFields as $chkd) {
                    if (in_array('title', $chkarray)) {
                        $this->Session->setFlash(__('There are duplicate fields'));
                        $this->redirect(array('action' => 'add', $id, 'qc_document_id' => $this->request->params['named']['qc_document_id']));
                    }
                }
            }

            if ($customTable['CustomTable']['default_field'] == 2) {
                $defaultfield = $customTable['CustomTable']['default_field_other'];
                $defaultfield = $this->_clean_table_names($defaultfield);
                
                foreach ($newFields as $chkd) {
                    if (in_array($defaultfield, $chkarray)) {
                        $this->Session->setFlash(__('There are duplicate fields'));
                        $this->redirect(array('action' => 'recreate', $id, 'qc_document_id' => $this->request->params['named']['qc_document_id']));
                    }
                }                
            }

            $customTable['CustomTableFields'] = $newFields;
            
            $this->CreateChildController->_create_controller($this->request->data, $table_name, $friendlyName);
            // this should only update haMany
            // $hasMany = $table_name_version;
            $this->CreateChildModel->_create_model($customTable, $customTable['CustomTable']['table_name'], $customTable['CustomTable']['name'], $defaultfield, $hasMany, $hasManyExisting);
            
            $this->CreateChildForm->_create_form($customTable, $customTable['CustomTable']['table_name'], $customTable['CustomTable']['name'], $defaultfield, null, null);
            $this->CreateChildView->_create_view($this->request->data, $this->request->data['CustomTable']['table_name'], $this->request->data['CustomTable']['name'], $defaultfield, null, null);
            $this->CustomTable->query($sql);
            $this->Session->setFlash(__('Changes Saved.'));
            $this->redirect(array('action' => 'recreate_child', $customTable['CustomTable']['id']));
        } else {
            $this->set('existingField', $this->data);
            $this->_commons($this->Session->read('User.id'));
        }
    }
    public function reports() {
        $this->render('/Elements/reports');
        $this->_commons($this->Session->read('User.id'));
    }
    public function last_updated_record($table_name = null, $user_id = null) {
        $model = Inflector::classify($table_name);            
        try{
            // echo "asd";
            $this->loadModel($model);
            $last_updated = $this->$model->find('first', array('fields' => array($model . '.created', $model . '.id'), 'conditions' => array($model . '.created_by' => $this->Session->read('User.id')), 'order' => array($model . '.created' => 'DESC')));
            if ($last_updated) {
                return $last_updated[$model];
            } else {
                return false;
            }    
        } catch (Exception $e){

        }

        
    }

    public function create_master(){

        if ($this->request->is('post') || $this->request->is('put')) {
            
            $this->autoRender = false;

            $this->request->data['CustomTable']['password'] = Security::hash($this->request->data['CustomTable']['password'], 'md5', true);
            $tableName = $this->_make_table_name(null,'masters');
            // check if table already exists
            $checkExisting = $this->CustomTable->find('count',array('conditions'=>array('CustomTable.table_name'=>$tableName[0])));
            if($checkExisting > 0){
                echo "Table already exists";
                return false;
            }
            $table_name = $tableName[0];
            $friendlyName = $this->request->data['CustomTable']['table_name'];

            $this->request->data['CustomTable']['table_name'] = $tableName[0];
            $this->request->data['CustomTable']['table_version'] = $tableName[1];
            $this->request->data['CustomTable']['table_type'] = 2;
            $this->request->data['CustomTable']['display_field'] = 0;
            $this->request->data['CustomTable']['default_field'] = 0;
            $this->request->data['CustomTable']['form_layout'] = 0;
            $this->request->data['CustomTable']['table_locked'] = 0;

            $this->request->data['CustomTable']['fields'] = '[{"dummy":"","field_name":"name","field_label":"Name","old_field_name":"name","linked_to":"-1","display_type":"0","field_type":"0","length":"255","size":"12","data_type":"text","mandatory":"1","default_field":"1","index_show":"1","drop":"0","new":"0","sequence":"","add_disabled":"0","who_can_edit":"\"\"","show_comments":""}]';
            
            $this->CustomTable->create();
            if($this->CustomTable->save($this->request->data,false)){
                
                $customTable = $this->CustomTable->find('first', array('conditions' => array('CustomTable.id' => $this->CustomTable->id)));                
                
                $defaultfield = 'name';
                $data = array($this->request->data,$table_name,$friendlyName,$defaultfield);
                $result = $this->curl('post','custom_forms','create_masters',$data);            
                $result = json_decode($result,true);



                if($result['error'] == 1 || $result == null){
                    echo "Something went wrong. Please try again";
                }else{
                    $this->_clear_cake_cache();
                    Configure::write('debug',2);

                    $result = json_decode($result['response']['finalResult'],true);

                    $viewCode = json_decode($result['controller_model'],true);
                    
                    if($viewCode['controller']){

                        $controller_file_name = Inflector::pluralize(Inflector::Classify($table_name)) . 'Controller.php';
                        $folder = APP . DS . 'Controller';
                        $controllerFolder = new Folder();
                        $controllerFolder->create($folder);
                        $file = $folder . DS . $controller_file_name;
                        $controllerFile = new File($file);
                        $controllerFile->create();
                        $controllerFile->open();
                        $controllerFile->write($viewCode['controller'], 'w', true);
                        $controllerFile->close();                                    
                    }

                    if($viewCode['model']){

                        $name = Inflector::Classify($table_name) . '.php';

                        $folder = APP . DS . 'Model';

                        $file = $folder . DS . $name;
                        $modelFile = new File($file);
                        $modelFile->create();
                        $modelFile->open();
                        $modelFile->write($viewCode['model'], 'w', true);
                        $modelFile->close();                                    
                    }
                    $viewCode = '';
                    
                    $viewCode = json_decode($result['viewFile'],true);

                    if($viewCode['index']){
                        $folder = APP . DS . 'View' . DS . Inflector::pluralize(Inflector::classify($table_name));        
                        $modelFolder = new Folder();
                        $modelFolder->create($folder);
                        $file = $folder . DS . 'index.ctp';
                        $modelFile = new File($file);
                        $modelFile->create();
                        $modelFile->open();
                        $modelFile->write($viewCode['index'], 'w', true);
                        $modelFile->close();                                    
                    }                

                    if($viewCode['view']){    
                        $folder = APP . DS . 'View' . DS . Inflector::pluralize(Inflector::classify($table_name));
                        $modelFolder = new Folder();
                        $modelFolder->create($folder);
                        $file = $folder . DS . 'view.ctp';
                        $modelFile = new File($file);
                        $modelFile->create();
                        $modelFile->open();
                        $modelFile->write($viewCode['view'], 'w', true);
                        $modelFile->close();
                    }                    

                    $addCode = json_decode($result['formFile'],true);

                    if($addCode['add']){
                        $folder = APP . DS . 'View' . DS . Inflector::pluralize(Inflector::classify($table_name));        
                        $modelFolder = new Folder();
                        $modelFolder->create($folder);
                        $file = $folder . DS . 'add.ctp';
                        $modelFile = new File($file);
                        $modelFile->create();
                        $modelFile->open();
                        $modelFile->write($addCode['add'], 'w', true);
                        $modelFile->close();
                        
                    }

                    if($addCode['edit']){
                        $folder = APP . DS . 'View' . DS . Inflector::pluralize(Inflector::classify($table_name));        
                        $modelFolder = new Folder();
                        $modelFolder->create($folder);
                        $file = $folder . DS . 'edit.ctp';
                        $modelFile = new File($file);
                        $modelFile->create();
                        $modelFile->open();
                        $modelFile->write($addCode['edit'], 'w', true);
                        $modelFile->close();
                        
                    }
                }

                $sql = "CREATE TABLE `" . $customTable['CustomTable']['table_name'] . "` ( ";
                $sql.= "`id` varchar(36) NOT NULL,";
                $sql.= "`sr_no` int(11) NOT NULL AUTO_INCREMENT,";
                $sql.= "`name` varchar(255) NOT NULL,";
                $sql.= "            
                `custom_table_id` varchar(36) DEFAULT NULL,
                `publish` tinyint(1) DEFAULT '1' COMMENT '0=Un 1=Pub',
                `record_status` tinyint(1) DEFAULT '0' COMMENT '0=Un-locked, 1=Locked',
                `status_user_id` varchar(36) DEFAULT NULL,
                `created_by` varchar(36) NOT NULL,
                `created` datetime NOT NULL,
                `modified_by` varchar(36) NOT NULL,
                `approved_by` varchar(36) DEFAULT NULL,
                `prepared_by` varchar(36) DEFAULT NULL,
                `modified` datetime NOT NULL,
                `soft_delete` tinyint(1) NOT NULL DEFAULT '0',
                `branchid` varchar(36) DEFAULT NULL,
                `departmentid` varchar(36) DEFAULT NULL,
                `company_id` varchar(36) DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `sr_no` (`sr_no`)
                ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
                ";
                // everything run properly -- now create table

                $this->_clear_cake_cache();
                Configure::write('debug',2);

                if($this->CustomTable->query($sql)){
                    $controllers = array();
                    $aCtrlClasses = App::objects('controller');
                    $skip = array('AppController', 'ApprovalsController', 'ApprovalCommentsController', 'CustomTablesController', 'FilesController', 'RecordsController', 'UserSessionsController');
                    foreach ($aCtrlClasses as $controller) {
                        if (!in_array($controller, $skip)) {
                            $controller = str_replace('Controller', '', $controller);
                            $name = $this->CustomTable->find('first', array('recursive' => 1, 'conditions' => array('CustomTable.table_name LIKE' => "%" . Inflector::underscore($controller)), 'fields' => array('CustomTable.id', 'CustomTable.name', 'CustomTable.table_version')));
                            if ($name) {
                                $controller = Inflector::classify($controller);
                                $linkedTos[$controller] = $name['CustomTable']['name'] . " ver " . $name['CustomTable']['table_version'];
                            } else {
                                $linkedTos[$controller] = $controller;
                            }
                        }
                    }
                    $controller_file_name = str_replace('Controller','', $controller_file_name);
                    $controller_file_name = str_replace('.php','', $controller_file_name);
                    $linkedTos[$controller_file_name] = $customTable['CustomTable']['name'] . " ver " . $customTable['CustomTable']['table_version'];

                    asort($linkedTos);
                    echo json_encode($linkedTos);
                    $this->_clear_cake_cache();
                    Configure::write('debug',0);  
                }else{
                    echo 'SQL failed!';
                }
            }else{
                return false;
            }
            exit;
        }
    }    

    public function add_template_fields(){
        $fields = json_decode(base64_decode($this->data['fields']),true);
        $fields = json_decode($fields[0],true);
        $this->set('fields',$fields);
    }

    public function get_data($field_name = null, $custom_table_id = null)
    {
        $customTable = $this->CustomTable->find("first", [
            "conditions" => ["CustomTable.id" => $custom_table_id],
            "recursive" => -1,
        ]);
        
        $model = Inflector::classify($customTable["CustomTable"]["table_name"]);
        
        $this->loadModel($model);
        $belogs = $this->$model->belongsTo;
        $hasMany = $this->$model->hasMany;
        $customArray = $this->$model->customArray;
        

        foreach ($belogs as $b => $vals) {
            if ($vals["foreignKey"] == $field_name) {
            }
        }
        $field = Inflector::pluralize(Inflector::variable($field_name));
        foreach ($customArray as $key => $val) {
            if ($key == $field) {
                $result = $val;
            }
        }

        if ($result) {
            $this->set("result", $result);
        } else {
            $result = [
                0 => "Null/Empty",
                1 => "Not NULl or Empty",
            ];
            $this->set("result", $result);
        }

        $this->set("model", $model);
        $this->set("field_name", $field_name);
    }

    public function reload_document($id = null){
        
    }

    public function check_before_delete($id = null){
        $customTable = $this->CustomTable->find('first',array('conditions'=>array('CustomTable.id'=>$id)));
        $cmodelName = Inflector::Classify($customTable['CustomTable']['table_name']);
        
        $dataSource = ConnectionManager::getDataSource('default');
        $sources = $dataSource->listSources();
        
        foreach($sources as $model){
            $this->loadModel(Inflector::classify($model));
            $m = Inflector::classify($model);
            $s = $this->$m->belongsTo;
            foreach($s as $pname => $fields){
                if($fields['className'] == $cmodelName){
                    $found[] = $model;
                }
            }
        }
        return $found;
    }

    public function field_details($id = null){
        $table = $this->CustomTable->find('first',array('conditions'=>array('CustomTable.id'=>$id),'recursive'=>-1,'fields'=>array('CustomTable.id','CustomTable.name','CustomTable.table_name', 'CustomTable.fields')));
        $fields = json_decode($table['CustomTable']['fields'],true);

        
        $formAddId = Inflector::singularize(Inflector::Classify($table['CustomTable']['table_name'])).'AddForm';
        $formEditId = Inflector::singularize(Inflector::Classify($table['CustomTable']['table_name'])).'EditForm';
        

        foreach($fields as $field){
            $ids[$field['field_name']]['id'] = Inflector::classify($table['CustomTable']['table_name']).Inflector::classify($field['field_name']);
            $ids[$field['field_name']]['name'] = 'data["'.Inflector::classify($table['CustomTable']['table_name']).'"]["'.$field['field_name'].']';
        }        
        exit;

    }

    public function save_scripts(){        
        $customTable = $this->CustomTable->find('first',array('recursive'=>-1, 'conditions'=>array('CustomTable.id'=>$this->request->data['CustomTable']['id'])));
        if($customTable){
            $customTable['CustomTable']['add_form_script'] = $this->request->data['CustomTable']['add_form_script'];
            $customTable['CustomTable']['edit_form_script'] = $this->request->data['CustomTable']['edit_form_script'];
            $this->CustomTable->create();
            $this->CustomTable->save($customTable,false);
            
            $this->Session->setFlash(__('Scripts Saved'));
            $this->redirect(array('action' => 'view', $this->request->data['CustomTable']['id']));
        }
        exit;
    }

    public function reset_table_password($id = null){
        if($id){
            $customTable = $this->CustomTable->find('first',array('conditions'=>array(
                'CustomTable.created_by'=>$this->Session->read('User.id'),
                'CustomTable.id'=>$id
            ),'recursive'=>-1));
            if($customTable){
                $this->loadModel('User');
                $user = $this->User->find('first',array('recursive'=>-1,'fields'=>array('User.id','User.password'), 'conditions'=>array('User.id'=>$this->Session->read('User.id'))));
                if($user){                    
                    $pass = base64_encode($user['User']['password']);                    
                    $customTable['CustomTable']['password'] = base64_decode($pass);                    
                    $this->CustomTable->create();
                    if($this->CustomTable->save($customTable,false)){
                        $this->Session->setFlash(__('You login password is now password to unlock this table.'));
                        $this->redirect(array('action' => 'unlock', $customTable['CustomTable']['id']));
                    }
                }
            }else{
                $this->Session->setFlash(__('Invalid Credentials.'));
                $this->redirect(array('action' => 'unlock', $customTable['CustomTable']['id']));
            }
        }
        

    }

    public function loadbelongstos($id = null){
        $customTable = $this->CustomTable->find('first',array('recursive'=>-1,'conditions'=>array('CustomTable.id'=>$id),'fields'=>array('CustomTable.id','CustomTable.belongs_to')));
        if($customTable){
            $this->set('customTable',$customTable);
        }
    }

    public function loadbegonstotablefields($m = null){
        $model = Inflector::Classify($m);
        $this->loadModel($model);
        $skip = array('file_key','versions','version_keys','last_saved','update_custom_table_document','file_type','add_records','date_created','document_type','mark_for_cr_update','temp_date_of_issue','temp_effective_from_date','cr_id','old_cr_id','parent_id','linked_documents','user_id','cover_page','page_orientation','pdf_footer_id','user_session_id','signature');

        $allfields = array_diff(array_keys($this->$model->schema()),$skip);
        // $allfields = array_keys($this->$model->schema());
        $this->set('allfields',$allfields);
        $this->set('belogsToModel',$model);
    }

    public function add_belongs_to_fields($model = null, $id = null, $f = null,$field = null) {
        $this->set('model',$model);
        $this->set('field',$field);
        $this->set('f',$f);
    }

    public function pdf_templates(){

    }

    public function link_processes($id = null){
        $this->loadModel('CustomTableProcess');
        if ($this->request->is('post')) {
            $processess = $this->request->data['str'];
            if($processess != null && $id != null){
                $this->CustomTableProcess->deleteAll(array('CustomTableProcess.custom_table_id'=>$id));
                foreach($processess as $process){
                    if($process != -1){
                        $data['CustomTableProcess']['custom_table_id'] = $id;
                        $data['CustomTableProcess']['process_id'] = $process;
                        $data['CustomTableProcess']['sequence'] = 1;
                        $this->CustomTableProcess->create();
                        $this->CustomTableProcess->save($data,false);
                    }                    
                }
                $this->redirect(array('action' => 'link_processes', $id, date('Ymdhis')));
            }else{
                echo "error";
            }
        }


        $this->loadModel('Process');
        $processes = $this->Process->find('list');
        $this->set('processes',$processes);
        $this->set('id',$id);
        $linkedProcesses = $this->CustomTableProcess->find('all',array('order'=>array('CustomTableProcess.sequence'=>'ASC'), 'conditions'=>array('CustomTableProcess.custom_table_id'=>$id)));
        $this->set('linkedProcesses',$linkedProcesses);
        return "asdasd";        
    }

    public function delete_link_processes($id = null, $process_id = null){
        if($id && $process_id){
            $this->loadModel('CustomTableProcess');
            $this->CustomTableProcess->deleteAll(array('CustomTableProcess.custom_table_id'=>$id,'CustomTableProcess.process_id'=>$process_id));
            $this->redirect(array('action' => 'link_processes', $id, date('Ymdhis')));
        }else{
            $this->redirect(array('action' => 'link_processes', $id, date('Ymdhis')));
        }
        
    }

    public function process_sequence($id = null){
        $this->loadModel('CustomTableProcess');
        $seq = 1;
        foreach(json_decode($this->request->params['named']['processes']) as $process_id){
            $process = $this->CustomTableProcess->find('first',array('conditions'=>array(
                'CustomTableProcess.custom_table_id'=>$this->request->params['named']['id'],
                'CustomTableProcess.process_id'=>$process_id,
            ),'recursive'=>-1));

            if($process){
                $this->CustomTableProcess->create();
                $process['CustomTableProcess']['sequence'] = $seq;
                $this->CustomTableProcess->create();
                $this->CustomTableProcess->save($process,false);
                $seq++;
            }            
        }   
        $this->redirect(array('action' => 'link_processes', $this->request->params['named']['id'], date('Ymdhis')));        
    }
}
