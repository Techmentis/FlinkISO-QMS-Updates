<div class="row">
    <div class="col-md-12">
        <?php echo $this->Session->flash(); ?>            
    </div>
</div>
<div class="access " style="text-align:center;">
    
    <div  id="users_ajax">
        <div class="" style="margin-top:5%; text-align:center">
            <div class="container">
                <i class="fa fa-5x fa-exclamation-triangle text-danger" aria-hidden="true"></i>
                <h1><span  class="text-danger"><?php echo __("Access Denied"); ?></span></h1>                
                <p><?php echo __("You do not have sufficient permissions to access this page. <br/>Contact your administrator for permissions related issues."); ?></p>
                <p><br /><?php echo $this->Html->link('Back','#',array('class'=>'btn btn-danger btn-xl','onclick'=>'history.back()'));?></p>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
                <?php
                debug($this->request->params['pass']);
                switch ($this->request->params['pass'][1]) {
                    case '01':
                        echo "<h4>Possible Reason</h4>";
                        echo "You do not have permissions to view records from the other branches or records created by other users.";
                        echo "<h4>Suggested Solution</h4>";
                        echo "Ask your administrator to allow you access to the branch from this this record is originated.<br />";
                        echo "Also set up is_view_all permission to true from user's edit page.";
                        break;

                    case '02':
                        echo "<h4>Possible Reason</h4>";
                        echo "Restricted access, not enough permissions.";
                        echo "<h4>Suggested Solution</h4>";
                        echo "If you are trying to add/ edit/ view Custom HTML Form, then you may not have create, edit or view permissions. You may ask your administrator to update your permissions.<br />";
                        echo "If you are trying to access other sections of the application, ask your administrator to assign you an administrator role if you need access to this section.<br />";
                        break;
                    
                    default:
                        // echo "Unknown";
                        break;
                }

                ?>        
        </div>
    </div>
</div>
