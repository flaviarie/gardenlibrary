-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 04, 2025 at 07:59 AM
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
-- Database: `library_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `book_id` varchar(25) NOT NULL,
  `title` varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `publish_date` date DEFAULT NULL,
  `category` varchar(3) NOT NULL,
  `book_cover` varchar(255) DEFAULT NULL,
  `added_date` date NOT NULL,
  `status` enum('available','borrowed','archived','reserved') DEFAULT 'available',
  `is_deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`book_id`, `title`, `author`, `description`, `publish_date`, `category`, `book_cover`, `added_date`, `status`, `is_deleted`) VALUES
('ABAPR101988-SCI00009', 'A Brief History of Time', 'Stephen Hawking', NULL, '1988-04-01', 'SCI', 'default_book_cover.svg', '2022-02-10', 'reserved', 0),
('ANJUN112025-SCI00026', 'Anthony langgam', 'Unknown Author', NULL, '2025-06-20', 'SCI', 'default_book_cover.svg', '2025-06-11', 'reserved', 1),
('APJAN101980-HIS00016', 'A People\'s History of the United States', 'Howard Zinn', NULL, '1980-01-01', 'HIS', 'default_book_cover.svg', '2022-02-10', 'borrowed', 0),
('BUJAN032015-FIC00025', 'buhay ng Aso', 'Unknown Author', NULL, '2015-01-31', 'FIC', 'default_book_cover.svg', '2025-06-03', 'available', 0),
('CLAUG102008-TEC00018', 'Clean Code', 'Robert C. Martin', NULL, '2008-08-01', 'TEC', 'default_book_cover.svg', '2022-02-10', 'borrowed', 0),
('COSEP101980-SCI00011', 'Cosmos', 'Carl Sagan', NULL, '1980-09-28', 'SCI', 'default_book_cover.svg', '2022-02-10', 'reserved', 0),
('DEOCT101994-TEC00020', 'Design Patterns', 'Gang of Four', NULL, '1994-10-21', 'TEC', 'default_book_cover.svg', '2022-02-10', 'borrowed', 0),
('GOJUL032025-SCI00026', '1 gorilla vs 100 men', 'Gelton B', NULL, '2025-07-03', 'SCI', 'book_1_gorilla_vs_100_men_1751548866.png', '2025-07-03', 'borrowed', 0),
('HAJUN101997-FIC00008', 'Harry Potter and the Philosopher\'s Stone', 'J.K. Rowling', NULL, '1997-06-26', 'FIC', 'default_book_cover.svg', '2022-02-10', 'borrowed', 0),
('INJAN101990-TEC00021', 'Introduction to Algorithms', 'Thomas H. Cormen', NULL, '1990-01-01', 'TEC', 'default_book_cover.svg', '2022-02-10', 'borrowed', 0),
('JUN101949-FIC00002', '1984', 'George Orwell', NULL, '1949-06-08', 'FIC', 'book_1984_1751547081.jpg', '2022-02-10', 'reserved', 0),
('LOSEP101954-FIC00006', 'Lord of the Flies', 'William Golding', NULL, '1954-09-17', 'FIC', 'default_book_cover.svg', '2022-02-10', 'available', 0),
('MEJAN10180-PHI00023', 'Meditations', 'Marcus Aurelius', NULL, '0180-01-01', 'PHI', 'default_book_cover.svg', '2022-02-10', 'available', 0),
('PRJAN101813-FIC00003', 'Pride and Prejudice', 'Jane Austen', NULL, '1813-01-28', 'FIC', 'default_book_cover.svg', '2022-02-10', 'reserved', 0),
('SAJAN102011-HIS00014', 'Sapiens: A Brief History of Humankind', 'Yuval Noah Harari', NULL, '2011-01-01', 'HIS', 'default_book_cover.svg', '2022-02-10', 'available', 0),
('SISEP101962-SCI00013', 'Silent Spring', 'Rachel Carson', NULL, '1962-09-27', 'SCI', 'default_book_cover.svg', '2022-02-10', 'available', 0),
('SQJUL042025-OTH00027', 'Squid Game', 'Gelton Blanca', NULL, '2025-07-04', 'OTH', 'book_Squid_Game_1751601876.jfif', '2025-07-04', 'available', 0),
('THAPR101925-FIC00004', 'The Great Gatsby', 'F. Scott Fitzgerald', NULL, '1925-04-10', 'FIC', 'default_book_cover.svg', '2022-02-10', 'available', 0),
('THJAN101962-HIS00015', 'The Guns of August', 'Barbara Tuchman', NULL, '1962-01-01', 'HIS', 'default_book_cover.svg', '2022-02-10', 'available', 0),
('THJAN101976-SCI00012', 'The Selfish Gene', 'Richard Dawkins', NULL, '1976-01-01', 'SCI', 'default_book_cover.svg', '2022-02-10', 'available', 0),
('THJAN10380-PHI00022', 'The Republic', 'Plato', NULL, '0380-01-01', 'PHI', 'default_book_cover.svg', '2022-02-10', 'available', 0),
('THJAN10500-PHI00024', 'The Art of War', 'Sun Tzu', NULL, '0500-01-01', 'PHI', 'default_book_cover.svg', '2022-02-10', 'borrowed', 0),
('THJUL101951-FIC00005', 'The Catcher in the Rye', 'J.D. Salinger', NULL, '1951-07-16', 'FIC', 'default_book_cover.svg', '2022-02-10', 'borrowed', 0),
('THJUN101947-HIS00017', 'The Diary of a Young Girl', 'Anne Frank', NULL, '1947-06-25', 'HIS', 'default_book_cover.svg', '2022-02-10', 'available', 0),
('THNOV101859-SCI00010', 'The Origin of Species', 'Charles Darwin', NULL, '1859-11-24', 'SCI', 'default_book_cover.svg', '2022-02-10', 'available', 0),
('THOCT101999-TEC00019', 'The Pragmatic Programmer', 'David Thomas', NULL, '1999-10-20', 'TEC', 'default_book_cover.svg', '2022-02-10', 'borrowed', 0),
('THSEP101937-FIC00007', 'The Hobbit', 'J.R.R. Tolkien', NULL, '1937-09-21', 'FIC', 'default_book_cover.svg', '2022-02-10', 'available', 0),
('TOJUL101960-FIC00001', 'To Kill a Mockingbird', 'Harper Lee', NULL, '1960-07-11', 'FIC', 'default_book_cover.svg', '2022-02-10', 'available', 0);

