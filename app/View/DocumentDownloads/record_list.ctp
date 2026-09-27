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
			<div class="well well-sm record-pdf-actions">
				<div class="row">
					<div class="col-md-5">
						<label style="margin:7px 0 0 0;">
							<input type="checkbox" id="record-pdf-select-all" /> Select all records
						</label>
					</div>
					<div class="col-md-7 text-right">
						<button type="button" class="btn btn-sm btn-primary record-bundle-button" onclick="generateRecordBundle('selected');">
							<i class="fa fa-files-o"></i> Selected records
						</button>
						<button type="button" class="btn btn-sm btn-success record-bundle-button" onclick="generateRecordBundle('all');">
							<i class="fa fa-file-pdf-o"></i> All records
						</button>
					</div>
				</div>
				<div id="record-bundle-result" style="margin-top:10px;"></div>
			</div>
			<p class="text-muted"><small>Use the download icon for an individual record, or select records to create one combined PDF. QC documents are not included.</small></p>
			<h5>Record</h5>
			<ul class="list-group pdf-list">
				<li class="list-group-item" id="<?php echo $record[Inflector::classify($record['CustomTable']['table_name'])]['id'];?>_li">
					<input type="checkbox" class="record-pdf-select" style="margin-right:10px;"
						data-record-id="<?php echo h($record[Inflector::classify($record['CustomTable']['table_name'])]['id']); ?>"
						data-custom-table-id="<?php echo h($record['CustomTable']['id']); ?>" />
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
							<input type="checkbox" class="record-pdf-select" style="margin-right:10px;"
								data-record-id="<?php echo h($record[Inflector::classify($record['CustomTable']['table_name'])]['id']); ?>"
								data-custom-table-id="<?php echo h($record['CustomTable']['id']); ?>" />
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
<?php echo $this->Form->hidden('add_header',array());?>
<?php echo $this->Form->hidden('printing',array());?>
<?php echo $this->Form->hidden('degraded_printing',array());?>
<?php echo $this->Form->hidden('modify_contents',array());?>
<?php echo $this->Form->hidden('copy_contents',array());?>
<?php echo $this->Form->hidden('modify_annotations',array());?>
<?php echo $this->Form->hidden('records',array('id'=>'record-bundle-records'));?>
<?php echo $this->Form->hidden('bundle_id',array('id'=>'record-bundle-id'));?>



<?php echo $this->Form->end();?>

<script type="text/javascript">
	$('#record-pdf-select-all').on('change', function(){
		$('.record-pdf-select').prop('checked', $(this).is(':checked'));
	});

	$('.record-pdf-select').on('change', function(){
		var all = $('.record-pdf-select').length > 0 &&
			$('.record-pdf-select:checked').length === $('.record-pdf-select').length;
		$('#record-pdf-select-all').prop('checked', all);
	});

	function generateRecordBundle(mode){
		var records = [];
		var selector = mode === 'all' ? '.record-pdf-select' : '.record-pdf-select:checked';
		$(selector).each(function(){
			records.push({
				id: $(this).data('record-id'),
				custom_table_id: $(this).data('custom-table-id')
			});
		});

		if(records.length === 0){
			$('#record-bundle-result').html('<div class="alert alert-warning">Select at least one record.</div>');
			return;
		}

		var bundleId = 'record-bundle-'+(new Date().getTime())+'-'+Math.random().toString(16).slice(2, 10);
		$('#record-bundle-records').val(JSON.stringify(records));
		$('#record-bundle-id').val(bundleId);
		$('.record-bundle-button').prop('disabled', true);
		$('#record-bundle-result').html('<div class="text-info"><i class="fa fa-refresh fa-spin"></i> Generating and combining '+records.length+' record PDF'+(records.length === 1 ? '' : 's')+'...</div>');
		var bundleStartedAt = new Date().getTime();

		$('#DocumentDownloadRecordListForm').ajaxSubmit({
			url: "<?php echo Router::url(array('controller'=>'document_downloads','action'=>'download_record_bundle'), true); ?>",
			type: 'POST',
			target: '#record-bundle-result',
			success: function(){
				$('.record-bundle-button').prop('disabled', false);
			},
			error: function(request){
				var elapsedSeconds = Math.round((new Date().getTime() - bundleStartedAt) / 1000);
				var message = '<i class="fa fa-refresh fa-spin"></i> PDF generation is still running. Waiting for the finished file...';
				if(elapsedSeconds >= 25 || request.status === 502 || request.status === 503 || request.status === 504){
					message += '<br><small>The web server stopped waiting for this long request. If this happens frequently, ask the administrator to increase the Apache/FastCGI timeout (currently approximately 30 seconds) and check PHP <code>max_execution_time</code> and <code>memory_limit</code>.</small>';
				}
				$('#record-bundle-result').html('<div class="alert alert-info">'+message+'</div>');
				pollRecordBundle(bundleId, 0);
			}
		});
	}

	function pollRecordBundle(bundleId, attempt){
		if(attempt >= 120){
			$('.record-bundle-button').prop('disabled', false);
			$('#record-bundle-result').html(
				'<div class="alert alert-danger">PDF generation did not finish within four minutes. '
				+'Ask the administrator to review the Apache/FastCGI request timeout, PHP <code>max_execution_time</code> and <code>memory_limit</code>, and the OnlyOffice conversion-service availability.</div>'
			);
			return;
		}

		$.ajax({
			url: "<?php echo Router::url(array('controller'=>'document_downloads','action'=>'record_bundle_status'), true); ?>/"+encodeURIComponent(bundleId),
			type: 'GET',
			dataType: 'json',
			success: function(result){
				if(result.ready){
					var link = $('<a/>', {href: result.url, target: '_blank', text: 'Download combined records PDF'});
					var box = $('<div/>', {'class':'alert alert-success record-bundle-download'});
					box.append($('<i/>', {'class':'fa fa-file-pdf-o'})).append('&nbsp;&nbsp;').append(link);
					$('#record-bundle-result').empty().append(box);
					$('.record-bundle-button').prop('disabled', false);
				}else if(result.error){
					$('#record-bundle-result').empty().append($('<div/>', {'class':'alert alert-danger', text:result.error}));
					$('.record-bundle-button').prop('disabled', false);
				}else{
					setTimeout(function(){ pollRecordBundle(bundleId, attempt + 1); }, 2000);
				}
			},
			error: function(){
				setTimeout(function(){ pollRecordBundle(bundleId, attempt + 1); }, 2000);
			}
		});
	}

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
