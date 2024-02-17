<?php 
if($this->request->params['named']['showdocs_mode'] == 0)$mode = 'view';
else $mode = 'edit';	
if($fileEdit){ 	
	if($is_qc == false){
		$key = $fileEdit['File']['file_key'];
		$file_type = $fileEdit['File']['file_type'];
	    $file_name = $fileEdit['File']['name'];
	    
	    if($file_type == 'doc' || $file_type == 'docx'){
	        $documentType = 'word';
	    }

	    if($file_type == 'xls' || $file_type == 'xlsx'){
	        $documentType = 'cell';
	    }
	    // $mode = 'edit';
	    $file_path = $fileEdit['File']['id'];
	    $file = $file_name.'.'.$file_type;
	    
	    echo $this->element('onlyoffice',array(
	            'url'=>$url,
	            'user_id'=>$user_id,
	            'placeholderid'=>$placeholderid,
	            'panel_title'=>'Document Viewer',
	            'mode'=>$mode,
	            'path'=>$file_path,
	            'file'=>$file,
	            'filetype'=>$file_type,
	            'documentType'=>$documentType,
	            'userid'=>$this->Session->read('User.id'),
	            'username'=>$this->Session->read('User.username'),
	            'preparedby'=>$this->Session->read('User.name'),
	            'filekey'=>$key,
	            'record_id'=>$fileEdit['File']['id'],
	            'company_id'=>$this->Session->read('User.company_id'),
	            'controller'=>'files',
	            'last_saved' => $fileEdit['File']['last_saved'],
            	'last_modified' => $fileEdit['File']['modified'],
            	'version_keys' => $fileEdit['File']['version_keys'],
            	'version' => null,
				'versions' => null,
				'docid'=> $fileEdit['File']['id']
	        ));

	}else{
		
		$key = $fileEdit['File']['file_key'];
		$file_type = $fileEdit['File']['file_type'];
		$file_name = $fileEdit['File']['title'];
		$document_number = $fileEdit['File']['document_number'];
		$document_version = $fileEdit['File']['revision_number'];

		$file_type = $fileEdit['File']['file_type'];

		if($file_type == 'doc' || $file_type == 'docx'){
			$documentType = 'word';
		}

		if($file_type == 'xls' || $file_type == 'xlsx'){
			$documentType = 'cell';
		}

		$mode = 'edit';

		$file_path = $fileEdit['File']['id'];


		// $file = $document_number.'-'.$file_name.'-'.$document_version.'.'.$file_type;
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

		?>
		<div class="row">
		<div class="col-md-12">
			<?php						
		        
				echo $this->element('onlyoffice_for_extra',array(
		            'url'=>$url,
					'placeholderid'=>0,
					'panel_title'=>'Document Viewer',
					'mode'=>$mode,
					'path'=>$file_path,
					'file'=>$file,
					'filetype'=>$file_type,
					'documentType'=>$documentType,
					'userid'=>$this->Session->read('User.id'),
					'username'=>$this->Session->read('User.username'),
					'preparedby'=>$fileEdit['PreparedBy']['name'],
					'filekey'=>$key,            
					'record_id'=>$file['File']['id'],
					'company_id'=>$this->Session->read('User.company_id'),
					'controller'=>'qc_documents',
					'last_saved'=>$file['File']['last_saved'],
					'last_modified' => $file['File']['modified'],
					'version_keys' => $file['File']['version_keys'],
					'version' => $this->data['QcDocument']['version'],
					'versions' => $this->data['QcDocument']['versions'],
					'docid'=> $file['File']['id'],
					'qcDocument' => $fileEdit
		        ));
			?>
		</div>
	</div>
	<?php }?>	
<?php } ?>

<?php echo $this->Form->hidden('additional_files',array('name'=>'data['.Inflector::Classify($this->request->controller).'][additional_files][]','default'=>$fileEdit['File']['id']));?>