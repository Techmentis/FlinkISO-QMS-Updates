<?php
App::uses('AppModel', 'Model');
/**
 * History Model
 *
 * @property UserSession $UserSession
 * @property SystemTable $SystemTable
 * @property MasterListOfFormat $MasterListOfFormat
 */
class History extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
public $validate = array(
	'sr_no' => array(
		'numeric' => array(
			'rule' => array('numeric'),
		),
	),
	'branchid' => array(
		'notempty' => array(
			'rule' => array('notBlank'),
		),
	),
	'departmentid' => array(
		'notempty' => array(
			'rule' => array('notBlank'),
		),
	),
	'created_by' => array(
		'notempty' => array(
			'rule' => array('notBlank'),
		),
	),
	'modified_by' => array(
		'notempty' => array(
			'rule' => array('notBlank'),
		),
	),
);
/**
 * belongsTo associations
 *
 * @var array
 */
public $belongsTo = array(
	'ApprovedBy' => array(
		'className' => 'Employee',
		'foreignKey' => 'approved_by',
		'conditions' => '',
		'fields' => array('id', 'name'),
		'order' => '' ),
	'PreparedBy' => array(
		'className' => 'Employee',
		'foreignKey' => 'prepared_by',
		'conditions' => '',
		'fields' => array('id', 'name'),
		'order' => ''
	),
	'UserSession' => array(
		'className' => 'UserSession',
		'foreignKey' => 'user_session_id',
		'conditions' => '',
		'fields' => array('id'),
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
	'Company' => array(
		'className' => 'Company',
		'foreignKey' => 'company_id',
		'conditions' => '',
		'fields' => array('id', 'name'),
		'order' => ''
	),
	'StatusUserId' => array(
		'className' => 'User',
		'foreignKey' => 'status_user_id',
		'conditions' => '',
		'fields' => array('id', 'name'),
		'order' => ''
	),
	'CreatedBy' => array(
		'className' => 'User',
		'foreignKey' => 'created_by',
		'conditions' => '',
		'fields' => array('id', 'name'),
		'order' => ''
	)
);
}
