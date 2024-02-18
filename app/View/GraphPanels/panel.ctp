<?php if($result){ 
$graph = $graphTypes[$record['GraphPanel']['graph_type']];
  if($record['GraphPanel']['color'] == '')$record['GraphPanel']['color'] = 'aqua';
  ?>
  <?php $id = $this->request->params['pass'][0] ;?>
  <div class="small-box bg-<?php echo $record['GraphPanel']['color'];?> box-panels">
    <div class="inner">
      <h4><?php echo Inflector::humanize($record['GraphPanel']['field_name']);?><br /><small><?php echo $record['CustomTable']['name'];?></small></h4>      
      <p>
      	<table class="table table-responsive table-panel">
         <?php 
         unset($result['total']);
         ksort($result);      	
         foreach($result as $r){ ?>
          <tr>
           <td><?php echo $r['labels'];?></td>
           <td><?php echo $r['data'];?></td>
         </tr>
       <?php }?>
     </table>
   </p>
 </div>
 <div class="icon">
   <?php echo $result['total'];?>
 </div> 
</div>
<?php }else{ ?>
	<script type="text/javascript">
		$("#<?php echo $this->request->params['pass'][0]?>_div").remove();
	</script>
<?php } ?>
