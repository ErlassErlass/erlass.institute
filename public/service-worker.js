const CACHE_NAME = 'erlass-ekskul-cache-v1';
const OFFLINE_URL = '/offline.html';
const CORE_ASSETS = [
    OFFLINE_URL,
    '/images/logo-erlass.png',
    '/favicon.ico'
];

// Install event: Pre-cache core assets
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(CORE_ASSETS);
        }).then(() => self.skipWaiting())
    );
});

// Activate event: Clean up old caches
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheName !== CACHE_NAME) {
                        return caches.delete(cacheName);
                    }
                })
            );
        }).then(() => self.clients.claim())
    );
});

// Fetch event: Network-First for documents, Cache-First for static assets
self.addEventListener('fetch', (event) => {
    // Only handle GET requests
    if (event.request.method !== 'GET') return;

    const requestUrl = new URL(event.request.url);

    // Document request (HTML Page navigation)
    if (event.request.mode === 'navigate') {
        event.respondWith(
            fetch(event.request)
                .then((networkResponse) => {
                    // Cache the new page dynamically
                    return caches.open(CACHE_NAME).then((cache) => {
                        cache.put(event.request, networkResponse.clone());
                        return networkResponse;
                    });
                })
                .catch(() => {
                    // Network failed: Try serving from cache
                    return caches.match(event.request).then((cachedResponse) => {
                        if (cachedResponse) {
                            return cachedResponse;
                        }
                        // Not in cache: Return offline fallback page
                        return caches.match(OFFLINE_URL);
                    });
                })
        );
        return;
    }

    // Static assets (CSS, JS, Fonts, Images)
    event.respondWith(
        caches.match(event.request).then((cachedResponse) => {
            if (cachedResponse) {
                return cachedResponse;
            }

            return fetch(event.request).then((networkResponse) => {
                // Cache dynamic images, fonts, css, js files
                if (
                    networkResponse.status === 200 &&
                    (requestUrl.origin === self.location.origin) &&
                    (event.request.destination === 'style' ||
                     event.request.destination === 'script' ||
                     event.request.destination === 'image' ||
                     event.request.destination === 'font')
                ) {
                    caches.open(CACHE_NAME).then((cache) => {
                        cache.put(event.request, networkResponse.clone());
                    });
                }
                return networkResponse;
            });
        }).catch(() => {
            // Static asset failed: Fallback to placeholder if it's an image
            if (event.request.destination === 'image') {
                return caches.match('/images/logo-erlass.png');
            }
        })
    );
});
