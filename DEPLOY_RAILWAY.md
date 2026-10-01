# Deploy KEUANGAN BAGAS ke Railway (PHP + MySQL gratis)

Railway anti-stuck seperti InfinityFree. Auto deploy dari GitHub.

## File yang sudah disiapkan
- `nixpacks.toml` - pakai PHP 8.3 + start via `php -S`
- `composer.json` - deteksi PHP
- `start.sh` - `php -S 0.0.0.0:$PORT -t /app`
- `railway.json` - config deploy Railway
- `Procfile` - fallback start command
- `config/database.php` - sudah support semua ENV Railway: `MYSQLHOST`, `MYSQLPORT`, `MYSQLDATABASE`, `MYSQLUSER`, `MYSQLPASSWORD`, `DATABASE_URL`, `MYSQL_URL`

## Langkah Deploy (5 menit)

### 1. Push ke GitHub (folder ini belum git init)
Buka terminal di folder `tes github`:
```bash
git init
git add .
git commit -m "siap deploy railway PWA"
git branch -M main
git remote add origin https://github.com/USERNAME/keuangan-bagas.git
git push -u origin main
```
Ganti USERNAME dengan username GitHub kamu.

### 2. Buat Project di Railway
1. Buka https://railway.app > Login dengan GitHub
2. New Project > Deploy from GitHub repo > pilih `keuangan-bagas`
3. Railway akan auto build & deploy service PHP kamu

### 3. Tambah MySQL Database
1. Di project Railway kamu > New > Database > Add MySQL
2. Railway otomatis inject ENV ke service PHP kamu: `MYSQLHOST`, `MYSQLPORT`, `MYSQLDATABASE`, `MYSQLUSER`, `MYSQLPASSWORD`, `MYSQL_URL`, `DATABASE_URL`
3. Tidak perlu setting manual lagi - `config/database.php` sudah baca semua ENV itu otomatis.

### 4. Import database/keuangan_bagas.sql
Cara A (paling mudah - via Railway MySQL console):
1. Di Railway > klik service MySQL > Data > atau Connect > `Connect to MySQL`
2. Copy isi `database/keuangan_bagas.sql` dan paste/execute di SQL console
   Atau: dari lokal jalankan:
   ```bash
   mysql -h <MYSQLHOST> -P <MYSQLPORT> -u <MYSQLUSER> -p<MYSQLPASSWORD> <MYSQLDATABASE> < database/keuangan_bagas.sql
   ```
   Ambil host/port/user/pass/db dari Variables di service MySQL.

Cara B: Buat endpoint import sekali pakai (sudah ada `bagas.php`? cek).

### 5. Generate Domain
1. Di service PHP kamu > Settings > Networking > Generate Domain
2. Dapat link `https://keuangan-bagas-production.up.railway.app`
3. Buka link itu > login `bagas` / `bagas`

### 6. Cek PWA
Buka link Railway di HP Chrome > banner Install muncul > Install > jadi aplikasi.

## Troubleshooting
- Build failed? Cek Deploy Logs di Railway
- 500 error? Lihat Logs > biasanya DB belum di-import. Import dulu `keuangan_bagas.sql`
- Database error? Cek Variables di service PHP sudah ada MYSQLHOST dll (otomatis jika MySQL dan PHP di 1 project)

## Biaya
Railway free $5 credit/bulan, cukup untuk project kecil. Jika habis, bisa upgrade atau pindah ke AlwaysData/AwardSpace.