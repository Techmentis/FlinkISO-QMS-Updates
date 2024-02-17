<div id="qcDocumentCategories_ajax">
	<?php echo $this->Session->flash();?>	

	<div class="qcDocumentCategories ">
		<?php echo $this->element('nav-header-lists',array('postData'=>array('pluralHumanName'=>'Qc Document Categories','modelClass'=>'QcDocumentCategory','options'=>array(),'pluralVar'=>'qcDocumentCategories'))); ?>


		<div class="nav panel panel-default">
			<div class="qcDocumentCategories form col-md-12">


				<table class="table table-responsive">
					<tr><td><?php echo __('Name'); ?></td>
						<td>
							<?php echo h($qcDocumentCategory['QcDocumentCategory']['name']); ?>
							&nbsp;
						</td></tr>
						<tr><td><?php echo __('Short Name'); ?></td>
							<td>
								<?php echo h($qcDocumentCategory['QcDocumentCategory']['short_name']); ?>
								&nbsp;
							</td></tr>
							<tr><td><?php echo __('Standard'); ?></td>
								<td>
									<?php echo $this->Html->link($qcDocumentCategory['Standard']['name'], array('controller' => 'standards', 'action' => 'view', $qcDocumentCategory['Standard']['id'])); ?>
									&nbsp;
								</td></tr>
								<tr><td><?php echo __('Parent Qc Document Category'); ?></td>
									<td>
										<?php echo $this->Html->link($qcDocumentCategory['ParentQcDocumentCategory']['name'], array('controller' => 'qc_document_categories', 'action' => 'view', $qcDocumentCategory['ParentQcDocumentCategory']['id'])); ?>
										&nbsp;
									</td></tr>
									<tr><td><?php echo __('Prepared By'); ?></td>

										<td><?php echo h($qcDocumentCategory['ApprovedBy']['name']); ?>&nbsp;</td></tr>
										<tr><td><?php echo __('Approved By'); ?></td>

											<td><?php echo h($qcDocumentCategory['ApprovedBy']['name']); ?>&nbsp;</td></tr>
											<tr><td><?php echo __('Publish'); ?></td>

												<td>
													<?php if($qcDocumentCategory['QcDocumentCategory']['publish'] == 1) { ?>
														<span class="fa fa-check"></span>
													<?php } else { ?>
														<span class="fa fa-close"></span>
														<?php } ?>&nbsp;</td>
													&nbsp;</td></tr>
													<tr><td><?php echo __('Soft Delete'); ?></td>

														<td>
															<?php if($qcDocumentCategory['QcDocumentCategory']['soft_delete'] == 1) { ?>
																<span class="fa fa-check"></span>
															<?php } else { ?>
																<span class="fa fa-close"></span>
																<?php } ?>&nbsp;</td>
															&nbsp;</td></tr>
															<tr>
																<td colspan="2">
																	<?php echo $this->Html->link('Delete This Record?', array('controller' => 'qc_document_categories', 'action' => 'delete', $qcDocumentCategory['ParentQcDocumentCategory']['id']),array('class'=>'btn btn-sm btn-danger')); ?>
																	<?php echo $this->Html->link('Edit This Record?', array('controller' => 'qc_document_categories', 'action' => 'delete', $qcDocumentCategory['ParentQcDocumentCategory']['id']),array('class'=> 'btn btn-sm btn-warning')); ?>
																</td>
															</tr>
														</table>

													</div>													
												</div>
												<?php echo $this->Js->writeBuffer();?>

											</div>
											<script>$.ajaxSetup({beforeSend:function(){$("#busy-indicator").show();},complete:function(){$("#busy-indicator").hide();}});</script>
