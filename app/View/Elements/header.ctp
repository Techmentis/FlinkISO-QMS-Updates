<header class="main-header">
  <nav class="navbar navbar-default navbar-fixed-top ">
    <div class="container-fluid">
      <!-- Logo -->
      <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
          <span class="fa fa-th"></span>
        </button>
        <?php echo $this->Html->link('FlinkISO',array('controller'=>'users','action'=>'dashboard'),array('class'=>'navbar-brand'));?>
      </div>

      <!-- Header Navbar: style can be found in header.less -->
      <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
        <ul class="nav navbar-nav navbar-left">
          <li class=" hidden-xs hidden-sm" style="margin-top:15px; color:#fff; padding:0px 8px 0 5px">|</li>
          <?php 
          echo '<li>'. $this->Html->link('<i class="fa fa-dashboard"></i>',array('controller'=>'users', 'action'=>'dashboard','timestamp'=>date('ymdhis')),
            array('class'=>'tooltip1 btn','escape'=>false,
              'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'bottom', 'title'=> 'Dashboard'
            )).'</li>';

          echo '<li>'. $this->Html->link('<i class="fa fa-bookmark"></i>',array('controller'=>'standards', 'action'=>'index','timestamp'=>date('ymdhis')),
            array('class'=>'tooltip1 btn','escape'=>false,
              'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'bottom', 'title'=> 'Standards'
            )).'</li>';

          echo '<li>'. $this->Html->link('<i class="fa fa-navicon"></i>',array('controller'=>'qc_document_categories', 'action'=>'index','timestamp'=>date('ymdhis')),
            array('class'=>'tooltip1 btn','escape'=>false,
              'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'bottom', 'title'=> 'Document Categories'
            )).'</li>';

          echo '<li>'. $this->Html->link('<i class="fa fa-folder-open"></i>',array('controller'=>'qc_documents', 'action'=>'index','timestamp'=>date('ymdhis')),
            array('class'=>'tooltip1 btn','escape'=>false,
              'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'bottom', 'title'=> 'Documents'
            )).'</li>';
          echo '<li>'. $this->Html->link('<i class="fa fa-chain"></i>',array('controller'=>'processes', 'action'=>'index','timestamp'=>date('ymdhis')),
            array('class'=>'tooltip1 btn','escape'=>false,
              'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'bottom', 'title'=> 'Processes'
            )).'</li>';
          echo '<li>'. $this->Html->link('<i class="fa fa-gears"></i>',array('controller'=>'custom_tables', 'action'=>'index','timestamp'=>date('ymdhis')),
            array('class'=>'tooltip1 btn','escape'=>false,
              'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'bottom', 'title'=> 'Forms'
            )).'</li>';
          echo '<li>'. $this->Html->link('<i class="fa fa-file-text"></i>',array('controller'=>'pdf_templates','action'=>'index','timestamp'=>date('ymdhis')),
            array('class'=>'tooltip1 btn','escape'=>false,
              'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'bottom', 'title'=> 'Pdf Templates'
            )).'</li>';

            ?>                
            <li class="dropdown" id="mm">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i  id="mega-menu" class="fa fa-database"></i></a>
              <ul class="dropdown-menu mega-menu get-size">
                <?php foreach($menus as $standard => $types){  ?>     
                  <li class="sub-li"><div class="header-li"><?php echo $standard;?></div>
                    <ul class="">
                      <?php foreach($types as $type => $customTables){ ?>             
                        <li ><a href="#" class="header-li-sub"><span><?php echo $type;?></span></a>
                          <ul class="">
                            <?php foreach($customTables as $customTable){ ?>
                              <li><?php echo $this->Html->link($customTable['CustomTable']['name'],array(
                                'controller'=>$customTable['CustomTable']['table_name'],
                                'action'=>'index',
                                'qc_document_id'=>$customTable['CustomTable']['qc_document_id'],
                                'custom_table_id'=>$customTable['CustomTable']['id'],
                                'process_id'=>$customTable['CustomTable']['process_id'],
                                'timestamp'=>date('ymdhis')
                                ));?></li>
                              <?php } ?>              
                            </ul>
                          </li>
                        <?php } ?>
                      </ul>
                    </li>
                  <?php } ?>
                </ul>
              </li>       
            </ul>
            <!-- Navbar Right Menu -->
            <?php if($this->Session->read('User')) echo $this->Element('top-menu');?>
          </div>
        </div>
      </nav>
    </header>
