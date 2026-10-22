<?php
App::uses('AppModel', 'Model');
class Language extends AppModel {
    public $actsAs = array();
    public $displayField = 'name';
    public $validate = array(
        'name' => array('rule' => 'notBlank', 'message' => 'Enter a language name.'),
        'code' => array(
            'format' => array('rule' => array('custom', '/^[a-z]{3}$/D'), 'message' => 'Use a three-letter CakePHP language code, for example deu.'),
            'supported' => array('rule' => 'supportedCode', 'message' => 'Use a supported CakePHP language code.'),
            'unique' => array('rule' => 'isUnique', 'message' => 'This language already exists.')
        )
    );
    public function supportedCode($check) {
        App::uses('L10n', 'I18n');
        $locale = new L10n();
        return (bool)$locale->catalog(current($check));
    }
    public function available() {
        return in_array($this->useTable, $this->getDataSource()->listSources(), true);
    }
    public function choices() {
        return $this->find('list', array('recursive' => -1, 'conditions' => array('Language.enabled' => 1), 'order' => array('Language.name' => 'ASC')));
    }
}
