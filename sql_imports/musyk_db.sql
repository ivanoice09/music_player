-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Giu 09, 2025 alle 17:56
-- Versione del server: 10.4.32-MariaDB
-- Versione PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `musyk_db`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `library_items`
--

CREATE TABLE `library_items` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `item_type` enum('playlist','artist','album') NOT NULL,
  `item_id` varchar(255) NOT NULL,
  `is_pinned` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Stores additional item data in JSON format' CHECK (json_valid(`metadata`))
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dump dei dati per la tabella `library_items`
--

INSERT INTO `library_items` (`id`, `user_id`, `item_type`, `item_id`, `is_pinned`, `created_at`, `metadata`) VALUES
(16, 1, 'playlist', '55', 1, '2025-06-09 15:39:17', '{\"name\":\"MyPlaylist55\",\"image_url\":\"http:\\/\\/localhost\\/musyk\\/public\\/assets\\/images\\/default-playlist-512px.png\"}'),
(17, 1, 'playlist', '56', 1, '2025-06-09 15:39:52', '{\"name\":\"MyPlaylist56\",\"image_url\":\"http:\\/\\/localhost\\/musyk\\/public\\/assets\\/images\\/default-playlist-512px.png\"}'),
(18, 2, 'playlist', '57', 1, '2025-06-09 15:46:03', '{\"name\":\"Favorites\",\"image\":\"http:\\/\\/localhost\\/musyk\\/public\\/assets\\/images\\/favourite-playlist-512px.png\",\"is_default\":true,\"is_pinned\":true}'),
(19, 2, 'playlist', '58', 0, '2025-06-09 15:50:53', '{\"name\":\"MyPlaylist2\",\"image_url\":\"http:\\/\\/localhost\\/musyk\\/public\\/assets\\/images\\/default-playlist-512px.png\"}'),
(20, 2, 'playlist', '59', 0, '2025-06-09 15:52:29', '{\"name\":\"MyPlaylist3\",\"image_url\":\"http:\\/\\/localhost\\/musyk\\/public\\/assets\\/images\\/default-playlist-512px.png\"}'),
(21, 2, 'playlist', '60', 0, '2025-06-09 15:53:25', '{\"name\":\"MyPlaylist4\",\"image_url\":\"http:\\/\\/localhost\\/musyk\\/public\\/assets\\/images\\/default-playlist-512px.png\"}');

-- --------------------------------------------------------

--
-- Struttura della tabella `playlists`
--

CREATE TABLE `playlists` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `is_default` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dump dei dati per la tabella `playlists`
--

