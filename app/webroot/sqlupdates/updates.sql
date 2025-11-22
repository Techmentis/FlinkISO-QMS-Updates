ALTER TABLE `document_downloads` CHANGE `reccord_id` `record_id` VARCHAR(36)  NULL DEFAULT NULL;
ALTER TABLE `document_downloads` ADD `name` VARCHAR(255) NULL AFTER `sr_no`;
ALTER TABLE `companies` ADD `change_management_table` VARCHAR(36) NULL AFTER `version`, ADD `change_management_table_fields` TEXT NULL AFTER `change_management_table`;



ALTER TABLE `graph_panels` ADD `data_type` INT(1) NULL DEFAULT '0' COMMENT '0=count,1=sum,2=avg' AFTER `graph_type`;
ALTER TABLE `graph_panels` ADD `value_field` VARCHAR(255) NULL AFTER `data_type`;
ALTER TABLE `graph_panels` ADD `position` INT NULL DEFAULT'0' AFTER `color`, ADD `size` INT NULL DEFAULT '3' AFTER `position`;
ALTER TABLE `graph_panels` ADD `title` VARCHAR(255) NULL DEFAULT NULL AFTER `sr_no`;


ALTER TABLE `qc_documents` ADD `name` VARCHAR(255)  NOT NULL AFTER `sr_no`;
ALTER TABLE `processes` CHANGE `process_objecttive_and_metrics` `process_objective_and_metrics` TEXT NULL DEFAULT NULL;

ALTER TABLE `users` ADD `pwd_last_modified` DATETIME NULL AFTER `email_token_expires`;

ALTER TABLE `qc_documents` ADD `additional_clauses` VARCHAR(255) NULL AFTER `standard_id`;
ALTER TABLE `users` ADD `is_creator` TINYINT(1) NULL DEFAULT '1' AFTER `is_approver`, ADD `is_publisher` TINYINT(1) NULL DEFAULT '1' AFTER `is_creator`;
ALTER TABLE `custom_tables` ADD `creators` TEXT NULL AFTER `users`, ADD `editors` TEXT NULL AFTER `creators`, ADD `viewers` TEXT NULL AFTER `editors`, ADD `approvers` TEXT NULL AFTER `viewers`;
ALTER TABLE `qc_documents` ADD `allow_download` TINYINT(1) NULL AFTER `editors`, ADD `allow_print` TINYINT(1) NULL AFTER `allow_download`;
ALTER TABLE `custom_triggers` ADD `notify_departments` TINYINT(1) NULL AFTER `notify_hods`, ADD `notify_branches` TINYINT(1) NULL AFTER `notify_departments`, ADD `notify_designations` TINYINT(1) NULL AFTER `notify_branches`;