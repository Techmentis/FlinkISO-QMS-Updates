<div id="customTriggers_ajax">
<?php echo $this->Session->flash();?>	
<div class="customTriggers ">
   <?php echo $this->element('nav-header-lists',array('postData'=>array('pluralHumanName'=>'Custom Triggers','modelClass'=>'CustomTrigger','options'=>array(),'pluralVar'=>'customTriggers'))); ?>
   <div class="nav panel panel-default">
      <div class="customTriggers form col-md-12">
         <table class="table table-responsive">
            <tr>
               <td><?php echo __('Custom Table'); ?></td>
               <td>
                  <?php echo $this->Html->link($customTrigger['CustomTable']['name'], array('controller' => 'custom_tables', 'action' => 'view', $customTrigger['CustomTable']['id'])); ?>
                  &nbsp;
               </td>
            </tr>
            <tr>
               <td><?php echo __('Name'); ?></td>
               <td>
                  <?php echo h($customTrigger['CustomTrigger']['name']); ?>
                  &nbsp;
               </td>
            </tr>
            <tr>
               <td><?php echo __('Details'); ?></td>
               <td>
                  <?php echo h($customTrigger['CustomTrigger']['details']); ?>
                  &nbsp;
               </td>
            </tr>
            <tr>
               <td><?php echo __('File Name'); ?></td>
               <td>
                  <?php echo h($customTrigger['CustomTrigger']['file_name']); ?>
                  &nbsp;
               </td>
            </tr>
            <tr>
               <td><?php echo __('Changed Field Value'); ?></td>
               <td>
                  <?php echo h($customTrigger['CustomTrigger']['changed_field_value']); ?>
                  &nbsp;
               </td>
            </tr>
            <tr>
               <td><?php echo __('Notify User'); ?></td>
               <td>
                  <?php echo h($customTrigger['CustomTrigger']['notify_user']); ?>
                  &nbsp;
               </td>
            </tr>
            <tr>
               <td><?php echo __('Notify Users'); ?></td>
               <td>
                  <?php echo h($customTrigger['CustomTrigger']['notify_users']); ?>
                  &nbsp;
               </td>
            </tr>
            <tr>
               <td><?php echo __('If Added'); ?></td>
               <td>
                  <?php echo h($customTrigger['CustomTrigger']['if_added']); ?>
                  &nbsp;
               </td>
            </tr>
            <tr>
               <td><?php echo __('If Edited'); ?></td>
               <td>
                  <?php echo h($customTrigger['CustomTrigger']['if_edited']); ?>
                  &nbsp;
               </td>
            </tr>
            <tr>
               <td><?php echo __('If Publish'); ?></td>
               <td>
                  <?php echo h($customTrigger['CustomTrigger']['if_publish']); ?>
                  &nbsp;
               </td>
            </tr>
            <tr>
               <td><?php echo __('If Approved'); ?></td>
               <td>
                  <?php echo h($customTrigger['CustomTrigger']['if_approved']); ?>
                  &nbsp;
               </td>
            </tr>
            <tr>
               <td><?php echo __('If Soft Delete'); ?></td>
               <td>
                  <?php echo h($customTrigger['CustomTrigger']['if_soft_delete']); ?>
                  &nbsp;
               </td>
            </tr>
            <tr>
               <td><?php echo __('Recipents'); ?></td>
               <td>
                  <?php echo h($customTrigger['CustomTrigger']['recipents']); ?>
                  &nbsp;
               </td>
            </tr>
            <tr>
               <td><?php echo __('Cc'); ?></td>
               <td>
                  <?php echo h($customTrigger['CustomTrigger']['cc']); ?>
                  &nbsp;
               </td>
            </tr>
            <tr>
               <td><?php echo __('Bcc'); ?></td>
               <td>
                  <?php echo h($customTrigger['CustomTrigger']['bcc']); ?>
                  &nbsp;
               </td>
            </tr>
            <tr>
               <td><?php echo __('Subject'); ?></td>
               <td>
                  <?php echo h($customTrigger['CustomTrigger']['subject']); ?>
                  &nbsp;
               </td>
            </tr>
            <tr>
               <td><?php echo __('Message'); ?></td>
               <td>
                  <?php echo h($customTrigger['CustomTrigger']['message']); ?>
                  &nbsp;
               </td>
            </tr>
            <tr>
               <td><?php echo __('Branch'); ?></td>
               <td>
                  <?php echo $this->Html->link($customTrigger['Branch']['name'], array('controller' => 'branches', 'action' => 'view', $customTrigger['Branch']['id'])); ?>
                  &nbsp;
               </td>
            </tr>
            <tr>
               <td><?php echo __('Department'); ?></td>
               <td>
                  <?php echo $this->Html->link($customTrigger['Department']['name'], array('controller' => 'departments', 'action' => 'view', $customTrigger['Department']['id'])); ?>
                  &nbsp;
               </td>
            </tr>
            <tr>
               <td><?php echo __('Designation'); ?></td>
               <td>
                  <?php echo $this->Html->link($customTrigger['Designation']['name'], array('controller' => 'designations', 'action' => 'view', $customTrigger['Designation']['id'])); ?>
                  &nbsp;
               </td>
            </tr>
            <tr>
               <td><?php echo __('Prepared By'); ?></td>
               <td><?php echo h($customTrigger['ApprovedBy']['name']); ?>&nbsp;</td>
            </tr>
            <tr>
               <td><?php echo __('Approved By'); ?></td>
               <td><?php echo h($customTrigger['ApprovedBy']['name']); ?>&nbsp;</td>
            </tr>
            <tr>
               <td><?php echo __('Soft Delete'); ?></td>
               <td>
                  <?php if($customTrigger['CustomTrigger']['soft_delete'] == 1) { ?>
                  <span class="fa fa-check"></span>
                  <?php } else { ?>
                  <span class="fa fa-close"></span>
                  <?php } ?>&nbsp;
               </td>
               &nbsp;</td>
            </tr>
            <tr>
               <td colspan="2">
                  <?php echo $this->Html->link('Delete This Record?', array('controller' => 'designations', 'action' => 'delete', $customTrigger['Designation']['id']),array('class'=>'btn btn-sm btn-danger')); ?>
                  <?php echo $this->Html->link('Edit This Record?', array('controller' => 'designations', 'action' => 'delete', $customTrigger['Designation']['id']),array('class'=> 'btn btn-sm btn-warning')); ?>
               </td>
            </tr>
         </table>
      </div>
   </div>
   <?php echo $this->Js->writeBuffer();?>
</div>
<script>$.ajaxSetup({beforeSend:function(){$("#busy-indicator").show();},complete:function(){$("#busy-indicator").hide();}});</script>