<?php
App::uses('AppController', 'Controller');
// App::uses('File', 'Utility');
App::uses('CakePdf', 'CakePdf.Pdf');
/**
 * DocumentDownloads Controller
 *
 * @property DocumentDownload $DocumentDownload
 * @property PaginatorComponent $Paginator
 */
class DocumentDownloadsController extends AppController {
	public $components = array('RequestHandler', 'Session','Paginator');
	public $helpers = array('Js', 'Session', 'Paginator');

	public function index($qc_document_id = null) {
		$this->DocumentDownload->recursive = 0;

		if(isset($this->request->params['named']['record_id'])){
			$this->paginate = array(
				'limit'=>20,
				'conditions'=>array(
					'DocumentDownload.record_id'=>$this->request->params['named']['record_id'],
					'DocumentDownload.custom_table_id'=>$this->request->params['named']['custom_table_id'],
				),
				'order'=>array('DocumentDownload.sr_no'=>'DESC'),			
			);
		}else{
			$this->paginate = array(
				'limit'=>20,
				'conditions'=>array('DocumentDownload.qc_document_id'=>$qc_document_id),
				'order'=>array('DocumentDownload.sr_no'=>'DESC'),			
			);
		}	
		$this->set('documentDownloads', $this->Paginator->paginate());
	}

/**
 * add method
 *
 * @return void
 */
public function add() {
	if ($this->request->is('post')) {
		$this->DocumentDownload->create();
		if ($this->DocumentDownload->save($this->request->data)) {
			$this->Session->setFlash(__('The document download has been saved.', true), 'default', array('class' => 'alert-success'));
			return $this->redirect(array('action' => 'index'));
		} else {
			$this->Flash->error(__('The document download could not be saved. Please, try again.'));
		}
	}
	$qcDocument = $this->DocumentDownload->QcDocument->find('first',array(
		'recursive'=>-1,
		'fields'=>array('QcDocument.id','QcDocument.it_categories'),
		'conditions'=>array('QcDocument.id'=>$this->request->params['pass'][0])));
	if($qcDocument){
		$this->set('qcDocument',$qcDocument);
	}

	if($this->request->params['named']['qc_document_id']){
		$qcDocument = $this->DocumentDownload->QcDocument->find('first',array(
			'recursive'=>-1,
			'fields'=>array('QcDocument.id','QcDocument.it_categories'),
			'conditions'=>array('QcDocument.id'=>$this->request->params['named']['qc_document_id'])));
		if($qcDocument){
			$this->set('qcDocument',$qcDocument);
		}			
	}	

	// check if pdf template available
	if($this->request->params['named']['custom_table_id']){
		$this->loadModel('PdfTemplate');
		$pdfTemplates = $this->PdfTemplate->find('list',array(
			'conditions'=>array(
				'PdfTemplate.template_type'=>0,
				'PdfTemplate.custom_table_id'=>$this->request->params['named']['custom_table_id'])));
		if($pdfTemplates){
			$this->set('pdfTemplates',$pdfTemplates);
		}
	}

	if($this->request->params['named']['custom_table_id']){
		$this->loadModel('PdfTemplate');
		$pdfTemplateHeaders = $this->PdfTemplate->find('list',array(
			'conditions'=>array(
				'PdfTemplate.template_type'=>1,
				'PdfTemplate.custom_table_id'=>$this->request->params['named']['custom_table_id'])));
		if($pdfTemplateHeaders){
			$this->set('pdfTemplateHeaders',$pdfTemplateHeaders);
		}
	}
}


public function add_download_details($type = null, $id = null){
	$this->autoRender = false;
	if($this->data['t'] == 'qc'){
		$doc['DocumentDownload']['qc_document_id'] = $this->data['id'];
		$this->loadModel('QcDocument');
		$qcDocument = $this->QcDocument->find('first',array('conditions'=>array('QcDocument.id'=>$this->data['id']),array('QcDocument.id','QcDocument.issue_number'),'recursive'=>-1));
	}
	if($this->data['t'] == 'rec'){

		$this->loadModel('File');
		$file = $this->File->find('first',array('conditions'=>array('File.id'=>$this->data['id']),'recursive'=>-1));
		$doc['DocumentDownload']['file_id'] = $this->data['id'];
		$doc['DocumentDownload']['custom_table_id'] = $file['File']['custom_table_id'];
		$doc['DocumentDownload']['record_id'] = $file['File']['record_id'];
	}

	$doc['DocumentDownload']['name'] = $this->data['n'];
	$doc['DocumentDownload']['download_by'] = $doc['DocumentDownload']['prepared_by'] = $doc['DocumentDownload']['approved_by'] = $this->Session->read('User.employee_id');
	$doc['DocumentDownload']['signature'] = $this->data['s'];
	$doc['DocumentDownload']['created_by'] = $this->Session->read('User.id');
	$doc['DocumentDownload']['created_by'] = 1;
	$doc['DocumentDownload']['downoad_time'] = $doc['DocumentDownload']['created'] = $doc['DocumentDownload']['modified'] = date('Y-m-d H:i:s');
	$doc['DocumentDownload']['soft_delete'] = 0;
	$doc['DocumentDownload']['publish'] = $doc['DocumentDownload']['add_document'] = 1;
	$doc['DocumentDownload']['issue'] = $qcDocument['QcDocument']['issue_number'];

	$this->DocumentDownload->create();
	$this->DocumentDownload->save($doc,false);
	exit;
}

public function record_list(){
	// $this->autoRender = false;
	if($this->request->data['DocumentDownload']['custom_table_id'] != null && $this->request->data['DocumentDownload']['custom_table_id'] != -1 && $this->request->data['DocumentDownload']['custom_table_id'] != ''){
		$this->loadModel('CustomTable');
		$customTable = $this->CustomTable->find('first',array('conditions'=>array('CustomTable.id'=>$this->request->data['DocumentDownload']['custom_table_id']),'recursive'=>-1));
		
		if($customTable){			
			$model = Inflector::Classify($customTable['CustomTable']['table_name']);
			$this->loadModel($model);
			if(isset($this->request->data['DocumentDownload']['record_id']) && ($this->request->data['DocumentDownload']['record_id'] != null || $this->request->data['DocumentDownload']['record_id'] != -1) ){
				$record = $this->$model->find('first',array(
					'recursive'=>0,
					'fields'=>array(
						$model.'.id',$model.'.'.$this->$model->displayField . ' as default',
						'PreparedBy.name','ApprovedBy.name',
						$model.'.file_id',$model.'.file_key',$model.'.custom_table_id','CustomTable.table_name','QcDocument.id',
					),
					'conditions'=>array($model.'.id'=>$this->request->data['DocumentDownload']['record_id'])));
				if($record){
					$this->set('record',$record);
					$this->set('fields',json_decode($customTable['CustomTable']['fields'],true));
				}

				if($this->request->data['DocumentDownload']['qc_document_id']){
					$this->loadModel('QcDocument');
					$qcDocument = $this->QcDocument->find('first',array('conditions'=>array('QcDocument.id'=>$this->request->data['DocumentDownload']['qc_document_id'])));					
					if($qcDocument){
						$this->set('qcDocument',$qcDocument);
						$fontsize = $this->request->data['DocumentDownload']['font_size'];
						$fontface = $this->request->data['DocumentDownload']['font_face'];
						// check if this record has linkedTos
						// $childs = $this->QcDocument->find('all',array('conditions'=>array('QcDocument.parent_document_id'=>$qcDocument['QcDocument']['id'])));
						$additionalTables  = $this->CustomTable->find('all',array('recursive'=>0, 'conditions'=>array('QcDocument.parent_document_id'=>$qcDocument['QcDocument']['id'])));
						foreach($additionalTables as $additionalTable){
							$childModel = Inflector::classify($additionalTable['CustomTable']['table_name']);
							$this->loadModel($childModel);
							$childRecords[$additionalTable['CustomTable']['name']] = $this->$childModel->find('all',array(
								'conditions'=>array($childModel.'.parent_id'=>$record[$model]['id']),
								'recursive'=>0,
								'fields'=>array(
									$childModel.'.id',$childModel.'.'.$this->$childModel->displayField .' as default',
									'PreparedBy.name','ApprovedBy.name',
									$childModel.'.file_id',$childModel.'.file_key',$childModel.'.custom_table_id','CustomTable.table_name'
								)
							));
						}						
						$this->set('childRecords',$childRecords);
					}else{
							//qc document not found
					}

				}



			}else{
					// record not found
			}
		}
	}else if(isset($this->request->data['DocumentDownload']['qc_document_id'])){
			$this->loadModel('QcDocument');
			$qcDocument = $this->QcDocument->find('first',array(
				'conditions'=>array('QcDocument.id'=>$this->request->data['DocumentDownload']['qc_document_id']),
				'recursive'=>-1,
				'fields'=>array(
					'QcDocument.id',
					'QcDocument.title',
					'QcDocument.file_key',
					'QcDocument.version',
					'QcDocument.file_type',
					'QcDocument.data_type',
					'QcDocument.document_number',
					'QcDocument.issue_number',
					'QcDocument.revision_number',					
				)));
			
			$qcDocumentChild = $this->QcDocument->find('all',array(
				'conditions'=>array('QcDocument.parent_document_id'=>$this->request->data['DocumentDownload']['qc_document_id']),
				'recursive'=>-1,
				'fields'=>array(
					'QcDocument.id',
					'QcDocument.title',
					'QcDocument.file_key',
					'QcDocument.version',
					'QcDocument.file_type',
					'QcDocument.data_type',
					'QcDocument.document_number',
					'QcDocument.issue_number',
					'QcDocument.revision_number',
					'QcDocument.parent_document_id',					
				)));
			
			$this->set('qcDocument',$qcDocument);
			$this->set('qcDocumentChild',$qcDocumentChild);			
	}	
}

public function download(){
	
	$this->set('addwatermark',false);
	if($this->request->params['named']['type'] == 'rec'){
		if($this->request->params['named']['id']){
			$path = WWW_ROOT .'files' . DS . 'pdf' . DS . $this->Session->read('User.id') . DS . $this->request->params['named']['id'];
			$folderToEmpty = New Folder($path);
			$folderToEmpty->delete();	
		}
		

		//run file script
		if($this->request->params['named']['model']){
			$model = $this->request->params['named']['model'];			
			$this->loadModel($model);
			$record = $this->$model->find('first',array('conditions'=>array($model.'.id'=>$this->request->params['named']['id'])));
			if($record){
				$customTable = $this->$model->CustomTable->find('first',array('conditions'=>array('CustomTable.id'=>$record[$model]['custom_table_id'])));				
				//check if file is available for this record
				$this->loadModel('DownloadFile');
				$file = $this->DownloadFile->find('first',array('conditions'=>array('DownloadFile.model'=>$model,'DownloadFile.record_id'=>$record[$model]['id'])));	
				$qcDocument = $this->$model->QcDocument->find('first',array('conditions'=>array('QcDocument.id'=>$record[$model]['qc_document_id'])));
				$this->set('qcDocument',$qcDocument);
				if($record){
					$this->set('record',$record);
					$this->set('fields',json_decode($customTable['CustomTable']['fields'],true));
					$fontsize = $this->request->data['DocumentDownload']['font_size'];
					$fontface = $this->request->data['DocumentDownload']['font_face'];
					if(!$fontsize)$fontsize = '12';
					if(!$fontface)$fontface = 'arial';

					if($this->request->data['DocumentDownload']['pdf_header_id'] != -1){
						$header_file = $this->_generate_template_header($qcDocument,$fontsize,$fontface,$record,$this->request->data['DocumentDownload']['pdf_header_id'],$model);	
						$this->set('header_file',$header_file);
					}else{
						$this->_generate_header($qcDocument,$fontsize,$fontface,$record[$model]['id']);
					}
					
					$this->loadModel('PdfTemplate');
					if($this->request->data['DocumentDownload']['pdf_template_id'] != -1){
						$pdfTemplate = $this->PdfTemplate->find('first',array('conditions'=>array('PdfTemplate.id'=>$this->request->data['DocumentDownload']['pdf_template_id']),'recursive'=>-1));	
						$this->set('pdfTemplate',$pdfTemplate);
						// open template
						$templateFile = Configure::read('files') . DS . 'pdf_template' . DS . $pdfTemplate['PdfTemplate']['id'] . DS . 'template.html';
						if($templateFile){
						
							$filetoread = fopen($templateFile, "r") or die("Unable to open file!");
							$contents = fread($filetoread,filesize($templateFile));
							fclose($filetoread);
						
							$content = $this->_generate_template_content($customTable['CustomTable']['fields'],$record,$model,$fontsize,$fontface,$contents,$pdfTemplate['PdfTemplate']['child_table_fields']);							
						}						
					}else{
						$content = $this->_generate_content($customTable['CustomTable']['fields'],$record,$model,$fontsize,$fontface);
					}					
				}	
			}			
			
			$additionalFiles = json_decode($record[$model]['additional_files']);
			if($additionalFiles && is_array($additionalFiles)){
				foreach($additionalFiles as $additionalFile){
					$aFile = $this->DownloadFile->find('first',array('conditions'=>array('DownloadFile.id'=>$additionalFile),'recursive'=>-1));
					if($aFile && $aFile['DownloadFile']['model'] == 'QcDocument'){
						$url = Router::url('/', true) .'/files/'.$this->Session->read('User.company_id') .'/files/'.$aFile['DownloadFile']['id'] .'/'. $aFile['DownloadFile']['name'].'.'.$aFile['DownloadFile']['file_type']; 						
						$this->_generate_onlyoffice_pdf($url,$aFile['DownloadFile']['file_type'],'pdf', null, $aFile['DownloadFile']['name'], $aFile['DownloadFile']['qc_document_id']);
					}	
				}
			}

			$pdfs = array();
			$path = WWW_ROOT .'files' . DS . 'pdf' . DS . $this->Session->read('User.id') . DS . $this->request->params['named']['id'];	
			$folder = new Folder($path);
			$pdfs = $folder->find('.*\.pdf');
			$this->set('pdfs',$pdfs);
			$this->set('id',$file['DownloadFile']['id']);
			

		}
	}else if($this->request->params['named']['type'] == 'qc'){
		// run onlyoffice script
		$this->loadModel('QcDocument');
		$qcDocument = $this->QcDocument->find('first',array(
			'recursive'=>-1,			
			'conditions'=>array('QcDocument.id'=>$this->request->params['named']['id'])));

		$file_type = $qcDocument['QcDocument']['file_type'];
		$file_name = $qcDocument['QcDocument']['title'];
		$document_number = $qcDocument['QcDocument']['document_number'];
		$document_version = $qcDocument['QcDocument']['revision_number'];
		$file_name = $document_number . '-' . $file_name . '-' . $document_version;
		$file_name = $this->_clean_table_names($file_name);
		$file = $file_name . '.' . $file_type;

		$url = Router::url('/', true) .'/files/'.$this->Session->read('User.company_id') .'/qc_documents/'.$qcDocument['QcDocument']['id'] .'/'. $file; 
		
		$this->_generate_onlyoffice_pdf($url,$qcDocument['QcDocument']['file_type'], 'pdf' ,null, $file_name ,$qcDocument['QcDocument']['id']);	

		$pdfs = array();
		$path = WWW_ROOT .'files' . DS . 'pdf' . DS . $this->Session->read('User.id') . DS . $this->request->params['named']['id'];	
		$folder = new Folder($path);
		$pdfs = $folder->find('.*\.pdf');
		$this->set('pdfs',$pdfs);
		$this->set('id',$qcDocument['QcDocument']['id']);
	}

	$this->set('signature',$this->request->data['DocumentDownload']['signature']);
				
}