--
-- Triggers `books`
--
DELIMITER $$
CREATE TRIGGER `generate_book_id` BEFORE INSERT ON `books` FOR EACH ROW BEGIN
    DECLARE title_prefix VARCHAR(2);
    DECLARE publish_month VARCHAR(3);
    DECLARE added_day VARCHAR(2);
    DECLARE publish_year VARCHAR(4);
    DECLARE book_count INT;
    DECLARE formatted_count VARCHAR(5);
    
    -- 1. Get first 2 letters from title (letters only, uppercase)
    SET title_prefix = UPPER(LEFT(REGEXP_REPLACE(NEW.title, '[^A-Za-z]', ''), 2));
    
    -- 2. Get month from publish date (3 letters, uppercase)
    SET publish_month = UPPER(DATE_FORMAT(NEW.publish_date, '%b'));
    
    -- 3. Get day when added to system 
    SET added_day = LPAD(DAY(NEW.added_date), 2, '0');
    
    -- 4. Get year from publish date
    SET publish_year = YEAR(NEW.publish_date);
    
    -- 5. Get count of existing books + 1
    SELECT COUNT(*) + 1 INTO book_count FROM books WHERE is_deleted = FALSE;
    
    -- 6. Format count as 5-digit number
    SET formatted_count = LPAD(book_count, 5, '0');
    
    -- 7. Generate the Book ID: THFEB102022-FIC00001
    SET NEW.book_id = CONCAT(title_prefix, publish_month, added_day, publish_year, '-', UPPER(NEW.category), formatted_count);
    
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `borrowings`
--

