<?php
    if($this->request->controller == 'custom_tables' || $this->request->params['named']['custom_table_id'] != '')echo $this->Html->link(__('Custom Tables'),array('controller'=>'custom_tables','action'=>'index','table_type'=>1),array('class'=>'text')).' /';
    if($this->request->controller == 'qc_documents')echo $this->Html->link(__('Documents'),array('controller'=>'qc_documents','action'=>'index'),array('class'=>'text')).' /';
    $controllers = array('branches','departments','employees','users','designations');
    
    if(in_array($this->request->controller,$controllers)){
        // echo '<div class="pull-right>"';
        echo $this->Html->link(__('Branches'),array('controller'=>'branches','action'=>'index'),array('class'=>'text')).' | ';
        echo $this->Html->link(__('Departments'),array('controller'=>'departments','action'=>'index'),array('class'=>'text')).' | ';
        echo $this->Html->link(__('Designations'),array('controller'=>'designations','action'=>'index'),array('class'=>'text')).' | ';
        echo $this->Html->link(__('Employees'),array('controller'=>'employees','action'=>'index'),array('class'=>'text')).' | ';
        echo $this->Html->link(__('Users'),array('controller'=>'users','action'=>'index'),array('class'=>'text')).' | ';
        // echo '</div>"';
    }
?>
