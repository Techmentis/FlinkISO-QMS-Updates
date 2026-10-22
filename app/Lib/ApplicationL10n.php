<?php
App::uses('L10n', 'I18n');
App::uses('I18n', 'I18n');
/** Register a user-defined PO folder without changing CakePHP's core locale list. */
class ApplicationL10n extends L10n {
    public static function apply($code) {
        if (!is_string($code) || !preg_match('/^[a-z]{3}$/D', $code)) return;
        $locale = new self();
        $locale->default = 'eng';
        if (!$locale->catalog($code)) {
            $locale->_l10nCatalog[$code] = array('language' => $code, 'locale' => $code,
                'localeFallback' => $code, 'charset' => 'utf-8', 'direction' => 'ltr');
        }
        $locale->get($code);
        I18n::getInstance()->l10n = $locale;
        I18n::clear();
    }
}
