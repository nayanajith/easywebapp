<?php
/*
System Database tables
*/
         
$system_table_schemas['program']="CREATE TABLE `program` (
  `id`               int(3) unsigned NOT NULL AUTO_INCREMENT,
  `short_name`       varchar(20) NOT NULL,
  `full_name`        varchar(300) NOT NULL,
  `logo`             varchar(300) NOT NULL,
  `degree`           varchar(500) NOT NULL,
  `class`            varchar(500) NOT NULL,
  `grade`            varchar(500) NOT NULL,
  `gpv`              varchar(500) NOT NULL,
  `table_prefix`     varchar(100) NOT NULL,
  `deleted`          boolean     DEFAULT false,
  `note`             varchar(300) DEFAULT NULL,
  PRIMARY KEY (`id`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$system_table_schemas['users']="CREATE TABLE `users` (
  `user_id`          INT NOT NULL AUTO_INCREMENT,
  `syear`             decimal(4,0) DEFAULT NULL,
  `current_school_id` decimal(10,0) DEFAULT NULL,
  `title`             varchar(5) DEFAULT NULL,
  `first_name`       varchar(100) DEFAULT NULL,
  `last_name`          varchar(100) DEFAULT NULL,
  `middle_names`      varchar(100) DEFAULT NULL,
  `username`          varchar(100) DEFAULT NULL,
  `password`          varchar(100) DEFAULT NULL,
  `phone`             varchar(100) DEFAULT NULL,
  `email`             varchar(100) DEFAULT NULL,
  `ldap_user_id`       varchar(100) DEFAULT NULL,
  `user_type`        enum('AC','AS','NA') COMMENT 'AC:academic, AS:academic support, NA:nonacademic',
  `permission`       enum('SUPER','NORMAL') NOT NULL DEFAULT 'NORMAL',
  `theme`             varchar(20) DEFAULT NULL,
  `layout`             varchar(20) DEFAULT NULL,
  `homeroom`          varchar(5) DEFAULT NULL,
  `programs`          text DEFAULT NULL,
  `last_login`       timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `failed_login`       decimal(10,0) DEFAULT NULL,
  `profile_id`       decimal(10,0) DEFAULT NULL,
  `rollover_id`       decimal(10,0) DEFAULT NULL,
  `deleted`          boolean     DEFAULT false,
   `note`             varchar(300) DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$system_table_schemas['permission']="CREATE TABLE `permission`(
  `user_id`          varchar(100) NOT NULL,
  `module`             varchar(100) NOT NULL,
  `page`             varchar(100) NOT NULL,
  `access_right`       enum('DENIED','READ','WRITE') NOT NULL DEFAULT 'DENIED',
  primary key (`user_id`,`module`,`page`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";



$system_table_schemas['log']="CREATE TABLE `log` (
  `id`                bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `proto`             varchar(5) DEFAULT NULL,
  `timestamp`          timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user_id`          INT NOT NULL,
  `ip`                varchar(15) NOT NULL DEFAULT '',
  `module`             varchar(50) NOT NULL DEFAULT '',
  `page`             varchar(50) NOT NULL DEFAULT '',
  `cmid`             bigint(10) unsigned NOT NULL DEFAULT '0',
  `action`             varchar(40) NOT NULL DEFAULT '',
  `url`                varchar(100) NOT NULL DEFAULT '',
  `host`             varchar(100) DEFAULT NULL,
  `request`          text DEFAULT NULL,
  `info`             text DEFAULT NULL,
  `agent`             text DEFAULT NULL,
  `deleted`          boolean     DEFAULT false,
  `note`             varchar(300) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$system_table_schemas['filter']="CREATE TABLE `filter` (
  `filter_name`       varchar(50) DEFAULT NULL,
  `table_name`       varchar(50) DEFAULT NULL,
  `user_id`          INT NOT NULL,
  `filter`             text,
  `deleted`          boolean     DEFAULT false,
   `note`             varchar(300) DEFAULT NULL,
   PRIMARY KEY (`filter_name`,`table_name`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";



?>
