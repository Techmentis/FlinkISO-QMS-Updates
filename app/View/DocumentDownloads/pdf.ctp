<div class="row">
	<div class="col-md-12">
		<ul class="list-group">
		<?php foreach($pdfs as $pdf){?>
			<li class="list-group-item"><a href="<?php echo Router::url('/', true) . 'files/pdf/'. $this->Session->read('User.id'). '/' .$pdf;?>" target="_blank"><?php echo $pdf;?></a></li>
		<?php }?>
		</ul>
	</div>
	</div>
</div>
