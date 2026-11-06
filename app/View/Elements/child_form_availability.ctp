<?php
$parentTable = $customTable['CustomTable'];
$savedAvailability = !empty($parentTable['tab_settings']) ? json_decode($parentTable['tab_settings'], true) : array();
$childAvailability = !empty($savedAvailability['child_tables']) && is_array($savedAvailability['child_tables']) ? $savedAvailability['child_tables'] : array();
?>
<?php if(!empty($childs)): ?>
    <div id="child-form-availability">
        <p class="help-block" style="font-size:13px"><?php echo h(__('Add rules to show, hide or make a child form read-only. Restrictions on the containing tab still apply. Save Rules & Apply rebuilds the module automatically.')); ?></p>
        <?php echo $this->Form->create('CustomTable', array('url'=>array('action'=>'update_child_form_settings', $parentTable['id']), 'id'=>'update-child-form-settings')); ?>
        <?php echo $this->Form->hidden('child_form_settings', array('id'=>'ChildFormAvailabilitySettings')); ?>
        <div class="table-responsive">
            <table class="table table-bordered" id="child-form-availability-table">
                <thead>
                    <tr>
                        <th><?php echo h(__('Name')); ?></th>
                        <th><?php echo h(__('Rules')); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach((array)$childs as $child):
                        $childTable = $child['CustomTable'];
                        $setting = isset($childAvailability[$childTable['table_name']]) ? $childAvailability[$childTable['table_name']] : array();
                        ?>
                        <tr data-child-table="<?php echo h($childTable['table_name']); ?>">
                            <td style="min-width:180px">
                                <strong><?php echo h($childTable['name']); ?></strong><small class="text-muted" style="display:block;overflow-wrap:anywhere"><?php echo h($childTable['table_name']); ?></small><?php if(!empty($childTable['description'])): ?><div class="text-muted" style="font-size:12px;margin-top:6px;white-space:normal;overflow-wrap:anywhere"><?php echo h($childTable['description']); ?></div><?php endif; ?>
                                    Ver: <?php echo h(isset($childTable['table_version']) ? $childTable['table_version'] : ''); ?> | <?php echo h(!empty($childTable['publish']) ? __('Published') : __('Unpublished')); ?>
                                <br /><br />
                                    <?php if(!empty($childTable['id'])){
                                    echo $this->Html->link('<i class="fa fa-cogs text-warning"></i>', array('action'=>'view', $childTable['id'], 'timestamp'=>date('ymdhis')), array('class'=>'btn btn-sm btn-default tooltip1', 'escape'=>false, 'title'=>__('Open child table')));
                                    echo $this->Html->link('<i class="fa fa-refresh text-warning"></i>', array('action'=>'unlock', $childTable['id'], 'next_action'=>'recreate_child', 'timestamp'=>date('ymdhis')), array('class'=>'btn btn-sm btn-default  tooltip1', 'escape'=>false, 'title'=>__('Recreate child form')));
                                    if(!empty($childTable['table_locked'])) echo $this->Html->link('<i class="fa fa-trash-o text-danger"></i>', array('action'=>'delete_child', $childTable['id']), array('class'=>'btn btn-sm btn-default tooltip1', 'escape'=>false, 'title'=>__('Delete child table')));
                                } ?>
                            </td>
                            <td style="min-width:760px"><?php echo $this->element('availability_rule_builder',array('availabilitySetting'=>$setting)); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="text-right" style="clear:both;padding-top:20px;background:#fff;border:0">
            <?php echo $this->Form->submit(__('Save Rules & Apply'), array('class'=>'btn btn-sm btn-success', 'div'=>false)); ?>
        </div>
        <?php echo $this->Form->end(); ?>
    </div>
<?php endif; ?>
