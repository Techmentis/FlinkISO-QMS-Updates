<div class="modal fade" id="custom-form-generation-modal" tabindex="-1" role="dialog" aria-labelledby="custom-form-generation-title" aria-hidden="true" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-sm" role="document">
		<div class="modal-content">
			<div class="modal-body text-center">
				<p class="custom-form-generation-icon"><i class="fa fa-refresh fa-spin fa-3x"></i></p>
				<h4 id="custom-form-generation-title">Generating or updating...</h4>
				<p class="text-muted">Please keep this window open while the form files are generated.</p>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
(function($){
	window.generateCustomFormInBackground = function(form){
		var $form = $(form);
		var $modal = $('#custom-form-generation-modal');
		var $submit = $form.find('#submit_id');
		var completed = false;
		var responseMessage = '';

		if($form.data('generation-in-progress')) return;
		$form.data('generation-in-progress', true);
		$submit.prop('disabled', true);
		$('#submit-indicator').hide();
		$modal.modal('show');

		$.ajax({
			url: $form.attr('action'),
			type: ($form.attr('method') || 'POST').toUpperCase(),
			data: new FormData($form[0]),
			processData: false,
			contentType: false,
			dataType: 'json',
			timeout: 0
		}).done(function(response){
			completed = response && response.success === true;
			responseMessage = response && response.message ? response.message : '';
		}).fail(function(xhr){
			var response = xhr.responseJSON;
			responseMessage = response && response.message
				? response.message
				: 'The form could not be generated. Please try again.';
		}).always(function(){
			$modal.one('hidden.bs.modal', function(){
				if(completed){
					window.location.reload();
					return;
				}
				$form.data('generation-in-progress', false);
				$submit.prop('disabled', false);
				if(responseMessage) window.alert(responseMessage);
			});
			$modal.modal('hide');
		});
	};
})(jQuery);
</script>
