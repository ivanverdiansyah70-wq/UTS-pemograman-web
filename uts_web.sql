-- database/uts_web.sql
-- Import file ini lewat HeidiSQL / phpMyAdmin / mysql CLI.

CREATE DATABASE IF NOT EXISTS uts_web CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE uts_web;

CREATE TABLE IF NOT EXISTS users (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    username   VARCHAR(50)  NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,            -- disimpan dalam bentuk hash bcrypt
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Akun demo: username "admin", password asli "admin123"
-- (hash di bawah dibuat dengan PHP password_hash('admin123', PASSWORD_BCRYPT))
INSERT INTO users (username, password) VALUES
('admin', '$2y$10$0zMrJpB8RkzBYzAQZm1FGuA8F6d251r0O9U1PfE4QKE1ltsLcz7qC')
ON DUPLICATE KEY UPDATE password = VALUES(password);
