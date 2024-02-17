<div id="qcDocuments_ajax">
	<style type="text/css">
		#generate_pdf .fa-spin{
			margin-top: 3px;
			margin-left: 8px;
		}	
		.control-fileupload{
			margin-top: 0;
		}
	</style>				
	<?php echo $this->Session->flash();?>	
	<?php echo $this->element('nav-header-lists',array('postData'=>array('pluralHumanName'=>'View Qc Documents','modelClass'=>'QcDocument','options'=>array("sr_no"=>"Sr No","document_number"=>"Document Number","reference_number"=>"Reference Number","issue_number"=>"Issue Number","date_of_next_issue"=>"Date Of Next Issue","date_of_issue"=>"Date Of Issue","effective_from_date"=>"Effective From Date","revision_number"=>"Revision Number","date_of_review"=>"Date Of Review","revision_date"=>"Revision Date","document_type"=>"Document Type","it_categories"=>"It Categories","document_status"=>"Document Status","issued_by"=>"Issued By","archived"=>"Archived","change_history"=>"Change History","cr_status"=>"Cr Status","mark_for_cr_update"=>"Mark For Cr Update","temp_date_of_issue"=>"Temp Date Of Issue","temp_effective_from_date"=>"Temp Effective From Date","linked_formats"=>"Linked Formats","cover_page"=>"Cover Page","page_orientation"=>"Page Orientation"),'pluralVar'=>'qcDocuments'))); ?>

	<div class="">
		<div class="row">
			<div class="col-md-8">
				<h3><?php echo h($qcDocument['QcDocument']['name']); ?> <small>.<?php echo h($qcDocument['QcDocument']['file_type']); ?></small><br />
					<small><?php echo $qcDocument['Standard']['name']; ?> / <?php echo h($qcDocument['Clause']['title']); ?> / <?php echo h($qcDocument['QcDocument']['document_number']); ?>  / <?php echo h($qcDocument['QcDocument']['revision_number']); ?></small></h3>
				</div>
				<div class="col-md-4 text-right hide"><br /><br />
					<div id="downloadpdf">
						<div class="btn btn-sm btn-info" id="generate_pdf"><i class="fa fa-file-pdf-o"></i>&nbsp;&nbsp;&nbsp;Generate PDF <i class="fa fa-refresh fa-spin hide"></i></div>
						<script type="text/javascript">
							$("#generate_pdf").on('click',function(){
								$(".fa-spin").removeClass('hide').addClass('show').addClass('pull-right');
								$.ajax({
									url: "<?php echo Router::url('/', true); ?><?php echo $this->request->params['controller'] ?>/download_pdf/<?php echo $qcDocument['QcDocument']['id']?>",
									success: function(data, result) {						    		
										$(".fa-spin").removeClass('show').addClass('hide');
										$("#downloadpdf").html(data);
									},						        
								});	
							});
						</script>	
					</div>
				</div>
			</div>
			<div class="view">
				<ul class="nav nav-tabs docs" role="tablist">			
					<li class="active"><a href="#document" data-toggle="tab">Details</a></li>
					<?php if($qcDocument['QcDocument']['file_type']){ ?>
						<li ><a href="#view_document" data-toggle="tab">Document</a></li>
					<?php }else{ ?>
						<li ><a style="background-color: #ff6060 !important;" href="#view_document" data-toggle="tab">Add Document</a></li>
					<?php } ?>					
					<li ><a href="#childdocument" data-toggle="tab">Child Documents <span class="badge"><?php echo count($childDocs)?></span></a></li>			  
					<li><a href="#tab_3" onclick="loadchangehistory()" data-toggle="tab">Change Requests <span class="badge"><?php echo $crs?></span></a></li>
					<li><a href="#tab_2" onclick="loadrevisions()" data-toggle="tab">Revisons <span class="badge"><?php echo $qcDocument['QcDocument']['revision_number']?></span></a></li>
					<li><a href="#tab_4" data-toggle="tab">Upload Additional Docs</a></li>
					<li><a href="#tab_5" data-toggle="tab">Read Receipts</a></li>
					<li><a href="#tab_6" data-toggle="tab">Downloads</a></li>
					<li ><?php echo $this->Html->link('Add Child&nbsp;&nbsp;<i class="fa fa-external-link"></i>',array('action'=>'add','parent_id'=>$qcDocument['QcDocument']['id']),array('escape'=>false));?></li>
				</ul>
				<div class="tab-content">
					
					<div class="tab-pane active" id="document">	  	
						<?php echo $this->element('document_view',array(
							'qcDocument'=>$qcDocument
						));?>
					</div>

					<div class="tab-pane" id="childdocument">	  
						<?php echo $this->element('childdocuments',array('childDocs'=>$childDocs));?>	  		
					</div>
					
					<div class="tab-pane" id="tab_2">
						<h4>Document Revision Hstory</h4>	
						
					</div>
					<div class="tab-pane" id="tab_3">
						<h4>Document Change Requestes</h4>
					</div>
					<div class="tab-pane" id="tab_4">
						<?php echo $this->element('fileuploads',array('id'=>$qcDocument['QcDocument']['id']));?>
					</div>	
					<div class="tab-pane" id="tab_5">
						<script type="text/javascript">
							$().ready(function(){
								$("#tab_5").load("<?php echo Router::url('/', true); ?>histories/read_receipts/<?php echo $qcDocument['QcDocument']['id'];?>");
							});
						</script>
					</div>
					<div class="tab-pane" id="tab_6">
						<script type="text/javascript">
							$().ready(function(){
								$("#tab_6").load("<?php echo Router::url('/', true); ?>document_downloads/index/<?php echo $qcDocument['QcDocument']['id'];?>");
							});
						</script>
					</div>
					<div class="tab-pane" id="view_document">
						<div class="row">
							
							<?php

							if($qcDocument['QcDocument']['archived'] == true){
								

								$url = WWW_ROOT . DS . 'files' . DS . $this->Session->read('User.company_id') . DS . 'archive' . DS . $qcDocument['QcDocument']['parent_id'] . DS . $qcDocument['QcDocument']['revision_number'] . DS . $qcDocument['QcDocument']['cr_id'] . DS . 'archived.' . $qcDocument['QcDocument']['file_type'];
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

								$mode = 'view';

								echo '<div class="col-md-12"><h4>Loading Archived Document</h4>'. $this->element('onlyoffice_load_archived',array(
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
									'preparedby'=>$qcDocument['PreparedBy']['name'],
									'filekey'=>$key,            
									'record_id'=>$qcDocument['QcDocument']['id'],
									'company_id'=>$this->Session->read('User.company_id'),
									'controller'=>$this->request->controller,
									'last_saved'=>$qcDocument['QcDocument']['last_saved'],
									'last_modified' => $qcDocument['QcDocument']['modified'],
									'version_keys' => $qcDocument['QcDocument']['version_keys'],
									'version' => $qcDocument['QcDocument']['version'],
									'versions' => $qcDocument['QcDocument']['versions'],
									'docid'=> $qcDocument['QcDocument']['id']
								)) . '</div>';

							}else{

								// load regular file						

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

								if(file_exists(WWW_ROOT . DS . 'files' . DS . $this->Session->read('User.company_id') . DS . 'qc_documents' . DS . $file_path . DS . $file)){
									echo '<div class="col-md-12">'. $this->element('onlyoffice',array(
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
										'preparedby'=>$qcDocument['PreparedBy']['name'],
										'filekey'=>$key,            
										'record_id'=>$qcDocument['QcDocument']['id'],
										'company_id'=>$this->Session->read('User.company_id'),
										'controller'=>$this->request->controller,
										'last_saved'=>$qcDocument['QcDocument']['last_saved'],
										'last_modified' => $qcDocument['QcDocument']['modified'],
										'version_keys' => $qcDocument['QcDocument']['version_keys'],
										'version' => $qcDocument['QcDocument']['version'],
										'versions' => $qcDocument['QcDocument']['versions'],
										'docid'=> $qcDocument['QcDocument']['id']
									)) .'</div>';
								}else{
									echo "<br /><br />";
									echo $this->Form->create('QcDocument',array('action'=>'upload_qc_document','type'=>'file',),array('role'=>'form','class'=>'form'));
									echo '<div class="col-md-8">'.$this->Form->hidden('file_type',array()).'</div>';?>
									<div class="col-md-8">
										<span class='control-fileupload'><i class='fa fa-file-o'></i>
											<?php echo $this->Form->input('file',array('type'=>'file', 'label'=>'Upload Document', 'div'=>false , 'onchange'=>'$(this).prev("label").html($(this).val().substr(12));'));?>
										</span>
									</div>
									<?php 
									echo '<div class="col-md-4">'. $this->Form->submit('Upload File',array('class'=>'btn btn-sm btn-info')).'</div>';
									echo $this->Form->input('id',array('default'=>$qcDocument['QcDocument']['id']));
									echo $this->Form->end();
								}	
							}
							
							?>
