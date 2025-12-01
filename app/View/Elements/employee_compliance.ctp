<?php
	// 0=>'Any user should update a single document for a defined schedule',
	// 1=>'Every user should update a saperate document for a defined schedule',
	// 2=>'Multiple users should update a single document for a defined schedule',		
?>
<div id="employee-comp-data" style="min-height:420px">
	<style>
		.nomargin{margin-top: 0px !important}
	</style>
	<div class="row">
		<div class="col-md-6">
			<h3><?php echo $customTable['CustomTable']['name'];?><br /><small>Employee Compliance Report</small></h3>
		</div>
		<div class="col-md-6">
			<h3><?php echo $schedules[$customTable['CustomTable']['schedule']];; ?></h3>
			<?php 
				if($customTable['CustomTable']['data_update_type'] == 1)echo "Every user should update a saperate document for a defined schedule.";
				if($customTable['CustomTable']['data_update_type'] == 2)echo "Multiple users should update a single document for a defined schedule.";
				if($customTable['CustomTable']['data_update_type'] == 0)echo "Any user should update a single document for a defined schedule.";
			?>
		</div>
	</div>
	<div class="row">
		<?php echo $this->Form->create(Inflector::classify($customTable['CustomTable']['table_name']),array('id'=>'copmform', 'class'=>'form','default'=>false));?>
		<div class="col-md-3"><?php echo $this->Form->input('department_id',array('class'=>'form-control', 'multiple'=>true, 'options'=>$PublishedDepartmentList));?></div>
		<div class="col-md-3"><?php echo $this->Form->input('branch_id',array('class'=>'form-control','multiple'=>true,'options'=>$PublishedBranchList));?></div>
		<div class="col-md-3"><?php echo $this->Form->input('date_range',array('class'=>'form-control','id'=>'adddaterange'));?></div>
		<div class="col-md-2"><?php echo $this->Form->submit('Submit',array('class'=>'btn btn-md btn-default','style'=>'margin-top:28px'));?></div>
		<div class="col-md-1"><i class="fa fa-refresh fa-spin pull-right" style="margin-top:40px" id="refspin"></i></div>
		<?php echo $this->Form->end();?>
	</div>
	<div class="row">
		<div class="col-md-12"><br />
			<table class="table table-bordered">
				<tr><th rowspan="2"><h4>Duration</h4></th>
					<?php foreach($users as $userid => $username){ ?>
						<th colspan="2"><?php echo $username;?></th>
					<?php } ?>
						<th colspan="2">Total</th>
						<th rowspan="2" style="background-color:#f5f5f5">%</th>
				</tr>
				<tr>
					<?php foreach($users as $userid => $username){ ?>
						<td>Expected</td>
						<td>Actual</td>
					<?php } ?>
						<th>Expected</th>
						<th>Actual</th>
				</tr>
			<?php
			foreach($results as $duration => $data){ ?>
				<tr>
					<th><?php echo $duration;?></th>
					<?php foreach($users as $userid => $username){ ?>
						<?php
							echo '<td style="background-color:#fff;color:#c0c0c0">'.$data[$username]['expected'].'</td>';								
							if($data[$username] && $data[$username]['actual'] > 0){
								$hav[$duration]['actual'] = $hav[$duration]['actual'] + $data[$username]['actual'];


								$hev[$duration]['expected'] = $hev[$duration]['expected'] + $data[$username]['expected'];
								$vav[$username]['actual'] = $vav[$username]['actual'] + $data[$username]['actual'];
								$vav[$username]['expected'] = $vav[$username]['expected'] + $data[$username]['expected'];
 								echo '<td style="background-color:#fff" ><strong>' . $data[$username]['actual'] . '</strong></td>';
							}else{
								$hev[$duration]['expected'] = $hev[$duration]['expected'] + $data[$username]['expected'];
								$vav[$username]['expected'] = $vav[$username]['expected'] + $data[$username]['expected'];
								echo '<td style="background-color:#fff;color:#c0c0c0">0</td>';
							}?>
					<?php } ?>
					<th><?php 
							if($customTable['CustomTable']['data_update_type'] == 0){								
								$hev[$duration]['expected'] = 1;
							}else{
								echo $hev[$duration]['expected'];
							}?></th>
					<th><?php 
							if($hav[$duration]['actual'])echo $hav[$duration]['actual'];
							else echo '<span style="color:#c0c0c0">0</span>' ?>
					</th>					
					<th style="background-color:#f5f5f5"><?php					
						if($hav[$duration]['actual'] != 0 && $hev[$duration]['expected'] != 0){							
							echo round($hav[$duration]['actual'] * 100 / $hev[$duration]['expected'],2);
						}else{
							echo 0;
						}
					?>%</th>
				</tr>
			<?php }?>
			<tr style="background-color:#f5f5f5">
				<th>Total</th>
				<?php foreach($vav as $user => $totals){ ?>
					<th><?php echo $totals['expected'];?></th>
					<th><?php echo $totals['actual'];
						
						if($totals['expected'] !=  0 && $totals['actual'] != 0){
							echo '<small class="pull-right"> '. round(($totals['actual'] * 100 /$totals['expected']),2 ) . '%</small>';
						}else{
							echo '<small class="pull-right"> 0%</small>';
						}
					
					?></th>
					<?php
						$totalexpected = $totalexpected + $totals['expected'];
						$totalactual = $totalactual + $totals['actual'];
					?>
				<?php } ?>
				<th>

					<?php 
					if($customTable['CustomTable']['data_update_type'] == 0){
						$diff = $days = date_diff(date_create($eDate),date_create($sDate));
						echo  $days->days;
						$totalexpected = $days->days;
					}else{
						echo $totalexpected;
					}
					
					?></th>
				<th><?php echo $totalactual;?></th>
				<th style="background: #000; color: #fff;"><?php 
					if($totalexpected > 0 && $totalactual > 0){
						echo round( $totalactual * 100 / $totalexpected, 2); 
					}else{						
						echo 0;
					}
				?>%</th>
			</tr>
			</table>
		</div>
	</div>
	<script>
		$().ready(function(){	
			$("#refspin").hide();
			<?php if(isset($sDate) && isset($eDate)){ ?>
				var startDate = "<?php echo date('mm/dd/Y',strtotime($sDate));?>";
				var endDate = "<?php echo date('mm/dd/Y',strtotime($eDate));?>";
			<?php }else{ ?>
				var endDate = new Date();
				var startDate = new Date();
				startDate.setMonth(endDate.getMonth() - 1);
			<?php } ?>
			$("#adddaterange").daterangepicker({
				startDate: startDate, // after open picker you'll see this dates as picked
    			endDate: endDate,
			});

			$("#copmform").on('submit',function(){
				$("#refspin").show();
				$("#employee-comp-data").load("<?php echo Router::url('/', true); ?><?php echo $this->request->params['controller'] ?>/employee_compliance/custom_table_id:<?php echo $this->request->params['named']['custom_table_id']?>",$("#copmform").serializeArray());				
			});

			$('select').chosen();
		})	
	</script>
</div>
