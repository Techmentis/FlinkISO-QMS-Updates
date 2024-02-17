<?php
App::uses('AppModel', 'Model');
/**
 * Approval Model
 *
 * @property User $User
 * @property StatusUser $StatusUser
 * @property Company $Company
 */
class GraphPanel extends AppModel {

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
		'notBlank' => array(
			'rule' => array('notBlank'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
		),
	),
	'filed_name' => array(
		'notBlank' => array(
			'rule' => array('notBlank'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
		),
	),
	'date_condition' => array(
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
		'fields' => array('id', 'name','table_name','fields'),
		'order' => ''
	)
);

public $customArray = array(
	'dateConditions'=>array(
		0=>'1 Week',
		1=>'Last 15 Days',
		2=>'This Month',
		3=>'Previous Month',
		4=>'Previous 3 Months',
		5=>'Previous 6 Month',
		6=>'All Time',
	),'graphTypes'=>array(
		0=>'pie',
		1=>'doughnut',
		2=>'bar',
		3=>'line',			
	),'dataTypes'=>array(
		0=>'Count',
		1=>'Sum',
		2=>'Avg'		
	)
);

}
