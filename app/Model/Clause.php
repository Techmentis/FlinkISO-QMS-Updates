<?php
App::uses('AppModel', 'Model');
/**
 * Clause Model
 *
 * @property Standard $Standard
 * @property StatusUser $StatusUser
 * @property SystemTable $SystemTable
 * @property Company $Company
 * @property QcDocument $QcDocument
 */
class Clause extends AppModel {

	public $virtualFields = array(
		'name' => 'CONCAT(Clause.standard, " - ", Clause.clause, " - ", Clause.sub-clause, " - ", Clause.title)',
		'intclause' => 'CAST(Clause.clause as UNSIGNED)'
	);
	public $displayField = 'name';

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
	'standard_id' => array(
		'uuid' => array(
			'rule' => array('uuid'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
		),
	),
	'clause' => array(
		'notBlank' => array(
			'rule' => array('notBlank'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
		),
	),
	'branchid' => array(
		'uuid' => array(
			'rule' => array('uuid'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
		),
	),
	'departmentid' => array(
		'uuid' => array(
			'rule' => array('uuid'),
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
);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
public $belongsTo = array(
	'Standard' => array(
		'className' => 'Standard',
		'foreignKey' => 'standard_id',
		'conditions' => '',
		'fields' => array('id', 'name'),
		'order' => ''
	),
	'SystemTable' => array(
		'className' => 'SystemTable',
		'foreignKey' => 'system_table_id',
		'conditions' => '',
		'fields' => array('id', 'name'),
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

/**
 * hasMany associations
 *
 * @var array
 */
public $hasMany = array(
	'QcDocument' => array(
		'className' => 'QcDocument',
		'foreignKey' => 'clause_id',
		'dependent' => false,
		'conditions' => '',
		'fields' => '',
		'order' => '',
		'limit' => '',
		'offset' => '',
		'exclusive' => '',
		'finderQuery' => '',
		'counterQuery' => ''
	)
);




}
