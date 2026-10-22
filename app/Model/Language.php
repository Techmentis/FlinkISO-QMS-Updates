<?php
App::uses('AppModel', 'Model');
class Language extends AppModel {
    public $actsAs = array();
    public $displayField = 'name';
    public $validate = array(
        'name' => array('rule' => 'notBlank', 'message' => 'Enter a language name.'),
        'code' => array(
            'format' => array('rule' => array('custom', '/^[a-z]{3}$/D'), 'message' => 'Use a unique three-letter language code, for example deu or mar.'),
            'unique' => array('rule' => 'isUnique', 'message' => 'This language already exists.')
        )
    );
    public function beforeSave($options = array()) {
        // Legacy language tables contain required audit fields and short_code.
        // Keep their existing UUIDs and supply defaults only for a new row.
        if (!$this->id) {
            foreach ($this->schema() as $field => $definition) {
                if ($field === $this->primaryKey || isset($this->data[$this->alias][$field]) ||
                    !empty($definition['null']) || isset($definition['default'])) continue;
                $type = $definition['type'];
                if ($field === 'short_code') $value = $this->data[$this->alias]['code'];
                elseif (in_array($type, array('integer', 'biginteger', 'float', 'decimal', 'boolean'))) $value = 0;
                elseif ($type === 'datetime' || $type === 'timestamp') $value = date('Y-m-d H:i:s');
                elseif ($type === 'date') $value = date('Y-m-d');
                elseif ($type === 'time') $value = date('H:i:s');
                else $value = '';
                $this->data[$this->alias][$field] = $value;
            }
        }
        parent::beforeSave($options);
        return true;
    }
    public function available() {
        return in_array($this->useTable, $this->getDataSource()->listSources(), true);
    }
    public function choices() {
        return $this->find('list', array('recursive' => -1, 'conditions' => array('Language.enabled' => 1), 'order' => array('Language.name' => 'ASC')));
    }
}
