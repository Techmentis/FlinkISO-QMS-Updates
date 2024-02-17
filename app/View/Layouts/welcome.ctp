<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-touch-fullscreen" content="yes">
  <title>FlinkISO | QMS</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <?php
  echo $this->Html->meta('icon');
  echo $this->Html->css(array(    
    'bootstrap/css/bootstrap.min',
    'dist/css/AdminLTE.min',
    'dist/css/skins/_all-skins.min',
    'plugins/iCheck/flat/blue',
    'plugins/morris/morris',
    'plugins/jvectormap/jquery-jvectormap-1.2.2',
    'plugins/datepicker/datepicker3',
    'plugins/daterangepicker/daterangepicker-bs3',
    'plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min',
    'jquery.countdown',
    'jquery-ui-1.9.2.custom.min',
    'bootstrap-chosen.min',
    'jquery.datepicker',
    'custom','font-awesome.min','icons'
  ));

  echo $this->fetch('css');
  ?>
  
  <?php
  echo $this->Html->script(array(
    'plugins/jQuery/jQuery-2.2.0.min',
    'plugins/jQueryUI/jquery-ui.min',
    'js/bootstrap.min',
    'plugins/fastclick/fastclick',
    'plugins/sparkline/jquery.sparkline.min',
    'plugins/jvectormap/jquery-jvectormap-1.2.2.min',
    'plugins/jvectormap/jquery-jvectormap-world-mill-en',
    'plugins/slimScroll/jquery.slimscroll.min',
    'dist/js/demo',
    'plugins/knob/jquery.knob',
    'jquery.datepicker',
    'chosen.min',
    'tooltip.min',
    'plugins/daterangepicker/daterangepicker',
    'plugins/datepicker/bootstrap-datepicker',
    
    
    'dist/js/app.min',
    'timeout',
    'jquery.countdown',
    'validation'
  ));
  echo $this->fetch('script');
  ?>
</head>
<body class="hold-transition skin-blue sidebar-collapse sidebar-mini">
  <div class="wrapper">
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
      <b>Version</b> 2.0
    </div>
    <strong>Copyright &copy; 2013 <a href="http://www.techmentis.biz">Techmentis Global Services Pvt Ltd</a>.</strong> All rights
    reserved.
  </footer>

</div>
</body>
</html>
