-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 29, 2026 at 06:24 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.4.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bt_guru`
--

-- --------------------------------------------------------

--
-- Table structure for table `blogs`
--

CREATE TABLE `blogs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `excerpt` text DEFAULT NULL,
  `content` longtext NOT NULL,
  `featured_image` varchar(255) DEFAULT NULL,
  `gallery` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`gallery`)),
  `category` varchar(255) DEFAULT NULL,
  `tags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`tags`)),
  `status` enum('draft','published','scheduled') NOT NULL DEFAULT 'draft',
  `published_at` timestamp NULL DEFAULT NULL,
  `views_count` bigint(20) NOT NULL DEFAULT 0,
  `meta` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`meta`)),
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `allow_comments` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `author` varchar(255) DEFAULT NULL,
  `publisher` varchar(255) DEFAULT NULL,
  `isbn` varchar(255) DEFAULT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'pdf',
  `pdf_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `physical_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `cover_image` varchar(255) DEFAULT NULL,
  `pdf_file` varchar(255) DEFAULT NULL,
  `stock_quantity` int(11) NOT NULL DEFAULT 0,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `tenant_id`, `title`, `slug`, `description`, `author`, `publisher`, `isbn`, `type`, `pdf_price`, `physical_price`, `cover_image`, `pdf_file`, `stock_quantity`, `status`, `metadata`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'Test Book', 'test-book', NULL, 'Pradip Kr', 'SS', '687138736', 'pdf', 150.00, 0.00, 'books/covers/yKomuD2JDYAGGesJzlKCCErscnfanFROUGib8cmG.png', 'books/pdfs/WT874pBrjaAlodwbvxk66vEEz42jYt8GLmnXQLNO.pdf', 0, 'active', '[]', '2026-05-29 14:22:43', '2026-05-29 14:22:43', NULL),
(2, 1, 'Test 2 phy', 'test-2-phy', NULL, 'sd', 'sdsd', '3524', 'physical', 1200.00, 499.97, 'books/covers/fo7hL6xV52pFoQvlYO9VyrZSfuFDFbXSdYhnM7Km.jpg', NULL, 119, 'active', '[]', '2026-05-29 14:44:27', '2026-05-29 14:59:33', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `book_orders`
--

CREATE TABLE `book_orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `book_id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `order_type` varchar(255) NOT NULL,
  `pdf_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `physical_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_status` varchar(255) NOT NULL DEFAULT 'pending',
  `payment_method` varchar(255) DEFAULT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `razorpay_order_id` varchar(255) DEFAULT NULL,
  `razorpay_payment_id` varchar(255) DEFAULT NULL,
  `razorpay_signature` varchar(255) DEFAULT NULL,
  `delivery_status` varchar(255) DEFAULT NULL,
  `delivery_address` text DEFAULT NULL,
  `delivery_phone` varchar(255) DEFAULT NULL,
  `tracking_number` varchar(255) DEFAULT NULL,
  `delivered_at` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `book_orders`
--

INSERT INTO `book_orders` (`id`, `tenant_id`, `book_id`, `student_id`, `order_type`, `pdf_price`, `physical_price`, `total_amount`, `payment_status`, `payment_method`, `transaction_id`, `razorpay_order_id`, `razorpay_payment_id`, `razorpay_signature`, `delivery_status`, `delivery_address`, `delivery_phone`, `tracking_number`, `delivered_at`, `notes`, `paid_at`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 2, 6, 'physical', 0.00, 499.97, 499.97, 'completed', 'manual', NULL, NULL, NULL, NULL, 'shipped', 'dfsdfsdf', '78924785412', '487487165', NULL, 'Book: Test 2 phy\r\nOrder Type: Physical\r\nDelivery Address: dfsdfsdf\r\nContact: 78924785412', '2026-05-29 14:59:33', '2026-05-29 14:59:33', '2026-05-29 15:00:50', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('bt_guru_cache_spatie.permission.cache', 'a:3:{s:5:\"alias\";a:4:{s:1:\"a\";s:2:\"id\";s:1:\"b\";s:4:\"name\";s:1:\"c\";s:10:\"guard_name\";s:1:\"r\";s:5:\"roles\";}s:11:\"permissions\";a:26:{i:0;a:4:{s:1:\"a\";i:1;s:1:\"b\";s:14:\"manage_tenants\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:1;a:4:{s:1:\"a\";i:2;s:1:\"b\";s:16:\"manage_all_users\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:2;a:4:{s:1:\"a\";i:3;s:1:\"b\";s:14:\"view_analytics\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:3;a:4:{s:1:\"a\";i:4;s:1:\"b\";s:12:\"manage_plans\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:4;a:4:{s:1:\"a\";i:5;s:1:\"b\";s:14:\"manage_domains\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:5;a:4:{s:1:\"a\";i:6;s:1:\"b\";s:15:\"suspend_tenants\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:6;a:4:{s:1:\"a\";i:7;s:1:\"b\";s:14:\"manage_courses\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:7;a:4:{s:1:\"a\";i:8;s:1:\"b\";s:15:\"manage_teachers\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:8;a:4:{s:1:\"a\";i:9;s:1:\"b\";s:15:\"manage_students\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:9;a:4:{s:1:\"a\";i:10;s:1:\"b\";s:18:\"manage_enrollments\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:10;a:4:{s:1:\"a\";i:11;s:1:\"b\";s:11:\"manage_fees\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:11;a:4:{s:1:\"a\";i:12;s:1:\"b\";s:14:\"manage_notices\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:12;a:4:{s:1:\"a\";i:13;s:1:\"b\";s:19:\"view_tenant_reports\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:13;a:4:{s:1:\"a\";i:14;s:1:\"b\";s:22:\"manage_tenant_settings\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:14;a:4:{s:1:\"a\";i:15;s:1:\"b\";s:18:\"approve_admissions\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:15;a:4:{s:1:\"a\";i:16;s:1:\"b\";s:21:\"view_assigned_courses\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:3;}}i:16;a:4:{s:1:\"a\";i:17;s:1:\"b\";s:20:\"view_course_students\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:3;}}i:17;a:4:{s:1:\"a\";i:18;s:1:\"b\";s:17:\"manage_attendance\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:3;}}i:18;a:4:{s:1:\"a\";i:19;s:1:\"b\";s:16:\"upload_materials\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:3;}}i:19;a:4:{s:1:\"a\";i:20;s:1:\"b\";s:22:\"view_teacher_dashboard\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:3;}}i:20;a:4:{s:1:\"a\";i:21;s:1:\"b\";s:21:\"view_enrolled_courses\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:4;}}i:21;a:4:{s:1:\"a\";i:22;s:1:\"b\";s:12:\"view_notices\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:4;}}i:22;a:4:{s:1:\"a\";i:23;s:1:\"b\";s:15:\"view_fee_status\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:4;}}i:23;a:4:{s:1:\"a\";i:24;s:1:\"b\";s:15:\"view_attendance\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:4;}}i:24;a:4:{s:1:\"a\";i:25;s:1:\"b\";s:22:\"view_student_dashboard\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:4;}}i:25;a:4:{s:1:\"a\";i:26;s:1:\"b\";s:23:\"access_course_materials\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:4;}}}s:5:\"roles\";a:4:{i:0;a:3:{s:1:\"a\";i:1;s:1:\"b\";s:11:\"super_admin\";s:1:\"c\";s:3:\"web\";}i:1;a:3:{s:1:\"a\";i:2;s:1:\"b\";s:12:\"tenant_admin\";s:1:\"c\";s:3:\"web\";}i:2;a:3:{s:1:\"a\";i:3;s:1:\"b\";s:7:\"teacher\";s:1:\"c\";s:3:\"web\";}i:3;a:3:{s:1:\"a\";i:4;s:1:\"b\";s:7:\"student\";s:1:\"c\";s:3:\"web\";}}}', 1780151174);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chapters`
--

CREATE TABLE `chapters` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `subject_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `order` int(11) NOT NULL DEFAULT 0,
  `status` enum('active','inactive','draft') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `chapters`
--

INSERT INTO `chapters` (`id`, `subject_id`, `title`, `description`, `order`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'India', NULL, 0, 'active', '2026-05-23 05:39:21', '2026-05-23 05:39:21', NULL),
(2, 1, 'History', NULL, 2, 'active', '2026-05-23 12:02:44', '2026-05-23 12:20:07', '2026-05-23 12:20:07');

-- --------------------------------------------------------

--
-- Table structure for table `coupons`
--

CREATE TABLE `coupons` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `discount_type` enum('percentage','fixed') NOT NULL DEFAULT 'percentage',
  `discount_value` decimal(10,2) NOT NULL,
  `max_uses` int(11) DEFAULT NULL,
  `used_count` int(11) NOT NULL DEFAULT 0,
  `valid_from` date DEFAULT NULL,
  `valid_until` date DEFAULT NULL,
  `applicable_plan_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`applicable_plan_ids`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `coupons`
--

INSERT INTO `coupons` (`id`, `code`, `description`, `discount_type`, `discount_value`, `max_uses`, `used_count`, `valid_from`, `valid_until`, `applicable_plan_ids`, `is_active`, `expires_at`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'GURU50', NULL, 'percentage', 49.99, NULL, 0, NULL, NULL, '[\"1\"]', 1, NULL, '2026-05-26 21:40:01', '2026-05-26 21:40:01', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `coupon_plan`
--

CREATE TABLE `coupon_plan` (
  `coupon_id` bigint(20) UNSIGNED NOT NULL,
  `plan_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `fees` decimal(10,2) NOT NULL DEFAULT 0.00,
  `fees_type` varchar(255) NOT NULL DEFAULT 'one_time',
  `past_month_fee` decimal(10,2) NOT NULL DEFAULT 0.00,
  `duration` varchar(255) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `thumbnail` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `tenant_id`, `title`, `slug`, `description`, `fees`, `fees_type`, `past_month_fee`, `duration`, `start_date`, `end_date`, `thumbnail`, `status`, `metadata`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'Mathematics Mastery', 'mathematics-mastery', 'Comprehensive mathematics course covering algebra, geometry, and calculus.', 1000.00, 'monthly', 800.00, '4 months 23 days', '2026-01-01', '2026-05-24', 'courses/CEXtGgDx6lncPTR11D9FNeuQaUlZPKAIblwbvNxO.jpg', 'active', '[]', '2026-05-23 04:57:23', '2026-05-26 10:37:38', NULL),
