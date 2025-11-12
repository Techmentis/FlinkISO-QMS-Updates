<?php
App::uses('AppController', 'Controller');
/**
 * RecordLocks Controller
 *
 * @property RecordLock $RecordLock
 * @property PaginatorComponent $Paginator
 */
class RecordLocksController extends AppController {

/**
 * add method
 *
 * @return void
 */
public function add($table_id = null) {
	if ($this->request->is('post')) {
		
		$this->RecordLock->create();
		$this->request->data['RecordLock']['lock_table_id'] = Inflector::classify($this->request->data['RecordLock']['lock_table_id']);
		if ($this->RecordLock->save($this->request->data)) {
			$this->Session->setFlash(__('The record lock has been saved.'));
			$this->request->data = null;
			
			$customTable = $this->RecordLock->Table->find('first',array('conditions'=>array('Table.id'=>$this->request->data['RecordLock']['table_id'])));
			$this->set('customTable',$customTable);
			
			$this->set('lockRecordConditions', $this->RecordLock->customArray['conditions']);
			$this->set('lockRecordActions', $this->RecordLock->customArray['actions']);                

			return $this->redirect(array('action' => 'add',$table_id));
		} else {
			$this->Session->setFlash(__('The record lock could not be saved. Please, try again.'));
		}
	}

	$customTable = $this->RecordLock->Table->find('first',array('conditions'=>array('Table.id'=>$table_id)));
	$this->set('customTable',$customTable);
	
	$this->set('lockRecordConditions', $this->RecordLock->customArray['conditions']);
	$this->set('lockRecordActions', $this->RecordLock->customArray['actions']);                

	$recordLocks = $this->RecordLock->find('all',array('conditions'=>array('RecordLock.table_id'=>$table_id)));
	$this->set('recordLocks',$recordLocks);
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
public function delete_lock($table_id = null) {

	$this->autoRender = false;
	
	if ($this->request->is(array('post', 'put'))) {
		$id = $this->data['id'];
	}

	if (!$this->RecordLock->exists($id)) {
		throw new NotFoundException(__('Invalid record lock'));
	}		

	if ($this->RecordLock->delete($id)) {
		
	} else {			
	}
	return true;		
}	
}
