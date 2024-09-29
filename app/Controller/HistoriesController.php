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
class HistoriesController extends AppController {
    /**
     * Components
     *
     * @var array
     */
    public $components = array('Paginator');
    
    public function read_receipts($id = null){
        $fields = array('History.controller_name','History.record_id','History.created_by','History.action','History.created');

        $conditions = $this->_check_request();
        $this->paginate = array(
            'order' => array('History.created' => 'DESC'), 
            'conditions'=>array('History.controller_name'=>'qc_documents', 'History.action !='=>array('get_directory_tree','mini_view','read_receipts','child_docs'),  'History.record_id'=>$id),
            'fields'=>$fields
        );
        
        $this->History->recursive = 0;
        $this->set('histories', $this->paginate());
        
        
        $this->set('users',$this->_get_user_list());
        $this->set('pending',$this->_read_pending($id));
    }

    public function _read_pending($id = null){
        $fields = array('History.controller_name','History.record_id','History.created_by','History.action','History.created');
        $this->loadModel('QcDocument');
        $qcdoc = $this->QcDocument->find('first',array(
            'recursive'=>-1,            
            'conditions'=>array('QcDocument.id'=>$id),
            'fields'=>array('QcDocument.id','QcDocument.user_id','QcDocument.branches','QcDocument.designations','QcDocument.departments','QcDocument.editors')
        ));

        if($qcdoc['QcDocument']['user_id']){
            $user_ids = json_decode($qcdoc['QcDocument']['user_id'],true);
        }
        
        $users = array();
        if($qcdoc['QcDocument']['branches']){
            $branches = json_decode($qcdoc['QcDocument']['branches'],true);
            foreach($branches as $branch){
                $branch_users = $this->History->CreatedBy->find('list',array('conditions'=>array('CreatedBy.branch_id'=>$branch)));
                $users = array_merge($users,$branch_users);  
            }
        }

        if($qcdoc['QcDocument']['departments']){
            $departments = json_decode($qcdoc['QcDocument']['departments'],true);
            foreach($departments as $department){
                $department_users = $this->History->CreatedBy->find('list',array('conditions'=>array('CreatedBy.department_id'=>$department)));
                $users = array_merge($users,$department_users);
            }
        }

        if($qcdoc['QcDocument']['editors']){
            $editors = json_decode($qcdoc['QcDocument']['editors'],true);
            foreach($editors as $editor){
                $editor_users = $this->History->CreatedBy->find('list',array('conditions'=>array('CreatedBy.id'=>$editor)));
                $users = array_merge($users,$editor_users);
            }
        }

        foreach($users as $user_id => $username){
            $read = $this->History->find('count',array('conditions'=>array(
                'History.controller_name'=>'qc_documents', 'History.action !='=>array('get_directory_tree','mini_view','read_receipts','child_docs'),  'History.record_id'=>$id,'History.created_by'=>$user_id
            )));

            if($read == 0){
                $read_pending[$user_id] = $username;
            }
        }

        return array($users,$read_pending);
    }
    
}