CREATE TABLE `borrowings` (
  `borrowing_id` int(11) NOT NULL,
  `book_id` varchar(25) NOT NULL,
  `user_id` int(11) NOT NULL,
  `borrow_date` date NOT NULL,
  `due_date` date NOT NULL,
  `return_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `borrowings`
--

INSERT INTO `borrowings` (`borrowing_id`, `book_id`, `user_id`, `borrow_date`, `due_date`, `return_date`) VALUES
(1, 'JUN101949-FIC00002', 3, '2024-12-01', '2024-12-15', NULL),
(2, 'THJUL101951-FIC00005', 4, '2024-12-05', '2024-12-19', NULL),
(3, 'HAJUN101997-FIC00008', 5, '2024-12-10', '2024-12-24', NULL),
(4, 'COSEP101980-SCI00011', 6, '2024-12-08', '2024-12-22', NULL),
(5, 'APJAN101980-HIS00016', 7, '2024-12-12', '2024-12-26', NULL),
(6, 'THOCT101999-TEC00019', 8, '2024-12-03', '2024-12-17', NULL),
(7, 'THJAN10500-PHI00024', 9, '2024-12-07', '2024-12-21', NULL),
(8, 'TOJUL101960-FIC00001', 3, '2024-11-01', '2024-11-15', '2024-11-14'),
(9, 'PRJAN101813-FIC00003', 4, '2024-11-05', '2024-11-19', '2024-11-18'),
(10, 'ABAPR101988-SCI00009', 5, '2024-11-10', '2024-11-24', '2024-11-22'),
(11, 'SAJAN102011-HIS00014', 6, '2024-11-15', '2024-11-29', '2024-11-28'),
(12, 'CLAUG102008-TEC00018', 7, '2024-11-20', '2024-12-04', '2024-12-03'),
(13, 'THAPR101925-FIC00004', 8, '2024-10-01', '2024-10-15', '2024-10-20'),
(14, 'THNOV101859-SCI00010', 9, '2024-10-05', '2024-10-19', '2024-10-25'),
(15, 'THJAN101962-HIS00015', 10, '2024-10-10', '2024-10-24', '2024-10-30'),
(16, 'LOSEP101954-FIC00006', 11, '2024-11-01', '2024-11-15', NULL),
(17, 'THJAN101976-SCI00012', 12, '2024-11-05', '2024-11-19', NULL),
(18, 'BUJAN032015-FIC00025', 11, '2025-07-03', '2025-07-10', '2025-07-03'),
(19, 'ABAPR101988-SCI00009', 18, '2025-07-04', '2025-07-18', '2025-07-04'),
(20, 'DEOCT101994-TEC00020', 18, '2025-07-04', '2025-07-18', NULL),
(21, 'TOJUL101960-FIC00001', 18, '2025-07-04', '2025-07-18', '2025-07-04'),
(22, 'ABAPR101988-SCI00009', 18, '2025-07-04', '2025-07-18', NULL),
(23, 'INJAN101990-TEC00021', 4, '2025-07-04', '2025-07-18', '2025-07-04'),
(24, 'CLAUG102008-TEC00018', 18, '2025-07-04', '2025-07-18', NULL),
(25, 'GOJUL032025-SCI00026', 18, '2025-07-04', '2025-07-11', NULL),
(26, 'INJAN101990-TEC00021', 2, '2025-07-04', '2025-07-18', NULL),
(27, 'GOJUL032025-SCI00026', 18, '2025-07-04', '2025-07-11', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `fines`
--

CREATE TABLE `fines` (
  `fine_id` int(11) NOT NULL,
  `borrowing_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` enum('unpaid','paid') DEFAULT 'unpaid',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `paid_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fines`
--

INSERT INTO `fines` (`fine_id`, `borrowing_id`, `amount`, `status`, `created_at`, `paid_at`) VALUES
(1, 13, 5.00, 'paid', '2025-06-20 08:35:19', '2024-10-20 16:00:00'),
(2, 14, 7.50, 'paid', '2025-06-20 08:35:19', '2024-10-25 16:00:00'),
(3, 15, 10.00, 'unpaid', '2025-06-20 08:35:19', NULL),
(4, 16, 12.50, 'unpaid', '2025-06-20 08:35:19', NULL),
(5, 17, 8.75, 'unpaid', '2025-06-20 08:35:19', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `reservation_id` int(11) NOT NULL,
  `book_id` varchar(25) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` enum('pending','notified','fulfilled','cancelled') DEFAULT 'pending',
  `reserved_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `notified_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`reservation_id`, `book_id`, `user_id`, `status`, `reserved_at`, `notified_at`, `expires_at`) VALUES
(1, 'CLAUG102008-TEC00018', 11, 'cancelled', '2025-07-03 12:04:27', NULL, NULL),
(2, 'GOJUL032025-SCI00026', 11, 'pending', '2025-07-03 13:21:54', NULL, NULL),
(3, 'COSEP101980-SCI00011', 11, 'pending', '2025-07-04 02:36:23', NULL, NULL),
(4, 'BUJAN032015-FIC00025', 18, 'cancelled', '2025-07-04 02:47:03', NULL, NULL),
(5, 'BUJAN032015-FIC00025', 18, 'cancelled', '2025-07-04 02:47:07', NULL, NULL),
(6, 'GOJUL032025-SCI00026', 18, 'cancelled', '2025-07-04 02:51:45', NULL, NULL),
(7, 'JUN101949-FIC00002', 18, 'cancelled', '2025-07-04 02:52:03', NULL, NULL),
(8, 'APJAN101980-HIS00016', 18, 'cancelled', '2025-07-04 02:55:53', NULL, NULL),
(9, 'GOJUL032025-SCI00026', 18, 'fulfilled', '2025-07-04 03:25:50', NULL, NULL),
(10, 'GOJUL032025-SCI00026', 4, 'cancelled', '2025-07-04 03:32:16', NULL, NULL),
(11, 'ABAPR101988-SCI00009', 2, 'pending', '2025-07-04 05:51:12', NULL, NULL),
(12, 'JUN101949-FIC00002', 18, 'pending', '2025-07-04 05:52:08', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `book_id` varchar(25) NOT NULL,
  `rating` int(1) NOT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `review_text` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','student') NOT NULL,
  `email` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `role`, `email`, `created_at`, `phone`, `address`, `updated_at`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'admin@gardenlibrary.com', '2025-06-20 08:35:19', NULL, NULL, '2025-07-04 01:57:52'),
