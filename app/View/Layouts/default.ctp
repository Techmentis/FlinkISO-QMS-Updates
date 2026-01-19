<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-touch-fullscreen" content="yes">
  <title><?php echo $this->Session->read('User.company_name')?> : <?php echo Inflector::humanize($this->request->controller);?></title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <?php
  echo $this->Html->meta('icon');
  echo $this->Html->css(array('font-awesome.min','icons','allcss'));
  echo $this->fetch('css');

  echo $this->Html->script(array(    
	'plugins/jQuery/jQuery-2.2.0.min',
	'plugins/jQueryUI/jquery-ui.min',
	'js/bootstrap.min',
	'validation',
	'chosen.min',
	'tooltip.min',
	'plugins/daterangepicker/moment.min',
	'jquery.datepicker',    
	'plugins/daterangepicker/daterangepicker',
	'plugins/datepicker/bootstrap-datepicker',    
));


  if($this->action == 'index'){
	echo $this->Html->script(array(
		'js-xlsx-master/dist/xlsx.core.min', 
		'FileSaver.js-master/FileSaver.min', 
		'TableExport-master/src/stable/js/tableexport.min',    

	)); 
}
echo $this->fetch('script');
?>
<style>.chosen-drop{z-index: 999}</style>
</head>
<body class="hold-transition skin-blue sidebar-collapse sidebar-mini">
	<?php if ($this->Session->read('User'))echo $this->Element('control-sidebar'); ?>
	<div class="wrapper">
	  <?php echo $this->Element('header');?>
	  <!-- Left side column. contains the logo and sidebar -->
	  <?php if ($this->Session->read('User'))echo $this->Element('asidebar');?>
	  <!-- Content Wrapper. Contains page content -->
	  <div class="content-wrapper">        
		  <!-- Content Header (Page header) -->
		  <?php if ($this->Session->read('User')) echo $this->Element('breadcrump');?>	    
		  <!-- Main content -->
		  <?php if(($_SERVER['SERVER_NAME']) == 'localhost'){ ?>
				<div class="row"><div class="col-md-12"><div class="alert text-danger"><small><strong>Note:</strong> ONLYOFFICE Doc Editors will not work on localhost. Either install ONLYOFFICE Doc Server on your premise and use IP address to access FlinkISO application Or use Static IP use ONLYOFFICE Hosted FlinkISO server.</small></div></div></div>
			<?php } ?>
		  <section class="content">
			  <?php echo $this->Session->flash(array('class'=>'alert-danger')); ?>			  
			  <?php if(isset($lock_message) && $lock_message != ''){ ?>
				<div class="show_lock_comments"><i class="fa  fa-exclamation-triangle"></i> <?php echo $lock_message;?></div>
			<?php } ?>

			<?php echo $this->fetch('content');?> 
			<div class="row"><div class="col-md-12"><div id="load_process"></div> </div></div>
			<!-- Info boxes -->
			<!-- /.row -->  
		</section>
		<!-- /.content -->
	</div>
	<!-- /.content-wrapper -->
	<footer class="main-footer">  
		<div class="pull-right hidden-xs">
		  <b>Version</b> 2.2.42
	  </div>
	  <strong>Copyright &copy; 2013 <a href="http://www.techmentis.biz">Techmentis Global Services Pvt Ltd</a>.</strong> All rights
	  reserved.
  </footer>
  </div>
