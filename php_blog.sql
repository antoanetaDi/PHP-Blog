-- phpMyAdmin SQL Dump
-- version 4.8.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 28, 2018 at 03:51 PM
-- Server version: 10.1.31-MariaDB
-- PHP Version: 7.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `php_blog`
--

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `text` text NOT NULL,
  `datum` datetime NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `text`, `datum`, `post_id`, `user_id`) VALUES
(335, 'Das ist ein interessanter Post. Ich lese gerne den Blog!', '2018-06-01 10:29:59', 19, 28),
(338, 'Ich bin einverstanden, aber man muss darÃ¼ber nachdenken :-))', '2018-06-01 10:36:53', 19, 26),
(339, 'Guter Post! Dankeeee!', '2018-06-01 10:40:51', 19, 27),
(340, 'Warum hat ZDF das Internet nicht verstanden? Ich habe den Post nicht verstanden! :-)', '2018-06-01 10:42:43', 19, 29),
(341, 'Ja, interessante Information!', '2018-06-01 10:43:33', 20, 29),
(342, 'Jaaaa, Big Data fÃ¼r mich auch! :-))))))))', '2018-06-01 10:44:45', 22, 29),
(343, 'Jaa Tim, ich stimme dir zu!', '2018-06-01 10:46:49', 20, 24),
(346, '...und fÃ¼r michhh!', '2018-06-01 11:01:25', 22, 24);

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `imgname` varchar(255) NOT NULL,
  `ueberschrift` varchar(255) NOT NULL,
  `text` text NOT NULL,
  `datum` datetime NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `imgname`, `ueberschrift`, `text`, `datum`, `user_id`) VALUES
(19, 'zdf.jpg', 'Das ZDF hat das Internet nicht verstanden', 'Eigentlich wollte ich mir ja die 4K-HDR-Berglandschaften des ZDF ansehen. Das geht nur leider nicht, weil der Sender Fernsehen mit Internet-Streaming verwechselt. Und die Verantwortlichen erwecken auch den Anschein, als verstÃ¼nden sie die Technik noch nicht so richtig. Als das ZDF vor einigen Wochen angekÃ¼ndigt hat, nach dem Testlauf vor einem Jahr nun auch Serien des Regelbetriebs in 4K-UHD samt HDR auszustrahlen, hab ich mir gedacht: Super! Nach unserem UHD-Test vor einem Jahr wollte ich sehen, ob nun auch endlich das normale Fernsehen eine optische QualitÃ¤t liefern kann, die mit den Angeboten von Netflix, Amazon oder der UHD-Blu-ray mithalten kann.', '2018-06-01 02:07:54', 27),
(20, 'adobe-magento.jpg', 'Adobe will Magento fÃ¼r 1,68 Milliarden Dollar kaufen dsfsdf', 'Magento gehÃ¶rte einst zu Ebay, jetzt wurde der E-Commerce-Shopsystemanbieter erneut verkauft. Adobe und Magento sind bereits seit Jahren Partner. Adobe Systems will den Online-Shopsystem-Anbieter Magento Commerce fÃ¼r 1,68 Milliarden US-Dollar kaufen. Das gab der Softwarehersteller Adobe Systems am 21. Mai 2018 bekannt. Die Ãœbernahme bietet eine Umsatzchance von etwa 13 Milliarden US-Dollar, sagte Brad Rencher, Executive Vice President Digital Experience bei Adobe, am Montag in einer Telefonkonferenz unter Berufung auf Marktforschungsunternehmen.', '2018-06-01 02:09:02', 28),
(21, 'fachkraefte-technische-berufe.jpeg', '315.000 unbesetzte Stellen im technischen Bereich', 'In Deutschland fehlen so viele Fachleute aus naturwissenschaftlichen und technischen Berufen wie noch nie seit Beginn der Erhebung 2011. Auf etwa 315.000 unbesetzte Stellen beziffert das Institut der deutschen Wirtschaft (IW) den derzeitigen Mangel. Im April war die Zahl laut Studie um ein Drittel hÃ¶her als im April des Vorjahres und doppelt so hoch wie Anfang 2015. \"Vor allem IT-FachkrÃ¤fte werden fÃ¼r die Gestaltung des digitalen Wandels in den Unternehmen hÃ¤nderingend gesucht\", heiÃŸt es in der Studie.', '2018-06-01 02:11:59', 29),
(22, 'digitaler-kapitalismus.jpeg', 'Big Data fÃ¼r alle', '\"Collect Moments, Not Things\", steht auf den Jutebeuteln, MacBook-Stickern und Smartphone-HÃ¼llen der Millennials in aller Welt. Besitzdenken und Konsumwahn sind out; Vernetztsein und Sharing lautet die Antwort des 21. Jahrhundert auf die Karriere- und Statussymbolversessenheit voriger Generationen. Aber wie revolutionÃ¤r ist das \"Sammeln von Momenten\" eigentlich? Und trÃ¤gt die darin ausgedrÃ¼ckte Haltung automatisch zum â€“ in letzter Zeit so oft prophezeiten â€“ Ende des Kapitalismus bei?', '2018-06-01 02:19:29', 26);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `vorname` varchar(255) NOT NULL,
  `nachname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `passwort` varchar(255) NOT NULL,
  `avatar` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `vorname`, `nachname`, `email`, `passwort`, `avatar`) VALUES
(24, 'Mark', 'MÃ¼ller', 'mark@home.com', '$2y$10$oeUMPmKkCO9A4N5haN/TuOp0cnB0XI49rAHeIaQQugOuHS1GjaDgq', 'filmmaker-2838945_640.jpg'),
(25, 'Karla', 'Mohr', 'karla@home.de', '$2y$10$kk/flAuR8N9S9kggjbvOcOoKygqn0NRKICxefqTHdXkRQR25WU6Da', 'Koala.jpg'),
(26, 'Kristine', 'Mark', 'kristine@home.de', '$2y$10$yNswrtaBZ10R5ghS2v3aaOorKnh6UOPGoJbYGPpQZGQJz.336zejW', 'defaultavatar.png'),
(27, 'Abel', 'Schmidt', 'abel@home.de', '$2y$10$.GbN6aQQDFHPa4446UvzHesDL5O..2nPIhct1IZEA2IrKxVP4r21G', 'beautiful-2405131_640.jpg'),
(28, 'Lara', 'Neumann', 'lara@home.de', '$2y$10$oFwr7uxINf4sIr5fQZcBG.izI5auVSWM/ZhcwUU5yRi7ROcIq7JSC', 'religion-3426159_640.jpg'),
(29, 'Tim', 'Werner', 'tim@home.de', '$2y$10$Jd4p20ZKQy/ggLJGa.0l9uPYMd/2GNIBEAfkkIJcnKWXWaHN4R5Mq', 'mammal-3123179_640.jpg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