(2, 1, 'Physics Fundamentals', 'physics-fundamentals', 'Learn the basics of physics including mechanics, thermodynamics, and electromagnetism.', 800.00, 'monthly', 500.00, '5 months 30 days', '2026-03-01', '2026-08-31', NULL, 'active', '[]', '2026-05-23 04:57:23', '2026-05-27 23:04:58', NULL),
(3, 1, 'Chemistry Essentials', 'chemistry-essentials', 'Organic and inorganic chemistry course for high school students.', 16000.00, 'one_time', 0.00, '5 months', NULL, NULL, NULL, 'active', '[]', '2026-05-23 04:57:23', '2026-05-23 04:57:23', NULL),
(4, 1, 'English Literature', 'english-literature', 'Explore classic and modern English literature with expert guidance.', 1000.00, 'monthly', 800.00, '7 months 30 days', '2026-01-01', '2026-08-31', NULL, 'active', '[]', '2026-05-23 04:57:23', '2026-05-24 04:45:34', NULL),
(5, 1, 'TE', 'te', 'G', 50.00, 'monthly', 30.00, '10 months 8 days', '2026-03-01', '2027-01-09', NULL, 'active', '[]', '2026-05-28 06:14:24', '2026-05-28 06:14:24', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `course_subscriptions`
--

CREATE TABLE `course_subscriptions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `enrollment_id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `course_id` bigint(20) UNSIGNED NOT NULL,
  `access_start` datetime NOT NULL,
  `access_end` datetime NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'current',
  `fee_paid` decimal(10,2) NOT NULL DEFAULT 0.00,
  `payment_status` varchar(255) NOT NULL DEFAULT 'pending',
  `remarks` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `course_subscriptions`
--

INSERT INTO `course_subscriptions` (`id`, `tenant_id`, `enrollment_id`, `student_id`, `course_id`, `access_start`, `access_end`, `type`, `fee_paid`, `payment_status`, `remarks`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 1, 12, 11, 5, '2026-05-28 11:53:06', '2026-06-27 11:53:06', 'monthly', 50.00, 'paid', 'Monthly fee for 5/2026', 2, '2026-05-28 06:23:06', '2026-05-28 06:23:06');

-- --------------------------------------------------------

--
-- Table structure for table `course_teacher`
--

CREATE TABLE `course_teacher` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `course_id` bigint(20) UNSIGNED NOT NULL,
  `teacher_id` bigint(20) UNSIGNED NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `course_teacher`
--

INSERT INTO `course_teacher` (`id`, `course_id`, `teacher_id`, `is_primary`, `created_at`, `updated_at`) VALUES
(1, 1, 3, 1, '2026-05-23 04:57:23', '2026-05-26 10:37:38'),
(3, 3, 5, 1, '2026-05-23 04:57:23', '2026-05-23 04:57:23'),
(4, 4, 3, 1, '2026-05-23 04:57:23', '2026-05-24 04:45:34'),
(5, 2, 3, 1, '2026-05-27 23:04:58', '2026-05-27 23:04:58'),
(6, 5, 3, 1, '2026-05-28 06:14:24', '2026-05-28 06:14:24'),
(7, 5, 4, 0, '2026-05-28 06:14:24', '2026-05-28 06:14:24'),
(8, 5, 5, 0, '2026-05-28 06:14:24', '2026-05-28 06:14:24');

-- --------------------------------------------------------

--
-- Table structure for table `curricula`
--

CREATE TABLE `curricula` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `course_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `order` int(11) NOT NULL DEFAULT 0,
  `status` enum('active','inactive','draft') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `curricula`
--

INSERT INTO `curricula` (`id`, `course_id`, `title`, `description`, `order`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'Batch 1', NULL, 1, 'active', '2026-05-23 05:38:40', '2026-05-23 05:39:05', NULL),
(2, 2, 'A', NULL, 0, 'active', '2026-05-27 23:01:59', '2026-05-27 23:01:59', NULL),
(3, 3, 'A', NULL, 0, 'active', '2026-05-28 06:13:00', '2026-05-28 06:13:00', NULL),
(4, 5, 'AD', NULL, 0, 'active', '2026-05-28 06:14:32', '2026-05-28 06:14:32', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `curriculum_contents`
--

CREATE TABLE `curriculum_contents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `contentable_type` varchar(255) NOT NULL,
  `contentable_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `video_url` varchar(255) DEFAULT NULL,
  `video_type` enum('youtube','vimeo','other') DEFAULT NULL,
  `order` int(11) NOT NULL DEFAULT 0,
  `available_from` date DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `curriculum_contents`
--

INSERT INTO `curriculum_contents` (`id`, `contentable_type`, `contentable_id`, `title`, `description`, `video_url`, `video_type`, `order`, `available_from`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 'App\\Models\\Subject', 1, 'Inside Sri Lanka\'s Mysterious Jungle', NULL, 'https://www.youtube.com/watch?v=lstCjVoCRmM', 'youtube', 0, NULL, NULL, '2026-05-23 06:14:27', '2026-05-23 06:14:27'),
(2, 'App\\Models\\Lesson', 1, '24 Hours in 2 Countries that Hate Each Other', NULL, 'https://www.youtube.com/watch?v=8_wiuDd691s', 'youtube', 0, NULL, NULL, '2026-05-23 06:17:53', '2026-05-23 06:17:53'),
(3, 'App\\Models\\Lesson', 1, 'He Was Born a Grown Man With Unlimited IQ, But No One Could Explain What He Was', NULL, 'https://www.youtube.com/watch?v=Ayp1o-zbHNI', 'youtube', 0, NULL, NULL, '2026-05-23 06:18:23', '2026-05-23 06:18:23'),
(4, 'App\\Models\\Lesson', 2, '24 Hours in 2 Countries that Hate Each Other', NULL, 'https://www.youtube.com/watch?v=8_wiuDd691s', 'youtube', 0, NULL, 3, '2026-05-23 06:40:05', '2026-05-23 06:40:05'),
(5, 'App\\Models\\Subject', 1, 'Volkswagen Polo TSI EP1 - 7 Reasons Why I Bought It | Project Polo', NULL, 'https://www.youtube.com/watch?v=eWCuurWc0Go', 'youtube', 0, NULL, 3, '2026-05-27 00:41:25', '2026-05-27 00:41:25'),
(6, 'App\\Models\\Subject', 2, 'Your are in trouble : 5 dangerous signs of a Narcissist', NULL, 'https://www.youtube.com/watch?v=fucMXQ3kKvU', 'youtube', 0, NULL, 3, '2026-05-27 01:51:42', '2026-05-27 01:51:42'),
(7, 'App\\Models\\Subject', 4, 'Tb', NULL, 'https://www.youtube.com/watch?v=OLN7nDPK_Aw&list=RDOLN7nDPK_Aw&start_radio=1', 'youtube', 0, NULL, 2, '2026-05-28 06:13:27', '2026-05-28 06:13:27'),
(8, 'App\\Models\\Subject', 5, 'AA', NULL, 'https://www.youtube.com/watch?v=OLN7nDPK_Aw&list=RDOLN7nDPK_Aw&start_radio=1', 'youtube', 0, NULL, 2, '2026-05-28 06:14:41', '2026-05-28 06:14:41'),
(9, 'App\\Models\\Subject', 5, 'fg', NULL, 'https://www.youtube.com/watch?v=mB-T23SMKw0', 'youtube', 0, NULL, 2, '2026-05-28 06:21:07', '2026-05-28 06:21:07');

-- --------------------------------------------------------

--
-- Table structure for table `curriculum_notes`
--

CREATE TABLE `curriculum_notes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED DEFAULT NULL,
  `noteable_type` varchar(255) NOT NULL,
  `noteable_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_type` varchar(255) NOT NULL DEFAULT 'pdf',
  `is_downloadable` tinyint(1) NOT NULL DEFAULT 0,
  `order` int(11) NOT NULL DEFAULT 0,
  `available_from` date DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `curriculum_notes`
--

INSERT INTO `curriculum_notes` (`id`, `tenant_id`, `noteable_type`, `noteable_id`, `title`, `file_path`, `file_type`, `is_downloadable`, `order`, `available_from`, `user_id`, `created_at`, `updated_at`) VALUES
(1, NULL, 'App\\Models\\Lesson', 1, 'test', 'curriculum_notes/NnYEGts09B9DRgu8OkgBsd6qXQYFSDYkqaOwHoYo.pdf', 'pdf', 0, 0, NULL, NULL, '2026-05-23 06:15:09', '2026-05-23 06:15:09'),
(2, NULL, 'App\\Models\\Subject', 3, 'A', 'curriculum_notes/BRUdXtzLAfIAAxVEFdaTfgvzq8Z9ZBcnG0QGFBTq.pdf', 'pdf', 0, 0, NULL, 2, '2026-05-27 23:02:37', '2026-05-27 23:02:37'),
(3, NULL, 'App\\Models\\Subject', 1, 'test', 'curriculum_notes/cVdwt2uoTbQxjsG6SzA6fj3yTrv7bqWlReKIaNmv.pdf', 'pdf', 0, 0, NULL, 2, '2026-05-28 09:20:10', '2026-05-28 09:20:10'),
(4, NULL, 'App\\Models\\Subject', 2, 'Teast', 'curriculum_notes/xe5edwrhRWdfC0qvnGpWdT91vEXhZKc8T5JOS7OR.pdf', 'pdf', 0, 0, NULL, 2, '2026-05-28 09:21:01', '2026-05-28 09:21:01'),
(5, NULL, 'App\\Models\\Subject', 2, 'df', 'curriculum_notes/5NrdMBi6ppACa4lMpFdRuYRDM8A60QsLl6NzbtpQ.pdf', 'pdf', 1, 0, NULL, 2, '2026-05-28 09:45:48', '2026-05-28 09:45:48');

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `course_id` bigint(20) UNSIGNED NOT NULL,
  `payment_status` varchar(255) NOT NULL DEFAULT 'pending',
  `enrollment_status` varchar(255) NOT NULL DEFAULT 'pending',
  `fees_paid` decimal(10,2) NOT NULL DEFAULT 0.00,
  `fees_total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `enrolled_at` timestamp NULL DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `enrollments`
--

INSERT INTO `enrollments` (`id`, `tenant_id`, `student_id`, `course_id`, `payment_status`, `enrollment_status`, `fees_paid`, `fees_total`, `enrolled_at`, `approved_at`, `approved_by`, `metadata`, `remarks`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 6, 1, 'completed', 'active', 15000.00, 15000.00, '2026-05-23 04:57:25', '2026-05-23 04:57:25', 2, '[]', NULL, '2026-05-23 04:57:25', '2026-05-23 04:57:25', NULL),
(2, 1, 6, 2, 'completed', 'active', 18000.00, 18000.00, '2026-05-23 04:57:25', '2026-05-23 04:57:25', 2, '[]', NULL, '2026-05-23 04:57:25', '2026-05-23 04:57:25', NULL),
(3, 1, 7, 1, 'partial', 'active', 7500.00, 15000.00, '2026-05-23 04:57:25', '2026-05-23 04:57:25', 2, '[]', NULL, '2026-05-23 04:57:25', '2026-05-23 04:57:25', NULL),
(4, 1, 7, 3, 'pending', 'approved', 0.00, 16000.00, NULL, '2026-05-23 05:33:32', 2, '[]', NULL, '2026-05-23 04:57:25', '2026-05-23 05:33:32', NULL),
(5, 1, 8, 2, 'completed', 'active', 18000.00, 18000.00, '2026-05-23 04:57:25', '2026-05-23 04:57:25', 2, '[]', NULL, '2026-05-23 04:57:25', '2026-05-23 04:57:25', NULL),
(6, 1, 9, 3, 'completed', 'active', 16000.00, 16000.00, '2026-05-23 04:57:25', '2026-05-23 04:57:25', 2, '[]', NULL, '2026-05-23 04:57:25', '2026-05-23 04:57:25', NULL),
(7, 1, 10, 4, 'completed', 'approved', 12000.00, 12000.00, NULL, '2026-05-23 04:57:25', 2, '[]', NULL, '2026-05-23 04:57:25', '2026-05-23 04:57:25', NULL),
(8, 1, 11, 1, 'completed', 'active', 1000.00, 15000.00, '2026-05-27 22:59:17', '2026-05-27 22:59:17', 2, '[]', NULL, '2026-05-23 04:57:25', '2026-05-27 22:59:18', NULL),
(9, 1, 6, 4, 'completed', 'active', 1000.00, 1000.00, '2026-05-24 05:35:21', '2026-05-24 05:35:21', 2, '[]', NULL, '2026-05-24 05:35:21', '2026-05-24 05:35:21', NULL),
(11, 1, 11, 2, 'pending', 'active', 0.00, 800.00, '2026-05-28 05:57:26', '2026-05-28 05:57:26', 2, '[]', NULL, '2026-05-28 05:57:26', '2026-05-28 06:10:22', NULL),
(12, 1, 11, 5, 'completed', 'active', 50.00, 50.00, '2026-05-28 06:23:06', '2026-05-28 06:23:06', 2, '[]', NULL, '2026-05-28 06:15:11', '2026-05-28 06:23:06', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `exams`
--

CREATE TABLE `exams` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `course_id` bigint(20) UNSIGNED NOT NULL,
  `subject_id` bigint(20) UNSIGNED DEFAULT NULL,
  `chapter_id` bigint(20) UNSIGNED DEFAULT NULL,
  `lesson_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `template` varchar(255) NOT NULL DEFAULT 'default',
  `status` enum('draft','published','active','completed','archived') NOT NULL DEFAULT 'draft',
  `total_marks` int(11) NOT NULL DEFAULT 0,
  `passing_marks` int(11) NOT NULL DEFAULT 0,
  `duration_minutes` int(11) DEFAULT NULL,
  `total_questions` int(11) NOT NULL DEFAULT 0,
  `shuffle_questions` tinyint(1) NOT NULL DEFAULT 0,
  `show_result_immediately` tinyint(1) NOT NULL DEFAULT 1,
  `allow_multiple_attempts` tinyint(1) NOT NULL DEFAULT 0,
  `max_attempts` int(11) DEFAULT NULL,
  `start_time` timestamp NULL DEFAULT NULL,
  `end_time` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exams`
--

INSERT INTO `exams` (`id`, `tenant_id`, `course_id`, `subject_id`, `chapter_id`, `lesson_id`, `created_by`, `title`, `description`, `template`, `status`, `total_marks`, `passing_marks`, `duration_minutes`, `total_questions`, `shuffle_questions`, `show_result_immediately`, `allow_multiple_attempts`, `max_attempts`, `start_time`, `end_time`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, 1, 1, 1, 2, 'Test 1', 'test 1', 'default', 'draft', 4, 0, 60, 4, 1, 1, 1, 3, '2026-05-26 04:04:00', '2026-05-28 04:04:00', '2026-05-25 22:34:49', '2026-05-25 23:50:19', '2026-05-25 23:50:19'),
(2, 1, 1, 1, 1, 1, 2, 'Test 1', NULL, 'default', 'published', 9, 0, 60, 9, 0, 1, 0, 1, NULL, NULL, '2026-05-25 23:50:42', '2026-05-26 00:17:28', NULL),
(3, 1, 1, 1, 1, 1, 3, 'test 3', NULL, 'default', 'draft', 0, 0, 120, 0, 1, 1, 1, 3, NULL, NULL, '2026-05-26 09:59:02', '2026-05-26 09:59:02', NULL),
(4, 1, 1, 1, NULL, NULL, 3, 'Test524', NULL, 'default', 'published', 1, 0, 60, 1, 0, 1, 0, 12, '2026-03-01 03:47:00', '2026-03-14 03:48:00', '2026-05-27 22:18:08', '2026-05-27 22:19:12', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `exam_answers`
--

CREATE TABLE `exam_answers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `attempt_id` bigint(20) UNSIGNED NOT NULL,
  `question_id` bigint(20) UNSIGNED NOT NULL,
  `selected_option_id` bigint(20) UNSIGNED DEFAULT NULL,
  `answer_text` text DEFAULT NULL,
  `is_correct` tinyint(1) DEFAULT NULL,
  `marks_obtained` decimal(8,2) NOT NULL DEFAULT 0.00,
  `negative_marks` decimal(8,2) NOT NULL DEFAULT 0.00,
  `answered_at` timestamp NULL DEFAULT NULL,
  `time_spent_seconds` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exam_answers`
--

INSERT INTO `exam_answers` (`id`, `attempt_id`, `question_id`, `selected_option_id`, `answer_text`, `is_correct`, `marks_obtained`, `negative_marks`, `answered_at`, `time_spent_seconds`, `created_at`, `updated_at`) VALUES
(1, 1, 8, 32, NULL, NULL, 0.00, 0.00, '2026-05-26 02:15:18', NULL, '2026-05-26 01:36:27', '2026-05-26 02:15:18'),
(2, 1, 13, 52, NULL, NULL, 0.00, 0.00, '2026-05-26 02:24:37', NULL, '2026-05-26 01:40:33', '2026-05-26 02:24:37'),
(3, 1, 9, 34, NULL, NULL, 0.00, 0.00, '2026-05-26 02:24:47', NULL, '2026-05-26 01:40:59', '2026-05-26 02:24:47'),
(4, 1, 10, 40, NULL, NULL, 0.00, 0.00, '2026-05-26 02:21:02', NULL, '2026-05-26 01:41:02', '2026-05-26 02:21:02'),
(5, 1, 11, 44, NULL, NULL, 0.00, 0.00, '2026-05-26 02:21:04', NULL, '2026-05-26 02:12:49', '2026-05-26 02:21:04'),
(6, 1, 6, 21, NULL, NULL, 0.00, 0.00, '2026-05-26 02:15:33', NULL, '2026-05-26 02:15:33', '2026-05-26 02:15:33'),
(7, 1, 12, 46, NULL, NULL, 0.00, 0.00, '2026-05-26 02:24:43', NULL, '2026-05-26 02:15:35', '2026-05-26 02:24:43'),
(8, 1, 7, 25, NULL, NULL, 0.00, 0.00, '2026-05-26 02:15:36', NULL, '2026-05-26 02:15:36', '2026-05-26 02:15:36'),
(9, 1, 5, 17, NULL, NULL, 0.00, 0.00, '2026-05-26 02:15:39', NULL, '2026-05-26 02:15:39', '2026-05-26 02:15:39');

-- --------------------------------------------------------

--
-- Table structure for table `exam_attempts`
--

CREATE TABLE `exam_attempts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `exam_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `enrollment_id` bigint(20) UNSIGNED NOT NULL,
  `started_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `submitted_at` timestamp NULL DEFAULT NULL,
  `ended_at` timestamp NULL DEFAULT NULL,
  `total_questions` int(11) NOT NULL DEFAULT 0,
  `answered_count` int(11) NOT NULL DEFAULT 0,
  `correct_count` int(11) NOT NULL DEFAULT 0,
  `wrong_count` int(11) NOT NULL DEFAULT 0,
  `skipped_count` int(11) NOT NULL DEFAULT 0,
  `marks_obtained` decimal(8,2) NOT NULL DEFAULT 0.00,
  `negative_marks` decimal(8,2) NOT NULL DEFAULT 0.00,
  `total_marks` decimal(8,2) NOT NULL DEFAULT 0.00,
  `percentage` decimal(5,2) NOT NULL DEFAULT 0.00,
  `status` enum('in_progress','submitted','graded','time_expired') NOT NULL DEFAULT 'in_progress',
  `is_passed` tinyint(1) DEFAULT NULL,
  `attempt_number` int(11) NOT NULL DEFAULT 1,
  `ip_address` varchar(255) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exam_attempts`
--

INSERT INTO `exam_attempts` (`id`, `exam_id`, `user_id`, `enrollment_id`, `started_at`, `submitted_at`, `ended_at`, `total_questions`, `answered_count`, `correct_count`, `wrong_count`, `skipped_count`, `marks_obtained`, `negative_marks`, `total_marks`, `percentage`, `status`, `is_passed`, `attempt_number`, `ip_address`, `user_agent`, `created_at`, `updated_at`) VALUES
(1, 2, 6, 1, '2026-05-26 16:28:13', NULL, NULL, 0, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, 'in_progress', 1, 1, NULL, NULL, '2026-05-26 01:19:44', '2026-05-26 10:58:13');

-- --------------------------------------------------------

--
-- Table structure for table `exam_questions`
--

CREATE TABLE `exam_questions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `exam_id` bigint(20) UNSIGNED NOT NULL,
  `section_id` bigint(20) UNSIGNED DEFAULT NULL,
  `question_text` text NOT NULL,
  `question_image` text DEFAULT NULL,
  `question_type` enum('single_choice','multiple_choice','true_false') NOT NULL DEFAULT 'single_choice',
  `explanation` text DEFAULT NULL,
  `marks` int(11) NOT NULL DEFAULT 1,
  `negative_marks` decimal(4,2) NOT NULL DEFAULT 0.00,
  `order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exam_questions`
--

INSERT INTO `exam_questions` (`id`, `exam_id`, `section_id`, `question_text`, `question_image`, `question_type`, `explanation`, `marks`, `negative_marks`, `order`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Which is correct?', NULL, 'single_choice', NULL, 1, 0.00, 1, '2026-05-25 22:40:54', '2026-05-25 22:40:54'),
(2, 1, NULL, 'What is the capital of France?', NULL, 'single_choice', NULL, 1, 0.25, 2, '2026-05-25 22:43:03', '2026-05-25 22:43:03'),
(3, 1, NULL, 'What is 2+2?', NULL, 'single_choice', NULL, 1, 0.25, 3, '2026-05-25 22:43:03', '2026-05-25 22:43:03'),
(4, 1, NULL, 'Which planet is closest to the Sun?', NULL, 'single_choice', NULL, 1, 0.25, 4, '2026-05-25 22:43:03', '2026-05-25 22:43:03'),
(5, 2, NULL, 'What is the capital of France?', NULL, 'single_choice', NULL, 1, 0.00, 1, '2026-05-26 00:15:45', '2026-05-26 00:15:45'),
(6, 2, NULL, 'What is 2+2?', NULL, 'single_choice', NULL, 1, 0.00, 2, '2026-05-26 00:15:46', '2026-05-26 00:15:46'),
(7, 2, NULL, 'Which planet is closest to the Sun?', NULL, 'single_choice', NULL, 1, 0.00, 3, '2026-05-26 00:15:46', '2026-05-26 00:15:46'),
(8, 2, 2, 'What is the capital of France?', NULL, 'single_choice', NULL, 1, 0.00, 4, '2026-05-26 00:17:01', '2026-05-26 00:17:01'),
(9, 2, 2, 'What is 2+2?', NULL, 'single_choice', NULL, 1, 0.00, 5, '2026-05-26 00:17:01', '2026-05-26 00:17:01'),
(10, 2, 2, 'Which planet is closest to the Sun?', NULL, 'single_choice', NULL, 1, 0.00, 6, '2026-05-26 00:17:01', '2026-05-26 00:17:01'),
(11, 2, 3, 'What is the capital of France?', NULL, 'single_choice', NULL, 1, 0.00, 7, '2026-05-26 00:17:28', '2026-05-26 00:17:28'),
(12, 2, 3, 'What is 2+2?', NULL, 'single_choice', NULL, 1, 0.00, 8, '2026-05-26 00:17:28', '2026-05-26 00:17:28'),
(13, 2, 3, 'Which planet is closest to the Sun?', NULL, 'single_choice', NULL, 1, 0.00, 9, '2026-05-26 00:17:28', '2026-05-26 00:17:28'),
(14, 4, 5, 'Who are you?', NULL, 'single_choice', NULL, 1, 0.00, 1, '2026-05-27 22:18:48', '2026-05-27 22:18:48');

-- --------------------------------------------------------

--
-- Table structure for table `exam_question_options`
--

CREATE TABLE `exam_question_options` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `question_id` bigint(20) UNSIGNED NOT NULL,
  `option_text` text NOT NULL,
  `option_image` text DEFAULT NULL,
  `is_correct` tinyint(1) NOT NULL DEFAULT 0,
  `order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exam_question_options`
--

INSERT INTO `exam_question_options` (`id`, `question_id`, `option_text`, `option_image`, `is_correct`, `order`, `created_at`, `updated_at`) VALUES
(1, 1, 'True', NULL, 1, 1, '2026-05-25 22:40:54', '2026-05-25 22:40:54'),
(2, 1, 'False', NULL, 0, 2, '2026-05-25 22:40:54', '2026-05-25 22:40:54'),
(3, 1, 'TREE', NULL, 0, 3, '2026-05-25 22:40:54', '2026-05-25 22:40:54'),
(4, 1, 'FLAHH', NULL, 0, 4, '2026-05-25 22:40:54', '2026-05-25 22:40:54'),
(5, 2, 'London', NULL, 0, 1, '2026-05-25 22:43:03', '2026-05-25 22:43:03'),
(6, 2, 'Berlin', NULL, 0, 2, '2026-05-25 22:43:03', '2026-05-25 22:43:03'),
(7, 2, 'Paris', NULL, 1, 3, '2026-05-25 22:43:03', '2026-05-25 22:43:03'),
(8, 2, 'Madrid', NULL, 0, 4, '2026-05-25 22:43:03', '2026-05-25 22:43:03'),
(9, 3, '2', NULL, 0, 1, '2026-05-25 22:43:03', '2026-05-25 22:43:03'),
(10, 3, '3', NULL, 0, 2, '2026-05-25 22:43:03', '2026-05-25 22:43:03'),
(11, 3, '4', NULL, 1, 3, '2026-05-25 22:43:03', '2026-05-25 22:43:03'),
(12, 3, '5', NULL, 0, 4, '2026-05-25 22:43:03', '2026-05-25 22:43:03'),
(13, 4, 'Venus', NULL, 0, 1, '2026-05-25 22:43:03', '2026-05-25 22:43:03'),
(14, 4, 'Earth', NULL, 0, 2, '2026-05-25 22:43:03', '2026-05-25 22:43:03'),
(15, 4, 'Mars', NULL, 0, 3, '2026-05-25 22:43:03', '2026-05-25 22:43:03'),
(16, 4, 'Mercury', NULL, 1, 4, '2026-05-25 22:43:03', '2026-05-25 22:43:03'),
(17, 5, 'London', NULL, 0, 1, '2026-05-26 00:15:46', '2026-05-26 00:15:46'),
(18, 5, 'Berlin', NULL, 0, 2, '2026-05-26 00:15:46', '2026-05-26 00:15:46'),
(19, 5, 'Paris', NULL, 1, 3, '2026-05-26 00:15:46', '2026-05-26 00:15:46'),
(20, 5, 'Madrid', NULL, 0, 4, '2026-05-26 00:15:46', '2026-05-26 00:15:46'),
(21, 6, '2', NULL, 0, 1, '2026-05-26 00:15:46', '2026-05-26 00:15:46'),
(22, 6, '3', NULL, 0, 2, '2026-05-26 00:15:46', '2026-05-26 00:15:46'),
(23, 6, '4', NULL, 1, 3, '2026-05-26 00:15:46', '2026-05-26 00:15:46'),
(24, 6, '5', NULL, 0, 4, '2026-05-26 00:15:46', '2026-05-26 00:15:46'),
(25, 7, 'Venus', NULL, 0, 1, '2026-05-26 00:15:46', '2026-05-26 00:15:46'),
(26, 7, 'Earth', NULL, 0, 2, '2026-05-26 00:15:46', '2026-05-26 00:15:46'),
(27, 7, 'Mars', NULL, 0, 3, '2026-05-26 00:15:46', '2026-05-26 00:15:46'),
(28, 7, 'Mercury', NULL, 1, 4, '2026-05-26 00:15:46', '2026-05-26 00:15:46'),
(29, 8, 'London', NULL, 0, 1, '2026-05-26 00:17:01', '2026-05-26 00:17:01'),
(30, 8, 'Berlin', NULL, 0, 2, '2026-05-26 00:17:01', '2026-05-26 00:17:01'),
(31, 8, 'Paris', NULL, 1, 3, '2026-05-26 00:17:01', '2026-05-26 00:17:01'),
(32, 8, 'Madrid', NULL, 0, 4, '2026-05-26 00:17:01', '2026-05-26 00:17:01'),
(33, 9, '2', NULL, 0, 1, '2026-05-26 00:17:01', '2026-05-26 00:17:01'),
(34, 9, '3', NULL, 0, 2, '2026-05-26 00:17:01', '2026-05-26 00:17:01'),
(35, 9, '4', NULL, 1, 3, '2026-05-26 00:17:01', '2026-05-26 00:17:01'),
(36, 9, '5', NULL, 0, 4, '2026-05-26 00:17:01', '2026-05-26 00:17:01'),
(37, 10, 'Venus', NULL, 0, 1, '2026-05-26 00:17:01', '2026-05-26 00:17:01'),
(38, 10, 'Earth', NULL, 0, 2, '2026-05-26 00:17:01', '2026-05-26 00:17:01'),
(39, 10, 'Mars', NULL, 0, 3, '2026-05-26 00:17:01', '2026-05-26 00:17:01'),
(40, 10, 'Mercury', NULL, 1, 4, '2026-05-26 00:17:01', '2026-05-26 00:17:01'),
(41, 11, 'London', NULL, 0, 1, '2026-05-26 00:17:28', '2026-05-26 00:17:28'),
(42, 11, 'Berlin', NULL, 0, 2, '2026-05-26 00:17:28', '2026-05-26 00:17:28'),
(43, 11, 'Paris', NULL, 1, 3, '2026-05-26 00:17:28', '2026-05-26 00:17:28'),
(44, 11, 'Madrid', NULL, 0, 4, '2026-05-26 00:17:28', '2026-05-26 00:17:28'),
(45, 12, '2', NULL, 0, 1, '2026-05-26 00:17:28', '2026-05-26 00:17:28'),
(46, 12, '3', NULL, 0, 2, '2026-05-26 00:17:28', '2026-05-26 00:17:28'),
(47, 12, '4', NULL, 1, 3, '2026-05-26 00:17:28', '2026-05-26 00:17:28'),
(48, 12, '5', NULL, 0, 4, '2026-05-26 00:17:28', '2026-05-26 00:17:28'),
(49, 13, 'Venus', NULL, 0, 1, '2026-05-26 00:17:28', '2026-05-26 00:17:28'),
(50, 13, 'Earth', NULL, 0, 2, '2026-05-26 00:17:28', '2026-05-26 00:17:28'),
(51, 13, 'Mars', NULL, 0, 3, '2026-05-26 00:17:28', '2026-05-26 00:17:28'),
(52, 13, 'Mercury', NULL, 1, 4, '2026-05-26 00:17:28', '2026-05-26 00:17:28'),
(53, 14, 'True', NULL, 1, 1, '2026-05-27 22:18:48', '2026-05-27 22:18:48'),
(54, 14, 'False', NULL, 0, 2, '2026-05-27 22:18:48', '2026-05-27 22:18:48');

-- --------------------------------------------------------

--
-- Table structure for table `exam_sections`
--

CREATE TABLE `exam_sections` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `exam_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `order` int(11) NOT NULL DEFAULT 0,
  `total_questions` int(11) NOT NULL DEFAULT 0,
  `marks_per_question` int(11) NOT NULL DEFAULT 1,
  `negative_marks_per_question` decimal(4,2) NOT NULL DEFAULT 0.00,
  `shuffle_questions` tinyint(1) NOT NULL DEFAULT 0,
  `time_limit_minutes` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exam_sections`
--

INSERT INTO `exam_sections` (`id`, `exam_id`, `title`, `description`, `order`, `total_questions`, `marks_per_question`, `negative_marks_per_question`, `shuffle_questions`, `time_limit_minutes`, `created_at`, `updated_at`) VALUES
(1, 1, 'Section A', NULL, 1, 0, 1, 0.00, 0, NULL, '2026-05-25 22:36:48', '2026-05-25 22:36:48'),
(2, 2, 'Section A', NULL, 1, 3, 1, 0.00, 0, NULL, '2026-05-26 00:16:48', '2026-05-26 00:17:01'),
(3, 2, 'Section B', NULL, 2, 3, 1, 0.00, 0, NULL, '2026-05-26 00:17:18', '2026-05-26 00:17:28'),
(4, 3, 'Section A', NULL, 1, 0, 1, 0.00, 0, NULL, '2026-05-26 10:01:23', '2026-05-26 10:01:23'),
(5, 4, 'Part 1', NULL, 1, 0, 1, 0.00, 0, NULL, '2026-05-27 22:18:19', '2026-05-27 22:18:19');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `landing_page_settings`
--

CREATE TABLE `landing_page_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `template` varchar(255) NOT NULL DEFAULT 'modern',
  `primary_color` varchar(255) NOT NULL DEFAULT '#10b981',
  `secondary_color` varchar(255) NOT NULL DEFAULT '#3b82f6',
  `accent_color` varchar(255) NOT NULL DEFAULT '#f59e0b',
  `font_family` varchar(255) NOT NULL DEFAULT 'Inter',
  `hero_section` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`hero_section`)),
  `features_section` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`features_section`)),
  `courses_section` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`courses_section`)),
  `stats_section` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`stats_section`)),
  `testimonials_section` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`testimonials_section`)),
  `cta_section` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`cta_section`)),
  `footer_section` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`footer_section`)),
  `show_navbar` tinyint(1) NOT NULL DEFAULT 1,
  `show_footer` tinyint(1) NOT NULL DEFAULT 1,
  `custom_css` text DEFAULT NULL,
  `custom_js` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `landing_page_settings`
--

INSERT INTO `landing_page_settings` (`id`, `tenant_id`, `template`, `primary_color`, `secondary_color`, `accent_color`, `font_family`, `hero_section`, `features_section`, `courses_section`, `stats_section`, `testimonials_section`, `cta_section`, `footer_section`, `show_navbar`, `show_footer`, `custom_css`, `custom_js`, `created_at`, `updated_at`) VALUES
(1, 1, 'modern', '#10b981', '#3b82f6', '#f59e0b', 'Inter', '{\"enabled\":\"1\",\"title\":\"Welcome to Our Academy\",\"subtitle\":\"Learn. Grow. Succeed.\",\"description\":\"Transform your future with our world-class courses and expert instructors.\",\"cta_text\":\"Explore Courses\",\"cta_link\":\"#courses\"}', '{\"enabled\":\"1\",\"title\":\"Why Choose Us?\",\"subtitle\":\"We provide the best learning experience\"}', '{\"enabled\":\"1\",\"title\":\"Our Popular Courses\",\"show_courses\":\"6\"}', '{\"enabled\":\"1\"}', '{\"enabled\":true,\"title\":\"What Our Students Say\",\"subtitle\":\"Real stories from real learners\",\"testimonials\":[{\"name\":\"John Doe\",\"role\":\"Web Developer\",\"image\":null,\"content\":\"This academy changed my life. The courses are well-structured and the instructors are amazing!\",\"rating\":5},{\"name\":\"Jane Smith\",\"role\":\"Data Scientist\",\"image\":null,\"content\":\"I learned so much in such a short time. Highly recommend to anyone looking to upskill.\",\"rating\":5}]}', '{\"enabled\":\"1\",\"title\":\"Ready to Start Learning?\",\"description\":\"Join thousands of students and start your journey today.\",\"button_text\":\"Get Started Now\"}', '{\"social_links\":{\"facebook\":\"\",\"twitter\":\"\",\"instagram\":\"\",\"linkedin\":\"\",\"youtube\":\"\"},\"copyright\":\"\\u00a9 2026 All rights reserved.\",\"contact_info\":{\"email\":\"contact@academy.com\",\"phone\":\"+1 234 567 890\",\"address\":\"123 Education Street, Learning City\"},\"quick_links\":[{\"text\":\"About Us\",\"url\":\"\\/about\"},{\"text\":\"Courses\",\"url\":\"\\/courses\"},{\"text\":\"Contact\",\"url\":\"\\/contact\"},{\"text\":\"Blog\",\"url\":\"\\/blog\"}]}', 1, 1, NULL, NULL, '2026-05-28 11:02:17', '2026-05-28 11:03:32');

-- --------------------------------------------------------

--
-- Table structure for table `lessons`
--

CREATE TABLE `lessons` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `chapter_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `video_url` varchar(255) DEFAULT NULL,
  `video_type` enum('youtube','vimeo','other') DEFAULT NULL,
  `duration_minutes` int(11) DEFAULT NULL,
  `order` int(11) NOT NULL DEFAULT 0,
  `status` enum('active','inactive','draft') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lessons`
--

INSERT INTO `lessons` (`id`, `chapter_id`, `title`, `description`, `video_url`, `video_type`, `duration_minutes`, `order`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'Part 1', NULL, NULL, 'youtube', NULL, 0, 'active', '2026-05-23 05:39:36', '2026-05-23 05:39:36', NULL),
(2, 1, 'Lession 2', NULL, NULL, 'youtube', NULL, 0, 'active', '2026-05-23 06:17:30', '2026-05-23 06:17:30', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `live_classes`
--

CREATE TABLE `live_classes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `course_id` bigint(20) UNSIGNED NOT NULL,
  `subject_id` bigint(20) UNSIGNED DEFAULT NULL,
  `chapter_id` bigint(20) UNSIGNED DEFAULT NULL,
  `lesson_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `platform` enum('google_meet','zoom','ms_teams','jitsi','other') NOT NULL DEFAULT 'google_meet',
  `meeting_url` varchar(255) NOT NULL,
  `video_url` varchar(255) DEFAULT NULL,
  `meeting_id` varchar(255) DEFAULT NULL,
  `meeting_password` varchar(255) DEFAULT NULL,
  `scheduled_at` datetime NOT NULL,
  `duration_minutes` int(11) NOT NULL DEFAULT 60,
  `status` enum('scheduled','live','completed','cancelled') NOT NULL DEFAULT 'scheduled',
  `is_public` tinyint(1) NOT NULL DEFAULT 0,
  `recurrence` enum('none','daily','weekly') NOT NULL DEFAULT 'none',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `live_classes`
--

INSERT INTO `live_classes` (`id`, `tenant_id`, `course_id`, `subject_id`, `chapter_id`, `lesson_id`, `created_by`, `title`, `description`, `platform`, `meeting_url`, `video_url`, `meeting_id`, `meeting_password`, `scheduled_at`, `duration_minutes`, `status`, `is_public`, `recurrence`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, 1, 1, 1, 2, 'Class Now', NULL, 'google_meet', 'https://meet.google.com/qbr-qfji-uve', NULL, NULL, NULL, '2026-05-25 17:10:00', 120, 'scheduled', 0, 'none', '2026-05-24 06:11:06', '2026-05-24 06:11:06', NULL),
(2, 1, 5, 5, NULL, NULL, 2, 'Test', NULL, 'google_meet', 'https://meet.google.com/qbr-qfji-uve', 'https://www.youtube.com/watch?v=QqI5pMJuFbE', NULL, NULL, '2026-05-28 12:58:00', 60, 'completed', 0, 'none', '2026-05-28 06:28:19', '2026-05-28 07:43:43', NULL),
(3, 1, 5, 5, NULL, NULL, 2, 'fg', NULL, 'google_meet', 'https://meet.google.com/qbr-qfji-uve', NULL, NULL, NULL, '2026-05-28 14:06:00', 60, 'completed', 0, 'none', '2026-05-28 07:36:53', '2026-05-28 08:03:20', NULL),
(4, 1, 5, 5, NULL, NULL, 2, 'asd', NULL, 'google_meet', 'https://meet.google.com/qbr-qfji-uve', NULL, NULL, NULL, '2026-05-28 15:37:00', 60, 'live', 1, 'none', '2026-05-28 09:07:11', '2026-05-28 09:07:35', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_tenants_table', 1),
(2, '0001_01_01_000001_create_users_table', 1),
(3, '0001_01_01_000002_create_cache_table', 1),
(4, '0001_01_01_000003_create_jobs_table', 1),
(5, '2024_01_10_000001_create_courses_table', 1),
(6, '2024_01_10_000002_create_course_teacher_table', 1),
(7, '2024_01_10_000003_create_enrollments_table', 1),
(8, '2024_01_10_000004_create_notices_table', 1),
(9, '2024_01_10_000005_create_permission_tables', 1),
(10, '2024_01_10_000006_create_personal_access_tokens_table', 1),
(11, '2025_05_23_000001_create_curricula_table', 2),
(12, '2025_05_23_000002_create_subjects_table', 2),
(13, '2025_05_23_000003_create_chapters_table', 2),
(14, '2025_05_23_000004_create_lessons_table', 2),
(15, '2025_05_23_000005_create_curriculum_contents_table', 2),
(16, '2025_05_23_000006_add_user_id_to_curriculum_tables', 3),
(17, '2024_01_10_000010_add_fees_type_to_courses_table', 4),
(18, '2025_05_24_000001_add_monthly_fee_settings_and_subscriptions', 5),
(19, '2025_05_24_000002_add_start_date_to_courses_table', 6),
(20, '2025_05_24_000003_add_end_date_to_courses_table', 7),
(21, '2025_05_24_000004_create_payment_requests_table', 8),
(22, '2025_05_24_000005_create_live_classes_table', 9),
(23, '2025_05_24_000006_add_level_ids_to_live_classes_table', 10),
(24, '2025_05_26_000001_create_exams_table', 11),
(25, '2025_05_26_100001_create_notifications_table', 12),
(26, '2025_05_26_200001_create_tenant_registrations_table', 13),
(27, '2025_05_26_300001_create_system_settings_table', 14),
(28, '2026_05_27_020950_create_coupons_table', 15),
(29, '2026_05_27_020950_create_subscription_plans_table', 15),
(30, '2026_05_27_020950_create_subscriptions_table', 15),
(31, '2026_05_27_023627_create_payments_table', 16),
(32, '2026_05_27_025507_create_coupon_plan_table', 17),
(33, '2026_05_27_050000_add_session_tracking_to_users', 18),
(34, '2026_05_27_060000_add_branding_fields_to_tenants', 19),
(35, '2026_05_27_180000_create_monthly_fees_table', 20),
(36, '2025_05_28_000001_add_metadata_to_payment_requests', 21),
(37, '2025_05_28_000002_change_access_dates_to_datetime', 22),
(38, '2025_05_28_000003_add_video_url_to_live_classes', 23),
(39, '2025_05_28_000004_add_is_public_to_live_classes', 24),
(40, '2025_05_28_000005_add_is_downloadable_to_curriculum_notes', 25),
(42, '2025_05_28_000006_add_tenant_id_to_curriculum_notes', 26),
(43, '2025_05_28_000007_create_landing_page_settings_table', 26),
(44, '2025_05_28_000008_create_blogs_table', 26),
(45, '2026_05_29_193241_create_books_table', 27),
(46, '2026_05_29_193315_create_book_orders_table', 27),
(47, '2026_05_29_200625_add_book_id_to_payment_requests', 28),
(48, '2026_05_29_201555_make_course_id_nullable_in_payment_requests', 29),
(49, '2026_05_29_201850_update_payment_type_enum_in_payment_requests', 30);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1),
(1, 'App\\Models\\User', 12),
(1, 'App\\Models\\User', 13),
(2, 'App\\Models\\User', 2),
(2, 'App\\Models\\User', 14),
(3, 'App\\Models\\User', 3),
(3, 'App\\Models\\User', 4),
(3, 'App\\Models\\User', 5),
(4, 'App\\Models\\User', 6),
(4, 'App\\Models\\User', 7),
(4, 'App\\Models\\User', 8),
(4, 'App\\Models\\User', 9),
(4, 'App\\Models\\User', 10),
(4, 'App\\Models\\User', 11),
(4, 'App\\Models\\User', 15);

-- --------------------------------------------------------

--
-- Table structure for table `monthly_fees`
--

CREATE TABLE `monthly_fees` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `enrollment_id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `year` year(4) NOT NULL,
  `month` tinyint(4) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` enum('pending','paid','overdue') NOT NULL DEFAULT 'pending',
  `paid_at` timestamp NULL DEFAULT NULL,
  `payment_method` varchar(255) DEFAULT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `monthly_fees`
--

INSERT INTO `monthly_fees` (`id`, `tenant_id`, `enrollment_id`, `student_id`, `year`, `month`, `amount`, `status`, `paid_at`, `payment_method`, `transaction_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 6, '2026', 5, 2500.00, 'pending', NULL, NULL, NULL, '2026-05-27 12:50:51', '2026-05-27 12:50:51'),
(2, 1, 1, 6, '2026', 6, 2500.00, 'pending', NULL, NULL, NULL, '2026-05-27 12:50:51', '2026-05-27 12:50:51'),
(3, 1, 1, 6, '2026', 7, 2500.00, 'pending', NULL, NULL, NULL, '2026-05-27 12:50:51', '2026-05-27 12:50:51'),
(4, 1, 1, 6, '2026', 8, 2500.00, 'pending', NULL, NULL, NULL, '2026-05-27 12:50:51', '2026-05-27 12:50:51'),
(5, 1, 1, 6, '2026', 9, 2500.00, 'pending', NULL, NULL, NULL, '2026-05-27 12:50:51', '2026-05-27 12:50:51'),
(6, 1, 1, 6, '2026', 10, 2500.00, 'pending', NULL, NULL, NULL, '2026-05-27 12:50:51', '2026-05-27 12:50:51'),
(7, 1, 2, 6, '2026', 5, 3000.00, 'pending', NULL, NULL, NULL, '2026-05-27 12:50:51', '2026-05-27 12:50:51'),
(8, 1, 2, 6, '2026', 6, 3000.00, 'pending', NULL, NULL, NULL, '2026-05-27 12:50:51', '2026-05-27 12:50:51'),
(9, 1, 2, 6, '2026', 7, 3000.00, 'pending', NULL, NULL, NULL, '2026-05-27 12:50:51', '2026-05-27 12:50:51'),
(10, 1, 2, 6, '2026', 8, 3000.00, 'pending', NULL, NULL, NULL, '2026-05-27 12:50:51', '2026-05-27 12:50:51'),
(11, 1, 2, 6, '2026', 9, 3000.00, 'pending', NULL, NULL, NULL, '2026-05-27 12:50:51', '2026-05-27 12:50:51'),
(12, 1, 2, 6, '2026', 10, 3000.00, 'pending', NULL, NULL, NULL, '2026-05-27 12:50:51', '2026-05-27 12:50:51'),
(13, 1, 3, 7, '2026', 5, 2500.00, 'pending', NULL, NULL, NULL, '2026-05-27 12:50:51', '2026-05-27 12:50:51'),
(14, 1, 3, 7, '2026', 6, 2500.00, 'pending', NULL, NULL, NULL, '2026-05-27 12:50:51', '2026-05-27 12:50:51'),
(15, 1, 3, 7, '2026', 7, 2500.00, 'pending', NULL, NULL, NULL, '2026-05-27 12:50:51', '2026-05-27 12:50:51'),
(16, 1, 3, 7, '2026', 8, 2500.00, 'pending', NULL, NULL, NULL, '2026-05-27 12:50:51', '2026-05-27 12:50:51'),
(17, 1, 3, 7, '2026', 9, 2500.00, 'pending', NULL, NULL, NULL, '2026-05-27 12:50:51', '2026-05-27 12:50:51'),
(18, 1, 3, 7, '2026', 10, 2500.00, 'pending', NULL, NULL, NULL, '2026-05-27 12:50:51', '2026-05-27 12:50:51'),
(19, 1, 5, 8, '2026', 5, 3000.00, 'pending', NULL, NULL, NULL, '2026-05-27 12:50:51', '2026-05-27 12:50:51'),
(20, 1, 5, 8, '2026', 6, 3000.00, 'pending', NULL, NULL, NULL, '2026-05-27 12:50:51', '2026-05-27 12:50:51'),
(21, 1, 5, 8, '2026', 7, 3000.00, 'pending', NULL, NULL, NULL, '2026-05-27 12:50:51', '2026-05-27 12:50:51'),
(22, 1, 5, 8, '2026', 8, 3000.00, 'pending', NULL, NULL, NULL, '2026-05-27 12:50:51', '2026-05-27 12:50:51'),
(23, 1, 5, 8, '2026', 9, 3000.00, 'pending', NULL, NULL, NULL, '2026-05-27 12:50:51', '2026-05-27 12:50:51'),
(24, 1, 5, 8, '2026', 10, 3000.00, 'pending', NULL, NULL, NULL, '2026-05-27 12:50:51', '2026-05-27 12:50:51'),
(25, 1, 6, 9, '2026', 5, 2666.67, 'pending', NULL, NULL, NULL, '2026-05-27 12:50:51', '2026-05-27 12:50:51'),
(26, 1, 6, 9, '2026', 6, 2666.67, 'pending', NULL, NULL, NULL, '2026-05-27 12:50:51', '2026-05-27 12:50:51'),
(27, 1, 6, 9, '2026', 7, 2666.67, 'pending', NULL, NULL, NULL, '2026-05-27 12:50:51', '2026-05-27 12:50:51'),
(28, 1, 6, 9, '2026', 8, 2666.67, 'pending', NULL, NULL, NULL, '2026-05-27 12:50:51', '2026-05-27 12:50:51'),
(29, 1, 6, 9, '2026', 9, 2666.67, 'pending', NULL, NULL, NULL, '2026-05-27 12:50:51', '2026-05-27 12:50:51'),
(30, 1, 6, 9, '2026', 10, 2666.67, 'pending', NULL, NULL, NULL, '2026-05-27 12:50:51', '2026-05-27 12:50:51'),
(31, 1, 9, 6, '2026', 5, 166.67, 'pending', NULL, NULL, NULL, '2026-05-27 12:50:51', '2026-05-27 12:50:51'),
(32, 1, 9, 6, '2026', 6, 166.67, 'pending', NULL, NULL, NULL, '2026-05-27 12:50:51', '2026-05-27 12:50:51'),
(33, 1, 9, 6, '2026', 7, 166.67, 'pending', NULL, NULL, NULL, '2026-05-27 12:50:51', '2026-05-27 12:50:51'),
(34, 1, 9, 6, '2026', 8, 166.67, 'pending', NULL, NULL, NULL, '2026-05-27 12:50:51', '2026-05-27 12:50:51'),
(35, 1, 9, 6, '2026', 9, 166.67, 'pending', NULL, NULL, NULL, '2026-05-27 12:50:51', '2026-05-27 12:50:51'),
(36, 1, 9, 6, '2026', 10, 166.67, 'pending', NULL, NULL, NULL, '2026-05-27 12:50:51', '2026-05-27 12:50:51'),
(37, 1, 8, 11, '2026', 5, 1000.00, 'paid', '2026-05-27 22:59:18', 'online', NULL, '2026-05-27 22:42:59', '2026-05-27 22:59:18'),
(38, 1, 11, 11, '2026', 5, 800.00, 'paid', '2026-05-28 06:09:21', 'online', NULL, '2026-05-28 05:57:26', '2026-05-28 06:09:21'),
(39, 1, 12, 11, '2026', 5, 50.00, 'paid', '2026-05-28 06:23:06', 'online', NULL, '2026-05-28 06:15:11', '2026-05-28 06:23:06');

-- --------------------------------------------------------

--
-- Table structure for table `notices`
--

CREATE TABLE `notices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'general',
  `audience` varchar(255) NOT NULL DEFAULT 'all',
  `course_id` bigint(20) UNSIGNED DEFAULT NULL,
  `publish_at` timestamp NULL DEFAULT NULL,
  `expire_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notices`
--

INSERT INTO `notices` (`id`, `tenant_id`, `created_by`, `title`, `content`, `type`, `audience`, `course_id`, `publish_at`, `expire_at`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 2, 'Urgent', 'Urgent', 'important', 'all', 1, NULL, NULL, 1, '2026-05-26 10:27:10', '2026-05-26 10:27:10', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `password_reset_tokens`
--

INSERT INTO `password_reset_tokens` (`email`, `token`, `created_at`) VALUES
('admin@btguru.in', '$2y$12$sBIO2gv93lC28uBZMAs/8e8l2zjLtBzda9rAZDNOdBzefe302KSU6', '2026-05-28 15:42:05');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `subscription_id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `payment_method` enum('razorpay','upi_qr','manual') NOT NULL DEFAULT 'razorpay',
  `amount` decimal(10,2) NOT NULL,
  `currency` varchar(3) NOT NULL DEFAULT 'INR',
  `payment_status` enum('pending','processing','completed','failed','refunded') NOT NULL DEFAULT 'pending',
  `transaction_id` varchar(255) DEFAULT NULL,
  `razorpay_order_id` varchar(255) DEFAULT NULL,
  `razorpay_payment_id` varchar(255) DEFAULT NULL,
  `razorpay_signature` varchar(255) DEFAULT NULL,
  `qr_code` varchar(255) DEFAULT NULL,
  `upi_id` varchar(255) DEFAULT NULL,
  `upi_transaction_id` varchar(255) DEFAULT NULL,
  `screenshot_path` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `failed_at` timestamp NULL DEFAULT NULL,
  `refund_amount` decimal(10,2) DEFAULT NULL,
  `refund_reason` varchar(255) DEFAULT NULL,
  `refunded_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `subscription_id`, `tenant_id`, `payment_method`, `amount`, `currency`, `payment_status`, `transaction_id`, `razorpay_order_id`, `razorpay_payment_id`, `razorpay_signature`, `qr_code`, `upi_id`, `upi_transaction_id`, `screenshot_path`, `notes`, `paid_at`, `failed_at`, `refund_amount`, `refund_reason`, `refunded_at`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 4, 1, 'upi_qr', 500.00, 'INR', 'processing', '32746801368', NULL, NULL, NULL, NULL, '', '32746801368', 'payment_screenshots/ddizQmUB5jze8foJqumNeWJDXHnmdnqNB5rFW5Oe.jpg', NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-26 21:31:22', '2026-05-26 21:31:22', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `payment_requests`
--

CREATE TABLE `payment_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `course_id` bigint(20) UNSIGNED DEFAULT NULL,
  `book_id` bigint(20) UNSIGNED DEFAULT NULL,
  `enrollment_id` bigint(20) UNSIGNED DEFAULT NULL,
  `payment_type` varchar(30) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `reference_number` varchar(255) DEFAULT NULL,
  `screenshot` varchar(255) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `admin_remark` text DEFAULT NULL,
  `reviewed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `month_number` int(11) DEFAULT NULL,
  `year_number` int(11) DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payment_requests`
--

INSERT INTO `payment_requests` (`id`, `tenant_id`, `student_id`, `course_id`, `book_id`, `enrollment_id`, `payment_type`, `amount`, `reference_number`, `screenshot`, `note`, `status`, `admin_remark`, `reviewed_by`, `reviewed_at`, `month_number`, `year_number`, `metadata`, `created_at`, `updated_at`) VALUES
(1, 1, 6, 4, NULL, 9, 'enrollment', 1000.00, '521467444224132465834678', 'payment_screenshots/TmoCiI23rE8AW4NX5KpAX217BCqLkm5D5IXVFDJx.jpg', NULL, 'approved', NULL, 2, '2026-05-24 05:35:21', 5, 2026, NULL, '2026-05-24 05:32:48', '2026-05-24 05:35:21'),
(2, 1, 11, 1, NULL, 8, 'monthly', 1000.00, NULL, 'payment_screenshots/jxAkYgWDuRMwYzYRLn7S8yyxq6ilftp9PLs2Fa0q.png', NULL, 'approved', NULL, 2, '2026-05-27 22:59:17', 5, 2026, NULL, '2026-05-27 22:34:58', '2026-05-27 22:59:18'),
(3, 1, 11, 2, NULL, NULL, 'monthly', 800.00, NULL, 'payment_screenshots/K3vxiTMTAGxG9vUNwDZBhJKRHEI8s555ovzqdeXc.png', NULL, 'rejected', 'g', 2, '2026-05-28 06:12:19', NULL, NULL, NULL, '2026-05-27 23:54:02', '2026-05-28 06:12:19'),
(4, 1, 11, 2, NULL, NULL, 'monthly', 800.00, NULL, 'payment_screenshots/6z8URnUdViWC5cvU5q1Nq3VQQrAJGdkS33ToH6Pc.png', NULL, 'rejected', 'go', 2, '2026-05-28 05:56:39', NULL, NULL, NULL, '2026-05-28 00:18:41', '2026-05-28 05:56:39'),
(5, 1, 11, 2, NULL, NULL, 'monthly', 800.00, NULL, 'payment_screenshots/JtKxEddlm8XGBsILdwthIewL4ZcRhoKK1TgpiDDt.png', NULL, 'rejected', 'h', 2, '2026-05-28 06:11:55', NULL, NULL, NULL, '2026-05-28 05:57:14', '2026-05-28 06:11:55'),
(6, 1, 11, 5, NULL, 12, 'monthly', 50.00, NULL, 'payment_screenshots/bEV3vI9aWSVy3gb02nHKLEDoOYUvYoIdhRMlF8WO.png', NULL, 'approved', NULL, 2, '2026-05-28 06:23:06', NULL, NULL, NULL, '2026-05-28 06:15:01', '2026-05-28 06:23:06'),
(7, 1, 6, NULL, 2, NULL, 'book_purchase', 499.97, NULL, 'payment_screenshots/AnhY9NkI44Vs9ZNTDZiAQqkLcJcbtIUT6QmkRFEY.png', 'Book: Test 2 phy\nOrder Type: Physical\nDelivery Address: dfsdfsdf\nContact: 78924785412', 'approved', NULL, 2, '2026-05-29 14:59:33', NULL, NULL, '{\"order_type\":\"physical\",\"pdf_price\":0,\"physical_price\":\"499.97\",\"delivery_address\":\"dfsdfsdf\",\"delivery_phone\":\"78924785412\"}', '2026-05-29 14:49:44', '2026-05-29 14:59:33');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'manage_tenants', 'web', '2026-05-23 04:57:21', '2026-05-23 04:57:21'),
(2, 'manage_all_users', 'web', '2026-05-23 04:57:21', '2026-05-23 04:57:21'),
(3, 'view_analytics', 'web', '2026-05-23 04:57:21', '2026-05-23 04:57:21'),
(4, 'manage_plans', 'web', '2026-05-23 04:57:21', '2026-05-23 04:57:21'),
(5, 'manage_domains', 'web', '2026-05-23 04:57:21', '2026-05-23 04:57:21'),
(6, 'suspend_tenants', 'web', '2026-05-23 04:57:21', '2026-05-23 04:57:21'),
(7, 'manage_courses', 'web', '2026-05-23 04:57:21', '2026-05-23 04:57:21'),
(8, 'manage_teachers', 'web', '2026-05-23 04:57:21', '2026-05-23 04:57:21'),
(9, 'manage_students', 'web', '2026-05-23 04:57:21', '2026-05-23 04:57:21'),
(10, 'manage_enrollments', 'web', '2026-05-23 04:57:22', '2026-05-23 04:57:22'),
(11, 'manage_fees', 'web', '2026-05-23 04:57:22', '2026-05-23 04:57:22'),
(12, 'manage_notices', 'web', '2026-05-23 04:57:22', '2026-05-23 04:57:22'),
(13, 'view_tenant_reports', 'web', '2026-05-23 04:57:22', '2026-05-23 04:57:22'),
(14, 'manage_tenant_settings', 'web', '2026-05-23 04:57:22', '2026-05-23 04:57:22'),
(15, 'approve_admissions', 'web', '2026-05-23 04:57:22', '2026-05-23 04:57:22'),
(16, 'view_assigned_courses', 'web', '2026-05-23 04:57:22', '2026-05-23 04:57:22'),
(17, 'view_course_students', 'web', '2026-05-23 04:57:22', '2026-05-23 04:57:22'),
(18, 'manage_attendance', 'web', '2026-05-23 04:57:22', '2026-05-23 04:57:22'),
(19, 'upload_materials', 'web', '2026-05-23 04:57:22', '2026-05-23 04:57:22'),
(20, 'view_teacher_dashboard', 'web', '2026-05-23 04:57:22', '2026-05-23 04:57:22'),
(21, 'view_enrolled_courses', 'web', '2026-05-23 04:57:22', '2026-05-23 04:57:22'),
(22, 'view_notices', 'web', '2026-05-23 04:57:22', '2026-05-23 04:57:22'),
(23, 'view_fee_status', 'web', '2026-05-23 04:57:22', '2026-05-23 04:57:22'),
(24, 'view_attendance', 'web', '2026-05-23 04:57:22', '2026-05-23 04:57:22'),
(25, 'view_student_dashboard', 'web', '2026-05-23 04:57:22', '2026-05-23 04:57:22'),
(26, 'access_course_materials', 'web', '2026-05-23 04:57:22', '2026-05-23 04:57:22');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'super_admin', 'web', '2026-05-23 04:57:22', '2026-05-23 04:57:22'),
(2, 'tenant_admin', 'web', '2026-05-23 04:57:22', '2026-05-23 04:57:22'),
(3, 'teacher', 'web', '2026-05-23 04:57:22', '2026-05-23 04:57:22'),
(4, 'student', 'web', '2026-05-23 04:57:22', '2026-05-23 04:57:22');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(6, 1),
(7, 2),
(8, 2),
(9, 2),
(10, 2),
(11, 2),
(12, 2),
(13, 2),
(14, 2),
(15, 2),
(16, 3),
(17, 3),
(18, 3),
(19, 3),
(20, 3),
(21, 4),
(22, 4),
(23, 4),
(24, 4),
(25, 4),
(26, 4);

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_notifications`
--

CREATE TABLE `student_notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `body` text DEFAULT NULL,
  `icon` varchar(255) NOT NULL DEFAULT 'bell',
  `url` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `student_notifications`
