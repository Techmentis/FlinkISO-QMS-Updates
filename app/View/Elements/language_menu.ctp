<?php if (!empty($languagePreferenceAvailable) && $this->Session->read('User.id')): ?>
<li class="dropdown" id="language-menu">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" title="<?php echo h(__('Language')); ?>" aria-label="<?php echo h(__('Language')); ?>">
        <i class="fa fa-globe" aria-hidden="true"></i><span class="sr-only"><?php echo __('Language'); ?></span>
    </a>
    <ul class="dropdown-menu">
        <?php foreach ((array)$availableLanguages as $record): $option = $record['Language'];
            $selected = $option['code'] === $this->Session->read('Config.language'); ?>
        <li<?php echo $selected ? ' class="active"' : ''; ?>>
            <form method="post" action="<?php echo h($this->Html->url(array('controller' => 'users', 'action' => 'change_language'))); ?>" style="margin:0">
                <input type="hidden" name="data[token]" value="<?php echo h($languageToken); ?>">
                <input type="hidden" name="data[User][language_id]" value="<?php echo h($option['id']); ?>">
                <input type="hidden" name="data[return_to]" value="<?php echo h($this->request->here()); ?>">
                <button type="submit" class="btn btn-link" style="display:block;width:100%;text-align:left;padding:6px 20px" <?php echo $selected ? 'aria-current="true"' : ''; ?>>
                    <?php echo h($option['name']); ?><?php if ($selected): ?> <i class="fa fa-check" aria-hidden="true"></i><?php endif; ?>
                </button>
            </form>
        </li>
        <?php endforeach; ?>
        <?php if ($this->Session->read('User.is_mr') || $this->Session->read('User.is_super_user')): ?>
        <li role="separator" class="divider"></li>
        <li><?php echo $this->Html->link(__('Add language'), array('controller' => 'languages', 'action' => 'index', '#' => 'add-language')); ?></li>
        <li><?php echo $this->Html->link(__('Update PO file'), array('controller' => 'languages', 'action' => 'index')); ?></li>
        <?php endif; ?>
    </ul>
</li>
<?php endif; ?>
