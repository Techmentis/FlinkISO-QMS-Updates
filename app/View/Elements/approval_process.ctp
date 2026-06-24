<?php if(!empty($approvalProcess)){ ?>
    <div class="box box-default">
        <div class="box-header with-border data-header" data-widget="collapse">
            <h3 class="box-title">
                <span class="text-gray-dark"><?php echo $approvalProcess['ApprovalProcess']['title'];?></span>
            </h3>
            <p><?php echo $approvalProcess['ApprovalProcess']['process_description'];?></p>
        </div>
        <div class="box-body no-padding">
            <ul class="list-group">        	
            <?php foreach($approvalProcess['ApprovalStep'] as $approvalStep){ ?>
                <?php
                    if($approvalStep['id'] == $currentStep['ApprovalStep']['id']) $class = " active";
                    else $class = "";
                ?>
                <li class="list-group-item <?php echo $class;?>">
                    <strong><?php echo $approvalStep['title'];?></strong>
                    <p class="text"><?php echo $approvalStep['comments'];?></p>                
                </li>   
            <?php } ?>
            </ul>
        </div>
    </div>
<?php } ?>