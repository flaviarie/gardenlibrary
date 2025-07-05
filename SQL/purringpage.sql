-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 05, 2025 at 03:59 AM
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
-- Database: `purringpage`
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
('ABAPR101988-SCI00009', 'A Brief History of Time', 'Stephen Hawking', 'A groundbreaking exploration of cosmology and theoretical physics that makes complex scientific concepts accessible to general readers.', '1988-04-01', 'SCI', 'default_book_cover.svg', '2022-02-10', 'reserved', 0),
('ANJUN112025-SCI00026', 'Anthony langgam', 'Unknown Author', 'A mysterious tale about Anthony and his strange adventures that captivate readers with unexpected twists and turns.', '2025-06-20', 'SCI', 'default_book_cover.svg', '2025-06-11', 'reserved', 1),
('APJAN101980-HIS00016', 'A People\'s History of the United States', 'Howard Zinn', 'A comprehensive alternative history of the United States told from the perspective of ordinary people, minorities, and those often overlooked by traditional history books.', '1980-01-01', 'HIS', 'default_book_cover.svg', '2022-02-10', 'borrowed', 0),
('BUJAN032015-FIC00025', 'buhay ng Aso', 'Unknown Author', 'A heartwarming story about the life and adventures of a dog, exploring themes of loyalty, friendship, and the bond between humans and animals.', '2015-01-31', 'FIC', 'default_book_cover.svg', '2025-06-03', 'available', 0),
('CLAUG102008-TEC00018', 'Clean Code', 'Robert C. Martin', 'A practical guide to writing clean, maintainable code with principles and practices that every software developer should know.', '2008-08-01', 'TEC', 'default_book_cover.svg', '2022-02-10', 'available', 0),
('COSEP101980-SCI00011', 'Cosmos', 'Carl Sagan', 'A magnificent journey through the universe that combines scientific rigor with poetic wonder, exploring our place in the cosmic perspective.', '1980-09-28', 'SCI', 'default_book_cover.svg', '2022-02-10', 'reserved', 0),
('DEOCT101994-TEC00020', 'Design Patterns', 'Gang of Four', 'The essential reference for object-oriented design patterns that solve common programming problems with reusable solutions.', '1994-10-21', 'TEC', 'default_book_cover.svg', '2022-02-10', 'borrowed', 0),
('GOJUL032025-SCI00026', '1 gorilla vs 100 men', 'Gelton B', 'A thrilling survival story pitting one gorilla against one hundred men in an ultimate test of strength, strategy, and determination.', '2025-07-03', 'SCI', 'book_1_gorilla_vs_100_men_1751548866.png', '2025-07-03', 'borrowed', 0),
('HAJUL042025-KID00030', 'Harry Potter and the Chamber of Secrets', 'J.K', 'The second instalment of boy wizard Harry Potter\'s adventures at Hogwarts School of Witchcraft and Wizardry, based on the novel by JK Rowling', '2025-07-04', 'KID', 'book_Harry_Potter_and_the_Chamber_of_Secrets_1751650970.jfif', '2025-07-04', 'available', 0),
('HAJUN101997-FIC00008', 'Harry Potter and the Philosopher\'s Stone', 'J.K. Rowling', 'An epic fantasy adventure about a young wizard\'s journey at Hogwarts School of Witchcraft and Wizardry, discovering friendship, courage, and magic.', '1997-06-26', 'FIC', 'default_book_cover.svg', '2022-02-10', 'borrowed', 0),
('INJAN101990-TEC00021', 'Introduction to Algorithms', 'Thomas H. Cormen', 'The comprehensive guide to algorithms and data structures, essential for computer science students and professional programmers.', '1990-01-01', 'TEC', 'default_book_cover.svg', '2022-02-10', 'borrowed', 0),
('JUN101949-FIC00002', '1984', 'George Orwell', 'A dystopian masterpiece depicting a totalitarian society where Big Brother watches everything and independent thought is a crime.', '1949-06-08', 'FIC', 'book_1984_1751547081.jpg', '2022-02-10', 'borrowed', 0),
('LOSEP101954-FIC00006', 'Lord of the Flies', 'William Golding', 'A disturbing tale of schoolboys stranded on an uninhabited island and their disastrous attempt to govern themselves.', '1954-09-17', 'FIC', 'default_book_cover.svg', '2022-02-10', 'available', 0),
('MEJAN10180-PHI00023', 'Meditations', 'Marcus Aurelius', 'Personal reflections and philosophical insights from the Roman Emperor Marcus Aurelius on virtue, morality, and the human condition.', '0180-01-01', 'PHI', 'default_book_cover.svg', '2022-02-10', 'borrowed', 0),
('PRJAN101813-FIC00003', 'Pride and Prejudice', 'Jane Austen', 'A witty social commentary and romance following Elizabeth Bennet as she navigates love, marriage, and social expectations in Regency England.', '1813-01-28', 'FIC', 'default_book_cover.svg', '2022-02-10', 'reserved', 0),
('SAJAN102011-HIS00014', 'Sapiens: A Brief History of Humankind', 'Yuval Noah Harari', 'A provocative account of human history from the Stone Age to the present, exploring how Homo sapiens conquered the world.', '2011-01-01', 'HIS', 'default_book_cover.svg', '2022-02-10', 'available', 0),
('SISEP101962-SCI00013', 'Silent Spring', 'Rachel Carson', 'A pioneering work of environmental science that launched the modern environmental movement by exposing the dangers of pesticides.', '1962-09-27', 'SCI', 'default_book_cover.svg', '2022-02-10', 'available', 0),
('SQJUL042025-OTH00027', 'Squid Game', 'Gelton Blanca', 'A Korean survival drama where desperate people compete in deadly children\'s games for a massive cash prize.', '2025-07-04', 'OTH', 'book_Squid_Game_1751601876.jfif', '2025-07-04', 'reserved', 0),
('THAPR101925-FIC00004', 'The Great Gatsby', 'F. Scott Fitzgerald', 'A tragic tale of the American Dream gone wrong, following Jay Gatsby\'s obsessive pursuit of love and status in the Jazz Age.', '1925-04-10', 'FIC', 'default_book_cover.svg', '2022-02-10', 'available', 0),
('THJAN101962-HIS00015', 'The Guns of August', 'Barbara Tuchman', 'A gripping historical account of the first month of World War I and the miscalculations that led to a global catastrophe.', '1962-01-01', 'HIS', 'default_book_cover.svg', '2022-02-10', 'available', 0),
('THJAN101976-SCI00012', 'The Selfish Gene', 'Richard Dawkins', 'A revolutionary work explaining evolution through the lens of gene-centered selection and its implications for understanding life.', '1976-01-01', 'SCI', 'default_book_cover.svg', '2022-02-10', 'available', 0),
('THJAN10380-PHI00022', 'The Republic', 'Plato', 'Plato\'s philosophical dialogue exploring justice, the ideal state, and the nature of reality through the famous Allegory of the Cave.', '0380-01-01', 'PHI', 'default_book_cover.svg', '2022-02-10', 'available', 0),
('THJAN10500-PHI00024', 'The Art of War', 'Sun Tzu', 'An ancient Chinese military treatise on strategy, tactics, and the philosophy of warfare that remains relevant today.', '0500-01-01', 'PHI', 'default_book_cover.svg', '2022-02-10', 'borrowed', 0),
('THJUL042025-FIC00028', 'The Witcher', 'Gelton Blanca', 'In a war-torn world filled with magic, monsters, and moral ambiguity, Geralt of Rivia—a legendary monster hunter known as a Witcher—sets out on a perilous journey to find his adopted daughter, Ciri, who is being pursued by the spectral and deadly Wild Hunt. As kingdoms fall and ancient prophecies stir, Geralt must navigate a landscape of political intrigue, dark magic, and unforgettable characters. Every choice carries weight, and every path shapes the fate of the world.', '2025-07-04', 'FIC', 'book_The_Witcher_1751643237.jpeg', '2025-07-04', 'available', 1),
('THJUL042025-FIC00029', 'The Witcher', 'Gelton B', 'In a war-torn world filled with magic, monsters, and moral ambiguity, Geralt of Rivia—a legendary monster hunter known as a Witcher—sets out on a perilous journey to find his adopted daughter, Ciri, who is being pursued by the spectral and deadly Wild Hunt. As kingdoms fall and ancient prophecies stir, Geralt must navigate a landscape of political intrigue, dark magic, and unforgettable characters. Every choice carries weight, and every path shapes the fate of the world.', '2025-07-04', 'FIC', 'book_The_Witcher_1751643379.jpeg', '2025-07-04', 'available', 0),
('THJUL042025-OTH00030', 'The Death of Superman', 'Dan Jurgens', 'It was a day that no one ever dreamed would arrive: the day an unstoppable force met an immovable object. In Doomsday, Superman met his ultimate match-and his death!', '2025-07-04', 'OTH', 'book_The_Death_of_Superman_1751644345.jpg', '2025-07-04', 'available', 0),
('THJUL101951-FIC00005', 'The Catcher in the Rye', 'J.D. Salinger', 'A coming-of-age novel following Holden Caulfield\'s rebellious journey through New York City as he grapples with alienation and identity.', '1951-07-16', 'FIC', 'default_book_cover.svg', '2022-02-10', 'borrowed', 0),
('THJUN101947-HIS00017', 'The Diary of a Young Girl', 'Anne Frank', 'The powerful and moving diary of a young Jewish girl hiding from the Nazis during World War II in Amsterdam.', '1947-06-25', 'HIS', 'default_book_cover.svg', '2022-02-10', 'available', 0),
('THNOV101859-SCI00010', 'The Origin of Species', 'Charles Darwin', 'Darwin\'s groundbreaking work on natural selection that revolutionized our understanding of evolution and the diversity of life.', '1859-11-24', 'SCI', 'default_book_cover.svg', '2022-02-10', 'available', 0),
('THOCT101999-TEC00019', 'The Pragmatic Programmer', 'David Thomas', 'A practical guide to software development with timeless advice on becoming a more effective and efficient programmer.', '1999-10-20', 'TEC', 'default_book_cover.svg', '2022-02-10', 'borrowed', 0),
('THSEP101937-FIC00007', 'The Hobbit', 'J.R.R. Tolkien', 'A charming fantasy adventure following Bilbo Baggins on an unexpected journey to reclaim the Lonely Mountain from the dragon Smaug.', '1937-09-21', 'FIC', 'default_book_cover.svg', '2022-02-10', 'available', 0),
('TOJUL101960-FIC00001', 'To Kill a Mockingbird', 'Harper Lee', 'A powerful novel about racial injustice in the American South, told through the eyes of Scout Finch as her father defends a black man falsely accused of rape.', '1960-07-11', 'FIC', 'default_book_cover.svg', '2022-02-10', 'available', 0);

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
(24, 'CLAUG102008-TEC00018', 18, '2025-07-04', '2025-07-18', '2025-07-04'),
(25, 'GOJUL032025-SCI00026', 18, '2025-07-04', '2025-07-11', '2025-07-04'),
(26, 'INJAN101990-TEC00021', 2, '2025-07-04', '2025-07-18', NULL),
(27, 'GOJUL032025-SCI00026', 18, '2025-07-04', '2025-07-11', NULL),
(28, 'SQJUL042025-OTH00027', 18, '2025-07-04', '2025-07-18', '2025-07-04'),
(29, 'MEJAN10180-PHI00023', 10, '2025-07-04', '2025-07-11', NULL),
(30, 'GOJUL032025-SCI00026', 11, '2025-07-04', '2025-07-11', '2025-07-04'),
(31, 'GOJUL032025-SCI00026', 7, '2025-07-04', '2025-07-11', NULL);

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
(12, 'JUN101949-FIC00002', 18, 'cancelled', '2025-07-04 05:52:08', NULL, NULL),
(13, 'SQJUL042025-OTH00027', 18, 'pending', '2025-07-04 15:25:46', NULL, NULL),
(14, 'THJUL042025-FIC00028', 18, 'cancelled', '2025-07-04 15:34:39', NULL, NULL),
(15, 'GOJUL032025-SCI00026', 7, 'fulfilled', '2025-07-04 15:53:45', NULL, NULL);

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
  `status` enum('active','suspended') NOT NULL DEFAULT 'active' COMMENT 'User account status - active or suspended',
  `email` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `role`, `status`, `email`, `created_at`, `phone`, `address`, `updated_at`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'active', 'admin@gardenlibrary.com', '2025-06-20 08:35:19', NULL, NULL, '2025-07-04 01:57:52'),
