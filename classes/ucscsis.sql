-- MySQL dump 10.13  Distrib 5.1.49, for debian-linux-gnu (i486)
--
-- Host: localhost    Database: ucscsis
-- ------------------------------------------------------
-- Server version   5.1.49-2

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `ucscsis`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `ucscsis` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `ucscsis`;

--
-- Table structure for table `bcsc_course`
--

DROP TABLE IF EXISTS `bcsc_course`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bcsc_course` (
  `RId` int(11) NOT NULL DEFAULT '0',
  `CourseId` varchar(10) DEFAULT NULL,
  `SYear` char(2) DEFAULT NULL,
  `Semester` char(2) DEFAULT NULL,
  `CourseName` varchar(60) DEFAULT NULL,
  `Prerequisite` varchar(50) DEFAULT NULL,
  `Credits_L` smallint(6) unsigned DEFAULT NULL,
  `Credits_P` smallint(6) unsigned DEFAULT '0',
  `MaxStudents` smallint(6) unsigned DEFAULT '0',
  `Compulsory` char(1) DEFAULT NULL,
  `AltCourseId` varchar(10) DEFAULT NULL,
  `OfferedBy` varchar(10) DEFAULT NULL,
  `GPACon` char(1) NOT NULL DEFAULT '',
  `Programs` varchar(15) NOT NULL DEFAULT '',
  `Tag` char(1) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bcsc_exam`
--

DROP TABLE IF EXISTS `bcsc_exam`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bcsc_exam` (
  `RId` int(11) NOT NULL DEFAULT '0',
  `ExamId` char(6) DEFAULT NULL,
  `CourseId` varchar(10) DEFAULT NULL,
  `Semester` char(2) DEFAULT NULL,
  `Date` date DEFAULT NULL,
  `Time` varchar(15) DEFAULT NULL,
  `Venue` varchar(25) DEFAULT NULL,
  `Paper_setting` date DEFAULT NULL,
  `Paper_moderation` date DEFAULT NULL,
  `Paper_marking` date DEFAULT NULL,
  `Tag` char(1) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bcsc_examiner`
--

DROP TABLE IF EXISTS `bcsc_examiner`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bcsc_examiner` (
  `RId` int(11) NOT NULL DEFAULT '0',
  `Id` char(10) NOT NULL DEFAULT '',
  `Examiner1` char(60) DEFAULT NULL,
  `Examiner2` char(60) DEFAULT NULL,
  `Tag` char(1) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bcsc_gpa`
--

