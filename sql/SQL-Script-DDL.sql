SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `dbcvgram`
--
DROP DATABASE IF EXISTS `dbcvgram`;
CREATE DATABASE IF NOT EXISTS `dbcvgram` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `dbcvgram`;

-- --------------------------------------------------------

--
-- Table structure for table `tbclassifier`
--

CREATE TABLE `tbclassifier` (
  `id` int(11) NOT NULL,
  `name` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure for table `tbcompany`
--

CREATE TABLE `tbcompany` (
  `id` int(11) NOT NULL,
  `name` varchar(300) NOT NULL,
  `employer_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tbcv`
--

CREATE TABLE `tbcv` (
  `id` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `region_id` int(11) NOT NULL,
  `district_id` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `classifier_id` int(11) NOT NULL,
  `attachment_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `skills` varchar(1000) NOT NULL,
  `last_edited_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tbcvfiles`
--

CREATE TABLE `tbcvfiles` (
  `id` int(11) NOT NULL,
  `name` varchar(120) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tbcvstatus`
--

CREATE TABLE `tbcvstatus` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `admin_access` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tbcvview`
--

CREATE TABLE `tbcvview` (
  `id` int(11) NOT NULL,
  `cv_id` int(11) NOT NULL,
  `viewed_at` datetime NOT NULL,
  `employer_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tbdistricts`
--

CREATE TABLE `tbdistricts` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `city_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tbfavorites`
--

CREATE TABLE `tbfavorites` (
  `id` int(11) NOT NULL,
  `cv_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tblogin`
--

CREATE TABLE `tblogin` (
  `id` int(11) NOT NULL,
  `login` varchar(50) NOT NULL,
  `password` varchar(250) NOT NULL,
  `user_id` int(11) NOT NULL,
  `session` varchar(100) NOT NULL,
  `expire` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tbmessage`
--

CREATE TABLE `tbmessage` (
  `id` int(11) NOT NULL,
  `text` text NOT NULL,
  `sent_at` datetime NOT NULL,
  `sent_from` int(11) NOT NULL,
  `sent_to` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tbprofile`
--

CREATE TABLE `tbprofile` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `age` int(11) NOT NULL,
  `phone` varchar(16) NOT NULL,
  `email` varchar(50) NOT NULL,
  `user_type` int(11) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure for table `tbprofilestatus`
--

CREATE TABLE `tbprofilestatus` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `admin_access` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tbregions`
--

CREATE TABLE `tbregions` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure for table `tbusertype`
--

CREATE TABLE `tbusertype` (
  `id` int(11) NOT NULL,
  `type_name` varchar(200) NOT NULL,
  `admin_role` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbclassifier`
--
ALTER TABLE `tbclassifier`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbcompany`
--
ALTER TABLE `tbcompany`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbcv`
--
ALTER TABLE `tbcv`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbcvfiles`
--
ALTER TABLE `tbcvfiles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbcvstatus`
--
ALTER TABLE `tbcvstatus`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbcvview`
--
ALTER TABLE `tbcvview`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbdistricts`
--
ALTER TABLE `tbdistricts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbfavorites`
--
ALTER TABLE `tbfavorites`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblogin`
--
ALTER TABLE `tblogin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbmessage`
--
ALTER TABLE `tbmessage`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbprofile`
--
ALTER TABLE `tbprofile`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbprofilestatus`
--
ALTER TABLE `tbprofilestatus`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbregions`
--
ALTER TABLE `tbregions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbusertype`
--
ALTER TABLE `tbusertype`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbclassifier`
--
ALTER TABLE `tbclassifier`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbcompany`
--
ALTER TABLE `tbcompany`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbcv`
--
ALTER TABLE `tbcv`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbcvfiles`
--
ALTER TABLE `tbcvfiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbcvstatus`
--
ALTER TABLE `tbcvstatus`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbcvview`
--
ALTER TABLE `tbcvview`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbdistricts`
--
ALTER TABLE `tbdistricts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbfavorites`
--
ALTER TABLE `tbfavorites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblogin`
--
ALTER TABLE `tblogin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbmessage`
--
ALTER TABLE `tbmessage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbprofile`
--
ALTER TABLE `tbprofile`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbprofilestatus`
--
ALTER TABLE `tbprofilestatus`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbregions`
--
ALTER TABLE `tbregions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbusertype`
--
ALTER TABLE `tbusertype`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;