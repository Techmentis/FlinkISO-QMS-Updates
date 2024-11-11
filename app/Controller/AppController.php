<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc.
 * (http://cakefoundation.org)
 * @link http://cakephp.org CakePHP(tm) Project
 * @package app.Controller
 * @since CakePHP(tm) v 0.2.9
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
App::uses('Controller', 'Controller');
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');
App::uses('Xml', 'Utility');
App::uses('ConnectionManager', 'Model');
/** adding new PDF plug in **/
Configure::write('CakePdf', array(
	'engine' => 'CakePdf.WkHtmlToPdf', 
	'binary' => Configure::read('WkHtmlToPdfPath'), 
	'crypto' => 'CakePdf.Pdftk', 	
	'options' => array(
		'print-media-type' => false, 
		'outline' => false, 
		'dpi' => 96, 
		'header-html' => Router::url('/', true) . 'files/pdf_header.html', 
		'footer-center' => 'Page [page] of [toPage]', 
		'footer-right' => 'Confidential Document. All rights reserved.', 
		'footer-font-size' => '8', 
		'footer-line' => true, 
		'header-line' => true,
		'enable-local-file-access' => true,
		'header-font-name' => 'Trebuchet MS', 'footer-font-name' => 'Trebuchet MS',), 
	'margin' => array(
		'bottom' => 10, 
		'left' => 10, 
		'right' => 10, 
		'top' => 25
	), 
	'title' => 'Generated via FlinkISO', 
	'orientation' => 'portrait', 
	'download' => true,

)
);
/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
App::import('Sanitize');
class AppController extends Controller {
	public $components = array('RequestHandler', 'Session','Gzip.Gzip');
	public $helpers = array('Js', 'Session', 'Paginator');
	public $user = null;
	public $userids = null;
	public $message_count = null;
	public $notification_count = null;
	public $branchIDYes = false;
	
	public function _check_login() {
		
		Configure::write("files", WWW_ROOT . 'files' . DS . $this->Session->read('User.company_id'));
		Configure::write("files_url", Router::url('/', true) . 'files/' . $this->Session->read('User.company_id'));
		
		Configure::write("path", WWW_ROOT . 'files' . DS . $this->Session->read('User.company_id') . DS . $this->request->controller);
		Configure::write("url", Router::url('/', true) . 'files/' . $this->Session->read('User.company_id') . '/' . $this->request->controller);
		Configure::write("common_path", 'files' . DS . $this->Session->read('User.company_id') . DS . $this->request->controller);
		
		$ignore = array('install_updates', 'register','activate', 'send_otp', 'generate_invoice', 'renew', 'invoices', 'check_invoice_date', 'login', 'logout', 'forgot_password', 'reset_password', 'save_doc','onlyofficechk','save_template','save_rec_doc','save_custom_docs','save_file');
		
		if (empty($this->Session->read('User.id')) && !in_array($this->action, $ignore)) {
			$this->Session->setFlash(__('Login to continue'));
			$this->redirect(array('controller' => 'users', 'action' => 'login'));
		}else{
			$ignore = array('install_updates', 'register','activate', 'send_otp', 'generate_invoice', 'renew', 'invoices', 'check_invoice_date', 'login', 'logout', 'forgot_password', 'reset_password', 'save_doc','onlyofficechk','save_template','save_rec_doc','save_custom_docs','save_file');

			if (empty($this->Session->read('User.id')) && !in_array($this->action, $ignore)) {
				try{
					$this->loadModel('UserSession');
					$this->UserSession->read(null,$this->Session->read('User.user_session_id'));
					$this->UserSession->set('end_time',date('Y-m-d H:i:d'));
					if($this->Session->read('User.id'))$this->UserSession->save();

					$base = explode('/' , $this->request->base);
					if($this->Session->read('User.dir_name') != $base[count($base)-1]){ 
						$ignore = array('login', 'logout','onlyofficechk');
						if(!in_array($this->action,$ignore)){
							$this->Session->write('User.id', NULL);
							$this->Session->destroy('User');
							$this->redirect(array('controller' => 'users', 'action' => 'logout')); 
						}
					}

				}catch(Exception $e){

				}
			}
		}
		if($this->Session->read('User.user_session_id')){
			$this->loadModel('UserSession');
			$this->UserSession->read(null,$this->Session->read('User.user_session_id'));
			$this->UserSession->set('end_time',date('Y-m-d H:i:s'));
			$this->UserSession->save(); 
		}		
	}
	
	public function beforeRender() {
		$this->_check_login();
		if($this->Session->read('User')){
			$this->_table_menu();
			$this->_check_lock();
			if($this->action == 'view')$this->_view();
			$this->_get_approver_list();
			$this->_find_parent();
		}
	}

	public function _find_parent(){ 
		if($this->action == 'add'){
			$this->loadModel('CustomTable');
			$customTable = $this->CustomTable->find('first',array('fields'=>array('CustomTable.id','CustomTable.custom_table_id','CustomTable.qc_document_id'),'conditions'=>array('CustomTable.id'=>$this->request->params['named']['custom_table_id']),'recursive'=>-1));
			if($customTable['CustomTable']['custom_table_id']){
				$parentCustomTable = $this->CustomTable->find('first',array('fields'=>array('CustomTable.id','CustomTable.custom_table_id','CustomTable.qc_document_id','CustomTable.table_name'),'conditions'=>array('CustomTable.id'=>$customTable['CustomTable']['custom_table_id']),'recursive'=>-1));
			}else{
				$qcDoc = $this->CustomTable->QcDocument->find('first',array('recursive'=>-1, 'fields'=>array('QcDocument.title', 'QcDocument.id','QcDocument.parent_document_id'), 'conditions'=>array( 'QcDocument.id'=>$customTable['CustomTable']['qc_document_id'])));
				if($qcDoc){
					
					$parentCustomTable = $this->CustomTable->find('first',array('fields'=>array('CustomTable.id','CustomTable.custom_table_id','CustomTable.qc_document_id','CustomTable.table_name'),'conditions'=>array('CustomTable.qc_document_id'=>$qcDoc['QcDocument']['parent_document_id']),'recursive'=>-1)); 
				}
			}

			if($parentCustomTable && $this->request->is('ajax') == false){
				$this->Session->setFlash('You can not add data directly to this table. Redirecting parent table.'); 
				$this->redirect(array('controller' => $parentCustomTable['CustomTable']['table_name'], 'action' => 'index','custom_table_id'=>$parentCustomTable['CustomTable']['id'],'qc_document_id'=>$parentCustomTable['CustomTable']['qc_document_id'])); 
			}
		}
		if($this->action == 'edit'){
			//check if request data as paret_id
			// if yes, check if custom table has parent 
			// if no, check if custom table qc document has parent
			if($this->request->data[Inflector::classify($this->request->controller)]['parent_id']){
				$this->loadModel('CustomTable');
				$customTable = $this->CustomTable->find('first',array('fields'=>array('CustomTable.id','CustomTable.custom_table_id','CustomTable.qc_document_id'),'conditions'=>array('CustomTable.id'=>$this->request->params['named']['custom_table_id']),'recursive'=>-1));
				if($customTable['CustomTable']['custom_table_id']){
					$parentCustomTable = $this->CustomTable->find('first',array('fields'=>array('CustomTable.id','CustomTable.custom_table_id','CustomTable.qc_document_id','CustomTable.table_name'),'conditions'=>array('CustomTable.id'=>$customTable['CustomTable']['custom_table_id']),'recursive'=>-1));
				}else{
					$qcDoc = $this->CustomTable->QcDocument->find('first',array('recursive'=>-1, 'fields'=>array('QcDocument.title', 'QcDocument.id','QcDocument.parent_document_id'), 'conditions'=>array( 'QcDocument.id'=>$customTable['CustomTable']['qc_document_id'])));
					if($qcDoc){
						
						$parentCustomTable = $this->CustomTable->find('first',array('fields'=>array('CustomTable.id','CustomTable.custom_table_id','CustomTable.qc_document_id','CustomTable.table_name'),'conditions'=>array('CustomTable.qc_document_id'=>$qcDoc['QcDocument']['parent_document_id']),'recursive'=>-1)); 
					}
				}

				if($parentCustomTable){
					$this->set('parent_table_name',$parentCustomTable['CustomTable']['table_name']);
				}
			}else{
				// echo $this->request->data[Inflector::classify($this->request->controller)]['parent_id'];
			}
		}

		if($this->action == 'view'){
			//check if request data as paret_id
			// if yes, check if custom table has parent 
			// if no, check if custom table qc document has parent
			if(isset(Inflector::singularize(Inflector::variable($this->request->controller))[Inflector::classify($this->request->controller)]['parent_id']) && Inflector::singularize(Inflector::variable($this->request->controller))[Inflector::classify($this->request->controller)]['parent_id']){
				$this->loadModel('CustomTable');
				$customTable = $this->CustomTable->find('first',array('fields'=>array('CustomTable.id','CustomTable.custom_table_id','CustomTable.qc_document_id'),'conditions'=>array('CustomTable.id'=>$this->request->params['named']['custom_table_id']),'recursive'=>-1));
				if($customTable['CustomTable']['custom_table_id']){
					$parentCustomTable = $this->CustomTable->find('first',array('fields'=>array('CustomTable.id','CustomTable.custom_table_id','CustomTable.qc_document_id','CustomTable.table_name'),'conditions'=>array('CustomTable.id'=>$customTable['CustomTable']['custom_table_id']),'recursive'=>-1));
				}else{
					$qcDoc = $this->CustomTable->QcDocument->find('first',array('recursive'=>-1, 'fields'=>array('QcDocument.title', 'QcDocument.id','QcDocument.parent_document_id'), 'conditions'=>array( 'QcDocument.id'=>$customTable['CustomTable']['qc_document_id'])));
					if($qcDoc){
						
						$parentCustomTable = $this->CustomTable->find('first',array('fields'=>array('CustomTable.id','CustomTable.custom_table_id','CustomTable.qc_document_id','CustomTable.table_name'),'conditions'=>array('CustomTable.qc_document_id'=>$qcDoc['QcDocument']['parent_document_id']),'recursive'=>-1)); 
					}
				}

				if($parentCustomTable){
					$this->set('parent_table_name',$parentCustomTable['CustomTable']['table_name']);
				}
			}else{
				// echo $this->request->data[Inflector::classify($this->request->controller)]['parent_id'];
			}
		}
	}

	public function dir_size() {
		$folder = new Folder(WWW_ROOT . DS . 'files' . DS . $this->Session->read('User.company_id'));
		$size = ($folder->dirsize() / 1073741824);
		// $ezie = 20;
		
		$dbsize = $this->_get_db_size();

		$this->set('dbsize',($dbsize[0][0]['Bytes']/100000000));
		$this->set('appsize', $size);
		$this->render('/Elements/dirsize');
	}

	public function _get_db_size(){
		$this->loadModel('UsageDetail');
		$dataSource = ConnectionManager::getDataSource('default');
		$dbname = $dataSource->config['database'];
		$sql = "
		SELECT
		table_schema AS $dbname,
		ROUND(SUM(data_length + index_length)) AS 'Bytes'
		FROM
		information_schema.tables
		WHERE
		table_schema = '".$dbname."'
		GROUP BY
		table_schema;

		";
		$dbsize = $this->UsageDetail->query($sql);
		return $dbsize[0][0]['Bytes'];
	}

	public function beforeFilter() {
		$this->_check_login();
		if($this->Session->read('User')){

			Configure::write("files", WWW_ROOT . 'files' . DS . $this->Session->read('User.company_id'));
			Configure::write("files_url", Router::url('/', true) . 'files/' . $this->Session->read('User.company_id'));

			Configure::write("path", WWW_ROOT . 'files' . DS . $this->Session->read('User.company_id') . DS . $this->request->controller);
			Configure::write("url", Router::url('/', true) . 'files/' . $this->Session->read('User.company_id') . '/' . $this->request->controller);
			Configure::write("common_path", 'files' . DS . $this->Session->read('User.company_id') . DS . $this->request->controller);

			if(
				($this->request->is('ajax') == true && $this->request->params['named']['allow_access_user'] != $this->Session->read('User.id')) || 
				$this->request->data['Access']['skip_access_check'] == 1 && $this->request->data['Access']['allow_access_user'] == $this->Session->read('User.id')){
				// this will allow user access without checking from access.
				// we need to add locks on child table restricting users to see only their records 
				$this->set('allowAccess',array(
					'custom_table_id'=>$this->request->params['named']['custom_table_id'],
					'allow_access_user'=>$this->Session->read('User.id')
				)); 
			}else{
				$skip = array('approval_comments','approvals','standards','processes'); 
				if(!in_array($this->request->controller,$skip))$this->_check_access();
			} 
			$skip_track_history = array('check_invoice_dateTue','dir_size','advance_search','assigned_tasks','index','jwtencode','field_fetch','check_document','code_input_main');

			if (
				!in_array($this->action, $skip_track_history) &&
				Inflector::Classify($this->name) != 'App' &&
				Inflector::Classify($this->name) != 'CakeError' &&
				$this->request->params['controller'] != 'user_sessions' &&
				$this->request->params['controller'] != 'email_triggers' &&
				$this->request->params['controller'] != 'billing' && 
				$this->request->params['controller'] != 'graph_panels'
			)
			{
				if($this->Session->read('User') && isset($this->request->params) && ($this->request['controller'] != 'file_uploads' && $this->action != 'get_department_employee') && $this->action !='mlfuserlist'){
					$this->_track_history();
				}
			}
		}
		
	} 

	public function back() {
		$this->redirect($this->referer());
	}
	public function _access_redirect($n = null){ 
		$ignore = array('install_updates', 'register','activate','send_otp', 'check_invoice_date','login', 'logout', 'forgot_password', 'reset_password', 'save_doc','access_denied','dashboard','dir_size','get_password_change_remind','last_updated_record','assigned_tasks','get_signatures','download_file','get_signature','save_signature','profile','upload','onlyofficechk','change_password','check_password_validation','save_template','clean_table_names','jwtencode','save_doc','save_rec_doc','save_custom_docs','save_file');
		if(!in_array($this->action,$ignore) 
			&& $this->request->controller != 'qc_documents' 
			// && $this->request->controller != 'standards' 
			&& $this->request->controller != 'custom_tables'
		){
			if($this->request->is('ajax') == false){
				if($this->request->controller == 'employees' && $this->action == 'view'){

				}else{
					$this->Session->setFlash(__('You are not authorized to view this section'), 'default', array('class' => 'alert alert-danger'));
					$this->redirect(array('controller' => 'users', 'action' => 'access_denied',$this->action));
				} 
			} else{
				exit;
			} 
		}
	}
	public function _check_access() {
		// if user is not admin
		if($this->Session->read('User.is_mr') == false){
			//check if its add/edit/view/ & custom table
			if(strpos($this->request->controller,"child") === false){ 
				if(isset($this->request->params['named']['qc_document_id'])){
					$this->loadModel('QcDocument');
					$sharing = $this->QcDocument->find('count',array(
					// 'recursive'=>-1,
					// 'fields'=>array('QcDocument.branches','QcDocument.departments','QcDocument.designations','QcDocument.user_id','QcDocument.id'),
						'conditions'=>array(
							'QcDocument.id'=>$this->request->params['named']['qc_document_id'],
							'OR'=>array(
								'QcDocument.prepared_by'=> $this->Session->read('User.employee_id'),
								'QcDocument.approved_by'=> $this->Session->read('User.employee_id'),
								'QcDocument.issued_by'=> $this->Session->read('User.employee_id'),
								'QcDocument.branches LIKE' => '%'.$this->Session->read('User.branch_id').'%',
								'QcDocument.departments LIKE' => '%'.$this->Session->read('User.department_id').'%',
							// 'QcDocument.designations LIKE' => '%'.$this->Session->read('User.designation_id').'%',
								'QcDocument.user_id LIKE' => '%'.$this->Session->read('User.id').'%',
							)
						)
					)); 
					// if($sharing == 0)$this->_access_redirect(1);
				}else{ 
					$this->loadModel('User');
					// $access = $this->User->find('first',array('fields'=>array('User.id','User.user_access'), 'conditions'=>array('User.id'=>$this->Session->read('User.id'))));
					$access = json_decode($access['User']['user_access'],true);
					$access = $access['user_access'];
					
					if(isset($access) && in_array($this->request->controller,array_keys($access))){
						if($access[$this->request->controller][$this->action] == 1){
							
						}else{
							$this->_access_redirect(2);
						}
					}else{

						// if delete
						// check if the user is creator // admin // hod 
						// if not access denied error
						$model = $this->modelClass;						
						if($this->request->data[$model]['prepared_by'] && $this->action == 'delete'){
							if($this->request->data[$model]['prepared_by'] == $this->Session->read('User.employee_id') || $this->Session->read('User.is_mr') == 1){
								
							}else{
								$this->_access_redirect(3);
							}
						}else{
							$this->_access_redirect(3); 
						}

					}
				} 
			}else{
				
			} 
		}

	}
	public function _check_request() {
		$onlyBranch = null;
		$onlyOwn = null;
		$con1 = null;
		$con2 = null;
		$modelName = $this->modelClass;
		$deptCon = array();
		
		// check if user/employee is involved departmentwise
		// and if the user is HoD
		
		// ***************** NOTE STARTS **************//
		// There could be multiple department fields //
		// ***************** NOTE ENDS **************//
		

		if($_SESSION['User']['hod'] == 1 && $this->Session->read('User.is_mr' == 0)){
			foreach($this->$modelName->belongsTo as $key => $belongs){
				if($belongs['className'] == 'Department'){
					$department_field = $belongs['foreignKey'];
					$department_field_class = $key;
				}
			} 			
			if($department_field){
				$schema = $this->$modelName->schema(); 
				// for json run condition 1
				if($schema[$department_field]['type'] == 'text'){
					$deptCon = array('or'=> array($modelName .'.'.$department_field.' LIKE ' => '%'. $this->Session->read('User.department_id').'%'));
				}else{
					$deptCon = array('or'=> array($modelName .'.'.$department_field => $this->Session->read('User.department_id')));
				} 
			}else{
				$deptCon = false;
			}
			
		}

		if($this->Session->read('User.is_mr') == 0 && $this->request->controller != 'qc_documents' && $this->request->controller != 'custom_tables' && $this->request->controller != 'standards'){ $onlyBranch = array('or'=>array($deptCon,$modelName.'.branchid'=>json_decode($this->Session->read('User.assigned_branches'),false)));
	}else{
		if($deptCon)$onlyBranch = $deptCon;
	}
		// if($this->Session->read('User.is_view_all') == 0 && $this->Session->read('User.assigned_branches' == null)){
	if($this->Session->read('User.is_view_all') == 0){
		$onlyOwn = array(
			'OR'=>array(
				$modelName.'.prepared_by'=>$this->Session->read('User.employee_id'),
				$modelName.'.approved_by'=>$this->Session->read('User.employee_id'),
				$modelName.'.created_by'=>$this->Session->read('User.id'),
				$modelName.'.modified_by'=>$this->Session->read('User.id'),
				$deptCon,
			)
		);
	}

	if($this->request->params['named'])
	{

		if(isset($this->request->params['named']['published']) && $this->request->params['named']['published'] == 0){
			$pubCon = array(
				'or'=>array(
					$modelName.'.publish' => 0,
					$modelName.'.publish is NULL'
				)
				
			);
		}else{
			$pubCon = array($modelName.'.publish'=>1);
		}

		if(isset($this->request->params['named']['published']) && $this->request->params['named']['published']==null)$con1 = null ; else $con1 = $pubCon;

		if(isset($this->request->params['named']['published']))$conditions=array($onlyBranch,$onlyOwn,$con1);
		else $conditions=array($onlyBranch,$onlyOwn,$con1);

	}
	else
	{
		$conditions=array($onlyBranch,$onlyOwn,null,$modelName.'.soft_delete'=>0); 
	}

	return array_filter($conditions);	

}
public function _get_count() {
}

public function _track_history($track = null) {

	$this->loadModel('History');
	$this->History->recursive = - 1;

	$this->History->create();
	$history = array();
	$model_name = Inflector::Classify($this->name);
	$history['History']['model_name'] = $model_name;
	$history['History']['controller_name'] = $this->request->params['controller'];
	$history['History']['action'] = $this->request->action;
	$history['History']['record_id'] = isset($this->request->params['pass'][0])? $this->request->params['pass'][0]: NULL;
	$history['History']['get_values'] = json_encode($this->request->params);
	if(isset($this->request->data['History'])){

		$history['History']['pre_post_values'] = $this->request->data['History']['pre_post_values'];
		
	}
	$history['History']['post_values'] = json_encode(array(
		$this->request->data
	));
	$history['History']['branch_id'] = $this->Session->read('User.branch_id');
	$history['History']['department_id'] = $this->Session->read('User.department_id');
	$history['History']['branchid'] = $this->Session->read('User.branch_id');
	$history['History']['departmentid'] = $this->Session->read('User.department_id');
	$history['History']['publish'] = 1;
	$history['History']['soft_delete'] = 0;
	$history['History']['created_by'] = $this->Session->read('User.id');
	$history['History']['user_session_id'] = $this->Session->read('User.user_session_id');

	try{
		$this->History->save($history); 
	}catch(Exception $e){

	}

	try{
		$this->History->save($history); 
	}catch(Exception $e){
			 //update usersession .. end time
		$this->History->UserSession->read(null,$this->Session->read('User.user_session_id'));
		$data['UserSession']['end_time'] = date('Y-m-d H:i:s');
		if($this->Session->read('User.id'))$this->History->UserSession->save($data['UserSession'], false);

	} 
}

public function _show_approvals() {
	$approvar['show_panel'] = true;
	return $approvar;
}
public function get_approvals() {
	if ($this->action == 'view' || $this->action == 'edit' || $this->action == 'recreate') {
			// $this->autoRender = false;
		$model = $this->modelClass;
		$record = $this->request->params['pass'][0];
		$this->loadModel('Approval');
		$approvals = $this->Approval->find('all', array('order'=>array('Approval.modified'=>'DESC', 'Approval.status'=>'DESC','Approval.approval_step'=>'ASC'), 'conditions' => array('Approval.model_name' => $model, 'Approval.record' => $record)));		
		return $approvals;
	}
}
public function get_approval($id = null, $creator = null) {
	$this->loadModel('Approval');
	$approval = $this->Approval->find('first', array('conditions' => array('Approval.id' => $id)));
	$this->set('approval', $approval);
}
	// public function _get_approver_list($creator = null) {
	// $this->loadModel('User');
	// $approversList = $this->User->find('list', array(
	// 'conditions' => array(
	// 'User.is_approver' => 1 
	// ))
	// );
	// $this->set('approversList', $approversList);
	// }

public function _get_approver_list($creator = null) {

	if(!$this->viewVars['approversList']){
		$this->loadModel('Employee');
		$this->Employee->virtualFields = array(
			'is_approver'=>'select `users`.`is_approver` from `users` where `users`.`employee_id` LIKE Employee.id'
		);
		$approversList = $this->Employee->find('list', array(
			'conditions' => array(
				'Employee.is_approver' => 1 
			))
	);
		$this->set('approversList', $approversList);		
	}	
}

public function _save_approvals($record_id = null) {
	
	$this->loadModel('Approval');
		// get approval cycle
	$model = $this->modelClass;
	if(!$record_id)$record_id = $this->request->data['Approval'][$model]['id'];
	$this->request->data['Approval'][$model]['record'] = $record_id;
		// generate some record details as well to add to approval for reference
	$model = $this->modelClass;
	$rec = $this->$model->find('first', array('recursive' => - 1, 'conditions' => array($model . '.id' => $this->request->data['Approval'][$model]['id']), 'fields' => array($model . '.id', $model . '.' . $this->$model->displayField)));
	$title = $rec[$model][$this->$model->displayField] . ' from ' . Inflector::humanize($this->request->controller) . ' section.';
	$cycle = $this->Approval->find('first', array('fields' => array('Approval.id', 'Approval.approval_cycle'), 'group' => array('Approval.approval_cycle'), 'order' => array('Approval.approval_cycle' => 'DESC'), 'conditions' => array('Approval.model_name' => $this->request->data[$this->modelClass]['model_name'], 'Approval.record' => $this->request->data[$this->modelClass]['id'])));

	if ($cycle) $cycle_count = count($cycle) + 1;
	else $cycle_count = 1;
	if ($this->request->data['Approval'][$model]['user_id']) {
		$approvalusers = $this->request->data['Approval'][$model]['user_id'];
		foreach ($approvalusers as $approvaluser) {
			if ($approvaluser != - 1) {
				$approvaldata['Approval']['title'] = $title;
				$approvaldata['Approval']['controller_name'] = $this->request->controller;
				$approvaldata['Approval']['model_name'] = $this->modelClass;
				$approvaldata['Approval']['record'] = $record_id;
				$approvaldata['Approval']['from'] = $this->request->data['Approval'][$model]['from'];
				$approvaldata['Approval']['user_id'] = $approvaluser;
				$approvaldata['Approval']['approval_status'] = 0;
				$approvaldata['Approval']['approval_mode'] = $this->request->data['Approval'][$model]['approval_mode'];
				$approvaldata['Approval']['approval_type'] = $this->request->data['Approval'][$model]['approval_type'];
				$approvaldata['Approval']['comments'] = $this->request->data['Approval'][$model]['comments'];
				$approvaldata['Approval']['approval_mode'] = $this->request->data['Approval'][$model]['approval_mode'];
				$approvaldata['Approval']['approval_cycle'] = $cycle_count;
				$this->Approval->create();
				$this->Approval->save($approvaldata,false);
				$this->_sent_approval_email(
					$approvaldata['Approval']['user_id'],
					0,
					$approvaldata['Approval']['comments'], 
					$approvaldata['Approval']['model_name']
				);
					// lock record
				$model = $approvaldata['Approval']['model_name'];
				$this->loadModel($model);
				$rec = $this->$model->find('first', array('conditions' => array($model . '.id' => $this->request->data['Approval'][$model]['record']), 'recursive' => - 1));
				if ($rec) {
					$rec[$model]['record_status'] = 1;
						// $rec[$this->modelClass]['record_status'] = 1;
					$this->$model->create();
					$this->$model->save($rec);
				}
			}
		} 
	}
}

public function _sent_approval_email($to = null,$message = null,$response = null,$model = null){

	$this->loadModel('User');
	$user = $this->User->find('first',array('conditions'=>array('OR'=>array('User.id'=>$to,'User.employee_id'=>$to))));
	if($user){
		if ($user['Employee']['office_email'] != '') {
			$email = $user['Employee']['office_email'];
		} else if ($user['Employee']['personal_email'] != '') {
			$email = $user['Employee']['personal_email'];
		} 
	}
	if($message == 1)$subject = 'FlinkISO: Record Approved';
	else $subject = 'FlinkISO: Approval';

	if ($email) {
		try {
			App::uses('CakeEmail', 'Network/Email');
			$email = $email;
			$EmailConfig = new CakeEmail("fast");
			$EmailConfig->to($email);
			$EmailConfig->subject($subject);
			$EmailConfig->template('approvalRequest');
			$EmailConfig->viewVars(array(
				'message' => $message,
				'url' => $login_url,
				'response' => $response,
				'by' => $this->Session->read('User.username'),
				'mode' => Inflector::humanize($model),
			));
			$EmailConfig->emailFormat('html');
			$EmailConfig->send();
		}
		catch (Exception $e) {
			$this->Session->setFlash(__('The user has been saved but fail to send email. Please check smtp details.', true), 'smtp'); 
		}

	}
}

	public function _get_approval_comnments($approval_id = null) { //
		// shift this
		$this->loadModel('ApprovalComment');
		$approvalComments = $this->ApprovalComment->find('all', array('order' => array('ApprovalComment.sr_no' => 'DESC'), 'conditions' => array('ApprovalComment.approval_id' => $approval_id)));
		$this->set('approvalComments', $approvalComments);
	}
	public function _save_approval_comments() {
		
		if ($this->request->data['ApprovalComment']['user_id'] != - 1 && $this->request->data['ApprovalComment']['status'] != 1) {
			$approvaldata['ApprovalComment']['approval_id'] = $this->request->data['ApprovalComment']['approval_id'];
			$approvaldata['ApprovalComment']['controller_name'] = $this->request->data['ApprovalComment']['controller_name'];
			$approvaldata['ApprovalComment']['model_name'] = $this->request->data['ApprovalComment']['model_name'];
			$approvaldata['ApprovalComment']['record'] = $this->request->data['ApprovalComment']['record'];
			$approvaldata['ApprovalComment']['from'] = $this->request->data['ApprovalComment']['from'];
			$approvaldata['ApprovalComment']['user_id'] = $this->request->data['ApprovalComment']['user_id'];
			$approvaldata['ApprovalComment']['approval_status'] = 0;
			$approvaldata['ApprovalComment']['approval_mode'] = $this->request->data['ApprovalComment']['approval_mode'];
			$approvaldata['ApprovalComment']['approval_type'] = $this->request->data['ApprovalComment']['approval_type'];
			$approvaldata['ApprovalComment']['comments'] = $this->request->data['ApprovalComment']['comments'];
			$approvaldata['ApprovalComment']['approval_mode'] = $this->request->data['ApprovalComment']['approval_mode'];
			$approvaldata['ApprovalComment']['approval_cycle'] = $cycle_count;
			$approvaldata['ApprovalComment']['response_status'] = 0;
			$this->loadModel('ApprovalComment');
			$this->ApprovalComment->create();
			$this->ApprovalComment->save($approvaldata);
			
			if ($this->request->data['ApprovalComment']['status'] == 1) {
				// update approval record
				$app = $this->ApprovalComment->Approval->find('first', array('recursive' => - 1, 'conditions' => array('Approval.id' => $this->request->data['ApprovalComment']['approval_id'])));
				$app['Approval']['status'] = 1;
				$this->ApprovalComment->Approval->create();
				$this->ApprovalComment->Approval->save($app);
			}
			if ($this->request->data['ApprovalComment']['status'] == 2) {
				// update approval record
				$app = $this->ApprovalComment->Approval->find('first', array('recursive' => - 1, 'conditions' => array('Approval.id' => $this->request->data['ApprovalComment']['approval_id'])));
				$app['Approval']['status'] = 1;
				$this->ApprovalComment->Approval->create();
				$this->ApprovalComment->Approval->save($app);
			}
			if (1 < 0) {
				// lock record
				$model = $approvaldata['Approval']['model_name'];
				$this->loadModel($model);
				$rec = $this->$model->find('first', array('conditions' => array($model . '.id' => $this->request->data['ApprovalComment']['record']), 'recursive' => - 1));
				$rec[$model]['record_status'] = 1;
				// $rec[$this->modelClass]['record_status'] = 1;
				$this->$model->create();
				$this->$model->save($rec);
			}
			// }
			
		} else if ($this->request->data['ApprovalComment']['user_id'] == - 1 && $this->request->data['ApprovalComment']['stauts'] == 1) {
			// this means record is approved
			$approvaldata['ApprovalComment']['approval_id'] = $this->request->data['ApprovalComment']['approval_id'];
			$approvaldata['ApprovalComment']['controller_name'] = $this->request->data['ApprovalComment']['controller_name'];
			$approvaldata['ApprovalComment']['model_name'] = $this->request->data['ApprovalComment']['model_name'];
			$approvaldata['ApprovalComment']['record'] = $this->request->data['ApprovalComment']['record'];
			$approvaldata['ApprovalComment']['from'] = $this->request->data['ApprovalComment']['from'];
			if ($this->request->data['ApprovalComment']['stauts'] == 1) {
				$approvaldata['ApprovalComment']['user_id'] = 'Approved';
				$approvaldata['ApprovalComment']['approval_status'] = $this->request->data['ApprovalComment']['stauts'];
			} else if ($this->request->data['ApprovalComment']['stauts'] == 2) {
				$approvaldata['ApprovalComment']['user_id'] = 'Not Approved';
				$approvaldata['ApprovalComment']['approval_status'] = $this->request->data['ApprovalComment']['stauts'];
			}
			$approvaldata['ApprovalComment']['approval_mode'] = $this->request->data['ApprovalComment']['approval_mode'];
			$approvaldata['ApprovalComment']['approval_type'] = $this->request->data['ApprovalComment']['approval_type'];
			$approvaldata['ApprovalComment']['comments'] = $this->request->data['ApprovalComment']['comments'];
			$approvaldata['ApprovalComment']['approval_mode'] = $this->request->data['ApprovalComment']['approval_mode'];
			$approvaldata['ApprovalComment']['approval_cycle'] = $cycle_count;
			
			$this->loadModel('ApprovalComment');
			$this->ApprovalComment->create();
			if ($this->ApprovalComment->save($approvaldata, false)) {
				// first updated approval record
				$appRec = $this->ApprovalComment->Approval->find('first', array('recursive' => - 1, 'conditions' => array('Approval.id' => $this->request->data['ApprovalComment']['approval_id']),));
				$appRec['Approval']['status'] = 1;
				$appRec['Approval']['approved_date_time'] = $appRec['Approval']['modified'] = date('Y-m-d H:i:s');
				$this->ApprovalComment->Approval->create();
				// later add
				
				if ($this->ApprovalComment->Approval->save($appRec, false)) {
				} else {
					echo "failed";
					exit;
				}
				// if approved
				// check if all other approvals are approved or pending
				// if all other approvals are approved, mark record as PUBLISHED
				// else no nothing
				// check if all approved
				$this->ApprovalComment->Approval->virtualFields = array('all' => 'select count(*) from approvals where approvals.record LIKE "' . $this->request->data['ApprovalComment']['record'] . '" AND approvals.controller_name LIKE "' . $this->request->data['ApprovalComment']['controller_name'] . '"', 'approved' => 'select count(*) from approvals where approvals.record LIKE "' . $this->request->data['ApprovalComment']['record'] . '" AND approvals.controller_name LIKE "' . $this->request->data['ApprovalComment']['controller_name'] . '" AND approvals.status = 1',);
				$allApprovals = $this->ApprovalComment->Approval->find('first', array('recursive' => - 1, 'fields' => array('Approval.id', 'Approval.controller_name', 'Approval.record', 'Approval.status', 'Approval.all', 'Approval.approved',), 'conditions' => array('Approval.controller_name' => $this->request->data['ApprovalComment']['controller_name'], 'Approval.record' => $this->request->data['ApprovalComment']['record'],)));
				if ($allApprovals['Approval']['all'] == $allApprovals['Approval']['approved']) {
					// this means record is approved by all
					// publish the record
					$model = $approvaldata['ApprovalComment']['model_name'];
					$this->loadModel($model);
					$rec = $this->$model->find('first', array('conditions' => array($model . '.id' => $this->request->data['ApprovalComment']['record']), 'recursive' => - 1));
					$rec[$model]['publish'] = 1;
					// $rec[$this->modelClass]['record_status'] = 1;
					$this->$model->create();
					$this->$model->save($rec);
				} else {
				}
			}
		}
	}
	public function _show_evidence() {
	}
	public function _get_branch_list() {
		$this->loadModel('Branch');
		$PublishedBranchList = $this->Branch->find('list', array('conditions' => array('Branch.soft_delete' => 0, 'Branch.publish' => 1), 'recursive' => - 1));
		$this->set(compact('PublishedBranchList'));
		return ($PublishedBranchList);
	}
	public function _get_department_list() {
		$this->loadModel('Department');
		$PublishedDepartmentList = $this->Department->find('list', array('conditions' => array('Department.soft_delete' => 0, 'Department.publish' => 1), 'recursive' => - 1));
		$this->set(compact('PublishedDepartmentList'));
		return ($PublishedDepartmentList);
	}
	public function _get_employee_list() {
		$this->loadModel('Employee');
		$PublishedEmployeeList = $this->Employee->find('list', array(
		// 'limit'=>10,
			'conditions' => array('Employee.soft_delete' => 0, 'Employee.publish' => 1), 'recursive' => - 1));
		$this->set(compact('PublishedEmployeeList'));
		return ($PublishedEmployeeList);
	}
	public function _get_designation_list() {
		$this->loadModel('Designation');
		$PublishedDesignationList = $this->Designation->find('list', array('conditions' => array('Designation.publish' => 1, 'Designation.soft_delete' => 0), 'recursive' => - 1));
		$this->set(compact('PublishedDesignationList'));
		return ($PublishedDesignationList);
	}
	public function _get_usernames() {
		$this->loadModel('User');
		$users = $this->User->find('all', array('conditions' => array('User.soft_delete' => 0, 'User.publish' => 1), 'fields' => array('User.id', 'User.name', 'User.username')));
		foreach ($users as $user) {
			$employeeUserNames[$user['User']['id']] = $user['User']['name'] . " (" . $user['User']['username'] . ")";
		}
		return ($employeeUserNames);
	}
	public function _get_user_list() {
		$this->loadModel('User');
		$users = $this->User->find('list', array('conditions' => array('User.soft_delete' => 0, 'User.publish' => 1)));
		$this->set('PublishedUserList', $users);
		return ($users);
	}
	public function _qc_document_header($id = null) {
		if(!$this->viewVars['document']){
			$this->loadModel('QcDocument');
			$document = $this->QcDocument->find('first', array(
				'recursive'=>0,
				'fields'=>array(
					'QcDocument.id',
					'QcDocument.name',
					'QcDocument.title',
					'QcDocument.file_key',
					'QcDocument.version',
					'QcDocument.file_status',
					'QcDocument.file_type',
					'QcDocument.schedule_id',
					'QcDocument.data_type',
					'QcDocument.add_records',
					'QcDocument.qc_document_category_id',
					'QcDocument.clause_id',
					'QcDocument.standard_id',
					'QcDocument.document_number',
					'QcDocument.issue_number',
					'QcDocument.date_of_issue',
					'QcDocument.date_of_next_issue',
					'QcDocument.effective_from_date',
					'QcDocument.revision_number',
					'QcDocument.update_version',
					'QcDocument.revision_date',
					'QcDocument.document_type',
					'QcDocument.document_status',
					'QcDocument.issued_by',
					'QcDocument.issuing_authority_id',
					'QcDocument.archived',
					'QcDocument.parent_document_id',
					'QcDocument.parent_id',
					'QcDocument.user_id',
					'QcDocument.editors',
					'QcDocument.publish',
					'QcDocument.prepared_by',
					'QcDocument.approved_by',
					'PreparedBy.id',
					'PreparedBy.name',
					'ApprovedBy.id',
					'ApprovedBy.name',
					'IssuedBy.id',
					'IssuedBy.name',

				),
				'conditions' => array('QcDocument.id' => $id)));				
			$this->set('document', $document);
		}else{			
			$document = $this->viewVars['document'];
		}
		return $document;
	}

	public function _process_header($id = null){
		$this->loadModel('Process');
		$process = $this->Process->find('first',array('conditions'=>array('Process.id'=>$id),'recursive'=>-1,'fields'=>array(
			'Process.id',
			'Process.name',
			'Process.file_name',
			'Process.file_type',
			'Process.file_key',
			'Process.version_keys',
			'Process.custom_table_id',
			'Process.standards',
			'Process.clauses',
		))); 

		$this->set('process', $process); 
		
		$standards = $this->Process->Standard->find('list',array('conditions'=>array('Standard.publish'=>1)));
		$this->set('standards', $standards);

		$clauses = $this->Process->Standard->Clause->find('list',array('conditions'=>array('Clause.publish'=>1)));
		$this->set('clauses', $clauses);

		$this->_get_department_list();
		$this->_get_branch_list();
		return $process;
	}

	public function _generate_onlyoffice_key($record_id = null) {
		$filekey = $record_id;
		$stat = date('ymdhis');
		$filekey = $filekey . $stat;		
		// return GenerateRevisionId($key);
		if (strlen($filekey) > 20) $filekey = crc32($filekey);
		$filekey = preg_replace("[^0-9-.a-zA-Z_=]", "_", $filekey);
		$filekey = substr($filekey, 0, min(array(strlen($filekey), 20)));
		return $filekey;
	}
	public function jwtencode($payload = null) {
		if(!$payload)$payload = $this->request->params['named']['payload'];		
		$header = ["alg" => "HS256", "typ" => "JWT"];
		$encHeader = $this->_base64UrlEncode(json_encode($header));
		$encPayload = $this->_base64UrlEncode(json_encode($payload));
		$hash = $this->_base64UrlEncode($this->_calculateHash($encHeader, $encPayload));
		$token = "$encHeader.$encPayload.$hash";
		return $token;
	}
	public function _calculateHash($encHeader, $encPayload) {
		return hash_hmac("sha256", ". $encHeader.$encPayload .", Configure::read('onlyofficesecret'), true);
	}
	public function _base64UrlEncode($str) {
		return str_replace("/", "_", str_replace("+", "-", trim(base64_encode($str), "=")));
	}
	public function _base64UrlDecode($payload) {
		$b64 = str_replace("_", "/", str_replace("-", "+", $payload));
		switch (strlen($b64) % 4) {
			case 2:
			$b64 = $b64 . "==";
			break;
			case 3:
			$b64 = $b64 . "=";
			break;
		}
		return base64_decode($b64);
	}
	public function _isValidUuid($uuid = null) {
		if (!is_string($uuid) || (preg_match('/^[a-f\d]{8}(-[a-f\d]{4}){4}[a-f\d]{8}$/i', $uuid) !== 1)) {
			return false;
		}
		return true;
	}
	public function listFolderFiles($dir = null, $re = null, $x = null) {
		$skiparray = array('.', '..', '.htaccess', '.DS_Store');
		if ($re == 0) $str = '<ul id="myUL">';
		else $str.= '<ul class="nested">';
		$x++;
		foreach (new DirectoryIterator($dir) as $fileInfo) {
			if (!$fileInfo->isDot()) {
				if (!in_array($fileInfo->getFilename(), $skiparray)) {
					if ($fileInfo->isDir()) {
						if ($this->_isValidUuid($fileInfo->getFilename()) == true) {
							$f = 'Folder';
						} else {
							$f = $fileInfo->getFilename();
						}
						$x++;
						$folderenc = base64_encode(Router::url('/', true) . 'files/' . $this->Session->read('User.company_id') . '/' . strstr($dir, $this->request->controller) . '/' . $fileInfo->getFilename());
						$str.= '<li id="fid_' . $x . '"><div class="caret"><i class="fa fa-folder-o"></i> <strong>' . $f.'</strong>';
						$str.= '<a href="javascript:void(0)" onClick=selectfolder("' . $folderenc . '",' . $x . ');> <i class="fa fa-arrow-circle-up pull-right"></i></a>';
						$str.= '<a href="javascript:void(0)" onClick=deletefolder("' . $folderenc . '",' . $x . ');> <i class="fa fa-remove text-danger pull-right"></i></a></div>';
						// $str.= '<a href="javascript:void(0)" onClick=editfolder("' . $folderenc . '",' . $x . ');> <i class="fa fa-edit text-warning pull-right"></i></a>';
					} else {
						$x++;
						// $class = '';
						$file = Router::url('/', true) . 'files/' . $this->Session->read('User.company_id') . '/' . strstr($dir, $this->request->controller) . '/' . $fileInfo->getFilename();
						$str.= '<li id="id_' . $x . '" class="nli"><div class="file"><a href="' . $file . '" target="_blank"><i class="fa fa-file-o"></i> ' . $fileInfo->getFilename() . '</a>';
						$str.= '<a href="javascript:void(0)" onClick=deletefile("' . base64_encode($file) . '",' . $x . ');> <i class="fa fa-remove text-warning pull-right"></i></a></div>';
					}
					if ($fileInfo->isDir()) {
						$str.= $this->listFolderFiles($fileInfo->getPathname(), 1, $x);
						$x++;
					}
					$str.= '</li>';
					$x++;
				}
			}
		}
		$str.= '</ul>';
		return $str;
	}
	public function get_directory_tree($id = null) {
		$x = 0;
		$path = Configure::read('path') . DS . $id . DS . 'addtional_documents';
		if (file_exists($path)) {
			$x++;
			$r = $this->listFolderFiles($path, 0, $x);
			$x++;
			return $r;
		}
	}
	public function upload_document($id = null) {
		$this->layout = 'ajax'; 
		if ($this->request->data['id'] && $this->request->data['file'] && $this->request->data['path']) {
			$path = ltrim(rtrim(str_replace(' ', '_', $this->request->data['path'])));
			$path = Configure::read('path') . DS . $this->request->data['id'] . DS . 'addtional_documents' . DS . $path;
			if (file_exists($path)) {
				echo "path exits";
			} else {
				$folder = new Folder($path);
				if ($folder->create($path)) {
					echo "new Folder Created";
				} else {
					echo "folder creation failed";	
				}				
			}

			move_uploaded_file($this->request['data']['file']['tmp_name'], $path . DS . $this->request['data']['file']['name']);
		}
		$this->set('data', array('id' => $this->request->data['id']));
		$this->render('/Elements/fileuploads');
	}
	public function delete_uploaded_file($file = null) {
		$this->autoRender = false;
		$file = base64_decode($file);
		$file = str_replace(Configure::read('url'), Configure::read('path'), $file);
		unlink($file);
		return true;
	}
	public function delete_uploaded_folder($folder = null) {
		$this->autoRender = false;
		$folder = base64_decode($folder);
		$folder = str_replace(Configure::read('url'), Configure::read('path'), $folder);
		if (file_exists($folder)) {
			$path = new Folder($folder);
			$path->delete($folder);
		} else {
			echo "Folder not found";
		}
	}
	
	public function delete() {

		if($this->request->controller == 'processes'){
			$options = array('conditions' => array('Process.' . $this->Process->primaryKey => $id));
			$process = $this->Process->find('first', $options);

			if($this->Session->read('User.is_mr') == false && $process['Process']['created_by'] != $this->Session->read('User.id')){
				$this->Session->setFlash(__('You can not delete this process.'));
				$this->redirect(array('action' => 'index', 'process_id' => $this->request->params['named']['process_id'], 'custom_table_id' => $this->request->params['named']['custom_table_id']));
			}
		}		

		if ($this->request->controller != 'custom_tables') {
			if ($this->request->is('post') || $this->request->is('put')) {
				$model = $this->modelClass;
				$record = $this->$model->find('first',array('conditions'=>array($model.'.id'=>$id),'recursive'=>-1));				
				$this->_recursive_delete($this->request->data[$model]['id'],$model);				
				$this->redirect(array('action' => 'index','custom_table_id'=>$record[$model]['custom_table_id'],'qc_document_id'=>$record[$model]['qc_document_id']));
			} else {
				$model = $this->modelClass;
				$this->loadModel($model);
				$rec = $this->$model->find('first',array('conditions'=>array($model.'.id'=>$this->request->params['pass'][0]),'fields'=>array($model.'.id',$model.'.prepared_by')));
				$rec = $rec[$model]['prepared_by'];
				$this->set('rec', $rec);
				$this->set('model', $model);
				$this->render('/Elements/delete');

			}
		}
	}


	public function _recursive_delete($id = null, $model = null){
		$this->loadModel('File');
		// get additional files$files
		$files = array();
		$record = $this->$model->find('first',array('conditions'=>array($model.'.id'=>$id),'recursive'=>-1));

		// find other tables
		$this->loadModel('QcDocument');
		$docs = $this->QcDocument->find('all',array('conditions'=>array(
			'QcDocument.parent_document_id'=>$record[$model]['qc_document_id'],
		),					
		'fields'=>array(
			'QcDocument.id',
			'QcDocument.title',
		)
	));

		if($docs){
			foreach($docs as $doc){
				if($doc['CustomTable']){
					foreach($doc['CustomTable'] as $cTable){
						$childModel = Inflector::classify($cTable['table_name']);	
						$this->loadModel($childModel);
						$childRecords = $this->$childModel->find('all',array(
							'recursive'=>-1,
							'conditions'=>array($childModel.'.parent_id'=>$id)));
						foreach($childRecords as $childRecord){
							$this->_recursive_delete($childRecord[$childModel]['id'],$childModel);
						}
					}
					
				}
				
			}
		}

		if($record[$model]['additional_files']){
			$files[] = json_decode($record[$model]['additional_files'],true);
			$files[] = $record[$model]['file_id'];
		}
		else $files[] = $record[$model]['file_id'];
		foreach($files as $file){
			if($file){
				$this->set('file',$file);
				$this->File->delete($file);						
				$path = Configure::read('files') . DS . 'files' . DS . $file;
				$folderToDelete = new Folder($path);
				$folderToDelete->delete();						
			}
		}

		$this->$model->delete($id);			
	}

	public function bulk_delete() {
		$model = $this->modelClass;

		if ($this->request->controller != 'custom_tables') {
			if ($this->request->is('post') || $this->request->is('put')) {
				if(!$this->request->data[$model]['password']){ 
					$this->Session->setFlash(__('Enter Password To Continue'));
					$this->loadModel($model);
					$hasManies = $this->$model->hasMany;
					$this->set('hasManies',$hasManies);
					$this->render('/Elements/bulk_delete');
				}else{
					
					// delete records after checking passwords 
					$this->loadModel('User');
					$user = $this->User->find('first', array('conditions' => array('User.status' => 1, 'User.soft_delete' => 0, 'User.publish' => 1, 'User.username' => $this->Session->read('User.username'))));
					if($user){
						if (trim($user['User']['password']) != trim(Security::hash($this->data[$model]['password'], 'md5', true))) {
							// incorrect password
							$this->Session->setFlash(__('Incorrect Password'));
							$this->render('/Elements/bulk_delete');
						}else{
							$this->loadModel($model);
							$records = json_decode($this->data[$model]['ids']);
							foreach($records as $id){
								if($id != '' && strlen($id) == 36){
									
									foreach($this->data[$model]['has_many'] as $hasMany){
										$foreignKey = $this->$model->hasMany[$hasMany]['foreignKey'];
										
										$this->loadModel($hasMany);
										// $this->$hasMany->deleteAll(array($masMany.'.'.$foreignKey => $id),false);
										$hasManyRecords = $this->$hasMany->find('list',array('recursive'=>-1,'conditions'=>array($hasMany.'.'.$foreignKey => $id)));
										foreach($hasManyRecords as $hasManyRecordid => $hasManyRecord){
											$this->$hasMany->delete($hasManyRecordid);
											// also need to delete files etc
										}
									}
									// after deleteting everything, delete main record
									$this->$model->delete($id);
								}
								
							} 
							$this->Session->setFlash(__('Records Deleted'));
							$this->redirect(array('controller'=>$this->request->controller, 'action' => 'index'));
						}
					}else{
						$this->Session->setFlash(__('Unknown user'));
						$this->render('/Elements/bulk_delete');
					}
				} 
			} else {
				$this->render('/Elements/bulk_delete'); 
			}
		}
	}

	public function download_pdf($id = null) {
		
		$path = WWW_ROOT .'files'. DS . 'pdf'. DS . $this->Session->read('User.id');

		$this->loadModel('QcDocument');
		$doc = $this->QcDocument->find('first', array('conditions' => array('QcDocument.id' => $id), 'recursive' => - 1));
		$file_type = $doc['QcDocument']['file_type'];
		$file_name = $doc['QcDocument']['title'];
		$document_number = $doc['QcDocument']['document_number'];
		$document_version = $doc['QcDocument']['revision_number'];
		$file = $document_number . '-' . $file_name . '-' . $document_version;
		$file = $this->_clean_table_names($file);
		$file = $file . '.' . $file_type;
		$pdffilename = 'returndoc.pdf';
		
		$url = Configure::read('url') . DS . $id . DS . $file;
		
		$url = str_replace('document_downloads','qc_documents',$url);
		$key = $this->_generate_onlyoffice_key($record_id . date('Ymdhis'));
		

		if($file_type == 'xls' || $file_type == 'xlsx'){
			$arraytopost = array(
				'fileType' => $file_type,
				'key' => $key,
				'outputtype' => 'pdf',
				'region' => 'en-US',
				'url' => $url,
				'async' => false,
				'spreadsheetLayout'=>array(
					'ignorePrintArea'=>true,
					'orientation'=>'portrait',
					'fitToWidth'=> 0,
					'fitToHeight'=> 0,
					'scale'=> 100,
					'headings'=> false,
					'gridLines'=> false,
					'pageSize'=> array(
						'width'=>'210mm',
						'height'=> '297mm'
					),
					'margins'=>array(
						'left'=> '10mm',
						'right'=> '10mm',
						'top'=> '10mm',
						'bottom'=> '5mm'
					)
				)
			);
		}else{
			$arraytopost = array('fileType' => $file_type, 'key' => $key, 'outputtype' => 'pdf', 'region' => 'en-US', 'url' => $url, 'async' => false);
		}
		
		$curl = curl_init();
		curl_setopt_array($curl, array(CURLOPT_URL => Configure::read('OnlyofficeConversionApi')."/ConvertService.ashx", CURLOPT_RETURNTRANSFER => true, CURLOPT_ENCODING => "", CURLOPT_MAXREDIRS => 10, CURLOPT_TIMEOUT => 30, CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_CUSTOMREQUEST => "POST", CURLOPT_POSTFIELDS => json_encode($arraytopost), CURLOPT_HTTPHEADER => array("cache-control: no-cache", "content-type: application/json", "postman-token: 0ce594dd-2a09-651f-b56f-0d77b6bf222e"),));
		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);
		if ($err) {
			echo "cURL Error #:" . $err;
		} else {
			$xmlString = $response;
			$xmlArray = Xml::toArray(Xml::build($xmlString));
			$file = $xmlArray['FileResult']['FileUrl'];
			
			$folder = new Folder();
			if ($folder->create($path)) {
			} else {
				echo "Folder creation failed";
				exit;
			}
			$pdffile = $path . DS . $pdffilename;
			if (file_exists($pdffile)) {
				unlink($pdffile);
			}
			if (copy($file, $pdffile)) {
				return $pdffile; 
			} else {
				return false; 
			} 
		}
	}
	public function save_custom_docs() {
		$this->autoRender = false;
		$file_id = $this->request->params['named']['file_id'];
		
		if (($body_stream = file_get_contents("php://input")) === FALSE) {
			echo "Bad Request";
		}
		$data = json_decode($body_stream, TRUE);
		
		if ($data["status"] == 2) {
			$data = json_decode($body_stream, TRUE);
			$this->loadModel('File');
			$local = $this->File->find('first', array('recursive' => - 1, 'conditions' => array('File.id' => $file_id))); 
			$path_for_save = WWW_ROOT . 'files' . DS . $local['File']['company_id'] . DS . $local['File']['controller'] . DS . $local['File']['user_id'] . DS . $local['File']['record_id'] . DS . $local['File']['qc_document_id']; 
			$this->loadModel('QcDocument');
			$qcdoc = $this->QcDocument->find('first', array('recursive' => - 1, 'conditions' => array('QcDocument.id' => $local['qc_document_id'])));
			$file_for_save = $path_for_save . DS . $local['File']['name'] . '.' . $local['File']['file_type'];
			
			$testfolder = new Folder($path_for_save);
			$testfolder->create($path_for_save);
			chmod($path_for_save, 0777);
			chmod($file_for_save, 0777);
			$downloadUri = $data["url"];

			if (file_get_contents($downloadUri) === FALSE) {
				
			} else {
				$new_data = file_get_contents($downloadUri);
				if (file_put_contents($file_for_save, $new_data)) {
					
				} else {
					
				} 
			}
			
			$key = $this->_generate_onlyoffice_key($record_id . date('Ymdhis'));
			
			$file = $local;
			$file['File']['pre_file_id'] = $file['File']['id'];
			unset($file['File']['id']);
			unset($file['File']['sr_no']);
			unset($file['File']['created']);
			unset($file['File']['modified']);
			$file['File']['data_received'] = json_encode($data);
			$file['File']['pre_file_key'] = $file['File']['file_key'];
			$file['File']['file_key'] = $data['key'];
			$file['File']['new_file_key'] = $key;
			$file['File']['file_status'] = 1;
			$file['File']['last_saved'] = date('Y-m-d H:i:s');
			$this->File->create();
			$this->File->save($file);
			// update record key
			if ($local['tmp'] != 'tmp') {
				$model = $local['File']['model'];
				$this->loadModel($model);
				$rec = $this->$model->find('first', array('conditions' => array($model . '.id' => $local['tmp']), 'recursive' => - 1));
				if ($rec) {
					$rec[$model]['file_key'] = $key;
					$this->$model->create();
					$this->$model->save($rec[$model]);
				}
			}
		}
		echo "{\"error\":0}";
	}
	public function get_child_select($parent_id = null, $model = null, $field = null) {
		$this->autoRender = false;
		$parent_id = $this->request->params['named']['parent_id'];
		$model = $this->request->params['named']['model'];
		$field = $this->request->params['named']['field'];
		if ($parent_id) {
			$this->loadModel($model);
			$results = $this->$model->find('list', array('conditions' => array($model . '.' . $field => $parent_id)));
			if ($results) {
				$con_str.= '<option value=-1>Select</option>';
				foreach ($results as $key => $value) {
					$con_str.= '<option value=' . $key . '>' . $value . '</option>';
				}
			}
		}
		return $con_str;
		exit;
	}
	public function get_field_list($table = null) {
		$this->autoRender = false;
		if ($this->request->params['named']['table'] != - 1) {
			$this->loadModel('CustomTable');
			$customTable = $this->CustomTable->find('first', array('conditions' => array('CustomTable.table_name' => $this->request->params['named']['table']), 'recursive' => - 1));
			$fields = json_decode($customTable['CustomTable']['fields'], true);
			if ($fields) {
				$con_str.= '<option value=-1>Select</option>';
				foreach ($fields as $field) {
					$con_str.= '<option value=' . $field['field_name'] . '>' . $field['field_name'] . '</option>';
				}
			}
		}
		return $con_str;
		exit;
	}


	public function _pre_search(){
		$modelName = $this->modelClass;
		
		if(!empty($customTable['CustomTable']['qc_document_id']) && !isset($this->request->params['named']['qc_document_id'])){
			
		}else{ 
			$document = $this->_qc_document_header($this->request->params['named']['qc_document_id']);
		}

		if(!empty($customTable['CustomTable']['process_id']) && !isset($this->request->params['named']['process_id'])){ 
			
		}else{ 
			$process = $this->_process_header($this->request->params['named']['process_id']);
		}
	}

	public function quick_search(){
		$model = $this->modelClass;
		$this->_pre_search();
		$fields = array_keys($this->$model->schema());
		
		$x = 0;

		$search_keys = array('id', 'name','title');

		$src = $this->$model->displayField;


		$srcs = explode(' ',$this->request->params['named']['search']);
		$conditions = array();
		
		foreach($srcs as $s){			
			$conditions[] = array('LOWER('.$model.'.'.$src .') LIKE ' => '%'.strtolower($s).'%');
			foreach ($search_keys as $keys) {
				if(in_array($keys, $fields)){
					$field_condition[] = array('LOWER('.$model.'.'.$keys.') LIKE' => '%'.strtolower($s).'%');
				}
			} 
		}
		if($field_condition)$conditions = array('OR'=>array_merge($conditions,$field_condition));					
		$this->paginate = array('limit'=>25, 'order' => array($model.'.id' => 'DESC'), 'conditions' => array($conditions));
		$this->$model->recursive = 0;			
		$this->set(Inflector::variable(Inflector::tableize($model)), $this->paginate()); 

		$this->_commons(); 
		$this->render('index');
	}

	public function advance_search() {
		if ($this->request->is('post')) {
			$modal = $this->modelClass;
			$this->loadModel($modal);
			foreach ($this->request->data['order'][$modal] as $field_name => $value) {
				if ($value['value'] != - 1) {
					if ($value['value'] == 0) $ord = 'ASC';
					if ($value['value'] == 1) $ord = 'DESC';
					$oderarray[$modal . '.' . $field_name] = $ord;
				}
			}
			foreach ($this->request->data['basic'][$modal] as $field_name => $details) {
				if ($field_name == 'file_types') $field_name = 'file_type';
				if ($field_name == 'tableTypes') $field_name = 'table_type';
				if ($details['value'] != '') {
					switch ($details['oprator']) {
						case '==':
						$condition[] = array($modal . '.' . $field_name => $details['value']);
						break;
						case '!=':
						$condition[] = array($modal . '.' . $field_name . ' !=' => $details['value']);
						break;
						case '>':
						$condition[] = array($modal . '.' . $field_name . ' >' => $details['value']);
						break;
						case '<':
						$condition[] = array($modal . '.' . $field_name . ' <' => $details['value']);
						break;
						case '%*':
						$condition[] = array('LOWER('.$modal . '.' . $field_name . ') LIKE ' => '%' . $details['value']);
						break;
						case '*%':
						$condition[] = array('LOWER('.$modal . '.' . $field_name . ') LIKE ' => $details['value'] . '%');
						break;
						case 'between':
						$dates = split('-', $details['value']);
						$startdate = date('Y-m-d', strtotime($dates[0]));
						$enddate = date('Y-m-d', strtotime($dates[1]));
						$condition[] = array('DATE(' . $modal . '.' . $field_name . ') BETWEEN ? and ? ' => array($startdate, $enddate));
						break;
						case '%*%':
						$condition[] = array('LOWER('.$modal . '.' . $field_name . ') LIKE ' => '%' . $details['value'] . '%');
						break;
						default:
							# code...

						break;
					}
				}
			}
			foreach ($this->request->data['advance'][$modal] as $field_name => $details) {
				if ($field_name == 'file_types') $field_name = 'file_type';
				if ($field_name == 'tableTypes') $field_name = 'table_type';
				if ($details['value'] != '') {
					switch ($details['oprator']) {
						case '==':
						$condition[] = array($modal . '.' . $field_name => $details['value']);
						break;
						case '!=':
						$condition[] = array($modal . '.' . $field_name . ' !=' => $details['value']);
						break;
						case '>':
						$condition[] = array($modal . '.' . $field_name . ' >' => $details['value']);
						break;
						case '<':
						$condition[] = array($modal . '.' . $field_name . ' <' => $details['value']);
						break;
						case '%*':
						$condition[] = array('LOWER('.$modal . '.' . $field_name . ') LIKE ' => '%' . $details['value']);
						break;
						case '*%':
						$condition[] = array('LOWER('.$modal . '.' . $field_name . ') LIKE ' => $details['value'] . '%');
						break;
						case 'between':
						break;
						case '%*%':
						$condition[] = array('LOWER('.$modal . '.' . $field_name . ') LIKE ' => '%' . $details['value'] . '%');
						break;
						default:
							# code...

						break;
					}
				}
			}
			if ($condition == null) {
				$this->Session->setFlash(__('No records to display! Please select search criteria.'), 'default', array('class' => 'alert alert-danger'));
				$variable = Inflector::variable(Inflector::pluralize($this->modelClass));
				$this->set($variable, false);
			} else {
				$conditions = $this->_check_request();
				$this->paginate = array('order' => $oderarray, 'conditions' => array($condition, $conditions), 'maxLimit' => 500, 'limit' => 500);
				$variable = Inflector::variable(Inflector::pluralize($this->modelClass));
				$this->set($variable, $this->paginate());
			}
			$this->_commons($this->Session->read('User.id'));
			$this->request->data = $this->request->data;
			$this->_pre_search(); 
			$this->render('index');
			// $result = $this->$modal->find('all',array('conditions'=>$condition));
			
		} else {
			$modal = $this->modelClass;
			$this->loadModel($modal);
			$fields = $this->$modal->schema();
			$belongs = $this->$modal->belongsTo;
			$fields_to_unset = array('id', 'sr_no', 'system_table_id', 'company_id', 'modified', 'modified_by', 'branchid', 'departmentid', 'soft_delete', 'record_status', 'status_user_id', 'division_id','master_list_of_format_id', 'login_status', 'password', 'user_access', 'copy_acl_from', 'password_token', 'user_session_id', 'divisionid', 'document_status', 'parent_id', 'work_instructions', 'record', 'file_key', 'version_key', 'file_dir', 'result', 'file_status', 'file_content','version','version_keys','versions','update_custom_table_document','file_type','data_type','add_records','update_version','cr_status','mark_for_cr_update','pdf_footer_id','signature','temp_date_of_issue','temp_effective_from_date');

			foreach($fields_to_unset as $f){
				unset($fields[$f]);
			}

			
			foreach ($belongs as $key => $value) {
				if (in_array($value['foreignKey'], array_keys($fields))) {
					unset($fields[$value['foreignKey']]);
				}
			}
			foreach ($fields as $field_name => $field_type) {
				switch ($field_type['type']) {
					case 'string':
					$src[$field_name] = array('==' => 'Equal To', '!=' => 'Not Equal To', '%*' => 'Starts With', '*%' => 'Ends With', '%*%' => 'Contains Word');
					break;
					case 'date':
					$src[$field_name] = array('==' => 'Equal To', '>' => 'Greater Than', '<' => 'Less Than', 'between' => 'Between');
					break;
					case 'integer':
					if ($field_type['length'] != 1) {
						$src[$field_name] = array('==' => 'Equal To', '!=' => 'Not Equal To', '>' => 'Greater Than', '<' => 'Less Than');
							// $src[$field_name] = array('==','!=','>','<');

					} else {
						$src[$field_name] = array('==' => 'Equal To', '!=' => 'Not Equal To', '>' => 'Greater Than', '<' => 'Less Than');
							// $src[$field_name] = array('==','!=','>','<');

					}
					break;
					case 'text':
					$src[$field_name] = array('%*%' => 'Contains Word');
					break;
					default:
						# code...

					break;
				}
			}
			foreach ($belongs as $bkey => $bvalue) {
				if (in_array($bvalue['foreignKey'], array_values($fields_to_unset))) {
					unset($belongs[$bkey]);
				}
			}
			foreach ($belongs as $key => $value) {
				// check belongs model
				// load model and fect list
				// add to result
				$m = $value['className'];
				$this->loadModel($m);				
				try{
					$getrecs = $this->$m->find('list', array('conditions' => array($m . '.publish' => 1, $m . '.soft_delete' => 0)));
					$belongsToModels[$key] = array('field_name' => $value['foreignKey'], 'records' => $getrecs);
				}catch(Exception $e){
					
				}				
			}

			$customArray = $this->$modal->customArray;
			if($this->request->params['named']['custom_table_id']){
				$this->loadModel('CustomTable');
				$table = $this->CustomTable->find('first',array('fields'=>array('CustomTable.id','CustomTable.fields'), 'recursive'=>-1,'conditions'=>array('CustomTable.id'=>$this->request->params['named']['custom_table_id'])));
				$fields = json_decode($table['CustomTable']['fields'],true); 
			}
			if($customArray){
				foreach ($customArray as $key => $value) {
					foreach($fields as $field){
						if($field['data_type'] == 'radio' && $field['field_name'] = Inflector::variable(Inflector::pluralize($key)) ){
							$belongsToModels[Inflector::underscore($key)] = array('field_name' => Inflector::singularize(Inflector::underscore($key)), 'records' => $value); 
						}
					}				
				}
			}				
			$this->set('src', $src);
			$this->set('belongsToModels', $belongsToModels);
			$this->set('modal', $modal);
			$this->render('/Elements/advance-search');
		}
	}
	public function _get_hasMany() {
		$model = $this->modelClass;
		$this->set('hasMany', $this->$model->hasMany);		
	}
	public function reports() {
		$unset = array('id', 'sr_no', 'name', 'title', 'number', 'system_table_id', 'company_id', 'created', 'modified', 'publish', 'branchid', 'departmentid', 'soft_delete', 'record_status', 'status_user_id', 'division_id', 'master_list_of_format_id', 'list_of_kpi_ids', 'system_table', 'risk_assesment_id', 'state_id', 'login_status', 'password', 'user_access', 'assigned_branches', 'copy_acl_from', 'password_token', 'parent_id', 'work_instructions', 'record', 'file_key', 'version_key', 'file_dir', 'result', 'file_content');
		$modelName = $this->modelClass;
		$this->loadModel($modelName);
		$txt = array('string', 'text');
		$number = array('boolean', 'integer', 'float');
		$dates = array('date', 'datetime');
		foreach ($this->$modelName->schema() as $field_name => $details) {
			if (in_array($details['type'], $txt) && !in_array($field_name, $unset)) {
				$labelFields[$field_name] = $field_name;
			}
			if (in_array($details['type'], $number) && !in_array($field_name, $unset)) {
				$dataFields[$field_name] = $field_name;
			}
			if (in_array($details['type'], $dates) && !in_array($field_name, $unset)) {
				$datefields[$field_name] = $field_name;
			}
		}
		$belongs = $this->$modelName->belongsTo;
		foreach ($belongs as $className => $details) {
			if (!in_array($field_name, $unset)) {
				$labelFields[$details['foreignKey']] = $className;
			}
			if (in_array($details['type'], $number) && !in_array($field_name, $unset)) {
				$datefields[$field_name] = $field_name;
			}
		}
		foreach ($this->$modelName->customArray as $cArrayKey => $cArrayD) {
			$variables[] = Inflector::variable($cArrayKey);
			$variablePlurals[] = Inflector::pluralize(Inflector::variable($cArrayKey));
		}
		$x = 0;
		foreach ($dataFields as $dataField) {
			if($this->$modelName->customArray && $variables && $variablePlurals){
				if (in_array($dataField, array_keys($this->$modelName->customArray)) || in_array($dataField, $variables) || in_array($dataField, $variablePlurals)) {
					if (in_array($dataField, array_keys($this->$modelName->customArray))) {
						$values = $this->$modelName->customArray[$dataField];
					}
					if (in_array($dataField, $variables)) {
						$values = $variables[$dataField];
					}
					if (in_array($dataField, $variablePlurals)) {
						$values = $variablePlurals[$dataField];
					}
					foreach ($values as $vkey => $value) {
						$pieChartsData[$dataField][$value] = $this->$modelName->find('count', array('conditions' => array($modelName . '.' . $dataField => $vkey)));
					}
				}
			}			
		}
		$this->set(array('labelFields' => $labelFields, 'datefields' => $datefields, 'dataFields' => $dataFields, 'resultTypes' => $resultTypes, 'ccount' => 1));
		$i = 0;
		if ($pieChartsData) {
			foreach ($pieChartsData as $pieChartDataKey => $pieChartDataValues) {
				$pieCharts[$pieChartDataKey]['labels'] = array_keys($pieChartDataValues);
				$pieCharts[$pieChartDataKey]['data'] = array_values($pieChartDataValues);
				foreach ($pieChartDataValues as $pieChartDataValue) {
					$pieCharts[$pieChartDataKey]['borderColor'][] = $pieCharts[$pieChartDataKey]['backgroundColor'][] = "#" . str_pad(dechex(rand(0x000000, 0xFFFFFF)), 6, 0, STR_PAD_LEFT);
				}
				$i++;
				$x++;
			}
		}
		$this->set('pieCharts', $pieCharts);
		$employees = $this->_get_employee_list();
		$departments = $this->_get_department_list();
		$branches = $this->_get_branch_list();
		$i = 0;
		$result = $dresult = $bresult = array();
		foreach ($employees as $id => $name) {
			$cnt = $this->$modelName->find('count', array('conditions' => array($modelName . '.prepared_by' => $id)));
			if ($cnt) {
				$result['labels'][$i] = $name;
				$result['data'][$i] = $cnt;
				$result['borderColor'][$i] = $result['backgroundColor'][$i] = "#" . str_pad(dechex(rand(0x000000, 0xFFFFFF)), 6, 0, STR_PAD_LEFT);
				$i++;
			}
		}
		$this->set('employeeDataEntry', $result);
		$i = 0;
		foreach ($departments as $id => $name) {
			$cnt = $this->$modelName->find('count', array('conditions' => array($modelName . '.departmentid' => $id)));
			$dresult['labels'][$i] = $name;
			$dresult['data'][$i] = $cnt;
			$dresult['borderColor'][$i] = $dresult['backgroundColor'][$i] = "#" . str_pad(dechex(rand(0x000000, 0xFFFFFF)), 6, 0, STR_PAD_LEFT);
			$i++;
		}
		$this->set('departmentDataEntry', $dresult);
		$i = 0;
		foreach ($branches as $id => $name) {
			$cnt = $this->$modelName->find('count', array('conditions' => array($modelName . '.branchid' => $id)));
			$bresult['labels'][$i] = $name;
			$bresult['data'][$i] = $cnt;
			$bresult['borderColor'][$i] = $bresult['backgroundColor'][$i] = "#" . str_pad(dechex(rand(0x000000, 0xFFFFFF)), 6, 0, STR_PAD_LEFT);
			$i++;
		}
		$this->set('branchDataEntry', $bresult);
		$this->render('/Elements/reports');
	}
	public function _get_fields($custom_table_id = null) {
		$this->loadModel('CustomTable');
		$fieldTypes = $this->CustomTable->customArray['fieldTypes'];
		$displayTypes = $this->CustomTable->customArray['displayTypes'];
		$table = $this->CustomTable->find('first', array('recursive' => - 1, 'conditions' => array('CustomTable.id' => $custom_table_id)));
		$modelName = Inflector::classify($table['CustomTable']['table_name']);
		if($table['CustomTable']['has_many']){
			$hasMany = array();
			foreach (json_decode($table['CustomTable']['has_many'], true) as $hm) {
				$hasMany[$hm['table_name']] = $hm['friendly_name'];
			}	
		}else{
			$hasMany = $this->$modelName->hasMany;
			foreach($hasMany as $key => $value){
				$hasMany[$key] = $value['className'];
			}
		}
		
		$belongsTo = array();
		
		if($table['CustomTable']['belongs_to']){
			foreach (json_decode($table['CustomTable']['belongs_to'], true) as $key => $value) {
				$belongsTo[$value] = $key;
			}	
		}
		
		foreach (json_decode($table['CustomTable']['fields'], true) as $fields) {
			if ($fields['linked_to'] != - 1) {
				$linkedFields[Inflector::Classify($fields['field_name']) ] = $fields['field_name'];
				// array('field'=>$fields['field_name'],'linkedTo'=>$fields['linked_to']);
				
			}
			if (in_array($fields['field_type'], array(2, 3, 4))) {
				// $numberFields[] = array('field'=>$fields['field_name'],'type'=>$fields['field_name']);
				$numberFields[$fields['field_name']] = $fields['field_name'];
			}
			if (in_array($fields['field_type'], array(4, 5, 6))) {
				// $dateFields[] = array('field'=>$fields['field_name'],'type'=>$fields['field_name']);
				$dateFields[$fields['field_name']] = $fields['field_name'];
			}
		}
		return array($linkedFields, $numberFields, $dateFields, $belongsTo, $hasMany);
	}
	public function generate_charts() {
		$allFields = $this->_get_fields($this->request->params['named']['custom_table_id']);
		if ($this->request->params['pass'][0]) $ccount = $this->request->params['pass'][0];
		else $ccount = 1;
		if ($this->request->is('post')) {
			$modelName = $this->modelClass;
			$this->loadModel($modelName);
			$txt = array('string', 'text');
			$number = array('boolean', 'integer', 'float');
			$dates = array('date', 'datetime');
			if (in_array($details['type'], $txt) && !in_array($field_name, $unset)) {
				$labelFields[$field_name] = $field_name;
			}
			if (in_array($details['type'], $number) && !in_array($field_name, $unset)) {
				$dataFields[$field_name] = $field_name;
			}
			if (in_array($details['type'], $dates) && !in_array($field_name, $unset)) {
				$datefields[$field_name] = $field_name;
			}
			$find = $this->request->data['Reports'];
			$find = array_shift($find);
			
			$belongs = $this->$modelName->belongsTo;
			if ($find['lables'] && isset($find['result_type'])) {
				// first get label master
				foreach ($belongs as $className => $details) {
					if ($find['lables'] == $className) { // ??
						$lists = $this->$modelName->$className->find('list');

						foreach ($lists as $key => $name) {
							if($find['result_type'] == 0){
								$result[$name] = $this->$modelName->find(
								// $resultTypes[$this->request->data['Reports']['result_type']],
									'count', array('conditions' => array($modelName . '.' . $details['foreignKey'] => $key)));
							}

							if($find['result_type'] == 1){
								$this->$modelName->virtualFields = array('sumvalue'=>'SUM('.$modelName.'.'.$find['data_field'].')');
								$rec = $this->$modelName->find(
									'first',
									array(
										'fields'=>array($modelName.'.sumvalue',$modelName.'.sumvalue'),
										'recursive'=>-1,
										'limit'=>1,
										// 'group'=>array($modelName.'.'.$key),
										'conditions' => array($modelName . '.' . $details['foreignKey'] => $key)
									));
								if($rec[$modelName]['sumvalue'])$result[$name] = $rec[$modelName]['sumvalue'];
								else $result[$name] = 0;
							}

							if($find['result_type'] == 2){
								$this->$modelName->virtualFields = array('sumvalue'=>'AVG('.$modelName.'.'.$find['data_field'].')');
								$rec = $this->$modelName->find(
									'first',
									array(
										'fields'=>array($modelName.'.sumvalue',$modelName.'.sumvalue'),
										'recursive'=>-1,
										'limit'=>1,
										// 'group'=>array($modelName.'.'.$key),
										'conditions' => array($modelName . '.' . $details['foreignKey'] => $key)
									));
								if($rec[$modelName]['sumvalue'])$result[$name] = $rec[$modelName]['sumvalue'];
								else $result[$name] = 0;
							}
						}
					}
				}
			}
		}

		$i = 0;
		foreach ($result as $key => $r) {
			$formChart['labels'][$i] = $key;
			$formChart['data'][$i] = $r;
			$formChart['borderColor'][$i] = $formChart['backgroundColor'][$i] = "#" . str_pad(dechex(rand(0x000000, 0xFFFFFF)), 6, 0, STR_PAD_LEFT);
			$i++;
		}

		$i = 0;
		$resultTypes = array('count', 'sum', 'avg');
		$chartTypes = array('line', 'bar', 'pie', 'doughnut');
		$this->set(array('labelFields' => $allFields[0], 'datefields' => $allFields[1], 'dataFields' => $allFields[2], 'belongsTo' => $allFields[3], 'hasMany' => $allFields[4], 'resultTypes' => $resultTypes, 'ccount' => $ccount));
		$this->set(array('resultTypes' => $resultTypes, 'chartTypes' => $chartTypes));
		$this->set('formChart', $formChart);
		$this->render('/Elements/generate_charts');
	}

	public function _getDisplayField($model = null){
		echo $model;
		exit;

	}

	public function get_default($model = null){
		$this->autoRender = false;
		$this->loadModel($model);
		return $this->$model->displayField;

	}

	public function _table_menu(){ 
		$this->loadModel('CustomTable');
		$standards = $this->CustomTable->QcDocument->Standard->find('list',array('conditions'=>array('Standard.publish'=>1)));
		$documentTypes = $this->CustomTable->QcDocument->customArray['documentTypes'];
		foreach($standards as $key => $value){ 
			foreach($documentTypes as $dkey => $documentType){
				$result = $this->CustomTable->find('all',array(
					'recursive'=>0,
					'fields'=>array('CustomTable.id','CustomTable.name','CustomTable.table_name','CustomTable.table_version','CustomTable.qc_document_id','CustomTable.process_id'), 
					'conditions'=>array(
						'QcDocument.standard_id'=>$key,
						'CustomTable.publish' => 1, 
						// 'QcDocument.add_records' => 1, 
						'QcDocument.document_type'=>$dkey,
						'QcDocument.parent_document_id'=> -1,
						'CustomTable.table_locked' => 0, 
						'CustomTable.table_name NOT LIKE' => '%_child_%', 
						'OR' => array(
							'QcDocument.departments LIKE ' => '%' . $this->Session->read('User.department_id') . '%',
							'QcDocument.branches LIKE ' => '%' . $this->Session->read('User.branch_id') . '%',
							'QcDocument.user_id LIKE ' => '%' . $this->Session->read('User.id') . '%',
							'QcDocument.editors LIKE ' => '%' . $this->Session->read('User.id') . '%'
						)
					)
				)); 
				if($result)$menus["$value"][$documentType] = $result;
			} 
		} 

		$this->set('menus',$menus); 
	}

	public function clean_table_names($tableName = null){
		$tableName = $this->_clean_table_names($tableName);
		return $tableName;
	}

	public function _clean_table_names($tableName = null){
		if($tableName){
			$tableName = ltrim(rtrim($tableName));		
			$tableName = str_replace('/', '_', $tableName);
			$tableName = str_replace('-', '_', $tableName);
			$tableName = str_replace('&', '_', $tableName);
			$tableName = ltrim(rtrim(strtolower($tableName)));
			$tableName = preg_replace('/[\@\.\;\()~!@#$%^&*_+" "-]+/', '_', $tableName);
			$tableName = preg_replace('/ */', '', $tableName);
			$tableName = preg_replace('/\\s+/', '_', $tableName); 
			$tableName = preg_replace('/-*-/', '_', $tableName);
			$tableName = preg_replace('/_*_/', '_', $tableName);
			// $tableName = preg_replace('/[0-9]/', '', $tableName);
			// $tableName = ltrim($tableName,'_');	
			$tableName = preg_replace('/^([^a-zA-Z0-9])*/', '', $tableName);		
			$tableName = rtrim($tableName,"_");

			return $tableName;
		}		
	}

	public function save_doc() { 
		$this->autoRender = false;
		$local = $this->request->params['named'];
		
		if (($body_stream = file_get_contents("php://input")) === FALSE) {
			echo "Bad Request";
		}
		$data = json_decode($body_stream, TRUE); 

		if ($data["status"] == 2) {
			$data = json_decode($body_stream, TRUE);
			
			if($local['tmp']){
				$record_id = $local['record_id'] = 'tmp'. DS . $local['record_id'];
			}else{
				$record_id = $local['record_id'] = $local['record_id'];
			}
			
			$path_for_save = WWW_ROOT . 'files' . DS . $local['company_id'] . DS . $local['controller'] . DS . $local['record_id'];

			if($local['controller'] == 'qc_documents'){
				if($this->request->params['named']['tmp']){
					
					$file_type = $local['filetype'];
					$file_name = 'blank';
					$fileName = $file_name . '.' . $file_type;
					
					$file_for_save = $path_for_save . DS . $fileName;

					$testfolder = new Folder($path_for_save);
					$testfolder->create($path_for_save);
					chmod($path_for_save, 0777);
					chmod($file_for_save, 0777);
					$downloadUri = $data["url"];
					
					if (file_get_contents($downloadUri) === FALSE) {

					} else {
						$new_data = file_get_contents($downloadUri);
						if (file_put_contents($file_for_save, $new_data,LOCK_EX)) {

							$qcdoc = $this->QcDocument->find('first', array('recursive' => - 1, 'conditions' => array('QcDocument.id' => $this->request->params['named']['record_id'])));
							$key = $this->_generate_onlyoffice_key($record_id . date('Ymdhis'));

							$updates = json_decode($qcdoc['QcDocument']['version_keys'],true);
							if(is_array($updates)){
								$last_modified = date('Y-m-d H:i:s');
								$updates[] = array('version_key'=>$key,'modified'=>$last_modified,'by'=>$local['user']);
							}else{
								$last_modified = date('Y-m-d H:i:s');
								$updates[] = array('version_key'=>$key,'modified'=>$last_modified,'by'=>$local['user']);
							}

							$qcdoc['QcDocument']['version_keys'] = json_encode($data['history']);

							$qcdoc['QcDocument']['file_key'] = $key;
							$qcdoc['QcDocument']['data_received'] = 'added from save_doc';
							$qcdoc['QcDocument']['file_status'] = 1;
							$qcdoc['QcDocument']['last_saved'] = $last_modified;
							$qcdoc['QcDocument']['version'] = 1;
							
							$this->QcDocument->create();
							$this->QcDocument->save($qcdoc['QcDocument']);

							$json = [
								"created" => date("Y-m-d H:i:s"),
								'uid'=>$data['history']['changes'][0]['user']['id'],
								'name'=>$data['history']['changes'][0]['user']['name'],
							];
							
							// write the encoded file information to the createdInfo.json file
							$version = 1;
							$history_file_for_save = $path_for_save . DS .$fileName.'-hist' . DS . $version;
							$historyFolder = new Folder($history_file_for_save);
							$historyFolder->create($history_file_for_save);
							chmod($history_file_for_save, 0777);
							file_put_contents($history_file_for_save . DS . "createdInfo.json", json_encode($json, JSON_PRETTY_PRINT));
						} else {

						} 
					} 
				}else{

					$this->loadModel('QcDocument');
					$qcdoc = $this->QcDocument->find('first', array('recursive' => - 1, 'conditions' => array('QcDocument.id' => $this->request->params['named']['record_id'])));
					
					$file_type = $qcdoc['QcDocument']['file_type'];
					$file_name = $qcdoc['QcDocument']['title'];
					$document_number = $qcdoc['QcDocument']['document_number'];
					$document_version = $qcdoc['QcDocument']['revision_number'];
					
					// save previous file version
					$fileName = $document_number . '-' . $file_name . '-' . $document_version;
					$fileName = $this->_clean_table_names($fileName);
					$fileName = $fileName . '.' . $file_type;
					$file_for_save = $path_for_save . DS . $fileName;

					$testfolder = new Folder($path_for_save);
					$testfolder->create($path_for_save);

					$version = $qcdoc['QcDocument']['version'];
					$history_file_for_save = $path_for_save . DS .$fileName.'-hist' . DS . $version;
					$historyFolder = new Folder($history_file_for_save);
					$historyFolder->create($history_file_for_save);


					chmod($path_for_save, 0777);
					chmod($file_for_save, 0777);
					chmod($history_file_for_save, 0777);

					$downloadUri = $data["url"];

					// update_version if(version to be updated of not);
					// if($qcdoc['QcDocument']['update_version'] == 1){

					$preFileName = 'prev' . '.'.$file_type;
					$url = $preFile_for_save = $history_file_for_save . DS . $preFileName;
					$url = str_replace('\/','/',$url);
						// adding diff.zip file
					$changesData = file_get_contents($data["changesurl"]);
					file_put_contents($history_file_for_save. DS . "diff.zip", $changesData, LOCK_EX);

						// adding prev.ext file
					$fromFile = new File($file_for_save);
					$fromFile->copy($preFile_for_save,true);

					$newHistory = array(
							// 'key'=>$qcdoc['QcDocument']['file_key'],
						'key'=>$data['key'],
						'version'=>$version,
						'changes'=> $data['history']['changes'],
						'serverVersion'=>$data['history']['serverVersion'],
						'created'=>$data['history']['changes'][0]['created'],
						'user'=>array(
							'id'=>$data['history']['changes'][0]['user']['id'],
							'name'=>$data['history']['changes'][0]['user']['name'],
							'url'=>$url
						),
					);

					file_put_contents($history_file_for_save . DS . "changes.json", json_encode($newHistory), LOCK_EX);

						// adding key.txt file
					file_put_contents($history_file_for_save . DS . "key.txt", $data['key'], LOCK_EX);

					$downloadUri = $data["url"];
					$historyData = $data["changesurl"];

					$versions = json_decode($qcdoc['QcDocument']['versions'],true);
					$versions[] = $newHistory;
					
					if (file_get_contents($downloadUri) === FALSE) {
						
					} else {
						$new_data = file_get_contents($downloadUri);
						if (file_put_contents($file_for_save, $new_data,LOCK_EX)) {

							$key = $this->_generate_onlyoffice_key($record_id . date('Ymdhis'));
							// $key = $data["key"];
							
							if(is_array($updates)){
								$last_modified = date('Y-m-d H:i:s');
								// $updates[] = array('version_key'=>$key,'modified'=>$last_modified,'by'=>$local['user']);
							}else{
								$last_modified = date('Y-m-d H:i:s');
								// $updates[] = array('version_key'=>$key,'modified'=>$last_modified,'by'=>$local['user']);
							}

							$qcdoc['QcDocument']['version_keys'] = json_encode($data['history']);
							$qcdoc['QcDocument']['file_key'] = $key;
							$qcdoc['QcDocument']['file_status'] = 1;
							$qcdoc['QcDocument']['last_saved'] = $last_modified;

							// if($qcdoc['QcDocument']['update_version'] == 1){
							$qcdoc['QcDocument']['version'] = $version + 1;
							$qcdoc['QcDocument']['versions'] = json_encode($versions);

								// $history_data = file_get_contents($historyData);
								// if (file_put_contents($history_file_for_save, $history_data)) {
								// }
							// }else{
							// // $qcdoc['QcDocument']['versions'] = json_encode($qcdoc['QcDocument']['versions']);
							// }
							
							$this->QcDocument->create();
							if($this->QcDocument->save($qcdoc['QcDocument'])){
								
							}else{
								
							}

							// update process document
							$this->loadModel('Process');
							$process = $this->Process->find('first',array('recursive'=>-1, 'conditions'=>array('Process.qc_document_id'=>$record_id)));
							
							if($process){
								$this->requestAction(array('controller'=>'processes','action'=>'update_process',$process['Process']['id']));
							}


							// also update custom table document.
							$this->loadModel('CustomTable');
							$customTables = $this->CustomTable->find('list',array('conditions'=>array('CustomTable.qc_document_id'=>$qcdoc['QcDocument']['id'])));
							
							if($customTables){
								foreach($customTables as $custom_table_id => $name){
									$qcpfile = WWW_ROOT . 'files' . DS . $local['company_id'] . DS . 'qc_documents' . DS . $qcdoc['QcDocument']['id'] . DS . $fileName;
									$tofile = WWW_ROOT . 'files' . DS . $local['company_id'] . DS . 'custom_tables' . DS . $custom_table_id . DS . $fileName;

									if (copy($qcpfile, $tofile)) {
									} else {
										
									}
								}
							}
						} else {
							
						}
						
					}
				}

			}else if($local['controller'] == 'processes'){
				$this->loadModel('Process');
				$process = $this->Process->find('first', array('recursive' => - 1, 'conditions' => array('Process.id' => $this->request->params['named']['record_id'])));
				
				$file_type = $process['Process']['file_type'];
				$file_name = $process['Process']['file_name'];
				
				$fileName = $file_name;
				
				$file_for_save = $path_for_save . DS . $fileName;
				$testfolder = new Folder($path_for_save);
				$testfolder->create($path_for_save);
				chmod($path_for_save, 0777);
				chmod($file_for_save, 0777);
				$downloadUri = $data["url"];
				if (file_get_contents($downloadUri) === FALSE) {
					
				} else {
					$new_data = file_get_contents($downloadUri);
					if (file_put_contents($file_for_save, $new_data)) {
						$key = $this->_generate_onlyoffice_key($record_id . date('Ymdhis'));
						$process['Process']['file_key'] = $key;
						$process['Process']['file_status'] = 1;
						$process['Process']['last_saved'] = date('Y-m-d H:i:s');
						$process['Process']['version_keys'] = $process['Process']['version_keys'] . ', ' . $key;
						$this->Process->create();
						$this->Process->save($process['Process']);

					} else {
						
					}
					
				} 
			}
			
		}
		echo "{\"error\":0}";
	}

	public function save_file() { 
		$this->autoRender = false;
		$local = $this->request->params['named'];
		
		if (($body_stream = file_get_contents("php://input")) === FALSE) {
			echo "Bad Request";
		}
		$data = json_decode($body_stream, TRUE);
		
		if ($data["status"] == 2) {
			$data = json_decode($body_stream, TRUE);
			$record_id = $this->request->params['named']['record_id'];
			$path_for_save = WWW_ROOT . 'files' . DS . $local['company_id'] . DS . 'files' . DS . $local['record_id'];
			
			$this->loadModel('File');
			
			$qcdoc = $this->File->find('first', array('recursive' => - 1, 'conditions' => array('File.id' => $this->request->params['named']['record_id'])));

			
			$file_type = $qcdoc['QcDocument']['file_type'];
			$file_name = $qcdoc['QcDocument']['title'];
			$file_name = $this->_clean_table_names($file_name);
			$fileName = $file_name . '.' . $file_type;
			$file_for_save = $path_for_save . DS . $fileName;


			$testfolder = new Folder($path_for_save);
			$testfolder->create($path_for_save);
			chmod($path_for_save, 0777);
			chmod($file_for_save, 0777);
			$downloadUri = $data["url"];

			if (file_get_contents($downloadUri) === FALSE) {

			} else {
				$new_data = file_get_contents($downloadUri);
				if (file_put_contents($file_for_save, $new_data)) {
					$key = $this->_generate_onlyoffice_key($record_id . date('Ymdhis')); 

					$updates = json_decode($qcdoc['QcDocument']['version_keys'],true);
					if(is_array($updates)){
						$last_modified = date('Y-m-d H:i:s');
						$updates[] = array('version_key'=>$key,'modified'=>$last_modified,'by'=>$local['user']);
					}else{
						$last_modified = date('Y-m-d H:i:s');
						$updates[] = array('version_key'=>$key,'modified'=>$last_modified,'by'=>$local['user']);
					}

					$qcdoc['QcDocument']['version_keys'] = json_encode($data["history"]);

					$qcdoc['File']['file_key'] = $key;
					$qcdoc['File']['file_status'] = 1;
					$qcdoc['File']['last_saved'] = $last_modified;

					$this->File->create();
					$this->File->save($qcdoc['file']);

				} else {

				}

			}
		}
		echo "{\"error\":0}";
	}

	public function _upload_custom_files($files = null, $id = null){
		
		$path = WWW_ROOT . 'files' . DS . $this->Session->read('User.company_id') . DS . $this->request->controller . DS . $id . DS . 'record_files';
		$recordfilesfolder = new Folder($path_for_save);
		$recordfilesfolder->create($path);
		chmod($path, 0777);
		foreach($files as $file){
			move_uploaded_file($file['tmp_name'], $path . DS . $file['name']); 
		}
		return true;

	}

	public function curl($type = null, $api_controller = null, $path = null,$data = null,$linkedTosWithDisplay = null){

		if(empty($type))$type = $this->request->params['named']['type'];
		$linkedTosWithDisplay = json_decode($this->request->params['named']['linkedTosWithDisplay']);
		if(empty($api_controller))$api_controller = $this->request->params['named']['api_controller'];
		
		if($type == 'post'){ 
			if(empty($data))$data = $this->request->params['named']['data'];

			$controllers = array();
			$aCtrlClasses = App::objects('controller');
			$skip = array('AppController', 'ApprovalsController', 'ApprovalCommentsController', 'CustomTablesController', 'FilesController', 'RecordsController', 'UserSessionsController');
			foreach ($aCtrlClasses as $controller) {
				if (!in_array($controller, $skip)) {
					$controller = str_replace('Controller', '', $controller);
					$name = $this->CustomTable->find('first', array('recursive' => 1, 'conditions' => array('CustomTable.table_name LIKE' => "%" . Inflector::underscore($controller)), 'fields' => array('CustomTable.id', 'CustomTable.name', 'CustomTable.table_version')));
					if ($name) {
						$controller = Inflector::classify($controller);
						$linkedTos[$controller] = $name['CustomTable']['name'] . " ver " . $name['CustomTable']['table_version'];
					} else {						
						$linkedTos[$controller] = $controller;
					}
				}
			}
			
			if(!empty($this->request->params['named']['path']))$path = $this->request->params['named']['path'];
			
			$str = 'company_id:'.$this->Session->read('User.company_id');
			$path = Configure::read('ApiPath').$api_controller."/".$path."/".$this->Session->read('User.company_id')."/api:false/". $str;
			$response = $this->_curl_post($path,$linkedTos,$data,$linkedTosWithDisplay);

			return $response;
		} 

		if($type == 'get'){
			$str = 'company_id:'.$this->Session->read('User.company_id');
			if(!empty($this->request->params['named']['type']))$str1 = '/type:'.$this->request->params['named']['type'];
			$path = Configure::read('ApiPath').$api_controller."/".$path."/".$this->Session->read('User.company_id')."/api:true/". $str.$str1;
			$response = $this->_curl_get($path);
			return $response;
		}
	}


	public function _curl_post($path = null, $linkedTos = null, $data = null,$linkedTosWithDisplay = null){ 

		$postdata = array(
			'linkedTos'=>$linkedTos,
			'data'=>$data,
			'linkedTosWithDisplay'=>$linkedTosWithDisplay 
		); 

		$curl = curl_init();
		
		curl_setopt_array($curl, array(
			CURLOPT_URL => $path,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => "------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"data\"\r\n\r\n".json_encode($postdata)."\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW--",
			CURLOPT_HTTPHEADER => array(
				"authorization: Basic YWJjOjEyMw==",
				"cache-control: no-cache",
				"content-type: multipart/form-data; boundary=----WebKitFormBoundary7MA4YWxkTrZu0gW",
				"postman-token:".base64_encode($this->Session->read('User.company_id')),			
			),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if($err){
			echo "Error";
		} 
		if($response){
			return $response;
		}
	}


	public function _curl_get($path = null){

		$curl = curl_init();
		$auth = base64_encode('user:'.$this->Session->read('User.company_id'));
		$head ='Authorization: Basic '. $auth;

		curl_setopt_array($curl, array(
			CURLOPT_URL => $path,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "GET",
			CURLOPT_HTTPHEADER => array( 
			),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);
		
		if($err){

		} 

		if($response){
			return $response;
		}

	}

	public function _file_clean_up(){
		$this->loadModel('File');
		$files = $this->File->find('all',array('conditions'=>array('File.record_id'=>'tmp'), 'fields'=>array('File.id','File.name','File.file_type'),'recursive'=>-1));
		foreach($files as $file){
			$check_files = Configure::read('files') . DS . 'files' . DS . $file['File']['id'] . DS . $file['File']['name'] .'.'.$file['File']['file_type'];
			if(!file_exists($check_files)){
				$this->File->delete($file['File']['id']);
			}
		}        

        // remove empty folders
		$file_folder = New Folder(Configure::read('files') . DS . 'files');
		$folders = $file_folder->read();
		foreach($folders[0] as $check_folder){
			$folder_to_check = New Folder(Configure::read('files') . DS . 'files' . DS . $check_folder);
			$check_files = $folder_to_check->read();
			if(count($check_files[1]) == 0){
                //delete folder and also dete file table record
                // $file = $this->File->find('first',array('conditions'=>array('File.id'=>$check_folder)));
				$this->File->delete($check_folder);
				$folder_to_check->delete();
			}else{
				$file = $this->File->find('count',array('conditions'=>array('File.id'=>$check_folder)));
				if($file == 0){
					$folder_to_check->delete();
				}
			}
		}        
	}


	public function _prepare_update(){	
		$this->_file_clean_up();	
		$this->cacheAction = false;
		$modelName = $this->modelClass;
		$this->$modelName->CustomTable->virtualFields = array(
			'document_schedule'=>'select schedule_id from qc_documents where qc_documents.id LIKE CustomTable.qc_document_id',
			'process_schedule'=>'select schedule_id from processes where processes.id LIKE CustomTable.process_id'
		);

		$customTable = $this->$modelName->CustomTable->find('first',array('conditions'=>array('CustomTable.id'=>$this->request->params['named']['custom_table_id']),'recursive'=>-1));		
		if($customTable['CustomTable']['document_schedule'])$schedule_id = $customTable['CustomTable']['document_schedule'];
		if($customTable['CustomTable']['process_schedule'])$schedule_id = $customTable['CustomTable']['process_schedule'];
		$this->loadModel('Schedule');
		$schedule = $this->Schedule->find('first',array('conditions'=>array('Schedule.id'=>$schedule_id),'recursive'=>-1,'fields'=>array('Schedule.id','Schedule.name')));
		
		switch ($schedule['Schedule']['name']) {
			case 'Daily':
			$previous_record_date = date('Y-m-d',strtotime('-1 day'));
			break;
			case 'Weekly':
			$previous_record_date = date('Y-m-d',strtotime('-1 week'));
			break;
			case 'Monthly':
			$previous_record_date = date('Y-m-d',strtotime('-1 month'));
			break;
			case 'Quarterly':
			$previous_record_date = date('Y-m-d',strtotime('-15 days'));
			break;
			case 'Yearly':
			$previous_record_date = date('Y-m-d',strtotime('-1 year'));
			break;
			case 'Half-Yearly':
			$previous_record_date = date('Y-m-d',strtotime('-6 months'));
			break;
			case 'None':
			$previous_record_date = null;
			break;
		} 

		if($customTable){
			if($customTable['CustomTable']['publish'] != 1){
				$this->Session->setFlash(__('This table is not yet available for adding data'));
				$this->redirect(array('action' => 'index','custom_table_id'=>$this->request->params['named']['custom_table_id'],'qc_document_id'=>$this->request->params['named']['qc_document_id'],'process_id'=>$this->request->params['process_id'])); 
			}
			$this->set('customTable',$customTable);
		}else{
			$this->Session->setFlash(__('Unknown table'));
			$this->redirect(array('action' => 'index','custom_table_id'=>$this->request->params['named']['custom_table_id'],'qc_document_id'=>$this->request->params['named']['qc_document_id'],'process_id'=>$this->request->params['process_id']));
		} 

		
		$modelName = $this->modelClass;
		if(!empty($this->request->params['named']['qc_document_id'])){ // treat as qc
			
			$this->loadModel('QcDocument');
			$qc_document = $this->QcDocument->find('first',array('conditions'=>array('QcDocument.id'=>$this->request->params['named']['qc_document_id'])));


			$file_type = $qc_document['QcDocument']['file_type'];
			$file_name = $file_name_without_ext = $this->_clean_table_names($qc_document['QcDocument']['title']);
			$document_number = $qc_document['QcDocument']['document_number'];
			$document_version = $qc_document['QcDocument']['revision_number'];
			$file_name = $document_number.'-'.$file_name.'-'.$document_version;
			$file_name = $this->_clean_table_names($file_name);
			$file_name = $file_name .'.'.$file_type;

			$file_name_without_ext = $document_number.'-'.$file_name_without_ext.'-'.$document_version;
			$file_name_without_ext = $this->_clean_table_names($file_name_without_ext);			

		}

		if(!empty($this->request->params['named']['process_id'])){ // treat as qc 
			$this->loadModel('Process');
			$process = $this->Process->find('first',array('conditions'=>array('Process.id'=>$this->request->params['named']['process_id']))); 
			$file_type = $process['Process']['file_type'];
			$file_name_without_ext = $process['Process']['name'];
			$file_name = $process['Process']['name'].'.'.$file_type; 
		} 


		if(!empty($customTable['CustomTable']['qc_document_id']) && !isset($this->request->params['named']['qc_document_id'])){
			$this->Session->setFlash(__('Select form first'));
			$this->redirect(array('controller'=>'custom_tables', 'action' => 'index'));
		}else{
			$document = $this->_qc_document_header($this->request->params['named']['qc_document_id']);
		}

		if(!empty($customTable['CustomTable']['process_id']) && !isset($this->request->params['named']['process_id'])){ 
			$this->Session->setFlash(__('Select form first'));
			$this->redirect(array('controller'=>'custom_tables', 'action' => 'index'));
		}else{ 
			$process = $this->_process_header($this->request->params['named']['process_id']);
		} 

		if(!empty($this->request->params['named']['custom_table_id'])){ // treat as custom forms
			try{
				$customTable = $this->$modelName->CustomTable->find('first',array('conditions'=>array('CustomTable.id'=>$this->request->params['named']['custom_table_id']),'recursive'=>-1)); 
			}catch(Exception $e){

			}			

			// check if file exists
			// if record schedule is daily and user is adding new record on a same day/ or schedule is monthly and user is adding record again in a same month, load existing file for update

			// find lass added file if schedule is not null
			$this->loadModel('File');
			
			if($previous_record_date != null){
				$existing_file = $this->File->find('first',array('conditions'=>array(
					'File.created_by'=>$this->Session->read('User.id'),
					'File.model'=>$this->modelClass,
					'File.controller' => $this->request->controller,
					'DATE(File.created)'=> date('Y-m-d',strtotime($previous_record_date))
				)));



			}else{
				$existing_file = null;
			}
			
			if($existing_file){
				return $existing_file;
			}else{
				// delete tmp files
				$files_to_delete = $this->File->find('all',array('conditions'=>array(
					'File.record_id'=>'tmp',
					'File.user_id'=>$this->Session->read('User.id'),
					'File.custom_table_id'=>$customTable['CustomTable']['id']),
				'recursive'=>-1,
			));

				foreach($files_to_delete as $file_to_delete){
					unlink(Configure::read('files') . DS . 'files' . $file_to_delete['File']['id'] . 'DS '. $file_to_delete['File']['name'].'.'.$file_to_delete['File']['file_type']);
					$this->File->delete($file_to_delete['File']['id']);
				}

				if(!empty($this->request->params['named']['qc_document_id'])){
					$fromFile = Configure::read('files') . DS . 'custom_tables' . DS . $customTable['CustomTable']['id'] . DS . $file_name;
				}

				if(!empty($this->request->params['named']['process_id'])){
					$fromFile = Configure::read('files') . DS . 'processes' . DS . $this->request->params['named']['process_id'] . DS . $file_name;
				}
				
					// copy file only if data_type is document or both. Skip copying file of data_type = 1
					if($document['QcDocument']['data_type'] != 1) { // data type is document or both then open for editing.

						if(file_exists($fromFile)){
							$data['File']['name'] = $file_name_without_ext;
							$data['File']['file_type'] = $file_type;
							$data['File']['file_key'] = $this->_generate_onlyoffice_key($this->request->params['named']['custom_table_id'].date('Ymdhis'));
							$data['File']['qc_document_id'] = $qc_document['QcDocument']['id'];
							$data['File']['process_id'] = $process['Process']['id'];
							$data['File']['custom_table_id'] = $this->request->params['named']['custom_table_id'];
							$data['File']['model'] = $this->modelClass;
							$data['File']['controller'] = $this->request->controller;
							$data['File']['prepared_by'] = $data['File']['modified_by'] = $this->Session->read('User.employee_id');
							$data['File']['created'] = date('Y-m-d h:i:s');
							$data['File']['created_by'] = $data['File']['user_id'] = $this->Session->read('User.id');
							$data['File']['record_id'] = 'tmp';
							$data['File']['file_status'] = 0;
							$data['File']['data_received'] = 'added from prepare_update';
							
							$this->File->create();
							if($this->File->save($data, false)){
								$toFile = Configure::read('files') . DS . 'files' . DS . $this->File->id . DS . $file_name;

								if(file_exists($toFile))unlink($toFile);
								
								$folder = new Folder(); 
								$dir = new Folder(Configure::read('files') . DS . 'files' . DS . $this->File->id, true, 0777);

								if (copy($fromFile, $toFile)) {
									$data['File']['id'] = $this->File->id;

									// also delete last unused files
									// same controller - model - user - last_saved == null 
									// record = tmp
									$filesToDelete = $this->File->find('all',array('conditions'=>array(
										'File.controller'=>$this->request->controller,
										'File.model'=>$this->modelClass,
										'File.record_id'=>'tmp',
										'File.user_id'=>$this->Session->read('User.id'),
										'File.last_saved'=>NULL,
										'File.id !=' => $this->File->id
									),'recursive'=>-1));
									
									foreach($filesToDelete as $fileToDelete){
										$this->File->delete(array($fileToDelete['File']['id']));
										// delete dir
										$dirToDelete = new Folder(Configure::read('files') . DS . 'files' . DS . $fileToDelete['File']['id']);
										$dirToDelete->delete();

									}

									return $data;
								}else{
									
									$this->File->delete(array($this->File->id));
									$this->Session->setFlash(__('Failed to copy file'));
									$this->redirect(array('action' => 'index','table_type'=>$this->request->params['named']['table_type']));$this->redirect(array('action' => 'index'));
								}
							}
						}else{
						// $this->Session->setFlash(__('Unable to copy file.'));
						// $this->redirect(array('action' => 'index','table_type'=>$this->request->params['named']['table_type']));
						}
					}else{


						// open same file in view mode						
						$data['File']['id'] = $customTable['CustomTable']['id'];
						$data['File']['name'] = $file_name_without_ext;
						$data['File']['file_type'] = $file_type;
						$data['File']['file_key'] = $this->_generate_onlyoffice_key($this->request->params['named']['custom_table_id'].date('Ymdhis'));
						$data['File']['qc_document_id'] = $qc_document['QcDocument']['id'];
						$data['File']['process_id'] = $process['Process']['id'];
						$data['File']['custom_table_id'] = $this->request->params['named']['custom_table_id'];
						$data['File']['model'] = $this->modelClass;
						$data['File']['controller'] = $this->request->controller;
						$data['File']['prepared_by'] = $data['File']['modified_by'] = $this->Session->read('User.employee_id');
						$data['File']['created'] = date('Y-m-d h:i:s');
						$data['File']['created_by'] = $data['File']['user_id'] = $this->Session->read('User.id');
						$data['File']['record_id'] = 'tmp';
						$data['File']['file_status'] = 0;
						$data['File']['data_received'] = 'added from prepare_update';

						

						return $data;
					}					
				} 
			} 

			if(!empty($customTable['CustomTable']['qc_document_id']) && !isset($this->request->params['named']['qc_document_id'])){
				$this->Session->setFlash(__('Select form first'));
				$this->redirect(array('controller'=>'custom_tables', 'action' => 'index'));
			}else{
				$document = $this->_qc_document_header($this->request->params['named']['qc_document_id']);
			}

			if(!empty($customTable['CustomTable']['process_id']) && !isset($this->request->params['named']['process_id'])){ 
				$this->Session->setFlash(__('Select form first'));
				$this->redirect(array('controller'=>'custom_tables', 'action' => 'index'));
			}else{
				$process = $this->_process_header($this->request->params['named']['process_id']);
			} 
		}

		public function _view(){
		// check if this document has linked documents with table
			$this->loadModel('CustomTable');
			$linkedTables = $this->CustomTable->find('all',array(
				'recursive'=>0,
				'fields'=>array(
					'CustomTable.id',
					'CustomTable.name',
					'CustomTable.table_name',
					'CustomTable.field_name',
					'CustomTable.field_value',
					'QcDocument.id',
					'QcDocument.parent_document_id',
					'QcDocument.title',
				),
				'conditions'=>array(
					'CustomTable.table_type !=' => 2,
					'QcDocument.parent_document_id' => $this->request->params['named']['qc_document_id'],
				// 'CustomTable.field_name'=>,
				// 'CustomTable.field_value'=>,
				)));
			$t = 0;
			foreach($linkedTables as $linkedTable){
				if($this->viewVars[Inflector::variable($this->modelClass)][$this->modelClass][$linkedTable['CustomTable']['field_name']] == $linkedTable['CustomTable']['field_value']){
					$load[$t]['custom_table_id'] = $linkedTable['CustomTable']['id'];
					$load[$t]['qc_document_id'] = $linkedTable['QcDocument']['id'];
					$load[$t]['name'] = $linkedTable['QcDocument']['title'];
					$load[$t]['table_name'] = $linkedTable['CustomTable']['table_name'];
					$load[$t]['action'] = 'add';
				}else{
					$load[$t]['custom_table_id'] = $linkedTable['CustomTable']['id'];
					$load[$t]['qc_document_id'] = $linkedTable['QcDocument']['id'];
					$load[$t]['name'] = $linkedTable['QcDocument']['title'];
					$load[$t]['table_name'] = $linkedTable['CustomTable']['table_name'];
					$load[$t]['action'] = 'index';
				} 
				$t++;
			}
			$this->set('loadLinkedTables',$load);
		}

		public function _update_belongTos($data = null){			
			try{
				foreach($data['belongsTos'] as $bModel => $belongsToData){
					$modelName = $this->modelClass;
					foreach($this->$modelName->belongsTo as $mname => $details){
						if($details['className'] == $bModel){
							//Field from main form
							$fieldFromMainForm = $data[$modelName][$details['foreignKey']];
							if($details['foreignKey'] != 'qc_document_id'){
								$this->loadModel($bModel);
								$bModelRecord = $this->$bModel->find('first',array('recursive'=>-1,'conditions'=>array($bModel.'.id'=>$fieldFromMainForm)));
								$dataarray[$bModel] = array_merge($bModelRecord[$bModel],$belongsToData);
								$this->$bModel->create();
								$this->$bModel->save($dataarray[$bModel],false);
							}							
						}
					}
				}
			}catch(Exception $e){

			}			
		}

		public function _add(){
			$modelName = $this->modelClass;			
			foreach($this->request->data[$modelName] as $key => $value){
				if(is_array($value)){
					if($this->request->data[$modelName][$key]['name']){
						$this->request->data[$modelName][$key] = json_encode($value); 
						$this->request->data['Files'][] = $value;
					}else{
						$this->request->data[$modelName][$key] = json_encode($value);
					}

				}
			}

			if($this->request->data[$modelName]['additional_files'])$additionalFiles = json_decode($this->request->data[$modelName]['additional_files'],true);

			if($this->request->params['named']['parent_record_id']){
				$this->request->data[$modelName]['parent_id'] = $this->request->params['named']['parent_record_id'];
			}

			$this->request->data['Approval'][$modelName]['publish'] = $this->request->data['Approval'][$modelName]['publish'];

			// if($this->request->data['Approval'][$modelName]['prepared_by'])$this->request->data[$modelName]['prepared_by'] = $this->request->data['Approval'][$modelName]['prepared_by'];
			if($this->action == 'add'){
				$this->request->data[$modelName]['prepared_by'] = $this->Session->read('User.employee_id');
			}

			if($this->action == 'edit'){
				// get exisiting record 
				$existingRecord = $this->$modelName->find('first',array('conditions'=>array($modelName.'.id'=>$this->request->data[$modelName]['id']),'recursive'=>-1));
				$this->request->data[$modelName]['prepared_by'] = $existingRecord[$modelName]['prepared_by'];
			}

			if($this->request->data['Approval'][$modelName]['publish'] == 1){
				$this->request->data[$modelName]['approved_by'] = $this->request->data['Approval'][$modelName]['approved_by'];
				$this->request->data[$modelName]['publish'] = 1;
			}


			if ($this->$modelName->save($this->request->data,false)) {
				$new_record_id = $this->$modelName->id;
				
				$this->_update_belongTos($this->request->data);
				
				$this->loadModel('File');
				$file = $this->File->find('first',array('conditions'=>array('File.id'=>$this->request->data[$modelName]['file_id'])));

				if($file){
					$file['File']['record_id'] = $new_record_id;
					$file['File']['file_status'] = 0;
					$file['File']['data_received'] = 'added from _add';
					$file['File']['last_modified'] = date('Y-m-d H:i:s');

					$this->File->create();
					try{
						$this->File->save($file); 
					}catch (Exception $e){

					}	
				}

				// add/update additional files
				if($additionalFiles){
					if(count($additionalFiles)){
						foreach($additionalFiles as $additionalFile){
							if($additionalFile != '' && strlen($additionalFiles[0]) == 36){
								$file = $this->File->find('first',array('conditions'=>array('File.id'=>$additionalFile)));

								if($file && $file['File']['record_id'] == 'tmp'){
									$file['File']['record_id'] = $new_record_id;
									$file['File']['file_status'] = 0;
									$file['File']['data_received'] = 'added from _add';
									$file['File']['last_modified'] = date('Y-m-d H:i:s');

									$this->File->create();
									try{
										$this->File->save($file); 
									}catch (Exception $e){

									}	
								}
							}
						}
					}	
				}
				if($this->request->data['Files'])$this->_upload_custom_files($this->request->data['Files'],$this->$modelName->id);

				// get hasMany

				$hasManies = $this->$modelName->hasMany;
				foreach($hasManies as $model => $fields){

					if($this->request->data[$model]){
						$this->loadModel($model);

						unset($this->request->data[$model]['count']);
						unset($this->request->data[$model]['file_id']);
						unset($this->request->data[$model]['file_key']);
						
						foreach($this->request->data[$model] as $cdata_old){
							foreach($cdata_old as $key => $value){
								if(is_array($value)){
									$cdata[$key] = json_encode($value);
								}else{
									$cdata[$key] = $value;
								}	
							}
							$this->$model->create();
							$cdata['parent_id'] = $this->$modelName->id;
							$cdata['created'] = date('Y-m-d H:i:s');
							$cdata['modified'] = date('Y-m-d H:i:s');
							$cdata['prepared_by'] = $this->Session->read('User.employee_id');
							$cdata['created_by'] = $this->Session->read('User.id');
							$cdata['branchid'] = $this->Session->read('User.branch_id');
							$cdata['departmentid'] = $this->Session->read('User.department_id');
							$cdata['qc_document_id'] = $this->request->data[$modelName]['qc_document_id'];

							try{
								$this->$model->save($cdata,false);
							}catch(Exception $e) {
						 		// do nothing; 
							}
						}
					}
				}
				
				
				if ($this->_show_approvals()) $this->_save_approvals($this->$modelName->id);
				$this->Session->setFlash(__('Record has been saved'));
				try{
				// trigger email here
					$this->_trigger_email($existingRecord); 
				}catch(Exception $e){
					$this->Session->setFlash(__('Email Trigger failed.')); 
				}


			 	// if parent record id is available, redirect to parent record view page

				$this->loadModel('QcDocument');
				$this->loadModel('CustomTable');
				if(isset($this->request->params['named']['parent_record_id']) && $this->request->params['named']['parent_record_id']){ 
					$this_document = $this->QcDocument->find('first',array(
						'fields'=>array(
							'QcDocument.id',
							'QcDocument.title',
							'QcDocument.parent_document_id',
						),
						'recursive'=>-1, 
						'conditions'=>array('QcDocument.id'=>$this->request->params['named']['qc_document_id']))); 


					if($this_document){
						$parent_document = $this->QcDocument->find('first',array(
						// 'fields'=>array(
						// 'QcDocument.id',
						// 'QcDocument.title',
						// 'QcDocument.parent_document_id',
						// ),
					// 'recursive'=>-1, 
							'conditions'=>array('QcDocument.id'=>$this_document['QcDocument']['parent_document_id']))); 
					}



					if($parent_document){
						foreach($parent_document['CustomTable'] as $cTable){

							$this->CustomTable->virtualFields = array(
								'rec'=>'select count(*) from `'.$cTable['table_name'].'` where id LIKE "'.$this->request->params['named']['parent_record_id'].'" '
							);

							$parent_rec = $this->CustomTable->find('first',array(
							// 'fields'=>array('CustomTable.id','CustomTable.qc_document_id','CustomTable.rec'), 
								'conditions'=>array('CustomTable.qc_document_id'=>$parent_document['QcDocument']['id'],'CustomTable.rec >'=>0),
								'recursive'=>-1));


							if($parent_rec){
								$this->redirect(array(
									'controller'=>$parent_rec['CustomTable']['table_name'], 
									'action' => 'view',
									$this->request->params['named']['parent_record_id'],
									'custom_table_id'=>$parent_rec['CustomTable']['custom_table_id'],
									'qc_document_id'=>$parent_document['QcDocument']['id'],
									'process_id'=>$this->request->params['named']['process_id']));
							}
						}
					}
				}


				$this->redirect(array('action' => 'index','custom_table_id'=>$this->request->params['named']['custom_table_id'],'qc_document_id'=>$this->request->params['named']['qc_document_id'],'process_id'=>$this->request->params['named']['process_id']));
			} else {
				$this->Session->setFlash(__('Record could not be saved. Please, try again.'));
			}
		}

		public function reloaddocument_copy(){
			$additionalFiles = json_decode(base64_decode($this->request->params['pass'][3]),true);
			if($additionalFiles){
				if(count($additionalFiles)){
					$this->loadModel('File');
					foreach($additionalFiles as $additionalFile){
						if($additionalFile != '' && strlen($additionalFiles[0]) == 36){
							$file = $this->File->find('first',array( 'recursive'=>-1, 'conditions'=>array('File.id'=> $additionalFile)));
							if($file){
								$this->set('fileEdit',$file);
								$this->set('is_qc',false);
								$this->render('/Elements/load_extra_document');
							}
						}
					}
				}else{
					
				}
			}else{
				
			}			
		}

		public function reloadfile($file_id = null){
			if($file_id){
				$this->loadModel('File');
				$file = $this->File->find('first',array('conditions'=>array('File.record_id'=>$file_id)));
				$this->set('fileEdit',$file);
				$this->set('is_qc',false);	      
			}
			$this->render('/Elements/load_extra_document');
		}

		public function reloadrecordfile($file_id = null){
			if($file_id){
				$this->loadModel('File');
				$file = $this->File->find('first',array('conditions'=>array('File.id'=>$file_id)));
				$this->set('fileEdit',$file);
				$this->set('is_qc',false);	 
				$this->set('controller',$file['File']['controller']);
			}
			$this->render('/Elements/load_extra_document');
		}

		public function _trigger_email($existingRecord = null){
			$modelName = $this->modelClass;
			unset($existingRecord[$modelName]['sr_no']);
			unset($existingRecord[$modelName]['created']);
			unset($existingRecord[$modelName]['modified']);
			unset($existingRecord[$modelName]['company_id']);
			unset($existingRecord[$modelName]['branchid']);
			unset($existingRecord[$modelName]['departmentid']);
			unset($existingRecord[$modelName]['status_user_id']);
			unset($existingRecord[$modelName]['record_status']);
			unset($existingRecord[$modelName]['file_key']);
			unset($existingRecord[$modelName]['file_id']);

			$this->loadModel('CustomTrigger');
			if($this->action == 'add')$action = 0;
			if($this->action == 'edit')$action = 1;

		// check for field changes
		// this idealy only should execute for edit
			if($this->action == 'edit'){
				$triggers = $this->CustomTrigger->find('all',array('recursive'=>-1, 'conditions'=>array(
					'CustomTrigger.custom_table_id'=>$this->request->params['named']['custom_table_id'],
					'CustomTrigger.field_name !='=> -1,
				)
			));
				if($triggers){
				// check for each field change and if there is a change, trigger email as per conditions
					foreach($triggers as $trigger){
						if(
							$this->request->data[$modelName][$trigger['CustomTrigger']['field_name']] != $existingRecord[$modelName][$trigger['CustomTrigger']['field_name']] && 
							$trigger['CustomTrigger']['changed_field_value'] == $this->request->data[$modelName][$trigger['CustomTrigger']['field_name']]
						){
							$tos = $this->_get_tos($trigger);
							if($tos){
								$subject = $trigger['CustomTrigger']['name'];
								$message = $trigger['CustomTrigger']['message'];
								$this->_send_trigger_email($tos,$subject,$message);
							}
						}
					}
				}
			} 

			$subject = $message = '';
			$trigger = array();
		// action based trigger 
			$trigger = $this->CustomTrigger->find('first',array('recursive'=>-1, 'conditions'=>array(
				'CustomTrigger.custom_table_id'=>$this->request->params['named']['custom_table_id'],
				'CustomTrigger.action'=>$action,
			)
		)); 

			if($trigger){
				$tos = $this->_get_tos($trigger);
				if($tos){
					$subject = $trigger['CustomTrigger']['name'];
					$message = $trigger['CustomTrigger']['message'];
					$this->_send_trigger_email($tos,$subject,$message);
				}
			}
		} 

		public function _get_tos($trigger = null){
			$this->loadModel('Employee');
			$modelName = $this->modelClass;
			foreach($this->$modelName->belongsTo as $belongs){
				if($belongs['className'] == 'Department')$department_field = $belongs['foreignKey'];
			}
			if($trigger['CustomTrigger']['notify_user'] != -1){
			// $tos['notify_user'][] = $this->request->data[$modelName][$trigger['CustomTrigger']['notify_user']];
				$employee = $this->Employee->find('first',array('recursive'=>-1,'fields'=>array('Employee.id','Employee.office_email'),
					'conditions'=>array('Employee.id'=>$this->request->data[$modelName][$trigger['CustomTrigger']['notify_user']])));

				if($employee)$tos[$employee['Employee']['office_email']] = $employee['Employee']['office_email'];
			}

			if($trigger['CustomTrigger']['notify_admins'] == true){
				$this->Employee->User->virtualFields = array(
					'office_email'=>'select `employees`.`office_email` from `employees` where `employees`.`id` LIKE User.employee_id'
				);
				$users = $this->Employee->User->find('all',array(
					'conditions'=>array('User.is_mr'=>1),
					'fields'=>array('User.id','User.employee_id','User.office_email'),
					'recursive'=>-1,
				));
				if($users){
					foreach($users as $user){
						$tos[$user['User']['office_email']] = $user['User']['office_email'];
					}
				}
			}


			$employee = array(); 
			if($trigger['CustomTrigger']['notify_users']){
				$notify_users = json_decode($trigger['CustomTrigger']['notify_users'],true); 
				if($notify_users){
					$employees = $this->Employee->find('list',array('fields'=>array('Employee.id','Employee.office_email'), 'conditions'=>array('Employee.is_hod'=>1, 'Employee.id'=>$notify_users)));
					foreach($employees as $employee){
						$tos[$employee['Employee']['office_email']] = $employee['Employee']['office_email'];
					}
				}
			}

			$employee = array();
			if($trigger['CustomTrigger']['notify_hods'] == true && $department_field){
			// get HoDs for these departments 
			// get added daprtments
				$departments = json_decode($this->request->data[$modelName][$department_field],true);
				if($departments){
					foreach($departments as $department){
						$hod = $this->Employee->find('first',array('fields'=>array('Employee.id','Employee.office_email'), 'conditions'=>array('Employee.is_hod'=>1, 'Employee.department_id'=>$department)));
						if($hod){
							$tos[$hod['Employee']['office_email']] = $hod['Employee']['office_email'];
						}
					}
				} 
			}

			if($trigger['CustomTrigger']['hod_departments']){
			// get HoDs for these departments 
				$notify_hods = $this->Employee->find('list',array('fields'=>array('Employee.id','Employee.office_email'), 'conditions'=>array('Employee.is_hod'=>1, 'Employee.department_id'=>json_decode($trigger['CustomTrigger']['hod_departments'],true))));
				foreach($notify_hods as $employee){
					$tos[$employee['Employee']['office_email']] = $employee['Employee']['office_email'];
				} 
			}

			return $tos;
		}

		public function _send_trigger_email($tos = null,$subject = null, $message = null){
			try{
				App::uses('CakeEmail', 'Network/Email');
				$EmailConfig = new CakeEmail("fast");
				$EmailConfig->to($tos);
			// $EmailConfig->to('mayureshvaidya@gmail.com');
				$EmailConfig->subject($subject);
				$EmailConfig->template('emailTrigger'); 
				$EmailConfig->viewVars(array(
					'record' => $this->request->data[$this->modelClass]['name'].''.$this->request->data[$this->modelClass]['title'],
					'employee' => $this->Session->read('User.name'),
					'date_time' => date('Y-m-d h:i:s'),
					'h2tag'=>' Email from Email Triggers',
					'msg_content'=>$message));
				$EmailConfig->emailFormat('html');
				$EmailConfig->send(); 

			} catch(Exception $e) {

			}

		}

		public function _fetch_file($id = null){
			$document = $this->_qc_document_header($this->request->params['named']['qc_document_id']);	
			if($this->action == 'edit'){
				if(!empty($customTable['CustomTable']['qc_document_id']) && !isset($this->request->params['named']['qc_document_id'])){
					$this->Session->setFlash(__('Select form first'));
					$this->redirect(array('controller'=>'custom_tables', 'action' => 'index'));
				}else{
					$document = $this->_qc_document_header($this->request->params['named']['qc_document_id']);					
				}

				if(!empty($customTable['CustomTable']['process_id']) && !isset($this->request->params['named']['process_id'])){ 
					$this->Session->setFlash(__('Select form first'));
					$this->redirect(array('controller'=>'custom_tables', 'action' => 'index'));
				}else{ 
					$process = $this->_process_header($this->request->params['named']['process_id']);
				} 
			}

			$customTable = $this->request->params['named']['custom_table_id'];
			$modelName = $this->modelClass;
			$customTable = $this->$modelName->CustomTable->find('first',array('conditions'=>array('CustomTable.id'=>$this->request->params['named']['custom_table_id']),'recursive'=>-1));
			$this->set('customTable',$customTable);
			// 0 = document
			// 1 = data
			// 2 = both
			if($document['QcDocument']['data_type'] && $document['QcDocument']['data_type'] == 1){
				// load table file				
				$file_type = $document['QcDocument']['file_type'];
				$file_name = $file_name_without_ext = $this->_clean_table_names($document['QcDocument']['title']);
				$document_number = $document['QcDocument']['document_number'];
				$document_version = $document['QcDocument']['revision_number'];
				$file_name = $document_number.'-'.$file_name.'-'.$document_version;
				$file_name = $this->_clean_table_names($file_name);
				$file_name = $file_name .'.'.$file_type;

				$file_name_without_ext = $document_number.'-'.$file_name_without_ext.'-'.$document_version;
				$file_name_without_ext = $this->_clean_table_names($file_name_without_ext);

				$file['File']['id'] = $customTable['CustomTable']['id'];
				$file['File']['name'] = $file_name_without_ext;
				$file['File']['file_type'] = $file_type;
				$file['File']['file_key'] = $this->_generate_onlyoffice_key($this->request->params['named']['custom_table_id'].date('Ymdhis'));
				$file['File']['qc_document_id'] = $document['QcDocument']['id'];
				$file['File']['process_id'] = $process['Process']['id'];
				$file['File']['custom_table_id'] = $this->request->params['named']['custom_table_id'];
				$file['File']['model'] = $this->modelClass;
				$file['File']['controller'] = $this->request->controller;
				$file['File']['prepared_by'] = $file['File']['modified_by'] = $this->Session->read('User.employee_id');
				$file['File']['created'] = date('Y-m-d h:i:s');
				$file['File']['created_by'] = $file['File']['user_id'] = $this->Session->read('User.id');
				if($id)$file['File']['record_id'] = $id;
				else $file['File']['record_id'] = 'tmp';
				$file['File']['file_status'] = 0;
				$file['File']['data_received'] = 'added from prepare_update';	
				return $file;
			}else{					
				try{
					$this->loadModel('File');
					$file = $this->File->find('first',array('recursive'=>-1, 'conditions'=>array('File.record_id'=>$id,'File.controller'=>$this->request->controller)));
					if($file){
						$file['File']['pre_file_key'] = $file['File']['file_key'];
						$file['File']['file_key'] = $this->_generate_onlyoffice_key($file['File']['id'].date('Ymdhis'));
						$file['File']['file_status'] = 0;
						$file['File']['data_received'] = 'added from fetch_file: mostly edit';
						// $file['File']['version_keys'] = $file['File']['version_keys'].','.$file['File']['file_key'];
						
						$this->File->create();
						$this->File->save($file,false);
						return $file; 
					}else{
						return false;
					}
				}catch(Exception $e){
					return false;
				}
			}		
		}

		public function _clear_cake_cache(){
			Cache::clear();
			clearCache();
			exec('rm '. APP . 'tmp' . DS . 'cache' . DS . 'persistent/*');
			exec('rm '. APP . 'tmp' . DS . 'cache' . DS . 'models/*');
			exec('rm '. APP . 'tmp' . DS . 'cache' . DS . 'views/*');
		}

		public function add_date_new($date = null){
			$this->autoRender = false;
			$default_date_type = array('days','weeks','months','years');
			$fromDate = date('Y-m-d',strtotime($this->request->data['fromDate']));
			$newdate = date('Y-m-d',strtotime('+'.$this->request->data['linkedTos']['default_date_number'].' '.$default_date_type[$this->request->data['linkedTos']['default_date_type']].' ',strtotime($fromDate)));
			return $newdate;
		}

		public function _check_lock($id = null){
			if($this->action == 'edit' || $this->action == 'view'){
				$this->loadModel('RecordLock');
				$rec = $this->RecordLock->find('first',array( 
					'conditions'=>array('RecordLock.lock_table_id'=>$this->modelClass)));	

				
				if($rec){
					$table = Inflector::classify($rec['Table']['table_name']); 
					foreach(json_decode($rec['Table']['fields'],true) as $field){
						if($field['linked_to'] != -1 && Inflector::classify($field['linked_to']) == $this->modelClass){
							$chk_field = $field['field_name'];
						}else{
							$chk_field = 'id';
						}
					}

					try{$this->loadModel($table);}catch (Exception $e){}

					try{
						$condition = array($table.'.'.$rec['RecordLock']['table_field'] => $rec['RecordLock']['csvoption'],
							$table.'.'.$chk_field => $this->request->params['pass'][0]);
						$record = $this->$table->find('count',array(
							'conditions'=>$condition));
					}catch (Exception $e){} 
					
					if($record > 0){
						if($this->action == 'edit'){ 
							$this->redirect(array('action' => 'view',$this->request->params['pass'][0])); 
						}else{
							$this->set('lock_message',$rec['RecordLock']['message']);
						}

					}

				}
			} 
		}

		public function load_document_version(){
			$this->set('thisKey',$this->request->data['key']);
			$this->render('/Elements/load_document_version');
		}

		public function generate_pdf(){
			if ($this->request->is('post') || $this->request->is('put')) {

			}else{
				$this->render('/Elements/sign_pdf');
			}
		}

		public function onlyofficechk(){
			$this->autoRender = false;
			$this->loadModel('UserSession');
			$session = $this->UserSession->find('first',array('recursive'=>-1,'fields'=>array('UserSession.id','UserSession.user_id','UserSession.start_time','UserSession.end_time'), 'conditions' => array(
				'UserSession.user_id'=>$this->data['user_id'],
				'UserSession.id'=>$this->data['user_session_id'])));
			if($session){
				return json_encode($session);
			}else{
				return json_encode($session);
			}
			exit;
		} 

		public function check_document(){
			$this->layout = 'ajax';
			$is_qc = false;
			$field = str_replace('data['.$this->modelClass.'][','', $this->request->params['pass'][1]);
			$field = str_replace(']','',$field);

			if($this->request->params['named']['custom_table_id']){
				$this->loadModel('CustomTable');
				$customTable = $this->CustomTable->find('first',array('conditions'=>array('CustomTable.id'=>$this->request->params['named']['custom_table_id']),'recursive'=>-1,'fields'=>array('CustomTable.id','CustomTable.fields')));
				
				$fields = json_decode($customTable['CustomTable']['fields'],true);
				foreach($fields as $fs){
					
					if($fs['field_name'] == $field){
						$showdocs = $fs['showdocs'];
						$showdocs_mode = $fs['showdocs_mode'];
						$showdocs_copy = $fs['showdocs_copy'];						
						$this->set('fs',$fs);

						// try and get the record details 
						$recordModel = Inflector::Classify($fs['linked_to']);
						$this->loadModel($recordModel);
						
						$thisRecord  = $this->$recordModel->find('first',array('conditions'=>array($recordModel.'.id'=>$this->request->params['pass'][0]),'recursive'=>-1));
						
						if($thisRecord){							
							$this->set('thisRecord',$thisRecord);
							$this->set('thisModel',$recordModel);
						}

					}
				}
			}

			$modelName = $this->modelClass;

			// check for additional files
			$record = $this->$modelName->find('first',array(
				'conditions'=>array($modelName.'.id'=>$this->request->params['named']['record_id']),
				'recursive'=>-1,
				'fields'=>array($modelName.'.id',$modelName.'.additional_files')));

			$additionalFiles = json_decode($record[$modelName]['additional_files'],true);
			$belongs = $this->$modelName->belongsTo;
			$this->loadModel('File');
			$ref = trim(str_replace(Router::url('/', true) .$this->request->controller,'-', $this->referer()));
			$ref = explode('/',$ref);

			if($ref[1] == 'add')$action = 'add';
			if($ref[1] == 'edit')$action = 'edit';
			
			foreach($belongs as $table => $data ){

				if($data['foreignKey'] == $field){
					$newModel = $data['className'];
					$this->loadModel($newModel);
					
					if($newModel != 'QcDocument'){	
						if($this->request->params['named']['showdocs'] == 1){
							$fileloaded = false;
							if(
								($this->request->params['named']['showdocs_mode'] == 0) || 
								($this->request->params['named']['showdocs_mode'] == 0 && $this->request->params['named']['showdocs_copy'] == 1)){
								//just load original file in view mode
								$file = $this->File->find('first',array('recursive'=>-1, 'conditions'=>array('File.model'=>$newModel,'File.record_id'=>$this->request->params['pass'][0])));								
								$this->set('is_qc',false);
								$this->set('fileEdit',$file);
								$fileloaded = true;
							}
							if($this->request->params['named']['showdocs_mode'] == 1 && $this->request->params['named']['showdocs_copy'] == 0){
								// load original file in edit mode
								$file = $this->File->find('first',array('recursive'=>-1, 'conditions'=>array('File.model'=>$newModel,'File.record_id'=>$this->request->params['pass'][0])));								
								$this->set('is_qc',false);
								$this->set('fileEdit',$file);
								$fileloaded = true;
							}

							if($fileloaded == false){
								if($action == 'add'){								
									if($this->request->params['named']['showdocs_mode'] == 1 && $this->request->params['named']['showdocs_copy'] == 1){	
										//just load original file in view mode
										$file = $this->File->find('first',array('recursive'=>-1, 'conditions'=>array('File.model'=>$newModel,'File.record_id'=>$this->request->params['pass'][0])));
										
										if($file){
											$newFile = $file;
											unset($newFile['File']['id']);
											unset($newFile['File']['sr_no']);
											$newFile['File']['model'] = $modelName;
											$newFile['File']['controller'] = $this->request->controller;
											$newFile['File']['created_by'] = $newFile['File']['modified_by'] = $this->Session->read('User.id');
											$newFile['File']['prepared_by'] = $newFile['File']['approved_by'] = $this->Session->read('User.employee_id');
											$newFile['File']['file_key'] = $this->_generate_onlyoffice_key($file['File']['id'].date('Ymdhis'));
											$newFile['File']['version_keys'] = NULL;
											$newFile['File']['data_received'] = 'additional files';
											// delete any existing files by this user which are tmp+additional files
											$filesToDelete = $this->File->find('all',array('conditions'=>array(
												'File.data_received'=>'additional files',									
												'File.created_by'=>$this->Session->read('User.id')
											),'recursive'=>-1));

											$path = Configure::read('files') . DS . 'files';
											foreach($filesToDelete as $fileToDelete){
												$toDelete = new Folder($path . DS . $fileToDelete['File']['id']);
												$toDelete->delete();									
												$this->File->delete($fileToDelete['File']['id']);
											}

											// now add new file
											
											$this->File->create();									
											if($this->File->save($newFile,false)){	
												$newFile['File']['id'] = $this->File->id;										
												$doc  = $path . DS  . $file['File']['id'] . DS . $file['File']['name'].'.'.$file['File']['file_type'];
												
												$docsave = $path . DS . $newFile['File']['id'] . DS . $file['File']['name'].'.'.$file['File']['file_type'];
												if(!file_exists($docsave)){
													$newFolder = new Folder();
													$newFolder->create(WWW_ROOT . DS . 'files' . DS . $this->Session->read('User.company_id') . DS . 'files' . DS . $this->File->id,0777);
													copy($doc,$docsave);	
												}
												
											}									
											$this->set('is_qc',false);
											$this->set('fileEdit',$newFile);
										}
									}
								}else{								
									foreach($additionalFiles as $additionalFile){
										$file = $this->File->find('first',array('conditions'=>array('File.id'=>$additionalFile),'recursive'=>-1));									
										$this->set('is_qc',false);
										$this->set('fileEdit',$file);
									}
								}
							}							
						}
						// for qc documents
					}else{									
						$is_qc = true;
						$this->loadModel('QcDocument');
						$qcfile = $this->QcDocument->find('first',array('recursive'=>-1,'conditions'=>array('QcDocument.id'=>$this->request->params['pass'][0])));
						
						// check if file already exists
						if($qcfile && $showdocs_copy != 0){
							if($showdocs_copy == 1){								
								// $this->loadModel('File');
								$file = $this->File->find('first',array('conditions'=>array(
									// 'File.record_id != '=>'',
									// 'OR'=>array(
									'File.record_id'=>$this->request->params['named']['record_id'],
										// 'File.record_id'=>'tmp',
									// ),
									
									'File.qc_document_id'=>$qcfile['QcDocument']['id']
								),'recursive'=>-1,'order'=>array('File.sr_no'=>'ASC')));
								// add new file to files table
								// copy qc file to new files id folder								
								if($file){									
									
									$file['File']['modified_by'] = $this->Session->read('User.employee_id');
									$file['File']['modified'] = date('Y-m-d h:i:s');
									$file['File']['file_status'] = 0;
									$file['File']['data_received'] = 'Already found '. $this->request->params['named']['record_id'];	
									
									$this->File->create();
									if($this->File->save($file,false)){
									}									
								}else{									
									$file_type = $qcfile['QcDocument']['file_type'];
									$file_name = $file_name_without_ext = $this->_clean_table_names($qcfile['QcDocument']['title']);
									$document_number = $qcfile['QcDocument']['document_number'];
									$document_version = $qcfile['QcDocument']['revision_number'];
									$file_name = $document_number.'-'.$file_name.'-'.$document_version;
									$file_name = $this->_clean_table_names($file_name);
									// $file_name = $file_name .'.'.$file_type;

									$file_name_without_ext = $document_number.'-'.$file_name_without_ext.'-'.$document_version;
									$file_name_without_ext = $this->_clean_table_names($file_name_without_ext);

									// $file['File']['id'] = $customTable['CustomTable']['id'];
									$file['File']['name'] = $file_name;
									$file['File']['file_type'] = $qcfile['QcDocument']['file_type'];
									// $file['File']['file_key'] = $qcfile['QcDocument']['file_key'];
									$file['File']['file_key'] = $this->_generate_onlyoffice_key($qcfile['QcDocument']['file_type'].date('Ymdhis'));
									$file['File']['qc_document_id'] = $qcfile['QcDocument']['id'];
									$file['File']['process_id'] = $process['Process']['id'];
									$file['File']['custom_table_id'] = $this->request->params['named']['custom_table_id'];
									$file['File']['model'] = 'QcDocument';
									$file['File']['controller'] = 'qc_documents';
									$file['File']['prepared_by'] = $file['File']['modified_by'] = $this->Session->read('User.employee_id');
									$file['File']['created'] = date('Y-m-d h:i:s');
									$file['File']['created_by'] = $file['File']['user_id'] = $this->Session->read('User.id');
									$file['File']['record_id'] = $this->request->params['named']['record_id'];
									$file['File']['file_status'] = 0;
									$file['File']['data_received'] = 'added from copy file 2';	

									$this->File->create();
									if($this->File->save($file,false)){											
										$doc  = WWW_ROOT . DS . 'files' . DS . $this->Session->read('User.company_id') . DS . 'qc_documents' . DS . $qcfile['QcDocument']['id'] . DS . $file['File']['name'].'.'.$file['File']['file_type'];

										$docsave = WWW_ROOT . DS . 'files' . DS . $this->Session->read('User.company_id') . DS . 'files' . DS . $this->File->id . DS . $file['File']['name'].'.'.$file['File']['file_type'];

										if(!file_exists($docsave)){
											$newFolder = new Folder();
											$newFolder->create(WWW_ROOT . DS . 'files' . DS . $this->Session->read('User.company_id') . DS . 'files' . DS . $this->File->id,0777);
											copy($doc,$docsave);	
										}

									}
									$file['File']['id'] = $this->File->id;
								}
								$this->set('is_qc',false);
								$this->set('fileEdit',$file);

							}else{	

								if($qcfile){									
									$filepath = WWW_ROOT . DS . 'files' . DS . $this->Session->read('User.company_id') . DS . 'qc_documents' . DS . $file['File']['id'];		
									$filename = $filepath . DS . $file['File']['name'].'.'.$file['File']['file_type'];
									
									if(file_exists($filename)){
										$this->set('is_qc',$is_qc);
										$this->set('fileEdit',$qcfile);
									}else{								
										// return false;
									}
								}
							}
						}

					}
				}
			}
			
			$this->view = '/Elements/load_extra_document';
			return $this->viewVars;
		}


		public function delete_document(){
			if($this->request->controller != 'qc_documents'){
				$this->view = '/Elements/delete_document';
				if ($this->request->is('post')) {
					$this->loadModel('User');
					$user = $this->User->find('first', array('conditions' => array('User.status' => 1, 'User.soft_delete' => 0, 'User.publish' => 1, 'User.username' => $this->Session->read('User.username'))));
					if ($user) {
						if (trim($user['User']['password']) != trim(Security::hash($this->request->data[Inflector::classify($this->request->controller)]['password'], 'md5', true))) {
							$this->Session->setFlash(__('Incorrect password', true), 'default', array('class' => 'alert-danger'));                    
						} else {
							$getid = explode('/',base64_decode($this->request->data[Inflector::classify($this->request->controller)]['url']));
							$id = $getid[count($getid)-2];
							$name = $getid[count($getid)-1];
							
							$this->loadModel('File');
							$this->File->delete($id,false);
							unlink(Configure::read("files") . DS . 'files' . DS . $id . DS . $name);							
							$this->Session->setFlash(__('File Deleted', true), 'default', array('class' => 'alert-success'));
							$this->redirect($this->request->data[Inflector::classify($this->request->controller)]['ref']);

						}
					}
				} else {            
					$this->set('ref', $this->referer());
				}		
			}
		}

		public function _returnDetaultField($allFields = null){

			foreach($allFields as $field){
				if($field['linked_to'] != -1){				
					$model = Inflector::classify($field['linked_to']);
					try{
						$this->loadModel($model);
						$linkedTosWithDisplay[Inflector::classify($field['field_name'])] = $this->$model->displayField;					
					}catch (Exception $e){

					}
					
				}
			}
			return $linkedTosWithDisplay;		
		}

		public function checkunique($value = null,$field_name = null){
			$this->autoRender = false;
			$model = $this->modelClass; 
			$rec = $this->$model->find('count',array('conditions'=>array($model.'.'.$field_name => base64_decode($value))));
			if($rec == 0){
				return false;
			}else{
				return true;
			}

		}

		public function code_input_main($id = null){
			$table = $this->CustomTable->find('first',array('fields'=>array('CustomTable.id','CustomTable.add_form_script','CustomTable.edit_form_script','CustomTable.table_name'),'recursive'=>-1, 'conditions'=>array('CustomTable.id'=>$id)));
			if($table){
				$this->set('table',$table);
			}
			$this->view = '/Elements/code_input_main';		

		}

		public function return_value_for_dropdown($model = null, $field = null,$id = null){
			$this->autoRender = false;
			try{
				$model = Inflector::classify($model);
				$this->loadModel($model);
				$rec = $this->$model->find('first',array('conditions'=>array($model.'.id' => $id),'fields'=>array($model.'.id',$model.'.'.$field)));
				if($rec){
					return $rec[$model][$field];
				}else{
					return false;
				}	
			}catch (Exception $e){
				return false;
			}			
			exit;
		}

		public function return_options_for_dropdown($parent = null, $child = null,$id = null){
			try{
				$parent = Inflector::Classify($parent);
				$child = Inflector::Classify($child);
				
				$this->autoRender = false;
				$this->loadModel($child);
				$belongs = $this->$child->belongsTo;
				
				if(in_array($parent,array_keys($belongs))){
					$key = $belongs[$parent]['foreignKey'];
				}
				

				if($key){
					$rec = $this->$child->find('list',array('conditions'=>array($child.'.'.$key => $id)));
					
					if($rec){
						$con_str.= '<option value=-1>Select</option>';
						foreach ($rec as $key => $value) {
							$con_str.= '<option value=' . $key . '>' . $value . '</option>';
						}
						return $con_str;
					}else{
						return false;
					}	
				}else{
					return false;
				}
			}catch (Exception $e){
				return false;
			}			
			exit;
		}

		public function fetch_record($model = null, $key = null, $field = null, $order = null, $id = null){
			$this->autoRender = false;
			$parent = Inflector::Classify($model);
			try{
				$this->loadModel($model);
				if($order == 'first')$order = ' DESC';
				if($order == 'last')$order = ' ASC';
				$rec = $this->$model->find('first',array('conditions'=>array($model.'.'.$key => $id),'recursive'=>-1));
				
				if($rec){
					return $rec[$model][$field];
				}else{
					return false;
				}
			}catch (Exception $e){
				return false;
			}			
			exit;
		}

		public function field_fetch($model = null, $fieldTobeChanged = null){

			$selectedModelName = $model;
			$currModel = $this->modelClass;
			
			try{
				$rec = $this->$currModel->$selectedModelName->find('first',array(
					'conditions'=>array($model.'.id'=>$this->request->params['named']['id']),
					'fields'=>array($model.'.id',$model.'.'.$this->request->params['pass'][1]),
					'recursive'=>-1));	

				if($rec){
					$this->set('record_value',$rec[$model][$this->request->params['pass'][1]]);	
				}
			}catch(Exception $e){

			}
			

			

			$this->set('selectedModelName',$selectedModelName);
			$thisModel = $this->modelClass;
			
			$selectedModel = $this->$thisModel->belongsTo[$model];						

			$model = $selectedModel['className'];
			$this->loadModel($model);
			
			$fields = $this->$model->schema();
			$fieldOnForm = $selectedModel['foreignKey'];
			

			$fieldDetails = $fields[$fieldTobeChanged];
			

			$tableName = $this->$model->useTable;
			
			// check if model exists in custome table

			$this->loadModel('CustomTable');
			$table = Inflector::underscore($selectedModel['className']);
			$this->loadModel('CustomTable');
			$customTable = $this->CustomTable->find('first',array('recursive'=>-1, 'fields'=>array('CustomTable.id','CustomTable.name','CustomTable.fields'), 'conditions'=>array('CustomTable.table_name LIKE '=> $tableName)));
			
			if($customTable){

				$customTableFields = json_decode($customTable['CustomTable']['fields'],true);
				if($fieldDetails['type'] == 'boolean'){
					// if field is publish
					if($fieldTobeChanged == 'publish'){
						$this->set('type','checkbox');					
					}else{
						
					}							
					// try and get values from model
				}

				if($fieldDetails['type'] == 'integer' && $fieldDetails['length'] == null ){
					if($customTable){
						foreach($customTableFields as $customTableField){
							if($customTableField['field_name'] == $fieldTobeChanged){								
								$values = explode(',',$customTableField['csvoptions']);
								$this->set('values',$values);
							}
						}
					}
				}

				if($fieldDetails['type'] == 'string' && $fieldDetails['length'] == 36 ){

					foreach($customTableFields as $customTableField){					
						if($customTableField['field_name'] == $fieldTobeChanged){
							$newModel = Inflector::classify($customTableField['linked_to']);						
							$this->loadModel($newModel);
							$options = $this->$newModel->find('list');
							$this->set('values',$options);
						}else{

						}
					}				
				}				
			}else{

				$field_details = json_decode(base64_decode($this->request->params['named']['field_details']),true);
				$findModel = base64_decode($field_details['field_label']);
				$newModel = $this->$thisModel->belongsTo[$findModel]['className'];
				$this->loadModel($newModel);
				
				$fieldSchema = $this->$newModel->schema();
				$field_details = $fieldSchema[$fieldTobeChanged];

				if($fieldDetails['type'] == 'boolean'){					
					// if field is publish
					if($fieldTobeChanged == 'publish'){
						$this->set('type','checkbox');					
					}else{
						
					}
					// try and get values from model
				}

				if($field_details['type'] == 'integer' && ($field_details['length'] == null || $field_details['length'] == 1)){
					//check in $customArray in Model
					$fieldDetails['length'] = 1;
					$customArray = $this->$newModel->customArray;
					if($customArray[Inflector::pluralize(Inflector::variable($fieldTobeChanged))]){
						$values = $customArray[Inflector::pluralize(Inflector::variable($fieldTobeChanged))];
						$this->set('values',$values);						
					}					
				}

				if($field_details['type'] == 'string' && $field_details['length'] == 36){					
					foreach($this->$newModel->belongsTo as $m => $d){
						if($d['foreignKey'] == $fieldTobeChanged){
							$newClass = $d['className'];
							$this->loadModel($newClass);
							$options = $this->$newClass->find('list');
							$this->set('values',$options);
						}
					}

				}

			}					
			$this->set('model',$model);			
			$this->set('fieldTobeChanged',$fieldTobeChanged);
			$this->set('fieldDetails',$fieldDetails);
			$this->set('schema',$this->$model->schema());
			$this->render('/Elements/field_fetch');
		}


		public function _generate_onlyoffice_pdf($url = null,$filetype = null,$outputtype = null, $password = null, $title = null,$record_id = null,$cover = null,$attach_cover = null){

			$this->set('addwatermark',true);
			
			// if($cover != true){
			// 	$delpath =  New Folder(WWW_ROOT .'files'. DS . 'pdf' . DS .$this->Session->read('User.id') . DS . $record_id);
			// 	if($record_id){
			// 		$delpath->delete();	
			// 	}

			// }			

			$path = Configure::read('OnlyofficeConversionApi'). '/ConvertService.ashx';
			$key = $this->_generate_onlyoffice_key($record_id . date('Ymdhis'));
			$payload = array(
				'async'=>false,
				'url'=>$url,
				'outputtype'=>$outputtype,
				'filetype'=>$filetype,
				'title'=>$title,
				'key'=>$key,			
			);
			$token = $this->jwtencode(json_encode($payload));
			$arr = [
				'async'=>false,
				'url'=>$url,
				'outputtype'=>$outputtype,
				'filetype'=>$filetype,
				'title'=>$title,
				'key'=>$key,			     
			];

    	// add header token
			$headerToken = "";
			$jwtHeader = 'jvFdQcdBMMyhOCNsva9z';

			$headerToken = $this->jwtEncode([ "payload" => $arr ]);
			$arr["token"] = $this->jwtEncode($arr);	    

			$data = json_encode($arr);
			
			// request parameters	
			$opts = array('http' => array(
				'method'  => 'POST',
				'timeout' => 30,
				'header'=> "Content-type: application/json\r\n" . 
				"Accept: application/json\r\n" .
				(empty($headerToken) ? "" : $jwtHeader.": Bearer $headerToken\r\n"),
				'content' => $data
			)
		);

			$context = stream_context_create($opts);
			$response_data = file_get_contents($path, FALSE, $context);
			$downloadUri = json_decode($response_data,true);
			$downloadUri = $downloadUri['fileUrl'];


			if (file_get_contents($downloadUri) === FALSE) {
				
			} else {
				
				$savepath = WWW_ROOT .'files' . DS . 'pdf' . DS . $this->Session->read('User.id') . DS . $record_id;

				if(!file_exists($savepath)){
					$folder = new Folder();
					if ($folder->create($savepath,0777)) {
					} else {
						echo "Folder creation failed";
						exit;
					}
				}				

				if($cover == false){
					$new_data = file_get_contents($downloadUri);
					$file_for_save = WWW_ROOT .'files' . DS . 'pdf' . DS . $this->Session->read('User.id') . DS . $record_id . DS .  '-remove-pdf-'. $title . '-' . date('his') .'.'.$outputtype;
					if (file_put_contents($file_for_save, $new_data)) {				
						$this->add_password($file_for_save,null,$record_id);
					} else {

					}
				}else{
					$new_data = file_get_contents($downloadUri);

					$file_for_save = WWW_ROOT .'files' . DS . 'pdf' . DS . $this->Session->read('User.id') . DS . $record_id . DS .  'cover-pdf'.'.'.$outputtype;					
					if (file_put_contents($file_for_save, $new_data)) {				
						unlink(WWW_ROOT .'files' . DS . 'pdf' . DS . $this->Session->read('User.id') . DS . $record_id . DS . 'template.html');
					} else {

					}
				}			
			}
		}

		public function add_password($pdf = null, $password = null, $record_id = null){

			$password = $this->request->data['DocumentDownload']['password'];
			// check if cover pdf exists, if yes, attach it
			$cover = WWW_ROOT .'files' . DS . 'pdf' . DS . $this->Session->read('User.id') . DS . $record_id . DS .  'cover-pdf.pdf';
			if(file_exists($cover)){
				$input = $pdf;
				$newoutput = str_replace('-remove-pdf-', '-add-cover-', $pdf);
				$exec = Configure::read('PDFTkPath') . ' A=' .$cover .' B=' .$input.  ' cat A B output '. $newoutput .'';	
				exec($exec);

				$input = $pdf;
				$output = str_replace('-add-cover-', '', $newoutput);
				$output = str_replace($record_id, $this->request->params['named']['id'],$output);
				$sign = $this->_sign_to_pdf($this->request->data['DocumentDownload']['signature'],$record_id,$this->request->data['DocumentDownload']['font_face'],$this->request->data['DocumentDownload']['font_size']);
				
				if($password && $password != ''){
					$exec = Configure::read('PDFTkPath') . ' ' .$newoutput .' multibackground ' .$sign.  ' output '. $output . ' user_pw '.$password.'';
				}else{
					$exec = Configure::read('PDFTkPath') . ' ' .$newoutput .' multibackground ' .$sign.  ' output '. $output .'';
				}
				exec($exec);
				unlink($newoutput);
				unlink($cover);
				unlink($input);
				unlink($sign);
			}else{
				$input = $pdf;
				$output = str_replace('-remove-pdf-', '', $pdf);
				$output = str_replace($record_id, $this->request->params['named']['id'],$output);
				$sign = $this->_sign_to_pdf($this->request->data['DocumentDownload']['signature'],$record_id,$this->request->data['DocumentDownload']['font_face'],$this->request->data['DocumentDownload']['font_size']);
				
				if($password && $password != ''){
					$exec = Configure::read('PDFTkPath') . ' ' .$input .' multibackground ' .$sign.  ' output '. $output . ' user_pw '.$password.'';
				}else{
					$exec = Configure::read('PDFTkPath') . ' ' .$input .' multibackground ' .$sign.  ' output '. $output .'';
				}
				exec($exec);
				unlink($input);
				unlink($sign);
			}
			
			
			
		}

		public function _sign_to_pdf($sign = null,$record_id = null,$font_face = null, $font_size = null){	
			$CakePdf = new CakePdf(array(
				'options' => array(
					'print-media-type' => false,
					'outline' => false,
					'dpi' => 360,
					'outline'=>true,
					'outline-depth'=>2,
					'enable-local-file-access'=>true,
					'footer-font-size'     => '6',
				),
				'margin' => array(
					'bottom' => 0,
					'left' => 0,
					'right' => 0,
					'top' => 0
				),
			)
		);

			// get document details
			$this->loadModel('QcDocument');
			$qcDocument = $this->QcDocument->find('first',
				array(
					'conditions'=>array('QcDocument.id'=>$record_id),
					'fields'=>array('QcDocument.id','QcDocument.it_categories','QcDocument.document_status'),
					'recursive'=>-1));

			if($qcDocument){
				$category = $this->QcDocument->customArray['itCategories'][$qcDocument['QcDocument']['it_categories']];
				$status = $this->QcDocument->customArray['documentStatuses'][$qcDocument['QcDocument']['document_status']];
				
				$this->set('category',$category);
				$this->set('status',$status);
			}
			
			$this->set('sign',$sign);
			$CakePdf->template('sign', 'sign');
			$CakePdf->viewVars($this->viewVars);
			
			$path = WWW_ROOT .'files'. DS . 'pdf' . DS .$this->Session->read('User.id') . DS . $record_id;

			try{
				$dir = WWW_ROOT .'files'. DS . 'pdf' .DS . $this->Session->read('User.id'). DS . $record_id;
				if(!file_exists($dir)){
					mkdir($dir);    
				}

				if(!file_exists($path)){
					mkdir($path);
				}
				chmod($dir,0777);
				chmod($path,0777);
			}catch(Exception $e){            
			}

			$pagecontentfilename = 'signpdf';
			$pdf = $CakePdf->custom_write($path,$path . DS . $pagecontentfilename.'-.pdf');
			$pdf = $path . DS . $pagecontentfilename.'.pdf';
			$pagecontentfilename = $path . DS . $pagecontentfilename.'-.pdf';   
			

			if(!$qcDocument){
				$qcDocument = $this->viewVars['qcDocument'];
			}

			if($this->viewVars['addwatermark'] == true){
				$output = $path . DS . 'signpdf.pdf';   
				$background = WWW_ROOT . 'files' . DS . 'samples' . DS . $qcDocument['QcDocument']['document_status'].'.pdf';
				$exec = Configure::read('PDFTkPath') . ' ' .$pagecontentfilename .' multistamp ' .$background.  ' output '. $output .'';
				exec($exec);  
				unlink($pagecontentfilename);
				$this->set('addwatermark',false);
				return $output;	
			}else{
				$output = $path . DS . 'signpdf.pdf';   
				$background = WWW_ROOT . 'files' . DS . 'samples' . DS . $qcDocument['QcDocument']['document_status'].'.pdf';
				$exec = Configure::read('PDFTkPath') . ' ' .$pagecontentfilename .'  output '. $output .'';				
				exec($exec);  
				unlink($pagecontentfilename);
				$this->set('addwatermark',false);
				return $output;	
			}
		}

		public function load_process($custom_table_id = null){			
			$this->loadModel('CustomTableProcess');
			$processes = $this->CustomTableProcess->find('all',array(
				'fields'=>array('Process.id','Process.name','CustomTableProcess.sequence'),
				'order'=>array('CustomTableProcess.sequence'=>'ASC'), 
				'conditions'=>array('CustomTableProcess.custom_table_id'=>$custom_table_id)));			
			$this->set('processes',$processes);
			$this->render('/Elements/load_process');
		}

		public function _write_to_file($folder = null, $file = null, $content = null){
			if($content){
				chmod($folder,0777);
				$fp = fopen($file, 'w');
				fwrite($fp, $content);
				fclose($fp);
			}	        
		}

	}
