-- Language preferences: users.language_id already exists in the application schema.
CREATE TABLE IF NOT EXISTS `languages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `code` varchar(3) NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT 1,
  `plural_forms` varchar(255) NOT NULL DEFAULT 'nplurals=2; plural=(n != 1);',
  PRIMARY KEY (`id`), UNIQUE KEY `language_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
INSERT INTO `languages` (`id`, `name`, `code`, `enabled`, `plural_forms`)
SELECT 1, 'English', 'eng', 1, 'nplurals=2; plural=(n != 1);'
WHERE NOT EXISTS (SELECT 1 FROM `languages` WHERE `code` = 'eng');
INSERT INTO `languages` (`name`, `code`, `enabled`, `plural_forms`)
SELECT 'Deutsch', 'deu', 1, 'nplurals=2; plural=(n != 1);'
WHERE NOT EXISTS (SELECT 1 FROM `languages` WHERE `code` = 'deu');
