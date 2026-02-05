<?php
App::uses('AppController', 'Controller');
/**
 * PdfTemplatees Controller
 *
 * @property PdfTemplate $PdfTemplate
 * @property PaginatorComponent $Paginator
 */
class PdfTemplatesController extends AppController {
/**
 * Components
 *
 * @var array
 */
public $components = array('Paginator');

public function _commons(){
    $companies = $this->PdfTemplate->Company->find('list', array('conditions' => array('Company.publish' => 1, 'Company.soft_delete' => 0)));
    $preparedBies = $this->PdfTemplate->PreparedBy->find('list', array('conditions' => array('PreparedBy.publish' => 1, 'PreparedBy.soft_delete' => 0)));
    $approvedBies = $this->PdfTemplate->ApprovedBy->find('list', array('conditions' => array('ApprovedBy.publish' => 1, 'ApprovedBy.soft_delete' => 0)));
    $createdBies = $this->PdfTemplate->CreatedBy->find('list', array('conditions' => array('CreatedBy.publish' => 1, 'CreatedBy.soft_delete' => 0)));
    $modifiedBies = $this->PdfTemplate->ModifiedBy->find('list', array('conditions' => array('ModifiedBy.publish' => 1, 'ModifiedBy.soft_delete' => 0)));
    $this->set(compact('systemTables', 'companies', 'preparedBies', 'approvedBies', 'createdBies', 'modifiedBies'));
    $count = $this->PdfTemplate->find('count');
    $published = $this->PdfTemplate->find('count', array('conditions' => array('PdfTemplate.publish' => 1)));
    $unpublished = $this->PdfTemplate->find('count', array('conditions' => array('PdfTemplate.publish' => 0)));
    $this->set(compact('count', 'published', 'unpublished'));
    $this->_get_department_list();
}

public function _get_system_table_id() {
    $this->loadModel('SystemTable');
    $this->SystemTable->recursive = - 1;
    $systemTableId = $this->SystemTable->find('first', array('conditions' => array('SystemTable.system_name' => $this->request->params['controller'])));
    return $systemTableId['SystemTable']['id'];
}
/**
 * index method
 *
 * @return void
 */
public function index() {
    $conditions = $this->_check_request();
    if($this->request->params['pass'][0])$existing = array('PdfTemplate.custom_table_id'=>$this->request->params['pass'][0]);
    else $existing;
    $this->paginate = array('order' => array('PdfTemplate.sr_no' => 'DESC'), 'conditions' => array($conditions,$existing));
    $this->PdfTemplate->recursive = 0;
    $this->set('pdfTemplates', $this->paginate());
    $this->_get_count();
    $this->_get_department_list(); 
    $headerFile = Configure::read('files') . DS . 'pdf_template' . DS . 'header/template.docx';
    if(file_exists($headerFile)){
        $this->set('headerFileExists',true);
    }else{
        $this->set('headerFileExists',false);
    } 
}

public function add($custom_table_id = null,$qc_document_id = null){
    $headerFile = Configure::read('files') . DS . 'pdf_template' . DS . 'header/template.docx';
    if(file_exists($headerFile)){
        $this->set('headerFileExists',true);
    }else{
        $this->set('headerFileExists',false);
    }
    if ($this->request->is('post')) {
        $path = Configure::read('files') . DS . 'pdf_template' . DS . $this->request['data']['PdfTemplate']['custom_table_id'] ;
        if(file_exists($path . DS . 'template.docx') && $this->request['data']['PdfTemplate']['custom_table_id']){
            
            $pdf_template_folder = new Folder();
            $pdf_template_folder->create($path,0777);
            $url = Router::url('/', true) . 'files/'.$this->Session->read('User.company_id').'/pdf_template/' . $this->request['data']['PdfTemplate']['custom_table_id'] .'/template.docx' ;
            
            $html = $this->_convert_to_html($url,'docx','html',null,'Template',$this->request['data']['PdfTemplate']['custom_table_id'],$this->Session->read('User.company_id'));
            $html = str_replace('&quot;','"',$html);
            if($this->request->data['PdfTemplate']['html_cleanup'] == 0){
                $html = $this->_html_cleanup($html);
            }

            $this->request->data['PdfTemplate']['template'] = $html;
            $this->request->data['PdfTemplate']['child_table_fields'] = json_encode($this->request->data['PdfTemplate']['child_table_fields']);
            $this->PdfTemplate->create();
            if($this->PdfTemplate->save($this->request->data,false)){
                // move this new pdftemplate id and delete these files
                $from = new Folder($path);
                $new = Configure::read('files') . DS . 'pdf_template' . DS . $this->PdfTemplate->id;
                $new_folder = new Folder();
                $new_folder->create($new,0777);
                $from->copy($new);
                $templateFile = Configure::read('files') . DS .'pdf_template' . DS . $this->PdfTemplate->id . DS . 'template.html';
                $filetoread = new File($templateFile);
                $html = $filetoread->read();
                $filetoread->close();
                $output = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $html);
                $output = preg_replace('/cellspacing=".*?"/i', ' cellspacing="1"', $output);
                $output = preg_replace('/cellpadding=".*?"/i', ' cellpadding="3"', $output);
                $output = preg_replace('/border=".*?"/i', ' border="1"', $output);
                $output = str_replace('<span>', '', $output);
                $output = str_replace('</span>', '', $output);
                $output = str_replace('<td width="', '<td alt="', $output);
                $style = '<style>table{width:100%;}</style>';
                $output = str_replace('</head>',$style.'</head>',$output);
                $output = str_replace('&quot;','"',$output);
                $filetoread->write($output);
                $from->delete($path);
                $this->Session->setFlash(__('Template Created', true), 'default', array('class' => 'alert-success'));
                $this->redirect(array('action' => 'edit',$this->PdfTemplate->id));
            }
        }else{
            $url = Router::url('/', true) . 'files/'.$this->Session->read('User.company_id').'/pdf_template/header/template.docx';
            $key = $this->_generate_onlyoffice_key('template'. date('Ymdhis'));
            $html = $this->_convert_to_html($url,'docx','html',null,'Header','header',$this->Session->read('User.company_id'));
            $path = Configure::read('files') . DS . 'pdf_template' . DS . 'header';
            $file = $path . DS . "template.html" ;
            $myfile = fopen($file, "w") or die("Unable to open file - 1");
            $style = "<style>
            body{font-family: '".$this->request->data['PdfTemplates']['font_face']."'; font-size:".$this->request->data['PdfTemplates']['font_size']."px}
            table{width:100%;background-color: #ccc; border-color: #ccc; font-size:".$this->request->data['PdfTemplates']['font_size']."px;padding:0px;margin:0px}
            tr{background-color: #fff;padding:0px;margin:0px}
            td,th{background-color: #fff;left;padding:0px;margin:0px}
            p{padding:1px;margin:0px}
            </style>";
            $html = str_replace('<html>','<!DOCTYPE html><html>',$html);
            $pdfTemplate = $this->PdfTemplate->find('first',array('conditions'=>array('PdfTemplate.id'=>$record_id),'recursive'=>-1));
            if($this->request->data['PdfTemplate']['html_cleanup'] == 0){
                $html = $this->_html_cleanup($html);
            }
            $output = str_replace('<td width="', '<td alt="', $html);
            $output = str_replace('&apos;', '"', $output);
            $output = str_replace('&quot;', '"', $output);
            $output = str_replace('</head>',$style.'</head>',$output);
            fwrite($myfile, $output);
            fclose($myfile); 
            $this->Session->setFlash(__('Header Created', true), 'default', array('class' => 'alert-success'));
            $this->redirect(array('action' => 'add','HeaderTemplate'));
        }
    }else{
        if($this->request->params['pass'][0]){
            if($this->request->params['pass'][0] == 'HeaderTemplate'){
                $qcDocument['QcDocument']['title'] = 'PDF Header';
                $this->set('qcDocument',$qcDocument);
                $headerFile = Configure::read('files') . DS . 'pdf_template' . DS . 'header/template.docx';
                if(file_exists($headerFile)){
                    $this->set('headerFile',true);
                }else{
                    $header = $this->_generate_header(); 
                }
                $headerFileHtml = Configure::read('files') . DS . 'pdf_template' . DS . 'header/template.html';
                if(file_exists($headerFileHtml)){
                    $filetoread = new File($headerFileHtml);
                    $html = $filetoread->read();
                    $this->set('headerFileHtml',$html);
                    $filetoread->close();
                }else{
                    $header = $this->_generate_header(); 
                }
                $key = $this->_generate_onlyoffice_key('template' . date('Ymdhis'));
                $this->set('key',$key);
                $this->set('header',true);
            }else{
                $this->set('header',false);
                $customTable = $this->PdfTemplate->CustomTable->find('first',array(
                    'fields'=>array(
                        'CustomTable.id',
                        'CustomTable.name',
                        'CustomTable.table_name',
                        'CustomTable.table_version',
                        'CustomTable.qc_document_id',
                        'CustomTable.fields',
                        'CustomTable.belongs_to',
                        'CustomTable.has_many',
                        'CustomTable.child_tables_fields',
                        'CustomTable.form_layout',
                        'CustomTable.custom_table_id',
                        'QcDocument.id',
                    ),
                    'conditions'=>array(
                        'CustomTable.id'=>$custom_table_id,
                    )
                ));
                $fields = json_decode($customTable['CustomTable']['fields'],true);
                if($fields){
                    foreach($fields as $field){
                        $parentFields[$customTable['CustomTable']['table_name']][$field['field_name']] = $field['field_label'];
                    }
                }
                $parentFields[$customTable['CustomTable']['table_name']]['prepared_by'] = base64_encode('prepared_by');
                $parentFields[$customTable['CustomTable']['table_name']]['approved_by'] = base64_encode('approved_by');
                $parentFields[$customTable['CustomTable']['table_name']]['created'] = base64_encode('created');
                $parentFields[$customTable['CustomTable']['table_name']]['modified'] = base64_encode('modified');
                // create folder/file/pdf_templates/custom_table_id/table_name.docx
                $path = Configure::read('files') . DS . 'pdf_template' . DS .$customTable['CustomTable']['id'] ;
                if(!file_exists($path . DS . 'template.docx')){
                    $pdf_template_folder = new Folder();
                    $pdf_template_folder->create($path,0777);
                }
                if(!$fontsize)$fontsize = '12';
                if(!$fontface)$fontface = 'arial';
                $qcDocument = $this->PdfTemplate->CustomTable->QcDocument->find('first',array(
                    'fields'=>array(
                        'QcDocument.id',
                        'QcDocument.name',
                        'QcDocument.file_type',
                        'QcDocument.title',
                        'QcDocument.document_number',
                        'QcDocument.revision_number'
                    ),
                    'conditions'=>array('QcDocument.id'=>$customTable['CustomTable']['qc_document_id']),'recursive'=>-1));
                // $content = $this->_generate_content($customTable['CustomTable']['fields'],$customTable,Inflector::classify($customTable['CustomTable']['table_name']),$fontsize,$fontface); 
                // if(!in_array($qcDocument['QcDocument']['file_type'], array('doc','docx')) ){
                //     $this->Session->setFlash(__('PDF Templates are only available for Doc, Docx file types.', true), 'default', array('class' => 'alert-danger'));
                //     $this->redirect(array('action' => 'index'));
                // }
                $this->set('qcDocument',$qcDocument);
                $pdfTemplateCount = $this->PdfTemplate->find('count',array('conditions'=>array('PdfTemplate.custom_table_id'=>$this->request->params['pass'][0])));
                    $this->set('pdfTemplateCount',$pdfTemplateCount);
                $html = $content;
                if(in_array($qcDocument['QcDocument']['file_type'], array('doc','docx')) ){
                    $file_type = $qcDocument['QcDocument']['file_type'];
                    $file_name = $qcDocument['QcDocument']['title'];
                    $document_number = $qcDocument['QcDocument']['document_number'];
                    $document_version = $qcDocument['QcDocument']['revision_number'];
                    // save previous file version
                    $fileName = $document_number . '-' . $file_name . '-' . $document_version;
                    $fileName = $this->_clean_table_names($fileName);
                    $fileName = $fileName . '.' . $file_type;
                    $existingDocument = Configure::read('files') . DS . 'qc_documents' . DS . $qcDocument['QcDocument']['id'] . DS . $fileName;
                                        
                    if(!file_exists($existingDocument)){
                    }else{
                        $copydoc = new File($existingDocument);
                        $docnew = $path . DS . 'template.'.$qcDocument['QcDocument']['file_type'];
                        copy($existingDocument,$docnew);
                    }                    
                    $file = $path .DS . "template.html" ;
                    $myfile = fopen($file, "w") or die("Unable to open file - 1");
                    fwrite($myfile, $html);
                    fclose($myfile);
                    $url = Router::url('/', true) . 'files/'.$this->Session->read('User.company_id').'/pdf_template/' . $customTable['CustomTable']['id'] .'/template.html' ;
                    // $this->_convert_to_html($url,'html','docx',null,$customTable['CustomTable']['name'],$customTable['CustomTable']['id'],$this->Session->read('User.company_id'));
                }else{
                    $existingDocument = Configure::read('files') . DS . 'qc_documents' . DS . $qcDocument['QcDocument']['id'] . DS . $fileName;
                    $this->set('qcDocument',$qcDocument);
                    $path = Configure::read('files') . DS . 'pdf_template' . DS .$customTable['CustomTable']['id'] ;
                    if(!file_exists($path . DS . 'template.docx')){
                        $pdf_template_folder = new Folder();
                        $pdf_template_folder->create($path,0777);
                    }
                    if(!$fontsize)$fontsize = '12';
                    if(!$fontface)$fontface = 'arial';
                    if($qc_document_id){
                        $qcDocument = $this->PdfTemplate->CustomTable->QcDocument->find('first',array('conditions'=>array('QcDocument.id'=>$qc_document_id),'recursive'=>-1));
                    }                    
                    $content = $this->_generate_content($customTable['CustomTable']['fields'],$customTable,Inflector::classify($customTable['CustomTable']['table_name']),$fontsize,$fontface);
                    $html = $content;
                    $file = $path .DS . "template.html" ;
                    $myfile = fopen($file, "w") or die("Unable to open file - 1");
                    fwrite($myfile, $html);
                    fclose($myfile);
                    
                    $url = Router::url('/', true) . 'files/'.$this->Session->read('User.company_id').'/pdf_template/' . $customTable['CustomTable']['id'] .'/template.html' ;
                    $this->_convert_to_html($url,'html','docx',null,$customTable['CustomTable']['name'],$customTable['CustomTable']['id'],$this->Session->read('User.company_id'));
                }
                
                $this->set('url',$url);
                $this->set('customTable',$customTable);
                $key = $this->_generate_onlyoffice_key($customTable['CustomTable']['id'] . date('Ymdhis'));
                $this->set('key',$key);
                $childTables = $this->PdfTemplate->CustomTable->find('all',array(
                    'fields'=>array(
                        'CustomTable.id',
                        'CustomTable.name',
                        'CustomTable.table_name',
                        'CustomTable.table_version',
                        'CustomTable.qc_document_id',
                        'CustomTable.fields',
                        'CustomTable.belongs_to',
                        'CustomTable.has_many',
                        'CustomTable.child_tables_fields',
                        'CustomTable.form_layout',
                        'CustomTable.custom_table_id',
                        'QcDocument.id',
                    ),
                    'conditions'=>array(
                        'OR'=>array(
                            'CustomTable.custom_table_id'=>$custom_table_id
                        )
                    )
                ));
                if($childTables){
                    foreach($childTables as $customTable){
                        $fields = json_decode($customTable['CustomTable']['fields'],true);
                        if($fields){
                            foreach($fields as $field){
                                $result[$customTable['CustomTable']['table_name']][$field['field_name']] = $field['field_label'];
                            }
                        }
                        if(!$customTable['CustomTable']['custom_table_id']){
                            $result[$customTable['CustomTable']['table_name']]['prepared_by'] = base64_encode('prepared_by');
                            $result[$customTable['CustomTable']['table_name']]['approved_by'] = base64_encode('approved_by');
                            $result[$customTable['CustomTable']['table_name']]['created'] = base64_encode('created');
                            $result[$customTable['CustomTable']['table_name']]['modified'] = base64_encode('modified');
                        }
                    }
                    $this->set('fields',$result);
                }else{

                }
                $customTable = $this->PdfTemplate->CustomTable->find('first',array('conditions'=>array('CustomTable.id'=>$custom_table_id),'recursive'=>-1));
                // $qcDocument = $this->PdfTemplate->CustomTable->QcDocument->find('first',array('conditions'=>array('QcDocument.id'=>$qc_document_id),'recursive'=>-1));
                $this->set('customTable',$customTable);
                $this->set('qcDocument',$qcDocument);
                $this->set('childTables',$childTables);
            }
        }
    }
    $customTables = $this->PdfTemplate->CustomTable->find('list', array('conditions' => array('CustomTable.publish' => 1, 'CustomTable.soft_delete' => 0)));
    $this->set('customTables',$customTables);
    $this->set('qcDocument',$qcDocument);
}

public function _generate_content($fields = null, $record = null, $model = null,$fontsize = null,$fontface = null){
    $this->set('fontsize',$fontsize);
    $this->set('fontface',$fontface);
    $path = WWW_ROOT .'files'. DS . 'pdf' . DS .$this->Session->read('User.id') . DS . $record[$model]['id'] ;
    $fields = json_decode($fields,true);
    $belongsTo = $this->$model->belongsTo;
    $str = "<style>
    body{font-family: '".$fontface."'; font-size:".$fontsize."px}
    table{background-color: #ccc; border-color: #ccc; font-size:".$fontsize."px}
    tr{background-color: #fff; text-align: left;}
    td,th{background-color: #fff; text-align: left;}
    </style>";
    $str .= '<table width="100%" border="1" cellspacing="1" cellpadding="3">';
    foreach($fields as $field){ 
        if($field['linked_to'] != -1){
            foreach($belongsTo as $modelname => $fieldDetails){
                if($fieldDetails['foreignKey'] == $field['field_name']){
                    $this->loadModel($modelname);
                    $displayField = $this->$modelname->displayField;
                    $str .= '<tr><th>'.base64_decode($field["field_label"]).'</th><td>$record["'.$modelname.'"]["'.$displayField.'"]</td>';
                }
            }
        }
        else{
            $str .= '<tr><th>'.base64_decode($field["field_label"]).'</th><td>$record["'.$model.'"]["'.$field["field_name"].'"]</td>';
        }

    }
    $str .= '<tr><th>Prepared By</th><td>$record["PreparedBy"]["name"]</td>';
    $str .= '<tr><th>Prepared By</th><td>$record["ApprovedBy"]["name"]</td>';
    $str .= "</table>";
    $t = 0;
    $childTables = $this->PdfTemplate->CustomTable->find('all',array(
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
            'CustomTable.custom_table_id'=>$record['CustomTable']['id'])
    ));

    if($childTables){
        foreach($childTables as $childTable){
            $str .= "<table width='100%' border=1 cellspacing=1 cellpadding=3>";
            $str .= '<h1>' . $childTable['CustomTable']['name'] .' <small class="pull-right">Loop Tables</small></h1>';
            $childTableModel = Inflector::classify($childTable['CustomTable']['table_name']);
            $this->loadModel($childTableModel);
            $str .= '<tr>';
            foreach(json_decode($childTable['CustomTable']['fields'],true) as $field){
                $str .= '<th>' . base64_decode($field['field_label']). '</th>';
            }
            $str .= '</tr>';
            $str .= '<tr>';
            foreach(json_decode($childTable['CustomTable']['fields'],true) as $field){
                $belongsTo = $this->$model->belongsTo;
                if($field['linked_to'] != -1){
                    foreach($belongsTo as $childTableModel => $fieldDetails){
                        if($fieldDetails['foreignKey'] == $field['field_name']){
                            $this->loadModel($modelname);
                            $displayField = $this->$modelname->displayField;
                            $str .= "<td></td>"; 
                        }
                    }
                }else if($field['data_type'] == 'radio'){
                    $csvoptions = explode(',',$field['csvoptions']);
                    $str .= "<td></td>";
                }else{
                    $str .= "<td></td>";
                }
            }
            $str .= '</tr>';
            $t++;
            $str .= "</table>"; 
        }
    }
    return $str;
}

public function _convert_to_html($url = null,$filetype = null,$outputtype = null, $password = null, $title = null,$record_id = null,$company_id = null){

    $delpath = New Folder(WWW_ROOT .'files'. DS . 'pdf' . DS .$this->Session->read('User.id') . DS . $record_id);
    if($record_id){
        $delpath->delete(); 
    }
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
    $aurl = array(
        'controller' => 'pdf_templates',
        'action' => 'jwtencode',
        'payload'=>base64_encode(json_encode($arr))
    );


    $token =$this->requestAction($aurl);
    $arr['token'] = $token;
    
    $jwtHeader = Configure::read('onlyofficesecret');
    $headerToken = $this->jwtEncode([ "payload" => $arr ]);
    $data = json_encode($arr);
    // request parameters 
    $opts = array('http' => array(
        'method'=> 'POST',
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
    
    $new_data = file_get_contents($downloadUri);
    if ($new_data === FALSE) {
        echo "error";
    } else {
        if($this->request->data['PdfTemplate']['html_cleanup'] == 0 && $outputtype == 'html'){
            if($record_id != 'cover'){
                $new_data = $this->_html_cleanup($new_data);
            }
        }
        $savepath = WWW_ROOT . 'files' . DS . $company_id. DS . 'pdf_template' . DS . $record_id;
        if(file_exists($savepath)){
        }else{
            $folder = new Folder();
            if ($folder->create($savepath,0777)) {
            } else {
                echo "Folder creation failed";
                exit;
            }
        }
        $new_data = str_replace('&quot;','"',$new_data);
        $file_for_save = WWW_ROOT .'files' . DS . $company_id. DS . 'pdf_template' . DS . $record_id . DS .'template.'.$outputtype;
        if (file_put_contents($file_for_save, $new_data)) { 
            return $new_data;
        } else {

        }
    }
}

public function edit($id = null){
    if ($this->request->is('post')) {
        $path = Configure::read('files') . DS . 'pdf_template' . DS . $this->request['data']['PdfTemplate']['id'] ;
        if(file_exists($path . DS . 'template.docx')){
            $pdf_template_folder = new Folder();
            $pdf_template_folder->create($path,0777);
            $url = Router::url('/', true) . 'files/'.$this->Session->read('User.company_id').'/pdf_template/' . $this->request['data']['PdfTemplate']['id'] .'/template.docx' ;
            $html = $this->_convert_to_html($url,'docx','html',null,'Template',$this->request['data']['PdfTemplate']['id'],$this->Session->read('User.company_id'));
            $html = str_replace('&quot;','"',$html);
            if($this->request->data['PdfTemplate']['html_cleanup'] == 0){
                $html = $this->_html_cleanup($html);
            }
            $this->request->data['PdfTemplate']['template'] = $html;
            $this->request->data['PdfTemplate']['child_table_fields'] = json_encode($this->request->data['PdfTemplate']['child_table_fields']);
            $this->PdfTemplate->create();
            $this->PdfTemplate->save($this->request->data['PdfTemplate'],false);
        }
    }

    $pdfTemplate = $this->PdfTemplate->find('first',array('conditions'=>array('PdfTemplate.id'=>$id)));
    if($pdfTemplate['PdfTemplate']['template_type'] == 3){
        $this->redirect(array('action' => 'add_cover','CoverTemplate'));
    }

    if($pdfTemplate){ 
        $folder = Configure::read('files') . DS .'pdf_template' . DS . $pdfTemplate['PdfTemplate']['id'];
        $file = $folder . DS .'template.html';
        $this->_write_to_file($folder,$file,$html);
        $this->request->data = $pdfTemplate;
        $customTable = $this->PdfTemplate->CustomTable->find('first',array(
            'fields'=>array(
                'CustomTable.id',
                'CustomTable.name',
                'CustomTable.table_name',
                'CustomTable.table_version',
                'CustomTable.qc_document_id',
                'CustomTable.fields',
                'CustomTable.belongs_to',
                'CustomTable.has_many',
                'CustomTable.child_tables_fields',
                'CustomTable.form_layout',
                'CustomTable.custom_table_id',
                'QcDocument.id',
            ),
            'conditions'=>array(
                'CustomTable.id'=>$pdfTemplate['PdfTemplate']['custom_table_id'],
            )
        ));
        $skiparray = array('id','sr_no','record_status','status_user_id','soft_delete','branchid','departmentid','created_by','created','system_table_id','company_id','qc_document_id','custom_table_id','file_id','file_key','parent_id','additional_files');
        $fields = json_decode($customTable['CustomTable']['fields'],true);
        if($fields){
            foreach($fields as $field){
                $parentFields[$customTable['CustomTable']['table_name']][$field['field_name']] = $field['field_label'];
                if($field['data_type'] == 'dropdown-s'){
                    $linkedToModel = Inflector::classify($field['linked_to']);
                    try{
                        $this->loadModel($linkedToModel);
                        foreach(array_keys($this->$linkedToModel->schema()) as $linkedToFields){
                            if(!in_array($linkedToFields,$skiparray)){
                                $linkedTosFields[Inflector::classify($field['field_name'])][$linkedToFields] = $linkedToFields;
                            }
                        }
                    }catch(Exception $e){
                    }
                }
            }
        }
        $parentFields[$customTable['CustomTable']['table_name']]['prepared_by'] = base64_encode('prepared_by');
        $parentFields[$customTable['CustomTable']['table_name']]['approved_by'] = base64_encode('approved_by');
        $parentFields[$customTable['CustomTable']['table_name']]['created'] = base64_encode('created');
        $parentFields[$customTable['CustomTable']['table_name']]['modified'] = base64_encode('modified');
        $childTables = $this->PdfTemplate->CustomTable->find('all',array(
            'fields'=>array(
                'CustomTable.id',
                'CustomTable.name',
                'CustomTable.table_name',
                'CustomTable.table_version',
                'CustomTable.qc_document_id',
                'CustomTable.fields',
                'CustomTable.belongs_to',
                'CustomTable.has_many',
                'CustomTable.child_tables_fields',
                'CustomTable.form_layout',
                'CustomTable.custom_table_id',
                'QcDocument.id',
            ),
            'conditions'=>array(
                'OR'=>array(
                    'CustomTable.custom_table_id'=>$pdfTemplate['PdfTemplate']['custom_table_id']
                )
            )
        ));
        if($childTables){
            foreach($childTables as $customTable){
                $fields = json_decode($customTable['CustomTable']['fields'],true);
                if($fields){
                    foreach($fields as $field){
                        $result[$customTable['CustomTable']['table_name']][$field['field_name']] = $field['field_label'];
                    }
                }
                if(!$customTable['CustomTable']['custom_table_id']){
                    $result[$customTable['CustomTable']['table_name']]['prepared_by'] = base64_encode('prepared_by');
                    $result[$customTable['CustomTable']['table_name']]['approved_by'] = base64_encode('approved_by');
                    $result[$customTable['CustomTable']['table_name']]['created'] = base64_encode('created');
                    $result[$customTable['CustomTable']['table_name']]['modified'] = base64_encode('modified');
                }
            }
            $this->set('fields',$result);
        }else{

        }
        $this->request->data['PdfTemplate']['child_table_fields'] = $result;
        $this->set('qcDocument',$qcDocument);
        $this->set('customTable',$customTable);
        $key = $this->_generate_onlyoffice_key($pdfTemplate['PdfTemplate']['id'] . date('Ymdhis'));
        $this->set('key',$key);
        $this->set('qcDocument',$qcDocument);
        $this->set('childTables',$childTables);
        $this->set('parentFields',$parentFields);
        $this->set('linkedTosFields',$linkedTosFields);
    }else{

    }
    $customTables = $this->PdfTemplate->CustomTable->find('list', array('conditions' => array('CustomTable.publish' => 1, 'CustomTable.soft_delete' => 0)));
    $this->set('customTables',$customTables);
}

public function save_template() {
    $this->autoRender = false;
    $local = $this->request->params['named'];
    if (($body_stream = file_get_contents("php://input")) === FALSE) {
        echo "Bad Request";
    }
    
    $data = json_decode($body_stream, TRUE);
    if ($data["status"] == 2) {
        $data = json_decode($body_stream, TRUE);
        $record_id = $this->request->params['named']['record_id'];
        $path_for_save = WWW_ROOT . 'files' . DS . $local['company_id'] . DS . 'pdf_template' . DS . $local['record_id'];
        $file_for_save = $path_for_save . DS . 'template.docx';
        $testfolder = new Folder($path_for_save,true, 0777);
        $testfolder->create($path_for_save);
        try{
            chmod($path_for_save, 0777);
        }catch (Exception $e){

        }

        try{
            chmod($file_for_save, 0777);
        }catch (Exception $e){

        }

        $downloadUri = $data["url"];
        if (file_get_contents($downloadUri) === FALSE) {

        } else {
            $new_data = file_get_contents($downloadUri);
            if (file_put_contents($file_for_save, $new_data)) {
                $key = $this->_generate_onlyoffice_key($record_id . date('Ymdhis'));
                $url = Router::url('/', true) . 'files/'.$local['company_id'].'/pdf_template/' . $record_id .'/template.docx' ;
                $html = $this->_convert_to_html($url,'docx','html',null,'Template',$record_id,$local['company_id']);
                // add clean up here
                $html = str_replace('&quot;','"',$html);
                // strip_tags($output,'style');
                $pdfTemplate = $this->PdfTemplate->find('first',array('conditions'=>array('PdfTemplate.id'=>$record_id),'recursive'=>-1));
                if($record_id != 'cover' && (isset($pdfTemplate) && $pdfTemplate['PdfTemplate']['html_cleanup'] == 0)){
                    $html = $this->_html_cleanup($html);
                }
                if($pdfTemplate['PdfTemplate']['template_type'] == 3){
                    $path = Configure::read('files') . DS . 'pdf_template' . DS . 'cover';
                }else{
                    $path = Configure::read('files') . DS . 'pdf_template' . DS .$record_id ;
                }
                $file = $path .DS . "template.html" ;
                $myfile = fopen($file, "w") or die("Unable to open file - 1");
                fwrite($myfile, $html);
                fclose($myfile);
                $pdfTemplate['PdfTemplate']['template'] = $html;
                $this->PdfTemplate->create();
                $this->PdfTemplate->save($pdfTemplate,false);
            } else {
            }
        }
    }
    echo "{\"error\":0}";
}

public function view($id = null){
    if($id){
        $pdfTemplate = $this->PdfTemplate->find('first',array('conditions'=>array('PdfTemplate.id'=>$id)));
        if($pdfTemplate){
            $templateFile = Configure::read('files') . DS .'pdf_template' . DS . $id . DS . 'template.html';
            $filetoread = new File($templateFile);
            $html = $filetoread->read();
            $filetoread->close();
            $this->set('html',$html);
            $this->set('pdfTemplate',$pdfTemplate);
        }else{
            $this->Session->setFlash(__('Template not found', true), 'default', array('class' => 'alert-danger'));
            $this->redirect(array('action' => 'index'));
        }
    }else{
        $this->Session->setFlash(__('Template not found', true), 'default', array('class' => 'alert-danger'));
        $this->redirect(array('action' => 'index'));
    }
}

public function delete($id = null){
    $this->PdfTemplate->delete($id);
    $path = Configure::read('files') . DS . 'pdf_template' . DS .$id ;
    $folderToDelete = new Folder($path);
    $folderToDelete->delete();
    $this->Session->setFlash(__('Template deleted', true), 'default', array('class' => 'alert-success'));
    $this->redirect(array('action' => 'index'));
}

public function _generate_header(){
    $table ="<!DOCTYPE html><html><head>";
    $table .= "<style>
    body{font-family: '\$fontface'; font-size:10px}
    table{background-color: #ccc; border-color: #ccc; font-family:'\$fontface'}
    tr{background-color: #fff; text-align: left;}
    td,th{background-color: #fff; text-align: left;}
    </style></head><body>";
    $table .= "<h2 style=\"font-size:24px;margin-bottom:10px\">\$qcDocument['QcDocument']['title']</h2>";
    $table .= "<table style=\"background-color:#000\" width=\"100%\" border=\"1\" cellspacing=\"1\" cellpadding=\"5\">
    <tr>
    <th style=\"border:1px solid #ccc\">Document Number</th>
    <td style=\"border:1px solid #ccc\">\$qcDocument['QcDocument']['document_number']</td>
    <th style=\"border:1px solid #ccc\">Revision Number</th>
    <td style=\"border:1px solid #ccc\">\$qcDocument['QcDocument']['revision_number']</td>
    <th style=\"border:1px solid #ccc\">Date Of Issue</th>
    <td style=\"border:1px solid #ccc\">\$document['QcDocument']['date_of_issue']</td>
    </tr>
    <tr>
    <th style=\"border:1px solid #ccc\">Prepared By</th>
    <td style=\"border:1px solid #ccc\">\$qcDocument['PreparedBy']['name']</td>
    <th style=\"border:1px solid #ccc\">Approved By</th>
    <td style=\"border:1px solid #ccc\">\$qcDocument['ApprovedBy']['name']</td>
    <th style=\"border:1px solid #ccc\">Issueed By</th>
    <td style=\"border:1px solid #ccc\">\$qcDocument['IssuedBy']['name']</td>
    </tr>
    </table>
    ";
    $table .= "</table></body></html>"; 
    $path = Configure::read('files') . DS . 'pdf_template' . DS . 'header';
    $headerFolder = new Folder();
    $headerFolder->create($path,0777);
    $this->_write_html_file('template',$table,'header');
}

public function _write_html_file($filename = null, $content = null, $record_id = null){
    $path = Configure::read('files') . DS . 'pdf_template' . DS . $record_id;
    $folder = new Folder();
    if ($folder->create($path,0777)) {
    } else {
        echo "Folder creation failed.";
        exit;
    }
    $file = $path . DS . $filename .".html" ;
    $myfile = fopen($file, "w") or die("Unable to open file - 12");
    fwrite($myfile, $content);
    fclose($myfile);
    $url = Router::url('/', true) . 'files/'.$this->Session->read('User.company_id').'/pdf_template/'.$record_id.'/'.$filename.'.html';
    $key = $this->_generate_onlyoffice_key('template'. date('Ymdhis'));
    $html = str_replace('&quot;','"',$html);
    $html = $this->_convert_to_html($url,'html','docx',null,$record_id,$record_id,$this->Session->read('User.company_id'));
}

public function add_cover(){
    $path = Configure::read('files') . DS . 'pdf_template' . DS . 'cover' . DS . 'template.docx';    
    if(file_exists($path)){
        $url = Router::url('/', true) . 'files/'.$this->Session->read('User.company_id').'/pdf_template/cover/template.docx';
        $key = $this->_generate_onlyoffice_key($pdfTemplate['PdfTemplate']['id'] . date('Ymdhis'));
        $this->set('key',$key);
        $this->set('url',$url);
        $this->request->data = $this->PdfTemplate->find('first',array('conditions'=>array('PdfTemplate.template_type'=>3,'PdfTemplate.custom_table_id'=>'PdfTemplate'))); 
        $this->set('cover',$cover);
        $url = Router::url('/', true) . 'files/'.$this->Session->read('User.company_id').'/pdf_template/cover/template.docx' ;
        $cover = $this->PdfTemplate->find('first',array('conditions'=>array('PdfTemplate.template_type'=>3,'PdfTemplate.custom_table_id'=>'PdfTemplate')));
        $this->set('cover',$cover);
    }else{
        $table =$this->_generate_cover();
        $data['PdfTemplate']['name'] = 'cover';
        $data['PdfTemplate']['custom_table_id'] = 'PdfTemplate';
        $data['PdfTemplate']['template_type'] = 3;
        $data['PdfTemplate']['header'] = 0;
        $data['PdfTemplate']['outline'] = 0;
        $data['PdfTemplate']['dpi'] = 360;
        $data['PdfTemplate']['outline_depth'] = 2;
        $data['PdfTemplate']['header_spacing'] = 0;
        $data['PdfTemplate']['footer_left'] = '';
        $data['PdfTemplate']['footer_center'] = '';
        $data['PdfTemplate']['footer_right'] = '';
        $data['PdfTemplate']['footer_font_size'] = '';
        $data['PdfTemplate']['margin_bottom'] = 5;
        $data['PdfTemplate']['margin_left'] = 10;
        $data['PdfTemplate']['margin_right'] = 10;
        $data['PdfTemplate']['margin_top'] = 20;
        $data['PdfTemplate']['html_cleanup'] = 1;
        $data['PdfTemplate']['template'] = $table;
        $this->PdfTemplate->create();
        $this->PdfTemplate->save($data,false);
        $cover = $this->PdfTemplate->find('first',array('conditions'=>array('PdfTemplate.template_type'=>3,'PdfTemplate.custom_table_id'=>'PdfTemplate')));
        $this->set('cover',$cover);
    }
}

public function _generate_cover(){
        // check if cover file exists
        $table ="<!DOCTYPE html>
        <html>
        <head>";
        $table .= "
        <style>
        body{font-family: '\$fontface'; font-size:10px}
        table{background-color: #ccc; border-color: #ccc; font-family:'\$fontface'}
        tr{background-color: #fff; text-align: left;}
        td,th{background-color: #fff; text-align: left;}
        </style>
        </head>
        <body>";
        $table .= "
        <h2 style=\"font-size:24px;margin-bottom:10px\">\$qcDocument['QcDocument']['title']</h2><br /><br /><br /><br /><br /><br />";
        $table .= "
        <table class=\"table table-responsive table-bordered\">
        <thead><h4>Document Details</h4></thead>
        <tbody>
        <tr><th>Document Name</th><td>\$qcDocument['QcDocument']['name']&nbsp;</td></tr>
        <tr><th>Document Number</th><td>\$qcDocument['QcDocument']['document_number']&nbsp;</td></tr>
        <tr><th>Standard</th><td>\$qcDocument['Standard']['name']</td></tr>
        <tr><th>'Clause</th><td>\$qcDocument['Clause']['title']&nbsp;</td></tr> 
        <tr><th>Qc Document Category</th><td>\$qcDocument['QcDocumentCategory']['name']&nbsp;</td></tr> 
        <tr><th>Issue Number</th><td>\$qcDocument['QcDocument']['issue_number']&nbsp;</td></tr>
        <tr><th>Date Of Issue</th><td>\$qcDocument['QcDocument']['date_of_issue']&nbsp;</td></tr>
        <tr><th>Date Of Next Issue</th><td>\$qcDocument['QcDocument']['date_of_next_issue']&nbsp;</td></tr>
        </tr><th>Effective From Date</th><td>\$qcDocument['QcDocument']['effective_from_date']&nbsp;</td></tr>
        </tr><th>Date Of Review</th><td>\$qcDocument['QcDocument']['date_of_review']&nbsp;</td></tr>
        </tr><th>Revision Date</th><td>\$qcDocument['QcDocument']['revision_date']&nbsp;</td></tr>
        </tr><th>Document Type</th><td>\$customArray['documentTypes'][\$qcDocument['QcDocument']['document_type']]&nbsp;</td></tr>
        </tr><th>Document Security</th><td>\$customArray['itCategories'][\$qcDocument['QcDocument']['it_categories']]&nbsp;</td></tr>
        </tr><th>Issued By</th><td>\$qcDocument['IssuedBy']['name']&nbsp;</td></tr>
        <tr><th>Issuing Authority</th><td>\$qcDocument['IssuingAuthority']['name']&nbsp;</td></tr>
        <tr><th>Prepared By</th><td>\$qcDocument['PreparedBy']['name']&nbsp;</td></tr>
        <tr><th>Approved By</th><td>\$qcDocument['ApprovedBy']['name']&nbsp;</td></tr>
        </tbody>
        </table>
        ";
        $table .= "
        </body>
        </html>"; 

        $path = Configure::read('files') . DS . 'pdf_template' . DS . 'cover';
        $headerFolder = new Folder();
        $headerFolder->create($path,0777);
        $this->_write_html_file('template',$table,'cover');
        return $table;
    }
}
