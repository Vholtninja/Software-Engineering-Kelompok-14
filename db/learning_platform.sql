-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 18, 2025 at 09:59 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `learning_platform`
--

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
('laravel_cache_f6e1126cedebf23e1463aee73f9df08783640400', 'i:1;', 1749312365),
('laravel_cache_f6e1126cedebf23e1463aee73f9df08783640400:timer', 'i:1749312365;', 1749312365),
('studycheck_cache_teacherr@gmail.com|127.0.0.1', 'i:1;', 1750230180),
('studycheck_cache_teacherr@gmail.com|127.0.0.1:timer', 'i:1750230180;', 1750230180);

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
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `slug` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  `level` enum('beginner','intermediate','advanced') NOT NULL,
  `thumbnail` varchar(255) DEFAULT NULL,
  `instructor_id` bigint(20) UNSIGNED NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `duration_minutes` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `title`, `description`, `slug`, `category`, `level`, `thumbnail`, `instructor_id`, `is_active`, `price`, `duration_minutes`, `created_at`, `updated_at`) VALUES
(2, 'Javascript Fundamental', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s,', 'javascript-fundamental', 'Tech', 'beginner', 'course-thumbnails/4LlscabV51teKRDiZ4w1utiuLihMuDXAjZtYo4xD.png', 2, 1, 0.00, 120, '2025-06-07 10:03:44', '2025-06-08 20:06:55'),
(4, 'Python', 'ijdsjbnfvisjbf', 'python', 'Python', 'beginner', NULL, 2, 1, 0.00, 120, '2025-06-08 22:26:14', '2025-06-08 22:26:14');

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
-- Table structure for table `final_projects`
--

CREATE TABLE `final_projects` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `course_id` bigint(20) UNSIGNED NOT NULL,
  `attachments` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`attachments`)),
  `deadline` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `final_project_submissions`
--

CREATE TABLE `final_project_submissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `final_project_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `notes` text DEFAULT NULL,
  `attachments` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`attachments`)),
  `status` enum('pending','graded') NOT NULL DEFAULT 'pending',
  `score` int(11) DEFAULT NULL,
  `feedback` text DEFAULT NULL,
  `graded_at` timestamp NULL DEFAULT NULL,
  `graded_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `forum_categories`
--

CREATE TABLE `forum_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `slug` varchar(255) NOT NULL,
  `color` varchar(255) NOT NULL DEFAULT '#007bff',
  `order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `forum_categories`
--

INSERT INTO `forum_categories` (`id`, `name`, `description`, `slug`, `color`, `order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'General Discussion', 'General topics and discussions about learning', 'general-discussion', '#6366f1', 1, 1, '2025-06-07 07:27:58', '2025-06-07 07:27:58'),
(2, 'Course Help', 'Get help with specific courses and assignments', 'course-help', '#059669', 2, 1, '2025-06-07 07:27:58', '2025-06-07 07:27:58'),
(3, 'Technical Support', 'Technical issues and platform support', 'technical-support', '#dc2626', 3, 1, '2025-06-07 07:27:58', '2025-06-07 07:27:58'),
(4, 'Study Groups', 'Form study groups and collaborate', 'study-groups', '#7c3aed', 4, 1, '2025-06-07 07:27:58', '2025-06-07 10:02:15'),
(5, 'Job & Career', 'Career advice and job opportunities', 'job-career', '#ea580c', 5, 1, '2025-06-07 07:27:58', '2025-06-07 07:27:58');

-- --------------------------------------------------------

--
-- Table structure for table `forum_replies`
--

CREATE TABLE `forum_replies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `content` text NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `thread_id` bigint(20) UNSIGNED NOT NULL,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `is_best_answer` tinyint(1) NOT NULL DEFAULT 0,
  `upvotes` int(11) NOT NULL DEFAULT 0,
  `downvotes` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `forum_replies`
--

INSERT INTO `forum_replies` (`id`, `content`, `user_id`, `thread_id`, `parent_id`, `is_best_answer`, `upvotes`, `downvotes`, `created_at`, `updated_at`) VALUES
(2, 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.', 3, 2, NULL, 0, 3, 0, '2025-06-07 10:10:32', '2025-06-08 12:36:30'),
(3, 'It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout.', 1, 2, 2, 0, 0, 0, '2025-06-07 10:11:13', '2025-06-07 10:11:13'),
(4, 'Be yourself and never surrender :)', 2, 3, NULL, 0, 1, 0, '2025-06-08 12:45:53', '2025-06-08 12:45:57'),
(5, 'sjkdbfjks', 4, 4, NULL, 0, 0, 0, '2025-06-08 22:24:11', '2025-06-08 22:24:11');

-- --------------------------------------------------------

--
-- Table structure for table `forum_reply_user_upvotes`
--

CREATE TABLE `forum_reply_user_upvotes` (
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `forum_reply_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `forum_reply_user_upvotes`
--

