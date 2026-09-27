<!-- Control Sidebar -->
<?php
  $advancedSearchNamed = isset($this->request->params['named']) ? $this->request->params['named'] : array();
  $advancedSearchUrl = Router::url(array(
    'controller' => $this->request->params['controller'],
    'action' => 'advance_search',
    'custom_table_id' => isset($advancedSearchNamed['custom_table_id']) ? $advancedSearchNamed['custom_table_id'] : '',
    'qc_document_id' => isset($advancedSearchNamed['qc_document_id']) ? $advancedSearchNamed['qc_document_id'] : '',
    'process_id' => isset($advancedSearchNamed['process_id']) ? $advancedSearchNamed['process_id'] : ''
  ), true);
?>
  <aside class="control-sidebar control-sidebar-light" id="advancedSearchSidebar" aria-label="Advanced Search" aria-hidden="true">
    <button type="button" class="advanced-search-sidebar-close advanced-search-sidebar-close-persistent" aria-label="Close Advanced Search" title="Close Advanced Search">&times;</button>
    <div class="row">
      <div class="col-md-12"> 
        <div id="ad_src_result"></div>
      </div>
    </div>
  </aside>
  <script type="text/javascript">
    (function($){
      var advancedSearchUrl = <?php echo json_encode($advancedSearchUrl); ?>;

      function closeAdvancedSearchSidebar(){
        $('#advancedSearchSidebar').removeClass('control-sidebar-open').attr('aria-hidden', 'true');
        $('body').removeClass('control-sidebar-open');
      }

      $(function(){
        // A refresh or back/forward cache restore must never reopen search.
        closeAdvancedSearchSidebar();
        $('#advancedSearchSidebar').height($('.sidebar-mini').height() + 100);
        $(document)
          .off('click.advancedSearchClose', '.advanced-search-sidebar-close')
          .on('click.advancedSearchClose', '.advanced-search-sidebar-close', function(event){
            event.preventDefault();
            event.stopImmediatePropagation();
            closeAdvancedSearchSidebar();
          })
          .off('click.advancedSearchOpen', '#ad_src')
          .on('click.advancedSearchOpen', '#ad_src', function(event){
            event.preventDefault();
            event.stopImmediatePropagation();

            var sidebar = $('#advancedSearchSidebar');
            if(sidebar.hasClass('control-sidebar-open')){
              closeAdvancedSearchSidebar();
              return;
            }

            $('body').removeClass('control-sidebar-open');
            sidebar.addClass('control-sidebar-open').attr('aria-hidden', 'false');
            $('#advancedSearchSidebar #ad_src_result')
              .html('<div class="advanced-search-loading">Loading Advanced Search&hellip;</div>')
              .load(advancedSearchUrl, function(response, status){
                if(status === 'error'){
                  $(this).html('<div class="alert alert-danger">Advanced Search could not be loaded. Please try again.</div>');
                }
              });
          });
      });

      $(window)
        .off('pageshow.advancedSearchSidebar')
        .on('pageshow.advancedSearchSidebar', closeAdvancedSearchSidebar);
    })(jQuery);
  </script>
