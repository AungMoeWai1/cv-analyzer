-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 05, 2024 at 12:47 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `recruitment`
--

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `campus` enum('full time','part time') NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `salary` varchar(255) DEFAULT NULL,
  `posted_date` date NOT NULL,
  `closing_date` date NOT NULL,
  `job_description` text NOT NULL,
  `job_requirement` text NOT NULL,
  `benefits` text NOT NULL,
  `job_type` varchar(255) NOT NULL,
  `userID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`id`, `name`, `campus`, `location`, `salary`, `posted_date`, `closing_date`, `job_description`, `job_requirement`, `benefits`, `job_type`, `userID`) VALUES
(11, 'web developer', 'full time', 'yangon', '700000', '2024-08-20', '2024-08-31', 'We are looking for an IT Web Developer responsible for managing the interchange of data between the server and the users. Your primary focus will be the development of all server-side logic, definition, and maintenance of the central database, and ensuring high performance and responsiveness to requests from the front-end. You will also be responsible for integrating the front-end elements built by your co-workers into the application. A basic understanding of front-end technologies is therefore necessary as well.', '- Strong understanding of PHP, Laravel, and MySQL.\r\n- Proficient understanding of code versioning tools, such as Git.\r\n- Understanding of front-end technologies, such as JavaScript, HTML5, and CSS3.\r\n- Experience with RESTful APIs.\r\n- Knowledge of modern authorization mechanisms, such as JSON Web Token.\r\n- Familiarity with modern frameworks like React.js or Vue.js is a plus.\r\n- Ability to write clean, readable, and efficient code.\r\n- Bachelor\'s degree in Computer Science or a related field.', 'ferry provide', 'developer', 1);

-- --------------------------------------------------------

--
-- Table structure for table `job_applications`
--

CREATE TABLE `job_applications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `skill` text DEFAULT NULL,
  `experience` text DEFAULT NULL,
  `certificate` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`certificate`)),
  `education` text DEFAULT NULL,
  `applied_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `adaptability` tinyint(1) DEFAULT 0,
  `communication` tinyint(1) DEFAULT 0,
  `emotional_intelligence` tinyint(1) DEFAULT 0,
  `leadership` tinyint(1) DEFAULT 0,
  `resilience` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `job_applications`
--

INSERT INTO `job_applications` (`id`, `user_id`, `job_id`, `skill`, `experience`, `certificate`, `education`, `applied_at`, `adaptability`, `communication`, `emotional_intelligence`, `leadership`, `resilience`) VALUES
(14, 15, 11, 'adslfklaasdfas', 'asdfasd', '[{\"name\":\"aaa\",\"path\":\"..\\/assets\\/certificates\\/01b8618a713d6bbfca3ab9bc53288400.jpg\"},{\"name\":\"bbb\",\"path\":\"..\\/assets\\/certificates\\/[wallcoo.com]_dualscreen_Wallpaper_CG_art_1867.jpg\"}]', 'asdf', '2024-09-04 15:50:58', 3, 3, 3, 3, 3);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `role` enum('employee','employer') NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `company` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `DOB` date DEFAULT NULL,
  `NRC` text DEFAULT NULL,
  `gender` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `role`, `phone`, `company`, `location`, `image`, `description`, `password`, `DOB`, `NRC`, `gender`) VALUES
(1, 'aung', 'aung@gmail.com', 'employer', NULL, 'aa', 'aa', NULL, 'adsf', '$2y$10$p.0fho5GlMJB1I5rRQKqi.eMebksEY10OQKR3QsBlvh28yTOy/Y1y', NULL, NULL, NULL),
(3, 'sithu', 'sithu@gmail.com', 'employee', '09250324578', NULL, 'yangon', '../assets/02225_cherryflowers_2560x1600.jpg', NULL, '$2y$10$AqEGpOE1yCsswTibwLd73OzTMNV6wzxeMgaRv7n4C./F4q.WwuBzm', NULL, NULL, NULL),
(5, 'naychi', 'naychi@gmail.com', 'employee', NULL, NULL, NULL, NULL, NULL, '$2y$10$15kqbiYMybQspWmGzX2mle1Nk0lBgplYnvUYHD0r534jkrhPECRT6', NULL, NULL, NULL),
(12, 'moe', 'moe@gmail.com', 'employee', '097645236451', NULL, 'yangon', '../assets/d-cover.jpg', NULL, '$2y$10$WFdGOuMz/9D87qqt/fn7l.oYQSYGoFnGmujJqMn4M/vHpFlCAXcSG', '2002-10-16', '10/LaMaNa(N)123456', 'male'),
(13, 'wai', 'wai@gmail.com', 'employer', NULL, 'xxx', 'yangon', NULL, 'it distribution', '$2y$10$YXL7Eycj/9bRxAIwK3FIueWuIOy061Rl9FQXd58sT61.W4OOwdrqq', NULL, NULL, NULL),
(14, 'hnin', 'hnin@gmail.com', 'employer', NULL, 'xyz', 'Nay Pyi Taw', NULL, 'Telecom company', '$2y$10$lonM1obCvLwz5If7/e5QLeG1Z3Fbmyd2YWBLDEYPlVIhOvMwRNyGK', NULL, NULL, NULL),
(15, 'thin', 'thin@gmail.com', 'employee', '09987654321', NULL, 'Yangon', '../assets/female.png', NULL, '$2y$10$g/IoYQbzK8APighsZ.Jd/edIL.DirNi4vWs.bqqhe9hdo/taE5JBm', '2000-10-04', '10/MaLaMa(N)123456', 'female'),
(16, 'su', 'su@gmail.com', 'employee', '09876567823', NULL, 'yangon', '../assets/male.png', NULL, '$2y$10$5U.aSv/5EDAdHGfwtolnke6OQm9nAMbqy0Xkj.PkqOWsVYk7K.9we', '2000-10-04', '8/GaGaNa(N)123456', 'female'),
(17, 'zay', 'zay@gmail.com', 'employee', '09767453211', NULL, 'yangon', '../assets/01b8618a713d6bbfca3ab9bc53288400.jpg', NULL, '$2y$10$C.hmOX2pDyh7CPbgpr2mJuVC/.LwnxUBqVliCSda0mQ9NUIl1S8Pa', NULL, NULL, NULL),
(18, 'ak', 'ak@gmail.com', 'employee', '0923456789', NULL, NULL, NULL, NULL, '$2y$10$CGku8MCF/NoYbjOwVFXWb.b9WjdXglS4NN.QAxFUU1/02DbcwQpi6', NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `job_applications`
--
ALTER TABLE `job_applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `job_id` (`job_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `job_applications`
--
ALTER TABLE `job_applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `job_applications`
--
ALTER TABLE `job_applications`
  ADD CONSTRAINT `job_applications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `job_applications_ibfk_2` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
