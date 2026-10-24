<?php
// config/database.php
// Koneksi ke MySQL memakai PDO. File ini di-require oleh halaman lain,
// lalu variabel $pdo bisa langsung dipakai untuk query.

$host    = '127.0.0.1';
$db      = 'uts_web';
$user    = 'root';
$pass    = '';          // default Laragon: password kosong
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,  // error dilempar sebagai exception
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,        // hasil query berupa array asosiatif
    PDO::ATTR_EMULATE_PREPARES   => false,                   // pakai prepared statement asli (anti SQL injection)
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    // Tampilkan pesan sederhana saja, tanpa detail teknis / stack trace.
    exit('Maaf, koneksi ke database gagal. Pastikan MySQL menyala dan database "uts_web" sudah diimpor.');
}
