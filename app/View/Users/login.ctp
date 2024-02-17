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
	</style>
	<div  id="users_ajax"> 
		<?php echo $this->Session->flash(); ?>
		<div class="row">
			<div class="col-md-6 col-xs-12 hidden-xs">

				<div class="panel">
					<div class="box-header with-border"><h3 class="box-title">FlinkISO&trade; <small>2.0</small> : Quality Management System</h3></div>
					<div class="panel-body">				
						<ul>
							<li>Add standards, clauses, document categories</li>
							<li>Create, issue, distribute documents</li>
							<li>Integrated with Onlyoffice&trade;</li>
							<li>Supported formats : doc,docx,xls,xlsx,odt,txt,ppt</li>
							<li>Upload documnent section</li>
							<li>Build your own forms linked to the documents</li>
							<li>Approval system</li>
							<li>Email Notifications</li>
							<li>Access controls</li>
							<li>Add business rules & tasks</li>
						</ul>
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
		</div>
		<div class="col-md-6"><?php echo $this->element('display_policy', array(), array('plugin' => 'PasswordSettingManager'));?></div>
		<?php echo $this->Form->end();?>
		</div>
		<div class="row">
			<?php if(Configure::read('WkHtmlToPdfPath') == ''){ ?>
				<div class="col-md-12">
					<div class="alert alert-danger">You must install WkHtmlToPdf and update its binery path in core.php for PDF export to function properly.</div>
				</div>
			<?php } ?>
			<?php if(Configure::read('PDFTkPath') == ''){ ?>
				<div class="col-md-12">
					<div class="alert alert-danger">You must install PDFTk Server and update its binery path in core.php for PDF export to function properly.</div>
				</div>
			<?php } ?>
		</div>
		<div class="row">
			<div class="col-md-12 col-xs-12 ">
				<div class="panel panel-default">
					<div class="panel-heading"><div class="panel-title"><?php echo __('Application Updates');?></div></div>
					<div class="panel-body">
						<?php
						try {
							$updates = Xml::build('http://www.flinkiso.com/flinkiso-updates/application-updates.xml', array(
								'return' => 'simplexml'
							));

							if ($updates) {
								foreach ($updates as $update):	
									if($update->number > $company_message['Company']['version']){ ?>
										<div class="row">
											<div class="col-md-12">
												<h4>Update Available!</h4>
												<p>Version : <?php echo $update->number;?><br />
													Details : <?php echo $update->description;?><br />
													<strong>You can update the application once you are logged in.</strong></p>
													<br /><small class="text-danger">Update & Install action is not reversible. We strongly recomend to take backup of your existing installed application before updating it.</small></div>
												</div>
											<?php }else if($update->number == $company_message['Company']['version']){

											}else{
												echo "There are no updates.";
											}
										endforeach;

									} else {
										echo "There are no updates.";
									}

								}
								catch (Exception $e) {
									echo "<h5 class='text-danger'>There are no updates.</small></h5>";
								}
								?>
							</div>
						</div>
					</div>

					<div class="col-md-12 col-xs-12">
						<div class="panel panel-default">
							<div class="panel-heading"><div class="panel-title"><?php echo __('Latest News & Updates');?></div></div>
							<div class="panel-body">
								<?php
								try {
									$xml = Xml::build('http://www.flinkiso.com/flinkiso-updates/news.xml', array(
										'return' => 'simplexml'
									));

									if ($xml) {
										foreach ($xml as $news):
											echo "<h5 class='text-info'>" . (string) $news->title . "<br /><small>" . (string) $news->date . "</small></h5>";
											echo "<p>" . (string) $news->description . "</p>";
											echo "<p><a href=" . (string) $news->link . " class='text-link' target='_blank'>" . (string) $news->link . "</a></p><br />";
										endforeach;

									} else {
										echo "Can not access updates";
									}

								}
								catch (Exception $e) {
									echo "<h5 class='text-danger'>There are no updates.</small></h5>";
								}
								?>
							</div>
						</div>
					</div>		
				</div>
				<?php
			}
			?>
