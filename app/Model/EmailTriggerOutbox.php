<?php
App::uses('AppModel', 'Model');

class EmailTriggerOutbox extends AppModel {
	public $useTable = 'email_trigger_outboxes';
	public $displayField = 'subject';
}
