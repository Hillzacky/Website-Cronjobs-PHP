-- MySQL dump 10.13  Distrib 8.0.23, for Linux (x86_64)
--
-- Host: localhost    Database: webcron
-- ------------------------------------------------------
-- Server version	8.0.23-0ubuntu0.20.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `webcron_category`
--

DROP TABLE IF EXISTS `webcron_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `webcron_category` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(60) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `modified_at` timestamp NULL DEFAULT NULL,
  `user_id` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_webcron_category_webcron_user1_idx` (`user_id`),
  CONSTRAINT `fk_webcron_category_webcron_user1` FOREIGN KEY (`user_id`) REFERENCES `webcron_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `webcron_category`
--

LOCK TABLES `webcron_category` WRITE;
/*!40000 ALTER TABLE `webcron_category` DISABLE KEYS */;
/*!40000 ALTER TABLE `webcron_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `webcron_log`
--

DROP TABLE IF EXISTS `webcron_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `webcron_log` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `curl_info` text,
  `response` longtext,
  `http_code` int DEFAULT NULL,
  `schedule_id` int unsigned NOT NULL,
  `user_id` int unsigned NOT NULL,
  `added_at` timestamp NULL DEFAULT NULL,
  `error_msg` text,
  `is_error` tinyint(1) DEFAULT NULL,
  `start_at` timestamp NULL DEFAULT NULL,
  `finish_at` timestamp NULL DEFAULT NULL,
  `schedule_time` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ix_added_at` (`added_at`),
  KEY `fk_webcron_log_webcron_schedule1_idx` (`schedule_id`),
  KEY `fk_webcron_log_webcron_user1_idx` (`user_id`),
  KEY `ix_schedule_error` (`schedule_id`,`is_error`),
  KEY `ix_user_schedule` (`schedule_id`,`user_id`),
  CONSTRAINT `fk_webcron_log_webcron_schedule1` FOREIGN KEY (`schedule_id`) REFERENCES `webcron_schedule` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_webcron_log_webcron_user1` FOREIGN KEY (`user_id`) REFERENCES `webcron_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `webcron_log`
--