DROP TABLE IF EXISTS `bcsc_gpa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bcsc_gpa` (
  `RId` int(11) NOT NULL DEFAULT '0',
  `IndexNo` char(8) NOT NULL DEFAULT '',
  `Tag` char(1) NOT NULL DEFAULT '',
  `GPV1` double DEFAULT NULL,
  `Credits1` int(3) NOT NULL DEFAULT '0',
  `GPA1` double NOT NULL DEFAULT '0',
  `GPV2` double DEFAULT NULL,
  `Credits2` int(3) NOT NULL DEFAULT '0',
  `GPA2` double NOT NULL DEFAULT '0',
  `GPV3` double DEFAULT NULL,
  `Credits3` int(3) NOT NULL DEFAULT '0',
  `GPA3` double NOT NULL DEFAULT '0',
  `GPV4` double DEFAULT NULL,
  `Credits4` int(3) NOT NULL DEFAULT '0',
  `GPA4` double NOT NULL DEFAULT '0',
  `GPVT` double DEFAULT NULL,
  `GPAT` float(5,2) DEFAULT NULL,
  `CreditsT` int(4) NOT NULL DEFAULT '0',
  `Comments` char(50) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bcsc_log`
--

DROP TABLE IF EXISTS `bcsc_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bcsc_log` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `time` bigint(10) unsigned NOT NULL DEFAULT '0',
  `userid` bigint(10) unsigned NOT NULL DEFAULT '0',
  `ip` varchar(15) NOT NULL DEFAULT '',
  `course` bigint(10) unsigned NOT NULL DEFAULT '0',
  `module` varchar(20) NOT NULL DEFAULT '',
  `cmid` bigint(10) unsigned NOT NULL DEFAULT '0',
  `action` varchar(40) NOT NULL DEFAULT '',
  `url` varchar(100) NOT NULL DEFAULT '',
  `info` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `log_coumodact_ix` (`course`,`module`,`action`),
  KEY `log_tim_ix` (`time`),
  KEY `log_act_ix` (`action`),
  KEY `log_usecou_ix` (`userid`,`course`),
  KEY `log_cmi_ix` (`cmid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Every action is logged as far as possible';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bcsc_marks`
--

DROP TABLE IF EXISTS `bcsc_marks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bcsc_marks` (
  `RId` int(11) NOT NULL DEFAULT '0',
  `ExamId` char(6) DEFAULT NULL,
  `IndexNo` char(8) NOT NULL DEFAULT '',
  `CourseId` char(10) NOT NULL DEFAULT '',
  `Marks1` int(3) unsigned NOT NULL DEFAULT '0',
  `Marks2` int(3) unsigned NOT NULL DEFAULT '0',
  `Marks3` int(3) NOT NULL DEFAULT '0',
  `Adjustment` int(11) NOT NULL DEFAULT '0',
  `Result1` char(2) DEFAULT NULL COMMENT 'Grades',
  `Result2` char(2) DEFAULT NULL COMMENT 'Grades',
  `Final` char(2) DEFAULT NULL COMMENT 'Grades',
  `CanRel` char(1) DEFAULT NULL,
  `Tag` char(1) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bcsc_student`
--

DROP TABLE IF EXISTS `bcsc_student`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bcsc_student` (
  `RId` int(11) NOT NULL DEFAULT '0',
  `IndexNo` varchar(8) DEFAULT NULL,
  `RegNo` varchar(20) DEFAULT NULL,
  `Batch` varchar(12) DEFAULT NULL COMMENT 'Batch',
  `Name` varchar(50) DEFAULT NULL,
  `Initials` varchar(20) DEFAULT NULL,
  `fullname` char(100) DEFAULT NULL,
  `sex` char(1) DEFAULT NULL,
  `Title` varchar(8) DEFAULT NULL COMMENT 'Title',
  `NID` varchar(15) DEFAULT NULL,
  `CYear` char(1) DEFAULT NULL,
  `DGPA` float(5,2) DEFAULT NULL,
  `CGPA` float(5,2) DEFAULT NULL,
  `FinalRes` char(3) DEFAULT NULL,
  `Duration` char(1) DEFAULT NULL,
  `dreg` date DEFAULT '2000-01-01',
  `dgrad` date DEFAULT '2000-01-01',
  `dob` date DEFAULT '2000-01-01',
  `Status` varchar(45) DEFAULT NULL,
  `Tag` char(1) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bict_course`
--

DROP TABLE IF EXISTS `bict_course`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bict_course` (
  `RId` int(11) NOT NULL DEFAULT '0',
  `CourseId` varchar(10) DEFAULT NULL,
  `SYear` char(2) DEFAULT NULL,
  `Semester` char(2) DEFAULT NULL,
  `CourseName` varchar(60) DEFAULT NULL,
  `Prerequisite` varchar(50) DEFAULT NULL,
  `Credits_L` smallint(6) unsigned DEFAULT NULL,
  `Credits_P` smallint(6) unsigned DEFAULT '0',
  `MaxStudents` smallint(6) unsigned DEFAULT '0',
  `Compulsory` char(1) DEFAULT NULL,
  `AltCourseId` varchar(10) DEFAULT NULL,
  `OfferedBy` varchar(10) DEFAULT NULL,
  `GPACon` char(1) NOT NULL DEFAULT '',
  `Programs` varchar(15) NOT NULL DEFAULT '',
  `Tag` char(1) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bict_exam`
--

DROP TABLE IF EXISTS `bict_exam`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bict_exam` (
  `RId` int(11) NOT NULL DEFAULT '0',
  `ExamId` char(6) DEFAULT NULL,
  `CourseId` varchar(10) DEFAULT NULL,
  `Semester` char(2) DEFAULT NULL,
  `Date` date DEFAULT NULL,
  `Time` varchar(15) DEFAULT NULL,
  `Venue` varchar(25) DEFAULT NULL,
  `Paper_setting` date DEFAULT NULL,
  `Paper_moderation` date DEFAULT NULL,
  `Paper_marking` date DEFAULT NULL,
  `Tag` char(1) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bict_examiner`
--

DROP TABLE IF EXISTS `bict_examiner`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bict_examiner` (
  `RId` int(11) NOT NULL DEFAULT '0',
  `Id` char(10) NOT NULL DEFAULT '',
  `Examiner1` char(60) DEFAULT NULL,
  `Examiner2` char(60) DEFAULT NULL,
  `Tag` char(1) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bict_gpa`
--

DROP TABLE IF EXISTS `bict_gpa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bict_gpa` (
  `RId` int(11) NOT NULL DEFAULT '0',
  `IndexNo` char(8) NOT NULL DEFAULT '',
  `Tag` char(1) NOT NULL DEFAULT '',
  `GPV1` double DEFAULT NULL,
  `Credits1` int(3) NOT NULL DEFAULT '0',
  `GPA1` double NOT NULL DEFAULT '0',
  `GPV2` double DEFAULT NULL,
  `Credits2` int(3) NOT NULL DEFAULT '0',
  `GPA2` double NOT NULL DEFAULT '0',
  `GPV3` double DEFAULT NULL,
  `Credits3` int(3) NOT NULL DEFAULT '0',
  `GPA3` double NOT NULL DEFAULT '0',
  `GPV4` double DEFAULT NULL,
  `Credits4` int(3) NOT NULL DEFAULT '0',
  `GPA4` double NOT NULL DEFAULT '0',
  `GPVT` double DEFAULT NULL,
  `GPAT` float(5,2) DEFAULT NULL,
  `CreditsT` int(4) NOT NULL DEFAULT '0',
  `Comments` char(50) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bict_log`
--

DROP TABLE IF EXISTS `bict_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bict_log` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `time` bigint(10) unsigned NOT NULL DEFAULT '0',
  `userid` bigint(10) unsigned NOT NULL DEFAULT '0',
  `ip` varchar(15) NOT NULL DEFAULT '',
  `course` bigint(10) unsigned NOT NULL DEFAULT '0',
  `module` varchar(20) NOT NULL DEFAULT '',
  `cmid` bigint(10) unsigned NOT NULL DEFAULT '0',
  `action` varchar(40) NOT NULL DEFAULT '',
  `url` varchar(100) NOT NULL DEFAULT '',
  `info` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `log_coumodact_ix` (`course`,`module`,`action`),
  KEY `log_tim_ix` (`time`),
  KEY `log_act_ix` (`action`),
  KEY `log_usecou_ix` (`userid`,`course`),
  KEY `log_cmi_ix` (`cmid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Every action is logged as far as possible';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bict_marks`
--

DROP TABLE IF EXISTS `bict_marks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bict_marks` (
  `RId` int(11) NOT NULL DEFAULT '0',
  `ExamId` char(6) DEFAULT NULL,
  `IndexNo` char(8) NOT NULL DEFAULT '',
  `CourseId` char(10) NOT NULL DEFAULT '',
  `Marks1` int(3) unsigned NOT NULL DEFAULT '0',
  `Marks2` int(3) unsigned NOT NULL DEFAULT '0',
  `Marks3` int(3) NOT NULL DEFAULT '0',
  `Adjustment` int(11) NOT NULL DEFAULT '0',
  `Result1` char(2) DEFAULT NULL COMMENT 'Grades',
  `Result2` char(2) DEFAULT NULL COMMENT 'Grades',
  `Final` char(2) DEFAULT NULL COMMENT 'Grades',
  `CanRel` char(1) DEFAULT NULL,
  `Tag` char(1) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bict_student`
--

DROP TABLE IF EXISTS `bict_student`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bict_student` (
  `RId` int(11) NOT NULL DEFAULT '0',
  `IndexNo` varchar(8) DEFAULT NULL,
  `RegNo` varchar(20) DEFAULT NULL,
  `Batch` varchar(12) DEFAULT NULL COMMENT 'Batch',
  `Name` varchar(50) DEFAULT NULL,
  `Initials` varchar(20) DEFAULT NULL,
  `fullname` char(100) DEFAULT NULL,
  `sex` char(1) DEFAULT NULL,
  `Title` varchar(8) DEFAULT NULL COMMENT 'Title',
  `NID` varchar(15) DEFAULT NULL,
  `CYear` char(1) DEFAULT NULL,
  `DGPA` float(5,2) DEFAULT NULL,
  `CGPA` float(5,2) DEFAULT NULL,
  `FinalRes` char(3) DEFAULT NULL,
  `dreg` date DEFAULT '2000-01-01',
  `dgrad` date DEFAULT '2000-01-01',
  `dob` date DEFAULT '2000-01-01',
  `Status` varchar(45) DEFAULT NULL,
  `Tag` char(1) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bit_course`
--

DROP TABLE IF EXISTS `bit_course`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bit_course` (
  `RId` int(11) NOT NULL DEFAULT '0',
  `CourseId` varchar(10) DEFAULT NULL,
  `SYear` char(2) DEFAULT NULL,
  `Semester` char(2) DEFAULT NULL,
  `CourseName` varchar(60) DEFAULT NULL,
  `Prerequisite` varchar(50) DEFAULT NULL,
  `Credits_L` smallint(6) unsigned DEFAULT NULL,
  `Credits_P` smallint(6) unsigned DEFAULT '0',
  `MaxStudents` smallint(6) unsigned DEFAULT '0',
  `Compulsory` char(1) DEFAULT NULL,
  `AltCourseId` varchar(10) DEFAULT NULL,
  `OfferedBy` varchar(10) DEFAULT NULL,
  `GPACon` char(1) NOT NULL DEFAULT '',
  `Programs` varchar(15) NOT NULL DEFAULT '',
  `Tag` char(1) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bit_log`
--

DROP TABLE IF EXISTS `bit_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bit_log` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `time` bigint(10) unsigned NOT NULL DEFAULT '0',
  `userid` bigint(10) unsigned NOT NULL DEFAULT '0',
  `ip` varchar(15) NOT NULL DEFAULT '',
  `course` bigint(10) unsigned NOT NULL DEFAULT '0',
  `module` varchar(20) NOT NULL DEFAULT '',
  `cmid` bigint(10) unsigned NOT NULL DEFAULT '0',
  `action` varchar(40) NOT NULL DEFAULT '',
  `url` varchar(100) NOT NULL DEFAULT '',
  `info` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `log_coumodact_ix` (`course`,`module`,`action`),
  KEY `log_tim_ix` (`time`),
  KEY `log_act_ix` (`action`),
  KEY `log_usecou_ix` (`userid`,`course`),
  KEY `log_cmi_ix` (`cmid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Every action is logged as far as possible';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `program`
--

DROP TABLE IF EXISTS `program`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `program` (
  `id` int(3) unsigned NOT NULL AUTO_INCREMENT,
  `short_name` varchar(20) NOT NULL,
  `full_name` varchar(300) NOT NULL,
  `degree` varchar(500) NOT NULL,
  `class` varchar(500) NOT NULL,
  `grade` varchar(500) NOT NULL,
  `gpv` varchar(500) NOT NULL,
  `table_prefix` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COMMENT='programs such as BCSc BICT MIS';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `staff_id` decimal(10,0) NOT NULL,
  `syear` decimal(4,0) DEFAULT NULL,
  `current_school_id` decimal(10,0) DEFAULT NULL,
  `title` varchar(5) DEFAULT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `phone` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `profile` varchar(30) DEFAULT NULL,
  `homeroom` varchar(5) DEFAULT NULL,
  `schools` varchar(255) DEFAULT NULL,
  `last_login` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `failed_login` decimal(10,0) DEFAULT NULL,
  `profile_id` decimal(10,0) DEFAULT NULL,
  `rollover_id` decimal(10,0) DEFAULT NULL,
  PRIMARY KEY (`staff_id`),
  UNIQUE KEY `staff_ind4` (`username`,`syear`) USING BTREE,
  KEY `staff_ind1` (`staff_id`,`syear`) USING BTREE,
  KEY `staff_ind2` (`last_name`,`first_name`) USING BTREE,
  KEY `staff_ind3` (`schools`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2010-11-25 10:27:43
