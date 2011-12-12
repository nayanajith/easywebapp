<?php
/*
Program database tables set of tables for each program
*/
include A_CORE."/database_schema.php";
$program_table_schemas=array();
$program_table_schemas['course']="CREATE TABLE `%scourse` ( 
 `course_id`         mediumint    NOT NULL AUTO_INCREMENT,
 `short_name`        varchar(20)  NOT NULL,
 `long_name`         varchar(100)  NOT NULL,
 `course_fee`        mediumint    NOT NULL,
 `seating_limit`     mediumint    NOT NULL,
 `venue`             varchar(60)  DEFAULT NULL,
 `time`              varchar(60)  DEFAULT NULL, 
 `lecturer`          varchar(60)  DEFAULT NULL,
 `administrator`     varchar(60)  DEFAULT NULL,
 `contact_number`    mediumint    DEFAULT NULL,
 `descr`             text         DEFAULT NULL,
 `display`           varchar(20)  default null,
 PRIMARY KEY (`course_id`)
 ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$program_table_schemas['schedule']="CREATE TABLE `%sschedule` (
  `session_id`          mediumint   NOT NULL AUTO_INCREMENT,
  `course_id`           mediumint   NOT NULL,
  `session_name`        varchar(30) DEFAULT NULL,
  `start_date`          DATE        DEFAULT NULL,
  `end_date`            DATE        DEFAULT NULL, 
  `display`           varchar(20)  default null,
	PRIMARY KEY (`session_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$program_table_schemas['reg']="CREATE TABLE `%sreg` (
  `reg_id`              mediumint   NOT NULL AUTO_INCREMENT,
  `session_id`          mediumint   NOT NULL,
  `student_id`          mediumint   NOT NULL,
  `status`              varchar(20) DEFAULT NULL,
  `payment_method`      varchar(20) DEFAULT NULL,
  `payment_id`          varchar(20) DEFAULT NULL,
  `certificate_id`      varchar(20) DEFAULT NULL,
  	PRIMARY KEY (`reg_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$program_table_schemas['student']="CREATE TABLE `%sstudent` (
  `student_id`          mediumint  	NOT NULL AUTO_INCREMENT,
  `email`               varchar(60)	NOT NULL,
  `NIC`                 varchar(20) NOT NULL,
  `title`               varchar(20) NOT NULL,
  `first_name`          varchar(100) NOT NULL,
  `middle_names`        varchar(100) NOT NULL,
  `last_name`           varchar(100) NOT NULL,
  `address_line_1`      varchar(200) NOT NULL,
  `address_line_2`      varchar(200) DEFAULT NULL,
  `address_line_3`      varchar(200) DEFAULT NULL,
  `city`                varchar(200) NOT NULL,
  `province`            varchar(200) NOT NULL,
  `phone_num_1`         varchar(15) NOT NULL,  
  `phone_num_2`         varchar(15) DEFAULT NULL,  
   UNIQUE KEY (`email`),
   UNIQUE KEY (`NIC`),
	PRIMARY KEY (`student_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";


?>
