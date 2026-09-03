<?php
App::uses('AppModel', 'Model');
/**
 * ApprovalStep Model
 *
 * @property ApprovalProcess $ApprovalProcess
 * @property StatusUser $StatusUser
 * @property Company $Company
 */
class ApprovalStep extends AppModel {

	protected function _fieldRuleContainsStep($rules, $stepId) {
		if(is_string($rules)) $rules = json_decode($rules, true);
		if(!is_array($rules)) return false;
		if(array_key_exists($stepId, $rules)) return true;
		if(isset($rules['approval_step_id']) && (string)$rules['approval_step_id'] === (string)$stepId) return true;
		foreach($rules as $value){
			if(is_array($value) && $this->_fieldRuleContainsStep($value, $stepId)) return true;
		}
		return false;
	}

	public function customFieldDependencies($stepId) {
		if(empty($stepId)) return array();
		App::uses('ClassRegistry', 'Utility');
		$CustomTable = ClassRegistry::init('CustomTable');
		$tables = $CustomTable->find('all', array(
			'recursive' => -1,
			'conditions' => array('CustomTable.fields LIKE' => '%' . $stepId . '%'),
			'fields' => array('CustomTable.id', 'CustomTable.name', 'CustomTable.table_name', 'CustomTable.fields')
		));
		$dependencies = array();
		foreach($tables as $table){
			$fields = json_decode($table['CustomTable']['fields'], true);
			if(!is_array($fields)) continue;
			foreach($fields as $field){
				if(empty($field['approval_step_rules']) || !$this->_fieldRuleContainsStep($field['approval_step_rules'], $stepId)) continue;
				$label = isset($field['field_label']) ? base64_decode($field['field_label'], true) : '';
				$dependencies[] = array(
					'custom_table_id' => $table['CustomTable']['id'],
					'form_name' => $table['CustomTable']['name'],
					'table_name' => $table['CustomTable']['table_name'],
					'field_name' => isset($field['field_name']) ? $field['field_name'] : '',
					'field_label' => $label !== false && $label !== '' ? $label : (isset($field['field_name']) ? Inflector::humanize($field['field_name']) : '')
				);
			}
		}
		return $dependencies;
	}

	public function beforeDelete($cascade = true) {
		if(!empty($this->id) && $this->customFieldDependencies($this->id)) return false;
		return parent::beforeDelete($cascade);
	}

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'sr_no' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'approval_process_id' => array(
			'uuid' => array(
				'rule' => array('uuid'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'title' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'process_step' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'created_by' => array(
			'uuid' => array(
				'rule' => array('uuid'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'modified_by' => array(
			'uuid' => array(
				'rule' => array('uuid'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'soft_delete' => array(
			'boolean' => array(
				'rule' => array('boolean'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'ApprovalProcess' => array(
			'className' => 'ApprovalProcess',
			'foreignKey' => 'approval_process_id',
			'conditions' => '',
			'fields' => array('id', 'title'),
			'order' => ''
		),
		'Company' => array(
			'className' => 'Company',
			'foreignKey' => 'company_id',
			'conditions' => '',
			'fields' => array('id', 'name'),
			'order' => ''
		),
		'BranchIds' => array(
			'className' => 'Branch',
			'foreignKey' => 'branchid',
			'conditions' => '',
			'fields' => array('id', 'name'),
			'order' => ''
		),
		'DepartmentIds' => array(
			'className' => 'Department',
			'foreignKey' => 'departmentid',
			'conditions' => '',
			'fields' => array('id', 'name'),
			'order' => ''
		),
		'PreparedBy' => array(
			'className' => 'Employee',
			'foreignKey' => 'prepared_by',
			'conditions' => '',
			'fields' => array('id', 'name'),
			'order' => ''
		),
		'ApprovedBy' => array(
			'className' => 'Employee',
			'foreignKey' => 'approved_by',
			'conditions' => '',
			'fields' => array('id', 'name'),
			'order' => ''
		)
	);

}
