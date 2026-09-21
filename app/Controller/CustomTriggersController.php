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
    public $components = array('Paginator');

    public function index()
    {
        $this->CustomTrigger->recursive = 0;
        $this->Paginator->settings = array(
        'limit' => 50,
        'order' => array('CustomTrigger.sr_no' => 'DESC'),
        'conditions' => array('CustomTrigger.soft_delete' => 0)
        );
        $customTriggers = $this->Paginator->paginate('CustomTrigger');
        $this->set(compact('customTriggers'));
        $this->set('eventNames', $this->_emailTriggerEvents());
        $this->set('PublishedEmployeeList', $this->_get_employee_list());
    }

    public function deliveries()
    {
        $this->loadModel('EmailTriggerOutbox');
        $conditions = array();
        if (!empty($this->request->params['named']['custom_table_id'])) {
            $triggerIds = $this->CustomTrigger->find('list', array(
            'recursive' => -1,
            'fields' => array('CustomTrigger.id', 'CustomTrigger.id'),
            'conditions' => array('CustomTrigger.custom_table_id' => $this->request->params['named']['custom_table_id'])
            ));
            $conditions['EmailTriggerOutbox.custom_trigger_id'] = array_values($triggerIds);
        }
        $this->Paginator->settings = array('limit' => 50, 'order' => array('EmailTriggerOutbox.created' => 'DESC'), 'conditions' => $conditions);
        $deliveries = $this->Paginator->paginate('EmailTriggerOutbox');
        $this->set(compact('deliveries'));
    }

    public function retry_delivery($id = null)
    {
        $this->autoRender = false;
        if (!$this->request->is('post')) throw new MethodNotAllowedException();
        $this->loadModel('EmailTriggerOutbox');
        if (!$this->EmailTriggerOutbox->exists($id)) throw new NotFoundException(__('Invalid delivery'));
        $this->EmailTriggerOutbox->id = $id;
        $this->EmailTriggerOutbox->save(array('EmailTriggerOutbox' => array(
        'status' => 'pending', 'attempts' => 0, 'last_error' => null, 'available_at' => date('Y-m-d H:i:s')
        )), false);
        $this->Session->setFlash(__('The email has been queued for retry.'));
        return $this->redirect($this->referer(array('action' => 'deliveries'), true));
    }

    public function toggle_enabled($id = null)
    {
        $this->autoRender = false;
        if (!$this->request->is('post')) throw new MethodNotAllowedException();
        $rule = $this->CustomTrigger->find('first', array('recursive' => -1, 'conditions' => array('CustomTrigger.id' => $id)));
        if (empty($rule)) throw new NotFoundException(__('Invalid trigger'));
        $this->CustomTrigger->id = $id;
        $this->CustomTrigger->saveField('enabled', empty($rule['CustomTrigger']['enabled']) ? 1 : 0, array('validate' => false));
        return $this->redirect($this->referer(array('action' => 'index'), true));
    }

    protected function _emailTriggerEvents()
    {
        return array(
        'record.created' => 'Record created',
        'record.updated' => 'Record updated',
        'record.deleted' => 'Record deleted',
        'field.changed' => 'Field value changed',
        'date.reminder' => 'Date reminder'
        );
    }

    protected function _setupTriggerForm($customTableId) {
        $customTable = $this->CustomTrigger->CustomTable->find("first", array("conditions" => array("CustomTable.id" =>$customTableId,),));
        if (empty($customTable)) {
            $this->Session->setFlash(__('Please select a valid Custom Form.'));
            return false;
        }

        $allFieldNames = json_decode($customTable["CustomTable"]["fields"],true);
        $fieldNames = $dateFieldNames = $fields = $notifyUsers = array();
        $notifyBranches = $notifyDepartments = $notifyDesignations = false;
        $nonBusinessFields = array('approval_step_id', 'approved_by', 'published_by', 'published_date', 'publish', 'record_status', 'status_user_id', 'created', 'created_by', 'modified', 'modified_by', 'company_id', 'branchid', 'departmentid');
        foreach ((array)$allFieldNames as $f) {
            if (!empty($f['field_name']) && !in_array($f['field_name'], $nonBusinessFields, true)) $fieldNames[$f["field_name"]] = !empty($f['field_label']) ? base64_decode($f['field_label'], true) : Inflector::humanize($f["field_name"]);
            if(in_array($f["data_type"], array('date', 'datetime'), true)) $dateFieldNames[$f["field_name"]] = !empty($f['field_label']) ? base64_decode($f['field_label'], true) : Inflector::humanize($f["field_name"]);
            $fields[$f["field_name"]] = $f;

            if ($f["linked_to"] == "Employees") {
                $notifyUsers[$f["field_name"]] = $f["field_name"];
            }
            if ($f["linked_to"] == "Users") {
                $notifyUsers[$f["field_name"]] = $f["field_name"];
            }

            if ($f["linked_to"] == "Branches") {
                $notifyBranches = true;
            }

            if ($f["linked_to"] == "Departments") {
                $notifyDepartments = true;
            }

            if ($f["linked_to"] == "Designations") {
                $notifyDesignations = true;
            }

            $notifyUsers["prepared_by"] = "prepared_by";
            $notifyUsers["approved_by"] = "approved_by";
        }
        $customTriggers = $this->CustomTrigger->find('all',array('conditions'=>array('CustomTrigger.custom_table_id'=>$customTableId)));

        $this->set(compact('customTriggers', 'customTable', 'fieldNames', 'dateFieldNames', 'fields', 'notifyUsers', 'notifyBranches', 'notifyDepartments', 'notifyDesignations'));
        $this->set('departments',$this->_get_department_list());
        $this->set('employees',$this->_get_employee_list());
        $eventNames = $this->_emailTriggerEvents();
        if (isset($dateFieldNames) && empty($dateFieldNames)) unset($eventNames['date.reminder']);
        $this->set('eventNames', $eventNames);
        $this->set('conditionOperators', array(
        'equals' => 'Changes to',
        'not_equals' => 'Changes to anything except',
        'changed' => 'Changes to any value',
        'to_empty' => 'Becomes empty',
        'from_empty' => 'Changes from empty'
        ));
        return true;
    }

    public function add()
    {
        if ($this->request->is("post")) {
            $trigger =& $this->request->data['CustomTrigger'];
            foreach (array('notify_users', 'hod_departments') as $jsonField) {
                if (isset($trigger[$jsonField]) && is_array($trigger[$jsonField])) {
                    $selectedIds = array_values(array_filter($trigger[$jsonField]));
                    $trigger[$jsonField] = !empty($selectedIds) ? json_encode($selectedIds) : null;
                }
            }
            $allowedEvents = array('record.created', 'record.updated', 'record.deleted', 'field.changed', 'date.reminder');
            if (empty($trigger['event_name']) || !in_array($trigger['event_name'], $allowedEvents, true)) $trigger['event_name'] = 'record.created';
            $definition = !empty($trigger['custom_table_id']) ? $this->CustomTrigger->CustomTable->find('first', array(
            'recursive' => -1,
            'conditions' => array('CustomTable.id' => $trigger['custom_table_id'])
            )) : array();
            if (empty($definition['CustomTable'])) {
                $this->Session->setFlash(__('Please select a valid Custom Form.'));
                return $this->redirect(array('controller' => 'custom_tables', 'action' => 'index'));
            }
            $definedFields = $definedDateFields = array();
            foreach ((array)json_decode($definition['CustomTable']['fields'], true) as $definedField) {
                if (empty($definedField['field_name'])) continue;
                $definedFields[] = $definedField['field_name'];
                if (in_array(isset($definedField['data_type']) ? $definedField['data_type'] : '', array('date', 'datetime'), true)) $definedDateFields[] = $definedField['field_name'];
            }
            if ($trigger['event_name'] === 'field.changed' && (empty($trigger['field_name']) || !in_array($trigger['field_name'], $definedFields, true))) {
                $this->Session->setFlash(__('Please select a field for the field-change trigger.'));
                return $this->redirect(array('action' => 'add', 'custom_table_id' => $trigger['custom_table_id']));
            }
            if ($trigger['event_name'] === 'date.reminder' && (empty($trigger['date_field']) || !in_array($trigger['date_field'], $definedDateFields, true))) {
                $this->Session->setFlash(__('Please select a date field for the reminder.'));
                return $this->redirect(array('action' => 'add', 'custom_table_id' => $trigger['custom_table_id']));
            }
            $legacyActions = array('record.created' => 0, 'record.updated' => 1, 'record.deleted' => 3, 'field.changed' => 1, 'date.reminder' => null);
            $trigger['action'] = $legacyActions[$trigger['event_name']];
            if ($trigger['event_name'] !== 'field.changed') {
                $trigger['field_name'] = null;
                $trigger['changed_field_value'] = null;
            }
            if ($trigger['event_name'] !== 'date.reminder') $trigger['date_field'] = null;
            $trigger['date_offset_days'] = isset($trigger['date_offset_days']) ? (int)$trigger['date_offset_days'] : 0;
            $trigger['enabled'] = !empty($trigger['enabled']) ? 1 : 0;
            $trigger['exclude_actor'] = !empty($trigger['exclude_actor']) ? 1 : 0;
            $trigger['delay_minutes'] = !empty($trigger['delay_minutes']) ? max(0, (int)$trigger['delay_minutes']) : 0;
            if (empty($trigger['delivery_timing']) || $trigger['delivery_timing'] === 'immediate') {
                $trigger['delivery_timing'] = 'immediate';
                $trigger['delay_minutes'] = 0;
            } else {
                $trigger['delivery_timing'] = 'delayed';
            }
            $hasRecipients = !empty($trigger['notify_admins']) || !empty($trigger['notify_hods']) ||
            !empty($trigger['notify_departments']) || !empty($trigger['notify_branches']) ||
            !empty($trigger['notify_designations']) || !empty($trigger['notify_users']) ||
            !empty($trigger['hod_departments']) || (!empty($trigger['notify_user']) && $trigger['notify_user'] !== '-1');
            if (!$hasRecipients) {
                $this->Session->setFlash(__('Please select at least one recipient.'));
                return $this->redirect(array('action' => 'add', 'custom_table_id' => $trigger['custom_table_id']));
            }

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
            if (!$this->_setupTriggerForm($this->request->params["named"]["custom_table_id"])) {
                return;
            }
        }
    }

    public function edit($id = null)
    {
        if (!$id) throw new NotFoundException(__('Missing trigger ID'));
        $trigger = $this->CustomTrigger->findById($id);
        if (!$trigger) throw new NotFoundException(__('Invalid trigger'));

        // FormHelper uses a hidden _method=PUT field when editing an existing
        // record, so edit submissions must accept both request methods.
        if ($this->request->is('post') || $this->request->is('put')) {
            $trigger =& $this->request->data['CustomTrigger'];
            foreach (array('notify_users', 'hod_departments') as $jsonField) {
                if (isset($trigger[$jsonField]) && is_array($trigger[$jsonField])) {
                    $selectedIds = array_values(array_filter($trigger[$jsonField]));
                    $trigger[$jsonField] = !empty($selectedIds) ? json_encode($selectedIds) : null;
                }
            }
            $allowedEvents = array('record.created', 'record.updated', 'record.deleted', 'field.changed', 'date.reminder');
            if (empty($trigger['event_name']) || !in_array($trigger['event_name'], $allowedEvents, true)) $trigger['event_name'] = 'record.created';
            $definition = !empty($trigger['custom_table_id']) ? $this->CustomTrigger->CustomTable->find('first', array(
            'recursive' => -1,
            'conditions' => array('CustomTable.id' => $trigger['custom_table_id'])
            )) : array();
            if (empty($definition['CustomTable'])) {
                $this->Session->setFlash(__('Please select a valid Custom Form.'));
                return $this->redirect(array('controller' => 'custom_tables', 'action' => 'index'));
            }
            $definedFields = $definedDateFields = array();
            foreach ((array)json_decode($definition['CustomTable']['fields'], true) as $definedField) {
                if (empty($definedField['field_name'])) continue;
                $definedFields[] = $definedField['field_name'];
                if (in_array(isset($definedField['data_type']) ? $definedField['data_type'] : '', array('date', 'datetime'), true)) $definedDateFields[] = $definedField['field_name'];
            }
            if ($trigger['event_name'] === 'field.changed' && (empty($trigger['field_name']) || !in_array($trigger['field_name'], $definedFields, true))) {
                $this->Session->setFlash(__('Please select a field for the field-change trigger.'));
                return $this->redirect(array('action' => 'edit', $id, 'custom_table_id' => $trigger['custom_table_id']));
            }
            if ($trigger['event_name'] === 'date.reminder' && (empty($trigger['date_field']) || !in_array($trigger['date_field'], $definedDateFields, true))) {
                $this->Session->setFlash(__('Please select a date field for the reminder.'));
                return $this->redirect(array('action' => 'edit', $id, 'custom_table_id' => $trigger['custom_table_id']));
            }
            $legacyActions = array('record.created' => 0, 'record.updated' => 1, 'record.deleted' => 3, 'field.changed' => 1, 'date.reminder' => null);
            $trigger['action'] = $legacyActions[$trigger['event_name']];
            if ($trigger['event_name'] !== 'field.changed') {
                $trigger['field_name'] = null;
                $trigger['changed_field_value'] = null;
            }
            if ($trigger['event_name'] !== 'date.reminder') $trigger['date_field'] = null;
            $trigger['date_offset_days'] = isset($trigger['date_offset_days']) ? (int)$trigger['date_offset_days'] : 0;
            $trigger['enabled'] = !empty($trigger['enabled']) ? 1 : 0;
            $trigger['exclude_actor'] = !empty($trigger['exclude_actor']) ? 1 : 0;
            $trigger['delay_minutes'] = !empty($trigger['delay_minutes']) ? max(0, (int)$trigger['delay_minutes']) : 0;
            if (empty($trigger['delivery_timing']) || $trigger['delivery_timing'] === 'immediate') {
                $trigger['delivery_timing'] = 'immediate';
                $trigger['delay_minutes'] = 0;
            } else {
                $trigger['delivery_timing'] = 'delayed';
            }
            $hasRecipients = !empty($trigger['notify_admins']) || !empty($trigger['notify_hods']) ||
            !empty($trigger['notify_departments']) || !empty($trigger['notify_branches']) ||
            !empty($trigger['notify_designations']) || !empty($trigger['notify_users']) ||
            !empty($trigger['hod_departments']) || (!empty($trigger['notify_user']) && $trigger['notify_user'] !== '-1');
            if (!$hasRecipients) {
                $this->Session->setFlash(__('Please select at least one recipient.'));
                return $this->redirect(array('action' => 'edit', $id, 'custom_table_id' => $trigger['custom_table_id']));
            }
            try {
                $saved = $this->CustomTrigger->save($this->request->data, false);
            } catch (PDOException $e) {
                CakeLog::write('error', 'Custom trigger '.$id.' update failed: '.$e->getMessage());
                $this->Session->setFlash(__("The custom trigger has not been updated. The database error was written to the error log."));
                return $this->redirect(array('action' => 'edit', $id, 'custom_table_id' => $this->request->data["CustomTrigger"]["custom_table_id"]));
            }

            if ($saved) {
                $this->Session->setFlash(__("The custom trigger has been updated"));
                return $this->redirect(array('action' => 'add', 'custom_table_id' => $this->request->data["CustomTrigger"]["custom_table_id"]));
            } else {
                $dataSource = $this->CustomTrigger->getDataSource();
                $databaseError = method_exists($dataSource, 'lastError') ? $dataSource->lastError() : null;
                CakeLog::write('error', 'Custom trigger '.$id.' update returned false. Validation: '.json_encode($this->CustomTrigger->validationErrors).'. Database: '.($databaseError ?: 'no database error reported'));
                $this->Session->setFlash(__("The custom trigger has not been updated. Details were written to the error log."));
                return $this->redirect(array('action' => 'edit', $id, 'custom_table_id' => $this->request->data["CustomTrigger"]["custom_table_id"]));
            }
        }else{
            $options = array('conditions' => array('CustomTrigger.' . $this-> CustomTrigger->primaryKey => $id));
            $this->request->data = $this-> CustomTrigger->find('first', $options);
        }

        $customTableId = $trigger['CustomTrigger']['custom_table_id'];
        if (!$this->_setupTriggerForm($customTableId)) {
            return;
        }
        $this->set('customTrigger', $trigger);
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
        $result = array();


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
            $this->set('hasOptions', true);
        } else {
            $this->set('result', array());
            $this->set('hasOptions', false);
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