 public function _generate_template_header($qcDocument = null,$fontsize = null,$fontface = null,$record = null,$template_id = null, $model = null){
 	$this->loadModel('PdfTemplate');
 	$header = $this->PdfTemplate->find('first',array('recursive'=>-1, 'conditions'=>array('PdfTemplate.id'=>$template_id)));
 	$header_html =  str_replace('&quot;','"',$header['PdfTemplate']['template']);
	
	$this->set('pdfHeader',$header);
	
	$belongsTo = $this->$model->belongsTo;
 	$fields = $this->viewVars['fields'];
	foreach($fields as $field){		
		if($field['linked_to'] != -1){
			foreach($belongsTo as $modelname => $fieldDetails){
				if($fieldDetails['foreignKey'] == $field['field_name']){
					$this->loadModel($modelname);
					$displayField = $this->$modelname->displayField;
					$header_html = str_replace('$record["'.$modelname.'"]["'.$field['field_name'].'"]',$record[$modelname][$displayField],$header_html);
				}
			}

		}else if($field['data_type'] == 'radio'){
			$csvoptions = explode(',',$field['csvoptions']);
			$header_html = str_replace('$record["'.$model.'"]["'.$field['field_name'].'"]',$csvoptions[$record[$model][$field['field_name']]],$header_html);
		}else{
			$header_html = str_replace('$record["'.$model.'"]["'.$field['field_name'].'"]',$record[$model][$field['field_name']],$header_html);
		}

	}

	$fields = array(
		'$qcDocument["QcDocument"]["name"]'=>$qcDocument["QcDocument"]["name"],
        '$qcDocument["QcDocument"]["title"]'=>$qcDocument["QcDocument"]["title"],
        '$qcDocument["QcDocument"]["document_number"]'=>$qcDocument["QcDocument"]["document_number"],
        '$qcDocument["QcDocument"]["issue_number"]'=>$qcDocument["QcDocument"]["issue_number"],
        '$qcDocument["QcDocument"]["date_of_next_issue"]'=>date(Configure::read('dateFormat',strtotime($qcDocument["QcDocument"]["date_of_next_issue"]))),
        '$qcDocument["QcDocument"]["date_of_issue"]'=>date(Configure::read('dateFormat',strtotime($qcDocument["QcDocument"]["date_of_issue"]))),
        '$qcDocument["QcDocument"]["effective_from_date"]'=>date(Configure::read('dateFormat',strtotime($qcDocument["QcDocument"]["effective_from_date"]))),
        '$qcDocument["QcDocument"]["revision_number"]'=>$qcDocument["QcDocument"]["revision_number"],
        '$qcDocument["QcDocument"]["date_of_review"]'=>date(Configure::read('dateFormat',strtotime($qcDocument["QcDocument"]["date_of_review"]))),
        '$qcDocument["QcDocument"]["revision_date"]'=>date(Configure::read('dateFormat',strtotime($qcDocument["QcDocument"]["revision_date"]))),
        '$qcDocument["Standard"]["name"]'=>$qcDocument["Standard"]["name"],
        '$qcDocument["Clause"]["name"]'=>$qcDocument["Clause"]["name"],
        '$qcDocument["Schedule"]["name"]'=>$qcDocument["Schedule"]["name"],
        '$qcDocument["IssuedBy"]["name"]'=>$qcDocument["IssuedBy"]["name"],
        '$qcDocument["PreparedBy"]["name"]'=>$qcDocument["PreparedBy"]["name"],
        '$qcDocument["ApprovedBy"]["name"]'=>$qcDocument["ApprovedBy"]["name"],
    );
    
    foreach($fields as $field => $value){       
        $header_html = str_replace($field,$value, $header_html);
    }      
	
	$this->_write_html_file($qcDocument['QcDocument']['id'], '<!DOCTYPE html>'.$header_html,$record[$model]['id']);

 }

public function _generate_header($qcDocument = null, $fontsize = null,$fontface = null,$record_id = null){	

	$headerFile = Configure::read('files') . DS . 'pdf_template' . DS . 'header/template.html';

	if(file_exists($headerFile)){
		$filetoread = fopen($headerFile, "r") or die("Unable to open file!");
		$table = fread($filetoread,filesize($headerFile));
		fclose($filetoread);

    $fields = array(
    	'$qcDocument["QcDocument"]["name"]'=>$qcDocument["QcDocument"]["name"],
        '$qcDocument["QcDocument"]["title"]'=>$qcDocument["QcDocument"]["title"],
        '$qcDocument["QcDocument"]["document_number"]'=>$qcDocument["QcDocument"]["document_number"],
        '$qcDocument["QcDocument"]["issue_number"]'=>$qcDocument["QcDocument"]["issue_number"],
        '$qcDocument["QcDocument"]["date_of_next_issue"]'=>date(Configure::read('dateFormat',strtotime($qcDocument["QcDocument"]["date_of_next_issue"]))),
        '$qcDocument["QcDocument"]["date_of_issue"]'=>date(Configure::read('dateFormat',strtotime($qcDocument["QcDocument"]["date_of_issue"]))),
        '$qcDocument["QcDocument"]["effective_from_date"]'=>date(Configure::read('dateFormat',strtotime($qcDocument["QcDocument"]["effective_from_date"]))),
        '$qcDocument["QcDocument"]["revision_number"]'=>$qcDocument["QcDocument"]["revision_number"],
        '$qcDocument["QcDocument"]["date_of_review"]'=>date(Configure::read('dateFormat',strtotime($qcDocument["QcDocument"]["date_of_review"]))),
        '$qcDocument["QcDocument"]["revision_date"]'=>date(Configure::read('dateFormat',strtotime($qcDocument["QcDocument"]["revision_date"]))),
        '$qcDocument["Standard"]["name"]'=>$qcDocument["Standard"]["name"],
        '$qcDocument["Clause"]["name"]'=>$qcDocument["Clause"]["name"],
        '$qcDocument["Schedule"]["name"]'=>$qcDocument["Schedule"]["name"],
        '$qcDocument["IssuedBy"]["name"]'=>$qcDocument["IssuedBy"]["name"],
        '$qcDocument["PreparedBy"]["name"]'=>$qcDocument["PreparedBy"]["name"],
        '$qcDocument["ApprovedBy"]["name"]'=>$qcDocument["ApprovedBy"]["name"],
    );

        foreach($fields as $field => $value){       
        	if($value != '')$table = str_replace($field,$value, $table);
	    }        
	}else{
		$table ="<!DOCTYPE html><html><head>";
		$table .= "<style>
			body{font-family: '".$fontface."'; font-size:10px}
			table{width:'100%' background-color: #ccc; border-color: #ccc; font-family:'".$fontface."'}
			tr{background-color: #fff; text-align: left;}
			td,th{background-color: #fff; text-align: left;}
		</style></head><body>";

		$table .= "<h2 style=\"font-size:24px;margin-bottom:10px\">". $qcDocument['QcDocument']['name']."</h2>";
		$table .= "<table style=\"background-color:#000\" width=\"100%\" border=\"1\" cellspacing=\"1\" cellpadding=\"5\">
					<tr>
						<th style=\"border:1px solid #ccc\">Document Number</th>
						<td style=\"border:1px solid #ccc\">". $qcDocument['QcDocument']['document_number']."</td>
						<th style=\"border:1px solid #ccc\">Revision Number</th>
						<td style=\"border:1px solid #ccc\">". $qcDocument['QcDocument']['revision_number']."</td>
						<th style=\"border:1px solid #ccc\">Date Of Issue</th>
						<td style=\"border:1px solid #ccc\">". date(Configure::read('dateFormat'),strtotime($qcDocument['QcDocument']['date_of_issue']))."</td>
					</tr>
					<tr>
						<th style=\"border:1px solid #ccc\">Prepared By</th>
						<td style=\"border:1px solid #ccc\">". $qcDocument['PreparedBy']['name']."</td>
						<th style=\"border:1px solid #ccc\">Approved By</th>
						<td style=\"border:1px solid #ccc\">". $qcDocument['ApprovedBy']['name']."</td>
						<th style=\"border:1px solid #ccc\">Issueed By</th>
						<td style=\"border:1px solid #ccc\">". $qcDocument['IssuedBy']['name']."</td>
					</tr>
				</table>
		";	
		$table .= "</table></body></html>";	
	}		
	$this->_write_html_file($qcDocument['QcDocument']['id'],$table,$record_id);
}


public function _write_html_file($filename = null, $content = null, $record_id = null){
	
	$path = WWW_ROOT .'files' . DS . 'pdf' . DS . $this->Session->read('User.id') . DS . $record_id;	

	$folder = new Folder();
	if ($folder->create($path,0777)) {
	} else {
		echo "Folder creation failed";
		exit;
	}

	$file = WWW_ROOT .'files' . DS . 'pdf' . DS . $this->Session->read('User.id'). DS . $record_id . DS . $filename .".html" ;
	$myfile = fopen($file, "w") or die("Unable to open file - 1");	
	fwrite($myfile, $content);
	fclose($myfile);	
}

public function _generate_template_content($fields = null, $record = null, $model = null,$fontsize = null,$fontface = null, $contents = null,$childTableFields = null){	

	$this->set('fontsize',$fontsize);
	$this->set('fontface',$fontface);
	$path = WWW_ROOT .'files'. DS . 'pdf' . DS .$this->Session->read('User.id') . DS . $record[$model]['id'] ;
	
	$fields = json_decode($fields,true);
	$belongsTo = $this->$model->belongsTo;

	$str = "<style>
	body{font-family: '".$fontface."'; font-size:".$fontsize."px}
	table{background-color: #ccc; border-color: #ccc; font-size:".$fontsize."px; width:100% !important}
	tr{background-color: #fff; text-align: left;}
	td,th{background-color: #fff; text-align: left;}
</style>";
		
	foreach($fields as $field){
		if($field['linked_to'] != -1){
			foreach($belongsTo as $modelname => $fieldDetails){
				if($fieldDetails['foreignKey'] == $field['field_name']){
					$this->loadModel($modelname);					
					$displayField = $this->$modelname->displayField;
					$f = '$record["'.$modelname.'"]["'.$displayField.'"]';
					$contents = str_replace($f, $record[$modelname][$displayField], $contents);
				}
			}

		}else if($field['data_type'] == 'radio'){
			$f = '$record["'.$model.'"]["'.$field['field_name'].'"]';
			$csvoptions = explode(',',$field['csvoptions']);			
			$contents = str_replace($f, $csvoptions[$record[$model][$field['field_name']]], $contents);
		}else if($field['field_type'] == 5){
			$f = '$record["'.$model.'"]["'.$field['field_name'].'"]';
			$contents = str_replace($f, date(Configure::read('dateFormat'),strtotime($record[$model][$field['field_name']])), $contents);			
		}else{
			$f = '$record["'.$model.'"]["'.$field['field_name'].'"]';
			$contents = str_replace($f, $record[$model][$field['field_name']], $contents);
		}

	}
	
	foreach($record as $modelN => $fs){
		if($modelN != $model){
			foreach($fs as $n => $val){
				$f = '$record["'.$modelN.'"]["'.$n.'"]';
				if($val && $f && !is_array($val))$contents = str_replace($f, $val, $contents);
			}
		}			
	}
	
	// prepared by & approved by
	$contents = str_replace('$record["PreparedBy"]["name"]', $record['PreparedBy']['name'], $contents);
	$contents = str_replace('$record["ApprovedBy"]["name"]', $record['ApprovedBy']['name'], $contents);

	$contents = str_replace('</head>',$str.'</head>',$contents);
	
	$chld = '';
	$t = 0;
	$childTables = $this->DocumentDownload->CustomTable->find('all',array(
		'recursive'=>-1,
		'fields'=>array(
			'CustomTable.id',
			'CustomTable.table_name',
			'CustomTable.name',
			'CustomTable.table_version',
			'CustomTable.table_type',
			'CustomTable.qc_document_id',
			'CustomTable.custom_table_id',
			'CustomTable.display_field',
			'CustomTable.fields',
			'CustomTable.form_layout',
		),
		'conditions'=>array(
			// 'CustomTable.publish'=>1,
			// 'CustomTable.table_locked'=>0,
			'CustomTable.custom_table_id'=>$record['CustomTable']['id'])
	));
	$cTables = json_decode($childTableFields,true);
	foreach($cTables as $cTable){
		$cTable = json_decode($cTable,true);
		$rearrangeCtabls[$cTable['name']] = explode(',',substr($cTable['fields'], 0,-1) );
	}
	
	if($childTables){		
		foreach($childTables as $childTable){
			$chld .= "<div style='width:100%;clear:both;margin-bottom:25px;'> <table width='100%' border=1 cellspacing=1 cellpadding=5>";
			// $chld .= '<h2>' . $childTable['CustomTable']['name'] .'</h2>';
			$childTableModel = Inflector::classify($childTable['CustomTable']['table_name']);
			$this->loadModel($childTableModel);
			$chld .= '<tr>';
			foreach($rearrangeCtabls[$childTable['CustomTable']['table_name']] as $cField){
				foreach(json_decode($childTable['CustomTable']['fields'],true) as $field){
					if($cField == $field['field_name']){
						$chld .= '<th>' . base64_decode($field['field_label']). '</th>';
					}					
				}
			}		
			$chld .= '</tr>';			

			$childRecords = $this->$childTableModel->find('all',array('conditions'=>array($childTableModel.'.parent_id'=>$record[$model]['id'])));
			foreach($childRecords as $childRecord){
				$chld .= '<tr>';
				foreach($rearrangeCtabls[$childTable['CustomTable']['table_name']] as $cField){
					foreach(json_decode($childTable['CustomTable']['fields'],true) as $field){
						if($cField == $field['field_name']){
							$belongsTo = $this->$childTableModel->belongsTo;
							if($field['linked_to'] != -1){
								foreach($belongsTo as $childTableModelbelogs => $fieldDetails){
									if($fieldDetails['foreignKey'] == $field['field_name']){
										$this->loadModel($modelname);
										$displayField = $this->$modelname->displayField;
										$chld .= "<td>".$childRecord[$childTableModelbelogs][$displayField]."</td>";		
									}
								}

							}else if($field['data_type'] == 'radio'){
								$csvoptions = explode(',',$field['csvoptions']);
								$chld .= "<td>".$csvoptions[$childRecord[$childTableModel][$field['field_name']]]."</td>";
							}else if($field['field_type'] == 5){								
								$chld .= "<td>".date(Configure::read('dateFormat',strtotime($childRecord[$childTableModel][$field['field_name']])))."</td>";
							}else{
								$chld .= "<td>".$childRecord[$childTableModel][$field['field_name']]."</td>";
							}
						}
					}
				}
				$chld .= '</tr>';			
			}
			$chld .= "</table></div>";
			$contents = str_replace($childTable['CustomTable']['table_name'],$chld,$contents);
			$t++;
		}		
	}	


	$fields = array(
        '$qcDocument["QcDocument"]["title"]'=>$this->viewVars['qcDocument']["QcDocument"]["title"],
        '$qcDocument["QcDocument"]["document_number"]'=>$this->viewVars['qcDocument']["QcDocument"]["document_number"],
        '$qcDocument["QcDocument"]["issue_number"]'=>$this->viewVars['qcDocument']["QcDocument"]["issue_number"],
        '$qcDocument["QcDocument"]["date_of_next_issue"]'=>date(Configure::read('dateFormat'),strtotime($this->viewVars['qcDocument']["QcDocument"]["date_of_next_issue"])),
        '$qcDocument["QcDocument"]["date_of_issue"]'=>date(Configure::read('dateFormat'),strtotime($this->viewVars['qcDocument']["QcDocument"]["date_of_issue"])),
        '$qcDocument["QcDocument"]["effective_from_date"]'=>date(Configure::read('dateFormat'),strtotime($this->viewVars['qcDocument']["QcDocument"]["effective_from_date"])),
        '$qcDocument["QcDocument"]["revision_number"]'=>date(Configure::read('dateFormat'),strtotime($this->viewVars['qcDocument']["QcDocument"]["revision_number"])),
        '$qcDocument["QcDocument"]["date_of_review"]'=>date(Configure::read('dateFormat'),strtotime($this->viewVars['qcDocument']["QcDocument"]["date_of_review"])),
        '$qcDocument["QcDocument"]["revision_date"]'=>date(Configure::read('dateFormat'),strtotime($this->viewVars['qcDocument']["QcDocument"]["revision_date"])),
        '$qcDocument["Standard"]["name"]'=>$this->viewVars['qcDocument']["Standard"]["name"],
        '$qcDocument["Clause"]["name"]'=>$this->viewVars['qcDocument']["Clause"]["name"],
        '$qcDocument["Schedule"]["name"]'=>$this->viewVars['qcDocument']["Schedule"]["name"],
        '$qcDocument["IssuedBy"]["name"]'=>$this->viewVars['qcDocument']["IssuedBy"]["name"],
        '$qcDocument["PreparedBy"]["name"]'=>$this->viewVars['qcDocument']["PreparedBy"]["name"],
        '$qcDocument["ApprovedBy"]["name"]'=>$this->viewVars['qcDocument']["ApprovedBy"]["name"],
    );
    
    foreach($fields as $field => $value){       
        $contents = str_replace($field,$value, $contents);
    }  
	
	$header_file = Router::url('/', true) . 'files/pdf/' .$this->Session->read('User.id') . '/' . $record[$model]['id']. '/' . $record[$model]['qc_document_id']. '.html';
	$filenamae = $model."-".$record[$model][$this->$model->displayField];

	if($record[$model]['file_id']){
		$this->loadModel('DownloadFile');
		$file = $this->DownloadFile->find('first',array('conditions'=>array('DownloadFile.id'=>$record[$model]['file_id'])));
		if($file)$this->onlyoffice_pdf($file);
	}
	
	
	$this->_generate_pdf_file($header_file,$contents,$filenamae,$record[$model]['id']);
}


public function _generate_content($fields = null, $record = null, $model = null,$fontsize = null,$fontface = null){
	$this->set('fontsize',$fontsize);
	$this->set('fontface',$fontface);
	$path = WWW_ROOT .'files'. DS . 'pdf' . DS .$this->Session->read('User.id') . DS . $record[$model]['id'] ;
	
	$fields = json_decode($fields,true);
	$belongsTo = $this->$model->belongsTo;

	$str = "<style>
	body{font-family: '".$fontface."'; font-size:".$fontsize."px}
	table{background-color: #ccc; border-color: #ccc; font-size:".$fontsize."px;width:100% !important}
	tr{background-color: #fff; text-align: left;}
	td,th{background-color: #fff; text-align: left;}
</style>";
	
	$str .= "<table width='100%' border=1 cellspacing=1 cellpadding=5>";
	foreach($fields as $field){		
		if($field['linked_to'] != -1){
			foreach($belongsTo as $modelname => $fieldDetails){
				if($fieldDetails['foreignKey'] == $field['field_name']){
					$this->loadModel($modelname);
					$displayField = $this->$modelname->displayField;
					$str .= "<tr><th>".base64_decode($field['field_label'])."</th><td>".$record[$modelname][$displayField]."</td>";		
				}
			}

		}else if($field['data_type'] == 'radio'){
			$csvoptions = explode(',',$field['csvoptions']);
			$str .= "<tr><th width='30%'>".base64_decode($field['field_label'])."</th><td>".$csvoptions[$record[$model][$field['field_name']]]."</td>";
		}else if($field['field_type'] == 5){
			$str .= "<tr><th>".base64_decode($field['field_label'])."</th><td>".date(Configure::read('dateFormat'),strtotime($record[$model][$field['field_name']]))."</td>";
		}else{
			$str .= "<tr><th>".base64_decode($field['field_label'])."</th><td>".$record[$model][$field['field_name']]."</td>";
		}

	}
	$str .= "<tr><th>Prepared By</th><td>".$record['PreparedBy']['name']."</td>";
	$str .= "<tr><th>Prepared By</th><td>".$record['ApprovedBy']['name']."</td>";
	
	$str .= "</table>";		
	
	$t = 0;
	$childTables = $this->DocumentDownload->CustomTable->find('all',array(
		'recursive'=>-1,
		'fields'=>array(
			'CustomTable.id',
			'CustomTable.table_name',
			'CustomTable.name',
			'CustomTable.table_version',
			'CustomTable.table_type',
			'CustomTable.qc_document_id',
			'CustomTable.custom_table_id',
			'CustomTable.display_field',
			'CustomTable.fields',
			'CustomTable.form_layout',
		),
		'conditions'=>array(
			// 'CustomTable.publish'=>1,
			// 'CustomTable.table_locked'=>0,
			'CustomTable.custom_table_id'=>$record['CustomTable']['id'])
	));

	if($childTables){
		$str .= "<table width='100%' border=1 cellspacing=1 cellpadding=5>";

		foreach($childTables as $childTable){
			$str .= '<h1>' . $childTable['CustomTable']['name'] .'</h1>';
			$childTableModel = Inflector::classify($childTable['CustomTable']['table_name']);
			$this->loadModel($childTableModel);
			$str .= '<tr>';
			foreach(json_decode($childTable['CustomTable']['fields'],true) as $field){
				$str .= '<th>' . base64_decode($field['field_label']). '</th>';
			}
			$str .= '</tr>';			

			$childRecords = $this->$childTableModel->find('all',array('conditions'=>array($childTableModel.'.parent_id'=>$record[$model]['id'])));
			foreach($childRecords as $childRecord){
				$str .= '<tr>';
				foreach(json_decode($childTable['CustomTable']['fields'],true) as $field){
					$belongsTo = $this->$model->belongsTo;
					if($field['linked_to'] != -1){
						foreach($belongsTo as $childTableModel => $fieldDetails){
							if($fieldDetails['foreignKey'] == $field['field_name']){
								$this->loadModel($modelname);
								$displayField = $this->$modelname->displayField;
								$str .= "<td>".$childRecord[$childTableModel][$displayField]."</td>";		
							}
						}

					}else if($field['data_type'] == 'radio'){
						$csvoptions = explode(',',$field['csvoptions']);
						$str .= "<td>".$csvoptions[$childRecord[$childTableModel][$field['field_name']]]."</td>";
					}else if($field['field_type'] == 5){						
						$str .= "<td>".date(Configure::read('dateFormat',strtotime($childRecord[$childTableModel][$field['field_name']])))."</td>";
					}else{
						$str .= "<td>".$childRecord[$childTableModel][$field['field_name']]."</td>";
					}
				}
				$str .= '</tr>';			
			}
			$t++;
		}

		$str .= "</table>";		
	}	

	
	$header_file = Router::url('/', true) . 'files/pdf/' .$this->Session->read('User.id') . '/' . $record[$model]['id']. '/' . $record[$model]['qc_document_id']. '.html';
	$filenamae = $model."-".$record[$model][$this->$model->displayField];

	if($record[$model]['file_id']){
		$this->loadModel('DownloadFile');
		$file = $this->DownloadFile->find('first',array('conditions'=>array('DownloadFile.id'=>$record[$model]['file_id'])));
		if($file)$this->onlyoffice_pdf($file);
	}
	
	$this->_generate_pdf_file($header_file,$str,$filenamae,$record[$model]['id']);
}


public function download_qc_document(){
	if(isset($this->request->data['DocumentDownload']['qc_document_id'])){
		$this->loadModel('QcDocument');
		$qcDocument = $this->QcDocument->find('first',array('conditions'=>array('QcDocument.id'=>$this->request->data['DocumentDownload']['qc_document_id'])));
		if($qcDocument){

			$file_name = $qcDocument['QcDocument']['document_number'] . '-' . $qcDocument['QcDocument']['title'] . '-' . $qcDocument['QcDocument']['revision_number'];
			$file_name = $this->_clean_table_names($file_name);
			$file_name = $file_name;

			$url = Router::url('/', true) . 'files/'. $this->Session->read('User.company_id') . '/qc_documents/' . $qcDocument['QcDocument']['id']. '/' . $file_name . '.' . $qcDocument['QcDocument']['file_type'];						
			$this->_generate_onlyoffice_pdf($url,$qcDocument['QcDocument']['file_type'], 'pdf' ,null, $file_name ,$qcDocument['QcDocument']['id']);	
		}
	}
}

public function get_child_records($additionalTables = null,$id = null){
	if($additionalTables){
		// from custom table
		$this->loadModel('CustomTable');
		$customTable = $this->CustomTable->find('first',array('conditions'=>array('CustomTable.id'=>$additionalTables['CustomTable']['id']),'recursive'=>-1));
		if($customTable){
			
			$model = Inflector::Classify($customTable['CustomTable']['table_name']);
			$this->loadModel($model);
			$records = $this->$model->find('all',array($model.'.parent_id'=>$id));
				foreach($records as $record){
					if($record){
						$this->set('record',$record);
						$this->set('fields',json_decode($customTable['CustomTable']['fields'],true));
					}
					if($this->request->data['DocumentDownload']['qc_document_id']){
						$this->loadModel('QcDocument');
						$qcDocument = $this->QcDocument->find('first',array('conditions'=>array('QcDocument.id'=>$customTable['CustomTable']['qc_document_id'])));
						if($qcDocument){

							$fontsize = $this->request->data['DocumentDownload']['font_size'];
							$fontface = $this->request->data['DocumentDownload']['font_face'];
							$this->_generate_header($qcDocument,$fontsize,$fontface);	
							// $this->_write_html_file($record[$model]['id'],null);
							$content = $this->_generate_content($customTable['CustomTable']['fields'],$record,$model,$fontsize,$fontface);		
							
							
						}else{
								//qc document not found
						}

					}
				}				
		}else{
				// custom table not found
		}
	}
}


public function onlyoffice_pdf($file = null){		
	$url = Router::url('/', true) . 'files/'. $this->Session->read('User.company_id') . '/files/' . $file['DownloadFile']['id']. '/' . $file['DownloadFile']['name'] . '.' . $file['DownloadFile']['file_type'];	
	$this->_generate_onlyoffice_pdf($url,$file['DownloadFile']['file_type'], 'pdf' ,null, $file['DownloadFile']['name'] ,$file['DownloadFile']['id']);	
}

public function _generate_pdf_file($header_file = null,$content = null,$fileName = null, $record_id = null){
	if(isset($this->viewVars['header_file'])){
		$header_file = $this->viewVars['header_file'];
	}

	if(isset($this->viewVars['pdfHeader']['PdfTemplate'])){
		$dpi = $this->viewVars['pdfHeader']['PdfTemplate']['dpi'];
		$outline = $this->viewVars['pdfHeader']['PdfTemplate']['outline']?'true':'false';
		$outline_depth = $this->viewVars['pdfHeader']['PdfTemplate']['outline_depth'];
		$header_spacing = $this->viewVars['pdfHeader']['PdfTemplate']['header_spacing'];
		$footer_left = $this->viewVars['pdfHeader']['PdfTemplate']['footer_left'];
		$footer_center = $this->viewVars['pdfHeader']['PdfTemplate']['footer_center'];
		$footer_right = $this->viewVars['pdfHeader']['PdfTemplate']['footer_right'];
		$footer_font_size = $this->viewVars['pdfHeader']['PdfTemplate']['footer_font_size'];
		$margin_bottom = $this->viewVars['pdfHeader']['PdfTemplate']['margin_bottom'];
		$margin_left = $this->viewVars['pdfHeader']['PdfTemplate']['margin_left'];
		$margin_right = $this->viewVars['pdfHeader']['PdfTemplate']['margin_right'];
		$margin_top = $this->viewVars['pdfHeader']['PdfTemplate']['margin_top'];

	}else if(isset($this->viewVars['pdfTemplate']['PdfTemplate'])){
		$dpi = $this->viewVars['pdfTemplate']['PdfTemplate']['dpi'];
		$outline = $this->viewVars['pdfTemplate']['PdfTemplate']['outline']?'true':'false';
		$outline_depth = $this->viewVars['pdfTemplate']['PdfTemplate']['outline_depth'];
		$header_spacing = $this->viewVars['pdfTemplate']['PdfTemplate']['header_spacing'];
		$footer_left = $this->viewVars['pdfTemplate']['PdfTemplate']['footer_left'];
		$footer_center = $this->viewVars['pdfTemplate']['PdfTemplate']['footer_center'];
		$footer_right = $this->viewVars['pdfTemplate']['PdfTemplate']['footer_right'];
		$footer_font_size = $this->viewVars['pdfTemplate']['PdfTemplate']['footer_font_size'];
		$margin_bottom = $this->viewVars['pdfTemplate']['PdfTemplate']['margin_bottom'];
		$margin_left = $this->viewVars['pdfTemplate']['PdfTemplate']['margin_left'];
		$margin_right = $this->viewVars['pdfTemplate']['PdfTemplate']['margin_right'];
		$margin_top = $this->viewVars['pdfTemplate']['PdfTemplate']['margin_top'];

		if($this->viewVars['pdfTemplate']['PdfTemplate']['header'] == 1){
			$header_file = '';
		}
	}
	
	if(!$dpi)$dpi = 360;
	if(!$outline)$outline = false;
	if(!$outline_depth)$outline_depth = 2;
	if(!$header_spacing)$header_spacing = 2;
	if(!$footer_left)$footer_left = 'Confidential Document
				Generated by www.flinkiso.com
				On : '. date('Y-m-d H:i:s');
	
	if(!$footer_center)$footer_center = 'Page [page] of [toPage]';
	if(!$footer_right)$footer_right = '';
	if(!$footer_font_size)$footer_font_size = 6;
	if(!$margin_bottom)$margin_bottom = 5;
	if(!$margin_left)$margin_left = 10;
	if(!$margin_right)$margin_right = 10;
	if(!$margin_top)$margin_top = 30;
	

	if($outline == true){
		$CakePdf = new CakePdf(array(				
			'options' => array(	
				'header-html' => $header_file,
				'print-media-type' => false,
				'outline' => true,
				'dpi' => $dpi,
				'outline-depth'=>$outline_depth,
				'enable-local-file-access'=>true,
				'header-spacing'=>$header_spacing,
				'footer-left'     => $footer_left,                
				'footer-center'     => $footer_center,
				'footer-font-size'     => $footer_font_size,
			),
			'margin' => array(
				'bottom' => $margin_bottom,
				'left' => $margin_left,
				'right' => $margin_right,
				'top' => $margin_top
			),
		));
	}else{
		$CakePdf = new CakePdf(array(				
			'options' => array(	
				'header-html' => $header_file,
				'print-media-type' => false,
				'outline' => false,
				'dpi' => $dpi,
				'outline-depth'=>$outline_depth,
				'enable-local-file-access'=>true,
				'header-spacing'=>$header_spacing,
				'footer-left'     => $footer_left,                
				'footer-center'     => $footer_center,
				'footer-font-size'     => $footer_font_size,
			),
			'margin' => array(
				'bottom' => $margin_bottom,
				'left' => $margin_left,
				'right' => $margin_right,
				'top' => $margin_top
			),
		));
	}

	// check if logo if is available
	$this->set('content',$content);
	$path = WWW_ROOT .'files'. DS . 'pdf' . DS . $this->Session->read('User.id') . DS . $record_id;
	
	try{
		$dir = WWW_ROOT .'files'. DS . 'pdf' . DS .$this->Session->read('User.id') . DS . $record_id;
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

	$fileName = str_replace(' ','-',$fileName);

	$fileName = '-remove-pdf-' . $fileName . '-'. date('his');
	$CakePdf->template('pagecontent', 'pagecontent');
	$CakePdf->viewVars($this->viewVars);
	$CakePdf->custom_write($path, $path . DS . $fileName.'.pdf');
	$pdf = $path . DS . $fileName.'.pdf';
	$fileName = $path . DS . $fileName.'.pdf';     
	$this->add_password($fileName,null,$record_id);
	// delete html file
	
	$header_file = str_replace(Router::url('/', true), WWW_ROOT, $header_file);

	unlink($header_file);
	return $fileName;
}	

}
