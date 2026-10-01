# KEUANGAN BAGAS

Aplikasi personal finance dashboard berbasis PHP 8.3, MySQL/MariaDB, PDO, Chart.js, dan UI glassmorphism responsive.

## Instalasi Laragon

1. Pastikan folder proyek berada di `C:\laragon\www\keuangan_bagas`.
2. Jalankan Apache/Nginx dan MySQL dari Laragon.
3. Buat database bernama `keuangan_bagas`.
4. Import file `database/keuangan_bagas.sql` melalui HeidiSQL, phpMyAdmin, atau MySQL CLI.
5. Periksa koneksi di `config/database.php`:
   - Host: `localhost`
   - Database: `keuangan_bagas`
   - Username: `root`
   - Password: kosong/default Laragon sesuai konfigurasi mesin
6. Buka `http://localhost/keuangan_bagas/`.

## Akun awal

- Username: `bagas`
- Password: `bagas`

Password di database sudah berupa hasil `password_hash()` dan proses login memakai `password_verify()`.

## Struktur penting

- `config/database.php`: koneksi PDO.
- `includes/auth.php`: session authentication dan CSRF.
- `includes/functions.php`: helper keamanan, validasi, format Rupiah, dan perhitungan.
- `includes/header.php`, `sidebar.php`, `navbar.php`, `footer.php`: layout reusable.
- `assets/css/style.css`: desain glassmorphism responsive.
- `assets/js/app.js`: modal, drawer mobile, toast, konfirmasi, dan format nominal.
- `database/keuangan_bagas.sql`: schema, foreign key, index, user awal, kategori, dan data dummy opsional.

## Keamanan

Semua query aplikasi memakai PDO prepared statement, halaman internal diproteksi session, form penting memiliki CSRF token, output HTML di-escape dengan `htmlspecialchars()`, nominal/tanggal divalidasi, dan query selalu dibatasi oleh `user_id`.

## Catatan data dummy

SQL berisi blok bertanda `DATA DUMMY OPSIONAL`. Hapus blok INSERT bertanda tersebut bila ingin memulai dari kondisi kosong.