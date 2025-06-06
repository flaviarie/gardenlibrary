CREATE DATABASE library_system;
USE library_system;

CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'student') NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE books (
    book_id VARCHAR(20) PRIMARY KEY,  -- Format: THFEB102022-FIC00001
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255) NOT NULL,
    publish_date DATE,
    category VARCHAR(3) NOT NULL,     -- FIC, SCI, etc.
    added_date DATE NOT NULL,
    status ENUM('available', 'borrowed', 'archived') DEFAULT 'available',
    is_deleted BOOLEAN DEFAULT FALSE
);

CREATE TABLE borrowings (
    borrowing_id INT AUTO_INCREMENT PRIMARY KEY,
    book_id VARCHAR(20) NOT NULL,
    user_id INT NOT NULL,
    borrow_date DATE NOT NULL,
    due_date DATE NOT NULL,
    return_date DATE NULL,
    FOREIGN KEY (book_id) REFERENCES books(book_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

CREATE TABLE fines (
    fine_id INT AUTO_INCREMENT PRIMARY KEY,
    borrowing_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    status ENUM('unpaid', 'paid') DEFAULT 'unpaid',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    paid_at TIMESTAMP NULL,
    FOREIGN KEY (borrowing_id) REFERENCES borrowings(borrowing_id)
);