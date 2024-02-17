<?php if($customTrigers){ ?>
	<div class="">
		<?php foreach($customTrigers as $name => $data){ ?>
			<?php if(count($data['records']) > 0){ ?>		
				<div class="col-md-6">
					<div class="box box-danger">
						<div class="box-header">
							<h3 class="box-title"><?php echo $name ?></h3>
							<div class="box-tools"><span class="badge"><?php echo count($data['records']);?></span></div>
						</div>
						<!-- /.box-header -->
						<div class="box-body table-responsive no-padding">
							<table class="table table-responsive">
								<?php foreach($data['records'] as $recs){ ?>
									<tr>
										<td><?php echo $recs[Inflector::classify($data['controller'])][$data['show_field']]?></td>
										<td class="text-right">
											<?php
											$names = '';
											if($recs[Inflector::classify($data['controller'])]['qc_document_id'])$names .= '/qc_document_id:'.$recs[Inflector::classify($data['controller'])][
												'qc_document_id'];
												if($recs[Inflector::classify($data['controller'])]['process_id'])$names .= '/process_id:'.$recs[Inflector::classify($data['controller'])][
													'process_id'];						    				
													echo $this->Form->create($data['controller'],array('controller'=>$data['controller'],'action'=>'edit/'.$recs[Inflector::classify($data['controller'])]['id'].$names,'id'=>false),array('id'=>false));
													echo $this->Form->hidden('Access.skip_access_check',array('default'=>1));
													echo $this->Form->hidden('Access.allow_access_user',array('default'=>$this->Session->read('User.id')));
													echo $this->Form->submit('Act',array('class'=>'btn btn-xs btn-danger'));
													echo $this->Form->end();
													?>
												</td>
											</tr>
										<?php } ?>
									</table>
								</div>
							</div>
						</div>
					<?php } ?>
				<?php } ?>
			</div>
<?php } ?>