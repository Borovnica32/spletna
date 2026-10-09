-- phpMyAdmin SQL Dump
-- version 5.2.1deb1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Mar 01, 2024 at 06:54 AM
-- Server version: 10.11.6-MariaDB-0+deb12u1
-- PHP Version: 8.2.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `zverina`
--

-- --------------------------------------------------------

--
-- Table structure for table `Answers`
--

CREATE TABLE `Answers` (
  `ID` int(11) NOT NULL,
  `ANSWER_TEXT` varchar(255) NOT NULL,
  `UP_ID` int(11) NOT NULL,
  `ANSWER_TO` int(11) NOT NULL,
  `DATUM_KOMENTARJA` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Answers`
--

INSERT INTO `Answers` (`ID`, `ANSWER_TEXT`, `UP_ID`, `ANSWER_TO`, `DATUM_KOMENTARJA`) VALUES
(1, 'Test komentar', 1, 1, '2024-02-29 07:21:14');

-- --------------------------------------------------------

--
-- Table structure for table `Blinker_IP`
--

CREATE TABLE `Blinker_IP` (
  `ID` int(11) NOT NULL,
  `UPORABNIK` varchar(30) DEFAULT NULL,
  `MAIL` varchar(30) NOT NULL,
  `GESLO` varchar(60) NOT NULL,
  `DATUM` timestamp NULL DEFAULT current_timestamp(),
  `ID_RANG` int(11) NOT NULL,
  `GROUP_ID` int(11) DEFAULT NULL,
  `VALIDATOR` varchar(100) DEFAULT NULL,
  `SELEKTOR` varchar(100) DEFAULT NULL,
  `POTECE` datetime DEFAULT NULL,
  `ZADNJAPRIJAVA` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Blinker_IP`
--

INSERT INTO `Blinker_IP` (`ID`, `UPORABNIK`, `MAIL`, `GESLO`, `DATUM`, `ID_RANG`, `GROUP_ID`, `VALIDATOR`, `SELEKTOR`, `POTECE`, `ZADNJAPRIJAVA`) VALUES
(1, 'RedWorld24', 'marko.zver2@gmail.com', '$2y$10$SZTi8o13A8dFfaCz/.vik.j9UW9nNGLNaaYIX/rx8c3zbfK5qznUu', '2022-05-15 10:27:40', 1, 3, '$2y$10$zcglcBdE8ltU7cNW6fIu4uSUiEG/cj79EiqOa65oU5dnpcQfbbJpC', 'LertoHipYEdPPjlckQ32HQkCIFkR8887iu3QbF6h', '2024-03-01 07:49:55', '2024-03-01 06:49:55'),
(5, 'testuser', 'testuser@gmail.com', '$2y$10$/YUwl50aY3INhInm0XpO4e7LyMIZ3lYMJ73ecgzCUsX3HuvZxKCS.', '2024-01-09 16:40:41', 2, 3, '$2y$10$eW0VY.on2u6IfcImlkuqteXIckfe21t4L/31TtT12OLwI38njy9z.', 'ucB4RX4n2qpApLgbu7BzC9jLjgMYS9fbC5YIYcSS', '2024-02-29 13:06:31', '2024-02-29 12:06:31'),
(6, 'borovnica', 'borovnica@gmail.com', '$2y$10$KgfVJ8ZWX32PjR5RwvsJR.it6Pgk4wP/b/KRcmfCsVp6ouZn.NToW', '2024-01-16 12:54:05', 3, 3, '$2y$10$4ZKwke4CBAVUcJiDj9l0LOcIzwd9o21dWTrOZbiyN2oB8YXEur9LK', 'DRdrXNu0rrz8IvYPCSeR5qWDAVAT3nfDCFXuBW8D', '2024-02-26 12:52:47', '2024-02-26 11:52:47'),
(7, 'Marko Zver', 'zverm@protonmail.com', '$2y$10$T0yIAL2T.VNqYNBJAhjyButWWpvz/3.fhEp/kxrqUHxEKfeEFSIe.', '2024-01-20 21:50:39', 4, 2, '$2y$10$hXl..75E7XtZevHLBR2XU.SnGinDq1efW/9hsYpI5lJDNS.xxsm/y', 'JDy7ZW9Ou1pimlhk3Op41WN3W2VqmeLRUGqGvC7o', '2024-01-20 22:51:20', '2024-01-20 21:51:20');

-- --------------------------------------------------------

--
-- Table structure for table `GroupAccess`
--

CREATE TABLE `GroupAccess` (
  `ID` int(11) NOT NULL,
  `FILE_ID` int(11) NOT NULL,
  `GROUP_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `GroupAccess`
--

INSERT INTO `GroupAccess` (`ID`, `FILE_ID`, `GROUP_ID`) VALUES
(2, 52, 3),
(5, 50, 3),
(9, 53, 3),
(12, 54, 3),
(14, 51, 3),
(20, 50, 2),
(21, 50, 4);

-- --------------------------------------------------------

--
-- Table structure for table `Groups`
--

CREATE TABLE `Groups` (
  `ID` int(11) NOT NULL,
  `GROUP_IME` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Groups`
--

INSERT INTO `Groups` (`ID`, `GROUP_IME`) VALUES
(2, 'Marketing'),
(3, 'IT'),
(4, 'Prodaja');

-- --------------------------------------------------------

--
-- Table structure for table `Kategorije`
--

CREATE TABLE `Kategorije` (
  `ID` int(11) NOT NULL,
  `KATEGORIJA` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Kategorije`
--

INSERT INTO `Kategorije` (`ID`, `KATEGORIJA`) VALUES
(101, 'Music'),
(102, 'Audio Books'),
(103, 'MP3'),
(104, 'Other Audio'),
(201, 'Xvid'),
(202, 'DVDRip'),
(203, 'WEBRip'),
(204, 'BlueRay 720p'),
(205, 'BlueRay 1080p'),
(206, '4K Movie'),
(207, 'Other Video'),
(301, 'PC Games'),
(302, 'Xbox Games'),
(303, 'PS Games'),
(304, 'Wii Games'),
(305, 'IOS iPhone/iPad Games'),
(306, 'Mac Games'),
(307, 'Android Games'),
(308, 'Linux Games'),
(401, 'Windows Software'),
(402, 'Mac Software'),
(403, 'Linux Software'),
(404, 'Other Software'),
(501, 'Windows Apps'),
(502, 'Mac Apps'),
(503, 'UNIX Apps'),
(504, 'Linux Apps'),
(505, 'IOS iPhone/iPad'),
(506, 'Android Apps'),
(507, 'Other Apps'),
(601, 'Word files'),
(602, 'Excel files'),
(603, 'PowerPoint files'),
(604, 'PDF files'),
(605, 'Zip files');

-- --------------------------------------------------------

--
-- Table structure for table `Nivoji`
--

CREATE TABLE `Nivoji` (
  `ID` int(11) NOT NULL,
  `ID_RANG` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Nivoji`
--

INSERT INTO `Nivoji` (`ID`, `ID_RANG`) VALUES
(1, 'Admin'),
(2, 'Moderator'),
(3, 'Uporabnik'),
(4, 'Blokirano');

-- --------------------------------------------------------

--
-- Table structure for table `Pravice`
--

CREATE TABLE `Pravice` (
  `ID` int(11) NOT NULL,
  `ID_DATOTEKE` int(11) DEFAULT NULL,
  `ID_UP` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Pravice`
--

INSERT INTO `Pravice` (`ID`, `ID_DATOTEKE`, `ID_UP`) VALUES
(7, 50, 1),
(9, 52, 1),
(17, 51, 1),
(37, 60, 1),
(42, 53, 1),
(43, 61, 1),
(48, 54, 1),
(51, 62, 1);

-- --------------------------------------------------------

--
-- Table structure for table `Threads`
--

CREATE TABLE `Threads` (
  `ID` int(11) NOT NULL,
  `THREAD_NASLOV` varchar(255) NOT NULL,
  `THREAD_VSEBINA` varchar(255) NOT NULL,
  `UP_ID` int(11) NOT NULL,
  `DATUM_OBJAVE` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Threads`
--

INSERT INTO `Threads` (`ID`, `THREAD_NASLOV`, `THREAD_VSEBINA`, `UP_ID`, `DATUM_OBJAVE`) VALUES
(1, 'Test vprasanje 1', 'vsebineThreads/thread_1.txt', 1, '2024-02-29 07:21:02');

-- --------------------------------------------------------

--
-- Table structure for table `Tiketi`
--

CREATE TABLE `Tiketi` (
  `ID` int(11) NOT NULL,
  `TIKET_IME` varchar(50) NOT NULL,
  `TIKET_VSEBINA` varchar(200) NOT NULL,
  `ID_UP` int(11) NOT NULL,
  `STAT` int(11) NOT NULL DEFAULT 1,
  `HANDLER` int(11) DEFAULT NULL,
  `DODANO` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Tiketi`
--

INSERT INTO `Tiketi` (`ID`, `TIKET_IME`, `TIKET_VSEBINA`, `ID_UP`, `STAT`, `HANDLER`, `DODANO`) VALUES
(2, 'Test tiket 1', 'vsebineTiketov/ticket_1.txt', 1, 2, 1, '2024-02-29');

-- --------------------------------------------------------

--
-- Table structure for table `TiketStatusi`
--

CREATE TABLE `TiketStatusi` (
  `ID` int(11) NOT NULL,
  `ID_STATUSA` int(11) NOT NULL,
  `STATUS` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `TiketStatusi`
--

INSERT INTO `TiketStatusi` (`ID`, `ID_STATUSA`, `STATUS`) VALUES
(1, 1, 'Open'),
(2, 2, 'Closed'),
(3, 3, 'Waiting');

-- --------------------------------------------------------

--
-- Table structure for table `Torrenti`
--

CREATE TABLE `Torrenti` (
  `ID` int(11) NOT NULL,
  `NASLOV` varchar(50) DEFAULT NULL,
  `ID_UP` int(11) DEFAULT NULL,
  `DATUM` date DEFAULT current_timestamp(),
  `DATOTEKA` char(255) DEFAULT NULL,
  `STAT` int(12) NOT NULL DEFAULT 1,
  `KATEGORIJA` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Torrenti`
--

INSERT INTO `Torrenti` (`ID`, `NASLOV`, `ID_UP`, `DATUM`, `DATOTEKA`, `STAT`, `KATEGORIJA`) VALUES
(50, 'Fialka', 5, '2024-01-09', 'files/Fialka_200.pdf', 0, 604),
(51, 'Tomos Alpino 6S', 1, '2024-01-09', 'files/Tomos-APN-6-S-ALPINO.pdf', 1, 604),
(52, 'Windows 10 reverse shell', 1, '2024-01-09', 'files/OMG_Reverse_shell_updated_v3.pdf', 0, 604),
(53, 'Boeing AH-64A Apache Helicopter Operating Manual', 1, '2024-01-10', 'files/Boeing_AH-64A_Apache_Helicopter_Operating_Manual.pdf', 0, 604),
(54, 'Python Cheat Sheet', 1, '2024-01-12', 'files/beginners_python_cheat_sheet_pcc_all.pdf', 1, 604),
(60, 'Uptime kuma zip', 1, '2024-02-11', 'files/uptime-kuma-master.zip', 1, 605),
(61, 'Dokumentacija za prakso', 1, '2024-02-16', 'files/Dokumantacija_prakticnega_dela.docx', 0, 601),
(62, 'Diski', 1, '2024-02-28', 'files/DISKI_VISO.pdf', 0, 604);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Answers`
--
ALTER TABLE `Answers`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `UP_ID` (`UP_ID`),
  ADD KEY `ANSWER_TO` (`ANSWER_TO`);

--
-- Indexes for table `Blinker_IP`
--
ALTER TABLE `Blinker_IP`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `ID_RANG` (`ID_RANG`),
  ADD KEY `GROUP_ID` (`GROUP_ID`);

--
-- Indexes for table `GroupAccess`
--
ALTER TABLE `GroupAccess`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `FILE_ID` (`FILE_ID`),
  ADD KEY `GROUP_ID` (`GROUP_ID`);

--
-- Indexes for table `Groups`
--
ALTER TABLE `Groups`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `Kategorije`
--
ALTER TABLE `Kategorije`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `Nivoji`
--
ALTER TABLE `Nivoji`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `Pravice`
--
ALTER TABLE `Pravice`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `ID_DATOTEKE` (`ID_DATOTEKE`),
  ADD KEY `ID_UP` (`ID_UP`);

--
-- Indexes for table `Threads`
--
ALTER TABLE `Threads`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `UP_ID` (`UP_ID`);

--
-- Indexes for table `Tiketi`
--
ALTER TABLE `Tiketi`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `ID_UP` (`ID_UP`),
  ADD KEY `DODELJENO_OSEBJE` (`HANDLER`),
  ADD KEY `STAT` (`STAT`);

--
-- Indexes for table `TiketStatusi`
--
ALTER TABLE `TiketStatusi`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `Torrenti`
--
ALTER TABLE `Torrenti`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `ID_UP` (`ID_UP`),
  ADD KEY `KATEGORIJA` (`KATEGORIJA`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Answers`
--
ALTER TABLE `Answers`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `Blinker_IP`
--
ALTER TABLE `Blinker_IP`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `GroupAccess`
--
ALTER TABLE `GroupAccess`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `Groups`
--
ALTER TABLE `Groups`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `Kategorije`
--
ALTER TABLE `Kategorije`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=606;

--
-- AUTO_INCREMENT for table `Nivoji`
--
ALTER TABLE `Nivoji`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `Pravice`
--
ALTER TABLE `Pravice`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `Threads`
--
ALTER TABLE `Threads`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `Tiketi`
--
ALTER TABLE `Tiketi`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `TiketStatusi`
--
ALTER TABLE `TiketStatusi`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `Torrenti`
--
ALTER TABLE `Torrenti`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Answers`
--
ALTER TABLE `Answers`
  ADD CONSTRAINT `Answers_ibfk_1` FOREIGN KEY (`UP_ID`) REFERENCES `Blinker_IP` (`ID`),
  ADD CONSTRAINT `Answers_ibfk_2` FOREIGN KEY (`ANSWER_TO`) REFERENCES `Threads` (`ID`);

--
-- Constraints for table `Blinker_IP`
--
ALTER TABLE `Blinker_IP`
  ADD CONSTRAINT `Blinker_IP_ibfk_1` FOREIGN KEY (`ID_RANG`) REFERENCES `Nivoji` (`ID`),
  ADD CONSTRAINT `Blinker_IP_ibfk_2` FOREIGN KEY (`GROUP_ID`) REFERENCES `Groups` (`ID`);

--
-- Constraints for table `GroupAccess`
--
ALTER TABLE `GroupAccess`
  ADD CONSTRAINT `GroupAccess_ibfk_1` FOREIGN KEY (`FILE_ID`) REFERENCES `Torrenti` (`ID`),
  ADD CONSTRAINT `GroupAccess_ibfk_2` FOREIGN KEY (`GROUP_ID`) REFERENCES `Groups` (`ID`);

--
-- Constraints for table `Pravice`
--
ALTER TABLE `Pravice`
  ADD CONSTRAINT `Pravice_ibfk_1` FOREIGN KEY (`ID_DATOTEKE`) REFERENCES `Torrenti` (`ID`),
  ADD CONSTRAINT `Pravice_ibfk_2` FOREIGN KEY (`ID_UP`) REFERENCES `Blinker_IP` (`ID`);

--
-- Constraints for table `Threads`
--
ALTER TABLE `Threads`
  ADD CONSTRAINT `Threads_ibfk_1` FOREIGN KEY (`UP_ID`) REFERENCES `Blinker_IP` (`ID`);

--
-- Constraints for table `Tiketi`
--
ALTER TABLE `Tiketi`
  ADD CONSTRAINT `Tiketi_ibfk_1` FOREIGN KEY (`ID_UP`) REFERENCES `Blinker_IP` (`ID`),
  ADD CONSTRAINT `Tiketi_ibfk_2` FOREIGN KEY (`HANDLER`) REFERENCES `Blinker_IP` (`ID`),
  ADD CONSTRAINT `Tiketi_ibfk_3` FOREIGN KEY (`STAT`) REFERENCES `TiketStatusi` (`ID`);

--
-- Constraints for table `Torrenti`
--
ALTER TABLE `Torrenti`
  ADD CONSTRAINT `Torrenti_ibfk_1` FOREIGN KEY (`ID_UP`) REFERENCES `Blinker_IP` (`ID`),
  ADD CONSTRAINT `Torrenti_ibfk_2` FOREIGN KEY (`KATEGORIJA`) REFERENCES `Kategorije` (`ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
