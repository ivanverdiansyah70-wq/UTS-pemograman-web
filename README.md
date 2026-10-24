# Slicing Figma "Snow" + Login PHP Native

Tugas kelompok Web Programming. Proyek ini adalah slicing desain Figma "Snow" menjadi halaman web, dilengkapi sistem login sederhana memakai PHP native (tanpa framework/composer), PDO, session, dan password hash.

Referensi: https://youtu.be/nW9-1m08SKY?si=bIetP_zWQAHI1nMY

## Struktur Folder

```
.
├── index.php            # Halaman utama (frontend)
├── dashboard.php        # Panel admin setelah login (grafik Genshin)
├── assets/              # CSS, JS, gambar
├── config/
│   └── database.php     # Koneksi PDO ke MySQL
├── auth/
│   ├── login.php        # Proses login (POST)
│   └── logout.php       # Proses logout
├── database/
│   └── uts_web.sql      # Tabel users + genshin_popularity, data demo
└── README.md
```

## Cara Menjalankan (Laragon)

1. Salin / clone folder proyek ke `laragon/www/` (atau jalankan dengan `php -S localhost:8000` di folder proyek).
2. Nyalakan Apache/MySQL di Laragon.
3. Import database `database/uts_web.sql` lewat HeidiSQL / phpMyAdmin, atau lewat CLI:
   ```
   mysql -u root < database/uts_web.sql
   ```
4. Buka di browser, mis. `http://localhost/nama-folder/` (atau `http://localhost:8000` jika memakai `php -S`).

Pengaturan koneksi database ada di `config/database.php` (host `127.0.0.1`, user `root`, password kosong, database `uts_web`).

## Alur Login & Dashboard

1. Klik ikon user (atau tombol Start) di halaman Snow → form login muncul.
2. Login berhasil → diarahkan ke `dashboard.php`: ucapan selamat datang, kartu statistik, grafik kepopuleran Genshin Impact per tahun, dan tabel data.
3. Saat sudah login, ikon user langsung membuka dashboard. Dashboard tidak bisa dibuka tanpa login.

Data grafik diambil dari tabel `genshin_popularity`. Angkanya estimasi pihak ketiga (Sensor Tower, AppMagic, ActivePlayer), bukan data resmi HoYoverse. Grafik digambar dengan SVG + JavaScript biasa (tanpa library), jadi tetap jalan offline.

## Akun Demo

| Username | Password |
|----------|----------|
| admin    | admin123 |

## Pembagian Tugas

| Anggota   | Peran                    | Bagian                                   |
|-----------|--------------------------|------------------------------------------|
| Ivan      | Frontend                 | `index.php`, `assets/`                   |
| Anggota 2 | Backend                  | `config/`, `auth/`                       |
| Anggota 3 | Database & Dokumentasi   | `database/`, `README.md`, laporan        |
