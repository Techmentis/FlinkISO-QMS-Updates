<style>
#fieldSettingsPanel #fieldSettingsPanelBody.has-settings > .builder-submit-footer {
	display: grid !important;
	grid-template-columns: minmax(0, 1fr) auto auto;
	align-items: center;
	gap: 8px;
}
#fieldSettingsPanelBody .builder-submit-footer .field-settings-unfreeze-hint {
	grid-area: 1 / 1 / 2 / -1;
}
#fieldSettingsPanelBody .builder-submit-footer > [role="alert"] {
	grid-column: 1 / -1;
	margin: 0;
}
#fieldSettingsPanelBody .builder-submit-footer .field-settings-update-status {
	grid-area: 2 / 1;
}
#fieldSettingsPanelBody .builder-submit-footer .field-settings-close.btn,
#fieldSettingsPanelBody .builder-submit-footer #submit_id,
#fieldSettingsPanelBody .builder-submit-footer .builder-panel-submit {
	grid-row: 2;
	margin: 0 !important;
	padding: 6px 12px;
	font-size: 14px;
	line-height: 1.42857143;
}
#fieldSettingsPanelBody .builder-submit-footer .field-settings-close.btn {
	grid-column: 2;
}
#fieldSettingsPanelBody .builder-submit-footer .field-settings-close.btn.hide {
	display: none !important;
}
#fieldSettingsPanelBody .builder-submit-footer #submit_id,
#fieldSettingsPanelBody .builder-submit-footer .builder-panel-submit {
	grid-column: 3;
}
#fieldSettingsPanel #fieldSettingsPanelBody.has-settings > .builder-submit-footer::after {
	content: none;
}
</style>
<script>
$(function(){
	var panel = document.getElementById('fieldSettingsPanelBody');
	var submit = document.getElementById('submit_id');
	if (!panel || !submit || !submit.form || !submit.form.contains(panel)) return;

	var blockedMessage = <?php echo json_encode(__('Submit was blocked. Check the field settings for missing or invalid values.'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
	var form = submit.form;
	var home = submit.parentNode;
	if (form.id) submit.setAttribute('form', form.id);

	var error = document.createElement('div');
	error.className = 'alert alert-danger';
	error.setAttribute('role', 'alert');
	error.hidden = true;
	form.insertBefore(error, form.firstChild);

	function showSubmitError(message) {
		var footer = $(panel).children('.field-settings-footer')[0];
		if (footer) footer.prepend(error);
		else form.insertBefore(error, form.firstChild);
		error.textContent = message;
		error.hidden = false;
		error.scrollIntoView({block: 'nearest'});
	}

	var isRecreate = home.id === 'recreate-submit-home';
	var panelSubmit;
	if (isRecreate) {
		panelSubmit = document.createElement('button');
		panelSubmit.type = 'button';
		panelSubmit.className = 'btn btn-success btn-sm builder-panel-submit';
		panelSubmit.textContent = submit.value || submit.textContent;
		panelSubmit.title = submit.title;
	}
	var saveStarted = false;
	$(form).on('builder:save-started', function() { saveStarted = true; });

	function submitErrorIsVisible() {
		if (error.hidden || !error.getClientRects().length) return false;
		var rect = error.getBoundingClientRect();
		var visibleElement = document.elementFromPoint(
			rect.left + rect.width / 2, rect.top + rect.height / 2
		);
		return visibleElement === error || error.contains(visibleElement);
	}

	// Observe clicks before an older API validator can stop them.
	window.addEventListener('click', function(event) {
		if (panelSubmit && panelSubmit.contains(event.target)) {
			event.preventDefault();
			event.stopImmediatePropagation();
			if (submit.disabled) return;

			var conversion = panel.querySelector('.field-conversion-preview .field-conversion-source:enabled');
			if (conversion && conversion.value) {
				showSubmitError(<?php echo json_encode(__('Click Queue conversion to confirm the field change, or clear the linked table selection.'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>);
				if (!submitErrorIsVisible()) window.alert(error.textContent);
				return;
			}

			// Restore the field settings before taking the normal form Submit path.
			var close = panel.querySelector('.field-settings-header .field-settings-close');
			if (close) close.click();
			if (submit.parentNode !== home) home.appendChild(submit);
			submit.click();
			return;
		}
		if (event.target !== submit && !submit.contains(event.target)) return;
		error.hidden = true;
		saveStarted = false;
		window.setTimeout(function() {
			if (saveStarted || (!isRecreate && !event.defaultPrevented)) return;

			if (error.hidden) {
				var fields = form.querySelectorAll('#sortable input, #fieldSettingsPanelBody input');
				var message = blockedMessage;
				for (var i = 0; i < fields.length; i++) {
					var field = fields[i];
					if (field.disabled || field.closest('.fi-v2-field-deleted')) continue;
					if (field.validity.customError) {
						message = field.validationMessage;
						break;
					}
				}
				showSubmitError(message);
			}
			if (isRecreate && !submitErrorIsVisible()) window.alert(error.textContent);
		}, 0);
	}, true);
	form.addEventListener('submit', function() { error.hidden = true; }, true);
	form.addEventListener('invalid', function(event) {
		if (!error.hidden) return;
		showSubmitError(event.target.validationMessage);
	}, true);
	$(form).on('builder:submit-blocked', function(event, message) { showSubmitError(message); });
	$(form).on('invalid-form.builderActions', function(event, validator) {
		var first = validator.errorList[0];
		if (first) showSubmitError(first.message);
	});
	var hintText = <?php echo json_encode(__('Page scrolling is locked. Click Update or Close to activate scrolling.'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;

	function syncBuilderActions(){
		var footer = $(panel).children('.field-settings-footer')[0];
		if (!footer) {
			if (error.parentNode !== form) form.insertBefore(error, form.firstChild);
			if (submit.parentNode !== home) home.appendChild(submit);
			return;
		}

		footer.classList.add('builder-submit-footer');
		var hint = footer.querySelector('.field-settings-unfreeze-hint');
		if (hint && hint.textContent !== hintText) hint.textContent = hintText;
		if (panelSubmit) {
			if (submit.parentNode !== home) home.appendChild(submit);
			if (footer.lastElementChild !== panelSubmit) footer.appendChild(panelSubmit);
		} else if (footer.lastElementChild !== submit) {
			footer.appendChild(submit);
		}
	}

	// Older API responses render the panel but leave the app's Submit in the footer.
	new MutationObserver(syncBuilderActions).observe(panel, {childList: true, subtree: true});
	syncBuilderActions();
});
</script>
