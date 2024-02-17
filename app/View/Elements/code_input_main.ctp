<div class="box box-default collapsed-box">
	<div class="box-header data-header" data-widget="collapse"><h3 class="box-title"><span class=""><i class="fa fa-code"></i></span> Add Javascripts </h3>
			<div class="box-tools pull-right"><button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button></div>
	</div>
	<div class="box-body">
		<?php echo $this->Form->create('CustomTable',array('action'=>'save_scripts'),array('role'=>'form','class'=>'form','type'=>'post'));
			echo $this->Form->input('id',array('default'=>$this->request->params['pass'][0]));
		?>
		<h5>&nbsp;&nbsp;ADD Form: <?php echo Inflector::singularize(Inflector::classify($table['CustomTable']['table_name']));?>AddForm</h5>		
		<code-input name="data[CustomTable][add_form_script]" required id="add-demo" style="resize: both; overflow: hidden; " lang="JavaScript" placeholder="Write some JavaScript!" template="add"><?php 
		if($table['CustomTable']['add_form_script']){echo $table['CustomTable']['add_form_script'];}else{
			echo
"$().ready(function(){
	//add your add page code here
})";};?>
		</code-input>
		<h5>&nbsp;&nbsp;Edit Form: <?php echo Inflector::singularize(Inflector::classify($table['CustomTable']['table_name']));?>EditForm</h5>		
		<code-input name="data[CustomTable][edit_form_script]" required id="edit-demo" style="resize: both; overflow: hidden; " lang="JavaScript" placeholder="Write some JavaScript!" template="edit"><?php 
		if($table['CustomTable']['edit_form_script']){echo $table['CustomTable']['edit_form_script'];}else{
			echo
"$().ready(function(){
	//add your add page code here
})";};?>
		</code-input>
		<?php 
			echo $this->Form->submit('Save Scripts',array('class'=>'btn btn-sm btn-success'));
			echo $this->Form->end();
		?>
	</div>
	<div class="box-footer">Do not add < script > tag. System will add it dynamically.
	<br /><strong>Credit: </strong><a href="https://github.com/WebCoder49/code-input" target="_blank">https://github.com/WebCoder49/code-input</a>
	</div>
	</div>
</div>
