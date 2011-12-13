<?php
/*
Program database tables set of tables for each program
*/
include(A_CORE."/database_schema.php");
$program_table_schemas	=array();
$system_table_schemas	=array();

$system_table_schemas['course']="CREATE TABLE `%scourse` (
   `course_id` 		   varchar(8) NOT NULL,
   `title` 		         varchar(200) NOT NULL,
   `description` 		   text NOT NULL,
   `fee` 		         int NOT NULL,
   `coordinator_name`   varchar(100) NOT NULL,
   `coordinator_email`  varchar(100) NOT NULL,
   `coordinator_phone`  varchar(100) NOT NULL,
   `disabled`           boolean DEFAULT false,
   `deleted`            boolean DEFAULT false,
 	`note` 				   varchar(300) DEFAULT NULL,
   PRIMARY KEY (`course_id`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$system_table_schemas['batch']="CREATE TABLE `%sbatch` (
   `batch_id` 		varchar(20) NOT NULL,
   `course_id` 	varchar(8) NOT NULL,
   `description` 	varchar(50) NOT NULL,
   `start_date`   date NOT NULL,
   `end_date`     date NOT NULL,
   `start_time`   time NOT NULL,
   `end_time`     time NOT NULL,
   `venue`        varchar(100) NOT NULL,
   `seats`        int(3) NOT NULL,
   `disabled`     boolean DEFAULT false,
   `deleted`      boolean DEFAULT false,
 	`note` 			varchar(300) DEFAULT NULL,
   UNIQUE KEY (`batch_id`,`course_id`),
   PRIMARY KEY (`batch_id`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$system_table_schemas['filter']="CREATE TABLE `%sfilter` (
  `filter_id` 		int(11) NOT NULL AUTO_INCREMENT,
  `table_name`    varchar(30) CHARACTER SET utf8 DEFAULT NULL,
  `user_id`       int(11) NOT NULL,
  `filter` 	      text CHARACTER SET utf8,
  `deleted`       tinyint(1) DEFAULT '0',
  `note`  	      text NULL,
  `filter_name`   varchar(100) CHARACTER SET utf8 NOT NULL DEFAULT '',
   PRIMARY KEY (`filter_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$program_table_schemas['enroll']="CREATE TABLE `%senroll` (
  `id`                  mediumint   NOT NULL AUTO_INCREMENT,
  `batch_id`            varchar(20)   NOT NULL,
  `registration_no`     varchar(8)   NOT NULL,
  `payment_status` 		enum('PENDING','ACCEPTED','REJECTED') DEFAULT NULL,
  `payment_method`      enum('OFFLINE','ONLINE') DEFAULT NULL,
  `transaction_id`      varchar(20) DEFAULT NULL,
  `reserved`            boolean DEFAULT NULL false,
  `updated_time` 		   timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

   UNIQUE KEY (`batch_id`,`registration_no`),
  	PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$program_table_schemas['invoice']="CREATE TABLE `%sinvoice` (
  `id`                  int   NOT NULL AUTO_INCREMENT,
  `invoice_id`          varchar(8)     NOT NULL,
  `invoice_title`       varchar(200)   NOT NULL,
  `purpose`             varchar(200)   NOT NULL,
  `amount_number`       int            NOT NULL,
  `amount_word`         varchar(200)   NOT NULL,
  `payer_name` 		   varchar(300)   NOT NULL,
  `payer_NIC` 		      varchar(11)    NOT NULL,
  `acc_no`              varchar(30)    NOT NULL,
   `updated_time` 		timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
   UNIQUE KEY (`invoice_id`),
  	PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";



$system_table_schemas['student']="CREATE TABLE `%sstudent` (
   `rec_id` 				int(11) NOT NULL AUTO_INCREMENT,
   `registration_no` 	varchar(8),
   `index_no` 				varchar(8),
   `program`				varchar(10) NOT NULL,
  	`title` 					varchar(5) NOT NULL,
   `first_name` 			varchar(50) NOT NULL,
   `middle_names` 		varchar(50) NOT NULL,
   `last_name` 			varchar(50) NOT NULL,
   `NIC` 					varchar(12) NULL,
   `DOB` 					date NULL,
   `gender` 				enum('F','M') NOT NULL,
   `confirm` 				varchar(40) NULL,

	/*degree 1 details*/
	`degree_title_1`		varchar(300),
	`year_of_award_1`		varchar(4),
	`class_1`				varchar(10),
	`university_1`			varchar(300),
	`date_entered_1`		date,
	`date_left_1`			date,
	`subject1_1`			varchar(200),
	`subject2_1`			varchar(200),
	`subject3_1`			varchar(200),
	`subject4_1`			varchar(200),

	/*degree 1 details*/
	`degree_title_2`		varchar(300),
	`year_of_award_2`		varchar(4),
	`class_2`				varchar(10),
	`university_2`			varchar(300),
	`date_entered_2`		date,
	`date_left_2`			date,
	`subject1_2`			varchar(200),
	`subject2_2`			varchar(200),
	`subject3_2`			varchar(200),
	`subject4_2`			varchar(200),

	/*other education/professional qualifications*/
	`edu_prof_qual1`		text,
	`edu_prof_qual2`		text,
	`edu_prof_qual3`		text,
	`edu_prof_qual4`		text,
	`edu_prof_qual5`		text,
	`edu_prof_qual6`		text,

	/*employment records*/
	`emp_rec1`				text,
	`emp_rec2`				text,
	`emp_rec3`				text,
	`emp_rec4`				text,
	`emp_rec5`				text,

	/*career details*/
   `job_description` 	text,
   `affiliation` 			varchar(300) NOT NULL,
   `designation` 			varchar(300) NOT NULL,

	/*permenent contact information*/
   `email_1` 				varchar(100) NOT NULL,
   `telephone_1` 			varchar(10) NOT NULL,
   `mobile_1` 				varchar(10) NOT NULL,
   `address1_1`			varchar(200) NOT NULL,
   `address2_1` 			varchar(200) NOT NULL,
   `address3_1` 			varchar(200) NOT NULL,

	/*Office contact information*/
   `email_2` 				varchar(100) NOT NULL,
   `telephone_2` 			varchar(10) NOT NULL,
   `mobile_2` 				varchar(10) NOT NULL,
   `address1_2`			varchar(200) NOT NULL,
   `address2_2` 			varchar(200) NOT NULL,
   `address3_2` 			varchar(200) NOT NULL,

	/*Correspondent address*/
	`corresp_addr`			enum('PERMANENT','OFFICE') NOT NULL DEFAULT 'PERMANENT',

   `city` 					varchar(100) NOT NULL,
   `state` 					varchar(50) NOT NULL,
   `zip` 					varchar(10) NOT NULL,
   `country` 				varchar(50) NOT NULL,

	/*refree information*/
	`referee_name1`		varchar(300),
	`referee_designation1`	varchar(300),
	`referee_work_place1`	varchar(300),
	`referee_address1`		text,
	`referee_phone1`		varchar(10),
	`referee_email1`		varchar(300),

	/*refree information*/
	`referee_name2`		varchar(300),
	`referee_designation2`	varchar(300),
	`referee_work_place2`	varchar(300),
	`referee_address2`		text,
	`referee_phone2`		varchar(10),
	`referee_email2`		varchar(300),

	/*login details*/
   `password` 				varchar(50) NOT NULL,
  	`functions` 			text NULL,
  	`last_login` 			timestamp NULL,
   `status` 				enum('PENDING','ACCEPTED','REJECTED') DEFAULT NULL,
	`downloaded` 			boolean NOT NULL DEFAULT false,
	`received` 				boolean NOT NULL DEFAULT false,
   `transaction_id` 		varchar(20),
   `updated_time` 		timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
   UNIQUE KEY (`email_1`),
   UNIQUE KEY (`registration_no`),
   UNIQUE KEY (`nic`),
   PRIMARY KEY (`rec_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";


add_table_prefix($system_table_schemas,MODULE);

/*Check and if not exists, create the database and the program table*/
$GLOBALS['CONNECTION'] 	= mysql_connect($GLOBALS['DB_HOST'], $GLOBALS['DB_USER'], $GLOBALS['DB_PASS']);
$db_selected 				= mysql_select_db($GLOBALS['DB'], $GLOBALS['CONNECTION']);

if (!$db_selected) {
	log_msg($GLOBALS['DB'],"Creating database and tables...");
	mysql_query("CREATE DATABASE ".$GLOBALS['DB'],$GLOBALS['CONNECTION']);
	create_system_tables();
	$db_selected = mysql_select_db($GLOBALS['DB'], $GLOBALS['CONNECTION']);
	if ($db_selected) {
		log_msg($GLOBALS['DB'],"Successfully created the database!");
	}
}elseif(isset($_REQUEST['create_tables']) && $_REQUEST['create_tables'] == 'true'){
	log_msg($GLOBALS['DB'],"Creating tables...");
	create_system_tables();
}

?>