INSERT INTO `forum_reply_user_upvotes` (`user_id`, `forum_reply_id`, `created_at`, `updated_at`) VALUES
(1, 2, NULL, NULL),
(2, 4, NULL, NULL),
(3, 2, NULL, NULL),
(4, 2, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `forum_threads`
--

CREATE TABLE `forum_threads` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `slug` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `is_pinned` tinyint(1) NOT NULL DEFAULT 0,
  `is_locked` tinyint(1) NOT NULL DEFAULT 0,
  `views` int(11) NOT NULL DEFAULT 0,
  `replies_count` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `forum_threads`
--

INSERT INTO `forum_threads` (`id`, `title`, `content`, `slug`, `user_id`, `category_id`, `is_pinned`, `is_locked`, `views`, `replies_count`, `created_at`, `updated_at`) VALUES
(2, 'Tes', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.', 'tes-1749316172', 4, 1, 0, 0, 5, 0, '2025-06-07 10:09:32', '2025-06-08 12:36:15'),
(3, 'Coding Tips for Beginner', 'Share your coding tips for beginner here!', 'coding-tips-for-beginner-1749410051', 4, 1, 0, 0, 3, 0, '2025-06-08 12:14:11', '2025-06-18 00:05:47'),
(4, 'Java', 'OJFNDEOJFNHO', 'java-1749446646', 4, 1, 0, 0, 1, 0, '2025-06-08 22:24:06', '2025-06-08 22:24:06');

-- --------------------------------------------------------

--
-- Table structure for table `homework`
--

CREATE TABLE `homework` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `question` text NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `course_id` bigint(20) UNSIGNED NOT NULL,
  `subject` enum('math','science','english','history','other') NOT NULL,
  `difficulty` enum('easy','medium','hard') NOT NULL,
  `attachments` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`attachments`)),
  `status` enum('pending','answered','closed') NOT NULL DEFAULT 'pending',
  `due_date` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `homework`
--

