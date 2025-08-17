-- Create the database
CREATE DATABASE newsletter_db;

-- Use the database
USE newsletter_db;

-- Create the subscribers table
CREATE TABLE subscribers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);