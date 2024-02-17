<?php
App::uses('AppModel', 'Model');
/**
 * CustomTrigger Model
 *
 * @property CustomTable $CustomTable
 * @property Branch $Branch
 * @property Department $Department
 * @property Designation $Designation
 * @property StatusUser $StatusUser
 * @property Company $Company
 */
class CustomTableProcess extends AppModel {

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
	'custom_table_id' => array(
		'uuid' => array(
			'rule' => array('uuid'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
		),
	),
	'process_id' => array(
		'uuid' => array(
			'rule' => array('uuid'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
		),
	),
	'sequence' => array(
		'notBlank' => array(
			'rule' => array('notBlank'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
		),
	)
);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
public $belongsTo = array(
	'CustomTable' => array(
		'className' => 'CustomTable',
		'foreignKey' => 'custom_table_id',
		'conditions' => '',
		'fields' => array('id', 'name','table_name','table_type'),
		'order' => ''
	),
	'Process' => array(
		'className' => 'Process',
		'foreignKey' => 'process_id',
		'conditions' => '',
		'fields' => array('id', 'name','qc_document_id'),
		'order' => ''
	)
);

}
