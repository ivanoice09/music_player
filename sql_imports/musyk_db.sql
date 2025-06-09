-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Giu 09, 2025 alle 09:33
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
(1, 1, 'artist', '593864', 1, '2025-06-05 14:26:14', '{\"name\":\"Praz Khanal\",\"image\":\"https:\\/\\/usercontent.jamendo.com?type=artist&id=593864&width=300\"}'),
(2, 1, 'artist', '596766', 0, '2025-06-05 14:26:14', '{\"name\":\"Doc Hartley\",\"image\":\"\"}'),
(3, 1, 'artist', '595393', 0, '2025-06-05 14:26:14', '{\"name\":\"Lowtone Music\",\"image\":\"https:\\/\\/usercontent.jamendo.com?type=artist&id=595393&width=300\"}'),
(4, 1, 'album', '570554', 1, '2025-06-05 14:26:14', '{\"name\":\"Be Without U\",\"image\":\"https:\\/\\/usercontent.jamendo.com?type=album&id=570554&width=300\",\"artist\":\"Neon NiteClub\"}'),
(5, 1, 'album', '581062', 1, '2025-06-05 14:26:14', '{\"name\":\"Alive\",\"image\":\"https:\\/\\/usercontent.jamendo.com?type=album&id=581062&width=300\",\"artist\":\"Songwriterz\"}'),
(6, 1, 'album', '589030', 1, '2025-06-05 14:26:14', '{\"name\":\"Lose My Rose\",\"image\":\"https:\\/\\/usercontent.jamendo.com?type=album&id=589030&width=300\",\"artist\":\"Ed Napoli\"}'),
(7, 1, 'playlist', 'default-1', 1, '2025-06-05 14:26:14', '{\"name\":\"Favorites\",\"image_url\":\"http:\\/\\/localhost\\/musyk\\/public\\/assets\\/images\\/default-playlist.png\",\"is_default\":true,\"is_pinned\":true}');

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
(30, 1, 'MyPlaylist30', 'http://localhost/musyk/public/assets/images/default-playlist.png', 0, '2025-06-09 07:23:16', '');

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
(1, 'ivanoice', 'ivankovicsibsion@gmail.com', '$2y$10$h5BI8hYj2vRTMpJeGOD3zujc59.Hw9tXFqMEYTUZeKMhuheBuiTVW', '2025-05-20 14:25:16');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT per la tabella `playlists`
--
ALTER TABLE `playlists`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT per la tabella `playlist_songs`
--
ALTER TABLE `playlist_songs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
