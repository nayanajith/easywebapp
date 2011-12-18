<?php
/*
Program database tables set of tables for each program
*/
include(A_CORE."/database_schema.php");
$program_table_schemas   =array();
$system_table_schemas   =array();

$program_table_schemas['course']="CREATE TABLE `%scourse` (
  `course_id`        varchar(10)   NOT NULL COMMENT 'Primary Key: Unique Course ID.',
  `student_year`     char(2)       DEFAULT NULL,
  `semester`         char(2)       DEFAULT NULL,
  `course_name`      varchar(60) DEFAULT NULL,
  `prerequisite`     varchar(50) DEFAULT NULL,
  `lecture_credits`  smallint(6) unsigned DEFAULT NULL,
  `practical_credits` smallint(6) unsigned DEFAULT '0',
  `maximum_students` smallint(6) unsigned DEFAULT '0',
  `alt_course_id`    varchar(10) DEFAULT NULL,
  `offered_by`       varchar(10) DEFAULT NULL,
  `compulsory`       char(1)       DEFAULT NULL,
  `non_grade`        boolean       DEFAULT NULL,
  `non_credit`       boolean       DEFAULT NULL,
  `pass_grade`       varchar(2)  DEFAULT NULL,
  `deleted`          boolean     DEFAULT false,
  `note`             varchar(300) DEFAULT NULL,
   PRIMARY KEY (`course_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table that contains information of all the courses.' ;";


$program_table_schemas['exam']="CREATE TABLE `%sexam` ( 
   `exam_id`         int NOT NULL AUTO_INCREMENT,
   `exam_hid`        varchar(20) NOT NULL,
   `exam_old_id`     varchar(6),
  `semester`         int,
  `student_year`     int,
  `academic_year`    varchar(10),
  `exam_date`        date,
  `exam_time`        time,
  `venue`            varchar(25),
  `deleted`          boolean DEFAULT false,
   `note`            varchar(300),
  `timestamp`        timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
   UNIQUE KEY (`exam_hid`),
   PRIMARY KEY (`exam_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$program_table_schemas['rubric']="CREATE TABLE `%srubric` ( 
   `exam_hid`        varchar(20)       NOT NULL,
   `course_id`       varchar(10)   NOT NULL,
   `paper`           float    DEFAULT null,
   `assignment`      float    DEFAULT null,
   `paper_must`      boolean     DEFAULT false,
   `assignment_must` boolean     DEFAULT false,
   `deleted`         boolean     DEFAULT false,
   `note`            varchar(300),
  `timestamp`        timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
   PRIMARY KEY (`exam_hid`,`course_id`),
   FOREIGN KEY (`exam_hid`) REFERENCES %sexam(`exam_hid`) ON UPDATE CASCADE ON DELETE SET NULL, 
   FOREIGN KEY (`course_id`) REFERENCES %scourse(`course_id`) ON UPDATE CASCADE ON DELETE SET NULL
   
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";


$program_table_schemas['paper']="CREATE TABLE `%spaper`(
   `paper_id`        int          NOT NULL AUTO_INCREMENT,
   `course_id`       varchar(50) NOT NULL,
   `exam_hid`        varchar(20) NOT NULL,
   `examiner`        varchar(200) NULL,
   `no_of_questions` int          NOT NULL,
   `sections`        varchar(20) NOT NULL,
   `answer_file`     varchar(200) NULL ,
   `marking_logic_file`   varchar(200)   NULL ,
   `state`           varchar(50) NOT NULL,
   `deleted`         boolean     DEFAULT false,
   `note`            varchar(300) NULL,
   PRIMARY KEY (`paper_id`),
   FOREIGN KEY (`exam_hid`) REFERENCES %sexam(`exam_hid`) ON UPDATE CASCADE ON DELETE SET NULL,
   FOREIGN KEY (`course_id`) REFERENCES %scourse(`course_id`) ON UPDATE CASCADE ON DELETE SET NULL
   
)ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$program_table_schemas['eligibility']="CREATE TABLE `%seligibility`(
  `eligibility_name` varchar(50)    NOT NULL ,
  `eligibility_year` int             NOT NULL ,
  `GPA1`             double               DEFAULT   NULL,
  `GPA2`             double               DEFAULT   NULL,
  `GPA3`             double               DEFAULT   NULL,
  `GPA4`             double               DEFAULT   NULL,
  `GPA`              double               DEFAULT   NULL,
  `credits1`         double               DEFAULT   NULL,
  `credits2`         double               DEFAULT   NULL,
  `credits3`         double               DEFAULT   NULL,
  `credits4`         double               DEFAULT   NULL,
  `credits`          double               DEFAULT   NULL,
  `courses_year1`    text                DEFAULT   NULL,
  `courses_year2`    text                DEFAULT   NULL,
  `courses_year3`    text                DEFAULT   NULL,
  `courses_year4`    text                DEFAULT   NULL,
  `attendance`       text                DEFAULT   NULL,
  `pre_eligibility`  varchar(1000)  DEFAULT   NULL,
  `deleted`          boolean          DEFAULT false,
  `note`             varchar(300)    DEFAULT NULL,
  PRIMARY KEY (`eligibility_name`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$program_table_schemas['push']="CREATE TABLE `%spush`(
  `paper_id`         int          NOT NULL AUTO_INCREMENT,
  `index_regexp`     char(8)       NOT NULL DEFAULT '',
  `push`             double       DEFAULT NULL,
  `deleted`          boolean     DEFAULT false,
   `note`            varchar(300) DEFAULT NULL,
  PRIMARY KEY (`paper_id`),
  FOREIGN KEY (`paper_id`) REFERENCES %spaper(`paper_id`) ON UPDATE CASCADE ON DELETE SET NULL
)ENGINE=MyISAM DEFAULT CHARSET=utf8;";


