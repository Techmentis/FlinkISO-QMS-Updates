<?php
App::uses('AppModel', 'Model');
/**
 * QcDocument Model
 *
 * @property QcDocumentCategory $QcDocumentCategory
 * @property Clause $Clause
 * @property Standard $Standard
 * @property IssuingAuthority $IssuingAuthority
 * @property Cr $Cr
 * @property OldCr $OldCr
 * @property QcDocument $ParentQcDocument
 * @property ParentDocument $ParentDocument
 * @property User $User
 * @property PdfFooter $PdfFooter
 * @property SystemTable $SystemTable
 * @property UserSession $UserSession
 * @property MasterListOfFormat $MasterListOfFormat
 * @property Division $Division
 * @property Company $Company
 * @property QcDocument $ChildQcDocument
 */
class QcDocument extends AppModel {

	public $displayField = 'name';

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
	'title' => array(
		'notBlank' => array(
			'rule' => array('notBlank'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
		),
	),
	'document_number' => array(
		'notBlank' => array(
			'rule' => array('notBlank'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
		),
	),
	'revision_number' => array(
		'notBlank' => array(
			'rule' => array('notBlank'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
		),
	),
	'date_of_issue' => array(
		'date' => array(
			'rule' => array('date'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
		),
	),
	'document_type' => array(
		'notBlank' => array(
			'rule' => array('notBlank'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
		),
	),
	'document_status' => array(
		'notBlank' => array(
			'rule' => array('notBlank'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
		),
	),
	'standard_id' => array(
		'notBlank' => array(
			'rule' => array('notBlank'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
		),
	),
	'clause_id' => array(
		'notBlank' => array(
			'rule' => array('notBlank'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
		),
	),
	'data_type' => array(
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
	'QcDocumentCategory' => array(
		'className' => 'QcDocumentCategory',
		'foreignKey' => 'qc_document_category_id',
		'conditions' => '',
		'fields' => array('id', 'name'),
		'order' => ''
	),
	'Clause' => array(
		'className' => 'Clause',
		'foreignKey' => 'clause_id',
		'conditions' => '',
		'fields' => array('id', 'title'),
		'order' => ''
	),
	'Standard' => array(
		'className' => 'Standard',
		'foreignKey' => 'standard_id',
		'conditions' => '',
		'fields' => array('id', 'name'),
		'order' => ''
	),
	'IssuingAuthority' => array(
		'className' => 'Employee',
		'foreignKey' => 'issuing_authority_id',
		'conditions' => '',
		'fields' => array('id', 'name'),
		'order' => ''
	),
	'Cr' => array(
		'className' => 'DocumentChangeRequest',
		'foreignKey' => 'cr_id',
		'conditions' => '',
		'fields' => array('id', 'name'),
		'order' => ''
	),
	'OldCr' => array(
		'className' => 'DocumentChangeRequest',
		'foreignKey' => 'old_cr_id',
		'conditions' => '',
		'fields' => array('id', 'name'),
		'order' => ''
	),
	'ParentQcDocument' => array(
		'className' => 'QcDocument',
		'foreignKey' => 'parent_id',
		'conditions' => '',
		'fields' => array('id', 'title'),
		'order' => ''
	),
	'ParentDocument' => array(
		'className' => 'QcDocument',
		'foreignKey' => 'parent_document_id',
		'conditions' => '',
		'fields' => array('id', 'title'),
		'order' => ''
	),
	'User' => array(
		'className' => 'User',
		'foreignKey' => 'user_id',
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
	'UserSession' => array(
		'className' => 'UserSession',
		'foreignKey' => 'user_session_id',
		'conditions' => '',
		'fields' => array('id'),
		'order' => ''
	),
	'Company' => array(
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
	),
	'IssuedBy' => array(
		'className' => 'Employee',
		'foreignKey' => 'issued_by',
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
	)
);

/**
 * hasMany associations
 *
 * @var array
 */
public $hasMany = array(
	'ChildQcDocument' => array(
		'className' => 'QcDocument',
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
	),'CustomTable' => array(
		'className' => 'CustomTable',
		'foreignKey' => 'qc_document_id',
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


public $customArray = 
array(
	'fileTypes'=>array(
		0=>'Add Content',
		1=>'Upload File',
	),'dataTypes'=>array(
		0=>'Document',
		1=>'Data',
		2=>'Both'
	),
	'documentStatuses' => array(
		'0' => 'Draft',
		'1' => 'Published/Issued',
		'2' => 'Approved',
		'3' => 'Under Revision',
		'4' => 'Archived',
		'5' => 'Awaiting Issue',
		'6' => 'Update Document',		
	),'bullets' => array(
		'0' => 'Show',
		'1' => 'Hide'
	),'itCategories' => array(
		'0' => 'Confidential',
		'1' => 'Internal',
		'2' => 'Restricted',
		'3' => 'Public'
	),'documentTypes' => 
	array(
		'0'=>'Manual',
		'1'=>'Procedure',
		'2'=>'Process',
		'3'=>'Work Instructions',
		'4'=>'Policy',
		'5'=>'Checklist',
		'6'=>'Formats',
		'7'=>'Template',
		'8'=>'Masters',
	)
);

}
