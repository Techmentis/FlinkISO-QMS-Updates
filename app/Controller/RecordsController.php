<?php
App::uses('AppController', 'Controller');
/**
 * Records Controller
 *
 * @property Record $Record
 * @property PaginatorComponent $Paginator
 */
class RecordsController extends AppController {
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
    public function _commons($creator = null) {
        if ($this->action == 'view' || $this->action == 'edit') $this->set('approvals', $this->get_approvals());
        $qcDocuments = $this->Record->QcDocument->find('list', array('conditions' => array('QcDocument.publish' => 1, 'QcDocument.soft_delete' => 0)));
        $systemTables = $this->Record->SystemTable->find('list', array('conditions' => array('SystemTable.publish' => 1, 'SystemTable.soft_delete' => 0)));
        $companies = $this->Record->Company->find('list', array('conditions' => array('Company.publish' => 1, 'Company.soft_delete' => 0)));
        $preparedBies = $this->Record->PreparedBy->find('list', array('conditions' => array('PreparedBy.publish' => 1, 'PreparedBy.soft_delete' => 0)));
        $approvedBies = $this->Record->ApprovedBy->find('list', array('conditions' => array('ApprovedBy.publish' => 1, 'ApprovedBy.soft_delete' => 0)));
        $createdBies = $this->Record->CreatedBy->find('list', array('conditions' => array('CreatedBy.publish' => 1, 'CreatedBy.soft_delete' => 0)));
        $modifiedBies = $this->Record->ModifiedBy->find('list', array('conditions' => array('ModifiedBy.publish' => 1, 'ModifiedBy.soft_delete' => 0)));
        $schedules = $this->Record->Schedule->find('list', array('conditions' => array('Schedule.publish' => 1, 'Schedule.soft_delete' => 0)));
        $this->set(compact('qcDocuments', 'systemTables', 'companies', 'preparedBies', 'approvedBies', 'createdBies', 'modifiedBies', 'schedules'));
        $count = $this->Record->find('count');
        $published = $this->Record->find('count', array('conditions' => array('Record.publish' => 1)));
        $unpublished = $this->Record->find('count', array('conditions' => array('Record.publish' => 0)));
        $this->set(compact('count', 'published', 'unpublished'));
        // add creator
        if ($this->request->params['named']['approval_id']) {
            $this->get_approval($this->request->params['named']['approval_id'], $creator);
            $this->_get_approval_comnments($this->request->params['named']['approval_id'], $creator);
        }
        $this->_get_approver_list($creator);
    }
    /**
     * index method
     *
     * @return void
     */
    public function index() {
        $conditions = $this->_check_request();
        $this->paginate = array('order' => array('Record.sr_no' => 'DESC'), 'conditions' => array($conditions));
        $this->Record->recursive = 0;
        $this->set('records', $this->paginate());
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
        if (!$this->Record->exists($id)) {
            throw new NotFoundException(__('Invalid record'));
        }
        $options = array('conditions' => array('Record.' . $this->Record->primaryKey => $id));
        $record = $this->Record->find('first', $options);
        $this->set('record', $record);
        $this->_commons($record['Record']['created_by']);
        $this->_qc_document_header($record['Record']['qc_document_id']);
        $options = array('recursive' => - 1, 'conditions' => array('QcDocument.' . $this->Record->QcDocument->primaryKey => $record['Record']['qc_document_id']));
        $qcDoc = $this->QcDocument->find('first', $options);
        $this->set('qcDoc', $qcDoc);
    }
    /**
     * add method
     *
     * @return void
     */
    public function add() {
        $doc = $this->Record->QcDocument->find('first', array('conditions' => array('QcDocument.id' => $this->request->params['pass'][0])));
        if ($doc) {
            $this->Record->create();
            $record['Record']['title'] = "Enter brief activity description";
            $record['Record']['qc_document_id'] = $this->request->params['pass'][0];
            $record['Record']['file_type'] = $doc['QcDocument']['file_type'];
            $record['Record']['date_time'] = date('Y-m-d H:i:s');
            $record['Record']['prepared_by'] = $this->Session->read('User.employee_id');
            $record['Record']['comments'] = '';
            $this->Record->save($record);
            $this->redirect(array('action' => 'edit', $this->Record->id));
        } else {
            $this->Session->setFlash(__('Select document first.'));
            $this->redirect(array('controller' => 'users', 'action' => 'dashboard'));
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
        if (!$this->Record->exists($id)) {
            throw new NotFoundException(__('Invalid record'));
        }
        if ($this->_show_approvals()) {
            $this->set(array('showApprovals' => $this->_show_approvals()));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            
            $this->request->data['Record']['system_table_id'] = $this->_get_system_table_id();
            if ($this->Record->save($this->request->data)) {
                if ($this->_show_approvals()) $this->_save_approvals();
                if ($this->_show_evidence() == true) $this->redirect(array('action' => 'view', $id));
                else $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The record could not be saved. Please, try again.'));
            }
        } else {
            $options = array('conditions' => array('Record.' . $this->Record->primaryKey => $id));
            $this->request->data = $this->Record->find('first', $options);
        }
        $this->_commons();
        $this->_qc_document_header($this->request->data['Record']['qc_document_id']);
        $options = array('recursive' => - 1, 'conditions' => array('QcDocument.' . $this->Record->QcDocument->primaryKey => $this->request->data['Record']['qc_document_id']));
        $qcDoc = $this->QcDocument->find('first', $options);
        $this->set('qcDoc', $qcDoc);
        $file_type = $qcDoc['QcDocument']['file_type'];
        $file_name = $qcDoc['QcDocument']['title'];
        $document_number = $qcDoc['QcDocument']['document_number'];
        $document_version = $qcDoc['QcDocument']['revision_number'];
        $file_name = $document_number . '-' . $file_name . '-' . $document_version . '.' . $file_type;
        
        $qcfile = WWW_ROOT . 'files' . DS . $this->Session->read('User.company_id') . DS . 'qc_documents' . DS . $qcDoc['QcDocument']['id'] . DS . $file_name;
        
        $file = Configure::read('path') . DS . $this->request->data['Record']['id'] . DS . $file_name;
        if (!file_exists($qcfile)) {
            echo "This document does not exists.";            
            
        } else {
            $from = $qcfile;
            if (file_exists($from)) {
                if (!file_exists(Configure::read('path') . DS . $this->request->data['Record']['id'])) {
                    $pathfolder = new Folder();
                    if ($pathfolder->create(Configure::read('path') . DS . $this->request->data['Record']['id'])) {
                        chmod(Configure::read('path') . DS . $this->request->data['Record']['id'],0777);
                        copy($from, $file);
                        $key = $this->_generate_onlyoffice_key($this->request->data['Record']['id'] . date('Ymdhis'));
                        $this->set('file_key', $key);
                        $rec = $this->Record->find('first', array('recursive' => - 1, 'conditions' => array('Record.id' => $this->request->data['Record']['id'])));
                        $rec['Record']['file_key'] = $key;
                        $this->Record->create();
                        $this->Record->save($rec['Record']);
                    } else {
                        echo "failed folder - 1" . Configure::read('path');
                    }
                } else {
                    // echo "failed folder -2 " . Configure::read('path');
                    
                }
            }
        }
        if ($this->request->data['Record']['file_key']) {
        } else {
            $key = $this->_generate_onlyoffice_key($this->request->data['Record']['id'] . date('Ymdhis'));
            $this->request->data['Record']['file_key'] = $key;
            $this->set('file_key', $key);
            $rec = $this->Record->find('first', array('recursive' => - 1, 'conditions' => array('Record.id' => $this->request->data['Record']['id'])));
            $rec['Record']['file_key'] = $key;
            $rec['Record']['file_key'] = $key;
            $this->Record->create();
            $this->Record->save($rec['Record']);
        }
        $this->_commons($this->request->data['QcDocument']['created_by']);
    }
    /**
     * approve method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function approve($id = null, $approvalId = null) {
        if (!$this->Record->exists($id)) {
            throw new NotFoundException(__('Invalid record'));
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
            if ($this->Record->save($this->request->data)) {
                if (!isset($this->request->data[$this->modelClass]['publish']) && $this->request->data['Approval']['user_id'] != - 1) {
                    $this->request->data[$this->modelClass]['publish'] = 0;
                }
                if ($this->Record->save($this->request->data)) {
                    $this->Session->setFlash(__('The record has been saved.'));
                    if ($this->_show_approvals()) $this->_save_approvals();
                } else {
                    $this->Session->setFlash(__('The record could not be saved. Please, try again.'));
                }
            } else {
                $this->Session->setFlash(__('The record could not be saved. Please, try again.'));
            }
        } else {
            $options = array('conditions' => array('Record.' . $this->Record->primaryKey => $id));
            $this->request->data = $this->Record->find('first', $options);
        }
        $this->_commons();
        $this->_qc_document_header($this->request->params['pass'][0]);
    }
}
