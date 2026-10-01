# Deploy ke InfinityFree (Gratis PHP + MySQL)

## Opsi A: InfinityFree (paling gampang, direkomendasikan)

1. Daftar di https://app.infinityfree.net/register
2. Create Account > pilih subdomain gratis misal `keuangan-bagas.infinityfreeapp.com`
3. Masuk cPanel (Client Area > Control Panel) :
   - Buat **MySQL Database** : catat `MySQL Host`, `Database Name`, `Username`, `Password`
     Contoh: host `sqlXXX.infinityfree.com`, db `if0_XXXX_keuangan`
   - Buka **phpMyAdmin** > pilih database kamu > **Import** > upload `database/keuangan_bagas.sql` > Go
4. Upload file:
   - Buka **File Manager** atau pakai **FTP** (FileZilla) dengan kredensial dari Client Area
   - Upload SEMUA file project ke folder `htdocs/` (bukan root)
   - Pastikan `index.php`, `login.php`, `config/`, `includes/`, `assets/`, `database/` ada di `htdocs/`
5. Setting koneksi database (PENTING):
   - InfinityFree tidak bisa pakai `localhost` biasa. Edit file `config/database.php`
   - Atau lebih mudah: di cPanel buka **Environment Variables** / buat file `.htaccess` tambahan:
     ```
     SetEnv DB_HOST sqlXXX.infinityfree.com
     SetEnv DB_NAME if0_XXXX_keuangan
     SetEnv DB_USER if0_XXXX
     SetEnv DB_PASS password_db_kamu
     ```
   - Alternatif tanpa SetEnv: langsung ganti const di `config/database.php`:
     ```php
     const DB_HOST = 'sqlXXX.infinityfree.com';
     const DB_NAME = 'if0_XXXX_keuangan';
     const DB_USER = 'if0_XXXX';
     const DB_PASS = 'password_kamu';
     ```
6. Buka `https://keuangan-bagas.infinityfreeapp.com` -> akan redirect ke `login.php`
   Login default: `bagas` / `bagas`

## Opsi B: Push ke GitHub dulu (opsional tapi bagus untuk backup)
```bash
git init
git add .
git commit -m "siap deploy infinityfree"
git branch -M main
git remote add origin https://github.com/USERNAME/keuangan-bagas.git
git push -u origin main
```

## Catatan Penting
- `index.html` sudah ada agar GitHub Pages tidak error 404, tapi logic utama tetap di `index.php` -> `login.php` agar tetap konek database.
- Di InfinityFree, `DirectoryIndex` sudah diatur di `.htaccess` jadi `index.php` akan diprioritaskan.
- Jika muncul error `Database belum siap` -> berarti import SQL belum berhasil atau kredensial DB salah.
