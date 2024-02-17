
<div class="row hide"><div class="col-md-12"><?php echo $this->Session->flash(); ?></div></div>
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
</div>