$program_table_schemas['log']="CREATE TABLE `%slog` (
  `id`               bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `time`             time,
  `timestamp`        timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user_id`          bigint(10) unsigned NOT NULL DEFAULT '0',
  `ip`               varchar(15) NOT NULL DEFAULT '',
  `course`           bigint(10) unsigned NOT NULL DEFAULT '0',
  `module`           varchar(20) NOT NULL DEFAULT '',
  `cmid`             bigint(10) unsigned NOT NULL DEFAULT '0',
  `action`           varchar(40) NOT NULL DEFAULT '',
  `url`              varchar(100) NOT NULL DEFAULT '',
  `note`             varchar(300) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

/*
PR->Present
AB->ABsent
MC->Medical
EO->Exam offence
*/

$program_table_schemas['marks']="CREATE TABLE `%smarks` (
  `exam_hid`         varchar(20)       NOT NULL,
  `course_id`        char(10)      NOT NULL,
  `index_no`         varchar(8)       NOT NULL,
  `paper_mark`       int(3)        unsigned DEFAULT null,
  `assignment_mark`  int(3)        unsigned DEFAULT null,
  `final_mark`       varchar(3)    DEFAULT null,
  `push`             int(3)        unsigned DEFAULT null,
  `grand_final_mark` varchar(3)    DEFAULT null,
  `grade`            varchar(2)    DEFAULT null,
  `gpv`              float          DEFAULT NULL,
  `state`            enum('PR','AB','MC','EO') default 'PR',
  `can_release`      boolean       DEFAULT true,
  `deleted`          boolean     DEFAULT false,
  `timestamp`        timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `note`             varchar(300)    DEFAULT NULL,
   PRIMARY KEY (`exam_hid`,`course_id`,`index_no`),
   FOREIGN KEY (`exam_hid`) REFERENCES %sexam(`exam_hid`) ON UPDATE CASCADE ON DELETE SET NULL,
   FOREIGN KEY (`course_id`) REFERENCES %scourse(`course_id`) ON UPDATE CASCADE ON DELETE SET NULL,
   FOREIGN KEY (`index_no`) REFERENCES %sstudent(`index_no`) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$program_table_schemas['student']="CREATE TABLE `%sstudent`(
   `rid`             bigint(10)       unsigned NOT NULL AUTO_INCREMENT,
   `index_no`        varchar(8)       DEFAULT NULL,
   `registration_no` varchar(20)    DEFAULT NULL,
   `batch_id`      varchar(12)    DEFAULT NULL COMMENT 'Batch',
   `full_name`       varchar(500)   DEFAULT NULL,
   `initials`        varchar(20)    DEFAULT NULL,
   `last_name`       varchar(300)   DEFAULT NULL,
   `permanent_address` varchar(500) DEFAULT NULL,
   `current_address` varchar(500)    DEFAULT NULL,
   `phone`           varchar(15)    DEFAULT NULL,
   `email`           varchar(200)    DEFAULT NULL,
   `gender`          enum('M','F')    DEFAULT 'M',
   `title`           enum('MR','MS','MRS','REV') DEFAULT 'MR',
   `designation`     varchar(200)    DEFAULT NULL,
   `work_place`      varchar(300)    DEFAULT NULL,
   `NID`             varchar(15)    DEFAULT NULL,
   `current_year`    varchar(1)       DEFAULT NULL,
   `degree_GPA`      float(5,2)       DEFAULT NULL,
   `class_GPA`       float(5,2)       DEFAULT NULL,
   `date_of_regist`  date             DEFAULT NULL,
   `date_of_graduation` date          DEFAULT NULL,
   `date_of_birth`   date             DEFAULT NULL,
   `province`        varchar(100)    DEFAULT NULL,
   `district`        varchar(100)    DEFAULT NULL,
   `nationality`     varchar(100)   DEFAULT NULL,
   `religion`        varchar(100)   DEFAULT NULL,
   `ugc_z_score`     varchar(100)   DEFAULT NULL,
   `z_score`         varchar(100)   DEFAULT NULL,
   `AL_index_no`     varchar(100)   DEFAULT NULL,
   `AL_subject`      varchar(100)   DEFAULT NULL,
   `AL_result`       varchar(100)    DEFAULT NULL,
   `AL_general_english` varchar(100) DEFAULT NULL,
   `AL_general_knowledge` varchar(100) DEFAULT NULL,
   `AL_district`     varchar(100)    DEFAULT NULL,
   `AL_district_no`  varchar(100)    DEFAULT NULL,
   `photo`           varchar(256)    DEFAULT NULL,
   `amount`          double          DEFAULT NULL,
   `date_of_paid`    date             DEFAULT NULL,
   `paid_branch`     varchar(100)    DEFAULT NULL,
   `registered`      boolean          DEFAULT NULL,
   `deleted`         boolean          DEFAULT false,
   `status`          enum('READING','TRANSFERED','CANCELED','BANNED','GRADUATED') DEFAULT 'READING',
  `timestamp`        timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
   `note`            varchar(300)    DEFAULT NULL,
	UNIQUE KEY (`index_no`),
	UNIQUE KEY (`registration_no`),
   FOREIGN KEY (`batch_id`) REFERENCES %sbatch(`batch_id`) ON UPDATE CASCADE ON DELETE SET NULL,
	PRIMARY KEY(`rid`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8";

$program_table_schemas['state']="CREATE TABLE `%sstate` (
  `index_no`         varchar(8)    DEFAULT NULL,
  `current_year`     char(1)       DEFAULT NULL,
  `status`           varchar(300) DEFAULT NULL,
  `comment_year1`    text          DEFAULT NULL,
  `note_year1`       text          DEFAULT NULL,
  `comment_year2`    text          DEFAULT NULL,
  `note_year2`       text          DEFAULT NULL,
  `comment_year3`    text          DEFAULT NULL,
  `note_year3`       text          DEFAULT NULL,
  `comment_year4`    text          DEFAULT NULL,
  `note_year4`       text          DEFAULT NULL,
  `deleted`          boolean     DEFAULT false,
  `timestamp`        timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `note`             varchar(300)    DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$program_table_schemas['gpa']="CREATE TABLE `%sgpa` (
  `index_no`         varchar(8)       NOT NULL DEFAULT '',
  `degree_class`     char(1)       NOT NULL DEFAULT '',
  `GPV1`             double       DEFAULT NULL,
  `credits1`         int(3)       NOT NULL DEFAULT '0',
  `GPA1`             double       NOT NULL DEFAULT '0',
  `GPV2`             double       DEFAULT NULL,
  `credits2`         int(3)       NOT NULL DEFAULT '0',
  `GPA2`             double       NOT NULL DEFAULT '0',
  `GPV3`             double       DEFAULT NULL,
  `credits3`         int(3)       NOT NULL DEFAULT '0',
  `GPA3`             double       NOT NULL DEFAULT '0',
  `GPV4`             double       DEFAULT NULL,
  `credits4`         int(3)       NOT NULL DEFAULT '0',
  `GPA4`             double       NOT NULL DEFAULT '0',
  `GPV`              double       DEFAULT NULL,
  `GPA`              float(5,2)    DEFAULT NULL,
  `credits`          int(4)       NOT NULL DEFAULT '0',
  `deleted`          boolean     DEFAULT false,
  `timestamp`        timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
   `note`            varchar(300)    NOT NULL DEFAULT '0',
   PRIMARY KEY (`index_no`,`degree_class`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
 
/*
$program_table_schemas['course_selection']="CREATE TABLE `%sscourse_selection`(
  `index_no`         varchar(8)       NOT NULL DEFAULT '',
  `courses_year1`    text          DEFAULT NULL,
  `attendance_year1` text          DEFAULT NULL,
  `courses_year2`    text          DEFAULT NULL,
  `attendance_year2` text          DEFAULT NULL,
  `courses_year3`    text          DEFAULT NULL,
  `attendance_year3` text          DEFAULT NULL,
  `courses_year4`    text          DEFAULT NULL,
  `attendance_year4` text          DEFAULT NULL,
   PRIMARY KEY (`index_no`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
 */

$program_table_schemas['batch']="CREATE TABLE `%sbatch` (
  `batch_id`         varchar(50) NOT NULL DEFAULT '',
  `admission_year`   varchar(15) DEFAULT NULL,
  `code`             varchar(2) DEFAULT NULL,
  `date_of_addmission` date DEFAULT NULL,
  `deleted`          boolean     DEFAULT false,
   `note`            varchar(300) DEFAULT NULL,
   PRIMARY KEY (`batch_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