LOCK TABLES `webcron_log` WRITE;
/*!40000 ALTER TABLE `webcron_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `webcron_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `webcron_profile`
--

DROP TABLE IF EXISTS `webcron_profile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `webcron_profile` (
  `name` varchar(45) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `avatar` varchar(120) DEFAULT NULL,
  `user_id` int unsigned NOT NULL,
  PRIMARY KEY (`user_id`),
  CONSTRAINT `fk_webcron_profile_webcron_user` FOREIGN KEY (`user_id`) REFERENCES `webcron_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `webcron_profile`
--

LOCK TABLES `webcron_profile` WRITE;
/*!40000 ALTER TABLE `webcron_profile` DISABLE KEYS */;
INSERT INTO `webcron_profile` VALUES ('User','admin@example.com',NULL,1);
/*!40000 ALTER TABLE `webcron_profile` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `webcron_schedule`
--

DROP TABLE IF EXISTS `webcron_schedule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `webcron_schedule` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `category_id` int unsigned DEFAULT NULL,
  `title` varchar(60) DEFAULT NULL,
  `url` text,
  `type` enum('expression','gui','alias') DEFAULT NULL,
  `command_type` enum('url','command') DEFAULT 'url',
  `expression` varchar(255) DEFAULT NULL,
  `total_executions` int unsigned DEFAULT '0',
  `max_executions` int unsigned DEFAULT '0',
  `status` enum('enabled','disabled') DEFAULT 'enabled',
  `notify` enum('fails','after','never') DEFAULT 'fails',
  `send_at_server` timestamp NULL DEFAULT NULL,
  `send_at_user` timestamp NULL DEFAULT NULL,
  `start_at_user` timestamp NULL DEFAULT NULL,
  `stop_at_user` timestamp NULL DEFAULT NULL,
  `timeout` int unsigned DEFAULT '400',
  `connection_timeout` int unsigned DEFAULT '20',
  `process_id` varchar(32) DEFAULT NULL,
  `success_if` text,
  `success_if_modificator` varchar(20) DEFAULT NULL,
  `fail_if` text,
  `fail_if_modificator` varchar(20) DEFAULT NULL,
  `http_auth_username` varchar(255) DEFAULT NULL,
  `http_auth_password` varchar(255) DEFAULT NULL,
  `headers` mediumtext,
  `cookie` mediumtext,
  `post` mediumtext,
  `description` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `modified_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `un_title` (`user_id`,`title`),
  KEY `ix_category_user` (`user_id`,`category_id`),
  KEY `fk_webcron_schedule_webcron_category1_idx` (`category_id`),
  KEY `ix_processid_sendatserver_status` (`process_id`,`send_at_server`,`status`),
  CONSTRAINT `fk_webcron_schedule_webcron_category1` FOREIGN KEY (`category_id`) REFERENCES `webcron_category` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_webcron_schedule_webcron_user1` FOREIGN KEY (`user_id`) REFERENCES `webcron_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `webcron_schedule`
--

LOCK TABLES `webcron_schedule` WRITE;
/*!40000 ALTER TABLE `webcron_schedule` DISABLE KEYS */;
/*!40000 ALTER TABLE `webcron_schedule` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `webcron_settings`
--

DROP TABLE IF EXISTS `webcron_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `webcron_settings` (
  `user_id` int unsigned NOT NULL,
  `timezone` varchar(60) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  CONSTRAINT `fk_webcron_settings_webcron_user1` FOREIGN KEY (`user_id`) REFERENCES `webcron_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `webcron_settings`
--

LOCK TABLES `webcron_settings` WRITE;
/*!40000 ALTER TABLE `webcron_settings` DISABLE KEYS */;
INSERT INTO `webcron_settings` VALUES (1,'Europe/Vilnius');
/*!40000 ALTER TABLE `webcron_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `webcron_stats`
--

DROP TABLE IF EXISTS `webcron_stats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `webcron_stats` (
  `insert_at` date NOT NULL,
  `failed` int unsigned DEFAULT '0',
  `success` int unsigned DEFAULT '0',
  `user_id` int unsigned NOT NULL,
  `schedule_id` int unsigned NOT NULL,
  PRIMARY KEY (`insert_at`,`user_id`,`schedule_id`),
  KEY `fk_webcron_stats_webcron_user1_idx` (`user_id`),
  KEY `fk_webcron_stats_webcron_schedule1_idx` (`schedule_id`),
  KEY `ix_user_schedule` (`user_id`,`schedule_id`),
  CONSTRAINT `fk_webcron_stats_webcron_schedule1` FOREIGN KEY (`schedule_id`) REFERENCES `webcron_schedule` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_webcron_stats_webcron_user1` FOREIGN KEY (`user_id`) REFERENCES `webcron_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `webcron_stats`
--

LOCK TABLES `webcron_stats` WRITE;
/*!40000 ALTER TABLE `webcron_stats` DISABLE KEYS */;
/*!40000 ALTER TABLE `webcron_stats` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `webcron_user`
--

DROP TABLE IF EXISTS `webcron_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `webcron_user` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `login` varchar(25) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `status` varchar(45) DEFAULT NULL,
  `ip` varchar(46) DEFAULT NULL,
  `last_login_ip` varchar(46) DEFAULT NULL,
  `role` varchar(45) DEFAULT NULL,
  `registered_at` timestamp NULL DEFAULT NULL,
  `modified_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `access_token` varchar(32) NOT NULL,
  `auth_key` varchar(32) NOT NULL,
  `lang_id` varchar(5) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `login_UNIQUE` (`login`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `webcron_user`
--

LOCK TABLES `webcron_user` WRITE;
/*!40000 ALTER TABLE `webcron_user` DISABLE KEYS */;
INSERT INTO `webcron_user` VALUES (1,'admin','$2y$13$2hZFlgXVDx7OYf9S9TuB4uXj2Jnvx87f6i.n1H/tcTvJfTNqVaxdq','1','127.0.0.1',NULL,'user','2021-05-10 19:31:36','2021-05-10 19:31:36',NULL,'54fdef3608f93e20e44606b23e734b42','rtGQL-pKzyO8sRcyilhotmXYB6X42kU-','en-US');
/*!40000 ALTER TABLE `webcron_user` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2021-05-13  8:44:22
