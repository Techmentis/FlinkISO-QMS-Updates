<div id="approvalComments_ajax">
<?php echo $this->Session->flash();?>	
<div class="approvalComments ">
   <?php echo $this->element('nav-header-lists',array('postData'=>array('pluralHumanName'=>'Approval Comments','modelClass'=>'ApprovalComment','options'=>array(),'pluralVar'=>'approvalComments'))); ?>
   <div class="nav panel panel-default">
      <div class="approvalComments form col-md-12">
         <table class="table table-responsive">
            <tr>
               <td><?php echo __('Approval'); ?></td>
               <td><?php echo $this->Html->link($approvalComment['Approval']['id'], array('controller' => 'approvals', 'action' => 'view', $approvalComment['Approval']['id'])); ?>&nbsp;</td>
            </tr>
            <tr>
               <td><?php echo __('User'); ?></td>
               <td><?php echo $this->Html->link($approvalComment['User']['name'], array('controller' => 'users', 'action' => 'view', $approvalComment['User']['id'])); ?>&nbsp;</td>
            </tr>
            <tr>
               <td><?php echo __('Comments'); ?></td>
               <td><?php echo h($approvalComment['ApprovalComment']['comments']); ?>&nbsp;</td>
            </tr>
            <tr>
               <td><?php echo __('Publish'); ?></td>
               <td>
                  <?php if($approvalComment['ApprovalComment']['publish'] == 1) { ?>
                  <span class="fa fa-check"></span>
                  <?php } else { ?>
                  <span class="fa fa-close"></span>
                  <?php } ?>&nbsp;						
                  &nbsp;
               </td>
            </tr>
            <tr>
               <td><?php echo __('Soft Delete'); ?></td>
               <td>
                  <?php if($approvalComment['ApprovalComment']['soft_delete'] == 1) { ?>
                  <span class="fa fa-check"></span>
                  <?php } else { ?>
                  <span class="fa fa-close"></span>
                  <?php } ?>&nbsp;
                  &nbsp;
               </td>
            </tr>
            <tr>
               <td colspan="2"><?php echo $this->Html->link('Delete This Record?', array('controller' => 'users', 'action' => 'delete', $approvalComment['ModifiedBy']['id']),array('class'=>'btn btn-sm btn-danger')); ?>
                  <?php echo $this->Html->link('Edit This Record?', array('controller' => 'users', 'action' => 'delete', $approvalComment['ModifiedBy']['id']),array('class'=> 'btn btn-sm btn-warning')); ?>
               </td>
            </tr>
         </table>
      </div>
   </div>
   <?php echo $this->Js->writeBuffer();?>
</div>
<script>$.ajaxSetup({beforeSend:function(){$("#busy-indicator").show();},complete:function(){$("#busy-indicator").hide();}});</script>