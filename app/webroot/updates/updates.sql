-- FlinkISO cumulative application database updates.

-- Existing-column and existing-table errors are ignored by the updater.

-- Legacy application schema updates
ALTER TABLE custom_triggers ADD notify_departments TINYINT(1) NULL AFTER notify_hods, ADD notify_branches TINYINT(1) NULL AFTER notify_departments, ADD notify_designations TINYINT(1) NULL AFTER notify_branches;
ALTER TABLE users CHANGE password password VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL;
ALTER TABLE qc_documents ADD and_or_condition TINYINT(1) NULL DEFAULT 0 AFTER designations;
ALTER TABLE `qc_documents` ADD `data_file_type` INT NULL DEFAULT '0' AFTER `data_type`;
ALTER TABLE `standards` ADD `short_name` VARCHAR(10) NULL AFTER `name`;
ALTER TABLE `users` ADD `is_hod` TINYINT(1) NULL DEFAULT '0' AFTER `is_mt`;
ALTER TABLE `qc_documents` ADD `published_by` VARCHAR(36) NULL AFTER `issuing_authority_id`;
ALTER TABLE `qc_documents` ADD `approval_step_id` VARCHAR(36) NULL AFTER `user_session_id`;
ALTER TABLE `qc_documents` ADD `reviewed_by` VARCHAR(36) NULL AFTER `prepared_by`, ADD `approval_date` DATE NULL AFTER `reviewed_by`, ADD `published_date` DATE NULL AFTER `approval_date`;

-- Approval workflow schema repair (no customer-specific seed data)
-- Repair for installations missing the approval workflow schema.
-- No customer-specific approval processes or users are inserted.

