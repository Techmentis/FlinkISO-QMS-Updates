<?php
App::uses('AppController', 'Controller');
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');


class GraphPanelsController extends AppController {

	public function graphs(){
		$conditions = $this->_check_request();
		$this->paginate = array('order' => array('GraphPanel.position' => 'ASC'), 'conditions' => array($conditions));
		$this->GraphPanel->recursive = 0;
		$this->set('graphPanels', $this->paginate());
		$this->set('dateConditions',$this->GraphPanel->customArray['dateConditions']);
		$this->set('graphTypes',$this->GraphPanel->customArray['graphTypes']);
		$this->set('dataTypes',$this->GraphPanel->customArray['dataTypes']);

		$this->set('branches',$this->_get_branch_list());
		$this->set('departments',$this->_get_department_list());
		$this->set('designations',$this->_get_designation_list());		
	}

	public function custom_table($custom_table_id = null){
		$custom_table = $this->GraphPanel->CustomTable->find('first',array('recursive'=>-1,'fields'=>array('CustomTable.id','CustomTable.fields'), 'conditions'=>array('CustomTable.id'=>$custom_table_id)));
		
		foreach(json_decode($custom_table['CustomTable']['fields'],true) as $field ){
			if($field['data_type'] == 'radio'){
            	// check if record exists
				$rec = $this->GraphPanel->find('count',array('conditions'=>array('GraphPanel.custom_table_id'=>$custom_table_id,'GraphPanel.field_name'=>$field['field_name'])));
				$radioaFields[$field['field_name']] = array($field['field_name'],$rec);
			}

			if(!empty($field['linked_to']) &&  $field['linked_to'] != -1 && $field['linked_to'] != 'Employees'){
            	// check if record exists
				$rec = $this->GraphPanel->find('count',array('conditions'=>array('GraphPanel.custom_table_id'=>$custom_table_id,'GraphPanel.field_name'=>$field['field_name'])));
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
			$data['GraphPanel']['custom_table_id'] = $data[1];
			$data['GraphPanel']['field_name'] = $data[0];
			$data['GraphPanel']['date_condition'] = 1;
			$data['GraphPanel']['publish'] = 1;
			$data['GraphPanel']['created_by'] = $this->Session->read('User.id');
			$data['GraphPanel']['created'] = date('Y-m-d H:i:s');
			$this->GraphPanel->create();
			$this->GraphPanel->save($data,false);
			return 1;
		}else{
			// delete 
			$this->GraphPanel->deleteAll(array('GraphPanel.field_name'=>$data[0],'GraphPanel.custom_table_id'=>$data[1]));
			return 2;

		}		
	}

	public function index(){
		$graphs = $this->GraphPanel->find('all',array('order'=>array('GraphPanel.position'=>'ASC'), 'conditions'=>array('GraphPanel.graph_type !=' => 4), 'recursive'=>0));
		$this->set('graphs',$graphs);

		$panels = $this->GraphPanel->find('all',array('order'=>array('GraphPanel.position'=>'ASC'), 'conditions'=>array('GraphPanel.graph_type' => 4), 'recursive'=>0));
		$this->set('panels',$panels);		
	}

	public function graph($id = null){		
		$record = $this->GraphPanel->find('first',array('conditions'=>array('GraphPanel.id'=>$id), 'recursive'=>0));				
		$tableName = Inflector::classify($record['CustomTable']['table_name']);
		$this->loadModel($tableName);
		$fields = json_decode($record['CustomTable']['fields'],true);
		$virtualFields = array();
		foreach($fields as $field){			
			$foundFields = null;
			if($field['field_name'] == $record['GraphPanel']['field_name'] && $field['data_type'] == 'radio' && $field['linked_to'] == -1){				
				$foundFields = explode(',',$field['csvoptions']);
				$cnt = 0;				
				foreach($foundFields as $foundField){
					$virtualFields[$foundField] = 'select count(*) from `' . $record['CustomTable']['table_name'] . '` where `'.$record['CustomTable']['table_name'].'`.`'.$field['field_name'].'` = '.$cnt;
					$vfields[] = $tableName.'.'.$foundField;
					$cnt++;
				}
				
			}elseif($field['field_name'] == $record['GraphPanel']['field_name'] && !empty($field['linked_to']) &&  $field['linked_to'] != -1 && $field['linked_to'] != 'Employees'){				
				$foundFields = null;
				// load data from linkedto table / display field
				$linkedToModel = Inflector::classify($field['linked_to']);
				try{
					$this->loadModel($linkedToModel);
					$recs = $this->$linkedToModel->find('list',array('conditions'=>array($linkedToModel.'.publish'=>1)));	
				}catch(Exception $e){

				}
				foreach($recs as $id => $display){
					if($record['GraphPanel']['data_type'] == 0){
						$virtualFields[trim($display)] = 'select count(*) from `' . $record['CustomTable']['table_name'] . '` where `'.$record['CustomTable']['table_name'].'`.`'.$field['field_name'].'` = "' .$id .'"';

						$vfields[] = $tableName.'.'.$display;
						$cnt++;					
					}

					if($record['GraphPanel']['data_type'] == 1){
						$virtualFields[trim($display)] = 'select SUM(`'.$record['GraphPanel']['value_field'].'`)  as `sumfield` from `' . $record['CustomTable']['table_name'] . '` where `'.$record['CustomTable']['table_name'].'`.`'.$field['field_name'].'` = "' .$id .'"';						
						$vfields[] = $tableName.'.'.trim($display);
						$cnt++;					
					}

					if($record['GraphPanel']['data_type'] == 2){
						$virtualFields[trim($display)] = 'select AVG(`'.$record['GraphPanel']['value_field'].'`)  as `sumfield` from `' . $record['CustomTable']['table_name'] . '` where `'.$record['CustomTable']['table_name'].'`.`'.$field['field_name'].'` = "' .$id .'"';						
						$vfields[] = $tableName.'.'.trim($display);
						$cnt++;	
					}					
				}
			}
		}
		
		
		if($vfields){
			$this->$tableName->virtualFields = 	$virtualFields;

			try{
				$tableDetails = $this->$tableName->find('first',array('recursive'=>-1,'fields'=>$vfields));				
			}catch(Exception $e){
				
			}
			
			if($tableDetails){
				$x = 0;
				foreach($tableDetails[$tableName] as $label => $data ){
					if($data == null)$data = 0;
					$result['labels'][$x] = $label;
					$result['data'][$x] = $data;
					$result['color'][$x] = "#".substr(md5(rand()), 4, 6);
					$x++;
				}

				$this->set('table',$record['CustomTable']['name']);
				$this->set('field',Inflector::humanize($record['GraphPanel']['field_name']));
				$this->set('result',$result);
				$this->set('record',$record);
			}	

			

			$this->set('graphTypes',$this->GraphPanel->customArray['graphTypes']);
			$this->set('dateConditions',$this->GraphPanel->customArray['dateConditions']);
		}		
	}

	public function panel($id){		
		$record = $this->GraphPanel->find('first',array('conditions'=>array('GraphPanel.id'=>$id), 'recursive'=>0));		
		$tableName = Inflector::classify($record['CustomTable']['table_name']);
		$this->loadModel($tableName);

		$fields = json_decode($record['CustomTable']['fields'],true);
		foreach($fields as $field){
			if($field['field_name'] == $record['GraphPanel']['field_name']){
				$foundFields = explode(',',$field['csvoptions']);
				$cnt = 0;
				foreach($foundFields as $foundField){
					$virtualFields[$foundField] = 'select count(*) from `' . $record['CustomTable']['table_name'] . '` where `'.$record['CustomTable']['table_name'].'`.`'.$field['field_name'].'` = '.$cnt;
					$vfields[] = $tableName.'.'.$foundField;
					$cnt++;
				}
				
			}
		}

		$virtualFields['total'] = 'select count(*) from `' . $record['CustomTable']['table_name'] . '`';
		$vfields[] = 'total';
		$this->$tableName->virtualFields = 	$virtualFields;
		
		try{
			$tableDetails = $this->$tableName->find('first',array('recursive'=>-1,'fields'=>$vfields));
		}catch(Exception $e){

		}
		if($tableDetails){
			$this->set('table',$record['CustomTable']['name']);
			$this->set('field',Inflector::humanize($record['GraphPanel']['field_name']));				
			$this->set('result',$tableDetails[$tableName]);
			$this->set('record',$record);
		}		

		$this->set('graphTypes',$this->GraphPanel->customArray['graphTypes']);
		$this->set('dateConditions',$this->GraphPanel->customArray['dateConditions']);
	}

	public function update($id = null){

		if ($this->request->is(array('post', 'put'))) {
			$data = json_decode(base64_decode($this->request->data['str']),true);
			
			$id = $data[1];
			$graphPanel = $this->GraphPanel->find('first',array('conditions'=>array('GraphPanel.id'=>$id)));
			$graphPanel['GraphPanel'][$data[2]] = $data[3];
			$this->GraphPanel->create();
			$this->GraphPanel->save($graphPanel,false);
			$this->set('graphPanel',$graphPanel);
			$this->set('dateConditions',$this->GraphPanel->customArray['dateConditions']);
			$this->set('graphTypes',$this->GraphPanel->customArray['graphTypes']);
			$this->set('dataTypes',$this->GraphPanel->customArray['dataTypes']);

			$this->redirect(array('action' => 'update',$graphPanel['GraphPanel']['id']));

			$this->set('branches',$this->_get_branch_list());
			$this->set('departments',$this->_get_department_list());
			$this->set('designations',$this->_get_designation_list());
		}

		else{
			
			$id = $this->request->params['pass'][0];
			$graphPanel = $this->GraphPanel->find('first',array('conditions'=>array('GraphPanel.id'=>$id)));
			$this->set('graphPanel',$graphPanel);
			$this->set('dateConditions',$this->GraphPanel->customArray['dateConditions']);
			$this->set('graphTypes',$this->GraphPanel->customArray['graphTypes']);
			$this->set('dataTypes',$this->GraphPanel->customArray['dataTypes']);
			$this->set('graphPanels',$this->GraphPanel->find('count'));
		}		
		
	}

	public function more($id = null){
		if ($this->request->is(array('post', 'put'))) {
			if(!empty($this->data['GraphPanel']['id'])){
				$graphPanel = $this->GraphPanel->find('first',array('recursive'=>-1, 'conditions'=>array('GraphPanel.id'=>$this->request->data['GraphPanel']['id'])));
				$graphPanel['GraphPanel']['title'] = $this->data['GraphPanel']['title'];
				$graphPanel['GraphPanel']['branches'] = json_encode($this->data['GraphPanel']['branches']);
				$graphPanel['GraphPanel']['departments'] = json_encode($this->data['GraphPanel']['departments']);
				$graphPanel['GraphPanel']['designations'] = json_encode($this->data['GraphPanel']['designations']);
				$this->GraphPanel->create();
				$this->GraphPanel->save($graphPanel,false);
				return true;
			}			
		}
		$graphPanel = $this->GraphPanel->find('first',array('conditions'=>array('GraphPanel.id'=>$id)));
		$this->set('graphPanel',$graphPanel);
		$this->set('branches',$this->_get_branch_list());
		$this->set('departments',$this->_get_department_list());
		$this->set('designations',$this->_get_designation_list());		
	}

	public function delete($id = null){

		$this->GraphPanel->delete($id);		
		$this->Session->setFlash(__('Graph Deleted'));		
		$this->redirect(array('action' => 'graphs'));

	}
}