$program_table_schemas['filter']="CREATE TABLE `%sfilter` (
  `filter_name`      varchar(50) DEFAULT NULL,
  `table_name`       varchar(50) DEFAULT NULL,
  `user_id`          INT NOT NULL,
  `filter`           text,
  `deleted`          boolean     DEFAULT false,
   `note`            varchar(300) DEFAULT NULL,
   PRIMARY KEY (`filter_name`,`table_name`,`user_id`)
  /*, FOREIGN KEY (`user_id`) REFERENCES %suser(`user_id`) ON UPDATE CASCADE ON DELETE SET NULL */
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

$program_table_schemas['mcq_paper']="CREATE TABLE %smcq_paper(
   `paper_id`        int NOT NULL AUTO_INCREMENT,
   `course_id`       varchar(50) NOT NULL,
   `exam_hid`        varchar(50) NOT NULL,
   `examiner`        varchar(200)   NULL,
   `no_of_questions` int NOT NULL,
   `sections`        varchar(20) NOT NULL,
   `answer_file`     varchar(200)   NULL,
   `mark_logic_file` varchar(200)   NULL,
   `state`           varchar(50) NOT NULL,
   `index_delimiter` varchar(10) DEFAULT ';:;',
   `question_delimiter`    varchar(10) DEFAULT ';',
   `answer_delimiter` varchar(10) DEFAULT ';',
   `first_line_header`       boolean DEFAULT true,
   `blank_answer`    varchar(10) DEFAULT 'BLANK',
   `no_answer`       varchar(10) DEFAULT 'NOA',
   `logic_question_delimiter` varchar(10) DEFAULT ';',
   `logic_option_delimiter` varchar(10) DEFAULT ';',
   `logic_first_line_header` boolean DEFAULT true,
  `timestamp`        timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
   `note`                   text  NULL,
   PRIMARY KEY (`paper_id`),
   UNIQUE KEY (`course_id`,`exam_hid`),
   FOREIGN KEY (`exam_hid`) REFERENCES %sexam(`exam_hid`) ON UPDATE CASCADE ON DELETE SET NULL,
   FOREIGN KEY (`course_id`) REFERENCES %scourse(`course_id`) ON UPDATE CASCADE ON DELETE SET NULL
)ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$program_table_schemas['mcq_answers']="CREATE TABLE %smcq_answers(
   `index_no`        varchar(8) NOT NULL,
   `paper_id`        int NOT NULL,
   `answers`         text NULL,
   `marks`           int NULL,
   `state`           varchar(50) NOT NULL,
  `timestamp`        timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
   `note`            text NULL,
   PRIMARY KEY (`index_no`,`paper_id`),
   FOREIGN KEY (`paper_id`) REFERENCES %spapr(`paper_id`) ON UPDATE CASCADE ON DELETE SET NULL
)ENGINE=MyISAM DEFAULT CHARSET=utf8;";



