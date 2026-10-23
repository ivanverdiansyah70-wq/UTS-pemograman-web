# Panduan Upload ke GitHub (untuk anggota kelompok)

Repo: https://github.com/ivanverdiansyah70-wq/UTS-pemograman-web

Bagian **Frontend (Ivan)** sudah di-upload. Anggota lain meng-upload bagiannya masing-masing
dari akun GitHub sendiri supaya commit tercatat atas nama masing-masing.

## Pembagian file

| Anggota | Bagian | File yang di-commit |
|---|---|---|
| Ivan | Frontend (slicing Figma) | `index.php`, `assets/`, `PANDUAN-UPLOAD.md` (sudah) |
| Anggota 2 | Backend PHP | `config/database.php`, `auth/login.php`, `auth/logout.php` |
| Anggota 3 | Database & Dokumentasi | `database/uts_web.sql`, `README.md`, laporan |

File backend & database dikirim Ivan lewat zip/WhatsApp.

## Syarat

1. Sudah install **Git** (https://git-scm.com) dan punya akun GitHub.
2. Sudah diundang Ivan sebagai **collaborator** (Settings → Collaborators di repo), lalu terima undangannya di email/GitHub.

## Langkah (lakukan berurutan: Anggota 3 dulu, lalu Anggota 2)

```bash
# 1. Ambil repo (cukup sekali)
git clone https://github.com/ivanverdiansyah70-wq/UTS-pemograman-web.git
cd UTS-pemograman-web

# 2. Atur nama & email akun GitHub KAMU (hanya untuk repo ini)
git config user.name "Nama Kamu"
git config user.email "email-github-kamu@example.com"

# 3. Salin file bagianmu ke folder repo (lihat tabel di atas), lalu:
git pull                     # ambil perubahan terbaru dulu

# Anggota 2 (Backend):
git add config/ auth/
git commit -m "feat: backend login dan logout PHP native"

# Anggota 3 (Database & Dokumentasi):
git add database/ README.md
git commit -m "feat: database users dan README"

# 4. Upload
git push
```

Saat `git push` pertama kali akan muncul jendela login GitHub → login dengan akun **kamu sendiri**.

## Kalau ada masalah

- **`rejected ... fetch first`** → jalankan `git pull --rebase` lalu `git push` lagi.
- **`Permission denied` / 403** → kamu belum jadi collaborator, atau login dengan akun yang salah.
  Hapus login lama: Windows → *Credential Manager* → *Windows Credentials* → hapus `git:https://github.com`, lalu push ulang.
- Cek commit sudah atas nama kamu: `git log --oneline --format="%h %an %s"`.

## Menjalankan project (Laragon)

1. Salin folder repo ke `laragon/www/` (atau jalankan `php -S localhost:8000` di folder repo).
2. Import `database/uts_web.sql` (HeidiSQL / phpMyAdmin).
3. Buka di browser, klik **Start**, login dengan `admin` / `admin123`.
