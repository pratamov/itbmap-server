-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: May 27, 2016 at 03:01 AM
-- Server version: 10.1.9-MariaDB
-- PHP Version: 5.6.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `itbmap`
--

-- --------------------------------------------------------

--
-- Table structure for table `map`
--

CREATE TABLE `map` (
  `uuid` varchar(50) NOT NULL,
  `position_x_beacon_1` float NOT NULL,
  `position_x_beacon_2` float NOT NULL,
  `position_x_beacon_3` float NOT NULL,
  `position_y_beacon_1` float NOT NULL,
  `position_y_beacon_2` float NOT NULL,
  `position_y_beacon_3` float NOT NULL,
  `map_raw_image_filename` varchar(200) NOT NULL,
  `map_tile_image_url` varchar(200) NOT NULL,
  `map_name` varchar(50) NOT NULL,
  `region_name` varchar(50) NOT NULL,
  `map_description` text NOT NULL,
  `map_real_width` float NOT NULL,
  `map_real_height` float NOT NULL,
  `map_height` int(11) NOT NULL,
  `map_width` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `map`
--

INSERT INTO `map` (`uuid`, `position_x_beacon_1`, `position_x_beacon_2`, `position_x_beacon_3`, `position_y_beacon_1`, `position_y_beacon_2`, `position_y_beacon_3`, `map_raw_image_filename`, `map_tile_image_url`, `map_name`, `region_name`, `map_description`, `map_real_width`, `map_real_height`, `map_height`, `map_width`) VALUES
('cb10023f-a318-3394-4199-a8730c7c1aec', 1, 3, 1, 1, 1, 3, 'ITB_LABTEK_V_LANTAI_3_TIMUR.jpg', 'http://216.126.192.36/maps/ITB_LABTEK_V_LANTAI_3_TIMUR/', 'ITB_LABTEK_V_LANTAI_3_TIMUR', 'Intitut Teknologi Bandung', '', 48, 48, 1948, 1948),
('tobuy1', 1, 3, 1, 1, 1, 3, 'ITB_LABTEK_V_LANTAI_3_BARAT.jpg', 'http://216.126.192.36/maps/ITB_LABTEK_V_LANTAI_3_BARAT/', 'ITB_LABTEK_V_LANTAI_3_BARAT', 'Intitut Teknologi Bandung', '', 54, 54, 2184, 2184),
('tobuy2', 1, 3, 1, 1, 1, 3, 'ITB_LABTEK_V_LANTAI_4_BARAT.jpg', 'http://216.126.192.36/maps/ITB_LABTEK_V_LANTAI_4_BARAT/', 'ITB_LABTEK_V_LANTAI_4_BARAT', 'Intitut Teknologi Bandung', '', 54, 54, 2188, 2188),
('tobuy3', 1, 3, 1, 1, 1, 3, 'ITB_LABTEK_V_LANTAI_4_TIMUR.jpg', 'http://216.126.192.36/maps/ITB_LABTEK_V_LANTAI_4_TIMUR/', 'ITB_LABTEK_V_LANTAI_4_TIMUR', 'Intitut Teknologi Bandung', '', 48, 48, 1948, 1948);

-- --------------------------------------------------------

--
-- Table structure for table `map_role`
--

CREATE TABLE `map_role` (
  `uuid` varchar(50) NOT NULL,
  `role_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `region`
--

CREATE TABLE `region` (
  `region_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `region`
--

INSERT INTO `region` (`region_name`) VALUES
('Intitut Teknologi Bandung');

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE `role` (
  `role_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_name` varchar(50) NOT NULL,
  `password` varchar(200) NOT NULL,
  `role_name` varchar(50) NOT NULL,
  `realm` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `map`
--
ALTER TABLE `map`
  ADD PRIMARY KEY (`uuid`),
  ADD UNIQUE KEY `map_name` (`map_name`);

--
-- Indexes for table `region`
--
ALTER TABLE `region`
  ADD PRIMARY KEY (`region_name`);

--
-- Indexes for table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`role_name`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_name`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
