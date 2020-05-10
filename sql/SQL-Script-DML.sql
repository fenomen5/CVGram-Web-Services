SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `dbcvgram`
--
USE `dbcvgram`;

--
-- Dumping data for table `tbclassifier`
--

INSERT INTO `tbclassifier` (`id`, `name`) VALUES
(1, 'Accounting, Management Accounting, Corporate Finance'),
(2, 'Administrative Personnel'),
(3, 'Art, Entertainment, Media'),
(4, 'Automotive Business'),
(5, 'Banks, Investments, Finance'),
(6, 'Career Starters, Students'),
(7, 'Construction, Real Estate'),
(8, 'Consulting'),
(9, 'Domestic Staff'),
(10, 'Government, NGOs'),
(11, 'Human Resources, Training'),
(12, 'IT, Internet, Telecom'),
(13, 'Installation and Service'),
(14, 'Insurance'),
(15, 'Lawyers'),
(16, 'Maintenance and Operations Personnel'),
(17, 'Management'),
(18, 'Manufacturing'),
(19, 'Medicine, Pharmaceuticals'),
(20, 'Marketing, Advertising, PR'),
(21, 'Procurement'),
(22, 'Raw Materials'),
(23, 'Sales'),
(24, 'Science, Education'),
(25, 'Security'),
(26, 'Sports Clubs, Fitness Clubs, Beauty Salons'),
(27, 'Tourism, Hotels, Restaurants'),
(28, 'Transport, Logistics');

--
-- Dumping data for table `tbcvstatus`
--

INSERT INTO `tbcvstatus` (`id`, `name`, `admin_access`) VALUES
(1, 'new', 0),
(2, 'published', 0),
(3, 'draft', 0),
(4, 'blocked', 1),
(5, 'closed', 0);

--
-- Dumping data for table `tbdistricts`
--

INSERT INTO `tbdistricts` (`id`, `name`, `city_id`) VALUES
(1, 'Auckland City', 1),
(2, 'Franklin', 1),
(3, 'Hauraki Gulf Islands', 1),
(4, 'Manukau City', 1),
(5, 'North Shore City', 1),
(6, 'Papakura', 1),
(7, 'Rodney', 1),
(8, 'Waiheke Island', 1),
(9, 'Waitakere City', 1),
(10, 'Carterton', 2),
(11, 'Kapiti Coast', 2),
(12, 'Lower Hutt', 2),
(13, 'Masterton', 2),
(14, 'Porirua', 2),
(15, 'South Wairarapa', 2),
(16, 'Upper Hutt', 2),
(17, 'Wellington', 2),
(18, 'Ashburton', 3),
(19, 'Banks Peninsula', 3),
(20, 'Christchurch City', 3),
(21, 'Hurunui', 3),
(22, 'Mackenzie', 3),
(23, 'Selwyn', 3),
(24, 'Timaru', 3),
(25, 'Waimakariri', 3),
(26, 'Waimate', 3);

--
-- Dumping data for table `tbprofilestatus`
--

INSERT INTO `tbprofilestatus` (`id`, `name`, `admin_access`) VALUES
(1, 'active', 0),
(2, 'blocked', 1);

--
-- Dumping data for table `tbregions`
--

INSERT INTO `tbregions` (`id`, `name`) VALUES
(1, 'Auckland'),
(2, 'Wellington'),
(3, 'Canterbury');

--
-- Dumping data for table `tbusertype`
--

INSERT INTO `tbusertype` (`id`, `type_name`, `admin_role`) VALUES
(1, 'Job seeker', 0),
(2, 'Employer', 0),
(3, 'Administrator', 1);
COMMIT;
