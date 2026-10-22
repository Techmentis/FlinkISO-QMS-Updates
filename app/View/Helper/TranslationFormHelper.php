<?php
App::uses('FormHelper', 'View/Helper');
/** Translate labels supplied at runtime; option keys and record values stay intact. */
class TranslationFormHelper extends FormHelper {
    public function label($fieldName = null, $text = null, $options = array()) {
        if (is_string($text) && $text !== '') $text = __($text);
        return parent::label($fieldName, $text, $options);
    }
    public function input($fieldName, $options = array()) {
        foreach (array('legend', 'placeholder', 'title') as $key) {
            if (isset($options[$key]) && is_string($options[$key])) $options[$key] = __($options[$key]);
        }
        return parent::input($fieldName, $options);
    }
}
