<?php
App::uses('AppController', 'Controller');
// App::uses('File', 'Utility');
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

	if($this->request->params['named']['custom_table_id']){
		$this->loadModel('CustomTable');

		$qcDocument = $this->CustomTable->find('first',array(
			'recursive'=>0,
			'fields'=>array('CustomTable.id','CustomTable.qc_document_id','QcDocument.id','QcDocument.it_categories'),
			'conditions'=>array('CustomTable.id'=>$this->request->params['named']['custom_table_id'])));
		$this->set('qcDocument',$qcDocument);		
	}
	
	$this->set('controller_name',$this->request->params['named']['controller_name']);
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

	$doc['Documentdownload']['download_by'] = $this->Session->read('User.employee_id');
	$doc['DocumentDownload']['name'] = $this->data['n'];	
	$doc['DocumentDownload']['prepared_by'] = $doc['DocumentDownload']['approved_by'] = $this->Session->read('User.employee_id');	 
	$doc['DocumentDownload']['signature'] = $this->data['s'];
	$doc['DocumentDownload']['created_by'] = $this->Session->read('User.id');
	$doc['DocumentDownload']['created_by'] = 1;
	$doc['DocumentDownload']['downoad_time'] = $doc['DocumentDownload']['created'] = $doc['DocumentDownload']['modified'] = date('Y-m-d H:i:s');
	$doc['DocumentDownload']['soft_delete'] = 0;
	$doc['DocumentDownload']['publish'] = $doc['DocumentDownload']['add_document'] = 1;
	$doc['DocumentDownload']['issue'] = $qcDocument['QcDocument']['issue_number'];
	$doc = array_merge($doc['DocumentDownload'],array('download_by'=>$this->Session->read('User.employee_id')));
	
	$this->DocumentDownload->create();
	if($this->DocumentDownload->save($doc,false)){
		return true;
	}else{
		return false;
	}
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
						$model.'.file_id',$model.'.file_key',$model.'.custom_table_id','CustomTable.id','CustomTable.table_name','QcDocument.id',
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
									$childModel.'.file_id',$childModel.'.file_key',$childModel.'.custom_table_id','CustomTable.id','CustomTable.table_name'
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

