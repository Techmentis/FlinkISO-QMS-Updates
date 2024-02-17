
<div class="row">
	<div class="col-md-12">		<?php         
		$key = $qcDocument['QcDocument']['file_key'];
		$file_type = $qcDocument['QcDocument']['file_type'];
        $file_name = $qcDocument['QcDocument']['title'];
        $document_number = $qcDocument['QcDocument']['document_number'];
        $document_version = $qcDocument['QcDocument']['revision_number'];

        $file_type = $qcDocument['QcDocument']['file_type'];
        
        if($file_type == 'doc' || $file_type == 'docx'){
            $documentType = 'word';
        }

        if($file_type == 'xls' || $file_type == 'xlsx'){
            $documentType = 'cell';
        }

        $editors = json_decode($this->request->data['QcDocument']['editors'],true);
        if($editors){
            if(in_array($this->Session->read('User.id'), $editors)){
                $mode = 'edit'; 
            }else{
                $mode = 'view';
            }    
        }else{
            if($this->action == 'edit'){
                $mode = 'edit'; 
            }else{
                $mode = 'view';
            }    
        }
        

        

        $file_path = $qcDocument['QcDocument']['id'];

        $file = $document_number.'-'.$file_name.'-'.$document_version;
        $file = ltrim(rtrim($file));
        $file = str_replace('-', '_', $file);
        $file = ltrim(rtrim(strtolower($file)));
        $file = preg_replace('/[\@\.\;\" "-]+/', '_', $file);
        $file = preg_replace('/  */', '_', $file);
        $file = preg_replace('/\\s+/', '_', $file);        
        $file = preg_replace('/-*-/', '_', $file);
        $file = preg_replace('/_*_/', '_', $file);

        $file = $file.'.'.$file_type;

        echo $this->element('onlyoffice',array(
            'url'=>$url,
            'placeholderid'=>$placeholderid,
            'panel_title'=>'Document Viewer',
            'mode'=>$mode,
            'path'=>$file_path,
            'file'=>$file,
            'filetype'=>$file_type,
            'documentType'=>$documentType,
            'userid'=>$this->Session->read('User.id'),
            'username'=>$this->Session->read('User.username'),
            'preparedby'=>$masterListOfFormat['PreparedBy']['name'],
            'filekey'=>$key,            
            'record_id'=>$qcDocument['QcDocument']['id'],
            'company_id'=>$this->Session->read('User.company_id'),
            'controller'=>$this->request->controller,
            'last_saved' => $qcDocument['QcDocument']['last_saved'],
            'last_modified' => $qcDocument['QcDocument']['modified'],
            'version_keys' => $qcDocument['QcDocument']['version_keys'],
            'version' => $qcDocument['QcDocument']['version'],
            'versions' => $qcDocument['QcDocument']['versions'],
            'docid'=> $qcDocument['QcDocument']['id']
        ));
        ?>
    </div>
</div>
