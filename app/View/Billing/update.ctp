<?php echo $this->element('billing_header_lists',array('header'=>'Update'));?>
<div class="row">
	<div class="col-md-12">
		<div class="row">
		<div class="col-md-12 col-xs-12">
			<div class="panel panel-default">
				<div class="panel-heading"><div class="panel-title"><h4>Update Available!</h4></div></div>
				<div class="panel-body" id="update_div">
				<?php
				    try {
				        $updates = Xml::build('http://www.flinkiso.com/flinkiso-updates/application-updates.xml', array(
				            'return' => 'simplexml'
				        ));
				        if ($updates) {
				            foreach ($updates as $update):	
				            	if($update->number > $this->Session->read('User.version')){ ?>
				                	<div class="row">
				                		<div class="col-md-12">
						                	<p>Version : <?php echo $update->number;?><br />
						                	Details : <?php echo $update->description;?><br />
						                	Release Date : <?php echo $update->date;?>
						                </p>
						                	<hr />
						                	<div class="btn btn-success btn-sm" id="update_btn">Install</div>
				                		</div>
				                	</div>
				                <?php }else if($update->number == $company_message['Company']['version']){				                	
				                }else{
				                	
				                }
				            endforeach;
				            
				        } else {
				            
				        }
				        
				    }
				    catch (Exception $e) {
				        echo "<h5 class='text-danger'> Can not access updates</small></h5>";
				    }
					?>
					<small class="text-danger"><br />Update & Install action is not reversible. We strongly recomend to take backup of your existing installed application before updating it.</small>					
				</div>
			</div>
		</div>
	</div>
	</div>
</div>
<div class="row hide">
	<div class="col-md-12">
		<div class="box box-solid">
	        <div class="box-body">
	            <h4 style="background-color:#f7f7f7; font-size: 14px; padding: 7px 10px; margin-top: 0;">
	                MODULES
	            </h4>
	            <div class="media">
	                <?php echo $this->element('modules');?>
	            </div>
	        </div>
	    </div>
	</div>
</div>

<script type="text/javascript">
	$("#update_btn").on('click',function(){
		$.ajax({
			async: true,
			url: "<?php echo Router::url('/', true); ?>billing/authentication",
				beforeSend: function(){
				     $("#update_div").html("Update started .. Authenticating... please wait");
				},
				success: function(data, result) {
					// $(".fa-spin").removeClass('show').addClass('hide');
					$("#update_div").html(data);
				},
			});
		});
</script>