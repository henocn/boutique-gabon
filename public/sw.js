// Service Worker pour gérer les notifications push
self.addEventListener('push', function(event) {
    if (!event.data) {
        return;
    }

    const data = event.data.json();
    const title = data.title || 'Nouvelle notification';
    const options = {
        body: data.body || '',
        icon: data.icon || '/images/logo.png',
        badge: data.badge || '/images/badge.png',
        tag: data.tag || 'notification',
        data: data.data || {},
        requireInteraction: true,
        vibrate: [200, 100, 200]
    };

    event.waitUntil(
        self.registration.showNotification(title, options)
    );
});

// Gestion du clic sur la notification
self.addEventListener('notificationclick', function(event) {
    event.notification.close();

    const urlToOpen = event.notification.data.url || '/';

    event.waitUntil(
        clients.matchAll({
            type: 'window',
            includeUncontrolled: true
        })
        .then(function(clientList) {
            // Si une fenêtre existe déjà, la focus
            for (let i = 0; i < clientList.length; i++) {
                const client = clientList[i];
                if (client.url === urlToOpen && 'focus' in client) {
                    return client.focus();
                }
            }
            // Sinon ouvrir une nouvelle fenêtre
            if (clients.openWindow) {
                return clients.openWindow(urlToOpen);
            }
        })
    );
});

// Gestion de la fermeture de notification
self.addEventListener('notificationclose', function(event) {
    console.log('Notification closed', event.notification.data);
});
