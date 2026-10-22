<?php echo $this->Html->script(array('jquery.validate.min', 'jquery-form.min')); ?>
<?php echo $this->fetch('script'); ?>
<div id="users_ajax">
    <?php echo $this->Session->flash(); ?>    
    <div class="row panel panel-default">
        <div class="col-md-1"></div>
        <div class="users form col-md-10">
            <h1><?php echo h(__('Welcome to FlinkISO')); ?>&trade;</h1>
            <h4><?php echo h(__('Your first step towards standardizing your Quality Management System.')); ?></h4>
            <hr />
            <h4><?php echo h(__('It is required that you add following to get you started.')); ?></h4>
            <br />
            <p>
                <div class="row">
                    <div class="col-md-1"><?php echo $this->Html->image('add_branches.png') ?></div>
                    <div class="col-md-11">
                        <strong><?php echo h(__('Branches:')); ?></strong>
                        <p><?php echo h(__('We have created one "Default" branch for you during your registration. You can rename this branch to your desired name. If your organization has multiple branches,locations, you can add all of them as well.')); ?></p>
                        <br />
                    </div>
                </div>
                
                <div class="clear-fix"></div>
                
                <div class="row">
                    <div class="col-md-1"><?php echo $this->Html->image('add_emloyees.png') ?></div>
                    <div class="col-md-11">
                        <strong><?php echo h(__('Employees:')); ?></strong>
                        <p><?php echo h(__('Once you create branches, we suggest you add the list of your employees. You can use our Import-Export functionality to add bulk employees and save time.')); ?></p>
                        <br />
                    </div>
                </div>
                
                <div class="clear-fix"></div>
                
                <div class="row">
                    <div class="col-md-1"><?php echo $this->Html->image('add_users.png') ?></div>
                    <div class="col-md-11">
                        <strong><?php echo h(__('Users:')); ?></strong>
                        <p><?php echo h(__('Once you add employees, you can add the list of employees who would be the users of FlinkISO. Users are those employees who will have access to this application, who are responsible for day-to-day QMS activities. These users (employees) are then responsible to maintain')); ?> & <?php echo h(__('update FlinkISO by adding required data, material on a daily basis.')); ?></p>
                        <br />
                    </div>
                </div>
                
                <div class="clear-fix"></div>
                
                
                <div class="row">
                    <div class="col-md-1"><?php echo $this->Html->image('add_branches.png') ?></div>
                    <div class="col-md-11">
                        <strong><?php echo h(__('Update Company Setting:')); ?></strong>
                        <p><?php echo h(__('You can complete your company profile by adding, welcome message, Message from director, Quality Policy, Vision Statement, Mission Statement. You can also upload  (copy-paste) your "Quality Manual"')); ?> & <?php echo h(__('current "Audit Plan" (for reference only) under company setting. These information will be visible to all non-MR users post their respective logins.')); ?></p>
                        <br />
                    </div>
                </div>                                
                <div class="clear-fix"></div>    
                <div class="row">                
                    <div class="col-md-12">
                        <strong><?php echo h(__('Others:')); ?></strong>
                        <p><?php echo h(__('The following data can also be uploaded in bulk using the import / export functionality : List Of Trainers , Products, Devices ,Materials, List of Customers')); ?></p>
                    </div>
                </div>
                <div class="clear-fix"></div>    
                <div class="row">
                  <div class="col-md-12 text-center"><?php echo $this->Html->link(__('Goto Dashboard'),array('action'=>'add_formats',1),array('class'=>array('btn btn-lg btn-success'))); ?><br /><br /><br /></div>	
              </div>
          </div>

