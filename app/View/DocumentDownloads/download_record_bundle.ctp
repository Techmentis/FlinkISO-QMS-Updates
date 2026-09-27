<?php if(!empty($bundle)){ ?>
	<div class="alert alert-success record-bundle-download">
		<i class="fa fa-file-pdf-o"></i>&nbsp;&nbsp;
		<a href="<?php echo h($bundle['url']); ?>" target="_blank">
			Download <?php echo intval($bundle['count']); ?> combined record<?php echo intval($bundle['count']) === 1 ? '' : 's'; ?>
		</a>
		<small class="text-muted"> (<?php echo h($bundle['name']); ?>)</small>
	</div>
<?php }else{ ?>
	<div class="alert alert-danger">
		<?php echo h(!empty($bundleError) ? $bundleError : 'The combined PDF could not be generated.'); ?>
	</div>
<?php } ?>
