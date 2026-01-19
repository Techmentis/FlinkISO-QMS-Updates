<?php if ($this->request->is('ajax') != true) {
	echo $this->Html->script(array('jquery.validate.min','jquery-form.min'));
	echo $this->fetch('script');
	?>
	<script>
		$().ready(function() {
			$('#UserLoginForm').validate();
		})
	</script>
	<style type="text/css">
		.checkbox, .date, .datetime-local, .form-check-inline, .number, .password, .radio, .select, .text, .textarea,label{margin-top: 0px;}
		.box-body{padding: 5px 10px 20px 10px;}
		.link{text-decoration: underline; color: #0194ff; font-weight: 700}
	</style>
	<div  id="users_ajax"> 
		<?php echo $this->Session->flash(); ?>
		<div class="row">
			<div class="col-md-6 col-xs-12 hidden-xs">

				<div class="panel">
					<div class="box-header with-border"><h3 class="box-title">Thank you for downloading FlinkISO&trade; <small>2.0</small> : Quality Management System</h3></div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-12">
								<p>You may want to consider following services: </p>
								<ul>
									<li><a href="https://www.flinkiso.com/services/training.html" class="link" target="_blank">QMS Training</a></li>
									<li><a href="https://www.flinkiso.com/services/customization.html" class="link" target="_blank">Software Customization</a></li>
									<li><a href="https://www.flinkiso.com/services/application-support.html"  class="link" target="_blank">Enterprise Support</a> </li>
									<li><a href="https://www.flinkiso.com/pricing/on-premise.html"  class="link" target="_blank">Compare Editions</a> </li>
									<li><a href="https://www.flinkiso.com/pricing/quote-for-enterprise-edition.html" class="link" target="_blank" >Quote Request For Enterprise Edition</a></li>
								</ul>
							</div>
							<div class="col-md-12">
								<h5>For rapid End-to-End application deployment, try our <a href="https://www.flinkiso.com/pricing/startup-plan.html" target="_blank" class="link" > Startup Plan!</a></h5>
							</div>
							<div class="col-md-12">
								<p><strong>Training:</strong> Contact sale@flinkiso.com for one time training @USD.150.</p>
							</div>
						</div>
					</div>		
				</div>		
			</div>
			<div class="col-md-6 col-xs-12">
				<?php echo $this->Form->create('User', array('controller' => 'login','role' => 'form','class' => ''))?>
				<div class="box">
					<div class="box-header with-border"><h3 class="box-title">Login</h3></div>
					<div class="box-body">

						<?php
						if (isset($this->request->params['pass'][0]))
							echo $this->Form->input('username', array('value' => base64_decode($this->request->params['pass']['0']),'class' => 'form-control','placeholder' => __('Please Enter username')));
						else
							echo $this->Form->input('username', array('class' => 'form-control','placeholder' => __('Please Enter username')));?>


						<?php echo $this->Form->input('password', array('class' => 'form-control','placeholder' => __('*********')));?>

					</div>
					<div class="box-footer">
						<?php echo $this->Html->link(__('Forgot password?'), array('action' => 'reset_password'), array('class' => 'pull-left forgot-pwd'));
						echo $this->Form->submit(__('Submit'), array('div' => false,'class' => 'btn btn-md btn-info pull-right')); ?>    						
					</div>				
				</div>
			</div>	
			<?php echo $this->Form->end();?>	
		</div>
	</div>
	<div class="row">
<?php if(Configure::read('WkHtmlToPdfPath') == ''){ ?>
	<div class="col-md-12">
		<div class="alert alert-danger">You must install WkHtmlToPdf and update its binary path in core.php for PDF export to function properly.</div>
	</div>
<?php } ?>
<?php if(Configure::read('PDFTkPath') == ''){ ?>
	<div class="col-md-12">
		<div class="alert alert-danger">You must install PDFTk Server and update its binary path in core.php for PDF export to function properly.</div>
	</div>
<?php } ?>
</div>
<div class="row">
<div class="col-md-6 col-xs-12  hidden-xs">
	<div class="panel panel-default">
		<div class="panel-heading"><div class="panel-title"><?php echo __('Application Updates');?></div></div>
		<div class="panel-body">
			<div id="output"></div>
				<script>
					var settings = {
					    url: "https://www.flinkiso.com/flinkiso-updates/application-updates.xml",
					    method: "GET",
					    timeout: 0,
					    dataType: "xml"
					};

					$.ajax(settings).done(function (xml) {
					    $(xml).find("version").each(function () {
					        var number = $(this).find("number").text().trim();
					        var date = $(this).find("date").text().trim();
					        var description = $(this).find("description").text().trim();
					        $("#output").append(
					            `<div class="update">
					                <h5 class="text-info">Version ${number}<br /><small>${date}</small></h5>                
					                <p>${description}</p>
					            	<hr />
					             </div>`
					        );
					    });
					});
				</script>							
			</div>
		</div>
	</div>
		<div class="col-md-6"><?php echo $this->element('display_policy', array(), array('plugin' => 'PasswordSettingManager'));?></div>
		<div class="col-md-12 col-xs-12  hidden-xs">
			<div class="panel panel-default">
				<div class="panel-heading"><div class="panel-title"><?php echo __('Latest News & Updates');?></div></div>
					<div class="panel-body">
						<div id="newsoutput"></div>
					</div>
				</div>
			</div>
			<script>
				var settings = {
				    url: "https://www.flinkiso.com/flinkiso-updates/news.xml",
				    method: "GET",
				    timeout: 0,
				    dataType: "xml"
				};

				$.ajax(settings).done(function (xml) {
				    $(xml).find("news").each(function () {
				        var title = $(this).find("title").text().trim();
				        var date = $(this).find("date").text().trim();
				        var description = $(this).find("description").text().trim();
				        var link = $(this).find("link").text().trim();

				        var $newsBlock = $("<div>", { class: "news-item" });

				        $newsBlock.append(`<h5 class="text-info">${title}<br />`);
				        $newsBlock.append(`<small>${date}</small></h4>`);
				        $newsBlock.append(`<p>${description}</p>`);

				        
				        $("<a>", {
				            href: link,
				            target: "_blank",
				            rel: "noopener noreferrer",
				            text: link
				        }).appendTo($newsBlock);

				        $("#newsoutput").append($newsBlock);
				    });
				});
			</script>						
		</div>		
	</div>
<?php
}
?>
