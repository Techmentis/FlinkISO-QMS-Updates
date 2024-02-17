<?php
/**
 * @link          https://cakephp.org CakePHP(tm) Project
 * @package       app.View.Errors
 * @since         CakePHP(tm) v 0.10.0.1076
 */
?>
<?php if($this->Session->read('User'))echo $this->element('nav-header-lists',array('postData'=>array('pluralHumanName'=>'Error','modelClass'=>'Error','options'=>array(),'pluralVar'=>'errors'))); ?>
<h2><?php echo $message; ?></h2>

<p class="text-danger">
	<strong><?php echo __d('cake', 'Error'); ?>: </strong>
	<?php echo __d('cake', 'An Internal Error Has Occurred.'); ?>
</p>

<p><hr /></p>
<h4>Possible Causes:</h4>
<p>
	<ul>
		<li>Database is not connected.
			<ul>
				<li>Make sure that you have imported app/webroot/schema/flinkiso-on-premise.sql</li>
				<li>Make sure to add database configuration at app/Config/Database.php under default config.</li>
			</ul>
		</li>
		<li>Directory permissions are not correct
			<ul>
				<li>Following folders must have read-write-execute permissions (recursive)
					<ul>
						<li>app/webtoot</li>
						<li>app/tmp</li>
						<li>lib/Cake/Cache</li>						
					</ul>
				</li>
				
			</ul>
		</li>
		<li>Missing tables or incorrect models.</li>
	</ul>
</p>
<p>If this error is occuring during the installation, visit: <a href="https://www.flinkiso.com/manual/installation.html">https://www.flinkiso.com/manual/installation.html</a> and follow the installation instructions.</p>
<p>You can also use FlinkISO Forum : <a href="https://forum.flinkiso.com/">https://forum.flinkiso.com/</a> for possible resolution.</p>

<p>If you have successfully installed the application and error is occuring after creating a custom HTML form, this error can be resolved by recreating the form.</p>
<p>If this error is coming after creating a child form or a form linked to a child document, <strong class="text-warning">recreate</strong> the parent form.</p>
<p><strong>Additional Info:</strong>Remove "STRICT_TRANS_TABLES" for sql_mode from MySQL configuration file.</p>
<hr />
<?php
if (Configure::read('debug') > 0):
	echo $this->element('exception_stack_trace');
endif;
