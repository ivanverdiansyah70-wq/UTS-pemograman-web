<?php
// dashboard.php
// Panel admin yang tampil setelah login berhasil.

session_start();

// Belum login? Kembalikan ke halaman utama dan buka form login.
$user = $_SESSION['user'] ?? null;
if (!is_array($user) || !isset($user['username'])) {
    header('Location: index.php?login=1');
    exit;
}
$username = (string)$user['username'];

require __DIR__ . '/config/database.php';

// Data grafik kepopuleran Genshin (urut dari periode terlama).
$rows = $pdo->query(
    'SELECT periode, pendapatan_juta_usd, pemain_juta FROM genshin_popularity ORDER BY periode'
)->fetchAll();

// Jumlah akun terdaftar.
$totalUsers = (int)$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();

// Hitung angka ringkasan untuk kartu statistik.
$totalRevenue = 0;
$peak = null;      // periode dengan pemain terbanyak
foreach ($rows as $r) {
    $totalRevenue += (int)$r['pendapatan_juta_usd'];
    if ($peak === null || (float)$r['pemain_juta'] > (float)$peak['pemain_juta']) {
        $peak = $r;
    }
}
$change = null;    // perubahan pendapatan periode terakhir dibanding sebelumnya (%)
$n = count($rows);
if ($n >= 2) {
    $prev = (int)$rows[$n - 2]['pendapatan_juta_usd'];
    $last = (int)$rows[$n - 1]['pendapatan_juta_usd'];
    $change = $prev > 0 ? round(($last - $prev) / $prev * 100) : null;
}

// Ucapan sesuai jam (WIB).
date_default_timezone_set('Asia/Jakarta');
$hour = (int)date('G');
if ($hour < 11)      { $greeting = 'Selamat pagi'; }
elseif ($hour < 15)  { $greeting = 'Selamat siang'; }
elseif ($hour < 18)  { $greeting = 'Selamat sore'; }
else                 { $greeting = 'Selamat malam'; }

$hari  = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
$bulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli',
          'Agustus', 'September', 'Oktober', 'November', 'Desember'];
$today = $hari[(int)date('w')] . ', ' . date('j') . ' ' . $bulan[(int)date('n')] . ' ' . date('Y');

function e($text) {
    return htmlspecialchars((string)$text, ENT_QUOTES, 'UTF-8');
}

// Format juta USD jadi teks singkat: 6771 -> "$6,8 M", 540 -> "$540 jt"
function usd($juta) {
    return $juta >= 1000
        ? '$' . number_format($juta / 1000, 1, ',', '.') . ' M'
        : '$' . number_format($juta, 0, ',', '.') . ' jt';
}

// Data untuk grafik (dibaca oleh assets/js/dashboard.js)
$chartData = array_map(function ($r) {
    return [
        'periode'    => $r['periode'],
        'pendapatan' => (int)$r['pendapatan_juta_usd'],
        'pemain'     => (float)$r['pemain_juta'],
    ];
}, $rows);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard - Snow Festival</title>
    <link rel="stylesheet" href="assets/css/dashboard.css">
</head>
<body>

