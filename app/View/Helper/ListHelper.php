<?php
class ListHelper extends AppHelper{
	public $helpers = array('Form','Html');
	public function showlist(
		$name = null,
		$model = null,
		$controller = null,
		$label = null,
		$value = null,
		$disabled = null,
		$func = false,
		$required = false,
		$recid = false
	){
		// echo $model;
		// if($value){
		// 	$this->loadModel($model);

		// 	$rec = $this->$model->find('first',
		// 			array('condition'=>array($model.'.'.$this->$model->displayField => $value),
		// 				'fields'=>array($model.'.'.'id',$model.'.'.$this->$model->displayField),
		// 				)
		// 		);
		// 	$val = $rec[$model][$this->$model->displayField];
		// }

		$aname = $name ."_auto";
		$afield = Inflector::Humanize($name);
		$afield = Inflector::Classify($afield);
		$aid = Inflector::Classify($controller) . $afield . 'Auto';
		$aid = str_replace('.', '', $aid);

		$field = Inflector::Humanize($name);
		$field = Inflector::Classify($field);
		$id = Inflector::Classify($controller) . $field;
		$id = str_replace('.', '', $id);		
		$html = '
		<style type="text/css">	
		.ui-autocomplete, .ui-front, .ui-menu, .ui-widget, .ui-widget-content{
			z-index:2
		}
		.ui-menu .ui-menu-item{
			background:#d6d2d2;
			padding:6px 10px;
			font-size:12px;  	
		}
		.ui-state-focus,
		.ui-state-hover,
		.ui-widget-content .ui-state-focus,
		.ui-widget-content .ui-state-hover,
		.ui-widget-header .ui-state-focus,
		.ui-widget-header .ui-state-hover {
			background:#d6d2d2;
			padding:6px 10px;
			font-size:12px;
			color:#fff !important;
			font-weight:normal !important;
			background:#333 !important;
			border:0px;  	
		}';
		$html .= '#'.$aid.'{
			background:#ededed;
			border-bottom: 2px solid #00a65a;
		}';

		$html .= '#'. $aid.'-submit-indicator{
			float:right;
			display:block;
			position:relative;
			margin-top:-25px;
			margin-right:5px;	
		}';

		$html .= '</style>';


		if($required == true)$required = 'required';
		else $required = '';

		
		if($label){
		// echo "1";
			if($func == true){
				$html .= "<div class='ui-widget'>".$this->Form->input($aname,array($required,'onChange'=>'getDetailRecs()','id'=>"$aid",'type'=>'textfield','label'=>$label,'default'=>$value,'disabled'=>$disabled))."</div>";
			}else{
				$html .= "<div class='ui-widget'>".$this->Form->input($aname,array($required, 'placeholder'=>'Search ' . $label,  'id'=>"$aid",'type'=>'textfield','label'=>$label,'default'=>$value,'disabled'=>$disabled))."</div>";
			}
			
		}else{
		// echo "2";
			$label = str_replace('_auto', '', $field);
			$label = Inflector::Humanize($field);
			$label = substr($label, 0, -3);
		// $html .= "<div class='ui-widget'>".$this->Form->input($aname,array('id'=>"$aid",'type'=>'textfield','label'=>$label,'default'=>$value,'disabled'=>$disabled))."</div>";
			if($func == true){
				$html .= "<div class='ui-widget'>".$this->Form->input($aname,array($required,'onChange'=>'getDetailRecs()','id'=>"$aid",'type'=>'textfield','label'=>false,'default'=>$value,'disabled'=>$disabled))."</div>";
			}else{
				$html .= "<div class='ui-widget'>".$this->Form->input($aname,array($required, 'placeholder'=>'Search ' . $label,'id'=>"$aid",'type'=>'textfield','label'=>false,'default'=>$value,'disabled'=>$disabled))."</div>";
			}
		}
		$html .= $this->Html->image('indicator.gif', array('id' => $aid.'-submit-indicator'));
		$html .= "<div class='hide'>".$this->Form->input($name,array('id'=>"$id",'type'=>'text','div'=>false,'label'=>false,'value'=>$recid))."</div>";
		
		
		
		// $html .= '<div class="ui-widget" style="margin-top:2em; font-family:Arial"></div>';



		$html .= '
		<script type="text/javascript">
		$("#'.$aid.'-submit-indicator").hide();
		$( function() {		    
			$( "#'.$aid.'" ).autocomplete({
				search  : function(){$("#'.$aid.'-submit-indicator").show();},
				response  : function(){$("#'.$aid.'-submit-indicator").hide();},
				source: "'.Router::url("/", true) . $this->request->params["controller"]. '/get_list/model:'.$model.'" ,
				minLength: 2,				
				select: function( event, ui ) {
					$( "#'.$id.'" ).val(ui.item.id);
				}
				} );
				} );	
				$.ajaxSetup({beforeSend:function(){$("#'.$aid.'-submit-indicator").show();},complete:function(){$("#'.$aid.'-submit-indicator").hide();}});
				</script>

				';
				
				return $html;
			}
		}
		?>