--

INSERT INTO `student_notifications` (`id`, `tenant_id`, `user_id`, `type`, `title`, `body`, `icon`, `url`, `is_read`, `read_at`, `created_at`, `updated_at`) VALUES
(1, 1, 6, 'exam', 'New Exam: Test524', 'A new exam has been published in Mathematics Mastery.', 'exam', '/student/exams', 1, '2026-05-27 22:19:24', '2026-05-27 22:19:12', '2026-05-27 22:19:24'),
(2, 1, 7, 'exam', 'New Exam: Test524', 'A new exam has been published in Mathematics Mastery.', 'exam', '/student/exams', 0, NULL, '2026-05-27 22:19:12', '2026-05-27 22:19:12'),
(3, 1, 11, 'payment', 'Payment Verified', 'Your payment for Mathematics Mastery has been verified.', 'payment', '/student/payments', 1, '2026-05-27 22:36:00', '2026-05-27 22:35:12', '2026-05-27 22:36:00'),
(4, 1, 11, 'payment', 'Payment Verified', 'Your payment for Mathematics Mastery has been verified.', 'payment', '/student/payments', 1, '2026-05-27 22:44:58', '2026-05-27 22:40:34', '2026-05-27 22:44:58'),
(5, 1, 11, 'payment', 'Payment Verified', 'Your payment for Mathematics Mastery has been verified.', 'payment', '/student/payments', 1, '2026-05-27 22:43:25', '2026-05-27 22:42:59', '2026-05-27 22:43:25'),
(6, 1, 11, 'payment', 'Payment Verified', 'Your payment for Mathematics Mastery has been verified.', 'payment', '/student/payments', 1, '2026-05-27 23:01:17', '2026-05-27 22:59:18', '2026-05-27 23:01:17'),
(7, 1, 11, 'payment', 'Payment Verified', 'Your payment for Physics Fundamentals has been verified.', 'payment', '/student/payments', 0, NULL, '2026-05-27 23:54:15', '2026-05-27 23:54:15'),
(8, 1, 11, 'payment', 'Payment Verified', 'Your payment for Physics Fundamentals has been verified.', 'payment', '/student/payments', 1, '2026-05-28 00:17:41', '2026-05-27 23:57:05', '2026-05-28 00:17:41'),
(9, 1, 11, 'payment', 'Payment Verified', 'Your payment for Physics Fundamentals has been verified.', 'payment', '/student/payments', 0, NULL, '2026-05-28 00:18:49', '2026-05-28 00:18:49'),
(10, 1, 11, 'payment', 'Payment Verified', 'Your payment for Physics Fundamentals has been verified.', 'payment', '/student/payments', 0, NULL, '2026-05-28 05:57:26', '2026-05-28 05:57:26'),
(11, 1, 11, 'payment', 'Payment Verified', 'Your payment for Physics Fundamentals has been verified.', 'payment', '/student/payments', 0, NULL, '2026-05-28 06:09:21', '2026-05-28 06:09:21'),
(12, 1, 11, 'payment', 'Payment Verified', 'Your payment for TE has been verified.', 'payment', '/student/payments', 0, NULL, '2026-05-28 06:15:11', '2026-05-28 06:15:11'),
(13, 1, 11, 'payment', 'Payment Verified', 'Your payment for TE has been verified.', 'payment', '/student/payments', 0, NULL, '2026-05-28 06:16:42', '2026-05-28 06:16:42'),
(14, 1, 11, 'payment', 'Payment Verified', 'Your payment for TE has been verified.', 'payment', '/student/payments', 0, NULL, '2026-05-28 06:19:27', '2026-05-28 06:19:27'),
(15, 1, 11, 'payment', 'Payment Verified', 'Your payment for TE has been verified.', 'payment', '/student/payments', 0, NULL, '2026-05-28 06:21:18', '2026-05-28 06:21:18'),
(16, 1, 11, 'payment', 'Payment Verified', 'Your payment for TE has been verified.', 'payment', '/student/payments', 0, NULL, '2026-05-28 06:23:07', '2026-05-28 06:23:07'),
(17, 1, 11, 'live_class', 'Live Class: Test', 'Scheduled on 28 May, 12:58 PM — TE', 'live', '/student/live-classes', 0, NULL, '2026-05-28 06:28:19', '2026-05-28 06:28:19'),
(18, 1, 11, 'live_class', 'Live Class: fg', 'Scheduled on 28 May, 02:06 PM — TE', 'live', '/student/live-classes', 0, NULL, '2026-05-28 07:36:53', '2026-05-28 07:36:53'),
(19, 1, 11, 'live_class', '🔴 LIVE NOW: fg', 'Your live class has started! Click to join now — TE', 'live', 'https://meet.google.com/qbr-qfji-uve', 1, '2026-05-28 07:38:59', '2026-05-28 07:36:56', '2026-05-28 07:38:59'),
(20, 1, 11, 'live_class', '✅ Class Ended: Test', 'The live class has ended. Recorded video will be available soon — TE', 'video', '/student/live-classes', 0, NULL, '2026-05-28 07:41:56', '2026-05-28 07:41:56'),
(21, 1, 11, 'live_class', '🔴 LIVE NOW: Test', 'Your live class has started! Click to join now — TE', 'live', 'https://meet.google.com/qbr-qfji-uve', 0, NULL, '2026-05-28 07:42:51', '2026-05-28 07:42:51'),
(22, 1, 11, 'live_class', '✅ Class Ended: Test', 'The live class has ended. Recorded video will be available soon — TE', 'video', '/student/live-classes', 0, NULL, '2026-05-28 07:43:39', '2026-05-28 07:43:39'),
(23, 1, 11, 'video', '📹 Recorded: Test', 'The recorded video is now available — TE', 'video', 'https://www.youtube.com/watch?v=QqI5pMJuFbE', 1, '2026-05-28 07:43:51', '2026-05-28 07:43:43', '2026-05-28 07:43:51'),
(24, 1, 11, 'live_class', '✅ Class Ended: fg', 'The live class has ended. Recorded video will be available soon — TE', 'video', '/student/live-classes', 0, NULL, '2026-05-28 08:03:20', '2026-05-28 08:03:20'),
(25, 1, 6, 'live_class', '🎥 Public Live Class: asd', 'A public live class has been scheduled — open to all students', 'video', '/student/live-classes', 1, '2026-05-28 09:08:27', '2026-05-28 09:07:11', '2026-05-28 09:08:27'),
(26, 1, 7, 'live_class', '🎥 Public Live Class: asd', 'A public live class has been scheduled — open to all students', 'video', '/student/live-classes', 0, NULL, '2026-05-28 09:07:11', '2026-05-28 09:07:11'),
(27, 1, 8, 'live_class', '🎥 Public Live Class: asd', 'A public live class has been scheduled — open to all students', 'video', '/student/live-classes', 0, NULL, '2026-05-28 09:07:11', '2026-05-28 09:07:11'),
(28, 1, 9, 'live_class', '🎥 Public Live Class: asd', 'A public live class has been scheduled — open to all students', 'video', '/student/live-classes', 0, NULL, '2026-05-28 09:07:11', '2026-05-28 09:07:11'),
(29, 1, 10, 'live_class', '🎥 Public Live Class: asd', 'A public live class has been scheduled — open to all students', 'video', '/student/live-classes', 0, NULL, '2026-05-28 09:07:11', '2026-05-28 09:07:11'),
(30, 1, 11, 'live_class', '🎥 Public Live Class: asd', 'A public live class has been scheduled — open to all students', 'video', '/student/live-classes', 0, NULL, '2026-05-28 09:07:11', '2026-05-28 09:07:11'),
(31, 1, 15, 'live_class', '🎥 Public Live Class: asd', 'A public live class has been scheduled — open to all students', 'video', '/student/live-classes', 0, NULL, '2026-05-28 09:07:11', '2026-05-28 09:07:11'),
(32, 1, 11, 'live_class', '🔴 LIVE NOW: asd', 'Your live class has started! Click to join now — TE', 'live', 'https://meet.google.com/qbr-qfji-uve', 0, NULL, '2026-05-28 09:07:35', '2026-05-28 09:07:35'),
(33, 1, 11, 'live_class', '🔴 LIVE NOW: asd', 'Your live class has started! Click to join now — TE', 'live', 'https://meet.google.com/qbr-qfji-uve', 0, NULL, '2026-05-28 09:07:36', '2026-05-28 09:07:36');

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `curriculum_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `order` int(11) NOT NULL DEFAULT 0,
  `status` enum('active','inactive','draft') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `curriculum_id`, `title`, `description`, `order`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'Geo', NULL, 0, 'active', '2026-05-23 05:39:11', '2026-05-23 05:39:11', NULL),
(2, 1, 'History', NULL, 1, 'active', '2026-05-23 12:20:18', '2026-05-23 12:20:18', NULL),
(3, 2, 'A', NULL, 0, 'active', '2026-05-27 23:02:05', '2026-05-27 23:02:05', NULL),
(4, 3, 'B', NULL, 0, 'active', '2026-05-28 06:13:05', '2026-05-28 06:13:05', NULL),
(5, 4, 'A', NULL, 0, 'active', '2026-05-28 06:14:36', '2026-05-28 06:14:36', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `subscriptions`
--

CREATE TABLE `subscriptions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `plan_id` bigint(20) UNSIGNED NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `trial_end_date` date DEFAULT NULL,
  `status` enum('trial','active','expired','cancelled') NOT NULL DEFAULT 'trial',
  `coupon_code_used` varchar(255) DEFAULT NULL,
  `original_price` decimal(10,2) DEFAULT NULL,
  `discount_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `final_price` decimal(10,2) DEFAULT NULL,
  `payment_status` varchar(255) NOT NULL DEFAULT 'pending',
  `payment_method` varchar(255) DEFAULT NULL,
  `payment_id` varchar(255) DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `auto_renew` tinyint(1) NOT NULL DEFAULT 0,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subscriptions`
