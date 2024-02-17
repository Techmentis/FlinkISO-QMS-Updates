<!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-light ">
    <div class="row">
      <div class="col-md-12"> 
        <div id="ad_src_result"></div>
        <script type="text/javascript">
          $().ready(function(){
            $("#ad_src_result").load("<?php echo Router::url('/', true); ?><?php echo $this->request->params['controller'] ?>/advance_search/custom_table_id:<?php echo $this->request->params['named']['custom_table_id'];?>/qc_document_id:<?php echo $this->request->params['named']['qc_document_id'];?>/process_id:<?php echo $this->request->params['named']['process_id'];?>");
          })          
        </script>
      </div>
    </div>
  </aside>
  <script type="text/javascript">
    $().ready(function(){
      $(".control-sidebar").height($(".sidebar-mini").height()+100);
    });
  </script>