public function _add_cover($data = null,$id = null){
	if(isset($data['DocumentDownload']['qc_document_id']) && ($data['DocumentDownload']['qc_document_id'] != '-1' || $data['DocumentDownload']['qc_document_id'] != 0)){
		$this->loadModel('QcDocument');
		$qcDocument = $this->QcDocument->find('first',array('conditions'=>array('QcDocument.id'=>$id)));		
		if($qcDocument){
			// load cover page
			$cover = Configure::read('files') . DS . 'pdf_template' . DS . 'cover' . DS . 'template.html';
			if(file_exists($cover)){
				$filetoread = fopen($cover, "r") or die("Unable to open file!");
				$contents = fread($filetoread,filesize($cover));
				fclose($filetoread);

				$contents = str_replace('&apos;', '"', $contents);
            	$contents = str_replace('&quot;', '"', $contents);
								
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
		        '$qcDocument["Clause"]["title"]'=>$qcDocument["Clause"]["title"],
		        // '$qcDocument["Clause"]["name"]'=>$qcDocument["Clause"]["name"],
		        '$qcDocument["Schedule"]["name"]'=>$qcDocument["Schedule"]["name"],
		        '$qcDocument["IssuedBy"]["name"]'=>$qcDocument["IssuedBy"]["name"],
		        '$qcDocument["PreparedBy"]["name"]'=>$qcDocument["PreparedBy"]["name"],
		        '$qcDocument["ApprovedBy"]["name"]'=>$qcDocument["ApprovedBy"]["name"],
		        '$qcDocument["QcDocumentCategory"]["name"]' => $qcDocument["QcDocumentCategory"]["name"],
		        '$qcDocument["IssuingAuthority"]["name"]'=>$qcDocument["IssuingAuthority"]["name"]
		    );

		        foreach($fields as $field => $value){       
		        	if($value != '')$contents = str_replace($field,$value, $contents);
			    }

				
				if($id){
					$path = WWW_ROOT .'files' . DS . 'pdf' . DS . $this->Session->read('User.id') . DS . $id;
					$folderToEmpty = New Folder($path);
					$folderToEmpty->delete();	
				}

				// $path = WWW_ROOT .'files' . DS . 'pdf' . DS . $this->Session->read('User.id') . DS . $id;
				
				// public function _generate_onlyoffice_pdf($url = null,$filetype = null,$outputtype = null, $password = null, $title = null,$record_id = null,$cover = null){
				// ($folder = null, $file = null, $content = null){
				
					
				$this->_write_html_file('template',$contents,$id);

				// $this->_write_to_file(
				// 	WWW_ROOT .'files' . DS . 'pdf' . DS . $this->Session->read('User.id') . DS . $id,
				// 	WWW_ROOT .'files' . DS . 'pdf' . DS . $this->Session->read('User.id') . DS . $id . DS . 'template.html',
				// 	$contents
				// );

				$url = Router::url('/', true) .'/files/pdf/'.$this->Session->read('User.id').'/'.$id .'/template.html';
				$this->_generate_onlyoffice_pdf($url,'html', 'pdf' ,null, 'template' ,$id,true);
				
				return true;
			}
		}
	}
	return true;
}
public function download(){
	if ($this->request->is('post')) {	
		$this->set('addwatermark',false);
		if($this->request->params['named']['type'] == 'rec'){			
			if($this->request->params['named']['id']){
				$path = WWW_ROOT .'files' . DS . 'pdf' . DS . $this->Session->read('User.id') . DS . $this->request->params['named']['id'];
				$folderToEmpty = New Folder($path);
				$folderToEmpty->delete();
			}
			
			if($this->request->data['DocumentDownload']['add_cover'] > 0){			
				$this->_add_cover($this->request->data,$this->request->params['named']['id']);
				$this->set('addcover',false);
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
							if($this->request->data['DocumentDownload']['add_header']){
								$header_file = $this->_generate_header($qcDocument,$fontsize,$fontface,$record[$model]['id']);	
							}
							
						}	

						
						$this->loadModel('PdfTemplate');
						if($this->request->data['DocumentDownload']['pdf_template_id'] != -1){
							$pdfTemplate = $this->PdfTemplate->find('first',array('conditions'=>array('PdfTemplate.id'=>$this->request->data['DocumentDownload']['pdf_template_id']),'recursive'=>-1));	
							$this->set('pdfTemplate',$pdfTemplate);
							$templateFile = Configure::read('files') . DS . 'pdf_template' . DS . $pdfTemplate['PdfTemplate']['id'] . DS . 'template.html';
							
							if($templateFile){
								$filetoread = fopen($templateFile, "r") or die("Unable to open file!");
								$contents = fread($filetoread,filesize($templateFile));
								fclose($filetoread);							
								$content = $this->_generate_template_content(
									$customTable['CustomTable']['fields'],
									$record,
									$model,
									$fontsize,
									$fontface,
									$contents,
									$pdfTemplate['PdfTemplate']['child_table_fields'],
									$header_file
								);
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
							$this->_generate_onlyoffice_pdf($url,$aFile['DownloadFile']['file_type'],'pdf', null, $aFile['DownloadFile']['name'], $aFile['DownloadFile']['qc_document_id'],false);
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
			
			if($this->request->params['named']['id']){
				$path = WWW_ROOT .'files' . DS . 'pdf' . DS . $this->Session->read('User.id') . DS . $this->request->params['named']['id'];
				$folderToEmpty = New Folder($path);
				$folderToEmpty->delete();
			}

			if($this->request->data['DocumentDownload']['add_cover'] > 0){
				$this->_add_cover($this->request->data,$this->request->params['named']['id']);
				$this->set('addcover',false);
			}

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
			$this->_generate_onlyoffice_pdf($url,$qcDocument['QcDocument']['file_type'], 'pdf' ,null, $file_name ,$qcDocument['QcDocument']['id'],false);	

			$pdfs = array();
			$path = WWW_ROOT .'files' . DS . 'pdf' . DS . $this->Session->read('User.id') . DS . $this->request->params['named']['id'];	
			$folder = new Folder($path);
			$pdfs = $folder->find('.*\.pdf');
			$this->set('pdfs',$pdfs);
			$this->set('id',$qcDocument['QcDocument']['id']);
		}
	}
	$this->set('signature',$this->request->data['DocumentDownload']['signature']);

}

/**
 * Generate one PDF containing only the selected custom-table records.
 * The related QC document and record attachments are deliberately excluded.
 */
public function download_record_bundle(){
	$this->layout = false;
	$this->set('bundle', null);
	$this->set('bundleError', null);

	if(!$this->request->is('post')){
		throw new MethodNotAllowedException();
	}

	$documentDownload = isset($this->request->data['DocumentDownload'])
		? $this->request->data['DocumentDownload'] : array();
	$records = !empty($documentDownload['records'])
		? json_decode($documentDownload['records'], true) : array();

	if(!is_array($records) || empty($records)){
		$this->set('bundleError', 'Select at least one record.');
		return;
	}
	if(count($records) > 200){
		$this->set('bundleError', 'A maximum of 200 records can be combined at one time.');
		return;
	}

	$originalDocumentDownload = $documentDownload;
	$unsecuredDocumentDownload = $documentDownload;
	foreach(array(
		'password','printing','degraded_printing','modify_contents','copy_contents',
		'screen_readers','assembly','fill_in','modify_annotations'
	) as $securityField){
		$unsecuredDocumentDownload[$securityField] = '';
	}
	$this->request->data['DocumentDownload'] = $unsecuredDocumentDownload;
	$requestedBundleId = isset($documentDownload['bundle_id']) ? $documentDownload['bundle_id'] : '';
	if(preg_match('/^record-bundle-[A-Za-z0-9-]{8,80}$/', $requestedBundleId)){
		$bundleId = $requestedBundleId;
	}else{
		$bundleId = 'record-bundle-'.date('YmdHis').'-'.substr(md5(microtime(true)), 0, 8);
	}
	$bundlePath = WWW_ROOT.'files'.DS.'pdf'.DS.$this->Session->read('User.id').DS.$bundleId;
	$folder = new Folder();
	if(!$folder->create($bundlePath, 0777)){
		$this->request->data['DocumentDownload'] = $originalDocumentDownload;
		$this->set('bundleError', 'Unable to create the combined PDF folder.');
		return;
	}
	$errorMarker = $bundlePath.DS.'error.txt';
	if(file_exists($errorMarker)) @unlink($errorMarker);

	$generatedPdfs = array();
	try{
		foreach($records as $recordSpec){
			$recordId = isset($recordSpec['id']) ? trim($recordSpec['id']) : '';
			$customTableId = isset($recordSpec['custom_table_id']) ? trim($recordSpec['custom_table_id']) : '';
			if($recordId === '' || $customTableId === ''){
				throw new InvalidArgumentException('An invalid record selection was received.');
			}

			$pdf = $this->_generate_record_only_pdf($customTableId, $recordId);
			if(!$pdf || !file_exists($pdf)){
				throw new RuntimeException('PDF generation failed for record '.$recordId.'.');
			}
			$generatedPdfs[] = $pdf;
		}

		$tempOutput = $bundlePath.DS.'-remove-pdf-records-'.date('Ymd-His').'.pdf';
		$pdftk = trim((string)Configure::read('PDFTkPath'));
		if($pdftk === '' || !is_executable($pdftk)){
			throw new RuntimeException('pdftk is not configured or is not executable.');
		}

		$command = escapeshellarg($pdftk);
		foreach($generatedPdfs as $generatedPdf){
			$command .= ' '.escapeshellarg($generatedPdf);
		}
		$command .= ' cat output '.escapeshellarg($tempOutput);
		$mergeOutput = array();
		$mergeCode = 0;
		exec($command.' 2>&1', $mergeOutput, $mergeCode);
		if($mergeCode !== 0 || !file_exists($tempOutput)){
			throw new RuntimeException('pdftk could not combine the record PDFs: '.implode("\n", $mergeOutput));
		}

		// Apply the user's password and permission choices once, to the final file.
		$this->request->data['DocumentDownload'] = $originalDocumentDownload;
		$password = isset($originalDocumentDownload['password']) ? $originalDocumentDownload['password'] : null;
		$finalOutput = $this->add_password($tempOutput, $password, $bundleId);
		if(!$finalOutput || !file_exists($finalOutput)){
			throw new RuntimeException('Unable to secure the combined record PDF.');
		}

		$this->set('bundle', array(
			'url' => Router::url('/', true).'files/pdf/'
				.rawurlencode($this->Session->read('User.id')).'/'
				.rawurlencode($bundleId).'/'.rawurlencode(basename($finalOutput)),
			'name' => basename($finalOutput),
			'count' => count($generatedPdfs),
		));
	}catch(Exception $e){
		$this->request->data['DocumentDownload'] = $originalDocumentDownload;
		@file_put_contents($errorMarker, $e->getMessage(), LOCK_EX);
		$this->set('bundleError', $e->getMessage());
	}
}

/**
 * Lightweight status endpoint used when the web server times out the long
 * ONLYOFFICE request while PHP continues producing the bundle.
 */
public function record_bundle_status($bundleId = null){
	$this->autoRender = false;
	$this->response->type('json');

	if(!preg_match('/^record-bundle-[A-Za-z0-9-]{8,80}$/', (string)$bundleId)){
		return $this->response->body(json_encode(array('ready'=>false, 'error'=>'Invalid combined PDF identifier.')));
	}

	$bundlePath = WWW_ROOT.'files'.DS.'pdf'.DS.$this->Session->read('User.id').DS.$bundleId;
	$errorMarker = $bundlePath.DS.'error.txt';
	if(file_exists($errorMarker)){
		$message = trim((string)file_get_contents($errorMarker));
		return $this->response->body(json_encode(array(
			'ready'=>false,
			'error'=>$message !== '' ? $message : 'The combined PDF could not be generated.',
		)));
	}

	$pdfs = glob($bundlePath.DS.'records-*.pdf');
	if(!empty($pdfs)){
		rsort($pdfs);
		$pdf = $pdfs[0];
		return $this->response->body(json_encode(array(
			'ready'=>true,
			'url'=>Router::url('/', true).'files/pdf/'
				.rawurlencode($this->Session->read('User.id')).'/'
				.rawurlencode($bundleId).'/'.rawurlencode(basename($pdf)),
			'name'=>basename($pdf),
		)));
	}

	return $this->response->body(json_encode(array('ready'=>false, 'processing'=>true)));
}

/**
 * Reuse the normal record renderer while omitting linked document files.
 */
protected function _generate_record_only_pdf($customTableId, $recordId){
	$this->loadModel('CustomTable');
	$customTable = $this->CustomTable->find('first', array(
		'conditions' => array('CustomTable.id' => $customTableId),
		'recursive' => -1,
	));
	if(empty($customTable['CustomTable']['table_name'])){
		throw new NotFoundException('The selected record type was not found.');
	}

	$model = Inflector::classify($customTable['CustomTable']['table_name']);
	$this->loadModel($model);
	$record = $this->$model->find('first', array(
		'conditions' => array(
			$model.'.id' => $recordId,
			$model.'.custom_table_id' => $customTableId,
		),
		'recursive' => 0,
	));
	if(empty($record[$model])){
		throw new NotFoundException('A selected record was not found.');
	}
	$record['CustomTable'] = $customTable['CustomTable'];

	// Record-only bundles must not include an uploaded/linked document.
	$record[$model]['file_id'] = null;
	$record[$model]['additional_files'] = null;

	$this->loadModel('QcDocument');
	$qcDocument = $this->QcDocument->find('first', array(
		'conditions' => array('QcDocument.id' => $record[$model]['qc_document_id']),
	));

	unset($this->viewVars['pdfHeader'], $this->viewVars['pdfTemplate'], $this->viewVars['header_file']);
	$this->set('qcDocument', $qcDocument);
	$this->set('record', $record);
	$this->set('fields', json_decode($customTable['CustomTable']['fields'], true));
	if(!empty($qcDocument['QcDocument']['title'])){
		$recordHeading = $qcDocument['QcDocument']['title'];
	}else if(!empty($customTable['CustomTable']['name'])){
		$recordHeading = $customTable['CustomTable']['name'];
	}else{
		$recordHeading = Inflector::humanize($customTable['CustomTable']['table_name']);
	}
	$displayField = $this->$model->displayField;
	$recordReference = isset($record[$model][$displayField]) ? $record[$model][$displayField] : '';
	$rootCustomTableId = isset($this->request->data['DocumentDownload']['custom_table_id'])
		? $this->request->data['DocumentDownload']['custom_table_id'] : '';
	$this->set('recordPdfHeading', $recordHeading);
	$this->set('recordPdfReference', $recordReference);
	$this->set('recordPdfType', $customTableId === $rootCustomTableId ? 'Main record' : 'Related record');

	$documentDownload = $this->request->data['DocumentDownload'];
	$fontSize = !empty($documentDownload['font_size']) ? $documentDownload['font_size'] : 12;
	$fontFace = !empty($documentDownload['font_face']) ? $documentDownload['font_face'] : 'Arial';
	$headerFile = null;
	$this->loadModel('PdfTemplate');

	if(!empty($documentDownload['pdf_header_id']) && $documentDownload['pdf_header_id'] != -1){
		$pdfHeader = $this->PdfTemplate->find('first', array(
			'conditions' => array(
				'PdfTemplate.id' => $documentDownload['pdf_header_id'],
				'PdfTemplate.custom_table_id' => $customTableId,
			),
			'recursive' => -1,
		));
		if(!empty($pdfHeader['PdfTemplate'])){
			$headerFile = $this->_generate_template_header(
				$qcDocument, $fontSize, $fontFace, $record,
				$documentDownload['pdf_header_id'], $model
			);
			$this->set('header_file', $headerFile);
		}else if(!empty($documentDownload['add_header'])){
			$headerFile = $this->_generate_header($qcDocument, $fontSize, $fontFace, $recordId);
		}
	}else if(!empty($documentDownload['add_header'])){
		$headerFile = $this->_generate_header($qcDocument, $fontSize, $fontFace, $recordId);
	}

	if(!empty($documentDownload['pdf_template_id']) && $documentDownload['pdf_template_id'] != -1){
		$pdfTemplate = $this->PdfTemplate->find('first', array(
			'conditions' => array(
				'PdfTemplate.id' => $documentDownload['pdf_template_id'],
				'PdfTemplate.custom_table_id' => $customTableId,
			),
			'recursive' => -1,
		));
		if(empty($pdfTemplate['PdfTemplate'])){
			// A parent table template cannot safely render a child table record.
			$generatedPdf = $this->_generate_content($customTable['CustomTable']['fields'], $record, $model, $fontSize, $fontFace);
		}else{
			$this->set('pdfTemplate', $pdfTemplate);
			$templateFile = Configure::read('files').DS.'pdf_template'.DS
				.$pdfTemplate['PdfTemplate']['id'].DS.'template.html';
			if(!is_file($templateFile)){
				throw new NotFoundException('The selected PDF template file was not found.');
			}
			$contents = file_get_contents($templateFile);
			$generatedPdf = $this->_generate_template_content(
				$customTable['CustomTable']['fields'], $record, $model, $fontSize, $fontFace,
				$contents, $pdfTemplate['PdfTemplate']['child_table_fields'], $headerFile
			);
		}
	}else{
		$generatedPdf = $this->_generate_content($customTable['CustomTable']['fields'], $record, $model, $fontSize, $fontFace);
	}

	return !empty($generatedPdf) ? $generatedPdf : false;
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
        '$qcDocument["Clause"]["title"]'=>$qcDocument["Clause"]["title"],
        '$qcDocument["Schedule"]["name"]'=>$qcDocument["Schedule"]["name"],
        '$qcDocument["IssuedBy"]["name"]'=>$qcDocument["IssuedBy"]["name"],
        '$qcDocument["PreparedBy"]["name"]'=>$qcDocument["PreparedBy"]["name"],
        '$qcDocument["ApprovedBy"]["name"]'=>$qcDocument["ApprovedBy"]["name"],
    );
    
    foreach($fields as $field => $value){       
        $header_html = str_replace($field,$value, $header_html);
    }      
	$file = $this->_write_html_file($qcDocument['QcDocument']['id'], '<!DOCTYPE html>'.$header_html,$record[$model]['id']);
	return $file;

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
        '$qcDocument["Clause"]["title"]'=>$qcDocument["Clause"]["title"],
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
	return $file; 	
}

public function _generate_template_content($fields = null, $record = null, $model = null,$fontsize = null,$fontface = null, $contents = null,$childTableFields = null, $header_file = null){
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
		$fieldResult = $this->field_render($field,$record,$model);
		if($fieldResult){
			$f = '$record["'.$model.'"]["'.$fieldResult['name'].'"]';
			$contents = str_replace($f, $fieldResult['value'], $contents);			
		}
	}

	$signature = $this->_fetch_signature($record['PreparedBy']['id']);	
	$contents = str_replace('$record["PreparedBy"]["name"]', $signature . $record['PreparedBy']['name'], $contents);
	$signature = $this->_fetch_signature($record['ApprovedBy']['id']);
	$contents = str_replace('$record["ApprovedBy"]["name"]', $signature.$record['ApprovedBy']['name'], $contents);

	foreach($record as $modelN => $fs){
		if($modelN != $model){
			foreach($fs as $n => $val){
				$f = '$record["'.$modelN.'"]["'.$n.'"]';
				if($val && $f && !is_array($val))$contents = str_replace($f, $val, $contents);
			}
		}
	}
	
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
					if($cField == $field['field_name'] || $cField == $field['linked_to_field_name']){
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
						if($cField == $field['field_name'] || $cField == $field['linked_to_field_name']){

							$fieldResult = $this->field_render($field,$childRecord,$childTableModel);
							if($fieldResult){
								$chld .= "<td>".$fieldResult['value']."&nbsp;</td>";
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
        '$qcDocument["Clause"]["title"]'=>$qcDocument["Clause"]["title"],
        '$qcDocument["Schedule"]["name"]'=>$this->viewVars['qcDocument']["Schedule"]["name"],
        '$qcDocument["IssuedBy"]["name"]'=>$this->viewVars['qcDocument']["IssuedBy"]["name"],
        '$qcDocument["PreparedBy"]["name"]'=>$this->viewVars['qcDocument']["PreparedBy"]["name"],
        '$qcDocument["ApprovedBy"]["name"]'=>$this->viewVars['qcDocument']["ApprovedBy"]["name"],
    );
    
    foreach($fields as $field => $value){       
        $contents = str_replace($field,$value, $contents);
    }  
	if(!$header_file){
		$header_file = Router::url('/', true) . 'files/pdf/' .$this->Session->read('User.id') . '/' . $record[$model]['id']. '/' . $record[$model]['qc_document_id']. '.html';	
	}
	
	$filenamae = $model."-".$record[$model][$this->$model->displayField];

	if($record[$model]['file_id']){
		$this->loadModel('DownloadFile');
		$file = $this->DownloadFile->find('first',array('conditions'=>array('DownloadFile.id'=>$record[$model]['file_id'])));
		if($file)$this->onlyoffice_pdf($file);
	}
	
		$contents .= $this->_get_approvals($model, $record[$model]['id']);
		return $this->_generate_pdf_file($header_file,$contents,$filenamae,$record[$model]['id']);
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
		$fieldResult = $this->field_render($field,$record,$model);
		if($fieldResult)$str .= "<tr><th>".$fieldResult['label']." &nbsp;</th><td>".$fieldResult['value']." &nbsp;</td></tr>";
	}
	
	$signature = $this->_fetch_signature($record['PreparedBy']['id']);
	$str .= "<tr><th>Prepared By</th><td>".$signature."".$record['PreparedBy']['name']."</td>";

	$signature = $this->_fetch_signature($record['ApprovedBy']['id']);
	$str .= "<tr><th>Approved By</th><td>".$signature."".$record['ApprovedBy']['name']."</td>";
	
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
			$field = null;
			foreach(json_decode($childTable['CustomTable']['fields'],true) as $field){
				$str .= '<th>' . base64_decode($field['field_label']). '</th>';
			}
			$str .= '</tr>';			

			$childRecords = $this->$childTableModel->find('all',array('conditions'=>array($childTableModel.'.parent_id'=>$record[$model]['id'])));
			$belongsTo = $this->$childTableModel->belongsTo;
			
			foreach($childRecords as $childRecord){
				$signature = '';
				$str .= '<tr>';
				$field = null;
				foreach(json_decode($childTable['CustomTable']['fields'],true) as $childfields){
						$fieldResult = $this->field_render($childfields,$childRecord,$childTableModel);
						if($fieldResult)$str .= "<td>".$fieldResult['value']." &nbsp;</td>";
				}
				$str .= '</tr>';				
			}
			$t++;
		}

		$str .= "</table>";		
	}	

	$str .= $this->_get_approvals($model, $record[$model]['id']);	

	$header_file = Router::url('/', true) . 'files/pdf/' .$this->Session->read('User.id') . '/' . $record[$model]['id']. '/' . $record[$model]['qc_document_id']. '.html';
	$filenamae = $model."-".$record[$model][$this->$model->displayField];

	if($record[$model]['file_id']){
		$this->loadModel('DownloadFile');
		$file = $this->DownloadFile->find('first',array('conditions'=>array('DownloadFile.id'=>$record[$model]['file_id'])));
		if($file)$this->onlyoffice_pdf($file);
	}
	
		return $this->_generate_pdf_file($header_file,$str,$filenamae,$record[$model]['id']);
}

