const CACHE_NAME = 'btguru-student-v1';
const STATIC_ASSETS = [
    '/',
    '/student/dashboard',
    '/student/courses',
    '/student/exams',
    '/student/live-classes',
    '/student/notifications',
    '/student/payments',
    '/build/assets/app.css',
    '/build/assets/app.js'
];

// Install event - cache static assets
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(STATIC_ASSETS).catch(() => {
                // Silently fail if some resources don't exist
                console.log('Some static assets could not be cached');
            });
        })
    );
    self.skipWaiting();
});

// Activate event - clean up old caches
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames
                    .filter((name) => name !== CACHE_NAME)
                    .map((name) => caches.delete(name))
            );
        })
    );
    self.clients.claim();
});

// Fetch event - serve from cache or network
self.addEventListener('fetch', (event) => {
    // Skip non-GET requests and API calls
    if (event.request.method !== 'GET' || 
        event.request.url.includes('/api/') ||
        event.request.url.includes('livewire') ||
        event.request.url.includes('socket')) {
        return;
    }

    // Skip external requests
    if (!event.request.url.startsWith(self.location.origin)) {
        return;
    }

    event.respondWith(
        caches.match(event.request).then((cached) => {
            if (cached) {
                // Return cached version and update cache in background
                fetch(event.request)
                    .then((response) => {
                        if (response.ok) {
                            caches.open(CACHE_NAME).then((cache) => {
                                cache.put(event.request, response.clone());
                            });
                        }
                    })
                    .catch(() => {});
                return cached;
            }

            // Not in cache - fetch from network
            return fetch(event.request)
                .then((response) => {
                    if (!response || response.status !== 200) {
                        return response;
                    }
                    
                    // Clone and cache the response
                    const responseToCache = response.clone();
                    caches.open(CACHE_NAME).then((cache) => {
                        cache.put(event.request, responseToCache);
                    });
                    
                    return response;
                })
                .catch(() => {
                    // Network failed - try to return offline page for navigation
                    if (event.request.mode === 'navigate') {
                        return caches.match('/student/dashboard');
                    }
                });
        })
    );
});

// Handle push notifications (if implemented later)
self.addEventListener('push', (event) => {
    if (!event.data) return;
    
    try {
        const data = event.data.json();
        const options = {
            body: data.body || 'New notification',
            icon: '/build/icon-192x192.png',
            badge: '/build/icon-72x72.png',
            tag: data.tag || 'default',
            requireInteraction: true,
            data: {
                url: data.url || '/student/dashboard'
            }
        };
        
        event.waitUntil(
            self.registration.showNotification(data.title || 'BT Guru', options)
        );
    } catch (e) {
        console.error('Push notification error:', e);
    }
});

// Handle notification click
self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    
    const url = event.notification.data?.url || '/student/dashboard';
    
    event.waitUntil(
        clients.matchAll({ type: 'window' }).then((clientList) => {
            // If a window client is already open, focus it
            for (const client of clientList) {
                if (client.url.includes(url) && 'focus' in client) {
                    return client.focus();
                }
            }
            // Otherwise, open a new window
            if (clients.openWindow) {
                return clients.openWindow(url);
            }
        })
    );
});
