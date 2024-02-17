<p style="font-size:<?php echo $this->viewVars['fontsize'];?>px;position: absolute; bottom: 10px; left: 20px;">
	<?php 
	if($category != ''){
		echo $category . ' Document';
	}?>
	
</p>	
<img src="<?php echo $sign;?>" height="125" style="position: absolute; bottom: 10px; right: 2px;">	
<p style="font-family: '<?php echo $this->viewVars['fontface'];?>'; font-size:<?php echo $this->viewVars['fontsize'];?>px;position: absolute; bottom: 10px; right: 20px;">
	Downloaded by <?php echo $this->Session->read('User.name');?> at <?php echo date('d M Y H:i:s') ?>
</p>
