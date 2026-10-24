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

-- Data grafik kepopuleran Genshin Impact di dashboard.
-- Periode dihitung per tahun rilis (September s.d. September tahun berikutnya).
-- Angka adalah ESTIMASI pihak ketiga (Sensor Tower / AppMagic untuk pendapatan mobile,
-- ActivePlayer dkk. untuk pemain bulanan), dibulatkan. Bukan data resmi HoYoverse.
CREATE TABLE IF NOT EXISTS genshin_popularity (
    id                  INT AUTO_INCREMENT PRIMARY KEY,
    periode             VARCHAR(9)   NOT NULL UNIQUE,   -- contoh: 2020/21
    pendapatan_juta_usd INT          NOT NULL,          -- pendapatan mobile, juta USD
    pemain_juta         DECIMAL(5,1) NOT NULL           -- rata-rata pemain aktif bulanan, juta
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO genshin_popularity (periode, pendapatan_juta_usd, pemain_juta) VALUES
('2020/21', 2000, 50.0),
('2021/22', 1700, 60.0),
('2022/23', 1600, 30.0),
('2023/24',  931, 22.0),
('2024/25',  540, 15.0)
ON DUPLICATE KEY UPDATE
    pendapatan_juta_usd = VALUES(pendapatan_juta_usd),
    pemain_juta         = VALUES(pemain_juta);
