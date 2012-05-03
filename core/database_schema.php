<?php
/*
System Database tables
*/
         
$system_table_schemas['program']="CREATE TABLE `program` (
  `rid`               INT(3) unsigned NOT NULL AUTO_INCREMENT,
  `short_name`       VARCHAR(20) NOT NULL,
  `full_name`        VARCHAR(300) NOT NULL,
  `logo`             VARCHAR(300) NOT NULL,
  `degree`           VARCHAR(500) NOT NULL,
  `class`            VARCHAR(500) NOT NULL,
  `grade`            VARCHAR(500) NOT NULL,
  `gpv`              VARCHAR(500) NOT NULL,
  `table_prefix`     VARCHAR(100) NOT NULL,
  `deleted`          BOOLEAN     DEFAULT false,
  `note`             VARCHAR(300) DEFAULT NULL,
  PRIMARY KEY (`rid`),
  UNIQUE KEY (`short_name`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$system_table_schemas['users']="CREATE TABLE `users` (
  `user_id`          INT NOT NULL AUTO_INCREMENT,
  `syear`            decimal(4,0) DEFAULT NULL,
  `current_school_id`decimal(10,0) DEFAULT NULL,
  `title`            VARCHAR(5) DEFAULT NULL,
  `first_name`       VARCHAR(100) DEFAULT NULL,
  `last_name`        VARCHAR(100) DEFAULT NULL,
  `middle_names`     VARCHAR(100) DEFAULT NULL,
  `username`         VARCHAR(100) DEFAULT NULL,
  `password`         VARCHAR(100) DEFAULT NULL,
  `phone`            VARCHAR(100) DEFAULT NULL,
  `email`            VARCHAR(100) DEFAULT NULL,
  `ldap_user_id`     VARCHAR(100) DEFAULT NULL,
  `group_id`         VARCHAR(100) DEFAULT 'DATA_ENTRY' COMMENT 'SUPER,ADMIN,DATA_ENTRY,HEADS,EXAMINATION,STUDENT',
  `theme`            VARCHAR(20) DEFAULT NULL,
  `layout`           VARCHAR(20) DEFAULT NULL,
  `homeroom`         VARCHAR(5) DEFAULT NULL,
  `programs`         TEXT DEFAULT NULL,
  `last_login`       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `failed_login`     decimal(10,0) DEFAULT NULL,
  `profile_id`       decimal(10,0) DEFAULT NULL,
  `rollover_id`      decimal(10,0) DEFAULT NULL,
  `deleted`          BOOLEAN     DEFAULT false,
  `note`             VARCHAR(300) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY (`username`),
  UNIQUE KEY (`ldap_user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$system_table_schemas['role']="CREATE TABLE `role`(
  `rid`              INT unsigned NOT NULL AUTO_INCREMENT,
  `group_name`       VARCHAR(100) NOT NULL COMMENT 'A short name to identify the group',
  `file_prefix`      VARCHAR(10) NOT NULL COMMENT 'The prefix for the group related files',
  `layout`           VARCHAR(10) NOT NULL COMMENT 'The page layout for the group',
  `theme`            VARCHAR(10) NOT NULL COMMENT 'Theme for the group',
  `description`      VARCHAR(300) NOT NULL COMMENT 'Description about the group',
  `timestamp`        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`rid`),
  UNIQUE KEY (`group_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";


$system_table_schemas['permission']="CREATE TABLE `permission`(
  `rid`              INT unsigned NOT NULL AUTO_INCREMENT,
  `group_user_id`    VARCHAR(100) NOT NULL,
  `module`           VARCHAR(100) NOT NULL,
  `page`             VARCHAR(100) NOT NULL,
  `is_user`          BOOLEAN NOT NULL,
  `timestamp`        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `access_right`     enum('DENIED','READ','WRITE') NOT NULL DEFAULT 'DENIED',
   PRIMARY KEY (`rid`),
   UNIQUE KEY (`group_user_id`,`module`,`page`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$system_table_schemas['log']="CREATE TABLE `log` (
  `rid`              BIGINT(10) unsigned NOT NULL AUTO_INCREMENT,
  `proto`            VARCHAR(5) DEFAULT NULL,
  `timestamp`        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user_id`          INT NOT NULL,
  `ip`               VARCHAR(15) NOT NULL DEFAULT '',
  `module_id`        VARCHAR(50) NOT NULL DEFAULT '',
  `page_id`          VARCHAR(50) NOT NULL DEFAULT '',
  `cmid`             BIGINT(10) unsigned NOT NULL DEFAULT '0',
  `action_`          VARCHAR(40) NOT NULL DEFAULT '',
  `url`              VARCHAR(100) NOT NULL DEFAULT '',
  `host`             VARCHAR(100) DEFAULT NULL,
  `request`          TEXT DEFAULT NULL,
  `info`             TEXT DEFAULT NULL,
  `agent`            TEXT DEFAULT NULL,
  `deleted`          BOOLEAN     DEFAULT false,
  `note`             VARCHAR(300) DEFAULT NULL,
  PRIMARY KEY (`rid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$system_table_schemas['filter']="CREATE TABLE `filter` (
  `rid`              INT unsigned NOT NULL AUTO_INCREMENT,
  `filter_name`      VARCHAR(50) DEFAULT NULL,
  `table_name`       VARCHAR(50) DEFAULT NULL,
  `timestamp`        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user_id`          INT NOT NULL,
  `filter_type`      ENUM('SQL','JSON') DEFAULT 'JSON',
  `filter`           TEXT,
  `deleted`          BOOLEAN     DEFAULT false,
  `note`             VARCHAR(300) DEFAULT NULL,
   PRIMARY KEY (`rid`),
   UNIQUE KEY (`filter_name`,`table_name`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$system_table_schemas['common_lists']="CREATE TABLE `common_lists`(
  `rid`              INT unsigned NOT NULL AUTO_INCREMENT,
  `list_name`        VARCHAR(100) NOT NULL,
  `list_label`       VARCHAR(300) NOT NULL,
  `json`             TEXT NOT NULL,
  `timestamp`        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
   PRIMARY KEY (`rid`),
   UNIQUE KEY (`list_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$system_table_schemas['update_common_lists']="INSERT INTO `common_lists`(`list_name`,`list_label`,`json`)values
   ('layout','Layout','[\"app\",\"pub\",\"web\"]'),
   ()
";

?>
