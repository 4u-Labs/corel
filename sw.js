const CACHE_NAME = 'corelclone-v3-cache';
const ASSETS = [
  './',
  './index.php',
  './style.css',
  './script.js',
  './favicon.png',
  './favicon.ico',
  './favicon-32x32.png',
  './favicon-16x16.png',
  './corelicon.png',
  './corelicon-192.png',
  './apple-touch-icon.png',
  './corelicon-welcome.png',
  './corelicon.svg',
  './libs/paper-full.min.js',
  './libs/qrcode.min.js',
  './libs/imagetracer.js'
];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      return cache.addAll(ASSETS).catch(err => console.warn('Cache non-fatal:', err));
    })
  );
  self.skipWaiting();
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) => {
      return Promise.all(
        keys.filter(k => k !== CACHE_NAME).map(k => caches.delete(k))
      );
    })
  );
  self.clients.claim();
});

self.addEventListener('fetch', (event) => {
  if (event.request.url.includes(':54321')) {
    return;
  }
  event.respondWith(
    caches.match(event.request).then((response) => {
      return response || fetch(event.request);
    }).catch(() => caches.match('./'))
  );
});
