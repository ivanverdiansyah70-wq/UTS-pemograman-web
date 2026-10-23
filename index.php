<?php
// ===== Bagian PHP: dijalankan di server sebelum HTML dikirim =====
session_start();

// Ambil data user dari session (diisi oleh auth/login.php)
$user      = $_SESSION['user'] ?? null;
$username  = (is_array($user) && isset($user['username'])) ? (string)$user['username'] : null;
$loggedIn  = $username !== null;

// Pesan error login (flash message): dibaca sekali lalu dihapus
$loginError = $_SESSION['login_error'] ?? '';
unset($_SESSION['login_error']);

// Overlay login terbuka otomatis kalau ?login=1 atau ada error
$openLogin = (isset($_GET['login']) && $_GET['login'] === '1') || $loginError !== '';

// Splash (Snow 1) dilewati kalau sudah login atau ?login=1 atau ada error
$skipSplash = $loggedIn || $openLogin;

// Fungsi kecil supaya menulis htmlspecialchars tidak panjang
function e($text) {
    return htmlspecialchars((string)$text, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Snow Festival</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<!-- Stage = kanvas desain 1440x850, diskalakan oleh JS.
     Kelas "is-splash" = posisi Snow 1; kalau dihapus, layer bergerak ke Snow 2. -->
<div id="stage" class="stage<?= $skipSplash ? '' : ' is-splash' ?>"
     data-logged-in="<?= $loggedIn ? '1' : '0' ?>">

    <!-- Layer dari paling bawah ke paling atas -->
    <div class="layer" id="main"><img src="assets/img/main-img.png" alt=""></div>
    <div class="layer" id="land3"><img src="assets/img/land-3.png" alt=""></div>
    <div class="layer" id="tree7"><img src="assets/img/tree-7.png" alt=""></div>
    <div class="layer" id="tree8"><img src="assets/img/tree-7.png" alt=""></div>
    <div class="layer" id="tree6"><img src="assets/img/tree-6.png" alt=""></div>
    <div class="layer" id="tree5m"><img src="assets/img/tree-6.png" alt=""></div>
    <div class="layer" id="land2"><img src="assets/img/land-2.png" alt=""></div>
    <div class="layer" id="land1"><img src="assets/img/land-1.png" alt=""></div>
    <div class="layer" id="ftree4"><img src="assets/img/tree-4.png" alt=""></div>
    <div class="layer" id="ftree3"><img src="assets/img/tree-3.png" alt=""></div>
    <div class="layer" id="ftree2"><img src="assets/img/tree-3.png" alt=""></div>
    <div class="layer" id="ftree1"><img src="assets/img/tree-1.png" alt=""></div>
    <div class="layer" id="ftree5"><img src="assets/img/tree-1.png" alt=""></div>

    <!-- Navigasi: 3 ikon bulat (dekoratif) -->
    <div class="layer" id="navi">
        <button type="button" class="nav-btn nav-shirt" aria-label="Kostum">
            <img class="nav-circle" src="assets/img/nav-circle.svg" alt="">
            <span class="nav-icon"><img class="fill" src="assets/img/nav-shirt.svg" alt=""><img class="overlay" src="assets/img/nav-shirt-overlay.svg" alt=""></span>
        </button>
        <button type="button" class="nav-btn nav-user" id="open-user" aria-label="Akun">
            <img class="nav-circle" src="assets/img/nav-circle.svg" alt="">
            <span class="nav-icon"><img class="fill" src="assets/img/nav-user.svg" alt=""><img class="overlay" src="assets/img/nav-user-overlay.svg" alt=""></span>
        </button>
        <button type="button" class="nav-btn nav-menu" aria-label="Menu">
            <img src="assets/img/nav-menu.svg" alt="">
        </button>
    </div>

    <?php if ($loggedIn): ?>
    <!-- Tampil hanya kalau sudah login -->
    <div class="user-pill" id="user-pill">
        <span>Halo, <?= e($username) ?></span>
        <a href="auth/logout.php">Logout</a>
    </div>
    <?php endif; ?>

    <!-- Logo (dua gambar ditumpuk) -->
    <div class="layer" id="logo">
        <img class="logo-main" src="assets/img/logo.svg" alt="Snow Festival">
        <img class="logo-overlay" src="assets/img/logo-overlay.svg" alt="">
    </div>

    <!-- Judul: kotak ini kecil, gambar SVG-nya sengaja meluber (lihat CSS) -->
    <div class="layer" id="heading"><img src="assets/img/heading.svg" alt="Perayaan dan Kebijaksanaan"></div>

    <!-- Tombol: satu gambar berisi Start (kiri) dan FAQ (kanan); dua button transparan di atasnya -->
    <div class="layer" id="button">
        <img src="assets/img/button.png" alt="">
        <button type="button" class="hit hit-start" id="btn-start" aria-label="Start"></button>
        <button type="button" class="hit hit-faq" id="btn-faq" aria-label="FAQ"></button>
    </div>

    <!-- Popup FAQ sederhana -->
    <div class="faq-popup" id="faq-popup" hidden>
        <strong>FAQ</strong>
        <p>Apa itu Snow Festival? Perayaan musim dingin tahunan.</p>
        <p>Perlu akun? Ya, login lewat tombol Start.</p>
        <p>Gratis? Ya, tanpa biaya masuk.</p>
        <button type="button" id="faq-close">Tutup</button>
    </div>

    <!-- Snow 3: overlay login -->
    <div class="login-overlay<?= $openLogin ? ' is-open' : '' ?>" id="login-overlay">
        <div class="login-panel" role="dialog" aria-modal="true" aria-labelledby="login-title">
            <button type="button" class="login-close" id="login-close" aria-label="Tutup">&times;</button>

            <div class="login-logo">
                <img class="logo-main" src="assets/img/logo.svg" alt="">
                <img class="logo-overlay" src="assets/img/logo-overlay.svg" alt="">
            </div>

            <h2 id="login-title">Login</h2>

            <?php if ($loginError !== ''): ?>
                <p class="login-error" role="alert"><?= e($loginError) ?></p>
            <?php endif; ?>

            <form method="post" action="auth/login.php">
                <input type="text" name="username" placeholder="Username" autocomplete="username" required>
                <input type="password" name="password" placeholder="Password" autocomplete="current-password" required>
                <button type="submit" class="login-submit">&#10022; Login &#10022;</button>
            </form>
        </div>
    </div>
</div>

<script src="assets/js/main.js"></script>
</body>
</html>
