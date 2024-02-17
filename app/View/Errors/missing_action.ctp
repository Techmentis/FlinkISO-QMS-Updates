<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @package       Cake.View.Errors
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

$pluginDot = empty($plugin) ? null : $plugin . '.';
?>
<?php echo $this->element('nav-header-lists',array('postData'=>array('pluralHumanName'=>'Error','modelClass'=>'Error','options'=>array(),'pluralVar'=>'errors'))); ?>
<style type="text/css">
    .error{text-align: center !important; float: none;}
</style>
<div class="main text-center">  
    <br /><br />  
    <i class="fa fa-code text-danger" style="font-size: 8em"></i>
    <h2><?php echo __d('cake_dev', 'Missing Action'); ?></h2>
    <h3>Solution : Try recreating the table and linkes/child tables.</h3>

    <p class="error text-center">
        <strong><?php echo __d('cake_dev', 'Error'); ?>: </strong>
        <?php echo __d('cake_dev', 'The action %1$s is not defined in controller %2$s', '<em>' . h($action) . '</em>', '<em>' . h($controller) . '</em>'); ?>
    </p>
    <p class="error hide">
        <strong><?php echo __d('cake_dev', 'Error'); ?>: </strong>
        <?php echo __d('cake_dev', 'Create the class %s below in file: %s', '<em>' . h($class) . '</em>', (empty($plugin) ? APP_DIR . DS : CakePlugin::path($plugin)) . 'Controller' . DS . h($class) . '.php'); ?>
    </p>
</div>
