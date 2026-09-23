<ul class="nav navbar-nav navbar-right">
  <?php 
	  if (Configure::read('AI.ai_enabled') === true) {
	    echo '<li class="fi-ai-nav-item"><a href="#" id="ask_ai_icon" class="tooltip1" aria-controls="load_ai_container" aria-expanded="false" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="FlinkISO AI">'. $this->Html->image("ai-svgrepo-com.svg",array("width"=>"20px")).'
	<span class="sr-only">Open FlinkISO AI</span></a></li>';
	  }
  if($this->Session->read('User.is_mr') == true){
    echo '<li class="">'.$this->Html->link('<i class="fa fa-cloud-download"></i>'.$update,array('controller'=>'billing','action'=>'update','timestamp'=>date('ymdhis')),array('class'=>'tooltip1','escape'=>false,'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'bottom', 'title'=> 'Updates')).'</li>';
  }
  ?>
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
        <small><?php echo $this->Session->read('User.department');?> | <?php echo $this->Session->read('User.branch');?></small>
        <div class="flinkiso-theme-picker" id="flinkisoThemePicker" aria-label="Application theme">
          <button type="button" class="theme-swatch theme-azure" data-theme="azure" title="Modern Azure" aria-label="Modern Azure"></button>
          <button type="button" class="theme-swatch theme-corporate" data-theme="corporate" title="Corporate Blue" aria-label="Corporate Blue"></button>
          <button type="button" class="theme-swatch theme-steel" data-theme="steel" title="Steel Blue" aria-label="Steel Blue"></button>
          <button type="button" class="theme-swatch theme-indigo" data-theme="indigo" title="Indigo" aria-label="Indigo"></button>
          <button type="button" class="theme-swatch theme-teal" data-theme="teal" title="Teal" aria-label="Teal"></button>
          <button type="button" class="theme-swatch theme-navy" data-theme="navy" title="Navy" aria-label="Navy"></button>
          <button type="button" class="theme-swatch theme-graphite" data-theme="graphite" title="Graphite" aria-label="Graphite"></button>
          </div>
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
