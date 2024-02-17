<?php foreach($menus as $standard => $types){  ?>    	
	<li class="treeview"><a href="#"><i class="fa fa-database"></i><span><?php echo $standard;?></span><i class="fa fa-angle-left pull-right"></i></a>
		<ul class="treeview-menu">
			<?php foreach($types as $type => $customTables){ ?>	    				
				<li class="treeview"><a href="#"><span><?php echo $type;?></span><i class="fa fa-angle-left pull-right"></i></a>
					<ul class="treeview-menu">
						<?php foreach($customTables as $customTable){ ?>
							<li><?php echo $this->Html->link($customTable['CustomTable']['name'],array(
								'controller'=>$customTable['CustomTable']['table_name'],
								'action'=>'index',
								'qc_document_id'=>$customTable['CustomTable']['qc_document_id'],
								'custom_table_id'=>$customTable['CustomTable']['id'],
								'process_id'=>$customTable['CustomTable']['process_id']));?></li>
							<?php } ?>	    				
						</ul>
					</li>
				<?php } ?>
			</ul>
		</li>
	<?php } ?>

