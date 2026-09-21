<?php
App::uses('AppShell', 'Console/Command');
App::uses('CakeEmail', 'Network/Email');
App::uses('CakeText', 'Utility');
App::uses('ClassRegistry', 'Utility');
App::uses('Inflector', 'Utility');
App::uses('Validation', 'Utility');
App::uses('EmailTriggerRule', 'Lib');

class EmailTriggerShell extends AppShell {
	public $uses = array('EmailTriggerOutbox', 'EmailTriggerDelivery', 'CustomTrigger', 'CustomTable', 'Employee', 'User');

	public function main() {
		$limit = !empty($this->params['limit']) ? max(1, (int)$this->params['limit']) : 100;
		$this->EmailTriggerOutbox->updateAll(
			array('EmailTriggerOutbox.status' => "'retry'", 'EmailTriggerOutbox.lock_token' => 'NULL'),
			array('EmailTriggerOutbox.status' => 'processing', 'EmailTriggerOutbox.modified <' => date('Y-m-d H:i:s', time() - 900))
		);
		$rows = $this->EmailTriggerOutbox->find('all', array(
			'recursive' => -1,
			'limit' => $limit,
			'order' => array('EmailTriggerOutbox.created' => 'ASC'),
			'conditions' => array(
				'EmailTriggerOutbox.status' => array('pending', 'retry'),
				'EmailTriggerOutbox.available_at <=' => date('Y-m-d H:i:s'),
				'EmailTriggerOutbox.attempts <' => 5
			)
		));

		foreach ($rows as $row) $this->_deliver($row['EmailTriggerOutbox']);
		$this->out(count($rows).' queued email(s) processed.');
	}

