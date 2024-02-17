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
            'order' => array('History.sr_no' => 'ASC'), 
            'conditions'=>array('History.controller_name'=>'qc_documents', 'History.action !='=>array('get_directory_tree','mini_view','read_receipts'),  'History.record_id'=>$id),
            'fields'=>$fields
        );
        
        $this->History->recursive = 0;
        $this->set('histories', $this->paginate());
        
        
        $this->set('users',$this->_get_user_list());
    }
    
}