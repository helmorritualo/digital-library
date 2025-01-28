CREATE DATABASE digital_library;
USE digital_library;

CREATE TABLE books (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255) NOT NULL,
    format ENUM('pdf', 'ebook', 'audiobook') NOT NULL,
    status ENUM('read', 'unread') NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

ALTER TABLE books ADD INDEX idx_status (status);
ALTER TABLE books ADD INDEX idx_format (format);
ALTER TABLE books ADD FULLTEXT INDEX idx_search (title, author);