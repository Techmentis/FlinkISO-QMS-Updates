<?php
App::uses('AppModel', 'Model');

/**
 * Persistent FlinkISO AI request/response history.
 */
class Ai extends AppModel {
    public $useTable = 'ais';
    public $recursive = -1;
    public $actsAs = array();
}