<script type="text/javascript">
	function addfiletype(filetype){
		$("#QcDocumentFileType").val(filetype);	
		$("#filetypediv").hide();
	}


	function loadchangehistory(){
		$("#tab_3").load("<?php echo Router::url('/', true); ?>/qc_documents/change_history/id:<?php echo $qcDocument['QcDocument']['id'];?>");
	}

	function loadrevisions(){
		$("#tab_2").load("<?php echo Router::url('/', true); ?>/qc_documents/load_revisions/id:<?php echo $qcDocument['QcDocument']['id'];?>");
	}

	$().ready(function(){
		$("#QcDocumentFile").on('change',function(){			
			let pathext = this.value;
			const pathfile = pathext.split(".");
			const arr = pathfile.length;
			const ext = pathfile[arr-1];
			
			var earr = ["doc","docx","xls","xlsx","pdf","ppt","odt","txt"];
			if($.inArray(ext,earr) > -1){
				
			}else{
				alert('This file type is not allowed : ' + ext);
				location.reload(true);
			}

			let path = pathfile[arr-2];
			const file = path.split("\\");
			const filename  = file[file.length - 1];

			var str = filename;
			
			if (!isNaN(str[0]) && !isNaN(str[str.length - 1])) {
				this.value = "";
				alert('File with onlynumbers is not allowed.');
				location.reload(true);
			} else {
				
			}
			$("#QcDocumentTitle").val(filename);
			addfiletype(ext);
		});
	})
</script>							
						</div>
					</div>


				</div>	

				<div class="tab-pane" id="read_receipts">
				</div>		
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">		
			<?php if($this->request->params['named']['approval_id'])echo $this->element('approval_form',array('approval'=>$approval));?>
			<?php echo $this->element('approval_history',array('approvals'=>$approvals,'current_approval'=>$this->request->params['named']['approval_id'],'approvalComments',$approvalComments));?>
		</div>
	</div>
	<?php echo $this->Js->writeBuffer();?>
</div>
<script>$.ajaxSetup({beforeSend:function(){$("#busy-indicator").show();},complete:function(){$("#busy-indicator").hide();}});</script>
