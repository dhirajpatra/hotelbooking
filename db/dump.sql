-- MySQL dump 10.13  Distrib 5.7.28, for Linux (x86_64)
--
-- Host: 192.168.224.2    Database: trivagodb
-- ------------------------------------------------------
-- Server version	5.6.46

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
-- Table structure for table `bookings`
--

DROP TABLE IF EXISTS `bookings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bookings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `listing_id` int(11) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `index2` (`listing_id`),
  CONSTRAINT `fk_bookings_1` FOREIGN KEY (`listing_id`) REFERENCES `listings` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bookings`
--

LOCK TABLES `bookings` WRITE;
/*!40000 ALTER TABLE `bookings` DISABLE KEYS */;
INSERT INTO `bookings` VALUES (1,1,'2020-01-13 05:08:30'),(2,2,'2020-01-13 05:08:35');
/*!40000 ALTER TABLE `bookings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category` varchar(45) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `index2` (`category`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'hotel','2020-01-13 04:47:35'),(2,'hostel','2020-01-13 04:47:35'),(3,'alternative','2020-01-13 04:47:35'),(4,'resort','2020-01-13 04:47:35'),(5,'guest-house','2020-01-13 04:47:35');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `listings`
--

DROP TABLE IF EXISTS `listings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `listings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `category_id` int(11) unsigned NOT NULL,
  `location_id` int(11) unsigned NOT NULL,
  `rating` tinyint(5) unsigned NOT NULL COMMENT '1 to 5',
  `reputation` int(10) unsigned NOT NULL,
  `price` decimal(10,2) unsigned NOT NULL,
  `image` varchar(45) NOT NULL,
  `availability` int(10) unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `index2` (`category_id`),
  KEY `index3` (`location_id`),
  KEY `index4` (`rating`),
  KEY `index5` (`reputation`),
  KEY `index6` (`availability`),
  CONSTRAINT `fk_listings_1` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_listings_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `listings`
--

LOCK TABLES `listings` WRITE;
/*!40000 ALTER TABLE `listings` DISABLE KEYS */;
INSERT INTO `listings` VALUES (1,'Super Hotel Guddyear',1,1,3,1000,92.00,'36400462.webp',9,'2020-01-13 05:05:39'),(2,'Good Hotel For all',1,2,4,836,56.00,'36400462.webp',19,'2020-01-13 05:06:12'),(4,'Super Hotel For few',1,4,5,836,140.00,'36400462.webp',5,'2020-01-13 05:26:32'),(5,'Markus Hotel',1,5,4,836,70.00,'36400462.webp',50,'2020-01-13 07:58:12'),(6,'Markus Hotel 2',1,6,4,836,70.00,'36400462.webp',50,'2020-01-13 08:56:37'),(7,'Trivago\' . ODnuD5d0iY . \' Hotel',1,7,4,836,70.00,'36400462.webp',50,'2020-01-13 09:03:20'),(8,'Trivago\' . cSRLWl4VEp . \' Hotel',1,8,4,836,70.00,'36400462.webp',50,'2020-01-13 09:20:10'),(9,'Trivago\' . HwQiEmdNpj . \' Hotel',1,9,4,836,70.00,'36400462.webp',50,'2020-01-13 09:21:51'),(10,'Trivago\' . 3GIbLZaDBr . \' Hotel',1,10,4,836,70.00,'36400462.webp',50,'2020-01-13 09:22:37');
/*!40000 ALTER TABLE `listings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `locations`
--

DROP TABLE IF EXISTS `locations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `locations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `city` varchar(45) NOT NULL,
  `state` varchar(45) NOT NULL,
  `country` varchar(45) NOT NULL,
  `zip` int(10) DEFAULT NULL,
  `address` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `index2` (`city`),
  KEY `index3` (`state`),
  KEY `index4` (`country`),
  KEY `index5` (`zip`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `locations`
--

LOCK TABLES `locations` WRITE;
/*!40000 ALTER TABLE `locations` DISABLE KEYS */;
INSERT INTO `locations` VALUES (1,'Berlin','Berlin','Germany',10125,'Invalidenstrasse 31 , 10115, Berlin, Deutschland','2020-01-13 05:05:39'),(2,'Berlin','Berlin','Germany',10325,'Invalidenstrasse 31 , 10115, Berlin, Deutschland','2020-01-13 05:06:12'),(4,'Berlin','Berlin','Germany',10025,'Invalide nstrasse 31 , 10115, Berlin, Deutschland','2020-01-13 05:26:32'),(5,'Berlin','Berlin','Germany',10045,'Invalide nstrasse 31 , 10115, Berlin, Deutschland','2020-01-13 07:58:12'),(6,'Berlin','Berlin','Germany',10045,'Invalide nstrasse 31 , 10115, Berlin, Deutschland','2020-01-13 08:56:37'),(7,'Berlin','Berlin','Germany',10045,'Invalide nstrasse 31 , 10115, Berlin, Deutschland','2020-01-13 09:03:20'),(8,'Berlin','Berlin','Germany',10045,'Invalide nstrasse 31 , 10115, Berlin, Deutschland','2020-01-13 09:20:10'),(9,'Berlin','Berlin','Germany',10045,'Invalide nstrasse 31 , 10115, Berlin, Deutschland','2020-01-13 09:21:51'),(10,'Berlin','Berlin','Germany',10045,'Invalide nstrasse 31 , 10115, Berlin, Deutschland','2020-01-13 09:22:37');
/*!40000 ALTER TABLE `locations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'trivagodb'
--

--
-- Dumping routines for database 'trivagodb'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2020-01-13 15:06:51
