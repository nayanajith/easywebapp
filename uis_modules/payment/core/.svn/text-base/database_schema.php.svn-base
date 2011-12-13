<?php
/*
Program database tables set of tables for each program
*/
include(A_CORE."/database_schema.php");
$program_table_schemas	=array();
$system_table_schemas	=array();

$system_table_schemas['payment']="CREATE TABLE `%spayment` (
   `r_id` 					int NOT NULL AUTO_INCREMENT,
   `transaction_id` 		varchar(50) NOT NULL,
   `employee_id` 			int(4) NOT NULL,
   `amount` 				decimal(15,2) NOT NULL,
   `real_amount` 			decimal(15,2) NOT NULL,
   `tax` 					decimal(15,5) NOT NULL,
	`tp_ref_id`				varchar(50) NOT NULL,
   `account_type` 		int(4) NULL,
   `alert_sent` 			boolean NOT NULL DEFAULT FALSE,
   `clearing_done` 		boolean NOT NULL DEFAULT FALSE,
   `status` 				enum('PENDING','ACCEPTED','REJECTED') NOT NULL DEFAULT 'PENDING',
   `init_time` 			varchar(50),
   `program_id` 			varchar(10),
   `pay_for_id` 			varchar(10),
   `client_ip` 			varchar(200) NOT NULL,
   `client_status` 		varchar(200) NOT NULL,
   `updated_time` 		timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
   UNIQUE KEY (`transaction_id`),
   PRIMARY KEY (`r_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";

$system_table_schemas['filter']="CREATE TABLE `%sfilter` (
  `table_name` varchar(30) CHARACTER SET utf8 DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `filter` 	text CHARACTER SET utf8,
  `deleted` tinyint(1) DEFAULT '0',
  `note`  	text NULL,
  `filter_name` varchar(100) CHARACTER SET utf8 NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";


$system_table_schemas['pay_for']="CREATE TABLE `%spay_for` (
	`pay_for_id` 		int(3) unsigned NOT NULL AUTO_INCREMENT,
   `program_id` 		int(3) NOT NULL,
   `short_name` 		varchar(20) NOT NULL,
   `description` 		varchar(50) NOT NULL,
   `amount` 			decimal(15,2) NOT NULL,
   `tax` 				decimal(15,2) NOT NULL,
   `registration` 	enum('OPTIONAL','COMPULSORY') NOT NULL DEFAULT 'COMPULSORY',
   `pay_for_code` 				varchar(4) NOT NULL,
   `coordinator` 		varchar(100) NOT NULL,
   `deleted`         boolean     DEFAULT false,
 	`note` 				varchar(300) DEFAULT NULL,
   UNIQUE KEY (`pay_for_code`,`program_id`),
   PRIMARY KEY (`pay_for_id`)
)";


/*
System Database tables
*/
$system_table_schemas['employee']="CREATE TABLE `%semployee` (
   `employee_id` 			int(11) NOT NULL AUTO_INCREMENT,
   `registration_type` 	varchar(20) NOT NULL,
  	`title` 					varchar(5) NOT NULL,
   `first_name` 			varchar(50) NOT NULL,
   `middle_names` 		varchar(50) NOT NULL,
   `last_name` 			varchar(50) NOT NULL,
   `NIC` 					varchar(12) NULL,
   `confirm` 				varchar(40) NULL,
   `affiliation` 			varchar(100) NOT NULL,
   `email` 					varchar(100) NOT NULL,
   `telephone` 			varchar(50) NOT NULL,
   `fax` 					varchar(50) DEFAULT NULL,
   `address1` 				varchar(100) NOT NULL,
   `address2` 				varchar(100) NOT NULL,
   `city` 					varchar(100) NOT NULL,
   `state` 					varchar(50) NOT NULL,
   `zip` 					varchar(10) NOT NULL,
   `country` 				varchar(50) NOT NULL,
   `password` 				varchar(50) NOT NULL,
  	`functions` 			text NULL,
  	`last_login` 			timestamp NULL,
   `status` 				enum('PENDING','ACCEPTED','REJECTED') NOT NULL DEFAULT 'PENDING',
   `updated_time` 		timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
   UNIQUE KEY (`email`),
   UNIQUE KEY (`nic`),
   PRIMARY KEY (`employee_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";


         
$system_table_schemas['program']="CREATE TABLE `%sprogram` (
   `program_id` 		int(3) unsigned NOT NULL AUTO_INCREMENT,
   `short_name` 		varchar(20) NOT NULL,
   `tp_ip` 				varchar(300) NOT NULL,
   `tp_key` 			varchar(300) NOT NULL,
   `tp_callback` 		varchar(300) NOT NULL,
	`tp_mail`			varchar(300) NOT NULL,
	`tp_phone`			varchar(15) NOT NULL,
   `description` 		varchar(50) NOT NULL,
   `registration` 	enum('OPTIONAL','COMPULSORY') NOT NULL DEFAULT 'COMPULSORY',
   `logo` 				varchar(300) NOT NULL,
   `program_code` 				varchar(4) NOT NULL,
   `coordinator` 		varchar(100) NOT NULL,
   `deleted`         boolean     DEFAULT false,
 	`note` 				varchar(300) DEFAULT NULL,
   UNIQUE KEY (`program_code`),
   PRIMARY KEY (`program_id`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$system_table_schemas['banks']="CREATE TABLE `%sbanks`(
   `bank_id`   	int(3) unsigned NOT NULL AUTO_INCREMENT,
   `bank_name`    varchar(200) NOT  NULL,
   `root_dir`     varchar(200) NOT  NULL,
   `gateway_url`  varchar(200) NOT  NULL,
   `callback_url` varchar(200) NOT  NULL,
   `igpkey_dir`   varchar(200) NOT  NULL,
   `note`   text NULL,
   PRIMARY KEY (`bank_id`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8;";

/*Addd prefix to each table to reflect the module*/
/*
function add_prefix(&$value,$key,$prefix){
	$value=sprintf($value,$prefix);
}
array_walk($system_table_schemas,'add_prefix',MODULE."_");
array_walk($program_table_schemas,'add_prefix',MODULE."_%s_");

*/

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
