<style>
  #changeApproverModal .modal-dialog { width:600px; max-width:calc(100% - 30px); margin:30px auto; }
  #changeApproverModal.in .modal-dialog {
    -webkit-transform:translate(0, 0)!important;
    -ms-transform:translate(0, 0)!important;
    -o-transform:translate(0, 0)!important;
    transform:translate(0, 0)!important;
  }
  #changeApproverModal .modal-content { display:flex; flex-direction:column; overflow:hidden; background:#fff; }
  #changeApproverModal .modal-header { display:block!important; position:relative; flex:0 0 55px; min-height:55px; padding:15px; z-index:10; background:#fff; color:#333; border-bottom:1px solid #ddd; }
  #changeApproverModal .modal-header .close { display:block!important; position:relative; z-index:11; float:right; margin:0; font-size:24px; line-height:20px; opacity:1; color:#333; cursor:pointer; }
  #changeApproverModal .modal-body { position:relative; flex:1 1 auto; padding:15px; height:400px; }
  #changeApproverModal iframe { display:block; width:100%; height:400px; border:0; }
</style>
<div class="modal fade" id="changeApproverModal" tabindex="-1" role="dialog" aria-labelledby="changeApproverModalTitle">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close change-approver-modal-close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="changeApproverModalTitle"><?php echo __('Change Approver'); ?></h4>
      </div>
      <div class="modal-body">
        <iframe title="<?php echo __('Change Approver'); ?>"></iframe>
      </div>
    </div>
  </div>
</div>
<script>
(function ($) {
  var $modal = $('#changeApproverModal');
  var frame = $modal.find('iframe')[0];
  $(document).on('click.changeApproverModal', 'a.approval-modal-link', function (event) {
    event.preventDefault();
    $modal.find('.modal-title').text($(this).attr('data-modal-title') || '<?php echo __('Approvals'); ?>');
    frame.src = this.href;
    $modal.modal('show');
  });
  $modal.on('click', '.change-approver-modal-close', function (event) {
    event.preventDefault();
    $modal.modal('hide');
  });
  $(frame).on('load', function () {
    try {
      var doc = frame.contentDocument;
      if (!doc || doc.getElementById('approvalsModalStyle')) return;
      var style = doc.createElement('style');
      style.id = 'approvalsModalStyle';
      style.textContent = '.main-header,.main-footer,.control-sidebar,.control-sidebar-bg{display:none!important}body{padding-top:0!important}.content-wrapper{margin-left:0!important;padding-top:0!important}.content-wrapper>.content-header{display:none!important}';
      doc.head.appendChild(style);
    } catch (e) { /* A redirected sign-in page may be on another origin. */ }
  });
  $modal.on('hidden.bs.modal', function () { frame.src = 'about:blank'; });
})(jQuery);
</script>
