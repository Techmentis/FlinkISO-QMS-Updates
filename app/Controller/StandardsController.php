<?php
App::uses('AppController', 'Controller');
/**
 * Standards Controller
 *
 * @property Standard $Standard
 */
class StandardsController extends AppController {
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
        if(isset($this->request->params['named']['published']) && $this->request->params['named']['published'] == 0 )$pub = 0;
        else $pub = 1;

        if(isset($this->request->params['named']['soft_delete']) && $this->request->params['named']['soft_delete'] == 1 ){$sd = 1; $pub = 0;}
        else $sd = 0;

        $standards = $this->Standard->find('list', array('order' => array('Standard.sr_no' => 'ASC'), 'conditions' => array('Standard.publish' => $pub, 'Standard.soft_delete' => $sd)));
        $this->set(compact('standards'));
    }
    public function documents() {
        $clauses = $this->Standard->Clause->find('all', array('conditions' => array('Clause.standard_id' => $this->request->params['pass'][0], 'Clause.sub-clause' => '', 'Clause.publish' => array(0,1), 'Clause.soft_delete' => 0), 'order' => array('Clause.intclause' => 'ASC'), 'recursive' => - 1));
        foreach ($clauses as $clause) {
            $sub_clause = $this->Standard->Clause->find('all', array('recursive' => - 1, 'order' => array('Clause.sub-clause' => 'asc'), 'conditions' => array('Clause.publish' => 1, 'Clause.soft_delete' => 0, 'Clause.standard_id' => $this->request->params['pass'][0], 'Clause.clause' => $clause['Clause']['clause'], 'Clause.sub-clause !=' => '')));
            $final[$clause['Clause']['clause']]['clause'] = $clause['Clause']['clause'];
            $final[$clause['Clause']['clause']]['title'] = $clause['Clause']['title'];
            $final[$clause['Clause']['clause']]['id'] = $clause['Clause']['id'];
            $final[$clause['Clause']['clause']]['sub'] = $sub_clause;
        }
        $this->set('final', $final);
    }
    public function files($id = null, $standard_id = null) {
        $id = str_replace('clause-', '', $id);
        $clause_record = $this->Standard->Clause->find('first', array('recursive' => - 1, 'conditions' => array('Clause.id' => $id)));
        $this->set('clause', $clause_record);
        $this->loadModel('CustomTable');

        if($this->Session->read('User.is_mr') == false){
            $accessConditions = array(                
                'OR'=>array(
                    'QcDocument.prepared_by'=> $this->Session->read('User.employee_id'),
                    'QcDocument.approved_by'=> $this->Session->read('User.employee_id'),
                    'QcDocument.issued_by'=> $this->Session->read('User.employee_id'),
                    'QcDocument.branches LIKE' => '%'.$this->Session->read('User.branch_id').'%',
                    'QcDocument.departments LIKE' => '%'.$this->Session->read('User.department_id').'%',
                    // 'QcDocument.designations LIKE' => '%'.$this->Session->read('User.designation_id').'%',
                    'QcDocument.user_id LIKE' => '%'.$this->Session->read('User.id').'%',
                )
            );
        }else{
            $accessConditions = array();
        }
        
        $sys = $this->CustomTable->find('all', array('recursive' => 1, 'conditions' => array(
            $accessConditions, 
            'QcDocument.standard_id' => $standard_id,
            'OR'=>array(
                'QcDocument.additional_clauses LIKE' => '%'.$id.'%',
                'QcDocument.clause_id' => $id,
            )
        )
    ));
        $this->set('tables', $sys);
        
        $accessConditions = array();
        // get master list of format
        $this->loadModel('QcDocument');

        $causeCondition = array(
        'OR'=>array(
            'QcDocument.additional_clauses LIKE' => '%'.$id.'%',
            'QcDocument.clause_id' => $id,
        ));

        if($this->Session->read('User.is_mr') == false){
            $accessConditions = array(
                'OR'=>array(
                    'QcDocument.prepared_by'=> $this->Session->read('User.employee_id'),
                    'QcDocument.approved_by'=> $this->Session->read('User.employee_id'),
                    'QcDocument.issued_by'=> $this->Session->read('User.employee_id'),
                    'QcDocument.branches LIKE' => '%'.$this->Session->read('User.branch_id').'%',
                    'QcDocument.departments LIKE' => '%'.$this->Session->read('User.department_id').'%',
                    // 'QcDocument.designations LIKE' => '%'.$this->Session->read('User.designation_id').'%',
                    'QcDocument.user_id LIKE' => '%'.$this->Session->read('User.id').'%',
                )
            );
        }else{
            
        }

        $documentStatusConditions = array(            
                'QcDocument.document_status != ' => 4,
                'QcDocument.archived' => 0
        );
        
        $qcDocuments = $this->QcDocument->find('all', array('conditions' =>array(
            $causeCondition,
            $accessConditions,  
            $documentStatusConditions              
            ), 
        'recursive' => 0));        
        $this->set(compact('qcDocuments'));
    }
    /**
     * view method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function view($id = null) {
        if (!$this->Standard->exists($id)) {
            throw new NotFoundException(__('Invalid standard'));
        }
        $options = array('conditions' => array('Standard.' . $this->Standard->primaryKey => $id));
        $this->set('standard', $this->Standard->find('first', $options));
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
            $this->request->data['Standard']['system_table_id'] = $this->_get_system_table_id();
            $this->request->data[$this->modelClass]['publish'] = $this->request->data['Approval']['Standard']['publish'];
            $this->Standard->create();
            
            if ($this->Standard->save($this->request->data)) {
                if(strpos($this->request->data['Standard']['clauses'], "\r\n") !== false){
                    $clauses = explode("\r\n",$this->request->data['Standard']['clauses']);    
                }else if(strpos($this->request->data['Standard']['clauses'], "\n\r") !== false){
                    $clauses = explode("\n\r",$this->request->data['Standard']['clauses']);    
                }else if(strpos($this->request->data['Standard']['clauses'], "\n") !== false){
                    $clauses = explode("\n",$this->request->data['Standard']['clauses']);    
                }else if(strpos($this->request->data['Standard']['clauses'], PHP_EOL) !== false){                
                    $clauses = explode(PHP_EOL,$this->request->data['Standard']['clauses']);    
                }else if(strpos($this->request->data['Standard']['clauses'], "\r") !== false){                
                    $clauses = explode("\r",$this->request->data['Standard']['clauses']);    
                }else{
                    echo "error";
                }

                foreach ($clauses as $clause) {
                    $clause = explode(',', $clause);
                    if (ltrim(rtrim($clause[2]))) {
                        $data['Clause']['title'] = ltrim(rtrim($clause[2]));
                        $data['Clause']['standard'] = $this->request->data['Standard']['name'];
                        $data['Clause']['standard_id'] = $this->Standard->id;
                        $data['Clause']['clause'] = ltrim(rtrim($clause[0]));
                        $data['Clause']['sub-clause'] = ltrim(rtrim($clause[1]));
                        $data['Clause']['details'] = ltrim(rtrim($clause[3]));
                        $data['Clause']['branchid'] = $this->Session->read('User.branch_id');
                        $data['Clause']['departmentid-clause'] = $this->Session->read('User.branch_id');
                        $data['Clause']['created_by'] = $this->Session->read('User.id');
                        $data['Clause']['modified_by'] = $this->Session->read('User.id');
                        $data['Clause']['created'] = date('Y-m-d h:i:s');
                        $data['Clause']['modified'] = date('Y-m-d h:i:s');
                        $data['Clause']['publish'] = 1;                        
                        $this->Standard->Clause->create();
                        try{
                            $this->Standard->Clause->save($data);
                        }catch(Exception $e) {
                              // do nothing;
                        }
                    }
                }
                if ($this->_show_approvals()) {
                    $this->loadModel('Approval');
                    $this->Approval->create();
                    $this->request->data['Approval']['Standard']['model_name'] = 'Standard';
                    $this->request->data['Approval']['Standard']['controller_name'] = $this->request->params['controller'];
                    $this->request->data['Approval']['Standard']['user_id'] = $this->request->data['Approval']['Standard']['user_id'];
                    $this->request->data['Approval']['Standard']['from'] = $this->Session->read('User.id');
                    $this->request->data['Approval']['Standard']['created_by'] = $this->Session->read('User.id');
                    $this->request->data['Approval']['Standard']['modified_by'] = $this->Session->read('User.id');
                    $this->request->data['Approval']['Standard']['record'] = $this->Standard->id;
                    $this->Approval->save($this->request->data['Approval']['Standard']);
                }
                $this->Session->setFlash(__('The standard has been saved'));
                if ($this->_show_evidence() == true) $this->redirect(array('action' => 'view', $this->Standard->id));
                else $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The standard could not be saved. Please, try again.'));
            }
        }
        $systemTables = $this->Standard->SystemTable->find('list', array('conditions' => array('SystemTable.publish' => 1, 'SystemTable.soft_delete' => 0)));
        // $divisions = $this->Standard->Division->find('list',array('conditions'=>array('Division.publish'=>1,'Division.soft_delete'=>0)));
        $companies = $this->Standard->Company->find('list', array('conditions' => array('Company.publish' => 1, 'Company.soft_delete' => 0)));
        $preparedBies = $this->Standard->PreparedBy->find('list', array('conditions' => array('PreparedBy.publish' => 1, 'PreparedBy.soft_delete' => 0)));
        $approvedBies = $this->Standard->ApprovedBy->find('list', array('conditions' => array('ApprovedBy.publish' => 1, 'ApprovedBy.soft_delete' => 0)));
        $createdBies = $this->Standard->CreatedBy->find('list', array('conditions' => array('CreatedBy.publish' => 1, 'CreatedBy.soft_delete' => 0)));
        $modifiedBies = $this->Standard->ModifiedBy->find('list', array('conditions' => array('ModifiedBy.publish' => 1, 'ModifiedBy.soft_delete' => 0)));
        $this->set(compact('systemTables', 'masterListOfFormats', 'divisions', 'companies', 'preparedBies', 'approvedBies', 'createdBies', 'modifiedBies'));
        $count = $this->Standard->find('count');
        $published = $this->Standard->find('count', array('conditions' => array('Standard.publish' => 1)));
        $unpublished = $this->Standard->find('count', array('conditions' => array('Standard.publish' => 0)));
        $this->set(compact('count', 'published', 'unpublished'));
    }
    /**
     * edit method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function edit($id = null) {
        if (!$this->Standard->exists($id)) {
            throw new NotFoundException(__('Invalid standard'));
        }
        if ($this->_show_approvals()) {
            $this->set(array('showApprovals' => $this->_show_approvals()));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            // 
            $this->request->data['Standard']['system_table_id'] = $this->_get_system_table_id();
            $this->request->data[$this->modelClass]['publish'] = $this->request->data['Approval']['Standard']['publish'];
            if($this->request->data[$this->modelClass]['publish'] == 1)$this->request->data[$this->modelClass]['soft_delete'] = 0;
            
            if ($this->Standard->save($this->request->data)) {

                $clauses = $this->Standard->Clause->find('all',array('recursive'=>1,'conditions'=>array('Clause.standard_id'=>$this->request->data['Standard']['id'])));
                if($clauses){
                    foreach($clauses as $clause){
                        $this->Standard->Clause->create();
                        $clause['standard'] = $this->request->data['Standard']['name'];
                        $clause['publish'] = 1;
                        $clause['soft_delete'] = 0;
                        $this->Standard->Clause->save($clause,false);
                    }
                }

                $qcDocumentCategories = $this->Standard->QcDocumentCategory->find('all',array('recursive'=>1,'conditions'=>array('QcDocumentCategory.standard_id'=>$this->request->data['Standard']['id'])));
                if($qcDocumentCategories){
                    foreach($qcDocumentCategories as $qcDocumentCategory){
                        $this->Standard->QcDocumentCategory->create();
                        $qcDocumentCategory['QcDocumentCategory']['publish'] = 1;
                        $qcDocumentCategory['QcDocumentCategory']['soft_delete'] = 0;
                        $this->Standard->QcDocumentCategory->save($qcDocumentCategory,false);
                    }
                }


                // if ($this->_show_approvals()) $this->_save_approvals();
                if ($this->_show_evidence() == true) $this->redirect(array('action' => 'view', $id));
                else $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The standard could not be saved. Please, try again.'));
            }
        } else {
            $options = array('conditions' => array('Standard.' . $this->Standard->primaryKey => $id));
            $this->request->data = $this->Standard->find('first', $options);
        }
        $systemTables = $this->Standard->SystemTable->find('list', array('conditions' => array('SystemTable.publish' => 1, 'SystemTable.soft_delete' => 0)));
        // $divisions = $this->Standard->Division->find('list',array('conditions'=>array('Division.publish'=>1,'Division.soft_delete'=>0)));
        $companies = $this->Standard->Company->find('list', array('conditions' => array('Company.publish' => 1, 'Company.soft_delete' => 0)));
        $preparedBies = $this->Standard->PreparedBy->find('list', array('conditions' => array('PreparedBy.publish' => 1, 'PreparedBy.soft_delete' => 0)));
        $approvedBies = $this->Standard->ApprovedBy->find('list', array('conditions' => array('ApprovedBy.publish' => 1, 'ApprovedBy.soft_delete' => 0)));
        $createdBies = $this->Standard->CreatedBy->find('list', array('conditions' => array('CreatedBy.publish' => 1, 'CreatedBy.soft_delete' => 0)));
        $modifiedBies = $this->Standard->ModifiedBy->find('list', array('conditions' => array('ModifiedBy.publish' => 1, 'ModifiedBy.soft_delete' => 0)));
        $this->set(compact('systemTables', 'masterListOfFormats', 'divisions', 'companies', 'preparedBies', 'approvedBies', 'createdBies', 'modifiedBies'));
        $count = $this->Standard->find('count');
        $published = $this->Standard->find('count', array('conditions' => array('Standard.publish' => 1)));
        $unpublished = $this->Standard->find('count', array('conditions' => array('Standard.publish' => 0)));
        $this->set(compact('count', 'published', 'unpublished'));
    }

    public function delete_standard() {
        if ($this->request->controller != 'custom_tables') {
            if ($this->request->is('post') || $this->request->is('put')) {
                $standard = $this->Standard->find('first',array('recursive'=>-1,'conditions'=>array('Standard.id'=>$this->request->data['Standard']['id'])));
                if($standard){
                    $standard['Standard']['publish'] = 0;
                    $standard['Standard']['soft_delete'] = 1;
                    $this->Standard->create();
                    if($this->Standard->save($standard,false)){
                        $this->loadModel('Clause');
                        $clauses = $this->Clause->find('all',array('recursive'=>-1, 'fields'=>array('Clause.id', 'Clause.standard_id','Clause.publish','Clause.soft_delete'), 'conditions'=>array('Clause.standard_id'=>$standard['Standard']['id'])));
                        if($clauses){
                            foreach($clauses as $clause){
                                $this->Clause->create();
                                $clause['Clause']['publish'] = 0;
                                $clause['Clause']['soft_delete'] = 1;
                                $this->Clause->save($clause,false);
                            }
                        }
                        
                        $qcDocumentCategories = $this->Standard->QcDocumentCategory->find('all',array('recursive'=>1,'conditions'=>array('QcDocumentCategory.standard_id'=>$standard['Standard']['id'])));
                        if($qcDocumentCategories){
                            foreach($qcDocumentCategories as $qcDocumentCategory){
                                $this->Standard->QcDocumentCategory->create();
                                $qcDocumentCategory['QcDocumentCategory']['publish'] = 0;
                                $qcDocumentCategory['QcDocumentCategory']['soft_delete'] = 1;
                                $this->Standard->QcDocumentCategory->save($qcDocumentCategory,false);
                            }
                        }
                    }
                }
                $this->Session->setFlash(__('The standard successfully delete.'));
                $this->redirect(array('action' => 'index'));
            } else {
                $model = $this->modelClass;
                $this->set('model', $model);
            }
        }
    }
}
