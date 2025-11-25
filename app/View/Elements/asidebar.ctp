<?php if($this->Session->read('User.id')){ ?>
  <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- Sidebar user panel -->
      <div class="user-panel">
        <div class="pull-left image">
          <?php
          if(file_exists(WWW_ROOT . 'img' . DS . $this->Session->read('User.company_id') . DS . 'profile' . DS . $this->Session->read('User.employee_id') . DS . 'profile.png')){
            echo $this->Html->image($this->Session->read('User.company_id') . DS . 'profile' . DS . $this->Session->read('User.employee_id') . DS . 'profile.png',array('class'=>'img-circle user-image'));
          }else{
            echo $this->Html->image('img/avatar.png',array('class'=>'img-circle user-image'));
          }
          ?>
        </div>
        <div class="pull-left info">
          <p><?php echo $this->Session->read('User.name');?></p>
          <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
        </div>
      </div>
      <ul class="sidebar-menu">
        <li class="header"><?php echo __('MAIN NAVIGATION');?></li>
        <li class="treeview">
          <a href="#">
            <i class="fa fa-dashboard"></i> <span><?php echo __('Dashboard');?></span> <i class="fa fa-angle-left pull-right"></i>
          </a>
          <ul class="treeview-menu">
            <li><?php echo $this->Html->link(__('Dashboard'), array('controller' => 'users', 'action' => 'dashboard','timestamp'=>date('ymdhis'))); ?></li>
            <li><?php echo $this->Html->link(__('Custom Tables'), array('controller' => 'custom_tables', 'action' => 'index','timestamp'=>date('ymdhis'))); ?></li>
          </ul>
        </li>
        <li class="treeview">
          <a href="#">
            <i class="fa fa-server"></i>
            <span><?php echo __('Masters'); ?> </span>            
          </a>
          <ul class="treeview-menu">            
            <li><?php echo $this->Html->link(__('Branches'), array('controller' => 'branches', 'action' => 'index','timestamp'=>date('ymdhis'))); ?></li>
            <li><?php echo $this->Html->link(__('Departments'), array('controller' => 'departments', 'action' => 'index','timestamp'=>date('ymdhis'))); ?></li>
            <li><?php echo $this->Html->link(__('Designations'), array('controller' => 'designations', 'action' => 'index','timestamp'=>date('ymdhis'))); ?></li>            
            <li><?php echo $this->Html->link(__('Employees'), array('controller' => 'employees', 'action' => 'index','timestamp'=>date('ymdhis'))); ?></li>
            <li><?php echo $this->Html->link(__('Employee Org Chart'), array('controller' => 'employees', 'action' => 'org_chart','timestamp'=>date('ymdhis'))); ?></li>
            <li><?php echo $this->Html->link(__('Designation Org Chart'), array('controller' => 'designations', 'action' => 'org_chart','timestamp'=>date('ymdhis'))); ?></li>
            <li><?php echo $this->Html->link(__('Users'), array('controller' => 'users', 'action' => 'index','timestamp'=>date('ymdhis'))); ?></li>           
          </ul>
        </li>
        <li class="treeview">
          <a href="#">
            <i class="fa fa-bookmark"></i>
            <span><?php echo __('Standards'); ?> </span> <i class="fa fa-angle-left pull-right"></i>         
          </a>
          <ul class="treeview-menu">
            <li><?php echo $this->Html->link(__('Add New Standard'), array('controller' => 'standards', 'action' => 'index','timestamp'=>date('ymdhis'))); ?></li>
            <li><?php echo $this->Html->link(__('Add New Clause'), array('controller' => 'clauses', 'action' => 'add','timestamp'=>date('ymdhis'))); ?></li>
            <li><?php echo $this->Html->link(__('New Document Category'), array('controller' => 'qc_document_categories', 'action' => 'index','timestamp'=>date('ymdhis'))); ?></li>                        
          </ul>
        </li>
        <li class="treeview">
          <a href="#">
            <i class="fa fa-folder-open"></i>
            <span><?php echo __('Document Management'); ?> </span> <i class="fa fa-angle-left pull-right"></i>
          </a>
          <ul class="treeview-menu">
            <li><?php echo $this->Html->link(__('Add New Document'), array('controller' => 'qc_documents', 'action' => 'add','timestamp'=>date('ymdhis'))); ?></li>
            <li><?php echo $this->Html->link(__('List Of Documents'), array('controller' => 'qc_documents','action'=>'index','timestamp'=>date('ymdhis'))); ?></li>
            <li><?php echo $this->Html->link(__('Add Template'), array('controller' => 'templates','action'=>'index','timestamp'=>date('ymdhis'))); ?></li>
          </ul>
        </li>
        <li class="treeview">
          <a href="#">
            <i class="fa fa-chain"></i>
            <span><?php echo __('Processes'); ?> </span> <i class="fa fa-angle-left pull-right"></i>
          </a>
          <ul class="treeview-menu">
            <li><?php echo $this->Html->link(__('Add New Process'), array('controller' => 'processes', 'action' => 'add','timestamp'=>date('ymdhis'))); ?></li>
            <li><?php echo $this->Html->link(__('List Of Processes'), array('controller' => 'processes','action'=>'index','timestamp'=>date('ymdhis'))); ?></li>            
          </ul>
        </li>        
        <li class="treeview">
          <a href="#">
            <i class="fa fa-pie-chart"></i>
            <span><?php echo __('Graph Panels'); ?> </span> <i class="fa fa-angle-left pull-right"></i>
          </a>
          <ul class="treeview-menu">
            <li><?php echo $this->Html->link('Graphs',
            array('controller' => 'graph_panels', 'action' => 'graphs','timestamp'=>date('ymdhis'))); ?>                  
          </li>
        </ul>
      </li>          
      <li class="treeview">
        <a href="#">
          <i class="fa fa-cog"></i>
          <span><?php echo __('Settings'); ?> </span> <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="treeview-menu">
          <li><?php echo $this->Html->link(__('Add Logo'), array('controller' => 'settings', 'action' => 'edit', $this->Session->read('User.company_id') , 'timestamp'=>date('ymdhis'))); ?></li>
          <li><?php echo $this->Html->link(__('Add SMTP Email'), array('controller' => 'settings', 'action' => 'smtp_details', $this->Session->read('User.company_id') , 'timestamp'=>date('ymdhis'))); ?></li>
          <li><?php echo $this->Html->link(__('Add Password Policy'), array('controller' => 'settings', 'action' => 'password_setting', $this->Session->read('User.company_id') , 'timestamp'=>date('ymdhis'))); ?></li>
          <li><?php echo $this->Html->link(__('Define Change History Table'), array('controller' => 'qc_documents', 'action' => 'define_change_history_table', 'timestamp'=>date('ymdhis'))); ?></li>
        </ul>
      </li>
    </li>
    
  </ul>
</section>
<!-- /.sidebar -->
</aside>
<?php } ?>
