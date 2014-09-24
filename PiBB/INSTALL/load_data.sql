-- MySQL dump 10.13  Distrib 5.5.18, for osx10.6 (i386)
--
-- Host: localhost    Database: cssauhco_xcbbs
-- ------------------------------------------------------
-- Server version	5.5.18

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
-- Table structure for table `BBS_BoardGroups`
--

-- DROP TABLE IF EXISTS `BBS_BoardGroups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `BBS_BoardGroups` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `weight` int(11) DEFAULT '1',
  `name` varchar(50) NOT NULL,
  `description` varchar(200) DEFAULT NULL,
  `description_en` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=91 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `BBS_BoardGroups`
--

LOCK TABLES `BBS_BoardGroups` WRITE;
/*!40000 ALTER TABLE `BBS_BoardGroups` DISABLE KEYS */;
INSERT INTO `BBS_BoardGroups` VALUES (10,1,'IT','&#20449;&#24687;&#25216;&#26415;','IT Technology'),(20,1,'General','&#22823;&#21315;&#19990;&#30028;','The World'),(30,1,'Fun','&#36259;&#21619;&#19987;&#26639;','Fun Topics'),(90,1,'Admin','&#35770;&#22363;&#31649;&#29702;','Site Administration');
/*!40000 ALTER TABLE `BBS_BoardGroups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `BBS_BoardList`
--

