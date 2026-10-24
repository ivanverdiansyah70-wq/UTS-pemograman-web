<?php
// auth/logout.php
// Menghapus session sehingga user dianggap keluar (logout).

session_start();

// 1. Kosongkan semua data session.
$_SESSION = [];

// 2. Hapus cookie session di browser.
if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
}

// 3. Hancurkan session di server.
session_destroy();

header('Location: ../index.php');
exit;
