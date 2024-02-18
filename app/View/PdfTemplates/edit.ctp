<?php echo $this->element('nav-header-lists',array('postData'=>array('pluralHumanName'=>'PDF Templates','modelClass'=>'PdfTemplate','options'=>array("sr_no"=>"Sr No","name"=>"Name"),'pluralVar'=>'pdfDepartments'))); ?>
<style type="text/css">
	.txtfld{width: 96%; border: none; margin: 5px 0}
	.childtables ul { list-style-type: none; margin: 0; padding: 0; margin-bottom: 10px; }
  	.childtables li { margin: 5px; padding: 5px; width: 150px; border:1px solid #ccc; width:100% }
  	.preview table{width: 100% !important}
</style>
<?php echo $this->Form->create('PdfTemplate',array('action'=>'edit'),array('class'=>'form-control')) ?>
<div class="main">
	<div class="row">				
		<?php if($header == false){ ?>
			<div class="col-md-4"><?php echo $this->Form->input('id',array('class'=>'form-control'));	 ;?><?php echo $this->Form->input('name',array('class'=>'form-control'));	 ;?></div>
			<div class="col-md-4"><?php echo $this->Form->input('custom_table_id',array('options'=>$customTables, 'default'=>$this->request->params['pass'][0]));?></div>
			<div class="col-md-4">
				<?php 
					$templateType = array('Content','Header');
					echo $this->Form->input('template_type',array('type'=>'radio', 'class'=>'radio-input','default'=>0, 'options'=>$templateType));	
				?>
			</div>
			<div class="col-md-2">
				<?php 
					$yesNos = array('Yes','No');
					echo $this->Form->input('header',array('type'=>'radio', 'class'=>'radio-input','default'=>0,'options'=>$yesNos));	
				?>
			</div>
			<div class="col-md-2">
				<?php 
					$yesNos = array('Yes','No');
					echo $this->Form->input('outline',array('type'=>'radio', 'class'=>'radio-input','default'=>1, 'options'=>$yesNos));	
				?>
			</div>					
			<div class="col-md-2"><?php echo $this->Form->input('outline_depth',array('class'=>'form-control','default'=>'2'));?></div>
			<div class="col-md-2"><?php echo $this->Form->input('dpi',array('class'=>'form-control','default'=>'360'));?></div>			
			<div class="col-md-2"><?php echo $this->Form->input('header_spacing',array('class'=>'form-control','default'=>'0'));?></div>
			<div class="col-md-2"><?php echo $this->Form->input('footer_left',array('class'=>'form-control','default'=>''));?></div>
		</div>
		<div class="row">				
			<div class="col-md-2"><?php echo $this->Form->input('footer_center',array('class'=>'form-control','default'=>'Page [page] of [toPage]'));?></div>
			<div class="col-md-2"><?php echo $this->Form->input('footer_font_size',array('class'=>'form-control','default'=>'6'));?></div>

			<div class="col-md-2"><?php echo $this->Form->input('margin_bottom',array('class'=>'form-control','default'=>'5'));?></div>
			<div class="col-md-2"><?php echo $this->Form->input('margin_left',array('class'=>'form-control','default'=>'10'));?></div>
			<div class="col-md-2"><?php echo $this->Form->input('margin_right',array('class'=>'form-control','default'=>'10'));?></div>
			<div class="col-md-2"><?php echo $this->Form->input('margin_top',array('class'=>'form-control','default'=>'20'));?></div>
		<div class="col-md-2 con">
				<?php 
					$yesNos = array('Yes','No');
					echo $this->Form->input('html_cleanup',array('type'=>'radio', 'class'=>'radio-input','default'=>1, 'options'=>$yesNos,));	
				?>
		</div>	
		<div class="col-md-12 con">
			Selecting Yes will clean the HTML generated by converting Docx file to HTML. It will remove all the tags like styles, span etc and keep the bare minimum HTML.
		</div>
		<?php }	?>			
		<?php if($header == true){ 
			echo '<div class="col-md-12 "><h5>HTML Preview</h5>'.$headerFileHtml.'</div>';

			$font_face = array('Arial'=>'Arial','Times New Roman'=>'Times New Roman','Tahoma'=>'Tahoma','Helvetica'=>'Helvetica');
			$edittype = array('Doc'=>'Document','Html'=>'HTML');
			?>
			<div class="col-md-4 "><?php echo $this->Form->input('PdfTemplates.font_size',array('default'=> 11 , 'class'=>'form-control'));?></div>
			<div class="col-md-4 "><?php echo $this->Form->input('PdfTemplates.font_face',array('options'=> $font_face, 'default'=>'Arial', 'class'=>'form-control'));?></div>
			<div class="col-md-4 "><?php echo $this->Form->input('PdfTemplates.Type',array('options'=> $edittype, 'default'=>'Doc', 'class'=>'form-control'));?></div>
		<?php }?>
		<div class="col-md-12">

			<?php
			$file_path = Configure::read('files') . DS. 'pdf_template' . DS . $this->request->data['PdfTemplate']['id']. 'template.docx';
			$file = 'template.docx';
			$documentType = 'word';

			echo $this->element('onlyoffice',array(
				'url'=>$url,
				'placeholderid'=>$placeholderid,
				'panel_title'=>'Template Viewer',
				'mode'=>'edit',
				'path'=>$file_path,
				'file'=>$file,
				'filetype'=>'docx',
				'documentType'=>'',
				'userid'=>$this->Session->read('User.id'),
				'username'=>$this->Session->read('User.username'),
				'preparedby'=>$this->Session->read('User.name'),
				'filekey'=>$key,
				'record_id'=>$this->request->data['PdfTemplate']['id'],
				'company_id'=>$this->Session->read('User.company_id'),
				'controller'=>'pdf_templates',
				'last_saved'=>$qcDocument['QcDocument']['last_saved'],
				'last_modified' => $this->request->data['PdfTemplate']['modified'],
				'version_keys' => $this->request->data['PdfTemplate']['version_keys'],
				'version' => $this->request->data['PdfTemplate']['version'],
				'versions' => $this->request->data['PdfTemplate']['versions'],
				'docid'=> $this->request->data['PdfTemplate']['id']
			));

			?>	
			<p>Do not add header and footer to the document. Header & Footer is generated seperatly and added in runtime</p>
		</div>	
		<div class="col-md-12"><h3>Available Fields <small>Copy-Paste fields from below in a document where you want the values to be displayed.</small></h3>
			<div class="row">
				<div class="col-md-4">
					<div class="panel panel-default">
						<div class="panel-heading"><h5>QC Document: <?php echo $qcDocument['QcDocument']['title'];?></h5></div>
						<div class="panel-body">							
							<?php
							$qcFields = array("title","document_number","document_number","issue_number","date_of_next_issue","date_of_issue","effective_from_date","revision_number","date_of_review","revision_date");
							foreach($qcFields as $qcField){
								$jid = 'QcDocument'.Inflector::camelize($qcField);
								echo $this->Form->input('data.PdfTempate.QcDocument.'.$qcField,array('type'=>'text', 'id'=>$jid, 'class'=>'txtfld','div'=>false,'label'=>false, 'default'=>'$qcDocument["QcDocument"]["'.$qcField.'"]')) .'<i class="fa fa-copy" onclick="myFunction(\''.$jid.'\')"></i>';
							}
								echo $this->Form->input('data.PdfTempate.Standard.name',array('type'=>'text', 'id'=>'PdfTemplateStandardName', 'class'=>'txtfld','div'=>false,'label'=>false, 'default'=>'$qcDocument["Standard"]["name"]')) .'<i class="fa fa-copy" onclick="myFunction(\'PdfTemplateStandardName\')"></i>';
								
								echo $this->Form->input('data.PdfTempate.Clause.name',array('type'=>'text', 'id'=>'PdfTemplateClausedName', 'class'=>'txtfld','div'=>false,'label'=>false, 'default'=>'$qcDocument["Clause"]["name"]')) .'<i class="fa fa-copy" onclick="myFunction(\'PdfTemplateClausedName\')"></i>';

								echo $this->Form->input('data.PdfTempate.PreparedBy.name',array('type'=>'text', 'id'=>'PdfTemplatePreparedByName', 'class'=>'txtfld','div'=>false,'label'=>false, 'default'=>'$qcDocument["PreparedBy"]["name"]')) .'<i class="fa fa-copy" onclick="myFunction(\'PdfTemplatePreparedByName\')"></i>';

								echo $this->Form->input('data.PdfTempate.ApprovedBy.name',array('type'=>'text', 'id'=>'PdfTemplateApprovedByName', 'class'=>'txtfld','div'=>false,'label'=>false, 'default'=>'$qcDocument["ApprovedBy"]["name"]')) .'<i class="fa fa-copy" onclick="myFunction(\'PdfTemplateApprovedByName\')"></i>';

								echo $this->Form->input('data.PdfTempate.IssuedBy.name',array('type'=>'text', 'id'=>'PdfTemplateIssuedByByName', 'class'=>'txtfld','div'=>false,'label'=>false, 'default'=>'$qcDocument["IssuedBy"]["name"]')) .'<i class="fa fa-copy" onclick="myFunction(\'PdfTemplateIssuedByByName\')"></i>';
							?>							
						</div>
						<div class="panel-footer"><strong>Note:</strong> You can fetch these values anywhere in a template.</div>
					</div>
				</div>
				<div class="col-md-4">
					<div class="panel panel-default">
						<div class="panel-heading"><h5>Main Table: <?php echo $this->request->data['CustomTable']['table_name'];?></h5></div>
						<div class="panel-body">							
							<?php
							foreach(json_decode($this->request->data['CustomTable']['fields'],true) as $mainField){
								if($mainField['linked_to'] == -1){
									echo  $this->Form->input(Inflector::classify($this->request->data['CustomTable']['table_name']).'.'.$mainField['field_name'],array('type'=>'text', 'class'=>'txtfld','div'=>false,'label'=>false, 'default'=>'$record["'. Inflector::classify($this->request->data['CustomTable']['table_name']).'"]["'.$mainField['field_name'].'"]')) .'<i class="fa fa-copy" onclick="myFunction(\''.Inflector::classify($this->request->data['CustomTable']['table_name']).Inflector::classify($mainField['field_name']).'\')"></i>';
								}else{
									$defaultField = $this->requestAction(array('action'=> 'get_default',Inflector::classify($mainField['linked_to'])));
									echo  $this->Form->input(Inflector::classify($mainField['field_name']).'.'.$defaultField,array('type'=>'text', 'class'=>'txtfld','div'=>false,'label'=>false, 'default'=>'$record["'. Inflector::classify($mainField['field_name']).'"]["'.$defaultField.'"]')) .'<i class="fa fa-copy" onclick="myFunction(\''.Inflector::classify($mainField['field_name']).Inflector::classify($defaultField).'\')"></i>';
								}
								
							}
							echo  $this->Form->input('PreparedBy.name',array('type'=>'text', 'class'=>'txtfld','div'=>false,'label'=>false, 'default'=>'$record["PreparedBy"]["name"]')) .'<i class="fa fa-copy" onclick="myFunction(\'PreparedByName\')"></i>';
							echo  $this->Form->input('ApprovedBy.name',array('type'=>'text', 'class'=>'txtfld','div'=>false,'label'=>false, 'default'=>'$record["ApprovedBy"]["name"]')) .'<i class="fa fa-copy" onclick="myFunction(\'ApprovedByName\')"></i>';
							?>							
						</div>
						<div class="panel-footer"><strong>Note:</strong> You can fetch these values anywhere in a template.</div>
					</div>
				</div>	
				<?php foreach($linkedTosFields as $model => $fields){ ?>
					<div class="col-md-4">
						<div class="panel panel-default">
							<div class="panel-heading"><h5>Linked Table Linked Fields: <?php echo $model;?></h5></div>
							<div class="panel-body">							
								<?php
								$skipFields = array('prepared_by','approved_by','modified_by','publish','modified');
								foreach($fields as $field){
									 if(!in_array($field, $skipFields)){
									 	echo  $this->Form->input($model.'.'.$field,array('type'=>'text', 'class'=>'txtfld','div'=>false,'label'=>false, 'default'=>'$record["'. $model.'"]["'.$field.'"]')) .'<i class="fa fa-copy" onclick="myFunction(\''.$model.Inflector::classify($field).'\')"></i>';			
									 }else{
									 		if($field == 'prepared_by'){
												$model = 'PreparedBy';
												$field = 'name';
												echo  $this->Form->input($model.'.name',array('type'=>'text', 'class'=>'txtfld','div'=>false,'label'=>false, 'default'=>'$record["'. $model.'"]["name"]')) .'<i class="fa fa-copy" onclick="myFunction(\''.$model.'Name'.'\')"></i>';			
											}

											if($field == 'modified_by'){
												$model = 'ModifiedBy';
												$field = 'name';
												echo  $this->Form->input($model.'.name',array('type'=>'text', 'class'=>'txtfld','div'=>false,'label'=>false, 'default'=>'sad')) .'<i class="fa fa-copy" onclick="myFunction(\''.$model.'Name'.'\')"></i>';			
											}
											if($field == 'approved_by'){
												$model = 'ApprovedBy';
												$field = 'name';
												echo  $this->Form->input($model.'.name',array('type'=>'text', 'class'=>'txtfld','div'=>false,'label'=>false, 'default'=>'$record["'. $model.'"]["name"]')) .'<i class="fa fa-copy" onclick="myFunction(\''.$model.'Name'.'\')"></i>';			
											}											
									 }									
								}
								?>							
							</div>
							<div class="panel-footer"><strong>Note:</strong> You can fetch these values anywhere in a template.</div>
						</div>
					</div>	
				<?php }?>
							

				<?php 
				$existingChildTables =json_decode($this->request->data['PdfTemplate']['child_table_fields'],true);
				if($existingChildTables){
					foreach($existingChildTables as $childTables){ 
						$childTable = json_decode($childTables,true);
						$childTable['CustomTable']['table_name'] = $childTable['name'];
						$childTable['CustomTable']['id'] = $childTable['id'];
						$childTable['CustomTable']['fields'] = explode(',', $childTable['fields']);
						?>

						<div class="col-md-4">
							<div class="panel panel-default">
								<div class="panel-heading"><h5>Child Table: <?php echo $childTable['CustomTable']['table_name'];?></h5></div>
								<div class="panel-body childtables">
									<ul id="<?php echo $childTable['CustomTable']['table_name'];?>">							
										<?php foreach($childTable['CustomTable']['fields'] as $mailField){
											if($mailField)echo  '<li class="ui-state-default">'.$mailField .'</li>';
										}									
										?>							
									</ul>
								</div>
								<div class="panel-footer"><strong>Note:</strong> This is a looped table. Values of this table can not be fetched outside this table.
									<?php echo $this->Form->hidden('PdfTemplate.child_table_fields.'.$c,array('type'=>'text'));?>
								</div>
							</div>
						</div>
					</div>
					<script type="text/javascript">
						$( "#<?php echo $childTable['CustomTable']['table_name'];?>" ).sortable({
					      revert: true,
					      stop:function(){
					      	var str = ''
					      	$("#<?php echo $childTable['CustomTable']['table_name'];?> li").each(function(){
					      		str = str + this.innerHTML + ',';
					      	});
					      	<?php 
					      		$tbl = '{"id":"'.$childTable['CustomTable']['id'].'","name":"'.$childTable['CustomTable']['table_name'].'"';
					      	?>
					      	
					      	$("#PdfTemplateChildTableFields<?php echo $c;?>").val('<?php echo $tbl;?>,"fields":"'+str+'"}');
					      }
					    });
							
						$().ready(function(){
							var str = ''
					      	$("#<?php echo $childTable['CustomTable']['table_name'];?> li").each(function(){
					      		str = str + this.innerHTML + ',';
					      	});
					      	<?php 
					      		$tbl = '{"id":"'.$childTable['CustomTable']['id'].'","name":"'.$childTable['CustomTable']['table_name'].'"';
					      	?>
					      	
					      	$("#PdfTemplateChildTableFields<?php echo $c;?>").val('<?php echo $tbl;?>,"fields":"'+str+'"}');
						})				    
					</script>
				<?php $c++; } 
				}else{
					$c = 0;
					foreach($childTables as $childTable){ ?>

						<div class="col-md-4">
							<div class="panel panel-default">
								<div class="panel-heading"><h5>Child Table: <?php echo $childTable['CustomTable']['table_name'];?></h5></div>
								<div class="panel-body childtables">
									<ul id="<?php echo $childTable['CustomTable']['table_name'];?>">							
										<?php foreach(json_decode($childTable['CustomTable']['fields'],true) as $mailField){
											echo  '<li class="ui-state-default">'.$mailField['field_name'] .'</li>';
										}									
										?>							
									</ul>
								</div>
								<div class="panel-footer"><strong>Note:</strong> This is a looped table. Values of this table can not be fetched outside this table.
									<?php echo $this->Form->hidden('PdfTemplate.child_table_fields.'.$c,array('type'=>'text'));?>
								</div>
							</div>
						</div>
					</div>
					<script type="text/javascript">
						$( "#<?php echo $childTable['CustomTable']['table_name'];?>" ).sortable({
					      revert: true,
					      stop:function(){
					      	var str = ''
					      	$("#<?php echo $childTable['CustomTable']['table_name'];?> li").each(function(){
					      		str = str + this.innerHTML + ',';
					      	});
					      	<?php 
					      		$tbl = '{"id":"'.$childTable['CustomTable']['id'].'","name":"'.$childTable['CustomTable']['table_name'].'"';
					      	?>
					      	
					      	$("#PdfTemplateChildTableFields<?php echo $c;?>").val('<?php echo $tbl;?>,"fields":"'+str+'"}');
					      }
					    });
							
						$().ready(function(){
							var str = ''
					      	$("#<?php echo $childTable['CustomTable']['table_name'];?> li").each(function(){
					      		str = str + this.innerHTML + ',';
					      	});
					      	<?php 
					      		$tbl = '{"id":"'.$childTable['CustomTable']['id'].'","name":"'.$childTable['CustomTable']['table_name'].'"';
					      	?>
					      	
					      	$("#PdfTemplateChildTableFields<?php echo $c;?>").val('<?php echo $tbl;?>,"fields":"'+str+'"}');
						})				    
					</script>
				<?php $c++; } 
				} ?>
				
		</div>

		<div class="col-md-12">
			<?php 
			echo $this->Form->submit('Save Template',array('class'=>'btn btn-lg btn-info pull-right'));
			echo $this->Form->end();
			?>	
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-heading"><h4>Preview</h4></div>
				<div class="panel-body preview"><?php echo $this->request->data['PdfTemplate']['template'];?></div>
			</div>			
		</div>
	</div>
</div>
<script type="text/javascript">
	function myFunction(id) {
		let copyGfGText = document.getElementById(id);
		copyGfGText.select();
    	document.execCommand("copy");
        console.log(copyGfGText.value);
}
</script>