<?php
App::uses('AppModel', 'Model');

class AiSetting extends AppModel {
    public $useTable = 'ai_settings';
    public $recursive = -1;
    public $actsAs = array();
}
