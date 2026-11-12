# FlinkISO-QMS-Updates

This repository is only created so that existing FlinkISO QMS On-Premise users can update their already downloaded & installed application. If you are new to the application, visit [https://www.flinkiso.com/](https://www.flinkiso.com) for more details. You can register on the webisite and download the latest version of the application. 

1. In order to download and install updates, first download and install full application from [https://www.flinkiso.com/](https://www.flinkiso.com)
1. Follow the instructions from README
1. Make sure you backup your existing application before running any updates

##### Note: Do not use this repository to upgrade FlinkISO-Lite repository. 

# Setup 
In order to use this autoupdate from within the application, you must initially download 

1. app/View/Elements/top-menu.ctp
1. app/Controller/BillingController.php and 
1. app/View/Billing 

files to your installed application. Once copied, click on the Download icon at the top.

# How to update Manually

1. Download the updates
1. Directly overwrite the existing files & folders in you <flinkiso_app>/app directory
1. Run following SQL commands in sequence 

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

# Auto Update

After this relase Update Icon is available on the Top menu of the application. When ever a new update or bug-fixes are released, we will notify all the registred users. Users can then update the application after login to application and by clicking the Update Icon. System will then back the existing application in <flinkiso_app>/backup<backup dat(YYYY-MM-DD)>/ folder and then update the application. Incase of a failure, user can restore the previously saved application.

## 10 November 2026 Custom Table rollout notes

- **Re-create controls.** The Main Form tab on Custom Table details includes a borderless Re-create icon for Super Users. Re-create and Re-create Child group Cancel and Close at the right with tooltips. Submit appears beside Update in Field Settings and stays visible when Update is hidden. With Field Settings closed, Submit returns to the footer beside Cancel and Close. The local API V2 footer aligns Update and Submit beneath the scrolling notice. The document editor is stacked above the builder after the side-by-side layout was reverted.
- **ONLYOFFICE document saving.** Custom Table Add and Re-create use signed save callbacks so ONLYOFFICE can save without a browser session. The Re-create change covers presentation files and retains document version tracking. Both Nacos copies include the callback changes. Local checks accepted a valid signed callback and rejected tampering. Saving a presentation end to end from ONLYOFFICE still needs verification.
- **Internal Audit Report.** The local Nacos Internal Audit module has a report with an icon at the end of the toolbar. It uses the audit, agenda, corrective action and corrective action child tables. The report action lives in AppController and the view uses the default layout, so regenerating the module does not overwrite the action. This report is specific to Nacos.
- **Audit Agenda labels.** The Standard column was reported to display raw UUIDs even after Re-create and Rebuild Module. Verify that existing child rows display Standard names after regeneration.
- **Approval routing.** The preparer can approve when they also have approver permission. The affected user could see Approved after the change, but the option to send the record to the next step was still missing. The next-step handoff remains open.
- **Rebuild Module.** Nacos showed an unlock link when rebuilding a locked form. Pulse was reported to prompt for the password in a popup. Verify the Pulse behavior and track parity separately from the generic API connection error.
- **Rollout.** These entries record local changes and open follow-ups. The Submit placement and ONLYOFFICE callbacks are in both Nacos copies. The Field Settings alignment is in local API V2. Shared updater code packaging, wider rollout and production verification are pending.
