<?php
/** Shared server-side rules for generated Add/Edit tabs. */
class TabAccessPolicy {
    public static function decode($value) {
        for ($pass = 0; $pass < 3 && is_string($value); $pass++) {
            $decoded = json_decode($value, true);
            if (json_last_error() !== JSON_ERROR_NONE) break;
            $value = $decoded;
        }
        return is_array($value) ? $value : array();
    }

    /** Ordered rules: the first match wins, otherwise normal visible/editable access. */
    public static function outcome($setting, $action, $record = array(), $fields = array(), $actor = array()) {
        if (!in_array($action, array('add','edit'), true)) return 'show';
        foreach (isset($setting['rules']) && is_array($setting['rules']) ? $setting['rules'] : array() as $rule) {
            if (!isset($rule['action']) || !in_array($rule['action'], array('both', $action), true)) continue;
            $conditions = isset($rule['conditions']) && is_array($rule['conditions']) ? $rule['conditions'] : array();
            $hasRowVisibility = false;
            foreach ($conditions as $condition) if (isset($condition['visibility'])) $hasRowVisibility = true;
            if ($hasRowVisibility) {
                $result = self::rowOutcome($rule, $action, $record, $fields, $actor);
                if ($result !== null) return $result;
                if (isset($rule['else_visibility'])) return $rule['else_visibility'];
                continue;
            }
            $any = isset($rule['match']) && $rule['match'] === 'any';
            $matched = !$conditions || !$any;
            foreach ($conditions as $condition) {
                $ok = self::matches($condition, $record, $fields, $actor, $action);
                if ($any && $ok) { $matched = true; break; }
                if (!$any && !$ok) { $matched = false; break; }
            }
            if ($matched && isset($rule['visibility']) && in_array($rule['visibility'], array('show','hide','readonly'), true)) return $rule['visibility'];
            if (!$matched && isset($rule['else_visibility'])) return $rule['else_visibility'];
        }
        return 'show';
    }

    /** AND binds within each group; OR separates alternative groups. */
    private static function rowOutcome($rule, $action, $record, $fields, $actor) {
        $groups = array(); $group = array();
        foreach ($rule['conditions'] as $condition) {
            $join = isset($condition['join']) ? $condition['join'] : (isset($rule['match']) && $rule['match'] === 'any' ? 'or' : 'and');
            if ($group && $join === 'or') { $groups[] = $group; $group = array(); }
            $group[] = $condition;
        }
        if ($group) $groups[] = $group;
        $rank = array('show'=>0,'readonly'=>1,'hide'=>2); $result = null;
        foreach ($groups as $conditions) {
            $matched = true;
            foreach ($conditions as $condition) {
                if (!self::matches($condition, $record, $fields, $actor, $action)) { $matched = false; break; }
            }
            if (!$matched) continue;
            foreach ($conditions as $condition) {
                $visibility = isset($condition['visibility']) ? $condition['visibility'] : $rule['visibility'];
                if (isset($rank[$visibility]) && ($result === null || $rank[$visibility] > $rank[$result])) $result = $visibility;
            }
        }
        return $result;
    }

    private static function fieldValues($name, $record, $fields) {
        $value = isset($record[$name]) ? $record[$name] : null;
        $values = is_array($value) ? $value : array($value);
        foreach (self::decode($fields) as $field) {
            if (empty($field['field_name']) || $field['field_name'] !== $name || empty($field['csvoptions'])) continue;
            $options = array_map('trim', explode(',', $field['csvoptions']));
            if (is_scalar($value) && isset($options[$value])) $values[] = $options[$value];
        }
        return self::normalise($values);
    }

    private static function normalise($values) {
        $result = array();
        foreach ((array)$values as $value) if (is_scalar($value) && trim((string)$value) !== '') $result[] = strtolower(trim((string)$value));
        return $result;
    }

    public static function matches($condition, $record, $fields, $actor, $action = '') {
        $source = isset($condition['source']) ? $condition['source'] : '';
        $name = isset($condition['field']) ? $condition['field'] : '';
        if ($source === 'actor') $current = self::normalise(isset($actor[$name]) ? (array)$actor[$name] : array());
        elseif ($source === 'workflow' && $name === 'approval_step_id') $current = self::normalise(isset($actor['approval_step_id']) ? (array)$actor['approval_step_id'] : array());
        elseif ($source === 'page' && $name === 'action') $current = self::normalise(array($action));
        elseif ($source === 'record') $current = self::fieldValues($name, $record, $fields);
        else return false;
        $op = isset($condition['operator']) ? $condition['operator'] : '';
        if ($op === 'empty') return !$current;
        if ($op === 'not_empty') return (bool)$current;
        $allowed = self::normalise(isset($condition['values']) ? $condition['values'] : array());
        if (!$allowed) return false;
        $found = (bool)array_intersect($current, $allowed);
        return $op === 'in' ? $found : ($op === 'not_in' ? !$found : false);
    }

