const CACHE_NAME = 'postani-cache-v1';
const urlsToCache = [
    '/',
];

// Install a service worker
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => {
                return cache.addAll(urlsToCache).catch(() => {});
            })
    );
});

// Cache and return requests (Network first fallback to Cache)
self.addEventListener('fetch', event => {
    // We only want to handle HTTP/HTTPS requests (Chrome extensions might trigger other protocols like chrome-extension://)
    if (event.request.url.startsWith('http')) {
        event.respondWith(
            fetch(event.request)
                .catch(() => {
                    return caches.match(event.request);
                })
        );
    }
});

// Update a service worker
self.addEventListener('activate', event => {
    const cacheWhitelist = [CACHE_NAME];
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(cacheName => {
                    if (cacheWhitelist.indexOf(cacheName) === -1) {
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
});