INSERT INTO `homework` (`id`, `title`, `description`, `question`, `student_id`, `course_id`, `subject`, `difficulty`, `attachments`, `status`, `due_date`, `created_at`, `updated_at`) VALUES
(2, 'Help', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s', 'It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.', 4, 2, 'other', 'easy', '[]', 'closed', NULL, '2025-06-07 10:09:07', '2025-06-08 22:25:29');

-- --------------------------------------------------------

--
-- Table structure for table `homework_answers`
--

CREATE TABLE `homework_answers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `answer` text NOT NULL,
  `explanation` text DEFAULT NULL,
  `homework_id` bigint(20) UNSIGNED NOT NULL,
  `teacher_id` bigint(20) UNSIGNED NOT NULL,
  `attachments` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`attachments`)),
  `is_best_answer` tinyint(1) NOT NULL DEFAULT 0,
  `upvotes` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `homework_answers`
--

INSERT INTO `homework_answers` (`id`, `answer`, `explanation`, `homework_id`, `teacher_id`, `attachments`, `is_best_answer`, `upvotes`, `created_at`, `updated_at`) VALUES
(4, 'It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.', 2, 3, '[]', 1, 0, '2025-06-07 10:10:16', '2025-06-08 22:25:29'),
(5, 'dbnaqwiufdhbnaeiufbniw', 'fwefewsfwefwefwefwef', 2, 2, '[]', 0, 0, '2025-06-08 12:46:38', '2025-06-08 22:25:29');

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
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_06_04_085313_add_role_to_users_table', 1),
(5, '2025_06_04_085325_create_courses_table', 1),
(6, '2025_06_04_085334_create_modules_table', 1),
(7, '2025_06_04_085342_create_quizzes_table', 1),
(8, '2025_06_04_085351_create_questions_table', 1),
(9, '2025_06_04_085359_create_user_progress_table', 1),
(10, '2025_06_04_085426_create_forum_categories_table', 1),
(11, '2025_06_04_085451_create_forum_threads_table', 1),
(12, '2025_06_04_085500_create_forum_replies_table', 1),
(13, '2025_06_04_085510_create_homework_table', 1),
(14, '2025_06_04_085518_create_homework_answers_table', 1),
(15, '2025_06_04_135530_create_quiz_attempts_table', 1),
(16, '2025_06_04_135531_create_final_project_submissions_table', 1),
(17, '2025_06_04_135531_create_final_projects_table', 1),
(18, '2025_06_07_141747_add_points_to_quiz_attempts_table', 1),
(19, '2025_06_07_153657_create_forum_reply_user_upvotes_table', 2);

-- --------------------------------------------------------

--
-- Table structure for table `modules`
--

CREATE TABLE `modules` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `content` longtext NOT NULL,
  `video_url` varchar(255) DEFAULT NULL,
  `attachments` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`attachments`)),
  `course_id` bigint(20) UNSIGNED NOT NULL,
  `order` int(11) NOT NULL DEFAULT 0,
  `is_published` tinyint(1) NOT NULL DEFAULT 0,
  `duration_minutes` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `modules`
--

INSERT INTO `modules` (`id`, `title`, `description`, `content`, `video_url`, `attachments`, `course_id`, `order`, `is_published`, `duration_minutes`, `created_at`, `updated_at`) VALUES
(4, 'Data Types', 'when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.\r\n\r\nLorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.', 'https://youtu.be/lfmg-EJ8gm4?si=Xh6IfbnUj2pF8_Ea', '[{\"name\":\"249244-none-837c3dfb.pdf\",\"path\":\"module-attachments\\/XEhr6UO2PpgeA0zBwfLg5HYZ95WQGDnnMPQqmBr4.pdf\",\"size\":180156}]', 2, 1, 1, 45, '2025-06-07 10:05:06', '2025-06-07 10:05:06'),
(7, 'Python for beginers', 'jhjh', 'jbncsxjikcbdsjikbc', NULL, '[]', 4, 0, 1, 20, '2025-06-08 22:26:48', '2025-06-08 22:26:48');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `question` text NOT NULL,
  `type` enum('multiple_choice','true_false') NOT NULL,
  `options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`options`)),
  `correct_answer` text NOT NULL,
  `explanation` text DEFAULT NULL,
  `quiz_id` bigint(20) UNSIGNED NOT NULL,
  `points` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id`, `question`, `type`, `options`, `correct_answer`, `explanation`, `quiz_id`, `points`, `created_at`, `updated_at`) VALUES
(5, 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.', 'multiple_choice', '[\"Satu\",\"Dua\",\"Tiga\"]', 'Satu', NULL, 3, 10, '2025-06-07 10:06:01', '2025-06-07 10:06:01'),
(6, 'and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum?', 'true_false', NULL, 'True', NULL, 3, 50, '2025-06-07 10:06:20', '2025-06-07 10:06:20'),
(7, 'It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.', 'multiple_choice', '[\"1\",\"2\",\"3\",\"4\"]', '1', NULL, 3, 25, '2025-06-07 10:06:41', '2025-06-07 10:06:41'),
(8, 'Anomali yang muncul ketika sahur adalah', 'multiple_choice', '[\"Tung Tung Tung Sahur\",\"Bombardino Crocodilo\",\"Trippi Troppi Troppa Trippa\"]', 'Tung Tung Tung Sahur', NULL, 4, 10, '2025-06-08 20:35:50', '2025-06-08 20:35:50');

-- --------------------------------------------------------

--
-- Table structure for table `quizzes`
--

CREATE TABLE `quizzes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `module_id` bigint(20) UNSIGNED NOT NULL,
  `time_limit` int(11) NOT NULL DEFAULT 30,
  `passing_score` int(11) NOT NULL DEFAULT 70,
  `max_attempts` int(11) NOT NULL DEFAULT 3,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `quizzes`
--

