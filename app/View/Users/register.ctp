<?php if(isset($register) && $register == false){ ?>
    <div class="main">
        <div class="row">
            <div class="col-md-12 text-center">            
                <!-- <i class="fa fa-check-circle-o" style="font-size: 120px; font-weight: 10;"></i> -->
                <p><h1 class="text-primary">Congratulations!<br /><small>Your instance is now ready</small></h1></p>            
                <p><br /><h5>Your username & password is your email address you have entered during the registration.<br />We have also sent you an email with your username & password along with the login link. <br /> Please save that email for future reference.<br />You can reset the password after login.</h5></p>
                <p><br />
                    <div class="btn-group">
                        <?php echo $this->Html->link('Access Your Instance','https://cloud.flinkiso.com/'.$url.'/users/login',array('class'=>'btn btn-lg btn-info')); ?>
                        <?php echo $this->Html->link('<i class="fa fa-caret-square-o-right"></i>','https://cloud.flinkiso.com/'.$url.'/users/login',array('class'=>'btn btn-lg btn-info','escape'=>false)); ?>
                    </div>
                </p>
            </div>
        </div>
    </div>


<?php }else{ ?>
    <div class="main">
        <div class="row">
            <div class="col-md-12 text-center">            
                <!-- <i class="fa fa-check-circle-o" style="font-size: 120px; font-weight: 10;"></i> -->
                <p><h1 class="text-primary">Congratulations!<br /><small>You are ready to setup the application.</small></h1></p>            
                <p><br /><h5>In order to continue, enter your email address which you have used while creating your accoung on https://www.flinkiso.com and click proceed.</h5><br /><br /></p>
                <p>
                    <div class="row">
                        <?php 
                        echo $this->Form->create('register',array('role'=>'form','class'=>'form','default'=>true));
                        echo "<div class='col-md-2 text-right'><label><br />Email:</label></div><div class='col-md-8'>" . $this->Form->input('email',array('label'=>false, 'class'=>'form-control','required')) . '</div>'; 
                        echo "<div class='col-md-2 text-left'>" .$this->Form->submit('Procced',array('div' => false, 'class' => 'btn btn-primary btn-success','id'=>'submit_id')) . '</div>'; 
                        echo $this->Form->end();
                        ?>
                    </div>
                </p>
            </div>
        </div>
    </div>
<?php } ?>