-- DROP TABLE IF EXISTS `BBS_BoardList`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `BBS_BoardList` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `GroupID` int(11) NOT NULL,
  `weight` int(11) DEFAULT '1',
  `name` varchar(50) NOT NULL,
  `title_en` varchar(50) NOT NULL,
  `title` varchar(50) NOT NULL,
  `description` varchar(512) DEFAULT NULL,
  `managers` varchar(512) DEFAULT NULL,
  `thread_count` int(11) DEFAULT '0',
  `thread_count_admin` int(11) DEFAULT '0',
  `post_count` int(11) DEFAULT '0',
  `post_count_admin` int(11) DEFAULT '0',
  `readonly` varchar(1) NOT NULL DEFAULT '0',
  `private` varchar(1) NOT NULL DEFAULT '0',
  `hidden` varchar(1) NOT NULL DEFAULT '0',
  `disabled` varchar(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `BBS_BoardList`
--

LOCK TABLES `BBS_BoardList` WRITE;
/*!40000 ALTER TABLE `BBS_BoardList` DISABLE KEYS */;
INSERT INTO `BBS_BoardList` VALUES (10,90,1,'BBS_F_Admin','Site Admin','&#31449;&#21153;&#31649;&#29702;','Forum Administration','1,admin,1',0,0,0,0,'0','0','0','0'),(30,10,1,'BBS_F_CS','Computer Science','&#30005;&#33041;&#31185;&#23398;','Computer science',NULL,0,0,0,0,'0','0','0','0'),(40,10,1,'BBS_F_Programming','Programming','&#32534;&#31243;&#19990;&#30028;','Programming world',NULL,0,0,0,0,'0','0','0','0'),(50,20,1,'BBS_F_News','News','&#26032;&#38395;&#21160;&#24577;','News',NULL,0,0,0,0,'0','0','0','0'),(70,30,1,'BBS_F_Fun','Fun','&#36731;&#26494;&#29255;&#21051;','Just for fun',NULL,0,0,0,0,'0','0','0','0');
/*!40000 ALTER TABLE `BBS_BoardList` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `BBS_BoardManager`
--

-- DROP TABLE IF EXISTS `BBS_BoardManager`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `BBS_BoardManager` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `forum_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_name` varchar(50) NOT NULL,
  `role` int(11) DEFAULT '0',
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `disabled` varchar(1) DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `BBS_BoardManager`
--

LOCK TABLES `BBS_BoardManager` WRITE;
/*!40000 ALTER TABLE `BBS_BoardManager` DISABLE KEYS */;
INSERT INTO `BBS_BoardManager` VALUES (1,90,1,'admin',1,'2014-09-23 21:07:27',NULL,'0');
/*!40000 ALTER TABLE `BBS_BoardManager` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `BBS_F_Admin`
--

-- DROP TABLE IF EXISTS `BBS_F_Admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `BBS_F_Admin` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `thread_id` int(11) NOT NULL DEFAULT '0',
  `reply_to_id` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL,
  `user_name` varchar(50) NOT NULL,
  `title` varchar(256) NOT NULL,
  `body` mediumtext NOT NULL,
  `keywords` varchar(256) DEFAULT NULL,
  `submit_timestamp` datetime NOT NULL,
  `submit_ip` varchar(20) DEFAULT NULL,
  `last_edit_user_id` int(11) DEFAULT '0',
  `last_edit_user_name` varchar(50) DEFAULT NULL,
  `last_edit_timestamp` datetime DEFAULT NULL,
  `last_reply_user_id` int(11) DEFAULT '0',
  `last_reply_user_name` varchar(50) DEFAULT NULL,
  `last_reply_timestamp` datetime DEFAULT NULL,
  `reply_count` int(11) DEFAULT '0',
  `click_count` int(11) DEFAULT '0',
  `like_count` int(11) DEFAULT '0',
  `score` float DEFAULT '0',
  `static_url` varchar(100) DEFAULT NULL,
  `indexed` varchar(1) DEFAULT '0',
  `attachment` varchar(256) DEFAULT NULL,
  `salt` varchar(20) DEFAULT NULL,
  `readonly` varchar(1) DEFAULT '0',
  `hidden` varchar(1) DEFAULT '0',
  `marked` varchar(1) DEFAULT '0',
  `digested` varchar(1) DEFAULT '0',
  `top` varchar(1) DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `BBS_F_Admin`
--

LOCK TABLES `BBS_F_Admin` WRITE;
/*!40000 ALTER TABLE `BBS_F_Admin` DISABLE KEYS */;
/*!40000 ALTER TABLE `BBS_F_Admin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `BBS_F_CS`
--

-- DROP TABLE IF EXISTS `BBS_F_CS`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `BBS_F_CS` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `thread_id` int(11) NOT NULL DEFAULT '0',
  `reply_to_id` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL,
  `user_name` varchar(50) NOT NULL,
  `title` varchar(256) NOT NULL,
  `body` mediumtext NOT NULL,
  `keywords` varchar(256) DEFAULT NULL,
  `submit_timestamp` datetime NOT NULL,
  `submit_ip` varchar(20) DEFAULT NULL,
  `last_edit_user_id` int(11) DEFAULT '0',
  `last_edit_user_name` varchar(50) DEFAULT NULL,
  `last_edit_timestamp` datetime DEFAULT NULL,
  `last_reply_user_id` int(11) DEFAULT '0',
  `last_reply_user_name` varchar(50) DEFAULT NULL,
  `last_reply_timestamp` datetime DEFAULT NULL,
  `reply_count` int(11) DEFAULT '0',
  `click_count` int(11) DEFAULT '0',
  `like_count` int(11) DEFAULT '0',
  `score` float DEFAULT '0',
  `static_url` varchar(100) DEFAULT NULL,
  `indexed` varchar(1) DEFAULT '0',
  `attachment` varchar(256) DEFAULT NULL,
  `salt` varchar(20) DEFAULT NULL,
  `readonly` varchar(1) DEFAULT '0',
  `hidden` varchar(1) DEFAULT '0',
  `marked` varchar(1) DEFAULT '0',
  `digested` varchar(1) DEFAULT '0',
  `top` varchar(1) DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `BBS_F_CS`
--

LOCK TABLES `BBS_F_CS` WRITE;
/*!40000 ALTER TABLE `BBS_F_CS` DISABLE KEYS */;
/*!40000 ALTER TABLE `BBS_F_CS` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `BBS_F_Fun`
--

-- DROP TABLE IF EXISTS `BBS_F_Fun`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `BBS_F_Fun` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `thread_id` int(11) NOT NULL DEFAULT '0',
  `reply_to_id` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL,
  `user_name` varchar(50) NOT NULL,
  `title` varchar(256) NOT NULL,
  `body` mediumtext NOT NULL,
  `keywords` varchar(256) DEFAULT NULL,
  `submit_timestamp` datetime NOT NULL,
  `submit_ip` varchar(20) DEFAULT NULL,
  `last_edit_user_id` int(11) DEFAULT '0',
  `last_edit_user_name` varchar(50) DEFAULT NULL,
  `last_edit_timestamp` datetime DEFAULT NULL,
  `last_reply_user_id` int(11) DEFAULT '0',
  `last_reply_user_name` varchar(50) DEFAULT NULL,
  `last_reply_timestamp` datetime DEFAULT NULL,
  `reply_count` int(11) DEFAULT '0',
  `click_count` int(11) DEFAULT '0',
  `like_count` int(11) DEFAULT '0',
  `score` float DEFAULT '0',
  `static_url` varchar(100) DEFAULT NULL,
  `indexed` varchar(1) DEFAULT '0',
  `attachment` varchar(256) DEFAULT NULL,
  `salt` varchar(20) DEFAULT NULL,
  `readonly` varchar(1) DEFAULT '0',
  `hidden` varchar(1) DEFAULT '0',
  `marked` varchar(1) DEFAULT '0',
  `digested` varchar(1) DEFAULT '0',
  `top` varchar(1) DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `BBS_F_Fun`
--

LOCK TABLES `BBS_F_Fun` WRITE;
/*!40000 ALTER TABLE `BBS_F_Fun` DISABLE KEYS */;
/*!40000 ALTER TABLE `BBS_F_Fun` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `BBS_F_News`
--

-- DROP TABLE IF EXISTS `BBS_F_News`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `BBS_F_News` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `thread_id` int(11) NOT NULL DEFAULT '0',
  `reply_to_id` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL,
  `user_name` varchar(50) NOT NULL,
  `title` varchar(256) NOT NULL,
  `body` mediumtext NOT NULL,
  `keywords` varchar(256) DEFAULT NULL,
  `submit_timestamp` datetime NOT NULL,
  `submit_ip` varchar(20) DEFAULT NULL,
  `last_edit_user_id` int(11) DEFAULT '0',
  `last_edit_user_name` varchar(50) DEFAULT NULL,
  `last_edit_timestamp` datetime DEFAULT NULL,
  `last_reply_user_id` int(11) DEFAULT '0',
  `last_reply_user_name` varchar(50) DEFAULT NULL,
  `last_reply_timestamp` datetime DEFAULT NULL,
  `reply_count` int(11) DEFAULT '0',
  `click_count` int(11) DEFAULT '0',
  `like_count` int(11) DEFAULT '0',
  `score` float DEFAULT '0',
  `static_url` varchar(100) DEFAULT NULL,
  `indexed` varchar(1) DEFAULT '0',
  `attachment` varchar(256) DEFAULT NULL,
  `salt` varchar(20) DEFAULT NULL,
  `readonly` varchar(1) DEFAULT '0',
  `hidden` varchar(1) DEFAULT '0',
  `marked` varchar(1) DEFAULT '0',
  `digested` varchar(1) DEFAULT '0',
  `top` varchar(1) DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `BBS_F_News`
--

LOCK TABLES `BBS_F_News` WRITE;
/*!40000 ALTER TABLE `BBS_F_News` DISABLE KEYS */;
/*!40000 ALTER TABLE `BBS_F_News` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `BBS_F_Programming`
--

-- DROP TABLE IF EXISTS `BBS_F_Programming`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `BBS_F_Programming` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `thread_id` int(11) NOT NULL DEFAULT '0',
  `reply_to_id` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL,
  `user_name` varchar(50) NOT NULL,
  `title` varchar(256) NOT NULL,
  `body` mediumtext NOT NULL,
  `keywords` varchar(256) DEFAULT NULL,
  `submit_timestamp` datetime NOT NULL,
  `submit_ip` varchar(20) DEFAULT NULL,
  `last_edit_user_id` int(11) DEFAULT '0',
  `last_edit_user_name` varchar(50) DEFAULT NULL,
  `last_edit_timestamp` datetime DEFAULT NULL,
  `last_reply_user_id` int(11) DEFAULT '0',
  `last_reply_user_name` varchar(50) DEFAULT NULL,
  `last_reply_timestamp` datetime DEFAULT NULL,
  `reply_count` int(11) DEFAULT '0',
  `click_count` int(11) DEFAULT '0',
  `like_count` int(11) DEFAULT '0',
  `score` float DEFAULT '0',
  `static_url` varchar(100) DEFAULT NULL,
  `indexed` varchar(1) DEFAULT '0',
  `attachment` varchar(256) DEFAULT NULL,
  `salt` varchar(20) DEFAULT NULL,
  `readonly` varchar(1) DEFAULT '0',
  `hidden` varchar(1) DEFAULT '0',
  `marked` varchar(1) DEFAULT '0',
  `digested` varchar(1) DEFAULT '0',
  `top` varchar(1) DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `BBS_F_Programming`
--

LOCK TABLES `BBS_F_Programming` WRITE;
/*!40000 ALTER TABLE `BBS_F_Programming` DISABLE KEYS */;
/*!40000 ALTER TABLE `BBS_F_Programming` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `BBS_Keywords`
--

-- DROP TABLE IF EXISTS `BBS_Keywords`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `BBS_Keywords` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `word` varchar(50) NOT NULL,
  `forum_id` int(11) NOT NULL,
  `forum_ct` int(11) DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `BBS_Keywords`
--

LOCK TABLES `BBS_Keywords` WRITE;
/*!40000 ALTER TABLE `BBS_Keywords` DISABLE KEYS */;
/*!40000 ALTER TABLE `BBS_Keywords` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `BBS_PrivateMembership`
--

-- DROP TABLE IF EXISTS `BBS_PrivateMembership`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `BBS_PrivateMembership` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `forum_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `uc_BBS_PrivateMembership` (`forum_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `BBS_PrivateMembership`
--

LOCK TABLES `BBS_PrivateMembership` WRITE;
/*!40000 ALTER TABLE `BBS_PrivateMembership` DISABLE KEYS */;
/*!40000 ALTER TABLE `BBS_PrivateMembership` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `IMail`
--

-- DROP TABLE IF EXISTS `IMail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `IMail` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(256) NOT NULL,
  `body` mediumtext NOT NULL,
  `attachment` varchar(256) DEFAULT NULL,
  `salt` varchar(20) DEFAULT NULL,
  `fk_sender_id` int(11) NOT NULL,
  `sender` varchar(50) NOT NULL,
  `send_state` varchar(20) NOT NULL,
  `send_time` datetime NOT NULL,
  `send_ip` varchar(20) DEFAULT NULL,
  `recv_id_list` varchar(10000) DEFAULT NULL,
  `recv_list` varchar(10000) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `IMail`
--

LOCK TABLES `IMail` WRITE;
/*!40000 ALTER TABLE `IMail` DISABLE KEYS */;
/*!40000 ALTER TABLE `IMail` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `IMailRecv`
--

-- DROP TABLE IF EXISTS `IMailRecv`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `IMailRecv` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `fk_mail_id` int(11) NOT NULL,
  `fk_recver_id` int(11) NOT NULL,
  `recver` varchar(50) NOT NULL,
  `recv_state` varchar(20) NOT NULL,
  `recv_time` datetime NOT NULL,
  `read_time` datetime DEFAULT NULL,
  `read_ip` varchar(20) DEFAULT NULL,
  `replied` varchar(1) DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `IMailRecv`
--

LOCK TABLES `IMailRecv` WRITE;
/*!40000 ALTER TABLE `IMailRecv` DISABLE KEYS */;
/*!40000 ALTER TABLE `IMailRecv` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `IMailRecvNotify`
--

-- DROP TABLE IF EXISTS `IMailRecvNotify`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `IMailRecvNotify` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `fk_imailrecv_id` int(11) NOT NULL,
  `notify_time` datetime NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `IMailRecvNotify`
--

LOCK TABLES `IMailRecvNotify` WRITE;
/*!40000 ALTER TABLE `IMailRecvNotify` DISABLE KEYS */;
/*!40000 ALTER TABLE `IMailRecvNotify` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `IMailState`
--

-- DROP TABLE IF EXISTS `IMailState`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `IMailState` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `state` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `IMailState`
--

LOCK TABLES `IMailState` WRITE;
/*!40000 ALTER TABLE `IMailState` DISABLE KEYS */;
INSERT INTO `IMailState` VALUES (1,'draft'),(2,'sent'),(6,'new'),(7,'read'),(11,'del_draft'),(12,'del_sent'),(16,'del_new'),(17,'del_read'),(21,'perm_del_draft'),(22,'perm_del_sent'),(26,'perm_del_new'),(27,'perm_del_read');
/*!40000 ALTER TABLE `IMailState` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Schema_TblCol`
--

-- DROP TABLE IF EXISTS `Schema_TblCol`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Schema_TblCol` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `TableName` varchar(100) NOT NULL,
  `Title` varchar(100) DEFAULT NULL,
  `Field` varchar(100) NOT NULL,
  `Type` varchar(100) NOT NULL,
  `Null` varchar(100) DEFAULT NULL,
  `Key` varchar(100) DEFAULT NULL,
  `Default` varchar(100) DEFAULT NULL,
  `Extra` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `TableName` (`TableName`,`Field`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Schema_TblCol`
--

LOCK TABLES `Schema_TblCol` WRITE;
/*!40000 ALTER TABLE `Schema_TblCol` DISABLE KEYS */;
/*!40000 ALTER TABLE `Schema_TblCol` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `User`
--

-- DROP TABLE IF EXISTS `User`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `User` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `login` varchar(50) NOT NULL,
  `passwd` varchar(32) NOT NULL,
  `note` varchar(50) DEFAULT '',
  `gid` int(11) NOT NULL DEFAULT '1',
  `reg_date` datetime NOT NULL,
  `last_login` datetime NOT NULL,
  `last_ip` varchar(20) NOT NULL,
  `approved` varchar(1) DEFAULT '1',
  `approve_date` datetime DEFAULT NULL,
  `enabled` varchar(1) DEFAULT '1',
  `activated` varchar(1) DEFAULT '1',
  `activation_code` varchar(50) DEFAULT NULL,
  `activation_date` datetime DEFAULT NULL,
  `bbs_score` int(11) DEFAULT '0',
  `bbs_new_count` int(11) DEFAULT '0',
  `bbs_reply_count` int(11) DEFAULT '0',
  `bbs_mark_count` int(11) DEFAULT '0',
  `bbs_digest_count` int(11) DEFAULT '0',
  `money` int(11) DEFAULT '0',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `login` (`login`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `User`
--

LOCK TABLES `User` WRITE;
/*!40000 ALTER TABLE `User` DISABLE KEYS */;
INSERT INTO `User` VALUES (1,'Demo','Admin','txchen@gmail.com','admin','5f4dcc3b5aa765d61d8327deb882cf99','',0,'2014-09-23 21:59:07','2014-09-23 21:59:07','127.0.0.1','1',NULL,'1','1',NULL,NULL,0,0,0,0,0,0);
/*!40000 ALTER TABLE `User` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `UserGroup`
--

-- DROP TABLE IF EXISTS `UserGroup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `UserGroup` (
  `ID` int(11) NOT NULL,
  `name` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `UserGroup`
--

LOCK TABLES `UserGroup` WRITE;
/*!40000 ALTER TABLE `UserGroup` DISABLE KEYS */;
INSERT INTO `UserGroup` VALUES (0,'admin');
/*!40000 ALTER TABLE `UserGroup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `User_LinkedIn`
--

-- DROP TABLE IF EXISTS `User_LinkedIn`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `User_LinkedIn` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `linkedin_id` varchar(50) NOT NULL,
  `fk_user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `linkedin_id` (`linkedin_id`),
  UNIQUE KEY `fk_user_id` (`fk_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `User_LinkedIn`
--

LOCK TABLES `User_LinkedIn` WRITE;
/*!40000 ALTER TABLE `User_LinkedIn` DISABLE KEYS */;
/*!40000 ALTER TABLE `User_LinkedIn` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `code_register`
--

-- DROP TABLE IF EXISTS `code_register`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `code_register` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL,
  `owner_user_id` int(11) NOT NULL,
  `create_date` datetime NOT NULL,
  `is_used` varchar(1) NOT NULL DEFAULT '0',
  `use_user_id` int(11) DEFAULT NULL,
  `use_date` datetime DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `code_register`
--

LOCK TABLES `code_register` WRITE;
/*!40000 ALTER TABLE `code_register` DISABLE KEYS */;
/*!40000 ALTER TABLE `code_register` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `log_site`
--

-- DROP TABLE IF EXISTS `log_site`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `log_site` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `action` varchar(20) NOT NULL,
  `ip` varchar(20) DEFAULT '',
  `note` varchar(20) DEFAULT '',
  `timestamp` datetime NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `log_site`
--

LOCK TABLES `log_site` WRITE;
/*!40000 ALTER TABLE `log_site` DISABLE KEYS */;
/*!40000 ALTER TABLE `log_site` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `v_log_site`
--

-- DROP TABLE IF EXISTS `v_log_site`;
/*!50001 DROP VIEW IF EXISTS `v_log_site`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `v_log_site` (
  `ID` int(11),
  `user_id` int(11),
  `action` varchar(20),
  `ip` varchar(20),
  `note` varchar(20),
  `timestamp` datetime,
  `login` varchar(50),
  `Name` varchar(101)
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Final view structure for view `v_log_site`
--

/*!50001 DROP TABLE IF EXISTS `v_log_site`*/;
/*!50001 DROP VIEW IF EXISTS `v_log_site`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_log_site` AS select `L`.`ID` AS `ID`,`L`.`user_id` AS `user_id`,`L`.`action` AS `action`,`L`.`ip` AS `ip`,`L`.`note` AS `note`,`L`.`timestamp` AS `timestamp`,`U`.`login` AS `login`,concat(concat(`U`.`first_name`,' '),`U`.`last_name`) AS `Name` from (`log_site` `L` join `user` `U`) where (`L`.`user_id` = `U`.`ID`) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-09-23 22:01:38
