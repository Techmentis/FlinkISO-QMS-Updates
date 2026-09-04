<?php 
	if(isset($customTable) && $customTable != null){
		$title = $customTable['CustomTable']['name'];
	}else{
		$title = Inflector::humanize($this->request->controller);
	}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-touch-fullscreen" content="yes">
  <title><?php echo $this->Session->read('User.company_name')?> : <?php echo $title;?></title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <?php
  echo $this->Html->meta('icon');
  echo $this->Html->css(array('font-awesome.min','icons','allcss','api'));
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
	$('select').chosen( { width:'100%' } );
	$('input[type=radio][readonly]').each(function(){
		$('#'+this.id+':not(:checked)').attr('disabled', true);
	});
	$('select[readonly]').each(function(){
		var select = $(this);
		select.trigger('chosen:updated');
		select.next('.chosen-container')
			.addClass('chosen-readonly')
			.attr('aria-disabled', 'true')
			.css({'pointer-events':'none', 'opacity':'0.65'})
			.on('mousedown.chosenReadonly keydown.chosenReadonly', function(event){
				event.preventDefault();
				event.stopImmediatePropagation();
			});
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
+<?php
	$tabSettings = isset($customTable['CustomTable']['tab_settings']) ? json_decode($customTable['CustomTable']['tab_settings'], true) : array();
	if(!is_array($tabSettings)) $tabSettings = array();
	if(
		$tabSettings &&
		($this->action == 'add' || $this->action == 'edit') &&
		!empty($this->request->params['named']['custom_table_id'])
	){
?>
<script type="text/javascript">
	$().ready(function(){
		var savedTabSettings = <?php echo json_encode($tabSettings, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;
		var tabSettings = savedTabSettings.tabs || savedTabSettings;
		var childFormSettings = savedTabSettings.child_forms || {};
		var currentAction = <?php echo json_encode($this->action); ?>;
		$('<style>.custom-form-tabs .custom-tab-visibility-hidden,.custom-form-tabs .custom-tab-empty-hidden{display:none!important}</style>').appendTo('head');

		function fieldValues(fieldName){
			var suffix = '[' + fieldName + ']';
			var values = [];
			$('.custom_form :input').filter(function(){
				return this.name && this.name.slice(-suffix.length) === suffix && !$(this).closest('.linked-child-form').length;
			}).each(function(){
				var input = $(this);
				if((input.is(':radio') || input.is(':checkbox')) && !input.is(':checked')) return;
				if(input.is('select')){
					input.find('option:selected').each(function(){
						values.push(String($(this).val()));
						values.push($.trim($(this).text()));
					});
				}else{
					values.push(String(input.val()));
					var label = input.attr('id') ? $('label[for="' + input.attr('id') + '"]') : $();
					if(label.length) values.push($.trim(label.text()));
				}
			});
			return values;
		}

		function setPanelInputsDisabled(panel, disabled){
			panel.find(':input').each(function(){
				var input = $(this);
				if(disabled){
					if(!input.prop('disabled')) input.data('tabVisibilityDisabled', true).prop('disabled', true);
				}else if(input.data('tabVisibilityDisabled')){
					input.prop('disabled', false).removeData('tabVisibilityDisabled');
				}
				if(input.is('select')) input.trigger('chosen:updated');
			});
		}

		function shouldHide(setting){
			setting = setting || {};
			var hide = (setting.action_visibility === 'hide_both') ||
				(setting.action_visibility === 'hide_add' && currentAction === 'add') ||
				(setting.action_visibility === 'hide_edit' && currentAction === 'edit');
			var requiredValues = $.isArray(setting.visible_when) ? setting.visible_when : [];

			if(!hide && setting.visibility_field && requiredValues.length){
				var currentValues = fieldValues(setting.visibility_field);
				hide = !currentValues.some(function(value){ return requiredValues.indexOf(value) !== -1; });
			}
			return hide;
		}

		function applyTabVisibility(){
			$('.custom-form-tabs').each(function(){
				var tabs = $(this);
				var firstVisibleLink = null;
				tabs.children('ul').children('li').each(function(){
					var tabLink = $(this).children('a').first();
					var tabName = $.trim(tabLink.text());
					var hide = shouldHide(tabSettings[tabName]);

					var panel = $(tabLink.attr('href'));
					$(this).toggleClass('custom-tab-visibility-hidden', hide);
					panel.toggleClass('custom-tab-visibility-hidden', hide);
					setPanelInputsDisabled(panel, hide);
					if(!hide && !firstVisibleLink) firstVisibleLink = tabLink;
				});

				var activeLink = tabs.children('ul').children('li.ui-tabs-active').not('.custom-tab-visibility-hidden, .custom-tab-empty-hidden').children('a').first();
				if(!activeLink.length && firstVisibleLink) firstVisibleLink.trigger('click');
			});
		}

		function applyChildFormVisibility(){
			$('.linked-child-form').each(function(){
				var childForm = $(this);
				var hide = shouldHide(childFormSettings[childForm.attr('data-child-table')]);
				var childId = childForm.attr('id');
				var childLink = childId ? $('a[href="#' + childId + '"]').first() : $();

				childForm.toggleClass('custom-tab-visibility-hidden', hide);
				childLink.parent('li').toggleClass('custom-tab-visibility-hidden', hide);
				setPanelInputsDisabled(childForm, hide);
			});

			// A parent tab containing only child forms follows them: keep it when at
			// least one child form is available, otherwise hide the empty parent tab.
			$('.custom-form-tabs').each(function(){
				var outerTabs = $(this);
				outerTabs.children('div[id^="custom-form-tab-"]').each(function(){
					var outerPanel = $(this);
					var childForms = outerPanel.find('.linked-child-form');
					if(!childForms.length) return;
					var visibleChildren = childForms.not('.custom-tab-visibility-hidden');
					var nonChildInputs = outerPanel.find(':input').filter(function(){
						return !$(this).closest('.linked-child-form').length;
					});
					var hideOuter = visibleChildren.length === 0 && nonChildInputs.length === 0;
					var outerLink = $('a[href="#' + outerPanel.attr('id') + '"]').first();
					outerPanel.toggleClass('custom-tab-empty-hidden', hideOuter);
					outerLink.parent('li').toggleClass('custom-tab-empty-hidden', hideOuter);
				});
				var activeOuterLink = outerTabs.children('ul').children('li.ui-tabs-active').not('.custom-tab-visibility-hidden, .custom-tab-empty-hidden').children('a').first();
				if(!activeOuterLink.length){
					outerTabs.children('ul').children('li').not('.custom-tab-visibility-hidden, .custom-tab-empty-hidden').children('a').first().trigger('click');
				}
			});
		}

		function disableHiddenTabInputs(){
			$('.custom-tab-visibility-hidden, .custom-tab-empty-hidden').find(':input').each(function(){
				var input = $(this);
				if(!input.prop('disabled')) input.data('tabVisibilityDisabled', true).prop('disabled', true);
				if(input.is('select')) input.trigger('chosen:updated');
			});
		}

		$(document).off('change.tabVisibility', '.custom_form :input').on('change.tabVisibility', '.custom_form :input', function(){
			applyTabVisibility();
			applyChildFormVisibility();
		});
		applyTabVisibility();
		applyChildFormVisibility();
		disableHiddenTabInputs();
		$(document).off('ajaxComplete.tabVisibility').on('ajaxComplete.tabVisibility', function(){
			applyChildFormVisibility();
			disableHiddenTabInputs();
		});
		// Run before jQuery Validate's normal submit handler. This is needed for
		// validators created by generated forms with ignore:null.
		document.addEventListener('submit', function(){ disableHiddenTabInputs(); }, true);
		$(document).off('click.tabVisibility', 'form :submit').on('click.tabVisibility', 'form :submit', function(){
			disableHiddenTabInputs();
		});
	});
</script>
<?php } ?>
<div id="ad_src_result"></div>
<?php if($this->request->params['named']['custom_table_id']){ ?>
<script type="text/javascript">
	$().ready(function(){
		$("#uni-ajaxload").hide(250);
		$(document).ajaxSend(function(){
	    $("#uni-ajaxload").fadeIn(250);
		});
		$(document).ajaxComplete(function(){
		    $("#uni-ajaxload").fadeOut(250);
		});
		$(document).ajaxStart(function() {
		   "#uni-ajaxload"
		}).ajaxStop(function() {
		    $("#uni-ajaxload").fadeOut(250);
		});
			
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

	$().ready(function(){
		$("#uni-ajaxload").hide(250);
		$(document).ajaxSend(function(){
	    $("#uni-ajaxload").fadeIn(250);
		});
		$(document).ajaxComplete(function(){
		    $("#uni-ajaxload").fadeOut(250);
		});
		$(document).ajaxStart(function() {
		   "#uni-ajaxload"
		}).ajaxStop(function() {
		    $("#uni-ajaxload").fadeOut(250);
		});
	});

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