--

INSERT INTO `subscriptions` (`id`, `tenant_id`, `plan_id`, `start_date`, `end_date`, `trial_end_date`, `status`, `coupon_code_used`, `original_price`, `discount_amount`, `final_price`, `payment_status`, `payment_method`, `payment_id`, `paid_at`, `auto_renew`, `cancelled_at`, `notes`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, '2026-05-27', '2026-06-26', '2026-06-06', 'active', NULL, 500.00, 0.00, 500.00, 'pending', NULL, NULL, NULL, 0, NULL, NULL, '2026-05-26 21:28:44', '2026-05-27 00:15:31', NULL),
(2, 1, 1, '2026-05-27', '2026-06-26', '2026-06-06', 'trial', NULL, 500.00, 0.00, 500.00, 'pending', NULL, NULL, NULL, 0, NULL, NULL, '2026-05-26 21:29:10', '2026-05-26 21:29:10', NULL),
(3, 1, 1, '2026-05-27', '2026-06-26', '2026-06-06', 'trial', NULL, 500.00, 0.00, 500.00, 'pending', NULL, NULL, NULL, 0, NULL, NULL, '2026-05-26 21:29:16', '2026-05-26 21:29:16', NULL),
(4, 1, 1, '2026-05-27', '2026-06-26', '2026-06-06', 'trial', NULL, 500.00, 0.00, 500.00, 'pending', NULL, NULL, NULL, 0, NULL, NULL, '2026-05-26 21:30:42', '2026-05-26 21:30:42', NULL),
(5, 1, 1, '2026-05-27', '2026-06-26', '2026-06-06', 'trial', NULL, 500.00, 0.00, 500.00, 'pending', NULL, NULL, NULL, 0, NULL, NULL, '2026-05-26 21:40:16', '2026-05-26 21:40:16', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `subscription_plans`
--

CREATE TABLE `subscription_plans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `currency` varchar(3) NOT NULL DEFAULT 'INR',
  `duration_days` int(11) NOT NULL,
  `trial_days` int(11) NOT NULL DEFAULT 0,
  `features` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`features`)),
  `is_popular` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subscription_plans`
--

INSERT INTO `subscription_plans` (`id`, `name`, `description`, `price`, `currency`, `duration_days`, `trial_days`, `features`, `is_popular`, `is_active`, `sort_order`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'BASIC', NULL, 500.00, 'INR', 30, 10, NULL, 1, 1, 1, '2026-05-26 21:04:58', '2026-05-26 21:04:58', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES
(1, 'mail_driver', 'smtp', '2026-05-26 11:46:57', '2026-05-26 11:46:57'),
(2, 'mail_host', 'smtp.hostinger.com', '2026-05-26 11:46:57', '2026-05-26 11:46:57'),
(3, 'mail_port', '587', '2026-05-26 11:46:57', '2026-05-26 11:46:57'),
(4, 'mail_username', 'no_reply@btguru.tech', '2026-05-26 11:46:57', '2026-05-26 20:45:43'),
(5, 'mail_encryption', 'tls', '2026-05-26 11:46:57', '2026-05-26 11:47:59'),
(6, 'mail_from_address', 'no_reply@btguru.tech', '2026-05-26 11:46:57', '2026-05-26 20:45:43'),
(7, 'mail_from_name', 'BT Guru', '2026-05-26 11:46:57', '2026-05-26 11:46:57'),
(8, 'mail_password', 'ToThePoint@123', '2026-05-26 11:46:57', '2026-05-26 20:45:43');

-- --------------------------------------------------------

--
-- Table structure for table `tenants`
--

CREATE TABLE `tenants` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `coaching_name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `subdomain` varchar(255) NOT NULL,
  `custom_domain` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `pwa_icon` varchar(255) DEFAULT NULL,
  `portal_icon` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `expires_at` timestamp NULL DEFAULT NULL,
  `settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`settings`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tenants`
--

INSERT INTO `tenants` (`id`, `coaching_name`, `slug`, `subdomain`, `custom_domain`, `logo`, `pwa_icon`, `portal_icon`, `email`, `phone`, `address`, `status`, `expires_at`, `settings`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Future Academy', 'future-academy', 'futureacademy', NULL, NULL, NULL, NULL, 'info@futureacademy.com', '+91-9876543210', '123 Education Street, Knowledge City, India', 'active', NULL, '{\"theme_color\":\"#3b82f6\",\"timezone\":\"Asia\\/Kolkata\",\"currency\":\"INR\",\"upi_id\":\"rohan@upi\",\"upi_name\":\"Future Academy\",\"bank_name\":null,\"bank_account\":null,\"bank_ifsc\":null,\"bank_holder\":null,\"tagline\":null,\"website\":null,\"phone_alt\":null,\"city\":null,\"state\":null,\"pincode\":null,\"facebook\":null,\"instagram\":null,\"youtube\":null,\"telegram\":null,\"whatsapp\":null,\"twitter\":null,\"linkedin\":null,\"mail_driver\":null,\"mail_host\":null,\"mail_port\":\"587\",\"mail_username\":null,\"mail_encryption\":\"tls\",\"mail_from_address\":null,\"mail_from_name\":\"Future Academy\",\"wa_provider\":null,\"wa_api_url\":null,\"wa_api_key\":null,\"wa_instance_id\":null,\"wa_token\":null,\"wa_from_number\":null,\"portal_title\":\"Future Academy\",\"mail_password\":null}', '2026-05-23 04:57:22', '2026-05-27 01:05:17', NULL),
(3, 'Super Academy', 'super-academy', 'superacademy', NULL, NULL, NULL, NULL, 'sourabarui@gmail.com', '+918282924454', 'Subhash Pally, Noapara, Barasat, Kolkata, WB, PIN - 700125, BARASAT, West Bengal, 700125', 'active', NULL, '{\"tagline\":\"Inspiring curiosity, character, and competence\",\"website\":null,\"coaching_type\":\"Competitive Exam (IIT\\/JEE\\/NEET)\",\"phone_alt\":null,\"city\":\"BARASAT\",\"state\":\"West Bengal\",\"pincode\":\"700125\"}', '2026-05-26 20:49:21', '2026-05-26 21:44:10', NULL),
(4, 'School Service Bangla', 'school-service-bangla', 'schoolservicebangla', NULL, NULL, NULL, NULL, 'tojoarjo1728@gmail.com', '+918282924454', 'Subhash Pally, Noapara, Barasat, Kolkata, WB, PIN - 700125', 'active', '2027-06-29 18:30:00', '[]', '2026-05-28 15:58:05', '2026-05-28 15:58:32', '2026-05-28 15:58:32');

-- --------------------------------------------------------

--
-- Table structure for table `tenant_registrations`
--

CREATE TABLE `tenant_registrations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`data`)),
  `token` varchar(64) NOT NULL,
  `otp` varchar(6) DEFAULT NULL,
  `otp_expires_at` timestamp NULL DEFAULT NULL,
  `email_verified` tinyint(1) NOT NULL DEFAULT 0,
  `step` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tenant_registrations`
--

INSERT INTO `tenant_registrations` (`id`, `data`, `token`, `otp`, `otp_expires_at`, `email_verified`, `step`, `created_at`, `updated_at`) VALUES
(1, '{\"coaching_name\":\"My Coaching\",\"subdomain\":\"mycoaching\",\"coaching_type\":\"Competitive Exam (IIT\\/JEE\\/NEET)\",\"tagline\":\"Inspiring curiosity, character, and competence\",\"website\":null,\"email\":\"sourabarui@gmail.com\",\"phone\":\"+918282924454\",\"phone_alt\":null,\"address\":\"Subhash Pally, Noapara, Barasat, Kolkata, WB, PIN - 700125\",\"city\":\"BARASAT\",\"state\":\"West Bengal\",\"pincode\":\"700125\",\"admin_name\":\"Sourav Barui\",\"admin_email\":\"sourabarui@gmail.com\",\"admin_phone\":\"+918282924454\",\"password\":\"$2y$12$ObuzqcA8VreTZ3FOSBuzmuWMHO7HXgQZ2OTAo.aNEVw7zOgdXuCb2\"}', 'ZZFwWm6FUcBDYsnBgfcjxXmo2IZx1TqcYPOfm2oFIxY15EVVlhfWJUsBSs2GOSXb', '460665', '2026-05-26 11:36:52', 0, 5, '2026-05-26 11:21:03', '2026-05-26 11:21:55'),
(3, '{\"coaching_name\":\"School Service Bangla\",\"subdomain\":\"schoolservicebangla\",\"coaching_type\":\"Government Exam (SSC\\/UPSC\\/Banking)\",\"tagline\":null,\"website\":null}', 'NNYZB9dMxZvMOu9X74fCFqNJg4rzKe6Qv1REHx0wloYkwXct5Bkzb4kzMVN3t55a', NULL, NULL, 0, 2, '2026-05-28 13:24:26', '2026-05-28 13:24:26'),
(4, '{\"coaching_name\":\"School Service Bangla\",\"subdomain\":\"schoolservicebangla\",\"coaching_type\":\"Government Exam (SSC\\/UPSC\\/Banking)\",\"tagline\":null,\"website\":null,\"email\":\"tojoarjo1728@gmail.com\",\"phone\":\"6289074234\",\"phone_alt\":null,\"address\":\"NOAPARA\",\"city\":\"BARASAT\",\"state\":\"West Bengal\",\"pincode\":\"700125\",\"admin_name\":\"PRADIP\",\"admin_email\":\"tojoarjo1728@gmail.com\",\"admin_phone\":\"+916289074234\",\"password\":\"$2y$12$zUq5Xa82UIb0wgrCoCKYw.UkF2hacGvEoa7TElBlWqxI66gg1Qrk6\"}', 'AAMaLQgi7gVZRbinnY51BMunv7Ou2NprLbi2JiynIcxNijMf8R1iR2pqsoFkE05g', '824250', '2026-05-28 14:34:02', 0, 5, '2026-05-28 14:15:52', '2026-05-28 14:19:15');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `remember_token` varchar(100) DEFAULT NULL,
  `current_session_id` varchar(255) DEFAULT NULL,
  `last_login_ip` varchar(255) DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `password_changed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `tenant_id`, `name`, `email`, `phone`, `email_verified_at`, `password`, `avatar`, `status`, `remember_token`, `current_session_id`, `last_login_ip`, `last_login_at`, `password_changed_at`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, NULL, 'Super Admin', 'admin@btguru.in', '+91-9999999999', '2026-05-23 04:57:22', '$2y$12$4w5SWiOGaV9A299pupR7cewBAacW0kKn9NZpG2bsGrs8heQoKI/46', NULL, 'active', NULL, NULL, NULL, NULL, NULL, '2026-05-23 04:57:22', '2026-05-23 04:57:22', NULL),