<div id="addsignature-employee"></div>
<?php
echo $this->Html->script(array(
	'dist/js/demo',
	'dist/js/app.min',
));
echo $this->fetch('script');
?>
<script type="text/javascript">
  $().ready(function(){

	var width = $( window ).width() - 50;
	var menuleft = $("#mega-menu").offset();
	var pos = menuleft.left - 25; 
	
	$(".get-size").attr("style","left:-"+pos+"px");
	$(".get-size").width(width + 'px');

	$("#mm").hover(function(){$(this).find('.dropdown-menu').first().toggle();});

	$('.tooltip1').tooltip();
	// $('select').chosen();
	$('select').chosen( { width:'100%' } );
	$('input[type=radio][readonly]').each(function(){
		$('#'+this.id+':not(:checked)').attr('disabled', true);
	});
	$('select[readonly]').each(function(){
		$("#"+this.id).prop('disabled',true).trigger('chosen:updated').prop('disabled',false)
	});

<?php if($this->request->params['named']['custom_table_id'] && ($this->action == 'add')){ ?>
	$('select').on('change',function(){
			check_document(this);
	});

	$('select').each(function(){
		check_document(this);
	})

<?php } ?>	

<?php if($this->request->params['named']['custom_table_id'] && ($this->action == 'edit')){ ?>
	$('select').on('change',function(){
			remoaddocument(this);
	});

	$('select').each(function(){
		remoaddocument(this);
	})

<?php } ?>	

});

  function check_document(t){
  		var showdocs = $("#"+t.id).attr('showdocs');
			var showdocs_mode = $("#"+t.id).attr('showdocs_mode');
			var showdocs_copy = $("#"+t.id).attr('showdocs_copy');
			if(showdocs == 1){
				$('#'+t.id+'_div_for_doc').remove();
				$(t).next('div').after('<div id="'+t.id+'_div_for_doc"></div>');

				$('#'+t.id+'_div_for_doc').load("<?php echo Router::url('/', true); ?><?php echo $this->request->params['controller'] ?>/check_document/" + t.value + "/" + t.name +"/" + t.id + "/custom_table_id:<?php echo $this->request->params['named']['custom_table_id'];?>/record_id:<?php echo $this->request->params['pass'][0];?>/"+ $("#"+t.id).attr('model') + "/showdocs:" + showdocs + "/showdocs_mode:" + showdocs_mode + "/showdocs_copy:" + showdocs_copy );		
			}
  }

  function remoaddocument(t){
  	
  		var showdocs = $("#"+t.id).attr('showdocs');
			var showdocs_mode = $("#"+t.id).attr('showdocs_mode');
			var showdocs_copy = $("#"+t.id).attr('showdocs_copy');
			if(showdocs == 1){
				$('#'+t.id+'_div_for_doc').remove();
				$(t).next('div').after('<div id="'+t.id+'_div_for_doc"></div>');
				$('#'+t.id+'_div_for_doc').load("<?php echo Router::url('/', true); ?><?php echo $this->request->params['controller'] ?>/check_document/" + t.value + "/" + t.name +"/" + t.id + "/custom_table_id:<?php echo $this->request->params['named']['custom_table_id'];?>/record_id:<?php echo $this->request->params['pass'][0];?>/"+ $("#"+t.id).attr('model') + "/showdocs:" + showdocs + "/showdocs_mode:" + showdocs_mode + "/showdocs_copy:" + showdocs_copy );		
			}
  }
</script>

<?php
if($customTable){ ?>
	<script type="text/javascript">
		function checkunique(val,name,id){
			$.ajax({
				url: "<?php echo Router::url('/', true); ?><?php echo $this->request->params['controller'] ?>/checkunique/"+ val +"/" + name,
					success: function(data, result) {
						if(data == true){
							$("#"+id).prev('label').append('<small style="color:#ed7c7c"> : Value Exists. Add unique value.</small>');
							$("#"+id).val('');
							$("#"+id).addClass('error').removeClass(' valid success');
						}
					},
			});
		}

		function donothing(){
			
		}
	</script>
<?php foreach(json_decode($customTable['CustomTable']['fields'],true) as $fields){  
		if($fields['field_type'] == 5 && $fields['default_date_from'] != -1){ 
			if($fields['default_date_from'] == "Today"){ ?>
				<script type="text/javascript">	
					$("#<?php echo Inflector::classify($customTable['CustomTable']['table_name']);?><?php echo Inflector::classify($fields['field_name']);?>").val("<?php echo date('Y-m-d');?>");
				</script>
			<?php }else{ ?>
		
			<script type="text/javascript">				
				$("#<?php echo Inflector::classify($customTable['CustomTable']['table_name']);?><?php echo Inflector::classify($fields['default_date_from']);?>").on('change',function(){
					$.ajax({
						type: "POST",
						dataType: "text",
						data : {
						  "linkedTos":<?php echo json_encode($fields);?>,
						  "fromDate":$("#<?php echo Inflector::classify($customTable['CustomTable']['table_name']);?><?php echo Inflector::classify($fields['default_date_from']);?>").val()
					  },
					  url: "<?php echo Router::url('/', true); ?><?php echo $this->request->params['controller'] ?>/add_date_new/",
					  success: function(data, result) {                                   
							$("#<?php echo Inflector::classify($customTable['CustomTable']['table_name']);?><?php echo Inflector::classify($fields['field_name']);?>").val(data);
						},                              
				}); 
				})
			</script>
		<?php } }
	}
}
?>

