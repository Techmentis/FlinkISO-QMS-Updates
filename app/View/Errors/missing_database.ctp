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
    <i class="fa fa-database text-danger" style="font-size: 8em"></i>
    <h2><?php echo __d('cake_dev', 'Missing Database Connection'); ?></h2>

    <p class="error text-center">
        <strong><?php echo __d('cake_dev', 'Error'); ?>: </strong>
        <?php echo __d('cake_dev', 'A Database connection using "%s" was missing or unable to connect.', h($class)); ?>
    </p>
</div>
