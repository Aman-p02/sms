-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 08, 2026 at 06:11 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sms`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_master`
--

CREATE TABLE `admin_master` (
  `adm_id` int(11) NOT NULL,
  `adm_user` varchar(50) NOT NULL,
  `adm_pass` varchar(50) NOT NULL,
  `adm_email` varchar(50) NOT NULL,
  `adm_contact` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_master`
--

INSERT INTO `admin_master` (`adm_id`, `adm_user`, `adm_pass`, `adm_email`, `adm_contact`) VALUES
(1, '111', '111', 'admin@gmail.com', '1111111111');

-- --------------------------------------------------------

--
-- Table structure for table `campus`
--

CREATE TABLE `campus` (
  `campus_id` int(5) NOT NULL,
  `campus_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `campus`
--

INSERT INTO `campus` (`campus_id`, `campus_name`) VALUES
(1, 'NEMSU Main, Tandag'),
(2, 'NEMSU Cantilan'),
(3, 'NEMSU Lianga'),
(4, 'NEMSU Tagbina'),
(5, 'NEMSU Cagwait'),
(6, 'NEMSU Bislig'),
(7, 'NEMSU San Miguel'),
(8, 'NEMSU Marihatag');

-- --------------------------------------------------------

--
-- Table structure for table `college`
--

CREATE TABLE `college` (
  `college_id` int(2) NOT NULL,
  `college_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `college`
--

INSERT INTO `college` (`college_id`, `college_name`) VALUES
(1, 'CITE - College of Engineering Computer Studies and Technology'),
(2, 'CBM - College of Business and Management'),
(3, 'CET - College of Engineering Technology'),
(4, 'CAS - College of Arts and Sciences'),
(5, 'CTE - College of Teacher Education');

-- --------------------------------------------------------

--
-- Table structure for table `program`
--

CREATE TABLE `program` (
  `prog_id` int(11) NOT NULL,
  `prog_name` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `program`
--

INSERT INTO `program` (`prog_id`, `prog_name`) VALUES
(1, 'BACHELOR OF ARTS MAJOR IN ECONOMICS'),
(2, 'BACHELOR OF ARTS MAJOR IN ENGLISH LANGUAGE'),
(3, 'BACHELOR OF ARTS MAJOR IN FILIPINO'),
(4, 'BACHELOR OF ARTS MAJOR IN POLITICAL SCIENCE'),
(5, 'BACHELOR OF ELEMENTARY EDUCATION MAJOR IN GENERAL CURRICULUM'),
(6, 'BACHELOR OF PUBLIC ADMINISTRATION'),
(7, 'BACHELOR OF SCIENCE IN BIOLOGY MAJOR IN BIOLOGY'),
(8, 'BACHELOR OF SCIENCE IN BUSINESS ADMINISTRATION MAJOR IN FINANCIAL MANAGEMENT'),
(9, 'BACHELOR OF SCIENCE IN BUSINESS ADMINISTRATION MAJOR IN HUMAN RESOURCE DEVELOPMENT AND MANAGEMENT'),
(10, 'BACHELOR OF SCIENCE IN BUSINESS ADMINISTRATION MAJOR IN MARKETING MANAGEMENT'),
(11, 'BACHELOR OF SCIENCE IN CIVIL ENGINEERING'),
(12, 'BACHELOR OF SCIENCE IN COMPUTER SCIENCE MAJOR IN COMPUTER SCIENCE'),
(13, 'BACHELOR OF SCIENCE IN EARLY CHILDHOOD EDUCATION'),
(14, 'BACHELOR OF SCIENCE IN ENVIRONMENTAL SCIENCE MAJOR IN ENVIRONMENTAL SCIENCE'),
(15, 'BACHELOR OF SCIENCE IN HOTEL AND RESTAURANT MANAGEMENT/BACHELOR OF SCIENCE IN HOSPITALITY MANAGEMENT MAJOR IN FOOD AND BEVERAGE SERVICE AND MANAGEMENT'),
(16, 'BACHELOR OF SCIENCE IN MATHEMATICS MAJOR IN MATHEMATICS'),
(17, 'BACHELOR OF SECONDARY EDUCATION MAJOR IN BIOLOGICAL SCIENCE'),
(18, 'BACHELOR OF SECONDARY EDUCATION MAJOR IN ENGLISH'),
(19, 'BACHELOR OF SECONDARY EDUCATION MAJOR IN MATHEMATICS'),
(20, 'BACHELOR OF SECONDARY EDUCATION MAJOR IN SCIENCES'),
(21, 'DIPLOMA IN MIDWIFERY'),
(22, 'BACHELOR OF SCIENCE IN MIDWIFERY');

-- --------------------------------------------------------

--
-- Table structure for table `scholarship`
--

CREATE TABLE `scholarship` (
  `app_id` int(11) NOT NULL,
  `stu_id` int(11) NOT NULL,
  `ss_id` int(11) NOT NULL,
  `app_status` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `scholarship`
--

INSERT INTO `scholarship` (`app_id`, `stu_id`, `ss_id`, `app_status`) VALUES
(5, 2, 12, 'Approved'),
(6, 2, 18, 'Rejected'),
(7, 3, 12, 'Rejected'),
(8, 2, 14, 'Applied'),
(9, 4, 12, 'Approved'),
(10, 4, 18, 'Applied'),
(11, 4, 14, 'Applied'),
(12, 2, 17, 'Applied'),
(13, 116, 19, 'Approved'),
(14, 116, 18, 'Rejected'),
(15, 116, 12, 'Rejected');

-- --------------------------------------------------------

--
-- Table structure for table `ss_master`
--

CREATE TABLE `ss_master` (
  `ss_id` int(5) NOT NULL,
  `ss_name` varchar(100) NOT NULL,
  `ss_type` varchar(100) NOT NULL,
  `ss_desc` varchar(1000) DEFAULT NULL,
  `ss_start` date NOT NULL,
  `ss_end` date NOT NULL,
  `ss_amount` int(10) NOT NULL,
  `ss_year` varchar(20) DEFAULT NULL,
  `ss_status` varchar(10) NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ss_master`
--

INSERT INTO `ss_master` (`ss_id`, `ss_name`, `ss_type`, `ss_desc`, `ss_start`, `ss_end`, `ss_amount`, `ss_year`, `ss_status`) VALUES
(12, 'DOST-SEI Undergraduate Scholarship', 'Full Merit', 'dsfdsfds', '2026-05-01', '2026-05-31', 40000, '2025-26', 'active'),
(13, 'SUC Tulong Dunong Program (TDP)', 'Scholarship', 'fytfhgf', '2026-04-01', '2026-05-03', 30000, '2025-26', 'active'),
(14, 'TES / Tertiary Education Subsidy (UniFAST)', 'Scholarship', 'sdfdsfdsfds', '2026-05-05', '2026-05-19', 42000, '2026-27', 'active'),
(15, 'DOST-SEI Undergraduate Scholarship', 'Scholarship', 'ghjghjhg', '2026-04-01', '2026-04-10', 20000, '2027-28', 'active'),
(16, 'Local Government Scholarships (Province/City/Municipality)', 'Grant', 'nbvnbvn', '2026-05-01', '2026-05-09', 25000, '2025-26', 'active'),
(17, 'SUC Tulong Dunong Program (TDP)', 'Half Merit', 'xvcx', '2025-05-06', '2025-05-31', 60000, '2026-27', 'active'),
(18, 'Tulong Dunong Program (TDP)', 'Full Merit', 'vnbvnbv', '2026-05-01', '2026-05-31', 38000, '2025-26', 'active'),
(19, 'CHED Scholarship Program (CSP)', 'Full Merit', 'abc', '2026-05-01', '2026-05-31', 60000, '2025-26', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `student_master`
--

CREATE TABLE `student_master` (
  `stu_id` int(11) NOT NULL,
  `stu_enroll` varchar(20) NOT NULL,
  `stu_fname` varchar(20) NOT NULL,
  `stu_lname` varchar(20) NOT NULL,
  `stu_ext` varchar(20) DEFAULT NULL,
  `stu_mname` varchar(20) DEFAULT NULL,
  `stu_gender` varchar(10) DEFAULT NULL,
  `stu_pass` varchar(50) DEFAULT NULL,
  `stu_dob` date DEFAULT NULL,
  `stu_email` varchar(50) DEFAULT NULL,
  `stu_contact` varchar(15) DEFAULT NULL,
  `stu_program` varchar(200) DEFAULT NULL,
  `stu_year_level` varchar(10) DEFAULT NULL,
  `stu_campus` varchar(50) DEFAULT NULL,
  `stu_cor` varchar(100) DEFAULT NULL,
  `stu_units` varchar(50) DEFAULT NULL,
  `stu_grade` varchar(20) DEFAULT NULL,
  `stu_gpa` varchar(20) DEFAULT NULL,
  `stu_adm_year` varchar(10) DEFAULT NULL,
  `stu_college` varchar(50) DEFAULT NULL,
  `stu_sem` varchar(10) DEFAULT NULL,
  `father_lname` varchar(50) DEFAULT NULL,
  `father_gname` varchar(50) DEFAULT NULL,
  `father_mname` varchar(50) DEFAULT NULL,
  `mother_lname` varchar(50) DEFAULT NULL,
  `mother_gname` varchar(50) DEFAULT NULL,
  `mother_mname` varchar(50) DEFAULT NULL,
  `stu_dswd` varchar(50) DEFAULT NULL,
  `stu_house` varchar(50) DEFAULT NULL,
  `stu_bci` varchar(50) DEFAULT NULL,
  `stu_amount` varchar(10) DEFAULT NULL,
  `stu_disabled` varchar(5) DEFAULT NULL,
  `stu_disability` varchar(50) DEFAULT NULL,
  `stu_marital` varchar(15) DEFAULT NULL,
  `stu_dependent` varchar(15) DEFAULT NULL,
  `stu_inmate` varchar(15) DEFAULT NULL,
  `stu_rebel` varchar(50) DEFAULT NULL,
  `stu_street` varchar(50) DEFAULT NULL,
  `stu_barangay` varchar(50) DEFAULT NULL,
  `stu_city` varchar(50) DEFAULT NULL,
  `stu_province` varchar(50) DEFAULT NULL,
  `stu_zip` varchar(50) DEFAULT NULL,
  `stu_perc` float DEFAULT NULL,
  `stu_profilepic` varchar(200) DEFAULT NULL,
  `stu_status` varchar(10) NOT NULL DEFAULT 'active',
  `complete` varchar(3) NOT NULL DEFAULT 'No'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_master`
--

INSERT INTO `student_master` (`stu_id`, `stu_enroll`, `stu_fname`, `stu_lname`, `stu_ext`, `stu_mname`, `stu_gender`, `stu_pass`, `stu_dob`, `stu_email`, `stu_contact`, `stu_program`, `stu_year_level`, `stu_campus`, `stu_cor`, `stu_units`, `stu_grade`, `stu_gpa`, `stu_adm_year`, `stu_college`, `stu_sem`, `father_lname`, `father_gname`, `father_mname`, `mother_lname`, `mother_gname`, `mother_mname`, `stu_dswd`, `stu_house`, `stu_bci`, `stu_amount`, `stu_disabled`, `stu_disability`, `stu_marital`, `stu_dependent`, `stu_inmate`, `stu_rebel`, `stu_street`, `stu_barangay`, `stu_city`, `stu_province`, `stu_zip`, `stu_perc`, `stu_profilepic`, `stu_status`, `complete`) VALUES
(2, '111', 'Mahesh', 'Goyani', 'MMG', 'M', 'M', '111', '2026-04-23', 'admin@gmail.com', '111', 'BACHELOR OF SCIENCE IN CIVIL ENGINEERING', '1', 'NEMSU Cantilan', 'uploads/student/111_cor.jpg', '10', 'C', '10', '2024-25', 'CAS - College of Arts and Sciences', '8', 'M', 'M', 'M', 'L', 'L', 'L', '111', '111', '111', '111', 'No', 'uploads/student/111_dc.pdf', 'Married', 'Yes', 'Yes', 'Yes', 'surat', 'surat', 'surat', 'surat', 'surat', 95.69, NULL, 'active', 'Yes'),
(3, '222', 'Mitesh', 'Godhani', 'MBG', 'Babubhai', 'M', '222', '0000-00-00', '222@gmail.com', '', 'IT', '', 'NEMSU', NULL, '', '', '', NULL, 'IT', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Single', 'No', 'No', 'No', NULL, NULL, NULL, NULL, NULL, 0, NULL, 'active', 'No'),
(4, '333', 'Tushar', '', '', '', '', '333', '0000-00-00', '333@gmail.com', '3333333333', 'BACHELOR OF SCIENCE IN COMPUTER SCIENCE MAJOR IN C', '', NULL, NULL, '', '', '', NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Single', 'No', 'No', 'No', NULL, NULL, NULL, NULL, NULL, 0, NULL, 'active', 'No'),
(114, '444', 'Safwan', '', '', '', 'M', '444', '0000-00-00', '444@gmail.com', '4444444444', 'BACHELOR OF ARTS MAJOR IN ECONOMICS', '1', NULL, NULL, '', '', '', NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Single', 'No', 'No', 'No', NULL, NULL, NULL, NULL, NULL, 0, NULL, 'active', 'No'),
(115, '555', 'Sheilla', 'S', 'SAB', 'S', 'F', '555', '2026-05-06', '555@gmail.com', '5555555555', 'BACHELOR OF SCIENCE IN BUSINESS ADMINISTRATION MAJ', '1', 'NEMSU Main, Tandag', NULL, '25', 'A', '9.5', '2024-25', 'CITE - College of Engineering Computer Studies and', '3', 'A', 'A', 'A', 'B', 'B', 'B', '12', '12', '12', '12', 'Yes', NULL, 'Married', 'Yes', 'Yes', 'Yes', 'phi', 'phi', 'phi', 'phi', '', 0, NULL, 'active', 'No'),
(116, '666', 'Peter', 'Alex', 'PTA', 'P', 'M', '666', '2026-05-01', '666@gmail.com', '6666666666', 'BACHELOR OF ARTS MAJOR IN ECONOMICS', '1', 'NEMSU Main, Tandag', 'uploads/student/666_cor.jpg', '666', 'B', '8.6', '2025-26', 'CTE - College of Teacher Education', '2', 'Alex', 'A', 'A', 'Marry', 'A', 'Marry', '666', '666', '666', '666', 'No', 'uploads/student/666_dc.pdf', 'Single', 'Yes', 'Yes', 'Yes', 'A', 'B', 'C', 'D', '3800', 86, NULL, 'active', 'Yes');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_master`
--
ALTER TABLE `admin_master`
  ADD PRIMARY KEY (`adm_id`);

--
-- Indexes for table `campus`
--
ALTER TABLE `campus`
  ADD PRIMARY KEY (`campus_id`);

--
-- Indexes for table `college`
--
ALTER TABLE `college`
  ADD PRIMARY KEY (`college_id`);

--
-- Indexes for table `program`
--
ALTER TABLE `program`
  ADD PRIMARY KEY (`prog_id`);

--
-- Indexes for table `scholarship`
--
ALTER TABLE `scholarship`
  ADD PRIMARY KEY (`app_id`);

--
-- Indexes for table `ss_master`
--
ALTER TABLE `ss_master`
  ADD PRIMARY KEY (`ss_id`);

--
-- Indexes for table `student_master`
--
ALTER TABLE `student_master`
  ADD PRIMARY KEY (`stu_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_master`
--
ALTER TABLE `admin_master`
  MODIFY `adm_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `campus`
--
ALTER TABLE `campus`
  MODIFY `campus_id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `college`
--
ALTER TABLE `college`
  MODIFY `college_id` int(2) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `program`
--
ALTER TABLE `program`
  MODIFY `prog_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `scholarship`
--
ALTER TABLE `scholarship`
  MODIFY `app_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `ss_master`
--
ALTER TABLE `ss_master`
  MODIFY `ss_id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `student_master`
--
ALTER TABLE `student_master`
  MODIFY `stu_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=117;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `scholarship`
--
ALTER TABLE `scholarship`
  ADD CONSTRAINT `fk_scholarship` FOREIGN KEY (`ss_id`) REFERENCES `ss_master` (`ss_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_student` FOREIGN KEY (`stu_id`) REFERENCES `student_master` (`stu_id`) ON DELETE CASCADE;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
