<?php
App::uses('AppModel', 'Model');
/**
 * Designation Model
 *
 * @property Designation $ParentDesignation
 * @property StatusUser $StatusUser
 * @property SystemTable $SystemTable
 * @property Division $Division
 * @property Designation $ChildDesignation
 * @property Employee $Employee
 */
class Designation extends AppModel {

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
	'name' => array(
		'notBlank' => array(
			'rule' => array('notBlank'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
		),
	),
	'level' => array(
		'numeric' => array(
			'rule' => array('numeric'),
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
	'ParentDesignation' => array(
		'className' => 'Designation',
		'foreignKey' => 'parent_id',
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
	'ChildDesignation' => array(
		'className' => 'Designation',
		'foreignKey' => 'parent_id',
		'dependent' => false,
		'conditions' => '',
		'fields' => '',
		'order' => '',
		'limit' => '',
		'offset' => '',
		'exclusive' => '',
		'finderQuery' => '',
		'counterQuery' => ''
	),
	'Employee' => array(
		'className' => 'Employee',
		'foreignKey' => 'designation_id',
		'dependent' => false,
		'conditions' => '',
		'fields' => array('Employee.id','Employee.name'),
		'order' => '',
		'limit' => '',
		'offset' => '',
		'exclusive' => '',
		'finderQuery' => '',
		'counterQuery' => ''
	)
);


}
