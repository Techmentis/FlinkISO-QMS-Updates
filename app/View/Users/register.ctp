<div class="main">
    <?php echo $this->Session->flash(); ?>
    <div class="row">
        <div class="col-md-12">
            <!-- <i class="fa fa-check-circle-o" style="font-size: 120px; font-weight: 10;"></i> -->
            <p><h1 class="text-primary"><?php echo h(__('Congratulations!')); ?><br /><small><?php echo h(__('You are ready to setup the application.')); ?></small></h1></p>            
            <p><br /><h5><?php echo h(__('In order to continue, enter your email address which you have used while creating your accoung on https://www.flinkiso.com and click proceed.')); ?></h5></p>
            <p>
                <div class="row">
                    <?php 
                    echo $this->Form->create('register',array('role'=>'form','class'=>'form','default'=>true));
                    echo "<div class='col-md-8'>" . $this->Form->input('email',array('label'=>__('Enter your email'), 'class'=>'form-control','required')) . '</div>'; 
                    echo "<div class='col-md-4 text-left'><br />" .$this->Form->submit(__('Procced'),array('div' => false, 'class' => 'btn btn-primary btn-success','id'=>'submit_id','style'=>'margin-top:8px')) . '</div>'; 
                    echo $this->Form->end();
                    ?>
                </div>
            </p>
        </div>
        <div class="col-md-12">
            <p>
                <h4><?php echo h(__('Note:')); ?></h4>
                <ul>
                    <li><?php echo h(__('Make sure CURL is installed and enabled')); ?></li>
                    <li><?php echo h(__('Check')); ?> <strong>/var/www/html/flinkiso/app/tmp/logs/debug.log</strong> <?php echo h(__('for cURL specific error')); ?></li>
                    <li><?php echo h(__('Check')); ?> <strong>/var/www/html/flinkiso/app/tmp/logs/error.log</strong> <?php echo h(__('for any other error')); ?></li>
                    <li><?php echo h(__('Make sure you are connected to the internet')); ?></li>
                    <li><?php echo h(__('In case you are unable to proceed, try running')); ?> <strong><?php echo h(__('TRUNCATE companies; TRUNCATE departments; TRUNCATE designations; TRUNCATE employees; TRUNCATE users;')); ?></strong> <?php echo h(__('from phpmyadmin and then try again')); ?></li>
                    <li><?php echo h(__('Contact help@flnkiso.com if the issue persists.')); ?></li>
                    <li><?php echo h(__('Visit')); ?> <a href="https://forum.flinkiso.com/posts/index/category_id:59128f8e-7ceb-11ee-89b5-e6b8e553f0bb" target="_blank">https://forum.flinkiso.com/posts/index/application_installation/</a> <?php echo h(__('for forum help.')); ?></li>
                </ul>
            </p>
        </div>
    </div>
</div>