public function _get_approvals($model = null,$id = null){
	$approvalStatuses = array(0=>'Pending',1=>'Approved',2=>'Not Approved');
	$this->loadModel('Approval');
	$approvals = $this->Approval->find('all',array(
		'recursive'=>0,
		'conditions'=>array('Approval.model_name'=>$model,'Approval.record'=>$id)));
	
	$appStr = '';
	foreach($approvals as $approval){
		$appStr .= "<br /><h3>Approvals</h3>";
		$appStr .= "<table width='100%' border=1 cellspacing=1 cellpadding=5>";	
		$appStr .= "<tr><th>Date/ Time</th><th>From</th><th>To</th><th>Comment</th><th>Response</th><th>Status</th></tr>";
		
		$appStr .= "<tr><th>". date(Configure::read('dateTimeFormat'),strtotime($approval['Approval']['created'])) ."</th><th>".$approval['From']['name']."</th><th>".$approval['Employee']['name']."</th><th>&nbsp;</th><th>&nbsp;</th><th>&nbsp;</th></tr>";
		
		$approvalComments = $this->Approval->ApprovalComment->find('all',array(
			'conditions'=>array('ApprovalComment.approval_id'=>$approval['Approval']['id'])));
		foreach($approvalComments as $approvalComment){
			$appStr .= "<tr><td>".date(Configure::read('dateTimeFormat'),strtotime($approvalComment['ApprovalComment']['created']))."</td><td>".$approvalComment['From']['name']."</td><td>".$approvalComment['User']['name']."</td><td>".$approvalComment['ApprovalComment']['comments']."</td><td>".$approvalComment['ApprovalComment']['response']."</td><td>".$approvalStatuses[$approvalComment['ApprovalComment']['response_status']]."</td></tr>";
		}
		$appStr .= "</table>";
	}

	return $appStr;	
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
			$this->_generate_onlyoffice_pdf($url,$qcDocument['QcDocument']['file_type'], 'pdf' ,null, $file_name ,$qcDocument['QcDocument']['id'],false);	
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
	$this->_generate_onlyoffice_pdf($url,$file['DownloadFile']['file_type'], 'pdf' ,null, $file['DownloadFile']['name'] ,$file['DownloadFile']['id'],false);	
}

