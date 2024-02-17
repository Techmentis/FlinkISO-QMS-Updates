<?php
App::uses('AppModel', 'Model');
/**
 * User Model
 *
 * @property Employee $Employee
 * @property Department $Department
 * @property Branch $Branch
 * @property Language $Language
 * @property SystemTable $SystemTable
 * @property MasterListOfFormat $MasterListOfFormat
 * @property Approval $Approval
 * @property CourierRegister $CourierRegister
 * @property DataBackUp $DataBackUp
 * @property DataType $DataType
 * @property FileUpload $FileUpload
 * @property MessageUserInbox $MessageUserInbox
 * @property MessageUserSent $MessageUserSent
 * @property MessageUserThrash $MessageUserThrash
 * @property Message $Message
 * @property Task $Task
 * @property UserSession $UserSession
 */
class User extends AppModel {

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
	'employee_id' => array(
		'notBlank' => array(
			'rule' => array('notBlank'),
		),
	),
	'username' => array(
		'notBlank' => array(
			'rule' => array('notBlank'),
		),
	),
	'password' => array(
		'notBlank' => array(
			'rule' => array('notBlank'),
		),
	),
	'department_id' => array(
		'notBlank' => array(
			'rule' => array('notBlank'),
		),
	),
	'branch_id' => array(
		'notBlank' => array(
			'rule' => array('notBlank'),
		),
	),
	'last_login' => array(
		'datetime' => array(
			'rule' => array('datetime'),
		),
	),
	'last_activity' => array(
		'datetime' => array(
			'rule' => array('datetime'),
		),
	),
	'branchid' => array(
		'notBlank' => array(
			'rule' => array('notBlank'),
		),
	),
	'departmentid' => array(
		'notBlank' => array(
			'rule' => array('notBlank'),
		),
	),
	'created_by' => array(
		'notBlank' => array(
			'rule' => array('notBlank'),
		),
	),
	'modified_by' => array(
		'notBlank' => array(
			'rule' => array('notBlank'),
		),
	),
	'name' => array(
		'notBlank' => array(
			'rule' => array('notBlank'),
		),
	),
	'company' => array(
		'notBlank' => array(
			'rule' => array('notBlank'),
		),
	),
	'email' => array(
		'notBlank' => array(
			'rule' => array('notBlank'),
		),'email' => array(
			'rule' => array('email'),
		),
	),
	'phone' => array(
		'notBlank' => array(
			'rule' => array('notBlank'),
		),'phone' => array(
			'rule' => array('phone'),
		),
	),
);
/**
 * belongsTo associations
 *
 * @var array
 */
public $belongsTo = array(        
	'Employee' => array(
		'className' => 'Employee',
		'foreignKey' => 'employee_id',
		'conditions' => '',
		'fields' => array('id', 'name', 'personal_email', 'office_email','is_hod','designation_id','department_id','branch_id'),
		'order' => ''
	),
	'Department' => array(
		'className' => 'Department',
		'foreignKey' => 'department_id',
		'conditions' => '',
		'fields' => array('id', 'name'),
		'order' => ''
	),
	'Branch' => array(
		'className' => 'Branch',
		'foreignKey' => 'branch_id',
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
	'Company' => array(
		'className' => 'Company',
		'foreignKey' => 'company_id',
		'conditions' => '',
		'fields' => array('id', 'name','is_smtp'),
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

/*
 * Custom validation method to ensure that the two entered passwords match
 *
 * @param string $password Password
 * @return boolean Success
 */
public function confirmPassword($password = null) {
	if ((isset($this->data[$this->alias]['password']) && isset($password['temppassword']))
		&& !empty($password['temppassword'])
		&& ($this->data[$this->alias]['password'] === $password['temppassword'])) {
		return true;
}
return false;
}

public function generateToken($length = 10) {
	$possible = '0123456789abcdefghijklmnopqrstuvwxyz';
	$token = "";
	$i = 0;
	while ($i < $length) {
		$char = substr($possible, mt_rand(0, strlen($possible) - 1), 1);
		if (!stristr($token, $char)) {
			$token .= $char;
			$i++;
		}
	}
	return $token;
}




}
