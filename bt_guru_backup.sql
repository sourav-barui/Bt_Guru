-- MySQL dump 10.13  Distrib 8.4.9, for Linux (x86_64)
--
-- Host: localhost    Database: bt_guru
-- ------------------------------------------------------
-- Server version	8.4.9

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
-- Table structure for table `blogs`
--

DROP TABLE IF EXISTS `blogs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `blogs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `excerpt` text COLLATE utf8mb4_unicode_ci,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `featured_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gallery` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `status` enum('draft','published','scheduled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `published_at` timestamp NULL DEFAULT NULL,
  `views_count` bigint NOT NULL DEFAULT '0',
  `meta` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `allow_comments` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `blogs_slug_unique` (`slug`),
  KEY `blogs_user_id_foreign` (`user_id`),
  KEY `blogs_tenant_id_status_published_at_index` (`tenant_id`,`status`,`published_at`),
  KEY `blogs_tenant_id_category_index` (`tenant_id`,`category`),
  KEY `blogs_tenant_id_is_featured_index` (`tenant_id`,`is_featured`),
  CONSTRAINT `blogs_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `blogs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `blogs_chk_1` CHECK (json_valid(`gallery`)),
  CONSTRAINT `blogs_chk_2` CHECK (json_valid(`tags`)),
  CONSTRAINT `blogs_chk_3` CHECK (json_valid(`meta`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blogs`
--

LOCK TABLES `blogs` WRITE;
/*!40000 ALTER TABLE `blogs` DISABLE KEYS */;
/*!40000 ALTER TABLE `blogs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `book_orders`
--

DROP TABLE IF EXISTS `book_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `book_orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `book_id` bigint unsigned NOT NULL,
  `student_id` bigint unsigned NOT NULL,
  `order_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pdf_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `physical_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_amount` decimal(10,2) NOT NULL,
  `payment_status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transaction_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `razorpay_order_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `razorpay_payment_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `razorpay_signature` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delivery_status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delivery_address` text COLLATE utf8mb4_unicode_ci,
  `delivery_phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tracking_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delivered_at` timestamp NULL DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `paid_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `book_orders_book_id_foreign` (`book_id`),
  KEY `book_orders_student_id_foreign` (`student_id`),
  KEY `book_orders_tenant_id_student_id_index` (`tenant_id`,`student_id`),
  KEY `book_orders_tenant_id_payment_status_index` (`tenant_id`,`payment_status`),
  KEY `book_orders_tenant_id_delivery_status_index` (`tenant_id`,`delivery_status`),
  CONSTRAINT `book_orders_book_id_foreign` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE,
  CONSTRAINT `book_orders_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `book_orders_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `book_orders`
--

LOCK TABLES `book_orders` WRITE;
/*!40000 ALTER TABLE `book_orders` DISABLE KEYS */;
INSERT INTO `book_orders` VALUES (1,1,2,6,'physical',0.00,499.97,499.97,'completed','manual',NULL,NULL,NULL,NULL,'shipped','dfsdfsdf','78924785412','487487165',NULL,'Book: Test 2 phy\r\nOrder Type: Physical\r\nDelivery Address: dfsdfsdf\r\nContact: 78924785412','2026-05-29 14:59:33','2026-05-29 14:59:33','2026-05-29 15:00:50',NULL);
/*!40000 ALTER TABLE `book_orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `books`
--

DROP TABLE IF EXISTS `books`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `books` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `author` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `publisher` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `isbn` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pdf',
  `pdf_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `physical_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `cover_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pdf_file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `stock_quantity` int NOT NULL DEFAULT '0',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `books_tenant_id_slug_unique` (`tenant_id`,`slug`),
  KEY `books_tenant_id_status_index` (`tenant_id`,`status`),
  KEY `books_tenant_id_type_index` (`tenant_id`,`type`),
  CONSTRAINT `books_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `books_chk_1` CHECK (json_valid(`metadata`))
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `books`
--

LOCK TABLES `books` WRITE;
/*!40000 ALTER TABLE `books` DISABLE KEYS */;
INSERT INTO `books` VALUES (1,1,'Test Book','test-book',NULL,'Pradip Kr','SS','687138736','pdf',150.00,0.00,'books/covers/yKomuD2JDYAGGesJzlKCCErscnfanFROUGib8cmG.png','books/pdfs/WT874pBrjaAlodwbvxk66vEEz42jYt8GLmnXQLNO.pdf',0,'active','[]','2026-05-29 14:22:43','2026-05-29 14:22:43',NULL),(2,1,'Test 2 phy','test-2-phy',NULL,'sd','sdsd','3524','physical',1200.00,499.97,'books/covers/fo7hL6xV52pFoQvlYO9VyrZSfuFDFbXSdYhnM7Km.jpg',NULL,119,'active','[]','2026-05-29 14:44:27','2026-05-29 14:59:33',NULL);
/*!40000 ALTER TABLE `books` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `btlive_recordings`
--

DROP TABLE IF EXISTS `btlive_recordings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `btlive_recordings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `live_class_id` bigint unsigned NOT NULL,
  `tenant_id` bigint unsigned NOT NULL,
  `recording_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `s3_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `s3_key` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_size` bigint DEFAULT NULL,
  `duration` int DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'recording',
  `started_at` timestamp NULL DEFAULT NULL,
  `ended_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `btlive_recordings_recording_id_unique` (`recording_id`),
  KEY `btlive_recordings_live_class_id_foreign` (`live_class_id`),
  KEY `btlive_recordings_tenant_id_status_index` (`tenant_id`,`status`),
  KEY `btlive_recordings_recording_id_index` (`recording_id`),
  CONSTRAINT `btlive_recordings_live_class_id_foreign` FOREIGN KEY (`live_class_id`) REFERENCES `live_classes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `btlive_recordings_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `btlive_recordings`
--

LOCK TABLES `btlive_recordings` WRITE;
/*!40000 ALTER TABLE `btlive_recordings` DISABLE KEYS */;
/*!40000 ALTER TABLE `btlive_recordings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chapters`
--

DROP TABLE IF EXISTS `chapters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chapters` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `subject_id` bigint unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `order` int NOT NULL DEFAULT '0',
  `status` enum('active','inactive','draft') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `chapters_subject_id_foreign` (`subject_id`),
  CONSTRAINT `chapters_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chapters`
--

LOCK TABLES `chapters` WRITE;
/*!40000 ALTER TABLE `chapters` DISABLE KEYS */;
INSERT INTO `chapters` VALUES (1,1,'India',NULL,0,'active','2026-05-23 05:39:21','2026-05-23 05:39:21',NULL),(2,1,'History',NULL,2,'active','2026-05-23 12:02:44','2026-05-23 12:20:07','2026-05-23 12:20:07'),(3,6,'Class 1',NULL,1,'active','2026-05-30 14:48:18','2026-05-30 14:48:18',NULL);
/*!40000 ALTER TABLE `chapters` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `coupon_plan`
--

DROP TABLE IF EXISTS `coupon_plan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `coupon_plan` (
  `coupon_id` bigint unsigned NOT NULL,
  `plan_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`coupon_id`,`plan_id`),
  KEY `coupon_plan_plan_id_foreign` (`plan_id`),
  CONSTRAINT `coupon_plan_coupon_id_foreign` FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`id`) ON DELETE CASCADE,
  CONSTRAINT `coupon_plan_plan_id_foreign` FOREIGN KEY (`plan_id`) REFERENCES `subscription_plans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coupon_plan`
--

LOCK TABLES `coupon_plan` WRITE;
/*!40000 ALTER TABLE `coupon_plan` DISABLE KEYS */;
/*!40000 ALTER TABLE `coupon_plan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `coupons`
--

DROP TABLE IF EXISTS `coupons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `coupons` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `discount_type` enum('percentage','fixed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'percentage',
  `discount_value` decimal(10,2) NOT NULL,
  `max_uses` int DEFAULT NULL,
  `used_count` int NOT NULL DEFAULT '0',
  `valid_from` date DEFAULT NULL,
  `valid_until` date DEFAULT NULL,
  `applicable_plan_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `coupons_code_unique` (`code`),
  KEY `coupons_code_index` (`code`),
  KEY `coupons_is_active_valid_from_valid_until_index` (`is_active`,`valid_from`,`valid_until`),
  CONSTRAINT `coupons_chk_1` CHECK (json_valid(`applicable_plan_ids`))
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coupons`
--

LOCK TABLES `coupons` WRITE;
/*!40000 ALTER TABLE `coupons` DISABLE KEYS */;
INSERT INTO `coupons` VALUES (1,'GURU50',NULL,'percentage',49.99,NULL,0,NULL,NULL,'[\"1\"]',1,NULL,'2026-05-26 21:40:01','2026-05-26 21:40:01',NULL);
/*!40000 ALTER TABLE `coupons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `course_subscriptions`
--

DROP TABLE IF EXISTS `course_subscriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `course_subscriptions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `enrollment_id` bigint unsigned NOT NULL,
  `student_id` bigint unsigned NOT NULL,
  `course_id` bigint unsigned NOT NULL,
  `access_start` datetime NOT NULL,
  `access_end` datetime NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'current',
  `fee_paid` decimal(10,2) NOT NULL DEFAULT '0.00',
  `payment_status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `course_subscriptions_enrollment_id_foreign` (`enrollment_id`),
  KEY `course_subscriptions_course_id_foreign` (`course_id`),
  KEY `course_subscriptions_created_by_foreign` (`created_by`),
  KEY `course_subscriptions_student_id_course_id_index` (`student_id`,`course_id`),
  KEY `course_subscriptions_tenant_id_payment_status_index` (`tenant_id`,`payment_status`),
  CONSTRAINT `course_subscriptions_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `course_subscriptions_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `course_subscriptions_enrollment_id_foreign` FOREIGN KEY (`enrollment_id`) REFERENCES `enrollments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `course_subscriptions_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `course_subscriptions_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `course_subscriptions`
--

LOCK TABLES `course_subscriptions` WRITE;
/*!40000 ALTER TABLE `course_subscriptions` DISABLE KEYS */;
INSERT INTO `course_subscriptions` VALUES (1,1,12,11,5,'2026-05-28 11:53:06','2026-06-27 11:53:06','monthly',50.00,'paid','Monthly fee for 5/2026',2,'2026-05-28 06:23:06','2026-05-28 06:23:06'),(2,5,13,18,6,'2026-05-30 14:51:18','2026-06-29 14:51:18','monthly',1500.00,'paid','Monthly fee for 5/2026',16,'2026-05-30 14:51:18','2026-05-30 14:51:18');
/*!40000 ALTER TABLE `course_subscriptions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `course_teacher`
--

DROP TABLE IF EXISTS `course_teacher`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `course_teacher` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `course_id` bigint unsigned NOT NULL,
  `teacher_id` bigint unsigned NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `course_teacher_course_id_teacher_id_unique` (`course_id`,`teacher_id`),
  KEY `course_teacher_teacher_id_foreign` (`teacher_id`),
  CONSTRAINT `course_teacher_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `course_teacher_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `course_teacher`
--

LOCK TABLES `course_teacher` WRITE;
/*!40000 ALTER TABLE `course_teacher` DISABLE KEYS */;
INSERT INTO `course_teacher` VALUES (1,1,3,1,'2026-05-23 04:57:23','2026-05-26 10:37:38'),(3,3,5,1,'2026-05-23 04:57:23','2026-05-23 04:57:23'),(4,4,3,1,'2026-05-23 04:57:23','2026-05-24 04:45:34'),(5,2,3,1,'2026-05-27 23:04:58','2026-05-27 23:04:58'),(6,5,3,1,'2026-05-28 06:14:24','2026-05-28 06:14:24'),(7,5,4,0,'2026-05-28 06:14:24','2026-05-28 06:14:24'),(8,5,5,0,'2026-05-28 06:14:24','2026-05-28 06:14:24');
/*!40000 ALTER TABLE `course_teacher` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `courses`
--

DROP TABLE IF EXISTS `courses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `courses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `fees` decimal(10,2) NOT NULL DEFAULT '0.00',
  `fees_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'one_time',
  `past_month_fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `duration` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `thumbnail` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `courses_tenant_id_slug_unique` (`tenant_id`,`slug`),
  KEY `courses_tenant_id_status_index` (`tenant_id`,`status`),
  CONSTRAINT `courses_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `courses_chk_1` CHECK (json_valid(`metadata`))
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `courses`
--

LOCK TABLES `courses` WRITE;
/*!40000 ALTER TABLE `courses` DISABLE KEYS */;
INSERT INTO `courses` VALUES (1,1,'Mathematics Mastery','mathematics-mastery','Comprehensive mathematics course covering algebra, geometry, and calculus.',1000.00,'monthly',800.00,'4 months 23 days','2026-01-01','2026-05-24','courses/CEXtGgDx6lncPTR11D9FNeuQaUlZPKAIblwbvNxO.jpg','active','[]','2026-05-23 04:57:23','2026-05-26 10:37:38',NULL),(2,1,'Physics Fundamentals','physics-fundamentals','Learn the basics of physics including mechanics, thermodynamics, and electromagnetism.',800.00,'monthly',500.00,'5 months 30 days','2026-03-01','2026-08-31',NULL,'active','[]','2026-05-23 04:57:23','2026-05-27 23:04:58',NULL),(3,1,'Chemistry Essentials','chemistry-essentials','Organic and inorganic chemistry course for high school students.',16000.00,'one_time',0.00,'5 months',NULL,NULL,NULL,'active','[]','2026-05-23 04:57:23','2026-05-23 04:57:23',NULL),(4,1,'English Literature','english-literature','Explore classic and modern English literature with expert guidance.',1000.00,'monthly',800.00,'7 months 30 days','2026-01-01','2026-08-31',NULL,'active','[]','2026-05-23 04:57:23','2026-05-24 04:45:34',NULL),(5,1,'TE','te','G',50.00,'monthly',30.00,'10 months 8 days','2026-03-01','2027-01-09',NULL,'active','[]','2026-05-28 06:14:24','2026-05-28 06:14:24',NULL),(6,5,'Maths','maths','s',1500.00,'monthly',200.00,'12 months 30 days','2026-01-01','2027-01-31',NULL,'active','[]','2026-05-30 14:47:35','2026-05-30 14:47:35',NULL),(7,17,'সাহিত্যের ইতিহাস ও ব্যাকরণ','sahitzer-itihas-oo-bzakrn','স্কুল সার্ভিস কমিশন, মাদ্রাসা সার্ভিস কমিশন ও পাবলিক সার্ভিস কমিশনের পরীক্ষার সিলেবাসে সাহিত্যের ইতিহাস ও ব্যাকরণ অংশ থাকে। সেটা মাথায় রেখে এই কোর্সে উপরের তিন পরীক্ষারই সিলেবাস কভার করা হবে। এর ফলে যে পরীক্ষাই আসুক, সাহিত্যের',0.00,'one_time',0.00,NULL,NULL,NULL,NULL,'active','[]','2026-06-01 16:42:38','2026-06-01 16:42:38',NULL);
/*!40000 ALTER TABLE `courses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `curricula`
--

DROP TABLE IF EXISTS `curricula`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `curricula` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `course_id` bigint unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `order` int NOT NULL DEFAULT '0',
  `status` enum('active','inactive','draft') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `curricula_course_id_foreign` (`course_id`),
  CONSTRAINT `curricula_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `curricula`
--

LOCK TABLES `curricula` WRITE;
/*!40000 ALTER TABLE `curricula` DISABLE KEYS */;
INSERT INTO `curricula` VALUES (1,1,'Batch 1',NULL,1,'active','2026-05-23 05:38:40','2026-05-23 05:39:05',NULL),(2,2,'A',NULL,0,'active','2026-05-27 23:01:59','2026-05-27 23:01:59',NULL),(3,3,'A',NULL,0,'active','2026-05-28 06:13:00','2026-05-28 06:13:00',NULL),(4,5,'AD',NULL,0,'active','2026-05-28 06:14:32','2026-05-28 06:14:32',NULL),(5,6,'English',NULL,1,'active','2026-05-30 14:47:55','2026-05-30 14:47:55',NULL);
/*!40000 ALTER TABLE `curricula` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `curriculum_contents`
--

DROP TABLE IF EXISTS `curriculum_contents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `curriculum_contents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `contentable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contentable_id` bigint unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `video_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `video_type` enum('youtube','vimeo','other') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order` int NOT NULL DEFAULT '0',
  `available_from` date DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `curriculum_contents_contentable_type_contentable_id_index` (`contentable_type`,`contentable_id`),
  KEY `curriculum_contents_user_id_foreign` (`user_id`),
  CONSTRAINT `curriculum_contents_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `curriculum_contents`
--

LOCK TABLES `curriculum_contents` WRITE;
/*!40000 ALTER TABLE `curriculum_contents` DISABLE KEYS */;
INSERT INTO `curriculum_contents` VALUES (1,'App\\Models\\Subject',1,'Inside Sri Lanka\'s Mysterious Jungle',NULL,'https://www.youtube.com/watch?v=lstCjVoCRmM','youtube',0,NULL,NULL,'2026-05-23 06:14:27','2026-05-23 06:14:27'),(2,'App\\Models\\Lesson',1,'24 Hours in 2 Countries that Hate Each Other',NULL,'https://www.youtube.com/watch?v=8_wiuDd691s','youtube',0,NULL,NULL,'2026-05-23 06:17:53','2026-05-23 06:17:53'),(3,'App\\Models\\Lesson',1,'He Was Born a Grown Man With Unlimited IQ, But No One Could Explain What He Was',NULL,'https://www.youtube.com/watch?v=Ayp1o-zbHNI','youtube',0,NULL,NULL,'2026-05-23 06:18:23','2026-05-23 06:18:23'),(4,'App\\Models\\Lesson',2,'24 Hours in 2 Countries that Hate Each Other',NULL,'https://www.youtube.com/watch?v=8_wiuDd691s','youtube',0,NULL,3,'2026-05-23 06:40:05','2026-05-23 06:40:05'),(5,'App\\Models\\Subject',1,'Volkswagen Polo TSI EP1 - 7 Reasons Why I Bought It | Project Polo',NULL,'https://www.youtube.com/watch?v=eWCuurWc0Go','youtube',0,NULL,3,'2026-05-27 00:41:25','2026-05-27 00:41:25'),(6,'App\\Models\\Subject',2,'Your are in trouble : 5 dangerous signs of a Narcissist',NULL,'https://www.youtube.com/watch?v=fucMXQ3kKvU','youtube',0,NULL,3,'2026-05-27 01:51:42','2026-05-27 01:51:42'),(7,'App\\Models\\Subject',4,'Tb',NULL,'https://www.youtube.com/watch?v=OLN7nDPK_Aw&list=RDOLN7nDPK_Aw&start_radio=1','youtube',0,NULL,2,'2026-05-28 06:13:27','2026-05-28 06:13:27'),(8,'App\\Models\\Subject',5,'AA',NULL,'https://www.youtube.com/watch?v=OLN7nDPK_Aw&list=RDOLN7nDPK_Aw&start_radio=1','youtube',0,NULL,2,'2026-05-28 06:14:41','2026-05-28 06:14:41'),(9,'App\\Models\\Subject',5,'fg',NULL,'https://www.youtube.com/watch?v=mB-T23SMKw0','youtube',0,NULL,2,'2026-05-28 06:21:07','2026-05-28 06:21:07');
/*!40000 ALTER TABLE `curriculum_contents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `curriculum_notes`
--

DROP TABLE IF EXISTS `curriculum_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `curriculum_notes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned DEFAULT NULL,
  `noteable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `noteable_id` bigint unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pdf',
  `is_downloadable` tinyint(1) NOT NULL DEFAULT '0',
  `order` int NOT NULL DEFAULT '0',
  `available_from` date DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `curriculum_notes_noteable_type_noteable_id_index` (`noteable_type`,`noteable_id`),
  KEY `curriculum_notes_user_id_foreign` (`user_id`),
  CONSTRAINT `curriculum_notes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `curriculum_notes`
--

LOCK TABLES `curriculum_notes` WRITE;
/*!40000 ALTER TABLE `curriculum_notes` DISABLE KEYS */;
INSERT INTO `curriculum_notes` VALUES (1,NULL,'App\\Models\\Lesson',1,'test','curriculum_notes/NnYEGts09B9DRgu8OkgBsd6qXQYFSDYkqaOwHoYo.pdf','pdf',0,0,NULL,NULL,'2026-05-23 06:15:09','2026-05-23 06:15:09'),(2,NULL,'App\\Models\\Subject',3,'A','curriculum_notes/BRUdXtzLAfIAAxVEFdaTfgvzq8Z9ZBcnG0QGFBTq.pdf','pdf',0,0,NULL,2,'2026-05-27 23:02:37','2026-05-27 23:02:37'),(3,NULL,'App\\Models\\Subject',1,'test','curriculum_notes/cVdwt2uoTbQxjsG6SzA6fj3yTrv7bqWlReKIaNmv.pdf','pdf',0,0,NULL,2,'2026-05-28 09:20:10','2026-05-28 09:20:10'),(4,NULL,'App\\Models\\Subject',2,'Teast','curriculum_notes/xe5edwrhRWdfC0qvnGpWdT91vEXhZKc8T5JOS7OR.pdf','pdf',0,0,NULL,2,'2026-05-28 09:21:01','2026-05-28 09:21:01'),(5,NULL,'App\\Models\\Subject',2,'df','curriculum_notes/5NrdMBi6ppACa4lMpFdRuYRDM8A60QsLl6NzbtpQ.pdf','pdf',1,0,NULL,2,'2026-05-28 09:45:48','2026-05-28 09:45:48'),(15,5,'App\\Models\\Chapter',3,'test 7','curriculum_notes/Pv29PPhTwcSqqKN6nF6RjiV6cI8DqUph2KNCsfl8.pdf','pdf',0,0,NULL,16,'2026-05-30 19:12:32','2026-05-30 19:12:32'),(16,5,'App\\Models\\Chapter',3,'tets','curriculum_notes/7062yIGKX0BYWAvdguxwnah89b9FKGoWvNdDE4VA.pdf','pdf',0,0,NULL,16,'2026-06-01 13:39:10','2026-06-01 13:39:10');
/*!40000 ALTER TABLE `curriculum_notes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `enrollments`
--

DROP TABLE IF EXISTS `enrollments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `enrollments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `student_id` bigint unsigned NOT NULL,
  `course_id` bigint unsigned NOT NULL,
  `payment_status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `enrollment_status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `fees_paid` decimal(10,2) NOT NULL DEFAULT '0.00',
  `fees_total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `enrolled_at` timestamp NULL DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `approved_by` bigint unsigned DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `enrollments_student_id_course_id_unique` (`student_id`,`course_id`),
  KEY `enrollments_course_id_foreign` (`course_id`),
  KEY `enrollments_approved_by_foreign` (`approved_by`),
  KEY `enrollments_tenant_id_enrollment_status_index` (`tenant_id`,`enrollment_status`),
  KEY `enrollments_tenant_id_payment_status_index` (`tenant_id`,`payment_status`),
  CONSTRAINT `enrollments_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `enrollments_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `enrollments_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `enrollments_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `enrollments_chk_1` CHECK (json_valid(`metadata`))
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `enrollments`
--

LOCK TABLES `enrollments` WRITE;
/*!40000 ALTER TABLE `enrollments` DISABLE KEYS */;
INSERT INTO `enrollments` VALUES (1,1,6,1,'completed','active',15000.00,15000.00,'2026-05-23 04:57:25','2026-05-23 04:57:25',2,'[]',NULL,'2026-05-23 04:57:25','2026-05-23 04:57:25',NULL),(2,1,6,2,'completed','active',18000.00,18000.00,'2026-05-23 04:57:25','2026-05-23 04:57:25',2,'[]',NULL,'2026-05-23 04:57:25','2026-05-23 04:57:25',NULL),(3,1,7,1,'partial','active',7500.00,15000.00,'2026-05-23 04:57:25','2026-05-23 04:57:25',2,'[]',NULL,'2026-05-23 04:57:25','2026-05-23 04:57:25',NULL),(4,1,7,3,'pending','approved',0.00,16000.00,NULL,'2026-05-23 05:33:32',2,'[]',NULL,'2026-05-23 04:57:25','2026-05-23 05:33:32',NULL),(5,1,8,2,'completed','active',18000.00,18000.00,'2026-05-23 04:57:25','2026-05-23 04:57:25',2,'[]',NULL,'2026-05-23 04:57:25','2026-05-23 04:57:25',NULL),(6,1,9,3,'completed','active',16000.00,16000.00,'2026-05-23 04:57:25','2026-05-23 04:57:25',2,'[]',NULL,'2026-05-23 04:57:25','2026-05-23 04:57:25',NULL),(7,1,10,4,'completed','approved',12000.00,12000.00,NULL,'2026-05-23 04:57:25',2,'[]',NULL,'2026-05-23 04:57:25','2026-05-23 04:57:25',NULL),(8,1,11,1,'completed','active',1000.00,15000.00,'2026-05-27 22:59:17','2026-05-27 22:59:17',2,'[]',NULL,'2026-05-23 04:57:25','2026-05-27 22:59:18',NULL),(9,1,6,4,'completed','active',1000.00,1000.00,'2026-05-24 05:35:21','2026-05-24 05:35:21',2,'[]',NULL,'2026-05-24 05:35:21','2026-05-24 05:35:21',NULL),(11,1,11,2,'pending','active',0.00,800.00,'2026-05-28 05:57:26','2026-05-28 05:57:26',2,'[]',NULL,'2026-05-28 05:57:26','2026-05-28 06:10:22',NULL),(12,1,11,5,'completed','active',50.00,50.00,'2026-05-28 06:23:06','2026-05-28 06:23:06',2,'[]',NULL,'2026-05-28 06:15:11','2026-05-28 06:23:06',NULL),(13,5,18,6,'completed','active',1500.00,1500.00,'2026-05-30 14:51:18','2026-05-30 14:51:18',16,'[]',NULL,'2026-05-30 14:51:18','2026-05-30 14:51:18',NULL);
/*!40000 ALTER TABLE `enrollments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `exam_answers`
--

DROP TABLE IF EXISTS `exam_answers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `exam_answers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `attempt_id` bigint unsigned NOT NULL,
  `question_id` bigint unsigned NOT NULL,
  `selected_option_id` bigint unsigned DEFAULT NULL,
  `answer_text` text COLLATE utf8mb4_unicode_ci,
  `is_correct` tinyint(1) DEFAULT NULL,
  `marks_obtained` decimal(8,2) NOT NULL DEFAULT '0.00',
  `negative_marks` decimal(8,2) NOT NULL DEFAULT '0.00',
  `answered_at` timestamp NULL DEFAULT NULL,
  `time_spent_seconds` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `exam_answers_question_id_foreign` (`question_id`),
  KEY `exam_answers_selected_option_id_foreign` (`selected_option_id`),
  KEY `exam_answers_attempt_id_question_id_index` (`attempt_id`,`question_id`),
  CONSTRAINT `exam_answers_attempt_id_foreign` FOREIGN KEY (`attempt_id`) REFERENCES `exam_attempts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `exam_answers_question_id_foreign` FOREIGN KEY (`question_id`) REFERENCES `exam_questions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `exam_answers_selected_option_id_foreign` FOREIGN KEY (`selected_option_id`) REFERENCES `exam_question_options` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exam_answers`
--

LOCK TABLES `exam_answers` WRITE;
/*!40000 ALTER TABLE `exam_answers` DISABLE KEYS */;
INSERT INTO `exam_answers` VALUES (1,1,8,32,NULL,NULL,0.00,0.00,'2026-05-26 02:15:18',NULL,'2026-05-26 01:36:27','2026-05-26 02:15:18'),(2,1,13,52,NULL,NULL,0.00,0.00,'2026-05-26 02:24:37',NULL,'2026-05-26 01:40:33','2026-05-26 02:24:37'),(3,1,9,34,NULL,NULL,0.00,0.00,'2026-05-26 02:24:47',NULL,'2026-05-26 01:40:59','2026-05-26 02:24:47'),(4,1,10,40,NULL,NULL,0.00,0.00,'2026-05-26 02:21:02',NULL,'2026-05-26 01:41:02','2026-05-26 02:21:02'),(5,1,11,44,NULL,NULL,0.00,0.00,'2026-05-26 02:21:04',NULL,'2026-05-26 02:12:49','2026-05-26 02:21:04'),(6,1,6,21,NULL,NULL,0.00,0.00,'2026-05-26 02:15:33',NULL,'2026-05-26 02:15:33','2026-05-26 02:15:33'),(7,1,12,46,NULL,NULL,0.00,0.00,'2026-05-26 02:24:43',NULL,'2026-05-26 02:15:35','2026-05-26 02:24:43'),(8,1,7,25,NULL,NULL,0.00,0.00,'2026-05-26 02:15:36',NULL,'2026-05-26 02:15:36','2026-05-26 02:15:36'),(9,1,5,17,NULL,NULL,0.00,0.00,'2026-05-26 02:15:39',NULL,'2026-05-26 02:15:39','2026-05-26 02:15:39');
/*!40000 ALTER TABLE `exam_answers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `exam_attempts`
--

DROP TABLE IF EXISTS `exam_attempts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `exam_attempts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `exam_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `enrollment_id` bigint unsigned NOT NULL,
  `started_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `submitted_at` timestamp NULL DEFAULT NULL,
  `ended_at` timestamp NULL DEFAULT NULL,
  `total_questions` int NOT NULL DEFAULT '0',
  `answered_count` int NOT NULL DEFAULT '0',
  `correct_count` int NOT NULL DEFAULT '0',
  `wrong_count` int NOT NULL DEFAULT '0',
  `skipped_count` int NOT NULL DEFAULT '0',
  `marks_obtained` decimal(8,2) NOT NULL DEFAULT '0.00',
  `negative_marks` decimal(8,2) NOT NULL DEFAULT '0.00',
  `total_marks` decimal(8,2) NOT NULL DEFAULT '0.00',
  `percentage` decimal(5,2) NOT NULL DEFAULT '0.00',
  `status` enum('in_progress','submitted','graded','time_expired') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'in_progress',
  `is_passed` tinyint(1) DEFAULT NULL,
  `attempt_number` int NOT NULL DEFAULT '1',
  `ip_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `exam_attempts_exam_id_user_id_attempt_number_unique` (`exam_id`,`user_id`,`attempt_number`),
  KEY `exam_attempts_user_id_foreign` (`user_id`),
  KEY `exam_attempts_enrollment_id_foreign` (`enrollment_id`),
  KEY `exam_attempts_exam_id_user_id_index` (`exam_id`,`user_id`),
  KEY `exam_attempts_status_submitted_at_index` (`status`,`submitted_at`),
  CONSTRAINT `exam_attempts_enrollment_id_foreign` FOREIGN KEY (`enrollment_id`) REFERENCES `enrollments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `exam_attempts_exam_id_foreign` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE CASCADE,
  CONSTRAINT `exam_attempts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exam_attempts`
--

LOCK TABLES `exam_attempts` WRITE;
/*!40000 ALTER TABLE `exam_attempts` DISABLE KEYS */;
INSERT INTO `exam_attempts` VALUES (1,2,6,1,'2026-05-26 16:28:13',NULL,NULL,0,0,0,0,0,0.00,0.00,0.00,0.00,'in_progress',1,1,NULL,NULL,'2026-05-26 01:19:44','2026-05-26 10:58:13');
/*!40000 ALTER TABLE `exam_attempts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `exam_question_options`
--

DROP TABLE IF EXISTS `exam_question_options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `exam_question_options` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `question_id` bigint unsigned NOT NULL,
  `option_text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `option_image` text COLLATE utf8mb4_unicode_ci,
  `is_correct` tinyint(1) NOT NULL DEFAULT '0',
  `order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `exam_question_options_question_id_order_index` (`question_id`,`order`),
  CONSTRAINT `exam_question_options_question_id_foreign` FOREIGN KEY (`question_id`) REFERENCES `exam_questions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exam_question_options`
--

LOCK TABLES `exam_question_options` WRITE;
/*!40000 ALTER TABLE `exam_question_options` DISABLE KEYS */;
INSERT INTO `exam_question_options` VALUES (1,1,'True',NULL,1,1,'2026-05-25 22:40:54','2026-05-25 22:40:54'),(2,1,'False',NULL,0,2,'2026-05-25 22:40:54','2026-05-25 22:40:54'),(3,1,'TREE',NULL,0,3,'2026-05-25 22:40:54','2026-05-25 22:40:54'),(4,1,'FLAHH',NULL,0,4,'2026-05-25 22:40:54','2026-05-25 22:40:54'),(5,2,'London',NULL,0,1,'2026-05-25 22:43:03','2026-05-25 22:43:03'),(6,2,'Berlin',NULL,0,2,'2026-05-25 22:43:03','2026-05-25 22:43:03'),(7,2,'Paris',NULL,1,3,'2026-05-25 22:43:03','2026-05-25 22:43:03'),(8,2,'Madrid',NULL,0,4,'2026-05-25 22:43:03','2026-05-25 22:43:03'),(9,3,'2',NULL,0,1,'2026-05-25 22:43:03','2026-05-25 22:43:03'),(10,3,'3',NULL,0,2,'2026-05-25 22:43:03','2026-05-25 22:43:03'),(11,3,'4',NULL,1,3,'2026-05-25 22:43:03','2026-05-25 22:43:03'),(12,3,'5',NULL,0,4,'2026-05-25 22:43:03','2026-05-25 22:43:03'),(13,4,'Venus',NULL,0,1,'2026-05-25 22:43:03','2026-05-25 22:43:03'),(14,4,'Earth',NULL,0,2,'2026-05-25 22:43:03','2026-05-25 22:43:03'),(15,4,'Mars',NULL,0,3,'2026-05-25 22:43:03','2026-05-25 22:43:03'),(16,4,'Mercury',NULL,1,4,'2026-05-25 22:43:03','2026-05-25 22:43:03'),(17,5,'London',NULL,0,1,'2026-05-26 00:15:46','2026-05-26 00:15:46'),(18,5,'Berlin',NULL,0,2,'2026-05-26 00:15:46','2026-05-26 00:15:46'),(19,5,'Paris',NULL,1,3,'2026-05-26 00:15:46','2026-05-26 00:15:46'),(20,5,'Madrid',NULL,0,4,'2026-05-26 00:15:46','2026-05-26 00:15:46'),(21,6,'2',NULL,0,1,'2026-05-26 00:15:46','2026-05-26 00:15:46'),(22,6,'3',NULL,0,2,'2026-05-26 00:15:46','2026-05-26 00:15:46'),(23,6,'4',NULL,1,3,'2026-05-26 00:15:46','2026-05-26 00:15:46'),(24,6,'5',NULL,0,4,'2026-05-26 00:15:46','2026-05-26 00:15:46'),(25,7,'Venus',NULL,0,1,'2026-05-26 00:15:46','2026-05-26 00:15:46'),(26,7,'Earth',NULL,0,2,'2026-05-26 00:15:46','2026-05-26 00:15:46'),(27,7,'Mars',NULL,0,3,'2026-05-26 00:15:46','2026-05-26 00:15:46'),(28,7,'Mercury',NULL,1,4,'2026-05-26 00:15:46','2026-05-26 00:15:46'),(29,8,'London',NULL,0,1,'2026-05-26 00:17:01','2026-05-26 00:17:01'),(30,8,'Berlin',NULL,0,2,'2026-05-26 00:17:01','2026-05-26 00:17:01'),(31,8,'Paris',NULL,1,3,'2026-05-26 00:17:01','2026-05-26 00:17:01'),(32,8,'Madrid',NULL,0,4,'2026-05-26 00:17:01','2026-05-26 00:17:01'),(33,9,'2',NULL,0,1,'2026-05-26 00:17:01','2026-05-26 00:17:01'),(34,9,'3',NULL,0,2,'2026-05-26 00:17:01','2026-05-26 00:17:01'),(35,9,'4',NULL,1,3,'2026-05-26 00:17:01','2026-05-26 00:17:01'),(36,9,'5',NULL,0,4,'2026-05-26 00:17:01','2026-05-26 00:17:01'),(37,10,'Venus',NULL,0,1,'2026-05-26 00:17:01','2026-05-26 00:17:01'),(38,10,'Earth',NULL,0,2,'2026-05-26 00:17:01','2026-05-26 00:17:01'),(39,10,'Mars',NULL,0,3,'2026-05-26 00:17:01','2026-05-26 00:17:01'),(40,10,'Mercury',NULL,1,4,'2026-05-26 00:17:01','2026-05-26 00:17:01'),(41,11,'London',NULL,0,1,'2026-05-26 00:17:28','2026-05-26 00:17:28'),(42,11,'Berlin',NULL,0,2,'2026-05-26 00:17:28','2026-05-26 00:17:28'),(43,11,'Paris',NULL,1,3,'2026-05-26 00:17:28','2026-05-26 00:17:28'),(44,11,'Madrid',NULL,0,4,'2026-05-26 00:17:28','2026-05-26 00:17:28'),(45,12,'2',NULL,0,1,'2026-05-26 00:17:28','2026-05-26 00:17:28'),(46,12,'3',NULL,0,2,'2026-05-26 00:17:28','2026-05-26 00:17:28'),(47,12,'4',NULL,1,3,'2026-05-26 00:17:28','2026-05-26 00:17:28'),(48,12,'5',NULL,0,4,'2026-05-26 00:17:28','2026-05-26 00:17:28'),(49,13,'Venus',NULL,0,1,'2026-05-26 00:17:28','2026-05-26 00:17:28'),(50,13,'Earth',NULL,0,2,'2026-05-26 00:17:28','2026-05-26 00:17:28'),(51,13,'Mars',NULL,0,3,'2026-05-26 00:17:28','2026-05-26 00:17:28'),(52,13,'Mercury',NULL,1,4,'2026-05-26 00:17:28','2026-05-26 00:17:28'),(53,14,'True',NULL,1,1,'2026-05-27 22:18:48','2026-05-27 22:18:48'),(54,14,'False',NULL,0,2,'2026-05-27 22:18:48','2026-05-27 22:18:48');
/*!40000 ALTER TABLE `exam_question_options` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `exam_questions`
--

DROP TABLE IF EXISTS `exam_questions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `exam_questions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `exam_id` bigint unsigned NOT NULL,
  `section_id` bigint unsigned DEFAULT NULL,
  `question_text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `question_image` text COLLATE utf8mb4_unicode_ci,
  `question_type` enum('single_choice','multiple_choice','true_false') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'single_choice',
  `explanation` text COLLATE utf8mb4_unicode_ci,
  `marks` int NOT NULL DEFAULT '1',
  `negative_marks` decimal(4,2) NOT NULL DEFAULT '0.00',
  `order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `exam_questions_section_id_foreign` (`section_id`),
  KEY `exam_questions_exam_id_section_id_index` (`exam_id`,`section_id`),
  KEY `exam_questions_exam_id_order_index` (`exam_id`,`order`),
  CONSTRAINT `exam_questions_exam_id_foreign` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE CASCADE,
  CONSTRAINT `exam_questions_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `exam_sections` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exam_questions`
--

LOCK TABLES `exam_questions` WRITE;
/*!40000 ALTER TABLE `exam_questions` DISABLE KEYS */;
INSERT INTO `exam_questions` VALUES (1,1,1,'Which is correct?',NULL,'single_choice',NULL,1,0.00,1,'2026-05-25 22:40:54','2026-05-25 22:40:54'),(2,1,NULL,'What is the capital of France?',NULL,'single_choice',NULL,1,0.25,2,'2026-05-25 22:43:03','2026-05-25 22:43:03'),(3,1,NULL,'What is 2+2?',NULL,'single_choice',NULL,1,0.25,3,'2026-05-25 22:43:03','2026-05-25 22:43:03'),(4,1,NULL,'Which planet is closest to the Sun?',NULL,'single_choice',NULL,1,0.25,4,'2026-05-25 22:43:03','2026-05-25 22:43:03'),(5,2,NULL,'What is the capital of France?',NULL,'single_choice',NULL,1,0.00,1,'2026-05-26 00:15:45','2026-05-26 00:15:45'),(6,2,NULL,'What is 2+2?',NULL,'single_choice',NULL,1,0.00,2,'2026-05-26 00:15:46','2026-05-26 00:15:46'),(7,2,NULL,'Which planet is closest to the Sun?',NULL,'single_choice',NULL,1,0.00,3,'2026-05-26 00:15:46','2026-05-26 00:15:46'),(8,2,2,'What is the capital of France?',NULL,'single_choice',NULL,1,0.00,4,'2026-05-26 00:17:01','2026-05-26 00:17:01'),(9,2,2,'What is 2+2?',NULL,'single_choice',NULL,1,0.00,5,'2026-05-26 00:17:01','2026-05-26 00:17:01'),(10,2,2,'Which planet is closest to the Sun?',NULL,'single_choice',NULL,1,0.00,6,'2026-05-26 00:17:01','2026-05-26 00:17:01'),(11,2,3,'What is the capital of France?',NULL,'single_choice',NULL,1,0.00,7,'2026-05-26 00:17:28','2026-05-26 00:17:28'),(12,2,3,'What is 2+2?',NULL,'single_choice',NULL,1,0.00,8,'2026-05-26 00:17:28','2026-05-26 00:17:28'),(13,2,3,'Which planet is closest to the Sun?',NULL,'single_choice',NULL,1,0.00,9,'2026-05-26 00:17:28','2026-05-26 00:17:28'),(14,4,5,'Who are you?',NULL,'single_choice',NULL,1,0.00,1,'2026-05-27 22:18:48','2026-05-27 22:18:48');
/*!40000 ALTER TABLE `exam_questions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `exam_sections`
--

DROP TABLE IF EXISTS `exam_sections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `exam_sections` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `exam_id` bigint unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `order` int NOT NULL DEFAULT '0',
  `total_questions` int NOT NULL DEFAULT '0',
  `marks_per_question` int NOT NULL DEFAULT '1',
  `negative_marks_per_question` decimal(4,2) NOT NULL DEFAULT '0.00',
  `shuffle_questions` tinyint(1) NOT NULL DEFAULT '0',
  `time_limit_minutes` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `exam_sections_exam_id_order_index` (`exam_id`,`order`),
  CONSTRAINT `exam_sections_exam_id_foreign` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exam_sections`
--

LOCK TABLES `exam_sections` WRITE;
/*!40000 ALTER TABLE `exam_sections` DISABLE KEYS */;
INSERT INTO `exam_sections` VALUES (1,1,'Section A',NULL,1,0,1,0.00,0,NULL,'2026-05-25 22:36:48','2026-05-25 22:36:48'),(2,2,'Section A',NULL,1,3,1,0.00,0,NULL,'2026-05-26 00:16:48','2026-05-26 00:17:01'),(3,2,'Section B',NULL,2,3,1,0.00,0,NULL,'2026-05-26 00:17:18','2026-05-26 00:17:28'),(4,3,'Section A',NULL,1,0,1,0.00,0,NULL,'2026-05-26 10:01:23','2026-05-26 10:01:23'),(5,4,'Part 1',NULL,1,0,1,0.00,0,NULL,'2026-05-27 22:18:19','2026-05-27 22:18:19');
/*!40000 ALTER TABLE `exam_sections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `exams`
--

DROP TABLE IF EXISTS `exams`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `exams` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `course_id` bigint unsigned NOT NULL,
  `subject_id` bigint unsigned DEFAULT NULL,
  `chapter_id` bigint unsigned DEFAULT NULL,
  `lesson_id` bigint unsigned DEFAULT NULL,
  `created_by` bigint unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `template` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'default',
  `status` enum('draft','published','active','completed','archived') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `total_marks` int NOT NULL DEFAULT '0',
  `passing_marks` int NOT NULL DEFAULT '0',
  `duration_minutes` int DEFAULT NULL,
  `total_questions` int NOT NULL DEFAULT '0',
  `shuffle_questions` tinyint(1) NOT NULL DEFAULT '0',
  `show_result_immediately` tinyint(1) NOT NULL DEFAULT '1',
  `allow_multiple_attempts` tinyint(1) NOT NULL DEFAULT '0',
  `max_attempts` int DEFAULT NULL,
  `start_time` timestamp NULL DEFAULT NULL,
  `end_time` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `exams_course_id_foreign` (`course_id`),
  KEY `exams_subject_id_foreign` (`subject_id`),
  KEY `exams_chapter_id_foreign` (`chapter_id`),
  KEY `exams_lesson_id_foreign` (`lesson_id`),
  KEY `exams_created_by_foreign` (`created_by`),
  KEY `exams_tenant_id_course_id_index` (`tenant_id`,`course_id`),
  KEY `exams_status_start_time_index` (`status`,`start_time`),
  CONSTRAINT `exams_chapter_id_foreign` FOREIGN KEY (`chapter_id`) REFERENCES `chapters` (`id`) ON DELETE CASCADE,
  CONSTRAINT `exams_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `exams_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `exams_lesson_id_foreign` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`) ON DELETE CASCADE,
  CONSTRAINT `exams_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `exams_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exams`
--

LOCK TABLES `exams` WRITE;
/*!40000 ALTER TABLE `exams` DISABLE KEYS */;
INSERT INTO `exams` VALUES (1,1,1,1,1,1,2,'Test 1','test 1','default','draft',4,0,60,4,1,1,1,3,'2026-05-26 04:04:00','2026-05-28 04:04:00','2026-05-25 22:34:49','2026-05-25 23:50:19','2026-05-25 23:50:19'),(2,1,1,1,1,1,2,'Test 1',NULL,'default','published',9,0,60,9,0,1,0,1,NULL,NULL,'2026-05-25 23:50:42','2026-05-26 00:17:28',NULL),(3,1,1,1,1,1,3,'test 3',NULL,'default','draft',0,0,120,0,1,1,1,3,NULL,NULL,'2026-05-26 09:59:02','2026-05-26 09:59:02',NULL),(4,1,1,1,NULL,NULL,3,'Test524',NULL,'default','published',1,0,60,1,0,1,0,12,'2026-03-01 03:47:00','2026-03-14 03:48:00','2026-05-27 22:18:08','2026-05-27 22:19:12',NULL);
/*!40000 ALTER TABLE `exams` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `landing_page_settings`
--

DROP TABLE IF EXISTS `landing_page_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `landing_page_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `template` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'modern',
  `primary_color` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#10b981',
  `secondary_color` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#3b82f6',
  `accent_color` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#f59e0b',
  `font_family` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Inter',
  `hero_section` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `features_section` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `courses_section` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `stats_section` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `testimonials_section` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `cta_section` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `footer_section` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `show_navbar` tinyint(1) NOT NULL DEFAULT '1',
  `show_footer` tinyint(1) NOT NULL DEFAULT '1',
  `custom_css` text COLLATE utf8mb4_unicode_ci,
  `custom_js` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `landing_page_settings_tenant_id_unique` (`tenant_id`),
  CONSTRAINT `landing_page_settings_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `landing_page_settings_chk_1` CHECK (json_valid(`hero_section`)),
  CONSTRAINT `landing_page_settings_chk_2` CHECK (json_valid(`features_section`)),
  CONSTRAINT `landing_page_settings_chk_3` CHECK (json_valid(`courses_section`)),
  CONSTRAINT `landing_page_settings_chk_4` CHECK (json_valid(`stats_section`)),
  CONSTRAINT `landing_page_settings_chk_5` CHECK (json_valid(`testimonials_section`)),
  CONSTRAINT `landing_page_settings_chk_6` CHECK (json_valid(`cta_section`)),
  CONSTRAINT `landing_page_settings_chk_7` CHECK (json_valid(`footer_section`))
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `landing_page_settings`
--

LOCK TABLES `landing_page_settings` WRITE;
/*!40000 ALTER TABLE `landing_page_settings` DISABLE KEYS */;
INSERT INTO `landing_page_settings` VALUES (1,1,'modern','#10b981','#3b82f6','#f59e0b','Inter','{\"enabled\":\"1\",\"title\":\"Welcome to Our Academy\",\"subtitle\":\"Learn. Grow. Succeed.\",\"description\":\"Transform your future with our world-class courses and expert instructors.\",\"cta_text\":\"Explore Courses\",\"cta_link\":\"#courses\"}','{\"enabled\":\"1\",\"title\":\"Why Choose Us?\",\"subtitle\":\"We provide the best learning experience\"}','{\"enabled\":\"1\",\"title\":\"Our Popular Courses\",\"show_courses\":\"6\"}','{\"enabled\":\"1\"}','{\"enabled\":true,\"title\":\"What Our Students Say\",\"subtitle\":\"Real stories from real learners\",\"testimonials\":[{\"name\":\"John Doe\",\"role\":\"Web Developer\",\"image\":null,\"content\":\"This academy changed my life. The courses are well-structured and the instructors are amazing!\",\"rating\":5},{\"name\":\"Jane Smith\",\"role\":\"Data Scientist\",\"image\":null,\"content\":\"I learned so much in such a short time. Highly recommend to anyone looking to upskill.\",\"rating\":5}]}','{\"enabled\":\"1\",\"title\":\"Ready to Start Learning?\",\"description\":\"Join thousands of students and start your journey today.\",\"button_text\":\"Get Started Now\"}','{\"social_links\":{\"facebook\":\"\",\"twitter\":\"\",\"instagram\":\"\",\"linkedin\":\"\",\"youtube\":\"\"},\"copyright\":\"\\u00a9 2026 All rights reserved.\",\"contact_info\":{\"email\":\"contact@academy.com\",\"phone\":\"+1 234 567 890\",\"address\":\"123 Education Street, Learning City\"},\"quick_links\":[{\"text\":\"About Us\",\"url\":\"\\/about\"},{\"text\":\"Courses\",\"url\":\"\\/courses\"},{\"text\":\"Contact\",\"url\":\"\\/contact\"},{\"text\":\"Blog\",\"url\":\"\\/blog\"}]}',1,1,NULL,NULL,'2026-05-28 11:02:17','2026-05-28 11:03:32');
/*!40000 ALTER TABLE `landing_page_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lessons`
--

DROP TABLE IF EXISTS `lessons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lessons` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `chapter_id` bigint unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `video_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `video_type` enum('youtube','vimeo','other') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `duration_minutes` int DEFAULT NULL,
  `order` int NOT NULL DEFAULT '0',
  `status` enum('active','inactive','draft') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lessons_chapter_id_foreign` (`chapter_id`),
  CONSTRAINT `lessons_chapter_id_foreign` FOREIGN KEY (`chapter_id`) REFERENCES `chapters` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lessons`
--

LOCK TABLES `lessons` WRITE;
/*!40000 ALTER TABLE `lessons` DISABLE KEYS */;
INSERT INTO `lessons` VALUES (1,1,'Part 1',NULL,NULL,'youtube',NULL,0,'active','2026-05-23 05:39:36','2026-05-23 05:39:36',NULL),(2,1,'Lession 2',NULL,NULL,'youtube',NULL,0,'active','2026-05-23 06:17:30','2026-05-23 06:17:30',NULL);
/*!40000 ALTER TABLE `lessons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `live_class_attendance`
--

DROP TABLE IF EXISTS `live_class_attendance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `live_class_attendance` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `live_class_id` bigint unsigned NOT NULL,
  `student_id` bigint unsigned NOT NULL,
  `joined_at` timestamp NOT NULL,
  `left_at` timestamp NULL DEFAULT NULL,
  `duration_seconds` int NOT NULL DEFAULT '0',
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `device_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `browser` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `os` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jitsi_participant_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `display_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `was_kicked` tinyint(1) NOT NULL DEFAULT '0',
  `kick_reason` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `live_class_attendance_student_id_foreign` (`student_id`),
  KEY `live_class_attendance_live_class_id_student_id_index` (`live_class_id`,`student_id`),
  KEY `live_class_attendance_tenant_id_live_class_id_index` (`tenant_id`,`live_class_id`),
  KEY `live_class_attendance_joined_at_index` (`joined_at`),
  CONSTRAINT `live_class_attendance_live_class_id_foreign` FOREIGN KEY (`live_class_id`) REFERENCES `live_classes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `live_class_attendance_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `live_class_attendance_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `live_class_attendance`
--

LOCK TABLES `live_class_attendance` WRITE;
/*!40000 ALTER TABLE `live_class_attendance` DISABLE KEYS */;
INSERT INTO `live_class_attendance` VALUES (1,5,7,18,'2026-06-01 10:45:04','2026-06-01 12:07:02',-4918,'172.18.0.1','mobile','Chrome','Linux',NULL,'Sourav Barui',0,NULL,'2026-06-01 10:45:04','2026-06-01 12:07:02'),(2,5,8,18,'2026-06-01 12:27:42','2026-06-01 12:27:51',-10,'172.18.0.1','mobile','Chrome','Linux',NULL,'Sourav Barui',0,NULL,'2026-06-01 12:27:42','2026-06-01 12:27:51'),(3,5,9,18,'2026-06-01 12:28:18',NULL,0,'172.18.0.1','mobile','Chrome','Linux',NULL,'Sourav Barui',0,NULL,'2026-06-01 12:28:18','2026-06-01 12:28:18'),(4,5,11,18,'2026-06-01 14:08:27',NULL,0,'172.18.0.1','mobile','Chrome','Linux',NULL,'Sourav Barui',0,NULL,'2026-06-01 14:08:27','2026-06-01 14:08:27'),(5,5,7,18,'2026-06-01 15:09:18',NULL,0,'172.18.0.1','desktop','Chrome','Windows',NULL,'Sourav Barui',0,NULL,'2026-06-01 15:09:18','2026-06-01 15:09:18'),(6,5,14,18,'2026-06-01 18:21:03','2026-06-01 18:29:24',-501,'172.18.0.1','desktop','Chrome','Windows',NULL,'Sourav Barui',0,NULL,'2026-06-01 18:21:03','2026-06-01 18:29:24'),(7,5,15,18,'2026-06-01 18:32:32',NULL,0,'172.18.0.1','desktop','Chrome','Windows',NULL,'Sourav Barui',0,NULL,'2026-06-01 18:32:32','2026-06-01 18:32:32');
/*!40000 ALTER TABLE `live_class_attendance` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `live_classes`
--

DROP TABLE IF EXISTS `live_classes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `live_classes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `course_id` bigint unsigned NOT NULL,
  `subject_id` bigint unsigned DEFAULT NULL,
  `chapter_id` bigint unsigned DEFAULT NULL,
  `lesson_id` bigint unsigned DEFAULT NULL,
  `created_by` bigint unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `platform` enum('google_meet','zoom','ms_teams','jitsi','other') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'google_meet',
  `is_btlive` tinyint(1) NOT NULL DEFAULT '0',
  `btlive_room_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meeting_url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `video_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meeting_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meeting_password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `scheduled_at` datetime NOT NULL,
  `duration_minutes` int NOT NULL DEFAULT '60',
  `status` enum('scheduled','live','completed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'scheduled',
  `is_public` tinyint(1) NOT NULL DEFAULT '0',
  `recurrence` enum('none','daily','weekly') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'none',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `btlive_room_password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `btlive_recording_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `btlive_recording_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `btlive_recording_status` enum('pending','recording','processing','completed','failed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `btlive_lobby_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `btlive_waiting_room_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `btlive_chat_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `btlive_teacher_only_video` tinyint(1) NOT NULL DEFAULT '1',
  `btlive_teacher_only_audio` tinyint(1) NOT NULL DEFAULT '1',
  `btlive_attendance_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `btlive_jwt_required` tinyint(1) NOT NULL DEFAULT '1',
  `btlive_started_at` timestamp NULL DEFAULT NULL,
  `btlive_ended_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `live_classes_tenant_id_foreign` (`tenant_id`),
  KEY `live_classes_course_id_foreign` (`course_id`),
  KEY `live_classes_created_by_foreign` (`created_by`),
  KEY `live_classes_subject_id_foreign` (`subject_id`),
  KEY `live_classes_chapter_id_foreign` (`chapter_id`),
  KEY `live_classes_lesson_id_foreign` (`lesson_id`),
  CONSTRAINT `live_classes_chapter_id_foreign` FOREIGN KEY (`chapter_id`) REFERENCES `chapters` (`id`) ON DELETE SET NULL,
  CONSTRAINT `live_classes_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `live_classes_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `live_classes_lesson_id_foreign` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`) ON DELETE SET NULL,
  CONSTRAINT `live_classes_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE SET NULL,
  CONSTRAINT `live_classes_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `live_classes`
--

LOCK TABLES `live_classes` WRITE;
/*!40000 ALTER TABLE `live_classes` DISABLE KEYS */;
INSERT INTO `live_classes` VALUES (1,1,1,1,1,1,2,'Class Now',NULL,'google_meet',0,NULL,'https://meet.google.com/qbr-qfji-uve',NULL,NULL,NULL,'2026-05-25 17:10:00',120,'scheduled',0,'none','2026-05-24 06:11:06','2026-05-24 06:11:06',NULL,NULL,NULL,NULL,'pending',1,1,1,1,1,1,1,NULL,NULL),(2,1,5,5,NULL,NULL,2,'Test',NULL,'google_meet',0,NULL,'https://meet.google.com/qbr-qfji-uve','https://www.youtube.com/watch?v=QqI5pMJuFbE',NULL,NULL,'2026-05-28 12:58:00',60,'completed',0,'none','2026-05-28 06:28:19','2026-05-28 07:43:43',NULL,NULL,NULL,NULL,'pending',1,1,1,1,1,1,1,NULL,NULL),(3,1,5,5,NULL,NULL,2,'fg',NULL,'google_meet',0,NULL,'https://meet.google.com/qbr-qfji-uve',NULL,NULL,NULL,'2026-05-28 14:06:00',60,'completed',0,'none','2026-05-28 07:36:53','2026-05-28 08:03:20',NULL,NULL,NULL,NULL,'pending',1,1,1,1,1,1,1,NULL,NULL),(4,1,5,5,NULL,NULL,2,'asd',NULL,'google_meet',0,NULL,'https://meet.google.com/qbr-qfji-uve',NULL,NULL,NULL,'2026-05-28 15:37:00',60,'live',1,'none','2026-05-28 09:07:11','2026-05-28 09:07:35',NULL,NULL,NULL,NULL,'pending',1,1,1,1,1,1,1,NULL,NULL),(5,5,6,6,3,NULL,16,'Live',NULL,'google_meet',0,NULL,'https://meet.google.com/poi-fnst-dix',NULL,NULL,NULL,'2026-05-30 20:11:00',60,'scheduled',0,'none','2026-05-30 19:11:12','2026-05-30 19:12:10','2026-05-30 19:12:10',NULL,NULL,NULL,'pending',1,1,1,1,1,1,1,NULL,NULL),(6,5,6,6,3,NULL,16,'test',NULL,'google_meet',0,NULL,'https://meet.google.com/qbr-qfji-uve',NULL,NULL,NULL,'2026-06-01 09:19:00',60,'scheduled',0,'none','2026-06-01 08:19:19','2026-06-01 08:22:28','2026-06-01 08:22:28',NULL,NULL,NULL,'pending',1,1,1,1,1,1,1,NULL,NULL),(7,5,6,NULL,3,NULL,16,'maths',NULL,'jitsi',1,'btlive-ecchapuron-6-7-7f07daf3','https://live.ecchapuron.btguru.tech/btlive-ecchapuron-6-7-7f07daf3',NULL,NULL,NULL,'2026-06-01 10:44:00',60,'completed',1,'none','2026-06-01 10:42:36','2026-06-01 18:29:38',NULL,NULL,NULL,NULL,'pending',1,1,1,1,1,1,1,'2026-06-01 10:42:39','2026-06-01 12:07:02'),(8,5,6,NULL,3,NULL,16,'Test',NULL,'jitsi',1,'btlive-ecchapuron-6-8-2b6d8b92','https://live.ecchapuron.btguru.tech/btlive-ecchapuron-6-8-2b6d8b92',NULL,NULL,NULL,'2026-06-01 12:09:00',60,'completed',0,'none','2026-06-01 12:08:44','2026-06-01 12:27:51',NULL,NULL,NULL,NULL,'pending',1,1,1,1,1,1,0,'2026-06-01 12:08:45','2026-06-01 12:27:51'),(9,5,6,NULL,NULL,NULL,16,'cool',NULL,'jitsi',1,'btlive-ecchapuron-6-9-1fbbbe79','https://live.ecchapuron.btguru.tech/btlive-ecchapuron-6-9-1fbbbe79',NULL,NULL,NULL,'2026-06-01 12:29:00',60,'completed',1,'none','2026-06-01 12:28:05','2026-06-01 14:07:47',NULL,NULL,NULL,NULL,'pending',1,1,1,1,1,1,1,'2026-06-01 12:28:06',NULL),(10,5,6,NULL,3,NULL,16,'test 4',NULL,'jitsi',1,'btlive-ecchapuron-6-10-823df4e0','https://live.ecchapuron.btguru.tech/btlive-ecchapuron-6-10-823df4e0',NULL,NULL,NULL,'2026-06-01 14:06:00',60,'scheduled',1,'none','2026-06-01 14:05:37','2026-06-01 14:05:37',NULL,NULL,NULL,NULL,'pending',1,1,1,1,1,1,1,NULL,NULL),(11,5,6,NULL,NULL,NULL,16,'maths','maths','jitsi',1,'btlive-ecchapuron-6-11-fd282b19','https://live.ecchapuron.btguru.tech/btlive-ecchapuron-6-11-fd282b19',NULL,NULL,NULL,'2026-06-01 14:09:00',60,'completed',1,'none','2026-06-01 14:08:04','2026-06-01 14:54:39',NULL,NULL,NULL,NULL,'pending',1,1,1,1,1,1,1,'2026-06-01 14:08:07',NULL),(12,5,6,NULL,NULL,NULL,16,'Test 6',NULL,'jitsi',1,'btlive-ecchapuron-6-12-7e49f1ad','https://live.ecchapuron.btguru.tech/btlive-ecchapuron-6-12-7e49f1ad',NULL,NULL,NULL,'2026-06-01 14:55:00',60,'completed',1,'none','2026-06-01 14:54:53','2026-06-01 14:56:27',NULL,NULL,NULL,NULL,'pending',1,1,1,1,1,1,1,'2026-06-01 14:54:54',NULL),(13,5,6,NULL,3,NULL,16,'Test 7',NULL,'jitsi',1,'btlive-ecchapuron-6-13-46948ec5','https://live.ecchapuron.btguru.tech/btlive-ecchapuron-6-13-46948ec5',NULL,NULL,NULL,'2026-06-01 14:58:00',60,'completed',1,'none','2026-06-01 14:57:06','2026-06-01 14:58:37',NULL,NULL,NULL,NULL,'pending',1,1,1,1,1,1,1,'2026-06-01 14:57:08','2026-06-01 14:58:37'),(14,5,6,NULL,3,NULL,16,'test 8',NULL,'jitsi',1,'btlive-ecchapuron-6-14-33b054eb','https://live.ecchapuron.btguru.tech/btlive-ecchapuron-6-14-33b054eb',NULL,NULL,NULL,'2026-06-01 16:17:00',60,'completed',1,'none','2026-06-01 16:16:04','2026-06-01 18:29:24',NULL,NULL,NULL,NULL,'pending',1,1,1,1,1,1,1,'2026-06-01 16:16:06','2026-06-01 18:29:24'),(15,5,6,NULL,NULL,NULL,16,'ffdfd',NULL,'jitsi',1,'btlive-ecchapuron-6-15-370fc493','https://live.ecchapuron.btguru.tech/btlive-ecchapuron-6-15-370fc493',NULL,NULL,NULL,'2026-06-01 18:33:00',60,'completed',1,'none','2026-06-01 18:32:15','2026-06-01 18:52:05',NULL,NULL,NULL,NULL,'pending',1,1,1,1,1,1,1,'2026-06-01 18:32:23',NULL);
/*!40000 ALTER TABLE `live_classes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_tenants_table',1),(2,'0001_01_01_000001_create_users_table',1),(3,'0001_01_01_000002_create_cache_table',1),(4,'0001_01_01_000003_create_jobs_table',1),(5,'2024_01_10_000001_create_courses_table',1),(6,'2024_01_10_000002_create_course_teacher_table',1),(7,'2024_01_10_000003_create_enrollments_table',1),(8,'2024_01_10_000004_create_notices_table',1),(9,'2024_01_10_000005_create_permission_tables',1),(10,'2024_01_10_000006_create_personal_access_tokens_table',1),(11,'2025_05_23_000001_create_curricula_table',2),(12,'2025_05_23_000002_create_subjects_table',2),(13,'2025_05_23_000003_create_chapters_table',2),(14,'2025_05_23_000004_create_lessons_table',2),(15,'2025_05_23_000005_create_curriculum_contents_table',2),(16,'2025_05_23_000006_add_user_id_to_curriculum_tables',3),(17,'2024_01_10_000010_add_fees_type_to_courses_table',4),(18,'2025_05_24_000001_add_monthly_fee_settings_and_subscriptions',5),(19,'2025_05_24_000002_add_start_date_to_courses_table',6),(20,'2025_05_24_000003_add_end_date_to_courses_table',7),(21,'2025_05_24_000004_create_payment_requests_table',8),(22,'2025_05_24_000005_create_live_classes_table',9),(23,'2025_05_24_000006_add_level_ids_to_live_classes_table',10),(24,'2025_05_26_000001_create_exams_table',11),(25,'2025_05_26_100001_create_notifications_table',12),(26,'2025_05_26_200001_create_tenant_registrations_table',13),(27,'2025_05_26_300001_create_system_settings_table',14),(28,'2026_05_27_020950_create_coupons_table',15),(29,'2026_05_27_020950_create_subscription_plans_table',15),(30,'2026_05_27_020950_create_subscriptions_table',15),(31,'2026_05_27_023627_create_payments_table',16),(32,'2026_05_27_025507_create_coupon_plan_table',17),(33,'2026_05_27_050000_add_session_tracking_to_users',18),(34,'2026_05_27_060000_add_branding_fields_to_tenants',19),(35,'2026_05_27_180000_create_monthly_fees_table',20),(36,'2025_05_28_000001_add_metadata_to_payment_requests',21),(37,'2025_05_28_000002_change_access_dates_to_datetime',22),(38,'2025_05_28_000003_add_video_url_to_live_classes',23),(39,'2025_05_28_000004_add_is_public_to_live_classes',24),(40,'2025_05_28_000005_add_is_downloadable_to_curriculum_notes',25),(42,'2025_05_28_000006_add_tenant_id_to_curriculum_notes',26),(43,'2025_05_28_000007_create_landing_page_settings_table',26),(44,'2025_05_28_000008_create_blogs_table',26),(45,'2026_05_29_193241_create_books_table',27),(46,'2026_05_29_193315_create_book_orders_table',27),(47,'2026_05_29_200625_add_book_id_to_payment_requests',28),(48,'2026_05_29_201555_make_course_id_nullable_in_payment_requests',29),(49,'2026_05_29_201850_update_payment_type_enum_in_payment_requests',30),(50,'2025_05_31_000001_add_btlive_fields_to_live_classes',31),(51,'2025_05_31_000002_create_live_class_attendance_table',31),(53,'2024_06_01_000001_add_btlive_settings_to_tenants',32),(54,'2024_06_01_000002_add_approval_to_btlive_recordings',32),(55,'2024_06_01_000000_create_btlive_recordings_table',33);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_permissions`
--

LOCK TABLES `model_has_permissions` WRITE;
/*!40000 ALTER TABLE `model_has_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `model_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_roles`
--

DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_roles`
--

LOCK TABLES `model_has_roles` WRITE;
/*!40000 ALTER TABLE `model_has_roles` DISABLE KEYS */;
INSERT INTO `model_has_roles` VALUES (1,'App\\Models\\User',1),(2,'App\\Models\\User',2),(3,'App\\Models\\User',3),(3,'App\\Models\\User',4),(3,'App\\Models\\User',5),(4,'App\\Models\\User',6),(4,'App\\Models\\User',7),(4,'App\\Models\\User',8),(4,'App\\Models\\User',9),(4,'App\\Models\\User',10),(4,'App\\Models\\User',11),(1,'App\\Models\\User',12),(1,'App\\Models\\User',13),(2,'App\\Models\\User',14),(4,'App\\Models\\User',15),(2,'App\\Models\\User',16),(4,'App\\Models\\User',17),(4,'App\\Models\\User',18),(2,'App\\Models\\User',19),(2,'App\\Models\\User',20),(4,'App\\Models\\User',21),(4,'App\\Models\\User',22);
/*!40000 ALTER TABLE `model_has_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `monthly_fees`
--

DROP TABLE IF EXISTS `monthly_fees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `monthly_fees` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `enrollment_id` bigint unsigned NOT NULL,
  `student_id` bigint unsigned NOT NULL,
  `year` year NOT NULL,
  `month` tinyint NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` enum('pending','paid','overdue') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `paid_at` timestamp NULL DEFAULT NULL,
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transaction_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `monthly_fees_enrollment_id_year_month_unique` (`enrollment_id`,`year`,`month`),
  KEY `monthly_fees_student_id_foreign` (`student_id`),
  KEY `monthly_fees_tenant_id_student_id_status_index` (`tenant_id`,`student_id`,`status`),
  CONSTRAINT `monthly_fees_enrollment_id_foreign` FOREIGN KEY (`enrollment_id`) REFERENCES `enrollments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `monthly_fees_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `monthly_fees_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `monthly_fees`
--

LOCK TABLES `monthly_fees` WRITE;
/*!40000 ALTER TABLE `monthly_fees` DISABLE KEYS */;
INSERT INTO `monthly_fees` VALUES (1,1,1,6,2026,5,2500.00,'pending',NULL,NULL,NULL,'2026-05-27 12:50:51','2026-05-27 12:50:51'),(2,1,1,6,2026,6,2500.00,'pending',NULL,NULL,NULL,'2026-05-27 12:50:51','2026-05-27 12:50:51'),(3,1,1,6,2026,7,2500.00,'pending',NULL,NULL,NULL,'2026-05-27 12:50:51','2026-05-27 12:50:51'),(4,1,1,6,2026,8,2500.00,'pending',NULL,NULL,NULL,'2026-05-27 12:50:51','2026-05-27 12:50:51'),(5,1,1,6,2026,9,2500.00,'pending',NULL,NULL,NULL,'2026-05-27 12:50:51','2026-05-27 12:50:51'),(6,1,1,6,2026,10,2500.00,'pending',NULL,NULL,NULL,'2026-05-27 12:50:51','2026-05-27 12:50:51'),(7,1,2,6,2026,5,3000.00,'pending',NULL,NULL,NULL,'2026-05-27 12:50:51','2026-05-27 12:50:51'),(8,1,2,6,2026,6,3000.00,'pending',NULL,NULL,NULL,'2026-05-27 12:50:51','2026-05-27 12:50:51'),(9,1,2,6,2026,7,3000.00,'pending',NULL,NULL,NULL,'2026-05-27 12:50:51','2026-05-27 12:50:51'),(10,1,2,6,2026,8,3000.00,'pending',NULL,NULL,NULL,'2026-05-27 12:50:51','2026-05-27 12:50:51'),(11,1,2,6,2026,9,3000.00,'pending',NULL,NULL,NULL,'2026-05-27 12:50:51','2026-05-27 12:50:51'),(12,1,2,6,2026,10,3000.00,'pending',NULL,NULL,NULL,'2026-05-27 12:50:51','2026-05-27 12:50:51'),(13,1,3,7,2026,5,2500.00,'pending',NULL,NULL,NULL,'2026-05-27 12:50:51','2026-05-27 12:50:51'),(14,1,3,7,2026,6,2500.00,'pending',NULL,NULL,NULL,'2026-05-27 12:50:51','2026-05-27 12:50:51'),(15,1,3,7,2026,7,2500.00,'pending',NULL,NULL,NULL,'2026-05-27 12:50:51','2026-05-27 12:50:51'),(16,1,3,7,2026,8,2500.00,'pending',NULL,NULL,NULL,'2026-05-27 12:50:51','2026-05-27 12:50:51'),(17,1,3,7,2026,9,2500.00,'pending',NULL,NULL,NULL,'2026-05-27 12:50:51','2026-05-27 12:50:51'),(18,1,3,7,2026,10,2500.00,'pending',NULL,NULL,NULL,'2026-05-27 12:50:51','2026-05-27 12:50:51'),(19,1,5,8,2026,5,3000.00,'pending',NULL,NULL,NULL,'2026-05-27 12:50:51','2026-05-27 12:50:51'),(20,1,5,8,2026,6,3000.00,'pending',NULL,NULL,NULL,'2026-05-27 12:50:51','2026-05-27 12:50:51'),(21,1,5,8,2026,7,3000.00,'pending',NULL,NULL,NULL,'2026-05-27 12:50:51','2026-05-27 12:50:51'),(22,1,5,8,2026,8,3000.00,'pending',NULL,NULL,NULL,'2026-05-27 12:50:51','2026-05-27 12:50:51'),(23,1,5,8,2026,9,3000.00,'pending',NULL,NULL,NULL,'2026-05-27 12:50:51','2026-05-27 12:50:51'),(24,1,5,8,2026,10,3000.00,'pending',NULL,NULL,NULL,'2026-05-27 12:50:51','2026-05-27 12:50:51'),(25,1,6,9,2026,5,2666.67,'pending',NULL,NULL,NULL,'2026-05-27 12:50:51','2026-05-27 12:50:51'),(26,1,6,9,2026,6,2666.67,'pending',NULL,NULL,NULL,'2026-05-27 12:50:51','2026-05-27 12:50:51'),(27,1,6,9,2026,7,2666.67,'pending',NULL,NULL,NULL,'2026-05-27 12:50:51','2026-05-27 12:50:51'),(28,1,6,9,2026,8,2666.67,'pending',NULL,NULL,NULL,'2026-05-27 12:50:51','2026-05-27 12:50:51'),(29,1,6,9,2026,9,2666.67,'pending',NULL,NULL,NULL,'2026-05-27 12:50:51','2026-05-27 12:50:51'),(30,1,6,9,2026,10,2666.67,'pending',NULL,NULL,NULL,'2026-05-27 12:50:51','2026-05-27 12:50:51'),(31,1,9,6,2026,5,166.67,'pending',NULL,NULL,NULL,'2026-05-27 12:50:51','2026-05-27 12:50:51'),(32,1,9,6,2026,6,166.67,'pending',NULL,NULL,NULL,'2026-05-27 12:50:51','2026-05-27 12:50:51'),(33,1,9,6,2026,7,166.67,'pending',NULL,NULL,NULL,'2026-05-27 12:50:51','2026-05-27 12:50:51'),(34,1,9,6,2026,8,166.67,'pending',NULL,NULL,NULL,'2026-05-27 12:50:51','2026-05-27 12:50:51'),(35,1,9,6,2026,9,166.67,'pending',NULL,NULL,NULL,'2026-05-27 12:50:51','2026-05-27 12:50:51'),(36,1,9,6,2026,10,166.67,'pending',NULL,NULL,NULL,'2026-05-27 12:50:51','2026-05-27 12:50:51'),(37,1,8,11,2026,5,1000.00,'paid','2026-05-27 22:59:18','online',NULL,'2026-05-27 22:42:59','2026-05-27 22:59:18'),(38,1,11,11,2026,5,800.00,'paid','2026-05-28 06:09:21','online',NULL,'2026-05-28 05:57:26','2026-05-28 06:09:21'),(39,1,12,11,2026,5,50.00,'paid','2026-05-28 06:23:06','online',NULL,'2026-05-28 06:15:11','2026-05-28 06:23:06'),(40,5,13,18,2026,5,1500.00,'paid','2026-05-30 14:51:18','online',NULL,'2026-05-30 14:51:18','2026-05-30 14:51:18');
/*!40000 ALTER TABLE `monthly_fees` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notices`
--

DROP TABLE IF EXISTS `notices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notices` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `created_by` bigint unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `audience` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'all',
  `course_id` bigint unsigned DEFAULT NULL,
  `publish_at` timestamp NULL DEFAULT NULL,
  `expire_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notices_created_by_foreign` (`created_by`),
  KEY `notices_course_id_foreign` (`course_id`),
  KEY `notices_tenant_id_is_active_index` (`tenant_id`,`is_active`),
  KEY `notices_tenant_id_type_index` (`tenant_id`,`type`),
  CONSTRAINT `notices_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `notices_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `notices_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notices`
--

LOCK TABLES `notices` WRITE;
/*!40000 ALTER TABLE `notices` DISABLE KEYS */;
INSERT INTO `notices` VALUES (1,1,2,'Urgent','Urgent','important','all',1,NULL,NULL,1,'2026-05-26 10:27:10','2026-05-26 10:27:10',NULL);
/*!40000 ALTER TABLE `notices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
INSERT INTO `password_reset_tokens` VALUES ('admin@btguru.in','$2y$12$sBIO2gv93lC28uBZMAs/8e8l2zjLtBzda9rAZDNOdBzefe302KSU6','2026-05-28 15:42:05');
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_requests`
--

DROP TABLE IF EXISTS `payment_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_requests` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `student_id` bigint unsigned NOT NULL,
  `course_id` bigint unsigned DEFAULT NULL,
  `book_id` bigint unsigned DEFAULT NULL,
  `enrollment_id` bigint unsigned DEFAULT NULL,
  `payment_type` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `reference_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `screenshot` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `admin_remark` text COLLATE utf8mb4_unicode_ci,
  `reviewed_by` bigint unsigned DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `month_number` int DEFAULT NULL,
  `year_number` int DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payment_requests_student_id_foreign` (`student_id`),
  KEY `payment_requests_course_id_foreign` (`course_id`),
  KEY `payment_requests_enrollment_id_foreign` (`enrollment_id`),
  KEY `payment_requests_reviewed_by_foreign` (`reviewed_by`),
  KEY `payment_requests_book_id_foreign` (`book_id`),
  KEY `payment_requests_tenant_id_book_id_index` (`tenant_id`,`book_id`),
  CONSTRAINT `payment_requests_book_id_foreign` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE SET NULL,
  CONSTRAINT `payment_requests_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payment_requests_enrollment_id_foreign` FOREIGN KEY (`enrollment_id`) REFERENCES `enrollments` (`id`) ON DELETE SET NULL,
  CONSTRAINT `payment_requests_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `payment_requests_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payment_requests_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payment_requests_chk_1` CHECK (json_valid(`metadata`))
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_requests`
--

LOCK TABLES `payment_requests` WRITE;
/*!40000 ALTER TABLE `payment_requests` DISABLE KEYS */;
INSERT INTO `payment_requests` VALUES (1,1,6,4,NULL,9,'enrollment',1000.00,'521467444224132465834678','payment_screenshots/TmoCiI23rE8AW4NX5KpAX217BCqLkm5D5IXVFDJx.jpg',NULL,'approved',NULL,2,'2026-05-24 05:35:21',5,2026,NULL,'2026-05-24 05:32:48','2026-05-24 05:35:21'),(2,1,11,1,NULL,8,'monthly',1000.00,NULL,'payment_screenshots/jxAkYgWDuRMwYzYRLn7S8yyxq6ilftp9PLs2Fa0q.png',NULL,'approved',NULL,2,'2026-05-27 22:59:17',5,2026,NULL,'2026-05-27 22:34:58','2026-05-27 22:59:18'),(3,1,11,2,NULL,NULL,'monthly',800.00,NULL,'payment_screenshots/K3vxiTMTAGxG9vUNwDZBhJKRHEI8s555ovzqdeXc.png',NULL,'rejected','g',2,'2026-05-28 06:12:19',NULL,NULL,NULL,'2026-05-27 23:54:02','2026-05-28 06:12:19'),(4,1,11,2,NULL,NULL,'monthly',800.00,NULL,'payment_screenshots/6z8URnUdViWC5cvU5q1Nq3VQQrAJGdkS33ToH6Pc.png',NULL,'rejected','go',2,'2026-05-28 05:56:39',NULL,NULL,NULL,'2026-05-28 00:18:41','2026-05-28 05:56:39'),(5,1,11,2,NULL,NULL,'monthly',800.00,NULL,'payment_screenshots/JtKxEddlm8XGBsILdwthIewL4ZcRhoKK1TgpiDDt.png',NULL,'rejected','h',2,'2026-05-28 06:11:55',NULL,NULL,NULL,'2026-05-28 05:57:14','2026-05-28 06:11:55'),(6,1,11,5,NULL,12,'monthly',50.00,NULL,'payment_screenshots/bEV3vI9aWSVy3gb02nHKLEDoOYUvYoIdhRMlF8WO.png',NULL,'approved',NULL,2,'2026-05-28 06:23:06',NULL,NULL,NULL,'2026-05-28 06:15:01','2026-05-28 06:23:06'),(7,1,6,NULL,2,NULL,'book_purchase',499.97,NULL,'payment_screenshots/AnhY9NkI44Vs9ZNTDZiAQqkLcJcbtIUT6QmkRFEY.png','Book: Test 2 phy\nOrder Type: Physical\nDelivery Address: dfsdfsdf\nContact: 78924785412','approved',NULL,2,'2026-05-29 14:59:33',NULL,NULL,'{\"order_type\":\"physical\",\"pdf_price\":0,\"physical_price\":\"499.97\",\"delivery_address\":\"dfsdfsdf\",\"delivery_phone\":\"78924785412\"}','2026-05-29 14:49:44','2026-05-29 14:59:33'),(8,5,18,6,NULL,13,'monthly',1500.00,NULL,'payment_screenshots/X2nghjHs7FxJtEGIiqYACB8tpFs12RuApbgfhrLh.jpg',NULL,'approved',NULL,16,'2026-05-30 14:51:18',NULL,NULL,NULL,'2026-05-30 14:50:53','2026-05-30 14:51:18');
/*!40000 ALTER TABLE `payment_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `subscription_id` bigint unsigned NOT NULL,
  `tenant_id` bigint unsigned NOT NULL,
  `payment_method` enum('razorpay','upi_qr','manual') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'razorpay',
  `amount` decimal(10,2) NOT NULL,
  `currency` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'INR',
  `payment_status` enum('pending','processing','completed','failed','refunded') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `transaction_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `razorpay_order_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `razorpay_payment_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `razorpay_signature` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qr_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `upi_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `upi_transaction_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `screenshot_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `paid_at` timestamp NULL DEFAULT NULL,
  `failed_at` timestamp NULL DEFAULT NULL,
  `refund_amount` decimal(10,2) DEFAULT NULL,
  `refund_reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `refunded_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payments_subscription_id_payment_status_index` (`subscription_id`,`payment_status`),
  KEY `payments_tenant_id_payment_status_index` (`tenant_id`,`payment_status`),
  KEY `payments_transaction_id_index` (`transaction_id`),
  CONSTRAINT `payments_subscription_id_foreign` FOREIGN KEY (`subscription_id`) REFERENCES `subscriptions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payments_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
INSERT INTO `payments` VALUES (1,4,1,'upi_qr',500.00,'INR','processing','32746801368',NULL,NULL,NULL,NULL,'','32746801368','payment_screenshots/ddizQmUB5jze8foJqumNeWJDXHnmdnqNB5rFW5Oe.jpg',NULL,NULL,NULL,NULL,NULL,NULL,'2026-05-26 21:31:22','2026-05-26 21:31:22',NULL);
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'manage_tenants','web','2026-05-23 04:57:21','2026-05-23 04:57:21'),(2,'manage_all_users','web','2026-05-23 04:57:21','2026-05-23 04:57:21'),(3,'view_analytics','web','2026-05-23 04:57:21','2026-05-23 04:57:21'),(4,'manage_plans','web','2026-05-23 04:57:21','2026-05-23 04:57:21'),(5,'manage_domains','web','2026-05-23 04:57:21','2026-05-23 04:57:21'),(6,'suspend_tenants','web','2026-05-23 04:57:21','2026-05-23 04:57:21'),(7,'manage_courses','web','2026-05-23 04:57:21','2026-05-23 04:57:21'),(8,'manage_teachers','web','2026-05-23 04:57:21','2026-05-23 04:57:21'),(9,'manage_students','web','2026-05-23 04:57:21','2026-05-23 04:57:21'),(10,'manage_enrollments','web','2026-05-23 04:57:22','2026-05-23 04:57:22'),(11,'manage_fees','web','2026-05-23 04:57:22','2026-05-23 04:57:22'),(12,'manage_notices','web','2026-05-23 04:57:22','2026-05-23 04:57:22'),(13,'view_tenant_reports','web','2026-05-23 04:57:22','2026-05-23 04:57:22'),(14,'manage_tenant_settings','web','2026-05-23 04:57:22','2026-05-23 04:57:22'),(15,'approve_admissions','web','2026-05-23 04:57:22','2026-05-23 04:57:22'),(16,'view_assigned_courses','web','2026-05-23 04:57:22','2026-05-23 04:57:22'),(17,'view_course_students','web','2026-05-23 04:57:22','2026-05-23 04:57:22'),(18,'manage_attendance','web','2026-05-23 04:57:22','2026-05-23 04:57:22'),(19,'upload_materials','web','2026-05-23 04:57:22','2026-05-23 04:57:22'),(20,'view_teacher_dashboard','web','2026-05-23 04:57:22','2026-05-23 04:57:22'),(21,'view_enrolled_courses','web','2026-05-23 04:57:22','2026-05-23 04:57:22'),(22,'view_notices','web','2026-05-23 04:57:22','2026-05-23 04:57:22'),(23,'view_fee_status','web','2026-05-23 04:57:22','2026-05-23 04:57:22'),(24,'view_attendance','web','2026-05-23 04:57:22','2026-05-23 04:57:22'),(25,'view_student_dashboard','web','2026-05-23 04:57:22','2026-05-23 04:57:22'),(26,'access_course_materials','web','2026-05-23 04:57:22','2026-05-23 04:57:22');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_has_permissions`
--

DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_has_permissions`
--

LOCK TABLES `role_has_permissions` WRITE;
/*!40000 ALTER TABLE `role_has_permissions` DISABLE KEYS */;
INSERT INTO `role_has_permissions` VALUES (1,1),(2,1),(3,1),(4,1),(5,1),(6,1),(7,2),(8,2),(9,2),(10,2),(11,2),(12,2),(13,2),(14,2),(15,2),(16,3),(17,3),(18,3),(19,3),(20,3),(21,4),(22,4),(23,4),(24,4),(25,4),(26,4);
/*!40000 ALTER TABLE `role_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'super_admin','web','2026-05-23 04:57:22','2026-05-23 04:57:22'),(2,'tenant_admin','web','2026-05-23 04:57:22','2026-05-23 04:57:22'),(3,'teacher','web','2026-05-23 04:57:22','2026-05-23 04:57:22'),(4,'student','web','2026-05-23 04:57:22','2026-05-23 04:57:22');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('bK40PtCXbpBYjgz2FiuVbmGrBzb4cI3WLtJ1lbp9',NULL,'161.35.114.97','Mozilla/5.0 zgrab/0.x','YTozOntzOjY6Il90b2tlbiI7czo0MDoiMDgxQjN0MmJKRElVODdBVHRjU2l3WGRFaUJucUp3MGlveVlCbkRTbCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjU6Imh0dHA6Ly8xNDUuMjIzLjE5Ljc3OjgwODAiO3M6NToicm91dGUiO3M6MTE6InRlbmFudC5ob21lIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1780371432),('GyOFKtlQ9wSjj1Rw16jgvo2KNLVmQt0hhRke8MHR',NULL,'172.18.0.1','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiTHR6anJHdkxOc0pQdzJMV0dHOExTSklKemJEQXdLS1NRM1g1MkJJQyI7czoxNzoiY3VycmVudF90ZW5hbnRfaWQiO2k6NTtzOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMDoiaHR0cHM6Ly9lY2NoYXB1cm9uLmJ0Z3VydS50ZWNoIjtzOjU6InJvdXRlIjtzOjExOiJ0ZW5hbnQuaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1780371106),('nE0vd26SPHw7R8LjwVytewQ6apnJT19310Du8siN',NULL,'172.18.0.1','Go-http-client/2.0','YToyOntzOjY6Il90b2tlbiI7czo0MDoiR2ZPU0NHalJMdEF3Wk45NGxXcUMwaDIwYTJCSHdjVnJVUkpVV0lXZiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1780367376),('QB3EzmoDnerw8S9GLBnJGHrS0O9I9rLN6lhPXPol',NULL,'172.18.0.1','Go-http-client/2.0','YToyOntzOjY6Il90b2tlbiI7czo0MDoibWd5M3V2NHoxeW5wQm9kb3M3WnFDYnNTZGVMSmZVQkU4REEzQzluSiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1780373754),('qI0JU9jQY4yZJltQAhGYG0Hft7aEF68j5UUMpmeD',NULL,'172.18.0.1','Python/3.14 aiohttp/3.13.3','YTozOntzOjY6Il90b2tlbiI7czo0MDoiUmJhNFBmelh5TFpJbGpoZWpkNXZKY3IwTG01WTRWYndzR2dMNndDMCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MTk6Imh0dHBzOi8vYnRndXJ1LnRlY2giO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1780374484);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student_notifications`
--

DROP TABLE IF EXISTS `student_notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `student_notifications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `body` text COLLATE utf8mb4_unicode_ci,
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'bell',
  `url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `student_notifications_tenant_id_foreign` (`tenant_id`),
  KEY `student_notifications_user_id_foreign` (`user_id`),
  CONSTRAINT `student_notifications_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `student_notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=69 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student_notifications`
--

LOCK TABLES `student_notifications` WRITE;
/*!40000 ALTER TABLE `student_notifications` DISABLE KEYS */;
INSERT INTO `student_notifications` VALUES (1,1,6,'exam','New Exam: Test524','A new exam has been published in Mathematics Mastery.','exam','/student/exams',1,'2026-05-27 22:19:24','2026-05-27 22:19:12','2026-05-27 22:19:24'),(2,1,7,'exam','New Exam: Test524','A new exam has been published in Mathematics Mastery.','exam','/student/exams',0,NULL,'2026-05-27 22:19:12','2026-05-27 22:19:12'),(3,1,11,'payment','Payment Verified','Your payment for Mathematics Mastery has been verified.','payment','/student/payments',1,'2026-05-27 22:36:00','2026-05-27 22:35:12','2026-05-27 22:36:00'),(4,1,11,'payment','Payment Verified','Your payment for Mathematics Mastery has been verified.','payment','/student/payments',1,'2026-05-27 22:44:58','2026-05-27 22:40:34','2026-05-27 22:44:58'),(5,1,11,'payment','Payment Verified','Your payment for Mathematics Mastery has been verified.','payment','/student/payments',1,'2026-05-27 22:43:25','2026-05-27 22:42:59','2026-05-27 22:43:25'),(6,1,11,'payment','Payment Verified','Your payment for Mathematics Mastery has been verified.','payment','/student/payments',1,'2026-05-27 23:01:17','2026-05-27 22:59:18','2026-05-27 23:01:17'),(7,1,11,'payment','Payment Verified','Your payment for Physics Fundamentals has been verified.','payment','/student/payments',0,NULL,'2026-05-27 23:54:15','2026-05-27 23:54:15'),(8,1,11,'payment','Payment Verified','Your payment for Physics Fundamentals has been verified.','payment','/student/payments',1,'2026-05-28 00:17:41','2026-05-27 23:57:05','2026-05-28 00:17:41'),(9,1,11,'payment','Payment Verified','Your payment for Physics Fundamentals has been verified.','payment','/student/payments',0,NULL,'2026-05-28 00:18:49','2026-05-28 00:18:49'),(10,1,11,'payment','Payment Verified','Your payment for Physics Fundamentals has been verified.','payment','/student/payments',0,NULL,'2026-05-28 05:57:26','2026-05-28 05:57:26'),(11,1,11,'payment','Payment Verified','Your payment for Physics Fundamentals has been verified.','payment','/student/payments',0,NULL,'2026-05-28 06:09:21','2026-05-28 06:09:21'),(12,1,11,'payment','Payment Verified','Your payment for TE has been verified.','payment','/student/payments',0,NULL,'2026-05-28 06:15:11','2026-05-28 06:15:11'),(13,1,11,'payment','Payment Verified','Your payment for TE has been verified.','payment','/student/payments',0,NULL,'2026-05-28 06:16:42','2026-05-28 06:16:42'),(14,1,11,'payment','Payment Verified','Your payment for TE has been verified.','payment','/student/payments',0,NULL,'2026-05-28 06:19:27','2026-05-28 06:19:27'),(15,1,11,'payment','Payment Verified','Your payment for TE has been verified.','payment','/student/payments',0,NULL,'2026-05-28 06:21:18','2026-05-28 06:21:18'),(16,1,11,'payment','Payment Verified','Your payment for TE has been verified.','payment','/student/payments',0,NULL,'2026-05-28 06:23:07','2026-05-28 06:23:07'),(17,1,11,'live_class','Live Class: Test','Scheduled on 28 May, 12:58 PM — TE','live','/student/live-classes',0,NULL,'2026-05-28 06:28:19','2026-05-28 06:28:19'),(18,1,11,'live_class','Live Class: fg','Scheduled on 28 May, 02:06 PM — TE','live','/student/live-classes',0,NULL,'2026-05-28 07:36:53','2026-05-28 07:36:53'),(19,1,11,'live_class','🔴 LIVE NOW: fg','Your live class has started! Click to join now — TE','live','https://meet.google.com/qbr-qfji-uve',1,'2026-05-28 07:38:59','2026-05-28 07:36:56','2026-05-28 07:38:59'),(20,1,11,'live_class','✅ Class Ended: Test','The live class has ended. Recorded video will be available soon — TE','video','/student/live-classes',0,NULL,'2026-05-28 07:41:56','2026-05-28 07:41:56'),(21,1,11,'live_class','🔴 LIVE NOW: Test','Your live class has started! Click to join now — TE','live','https://meet.google.com/qbr-qfji-uve',0,NULL,'2026-05-28 07:42:51','2026-05-28 07:42:51'),(22,1,11,'live_class','✅ Class Ended: Test','The live class has ended. Recorded video will be available soon — TE','video','/student/live-classes',0,NULL,'2026-05-28 07:43:39','2026-05-28 07:43:39'),(23,1,11,'video','📹 Recorded: Test','The recorded video is now available — TE','video','https://www.youtube.com/watch?v=QqI5pMJuFbE',1,'2026-05-28 07:43:51','2026-05-28 07:43:43','2026-05-28 07:43:51'),(24,1,11,'live_class','✅ Class Ended: fg','The live class has ended. Recorded video will be available soon — TE','video','/student/live-classes',0,NULL,'2026-05-28 08:03:20','2026-05-28 08:03:20'),(25,1,6,'live_class','🎥 Public Live Class: asd','A public live class has been scheduled — open to all students','video','/student/live-classes',1,'2026-05-28 09:08:27','2026-05-28 09:07:11','2026-05-28 09:08:27'),(26,1,7,'live_class','🎥 Public Live Class: asd','A public live class has been scheduled — open to all students','video','/student/live-classes',0,NULL,'2026-05-28 09:07:11','2026-05-28 09:07:11'),(27,1,8,'live_class','🎥 Public Live Class: asd','A public live class has been scheduled — open to all students','video','/student/live-classes',0,NULL,'2026-05-28 09:07:11','2026-05-28 09:07:11'),(28,1,9,'live_class','🎥 Public Live Class: asd','A public live class has been scheduled — open to all students','video','/student/live-classes',0,NULL,'2026-05-28 09:07:11','2026-05-28 09:07:11'),(29,1,10,'live_class','🎥 Public Live Class: asd','A public live class has been scheduled — open to all students','video','/student/live-classes',0,NULL,'2026-05-28 09:07:11','2026-05-28 09:07:11'),(30,1,11,'live_class','🎥 Public Live Class: asd','A public live class has been scheduled — open to all students','video','/student/live-classes',0,NULL,'2026-05-28 09:07:11','2026-05-28 09:07:11'),(31,1,15,'live_class','🎥 Public Live Class: asd','A public live class has been scheduled — open to all students','video','/student/live-classes',0,NULL,'2026-05-28 09:07:11','2026-05-28 09:07:11'),(32,1,11,'live_class','🔴 LIVE NOW: asd','Your live class has started! Click to join now — TE','live','https://meet.google.com/qbr-qfji-uve',0,NULL,'2026-05-28 09:07:35','2026-05-28 09:07:35'),(33,1,11,'live_class','🔴 LIVE NOW: asd','Your live class has started! Click to join now — TE','live','https://meet.google.com/qbr-qfji-uve',0,NULL,'2026-05-28 09:07:36','2026-05-28 09:07:36'),(34,5,18,'payment','Payment Verified','Your payment for Maths has been verified.','payment','/student/payments',1,'2026-05-30 14:53:33','2026-05-30 14:51:18','2026-05-30 14:53:33'),(35,5,18,'live_class','Live Class: Live','Scheduled on 30 May, 08:11 PM — Maths','live','/student/live-classes',1,'2026-05-30 19:11:39','2026-05-30 19:11:12','2026-05-30 19:11:39'),(36,5,18,'live_class','Live Class: test','Scheduled on 01 Jun, 09:19 AM — Maths','live','/student/live-classes',1,'2026-06-01 11:01:47','2026-06-01 08:19:19','2026-06-01 11:01:47'),(37,5,18,'live_class','📅 Class Scheduled: maths','New BTLive class on Mon, Jun 01 at 10:44 AM — Maths','calendar','/student/live-classes',1,'2026-06-01 10:46:28','2026-06-01 10:42:36','2026-06-01 10:46:28'),(38,5,18,'live_class','🔴 LIVE NOW: maths','Your class is starting now! Join BTLive classroom — Maths','video','/student/btlive/7/join',1,'2026-06-01 10:45:04','2026-06-01 10:42:39','2026-06-01 10:45:04'),(39,5,18,'live_class','✅ Class Ended: maths','The live class has ended. Recorded video will be available soon — Maths','video','/student/live-classes',1,'2026-06-01 12:27:27','2026-06-01 11:45:59','2026-06-01 12:27:27'),(40,5,18,'live_class','🔴 LIVE NOW: maths','Your live class has started! Click to join now — Maths','live','https://live.ecchapuron.btguru.tech/btlive-ecchapuron-6-7-7f07daf3',1,'2026-06-01 12:27:27','2026-06-01 11:46:02','2026-06-01 12:27:27'),(41,5,18,'live_class','🔴 LIVE NOW: maths','Your live class has started! Click to join now — Maths','live','https://live.ecchapuron.btguru.tech/btlive-ecchapuron-6-7-7f07daf3',1,'2026-06-01 12:27:27','2026-06-01 11:50:51','2026-06-01 12:27:27'),(42,5,18,'live_class','✅ Class Ended: maths','The live class has ended. Recorded video will be available soon — Maths','video','/student/live-classes',1,'2026-06-01 12:27:27','2026-06-01 12:00:20','2026-06-01 12:27:27'),(43,5,18,'live_class','🔴 LIVE NOW: maths','Your live class has started! Click to join now — Maths','live','https://live.ecchapuron.btguru.tech/btlive-ecchapuron-6-7-7f07daf3',1,'2026-06-01 12:27:27','2026-06-01 12:00:22','2026-06-01 12:27:27'),(44,5,18,'live_class','✅ Class Ended: maths','The live class has ended. Recording will be available soon.','video','/student/live-classes',1,'2026-06-01 12:27:27','2026-06-01 12:07:02','2026-06-01 12:27:27'),(45,5,18,'live_class','📅 Class Scheduled: Test','New BTLive class on Mon, Jun 01 at 12:09 PM — Maths','calendar','/student/live-classes',1,'2026-06-01 12:27:27','2026-06-01 12:08:44','2026-06-01 12:27:27'),(46,5,18,'live_class','🔴 LIVE NOW: Test','Your class is starting now! Join BTLive classroom — Maths','video','/student/btlive/8/join',1,'2026-06-01 12:27:27','2026-06-01 12:08:45','2026-06-01 12:27:27'),(47,5,18,'live_class','✅ Class Ended: Test','The live class has ended. Recorded video will be available soon — Maths','video','/student/live-classes',1,'2026-06-01 12:27:27','2026-06-01 12:26:43','2026-06-01 12:27:27'),(48,5,18,'live_class','🔴 LIVE NOW: Test','Your live class has started! Click to join now — Maths','live','https://live.ecchapuron.btguru.tech/btlive-ecchapuron-6-8-2b6d8b92',1,'2026-06-01 12:27:27','2026-06-01 12:26:44','2026-06-01 12:27:27'),(49,5,18,'live_class','✅ Class Ended: Test','The live class has ended. Recording will be available soon.','video','/student/live-classes',1,'2026-06-01 13:43:44','2026-06-01 12:27:51','2026-06-01 13:43:44'),(50,5,18,'live_class','📅 Class Scheduled: cool','New BTLive class on Mon, Jun 01 at 12:29 PM — Maths','calendar','/student/live-classes',1,'2026-06-01 13:43:44','2026-06-01 12:28:05','2026-06-01 13:43:44'),(51,5,18,'live_class','🔴 LIVE NOW: cool','Your class is starting now! Join BTLive classroom — Maths','video','/student/btlive/9/join',1,'2026-06-01 12:31:18','2026-06-01 12:28:06','2026-06-01 12:31:18'),(52,5,18,'live_class','📅 Class Scheduled: test 4','New BTLive class on Mon, Jun 01 at 02:06 PM — Maths','calendar','/student/live-classes',1,'2026-06-01 14:06:52','2026-06-01 14:05:37','2026-06-01 14:06:52'),(53,5,18,'live_class','📅 Class Scheduled: maths','New BTLive class on Mon, Jun 01 at 02:09 PM — Maths','calendar','/student/live-classes',1,'2026-06-01 15:07:21','2026-06-01 14:08:04','2026-06-01 15:07:21'),(54,5,18,'live_class','🔴 LIVE NOW: maths','Your class is starting now! Join BTLive classroom — Maths','video','/student/btlive/11/join',1,'2026-06-01 15:03:24','2026-06-01 14:08:07','2026-06-01 15:03:24'),(55,5,18,'live_class','📅 Class Scheduled: Test 6','New BTLive class on Mon, Jun 01 at 02:55 PM — Maths','calendar','/student/live-classes',1,'2026-06-01 15:07:21','2026-06-01 14:54:53','2026-06-01 15:07:21'),(56,5,18,'live_class','🔴 LIVE NOW: Test 6','Your class is starting now! Join BTLive classroom — Maths','video','/student/btlive/12/join',1,'2026-06-01 15:07:21','2026-06-01 14:54:54','2026-06-01 15:07:21'),(57,5,18,'live_class','📅 Class Scheduled: Test 7','New BTLive class on Mon, Jun 01 at 02:58 PM — Maths','calendar','/student/live-classes',1,'2026-06-01 15:07:21','2026-06-01 14:57:06','2026-06-01 15:07:21'),(58,5,18,'live_class','🔴 LIVE NOW: Test 7','Your class is starting now! Join BTLive classroom — Maths','video','/student/btlive/13/join',1,'2026-06-01 15:07:21','2026-06-01 14:57:08','2026-06-01 15:07:21'),(59,5,18,'live_class','✅ Class Ended: Test 7','The live class has ended. Recording will be available soon.','video','/student/live-classes',1,'2026-06-01 15:04:23','2026-06-01 14:58:37','2026-06-01 15:04:23'),(60,5,18,'live_class','🔴 LIVE NOW: maths','Your live class has started! Click to join now — Maths','live','https://live.ecchapuron.btguru.tech/btlive-ecchapuron-6-7-7f07daf3',1,'2026-06-01 15:08:51','2026-06-01 15:08:06','2026-06-01 15:08:51'),(61,5,18,'live_class','✅ Class Ended: maths','The live class has ended. Recorded video will be available soon — Maths','video','/student/live-classes',1,'2026-06-01 18:20:24','2026-06-01 15:56:44','2026-06-01 18:20:24'),(62,5,18,'live_class','📅 Class Scheduled: test 8','New BTLive class on Mon, Jun 01 at 04:17 PM — Maths','calendar','/student/live-classes',1,'2026-06-01 18:20:24','2026-06-01 16:16:04','2026-06-01 18:20:24'),(63,5,18,'live_class','🔴 LIVE NOW: test 8','Your class is starting now! Join BTLive classroom — Maths','video','/student/btlive/14/join',1,'2026-06-01 18:20:24','2026-06-01 16:16:06','2026-06-01 18:20:24'),(64,5,18,'live_class','🔴 LIVE NOW: test 8','Your live class has started! Click to join now — Maths','live','https://live.ecchapuron.btguru.tech/btlive-ecchapuron-6-14-33b054eb',1,'2026-06-01 18:20:24','2026-06-01 16:16:18','2026-06-01 18:20:24'),(65,5,18,'live_class','🔴 LIVE NOW: maths','Your live class has started! Click to join now — Maths','live','https://live.ecchapuron.btguru.tech/btlive-ecchapuron-6-7-7f07daf3',1,'2026-06-01 18:20:24','2026-06-01 18:19:07','2026-06-01 18:20:24'),(66,5,18,'live_class','✅ Class Ended: test 8','The live class has ended. Recording will be available soon.','video','/student/live-classes',0,NULL,'2026-06-01 18:29:24','2026-06-01 18:29:24'),(67,5,18,'live_class','📅 Class Scheduled: ffdfd','New BTLive class on Mon, Jun 01 at 06:33 PM — Maths','calendar','/student/live-classes',0,NULL,'2026-06-01 18:32:15','2026-06-01 18:32:15'),(68,5,18,'live_class','🔴 LIVE NOW: ffdfd','Your class is starting now! Join BTLive classroom — Maths','video','/student/btlive/15/join',1,'2026-06-01 18:51:18','2026-06-01 18:32:23','2026-06-01 18:51:18');
/*!40000 ALTER TABLE `student_notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subjects`
--

DROP TABLE IF EXISTS `subjects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `subjects` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `curriculum_id` bigint unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `order` int NOT NULL DEFAULT '0',
  `status` enum('active','inactive','draft') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subjects_curriculum_id_foreign` (`curriculum_id`),
  CONSTRAINT `subjects_curriculum_id_foreign` FOREIGN KEY (`curriculum_id`) REFERENCES `curricula` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subjects`
--

LOCK TABLES `subjects` WRITE;
/*!40000 ALTER TABLE `subjects` DISABLE KEYS */;
INSERT INTO `subjects` VALUES (1,1,'Geo',NULL,0,'active','2026-05-23 05:39:11','2026-05-23 05:39:11',NULL),(2,1,'History',NULL,1,'active','2026-05-23 12:20:18','2026-05-23 12:20:18',NULL),(3,2,'A',NULL,0,'active','2026-05-27 23:02:05','2026-05-27 23:02:05',NULL),(4,3,'B',NULL,0,'active','2026-05-28 06:13:05','2026-05-28 06:13:05',NULL),(5,4,'A',NULL,0,'active','2026-05-28 06:14:36','2026-05-28 06:14:36',NULL),(6,5,'Grammer',NULL,1,'active','2026-05-30 14:48:06','2026-05-30 14:48:06',NULL);
/*!40000 ALTER TABLE `subjects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subscription_plans`
--

DROP TABLE IF EXISTS `subscription_plans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `subscription_plans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `price` decimal(10,2) NOT NULL,
  `currency` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'INR',
  `duration_days` int NOT NULL,
  `trial_days` int NOT NULL DEFAULT '0',
  `features` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `is_popular` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `subscription_plans_chk_1` CHECK (json_valid(`features`))
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subscription_plans`
--

LOCK TABLES `subscription_plans` WRITE;
/*!40000 ALTER TABLE `subscription_plans` DISABLE KEYS */;
INSERT INTO `subscription_plans` VALUES (1,'BASIC',NULL,500.00,'INR',30,10,NULL,1,1,1,'2026-05-26 21:04:58','2026-05-26 21:04:58',NULL);
/*!40000 ALTER TABLE `subscription_plans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subscriptions`
--

DROP TABLE IF EXISTS `subscriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `subscriptions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `plan_id` bigint unsigned NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `trial_end_date` date DEFAULT NULL,
  `status` enum('trial','active','expired','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'trial',
  `coupon_code_used` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `original_price` decimal(10,2) DEFAULT NULL,
  `discount_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `final_price` decimal(10,2) DEFAULT NULL,
  `payment_status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `auto_renew` tinyint(1) NOT NULL DEFAULT '0',
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subscriptions_plan_id_foreign` (`plan_id`),
  KEY `subscriptions_tenant_id_status_index` (`tenant_id`,`status`),
  KEY `subscriptions_status_end_date_index` (`status`,`end_date`),
  CONSTRAINT `subscriptions_plan_id_foreign` FOREIGN KEY (`plan_id`) REFERENCES `subscription_plans` (`id`),
  CONSTRAINT `subscriptions_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subscriptions`
--

LOCK TABLES `subscriptions` WRITE;
/*!40000 ALTER TABLE `subscriptions` DISABLE KEYS */;
INSERT INTO `subscriptions` VALUES (1,1,1,'2026-05-27','2026-06-26','2026-06-06','active',NULL,500.00,0.00,500.00,'pending',NULL,NULL,NULL,0,NULL,NULL,'2026-05-26 21:28:44','2026-05-27 00:15:31',NULL),(2,1,1,'2026-05-27','2026-06-26','2026-06-06','trial',NULL,500.00,0.00,500.00,'pending',NULL,NULL,NULL,0,NULL,NULL,'2026-05-26 21:29:10','2026-05-26 21:29:10',NULL),(3,1,1,'2026-05-27','2026-06-26','2026-06-06','trial',NULL,500.00,0.00,500.00,'pending',NULL,NULL,NULL,0,NULL,NULL,'2026-05-26 21:29:16','2026-05-26 21:29:16',NULL),(4,1,1,'2026-05-27','2026-06-26','2026-06-06','trial',NULL,500.00,0.00,500.00,'pending',NULL,NULL,NULL,0,NULL,NULL,'2026-05-26 21:30:42','2026-05-26 21:30:42',NULL),(5,1,1,'2026-05-27','2026-06-26','2026-06-06','trial',NULL,500.00,0.00,500.00,'pending',NULL,NULL,NULL,0,NULL,NULL,'2026-05-26 21:40:16','2026-05-26 21:40:16',NULL),(6,5,1,'2026-05-30','2026-06-29','2026-06-09','trial',NULL,500.00,0.00,500.00,'pending',NULL,NULL,NULL,0,NULL,NULL,'2026-05-30 11:57:58','2026-05-30 11:57:58',NULL),(7,5,1,'2026-05-30','2026-06-29','2026-06-09','trial',NULL,500.00,0.00,500.00,'pending',NULL,NULL,NULL,0,NULL,NULL,'2026-05-30 22:43:40','2026-05-30 22:43:40',NULL);
/*!40000 ALTER TABLE `subscriptions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `system_settings`
--

DROP TABLE IF EXISTS `system_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `system_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `system_settings_key_unique` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `system_settings`
--

LOCK TABLES `system_settings` WRITE;
/*!40000 ALTER TABLE `system_settings` DISABLE KEYS */;
INSERT INTO `system_settings` VALUES (1,'mail_driver','smtp','2026-05-26 11:46:57','2026-05-26 11:46:57'),(2,'mail_host','smtp.hostinger.com','2026-05-26 11:46:57','2026-05-26 11:46:57'),(3,'mail_port','587','2026-05-26 11:46:57','2026-05-26 11:46:57'),(4,'mail_username','no_reply@btguru.tech','2026-05-26 11:46:57','2026-05-26 20:45:43'),(5,'mail_encryption','tls','2026-05-26 11:46:57','2026-05-26 11:47:59'),(6,'mail_from_address','no_reply@btguru.tech','2026-05-26 11:46:57','2026-05-26 20:45:43'),(7,'mail_from_name','BT Guru','2026-05-26 11:46:57','2026-05-26 11:46:57'),(8,'mail_password','ToThePoint@123','2026-05-26 11:46:57','2026-05-26 20:45:43');
/*!40000 ALTER TABLE `system_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tenant_registrations`
--

DROP TABLE IF EXISTS `tenant_registrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tenant_registrations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `otp` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `otp_expires_at` timestamp NULL DEFAULT NULL,
  `email_verified` tinyint(1) NOT NULL DEFAULT '0',
  `step` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tenant_registrations_token_unique` (`token`),
  CONSTRAINT `tenant_registrations_chk_1` CHECK (json_valid(`data`))
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tenant_registrations`
--

LOCK TABLES `tenant_registrations` WRITE;
/*!40000 ALTER TABLE `tenant_registrations` DISABLE KEYS */;
INSERT INTO `tenant_registrations` VALUES (1,'{\"coaching_name\":\"My Coaching\",\"subdomain\":\"mycoaching\",\"coaching_type\":\"Competitive Exam (IIT\\/JEE\\/NEET)\",\"tagline\":\"Inspiring curiosity, character, and competence\",\"website\":null,\"email\":\"sourabarui@gmail.com\",\"phone\":\"+918282924454\",\"phone_alt\":null,\"address\":\"Subhash Pally, Noapara, Barasat, Kolkata, WB, PIN - 700125\",\"city\":\"BARASAT\",\"state\":\"West Bengal\",\"pincode\":\"700125\",\"admin_name\":\"Sourav Barui\",\"admin_email\":\"sourabarui@gmail.com\",\"admin_phone\":\"+918282924454\",\"password\":\"$2y$12$ObuzqcA8VreTZ3FOSBuzmuWMHO7HXgQZ2OTAo.aNEVw7zOgdXuCb2\"}','ZZFwWm6FUcBDYsnBgfcjxXmo2IZx1TqcYPOfm2oFIxY15EVVlhfWJUsBSs2GOSXb','460665','2026-05-26 11:36:52',0,5,'2026-05-26 11:21:03','2026-05-26 11:21:55'),(3,'{\"coaching_name\":\"School Service Bangla\",\"subdomain\":\"schoolservicebangla\",\"coaching_type\":\"Government Exam (SSC\\/UPSC\\/Banking)\",\"tagline\":null,\"website\":null}','NNYZB9dMxZvMOu9X74fCFqNJg4rzKe6Qv1REHx0wloYkwXct5Bkzb4kzMVN3t55a',NULL,NULL,0,2,'2026-05-28 13:24:26','2026-05-28 13:24:26'),(4,'{\"coaching_name\":\"School Service Bangla\",\"subdomain\":\"schoolservicebangla\",\"coaching_type\":\"Government Exam (SSC\\/UPSC\\/Banking)\",\"tagline\":null,\"website\":null,\"email\":\"tojoarjo1728@gmail.com\",\"phone\":\"6289074234\",\"phone_alt\":null,\"address\":\"NOAPARA\",\"city\":\"BARASAT\",\"state\":\"West Bengal\",\"pincode\":\"700125\",\"admin_name\":\"PRADIP\",\"admin_email\":\"tojoarjo1728@gmail.com\",\"admin_phone\":\"+916289074234\",\"password\":\"$2y$12$zUq5Xa82UIb0wgrCoCKYw.UkF2hacGvEoa7TElBlWqxI66gg1Qrk6\"}','AAMaLQgi7gVZRbinnY51BMunv7Ou2NprLbi2JiynIcxNijMf8R1iR2pqsoFkE05g','824250','2026-05-28 14:34:02',0,5,'2026-05-28 14:15:52','2026-05-28 14:19:15'),(6,'{\"coaching_name\":\"School Service Bangla\",\"subdomain\":\"school-service-bangla\",\"coaching_type\":\"Government Exam (SSC\\/UPSC\\/Banking)\",\"tagline\":null,\"website\":null}','dVrNr6DshsjEiW3qIg86yeaz2fKiBlX9slWz4vory7RxpvZb9aKy8Ra4WsCkaVy0',NULL,NULL,0,2,'2026-05-30 22:08:38','2026-05-30 22:08:38'),(7,'{\"coaching_name\":\"school Service Bangla\",\"subdomain\":\"school-service-bangla\",\"coaching_type\":\"Government Exam (SSC\\/UPSC\\/Banking)\",\"tagline\":null,\"website\":null,\"email\":\"schoolservicebangla@gmail.com\",\"phone\":\"6289074234\",\"phone_alt\":null,\"address\":\"Noapara\",\"city\":\"Kolkata\",\"state\":\"West Bengal\",\"pincode\":\"700125\",\"admin_name\":\"Paritosh Gain\",\"admin_email\":\"schoolservicebangla@gmail.com\",\"admin_phone\":\"6289074234\",\"password\":\"$2y$12$v3vNWR2D8upWJ05AxCVe..W\\/kyT\\/fJSSbcneyKV.G.spKjqlI3uvG\"}','1cJODeZQW5S3EtKPwwaIMOxMxjKta6cW5X9KUDUzuggxUQ2VU0ifeKi69oTE52jP','265003','2026-05-30 22:34:42',0,5,'2026-05-30 22:13:02','2026-05-30 22:19:42'),(8,'{\"coaching_name\":\"s\",\"subdomain\":\"ss\",\"coaching_type\":\"College Coaching\",\"tagline\":null,\"website\":null}','RhQMZEY1dnQ5uNVhtDPidex8MSbtMdFNs74M8Obb5h8hCsHzW9h09PZeiTpGpC5s',NULL,NULL,0,2,'2026-05-30 23:21:56','2026-05-30 23:21:56'),(9,'{\"coaching_name\":\"School Service Bangla\",\"subdomain\":\"school-service-bangla\",\"coaching_type\":\"Government Exam (SSC\\/UPSC\\/Banking)\",\"tagline\":null,\"website\":null,\"email\":\"sourabarui31@gmail.com\",\"phone\":\"+918282924454\",\"phone_alt\":null,\"address\":\"Subhash Pally, Noapara, Barasat, Kolkata, WB, PIN - 700125\",\"city\":\"BARASAT\",\"state\":\"West Bengal\",\"pincode\":\"700125\",\"admin_name\":\"Sourav Barui\",\"admin_email\":\"sourabarui31@gmail.com\",\"admin_phone\":\"+918282924454\",\"password\":\"$2y$12$qASdCJck56nbEQsbcrSReuwQ6dMvFAnQglgUggdfqXfU8Jo12qZqa\"}','0Mr9oqo8nKdsSMEEdfSseWxwjyhoYU5vL2oDMWHqwYOK6cRRGSAyVfFT76DUNnVk',NULL,NULL,0,4,'2026-05-30 23:23:06','2026-05-30 23:24:04'),(10,'{\"coaching_name\":\"School Service Bangla\",\"subdomain\":\"school-service-bangla\",\"coaching_type\":\"Government Exam (SSC\\/UPSC\\/Banking)\",\"tagline\":null,\"website\":null}','2GQXu1vT1oD8uCn0rGVy6vjPWEtjD0SnCTVcUVUgVb0AeD3yyhvIXvWCaLOU3QJM',NULL,NULL,0,2,'2026-05-31 00:01:56','2026-05-31 00:01:56'),(12,'{\"coaching_name\":\"School Service Bangla\",\"subdomain\":\"school-service-bangla\",\"coaching_type\":\"Government Exam (SSC\\/UPSC\\/Banking)\",\"tagline\":null,\"website\":null,\"email\":\"schoolservicebangla@gmail.com\",\"phone\":\"+916289074234\",\"phone_alt\":null,\"address\":\"NOAPARA\",\"city\":\"BARASAT\",\"state\":\"West Bengal\",\"pincode\":\"700125\",\"admin_name\":\"School Service Bangla\",\"admin_email\":\"schoolservicebangla@gmail.com\",\"admin_phone\":\"+918282924454\",\"password\":\"$2y$12$URI4K\\/OPBsDpKo96l2UVp.I1JjppfDgbatzEJF6Ml4GYh9j.BVTcC\"}','F60KGawhBWrjaUAgZWR900dx6SRjPXoHhetDM050BzXQ6dIxVP3T1PuzI3a6gLpp','189613','2026-05-31 23:12:35',0,5,'2026-05-31 22:51:35','2026-05-31 22:57:35'),(13,'{\"coaching_name\":\"School Service Bangla\",\"subdomain\":\"school-service-bangla\",\"coaching_type\":\"Government Exam (SSC\\/UPSC\\/Banking)\",\"tagline\":null,\"website\":null,\"email\":\"pelil27628@doreact.com\",\"phone\":\"+916289074234\",\"phone_alt\":null,\"address\":\"NOAPARA\",\"city\":\"BARASAT\",\"state\":\"West Bengal\",\"pincode\":\"700125\",\"admin_name\":\"Paritosh Gain\",\"admin_email\":\"pelil27628@doreact.com\",\"admin_phone\":\"+916289074234\",\"password\":\"$2y$12$0JTzs4N74gt5xvU1evaZMe2KXsBPx0W5u1pB06nYU1CvpeY\\/XSFuC\"}','sKtrOH2E1qoSL3FlRpuc0Kry8oP8NCTpdTScx8Kyrothba3UGVV4apgh8x6G3otw','958206','2026-06-01 08:21:27',0,5,'2026-06-01 08:03:13','2026-06-01 08:06:29');
/*!40000 ALTER TABLE `tenant_registrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tenants`
--

DROP TABLE IF EXISTS `tenants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tenants` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `coaching_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subdomain` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `custom_domain` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pwa_icon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `portal_icon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `expires_at` timestamp NULL DEFAULT NULL,
  `settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `btlive_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `btlive_recording_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `btlive_auto_start_recording` tinyint(1) NOT NULL DEFAULT '0',
  `btlive_max_participants` int NOT NULL DEFAULT '100',
  `btlive_max_recording_duration` int NOT NULL DEFAULT '240',
  `btlive_s3_bucket` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `btlive_s3_region` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'us-east-1',
  `btlive_s3_access_key` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `btlive_s3_secret_key` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `btlive_s3_endpoint` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tenants_slug_unique` (`slug`),
  UNIQUE KEY `tenants_subdomain_unique` (`subdomain`),
  UNIQUE KEY `tenants_custom_domain_unique` (`custom_domain`),
  KEY `tenants_subdomain_index` (`subdomain`),
  KEY `tenants_custom_domain_index` (`custom_domain`),
  KEY `tenants_status_index` (`status`),
  CONSTRAINT `tenants_chk_1` CHECK (json_valid(`settings`))
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tenants`
--

LOCK TABLES `tenants` WRITE;
/*!40000 ALTER TABLE `tenants` DISABLE KEYS */;
INSERT INTO `tenants` VALUES (1,'Future Academy','future-academy','futureacademy',NULL,NULL,NULL,NULL,'info@futureacademy.com','+91-9876543210','123 Education Street, Knowledge City, India','active',NULL,'{\"theme_color\":\"#3b82f6\",\"timezone\":\"Asia\\/Kolkata\",\"currency\":\"INR\",\"upi_id\":\"rohan@upi\",\"upi_name\":\"Future Academy\",\"bank_name\":null,\"bank_account\":null,\"bank_ifsc\":null,\"bank_holder\":null,\"tagline\":null,\"website\":null,\"phone_alt\":null,\"city\":null,\"state\":null,\"pincode\":null,\"facebook\":null,\"instagram\":null,\"youtube\":null,\"telegram\":null,\"whatsapp\":null,\"twitter\":null,\"linkedin\":null,\"mail_driver\":null,\"mail_host\":null,\"mail_port\":\"587\",\"mail_username\":null,\"mail_encryption\":\"tls\",\"mail_from_address\":null,\"mail_from_name\":\"Future Academy\",\"wa_provider\":null,\"wa_api_url\":null,\"wa_api_key\":null,\"wa_instance_id\":null,\"wa_token\":null,\"wa_from_number\":null,\"portal_title\":\"Future Academy\",\"mail_password\":null}','2026-05-23 04:57:22','2026-05-27 01:05:17',NULL,0,0,0,100,240,NULL,'us-east-1',NULL,NULL,NULL),(3,'Super Academy','super-academy','superacademy',NULL,NULL,NULL,NULL,'sourabarui@gmail.com','+918282924454','Subhash Pally, Noapara, Barasat, Kolkata, WB, PIN - 700125, BARASAT, West Bengal, 700125','active',NULL,'{\"tagline\":\"Inspiring curiosity, character, and competence\",\"website\":null,\"coaching_type\":\"Competitive Exam (IIT\\/JEE\\/NEET)\",\"phone_alt\":null,\"city\":\"BARASAT\",\"state\":\"West Bengal\",\"pincode\":\"700125\"}','2026-05-26 20:49:21','2026-05-26 21:44:10',NULL,0,0,0,100,240,NULL,'us-east-1',NULL,NULL,NULL),(4,'School Service Bangla','school-service-bangla','schoolservicebangla',NULL,NULL,NULL,NULL,'tojoarjo1728@gmail.com','+918282924454','Subhash Pally, Noapara, Barasat, Kolkata, WB, PIN - 700125','active','2027-06-29 18:30:00','[]','2026-05-28 15:58:05','2026-05-28 15:58:32','2026-05-28 15:58:32',0,0,0,100,240,NULL,'us-east-1',NULL,NULL,NULL),(5,'Ecchapuron','ecchapuron','ecchapuron',NULL,'tenant/logos/MlZaiQDH9secTP8HAJumwa3vasNztI15jFMqEDy5.png','tenant/pwa_icons/o4iAgt48oU2mz4LMsbmgeLfGGUQsEcp7sfhw2aNe.png','tenant/portal_icons/va0u0HPSy5al3jhafpTD3b434zR5mM4yE8bF45UF.png','techbarui3@gmail.com','+918282924454','Subhash Pally, Noapara, Barasat, Kolkata, WB, PIN - 700125, BARASAT, West Bengal, 700125','active',NULL,'{\"tagline\":null,\"website\":null,\"coaching_type\":\"Government Exam (SSC\\/UPSC\\/Banking)\",\"phone_alt\":null,\"city\":\"BARASAT\",\"state\":\"West Bengal\",\"pincode\":\"700125\",\"facebook\":null,\"instagram\":null,\"youtube\":null,\"telegram\":null,\"whatsapp\":null,\"twitter\":null,\"linkedin\":null,\"upi_id\":null,\"upi_name\":\"Ecchapuron\",\"bank_name\":null,\"bank_account\":null,\"bank_ifsc\":null,\"bank_holder\":null,\"mail_driver\":null,\"mail_host\":null,\"mail_port\":\"587\",\"mail_username\":null,\"mail_encryption\":\"tls\",\"mail_from_address\":null,\"mail_from_name\":\"Ecchapuron\",\"wa_provider\":null,\"wa_api_url\":null,\"wa_api_key\":null,\"wa_instance_id\":null,\"wa_token\":null,\"wa_from_number\":null,\"portal_title\":null,\"mail_password\":null}','2026-05-30 11:56:36','2026-05-30 11:59:06',NULL,0,0,0,100,240,NULL,'us-east-1',NULL,NULL,NULL),(12,'myclass','myclass','myclass',NULL,NULL,NULL,NULL,'sushanta1997barui@gmail.com','+918282924454','Subhash Pally, Noapara, Barasat, Kolkata, WB, PIN - 700125, BARASAT, West Bengal, 700125','active',NULL,'{\"tagline\":null,\"website\":null,\"coaching_type\":\"Government Exam (SSC\\/UPSC\\/Banking)\",\"phone_alt\":null,\"city\":\"BARASAT\",\"state\":\"West Bengal\",\"pincode\":\"700125\"}','2026-05-31 00:05:34','2026-05-31 00:06:14',NULL,0,0,0,100,240,NULL,'us-east-1',NULL,NULL,NULL),(17,'School Service Bangla','school-service-bangla-001','schoolservicebanglawb',NULL,NULL,NULL,NULL,'schoolservicebangla@gmail.com','+916289074234','NOAPARA, BARASAT, West Bengal, 700125','active',NULL,'{\"tagline\":null,\"website\":null,\"coaching_type\":\"Government Exam (SSC\\/UPSC\\/Banking)\",\"phone_alt\":null,\"city\":\"BARASAT\",\"state\":\"West Bengal\",\"pincode\":\"700125\"}','2026-06-01 08:23:11','2026-06-01 09:00:52',NULL,0,0,0,100,240,NULL,'us-east-1',NULL,NULL,NULL);
/*!40000 ALTER TABLE `tenants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `current_session_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_login_ip` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `password_changed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_tenant_id_unique` (`email`,`tenant_id`),
  KEY `users_tenant_id_status_index` (`tenant_id`,`status`),
  KEY `users_tenant_id_email_index` (`tenant_id`,`email`),
  CONSTRAINT `users_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,NULL,'Super Admin','admin@btguru.in','+91-9999999999','2026-05-23 04:57:22','$2y$12$4w5SWiOGaV9A299pupR7cewBAacW0kKn9NZpG2bsGrs8heQoKI/46',NULL,'active',NULL,NULL,NULL,NULL,NULL,'2026-05-23 04:57:22','2026-05-23 04:57:22',NULL),(2,1,'John Smith','admin@futureacademy.com','+91-9876543211','2026-05-23 04:57:22','$2y$12$jy9vTShqIf3LXhUwhTnZyuDwIGU7YMcYcrKvzo.NoYxEUrFslcYMe',NULL,'active',NULL,NULL,NULL,NULL,NULL,'2026-05-23 04:57:22','2026-05-23 04:57:22',NULL),(3,1,'Dr. Sarah Johnson','sarah@futureacademy.com','+91-9876543212','2026-05-23 04:57:23','$2y$12$YFTrT27HQkWrjIHDk6Ze.ewHOi74obxwuhqFuwGemPwhuHteEG9AS',NULL,'active',NULL,NULL,NULL,NULL,NULL,'2026-05-23 04:57:23','2026-05-23 04:57:23',NULL),(4,1,'Prof. Michael Chen','michael@futureacademy.com','+91-9876543213','2026-05-23 04:57:23','$2y$12$0L88hDuevNXU2d9QaN/q4ue4tgV92C0pQStrJQ8m.JldsOa63hqIu',NULL,'active',NULL,NULL,NULL,NULL,NULL,'2026-05-23 04:57:23','2026-05-23 04:57:23',NULL),(5,1,'Ms. Priya Sharma','priya@futureacademy.com','+91-9876543214','2026-05-23 04:57:23','$2y$12$FCvtQU4Tf70nSAxSHpdzQu10hXtbNAir10oELfbB0Ld86BeTtjBP6',NULL,'active',NULL,NULL,NULL,NULL,NULL,'2026-05-23 04:57:23','2026-05-23 04:57:23',NULL),(6,1,'Rahul Kumar','rahul@email.com','+91-8888888881','2026-05-23 04:57:23','$2y$12$ifWrPe2B9Rvem2x3yCPAee.9Dj70XzgM262omSgEVewUHhTtW0aGy',NULL,'active',NULL,'gb08lyafyDenfbKLkumJJ65RJ86xuv9EtJbNqIuH','127.0.0.1','2026-05-29 13:52:03',NULL,'2026-05-23 04:57:23','2026-05-27 22:30:23',NULL),(7,1,'Emma Wilson','emma@email.com','+91-8888888882','2026-05-23 04:57:24','$2y$12$ci.HphqdVXzOsk.GxftYr.VGXU2BFAp.okqofHlV11tTE74M8lt6S',NULL,'active',NULL,NULL,NULL,NULL,NULL,'2026-05-23 04:57:24','2026-05-23 04:57:24',NULL),(8,1,'Amit Patel','amit@email.com','+91-8888888883','2026-05-23 04:57:24','$2y$12$mnetCVrIaSSMrxgC27.SEOGyGwZOFw0ZQu24IVwXCYL6uD6x.MPbS',NULL,'active',NULL,NULL,NULL,NULL,NULL,'2026-05-23 04:57:24','2026-05-23 04:57:24',NULL),(9,1,'Sneha Gupta','sneha@email.com','+91-8888888884','2026-05-23 04:57:24','$2y$12$tRHk8/9bqnducRRoJ48k2.8mRVmZy1X/3Fi2cJoIRtEgzkmnJZNom',NULL,'active',NULL,NULL,NULL,NULL,NULL,'2026-05-23 04:57:24','2026-05-23 04:57:24',NULL),(10,1,'David Lee','david@email.com','+91-8888888885','2026-05-23 04:57:24','$2y$12$uqXIhjc3wSeZoXB/x3NI5.CCjBuOB13Uz2uMdViaAzdER577A7oE.',NULL,'active',NULL,NULL,NULL,NULL,NULL,'2026-05-23 04:57:24','2026-05-23 04:57:24',NULL),(11,1,'Priya Patel','priya.s@email.com','+91-8888888886','2026-05-23 04:57:25','$2y$12$fYt5voaab7nXGRFOO9ghOOk/eiVa/sR6Ler75RHQ17EZhSS9e1Lq6',NULL,'active',NULL,'fSN43zhCgvgRmyKhXCHH0uS99VDqnG595VRECpiz','127.0.0.1','2026-05-27 22:30:36',NULL,'2026-05-23 04:57:25','2026-05-23 04:57:25',NULL),(12,NULL,'Super Admin','admin@btguru.in','+91-9999999999','2026-05-23 04:59:39','$2y$12$5VgQCQXWaur4cpds6nVmq.BqOz.p5Xvw4tnPPCPu5WJPuGuyXM9Ym',NULL,'active',NULL,NULL,NULL,NULL,NULL,'2026-05-23 04:59:39','2026-05-23 04:59:39',NULL),(13,NULL,'Super Admin','admin@btguru.in','+91-9999999999','2026-05-23 05:00:45','$2y$12$jUSrpQpVZimVAbiEURRyUOGqX0rlo6mfG5h2LjTrx3Fo.XGuS63.q',NULL,'active',NULL,NULL,NULL,NULL,NULL,'2026-05-23 05:00:45','2026-05-23 05:00:45',NULL),(14,3,'SUNIL KUMAR BARUI','sourabarui@gmail.com','+918282924454','2026-05-26 20:49:21','$2y$12$hat1qfxRL6sYQWz2Ed7AYuuJ.SX7gLVYquZR/8VZmSMrfQ3nUgB.i',NULL,'active',NULL,NULL,NULL,NULL,NULL,'2026-05-26 20:49:21','2026-05-26 20:49:21',NULL),(15,1,'Sourav Barui','sourabarui@gmail.com','+918282924454','2026-05-27 00:59:38','$2y$12$Elg96y6FVxJMKzmFhhKsN.DrNtrKk7sj3o1QxfByGkW27CkpMvTJm',NULL,'active',NULL,'jA0htYD8brkrjHwWzGehoFV7u3lCvhour6yO6Yf0','127.0.0.1','2026-05-27 01:00:10',NULL,'2026-05-27 00:59:38','2026-05-27 01:00:10',NULL),(16,5,'SUNIL KUMAR BARUI','techbarui3@gmail.com','+918282924454','2026-05-30 11:56:36','$2y$12$crjM6wGWog28AVB4zrd5Peg4.dEmmzvRTwovtx2g/N311KPgCH.MG',NULL,'active',NULL,NULL,NULL,NULL,NULL,'2026-05-30 11:56:36','2026-05-30 11:56:36',NULL),(17,5,'Barui Tech','sourabrui@gmail.com','9830557358','2026-05-30 13:33:42','$2y$12$H5h4o5BJfSd4fzrLVpDM6O3Btr4hU4rUDvnIVW/kF8tXUuLOwaF.e',NULL,'active',NULL,NULL,NULL,NULL,NULL,'2026-05-30 13:33:42','2026-05-30 13:39:35','2026-05-30 13:39:35'),(18,5,'Sourav Barui','sourabarui@gmail.com','+918282924454','2026-05-30 13:39:05','$2y$12$S7343Uokl2UeKmFKNzArZOigGsAY0llAStlVGsaD/b4lKFVD6cJne',NULL,'active',NULL,'fGfBlTzXRUvKjAQROtMcg7IYdizvtAHBN29axHam','172.18.0.1','2026-06-01 18:20:23',NULL,'2026-05-30 13:39:05','2026-06-01 15:06:18',NULL),(19,12,'Sushanta Barui','sushanta1997barui@gmail.com','+918282924454','2026-05-31 00:05:34','$2y$12$1y7x2./l75IOcU7AzFxwOOAgFa.cQS8rFHiFUrH5WxkrvQPrMBND6',NULL,'active',NULL,NULL,NULL,NULL,NULL,'2026-05-31 00:05:34','2026-05-31 00:05:34',NULL),(20,17,'Paritosh Gain','schoolservicebangla@gmail.com','+916289074234','2026-06-01 08:23:11','$2y$12$5zUgnjZbX6rFuvBuhDCWA.fYe17NuM4w5/Uq1kGB9sSC9OOUvrb1i',NULL,'active',NULL,NULL,NULL,NULL,NULL,'2026-06-01 08:23:11','2026-06-01 09:19:56',NULL),(21,17,'Sourav Barui','sourabarui@gmail.com','+918282924454','2026-06-01 09:14:57','$2y$12$fvdJnX6NVuoP4f049ljoF.4RgsB0vn9plcEXhVxQRm7kmAueOuzx6',NULL,'active',NULL,'ZARTgmLtCLc45YgisJIvMWb1ChOLBMP59oCkICU6','172.18.0.1','2026-06-01 09:16:23',NULL,'2026-06-01 09:14:57','2026-06-01 09:14:57',NULL),(22,17,'Rina Ray','rayrina1970@gmail.com','7003880358','2026-06-01 11:59:42','$2y$12$088CxP.qnR3tf1mqSH9xr.R2WD49tAM/wUZfnifH6w2stMrGiDja.',NULL,'active',NULL,'IUEEKs88rIaI4Uw7K0sIgyEVKsACYiI8WySqvcRC','172.18.0.1','2026-06-01 12:00:03',NULL,'2026-06-01 11:59:42','2026-06-01 11:59:42',NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-06-02  8:45:11