INSERT INTO `quizzes` (`id`, `title`, `description`, `module_id`, `time_limit`, `passing_score`, `max_attempts`, `is_active`, `created_at`, `updated_at`) VALUES
(3, 'Quiz A', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting', 4, 5, 75, 3, 1, '2025-06-07 10:05:34', '2025-06-07 10:05:34'),
(4, 'Quiz B', NULL, 4, 15, 50, 3, 1, '2025-06-08 20:33:49', '2025-06-08 20:33:49');

-- --------------------------------------------------------

--
-- Table structure for table `quiz_attempts`
--

CREATE TABLE `quiz_attempts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `quiz_id` bigint(20) UNSIGNED NOT NULL,
  `answers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`answers`)),
  `score` int(11) NOT NULL,
  `total_questions` int(11) NOT NULL,
  `percentage` decimal(5,2) NOT NULL DEFAULT 0.00,
  `points_earned` int(11) NOT NULL DEFAULT 0,
  `passed` tinyint(1) NOT NULL DEFAULT 0,
  `started_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `quiz_attempts`
--

INSERT INTO `quiz_attempts` (`id`, `user_id`, `quiz_id`, `answers`, `score`, `total_questions`, `percentage`, `points_earned`, `passed`, `started_at`, `completed_at`, `created_at`, `updated_at`) VALUES
(5, 4, 3, '{\"7\":\"1\",\"5\":\"Satu\",\"6\":\"True\"}', 3, 3, 100.00, 85, 1, '2025-06-07 10:08:12', '2025-06-07 10:08:23', '2025-06-07 10:08:23', '2025-06-07 10:08:23'),
(6, 4, 4, '{\"8\":\"Tung Tung Tung Sahur\"}', 1, 1, 100.00, 10, 1, '2025-06-08 22:24:41', '2025-06-08 22:24:48', '2025-06-08 22:24:48', '2025-06-08 22:24:48');

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

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('rwXqAz26mDDhQFCCiZT8EYCf2ZxLAvm47gr9zl96', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiTmU2RlV3d0hUZDBHcHl0d2x6bjVlVjlJTUFCeThtWXZocDhBcVIxRyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTI6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9jb3Vyc2VzL2phdmFzY3JpcHQtZnVuZGFtZW50YWwiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=', 1750231139);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `role` enum('student','teacher','expert','moderator','admin') NOT NULL DEFAULT 'student',
  `avatar` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `institution` varchar(255) DEFAULT NULL,
  `level` enum('beginner','intermediate','advanced') NOT NULL DEFAULT 'beginner',
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `points` int(11) NOT NULL DEFAULT 0,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `role`, `avatar`, `bio`, `institution`, `level`, `is_verified`, `points`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Atmin Telah Tiba', 'admin@gmail.com', 'admin', 'avatars/CQMXR1nHIWZlQu7fgHi2swIzF2PkAIk6D2ul8Fqx.jpg', NULL, NULL, 'beginner', 1, 0, '2025-06-07 07:27:56', '$2y$12$VgmfT3mQ2S0bYqmLMSfmaup/ehvlUHCY/p0iJxdLn8EZPcoHhCSrO', 'pzl92HeqCl9hSOXxKIXynUpX15F7bambOdE8iKmvl5H3dlfodSjGN4DahEsU', '2025-06-07 07:27:56', '2025-06-08 20:21:50'),
(2, 'Mas Fredy', 'teacher@gmail.com', 'teacher', 'avatars/qyWOxdw6LosKUzdOLSnsFxDEDA1VrL8S1aq7DRpf.jpg', 'Experienced mathematics teacher with 10+ years of teaching experience.', 'Tech University', 'beginner', 1, 1500, '2025-06-07 07:27:56', '$2y$12$uwiSP.BJxg/0eS77NbgKj.pmwTM3wvRwpDe0KWrlmgYpj9nqaL3o6', NULL, '2025-06-07 07:27:56', '2025-06-08 20:29:17'),
(3, 'Beliau', 'expert@gmail.com', 'expert', 'avatars/PB44Q0BbJZuxqAJ3UHTVDODCTmNgl3BxhwlH5J28.jpg', 'Software engineering expert specializing in web development.', 'Tech Corp', 'advanced', 1, 2600, '2025-06-07 07:27:57', '$2y$12$IcYtw1OZFf.3xsNGzhfDxOrWQzSR2zAT1T2JugsF5ZMEFFfH/YJZi', NULL, '2025-06-07 07:27:57', '2025-06-08 20:23:02'),
(4, 'Fikri', 'student@gmail.com', 'student', 'avatars/4hehifwq8B7Quc2j7ebJszzx5g8DQycM90tcRO48.jpg', 'Computer Science student passionate about learning new technologies.', 'State University', 'intermediate', 0, 295, '2025-06-07 07:27:57', '$2y$12$75Tz6T2y5W1mAtoYbJHUnu30ONBD.HJ6joCva2QYWeTXL8dqBAw.C', '7kmDXJf95MSYS6aSlaUM5scMUuvOQvXyQiNrC6lPiAqrh0ueWltXJ0kyWBwC', '2025-06-07 07:27:57', '2025-06-08 22:24:48'),
(25, 'Adam', 'adam@gmail.com', 'student', 'avatars/Q7qV4DGxKpYzpi2IqLOXMFcmJOEyNlkRjT69NPVZ.jpg', NULL, NULL, 'beginner', 1, 0, NULL, '$2y$12$A4zm9BmWMv5f7pTfyBFK5eB8pb5pu6WjAfpMD4BoM5k3JGSc9RzfS', NULL, '2025-06-07 09:02:51', '2025-06-07 10:01:42'),
(26, 'tes', 'tes@mail.com', 'student', NULL, NULL, NULL, 'beginner', 0, 0, NULL, '$2y$12$vLZ4.7s4K4GM0ISqh.yATe6Lp/qjUNKPjuYC/URXqx.7nZLkU2G/m', NULL, '2025-06-08 10:12:59', '2025-06-08 10:12:59');

-- --------------------------------------------------------

--
-- Table structure for table `user_progress`
--

CREATE TABLE `user_progress` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `course_id` bigint(20) UNSIGNED NOT NULL,
  `module_id` bigint(20) UNSIGNED DEFAULT NULL,
  `progress_percentage` decimal(5,2) NOT NULL DEFAULT 0.00,
  `is_completed` tinyint(1) NOT NULL DEFAULT 0,
  `completed_at` timestamp NULL DEFAULT NULL,
  `time_spent` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_progress`
