-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 14, 2025 at 06:47 PM
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
-- Table structure for table `bets`
--

CREATE TABLE `bets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `match_id` int(11) NOT NULL,
  `bet_type` varchar(50) NOT NULL,
  `bet_value` varchar(20) DEFAULT NULL,
  `bet_category` varchar(30) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `odds` decimal(5,2) NOT NULL,
  `status` enum('pending','won','lost','cancelled') DEFAULT 'pending',
  `potential_payout` decimal(10,2) NOT NULL,
  `cashed_out_amount` decimal(10,2) DEFAULT NULL,
  `cashed_out_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(1, 'admin', 'admin@example.com', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcfl7p92ldGxad68LJZdL17lhWy', 5000.00, 'admin', '2025-11-28 21:34:43');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bets`
--
ALTER TABLE `bets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_match_id` (`match_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created_at` (`created_at`);

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
-- Indexes for table `lineup_matches`
--
ALTER TABLE `lineup_matches`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_match_date` (`match_date`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `lineup_players`
--
ALTER TABLE `lineup_players`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_lineup_match_id` (`lineup_match_id`),
  ADD KEY `idx_team_side` (`team_side`),
  ADD KEY `idx_is_starter` (`is_starter`),
  ADD UNIQUE KEY `uniq_lineup_player` (`lineup_match_id`, `team_side`, `player_name`, `player_number`, `pos_x`, `pos_y`, `is_starter`);

--
-- Indexes for table `lineup_substitutions`
--
ALTER TABLE `lineup_substitutions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_lineup_match_id` (`lineup_match_id`),
  ADD KEY `idx_team_side` (`team_side`);

--
-- Indexes for table `lineup_injuries`
--
ALTER TABLE `lineup_injuries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_lineup_match_id` (`lineup_match_id`),
  ADD KEY `idx_team_side` (`team_side`);

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
-- AUTO_INCREMENT for table `bets`
--
ALTER TABLE `bets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `matches`
--
ALTER TABLE `matches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lineup_matches`
--
ALTER TABLE `lineup_matches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lineup_players`
--
ALTER TABLE `lineup_players`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lineup_substitutions`
--
ALTER TABLE `lineup_substitutions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lineup_injuries`
--
ALTER TABLE `lineup_injuries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `parlays`
--
ALTER TABLE `parlays`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `parlay_selections`
--
ALTER TABLE `parlay_selections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bets`
--
ALTER TABLE `bets`
  ADD CONSTRAINT `fk_bets_match` FOREIGN KEY (`match_id`) REFERENCES `matches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_bets_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lineup_players`
--
ALTER TABLE `lineup_players`
  ADD CONSTRAINT `fk_lineup_players_match` FOREIGN KEY (`lineup_match_id`) REFERENCES `lineup_matches` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lineup_substitutions`
--
ALTER TABLE `lineup_substitutions`
  ADD CONSTRAINT `fk_lineup_substitutions_match` FOREIGN KEY (`lineup_match_id`) REFERENCES `lineup_matches` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lineup_injuries`
--
ALTER TABLE `lineup_injuries`
  ADD CONSTRAINT `fk_lineup_injuries_match` FOREIGN KEY (`lineup_match_id`) REFERENCES `lineup_matches` (`id`) ON DELETE CASCADE;

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
ALTER TABLE `promotions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_promotions_active` (`is_active`),
  ADD KEY `idx_promotions_sort` (`sort_order`);
ALTER TABLE `promotions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

  
CREATE TABLE `home_page_hero` (
  `id` int(11) NOT NULL,
  `sports_text` varchar(40) NOT NULL,
  `bet_text` varchar(40) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
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
CREATE TABLE `home_page_steps` (
  `id` int(11) NOT NULL,
  `step_number` int(11) NOT NULL,
  `step_title` varchar(160) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
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
CREATE TABLE `home_page_favorites` (
  `id` int(11) NOT NULL,
  `item_label` varchar(60) NOT NULL,
  `item_name` varchar(120) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
ALTER TABLE `home_page_hero`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `home_page_sections`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `home_page_steps`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_home_page_steps_sort` (`sort_order`);
ALTER TABLE `home_page_banner`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `home_page_leagues`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_home_page_leagues_active` (`is_active`),
  ADD KEY `idx_home_page_leagues_sort` (`sort_order`);
ALTER TABLE `home_page_favorites`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_home_page_favorites_active` (`is_active`),
  ADD KEY `idx_home_page_favorites_sort` (`sort_order`);
ALTER TABLE `home_page_hero`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `home_page_sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `home_page_steps`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `home_page_banner`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `home_page_leagues`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `home_page_favorites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
  
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
CREATE TABLE `sports_page_sports` (
  `id` int(11) NOT NULL,
  `sport_name` varchar(120) NOT NULL,
  `matches_count` int(11) NOT NULL DEFAULT 0,
  `matches_label` varchar(40) NOT NULL DEFAULT 'matches',
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
CREATE TABLE `sports_page_leagues` (
  `id` int(11) NOT NULL,
  `league_title` varchar(160) NOT NULL,
  `league_country` varchar(120) NOT NULL,
  `matches_count` int(11) NOT NULL DEFAULT 0,
  `matches_label` varchar(40) NOT NULL DEFAULT 'matches',
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
ALTER TABLE `sports_page_sections`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `sports_page_sports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_sports_page_sports_active` (`is_active`),
  ADD KEY `idx_sports_page_sports_sort` (`sort_order`);
ALTER TABLE `sports_page_leagues`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_sports_page_leagues_active` (`is_active`),
  ADD KEY `idx_sports_page_leagues_sort` (`sort_order`);
ALTER TABLE `sports_page_sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `sports_page_sports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `sports_page_leagues`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
