<?php
App::uses('AppModel', 'Model');
/**
 * UserSession Model
 *
 * @property User $User
 * @property Employee $Employee
 * @property Evidence $Evidence
 * @property FileUpload $FileUpload
 * @property History $History
 */
class UserSession extends AppModel {

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
	'ip_address' => array(
		'notBlank' => array(
			'rule' => array('notBlank'),
		),
	),
	'start_time' => array(
		'datetime' => array(
			'rule' => array('datetime'),
		),
	),
	'end_time' => array(
		'datetime' => array(
			'rule' => array('datetime'),
		),
	),
	'user_id' => array(
		'notBlank' => array(
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
	'User' => array(
		'className' => 'User',
		'foreignKey' => 'user_id',
		'conditions' => '',
		'fields' => array('id', 'name'),
		'order' => ''
	),
	'Employee' => array(
		'className' => 'Employee',
		'foreignKey' => 'employee_id',
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
	)
);

/**
 * hasMany associations
 *
 * @var array
 */
public $hasMany = array(
	'History' => array(
		'className' => 'History',
		'foreignKey' => 'user_session_id',
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
