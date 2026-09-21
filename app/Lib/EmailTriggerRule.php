<?php

/**
 * Pure rule helpers kept outside controllers so UI, API and scheduled workers
 * evaluate trigger definitions in exactly the same way.
 */
class EmailTriggerRule {
	public static $approvalFields = array(
		'approval_step_id', 'approved_by', 'published_by', 'published_date',
		'publish', 'record_status', 'status_user_id'
	);
	public static $technicalFields = array(
		'sr_no', 'created', 'created_by', 'modified', 'modified_by',
		'company_id', 'branchid', 'departmentid', 'file_key', 'file_id'
	);

	public static function eventName($trigger) {
		if (!empty($trigger['event_name'])) return $trigger['event_name'];
		if (!empty($trigger['field_name']) && $trigger['field_name'] !== '-1') return 'field.changed';
		$legacy = array(0 => 'record.created', 1 => 'record.updated', 3 => 'record.deleted');
		return isset($legacy[$trigger['action']]) ? $legacy[$trigger['action']] : null;
	}

	public static function changedFields($before, $after) {
		$changes = array();
		foreach ((array)$after as $field => $value) {
			$oldValue = array_key_exists($field, (array)$before) ? $before[$field] : null;
			if ((string)$oldValue !== (string)$value) {
				$changes[$field] = array('old' => $oldValue, 'new' => $value);
			}
		}
		return $changes;
	}

	public static function isApprovalOnlyChange($changes) {
		if (empty($changes)) return false;
		foreach (array_keys($changes) as $field) {
			if (in_array($field, self::$technicalFields, true)) continue;
			if (!in_array($field, self::$approvalFields, true)) return false;
		}
		return true;
	}

	public static function matches($trigger, $eventName, $before, $after) {
		if (empty($trigger['enabled']) && array_key_exists('enabled', $trigger)) return false;
		if (self::eventName($trigger) !== $eventName) return false;
		if ($eventName !== 'field.changed') return true;

		$field = isset($trigger['field_name']) ? $trigger['field_name'] : null;
		if (!$field || in_array($field, self::$approvalFields, true)) return false;
		$oldValue = array_key_exists($field, (array)$before) ? $before[$field] : null;
		$newValue = array_key_exists($field, (array)$after) ? $after[$field] : null;
		if ((string)$oldValue === (string)$newValue) return false;

		$operator = !empty($trigger['condition_operator']) ? $trigger['condition_operator'] : 'equals';
		$expected = isset($trigger['changed_field_value']) ? $trigger['changed_field_value'] : null;
		switch ($operator) {
			case 'changed': return true;
			case 'not_equals': return (string)$newValue !== (string)$expected;
			case 'to_empty': return $newValue === null || $newValue === '';
			case 'from_empty': return ($oldValue === null || $oldValue === '') && !($newValue === null || $newValue === '');
			default: return (string)$newValue === (string)$expected;
		}
	}

	public static function render($template, $context) {
		$values = array(
			'event.name' => isset($context['event_name']) ? $context['event_name'] : '',
			'record.id' => isset($context['record_id']) ? $context['record_id'] : '',
			'record.display_value' => isset($context['display_value']) ? $context['display_value'] : '',
			'actor.name' => isset($context['actor_name']) ? $context['actor_name'] : '',
			'module.name' => isset($context['module_name']) ? $context['module_name'] : '',
			'reminder.target_date' => isset($context['target_date']) ? $context['target_date'] : '',
			'reminder.offset_days' => isset($context['offset_days']) ? $context['offset_days'] : ''
		);
		foreach ((array)(isset($context['record']) ? $context['record'] : array()) as $field => $value) {
			if (!is_array($value)) $values['record.'.$field] = $value;
		}
		foreach ((array)(isset($context['changes']) ? $context['changes'] : array()) as $field => $change) {
			$values['changes.'.$field.'.old'] = isset($change['old']) ? $change['old'] : '';
			$values['changes.'.$field.'.new'] = isset($change['new']) ? $change['new'] : '';
		}
		return preg_replace_callback('/\{\{\s*([a-zA-Z0-9_.]+)\s*\}\}/', function ($match) use ($values) {
			return isset($values[$match[1]]) ? htmlspecialchars((string)$values[$match[1]], ENT_QUOTES, 'UTF-8') : '';
		}, (string)$template);
	}
}
