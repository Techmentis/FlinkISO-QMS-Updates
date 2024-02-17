<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-touch-fullscreen" content="yes">
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
    'plugins/jQueryUI/jquery-ui.min',
    'js/bootstrap.min',
    'validation',
    'chosen.min',
    'tooltip.min',
    'plugins/daterangepicker/moment.min',
    'jquery.datepicker',    
    'plugins/daterangepicker/daterangepicker',
    'plugins/datepicker/bootstrap-datepicker',    
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
          <b>Version</b> 2.0
      </div>
      <strong>Copyright &copy; 2013 <a href="http://www.techmentis.biz">Techmentis Global Services Pvt Ltd</a>.</strong> All rights
      reserved.
  </footer>
  <?php
  if ($this->Session->read('User'))
    // echo $this->Element('control-sidebar');
    ?>  

</div>

<?php
echo $this->Html->script(array(
    'dist/js/demo',
    'dist/js/app.min',
));
echo $this->fetch('script');
?>
<script type="text/javascript">
  $().ready(function(){
    $('.tooltip1').tooltip();
    $('select').chosen();
});
</script>
</body>
</html>
