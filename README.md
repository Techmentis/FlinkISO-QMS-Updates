# FlinkISO-QMS-Updates

1. In order to download and install updates, first download and install full application from [https://www.flinkiso.com/](https://www.flinkiso.com)
1. Follow the instructions from README
1. Make sure you backup your existing application before running any updates

# How to update

1. Download the updates
1. Directly overwrite the existing files & folders in you <flinkiso_app>/app directory
1. Run following SQL commands in sequence 

`ALTER TABLE `document_downloads` CHANGE `reccord_id` `record_id` VARCHAR(36) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;`

`ALTER TABLE `document_downloads` ADD `name` VARCHAR(255) NULL AFTER `sr_no`;`

`ALTER TABLE `companies` ADD `change_management_table` VARCHAR(36) NULL AFTER `version`, ADD `change_management_table_fields` TEXT NULL AFTER `change_management_table`;`
