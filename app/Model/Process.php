<?php
App::uses('AppModel', 'Model');

class Process extends AppModel {

	public $useTable = 'processes';


	public $validate = array(
		'name' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
			),
		),
		'process_definition' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
			),
		),
		'process_objective_and_metrics' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
			),
		),
		'process_owners' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
			),
		),
		'applicable_to_branches' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
			),
		),
		'process_output' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
			),
		),
		'standards' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
			),
		),
		'clauses' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
			),
		),
	);

	public $belongsTo = array(
		'QcDocument' => array(
			'className' => 'QcDocument',
			'foreignKey' => 'qc_document_id',
			'conditions' => '',
			'fields' => array(),
			'order' => ''
		),
		'Company' => array(
			'className' => 'Company',
			'foreignKey' => 'company_id',
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
		),
		'CreatedBy' => array(
			'className' => 'User',
			'foreignKey' => 'create_by',
			'conditions' => '',
			'fields' => array('id', 'username'),
			'order' => ''
		),
		'ModifiedBy' => array(
			'className' => 'User',
			'foreignKey' => 'modified_by',
			'conditions' => '',
			'fields' => array('id', 'username'),
			'order' => ''
		),
		'ProcessOwner' => array(
			'className' => 'Department',
			'foreignKey' => 'process_owners',
			'conditions' => '',
			'fields' => array('id', 'name'),
			'order' => ''
		),
		'ApplicableToBranch' => array(
			'className' => 'Branch',
			'foreignKey' => 'applicable_to_branches',
			'conditions' => '',
			'fields' => array('id', 'name'),
			'order' => ''
		),
		'InputProcess' => array(
			'className' => 'Process',
			'foreignKey' => 'input_processes',
			'conditions' => '',
			'fields' => array('id', 'name'),
			'order' => ''
		),
		'OutputProcess' => array(
			'className' => 'Process',
			'foreignKey' => 'output_processes',
			'conditions' => '',
			'fields' => array('id', 'name'),
			'order' => ''
		),
		'Standard' => array(
			'className' => 'Standard',
			'foreignKey' => 'standards',
			'conditions' => '',
			'fields' => array('id', 'name'),
			'order' => ''
		),
		'Clause' => array(
			'className' => 'Clause',
			'foreignKey' => 'clauses',
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
	);

/**
 * hasMany associations
 *
 * @var array
 */
public $hasMany = array(
	'CustomTable' => array(
		'className' => 'CustomTable',
		'foreignKey' => 'process_id',
		'dependent' => false,
		'conditions' => array('custom_table_id'=>''),
		'fields' => array('id', 'qc_document_id','name','table_name','table_version','process_id','custom_table_id','description'),
		'order' => '',
		'limit' => '',
		'offset' => '',
		'exclusive' => '',
		'finderQuery' => '',
		'counterQuery' => ''
	)
);

public $customArray = array(
	'file_types'=>array(
		0=>'Add Content',
		1=>'Upload File',
	),'data_types'=>array(
		0=>'Document',
		1=>'Data',
		2=>'Both'
	),
	'document_status' => array(
		'0' => 'Draft',
		'1' => 'Published/Issued',
		'2' => 'Approved',
		'3' => 'Under Revision',
		'4' => 'Archived',
		'5' => 'Awaiting Issue'
	),'bullets' => array(
		'0' => 'Show',
		'1' => 'Hide'
	),'it_categories' => array(
		'0' => 'Confidential',
		'1' => 'Internal',
		'2' => 'Restricted',
		'3' => 'Public'
	),'document_type' => 
	array(
		'0'=>'Manual',
		'1'=>'Procedure',
		'2'=>'Work Instructions',
		'3'=>'Policy',
		'4'=>'Checklist',
		'5'=>'Formats',
		'6'=>'Template',
		'7'=>'Masters',
	)
);
}