	/** Queue date-based rules. Intended to run once each day. */
	public function scan() {
		$triggers = $this->CustomTrigger->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				'CustomTrigger.event_name' => 'date.reminder',
				'CustomTrigger.enabled' => 1,
				'CustomTrigger.soft_delete' => 0,
				'CustomTrigger.date_field !=' => ''
			)
		));
		$queued = 0;
		foreach ($triggers as $row) {
			$trigger = $row['CustomTrigger'];
			$customTable = $this->CustomTable->find('first', array(
				'recursive' => -1,
				'conditions' => array('CustomTable.id' => $trigger['custom_table_id'])
			));
			if (empty($customTable['CustomTable']['table_name'])) continue;

			$modelName = Inflector::classify($customTable['CustomTable']['table_name']);
			$model = ClassRegistry::init($modelName);
			$dateField = $trigger['date_field'];
			if (!$model || !$model->hasField($dateField)) {
				CakeLog::write('error', 'Email trigger '.$trigger['id'].' refers to an invalid date field.');
				continue;
			}

			$offset = isset($trigger['date_offset_days']) ? (int)$trigger['date_offset_days'] : 0;
			$targetDate = date('Y-m-d', strtotime(($offset >= 0 ? '+' : '').$offset.' days'));
			$recordConditions = array($modelName.'.'.$dateField.' LIKE' => $targetDate.'%');
			if ($model->hasField('soft_delete')) $recordConditions[$modelName.'.soft_delete'] = 0;
			$records = $model->find('all', array(
				'recursive' => -1,
				'conditions' => $recordConditions
			));
			foreach ($records as $recordRow) {
				if (empty($recordRow[$modelName]['id'])) continue;
				$record = $recordRow[$modelName];
				$recipients = $this->_resolveRecipients($trigger, $record, $customTable);
				if (empty($recipients)) {
					CakeLog::write('notice', 'Email trigger '.$trigger['id'].' matched date reminder but resolved no recipients.');
					continue;
				}
				$displayField = !empty($model->displayField) ? $model->displayField : 'id';
				$eventSeed = $trigger['id'].'|'.$record['id'].'|'.$targetDate.'|'.$offset;
				$context = array(
					'event_id' => substr(hash('sha256', $eventSeed), 0, 36),
					'event_name' => 'date.reminder',
					'model_name' => $modelName,
					'module_name' => !empty($customTable['CustomTable']['name']) ? $customTable['CustomTable']['name'] : Inflector::humanize($modelName),
					'record_id' => $record['id'],
					'display_value' => isset($record[$displayField]) ? $record[$displayField] : $record['id'],
					'record' => $record,
					'before' => array(),
					'changes' => array(),
					'actor_id' => null,
					'actor_employee_id' => null,
					'actor_name' => 'FlinkISO Scheduler',
					'source' => 'scheduler',
					'target_date' => $targetDate,
					'offset_days' => $offset
				);
				$subject = html_entity_decode(strip_tags(EmailTriggerRule::render($trigger['name'], $context)), ENT_QUOTES, 'UTF-8');
				$message = EmailTriggerRule::render($trigger['message'], $context);
				$queued += $this->_enqueue($trigger, $recipients, $subject, $message, $context);
			}
		}
		$this->out($queued.' date reminder email(s) queued.');
	}

	protected function _resolveRecipients($trigger, $record, $customTable) {
		$emails = array();
		$addEmployees = function ($ids) use (&$emails) {
			$ids = array_values(array_unique(array_filter((array)$ids)));
			if (empty($ids)) return;
			$employees = $this->Employee->find('list', array(
				'recursive' => -1,
				'fields' => array('Employee.id', 'Employee.office_email'),
				'conditions' => array('Employee.id' => $ids, 'Employee.publish' => 1, 'Employee.soft_delete' => 0)
			));
			foreach ((array)$employees as $email) if (Validation::email($email)) $emails[strtolower($email)] = $email;
		};
		$decodeIds = function ($value) {
			if (is_array($value)) return $value;
			$decoded = json_decode((string)$value, true);
			return is_array($decoded) ? $decoded : ($value && $value !== '-1' ? array($value) : array());
		};

		if (!empty($trigger['notify_user']) && $trigger['notify_user'] !== '-1' && isset($record[$trigger['notify_user']])) {
			$personIds = $decodeIds($record[$trigger['notify_user']]);
			$addEmployees($personIds);
			$userEmployees = $this->User->find('list', array(
				'recursive' => -1,
				'fields' => array('User.id', 'User.employee_id'),
				'conditions' => array('User.id' => $personIds, 'User.status' => 1, 'User.publish' => 1, 'User.soft_delete' => 0)
			));
			$addEmployees(array_values((array)$userEmployees));
		}
		if (!empty($trigger['notify_users'])) $addEmployees($decodeIds($trigger['notify_users']));
		if (!empty($trigger['notify_admins'])) {
			$admins = $this->User->find('list', array(
				'recursive' => -1,
				'fields' => array('User.id', 'User.employee_id'),
				'conditions' => array('User.is_mr' => 1, 'User.status' => 1, 'User.publish' => 1, 'User.soft_delete' => 0)
			));
			$addEmployees(array_values((array)$admins));
		}

		$fields = !empty($customTable['CustomTable']['fields']) ? json_decode($customTable['CustomTable']['fields'], true) : array();
		$groupIds = array('Departments' => array(), 'Branches' => array(), 'Designations' => array());
		foreach ((array)$fields as $field) {
			if (empty($field['field_name']) || empty($field['linked_to']) || !isset($record[$field['field_name']])) continue;
			if (isset($groupIds[$field['linked_to']])) $groupIds[$field['linked_to']] = array_merge($groupIds[$field['linked_to']], $decodeIds($record[$field['field_name']]));
		}
		$hodDepartmentIds = !empty($trigger['hod_departments']) ? $decodeIds($trigger['hod_departments']) : array();
		if (!empty($trigger['notify_hods'])) $hodDepartmentIds = array_merge($hodDepartmentIds, $groupIds['Departments']);
		$conditions = array('Employee.publish' => 1, 'Employee.soft_delete' => 0);
		$or = array();
		if (!empty($trigger['notify_departments']) && !empty($groupIds['Departments'])) $or[] = array('Employee.department_id' => array_unique($groupIds['Departments']));
		if (!empty($trigger['notify_branches']) && !empty($groupIds['Branches'])) $or[] = array('Employee.branch_id' => array_unique($groupIds['Branches']));
		if (!empty($trigger['notify_designations']) && !empty($groupIds['Designations'])) $or[] = array('Employee.designation_id' => array_unique($groupIds['Designations']));
		if (!empty($hodDepartmentIds)) $or[] = array('Employee.is_hod' => 1, 'Employee.department_id' => array_unique($hodDepartmentIds));
		if (!empty($or)) {
			$conditions['OR'] = $or;
			$groupEmails = $this->Employee->find('list', array('recursive' => -1, 'fields' => array('Employee.id', 'Employee.office_email'), 'conditions' => $conditions));
			foreach ((array)$groupEmails as $email) if (Validation::email($email)) $emails[strtolower($email)] = $email;
		}
		return array_values($emails);
	}

	protected function _enqueue($trigger, $recipients, $subject, $message, $context) {
		$queued = 0;
		$delay = !empty($trigger['delay_minutes']) ? max(0, (int)$trigger['delay_minutes']) : 0;
		$cooldown = !empty($trigger['cooldown_minutes']) ? max(0, (int)$trigger['cooldown_minutes']) : 0;
		$payloadKeys = array('event_id', 'event_name', 'model_name', 'module_name', 'record_id', 'display_value', 'actor_id', 'actor_employee_id', 'actor_name', 'source', 'target_date', 'offset_days');
		$deliveryPayload = array_intersect_key($context, array_flip($payloadKeys));
		foreach ($recipients as $recipient) {
			if ($cooldown > 0 && $this->EmailTriggerOutbox->find('count', array('conditions' => array(
				'EmailTriggerOutbox.custom_trigger_id' => $trigger['id'],
				'EmailTriggerOutbox.record_id' => $context['record_id'],
				'EmailTriggerOutbox.recipient_email' => $recipient,
				'EmailTriggerOutbox.status !=' => 'failed',
				'EmailTriggerOutbox.created >=' => date('Y-m-d H:i:s', time() - ($cooldown * 60))
			)))) continue;
			$idempotencyKey = hash('sha256', $context['event_id'].'|'.$trigger['id'].'|'.strtolower($recipient));
			if ($this->EmailTriggerOutbox->find('count', array('conditions' => array('EmailTriggerOutbox.idempotency_key' => $idempotencyKey)))) continue;
			$this->EmailTriggerOutbox->create();
			$saved = $this->EmailTriggerOutbox->save(array('EmailTriggerOutbox' => array(
				'id' => CakeText::uuid(),
				'custom_trigger_id' => $trigger['id'],
				'event_id' => $context['event_id'],
				'event_name' => $context['event_name'],
				'model_name' => $context['model_name'],
				'record_id' => $context['record_id'],
				'recipient_email' => $recipient,
				'subject' => $subject,
				'message' => $message,
				'payload' => json_encode($deliveryPayload),
				'idempotency_key' => $idempotencyKey,
				'status' => 'pending',
				'attempts' => 0,
				'available_at' => date('Y-m-d H:i:s', time() + ($delay * 60))
			)), false);
			if ($saved) $queued++;
			else CakeLog::write('error', 'Unable to enqueue scheduled email trigger '.$trigger['id'].' for '.$recipient.'.');
		}
		return $queued;
	}

	protected function _deliver($row) {
		$lockToken = CakeText::uuid();
		$claimed = $this->EmailTriggerOutbox->updateAll(
			array('EmailTriggerOutbox.status' => "'processing'", 'EmailTriggerOutbox.lock_token' => "'".$lockToken."'", 'EmailTriggerOutbox.modified' => "'".date('Y-m-d H:i:s')."'"),
			array('EmailTriggerOutbox.id' => $row['id'], 'EmailTriggerOutbox.status' => array('pending', 'retry'))
		);
		if (!$claimed) return;
		$claimedRow = $this->EmailTriggerOutbox->find('first', array('recursive' => -1, 'fields' => array('EmailTriggerOutbox.lock_token'), 'conditions' => array('EmailTriggerOutbox.id' => $row['id'])));
		if (empty($claimedRow['EmailTriggerOutbox']['lock_token']) || $claimedRow['EmailTriggerOutbox']['lock_token'] !== $lockToken) return;

		$attempt = ((int)$row['attempts']) + 1;
		$status = 'sent';
		$error = null;
		try {
			$payload = json_decode($row['payload'], true);
			$email = new CakeEmail('fast');
			$email->to($row['recipient_email']);
			$email->subject($row['subject']);
			$email->template('emailTrigger');
			$email->viewVars(array(
				'record' => !empty($payload['display_value']) ? $payload['display_value'] : $row['record_id'],
				'employee' => !empty($payload['actor_name']) ? $payload['actor_name'] : 'FlinkISO',
				'date_time' => date('Y-m-d H:i:s'),
				'h2tag' => !empty($payload['module_name']) ? $payload['module_name'] : 'Email Trigger',
				'msg_content' => $row['message']
			));
			$email->emailFormat('html');
			$email->send();
		} catch (Exception $exception) {
			$error = $exception->getMessage();
			$status = $attempt >= 5 ? 'failed' : 'retry';
			CakeLog::write('error', 'Email trigger delivery '.$row['id'].' failed: '.$error);
		}

		$nextAttempt = date('Y-m-d H:i:s', time() + (min(60, pow(2, $attempt)) * 60));
		$this->EmailTriggerOutbox->id = $row['id'];
		$this->EmailTriggerOutbox->save(array('EmailTriggerOutbox' => array(
			'status' => $status,
			'lock_token' => null,
			'attempts' => $attempt,
			'last_error' => $error,
			'available_at' => $status === 'retry' ? $nextAttempt : $row['available_at'],
			'sent_at' => $status === 'sent' ? date('Y-m-d H:i:s') : null
		)), false);

		$this->EmailTriggerDelivery->create();
		$this->EmailTriggerDelivery->save(array('EmailTriggerDelivery' => array(
			'id' => CakeText::uuid(),
			'email_trigger_outbox_id' => $row['id'],
			'custom_trigger_id' => $row['custom_trigger_id'],
			'event_id' => $row['event_id'],
			'recipient_email' => $row['recipient_email'],
			'status' => $status,
			'attempt' => $attempt,
			'error_message' => $error
		)), false);
	}

	public function getOptionParser() {
		return parent::getOptionParser()
			->description('Deliver queued Custom Form email-trigger messages.')
			->addSubcommand('scan', array('help' => 'Queue matching date-reminder rules.'))
			->addOption('limit', array('short' => 'l', 'default' => 100, 'help' => 'Maximum messages to process.'));
	}
}
