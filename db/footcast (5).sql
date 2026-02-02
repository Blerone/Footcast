-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Feb 02, 2026 at 01:07 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `footcast`
--

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(120) NOT NULL,
  `email` varchar(160) NOT NULL,
  `subject` varchar(180) DEFAULT NULL,
  `message` text NOT NULL,
  `status` enum('new','read') NOT NULL DEFAULT 'new',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `subject`, `message`, `status`, `created_at`) VALUES
(1, 'vesa susuri', 'vvesasusuri@gmail.com', '34', '34', 'new', '2026-01-30 17:43:26'),
(5, 'vesa susuri', 'vvesasusuri@gmail.com', 'er', 'er', 'new', '2026-01-30 17:45:09'),
(6, 'vesa susuri', 'vvesasusuri@gmail.com', 'er', 'ererer', 'new', '2026-01-30 17:46:15'),
(7, 'vesa susuri', 'vvesasusuri@gmail.com', 'er', 'ererer', 'new', '2026-01-30 17:47:36');

-- --------------------------------------------------------

--
-- Table structure for table `home_page_banner`
--

CREATE TABLE `home_page_banner` (
  `id` int(11) NOT NULL,
  `home_team` varchar(120) NOT NULL,
  `away_team` varchar(120) NOT NULL,
  `days_value` int(11) NOT NULL DEFAULT 0,
  `hours_value` int(11) NOT NULL DEFAULT 0,
  `minutes_value` int(11) NOT NULL DEFAULT 0,
  `seconds_value` int(11) NOT NULL DEFAULT 0,
  `days_label` varchar(30) NOT NULL DEFAULT 'Days',
  `hours_label` varchar(30) NOT NULL DEFAULT 'Hours',
  `minutes_label` varchar(30) NOT NULL DEFAULT 'Minutes',
  `seconds_label` varchar(30) NOT NULL DEFAULT 'Seconds',
  `odds_first` varchar(20) NOT NULL,
  `odds_second` varchar(20) NOT NULL,
  `odds_third` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `home_page_banner`
--

INSERT INTO `home_page_banner` (`id`, `home_team`, `away_team`, `days_value`, `hours_value`, `minutes_value`, `seconds_value`, `days_label`, `hours_label`, `minutes_label`, `seconds_label`, `odds_first`, `odds_second`, `odds_third`, `created_at`, `updated_at`) VALUES
(1, 'Real Madrid', 'Barcelona', 3, 12, 47, 32, 'Days', 'Hours', 'Minutes', 'Seconds', '1.4X', '2.3X', '3.4X', '2026-01-29 21:48:24', '2026-01-29 21:48:24');

-- --------------------------------------------------------

--
-- Table structure for table `home_page_favorites`
--

CREATE TABLE `home_page_favorites` (
  `id` int(11) NOT NULL,
  `item_label` varchar(60) NOT NULL,
  `item_name` varchar(120) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `home_page_favorites`
--

INSERT INTO `home_page_favorites` (`id`, `item_label`, `item_name`, `sort_order`, `is_active`) VALUES
(1, 'Player', 'Haaland', 1, 1),
(2, 'Coach', 'Maresca', 2, 1),
(3, 'Club', 'Chelsea', 3, 1),
(4, 'Club', 'Real Madrid', 4, 1),
(5, 'Club', 'Bayern Munich', 5, 1),
(6, 'Player', 'Mbappe', 6, 1),
(7, 'Player', 'Estêvão', 7, 1),
(8, 'Club', 'Inter Milan', 8, 1),
(9, 'Coach', 'Xabi Alonso', 9, 1);

-- --------------------------------------------------------

--
-- Table structure for table `home_page_hero`
--

CREATE TABLE `home_page_hero` (
  `id` int(11) NOT NULL,
  `sports_text` varchar(40) NOT NULL,
  `bet_text` varchar(40) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `home_page_hero`
--

INSERT INTO `home_page_hero` (`id`, `sports_text`, `bet_text`, `created_at`, `updated_at`) VALUES
(1, 'SPORTS', 'BET', '2026-01-29 21:48:24', '2026-01-29 21:48:24');

-- --------------------------------------------------------

--
-- Table structure for table `home_page_leagues`
--

CREATE TABLE `home_page_leagues` (
  `id` int(11) NOT NULL,
  `league_name` varchar(120) NOT NULL,
  `stats_value` varchar(40) NOT NULL,
  `stats_label` varchar(80) NOT NULL,
  `top_scorer_label` varchar(120) NOT NULL,
  `goals_text` varchar(40) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `home_page_leagues`
--

INSERT INTO `home_page_leagues` (`id`, `league_name`, `stats_value`, `stats_label`, `top_scorer_label`, `goals_text`, `sort_order`, `is_active`) VALUES
(1, 'Premier League', '204', 'active players', 'Top Goal Scorer', '60 G/A', 1, 1),
(2, 'Seria A', '194', 'active players', 'Top Goal Scorer', '30 G/A', 2, 1),
(3, 'Budensliga', '194', 'active players', 'Top Goal Scorer', '30 G/A', 3, 1),
(4, 'La Liga', '194', 'active players', 'Top Goal Scorer', '30 G/A', 4, 1);

-- --------------------------------------------------------

--
-- Table structure for table `home_page_sections`
--

CREATE TABLE `home_page_sections` (
  `id` int(11) NOT NULL,
  `trusted_by_title` varchar(120) NOT NULL,
  `about_title` varchar(180) NOT NULL,
  `about_highlight` varchar(120) NOT NULL,
  `about_body` text NOT NULL,
  `bet_steps_title` varchar(160) NOT NULL,
  `popular_leagues_title` varchar(120) NOT NULL,
  `favorites_title` varchar(120) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `home_page_sections`
--

INSERT INTO `home_page_sections` (`id`, `trusted_by_title`, `about_title`, `about_highlight`, `about_body`, `bet_steps_title`, `popular_leagues_title`, `favorites_title`, `created_at`, `updated_at`) VALUES
(1, 'Trusted By', 'One click from', 'Winning It All', 'FootCast is your go-to spot for football betting done right. Place smart bets, follow live stats, and stay ahead with real-time insights. From major leagues to local matches, FootCast keeps every game exciting where your passion for football meets the thrill of winning.', 'How to place a BET ?', 'Popular Leagues', 'Fan’s FAVORITE', '2026-01-29 21:48:24', '2026-01-29 21:48:24');

-- --------------------------------------------------------

--
-- Table structure for table `home_page_steps`
--

CREATE TABLE `home_page_steps` (
  `id` int(11) NOT NULL,
  `step_number` int(11) NOT NULL,
  `step_title` varchar(160) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `home_page_steps`
--

INSERT INTO `home_page_steps` (`id`, `step_number`, `step_title`, `sort_order`) VALUES
(1, 1, 'Create an Account', 1),
(2, 2, 'Find your Team', 2),
(3, 3, 'Place your BET', 3),
(4, 4, 'You won? Withdraw Now!', 4);

-- --------------------------------------------------------

--
-- Table structure for table `league_standings`
--

CREATE TABLE `league_standings` (
  `id` int(11) NOT NULL,
  `league_code` varchar(10) NOT NULL,
  `league_name` varchar(100) NOT NULL,
  `season` varchar(20) DEFAULT NULL,
  `team_name` varchar(100) NOT NULL,
  `team_id` int(11) DEFAULT NULL,
  `position` int(11) NOT NULL,
  `played` int(11) DEFAULT 0,
  `won` int(11) DEFAULT 0,
  `drawn` int(11) DEFAULT 0,
  `lost` int(11) DEFAULT 0,
  `goals_for` int(11) DEFAULT 0,
  `goals_against` int(11) DEFAULT 0,
  `goal_difference` int(11) DEFAULT 0,
  `points` int(11) DEFAULT 0,
  `form` varchar(10) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `league_standings`
--

INSERT INTO `league_standings` (`id`, `league_code`, `league_name`, `season`, `team_name`, `team_id`, `position`, `played`, `won`, `drawn`, `lost`, `goals_for`, `goals_against`, `goal_difference`, `points`, `form`, `updated_at`) VALUES
(1, 'PL', 'Premier League', '2024-2025', 'Arsenal', NULL, 1, 0, 0, 0, 0, 0, 0, 0, 0, NULL, '2025-11-24 20:53:58'),
(2, 'PL', 'Premier League', '2024-2025', 'Manchester City', NULL, 2, 0, 0, 0, 0, 0, 0, 0, 0, NULL, '2025-11-24 20:53:58'),
(3, 'PL', 'Premier League', '2024-2025', 'Liverpool', NULL, 3, 0, 0, 0, 0, 0, 0, 0, 0, NULL, '2025-11-24 20:53:58'),
(4, 'PL', 'Premier League', '2024-2025', 'Chelsea', NULL, 4, 0, 0, 0, 0, 0, 0, 0, 0, NULL, '2025-11-24 20:53:58'),
(5, 'PL', 'Premier League', '2024-2025', 'Manchester United', NULL, 5, 0, 0, 0, 0, 0, 0, 0, 0, NULL, '2025-11-24 20:53:58'),
(6, 'PL', 'Premier League', '2025-2026', 'Arsenal FC', 57, 1, 22, 15, 5, 2, 40, 14, 26, 50, 'D,D,W,W,W', '2026-01-17 19:35:47'),
(7, 'PL', 'Premier League', '2025-2026', 'Chelsea FC', 61, 6, 22, 9, 7, 6, 36, 24, 12, 34, 'W,L,D,D,L', '2026-01-17 19:35:47'),
(8, 'PL', 'Premier League', '2025-2026', 'Manchester City FC', 65, 2, 22, 13, 4, 5, 45, 21, 24, 43, 'L,D,D,D,W', '2026-01-17 19:35:47'),
(9, 'PL', 'Premier League', '2025-2026', 'Aston Villa FC', 58, 3, 21, 13, 4, 4, 33, 24, 9, 43, 'D,W,L,W,W', '2026-01-12 21:11:19'),
(10, 'PL', 'Premier League', '2025-2026', 'Crystal Palace FC', 354, 13, 22, 7, 7, 8, 23, 25, -2, 28, 'L,D,L,D,L', '2026-01-17 19:35:47'),
(11, 'PL', 'Premier League', '2025-2026', 'Brighton & Hove Albion FC', 397, 11, 21, 7, 8, 6, 31, 28, 3, 29, 'D,W,D,L,D', '2026-01-12 21:11:19'),
(12, 'PL', 'Premier League', '2025-2026', 'Sunderland AFC', 71, 8, 22, 8, 9, 5, 23, 23, 0, 33, 'W,L,D,D,D', '2026-01-17 19:35:47'),
(13, 'PL', 'Premier League', '2025-2026', 'AFC Bournemouth', 1044, 15, 21, 6, 8, 7, 34, 40, -6, 26, 'W,L,D,L,D', '2026-01-12 21:11:19'),
(14, 'PL', 'Premier League', '2025-2026', 'Tottenham Hotspur FC', 73, 14, 22, 7, 6, 9, 31, 29, 2, 27, 'L,L,D,D,W', '2026-01-17 19:35:47'),
(15, 'PL', 'Premier League', '2025-2026', 'Manchester United FC', 66, 5, 22, 9, 8, 5, 38, 32, 6, 35, 'W,D,D,D,W', '2026-01-17 19:35:47'),
(16, 'PL', 'Premier League', '2025-2026', 'Liverpool FC', 64, 4, 22, 10, 6, 6, 33, 29, 4, 36, 'D,D,D,D,W', '2026-01-17 19:35:47'),
(17, 'PL', 'Premier League', '2025-2026', 'Brentford FC', 402, 7, 22, 10, 3, 9, 35, 30, 5, 33, 'L,W,W,D,W', '2026-01-17 19:35:47'),
(18, 'PL', 'Premier League', '2025-2026', 'Everton FC', 62, 12, 21, 8, 5, 8, 23, 25, -2, 29, 'D,L,W,D,L', '2026-01-12 21:11:19'),
(19, 'PL', 'Premier League', '2025-2026', 'Newcastle United FC', 67, 9, 21, 9, 5, 7, 32, 27, 5, 32, 'W,W,W,L,D', '2026-01-17 19:35:47'),
(20, 'PL', 'Premier League', '2025-2026', 'Fulham FC', 63, 10, 22, 9, 4, 9, 30, 31, -1, 31, 'L,W,D,D,W', '2026-01-17 19:35:47'),
(21, 'PL', 'Premier League', '2025-2026', 'Nottingham Forest FC', 351, 17, 22, 6, 4, 12, 21, 34, -13, 22, 'D,W,L,L,L', '2026-01-17 19:35:47'),
(22, 'PL', 'Premier League', '2025-2026', 'West Ham United FC', 563, 18, 22, 4, 5, 13, 24, 44, -20, 17, 'W,L,L,D,L', '2026-01-17 19:35:47'),
(23, 'PL', 'Premier League', '2025-2026', 'Leeds United FC', 341, 16, 22, 6, 7, 9, 30, 37, -7, 25, 'W,L,D,D,D', '2026-01-17 19:35:47'),
(24, 'PL', 'Premier League', '2025-2026', 'Burnley FC', 328, 19, 22, 3, 5, 14, 23, 42, -19, 14, 'D,D,L,L,D', '2026-01-17 19:35:47'),
(25, 'PL', 'Premier League', '2025-2026', 'Wolverhampton Wanderers FC', 76, 20, 21, 1, 4, 16, 15, 41, -26, 7, 'D,W,D,L,L', '2026-01-12 21:11:19'),
(26, 'PL', 'Premier League', '2024-2025', 'Liverpool FC', 64, 1, 38, 25, 9, 4, 86, 41, 45, 84, 'D,L,D,L,W', '2025-11-24 21:02:55'),
(27, 'PL', 'Premier League', '2024-2025', 'Arsenal FC', 57, 2, 38, 20, 14, 4, 69, 34, 35, 74, 'W,W,D,L,D', '2025-11-24 21:02:55'),
(28, 'PL', 'Premier League', '2024-2025', 'Manchester City FC', 65, 3, 38, 21, 8, 9, 72, 44, 28, 71, 'W,W,D,W,W', '2025-11-24 21:02:55'),
(29, 'PL', 'Premier League', '2024-2025', 'Chelsea FC', 61, 4, 38, 20, 9, 9, 64, 43, 21, 69, 'W,W,L,W,W', '2025-11-24 21:02:55'),
(30, 'PL', 'Premier League', '2024-2025', 'Newcastle United FC', 67, 5, 38, 20, 6, 12, 68, 47, 21, 66, 'L,L,W,D,W', '2025-11-24 21:02:55'),
(31, 'PL', 'Premier League', '2024-2025', 'Aston Villa FC', 58, 6, 38, 19, 9, 10, 58, 51, 7, 66, 'L,W,W,W,L', '2025-11-24 21:02:55'),
(32, 'PL', 'Premier League', '2024-2025', 'Nottingham Forest FC', 351, 7, 38, 19, 8, 11, 58, 46, 12, 65, 'L,W,D,D,L', '2025-11-24 21:02:55'),
(33, 'PL', 'Premier League', '2024-2025', 'Brighton & Hove Albion FC', 397, 8, 38, 16, 13, 9, 66, 59, 7, 61, 'W,W,W,D,W', '2025-11-24 21:02:55'),
(34, 'PL', 'Premier League', '2024-2025', 'AFC Bournemouth', 1044, 9, 38, 15, 11, 12, 58, 46, 12, 56, 'W,L,L,W,D', '2025-11-24 21:02:55'),
(35, 'PL', 'Premier League', '2024-2025', 'Brentford FC', 402, 10, 38, 16, 8, 14, 66, 57, 9, 56, 'D,L,W,W,W', '2025-11-24 21:02:55'),
(36, 'PL', 'Premier League', '2024-2025', 'Fulham FC', 63, 11, 38, 15, 9, 14, 54, 54, 0, 54, 'L,W,L,L,W', '2025-11-24 21:02:55'),
(37, 'PL', 'Premier League', '2024-2025', 'Crystal Palace FC', 354, 12, 38, 13, 14, 11, 51, 51, 0, 53, 'D,W,W,D,D', '2025-11-24 21:02:55'),
(38, 'PL', 'Premier League', '2024-2025', 'Everton FC', 62, 13, 38, 11, 15, 12, 42, 44, -2, 48, 'W,W,W,D,L', '2025-11-24 21:02:55'),
(39, 'PL', 'Premier League', '2024-2025', 'West Ham United FC', 563, 14, 38, 11, 10, 17, 46, 62, -16, 43, 'W,L,W,D,L', '2025-11-24 21:02:55'),
(40, 'PL', 'Premier League', '2024-2025', 'Manchester United FC', 66, 15, 38, 11, 9, 18, 44, 54, -10, 42, 'W,L,L,L,D', '2025-11-24 21:02:55'),
(41, 'PL', 'Premier League', '2024-2025', 'Wolverhampton Wanderers FC', 76, 16, 38, 12, 6, 20, 54, 69, -15, 42, 'D,L,L,L,W', '2025-11-24 21:02:55'),
(42, 'PL', 'Premier League', '2024-2025', 'Tottenham Hotspur FC', 73, 17, 38, 11, 5, 22, 64, 65, -1, 38, 'L,L,L,D,L', '2025-11-24 21:02:55'),
(43, 'PL', 'Premier League', '2024-2025', 'Leicester City FC', 338, 18, 38, 6, 7, 25, 33, 80, -47, 25, 'L,W,D,W,L', '2025-11-24 21:02:55'),
(44, 'PL', 'Premier League', '2024-2025', 'Ipswich Town FC', 349, 19, 38, 4, 10, 24, 36, 82, -46, 22, 'L,L,L,D,L', '2025-11-24 21:02:55'),
(45, 'PL', 'Premier League', '2024-2025', 'Southampton FC', 340, 20, 38, 2, 6, 30, 26, 86, -60, 12, 'L,L,D,L,L', '2025-11-24 21:02:55'),
(106, 'BL1', 'Bundesliga', '2025-2026', 'FC Bayern München', 5, 1, 11, 10, 1, 0, 41, 8, 33, 31, 'W,D,W,W,W', '2025-11-27 18:36:24'),
(107, 'BL1', 'Bundesliga', '2025-2026', 'RB Leipzig', 721, 2, 11, 8, 1, 2, 22, 13, 9, 25, 'W,L,W,W,W', '2025-11-27 18:36:24'),
(108, 'BL1', 'Bundesliga', '2025-2026', 'Bayer 04 Leverkusen', 3, 3, 11, 7, 2, 2, 27, 15, 12, 23, 'W,W,L,W,W', '2025-11-27 18:36:24'),
(109, 'BL1', 'Bundesliga', '2025-2026', 'Borussia Dortmund', 4, 4, 11, 6, 4, 1, 19, 10, 9, 22, 'D,D,W,W,L', '2025-11-27 18:36:24'),
(110, 'BL1', 'Bundesliga', '2025-2026', 'VfB Stuttgart', 10, 5, 11, 7, 1, 3, 20, 15, 5, 22, 'D,W,L,W,W', '2025-11-27 18:36:24'),
(111, 'BL1', 'Bundesliga', '2025-2026', 'Eintracht Frankfurt', 19, 6, 11, 6, 2, 3, 27, 22, 5, 20, 'W,W,D,W,D', '2025-11-27 18:36:24'),
(112, 'BL1', 'Bundesliga', '2025-2026', 'TSG 1899 Hoffenheim', 2, 7, 11, 6, 2, 3, 22, 17, 5, 20, 'D,W,W,W,W', '2025-11-27 18:36:24'),
(113, 'BL1', 'Bundesliga', '2025-2026', '1. FC Union Berlin', 28, 8, 11, 4, 3, 4, 14, 17, -3, 15, 'W,D,D,L,W', '2025-11-27 18:36:24'),
(114, 'BL1', 'Bundesliga', '2025-2026', 'SV Werder Bremen', 12, 9, 11, 4, 3, 4, 15, 20, -5, 15, 'L,W,D,W,D', '2025-11-27 18:36:24'),
(115, 'BL1', 'Bundesliga', '2025-2026', '1. FC Köln', 1, 10, 11, 4, 2, 5, 20, 19, 1, 14, 'L,L,W,L,D', '2025-11-27 18:36:24'),
(116, 'BL1', 'Bundesliga', '2025-2026', 'SC Freiburg', 17, 11, 11, 3, 4, 4, 15, 20, -5, 13, 'L,W,D,L,D', '2025-11-27 18:36:24'),
(117, 'BL1', 'Bundesliga', '2025-2026', 'Borussia Mönchengladbach', 18, 12, 11, 3, 3, 5, 16, 19, -3, 12, 'W,W,W,L,L', '2025-11-27 18:36:24'),
(118, 'BL1', 'Bundesliga', '2025-2026', 'FC Augsburg', 16, 13, 11, 3, 1, 7, 15, 24, -9, 10, 'W,L,L,L,D', '2025-11-27 18:36:24'),
(119, 'BL1', 'Bundesliga', '2025-2026', 'Hamburger SV', 7, 14, 11, 2, 3, 6, 9, 17, -8, 9, 'L,D,L,L,L', '2025-11-27 18:36:24'),
(120, 'BL1', 'Bundesliga', '2025-2026', 'VfL Wolfsburg', 11, 15, 11, 2, 2, 7, 13, 21, -8, 8, 'L,L,L,W,L', '2025-11-27 18:36:24'),
(121, 'BL1', 'Bundesliga', '2025-2026', 'FC St. Pauli 1910', 20, 16, 11, 2, 1, 8, 9, 21, -12, 7, 'L,L,L,L,L', '2025-11-27 18:36:24'),
(122, 'BL1', 'Bundesliga', '2025-2026', '1. FSV Mainz 05', 15, 17, 11, 1, 3, 7, 11, 19, -8, 6, 'D,L,D,L,L', '2025-11-27 18:36:24'),
(123, 'BL1', 'Bundesliga', '2025-2026', '1. FC Heidenheim 1846', 44, 18, 11, 1, 2, 8, 8, 26, -18, 5, 'L,L,D,L,D', '2025-11-27 18:36:24'),
(124, 'SA', 'Serie A', '2025-2026', 'AS Roma', 100, 4, 13, 9, 0, 4, 15, 7, 8, 27, 'L,W,W,L,W', '2025-12-02 17:19:28'),
(125, 'SA', 'Serie A', '2025-2026', 'AC Milan', 98, 1, 13, 8, 4, 1, 19, 9, 10, 28, 'W,W,D,W,D', '2025-12-02 17:19:28'),
(126, 'SA', 'Serie A', '2025-2026', 'SSC Napoli', 113, 2, 13, 9, 1, 3, 20, 11, 9, 28, 'W,W,L,D,W', '2025-12-02 17:19:28'),
(127, 'SA', 'Serie A', '2025-2026', 'FC Internazionale Milano', 108, 3, 13, 9, 0, 4, 28, 13, 15, 27, 'W,L,W,W,W', '2025-12-02 17:19:28'),
(128, 'SA', 'Serie A', '2025-2026', 'Bologna FC 1909', 103, 6, 13, 7, 3, 3, 22, 11, 11, 24, 'L,W,W,W,D', '2025-12-02 17:19:28'),
(129, 'SA', 'Serie A', '2025-2026', 'Como 1907', 7397, 5, 13, 6, 6, 1, 19, 7, 12, 24, 'W,W,D,D,W', '2025-12-02 17:19:28'),
(130, 'SA', 'Serie A', '2025-2026', 'Juventus FC', 109, 7, 13, 6, 5, 2, 17, 12, 5, 23, 'W,D,D,W,W', '2025-12-02 17:19:28'),
(131, 'SA', 'Serie A', '2025-2026', 'SS Lazio', 110, 8, 13, 5, 3, 5, 15, 10, 5, 18, 'L,W,L,W,D', '2025-12-02 17:19:28'),
(132, 'SA', 'Serie A', '2025-2026', 'US Sassuolo Calcio', 471, 10, 13, 5, 2, 6, 16, 16, 0, 17, 'L,D,W,L,W', '2025-12-02 17:19:28'),
(133, 'SA', 'Serie A', '2025-2026', 'Udinese Calcio', 115, 9, 13, 5, 3, 5, 14, 20, -6, 18, 'W,L,L,W,L', '2025-12-02 17:19:28'),
(134, 'SA', 'Serie A', '2025-2026', 'US Cremonese', 457, 11, 13, 4, 5, 4, 16, 17, -1, 17, 'W,L,L,L,W', '2025-12-02 17:19:28'),
(135, 'SA', 'Serie A', '2025-2026', 'Torino FC', 586, 13, 13, 3, 5, 5, 12, 23, -11, 14, 'L,L,D,D,D', '2025-12-02 17:19:28'),
(136, 'SA', 'Serie A', '2025-2026', 'Atalanta BC', 102, 12, 13, 3, 7, 3, 16, 14, 2, 16, 'W,L,L,L,D', '2025-12-02 17:19:28'),
(137, 'SA', 'Serie A', '2025-2026', 'Cagliari Calcio', 104, 15, 13, 2, 5, 6, 13, 19, -6, 11, 'L,D,D,L,L', '2025-12-02 17:19:28'),
(138, 'SA', 'Serie A', '2025-2026', 'Parma Calcio 1913', 112, 17, 13, 2, 5, 6, 9, 17, -8, 11, 'L,W,D,L,L', '2025-12-02 17:19:28'),
(139, 'SA', 'Serie A', '2025-2026', 'AC Pisa 1909', 487, 18, 13, 1, 7, 5, 10, 18, -8, 10, 'L,D,W,D,D', '2025-12-02 17:19:28'),
(140, 'SA', 'Serie A', '2025-2026', 'US Lecce', 5890, 14, 13, 3, 4, 6, 10, 17, -7, 13, 'W,L,D,W,L', '2025-12-02 17:19:28'),
(141, 'SA', 'Serie A', '2025-2026', 'Genoa CFC', 107, 16, 13, 2, 5, 6, 13, 20, -7, 11, 'W,D,D,W,L', '2025-12-02 17:19:28'),
(142, 'SA', 'Serie A', '2025-2026', 'ACF Fiorentina', 99, 19, 13, 0, 6, 7, 10, 21, -11, 6, 'L,D,D,L,L', '2025-12-02 17:19:28'),
(143, 'SA', 'Serie A', '2025-2026', 'Hellas Verona FC', 450, 20, 13, 0, 6, 7, 8, 20, -12, 6, 'L,L,D,L,L', '2025-12-02 17:19:28'),
(264, 'PD', 'Primera Division', '2025-2026', 'FC Barcelona', 81, 1, 14, 11, 1, 2, 39, 16, 23, 34, 'W,W,W,W,L', '2025-12-02 17:18:38'),
(265, 'PD', 'Primera Division', '2025-2026', 'Real Madrid CF', 86, 2, 14, 10, 3, 1, 29, 13, 16, 33, 'D,D,D,W,W', '2025-12-02 17:18:38'),
(266, 'PD', 'Primera Division', '2025-2026', 'Villarreal CF', 94, 3, 14, 10, 2, 2, 29, 13, 16, 32, 'W,W,W,W,W', '2025-12-02 17:18:38'),
(267, 'PD', 'Primera Division', '2025-2026', 'Club Atlético de Madrid', 78, 4, 14, 9, 4, 1, 27, 11, 16, 31, 'W,W,W,W,W', '2025-12-02 17:18:38'),
(268, 'PD', 'Primera Division', '2025-2026', 'Real Betis Balompié', 90, 5, 14, 6, 6, 2, 22, 14, 8, 24, 'W,D,D,W,L', '2025-12-02 17:18:38'),
(269, 'PD', 'Primera Division', '2025-2026', 'RCD Espanyol de Barcelona', 80, 6, 14, 7, 3, 4, 18, 16, 2, 24, 'W,W,L,L,W', '2025-12-02 17:18:38'),
(270, 'PD', 'Primera Division', '2025-2026', 'Getafe CF', 82, 7, 14, 6, 2, 6, 13, 15, -2, 20, 'W,L,L,W,W', '2025-12-02 17:18:38'),
(271, 'PD', 'Primera Division', '2025-2026', 'Athletic Club', 77, 8, 14, 6, 2, 6, 14, 17, -3, 20, 'W,L,W,L,L', '2025-12-02 17:18:38'),
(272, 'PD', 'Primera Division', '2025-2026', 'Rayo Vallecano de Madrid', 87, 9, 14, 4, 5, 5, 13, 15, -2, 17, 'D,D,D,L,W', '2025-12-02 17:18:38'),
(273, 'PD', 'Primera Division', '2025-2026', 'Real Sociedad de Fútbol', 92, 10, 14, 4, 4, 6, 19, 21, -2, 16, 'L,W,D,W,W', '2025-12-02 17:18:38'),
(274, 'PD', 'Primera Division', '2025-2026', 'Elche CF', 285, 11, 14, 3, 7, 4, 15, 17, -2, 16, 'L,D,D,L,L', '2025-12-02 17:18:38'),
(275, 'PD', 'Primera Division', '2025-2026', 'RC Celta de Vigo', 558, 12, 14, 3, 7, 4, 16, 19, -3, 16, 'L,W,L,W,W', '2025-12-02 17:18:38'),
(276, 'PD', 'Primera Division', '2025-2026', 'Sevilla FC', 559, 13, 14, 5, 1, 8, 19, 23, -4, 16, 'L,L,W,L,L', '2025-12-02 17:18:38'),
(277, 'PD', 'Primera Division', '2025-2026', 'Deportivo Alavés', 263, 14, 14, 4, 3, 7, 12, 15, -3, 15, 'L,L,L,W,L', '2025-12-02 17:18:38'),
(278, 'PD', 'Primera Division', '2025-2026', 'Valencia CF', 95, 15, 14, 3, 5, 6, 13, 22, -9, 14, 'D,W,D,L,L', '2025-12-02 17:18:38'),
(279, 'PD', 'Primera Division', '2025-2026', 'RCD Mallorca', 89, 16, 14, 3, 4, 7, 15, 22, -7, 13, 'D,L,W,L,D', '2025-12-02 17:18:38'),
(280, 'PD', 'Primera Division', '2025-2026', 'CA Osasuna', 79, 17, 14, 3, 3, 8, 12, 18, -6, 12, 'D,L,L,D,L', '2025-12-02 17:18:38'),
(281, 'PD', 'Primera Division', '2025-2026', 'Girona FC', 298, 18, 14, 2, 6, 6, 13, 26, -13, 12, 'D,D,W,L,D', '2025-12-02 17:18:38'),
(282, 'PD', 'Primera Division', '2025-2026', 'Levante UD', 88, 19, 14, 2, 3, 9, 16, 26, -10, 9, 'L,L,L,L,D', '2025-12-02 17:18:38'),
(283, 'PD', 'Primera Division', '2025-2026', 'Real Oviedo', 1048, 20, 14, 2, 3, 9, 7, 22, -15, 9, 'L,D,L,D,D', '2025-12-02 17:18:38');

-- --------------------------------------------------------

--
-- Table structure for table `lineup_injuries`
--

CREATE TABLE `lineup_injuries` (
  `id` int(11) NOT NULL,
  `lineup_match_id` int(11) NOT NULL,
  `team_side` enum('home','away') NOT NULL,
  `player_name` varchar(100) NOT NULL,
  `reason` varchar(120) DEFAULT NULL,
  `type` enum('injury','suspension') NOT NULL DEFAULT 'injury',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lineup_injuries`
--

INSERT INTO `lineup_injuries` (`id`, `lineup_match_id`, `team_side`, `player_name`, `reason`, `type`, `created_at`) VALUES
(1, 1, 'home', 'Romeo Lavia', 'Muscle injury', 'injury', '2026-01-28 02:00:46'),
(2, 1, 'away', 'Pedri', 'Muscle injury', 'injury', '2026-01-28 02:00:46'),
(3, 1, 'home', 'Cole', 'Broken toe', 'injury', '2026-01-28 02:00:46'),
(4, 1, 'away', 'Gavi', 'Knee injury', 'injury', '2026-01-28 02:00:46'),
(5, 1, 'home', 'Dario Essugo', 'Thigh injury', 'injury', '2026-01-28 02:00:46'),
(6, 1, 'away', 'Marc-Andre ter Stegen', 'Back injury', 'injury', '2026-01-28 02:00:46'),
(7, 1, 'home', 'Levi Colwill', 'Cruciate ligament injury', 'injury', '2026-01-28 02:00:46'),
(8, 1, 'home', 'Mykhailo Mudryk', 'Personal reasons', 'suspension', '2026-01-28 02:00:46');

-- --------------------------------------------------------

--
-- Table structure for table `lineup_matches`
--

CREATE TABLE `lineup_matches` (
  `id` int(11) NOT NULL,
  `home_team` varchar(100) NOT NULL,
  `away_team` varchar(100) NOT NULL,
  `competition` varchar(120) NOT NULL,
  `match_date` datetime NOT NULL,
  `home_logo` varchar(255) DEFAULT NULL,
  `away_logo` varchar(255) DEFAULT NULL,
  `home_formation` varchar(20) DEFAULT NULL,
  `away_formation` varchar(20) DEFAULT NULL,
  `home_coach` varchar(100) DEFAULT NULL,
  `away_coach` varchar(100) DEFAULT NULL,
  `status` enum('scheduled','live','finished') DEFAULT 'scheduled',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lineup_matches`
--

INSERT INTO `lineup_matches` (`id`, `home_team`, `away_team`, `competition`, `match_date`, `home_logo`, `away_logo`, `home_formation`, `away_formation`, `home_coach`, `away_coach`, `status`, `created_at`, `updated_at`) VALUES
(1, 'CHELSEA', 'BARCELONA', 'UEFA CHAMPIONS LEAGUE', '2025-01-15 21:00:00', './assets/images/footlogos/colorfullogos/chels.png', './assets/images/footlogos/colorfullogos/fcbarca.png', '4-3-2-1', '4-3-3', 'Enzo Maresca', 'Xavi Hernandez', 'finished', '2026-01-28 17:55:03', '2026-01-29 21:08:41');

-- --------------------------------------------------------

--
-- Table structure for table `lineup_players`
--

CREATE TABLE `lineup_players` (
  `id` int(11) NOT NULL,
  `lineup_match_id` int(11) NOT NULL,
  `team_side` enum('home','away') NOT NULL,
  `player_name` varchar(100) NOT NULL,
  `player_number` int(11) DEFAULT NULL,
  `position_label` varchar(20) DEFAULT NULL,
  `pos_x` decimal(5,2) DEFAULT NULL,
  `pos_y` decimal(5,2) DEFAULT NULL,
  `is_starter` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lineup_players`
--

INSERT INTO `lineup_players` (`id`, `lineup_match_id`, `team_side`, `player_name`, `player_number`, `position_label`, `pos_x`, `pos_y`, `is_starter`, `created_at`) VALUES
(1, 1, 'home', 'Robert Sanchez', 1, 'GK', 50.00, 7.00, 1, '2026-01-28 01:58:39'),
(2, 1, 'home', 'Malo Gusto', 27, 'RB', 18.00, 17.00, 1, '2026-01-28 01:58:39'),
(3, 1, 'home', 'Wesley Fofana', 29, 'CB', 36.00, 17.00, 1, '2026-01-28 01:58:39'),
(4, 1, 'home', 'Trevoh Chalobah', 23, 'CB', 64.00, 17.00, 1, '2026-01-28 01:58:39'),
(5, 1, 'home', 'Marc Cucurella', 3, 'LB', 82.00, 17.00, 1, '2026-01-28 01:58:39'),
(6, 1, 'home', 'Reece James', 24, 'CM', 30.00, 27.00, 1, '2026-01-28 01:58:39'),
(7, 1, 'home', 'Moises Caicedo', 25, 'CM', 70.00, 27.00, 1, '2026-01-28 01:58:39'),
(8, 1, 'home', 'Estevao', 41, 'RW', 18.00, 37.00, 1, '2026-01-28 01:58:39'),
(9, 1, 'home', 'Enzo Fernandez', 8, 'CM', 50.00, 37.00, 1, '2026-01-28 01:58:39'),
(10, 1, 'home', 'Alejandro Garnacho', 49, 'LW', 82.00, 37.00, 1, '2026-01-28 01:58:39'),
(11, 1, 'home', 'Pedro Neto', 7, 'ST', 50.00, 47.00, 1, '2026-01-28 01:58:39'),
(12, 1, 'away', 'Robert Lewandowski', 9, 'ST', 50.00, 56.00, 1, '2026-01-28 01:58:39'),
(13, 1, 'away', 'Ferran Torres', 7, 'LW', 20.00, 64.00, 1, '2026-01-28 01:58:39'),
(14, 1, 'away', 'Fermin Lopez', 16, 'CM', 50.00, 64.00, 1, '2026-01-28 01:58:39'),
(15, 1, 'away', 'Lamine Yamal', 10, 'RW', 80.00, 64.00, 1, '2026-01-28 01:58:39'),
(16, 1, 'away', 'Frenkie De Jong', 21, 'CM', 30.00, 72.00, 1, '2026-01-28 01:58:39'),
(17, 1, 'away', 'Eric Garcia', 24, 'CM', 70.00, 72.00, 1, '2026-01-28 01:58:39'),
(18, 1, 'away', 'Alejandro Balde', 3, 'LB', 18.00, 82.00, 1, '2026-01-28 01:58:39'),
(19, 1, 'away', 'Pau Cubarsi', 5, 'CB', 36.00, 82.00, 1, '2026-01-28 01:58:39'),
(20, 1, 'away', 'Ronald Araujo', 4, 'CB', 64.00, 82.00, 1, '2026-01-28 01:58:39'),
(21, 1, 'away', 'Jules Kounde', 23, 'RB', 82.00, 82.00, 1, '2026-01-28 01:58:39'),
(22, 1, 'away', 'Joan Garcia', 1, 'GK', 50.00, 93.00, 1, '2026-01-28 01:58:39'),
(23, 1, 'home', 'Filip Jorgensen', 12, 'GK', NULL, NULL, 0, '2026-01-28 01:58:39'),
(24, 1, 'home', 'Tosin Adarabioyo', 4, 'CB', NULL, NULL, 0, '2026-01-28 01:58:39'),
(25, 1, 'home', 'Benoit Badiashile', 5, 'CB', NULL, NULL, 0, '2026-01-28 01:58:39'),
(26, 1, 'home', 'Jorrel Hato', 21, 'LB', NULL, NULL, 0, '2026-01-28 01:58:39'),
(27, 1, 'home', 'Joshua Acheampong', 34, 'RB', NULL, NULL, 0, '2026-01-28 01:58:39'),
(28, 1, 'home', 'Andrey Santos', 17, 'CM', NULL, NULL, 0, '2026-01-28 01:58:39'),
(29, 1, 'home', 'Facundo Buonanotte', 40, 'AM', NULL, NULL, 0, '2026-01-28 01:58:39'),
(30, 1, 'home', 'Liam Delap', 8, 'ST', NULL, NULL, 0, '2026-01-28 01:58:39'),
(31, 1, 'home', 'Jamie Gittens', 11, 'LW', NULL, NULL, 0, '2026-01-28 01:58:39'),
(32, 1, 'home', 'Joao Pedro', 21, 'FW', NULL, NULL, 0, '2026-01-28 01:58:39'),
(33, 1, 'home', 'Marc Guiu', 38, 'ST', NULL, NULL, 0, '2026-01-28 01:58:39'),
(34, 1, 'away', 'Wojciech Szczesny', 25, 'GK', NULL, NULL, 0, '2026-01-28 01:58:39'),
(35, 1, 'away', 'Diego Kochen', 31, 'GK', NULL, NULL, 0, '2026-01-28 01:58:39'),
(36, 1, 'away', 'Andreas Christensen', 15, 'CB', NULL, NULL, 0, '2026-01-28 01:58:39'),
(37, 1, 'away', 'Gerard Martin', 18, 'LB', NULL, NULL, 0, '2026-01-28 01:58:39'),
(38, 1, 'away', 'Marc Casado', 17, 'CM', NULL, NULL, 0, '2026-01-28 01:58:39'),
(39, 1, 'away', 'Dani Olmo', 20, 'AM', NULL, NULL, 0, '2026-01-28 01:58:39'),
(40, 1, 'away', 'Marc Bernal', 22, 'CM', NULL, NULL, 0, '2026-01-28 01:58:39'),
(41, 1, 'away', 'Pedro Fernandez', 27, 'CM', NULL, NULL, 0, '2026-01-28 01:58:39'),
(42, 1, 'away', 'Raphinha', 11, 'RW', NULL, NULL, 0, '2026-01-28 01:58:39'),
(43, 1, 'away', 'Marcus Rashford', 14, 'FW', NULL, NULL, 0, '2026-01-28 01:58:39'),
(44, 1, 'away', 'Roony Bardghji', 28, 'RW', NULL, NULL, 0, '2026-01-28 01:58:39');

-- --------------------------------------------------------

--
-- Table structure for table `lineup_substitutions`
--

CREATE TABLE `lineup_substitutions` (
  `id` int(11) NOT NULL,
  `lineup_match_id` int(11) NOT NULL,
  `team_side` enum('home','away') NOT NULL,
  `minute` int(11) DEFAULT NULL,
  `player_out` varchar(100) NOT NULL,
  `player_in` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lineup_substitutions`
--

INSERT INTO `lineup_substitutions` (`id`, `lineup_match_id`, `team_side`, `minute`, `player_out`, `player_in`, `created_at`) VALUES
(1, 1, 'home', 46, 'Malo Gusto', 'Andrey Santos', '2026-01-28 01:59:51'),
(2, 1, 'away', 46, 'Ferran Torres', 'Marcus Rashford', '2026-01-28 01:59:51'),
(3, 1, 'home', 59, 'Alejandro Garnacho', 'Liam Delap', '2026-01-28 01:59:51'),
(4, 1, 'away', 62, 'Fermin Lopez', 'Andreas Christensen', '2026-01-28 01:59:51'),
(5, 1, 'home', 76, 'Pedro Neto', 'Jamie Gittens', '2026-01-28 01:59:51'),
(6, 1, 'away', 62, 'Robert Lewandowski', 'Raphinha', '2026-01-28 01:59:51'),
(7, 1, 'home', 82, 'Estevao', 'Tyrique George', '2026-01-28 01:59:51'),
(8, 1, 'away', 79, 'Alejandro Balde', 'Gerard Martin', '2026-01-28 01:59:51'),
(9, 1, 'home', 82, 'Reece James', 'Joshua Acheampong', '2026-01-28 01:59:51'),
(10, 1, 'away', 80, 'Lamine Yamal', 'Dani Olmo', '2026-01-28 01:59:51');

-- --------------------------------------------------------

--
-- Table structure for table `matches`
--

CREATE TABLE `matches` (
  `id` int(11) NOT NULL,
  `api_fixture_id` int(11) DEFAULT NULL,
  `home_team` varchar(100) NOT NULL,
  `away_team` varchar(100) NOT NULL,
  `match_date` datetime NOT NULL,
  `home_score` int(11) DEFAULT NULL,
  `away_score` int(11) DEFAULT NULL,
  `home_score_1h` int(11) DEFAULT NULL,
  `away_score_1h` int(11) DEFAULT NULL,
  `status` enum('upcoming','live','finished','cancelled') DEFAULT 'upcoming',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `matches`
--

INSERT INTO `matches` (`id`, `api_fixture_id`, `home_team`, `away_team`, `match_date`, `home_score`, `away_score`, `home_score_1h`, `away_score_1h`, `status`, `created_at`, `updated_at`) VALUES
(1, 538001, 'Manchester United FC', 'Manchester City FC', '2026-01-17 13:30:00', 2, 0, 0, 0, 'finished', '2026-01-12 20:51:34', '2026-01-27 15:51:29'),
(2, 537998, 'Chelsea FC', 'Brentford FC', '2026-01-17 15:00:00', 2, 0, 1, 0, 'finished', '2026-01-12 22:37:13', '2026-01-27 15:51:29'),
(3, 544405, 'Getafe CF', 'Valencia CF', '2026-01-18 13:00:00', 0, 1, 0, 0, 'finished', '2026-01-17 21:58:55', '2026-01-27 15:51:31'),
(4, 538004, 'Wolverhampton Wanderers FC', 'Newcastle United FC', '2026-01-18 14:00:00', 0, 0, 0, 0, 'finished', '2026-01-17 22:04:57', '2026-01-27 15:51:30'),
(5, 537996, 'Aston Villa FC', 'Everton FC', '2026-01-18 16:30:00', 0, 1, 0, 0, 'finished', '2026-01-17 22:08:22', '2026-01-27 15:51:29'),
(6, 537997, 'Brighton & Hove Albion FC', 'AFC Bournemouth', '2026-01-19 20:00:00', 1, 1, 0, 1, 'finished', '2026-01-19 00:19:04', '2026-01-27 15:51:29'),
(7, 540576, 'FC St. Pauli 1910', 'Hamburger SV', '2026-01-23 19:30:00', 0, 0, 0, 0, 'finished', '2026-01-19 00:19:04', '2026-01-27 15:51:31'),
(8, 538006, 'Crystal Palace FC', 'Chelsea FC', '2026-01-25 14:00:00', 1, 3, 0, 1, 'finished', '2026-01-25 10:02:05', '2026-01-27 15:51:30'),
(9, 538008, 'Brentford FC', 'Nottingham Forest FC', '2026-01-25 14:00:00', 0, 2, 0, 1, 'finished', '2026-01-25 10:02:42', '2026-01-27 15:51:30'),
(10, 538017, 'Brighton & Hove Albion FC', 'Everton FC', '2026-01-31 15:00:00', 1, 1, 0, 0, 'finished', '2026-01-27 14:39:33', '2026-02-01 19:06:44'),
(29, 538015, 'Sunderland AFC', 'Burnley FC', '2026-02-02 20:00:00', NULL, NULL, NULL, NULL, 'upcoming', '2026-02-01 19:06:27', '2026-02-01 19:06:27');

-- --------------------------------------------------------

--
-- Table structure for table `parlays`
--

CREATE TABLE `parlays` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `stake` decimal(10,2) NOT NULL,
  `total_odds` decimal(10,2) NOT NULL,
  `potential_payout` decimal(10,2) NOT NULL,
  `status` enum('pending','won','lost','cancelled') DEFAULT 'pending',
  `cashed_out_amount` decimal(10,2) DEFAULT NULL,
  `cashed_out_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `parlays`
--

INSERT INTO `parlays` (`id`, `user_id`, `stake`, `total_odds`, `potential_payout`, `status`, `cashed_out_amount`, `cashed_out_at`, `created_at`) VALUES
(1, 4, 56.00, 36.52, 2045.01, 'won', NULL, NULL, '2026-01-12 20:10:21'),
(2, 4, 20.00, 17.38, 347.51, 'won', NULL, NULL, '2026-01-12 20:10:30'),
(3, 4, 3.00, 3.10, 9.30, 'won', NULL, NULL, '2026-01-12 20:12:21'),
(4, 4, 20.00, 3.10, 62.00, 'won', NULL, NULL, '2026-01-12 20:16:49'),
(5, 4, 20.00, 19.42, 388.40, 'lost', NULL, NULL, '2026-01-12 20:51:34'),
(6, 4, 20.00, 7.49, 149.80, 'lost', NULL, NULL, '2026-01-12 21:28:34'),
(7, 4, 20.00, 9.08, 181.50, 'won', NULL, NULL, '2026-01-12 22:03:29'),
(8, 4, 20.00, 5.57, 111.37, 'won', NULL, NULL, '2026-01-12 22:06:36'),
(9, 4, 54.00, 16.10, 869.22, 'won', NULL, NULL, '2026-01-12 22:32:50'),
(10, 4, 22.00, 7.03, 154.61, 'lost', NULL, NULL, '2026-01-12 22:37:13'),
(11, 4, 555.00, 103.17, 57261.76, 'lost', NULL, NULL, '2026-01-13 17:45:03'),
(12, 4, 50.00, 8.27, 413.66, 'lost', NULL, NULL, '2026-01-17 21:58:55'),
(13, 4, 50.00, 26.82, 1341.05, 'lost', NULL, NULL, '2026-01-17 22:04:57'),
(14, 4, 20.00, 172.80, 3456.06, 'lost', NULL, NULL, '2026-01-17 22:08:22'),
(15, 4, 56.00, 253.98, 14223.07, 'lost', NULL, NULL, '2026-01-19 00:19:04'),
(16, 4, 34.00, 79.68, 2709.14, 'lost', NULL, NULL, '2026-01-25 10:02:05'),
(17, 4, 24.00, 26.87, 644.97, 'lost', NULL, NULL, '2026-01-25 10:02:42'),
(18, 4, 25.00, 25.03, 625.82, 'lost', NULL, NULL, '2026-01-27 14:39:33'),
(19, 4, 86.00, 12.74, 1095.74, 'pending', NULL, NULL, '2026-02-01 19:06:27');

-- --------------------------------------------------------

--
-- Table structure for table `parlay_selections`
--

CREATE TABLE `parlay_selections` (
  `id` int(11) NOT NULL,
  `parlay_id` int(11) NOT NULL,
  `match_id` int(11) NOT NULL,
  `bet_type` varchar(50) NOT NULL,
  `bet_value` varchar(20) DEFAULT NULL,
  `bet_category` varchar(30) DEFAULT NULL,
  `odds` decimal(5,2) NOT NULL,
  `status` enum('pending','won','lost','cancelled') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `parlay_selections`
--

INSERT INTO `parlay_selections` (`id`, `parlay_id`, `match_id`, `bet_type`, `bet_value`, `bet_category`, `odds`, `status`) VALUES
(9, 5, 1, 'draw', NULL, 'draw', 2.86, 'lost'),
(10, 5, 1, '1h_away_win', NULL, '1h_away_win', 3.43, 'lost'),
(11, 5, 1, 'shots_towards_goal_over_12.5', 'over_12.5', 'shots_towards_goal', 1.98, 'lost'),
(12, 6, 1, 'draw', NULL, 'draw', 2.86, 'lost'),
(13, 6, 1, 'shots_towards_goal_under_10.5', 'under_10.5', 'shots_towards_goal', 2.62, 'lost'),
(21, 10, 2, 'Match Result', 'Chelsea FC', 'Premier League', 1.65, 'lost'),
(22, 10, 2, 'Corners', 'Over 10.5', 'Premier League', 1.91, 'lost'),
(23, 10, 2, 'Yellow Cards', 'Under 1.5', 'Premier League', 2.23, 'lost'),
(24, 11, 1, 'Match Result', 'Manchester United FC', 'Premier League', 1.78, 'lost'),
(25, 11, 1, '1st Half Result', 'Draw', 'Premier League', 3.23, 'lost'),
(26, 11, 1, '2nd Half Result', 'Manchester United FC', 'Premier League', 2.14, 'lost'),
(27, 11, 1, 'Corners', 'Over 10.5', 'Premier League', 1.86, 'lost'),
(28, 11, 1, 'Yellow Cards', 'Under 1.5', 'Premier League', 2.04, 'lost'),
(29, 11, 1, 'Cards (Total)', 'Under 4.5', 'Premier League', 2.21, 'lost'),
(30, 12, 3, 'Match Result', 'Draw', 'La Liga', 3.27, 'lost'),
(31, 12, 3, '2nd Half Result', 'Valencia CF', 'La Liga', 2.53, 'lost'),
(32, 13, 4, 'Match Result', 'Newcastle United FC', 'Premier League', 1.92, 'lost'),
(33, 13, 4, 'Corners', 'Under 10.5', 'Premier League', 2.08, 'lost'),
(34, 13, 4, 'Cards (Total)', 'Over 5.5', 'Premier League', 2.30, 'lost'),
(35, 14, 5, 'Match Result', 'Draw', 'Premier League', 3.39, 'lost'),
(36, 14, 5, '2nd Half Result', 'Everton FC', 'Premier League', 2.47, 'lost'),
(37, 14, 5, 'Corners', 'Over 10.5', 'Premier League', 2.07, 'lost'),
(38, 14, 5, 'Yellow Cards', 'Over 1.5', 'Premier League', 2.25, 'lost'),
(39, 14, 5, 'Cards (Total)', 'Under 4.5', 'Premier League', 2.11, 'lost'),
(40, 14, 5, 'Offsides', 'Over 3.5', 'Premier League', 2.10, 'lost'),
(41, 15, 6, 'Match Result', 'Draw', 'Premier League', 2.99, 'lost'),
(42, 15, 7, 'Match Result', 'Hamburger SV', 'Bundesliga', 1.99, 'lost'),
(43, 15, 7, 'Match Result', 'Draw', 'Bundesliga', 2.92, 'lost'),
(44, 15, 7, '2nd Half Result', 'Draw', 'Bundesliga', 3.21, 'lost'),
(45, 15, 7, 'Corners', 'Over 10.5', 'Bundesliga', 2.07, 'lost'),
(46, 15, 7, 'Yellow Cards', 'Under 3.5', 'Bundesliga', 2.20, 'lost'),
(47, 16, 8, 'Match Result', 'Crystal Palace FC', 'Premier League', 1.98, 'lost'),
(48, 16, 8, '1st Half Result', 'Draw', 'Premier League', 3.96, 'lost'),
(49, 16, 8, '2nd Half Result', 'Chelsea FC', 'Premier League', 2.63, 'lost'),
(50, 16, 8, 'Corners', 'Over 10.5', 'Premier League', 1.84, 'lost'),
(51, 16, 8, 'Yellow Cards', 'Under 1.5', 'Premier League', 2.10, 'lost'),
(52, 17, 9, 'Match Result', 'Brentford FC', 'Premier League', 1.53, 'lost'),
(53, 17, 9, 'Throw-ins', 'Under 40.5', 'Premier League', 2.22, 'lost'),
(54, 17, 9, '2nd Half Result', 'Brentford FC', 'Premier League', 1.84, 'lost'),
(55, 17, 9, 'Corners', 'Under 9.5', 'Premier League', 2.15, 'lost'),
(56, 17, 9, 'Offsides', 'Under 2.5', 'Premier League', 2.00, 'lost'),
(57, 18, 10, 'Match Result', 'Draw', 'Premier League', 2.72, 'lost'),
(58, 18, 10, '2nd Half Result', 'Draw', 'Premier League', 2.99, 'lost'),
(59, 18, 10, 'Yellow Cards', 'Under 1.5', 'Premier League', 1.90, 'lost'),
(60, 18, 10, 'Posts and Crossbar', 'Under 0.5', 'Premier League', 1.62, 'lost'),
(61, 19, 29, 'Match Result', 'Draw', 'Premier League', 3.16, 'pending'),
(62, 19, 29, 'Offsides', 'Over 3.5', 'Premier League', 2.10, 'pending'),
(63, 19, 29, 'Yellow Cards', 'Under 1.5', 'Premier League', 1.92, 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `promotions`
--

CREATE TABLE `promotions` (
  `id` int(11) NOT NULL,
  `title` varchar(120) NOT NULL,
  `description` text NOT NULL,
  `promo_code` varchar(50) DEFAULT NULL,
  `tag_label` varchar(60) DEFAULT NULL,
  `tag_style` varchar(40) DEFAULT NULL,
  `icon_name` varchar(80) DEFAULT NULL,
  `card_style` varchar(40) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `promotions`
--

INSERT INTO `promotions` (`id`, `title`, `description`, `promo_code`, `tag_label`, `tag_style`, `icon_name`, `card_style`, `is_active`, `sort_order`, `start_date`, `end_date`, `created_at`, `updated_at`) VALUES
(1, 'Welcome Bonus 123', 'Get 100% match on your first deposit up to $500.', 'WELCOME100', 'New Users', 'tag-green', 'featured_seasonal_and_gifts', 'card-purple', 1, 1, NULL, NULL, '2026-01-29 00:25:53', '2026-01-29 21:08:52'),
(2, 'Free Bet Friday', 'Place 5 bets during the week, get a free $20 bet on Friday.', 'FRIDAY20', 'Weekly', 'pill-weekly', 'calendar_today', 'card-blue', 1, 2, NULL, NULL, '2026-01-29 00:25:53', '2026-01-29 00:25:53'),
(3, 'Accumulator Boost', 'Up to 50% profit boost on accumulators with 5+ selections.', 'ACCA50', 'Popular', 'pill-popular', 'call_made', 'card-orange', 1, 3, NULL, NULL, '2026-01-29 00:25:53', '2026-01-29 00:25:53'),
(4, 'Refer a Friend', 'Invite your friends and get a $25 free bet for each successful signup.', 'FRIEND25', 'Limited', 'tag-red', 'contacts_product', 'card-pink', 1, 4, NULL, NULL, '2026-01-29 00:25:53', '2026-01-29 00:25:53'),
(5, 'Daily Odds Boost', 'Get boosted odds on selected matches every day.', 'BOOST10', 'Daily', 'pill-daily', 'bolt', 'card-yellow', 1, 5, NULL, NULL, '2026-01-29 00:25:53', '2026-01-29 00:25:53'),
(6, 'Cashback Weekend', 'Get 10% cashback on your net losses every weekend.', 'CASHBACK10', 'Weekly', 'pill-weekly-red', 'percent', 'card-red', 1, 6, NULL, NULL, '2026-01-29 00:25:53', '2026-01-29 00:25:53');

-- --------------------------------------------------------

--
-- Table structure for table `sports_page_leagues`
--

CREATE TABLE `sports_page_leagues` (
  `id` int(11) NOT NULL,
  `league_title` varchar(160) NOT NULL,
  `league_country` varchar(120) NOT NULL,
  `matches_count` int(11) NOT NULL DEFAULT 0,
  `matches_label` varchar(40) NOT NULL DEFAULT 'matches',
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sports_page_leagues`
--

INSERT INTO `sports_page_leagues` (`id`, `league_title`, `league_country`, `matches_count`, `matches_label`, `sort_order`, `is_active`) VALUES
(1, 'UEFA Champions League', 'Europe', 16, 'matches', 1, 1),
(2, 'Premier League', 'England', 38, 'matches', 2, 1),
(3, 'La Liga', 'Spain', 34, 'matches', 3, 1),
(4, 'Serie A', 'Italy', 32, 'matches', 4, 1),
(5, 'Bundesliga', 'Germany', 26, 'matches', 5, 1),
(6, 'Ligue 1', 'France', 26, 'matches', 6, 1),
(7, 'MLS', 'USA', 22, 'matches', 7, 1),
(8, 'Primeira Liga', 'Brazil', 18, 'matches', 8, 1);

-- --------------------------------------------------------

--
-- Table structure for table `sports_page_sections`
--

CREATE TABLE `sports_page_sections` (
  `id` int(11) NOT NULL,
  `popular_sports_title` varchar(120) NOT NULL,
  `top_leagues_title` varchar(120) NOT NULL,
  `newsletter_title` varchar(160) NOT NULL,
  `newsletter_placeholder` varchar(160) NOT NULL,
  `newsletter_button_text` varchar(60) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sports_page_sections`
--

INSERT INTO `sports_page_sections` (`id`, `popular_sports_title`, `top_leagues_title`, `newsletter_title`, `newsletter_placeholder`, `newsletter_button_text`, `created_at`, `updated_at`) VALUES
(1, 'Popular Sports', 'Top Leagues', 'SUBSCRIBE TO OUR NEWSLETTER', 'Enter your email address...', 'Subscribe', '2026-01-29 22:53:34', '2026-01-29 22:53:34');

-- --------------------------------------------------------

--
-- Table structure for table `sports_page_sports`
--

CREATE TABLE `sports_page_sports` (
  `id` int(11) NOT NULL,
  `sport_name` varchar(120) NOT NULL,
  `matches_count` int(11) NOT NULL DEFAULT 0,
  `matches_label` varchar(40) NOT NULL DEFAULT 'matches',
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sports_page_sports`
--

INSERT INTO `sports_page_sports` (`id`, `sport_name`, `matches_count`, `matches_label`, `sort_order`, `is_active`) VALUES
(1, 'Football', 234, 'matches', 1, 1),
(2, 'Formula 1', 28, 'matches', 2, 1),
(3, 'Basketball', 126, 'matches', 3, 1),
(4, 'Volleyball', 34, 'matches', 4, 1),
(5, 'Ice Hockey', 24, 'matches', 5, 1);

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` enum('deposit','withdrawal') NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` enum('pending','completed','failed','cancelled') DEFAULT 'pending',
  `payment_method` varchar(50) DEFAULT 'paypal',
  `payment_id` varchar(255) DEFAULT NULL,
  `payment_details` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `user_id`, `type`, `amount`, `status`, `payment_method`, `payment_id`, `payment_details`, `created_at`, `updated_at`) VALUES
(1, 2, 'deposit', 25.00, 'completed', 'paypal', 'demo_1762797238195', NULL, '2025-11-10 17:53:58', '2025-11-10 17:53:58'),
(2, 2, 'deposit', 100.00, 'completed', 'paypal', 'demo_1765734168305', NULL, '2025-12-14 17:42:48', '2025-12-14 17:42:48');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `balance` decimal(10,2) DEFAULT 1000.00,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `balance`, `role`, `created_at`) VALUES
(1, 'admin', 'admin@example.com', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcfl7p92ldGxad68LJZdL17lhWy', 5000.00, 'admin', '2025-11-28 21:34:43'),
(4, 'vesa susuri', 'vesasusuri@gmail.com', '$2y$10$dCh3Ck3xUSenPmuBE5yLQOgOCPGNCV6wig100O350df14Q8RyaDJe', 4340.91, 'user', '2026-01-12 20:10:13'),
(6, 'admin1', 'admin@admin.com', '$2y$10$UBkU041TXoFfzBaeyv1vcuhdru8OjP8GlnCmLfiRHoz1YqKhICsNS', 6000.00, 'admin', '2026-01-27 16:26:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `home_page_banner`
--
ALTER TABLE `home_page_banner`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `home_page_favorites`
--
ALTER TABLE `home_page_favorites`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_home_page_favorites_active` (`is_active`),
  ADD KEY `idx_home_page_favorites_sort` (`sort_order`);

--
-- Indexes for table `home_page_hero`
--
ALTER TABLE `home_page_hero`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `home_page_leagues`
--
ALTER TABLE `home_page_leagues`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_home_page_leagues_active` (`is_active`),
  ADD KEY `idx_home_page_leagues_sort` (`sort_order`);

--
-- Indexes for table `home_page_sections`
--
ALTER TABLE `home_page_sections`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `home_page_steps`
--
ALTER TABLE `home_page_steps`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_home_page_steps_sort` (`sort_order`);

--
-- Indexes for table `league_standings`
--
ALTER TABLE `league_standings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_league_team` (`league_code`,`team_name`,`season`),
  ADD KEY `league_code` (`league_code`),
  ADD KEY `position` (`position`);

--
-- Indexes for table `lineup_injuries`
--
ALTER TABLE `lineup_injuries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lineup_players`
--
ALTER TABLE `lineup_players`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lineup_substitutions`
--
ALTER TABLE `lineup_substitutions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `matches`
--
ALTER TABLE `matches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_api_fixture_id` (`api_fixture_id`),
  ADD KEY `idx_api_fixture_id` (`api_fixture_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_match_date` (`match_date`);

--
-- Indexes for table `parlays`
--
ALTER TABLE `parlays`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `parlay_selections`
--
ALTER TABLE `parlay_selections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_parlay_id` (`parlay_id`),
  ADD KEY `idx_match_id` (`match_id`);

--
-- Indexes for table `promotions`
--
ALTER TABLE `promotions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_promotions_active` (`is_active`),
  ADD KEY `idx_promotions_sort` (`sort_order`);

--
-- Indexes for table `sports_page_leagues`
--
ALTER TABLE `sports_page_leagues`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_sports_page_leagues_active` (`is_active`),
  ADD KEY `idx_sports_page_leagues_sort` (`sort_order`);

--
-- Indexes for table `sports_page_sections`
--
ALTER TABLE `sports_page_sections`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sports_page_sports`
--
ALTER TABLE `sports_page_sports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_sports_page_sports_active` (`is_active`),
  ADD KEY `idx_sports_page_sports_sort` (`sort_order`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `payment_id` (`payment_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_username` (`username`),
  ADD KEY `idx_email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `home_page_banner`
--
ALTER TABLE `home_page_banner`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `home_page_favorites`
--
ALTER TABLE `home_page_favorites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `home_page_hero`
--
ALTER TABLE `home_page_hero`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `home_page_leagues`
--
ALTER TABLE `home_page_leagues`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `home_page_sections`
--
ALTER TABLE `home_page_sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `home_page_steps`
--
ALTER TABLE `home_page_steps`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `league_standings`
--
ALTER TABLE `league_standings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=444;

--
-- AUTO_INCREMENT for table `lineup_injuries`
--
ALTER TABLE `lineup_injuries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `lineup_players`
--
ALTER TABLE `lineup_players`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=133;

--
-- AUTO_INCREMENT for table `lineup_substitutions`
--
ALTER TABLE `lineup_substitutions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `matches`
--
ALTER TABLE `matches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `parlays`
--
ALTER TABLE `parlays`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `parlay_selections`
--
ALTER TABLE `parlay_selections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `promotions`
--
ALTER TABLE `promotions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `sports_page_leagues`
--
ALTER TABLE `sports_page_leagues`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `sports_page_sections`
--
ALTER TABLE `sports_page_sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `sports_page_sports`
--
ALTER TABLE `sports_page_sports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `parlays`
--
ALTER TABLE `parlays`
  ADD CONSTRAINT `fk_parlays_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `parlay_selections`
--
ALTER TABLE `parlay_selections`
  ADD CONSTRAINT `fk_parlay_selections_match` FOREIGN KEY (`match_id`) REFERENCES `matches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_parlay_selections_parlay` FOREIGN KEY (`parlay_id`) REFERENCES `parlays` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