INSERT INTO `playlists` (`id`, `user_id`, `name`, `image_url`, `is_default`, `created_at`, `description`) VALUES
(1, 1, 'MyPlaylist1', 'http://localhost/musyk/public/assets/images/default-playlist.png', 0, '2025-06-06 10:55:33', ''),
(2, 1, 'MyPlaylist2', 'http://localhost/musyk/public/assets/images/default-playlist.png', 0, '2025-06-06 10:58:12', ''),
(3, 1, 'MyPlaylist3', 'http://localhost/musyk/public/assets/images/default-playlist.png', 0, '2025-06-06 10:59:41', ''),
(4, 1, 'MyPlaylist4', 'http://localhost/musyk/public/assets/images/default-playlist.png', 0, '2025-06-06 12:10:47', ''),
(5, 1, 'MyPlaylist5', 'http://localhost/musyk/public/assets/images/default-playlist.png', 0, '2025-06-06 12:11:06', ''),
(6, 1, 'MyPlaylist6', 'http://localhost/musyk/public/assets/images/default-playlist.png', 0, '2025-06-06 12:28:59', ''),
(7, 1, 'MyPlaylist7', 'http://localhost/musyk/public/assets/images/default-playlist.png', 0, '2025-06-06 12:43:28', ''),
(8, 1, 'MyPlaylist8', 'http://localhost/musyk/public/assets/images/default-playlist.png', 0, '2025-06-06 12:47:03', ''),
(9, 1, 'MyPlaylist9', 'http://localhost/musyk/public/assets/images/default-playlist.png', 0, '2025-06-06 13:17:58', ''),
(10, 1, 'MyPlaylist10', 'http://localhost/musyk/public/assets/images/default-playlist.png', 0, '2025-06-06 13:22:15', ''),
(11, 1, 'MyPlaylist11', 'http://localhost/musyk/public/assets/images/default-playlist.png', 0, '2025-06-06 13:25:50', ''),
(12, 1, 'MyPlaylist12', 'http://localhost/musyk/public/assets/images/default-playlist.png', 0, '2025-06-06 13:26:10', ''),
(13, 1, 'MyPlaylist13', 'http://localhost/musyk/public/assets/images/default-playlist.png', 0, '2025-06-06 13:27:59', ''),
(14, 1, 'MyPlaylist14', 'http://localhost/musyk/public/assets/images/default-playlist.png', 0, '2025-06-06 13:36:01', ''),
(15, 1, 'MyPlaylist15', 'http://localhost/musyk/public/assets/images/default-playlist.png', 0, '2025-06-06 13:41:21', ''),
(16, 1, 'MyPlaylist16', 'http://localhost/musyk/public/assets/images/default-playlist.png', 0, '2025-06-06 13:41:42', ''),
(17, 1, 'MyPlaylist17', 'http://localhost/musyk/public/assets/images/default-playlist.png', 0, '2025-06-06 13:42:15', ''),
(18, 1, 'MyPlaylist18', 'http://localhost/musyk/public/assets/images/default-playlist.png', 0, '2025-06-06 13:54:01', ''),
(19, 1, 'MyPlaylist19', 'http://localhost/musyk/public/assets/images/default-playlist.png', 0, '2025-06-06 14:05:38', ''),
(20, 1, 'MyPlaylist20', 'http://localhost/musyk/public/assets/images/default-playlist.png', 0, '2025-06-06 14:08:00', ''),
(21, 1, 'MyPlaylist21', 'http://localhost/musyk/public/assets/images/default-playlist.png', 0, '2025-06-06 14:12:25', ''),
(22, 1, 'MyPlaylist22', 'http://localhost/musyk/public/assets/images/default-playlist.png', 0, '2025-06-06 14:17:01', ''),
(23, 1, 'MyPlaylist23', 'http://localhost/musyk/public/assets/images/default-playlist.png', 0, '2025-06-06 14:29:11', ''),
(24, 1, 'MyPlaylist24', 'http://localhost/musyk/public/assets/images/default-playlist.png', 0, '2025-06-06 14:44:47', ''),
(25, 1, 'MyPlaylist25', 'http://localhost/musyk/public/assets/images/default-playlist.png', 0, '2025-06-06 14:45:22', ''),
(26, 1, 'MyPlaylist26', 'http://localhost/musyk/public/assets/images/default-playlist.png', 0, '2025-06-06 15:19:40', ''),
(27, 1, 'MyPlaylist27', 'http://localhost/musyk/public/assets/images/default-playlist.png', 0, '2025-06-06 15:35:11', ''),
(28, 1, 'MyPlaylist28', 'http://localhost/musyk/public/assets/images/default-playlist.png', 0, '2025-06-06 15:42:13', ''),
(29, 1, 'MyPlaylist29', 'http://localhost/musyk/public/assets/images/default-playlist.png', 0, '2025-06-09 07:16:30', ''),
(30, 1, 'MyPlaylist30', 'http://localhost/musyk/public/assets/images/default-playlist.png', 0, '2025-06-09 07:23:16', ''),
(31, 1, 'MyPlaylist31', 'http://localhost/musyk/public/assets/images/default-playlist.png', 0, '2025-06-09 07:51:46', ''),
(32, 1, 'MyPlaylist32', 'http://localhost/musyk/public/assets/images/default-playlist.png', 0, '2025-06-09 07:53:53', ''),
(33, 1, 'MyPlaylist33', 'http://localhost/musyk/public/assets/images/default-playlist.png', 0, '2025-06-09 08:08:13', ''),
(34, 1, 'MyPlaylist34', 'http://localhost/musyk/public/assets/images/playlist/default-playlist.png', 0, '2025-06-09 08:09:02', ''),
(35, 1, 'MyPlaylist35', 'http://localhost/musyk/public/assets/images/playlist/default-playlist.png', 0, '2025-06-09 08:09:31', ''),
(36, 1, 'MyPlaylist36', 'http://localhost/musyk/public/assets/images/playlist/default-playlist-16px.png', 0, '2025-06-09 08:11:39', ''),
(37, 1, 'MyPlaylist37', 'http://localhost/musyk/public/assets/images/playlist/default-playlist-512px.png', 0, '2025-06-09 08:15:47', ''),
(38, 1, 'MyPlaylist38', 'http://localhost/musyk/public/assets/images/playlist/default-playlist-512px.png', 0, '2025-06-09 08:15:59', ''),
(39, 1, 'MyPlaylist39', 'http://localhost/musyk/public/assets/images/playlist/default-playlist-512px.png', 0, '2025-06-09 08:18:22', ''),
(40, 1, 'MyPlaylist40', 'http://localhost/musyk/public/assets/images/playlist/default-playlist-512px.png', 0, '2025-06-09 08:18:58', ''),
(41, 1, 'MyPlaylist41', 'http://localhost/musyk/public/assets/images/playlist/default-playlist-512px.png', 0, '2025-06-09 08:21:47', ''),
(42, 1, 'MyPlaylist42', 'http://localhost/musyk/public/assets/images/playlist/default-playlist-512px.png', 0, '2025-06-09 08:22:16', ''),
(43, 1, 'MyPlaylist43', 'http://localhost/musyk/public/assets/images/playlist/default-playlist-512px.png', 0, '2025-06-09 08:27:25', ''),
(44, 1, 'MyPlaylist44', 'http://localhost/musyk/public/assets/images/playlist/default-playlist-512px.png', 0, '2025-06-09 08:28:12', ''),
(45, 1, 'MyPlaylist45', 'http://localhost/musyk/public/assets/images/playlist/default-playlist-512px.png', 0, '2025-06-09 08:28:46', ''),
(46, 1, 'MyPlaylist46', 'http://localhost/musyk/public/assets/images/playlist/default-playlist-512px.png', 0, '2025-06-09 08:29:37', ''),
(47, 1, 'MyPlaylist47', 'http://localhost/musyk/public/assets/images/playlist/default-playlist-512px.png', 0, '2025-06-09 08:31:59', ''),
(48, 1, 'MyPlaylist48', 'http://localhost/musyk/public/assets/images/playlist/default-playlist-512px.png', 0, '2025-06-09 08:32:25', ''),
(49, 1, 'MyPlaylist49', 'http://localhost/musyk/public/assets/images/playlist/default-playlist-512px.png', 0, '2025-06-09 08:36:34', ''),
(50, 1, 'MyPlaylist50', 'http://localhost/musyk/public/assets/images/playlist/default-playlist-512px.png', 0, '2025-06-09 08:36:42', ''),
(51, 1, 'MyPlaylist51', 'http://localhost/musyk/public/assets/images/playlist/default-playlist-512px.png', 0, '2025-06-09 08:36:58', ''),
(52, 1, 'MyPlaylist52', 'http://localhost/musyk/public/assets/images/playlist/default-playlist-512px.png', 0, '2025-06-09 08:42:11', ''),
(53, 1, 'MyPlaylist53', 'http://localhost/musyk/public/assets/images/playlist/default-playlist-512px.png', 0, '2025-06-09 09:03:27', ''),
(54, 1, 'Favorites', 'http://localhost/musyk/public/assets/images/playlist/favourite-playlist-512px.png', 1, '2025-06-09 14:14:31', 'Your favorite tracks'),
(55, 1, 'MyPlaylist55', 'http://localhost/musyk/public/assets/images/default-playlist-512px.png', 0, '2025-06-09 15:39:17', ''),
(56, 1, 'MyPlaylist56', 'http://localhost/musyk/public/assets/images/default-playlist-512px.png', 0, '2025-06-09 15:39:52', ''),
(57, 2, 'Favorites', 'http://localhost/musyk/public/assets/images/favourite-playlist-512px.png', 1, '2025-06-09 15:46:03', 'Your favorite tracks'),
(58, 2, 'MyPlaylist2', 'http://localhost/musyk/public/assets/images/default-playlist-512px.png', 0, '2025-06-09 15:50:53', ''),
(59, 2, 'MyPlaylist3', 'http://localhost/musyk/public/assets/images/default-playlist-512px.png', 0, '2025-06-09 15:52:29', ''),
(60, 2, 'MyPlaylist4', 'http://localhost/musyk/public/assets/images/default-playlist-512px.png', 0, '2025-06-09 15:53:25', '');

-- --------------------------------------------------------

--
-- Struttura della tabella `playlist_songs`
--

CREATE TABLE `playlist_songs` (
  `id` int(11) NOT NULL,
  `playlist_id` int(11) NOT NULL,
  `song_id` varchar(255) NOT NULL,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dump dei dati per la tabella `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `created_at`) VALUES
(1, 'ivanoice', 'ivankovicsibsion@gmail.com', '$2y$10$h5BI8hYj2vRTMpJeGOD3zujc59.Hw9tXFqMEYTUZeKMhuheBuiTVW', '2025-05-20 14:25:16'),
(2, 'gigi', 'gigiconch@gmail.com', '$2y$10$gKFyCHPLBWvd7hyLLOE4d.CYehuZ.9BRuQCD6HtvCndjf5msO/B7q', '2025-06-09 15:46:03');

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `library_items`
--
ALTER TABLE `library_items`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `playlists`
--
ALTER TABLE `playlists`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `playlist_songs`
--
ALTER TABLE `playlist_songs`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `library_items`
--
ALTER TABLE `library_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT per la tabella `playlists`
--
ALTER TABLE `playlists`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT per la tabella `playlist_songs`
--
ALTER TABLE `playlist_songs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
