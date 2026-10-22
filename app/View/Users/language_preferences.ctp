<div class="panel panel-default">
    <div class="panel-heading"><h3><?php echo __('Language'); ?></h3></div>
    <div class="panel-body">
        <?php echo $this->Session->flash(); ?>
        <?php echo $this->Form->create('User', array('url' => array('controller' => 'users', 'action' => 'language_preferences'))); ?>
        <input type="hidden" name="data[token]" value="<?php echo h($languageToken); ?>">
        <?php echo $this->Form->input('language_id', array('label' => __('Language'), 'options' => $languages, 'class' => 'form-control', 'empty' => __('Select an available language.'))); ?>
        <p><?php echo __('Your language preference is saved to your profile. Untranslated text appears in English.'); ?></p>
        <?php echo $this->Form->end(array('label' => __('Save'), 'class' => 'btn btn-primary')); ?>
        <p><?php echo $this->Html->link(__('Profile'), array('controller' => 'employees', 'action' => 'view', $this->Session->read('User.employee_id'))); ?></p>
    </div>
</div>