(2, 'librarian1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'librarian@gardenlibrary.com', '2025-06-20 08:35:19', NULL, NULL, '2025-07-04 01:57:52'),
(3, 'john_doe', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 'john.doe@student.edu', '2025-06-20 08:35:19', NULL, NULL, '2025-07-04 01:57:52'),
(4, 'jane_smith', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 'jane.smith@student.edu', '2025-06-20 08:35:19', NULL, NULL, '2025-07-04 01:57:52'),
(5, 'mike_johnson', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 'mike.johnson@student.edu', '2025-06-20 08:35:19', NULL, NULL, '2025-07-04 01:57:52'),
(6, 'sarah_wilson', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 'sarah.wilson@student.edu', '2025-06-20 08:35:19', NULL, NULL, '2025-07-04 01:57:52'),
(7, 'david_brown', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 'david.brown@student.edu', '2025-06-20 08:35:19', NULL, NULL, '2025-07-04 01:57:52'),
(8, 'lisa_garcia', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 'lisa.garcia@student.edu', '2025-06-20 08:35:19', NULL, NULL, '2025-07-04 01:57:52'),
(9, 'tom_davis', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 'tom.davis@student.edu', '2025-06-20 08:35:19', NULL, NULL, '2025-07-04 01:57:52'),
(10, 'emma_miller', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 'emma.miller@student.edu', '2025-06-20 08:35:19', NULL, NULL, '2025-07-04 01:57:52'),
(11, 'alex_taylor', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 'alex.taylor@student.edu', '2025-06-20 08:35:19', NULL, NULL, '2025-07-04 01:57:52'),
(12, 'maria_rodriguez', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 'maria.rodriguez@student.edu', '2025-06-20 08:35:19', NULL, NULL, '2025-07-04 01:57:52'),
(14, 'gelton', '$2y$10$wcC8m4R/h5k7BRMwFdoQlew/Y9slZsIpUD62iUWrehjuyow..jkYi', 'admin', '202310839@fit.edu.ph', '2025-07-03 14:48:21', NULL, NULL, '2025-07-04 01:57:52'),
(15, 'testuser', '$2y$10$uRiQl9wpvRNk/lDMSXbxue3EePBa86Gk77kq3AbSIzrkP.RskUIJm', 'student', 'test@test.com', '2025-07-03 15:08:34', NULL, NULL, '2025-07-04 01:57:52'),
(16, 'testuser124', '$2y$10$KZqG16iqUhbAyB0cGyx9O.tEVqqEZHmi2GAcatzrm9RD.0DGmQaVC', 'student', 'test823@test.com', '2025-07-03 15:09:59', NULL, NULL, '2025-07-04 01:57:52'),
(17, 'hahahaha', '$2y$10$sDZ1h0QylYRgzsB.m679o..4Nm59Y6cOB0GNVZ2x84n7zmB/E1feq', 'student', 'hahahaha@hehe.com', '2025-07-03 15:17:03', NULL, NULL, '2025-07-04 01:57:52'),
(18, 'pogi123', '$2y$10$XRK0FQu0xvlKfL/JoeuCQuIYQBvFqrsooZghht0.H2YYBp.yTOC9i', 'student', 'pogi123@gmail.com', '2025-07-04 01:50:49', NULL, NULL, '2025-07-04 01:57:52');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`book_id`);

--
-- Indexes for table `borrowings`
--
ALTER TABLE `borrowings`
  ADD PRIMARY KEY (`borrowing_id`),
  ADD KEY `book_id` (`book_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `fines`
--
ALTER TABLE `fines`
  ADD PRIMARY KEY (`fine_id`),
  ADD KEY `borrowing_id` (`borrowing_id`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`reservation_id`),
  ADD KEY `book_id` (`book_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD UNIQUE KEY `unique_user_book_review` (`user_id`,`book_id`),
  ADD KEY `book_id` (`book_id`),
  ADD KEY `rating` (`rating`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `borrowings`
--
ALTER TABLE `borrowings`
  MODIFY `borrowing_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `fines`
--
ALTER TABLE `fines`
  MODIFY `fine_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `reservation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `borrowings`
--
ALTER TABLE `borrowings`
  ADD CONSTRAINT `borrowings_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`),
  ADD CONSTRAINT `borrowings_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `fines`
--
ALTER TABLE `fines`
  ADD CONSTRAINT `fines_ibfk_1` FOREIGN KEY (`borrowing_id`) REFERENCES `borrowings` (`borrowing_id`);

--
-- Constraints for table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`),
  ADD CONSTRAINT `reservations_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
