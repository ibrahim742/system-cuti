self.addEventListener('install', (event) => {
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(self.clients.claim());
});

self.addEventListener('fetch', (event) => {
    // Biarkan semua request langsung ke server agar session & CSRF Laravel selalu sinkron
    event.respondWith(fetch(event.request));
});