--

INSERT INTO `user_progress` (`id`, `user_id`, `course_id`, `module_id`, `progress_percentage`, `is_completed`, `completed_at`, `time_spent`, `created_at`, `updated_at`) VALUES
(10, 4, 2, NULL, 100.00, 1, '2025-06-08 22:24:48', 0, '2025-06-07 10:07:55', '2025-06-08 22:24:48'),
(11, 4, 2, 4, 100.00, 1, '2025-06-08 22:24:48', 0, '2025-06-07 10:08:23', '2025-06-08 22:24:48'),
(12, 1, 2, NULL, 0.00, 0, NULL, 0, '2025-06-07 10:13:12', '2025-06-08 20:06:55');

--
-- Indexes for dumped tables
--

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
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `courses_slug_unique` (`slug`),
  ADD KEY `courses_instructor_id_foreign` (`instructor_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `final_projects`
--
ALTER TABLE `final_projects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `final_projects_course_id_foreign` (`course_id`);

--
-- Indexes for table `final_project_submissions`
--
ALTER TABLE `final_project_submissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `final_project_submissions_final_project_id_foreign` (`final_project_id`),
  ADD KEY `final_project_submissions_user_id_foreign` (`user_id`),
  ADD KEY `final_project_submissions_graded_by_foreign` (`graded_by`);

--
-- Indexes for table `forum_categories`
--
ALTER TABLE `forum_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `forum_categories_slug_unique` (`slug`);

--
-- Indexes for table `forum_replies`
--
ALTER TABLE `forum_replies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `forum_replies_user_id_foreign` (`user_id`),
  ADD KEY `forum_replies_thread_id_foreign` (`thread_id`),
  ADD KEY `forum_replies_parent_id_foreign` (`parent_id`);

--
-- Indexes for table `forum_reply_user_upvotes`
--
ALTER TABLE `forum_reply_user_upvotes`
  ADD PRIMARY KEY (`user_id`,`forum_reply_id`),
  ADD KEY `forum_reply_user_upvotes_forum_reply_id_foreign` (`forum_reply_id`);

--
-- Indexes for table `forum_threads`
--
ALTER TABLE `forum_threads`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `forum_threads_slug_unique` (`slug`),
  ADD KEY `forum_threads_user_id_foreign` (`user_id`),
  ADD KEY `forum_threads_category_id_foreign` (`category_id`);

--
-- Indexes for table `homework`
--
ALTER TABLE `homework`
  ADD PRIMARY KEY (`id`),
  ADD KEY `homework_student_id_foreign` (`student_id`),
  ADD KEY `homework_course_id_foreign` (`course_id`);

--
-- Indexes for table `homework_answers`
--
ALTER TABLE `homework_answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `homework_answers_homework_id_foreign` (`homework_id`),
  ADD KEY `homework_answers_teacher_id_foreign` (`teacher_id`);

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
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `modules`
--
ALTER TABLE `modules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `modules_course_id_foreign` (`course_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `questions_quiz_id_foreign` (`quiz_id`);

--
-- Indexes for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quizzes_module_id_foreign` (`module_id`);

--
-- Indexes for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quiz_attempts_user_id_foreign` (`user_id`),
  ADD KEY `quiz_attempts_quiz_id_foreign` (`quiz_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `user_progress`
--
ALTER TABLE `user_progress`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_course_module_unique` (`user_id`,`course_id`,`module_id`),
  ADD KEY `user_progress_course_id_foreign` (`course_id`),
  ADD KEY `user_progress_module_id_foreign` (`module_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `final_projects`
--
ALTER TABLE `final_projects`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `final_project_submissions`
--
ALTER TABLE `final_project_submissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `forum_categories`
--
ALTER TABLE `forum_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `forum_replies`
--
ALTER TABLE `forum_replies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `forum_threads`
--
ALTER TABLE `forum_threads`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `homework`
--
ALTER TABLE `homework`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `homework_answers`
--
ALTER TABLE `homework_answers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `modules`
--
ALTER TABLE `modules`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `quizzes`
--
ALTER TABLE `quizzes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `user_progress`
--
ALTER TABLE `user_progress`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `courses_instructor_id_foreign` FOREIGN KEY (`instructor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `final_projects`
--
ALTER TABLE `final_projects`
  ADD CONSTRAINT `final_projects_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `final_project_submissions`
--
ALTER TABLE `final_project_submissions`
  ADD CONSTRAINT `final_project_submissions_final_project_id_foreign` FOREIGN KEY (`final_project_id`) REFERENCES `final_projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `final_project_submissions_graded_by_foreign` FOREIGN KEY (`graded_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `final_project_submissions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `forum_replies`
--
ALTER TABLE `forum_replies`
  ADD CONSTRAINT `forum_replies_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `forum_replies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `forum_replies_thread_id_foreign` FOREIGN KEY (`thread_id`) REFERENCES `forum_threads` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `forum_replies_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `forum_reply_user_upvotes`
--
ALTER TABLE `forum_reply_user_upvotes`
  ADD CONSTRAINT `forum_reply_user_upvotes_forum_reply_id_foreign` FOREIGN KEY (`forum_reply_id`) REFERENCES `forum_replies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `forum_reply_user_upvotes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `forum_threads`
--
ALTER TABLE `forum_threads`
  ADD CONSTRAINT `forum_threads_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `forum_categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `forum_threads_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `homework`
--
ALTER TABLE `homework`
  ADD CONSTRAINT `homework_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `homework_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `homework_answers`
--
ALTER TABLE `homework_answers`
  ADD CONSTRAINT `homework_answers_homework_id_foreign` FOREIGN KEY (`homework_id`) REFERENCES `homework` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `homework_answers_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `modules`
--
ALTER TABLE `modules`
  ADD CONSTRAINT `modules_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_quiz_id_foreign` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD CONSTRAINT `quizzes_module_id_foreign` FOREIGN KEY (`module_id`) REFERENCES `modules` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  ADD CONSTRAINT `quiz_attempts_quiz_id_foreign` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `quiz_attempts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_progress`
--
ALTER TABLE `user_progress`
  ADD CONSTRAINT `user_progress_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_progress_module_id_foreign` FOREIGN KEY (`module_id`) REFERENCES `modules` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_progress_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
