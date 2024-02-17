<div id="records_ajax">
	<?php echo $this->Session->flash();?>	

	<div class="records ">
		<?php echo $this->element('nav-header-lists',array('postData'=>array('pluralHumanName'=>'Records','modelClass'=>'Record','options'=>array(),'pluralVar'=>'records'))); ?>


		<div class="nav panel panel-default">
			<div class="records form col-md-12">


				<table class="table table-responsive">
					<tr><td><?php echo __('Title'); ?></td>
						<td>
							<?php echo h($record['Record']['title']); ?>
							&nbsp;
						</td></tr>
						<tr><td><?php echo __('Qc Document'); ?></td>
							<td>
								<?php echo $this->Html->link($record['QcDocument']['title'], array('controller' => 'qc_documents', 'action' => 'view', $record['QcDocument']['id'])); ?>
								&nbsp;
							</td></tr>
							<tr><td><?php echo __('Comments'); ?></td>
								<td>
									<?php echo h($record['Record']['comments']); ?>
									&nbsp;
								</td></tr>
								<tr><td><?php echo __('Prepared By'); ?></td>

									<td><?php echo h($record['ApprovedBy']['name']); ?>&nbsp;</td></tr>
									<tr><td><?php echo __('Approved By'); ?></td>

										<td><?php echo h($record['ApprovedBy']['name']); ?>&nbsp;</td></tr>
										<tr><td><?php echo __('Publish'); ?></td>

											<td>
												<?php if($record['Record']['publish'] == 1) { ?>
													<span class="fa fa-check"></span>
												<?php } else { ?>
													<span class="fa fa-close"></span>
													<?php } ?>&nbsp;</td>
												&nbsp;</td></tr>
												<tr><td><?php echo __('Soft Delete'); ?></td>

													<td>
														<?php if($record['Record']['soft_delete'] == 1) { ?>
															<span class="fa fa-check"></span>
														<?php } else { ?>
															<span class="fa fa-close"></span>
															<?php } ?>&nbsp;</td>
														&nbsp;</td></tr>
														<tr>
															<td colspan="2">
																<div class="btn-group1">
																	<?php     
																	echo $this->Html->link('Delete This Record?',array('action'=>'delete',$record['Record']['id']),array('confirm'=>'This action can not be revered. Do you want to procces?', 'class'=>'btn btn-xs btn-danger'));
																	echo "&nbsp;" . $this->Html->link('Edit This Record?',array('action'=>'edit',$record['Record']['id']),array('class'=>'btn btn-xs btn-warning'));        
																	?>
																</div>
															</td>
														</tr>
													</table>

												</div>												
											</div>
											<div class="row">
												<div class="col-md-12">
													<?php 
													$key = $record['Record']['file_key'];
													$file_type = $qcDoc['QcDocument']['file_type'];
													$file_name = $qcDoc['QcDocument']['title'];
													$document_number = $qcDoc['QcDocument']['document_number'];
													$document_version = $qcDoc['QcDocument']['revision_number'];

													$file_type = $qcDoc['QcDocument']['file_type'];
													
													if($file_type == 'doc' || $file_type == 'docx'){
														$documentType = 'word';
													}

													if($file_type == 'xls' || $file_type == 'xlsx'){
														$documentType = 'cell';
													}

													$mode = 'view';

													$file_path = $record['Record']['id'];


													$file = $document_number.'-'.$file_name.'-'.$document_version.'.'.$file_type;

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
														'record_id'=>$record['Record']['id'],
														'company_id'=>$this->Session->read('User.company_id'),
														'controller'=>$this->request->controller,
													));
													?>
												</div>
											</div>	

											<?php echo $this->Js->writeBuffer();?>
											<div class="row">
												<div class="col-md-12">
													<?php if($this->request->params['named']['approval_id'])echo $this->element('approval_form',array('approval'=>$approval));?>
													<?php echo $this->element('approval_history',array('approval'=>$approval,'approvals'=>$approvals,'current_approval'=>$this->request->params['named']['approval_id'],'approvalComments',$approvalComments));?>
												</div>
											</div>

										</div>
										<script>$.ajaxSetup({beforeSend:function(){$("#busy-indicator").show();},complete:function(){$("#busy-indicator").hide();}});</script>