<?php 

if($this->action == 'index'){?>
	<script type="text/javascript">
		$("#exportcsv").tableExport(
		{
			  headers: true,					// (Boolean), display table headers (th or td elements) in the <thead>, (default: true)
			  footers: true,					// (Boolean), display table footers (th or td elements) in the <tfoot>, (default: false)
			  formats: ["csv"],					// (String[]), filetype(s) for the export, (default: ['xlsx', 'csv', 'txt'])
			  filename: "<?php echo $this->request->controller;?>",                     // (id, String), filename for the downloaded file, (default: 'id')
			  bootstrap: true,                  // (Boolean), style buttons using bootstrap, (default: true)
			  exportButtons: true,              // (Boolean), automatically generate the built-in export buttons for each of the specified formats (default: true)
			  position: "bottom",               // (top, bottom), position of the caption element relative to table, (default: 'bottom')
			  ignoreRows: null,                 // (Number, Number[]), row indices to exclude from the exported file(s) (default: null)
			  ignoreCols: [$("#exportcsv").find('th').length-1],                   // (Number, Number[]), column indices to exclude from the exported file(s) (default: null)
			  trimWhitespace: true,             // (Boolean), remove all leading/trailing newlines, spaces, and tabs from cell text in the exported file(s) (default: false)
			  RTL: false,                       // (Boolean), set direction of the worksheet to right-to-left (default: false)
			  sheetname: "<?php echo $this->request->controller;?>"  // (id, String), sheet name for the exported spreadsheet, (default: 'id')
		  }
		  );
	  </script>
  <?php }    ?>
</script>

<?php
	if($customTable){
		if($this->action == 'add'){
			echo "<script type=\"text/javascript\">";
			echo $customTable['CustomTable']['add_form_script'];
			echo "</script>";
		}

		if($this->action == 'edit'){
			echo "<script type=\"text/javascript\">";
			echo $customTable['CustomTable']['edit_form_script'];
			echo "</script>";
		}			
	}

?>
<div id="ad_src_result"></div>
<?php if($this->request->params['named']['custom_table_id']){ ?>
<script type="text/javascript">
	$().ready(function(){
		$.ajax({
				type: "POST",
				url: "<?php echo Router::url('/', true); ?><?php echo $this->request->params['controller'] ?>/load_process/<?php echo $this->request->params['named']['custom_table_id'];?>",
			  success: function(data, result) {                                   
					$(".content").append(data);	
					// $("#main").append(data);
				},                              
		}); 
	});
</script>
<?php } ?>
<script>
	function addsignature(employee,fieldid){
		$("#"+fieldid).val(-1).trigger('chosen:updated');
		$("input[type=submit]").hide();
		$("#addsignature-employee").load("<?php echo Router::url('/', true); ?>/<?php echo $this->request->controller;?>/addsignature/"+employee+"/"+fieldid);		
	}

	function checkext(ele,exts,eid){

		let pathext = ele;
		const pathfile = pathext.split(".");
		const arr = pathfile.length;
		const ext = pathfile[arr-1];
		
		var earr = exts;
		
		if($.inArray(ext,earr) > -1){
			
		}else{
			alert('This file type is not allowed : ' + ext);
			$("#"+eid).val('');
			$("#"+eid).prev("label").html('');
		}
	}
</script>
</body>
</html>
