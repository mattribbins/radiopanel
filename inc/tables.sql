-- RadioPanel
-- SQL Import

-- Create tables
CREATE TABLE `users` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`username` VARCHAR(50) NOT NULL DEFAULT '0',
	`password` VARCHAR(255) NOT NULL DEFAULT '0',
	`salt` VARCHAR(127) NOT NULL DEFAULT '0',
	`email` VARCHAR(127) NOT NULL DEFAULT '0',
	`access` VARCHAR(2) NOT NULL DEFAULT '0',
	primary key (`id`))
)
COMMENT='Holds user accounts'
COLLATE='utf8_general_ci'
ENGINE=InnoDB;

CREATE TABLE `figures` (
	`fid` INT(16) UNSIGNED NULL AUTO_INCREMENT,
	`timestamp`  INT(10) UNSIGNED NULL DEFAULT NULL,
	`listeners` MEDIUMINT UNSIGNED NULL,
	PRIMARY KEY (`fid`)
)
COMMENT='Holds total listener figures'
COLLATE='utf8_general_ci'
ENGINE=InnoDB;

CREATE TABLE `streams` (
	`sid` SMALLINT UNSIGNED NULL AUTO_INCREMENT,
	`name` VARCHAR(64) NULL DEFAULT '0',
	`server` VARCHAR(256) NULL DEFAULT '0',
	`username` VARCHAR(64) NULL DEFAULT '0',
	`password` VARCHAR(64) NULL DEFAULT '0',
	`mountpoint` VARCHAR(64) NULL DEFAULT '0',
	`active` TINYINT UNSIGNED NULL DEFAULT '0',
	PRIMARY KEY (`sid`)
)
COMMENT='Holds the list of streams'
COLLATE='utf8_general_ci'
ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `clients` (
  `cid` int(10) NOT NULL AUTO_INCREMENT,
  `iceid` int(12) NOT NULL,
  `sid` int(10) NOT NULL,
  `server` varchar(15) NOT NULL,
  `mount` varchar(80) NOT NULL,
  `agent` varchar(255) NOT NULL,
  `referrer` varchar(512) DEFAULT NULL,
  `ip` varchar(20) NOT NULL,
  `city` varchar(30) DEFAULT NULL,
  `country` varchar(20) DEFAULT NULL,
  `duration` int(11) DEFAULT NULL,
  `datetime_start` datetime NOT NULL,
  `datetime_end` datetime DEFAULT NULL,
  PRIMARY KEY (`cid`),
  UNIQUE KEY `cid` (`cid`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `settings` (
  `setting` varchar(50) NOT NULL,
  `value` varchar(100) NOT NULL,
  UNIQUE KEY `setting` (`setting`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `radiopanel_test`.`settings` (`setting`, `value`) VALUES ('ver', '1.1.0.1');

-- Admin user account
-- Username: admin 
-- Password: password
INSERT INTO `users` (`username`, `password`, `salt`, `email`, `access`) VALUES
	('admin', '491210be0ea277a848bbe61ffefbe1cc72ef2ea9', 'I%Q@or7MY0#XOyB', 'radiopanel@localhost', '99');
