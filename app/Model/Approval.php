<?php
App::uses('AppModel', 'Model');
/**
 * Approval Model
 *
 * @property User $User
 * @property StatusUser $StatusUser
 * @property Company $Company
 */
class Approval extends AppModel {

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
	'model_name' => array(
		'notBlank' => array(
			'rule' => array('notBlank'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
		),
	),
	'controller_name' => array(
		'notBlank' => array(
			'rule' => array('notBlank'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
		),
	),
	'record' => array(
		'uuid' => array(
			'rule' => array('uuid'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
		),
	),
	'from' => array(
		'uuid' => array(
			'rule' => array('uuid'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
		),
	),
	'user_id' => array(
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
	'User' => array(
		'className' => 'User',
		'foreignKey' => 'user_id',
		'conditions' => '',
		'fields' => array('id', 'name'),
		'order' => ''
	),
	'From' => array(
		'className' => 'User',
		'foreignKey' => 'from',
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
	),'ApprovalProcess' => array(
		'className' => 'ApprovalProcess',
		'foreignKey' => 'Approval_process_id',
		'conditions' => '',
		'fields' => array('id', 'title'),
		'order' => ''
	),'ApprovalStep' => array(
		'className' => 'ApprovalStep',
		'foreignKey' => 'approval_step_id',
		'conditions' => '',
		'fields' => array('id', 'title'),
		'order' => ''
	)
);

/**
 * hasMany associations
 *
 * @var array
 */
public $hasMany = array(
	'ApprovalComment' => array(
		'className' => 'ApprovalComment',
		'foreignKey' => 'approval_id',
		'dependent' => false,
		'conditions' => '',
		'fields' => '',
		'order' => array('ApprovalComment.sr_no'=>'DESC'),
		'limit' => '',
		'offset' => '',
		'exclusive' => '',
		'finderQuery' => '',
		'counterQuery' => '',
		'dependent' => true
	)
);






}
