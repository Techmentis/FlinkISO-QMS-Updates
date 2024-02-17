<ul class="nav navbar-nav navbar-right">
  <?php 
  if($this->Session->read('User.is_mr') == true){
    echo '<li class="">'.$this->Html->link('<i class="fa fa-graduation-cap"></i>',array('controller'=>'trainings','action'=>'index','timestamp'=>date('ymdhis')),array('class'=>'tooltip1', 'escape'=>false,'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'bottom', 'title'=> 'Trainings')).'</li>';      

    echo '<li class="">'.$this->Html->link('<i class="fa fa-cloud-download"></i>'.$update,array('controller'=>'billing','action'=>'update','timestamp'=>date('ymdhis')),array('class'=>'tooltip1','escape'=>false,'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'bottom', 'title'=> 'Updates')).'</li>';      

    echo '<li class="">'.$this->Html->link('<i class="fa fa-bar-chart"></i>',array('controller'=>'billing','action'=>'usage_details','timestamp'=>date('ymdhis')),array('class'=>'tooltip1','escape'=>false,'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'bottom', 'title'=> 'Check Usage Details')).'</li>';      
  }
  ?>
  
  <li class="dropdown notifications-menu">    
    <?php
    echo $this->Html->link('<i class="fa fa-credit-card"></i><div id="billingcount"></div>','#',array('class'=>'dropdown-toggle','data-toggle'=>'dropdown' , 'aria-expanded'=>'true', 'escape'=>false));        
    ?>        
    <ul class="dropdown-menu">
      <li>
        <ul class="menu">
          <?php
          if($this->Session->read('User.trial_balance_days') <= 15 && $this->Session->read('User.trial_balance_days') != 0){
            echo "<li>".$this->element('trial_balance_days',array('trial_expired'=>0 ,'days'=>$this->Session->read('User.trial_balance_days')))."</li>";
          }else{
            echo "<li>".$this->element('trial_balance_days',array('trial_expired'=>1 , 'days'=> $app_status['end_date']))."</li>";
          }?>
        </ul>            
      </li>
      <li class="footer">
        <?php 
        echo $this->Html->link('Vew Invoices',array('controller'=>'billing','action'=>'invoices','pending','timestamp'=>date('ymdhis')),array('class'=>'dropdown-toggle','data-toggle'=>'dropdown' ,'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'bottom', 'title'=> 'Billing'));
        ?>        
      </li>
      
    </ul>
  </li>
  <li class="dropdown user user-menu">
    <a href="#" class="dropdown-toggle user-menu-small" data-toggle="dropdown">
      <?php
      if(file_exists(WWW_ROOT . 'img' . DS . $this->Session->read('User.company_id') . DS . 'profile' . DS . $this->Session->read('User.employee_id') . DS . 'profile.png')){
        echo $this->Html->image($this->Session->read('User.company_id') . DS . 'profile' . DS . $this->Session->read('User.employee_id') . DS . 'profile.png',array('class'=>'img-circle user-image'));
      }else{
        echo $this->Html->image('img/avatar.png',array('class'=>'img-circle user-image'));
      }
      ?>
      <span class="hidden-xs"><?php echo $this->Session->read('User.name'); ?></span>
    </a>
    <ul class="dropdown-menu userprofile">
      <li class="user-header hidden-xs">
        <?php
        if(file_exists(WWW_ROOT . 'img' . DS . $this->Session->read('User.company_id') . DS . 'profile' . DS . $this->Session->read('User.employee_id') . DS . 'profile.png')){
          echo $this->Html->image($this->Session->read('User.company_id') . DS . 'profile' . DS . $this->Session->read('User.employee_id') . DS . 'profile.png',array('class'=>'img-circle'));
        }else{ ?>
         <?php echo $this->Html->image('img/avatar.png',array('class'=>'img-circle'));?> 
       <?php }
       ?>
       
       <p>
        <?php echo $this->Session->read('User.name');?>
        <small><?php echo $this->Session->read('User.branch');?></small>
      </p>
    </li>    
    <li class="user-footer">
      <div class="pull-left">
        <?php echo $this->Html->link('Reset Password',array('controller'=>'users','action'=>'change_password','timestamp'=>date('ymdhis')),array('class'=>'btn btn-default btn-flat')); ?>
        <?php echo $this->Html->link('Profile',array('controller'=>'employees','action'=>'view', $this->Session->read('User.employee_id'),'timestamp'=>date('ymdhis')),array('class'=>'btn btn-default btn-flat')); ?>
      </div>
      <div class="pull-right">
        <?php echo $this->Html->link('Logout',array('controller'=>'users','action'=>'logout','timestamp'=>date('ymdhis')),array('class'=>'btn btn-default btn-flat')); ?>
      </div>
    </li>
  </ul>
</li>
</ul>

