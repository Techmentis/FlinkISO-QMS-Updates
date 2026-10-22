<ul class="nav navbar-nav navbar-right">
  <?php 
	  if (Configure::read('AI.ai_enabled') === true && $this->Session->read('User.id')) {
	    echo '<li class="fi-ai-nav-item"><a href="#" id="ask_ai_icon" class="tooltip1" aria-controls="load_ai_container" aria-expanded="false" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="FlinkISO AI">'. $this->Html->image("ai-svgrepo-com.svg",array("width"=>"20px")).'
	<span class="sr-only">'.h(__('Open FlinkISO AI')).'</span></a></li>';
	  }
  if($this->Session->read('User.is_mr') == true){
    echo '<li class="">'.$this->Html->link('<i class="fa fa-cloud-download"></i>'.$update,array('controller'=>'billing','action'=>'update','timestamp'=>date('ymdhis')),array('class'=>'tooltip1','escape'=>false,'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'bottom', 'title'=> __('Updates'))).'</li>';
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
        <div class="flinkiso-theme-picker" id="flinkisoThemePicker" aria-label="<?php echo h(__('Application theme')); ?>">
          <button type="button" class="theme-swatch theme-azure" data-theme="azure" title="<?php echo h(__('Modern Azure')); ?>" aria-label="<?php echo h(__('Modern Azure')); ?>"></button>
          <button type="button" class="theme-swatch theme-corporate" data-theme="corporate" title="<?php echo h(__('Corporate Blue')); ?>" aria-label="<?php echo h(__('Corporate Blue')); ?>"></button>
          <button type="button" class="theme-swatch theme-steel" data-theme="steel" title="<?php echo h(__('Steel Blue')); ?>" aria-label="<?php echo h(__('Steel Blue')); ?>"></button>
          <button type="button" class="theme-swatch theme-indigo" data-theme="indigo" title="<?php echo h(__('Indigo')); ?>" aria-label="<?php echo h(__('Indigo')); ?>"></button>
          <button type="button" class="theme-swatch theme-teal" data-theme="teal" title="<?php echo h(__('Teal')); ?>" aria-label="<?php echo h(__('Teal')); ?>"></button>
          <button type="button" class="theme-swatch theme-navy" data-theme="navy" title="<?php echo h(__('Navy')); ?>" aria-label="<?php echo h(__('Navy')); ?>"></button>
          <button type="button" class="theme-swatch theme-graphite" data-theme="graphite" title="<?php echo h(__('Graphite')); ?>" aria-label="<?php echo h(__('Graphite')); ?>"></button>
          </div>
        </p>
    </li>    
    <?php if (!empty($languagePreferenceAvailable)): ?>
    <li><?php echo $this->Html->link(__('Language'), array('controller' => 'users', 'action' => 'language_preferences')); ?></li>
    <?php endif; ?>
    <li class="user-footer">
      <div class="pull-left">
        <?php echo $this->Html->link(__('Reset Password'),array('controller'=>'users','action'=>'change_password','timestamp'=>date('ymdhis')),array('class'=>'btn btn-default btn-flat')); ?>
        <?php echo $this->Html->link(__('Profile'),array('controller'=>'employees','action'=>'view', $this->Session->read('User.employee_id'),'timestamp'=>date('ymdhis')),array('class'=>'btn btn-default btn-flat')); ?>
      </div>
      <div class="pull-right">
        <?php echo $this->Html->link(__('Logout'),array('controller'=>'users','action'=>'logout','timestamp'=>date('ymdhis')),array('class'=>'btn btn-default btn-flat')); ?>
      </div>
    </li>
  </ul>
</li>
</ul>