    private static function cleanRules($setting, $fields, $allowedOptions) {
        if (!is_array($setting['rules']) || count($setting['rules']) > 100) throw new InvalidArgumentException('Invalid rules.');
        $known = array();
        foreach (self::decode($fields) as $field) {
            if (!empty($field['field_name']) && in_array(isset($field['data_type']) ? $field['data_type'] : '', array('radio','dropdown-s'), true)) {
                $name = $field['field_name'];
                $known[$name] = isset($allowedOptions[$name]) ? $allowedOptions[$name] : (!empty($field['csvoptions']) ? array_map('trim', explode(',', $field['csvoptions'])) : array());
            }
        }
        $rules = array();
        $version = isset($setting['version']) && (int)$setting['version'] === 3 ? 3 : 2;
        foreach ($setting['rules'] as $rule) {
            if (!is_array($rule) || !isset($rule['action'], $rule['visibility'], $rule['match'], $rule['conditions'])
                || !in_array($rule['action'], array('add','edit','both'), true)
                || !in_array($rule['visibility'], array('show','hide','readonly'), true)
                || !in_array($rule['match'], array('all','any'), true)
                || !is_array($rule['conditions']) || count($rule['conditions']) > 50) throw new InvalidArgumentException('Complete each rule before saving.');
            $conditions = array();
            foreach ($rule['conditions'] as $c) {
                if (!is_array($c) || !isset($c['source'],$c['field'],$c['operator']) || !in_array($c['operator'], array('in','not_in','empty','not_empty'), true)) throw new InvalidArgumentException('Invalid rule condition.');
                $key = $c['source'] === 'record' ? $c['field'] : '@'.$c['source'].'.'.$c['field'];
                if (!in_array($c['source'], array('record','actor','workflow','page'),true) || ($c['source'] === 'record' && !array_key_exists($key,$known))
                    || ($c['source'] === 'actor' && (!in_array($c['field'],array('id','department_id','designation_id'),true) || !array_key_exists($key,$allowedOptions)))
                    || ($c['source'] === 'workflow' && ($c['field'] !== 'approval_step_id' || !array_key_exists($key,$allowedOptions)))
                    || ($c['source'] === 'page' && $c['field'] !== 'action')) throw new InvalidArgumentException('Unavailable condition field.');
                $options = $c['source'] === 'page' ? array('add','edit') : ($c['source'] === 'record' ? $known[$key] : $allowedOptions[$key]);
                $values = isset($c['values']) && is_array($c['values']) ? $c['values'] : array();
                foreach ($values as $value) if (!is_scalar($value)) throw new InvalidArgumentException('Invalid condition value.');
                $values = array_values(array_unique(array_map('strval',$values)));
                if (in_array($c['operator'],array('in','not_in'),true)) {
                    if (!$values || array_diff($values,array_map('strval',$options))) throw new InvalidArgumentException('Choose valid values for every condition.');
                } else $values = array();
                $cleanCondition = array('source'=>$c['source'],'field'=>$c['field'],'operator'=>$c['operator'],'values'=>$values);
                if (array_key_exists('visibility',$c) || array_key_exists('join',$c)) {
                    if (!isset($c['visibility'],$c['join']) || !in_array($c['visibility'],array('show','hide','readonly'),true) || !in_array($c['join'],array('and','or'),true)) throw new InvalidArgumentException('Invalid condition visibility or connector.');
                    $cleanCondition['join'] = $c['join'];
                    $cleanCondition['visibility'] = $c['visibility'];
                    $version = 3;
                }
                $conditions[] = $cleanCondition;
            }
            $cleanRule = array('action'=>$rule['action'],'visibility'=>$rule['visibility'],'match'=>$rule['match'],'conditions'=>$conditions);
            if (array_key_exists('else_visibility', $rule)) {
                if (!in_array($rule['else_visibility'], array('show','hide','readonly'), true)) {
                    throw new InvalidArgumentException('Choose a valid Else visibility.');
                }
                $cleanRule['else_visibility'] = $rule['else_visibility'];
            }
            $rules[] = $cleanRule;
        }
        return array('version'=>$version,'rules'=>$rules);
    }

    public static function isReadonly($setting, $action, $record = array(), $fields = array(), $actor = array()) {
        if (!in_array($action, array('add', 'edit'), true) || !is_array($setting)) return false;
        if (isset($setting['rules'])) return self::outcome($setting, $action, $record, $fields, $actor) === 'readonly';
        $mode = isset($setting['action_visibility']) ? $setting['action_visibility'] : 'always';
        return $mode === 'readonly_both' || $mode === 'readonly_' . $action;
    }

