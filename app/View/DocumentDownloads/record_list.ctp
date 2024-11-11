<?php if(isset($qcDocument)){ ?>	
<div class="row">
	<div class="col-md-12">
		<ul class="list-group pdf-list">
			<li class="list-group-item" id="<?php echo $qcDocument['QcDocument']['id']?>_li">
				<a href="#" class="li_a" onclick="postvalues('<?php echo $qcDocument['QcDocument']['id']?>','qc','QcDocument')"><?php echo $qcDocument['QcDocument']['document_number']?>-<?php echo $qcDocument['QcDocument']['title']?>-<?php echo $qcDocument['QcDocument']['revision_number']?>
				<i class="fa fa-download pull-right" id="<?php echo $qcDocument['QcDocument']['id'];?>_fa"></i>
				</a>
			</li>
			<div id="<?php echo $qcDocument['QcDocument']['id'];?>_li_div"></div>
		</ul>
		<?php if(isset($qcDocumentChild)){ ?>		
			<h5>Child Documents</h5>
			<ul class="list-group pdf-list">
			<?php foreach($qcDocumentChild as $qcDocument){?>
				<li class="list-group-item" id="<?php echo $qcDocument['QcDocument']['id']?>_li">
					<a href="#" class="li_a" onclick="postvalues('<?php echo $qcDocument['QcDocument']['id']?>','qc','QcDocument')"><?php echo $qcDocument['QcDocument']['document_number']?>-<?php echo $qcDocument['QcDocument']['title']?>-<?php echo $qcDocument['QcDocument']['revision_number']?>
					<i class="fa fa-download pull-right" id="<?php echo $qcDocument['QcDocument']['id'];?>_fa"></i>
					</a>
				</li>
				<div id="<?php echo $qcDocument['QcDocument']['id'];?>_li_div"></div>
			<?php }?>
			</ul>
		<?php } ?>
	</div>
	</div>
</div>

<?php } ?>

<?php if(isset($record)){ ?>
	<div class="row">
		<div class="col-md-12">
			<h5>Record</h5>
			<ul class="list-group pdf-list">
				<li class="list-group-item" id="<?php echo $record[Inflector::classify($record['CustomTable']['table_name'])]['id'];?>_li">
					<a href="#" class="li_a" id="<?php echo $record[Inflector::classify($record['CustomTable']['table_name'])]['id'];?>" onclick="postvalues('<?php echo $record[Inflector::classify($record['CustomTable']['table_name'])]['id'];?>','rec','<?php echo Inflector::classify($record['CustomTable']['table_name']);?>')">
						<?php echo $record[Inflector::classify($record['CustomTable']['table_name'])]['default'];?><br />
						<small>Prepared By: <?php echo $record['PreparedBy']['name'];?> / Approved By: <?php echo $record['ApprovedBy']['name'];?> </small>					
						<i class="fa fa-download pull-right" id="<?php echo $record[Inflector::classify($record['CustomTable']['table_name'])]['id'];?>_fa"></i>
					</a>
				</li>	
				<div id="<?php echo $record[Inflector::classify($record['CustomTable']['table_name'])]['id'];?>_li_div"></div>
			</ul>
			<?php foreach($childRecords as $tableName => $records){ ?>
				<h5><?php echo $tableName;?></h5>
				<ul class="list-group pdf-list">
					<?php foreach($records as $record){ ?>
						<li class="list-group-item" id="<?php echo $record[Inflector::classify($record['CustomTable']['table_name'])]['id'];?>_li">
							<a href="#" class="li_a" onclick="postvalues('<?php echo $record[Inflector::classify($record['CustomTable']['table_name'])]['id'];?>','rec','<?php echo Inflector::classify($record['CustomTable']['table_name']);?>')">
								<?php echo $record[Inflector::classify($record['CustomTable']['table_name'])]['default'];?><br />
								<small>Prepared By: <?php echo $record['PreparedBy']['name'];?> / Approved By: <?php echo $record['ApprovedBy']['name'];?> </small>							
								<i class="fa fa-download pull-right" id="<?php echo $record[Inflector::classify($record['CustomTable']['table_name'])]['id'];?>_fa"></i>
							</a>
						</li>
						<div id="<?php echo $record[Inflector::classify($record['CustomTable']['table_name'])]['id'];?>_li_div"></div>
					<?php } ?>
				</ul>
			<?php }?>
		</div>
	</div>
<?php } ?>
<?php echo $this->Form->create('DocumentDownload',array(),array('default'=>false)); ?>
<?php echo $this->Form->hidden('add_document',array());?>
<?php echo $this->Form->hidden('add_cover_page',array());?>
<?php echo $this->Form->hidden('add_parent_records',array());?>
<?php echo $this->Form->hidden('add_child_records',array());?>
<?php echo $this->Form->hidden('add_linked_form_records',array());?>
<?php echo $this->Form->hidden('password',array());?>		
<?php echo $this->Form->hidden('font_size',array());?>
<?php echo $this->Form->hidden('font_face',array());?>
<?php echo $this->Form->hidden('record_id',array());?>
<?php echo $this->Form->hidden('custom_table_id',array());?>
<?php echo $this->Form->hidden('qc_document_id',array());?>
<?php echo $this->Form->hidden('process_id',array());?>
<?php echo $this->Form->hidden('signature',array());?>
<?php echo $this->Form->hidden('pdf_template_id',array());?>
<?php echo $this->Form->hidden('pdf_header_id',array());?>
<?php echo $this->Form->hidden('add_cover',array());?>



<?php echo $this->Form->end();?>

<script type="text/javascript">
	function postvalues(id,type,model){
		$('.li_a').click(function () {return false;});
		$("#"+id+"_fa").removeClass('fa-download').addClass('fa-refresh fa-spin');		
		$("#DocumentDownloadRecordListForm").ajaxSubmit({
			url: "<?php echo Router::url('/', true); ?><?php echo $this->request->params['controller'] ?>/download/type:"+type+"/id:"+id+"/model:"+model,
			type: 'POST',
			target: '#'+id+"_li_div",			
			beforeSend: function(){
				
			},
			complete: function(data,response) {				
				$("#"+id+"_fa").removeClass('fa-refresh fa-spin').addClass('fa-check text-success');
				$("#"+id+"_li_div").html(data.responseText);				
				$(".modal-title").html("Your PDFs files are ready for download.");
				$('.li_a').unbind('click');
			},
			error: function(request, status, error) {                    
				alert('Action failed!');
			}
		});

	}
</script>