CREATE TABLE IF NOT EXISTS `approval_processes` (
  `id` varchar(36) NOT NULL,
  `sr_no` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT 'Process Title',
  `process_description` text NOT NULL,
  `applicable_to` text,
  `publish` tinyint(1) DEFAULT '1' COMMENT '0=Un 1=Pub',
  `record_status` tinyint(1) DEFAULT '0' COMMENT '0=Un-locked, 1=Locked',
  `status_user_id` varchar(36) DEFAULT NULL,
  `approval_step_id` varchar(36) DEFAULT NULL,
  `created_by` varchar(36) NOT NULL,
  `created` datetime NOT NULL,
  `modified_by` varchar(36) NOT NULL,
  `approved_by` varchar(36) DEFAULT NULL,
  `prepared_by` varchar(36) DEFAULT NULL,
  `modified` datetime NOT NULL,
  `soft_delete` tinyint(1) NOT NULL DEFAULT '0',
  `branchid` varchar(36) DEFAULT NULL,
  `departmentid` varchar(36) DEFAULT NULL,
  `company_id` varchar(36) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sr_no` (`sr_no`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `approval_steps` (
  `id` varchar(36) NOT NULL,
  `sr_no` int(11) NOT NULL AUTO_INCREMENT,
  `approval_process_id` varchar(36) NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT 'Process Title',
  `process_step` int(11) NOT NULL,
  `send_to_department_hod` tinyint(1) DEFAULT NULL,
  `send_to_designation` text,
  `send_to_admins` tinyint(1) DEFAULT NULL,
  `send_to_reviwers` tinyint(1) DEFAULT NULL,
  `send_to_publishers` tinyint(1) DEFAULT NULL,
  `send_to_approvers` tinyint(1) DEFAULT NULL,
  `send_to_users` text,
  `ignore_department` tinyint(1) DEFAULT '0',
  `ignore_branch` tinyint(1) DEFAULT '0',
  `comments` text,
  `approval_mode` int(11) DEFAULT '0' COMMENT '0=view-only, 1=edit',
  `approval_type` int(11) DEFAULT '0' COMMENT '0=all, 1=any',
  `publish` tinyint(1) DEFAULT '1' COMMENT '0=Un 1=Pub',
  `record_status` tinyint(1) DEFAULT '0' COMMENT '0=Un-locked, 1=Locked',
  `status_user_id` varchar(36) DEFAULT NULL,
  `created_by` varchar(36) NOT NULL,
  `created` datetime NOT NULL,
  `modified_by` varchar(36) NOT NULL,
  `approved_by` varchar(36) DEFAULT NULL,
  `prepared_by` varchar(36) DEFAULT NULL,
  `modified` datetime NOT NULL,
  `soft_delete` tinyint(1) NOT NULL DEFAULT '0',
  `branchid` varchar(36) DEFAULT NULL,
  `departmentid` varchar(36) DEFAULT NULL,
  `company_id` varchar(36) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sr_no` (`sr_no`),
  KEY `approval_process_id` (`approval_process_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

SET @add_approval_process_id = IF(
  (SELECT COUNT(*) FROM information_schema.COLUMNS
   WHERE TABLE_SCHEMA = DATABASE()
     AND TABLE_NAME = 'custom_tables'
     AND COLUMN_NAME = 'approval_process_id') = 0,
  'ALTER TABLE `custom_tables` ADD `approval_process_id` varchar(36) NULL AFTER `approvers`',
  'SELECT 1'
);
PREPARE add_approval_process_id_statement FROM @add_approval_process_id;
EXECUTE add_approval_process_id_statement;
DEALLOCATE PREPARE add_approval_process_id_statement;

-- Approval workflow column updates
ALTER TABLE `approvals` ADD `approval_step_id` VARCHAR(36) NULL AFTER `comments`;
ALTER TABLE `approval_comments` ADD `approval_step_id` VARCHAR(36) NULL AFTER `approval_id`;
ALTER TABLE `approvals` ADD `approval_process_id` VARCHAR(36) NULL AFTER `approval_step_id`;
ALTER TABLE `approval_comments` ADD `approval_process_id` VARCHAR(36) NULL AFTER `approval_step_id`;
ALTER TABLE `approval_comments` ADD `approval_cycle` INT(11) NULL DEFAULT '0' AFTER `approval_process_id`;
ALTER TABLE custom_tables ADD tab_settings TEXT NULL;
ALTER TABLE `approval_steps` MODIFY `send_to_designation` TEXT NULL;
-- Current trigger, delivery, and AI updates
ALTER TABLE `custom_triggers` ADD `event_name` varchar(80) DEFAULT NULL AFTER `action`, ADD `condition_operator` varchar(30) NOT NULL DEFAULT 'equals' AFTER `changed_field_value`, ADD `exclude_actor` tinyint(1) NOT NULL DEFAULT '0' AFTER `notify_designations`, ADD `enabled` tinyint(1) NOT NULL DEFAULT '1' AFTER `exclude_actor`, ADD `delivery_timing` varchar(20) NOT NULL DEFAULT 'immediate' AFTER `enabled`, ADD `delay_minutes` int(11) NOT NULL DEFAULT '0' AFTER `delivery_timing`, ADD `cooldown_minutes` int(11) NOT NULL DEFAULT '0' AFTER `delay_minutes`;
ALTER TABLE `custom_triggers` ADD `date_field` varchar(250) DEFAULT NULL AFTER `cooldown_minutes`, ADD `date_offset_days` int(11) NOT NULL DEFAULT '0' AFTER `date_field`;
UPDATE `custom_triggers` SET `event_name` = CASE WHEN `field_name` IS NOT NULL AND `field_name` <> '' AND `field_name` <> '-1' THEN 'field.changed' WHEN `action` = 0 THEN 'record.created' WHEN `action` = 1 THEN 'record.updated' WHEN `action` = 3 THEN 'record.deleted' ELSE NULL END WHERE `event_name` IS NULL;
CREATE TABLE IF NOT EXISTS `email_trigger_outboxes` ( `id` varchar(36) NOT NULL, `custom_trigger_id` varchar(36) NOT NULL, `event_id` varchar(36) NOT NULL, `event_name` varchar(80) NOT NULL, `model_name` varchar(120) NOT NULL, `record_id` varchar(36) NOT NULL, `recipient_email` varchar(255) NOT NULL, `subject` varchar(255) NOT NULL, `message` text, `payload` longtext, `idempotency_key` varchar(64) NOT NULL, `status` varchar(20) NOT NULL DEFAULT 'pending', `lock_token` varchar(36) DEFAULT NULL, `attempts` int(11) NOT NULL DEFAULT '0', `available_at` datetime NOT NULL, `last_error` text, `sent_at` datetime DEFAULT NULL, `created` datetime NOT NULL, `modified` datetime NOT NULL, PRIMARY KEY (`id`), UNIQUE KEY `idempotency_key` (`idempotency_key`), KEY `delivery_queue` (`status`,`available_at`), KEY `trigger_record` (`custom_trigger_id`,`record_id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `email_trigger_deliveries` ( `id` varchar(36) NOT NULL, `email_trigger_outbox_id` varchar(36) NOT NULL, `custom_trigger_id` varchar(36) NOT NULL, `event_id` varchar(36) NOT NULL, `recipient_email` varchar(255) NOT NULL, `status` varchar(20) NOT NULL, `attempt` int(11) NOT NULL DEFAULT '1', `error_message` text, `created` datetime NOT NULL, PRIMARY KEY (`id`), KEY `outbox` (`email_trigger_outbox_id`), KEY `trigger_event` (`custom_trigger_id`,`event_id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8;
ALTER TABLE `email_trigger_outboxes` ADD `sr_no` int(11) NOT NULL AUTO_INCREMENT UNIQUE AFTER `id`;
ALTER TABLE `email_trigger_outboxes` ADD `created_by` varchar(36) NOT NULL DEFAULT '0' AFTER `sent_at`;
ALTER TABLE `email_trigger_outboxes` ADD `modified_by` varchar(36) NOT NULL DEFAULT '0' AFTER `created`;
ALTER TABLE `email_trigger_outboxes` MODIFY `sr_no` int(11) NOT NULL AUTO_INCREMENT, MODIFY `created_by` varchar(36) NOT NULL DEFAULT '0', MODIFY `modified_by` varchar(36) NOT NULL DEFAULT '0';

-- Create AI history for installations that do not yet have the ais table.
CREATE TABLE IF NOT EXISTS `ais` (
  `id` char(36) NOT NULL,
  `company_id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `user_name` varchar(255) NOT NULL DEFAULT '',
  `source_controller` varchar(255) NOT NULL,
  `source_action` varchar(100) NOT NULL DEFAULT '',
  `custom_table_id` char(36) DEFAULT NULL,
  `qc_document_id` char(36) DEFAULT NULL,
  `record_id` char(36) DEFAULT NULL,
  `request` text NOT NULL,
  `response` longtext,
  `raw_response` longtext,
  `rebuild_response` longtext,
  `operation` varchar(64) NOT NULL DEFAULT '',
  `status` varchar(32) NOT NULL DEFAULT 'processing',
  `model` varchar(100) NOT NULL DEFAULT '',
  `duration_ms` int unsigned NOT NULL DEFAULT 0,
  `http_status` smallint unsigned NOT NULL DEFAULT 0,
  `error_details` text,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `context_lookup` (`company_id`,`source_controller`,`custom_table_id`,`qc_document_id`,`created`),
  KEY `user_lookup` (`company_id`,`user_id`,`created`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Upgrade the legacy question/response AI table for the persistent assistant
-- history. Each ADD is split by the updater, so an already-present column does
-- not prevent any remaining columns from being installed.
ALTER TABLE `ais` ADD `user_id` varchar(36) NOT NULL DEFAULT '', ADD `user_name` varchar(255) NOT NULL DEFAULT '', ADD `source_controller` varchar(255) NOT NULL DEFAULT '', ADD `source_action` varchar(100) NOT NULL DEFAULT '', ADD `custom_table_id` varchar(36) NULL DEFAULT NULL, ADD `qc_document_id` varchar(36) NULL DEFAULT NULL, ADD `record_id` varchar(36) NULL DEFAULT NULL, ADD `request` text NULL, ADD `raw_response` longtext NULL, ADD `rebuild_response` longtext NULL, ADD `operation` varchar(64) NOT NULL DEFAULT '', ADD `status` varchar(32) NOT NULL DEFAULT 'completed', ADD `model` varchar(100) NOT NULL DEFAULT '', ADD `duration_ms` int unsigned NOT NULL DEFAULT '0', ADD `http_status` smallint unsigned NOT NULL DEFAULT '0', ADD `error_details` text NULL;
ALTER TABLE `ais` MODIFY `response` longtext NULL;
ALTER TABLE `ais` ADD KEY `context_lookup` (`company_id`,`source_controller`,`custom_table_id`,`qc_document_id`,`created`);
ALTER TABLE `ais` ADD KEY `user_lookup` (`company_id`,`user_id`,`created`);

-- Super User is explicitly assigned by administrators; existing admins are
-- not automatically granted form-builder or Form Creation AI access.
ALTER TABLE `users` ADD COLUMN `is_super_user` TINYINT(1) NOT NULL DEFAULT 0 AFTER `is_mr`;


-- Language preferences. Existing catalogs, language rows and users.language_id are preserved.
CREATE TABLE IF NOT EXISTS `languages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `code` varchar(3) NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT 1,
  `plural_forms` varchar(255) NOT NULL DEFAULT 'nplurals=2; plural=(n != 1);',
  PRIMARY KEY (`id`), UNIQUE KEY `language_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Older installations have a UUID/short_code languages table. Add translation fields
-- without replacing its IDs or records. Duplicate-column/index errors are skipped by the updater.
ALTER TABLE `languages` ADD COLUMN `code` varchar(3) NULL DEFAULT NULL;
ALTER TABLE `languages` ADD COLUMN `enabled` tinyint(1) NOT NULL DEFAULT 0;
ALTER TABLE `languages` ADD COLUMN `plural_forms` varchar(255) NOT NULL DEFAULT 'nplurals=2; plural=(n != 1);';
ALTER TABLE `languages` ADD UNIQUE KEY `language_code` (`code`);
ALTER TABLE `languages` MODIFY `name` varchar(100) CHARACTER SET utf8 NOT NULL;

-- Accommodate both modern integer IDs and legacy UUID IDs/required metadata.
SET @language_numeric_id = (SELECT DATA_TYPE IN ('tinyint','smallint','mediumint','int','bigint') FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'languages' AND COLUMN_NAME = 'id');
SET @language_required_columns = (SELECT GROUP_CONCAT(CONCAT(', `', COLUMN_NAME, '`') ORDER BY ORDINAL_POSITION SEPARATOR '') FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'languages' AND IS_NULLABLE = 'NO' AND COLUMN_DEFAULT IS NULL AND EXTRA NOT LIKE '%auto_increment%' AND COLUMN_NAME NOT IN ('id','name','code','enabled','plural_forms'));
SET @language_required_values = (SELECT GROUP_CONCAT(CONCAT(', ', CASE WHEN COLUMN_NAME = 'short_code' THEN '@language_code' WHEN DATA_TYPE IN ('tinyint','smallint','mediumint','int','bigint','decimal','float','double','bit') THEN '0' WHEN DATA_TYPE = 'date' THEN 'CURRENT_DATE()' WHEN DATA_TYPE IN ('datetime','timestamp') THEN 'NOW()' WHEN DATA_TYPE = 'time' THEN 'CURRENT_TIME()' ELSE CHAR(39,39) END) ORDER BY ORDINAL_POSITION SEPARATOR '') FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'languages' AND IS_NULLABLE = 'NO' AND COLUMN_DEFAULT IS NULL AND EXTRA NOT LIKE '%auto_increment%' AND COLUMN_NAME NOT IN ('id','name','code','enabled','plural_forms'));
SET @language_seed_sql = CONCAT('INSERT INTO `languages` (`id`, `name`, `code`, `enabled`, `plural_forms`', COALESCE(@language_required_columns, ''), ') SELECT ', IF(@language_numeric_id, '(SELECT COALESCE(MAX(CAST(existing.id AS UNSIGNED)), 0) + 1 FROM languages existing)', 'UUID()'), ', @language_name, @language_code, 1, @language_plural', COALESCE(@language_required_values, ''), ' WHERE NOT EXISTS (SELECT 1 FROM languages WHERE code = @language_code)');
PREPARE flinkiso_language_seed FROM @language_seed_sql;
SET @language_name = 'English', @language_code = 'eng', @language_plural = 'nplurals=2; plural=(n != 1);';
EXECUTE flinkiso_language_seed;
SET @language_name = 'Deutsch', @language_code = 'deu';
EXECUTE flinkiso_language_seed;
DEALLOCATE PREPARE flinkiso_language_seed;
