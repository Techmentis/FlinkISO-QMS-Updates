<?php
    if($this->request->controller == 'custom_tables' || $this->request->params['named']['custom_table_id'] != '')echo $this->Html->link('Custom Tables',array('controller'=>'custom_tables','action'=>'index'),array('class'=>'text')).' /';
    if($this->request->controller == 'qc_documents')echo $this->Html->link('Documents',array('controller'=>'qc_documents','action'=>'index'),array('class'=>'text')).' /';
    $controllers = array('branches','departments','employees','users','designations');
    
    if(in_array($this->request->controller,$controllers)){
        // echo '<div class="pull-right>"';
        echo $this->Html->link('Branches',array('controller'=>'branches','action'=>'index'),array('class'=>'text')).' | ';
        echo $this->Html->link('Departments',array('controller'=>'departments','action'=>'index'),array('class'=>'text')).' | ';
        echo $this->Html->link('Designations',array('controller'=>'designations','action'=>'index'),array('class'=>'text')).' | ';
        echo $this->Html->link('Employees',array('controller'=>'employees','action'=>'index'),array('class'=>'text')).' | ';
        echo $this->Html->link('Users',array('controller'=>'users','action'=>'index'),array('class'=>'text')).' | ';
        // echo '</div>"';
    }
?>
