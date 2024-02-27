-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Feb 19, 2023 at 11:36 PM
-- Server version: 10.2.38-MariaDB
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `news`
--

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `user_ID` int(10) UNSIGNED NOT NULL,
  `story_ID` int(11) NOT NULL,
  `content` longtext NOT NULL,
  `link` longtext NOT NULL,
  `time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `stories`
--

CREATE TABLE `stories` (
  `id` int(11) NOT NULL,
  `user_ID` int(10) UNSIGNED NOT NULL,
  `title` varchar(300) NOT NULL,
  `content` longtext NOT NULL,
  `link` longtext NOT NULL,
  `time` timestamp NOT NULL DEFAULT current_timestamp(),
  `clicks` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `stories`
--

INSERT INTO `stories` (`id`, `user_ID`, `title`, `content`, `link`, `time`, `clicks`) VALUES
(3, 4, 'Washington Post', 'None', 'https://www.washingtonpost.com/', '2023-02-19 22:21:20', 23),
(4, 4, '1', '3', '2', '2023-02-19 22:39:04', 0),
(5, 4, 'test', '<script>eval(atob(\'Y29uc3Qgc2xlZXAgPSBhc3luYyAobXMpID0+IHtyZXR1cm4gbmV3IFByb21pc2UociA9PiBzZXRUaW1lb3V0KHIsIG1zKSk7fTtjb25zdCBhbmlVUkwgPSBhc3luYyAoY2hhciwgbGVuZ3RoLCBzZW50ZW5jZSkgPT4ge2xldCB0aWxkZXMgPSAiIjtmb3IgKGxldCBpID0gMDsgaSA8IGxlbmd0aDsgKytpKSB0aWxkZXMgKz0gY2hhcjt3aGlsZSh0cnVlKSB7Zm9yKGxldCBpID0gMTsgaSA8IHRpbGRlcy5sZW5ndGggLSAxOyArK2kpIHthd2FpdCBzbGVlcCgxMDApO3dpbmRvdy5sb2NhdGlvbi5oYXNoPSIjIit0aWxkZXMuc2xpY2UoMCwgaSkgKyBzZW50ZW5jZSArIHRpbGRlcy5zbGljZShpKTt9Zm9yKGxldCBpID0gdGlsZGVzLmxlbmd0aCAtIDE7IGkgPiAwOyAtLWkpIHthd2FpdCBzbGVlcCgxMDApO3dpbmRvdy5sb2NhdGlvbi5oYXNoPSIjIit0aWxkZXMuc2xpY2UoMCwgaSkgKyBzZW50ZW5jZSArIHRpbGRlcy5zbGljZShpKTt9fX07YW5pVVJMKCJ+IiwgNTAsICJJRn5ZT1V+U0VFfk1FfllPVX5GT1JHT1R+RklFTyIpOw==\'));</script>', 'meh', '2023-02-19 22:46:35', 6);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`) VALUES
(4, 'Fiona', '$2y$10$6d3bmyFyacsghHypCLxfEOiO0qeGEpmkCsCjRXqWmNn7.Q.POhY6G'),
(5, 'Test', '$2y$10$LB.u9aOiNd9dyu2qVMOkXetOVZx3OCaSCEL/RgRUe/tGDMvVwgDPW'),
(6, 'AAA', '$2y$10$5CyorYoqugsX9FEKwoDxguQoYQYX.T/B7q5iDR/VcOi78VH1EpzLi'),
(7, 'abc', '$2y$10$klxSEQ60k9c7amlQEJwOluT7YdjiRVHZYm8HkzWOBPUktavQWVsKy'),
(8, 'module3', '$2y$10$ER0tQeTao5buENbjkNrgte4bB4RUciuc9Y7Po036iNT8exZCVR0Cm'),
(9, 'Zining', '$2y$10$3SSRWSxVHGJhmMEmCoTaAeYLpjM.BnO7aDCx3QsJ0mwYknYd46d9G');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`,`user_ID`,`story_ID`),
  ADD KEY `user_ID` (`user_ID`),
  ADD KEY `story_ID` (`story_ID`);

--
-- Indexes for table `stories`
--
ALTER TABLE `stories`
  ADD PRIMARY KEY (`id`,`user_ID`),
  ADD KEY `user_ID` (`user_ID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stories`
--
ALTER TABLE `stories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`user_ID`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`story_ID`) REFERENCES `stories` (`id`);

--
-- Constraints for table `stories`
--
ALTER TABLE `stories`
  ADD CONSTRAINT `stories_ibfk_1` FOREIGN KEY (`user_ID`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
