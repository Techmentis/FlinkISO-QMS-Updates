<?php echo $this->Session->flash();?>
<?php if(!$found){ ?>
	<div class="row">
		<div class="col-md-12"><h3>Delete Table</h3></div>
		<div class="col-md-12">
			<i class="fa fa-exclamation-triangle text-danger fa-lg"></i>&nbsp; <span class="text-danger">This will delete all the Forms, Data for this table as well as child tables. This action can not be reversed.</span><hr />
		</div>
		<?php 
		echo $this->Form->create('CustomTable',array('action'=>'delete'),array('class'=>'form'));
		echo $this->Form->input('id',array('default'=>$this->request->params['pass'][0]));
		echo '<div class="col-md-4">'.$this->Form->input('password',array('type'=>'password','class'=>'form-control')).'</div>';
		echo '<div class="col-md-2"><br />'.$this->Form->submit('Submit',array('class'=>'btn btn-sm btn-success')).'</div>';
		echo $this->Form->end();
		?>	
	</div>

<?php }else{ ?>
	<div class="col-md-12"><h3>Can Not Delete This Table</h3></div>
		<div class="col-md-12"><p>
			<i class="fa fa-exclamation-triangle text-danger fa-lg"></i>&nbsp; <span class="text-danger">
				<?php  if($found){
			        echo "This table is linked with with the following table/s: <ol>";
			        foreach($found as $tables){
			            echo "<li>" . $tables . "</li>";
			        }
			        echo "</ol>";
			        echo "First removed the linked fields from these tables and then you can delete this table.</p>";
			    }

			    echo $this->Html->link('Back',array('action'=>'view', $this->data['CustomTable']['id']),array('class'=>'btn btn-sm btn-warning'));
			    ?>
			    </span><hr />
			</div>
		</div>
<?php } ?>