$program_table_schemas['mcq_marking_logic']="CREATE TABLE %smcq_marking_logic(
   `question`        int NOT NULL,
   `option_id`       int NOT NULL DEFAULT 1,
   `multiple_choice` boolean NOT NULL DEFAULT 1,
   `mark_for_wrong_sns` int NULL,
   `mark_for_correct_sns` int NULL,
   `paper_id`        varchar(50) NOT NULL,
   `A`               int NULL,
   `B`               int NULL,
   `C`               int NULL,
   `D`               int NULL,
   `E`               int NULL,
   `BLANK`           int NULL,
   `NOA`             int NULL,
  `timestamp`        timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
   `note`            text NULL,
   PRIMARY KEY (`question`,`option_id`,`paper_id`),
   FOREIGN KEY (`paper_id`) REFERENCES %spapr(`paper_id`) ON UPDATE CASCADE ON DELETE SET NULL
  )ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$program_table_schemas['mcq_marks']="CREATE TABLE %smcq_marks(
  `index_no`           varchar(8) NOT NULL,
  `paper_id`           int NOT NULL,
  `section`            int NOT NULL,
  `mark`               int NOT NULL,
  `manual_mark`        int NOT NULL DEFAULT 0,
  `timestamp`          timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
   PRIMARY KEY (`index_no`,`paper_id`,`section`),
   FOREIGN KEY (`paper_id`) REFERENCES %spapr(`paper_id`) ON UPDATE CASCADE ON DELETE SET NULL
)ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$program_table_schemas['transcript']="CREATE TABLE `%stranscript` (
  `id`    int NOT NULL AUTO_INCREMENT,
  `index_no`           varchar(8) NOT NULL,
  `transcript_id`      char(2) DEFAULT NULL,
  `timestamp`          timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
   PRIMARY KEY (`id`),
   UNIQUE KEY (`transcript_id`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$program_table_schemas['grades']="CREATE TABLE `%sgrades` (
  `mark`                int(3) NOT NULL DEFAULT '0',
  `grade`               char(2) DEFAULT NULL,
  `gpv`                 decimal(3,2) DEFAULT NULL,
  PRIMARY KEY (`mark`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8;";

//Initial data for the grade table
$program_table_schemas['grades']="INSERT INTO `%sgrades` VALUES (0,'E','0.00'),(1,'E','0.00'),(2,'E','0.00'),(3,'E','0.00'),(4,'E','0.00'),(5,'E','0.00'),(6,'E','0.00'),(7,'E','0.00'),(8,'E','0.00'),(9,'E','0.00'),(10,'E','0.00'),(11,'E','0.00'),(12,'E','0.00'),(13,'E','0.00'),(14,'E','0.00'),(15,'E','0.00'),(16,'E','0.00'),(17,'E','0.00'),(18,'E','0.00'),(19,'E','0.00'),(20,'D-','0.75'),(21,'D-','0.75'),(22,'D-','0.75'),(23,'D-','0.75'),(24,'D-','0.75'),(25,'D-','0.75'),(26,'D-','0.75'),(27,'D-','0.75'),(28,'D-','0.75'),(29,'D-','0.75'),(30,'D','1.00'),(31,'D','1.00'),(32,'D','1.00'),(33,'D','1.00'),(34,'D','1.00'),(35,'D','1.00'),(36,'D','1.00'),(37,'D','1.00'),(38,'D','1.00'),(39,'D','1.00'),(40,'D+','1.25'),(41,'D+','1.25'),(42,'D+','1.25'),(43,'D+','1.25'),(44,'D+','1.25'),(45,'C-','1.75'),(46,'C-','1.75'),(47,'C-','1.75'),(48,'C-','1.75'),(49,'C-','1.75'),(50,'C','2.00'),(51,'C','2.00'),(52,'C','2.00'),(53,'C','2.00'),(54,'C','2.00'),(55,'C+','2.25'),(56,'C+','2.25'),(57,'C+','2.25'),(58,'C+','2.25'),(59,'C+','2.25'),(60,'B-','2.75'),(61,'B-','2.75'),(62,'B-','2.75'),(63,'B-','2.75'),(64,'B-','2.75'),(65,'B','3.00'),(66,'B','3.00'),(67,'B','3.00'),(68,'B','3.00'),(69,'B','3.00'),(70,'B+','3.25'),(71,'B+','3.25'),(72,'B+','3.25'),(73,'B+','3.25'),(74,'B+','3.25'),(75,'A-','3.75'),(76,'A-','3.75'),(77,'A-','3.75'),(78,'A-','3.75'),(79,'A-','3.75'),(80,'A','4.00'),(81,'A','4.00'),(82,'A','4.00'),(83,'A','4.00'),(84,'A','4.00'),(85,'A','4.00'),(86,'A','4.00'),(87,'A','4.00'),(88,'A','4.00'),(89,'A','4.00'),(90,'A+','4.25'),(91,'A+','4.25'),(92,'A+','4.25'),(93,'A+','4.25'),(94,'A+','4.25'),(95,'A+','4.25'),(96,'A+','4.25'),(97,'A+','4.25'),(98,'A+','4.25'),(99,'A+','4.25'),(100,'A+','4.25');";


/*
System Database tables
*/
         
$system_table_schemas['program']="CREATE TABLE `program` (
  `id`                  int(3) unsigned NOT NULL AUTO_INCREMENT,
  `short_name`          varchar(20) NOT NULL,
  `full_name`           varchar(300) NOT NULL,
  `logo`                varchar(300) NOT NULL,
  `degree`              varchar(500) NOT NULL,
  `class`               varchar(500) NOT NULL,
  `grade`               varchar(500) NOT NULL,
  `gpv`                 varchar(500) NOT NULL,
  `table_prefix`        varchar(100) NOT NULL,
  `deleted`             boolean     DEFAULT false,
  `note`               varchar(300) DEFAULT NULL,
  PRIMARY KEY (`id`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8;";



/*
add_table_prefix($system_table_schemas,MODULE);

//Check and if not exists, create the database and the program table
$GLOBALS['CONNECTION']    = mysql_connect($GLOBALS['DB_HOST'], $GLOBALS['DB_USER'], $GLOBALS['DB_PASS']);
$db_selected             = mysql_select_db($GLOBALS['DB'], $GLOBALS['CONNECTION']);

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
 */
?>
