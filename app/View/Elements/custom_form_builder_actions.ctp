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
#fieldSettingsPanelBody .builder-submit-footer .field-settings-update-status {
	grid-area: 2 / 1;
}
#fieldSettingsPanelBody .builder-submit-footer .field-settings-close.btn,
#fieldSettingsPanelBody .builder-submit-footer #submit_id {
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
#fieldSettingsPanelBody .builder-submit-footer #submit_id {
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

	var home = submit.parentNode;
	var hintText = <?php echo json_encode(__('Page scrolling is locked. Click Update or Close to activate scrolling.'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;

	function syncBuilderActions(){
		var footer = $(panel).children('.field-settings-footer')[0];
		if (!footer) {
			if (submit.parentNode !== home) home.appendChild(submit);
			return;
		}

		footer.classList.add('builder-submit-footer');
		var hint = footer.querySelector('.field-settings-unfreeze-hint');
		if (hint && hint.textContent !== hintText) hint.textContent = hintText;
		if (footer.lastElementChild !== submit) footer.appendChild(submit);
	}

	// Older API responses render the panel but leave the app's Submit in the footer.
	new MutationObserver(syncBuilderActions).observe(panel, {childList: true, subtree: true});
	syncBuilderActions();
});
</script>
