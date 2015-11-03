-- phpMyAdmin SQL Dump
-- version 4.2.7.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 26 Okt 2015 pada 07.12
-- Versi Server: 5.5.39
-- PHP Version: 5.4.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `db_coc`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `base`
--

CREATE TABLE IF NOT EXISTS `base` (
`idbase` int(11) NOT NULL,
  `base_name` tinytext NOT NULL,
  `base_image` tinytext NOT NULL,
  `base_town_hall` int(11) NOT NULL,
  `category_idcategory` int(11) NOT NULL,
  `base_created_by` int(11) NOT NULL,
  `base_update_by` int(11) NOT NULL,
  `base_created_date` datetime NOT NULL,
  `base_update_date` datetime NOT NULL,
  `base_view_count` int(11) NOT NULL,
  `base_desc` text NOT NULL,
  `base_status` varchar(100) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data untuk tabel `base`
--

INSERT INTO `base` (`idbase`, `base_name`, `base_image`, `base_town_hall`, `category_idcategory`, `base_created_by`, `base_update_by`, `base_created_date`, `base_update_date`, `base_view_count`, `base_desc`, `base_status`) VALUES
(1, 'Base Anti Naga', 'kitty-base.jpg', 7, 1, 1, 0, '2015-10-20 00:00:00', '0000-00-00 00:00:00', 1, '  Lorem ipsum dolor sit amet, consectetur adipiscing elit.\r\n                                    Lorem ipsum dolor sit amet, consectetur adipiscing elit.', ''),
(2, 'Base Anti Naga', 'kitty-base.jpg', 7, 1, 1, 0, '2015-10-20 00:00:00', '0000-00-00 00:00:00', 1, '  Lorem ipsum dolor sit amet, consectetur adipiscing elit.\r\n                                    Lorem ipsum dolor sit amet, consectetur adipiscing elit.', '');

-- --------------------------------------------------------

--
-- Struktur dari tabel `category`
--

CREATE TABLE IF NOT EXISTS `category` (
`idcategory` int(11) NOT NULL,
  `category_name` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktur dari tabel `comment`
--

CREATE TABLE IF NOT EXISTS `comment` (
`idcomment` int(11) NOT NULL,
  `comment_table_reff` varchar(200) NOT NULL,
  `comment_table_reff_id` int(11) NOT NULL,
  `comment_text` text NOT NULL,
  `comment_created_date` datetime NOT NULL,
  `comment_update_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktur dari tabel `rating`
--

CREATE TABLE IF NOT EXISTS `rating` (
`idrating` int(11) NOT NULL,
  `rating_star_count` int(11) NOT NULL,
  `rating_table_reff` varchar(100) NOT NULL,
  `rating_table_reff_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktur dari tabel `user`
--

CREATE TABLE IF NOT EXISTS `user` (
`iduser` int(11) NOT NULL,
  `user_name` varchar(200) NOT NULL,
  `user_email` varchar(200) NOT NULL,
  `user_password` varchar(200) NOT NULL,
  `user_image` tinytext NOT NULL,
  `user_status` varchar(100) NOT NULL,
  `clan_idclan` int(11) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data untuk tabel `user`
--

INSERT INTO `user` (`iduser`, `user_name`, `user_email`, `user_password`, `user_image`, `user_status`, `clan_idclan`) VALUES
(1, 'ridwan', 'ridwanskaterocks@gmail.com', 'pasundan', 'user.jpg', 'active', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `base`
--
ALTER TABLE `base`
 ADD PRIMARY KEY (`idbase`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
 ADD PRIMARY KEY (`idcategory`);

--
-- Indexes for table `comment`
--
ALTER TABLE `comment`
 ADD PRIMARY KEY (`idcomment`);

--
-- Indexes for table `rating`
--
ALTER TABLE `rating`
 ADD PRIMARY KEY (`idrating`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
 ADD PRIMARY KEY (`iduser`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `base`
--
ALTER TABLE `base`
MODIFY `idbase` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
MODIFY `idcategory` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `comment`
--
ALTER TABLE `comment`
MODIFY `idcomment` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `rating`
--
ALTER TABLE `rating`
MODIFY `idrating` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
MODIFY `iduser` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
