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