(2, 1, 'John Smith', 'admin@futureacademy.com', '+91-9876543211', '2026-05-23 04:57:22', '$2y$12$jy9vTShqIf3LXhUwhTnZyuDwIGU7YMcYcrKvzo.NoYxEUrFslcYMe', NULL, 'active', NULL, NULL, NULL, NULL, NULL, '2026-05-23 04:57:22', '2026-05-23 04:57:22', NULL),
(3, 1, 'Dr. Sarah Johnson', 'sarah@futureacademy.com', '+91-9876543212', '2026-05-23 04:57:23', '$2y$12$YFTrT27HQkWrjIHDk6Ze.ewHOi74obxwuhqFuwGemPwhuHteEG9AS', NULL, 'active', NULL, NULL, NULL, NULL, NULL, '2026-05-23 04:57:23', '2026-05-23 04:57:23', NULL),
(4, 1, 'Prof. Michael Chen', 'michael@futureacademy.com', '+91-9876543213', '2026-05-23 04:57:23', '$2y$12$0L88hDuevNXU2d9QaN/q4ue4tgV92C0pQStrJQ8m.JldsOa63hqIu', NULL, 'active', NULL, NULL, NULL, NULL, NULL, '2026-05-23 04:57:23', '2026-05-23 04:57:23', NULL),
(5, 1, 'Ms. Priya Sharma', 'priya@futureacademy.com', '+91-9876543214', '2026-05-23 04:57:23', '$2y$12$FCvtQU4Tf70nSAxSHpdzQu10hXtbNAir10oELfbB0Ld86BeTtjBP6', NULL, 'active', NULL, NULL, NULL, NULL, NULL, '2026-05-23 04:57:23', '2026-05-23 04:57:23', NULL),
(6, 1, 'Rahul Kumar', 'rahul@email.com', '+91-8888888881', '2026-05-23 04:57:23', '$2y$12$ifWrPe2B9Rvem2x3yCPAee.9Dj70XzgM262omSgEVewUHhTtW0aGy', NULL, 'active', NULL, 'gb08lyafyDenfbKLkumJJ65RJ86xuv9EtJbNqIuH', '127.0.0.1', '2026-05-29 13:52:03', NULL, '2026-05-23 04:57:23', '2026-05-27 22:30:23', NULL),
(7, 1, 'Emma Wilson', 'emma@email.com', '+91-8888888882', '2026-05-23 04:57:24', '$2y$12$ci.HphqdVXzOsk.GxftYr.VGXU2BFAp.okqofHlV11tTE74M8lt6S', NULL, 'active', NULL, NULL, NULL, NULL, NULL, '2026-05-23 04:57:24', '2026-05-23 04:57:24', NULL),
(8, 1, 'Amit Patel', 'amit@email.com', '+91-8888888883', '2026-05-23 04:57:24', '$2y$12$mnetCVrIaSSMrxgC27.SEOGyGwZOFw0ZQu24IVwXCYL6uD6x.MPbS', NULL, 'active', NULL, NULL, NULL, NULL, NULL, '2026-05-23 04:57:24', '2026-05-23 04:57:24', NULL),
(9, 1, 'Sneha Gupta', 'sneha@email.com', '+91-8888888884', '2026-05-23 04:57:24', '$2y$12$tRHk8/9bqnducRRoJ48k2.8mRVmZy1X/3Fi2cJoIRtEgzkmnJZNom', NULL, 'active', NULL, NULL, NULL, NULL, NULL, '2026-05-23 04:57:24', '2026-05-23 04:57:24', NULL),
(10, 1, 'David Lee', 'david@email.com', '+91-8888888885', '2026-05-23 04:57:24', '$2y$12$uqXIhjc3wSeZoXB/x3NI5.CCjBuOB13Uz2uMdViaAzdER577A7oE.', NULL, 'active', NULL, NULL, NULL, NULL, NULL, '2026-05-23 04:57:24', '2026-05-23 04:57:24', NULL),
(11, 1, 'Priya Patel', 'priya.s@email.com', '+91-8888888886', '2026-05-23 04:57:25', '$2y$12$fYt5voaab7nXGRFOO9ghOOk/eiVa/sR6Ler75RHQ17EZhSS9e1Lq6', NULL, 'active', NULL, 'fSN43zhCgvgRmyKhXCHH0uS99VDqnG595VRECpiz', '127.0.0.1', '2026-05-27 22:30:36', NULL, '2026-05-23 04:57:25', '2026-05-23 04:57:25', NULL),
(12, NULL, 'Super Admin', 'admin@btguru.in', '+91-9999999999', '2026-05-23 04:59:39', '$2y$12$5VgQCQXWaur4cpds6nVmq.BqOz.p5Xvw4tnPPCPu5WJPuGuyXM9Ym', NULL, 'active', NULL, NULL, NULL, NULL, NULL, '2026-05-23 04:59:39', '2026-05-23 04:59:39', NULL),
(13, NULL, 'Super Admin', 'admin@btguru.in', '+91-9999999999', '2026-05-23 05:00:45', '$2y$12$jUSrpQpVZimVAbiEURRyUOGqX0rlo6mfG5h2LjTrx3Fo.XGuS63.q', NULL, 'active', NULL, NULL, NULL, NULL, NULL, '2026-05-23 05:00:45', '2026-05-23 05:00:45', NULL),
(14, 3, 'SUNIL KUMAR BARUI', 'sourabarui@gmail.com', '+918282924454', '2026-05-26 20:49:21', '$2y$12$hat1qfxRL6sYQWz2Ed7AYuuJ.SX7gLVYquZR/8VZmSMrfQ3nUgB.i', NULL, 'active', NULL, NULL, NULL, NULL, NULL, '2026-05-26 20:49:21', '2026-05-26 20:49:21', NULL),
(15, 1, 'Sourav Barui', 'sourabarui@gmail.com', '+918282924454', '2026-05-27 00:59:38', '$2y$12$Elg96y6FVxJMKzmFhhKsN.DrNtrKk7sj3o1QxfByGkW27CkpMvTJm', NULL, 'active', NULL, 'jA0htYD8brkrjHwWzGehoFV7u3lCvhour6yO6Yf0', '127.0.0.1', '2026-05-27 01:00:10', NULL, '2026-05-27 00:59:38', '2026-05-27 01:00:10', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `blogs`
--
ALTER TABLE `blogs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `blogs_slug_unique` (`slug`),
  ADD KEY `blogs_user_id_foreign` (`user_id`),
  ADD KEY `blogs_tenant_id_status_published_at_index` (`tenant_id`,`status`,`published_at`),
  ADD KEY `blogs_tenant_id_category_index` (`tenant_id`,`category`),
  ADD KEY `blogs_tenant_id_is_featured_index` (`tenant_id`,`is_featured`);

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `books_tenant_id_slug_unique` (`tenant_id`,`slug`),
  ADD KEY `books_tenant_id_status_index` (`tenant_id`,`status`),
  ADD KEY `books_tenant_id_type_index` (`tenant_id`,`type`);

--
-- Indexes for table `book_orders`
--
ALTER TABLE `book_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `book_orders_book_id_foreign` (`book_id`),
  ADD KEY `book_orders_student_id_foreign` (`student_id`),
  ADD KEY `book_orders_tenant_id_student_id_index` (`tenant_id`,`student_id`),
  ADD KEY `book_orders_tenant_id_payment_status_index` (`tenant_id`,`payment_status`),
  ADD KEY `book_orders_tenant_id_delivery_status_index` (`tenant_id`,`delivery_status`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `chapters`
--
ALTER TABLE `chapters`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chapters_subject_id_foreign` (`subject_id`);

--
-- Indexes for table `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `coupons_code_unique` (`code`),
  ADD KEY `coupons_code_index` (`code`),
  ADD KEY `coupons_is_active_valid_from_valid_until_index` (`is_active`,`valid_from`,`valid_until`);

--
-- Indexes for table `coupon_plan`
--
ALTER TABLE `coupon_plan`
  ADD PRIMARY KEY (`coupon_id`,`plan_id`),
  ADD KEY `coupon_plan_plan_id_foreign` (`plan_id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `courses_tenant_id_slug_unique` (`tenant_id`,`slug`),
  ADD KEY `courses_tenant_id_status_index` (`tenant_id`,`status`);

--
-- Indexes for table `course_subscriptions`
--
ALTER TABLE `course_subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_subscriptions_enrollment_id_foreign` (`enrollment_id`),
  ADD KEY `course_subscriptions_course_id_foreign` (`course_id`),
  ADD KEY `course_subscriptions_created_by_foreign` (`created_by`),
  ADD KEY `course_subscriptions_student_id_course_id_index` (`student_id`,`course_id`),
  ADD KEY `course_subscriptions_tenant_id_payment_status_index` (`tenant_id`,`payment_status`);

--
-- Indexes for table `course_teacher`
--
ALTER TABLE `course_teacher`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `course_teacher_course_id_teacher_id_unique` (`course_id`,`teacher_id`),
  ADD KEY `course_teacher_teacher_id_foreign` (`teacher_id`);

--
-- Indexes for table `curricula`
--
ALTER TABLE `curricula`
  ADD PRIMARY KEY (`id`),
  ADD KEY `curricula_course_id_foreign` (`course_id`);

--
-- Indexes for table `curriculum_contents`
--
ALTER TABLE `curriculum_contents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `curriculum_contents_contentable_type_contentable_id_index` (`contentable_type`,`contentable_id`),
  ADD KEY `curriculum_contents_user_id_foreign` (`user_id`);

--
-- Indexes for table `curriculum_notes`
--
ALTER TABLE `curriculum_notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `curriculum_notes_noteable_type_noteable_id_index` (`noteable_type`,`noteable_id`),
  ADD KEY `curriculum_notes_user_id_foreign` (`user_id`);

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `enrollments_student_id_course_id_unique` (`student_id`,`course_id`),
  ADD KEY `enrollments_course_id_foreign` (`course_id`),
  ADD KEY `enrollments_approved_by_foreign` (`approved_by`),
  ADD KEY `enrollments_tenant_id_enrollment_status_index` (`tenant_id`,`enrollment_status`),
  ADD KEY `enrollments_tenant_id_payment_status_index` (`tenant_id`,`payment_status`);

--
-- Indexes for table `exams`
--
ALTER TABLE `exams`
  ADD PRIMARY KEY (`id`),
  ADD KEY `exams_course_id_foreign` (`course_id`),
  ADD KEY `exams_subject_id_foreign` (`subject_id`),
  ADD KEY `exams_chapter_id_foreign` (`chapter_id`),
  ADD KEY `exams_lesson_id_foreign` (`lesson_id`),
  ADD KEY `exams_created_by_foreign` (`created_by`),
  ADD KEY `exams_tenant_id_course_id_index` (`tenant_id`,`course_id`),
  ADD KEY `exams_status_start_time_index` (`status`,`start_time`);

--
-- Indexes for table `exam_answers`
--
ALTER TABLE `exam_answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `exam_answers_question_id_foreign` (`question_id`),
  ADD KEY `exam_answers_selected_option_id_foreign` (`selected_option_id`),
  ADD KEY `exam_answers_attempt_id_question_id_index` (`attempt_id`,`question_id`);

--
-- Indexes for table `exam_attempts`
--
ALTER TABLE `exam_attempts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `exam_attempts_exam_id_user_id_attempt_number_unique` (`exam_id`,`user_id`,`attempt_number`),
  ADD KEY `exam_attempts_user_id_foreign` (`user_id`),
  ADD KEY `exam_attempts_enrollment_id_foreign` (`enrollment_id`),
  ADD KEY `exam_attempts_exam_id_user_id_index` (`exam_id`,`user_id`),
  ADD KEY `exam_attempts_status_submitted_at_index` (`status`,`submitted_at`);

--
-- Indexes for table `exam_questions`
--
ALTER TABLE `exam_questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `exam_questions_section_id_foreign` (`section_id`),
  ADD KEY `exam_questions_exam_id_section_id_index` (`exam_id`,`section_id`),
  ADD KEY `exam_questions_exam_id_order_index` (`exam_id`,`order`);

--
-- Indexes for table `exam_question_options`
--
ALTER TABLE `exam_question_options`
  ADD PRIMARY KEY (`id`),
  ADD KEY `exam_question_options_question_id_order_index` (`question_id`,`order`);

--
-- Indexes for table `exam_sections`
--
ALTER TABLE `exam_sections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `exam_sections_exam_id_order_index` (`exam_id`,`order`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `landing_page_settings`
--
ALTER TABLE `landing_page_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `landing_page_settings_tenant_id_unique` (`tenant_id`);

--
-- Indexes for table `lessons`
--
ALTER TABLE `lessons`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lessons_chapter_id_foreign` (`chapter_id`);

--
-- Indexes for table `live_classes`
--
ALTER TABLE `live_classes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `live_classes_tenant_id_foreign` (`tenant_id`),
  ADD KEY `live_classes_course_id_foreign` (`course_id`),
  ADD KEY `live_classes_created_by_foreign` (`created_by`),
  ADD KEY `live_classes_subject_id_foreign` (`subject_id`),
  ADD KEY `live_classes_chapter_id_foreign` (`chapter_id`),
  ADD KEY `live_classes_lesson_id_foreign` (`lesson_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `monthly_fees`
--
ALTER TABLE `monthly_fees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `monthly_fees_enrollment_id_year_month_unique` (`enrollment_id`,`year`,`month`),
  ADD KEY `monthly_fees_student_id_foreign` (`student_id`),
  ADD KEY `monthly_fees_tenant_id_student_id_status_index` (`tenant_id`,`student_id`,`status`);

--
-- Indexes for table `notices`
--
ALTER TABLE `notices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notices_created_by_foreign` (`created_by`),
  ADD KEY `notices_course_id_foreign` (`course_id`),
  ADD KEY `notices_tenant_id_is_active_index` (`tenant_id`,`is_active`),
  ADD KEY `notices_tenant_id_type_index` (`tenant_id`,`type`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payments_subscription_id_payment_status_index` (`subscription_id`,`payment_status`),
  ADD KEY `payments_tenant_id_payment_status_index` (`tenant_id`,`payment_status`),
  ADD KEY `payments_transaction_id_index` (`transaction_id`);

--
-- Indexes for table `payment_requests`
--
ALTER TABLE `payment_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payment_requests_student_id_foreign` (`student_id`),
  ADD KEY `payment_requests_course_id_foreign` (`course_id`),
  ADD KEY `payment_requests_enrollment_id_foreign` (`enrollment_id`),
  ADD KEY `payment_requests_reviewed_by_foreign` (`reviewed_by`),
  ADD KEY `payment_requests_book_id_foreign` (`book_id`),
  ADD KEY `payment_requests_tenant_id_book_id_index` (`tenant_id`,`book_id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `student_notifications`
--
ALTER TABLE `student_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_notifications_tenant_id_foreign` (`tenant_id`),
  ADD KEY `student_notifications_user_id_foreign` (`user_id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subjects_curriculum_id_foreign` (`curriculum_id`);

--
-- Indexes for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subscriptions_plan_id_foreign` (`plan_id`),
  ADD KEY `subscriptions_tenant_id_status_index` (`tenant_id`,`status`),
  ADD KEY `subscriptions_status_end_date_index` (`status`,`end_date`);

--
-- Indexes for table `subscription_plans`
--
ALTER TABLE `subscription_plans`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `system_settings_key_unique` (`key`);

--
-- Indexes for table `tenants`
--
ALTER TABLE `tenants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tenants_slug_unique` (`slug`),
  ADD UNIQUE KEY `tenants_subdomain_unique` (`subdomain`),
  ADD UNIQUE KEY `tenants_custom_domain_unique` (`custom_domain`),
  ADD KEY `tenants_subdomain_index` (`subdomain`),
  ADD KEY `tenants_custom_domain_index` (`custom_domain`),
  ADD KEY `tenants_status_index` (`status`);

--
-- Indexes for table `tenant_registrations`
--
ALTER TABLE `tenant_registrations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tenant_registrations_token_unique` (`token`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_tenant_id_unique` (`email`,`tenant_id`),
  ADD KEY `users_tenant_id_status_index` (`tenant_id`,`status`),
  ADD KEY `users_tenant_id_email_index` (`tenant_id`,`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `blogs`
--
ALTER TABLE `blogs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `book_orders`
--
ALTER TABLE `book_orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `chapters`
--
ALTER TABLE `chapters`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `coupons`
--
ALTER TABLE `coupons`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `course_subscriptions`
--
ALTER TABLE `course_subscriptions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `course_teacher`
--
ALTER TABLE `course_teacher`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `curricula`
--
ALTER TABLE `curricula`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `curriculum_contents`
--
ALTER TABLE `curriculum_contents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `curriculum_notes`
--
ALTER TABLE `curriculum_notes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `exams`
--
ALTER TABLE `exams`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `exam_answers`
--
ALTER TABLE `exam_answers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `exam_attempts`
--
ALTER TABLE `exam_attempts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `exam_questions`
--
ALTER TABLE `exam_questions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `exam_question_options`
--
ALTER TABLE `exam_question_options`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `exam_sections`
--
ALTER TABLE `exam_sections`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `landing_page_settings`
--
ALTER TABLE `landing_page_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `lessons`
--
ALTER TABLE `lessons`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `live_classes`
--
ALTER TABLE `live_classes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `monthly_fees`
--
ALTER TABLE `monthly_fees`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `notices`
--
ALTER TABLE `notices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `payment_requests`
--
ALTER TABLE `payment_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `student_notifications`
--
ALTER TABLE `student_notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `subscriptions`
--
ALTER TABLE `subscriptions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `subscription_plans`
--
ALTER TABLE `subscription_plans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tenants`
--
ALTER TABLE `tenants`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tenant_registrations`
--
ALTER TABLE `tenant_registrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `blogs`
--
ALTER TABLE `blogs`
  ADD CONSTRAINT `blogs_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `blogs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `books_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `book_orders`
--
ALTER TABLE `book_orders`
  ADD CONSTRAINT `book_orders_book_id_foreign` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `book_orders_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `book_orders_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `chapters`
--
ALTER TABLE `chapters`
  ADD CONSTRAINT `chapters_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `coupon_plan`
--
ALTER TABLE `coupon_plan`
  ADD CONSTRAINT `coupon_plan_coupon_id_foreign` FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `coupon_plan_plan_id_foreign` FOREIGN KEY (`plan_id`) REFERENCES `subscription_plans` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `courses_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `course_subscriptions`
--
ALTER TABLE `course_subscriptions`
  ADD CONSTRAINT `course_subscriptions_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `course_subscriptions_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `course_subscriptions_enrollment_id_foreign` FOREIGN KEY (`enrollment_id`) REFERENCES `enrollments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `course_subscriptions_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `course_subscriptions_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `course_teacher`
--
ALTER TABLE `course_teacher`
  ADD CONSTRAINT `course_teacher_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `course_teacher_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `curricula`
--
ALTER TABLE `curricula`
  ADD CONSTRAINT `curricula_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `curriculum_contents`
--
ALTER TABLE `curriculum_contents`
  ADD CONSTRAINT `curriculum_contents_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `curriculum_notes`
--
ALTER TABLE `curriculum_notes`
  ADD CONSTRAINT `curriculum_notes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD CONSTRAINT `enrollments_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `enrollments_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `enrollments_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `enrollments_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `exams`
--
ALTER TABLE `exams`
  ADD CONSTRAINT `exams_chapter_id_foreign` FOREIGN KEY (`chapter_id`) REFERENCES `chapters` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exams_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exams_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exams_lesson_id_foreign` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exams_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exams_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `exam_answers`
--
ALTER TABLE `exam_answers`
  ADD CONSTRAINT `exam_answers_attempt_id_foreign` FOREIGN KEY (`attempt_id`) REFERENCES `exam_attempts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_answers_question_id_foreign` FOREIGN KEY (`question_id`) REFERENCES `exam_questions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_answers_selected_option_id_foreign` FOREIGN KEY (`selected_option_id`) REFERENCES `exam_question_options` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `exam_attempts`
--
ALTER TABLE `exam_attempts`
  ADD CONSTRAINT `exam_attempts_enrollment_id_foreign` FOREIGN KEY (`enrollment_id`) REFERENCES `enrollments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_attempts_exam_id_foreign` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_attempts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `exam_questions`
--
ALTER TABLE `exam_questions`
  ADD CONSTRAINT `exam_questions_exam_id_foreign` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_questions_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `exam_sections` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `exam_question_options`
--
ALTER TABLE `exam_question_options`
  ADD CONSTRAINT `exam_question_options_question_id_foreign` FOREIGN KEY (`question_id`) REFERENCES `exam_questions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `exam_sections`
--
ALTER TABLE `exam_sections`
  ADD CONSTRAINT `exam_sections_exam_id_foreign` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `landing_page_settings`
--
ALTER TABLE `landing_page_settings`
  ADD CONSTRAINT `landing_page_settings_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lessons`
--
ALTER TABLE `lessons`
  ADD CONSTRAINT `lessons_chapter_id_foreign` FOREIGN KEY (`chapter_id`) REFERENCES `chapters` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `live_classes`
--
ALTER TABLE `live_classes`
  ADD CONSTRAINT `live_classes_chapter_id_foreign` FOREIGN KEY (`chapter_id`) REFERENCES `chapters` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `live_classes_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `live_classes_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `live_classes_lesson_id_foreign` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `live_classes_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `live_classes_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `monthly_fees`
--
ALTER TABLE `monthly_fees`
  ADD CONSTRAINT `monthly_fees_enrollment_id_foreign` FOREIGN KEY (`enrollment_id`) REFERENCES `enrollments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `monthly_fees_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `monthly_fees_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notices`
--
ALTER TABLE `notices`
  ADD CONSTRAINT `notices_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notices_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notices_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_subscription_id_foreign` FOREIGN KEY (`subscription_id`) REFERENCES `subscriptions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payment_requests`
--
ALTER TABLE `payment_requests`
  ADD CONSTRAINT `payment_requests_book_id_foreign` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `payment_requests_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payment_requests_enrollment_id_foreign` FOREIGN KEY (`enrollment_id`) REFERENCES `enrollments` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `payment_requests_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `payment_requests_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payment_requests_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_notifications`
--
ALTER TABLE `student_notifications`
  ADD CONSTRAINT `student_notifications_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `subjects`
--
ALTER TABLE `subjects`
  ADD CONSTRAINT `subjects_curriculum_id_foreign` FOREIGN KEY (`curriculum_id`) REFERENCES `curricula` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD CONSTRAINT `subscriptions_plan_id_foreign` FOREIGN KEY (`plan_id`) REFERENCES `subscription_plans` (`id`),
  ADD CONSTRAINT `subscriptions_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
