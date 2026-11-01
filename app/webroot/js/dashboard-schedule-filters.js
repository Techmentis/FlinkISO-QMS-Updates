(function (document) {
    'use strict';
    var panel = document.getElementById('shared-custom-forms');
    if (!panel || panel.getAttribute('data-schedule-filters-ready') === 'true') return;
    panel.setAttribute('data-schedule-filters-ready', 'true');
    var buttons = panel.querySelectorAll('[data-form-schedule]');
    var rows = panel.querySelectorAll('[data-form-schedule-row]');
    function selectSchedule(button) {
        if (button.disabled) return;
        var schedule = button.getAttribute('data-form-schedule');
        for (var i = 0; i < buttons.length; i++) {
            var selected = buttons[i] === button;
            buttons[i].setAttribute('aria-pressed', selected ? 'true' : 'false');
        }
        for (var j = 0; j < rows.length; j++) {
            rows[j].style.display = rows[j].getAttribute('data-form-schedule-row') === schedule ? '' : 'none';
        }
    }
    for (var i = 0; i < buttons.length; i++) {
        buttons[i].addEventListener('click', function () { selectSchedule(this); });
    }
}(document));
