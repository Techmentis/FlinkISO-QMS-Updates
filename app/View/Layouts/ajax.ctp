<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
?>
<?php echo $this->fetch('content'); ?>
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
<script>
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
?>