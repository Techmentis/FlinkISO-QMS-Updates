<div class="row"> 
    <div class="col-md-12"><h4><?php 
    if(!$header)echo 'Billing';
    else echo $header;
?></h4>
</div>
<div class="col-md-8 col-sm-12">
    <?php
    echo $this->Html->link('<i class="fa fa-dashboard"></i>',array('controller'=>'users', 'action'=>'dashboard'),
        array('class'=>'tooltip1 btn btn btn-app btn-sm btn-default','escape'=>false,
            'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'bottom', 'title'=> 'Dashboard'
        ));

    echo $this->Html->link('<i class="fa fa-graduation-cap"></i>',array('controller'=>'trainings', 'action'=>'index'),
        array('class'=>'tooltip1 btn btn btn-app btn-sm btn-default','escape'=>false,
            'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'bottom', 'title'=> 'Trainings'
        ));

    if($this->request->controller == 'trainings'){
        echo $this->Html->link('<i class="fa fa-plus"></i>',array('controller'=>'trainings', 'action'=>'trainings'),
            array('class'=>'tooltip1 btn btn btn-app btn-sm btn-default','escape'=>false,
                'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'bottom', 'title'=> 'Add Trainings'
            ));
    }

    echo $this->Html->link('<i class="fa fa-credit-card text-danger"></i>',array('controller'=>'billing', 'action'=>'invoices','pending'),
        array('class'=>'tooltip1 btn btn btn-app btn-sm','escape'=>false,
            'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'bottom', 'title'=> 'Pending Invoices'
        ));

    echo $this->Html->link('<i class="fa fa-credit-card text-success"></i>',array('controller'=>'billing', 'action'=>'invoices','paid'),
        array('class'=>'tooltip1 btn btn btn-app btn-sm','escape'=>false,
            'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'bottom', 'title'=> 'Paid Invoices'
        ));
    
    $named = $this->request->params['named'];
    foreach($named as $name => $name_value){
        $str .= $name .':' . $name_value .'/';
    }
    ?>
</div>
</div>