<div class="layout">
    <!-- ===== Sidebar ===== -->
    <aside class="sidebar" id="sidebar">
        <a class="brand" href="index.php">
            <span class="brand-logo">
                <img class="logo-main" src="assets/img/logo.svg" alt="">
                <img class="logo-overlay" src="assets/img/logo-overlay.svg" alt="">
            </span>
            <span>Snow Admin</span>
        </a>

        <nav class="menu">
            <a class="menu-item is-active" href="#ringkasan"><span class="ico">&#9638;</span> Dashboard</a>
            <a class="menu-item" href="#grafik"><span class="ico">&#9650;</span> Grafik Genshin</a>
            <a class="menu-item" href="#tabel"><span class="ico">&#9776;</span> Data Tabel</a>
            <a class="menu-item" href="index.php"><span class="ico">&#10052;</span> Halaman Snow</a>
        </nav>

        <a class="menu-item logout" href="auth/logout.php"><span class="ico">&#8617;</span> Logout</a>
    </aside>

    <!-- ===== Konten ===== -->
    <div class="main">
        <header class="topbar">
            <button type="button" class="menu-toggle" id="menu-toggle" aria-label="Buka menu" aria-controls="sidebar" aria-expanded="false">&#9776;</button>
            <div>
                <h1>Dashboard</h1>
                <p class="muted"><?= e($today) ?></p>
            </div>
            <div class="profile">
                <span class="avatar" aria-hidden="true"><?= e(strtoupper(mb_substr($username, 0, 1))) ?></span>
                <span class="profile-name"><?= e($username) ?></span>
            </div>
        </header>

        <!-- Ucapan selamat datang -->
        <section class="card welcome" id="ringkasan">
            <div>
                <p class="welcome-sub"><?= e($greeting) ?>,</p>
                <h2>Selamat datang, <?= e($username) ?>!</h2>
                <p>Kamu berhasil masuk ke panel admin Snow Festival. Di bawah ini ringkasan
                   kepopuleran <strong>Genshin Impact</strong> dari tahun ke tahun.</p>
            </div>
            <span class="welcome-flake" aria-hidden="true">&#10052;</span>
        </section>

        <!-- Kartu statistik -->
        <section class="stats">
            <div class="card stat">
                <p class="stat-label">Total pendapatan mobile</p>
                <p class="stat-value"><?= e(usd($totalRevenue)) ?></p>
                <p class="stat-note"><?= $n ?> tahun sejak rilis (estimasi)</p>
            </div>
            <div class="card stat">
                <p class="stat-label">Puncak pemain bulanan</p>
                <p class="stat-value"><?= $peak ? e(number_format((float)$peak['pemain_juta'], 0, ',', '.')) . ' jt' : '-' ?></p>
                <p class="stat-note"><?= $peak ? 'Periode ' . e($peak['periode']) : 'Belum ada data' ?></p>
            </div>
            <div class="card stat">
                <p class="stat-label">Pendapatan periode terakhir</p>
                <p class="stat-value <?= $change !== null && $change < 0 ? 'down' : 'up' ?>">
                    <?= $change === null ? '-' : ($change > 0 ? '+' : '') . $change . '%' ?>
                </p>
                <p class="stat-note">dibanding periode sebelumnya</p>
            </div>
            <div class="card stat">
                <p class="stat-label">Akun terdaftar</p>
                <p class="stat-value"><?= $totalUsers ?></p>
                <p class="stat-note">dari tabel <code>users</code></p>
            </div>
        </section>

        <!-- Grafik -->
        <section class="card" id="grafik">
            <div class="card-head">
                <div>
                    <h3>Kepopuleran Genshin Impact per Tahun</h3>
                    <p class="muted">Pendapatan mobile (batang) dan pemain aktif bulanan (garis)</p>
                </div>
                <div class="legend" id="legend">
                    <button type="button" class="legend-item" data-series="pendapatan" aria-pressed="true">
                        <span class="swatch bar"></span> Pendapatan (juta USD)
                    </button>
                    <button type="button" class="legend-item" data-series="pemain" aria-pressed="true">
                        <span class="swatch line"></span> Pemain (juta)
                    </button>
                </div>
            </div>

            <?php if ($n === 0): ?>
                <p class="empty">Data grafik kosong. Import ulang <code>database/uts_web.sql</code>.</p>
            <?php else: ?>
                <div class="chart-wrap">
                    <svg id="chart" role="img" aria-label="Grafik kepopuleran Genshin Impact per tahun"></svg>
                    <div class="tooltip" id="tooltip" hidden></div>
                </div>
            <?php endif; ?>

            <p class="source">
                Periode dihitung per tahun rilis (Sep&ndash;Sep). Angka adalah estimasi pihak ketiga
                (Sensor Tower, AppMagic, ActivePlayer), dibulatkan &mdash; bukan data resmi HoYoverse.
            </p>
        </section>

        <!-- Tabel -->
        <section class="card" id="tabel">
            <h3>Data Tabel</h3>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr><th>Periode</th><th>Pendapatan mobile</th><th>Pemain aktif bulanan</th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($rows as $r): ?>
                        <tr>
                            <td><?= e($r['periode']) ?></td>
                            <td><?= e(usd((int)$r['pendapatan_juta_usd'])) ?></td>
                            <td><?= e(number_format((float)$r['pemain_juta'], 0, ',', '.')) ?> juta</td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>

<script>
    // Data dari database, dipakai untuk menggambar grafik
    window.CHART_DATA = <?= json_encode($chartData, JSON_HEX_TAG | JSON_HEX_AMP) ?>;
</script>
<script src="assets/js/dashboard.js"></script>
</body>
</html>
