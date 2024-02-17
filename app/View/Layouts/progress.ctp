<?php
/**
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTDXHTML 1.0 Transitional//EN" <html lang="en-US" xml:lang="en-US" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>FlinkISO</title>
    <meta http-equiv="refresh" content="0.5">
    <meta http-Equiv="Cache-Control" Content="no-cache">
    <meta http-Equiv="Pragma" Content="no-cache">
    <meta http-Equiv="Expires" Content="0">
    <style>
        .progress {
            background-color: #f5f5f5;
            border-radius: 4px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1) inset;
            height: 20px;
            margin-bottom: 20px;
            overflow: hidden;
        }
        .progress-bar {
            background-color: #428bca;
            box-shadow: 0 -1px 0 rgba(0, 0, 0, 0.15) inset;
            color: #fff;
            float: left;
            font-size: 12px;
            height: 100%;
            text-align: center;
            transition: width 0.6s ease 0s;
            width: 0;
        }
    </style>
</head>
<body style="background: none">
    <?php echo $this->fetch('content'); ?>
</body>