    public static function isVisible($setting, $action, $record = array(), $fields = array(), $actor = array()) {
        if (isset($setting['rules'])) return self::outcome($setting, $action, $record, $fields, $actor) !== 'hide';
        $mode = isset($setting['action_visibility']) ? $setting['action_visibility'] : 'always';
        if (in_array($action, array('add', 'edit'), true) && ($mode === 'hide_both' || $mode === 'hide_' . $action)) return false;
        if (empty($setting['visibility_field']) || empty($setting['visible_when']) || !is_array($setting['visible_when'])) return true;
        $name = $setting['visibility_field'];
        $value = isset($record[$name]) ? $record[$name] : null;
        $values = is_array($value) ? $value : array($value);
        foreach (self::decode($fields) as $field) {
            if (empty($field['field_name']) || $field['field_name'] !== $name || empty($field['csvoptions'])) continue;
            $options = array_map('trim', explode(',', $field['csvoptions']));
            if (is_scalar($value) && isset($options[$value])) $values[] = $options[$value];
        }
        $allowed = array_map(function($v){ return strtolower(trim((string)$v)); }, $setting['visible_when']);
        foreach ($values as $current) {
            if (is_scalar($current) && in_array(strtolower(trim((string)$current)), $allowed, true)) return true;
        }
        return false;
    }

    public static function cleanSetting($setting, $fields, $allowedOptions = array()) {
        if (array_key_exists('rules', $setting)) return self::cleanRules($setting, $fields, $allowedOptions);
        $allowedModes = array('always','hide_add','hide_edit','hide_both','readonly_add','readonly_edit','readonly_both');
        $mode = isset($setting['action_visibility']) && in_array($setting['action_visibility'], $allowedModes, true) ? $setting['action_visibility'] : 'always';
        $name = isset($setting['visibility_field']) ? $setting['visibility_field'] : '';
        $values = isset($setting['visible_when']) && is_array($setting['visible_when']) ? $setting['visible_when'] : array();
        foreach (self::decode($fields) as $field) {
            if (!empty($field['field_name']) && $field['field_name'] === $name && in_array(isset($field['data_type']) ? $field['data_type'] : '', array('radio','dropdown-s'), true)) {
                $options = isset($allowedOptions[$name]) ? $allowedOptions[$name] : (!empty($field['csvoptions']) ? array_map('trim', explode(',', $field['csvoptions'])) : array());
                $values = array_values(array_intersect($options, $values));
                return array('action_visibility'=>$mode, 'visibility_field'=>$values ? $name : '', 'visible_when'=>$values);
            }
        }
        return array('action_visibility'=>$mode, 'visibility_field'=>'', 'visible_when'=>array());
    }

    public static function protectedFields($fields, $settings, $action, $record = array(), $actor = array()) {
        $settings = self::decode($settings);
        $tabs = isset($settings['tabs']) ? self::decode($settings['tabs']) : $settings;
        $protected = array();
        foreach (self::decode($fields) as $field) {
            $name = isset($field['tab_name']) ? trim($field['tab_name']) : '';
            if ($name === '' || $name === '-1' || empty($field['field_name'])) continue;
            $setting = isset($tabs[$name]) ? $tabs[$name] : array();
            if (self::isReadonly($setting, $action, $record, $fields, $actor) || (isset($setting['rules']) && !self::isVisible($setting, $action, $record, $fields, $actor))) $protected[$field['field_name']] = $field;
        }
        return $protected;
    }

    public static function protectedChildTables($fields, $settings, $action, $record = array(), $actor = array()) {
        $tables = array();
        $settings = self::decode($settings);
        $tabs = isset($settings['tabs']) ? self::decode($settings['tabs']) : $settings;
        foreach (self::decode($fields) as $field) {
            if (!isset($field['display_type']) || (int)$field['display_type'] !== 10 || empty($field['tab_name'])) continue;
            $setting = isset($tabs[trim($field['tab_name'])]) ? $tabs[trim($field['tab_name'])] : array();
            if (!self::isReadonly($setting, $action, $record, $fields, $actor) && self::isVisible($setting, $action, $record, $fields, $actor)) continue;
            foreach (self::decode(isset($field['child_tables']) ? $field['child_tables'] : array()) as $table) {
                if (is_string($table) && $table !== '') $tables[$table] = true;
            }
        }
        foreach (isset($settings['child_tables']) ? self::decode($settings['child_tables']) : array() as $table => $setting) {
            if (self::isReadonly($setting, $action, $record, $fields, $actor) || !self::isVisible($setting, $action, $record, $fields, $actor)) $tables[$table] = true;
        }
        return array_keys($tables);
    }
}
