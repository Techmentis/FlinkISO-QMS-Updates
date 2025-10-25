<style type="text/css">
	.ui-tabs .ui-tabs-panel{padding: 0 15px !important;}
</style>
<div id="qcDocuments_ajax">
	<div class="row">
		<div class="col-md-12">
			<?php echo $this->element('qc_doc_header',array('document',$qcDocument));?>
		</div>
	</div>
</div>
<div class="tab-pane" id="view_document">
	<div class="row">
		<div class="col-md-12">
			<?php 				
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

			$mode = 'view';

			$file_path = $qcDocument['QcDocument']['id'];


    		// $file = $document_number.'-'.$file_name.'-'.$document_version.'.'.$file_type;
			$file = $document_number.'-'.$file_name.'-'.$document_version;
			// $file = ltrim(rtrim($file));
			// $file = str_replace('-', '_', $file);
			// $file = ltrim(rtrim(strtolower($file)));
			// $file = preg_replace('/[\@\.\;\" "-]+/', '_', $file);
			// $file = preg_replace('/  */', '_', $file);
			// $file = preg_replace('/\\s+/', '_', $file);        
			// $file = preg_replace('/-*-/', '_', $file);
			// $file = preg_replace('/_*_/', '_', $file);
			$file = $this->requestAction(array('action'=>'clean_table_names',$file));
			$file = $file.'.'.$file_type;

			echo $this->element('onlyoffice',array(
				'url'=>$url,
				'placeholderid'=>0,
				'panel_title'=>'Document Viewer',
				'mode'=>'view',
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
				'last_saved' => $qcDocument['QcDocument']['last_saved']
			));
			?>
		</div>
		<div class="col-md-12">
			<div class="btn-group">
				<?php echo $this->Html->link('View',array('controller'=>'qc_documents','action'=>'view',$qcDocument['QcDocument']['id']),array('class'=>'btn btn-sm btn-info','target'=>'_blank'));?>
				<?php echo $this->Html->link('Edit',array('controller'=>'qc_documents','action'=>'edit',$qcDocument['QcDocument']['id']),array('class'=>'btn btn-sm btn-warning','target'=>'_blank'));?>
			</div>
		</div>
	</div>
</div>
