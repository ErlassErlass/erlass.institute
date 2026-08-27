const CACHE_NAME = 'erlass-ekskul-cache-v5';
const OFFLINE_URL = '/offline.html';
const CORE_ASSETS = [
    OFFLINE_URL,
    '/images/logo-erlass.png',
    '/images/logo-erlass-compressed.png',
    '/images/favicon-192.png',
    '/favicon-32.png',
    '/favicon.ico',
    '/error.html'
];
const MAX_DYNAMIC_ITEMS = 100;

// Helper: Trim cache size to prevent memory bloat
const trimCache = async (cacheName, maxItems) => {
    const cache = await caches.open(cacheName);
    const keys = await cache.keys();
    if (keys.length > maxItems) {
        await cache.delete(keys[0]);
        trimCache(cacheName, maxItems);
    }
};

// Install event: Pre-cache core assets
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(CORE_ASSETS);
        }).then(() => self.skipWaiting())
    );
});

// Activate event: Clean up old caches & claim clients immediately
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

// Listen for SKIP_WAITING message from client UI
self.addEventListener('message', (event) => {
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
});

// Fetch event: Network-First for HTML navigation with 12s Timeout & Stale-While-Revalidate for Static Assets
self.addEventListener('fetch', (event) => {
    if (event.request.method !== 'GET') return;

    const requestUrl = new URL(event.request.url);

    // 1. Document request (HTML Page navigation): Network-First with 12s Timeout
    if (event.request.mode === 'navigate') {
        event.respondWith(
            new Promise((resolve) => {
                let isTimedOut = false;
                const timeoutId = setTimeout(() => {
                    isTimedOut = true;
                    // Fallback on network timeout (12s)
                    caches.match(event.request).then((cachedResponse) => {
                        if (cachedResponse) {
                            resolve(cachedResponse);
                        } else {
                            caches.match(OFFLINE_URL).then((offlineRes) => resolve(offlineRes));
                        }
                    });
                }, 12000);

                fetch(event.request)
                    .then((networkResponse) => {
                        clearTimeout(timeoutId);
                        if (!isTimedOut && networkResponse.status === 200) {
                            const responseClone = networkResponse.clone();
                            caches.open(CACHE_NAME).then((cache) => {
                                cache.put(event.request, responseClone);
                                trimCache(CACHE_NAME, MAX_DYNAMIC_ITEMS);
                            });
                        }
                        if (!isTimedOut) resolve(networkResponse);
                    })
                    .catch(() => {
                        clearTimeout(timeoutId);
                        if (!isTimedOut) {
                            caches.match(event.request).then((cachedResponse) => {
                                if (cachedResponse) {
                                    resolve(cachedResponse);
                                } else {
                                    caches.match(OFFLINE_URL).then((offlineRes) => resolve(offlineRes));
                                }
                            });
                        }
                    });
            })
        );
        return;
    }

    // 2. Static Assets (CSS, JS, Fonts, Images): Stale-While-Revalidate
    if (
        requestUrl.origin === self.location.origin &&
        (event.request.destination === 'style' ||
         event.request.destination === 'script' ||
         event.request.destination === 'image' ||
         event.request.destination === 'font')
    ) {
        event.respondWith(
            caches.match(event.request).then((cachedResponse) => {
                const fetchPromise = fetch(event.request)
                    .then((networkResponse) => {
                        if (networkResponse.status === 200) {
                            const responseClone = networkResponse.clone();
                            caches.open(CACHE_NAME).then((cache) => {
                                cache.put(event.request, responseClone);
                                trimCache(CACHE_NAME, MAX_DYNAMIC_ITEMS);
                            });
                        }
                        return networkResponse;
                    })
                    .catch(() => {
                        if (event.request.destination === 'image') {
                            return caches.match('/images/logo-erlass.png');
                        }
                    });

                return cachedResponse || fetchPromise;
            })
        );
    }
});

// Push Notification Handler
self.addEventListener('push', (event) => {
    let data = { title: 'Erlass Ekskul', body: 'Ada pembaruan penting di Erlass Portal.', icon: '/images/favicon-192.png', url: '/dashboard' };
    if (event.data) {
        try {
            data = Object.assign(data, event.data.json());
        } catch (e) {
            data.body = event.data.text();
        }
    }

    const options = {
        body: data.body,
        icon: data.icon || '/images/favicon-192.png',
        badge: '/favicon-32.png',
        vibrate: [100, 50, 100],
        data: { url: data.url || '/dashboard' }
    };

    event.waitUntil(self.registration.showNotification(data.title, options));
});

// Push Notification Click Event
self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    const targetUrl = event.notification.data ? event.notification.data.url : '/dashboard';
    event.waitUntil(
        clients.matchAll({ type: 'window' }).then((clientList) => {
            for (const client of clientList) {
                if (client.url === targetUrl && 'focus' in client) {
                    return client.focus();
                }
            }
            if (clients.openWindow) {
                return clients.openWindow(targetUrl);
            }
        })
    );
});
