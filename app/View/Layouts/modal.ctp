<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php
  echo $this->Html->css(array('font-awesome.min', 'icons', 'allcss', 'api'));
  echo $this->Html->script(array(
    'plugins/jQuery/jQuery-2.2.0.min',
    'chosen.min'
  ));
  ?>
  <style>
    html, body { margin:0; padding:0; background:#fff; }
    body { padding:0 15px; }
    .modal-form-content { width:100%; }
  </style>
</head>
<body>
  <div class="modal-form-content"><?php echo $this->fetch('content'); ?></div>
</body>
</html>
