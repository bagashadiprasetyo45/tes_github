# Deploy KEUANGAN BAGAS - PWA Edition

Aplikasi sudah jadi PWA, bisa di-Install di HP seperti aplikasi native.

## File PWA yang ditambahkan
- `manifest.json` - config install (nama, icon, warna)
- `service-worker.js` - cache & offline fallback
- `offline.html` - halaman saat offline
- `assets/icons/icon-192.png`, `icon-512.png`, `icon-512-maskable.png`, `apple-touch-icon.png`

## Deploy ke InfinityFree (tetap sama)

1. Upload `keuangan-bagas-PWA.zip` ke `htdocs/` di InfinityFree dan Extract
2. Import `database/keuangan_bagas.sql` via phpMyAdmin
3. Atur `config/database.php` (host/db/user/pass dari InfinityFree)
4. Buka `https://namamu.infinityfreeapp.com` -> login `bagas` / `bagas`

### PWA wajib HTTPS
InfinityFree sudah HTTPS gratis, jadi PWA akan aktif otomatis.
Jika di Laragon (http://localhost) PWA tetap jalan karena `localhost` dianggap secure context oleh browser.

## Cara Install di HP

### Android (Chrome)
1. Buka website kamu di Chrome HP
2. Muncul banner bawah "Install KEUANGAN BAGAS" -> tap **Install**
   Atau menu Chrome (⋮) > **Install aplikasi** / **Pasang aplikasi**
3. Icon muncul di Home Screen, buka fullscreen tanpa address bar

### iPhone (Safari)
1. Buka website di Safari
2. Tap **Share** (kotak + panah) > **Add to Home Screen** / **Tambah ke Layar Utama**
3. Tap **Add** -> icon muncul di home screen

## Tes PWA
- Buka DevTools > Application > Manifest (harus terbaca)
- DevTools > Application > Service Workers (harus Registered)
- Lighthouse > PWA audit harus hijau

## Catatan
- `service-worker.js` pakai strategi network-first untuk .php (data selalu fresh) dan cache-first untuk assets (cepat)
- Jika update code, ganti `CACHE_NAME` di `service-worker.js` dari `v1` ke `v2` agar cache refresh
