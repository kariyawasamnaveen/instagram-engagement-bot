const CACHE_NAME = 'nvo-cache-v4';
const urlsToCache = [
    '/',
    '/assets/css/core.css',
    '/assets/js/core.js',
    '/assets/images/logo.png'
];

self.addEventListener('install', event => {
    self.skipWaiting(); // Force the waiting service worker to become the active service worker.
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => cache.addAll(urlsToCache))
    );
});

// Clear old caches on activate
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(cacheName => {
                    if (cacheName !== CACHE_NAME) {
                        return caches.delete(cacheName);
                    }
                })
            );
        }).then(() => self.clients.claim()) // Claim clients immediately
    );
});

self.addEventListener('fetch', event => {
    // For navigation requests, fetch from network first, fallback to cache
    if (event.request.mode === 'navigate') {
        event.respondWith(
            fetch(event.request).catch(() => caches.match(event.request))
        );
        return;
    }
    
    // For other requests, use cache first
    event.respondWith(
        caches.match(event.request)
            .then(response => response || fetch(event.request))
    );
});
