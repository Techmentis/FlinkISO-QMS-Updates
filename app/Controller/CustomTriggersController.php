<?php
App::uses("AppController", "Controller");
/**
 * CustomTriggers Controller
 *
 * @property CustomTrigger $CustomTrigger
 * @property PaginatorComponent $Paginator
 */
class CustomTriggersController extends AppController
{
    
    /**
     * add method
     *
     * @return void
     */
    public function add()
    {
        if ($this->request->is("post")) {            
            // $this->request->data['CustomTrigger']['hod_departments'] = json_encode($this->request->data['CustomTrigger']['hod_departments']);
            
            $this->CustomTrigger->create();            
            
            if ($this->CustomTrigger->save($this->request->data)) {                
                $this->Session->setFlash(__("The custom trigger has been saved"));
                $this->redirect(array('action' => 'add','custom_table_id'=>$this->request->data["CustomTrigger"]["custom_table_id"]));
            } else {
                $this->redirect(array('action' => 'add','custom_table_id'=>$this->request->data["CustomTrigger"]["custom_table_id"]));
            }
        }
        
        $this->set(compact("customTables","branches","departments"));
        
        if (!$this->request->params["named"]["custom_table_id"]) {
            $this->Session->setFlash(__("Please select table first"));
            $this->redirect(array(
                "controller" => "custom_tables",
                "action" => "add",
                $this->CustomTrigger->id,
            ));
        } else {
            
            $customTable = $this->CustomTrigger->CustomTable->find("first", array("conditions" => array("CustomTable.id" =>$this->request->params["named"]["custom_table_id"],),));
            
            $allFieldNames = json_decode($customTable["CustomTable"]["fields"],true);
            foreach ($allFieldNames as $f) {
                if($f["data_type"] == 'radio')$fieldNames[$f["field_name"]] = $f["field_name"];
                $fields[$f["field_name"]] = $f;

                if ($f["linked_to"] == "Employees") {
                    $notifyUsers[$f["field_name"]] = $f["field_name"];
                }
                if ($f["linked_to"] == "Users") {
                    $notifyUsers[$f["field_name"]] = $f["field_name"];
                }
                $notifyUsers["prepared_by"] = "prepared_by";
                $notifyUsers["approved_by"] = "approved_by";

                $customTriggers = $this->CustomTrigger->find('all',array('conditions'=>array('CustomTrigger.custom_table_id'=>$this->request->params["named"]["custom_table_id"])));
                $this->set("customTriggers", $customTriggers);
                $this->set("customTable", $customTable);
                $this->set("fieldNames", $fieldNames);
                $this->set("fields", $fields);
                $this->set("notifyUsers", $notifyUsers);
            }
        }
        $this->set('departments',$this->_get_department_list());
        $this->set('employees',$this->_get_employee_list());        
    }

    
    public function get_data($field_name = null, $custom_table_id = null)
    {
        $customTable = $this->CustomTrigger->CustomTable->find("first", [
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

    /**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
    public function delete_trigger() {

        $this->autoRender = false;
        
        if ($this->request->is(array('post', 'put'))) {
            $id = $this->data['id'];
        }

        if (!$this->CustomTrigger->exists($id)) {
            throw new NotFoundException(__('Invalid trigger'));
        }       

        if ($this->CustomTrigger->delete($id)) {
            
        } else {            
        }
        return true;        
    }   
}
