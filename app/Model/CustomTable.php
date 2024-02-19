<?php
App::uses('AppModel', 'Model');
/**
 * CustomTable Model
 *
 * @property StatusUser $StatusUser
 * @property Company $Company
 */
class CustomTable extends AppModel {

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
	'table_name' => array(
		'notBlank' => array(
			'rule' => array('notBlank'),
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
	'password' => array(
		'notBlank' => array(
			'rule' => array('notBlank'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
		),
	),
	're-password' => array(
		'notBlank' => array(
			'rule' => array('notBlank'),
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
	'ParentTable' => array(
		'className' => 'CustomTable',
		'foreignKey' => 'custom_table_id',
		'conditions' => '',
		'fields' => array('id','name','table_name','table_version', 'fields', 'has_many'),
		'order' => ''
	),'QcDocument' => array(
		'className' => 'QcDocument',
		'foreignKey' => 'qc_document_id',
		'conditions' => '',
		'fields' => array('id', 'name', 'title','document_number','revision_number','file_type','departments','branches','user_id','schedule_id','publish','parent_document_id'),
		'order' => ''
	),'Process' => array(
		'className' => 'Process',
		'foreignKey' => 'process_id',
		'conditions' => '',
		'fields' => array('id', 'name', 'file_name','file_key','file_type','publish','process_owners','applicable_to_branches'),
		'order' => ''
	),'Company' => array(
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

public $hasMany = array(
	'CustomTrigger' => array(
		'className' => 'CustomTrigger',
		'foreignKey' => 'custom_table_id',
		'dependent' => false,
		'conditions' => '',
		'fields' => '',
		'order' => array('CustomTrigger.sr_no'=>'DESC'),
		'limit' => '',
		'offset' => '',
		'exclusive' => '',
		'finderQuery' => '',
		'counterQuery' => ''
	),'RecordLock' => array(
		'className' => 'RecordLock',
		'foreignKey' => 'lock_table_id',
		'dependent' => false,
		'conditions' => '',
		'fields' => '',
		'order' => array(),
		'limit' => '',
		'offset' => '',
		'exclusive' => '',
		'finderQuery' => '',
		'counterQuery' => ''
	),'CustomTableTask' => array(
		'className' => 'CustomTableTask',
		'foreignKey' => 'custom_table_id',
		'dependent' => false,
		'conditions' => '',
		'fields' => '',
		'order' => array(),
		'limit' => '',
		'offset' => '',
		'exclusive' => '',
		'finderQuery' => '',
		'counterQuery' => ''
	),'GraphPanel' => array(
		'className' => 'GraphPanel',
		'foreignKey' => 'custom_table_id',
		'dependent' => false,
		'conditions' => '',
		'fields' => '',
		'order' => array(),
		'limit' => '',
		'offset' => '',
		'exclusive' => '',
		'finderQuery' => '',
		'counterQuery' => ''
	),'File' => array(
		'className' => 'File',
		'foreignKey' => 'custom_table_id',
		'dependent' => false,
		'conditions' => '',
		'fields' => '',
		'order' => array(),
		'limit' => '',
		'offset' => '',
		'exclusive' => '',
		'finderQuery' => '',
		'counterQuery' => ''
	),'CustomCode' => array(
		'className' => 'CustomCode',
		'foreignKey' => 'custom_table_id',
		'dependent' => false,
		'conditions' => '',
		'fields' => '',
		'order' => array(),
		'limit' => '',
		'offset' => '',
		'exclusive' => '',
		'finderQuery' => '',
		'counterQuery' => ''
	),'CustomTableProcess' => array(
		'className' => 'CustomTableProcess',
		'foreignKey' => 'custom_table_id',
		'dependent' => false,
		'conditions' => '',
		'fields' => '',
		'order' => array(),
		'limit' => '',
		'offset' => '',
		'exclusive' => '',
		'finderQuery' => '',
		'counterQuery' => ''
	),
);

public $customArray = array(
	'fieldTypes' => array(
		0=>'varchar',
		1=>'text',
		2=>'int',
		3=>'tinyint',
		4=>'float',	
		5=>'date',
		6=>'datetime',
		7=>'file'

	),

	'displayTypes' => array(
		0=>'text',
		1=>'radio',
		2=>'checkbox',
		3=>'dropdown',
		4=>'multiple',
		5=>'break',
		6=>'file',
		7=>'comments',
		8=>'hidden'
	),
	'tableTypes' => array(
		0=>'QC Document',
		1=>'Process',
	),
	'dataTypes'=>array(
		0=>'Data',
		1=>'Document',
		2=>'Both',	
	)
);

}
