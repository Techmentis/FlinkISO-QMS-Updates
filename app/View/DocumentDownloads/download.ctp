<?php if($pdfs){ ?>
	<?php foreach($pdfs as $pdf){?>

		<li class="list-group-item">
			<i class="fa fa-file-pdf-o"></i>&nbsp;&nbsp;
			<a href="<?php echo Router::url('/', true) . 'files/pdf/'. $this->Session->read('User.id'). '/' . $this->request->params['named']['id']  .'/'.$pdf;?>" target="_blank" onclick="add('<?php echo $this->request->params['named']['type'];?>','<?php echo $id?>','<?php echo $signature;?>','<?php echo $pdf;?>');"><?php echo $pdf;?></a></li>
	<?php }?>
<?php }else{ ?>
	<li class="list-group-item">No documents found for this record</li>
<?php } ?>
<script type="text/javascript">
	function add(t,id,s,n){
		$.ajax({
			type: "POST",
            dataType: "json",
            contentType: "application/json; charset=utf-8",
            data: JSON.stringify({t:t,id:id,s:s,n:n}),
			url: "<?php echo Router::url('/', true); ?><?php echo $this->request->params['controller'] ?>/add_download_details",
				success: function(data, result) {
					return true;
				},
		});

	}
</script>
