<?php
App::uses('AppController', 'Controller');
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');


class PieChartPanelsController extends AppController {

	public function custom_table($custom_table_id = null){
		$custom_table = $this->PieChartPanel->CustomTable->find('first',array('recursive'=>-1,'fields'=>array('CustomTable.id','CustomTable.fields'), 'conditions'=>array('CustomTable.id'=>$custom_table_id)));
		
		foreach(json_decode($custom_table['CustomTable']['fields'],true) as $field ){
			if($field['data_type'] == 'radio'){
            	// check if record exists
				$rec = $this->PieChartPanel->find('count',array('conditions'=>array('PieChartPanel.custom_table_id'=>$custom_table_id,'PieChartPanel.field_name'=>$field['field_name'])));

				$radioaFields[$field['field_name']] = array($field['field_name'],$rec);
			}
		}
		$this->set('custom_table_id',$custom_table_id);
		$this->set('radioaFields',$radioaFields);
	}

	public function reset_display(){
		$this->autoRender = false;
		$data = json_decode(base64_decode($this->request->data['str']),true);
		if($data[2] == 0){
			// add 
			$data['PieChartPanel']['custom_table_id'] = $data[1];
			$data['PieChartPanel']['field_name'] = $data[0];
			$data['PieChartPanel']['date_condition'] = 1;
			$data['PieChartPanel']['publish'] = 1;
			$data['PieChartPanel']['created_by'] = $this->Session->read('User.id');
			$data['PieChartPanel']['created'] = date('Y-m-d H:i:s');
			$this->PieChartPanel->create();
			$this->PieChartPanel->save($data,false);
			return 1;
		}else{
			// delete 
			$this->PieChartPanel->deleteAll(array('PieChartPanel.field_name'=>$data[0],'PieChartPanel.custom_table_id'=>$data[1]));
			return 2;

		}		
	}

	public function index(){
		$records = $this->PieChartPanel->find('all',array('recursive'=>0));
		$this->set('records',$records);
	}

	public function pie($id){
		$record = $this->PieChartPanel->find('first',array('conditions'=>array('PieChartPanel.id'=>$id), 'recursive'=>0));
		// foreach($records as $record){
		$tableName = Inflector::classify($record['CustomTable']['table_name']);
		$this->loadModel($tableName);

		$fields = json_decode($record['CustomTable']['fields'],true);
		foreach($fields as $field){
			if($field['field_name'] == $record['PieChartPanel']['field_name']){
				$foundFields = explode(',',$field['csvoptions']);
				$cnt = 0;
				foreach($foundFields as $foundField){
					$virtualFields[$foundField] = 'select count(*) from `' . $record['CustomTable']['table_name'] . '` where `'.$record['CustomTable']['table_name'].'`.`'.$field['field_name'].'` = '.$cnt;
					$vfields[] = $tableName.'.'.$foundField;
					$cnt++;
				}
				
			}
		}

		$this->$tableName->virtualFields = 	$virtualFields;
		try{
			$tableDetails = $this->$tableName->find('first',array('recursive'=>-1,'fields'=>$vfields));
		}catch(Exception $e){

		}
		if($tableDetails){
			$x = 0;
			foreach($tableDetails[$tableName] as $label => $data ){
				$result['labels'][$x] = $label;
				$result['data'][$x] = $data;
				$result['color'][$x] = "#".substr(md5(rand()), 0, 6);
				$x++;
			}
			$this->set('table',$record['CustomTable']['name']);
			$this->set('field',Inflector::humanize($record['PieChartPanel']['field_name']));
			$this->set('id',$id);
			$this->set('result',$result);
		}
		// }
	}
}
