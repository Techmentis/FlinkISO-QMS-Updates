<?php echo $this->element('checkbox-script'); ?><div  id="main">
	<?php echo $this->Session->flash(); ?>	
	<div class="approvalComments ">
		<?php echo $this->element('nav-header-lists', ['postData' => ['pluralHumanName' => 'Approval Comments', 'modelClass' => 'ApprovalComment', 'options' => ["sr_no" => "Sr No", "comments" => "Comments"], 'pluralVar' => 'approvalComments']]); ?>

		<script type="text/javascript">
			$(document).ready(function(){
				$('table th a, .pag_list li span a').on('click', function() {
					var url = $(this).attr("href");
					$('#main').load(url);
					return false;
				});
			});
		</script>	
		<div class="table-responsive">
			<?php echo $this->Form->create(['class' => 'no-padding no-margin no-background']); ?>				
			<table cellpadding="0" cellspacing="0" class="table table-hover index">
				<tr>
					<th ><input type="checkbox" id="selectAll"></th>
					<th><?php echo $this->Paginator->sort('approval_id'); ?></th>
					<th><?php echo $this->Paginator->sort('user_id'); ?></th>
					<th><?php echo $this->Paginator->sort('comments'); ?></th>
					<th><?php echo $this->Paginator->sort('publish'); ?></th>
				</tr>
				<?php if ($approvalComments) { ?>
					<?php foreach ($approvalComments as $approvalComment): ?>
						<tr>
							<td class=" actions">	<?php echo $this->element('actions', [
								'created' => $approvalComment['ApprovalComment']['created_by'],
								'postVal' => $approvalComment['ApprovalComment']['id'],
								'softDelete' => $approvalComment['ApprovalComment']['soft_delete'],
								]); ?>	</td>		<td>
									<?php echo $this->Html->link($approvalComment['Approval']['id'], ['controller' => 'approvals', 'action' => 'view', $approvalComment['Approval']['id']]); ?>
								</td>
								<td>
									<?php echo $this->Html->link($approvalComment['User']['name'], ['controller' => 'users', 'action' => 'view', $approvalComment['User']['id']]); ?>
								</td>
								<td><?php echo h($approvalComment['ApprovalComment']['comments']); ?>&nbsp;</td>

								<td width="60">
									<?php if ($approvalComment['ApprovalComment']['publish'] == 1) { ?>
										<span class="fa fa-check"></span>
									<?php } else { ?>
										<span class="fa fa-close"></span>
										<?php } ?>&nbsp;</td>
									</tr>
								<?php endforeach; ?>
							<?php } else { ?>
								<tr><td colspan=42>No results found</td></tr>
							<?php } ?>
						</table>
						<?php echo $this->Form->end(); ?>			
					</div>
					<p>
						<?php
						echo $this->Paginator->options([]);
						echo $this->Paginator->counter(['format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')]);
					?>			</p>
					<ul class="pagination">
						<?php
						echo "<li class='previous'>" . $this->Paginator->prev('< ' . __('previous'), [], null, ['class' => 'prev disabled']) . "</li>";
						echo "<li>" . $this->Paginator->numbers(['separator' => '']) . "</li>";
						echo "<li class='next'>" .
						$this->Paginator->next(__('next') . ' >', [], null, [
							'class' => 'next disabled',
						]) .
						"</li>";
						?>
					</ul>
				</div>
			</div>
		</div>	

	</div>

	<script>$.ajaxSetup({beforeSend:function(){$("#busy-indicator").show();},complete:function(){$("#busy-indicator").hide();}});</script>
