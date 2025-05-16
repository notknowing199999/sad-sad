-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 06, 2025 at 10:41 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tripko_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `fares`
--

CREATE TABLE `fares` (
  `fare_id` int(11) NOT NULL,
  `from_terminal_id` int(11) NOT NULL,
  `to_terminal_id` int(11) NOT NULL,
  `transport_type_id` int(11) NOT NULL,
  `category` varchar(50) NOT NULL,
  `amount` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fares`
--

INSERT INTO `fares` (`fare_id`, `from_terminal_id`, `to_terminal_id`, `transport_type_id`, `category`, `amount`) VALUES
(1, 2, 5, 2, 'Student', 98.00);

-- --------------------------------------------------------

--
-- Table structure for table `festivals`
--

CREATE TABLE `festivals` (
  `festival_id` int(11) NOT NULL,
  `name` varchar(150) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `date` date DEFAULT NULL,
  `town_id` int(11) DEFAULT NULL,
  `image_path` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `festivals`
--

INSERT INTO `festivals` (`festival_id`, `name`, `description`, `date`, `town_id`, `image_path`) VALUES
(4, 'Binungey Festival', 'Celebration of the local delicacy binungey', '2025-05-15', 14, 'binungey-fest.jpg'),
(5, 'Hundred Islands Festival', 'Annual celebration of Alaminos culture', '2025-04-30', 3, 'alaminos.jpg'),
(6, 'Pista\'y Dayat', 'Sea Festival celebrating marine resources', '2025-05-01', 22, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `itineraries`
--

CREATE TABLE `itineraries` (
  `itinerary_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `destination_id` int(11) DEFAULT NULL,
  `environmental_fee` varchar(100) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `itineraries`
--

INSERT INTO `itineraries` (`itinerary_id`, `name`, `description`, `destination_id`, `environmental_fee`, `image_path`, `created_at`) VALUES
(1, 'Talon ka sa falls', 'tatalon ka ngani', 14, '2k ', '68104fd27c4bc_Bolinao Falls.jfif', '2025-04-29 04:04:34');

-- --------------------------------------------------------

--
-- Table structure for table `route_terminals`
--

CREATE TABLE `route_terminals` (
  `terminal_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `town` varchar(100) DEFAULT NULL,
  `coordinates` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `route_terminals`
--

INSERT INTO `route_terminals` (`terminal_id`, `name`, `town`, `coordinates`) VALUES
(1, 'Victory Bolinao', 'Bolinao', '111'),
(2, 'Victory Alaminos', 'Alaminos', '222'),
(3, 'Five Star Dagupan', 'Dagupan', '333'),
(4, 'Five Star Alaminos', 'Alaminos', '444'),
(5, 'Victory Dagupan', 'Dagupan', '555');

-- --------------------------------------------------------

--
-- Table structure for table `route_transport_types`
--

CREATE TABLE `route_transport_types` (
  `id` int(11) NOT NULL,
  `route_id` int(11) NOT NULL,
  `transport_type_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `route_transport_types`
--

INSERT INTO `route_transport_types` (`id`, `route_id`, `transport_type_id`) VALUES
(11, 1, 2),
(12, 1, 4),
(13, 6, 1),
(14, 6, 3),
(15, 7, 1),
(16, 7, 4),
(17, 8, 2),
(18, 8, 4);

-- --------------------------------------------------------

--
-- Table structure for table `tourism_office`
--

CREATE TABLE `tourism_office` (
  `office_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `office_name` varchar(100) DEFAULT NULL,
  `head_name` varchar(100) DEFAULT NULL,
  `town_id` int(11) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `contact_info` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tourism_office`
--

INSERT INTO `tourism_office` (`office_id`, `user_id`, `office_name`, `head_name`, `town_id`, `address`, `contact_info`, `email`) VALUES
(1, 4, 'Alaminos Tourism Office', 'John Doe', 3, 'Alaminos City Hall, Pangasinan', '09123456789', 'alaminos.tourism@example.com'),
(2, 5, 'Bolinao Tourism Office', 'Jane Smith', 14, 'Bolinao Municipal Hall, Pangasinan', '09987654321', 'bolinao.tourism@example.com');

-- --------------------------------------------------------

--
-- Table structure for table `tourism_office_content`
--

CREATE TABLE `tourism_office_content` (
  `content_id` int(11) NOT NULL,
  `office_id` int(11) NOT NULL,
  `content_type` enum('tourist_spot','itinerary','festival') NOT NULL,
  `content_reference_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tourism_office_content`
--

INSERT INTO `tourism_office_content` (`content_id`, `office_id`, `content_type`, `content_reference_id`, `created_at`) VALUES
(1, 1, 'tourist_spot', 5, '2025-05-03 04:05:11'),
(2, 2, 'tourist_spot', 1, '2025-05-03 04:05:11'),
(3, 2, 'tourist_spot', 6, '2025-05-03 04:05:11'),
(4, 1, 'festival', 5, '2025-05-03 04:05:22'),
(5, 2, 'festival', 4, '2025-05-03 04:05:22'),
(7, 2, 'itinerary', 1, '2025-05-03 04:05:28');

-- --------------------------------------------------------

--
-- Table structure for table `tourist_spots`
--

CREATE TABLE `tourist_spots` (
  `spot_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(100) NOT NULL,
  `town_id` int(11) DEFAULT NULL,
  `contact_info` varchar(100) DEFAULT NULL,
  `image_path` text DEFAULT NULL,
  `status` enum('active','inactive','maintenance') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tourist_spots`
--

INSERT INTO `tourist_spots` (`spot_id`, `name`, `description`, `category`, `town_id`, `contact_info`, `image_path`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Bolinao Falls', 'amazing ', 'Waterfalls', 14, '', '68103c630ab13_Bolinao Falls.jfif', 'active', '2025-04-29 02:41:39', '2025-04-29 02:41:39'),
(5, 'Hundred Islands', 'Famous tourist destination featuring 124 islands at low tide', 'Islands', 3, '+63 923 456 7890', '6813945d4ac34_hundred-island.jpg', 'active', '2025-04-29 10:03:51', '2025-05-01 15:33:49'),
(6, 'Bolinao Lighthouse', 'Historic lighthouse with panoramic views', 'Beach', 14, '+63 934 567 8901', '681394705bcd7_bolinao3.jpg', 'active', '2025-04-29 10:03:51', '2025-05-01 15:34:08'),
(11, 'Abagatanen Beach', 'wow siya', 'Beach', 1, '0999634535', '681394af174f6_abagatanen-beach.jpg', 'active', '2025-05-01 15:35:11', '2025-05-01 15:35:11');

-- --------------------------------------------------------

--
-- Table structure for table `towns`
--

CREATE TABLE `towns` (
  `town_id` int(11) NOT NULL,
  `town_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `towns`
--

INSERT INTO `towns` (`town_id`, `town_name`) VALUES
(1, 'Agno'),
(2, 'Aguilar'),
(3, 'Alaminos'),
(4, 'Alcala'),
(5, 'Anda'),
(6, 'Asingan'),
(7, 'Balungao'),
(8, 'Bani'),
(9, 'Basista'),
(10, 'Bautista'),
(11, 'Bayambang'),
(12, 'Binalonan'),
(13, 'Binmaley'),
(14, 'Bolinao'),
(15, 'Bugallon'),
(16, 'Burgos'),
(17, 'Calasiao'),
(18, 'Dasol'),
(19, 'Dagupan'),
(20, 'Infanta'),
(21, 'Labrador'),
(22, 'Lingayen'),
(23, 'Mabini'),
(24, 'Malasiqui'),
(25, 'Manaoag'),
(26, 'Mangaldan'),
(27, 'Mangatarem'),
(28, 'Mapandan'),
(29, 'Natividad'),
(30, 'Pozorrubio'),
(31, 'Rosales'),
(32, 'San Carlos'),
(33, 'San Fabian'),
(34, 'San Jacinto'),
(35, 'San Manuel'),
(36, 'San Nicolas'),
(37, 'San Quintin'),
(38, 'Santa Barbara'),
(39, 'Santa Maria'),
(40, 'Santo Tomas'),
(41, 'Sison'),
(42, 'Sual'),
(43, 'Tayug'),
(44, 'Umingan'),
(45, 'Urbiztondo'),
(46, 'Urdaneta'),
(47, 'Villasis');

-- --------------------------------------------------------

--
-- Table structure for table `transportation_type`
--

CREATE TABLE `transportation_type` (
  `transport_type_id` int(11) NOT NULL,
  `type` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transportation_type`
--

INSERT INTO `transportation_type` (`transport_type_id`, `type`) VALUES
(1, 'Mini Bus'),
(2, 'Air-conditioned Bus'),
(3, 'Ordinary Bus'),
(4, 'Van');

-- --------------------------------------------------------

--
-- Table structure for table `transport_route`
--

CREATE TABLE `transport_route` (
  `route_id` int(11) NOT NULL,
  `origin_terminal_id` int(11) NOT NULL,
  `destination_terminal_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transport_route`
--

INSERT INTO `transport_route` (`route_id`, `origin_terminal_id`, `destination_terminal_id`) VALUES
(1, 5, 2),
(6, 1, 3),
(7, 2, 4),
(8, 3, 5);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_type_id` int(11) DEFAULT NULL,
  `user_status_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `username`, `password`, `user_type_id`, `user_status_id`) VALUES
(1, 'admin', '$2y$10$pJSv4LCr02L0ZDSYVEbpwejTjdJweCC051wL2HxKJQlQIOD9Vxgla', 1, 1),
(3, 'user', '$2y$10$0/3EVjX4omCeS.sclaA8duA3lCYENDxW53yts2W/bubmZaGLGbiVq', 2, 1),
(4, 'alaminos_tourism', '$2y$10$pJSv4LCr02L0ZDSYVEbpwejTjdJweCC051wL2HxKJQlQIOD9Vxgla', 3, 1),
(5, 'bolinao_tourism', '$2y$10$pJSv4LCr02L0ZDSYVEbpwejTjdJweCC051wL2HxKJQlQIOD9Vxgla', 3, 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_profile`
--

CREATE TABLE `user_profile` (
  `profile_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `user_profile_dob` date DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `user_profile_photo` text DEFAULT NULL,
  `user_profile_created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_profile_updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_profile`
--

INSERT INTO `user_profile` (`profile_id`, `user_id`, `first_name`, `last_name`, `user_profile_dob`, `email`, `contact_number`, `user_profile_photo`, `user_profile_created_at`, `user_profile_updated_at`) VALUES
(1, 1, 'System', 'Administrator', NULL, 'admin@tripko.com', '09123456789', NULL, '2025-05-01 13:06:43', '2025-05-01 13:06:43'),
(3, 3, NULL, NULL, NULL, NULL, NULL, NULL, '2025-05-02 02:00:44', '2025-05-02 02:00:44');

-- --------------------------------------------------------

--
-- Table structure for table `user_status`
--

CREATE TABLE `user_status` (
  `user_status_id` int(11) NOT NULL,
  `status_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_status`
--

INSERT INTO `user_status` (`user_status_id`, `status_name`) VALUES
(1, 'Active'),
(2, 'Inactive');

-- --------------------------------------------------------

--
-- Table structure for table `user_type`
--

CREATE TABLE `user_type` (
  `user_type_id` int(11) NOT NULL,
  `type_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_type`
--

INSERT INTO `user_type` (`user_type_id`, `type_name`) VALUES
(1, 'Admin'),
(3, 'officer_account'),
(2, 'Regular');

-- --------------------------------------------------------

--
-- Table structure for table `visitors_tracking`
--

CREATE TABLE `visitors_tracking` (
  `tracking_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `visit_date` date DEFAULT NULL,
  `visitor_count` int(11) DEFAULT NULL,
  `spot_id` int(11) DEFAULT NULL,
  `festival_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `visitors_tracking`
--

INSERT INTO `visitors_tracking` (`tracking_id`, `user_id`, `visit_date`, `visitor_count`, `spot_id`, `festival_id`) VALUES
(365, NULL, '2025-01-29', 64, 5, NULL),
(366, NULL, '2025-01-29', 74, 1, NULL),
(367, NULL, '2025-01-29', 99, 6, NULL),
(369, NULL, '2025-01-30', 50, 5, NULL),
(370, NULL, '2025-01-30', 98, 1, NULL),
(371, NULL, '2025-01-30', 32, 6, NULL),
(373, NULL, '2025-01-31', 99, 5, NULL),
(374, NULL, '2025-01-31', 23, 1, NULL),
(375, NULL, '2025-01-31', 28, 6, NULL),
(377, NULL, '2025-02-01', 110, 5, NULL),
(378, NULL, '2025-02-01', 127, 1, NULL),
(379, NULL, '2025-02-01', 51, 6, NULL),
(381, NULL, '2025-02-02', 190, 5, NULL),
(382, NULL, '2025-02-02', 115, 1, NULL),
(383, NULL, '2025-02-02', 121, 6, NULL),
(385, NULL, '2025-02-03', 91, 5, NULL),
(386, NULL, '2025-02-03', 62, 1, NULL),
(387, NULL, '2025-02-03', 90, 6, NULL),
(389, NULL, '2025-02-04', 37, 5, NULL),
(390, NULL, '2025-02-04', 75, 1, NULL),
(391, NULL, '2025-02-04', 85, 6, NULL),
(393, NULL, '2025-02-05', 62, 5, NULL),
(394, NULL, '2025-02-05', 49, 1, NULL),
(395, NULL, '2025-02-05', 94, 6, NULL),
(397, NULL, '2025-02-06', 75, 5, NULL),
(398, NULL, '2025-02-06', 60, 1, NULL),
(399, NULL, '2025-02-06', 64, 6, NULL),
(401, NULL, '2025-02-07', 48, 5, NULL),
(402, NULL, '2025-02-07', 70, 1, NULL),
(403, NULL, '2025-02-07', 32, 6, NULL),
(405, NULL, '2025-02-08', 123, 5, NULL),
(406, NULL, '2025-02-08', 136, 1, NULL),
(407, NULL, '2025-02-08', 60, 6, NULL),
(409, NULL, '2025-02-09', 141, 5, NULL),
(410, NULL, '2025-02-09', 122, 1, NULL),
(411, NULL, '2025-02-09', 51, 6, NULL),
(413, NULL, '2025-02-10', 47, 5, NULL),
(414, NULL, '2025-02-10', 33, 1, NULL),
(415, NULL, '2025-02-10', 77, 6, NULL),
(417, NULL, '2025-02-11', 95, 5, NULL),
(418, NULL, '2025-02-11', 41, 1, NULL),
(419, NULL, '2025-02-11', 37, 6, NULL),
(421, NULL, '2025-02-12', 92, 5, NULL),
(422, NULL, '2025-02-12', 79, 1, NULL),
(423, NULL, '2025-02-12', 23, 6, NULL),
(425, NULL, '2025-02-13', 95, 5, NULL),
(426, NULL, '2025-02-13', 40, 1, NULL),
(427, NULL, '2025-02-13', 32, 6, NULL),
(429, NULL, '2025-02-14', 62, 5, NULL),
(430, NULL, '2025-02-14', 66, 1, NULL),
(431, NULL, '2025-02-14', 44, 6, NULL),
(433, NULL, '2025-02-15', 83, 5, NULL),
(434, NULL, '2025-02-15', 50, 1, NULL),
(435, NULL, '2025-02-15', 157, 6, NULL),
(437, NULL, '2025-02-16', 84, 5, NULL),
(438, NULL, '2025-02-16', 108, 1, NULL),
(439, NULL, '2025-02-16', 151, 6, NULL),
(441, NULL, '2025-02-17', 30, 5, NULL),
(442, NULL, '2025-02-17', 70, 1, NULL),
(443, NULL, '2025-02-17', 47, 6, NULL),
(445, NULL, '2025-02-18', 48, 5, NULL),
(446, NULL, '2025-02-18', 49, 1, NULL),
(447, NULL, '2025-02-18', 75, 6, NULL),
(449, NULL, '2025-02-19', 21, 5, NULL),
(450, NULL, '2025-02-19', 69, 1, NULL),
(451, NULL, '2025-02-19', 89, 6, NULL),
(453, NULL, '2025-02-20', 49, 5, NULL),
(454, NULL, '2025-02-20', 51, 1, NULL),
(455, NULL, '2025-02-20', 27, 6, NULL),
(457, NULL, '2025-02-21', 55, 5, NULL),
(458, NULL, '2025-02-21', 76, 1, NULL),
(459, NULL, '2025-02-21', 36, 6, NULL),
(461, NULL, '2025-02-22', 150, 5, NULL),
(462, NULL, '2025-02-22', 193, 1, NULL),
(463, NULL, '2025-02-22', 171, 6, NULL),
(465, NULL, '2025-02-23', 185, 5, NULL),
(466, NULL, '2025-02-23', 76, 1, NULL),
(467, NULL, '2025-02-23', 179, 6, NULL),
(469, NULL, '2025-02-24', 59, 5, NULL),
(470, NULL, '2025-02-24', 70, 1, NULL),
(471, NULL, '2025-02-24', 38, 6, NULL),
(473, NULL, '2025-02-25', 40, 5, NULL),
(474, NULL, '2025-02-25', 33, 1, NULL),
(475, NULL, '2025-02-25', 52, 6, NULL),
(477, NULL, '2025-02-26', 24, 5, NULL),
(478, NULL, '2025-02-26', 63, 1, NULL),
(479, NULL, '2025-02-26', 97, 6, NULL),
(481, NULL, '2025-02-27', 70, 5, NULL),
(482, NULL, '2025-02-27', 81, 1, NULL),
(483, NULL, '2025-02-27', 46, 6, NULL),
(485, NULL, '2025-02-28', 52, 5, NULL),
(486, NULL, '2025-02-28', 42, 1, NULL),
(487, NULL, '2025-02-28', 55, 6, NULL),
(489, NULL, '2025-03-01', 176, 5, NULL),
(490, NULL, '2025-03-01', 194, 1, NULL),
(491, NULL, '2025-03-01', 297, 6, NULL),
(493, NULL, '2025-03-02', 86, 5, NULL),
(494, NULL, '2025-03-02', 233, 1, NULL),
(495, NULL, '2025-03-02', 138, 6, NULL),
(497, NULL, '2025-03-03', 32, 5, NULL),
(498, NULL, '2025-03-03', 96, 1, NULL),
(499, NULL, '2025-03-03', 72, 6, NULL),
(501, NULL, '2025-03-04', 71, 5, NULL),
(502, NULL, '2025-03-04', 122, 1, NULL),
(503, NULL, '2025-03-04', 111, 6, NULL),
(505, NULL, '2025-03-05', 117, 5, NULL),
(506, NULL, '2025-03-05', 92, 1, NULL),
(507, NULL, '2025-03-05', 63, 6, NULL),
(509, NULL, '2025-03-06', 39, 5, NULL),
(510, NULL, '2025-03-06', 36, 1, NULL),
(511, NULL, '2025-03-06', 30, 6, NULL),
(513, NULL, '2025-03-07', 74, 5, NULL),
(514, NULL, '2025-03-07', 107, 1, NULL),
(515, NULL, '2025-03-07', 131, 6, NULL),
(517, NULL, '2025-03-08', 180, 5, NULL),
(518, NULL, '2025-03-08', 258, 1, NULL),
(519, NULL, '2025-03-08', 89, 6, NULL),
(521, NULL, '2025-03-09', 143, 5, NULL),
(522, NULL, '2025-03-09', 113, 1, NULL),
(523, NULL, '2025-03-09', 285, 6, NULL),
(525, NULL, '2025-03-10', 74, 5, NULL),
(526, NULL, '2025-03-10', 69, 1, NULL),
(527, NULL, '2025-03-10', 144, 6, NULL),
(529, NULL, '2025-03-11', 144, 5, NULL),
(530, NULL, '2025-03-11', 44, 1, NULL),
(531, NULL, '2025-03-11', 51, 6, NULL),
(533, NULL, '2025-03-12', 132, 5, NULL),
(534, NULL, '2025-03-12', 66, 1, NULL),
(535, NULL, '2025-03-12', 119, 6, NULL),
(537, NULL, '2025-03-13', 143, 5, NULL),
(538, NULL, '2025-03-13', 114, 1, NULL),
(539, NULL, '2025-03-13', 48, 6, NULL),
(541, NULL, '2025-03-14', 41, 5, NULL),
(542, NULL, '2025-03-14', 45, 1, NULL),
(543, NULL, '2025-03-14', 68, 6, NULL),
(545, NULL, '2025-03-15', 185, 5, NULL),
(546, NULL, '2025-03-15', 83, 1, NULL),
(547, NULL, '2025-03-15', 86, 6, NULL),
(549, NULL, '2025-03-16', 222, 5, NULL),
(550, NULL, '2025-03-16', 299, 1, NULL),
(551, NULL, '2025-03-16', 213, 6, NULL),
(553, NULL, '2025-03-17', 74, 5, NULL),
(554, NULL, '2025-03-17', 122, 1, NULL),
(555, NULL, '2025-03-17', 65, 6, NULL),
(557, NULL, '2025-03-18', 95, 5, NULL),
(558, NULL, '2025-03-18', 110, 1, NULL),
(559, NULL, '2025-03-18', 74, 6, NULL),
(561, NULL, '2025-03-19', 63, 5, NULL),
(562, NULL, '2025-03-19', 113, 1, NULL),
(563, NULL, '2025-03-19', 89, 6, NULL),
(565, NULL, '2025-03-20', 138, 5, NULL),
(566, NULL, '2025-03-20', 129, 1, NULL),
(567, NULL, '2025-03-20', 141, 6, NULL),
(569, NULL, '2025-03-21', 140, 5, NULL),
(570, NULL, '2025-03-21', 60, 1, NULL),
(571, NULL, '2025-03-21', 42, 6, NULL),
(573, NULL, '2025-03-22', 237, 5, NULL),
(574, NULL, '2025-03-22', 239, 1, NULL),
(575, NULL, '2025-03-22', 192, 6, NULL),
(577, NULL, '2025-03-23', 95, 5, NULL),
(578, NULL, '2025-03-23', 218, 1, NULL),
(579, NULL, '2025-03-23', 165, 6, NULL),
(581, NULL, '2025-03-24', 47, 5, NULL),
(582, NULL, '2025-03-24', 143, 1, NULL),
(583, NULL, '2025-03-24', 122, 6, NULL),
(585, NULL, '2025-03-25', 45, 5, NULL),
(586, NULL, '2025-03-25', 89, 1, NULL),
(587, NULL, '2025-03-25', 38, 6, NULL),
(589, NULL, '2025-03-26', 98, 5, NULL),
(590, NULL, '2025-03-26', 147, 1, NULL),
(591, NULL, '2025-03-26', 62, 6, NULL),
(593, NULL, '2025-03-27', 83, 5, NULL),
(594, NULL, '2025-03-27', 56, 1, NULL),
(595, NULL, '2025-03-27', 104, 6, NULL),
(597, NULL, '2025-03-28', 120, 5, NULL),
(598, NULL, '2025-03-28', 104, 1, NULL),
(599, NULL, '2025-03-28', 140, 6, NULL),
(601, NULL, '2025-03-29', 84, 5, NULL),
(602, NULL, '2025-03-29', 143, 1, NULL),
(603, NULL, '2025-03-29', 299, 6, NULL),
(605, NULL, '2025-03-30', 165, 5, NULL),
(606, NULL, '2025-03-30', 177, 1, NULL),
(607, NULL, '2025-03-30', 93, 6, NULL),
(609, NULL, '2025-03-31', 80, 5, NULL),
(610, NULL, '2025-03-31', 63, 1, NULL),
(611, NULL, '2025-03-31', 42, 6, NULL),
(613, NULL, '2025-04-01', 122, 5, NULL),
(614, NULL, '2025-04-01', 72, 1, NULL),
(615, NULL, '2025-04-01', 111, 6, NULL),
(617, NULL, '2025-04-02', 57, 5, NULL),
(618, NULL, '2025-04-02', 96, 1, NULL),
(619, NULL, '2025-04-02', 102, 6, NULL),
(621, NULL, '2025-04-03', 147, 5, NULL),
(622, NULL, '2025-04-03', 113, 1, NULL),
(623, NULL, '2025-04-03', 135, 6, NULL),
(625, NULL, '2025-04-04', 116, 5, NULL),
(626, NULL, '2025-04-04', 69, 1, NULL),
(627, NULL, '2025-04-04', 116, 6, NULL),
(629, NULL, '2025-04-05', 242, 5, NULL),
(630, NULL, '2025-04-05', 95, 1, NULL),
(631, NULL, '2025-04-05', 266, 6, NULL),
(633, NULL, '2025-04-06', 122, 5, NULL),
(634, NULL, '2025-04-06', 159, 1, NULL),
(635, NULL, '2025-04-06', 161, 6, NULL),
(637, NULL, '2025-04-07', 111, 5, NULL),
(638, NULL, '2025-04-07', 33, 1, NULL),
(639, NULL, '2025-04-07', 149, 6, NULL),
(641, NULL, '2025-04-08', 99, 5, NULL),
(642, NULL, '2025-04-08', 89, 1, NULL),
(643, NULL, '2025-04-08', 129, 6, NULL),
(645, NULL, '2025-04-09', 71, 5, NULL),
(646, NULL, '2025-04-09', 56, 1, NULL),
(647, NULL, '2025-04-09', 90, 6, NULL),
(649, NULL, '2025-04-10', 36, 5, NULL),
(650, NULL, '2025-04-10', 65, 1, NULL),
(651, NULL, '2025-04-10', 78, 6, NULL),
(653, NULL, '2025-04-11', 131, 5, NULL),
(654, NULL, '2025-04-11', 95, 1, NULL),
(655, NULL, '2025-04-11', 114, 6, NULL),
(657, NULL, '2025-04-12', 282, 5, NULL),
(658, NULL, '2025-04-12', 173, 1, NULL),
(659, NULL, '2025-04-12', 99, 6, NULL),
(661, NULL, '2025-04-13', 92, 5, NULL),
(662, NULL, '2025-04-13', 221, 1, NULL),
(663, NULL, '2025-04-13', 222, 6, NULL),
(665, NULL, '2025-04-14', 132, 5, NULL),
(666, NULL, '2025-04-14', 54, 1, NULL),
(667, NULL, '2025-04-14', 80, 6, NULL),
(669, NULL, '2025-04-15', 150, 5, NULL),
(670, NULL, '2025-04-15', 140, 1, NULL),
(671, NULL, '2025-04-15', 48, 6, NULL),
(673, NULL, '2025-04-16', 68, 5, NULL),
(674, NULL, '2025-04-16', 41, 1, NULL),
(675, NULL, '2025-04-16', 57, 6, NULL),
(677, NULL, '2025-04-17', 110, 5, NULL),
(678, NULL, '2025-04-17', 90, 1, NULL),
(679, NULL, '2025-04-17', 143, 6, NULL),
(681, NULL, '2025-04-18', 72, 5, NULL),
(682, NULL, '2025-04-18', 89, 1, NULL),
(683, NULL, '2025-04-18', 143, 6, NULL),
(685, NULL, '2025-04-19', 153, 5, NULL),
(686, NULL, '2025-04-19', 300, 1, NULL),
(687, NULL, '2025-04-19', 242, 6, NULL),
(689, NULL, '2025-04-20', 80, 5, NULL),
(690, NULL, '2025-04-20', 204, 1, NULL),
(691, NULL, '2025-04-20', 273, 6, NULL),
(693, NULL, '2025-04-21', 120, 5, NULL),
(694, NULL, '2025-04-21', 116, 1, NULL),
(695, NULL, '2025-04-21', 125, 6, NULL),
(697, NULL, '2025-04-22', 80, 5, NULL),
(698, NULL, '2025-04-22', 72, 1, NULL),
(699, NULL, '2025-04-22', 80, 6, NULL),
(701, NULL, '2025-04-23', 47, 5, NULL),
(702, NULL, '2025-04-23', 125, 1, NULL),
(703, NULL, '2025-04-23', 150, 6, NULL),
(705, NULL, '2025-04-24', 122, 5, NULL),
(706, NULL, '2025-04-24', 36, 1, NULL),
(707, NULL, '2025-04-24', 150, 6, NULL),
(709, NULL, '2025-04-25', 149, 5, NULL),
(710, NULL, '2025-04-25', 92, 1, NULL),
(711, NULL, '2025-04-25', 138, 6, NULL),
(713, NULL, '2025-04-26', 263, 5, NULL),
(714, NULL, '2025-04-26', 281, 1, NULL),
(715, NULL, '2025-04-26', 140, 6, NULL),
(717, NULL, '2025-04-27', 224, 5, NULL),
(718, NULL, '2025-04-27', 191, 1, NULL),
(719, NULL, '2025-04-27', 182, 6, NULL),
(721, NULL, '2025-04-28', 87, 5, NULL),
(722, NULL, '2025-04-28', 135, 1, NULL),
(723, NULL, '2025-04-28', 78, 6, NULL),
(725, NULL, '2025-04-29', 48, 5, NULL),
(726, NULL, '2025-04-29', 119, 1, NULL),
(727, NULL, '2025-04-29', 57, 6, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `fares`
--
ALTER TABLE `fares`
  ADD PRIMARY KEY (`fare_id`),
  ADD KEY `from_terminal_id` (`from_terminal_id`),
  ADD KEY `to_terminal_id` (`to_terminal_id`),
  ADD KEY `transport_type_id` (`transport_type_id`);

--
-- Indexes for table `festivals`
--
ALTER TABLE `festivals`
  ADD PRIMARY KEY (`festival_id`),
  ADD KEY `town_id` (`town_id`);

--
-- Indexes for table `itineraries`
--
ALTER TABLE `itineraries`
  ADD PRIMARY KEY (`itinerary_id`),
  ADD KEY `destination_id` (`destination_id`);

--
-- Indexes for table `route_terminals`
--
ALTER TABLE `route_terminals`
  ADD PRIMARY KEY (`terminal_id`);

--
-- Indexes for table `route_transport_types`
--
ALTER TABLE `route_transport_types`
  ADD PRIMARY KEY (`id`),
  ADD KEY `route_id` (`route_id`),
  ADD KEY `transport_type_id` (`transport_type_id`);

--
-- Indexes for table `tourism_office`
--
ALTER TABLE `tourism_office`
  ADD PRIMARY KEY (`office_id`),
  ADD KEY `town_id` (`town_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `tourism_office_content`
--
ALTER TABLE `tourism_office_content`
  ADD PRIMARY KEY (`content_id`),
  ADD KEY `office_id` (`office_id`);

--
-- Indexes for table `tourist_spots`
--
ALTER TABLE `tourist_spots`
  ADD PRIMARY KEY (`spot_id`),
  ADD KEY `town_id` (`town_id`);

--
-- Indexes for table `towns`
--
ALTER TABLE `towns`
  ADD PRIMARY KEY (`town_id`);

--
-- Indexes for table `transportation_type`
--
ALTER TABLE `transportation_type`
  ADD PRIMARY KEY (`transport_type_id`);

--
-- Indexes for table `transport_route`
--
ALTER TABLE `transport_route`
  ADD PRIMARY KEY (`route_id`),
  ADD KEY `origin_terminal_id` (`origin_terminal_id`),
  ADD KEY `destination_terminal_id` (`destination_terminal_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `unique_username` (`username`),
  ADD KEY `user_type_id` (`user_type_id`),
  ADD KEY `user_status_id` (`user_status_id`);

--
-- Indexes for table `user_profile`
--
ALTER TABLE `user_profile`
  ADD PRIMARY KEY (`profile_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `user_status`
--
ALTER TABLE `user_status`
  ADD PRIMARY KEY (`user_status_id`),
  ADD UNIQUE KEY `unique_status_name` (`status_name`);

--
-- Indexes for table `user_type`
--
ALTER TABLE `user_type`
  ADD PRIMARY KEY (`user_type_id`),
  ADD UNIQUE KEY `unique_type_name` (`type_name`);

--
-- Indexes for table `visitors_tracking`
--
ALTER TABLE `visitors_tracking`
  ADD PRIMARY KEY (`tracking_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `spot_id` (`spot_id`),
  ADD KEY `festival_id` (`festival_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `fares`
--
ALTER TABLE `fares`
  MODIFY `fare_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `festivals`
--
ALTER TABLE `festivals`
  MODIFY `festival_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `itineraries`
--
ALTER TABLE `itineraries`
  MODIFY `itinerary_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `route_terminals`
--
ALTER TABLE `route_terminals`
  MODIFY `terminal_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `route_transport_types`
--
ALTER TABLE `route_transport_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `tourism_office`
--
ALTER TABLE `tourism_office`
  MODIFY `office_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tourism_office_content`
--
ALTER TABLE `tourism_office_content`
  MODIFY `content_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tourist_spots`
--
ALTER TABLE `tourist_spots`
  MODIFY `spot_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `towns`
--
ALTER TABLE `towns`
  MODIFY `town_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `transportation_type`
--
ALTER TABLE `transportation_type`
  MODIFY `transport_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `transport_route`
--
ALTER TABLE `transport_route`
  MODIFY `route_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `user_profile`
--
ALTER TABLE `user_profile`
  MODIFY `profile_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user_status`
--
ALTER TABLE `user_status`
  MODIFY `user_status_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user_type`
--
ALTER TABLE `user_type`
  MODIFY `user_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `visitors_tracking`
--
ALTER TABLE `visitors_tracking`
  MODIFY `tracking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=728;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `fares`
--
ALTER TABLE `fares`
  ADD CONSTRAINT `fares_ibfk_1` FOREIGN KEY (`from_terminal_id`) REFERENCES `route_terminals` (`terminal_id`),
  ADD CONSTRAINT `fares_ibfk_2` FOREIGN KEY (`to_terminal_id`) REFERENCES `route_terminals` (`terminal_id`),
  ADD CONSTRAINT `fares_ibfk_3` FOREIGN KEY (`transport_type_id`) REFERENCES `transportation_type` (`transport_type_id`);

--
-- Constraints for table `festivals`
--
ALTER TABLE `festivals`
  ADD CONSTRAINT `festivals_ibfk_1` FOREIGN KEY (`town_id`) REFERENCES `towns` (`town_id`);

--
-- Constraints for table `itineraries`
--
ALTER TABLE `itineraries`
  ADD CONSTRAINT `itineraries_ibfk_1` FOREIGN KEY (`destination_id`) REFERENCES `towns` (`town_id`) ON DELETE SET NULL;

--
-- Constraints for table `route_transport_types`
--
ALTER TABLE `route_transport_types`
  ADD CONSTRAINT `route_transport_types_ibfk_1` FOREIGN KEY (`route_id`) REFERENCES `transport_route` (`route_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `route_transport_types_ibfk_2` FOREIGN KEY (`transport_type_id`) REFERENCES `transportation_type` (`transport_type_id`) ON DELETE CASCADE;

--
-- Constraints for table `tourism_office`
--
ALTER TABLE `tourism_office`
  ADD CONSTRAINT `tourism_office_ibfk_1` FOREIGN KEY (`town_id`) REFERENCES `towns` (`town_id`),
  ADD CONSTRAINT `tourism_office_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `tourism_office_content`
--
ALTER TABLE `tourism_office_content`
  ADD CONSTRAINT `tourism_office_content_ibfk_1` FOREIGN KEY (`office_id`) REFERENCES `tourism_office` (`office_id`) ON DELETE CASCADE;

--
-- Constraints for table `tourist_spots`
--
ALTER TABLE `tourist_spots`
  ADD CONSTRAINT `tourist_spots_ibfk_1` FOREIGN KEY (`town_id`) REFERENCES `towns` (`town_id`);

--
-- Constraints for table `transport_route`
--
ALTER TABLE `transport_route`
  ADD CONSTRAINT `transport_route_ibfk_1` FOREIGN KEY (`origin_terminal_id`) REFERENCES `route_terminals` (`terminal_id`),
  ADD CONSTRAINT `transport_route_ibfk_2` FOREIGN KEY (`destination_terminal_id`) REFERENCES `route_terminals` (`terminal_id`);

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`user_type_id`) REFERENCES `user_type` (`user_type_id`),
  ADD CONSTRAINT `user_ibfk_2` FOREIGN KEY (`user_status_id`) REFERENCES `user_status` (`user_status_id`);

--
-- Constraints for table `user_profile`
--
ALTER TABLE `user_profile`
  ADD CONSTRAINT `user_profile_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `visitors_tracking`
--
ALTER TABLE `visitors_tracking`
  ADD CONSTRAINT `visitors_tracking_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`),
  ADD CONSTRAINT `visitors_tracking_ibfk_2` FOREIGN KEY (`spot_id`) REFERENCES `tourist_spots` (`spot_id`),
  ADD CONSTRAINT `visitors_tracking_ibfk_3` FOREIGN KEY (`festival_id`) REFERENCES `festivals` (`festival_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
