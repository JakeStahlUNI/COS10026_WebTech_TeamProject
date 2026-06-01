-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 01, 2026 at 01:40 PM
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
-- Database: `groupproject`
--

-- --------------------------------------------------------

--
-- Table structure for table `contributions`
--

CREATE TABLE `contributions` (
  `contribution_list_id` int(11) NOT NULL,
  `member_name` varchar(100) NOT NULL,
  `contribution_project1` text NOT NULL,
  `contribution_project2` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contributions`
--

INSERT INTO `contributions` (`contribution_list_id`, `member_name`, `contribution_project1`, `contribution_project2`) VALUES
(1, 'Jake Stahl', 'Developed the index page layout and styling.', 'Built manage.php: list all EOIs, HR personnel can log in to view, filter, delete applications, update statuses, and sort data—all operations are secure.'),
(2, 'Ethan Hoang', 'Created the Jobs page with job listings and styling.', 'Created a job listing page that can retrieve job information from a database and supports searching. '),
(3, 'Lingyu Fu', 'Designed and built the Apply page with form and stying.', 'Added form validation and backend processing. Create an field for all variables of apply page.'),
(4, 'Jinhang Wu', 'Developed the About page and styling.', 'Created the contribution table and connect to about page. Convert the footer and header into an .inc file and connect it to the index, about, apply, and job pages using PHP.');

-- --------------------------------------------------------

--
-- Table structure for table `eoi`
--

CREATE TABLE `eoi` (
  `EOInumber` int(11) NOT NULL,
  `jobref` varchar(5) NOT NULL,
  `firstname` varchar(20) NOT NULL,
  `lastname` varchar(20) NOT NULL,
  `dob` varchar(10) NOT NULL,
  `gender` varchar(20) NOT NULL,
  `streetaddress` varchar(40) NOT NULL,
  `suburb` varchar(40) NOT NULL,
  `state` varchar(3) NOT NULL,
  `postcode` varchar(4) NOT NULL,
  `email` varchar(50) NOT NULL,
  `phone` varchar(12) NOT NULL,
  `skills` text DEFAULT NULL,
  `otherskills` varchar(300) DEFAULT NULL,
  `declaration` varchar(10) NOT NULL,
  `status` enum('New','Current','Final') NOT NULL DEFAULT 'New'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `eoi`
--

INSERT INTO `eoi` (`EOInumber`, `jobref`, `firstname`, `lastname`, `dob`, `gender`, `streetaddress`, `suburb`, `state`, `postcode`, `email`, `phone`, `skills`, `otherskills`, `declaration`, `status`) VALUES
(1, '10001', 'Jake', 'Stahl', '12/12/1900', 'male', 'idk', 'no clue', 'SA', '1234', 'YouShouldHireMe@gmail.com', '1234567890', '', 'You guys said I only need to pass high school', 'agreed', 'New'),
(2, '20002', 'adam', 'branch', '09/09/2000', 'non-binary', 'some where', 'idk', 'NSW', '4321', 'doiawndo@oaind.com', '0987654321', 'HTML, CSS, Accessibility Design', 'I made a poster once', 'agreed', 'New'),
(3, '20002', 'Ben', 'Ten', '04/07/2018', 'male', 'lmoa', 'lmao', 'WA', '8765', 'benten@hot.com', '765432123456', 'UI/UX Design, Content Development', 'just a kid that wants to have fun', 'agreed', 'New'),
(4, '00001', 'Bob', 'Jane', '02/04/2005', 'male', 'ldanks', 'sadjbas', 'WA', '2134', 'sakhdb@akjbd.com', '123089745104', '', 'T mart', 'agreed', 'New'),
(5, '10001', 'tommy', 'baller', '09/09/2009', 'non-binary', 'wqdad', 'dqwdwq', 'SA', '1312', 'wqdeqde@fbwq.com', '536753645243', 'Accessibility Design, UI/UX Design', 'Make me CEO bro', 'agreed', 'New'),
(6, '21345', 'jake', 'notstahl', '03/03/2004', 'female', '2uoebdweq', 'dqwiodiqw', 'SA', '1241', 'dasda@qkjbed.com', '634563754563', 'UI/UX Design', 'hire me', 'agreed', 'New'),
(7, '65453', 'harry', 'stahl', '02/04/2000', 'male', 'wefewf', 'fwefew', 'QLD', '2132', '2132132@akjwbf.com', '532525315553', 'Accessibility Design', 'Smae', 'agreed', 'New');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `job_id` int(11) NOT NULL,
  `Title` text NOT NULL,
  `Description` text NOT NULL,
  `Salary` int(11) NOT NULL,
  `Responsibilities` text NOT NULL,
  `Qualifications` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`job_id`, `Title`, `Description`, `Salary`, `Responsibilities`, `Qualifications`) VALUES
(1, 'graphic designer ', 'Create visual content to communicate ideas and messages.', 50000, 'Web design, Branding, Digital marketing', 'Bachelor\'s degree in Graphic Design\r\n5 years in industry experience '),
(10001, 'CEO', 'We need a guy to run this company.', 1000000, 'you gotta do everything', 'Highschool diploma'),
(20001, 'Cleaner', 'Clean stuff', 2, 'get a mop and clean the tables.', 'Knowing how a mop works');

-- --------------------------------------------------------

--
-- Table structure for table `manager`
--

CREATE TABLE `manager` (
  `User_ID` int(11) NOT NULL,
  `Username` varchar(20) NOT NULL,
  `Password` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `manager`
--

INSERT INTO `manager` (`User_ID`, `Username`, `Password`) VALUES
(1, 'admin', 'admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `contributions`
--
ALTER TABLE `contributions`
  ADD PRIMARY KEY (`contribution_list_id`);

--
-- Indexes for table `eoi`
--
ALTER TABLE `eoi`
  ADD PRIMARY KEY (`EOInumber`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`job_id`);

--
-- Indexes for table `manager`
--
ALTER TABLE `manager`
  ADD PRIMARY KEY (`User_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `eoi`
--
ALTER TABLE `eoi`
  MODIFY `EOInumber` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `job_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20002;

--
-- AUTO_INCREMENT for table `manager`
--
ALTER TABLE `manager`
  MODIFY `User_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
