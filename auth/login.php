<?php
// auth/login.php
// Memproses form login (method POST).

session_start();

// Hanya boleh diakses lewat POST; selain itu kembali ke halaman utama.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

require __DIR__ . '/../config/database.php';

// Ambil input dan buang spasi di awal/akhir.
$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

// Validasi: tidak boleh kosong.
if ($username === '' || $password === '') {
    $_SESSION['login_error'] = 'Username dan password wajib diisi.';
    header('Location: ../index.php?login=1');
    exit;
}

// Cari user dengan prepared statement (aman dari SQL injection).
$stmt = $pdo->prepare('SELECT id, username, password FROM users WHERE username = ? LIMIT 1');
$stmt->execute([$username]);
$user = $stmt->fetch();

// Cocokkan password input dengan hash di database.
if ($user && password_verify($password, $user['password'])) {
    session_regenerate_id(true); // ganti ID session agar aman dari session fixation
    $_SESSION['user'] = [
        'id'       => $user['id'],
        'username' => $user['username'],
    ];
    unset($_SESSION['login_error']);
    header('Location: ../index.php');
    exit;
}

// Login gagal.
$_SESSION['login_error'] = 'Username atau password salah.';
header('Location: ../index.php?login=1');
exit;
