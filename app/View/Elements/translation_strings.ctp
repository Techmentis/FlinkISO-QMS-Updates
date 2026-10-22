<?php App::uses('TranslationMessages', 'Lib'); ?>
<script>
window.fiTranslations = <?php echo json_encode(TranslationMessages::javascript(), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
window.fiTranslate = function (message) {
    return Object.prototype.hasOwnProperty.call(window.fiTranslations, message) ? window.fiTranslations[message] : message;
};
document.addEventListener('DOMContentLoaded', function () {
    if (!window.jQuery || !jQuery.validator) return;
    jQuery.extend(jQuery.validator.messages, {
        required: window.fiTranslate("This field is required."),
        remote: window.fiTranslate("Please fix this field."),
        email: window.fiTranslate("Please enter a valid email address."),
        url: window.fiTranslate("Please enter a valid URL."),
        date: window.fiTranslate("Please enter a valid date."),
        dateISO: window.fiTranslate("Please enter a valid date (ISO)."),
        number: window.fiTranslate("Please enter a valid number."),
        digits: window.fiTranslate("Please enter only digits."),
        creditcard: window.fiTranslate("Please enter a valid credit card number."),
        equalTo: window.fiTranslate("Please enter the same value again."),
        maxlength: jQuery.validator.format(window.fiTranslate("Please enter no more than {0} characters.")),
        minlength: jQuery.validator.format(window.fiTranslate("Please enter at least {0} characters.")),
        rangelength: jQuery.validator.format(window.fiTranslate("Please enter a value between {0} and {1} characters long.")),
        range: jQuery.validator.format(window.fiTranslate("Please enter a value between {0} and {1}.")),
        max: jQuery.validator.format(window.fiTranslate("Please enter a value less than or equal to {0}.")),
        min: jQuery.validator.format(window.fiTranslate("Please enter a value greater than or equal to {0}.")),
    });
});
</script>
