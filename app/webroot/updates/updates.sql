ALTER TABLE document_downloads CHANGE reccord_id record_id VARCHAR(36)  NULL DEFAULT NULL;
ALTER TABLE document_downloads ADD name VARCHAR(255) NULL AFTER sr_no;
ALTER TABLE companies ADD change_management_table VARCHAR(36) NULL AFTER version, ADD change_management_table_fields TEXT NULL AFTER change_management_table;
ALTER TABLE graph_panels ADD data_type INT(1) NULL DEFAULT 0 AFTER graph_type;
ALTER TABLE graph_panels ADD value_field VARCHAR(255) NULL AFTER data_type;
ALTER TABLE graph_panels ADD position INT NULL DEFAULT 0 AFTER color, ADD size INT NULL DEFAULT 3 AFTER position;
ALTER TABLE graph_panels ADD title VARCHAR(255) NULL DEFAULT NULL AFTER sr_no;
ALTER TABLE qc_documents ADD name VARCHAR(255)  NOT NULL AFTER sr_no;
ALTER TABLE processes CHANGE process_objecttive_and_metrics process_objective_and_metrics TEXT NULL DEFAULT NULL;
ALTER TABLE users ADD pwd_last_modified DATETIME NULL AFTER email_token_expires;
ALTER TABLE qc_documents ADD additional_clauses VARCHAR(255) NULL AFTER standard_id;
ALTER TABLE qc_documents ADD data_update_type INT(1) NULL DEFAULT 0 AFTER schedule_id;
ALTER TABLE users ADD is_creator TINYINT(1) NULL DEFAULT 1 AFTER is_approver, ADD is_publisher TINYINT(1) NULL DEFAULT 1 AFTER is_creator;
ALTER TABLE custom_tables ADD creators TEXT NULL AFTER users, ADD editors TEXT NULL AFTER creators, ADD viewers TEXT NULL AFTER editors, ADD approvers TEXT NULL AFTER viewers;
ALTER TABLE files ADD versions TEXT NULL AFTER version_keys;
ALTER TABLE qc_documents ADD allow_download TINYINT(1) NULL AFTER editors, ADD allow_print TINYINT(1) NULL AFTER allow_download;
ALTER TABLE custom_triggers ADD notify_departments TINYINT(1) NULL AFTER notify_hods, ADD notify_branches TINYINT(1) NULL AFTER notify_departments, ADD notify_designations TINYINT(1) NULL AFTER notify_branches;
ALTER TABLE users CHANGE password password VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL;
ALTER TABLE qc_documents ADD and_or_condition TINYINT(1) NULL DEFAULT 0 AFTER designations;
ALTER TABLE `qc_documents` ADD `data_file_type` INT NULL DEFAULT '0' AFTER `data_type`;
ALTER TABLE `standards` ADD `short_name` VARCHAR(10) NULL AFTER `name`;
-- Table structure for table `approval_processes`
--

CREATE TABLE `approval_processes` (`id` varchar(36) NOT NULL,`sr_no` int(11) NOT NULL,`title` varchar(255) NOT NULL DEFAULT 'Process Title',`process_description` text NOT NULL,`applicable_to` text,`publish` tinyint(1) DEFAULT '1' COMMENT '0=Un 1=Pub',`record_status` tinyint(1) DEFAULT '0' COMMENT '0=Un-locked, 1=Locked',`status_user_id` varchar(36) DEFAULT NULL,`approval_step_id` varchar(36) DEFAULT NULL,`created_by` varchar(36) NOT NULL,`created` datetime NOT NULL,`modified_by` varchar(36) NOT NULL,`approved_by` varchar(36) DEFAULT NULL,`prepared_by` varchar(36) DEFAULT NULL,`modified` datetime NOT NULL,`soft_delete` tinyint(1) NOT NULL DEFAULT '0',`branchid` varchar(36) DEFAULT NULL,`departmentid` varchar(36) DEFAULT NULL,`company_id` varchar(36) DEFAULT NULL) ENGINE=MyISAM DEFAULT CHARSET=utf8;
INSERT INTO `approval_processes` (`id`, `sr_no`, `title`, `process_description`, `applicable_to`, `publish`, `record_status`, `status_user_id`, `approval_step_id`, `created_by`, `created`, `modified_by`, `approved_by`, `prepared_by`, `modified`, `soft_delete`, `branchid`, `departmentid`, `company_id`) VALUES
('1e03dd49-d903-4946-8e02-ce5a54c79cc0', 1, 'General Approval Process', 'General Approval Process for custom html form', '[\"28eb2068-cf9d-4dce-8ea6-2347e14b3d60\",\"b2636904-8dff-4356-a06a-4887341c8def\",\"06ab4495-44dc-48b3-86da-61f67f492cb7\",\"b5eaa2ea-e624-4028-887c-c55d3dc1cece\",\"e60b9ebd-6a04-4caa-975d-c7882a65a704\",\"ac69bfd7-9b0a-4cc8-8708-5fd9a015d16e\",\"795b0c71-a7dc-4559-bfb3-23be11d735d2\",\"a41faf5d-4748-4c3c-8de2-03b91630a93d\",\"0d35f561-262a-4620-903e-61eb6e9a683a\"]', NULL, 0, NULL, NULL, '8df2f194-4937-45cb-babc-d2a949df4f08', '2026-04-11 19:20:57', '8df2f194-4937-45cb-babc-d2a949df4f08', NULL, NULL, '2026-06-22 14:41:27', 0, '8d454efc-842c-4d41-8637-375f46b67ed4', 'e89ddb75-5305-4b1a-95c7-82eca642e965', 'a50c7ec8-d59c-4095-8297-5cad825f8aa6'),
('3233d6a8-eb16-412f-bf9f-bd3189aa491b', 2, 'Document Approval Process', 'Every use must send the newly created document to 1. Reviewer 2. Approver 3. Publisher.', '[\"qc_documents\"]', NULL, 0, NULL, NULL, '8df2f194-4937-45cb-babc-d2a949df4f08', '2026-06-09 14:19:45', '8df2f194-4937-45cb-babc-d2a949df4f08', NULL, NULL, '2026-06-23 15:30:01', 0, '8d454efc-842c-4d41-8637-375f46b67ed4', 'e89ddb75-5305-4b1a-95c7-82eca642e965', 'a50c7ec8-d59c-4095-8297-5cad825f8aa6');
ALTER TABLE `approval_processes` ADD PRIMARY KEY (`id`), ADD KEY `sr_no` (`sr_no`);
ALTER TABLE `approval_processes`  MODIFY `sr_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
ALTER TABLE `custom_tables` ADD `approval_process_id` VARCHAR(36) NULL AFTER `approvers`;
ALTER TABLE `users` ADD `is_hod` TINYINT(1) NULL DEFAULT '0' AFTER `is_mt`;