<div id="standards_ajax">
	<?php echo $this->Session->flash();?>	
	<div class="nav panel panel-default">
		<div class="standards form col-md-12">
			<h4><?php echo __('View Standard'); ?></h4>

			<table class="table table-responsive">
				<tr>
					<td><?php echo __('Name'); ?></td>
					<td><?php echo h($standard['Standard']['name']); ?>&nbsp;</td>
				</tr>
				<tr>
					<td><?php echo __('Brief Description'); ?></td>
					<td><?php echo h($standard['Standard']['details']); ?>&nbsp;</td>
				</tr>
				<tr>
					<td><?php echo __('Prepared By'); ?></td>
					<td><?php echo h($standard['PreparedBy']['name']); ?>&nbsp;</td>
				</tr>
				<tr>
					<td><?php echo __('Approved By'); ?></td>
					<td><?php echo h($standard['ApprovedBy']['name']); ?>&nbsp;</td>
				</tr>
				<tr>
					<td><?php echo __('Publish'); ?></td>
					<td>
						<?php if($standard['Standard']['publish'] == 1) { ?>
							<span class="fa fa-check"></span>
						<?php } else { ?>
							<span class="fa fa-close"></span>
							<?php } ?>&nbsp;
						</td>
					</tr>
					<tr>
						<td><?php echo __('Soft Delete'); ?></td>
						<td>
							<?php if($standard['Standard']['soft_delete'] == 1) { ?>
								<span class="fa fa-check"></span>
							<?php } else { ?>
								<span class="fa fa-close"></span>
								<?php } ?>&nbsp;
							</td>
						</tr>
					</table>
				</div>				
			</div>

		</div>

