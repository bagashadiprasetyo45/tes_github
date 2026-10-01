const CACHE_NAME = 'keuangan-bagas-v1';
const URLS_TO_CACHE = [
  './',
  'index.php',
  'login.php',
  'manifest.json',
  'assets/css/style.css',
  'assets/js/app.js',
  'assets/icons/icon-192.png',
  'assets/icons/icon-512.png'
];

self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME).then(cache => cache.addAll(URLS_TO_CACHE)).then(()=> self.skipWaiting())
  );
});

self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(keys => Promise.all(keys.filter(k => k !== CACHE_NAME).map(k => caches.delete(k)))).then(()=> self.clients.claim())
  );
});

self.addEventListener('fetch', event => {
  if (event.request.method !== 'GET') return;
  const url = new URL(event.request.url);
  // Jangan cache POST / API / phpMyAdmin, hanya GET navigasi & assets
  // Network-first untuk .php (biar data selalu fresh), cache-first untuk assets
  if (url.pathname.endsWith('.php') || url.pathname === '/' ) {
    event.respondWith(
      fetch(event.request).then(resp => {
        // simpan copy ke cache jika ok
        if (resp.ok) {
          const clone = resp.clone();
          caches.open(CACHE_NAME).then(c => c.put(event.request, clone));
        }
        return resp;
      }).catch(()=> caches.match(event.request).then(r => r || caches.match('login.php')))
    );
    return;
  }
  // assets: cache-first
  event.respondWith(
    caches.match(event.request).then(cached => cached || fetch(event.request).then(resp => {
      if (resp.ok) { const clone = resp.clone(); caches.open(CACHE_NAME).then(c => c.put(event.request, clone)); }
      return resp;
    }))
  );
});
