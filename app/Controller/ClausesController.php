<?php
App::uses('AppController', 'Controller');
/**
 * Clauses Controller
 *
 * @property Clause $Clause
 * @property PaginatorComponent $Paginator
 */
class ClausesController extends AppController {
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
    /**
     * index method
     *
     * @return void
     */
    public function index() {
        $conditions = $this->_check_request();
        $this->paginate = array('order' => array('Clause.sr_no' => 'DESC'), 'conditions' => array($conditions));
        $this->Clause->recursive = 0;
        $this->set('clauses', $this->paginate());
        $this->_get_count();
    }

    public function _commons($creator = null){

    }
    /**
     * view method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function view($id = null) {
        if (!$this->Clause->exists($id)) {
            throw new NotFoundException(__('Invalid clause'));
        }
        $options = array('conditions' => array('Clause.' . $this->Clause->primaryKey => $id));
        $this->set('clause', $this->Clause->find('first', $options));
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
            $this->request->data[$this->modelClass]['publish'] = $this->request->data['Approval'][$this->modelClass]['publish'];
            $this->request->data['Clause']['system_table_id'] = $this->_get_system_table_id();
            $this->Clause->create();
            $standard = $this->Clause->Standard->find('first',array('conditions'=>array('Standard.id'=>$this->request->data['Clause']['standard_id']),'recursive'=>-1));
            $this->request->data['Clause']['standard'] = $standard['Standard']['name'];
            if ($this->Clause->save($this->request->data)) {
                if ($this->_show_approvals()) {
                    $this->loadModel('Approval');
                    $this->Approval->create();
                    $this->request->data['Approval']['model_name'] = 'Clause';
                    $this->request->data['Approval']['controller_name'] = $this->request->params['controller'];
                    $this->request->data['Approval']['user_id'] = $this->request->data['Approval']['user_id'];
                    $this->request->data['Approval']['from'] = $this->Session->read('User.id');
                    $this->request->data['Approval']['created_by'] = $this->Session->read('User.id');
                    $this->request->data['Approval']['modified_by'] = $this->Session->read('User.id');
                    $this->request->data['Approval']['record'] = $this->Clause->id;
                    $this->Approval->save($this->request->data['Approval']);
                }
                $this->Session->setFlash(__('The clause has been saved'));
                if ($this->_show_evidence() == true) $this->redirect(array('action' => 'view', $this->Clause->id));
                else $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The clause could not be saved. Please, try again.'));
            }
        }
        $standards = $this->Clause->Standard->find('list', array('conditions' => array('Standard.publish' => 1, 'Standard.soft_delete' => 0)));
        $systemTables = $this->Clause->SystemTable->find('list', array('conditions' => array('SystemTable.publish' => 1, 'SystemTable.soft_delete' => 0)));
        $companies = $this->Clause->Company->find('list', array('conditions' => array('Company.publish' => 1, 'Company.soft_delete' => 0)));
        $preparedBies = $this->Clause->PreparedBy->find('list', array('conditions' => array('PreparedBy.publish' => 1, 'PreparedBy.soft_delete' => 0)));
        $approvedBies = $this->Clause->ApprovedBy->find('list', array('conditions' => array('ApprovedBy.publish' => 1, 'ApprovedBy.soft_delete' => 0)));
        $createdBies = $this->Clause->CreatedBy->find('list', array('conditions' => array('CreatedBy.publish' => 1, 'CreatedBy.soft_delete' => 0)));
        $modifiedBies = $this->Clause->ModifiedBy->find('list', array('conditions' => array('ModifiedBy.publish' => 1, 'ModifiedBy.soft_delete' => 0)));
        $this->set(compact('standards', 'systemTables', 'companies', 'preparedBies', 'approvedBies', 'createdBies', 'modifiedBies'));
        $count = $this->Clause->find('count');
        $published = $this->Clause->find('count', array('conditions' => array('Clause.publish' => 1)));
        $unpublished = $this->Clause->find('count', array('conditions' => array('Clause.publish' => 0)));
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
        if (!$this->Clause->exists($id)) {
            throw new NotFoundException(__('Invalid clause'));
        }
        if ($this->_show_approvals()) {
            $this->set(array('showApprovals' => $this->_show_approvals()));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            
            $this->request->data['Clause']['system_table_id'] = $this->_get_system_table_id();
            $this->request->data[$this->modelClass]['publish'] = $this->request->data['Approval'][$this->modelClass]['publish'];
            $standard = $this->Clause->Standard->find('first',array('conditions'=>array('Standard.id'=>$this->request->data['Clause']['standard_id']),'recursive'=>-1));
            $this->request->data['Clause']['standard'] = $standard['Standard']['name'];

            if ($this->Clause->save($this->request->data)) {
                if ($this->_show_approvals()) $this->_save_approvals();
                if ($this->_show_evidence() == true) $this->redirect(array('action' => 'view', $id));
                else $this->redirect(array('controller' => 'clauses', 'action' => 'index'));
            } else {
                $this->Session->setFlash(__('The clause could not be saved. Please, try again.'));
            }
        } else {
            $options = array('conditions' => array('Clause.' . $this->Clause->primaryKey => $id));
            $this->request->data = $this->Clause->find('first', $options);
        }
        $standards = $this->Clause->Standard->find('list', array('conditions' => array('Standard.publish' => 1, 'Standard.soft_delete' => 0)));
        $systemTables = $this->Clause->SystemTable->find('list', array('conditions' => array('SystemTable.publish' => 1, 'SystemTable.soft_delete' => 0)));
        $companies = $this->Clause->Company->find('list', array('conditions' => array('Company.publish' => 1, 'Company.soft_delete' => 0)));
        $preparedBies = $this->Clause->PreparedBy->find('list', array('conditions' => array('PreparedBy.publish' => 1, 'PreparedBy.soft_delete' => 0)));
        $approvedBies = $this->Clause->ApprovedBy->find('list', array('conditions' => array('ApprovedBy.publish' => 1, 'ApprovedBy.soft_delete' => 0)));
        $createdBies = $this->Clause->CreatedBy->find('list', array('conditions' => array('CreatedBy.publish' => 1, 'CreatedBy.soft_delete' => 0)));
        $modifiedBies = $this->Clause->ModifiedBy->find('list', array('conditions' => array('ModifiedBy.publish' => 1, 'ModifiedBy.soft_delete' => 0)));
        $this->set(compact('standards', 'systemTables', 'companies', 'preparedBies', 'approvedBies', 'createdBies', 'modifiedBies'));
        $count = $this->Clause->find('count');
        $published = $this->Clause->find('count', array('conditions' => array('Clause.publish' => 1)));
        $unpublished = $this->Clause->find('count', array('conditions' => array('Clause.publish' => 0)));
        $this->set(compact('count', 'published', 'unpublished'));
    }
    public function csv() {
        $str = "
        1,,Scope,58511238-fba8-4db9-aad0-833fc20b8995;
        2,,Normative references,58511238-fba8-4db9-aad0-833fc20b8995;
        3,,Terms and Definitions,58511238-fba8-4db9-aad0-833fc20b8995;
        4,,Context of the organization,58511238-fba8-4db9-aad0-833fc20b8995;
        4,4.1, Understanding Context of the Organization,58511238-fba8-4db9-aad0-833fc20b8995;
        4,4.2, Understanding the needs and expectations of interested parties,58511238-fba8-4db9-aad0-833fc20b8995;
        4,4.3 Determining the scope of the quality management system,58511238-fba8-4db9-aad0-833fc20b8995;
        4,4.4, Quality management system and its processes,58511238-fba8-4db9-aad0-833fc20b8995;
        5,, Leadership,58511238-fba8-4db9-aad0-833fc20b8995;
        5,5.1, Leadership and commitment,58511238-fba8-4db9-aad0-833fc20b8995;
        5,5.1.1, Leadership And Commitment For The Quality Management System,58511238-fba8-4db9-aad0-833fc20b8995;
        5,5.1.2, Customer Focus,58511238-fba8-4db9-aad0-833fc20b8995;
        5,5.2, Policy,58511238-fba8-4db9-aad0-833fc20b8995;
        5,5.2.1, Establishing the quality policy,58511238-fba8-4db9-aad0-833fc20b8995;
        5,5.2.2, Communicating the quality policy,58511238-fba8-4db9-aad0-833fc20b8995;
        5,5.3, Organizational roles, responsibilities and authorities,58511238-fba8-4db9-aad0-833fc20b8995;
        6,, Planning,58511238-fba8-4db9-aad0-833fc20b8995;
        6,6.1, Actions to address risks and opportunities,58511238-fba8-4db9-aad0-833fc20b8995;
        6,6.2, Quality objectives and planning to achieve them,58511238-fba8-4db9-aad0-833fc20b8995;
        6,6.3, Planning of changes,58511238-fba8-4db9-aad0-833fc20b8995;
        7,, Support,58511238-fba8-4db9-aad0-833fc20b8995;
        7,7.1, Resources,58511238-fba8-4db9-aad0-833fc20b8995;
        7,7.1.1, General,58511238-fba8-4db9-aad0-833fc20b8995;
        7,7.1.2, People,58511238-fba8-4db9-aad0-833fc20b8995;
        7,7.1.3, Infrastructure,58511238-fba8-4db9-aad0-833fc20b8995;
        7,7.1.4, Environment for the operation of processes,58511238-fba8-4db9-aad0-833fc20b8995;
        7,7.1.5, Monitoring and measuring resources,58511238-fba8-4db9-aad0-833fc20b8995;
        7,7.1.6, Organizational knowledge,58511238-fba8-4db9-aad0-833fc20b8995;
        7,7.2, Competence,58511238-fba8-4db9-aad0-833fc20b8995;
        7,7.3, Awareness,58511238-fba8-4db9-aad0-833fc20b8995;
        7,7.4, Communication,58511238-fba8-4db9-aad0-833fc20b8995;
        7,7.5, Documented information,58511238-fba8-4db9-aad0-833fc20b8995;
        7,7.5.1, General,58511238-fba8-4db9-aad0-833fc20b8995;
        7,7.5.2, Creating and updating documented information,58511238-fba8-4db9-aad0-833fc20b8995;
        7,7.5.3, Control of documented information,58511238-fba8-4db9-aad0-833fc20b8995;
        8,, Operation,58511238-fba8-4db9-aad0-833fc20b8995;
        8,8.1, Operational planning and control,58511238-fba8-4db9-aad0-833fc20b8995;
        8,8.2, Requirements for products and services,58511238-fba8-4db9-aad0-833fc20b8995;
        8,8.3, Design and development of products and services,58511238-fba8-4db9-aad0-833fc20b8995;
        8,8.4, Control of externally provided processes, products and services,58511238-fba8-4db9-aad0-833fc20b8995;
        8,8.5, Product and service provision,58511238-fba8-4db9-aad0-833fc20b8995;
        8,8.6, Release of products and services,58511238-fba8-4db9-aad0-833fc20b8995;
        8,8.7, Control of nonconforming outputs,58511238-fba8-4db9-aad0-833fc20b8995;
        9,, Performance evaluation,58511238-fba8-4db9-aad0-833fc20b8995;
        9,9.1, Monitoring, measurement, analysis and evaluation,58511238-fba8-4db9-aad0-833fc20b8995;
        9,9.1.2, Customer Satisfaction,58511238-fba8-4db9-aad0-833fc20b8995;
        9,9.2, Internal Audit,58511238-fba8-4db9-aad0-833fc20b8995;
        9,9.3, Management Review,58511238-fba8-4db9-aad0-833fc20b8995;
        10,, Improvement,58511238-fba8-4db9-aad0-833fc20b8995;
        10,10.1, General,58511238-fba8-4db9-aad0-833fc20b8995;
        10,10.2, Nonconformity in ISO 9001,58511238-fba8-4db9-aad0-833fc20b8995;
        10,10.2, What is Non-conformance?,58511238-fba8-4db9-aad0-833fc20b8995;
        10,10.2, Corrective Action,58511238-fba8-4db9-aad0-833fc20b8995;
        10,10.3,Continual Improvement,58511238-fba8-4db9-aad0-833fc20b8995;
        ";        
        $data = split(';', ltrim(rtrim($str)));
        foreach ($data as $info) {
            if ($info != ',') $cc = split(',', $info);
            if ($cc[2]) {
                $clause['Clause']['title'] = ltrim(rtrim($cc[2]));
                $clause['Clause']['standard_id'] = ltrim(rtrim($cc[3]));
                $clause['Clause']['clause'] = ltrim(rtrim($cc[0]));
                $clause['Clause']['sub-clause'] = ltrim(rtrim($cc[1]));
                $clause['Clause']['branchid'] = $this->Session->read('User.branch_id');
                $clause['Clause']['departmentid-clause'] = $this->Session->read('User.branch_id');
                $clause['Clause']['created_by'] = $this->Session->read('User.id');
                $clause['Clause']['modified_by'] = $this->Session->read('User.id');
                $clause['Clause']['created'] = date('Y-m-d h:i:s');
                $clause['Clause']['modified'] = date('Y-m-d h:i:s');                
                $this->Clause->create();
                $this->Clause->save($clause);
            }
        }
        exit;
    }
}
