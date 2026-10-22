<!DOCTYPE html>
<html>
<head>
<?php echo $this->element('translation_strings'); ?>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-touch-fullscreen" content="yes">
  <meta name="referrer" content="no-referrer-when-downgrade">
  <title>FlinkISO : <?php echo Inflector::humanize($this->request->controller);?></title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <?php
  echo $this->Html->meta('icon');
  echo $this->Html->css(array('allcss'));

  echo $this->fetch('css');
  ?>
  <?php
  echo $this->Html->script(array(
    'plugins/jQuery/jQuery-2.2.0.min',
    'js/bootstrap.min',
    'validation',
));


  if($this->action == 'index'){
    echo $this->Html->script(array(
        'js-xlsx-master/dist/xlsx.core.min', 
        'FileSaver.js-master/FileSaver.min', 
        'TableExport-master/src/stable/js/tableexport.min',    

    )); 
}    

echo $this->fetch('script');
?>

</head>
<body class="hold-transition skin-blue sidebar-collapse sidebar-mini">
    <div class="wrapper">
      <?php echo $this->Element('login-nav');?>  
      <!-- Left side column. contains the logo and sidebar -->
      <?php if ($this->Session->read('User'))echo $this->Element('asidebar');?>
      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">        
          <!-- Content Header (Page header) -->
          
          <!-- Main content -->
          <section class="content">
              <?php echo $this->Session->flash(array('class'=>'alert-danger')); ?>
              <?php echo $this->fetch('content');?>  
              <!-- Info boxes -->
              <!-- /.row -->  
          </section>
          <!-- /.content -->
      </div>
      <!-- /.content-wrapper -->
      <footer class="main-footer">  
        <div class="pull-right hidden-xs">
          <b><?php echo h(__('Version')); ?></b> 2.4.42
      </div>
      <strong><?php echo h(__('Copyright')); ?> &copy; 2013 <a href="http://www.techmentis.biz"><?php echo h(__('Techmentis Global Services Pvt Ltd')); ?></a>.</strong> <?php echo h(__('All rights
      reserved.')); ?>
  </footer>
</div>
<?php
echo $this->Html->script(array(
    'dist/js/demo',
    'dist/js/app.min',
));
echo $this->fetch('script');
?>
</body>
</html>
