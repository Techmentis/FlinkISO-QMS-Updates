<div class="modal fade" id="custom-form-generation-modal" tabindex="-1" role="dialog" aria-labelledby="custom-form-generation-title">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="custom-form-generation-title"><?php echo __('Updating form'); ?></h4>
			</div>
			<div class="modal-body">
				<p id="custom-form-generation-message"><?php echo __('Saving the form configuration and rebuilding the generated files...'); ?></p>
				<div class="progress">
					<div id="custom-form-generation-progress" class="progress-bar progress-bar-striped active" role="progressbar" style="width: 100%">
						<span class="sr-only"><?php echo __('Update in progress'); ?></span>
					</div>
				</div>
				<div id="custom-form-generation-error" class="alert alert-danger" style="display:none"></div>
			</div>
			<div class="modal-footer" id="custom-form-generation-footer" style="display:none">
				<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Close'); ?></button>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
(function(window, $){
	'use strict';

	window.generateCustomFormInBackground = function(form){
		var $form = $(form);
		var $submit = $form.find(':submit');
		var $modal = $('#custom-form-generation-modal');
		var $message = $('#custom-form-generation-message');
		var $progress = $('#custom-form-generation-progress');
		var $error = $('#custom-form-generation-error');
		var $footer = $('#custom-form-generation-footer');

		$submit.prop('disabled', true);
		$('#submit-indicator').show();
		$message.text('<?php echo addslashes(__('Saving the form configuration and rebuilding the generated files...')); ?>');
		$progress.show().addClass('active').css('width', '100%');
		$error.hide().empty();
		$footer.hide();
		$modal.modal({backdrop: 'static', keyboard: false, show: true});

		$.ajax({
			url: $form.attr('action'),
			type: ($form.attr('method') || 'POST').toUpperCase(),
			data: $form.serialize(),
			dataType: 'json',
			timeout: 0,
			success: function(response){
				if(!response || response.success !== true){
					showGenerationError(response && response.message ? response.message : '<?php echo addslashes(__('The form could not be updated.')); ?>');
					return;
				}

				$progress.removeClass('active').css('width', '100%');
				$message.text(response.message || '<?php echo addslashes(__('Form updated successfully.')); ?>');
				window.setTimeout(function(){ window.location.reload(); }, 700);
			},
			error: function(xhr){
				var response = xhr.responseJSON;
				if(!response && xhr.responseText){
					try { response = JSON.parse(xhr.responseText); } catch(ignore) {}
				}
				showGenerationError(response && response.message ? response.message : '<?php echo addslashes(__('The form update failed. Please try again.')); ?>');
			}
		});

		function showGenerationError(message){
			$submit.prop('disabled', false);
			$('#submit-indicator').hide();
			$progress.hide().removeClass('active');
			$message.text('<?php echo addslashes(__('The form was not updated.')); ?>');
			$error.text(message).show();
			$footer.show();
		}
	};
})(window, window.jQuery);
</script>
