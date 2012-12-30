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

-- Admin user account
-- Username: admin 
-- Password: password
INSERT INTO `users` (`username`, `password`, `salt`, `email`, `access`) VALUES
	('admin', '491210be0ea277a848bbe61ffefbe1cc72ef2ea9', 'I%Q@or7MY0#XOyB', 'radiopanel@localhost', '99');
