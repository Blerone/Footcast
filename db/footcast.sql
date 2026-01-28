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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
