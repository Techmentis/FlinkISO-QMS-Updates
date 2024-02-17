<?php
App::uses('AppModel', 'Model');
/**
 * Company Model
 *
 * @property SystemTable $SystemTable
 * @property MasterListOfFormat $MasterListOfFormat
 */
class Setting extends AppModel {

	public $useTable = 'companies';
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
	'name' => array(
		'notempty' => array(
			'rule' => array('notempty'),
		),
	),
	'description' => array(
		'notempty' => array(
			'rule' => array('notempty'),
		),
	),
	'number_of_branches' => array(
		'numeric' => array(
			'rule' => array('numeric'),
		),
	),
	'flinkiso_start_date' => array(
		'date' => array(
			'rule' => array('date'),
		),
	),
	'flinkiso_end_date' => array(
		'date' => array(
			'rule' => array('date'),
		),
	),
	'branchid' => array(
		'notempty' => array(
			'rule' => array('notempty'),
		),
	),
	'departmentid' => array(
		'notempty' => array(
			'rule' => array('notempty'),
		),
	),
	'created_by' => array(
		'notempty' => array(
			'rule' => array('notempty'),
		),
	),
	'modified_by' => array(
		'notempty' => array(
			'rule' => array('notempty'),
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
	'SystemTable' => array(
		'className' => 'SystemTable',
		'foreignKey' => 'system_table_id',
		'conditions' => '',
		'fields' => array('id', 'name'),
		'order' => ''
	),
	'Schedule' => array(
		'className' => 'Schedule',
		'foreignKey' => 'schedule_id',
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
	'StatusUserId' => array(
		'className' => 'User',
		'foreignKey' => 'status_user_id',
		'conditions' => '',
		'fields' => array('id', 'name'),
		'order' => ''
	)
);

}
