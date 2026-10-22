<?php
App::uses('L10n', 'I18n');
/** Bundled suggestions, never a whitelist of languages administrators may add. */
class LanguageReference {
    public static function all() {
        $rows = json_decode(file_get_contents(dirname(__FILE__) . '/LanguageData/languages.json'), true);
        $locale = new L10n();
        foreach ($locale->catalog() as $entry) {
            $code = $entry['locale'];
            if (preg_match('/^[a-z]{3}$/D', $code) && !isset($rows[$code])) {
                $rows[$code] = array('name' => $entry['language'], 'plural_forms' => '');
            }
        }
        uasort($rows, function ($a, $b) { return strnatcasecmp($a['name'], $b['name']); });
        return $rows;
    }
    public static function pluralForms($code) {
        $rows = self::all();
        return isset($rows[$code]) ? $rows[$code]['plural_forms'] : '';
    }
}
