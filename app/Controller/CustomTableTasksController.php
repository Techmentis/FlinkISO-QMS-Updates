<?php
App::uses('AppController', 'Controller');
/**
 * CustomTableTasks Controller
 *
 * @property CustomTableTask $CustomTableTask
 * @property PaginatorComponent $Paginator
 */
class CustomTableTasksController extends AppController {

/**
 * add method
 *
 * @return void
 */
public function add($custom_table_id = nuu) {
  if ($this->request->is('post')) {
			// check duplicate
     $customTableChk = $this->CustomTableTask->find('count',array('conditions'=>array(
        'CustomTableTask.employee_field'=>$this->request->data['CustomTableTask']['employee_field'],
        'CustomTableTask.condition_field'=>$this->request->data['CustomTableTask']['condition_field'],
        'CustomTableTask.condition'=>$this->request->data['CustomTableTask']['condition'],
        'CustomTableTask.csvoption'=>$this->request->data['CustomTableTask']['csvoption'],
        'CustomTableTask.date_field'=>$this->request->data['CustomTableTask']['date_field'],
        'CustomTableTask.id'=>$this->request->data['CustomTableTask']['id'])));

     if($customTableChk == 0){
        $this->CustomTableTask->create();
        if ($this->CustomTableTask->save($this->request->data)) {
           $this->Session->setFlash(__('The custom task has been saved.'));
           $this->request->data = null;
           
           $customTable = $this->CustomTableTask->CustomTable->find('first',array('conditions'=>array('CustomTable.id'=>$this->request->data['CustomTableTask']['custom_table_id'])));
           $this->set('customTable',$customTable);
           
           $this->set('customTaskConditions', $this->CustomTableTask->customArray['conditions']);		        
           return $this->redirect(array('action' => 'add',$custom_table_id));
       } else {				
           $this->Session->setFlash(__('The custom task could not be saved. Please, try again.'));
       }
   }else{
    $this->Session->setFlash(__('Duplicate Task. Please try other options'));
}
}

$customTable = $this->CustomTableTask->CustomTable->find('first',array('conditions'=>array('CustomTable.id'=>$custom_table_id),'recursive'=>0));
$fields = json_decode($customTable['CustomTable']['fields'],true);
        // $f = 0;
foreach($fields as $field){
    if($field['linked_to'] == 'Employees'){$result['emp'][$field['field_name']] = $field['field_name'];}
    if($field['data_type'] == 'radio'){$result['radio'][$field['field_name']] = $field['field_name'];}
    if($field['data_type'] == 'date'){$result['date'][$field['field_name']] = $field['field_name'];}
    
}

$this->set('customTable',$customTable);
$this->set('result',$result);
$this->set('customTaskConditions', $this->CustomTableTask->customArray['conditions']);        
$customTableTasks = $this->CustomTableTask->find('all',array('conditions'=>array('CustomTableTask.custom_table_id'=>$custom_table_id)));
$this->set('customTableTasks',$customTableTasks);        
}


public function getcsvs($custom_table_id = null,$field_name = null){
    $this->autoRender = false;
    $this->loadModel('CustomTable');
    $table = $this->CustomTable->find('first',array('recursive'=>-1,'fields'=>array('CustomTable.id','CustomTable.fields'), 'conditions'=>array('CustomTable.id'=>$custom_table_id)));
    foreach(json_decode($table['CustomTable']['fields'],true) as $field ){
        if($field['field_name'] == $field_name){
            $options = explode(',',$field['csvoptions']);            
            if ($options) {
                $con_str.= '<option value=-1>Select</option>';
                foreach ($options as $key => $value) {
                    $con_str.= '<option value=' . $key . '>' . $value . '</option>';
                }
            }
        }
    }

    return $con_str;
    exit;

}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
public function delete_task($table_id) {

  $this->autoRender = false;
  
  if ($this->request->is(array('post', 'put'))) {
     $id = $this->data['id'];
 }

 if (!$this->CustomTableTask->exists($id)) {
     throw new NotFoundException(__('Invalid custom task'));
 }		

 if ($this->CustomTableTask->delete($id)) {
     
 } else {			
 }
 return true;		
}	


public function assigned_tasks(){        
    $customTableTasks = $this->CustomTableTask->find('all',array(
        'fields'=>array(
            'CustomTableTask.id',
            'CustomTableTask.custom_table_id',
            'CustomTableTask.employee_field',
            'CustomTableTask.condition_field',
            'CustomTableTask.condition',
            'CustomTableTask.csvoption',
            'CustomTableTask.date_field',
            'CustomTableTask.message',
            'CustomTable.id',
            'CustomTable.name',
            'CustomTable.display_field',
            'CustomTable.table_name',
            'CustomTable.qc_document_id',
            'CustomTable.process_id',
            'CustomTable.custom_table_id',
        )
    ));

        // $displayFields = array(1=>'title',0=>'name');
    foreach($customTableTasks as $customTableTask){        
        $parent  = array();
        if(!empty($customTableTask['CustomTable']['custom_table_id'])){
                // get parent table
            $parent = $this->CustomTableTask->CustomTable->find('first',array(
                'fields'=>array('CustomTable.id','CustomTable.table_name','CustomTable.qc_document_id','CustomTable.process_id','CustomTable.display_field'),
                'conditions'=>array('CustomTable.id'=>$customTableTask['CustomTable']['custom_table_id']),'recursive'=>-1));                
        }            
        $modal = Inflector::classify($customTableTask['CustomTable']['table_name']);
        
        try{
            $this->loadModel($modal);
            $displayField = $this->$modal->displayField;
            $customTableTask['CustomTable']['display_field'] = $displayField;
            if($parent){
                $parentid = $modal.'.parent_id';                    
            }else{
                $parentid = '';                    
            }
            $recs = $this->$modal->find('all',array('recursive'=>-1,
               'fields'=>array(
                  $modal.'.id',
                  $modal.'.'.$customTableTask['CustomTableTask']['date_field'],
                  $modal.'.'.$customTableTask['CustomTableTask']['employee_field'],
                  $modal.'.'.$displayField,
                  $modal.'.'.$customTableTask['CustomTableTask']['condition_field'],
                  $modal.'.prepared_by',
                  $parentid
              ),
               'conditions'=>array(
                   $modal.'.'.$customTableTask['CustomTableTask']['condition_field'] => $customTableTask['CustomTableTask']['csvoption'],
                   $modal.'.'.$customTableTask['CustomTableTask']['employee_field'] => $this->Session->read('User.employee_id'),
               )
           )); 

        }catch(Exception $e){

        }
        if($recs){
           $finalCustomTableTask[] = array('table'=>$customTableTask,'records'=>$recs,'parent'=>$parent, 'display_field'=>$displayFields[$customTableTask['CustomTable']['display_field']]);
       }            
   }        
   
   $this->set('finalCustomTableTask',$finalCustomTableTask);
   $this->set('PublishedEmployeeList',$this->_get_employee_list());
}
}