public function _generate_pdf_file($header_file = null,$content = null,$fileName = null, $record_id = null){
	// Trial pipeline: CakePHP assembles the record HTML, ONLYOFFICE renders it,
	// and AppController::add_password() applies the final pdftk security.
	return $this->_generate_onlyoffice_record_pdf($header_file, $content, $fileName, $record_id);
}

	protected function _generate_onlyoffice_record_pdf($header_file = null, $content = null, $fileName = null, $record_id = null){
		if(empty($record_id)){
			throw new InvalidArgumentException('A record id is required to generate the PDF.');
		}

		if(isset($this->viewVars['header_file'])){
			$header_file = $this->viewVars['header_file'];
		}

		$config = array(
			'margin_bottom' => 5,
			'margin_left' => 10,
			'margin_right' => 10,
			'margin_top' => 30,
			'footer_left' => 'Confidential Document - Generated by www.flinkiso.com - On: '.date('Y-m-d H:i:s'),
			'footer_center' => '',
			'footer_right' => '',
			'footer_font_size' => 6,
		);

		$template = null;
		if(isset($this->viewVars['pdfHeader']['PdfTemplate'])){
			$template = $this->viewVars['pdfHeader']['PdfTemplate'];
		}else if(isset($this->viewVars['pdfTemplate']['PdfTemplate'])){
			$template = $this->viewVars['pdfTemplate']['PdfTemplate'];
			if(!empty($template['header'])){
				// This template already contains its own header.
				$header_file = null;
			}
		}

		if(is_array($template)){
			foreach(array_keys($config) as $key){
				if(isset($template[$key]) && $template[$key] !== ''){
					$config[$key] = $template[$key];
				}
			}
		}

		$header_path = $this->_pdf_local_path($header_file);
		$header_html = '';
		if($header_path && file_exists($header_path)){
			$header_html = file_get_contents($header_path);
		}else if(!empty($header_file) && !$header_path){
			// Fetch only genuinely external headers. A missing same-origin header
			// URL is routed through CakePHP and returns the login page, which must
			// never be embedded in the generated record PDF.
			$header_html = @file_get_contents($header_file);
		}
		$header_html = $this->_pdf_html_body($header_html);

		$footer_parts = array();
		foreach(array($config['footer_left'], $config['footer_center'], $config['footer_right']) as $footer_part){
			$footer_part = trim((string)$footer_part);
			// Legacy page-number tokens cannot be expanded in ordinary HTML.
			if($footer_part !== '' && stripos($footer_part, '[page]') === false && stripos($footer_part, '[toPage]') === false){
				$footer_parts[] = nl2br(htmlspecialchars($footer_part, ENT_QUOTES, 'UTF-8'));
			}
		}

		$css = '<style type="text/css">'
			. '@page { margin: '.floatval($config['margin_top']).'mm '
			. floatval($config['margin_right']).'mm '
			. floatval($config['margin_bottom']).'mm '
			. floatval($config['margin_left']).'mm; }'
			. '.flinkiso-pdf-header { margin-bottom: 8mm; }'
			. '.flinkiso-record-heading { border-bottom: 2px solid #333; margin: 0 0 8mm 0; padding: 0 0 4mm 0; }'
			. '.flinkiso-record-heading h1 { font-family: Arial, sans-serif; font-size: 22px; line-height: 1.2; margin: 1mm 0; }'
			. '.flinkiso-record-type { color: #666; font-family: Arial, sans-serif; font-size: 9px; font-weight: bold; letter-spacing: 1px; text-transform: uppercase; }'
			. '.flinkiso-record-reference { color: #444; font-family: Arial, sans-serif; font-size: 12px; }'
			. '.flinkiso-pdf-footer { margin-top: 8mm; color: #555; font-size: '
			. floatval($config['footer_font_size']).'px; }'
			. '</style>';

		$header_block = $header_html !== '' ? '<div class="flinkiso-pdf-header">'.$header_html.'</div>' : '';
		$record_heading_block = '';
		if(!empty($this->viewVars['recordPdfHeading'])){
			$record_heading = htmlspecialchars($this->viewVars['recordPdfHeading'], ENT_QUOTES, 'UTF-8');
			$record_reference = !empty($this->viewVars['recordPdfReference'])
				? htmlspecialchars($this->viewVars['recordPdfReference'], ENT_QUOTES, 'UTF-8') : '';
			$record_type = !empty($this->viewVars['recordPdfType'])
				? htmlspecialchars($this->viewVars['recordPdfType'], ENT_QUOTES, 'UTF-8') : 'Record';
			$record_heading_block = '<div class="flinkiso-record-heading">'
				.'<div class="flinkiso-record-type">'.$record_type.'</div>'
				.'<h1>'.$record_heading.'</h1>'
				.($record_reference !== '' ? '<div class="flinkiso-record-reference">'.$record_reference.'</div>' : '')
				.'</div>';
		}
		$footer_block = !empty($footer_parts) ? '<div class="flinkiso-pdf-footer">'.implode(' &nbsp; ', $footer_parts).'</div>' : '';
		$html = (string)$content;

		if(stripos($html, '<html') !== false){
			if(stripos($html, '</head>') !== false){
				$html = preg_replace('/<\/head>/i', $css.'</head>', $html, 1);
			}else{
				$html = $css.$html;
			}

			$opening_blocks = $header_block.$record_heading_block;
			if($opening_blocks !== ''){
				if(preg_match('/<body[^>]*>/i', $html)){
					$html = preg_replace_callback('/<body[^>]*>/i', function($matches) use ($opening_blocks){
						return $matches[0].$opening_blocks;
					}, $html, 1);
				}else{
					$html = $opening_blocks.$html;
				}
			}

			if(stripos($html, '</body>') !== false){
				$html = preg_replace('/<\/body>/i', $footer_block.'</body>', $html, 1);
			}else{
				$html .= $footer_block;
			}
		}else{
			$html = '<!DOCTYPE html><html><head><meta charset="UTF-8">'.$css.'</head><body>'
				.$header_block.$record_heading_block.$html.$footer_block.'</body></html>';
		}

		$safe_name = trim($this->_clean_table_names(str_replace(' ', '-', (string)$fileName)), '-');
		if($safe_name === '') $safe_name = 'record-report';
		$temp_name = 'onlyoffice-'.$safe_name.'-'.date('YmdHis').'-'.substr(md5(microtime(true)), 0, 8);
		$temp_file = $this->_write_html_file($temp_name, $html, $record_id);
		$temp_url = rtrim(Router::url('/', true), '/').'/files/pdf/'
			.rawurlencode($this->Session->read('User.id')).'/'
			.rawurlencode($record_id).'/'.rawurlencode($temp_name).'.html';

		try{
			$output_file = $this->_generate_onlyoffice_pdf($temp_url, 'html', 'pdf', null, $safe_name, $record_id, false);
		}catch(Exception $e){
			if($temp_file && file_exists($temp_file)) @unlink($temp_file);
			throw $e;
		}

		if($temp_file && file_exists($temp_file)) @unlink($temp_file);
		if($header_path && file_exists($header_path)){
			$record_dir = realpath(WWW_ROOT.'files'.DS.'pdf'.DS.$this->Session->read('User.id').DS.$record_id);
			$real_header = realpath($header_path);
			if($record_dir && $real_header && strpos($real_header, $record_dir.DS) === 0){
				@unlink($header_path);
			}
		}

		return $output_file;
	}

	protected function _pdf_local_path($file = null){
		if(empty($file)) return null;
		if(file_exists($file)) return $file;

		$base_url = rtrim(Router::url('/', true), '/');
		if(strpos($file, $base_url) === 0){
			$relative = ltrim(substr($file, strlen($base_url)), '/');
			return WWW_ROOT.str_replace('/', DS, $relative);
		}

		return null;
	}

	protected function _pdf_html_body($html = null){
		$html = trim((string)$html);
		if($html === '') return '';
		if(preg_match('/<body[^>]*>(.*?)<\/body>/is', $html, $matches)){
			return $matches[1];
		}
		return $html;
	}

	public function _fetch_signature($id = null){
		$this->loadModel('User');
		$user = $this->User->find('first', array(
			'fields'=>array(
				'User.id',
				'User.password',
				'User.employee_id',
				'Employee.id',
				'Employee.signature',
			),
			'conditions' => array('User.status' => 1, 'User.soft_delete' => 0, 'User.publish' => 1, 'User.employee_id' => $id)));

			$img = WWW_ROOT. DS. 'img'. DS . $this->Session->read('User.company_id'). DS .'signature'. DS. $user['Employee']['id']. DS. 'sign.png';
			if(file_exists($img)){
				$response = "<img src='".$img."' width=100><br />";
			}else if($user['Employee']['signature']){
				$response = "<img src='".$user['Employee']['signature']."' width=100><br />";
			}else{
				$response = 'Signature not available';
			}			
		return $response;
	}
}