(2, 'librarian1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'active', 'librarian@gardenlibrary.com', '2025-06-20 08:35:19', NULL, NULL, '2025-07-04 01:57:52'),
(3, 'john_doe', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 'active', 'john.doe@student.edu', '2025-06-20 08:35:19', NULL, NULL, '2025-07-04 01:57:52'),
(4, 'jane_smith', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 'active', 'jane.smith@student.edu', '2025-06-20 08:35:19', NULL, NULL, '2025-07-04 01:57:52'),
(5, 'mike_johnson', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 'active', 'mike.johnson@student.edu', '2025-06-20 08:35:19', NULL, NULL, '2025-07-04 01:57:52'),
(6, 'sarah_wilson', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 'suspended', 'sarah.wilson@student.edu', '2025-06-20 08:35:19', NULL, NULL, '2025-07-04 07:07:08'),
(7, 'david_brown', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 'active', 'david.brown@student.edu', '2025-06-20 08:35:19', NULL, NULL, '2025-07-04 01:57:52'),
(8, 'lisa_garcia', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 'active', 'lisa.garcia@student.edu', '2025-06-20 08:35:19', NULL, NULL, '2025-07-04 01:57:52'),
(9, 'tom_davis', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 'active', 'tom.davis@student.edu', '2025-06-20 08:35:19', NULL, NULL, '2025-07-04 01:57:52'),
(10, 'emma_miller', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 'active', 'emma.miller@student.edu', '2025-06-20 08:35:19', NULL, NULL, '2025-07-04 01:57:52'),
(11, 'alex_taylor', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 'active', 'alex.taylor@student.edu', '2025-06-20 08:35:19', NULL, NULL, '2025-07-04 01:57:52'),
(12, 'maria_rodriguez', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 'active', 'maria.rodriguez@student.edu', '2025-06-20 08:35:19', NULL, NULL, '2025-07-04 01:57:52'),
(14, 'gelton', '$2y$10$wcC8m4R/h5k7BRMwFdoQlew/Y9slZsIpUD62iUWrehjuyow..jkYi', 'admin', 'active', '202310839@fit.edu.ph', '2025-07-03 14:48:21', NULL, NULL, '2025-07-04 01:57:52'),
(15, 'testuser', '$2y$10$uRiQl9wpvRNk/lDMSXbxue3EePBa86Gk77kq3AbSIzrkP.RskUIJm', 'student', 'active', 'test@test.com', '2025-07-03 15:08:34', NULL, NULL, '2025-07-04 01:57:52'),
(16, 'testuser124', '$2y$10$KZqG16iqUhbAyB0cGyx9O.tEVqqEZHmi2GAcatzrm9RD.0DGmQaVC', 'student', 'active', 'test823@test.com', '2025-07-03 15:09:59', NULL, NULL, '2025-07-04 15:41:17'),
(17, 'hahahaha', '$2y$10$sDZ1h0QylYRgzsB.m679o..4Nm59Y6cOB0GNVZ2x84n7zmB/E1feq', 'student', 'active', 'hahahaha@hehe.com', '2025-07-03 15:17:03', NULL, NULL, '2025-07-04 01:57:52'),
(18, 'pogi123', '$2y$10$XRK0FQu0xvlKfL/JoeuCQuIYQBvFqrsooZghht0.H2YYBp.yTOC9i', 'student', 'active', 'pogi123@gmail.com', '2025-07-04 01:50:49', NULL, NULL, '2025-07-04 01:57:52'),
(19, 'Brownie2nd', '$2y$10$YrOuLz8CoQLpxrVQigWpuewWoZuHcK5z9MYH1lnrVcZ2ssVnNk6ie', 'admin', 'active', 'Brownie2nd@gmail.com', '2025-07-04 15:13:12', NULL, NULL, '2025-07-04 15:13:12'),
(20, 'new1', '$2y$10$D8t2LU0Q0WmWdd/CAZWiq.Ub540yNxb2BTqVoalXlIAglazkw3VuC', 'admin', 'active', 'new@new.com', '2025-07-04 15:44:13', NULL, NULL, '2025-07-04 15:44:13');

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
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_user_status` (`status`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `borrowings`
--
ALTER TABLE `borrowings`
  MODIFY `borrowing_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `fines`
--
ALTER TABLE `fines`
  MODIFY `fine_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `reservation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

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
