// Service Worker pour gérer les push notifications

self.addEventListener('push', event => {
    const data = event.data.json();

    const options = {
        body: data.body || 'Nouvelle notification',
        icon: data.icon || '/images/logo.png',
        badge: data.badge || '/images/badge.png',
        tag: data.tag || 'notification',
        data: data.data || {},
        actions: [
            {
                action: 'open',
                title: 'Ouvrir'
            },
            {
                action: 'close',
                title: 'Fermer'
            }
        ]
    };

    event.waitUntil(
        self.registration.showNotification(data.title || 'Notification', options)
    );
});

self.addEventListener('notificationclick', event => {
    event.notification.close();

    if (event.action === 'close') {
        return;
    }

    // Ouvrir l'URL fournie ou la page par défaut
    const url = event.notification.data.url || '/';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true })
            .then(clientList => {
                // Chercher une fenêtre déjà ouverte
                for (let i = 0; i < clientList.length; i++) {
                    const client = clientList[i];
                    if (client.url === url && 'focus' in client) {
                        return client.focus();
                    }
                }
                // Sinon ouvrir une nouvelle fenêtre
                if (clients.openWindow) {
                    return clients.openWindow(url);
                }
            })
    );
});

self.addEventListener('notificationclose', event => {
    // Optionnel : log quand une notification est fermée
    console.log('Notification closed:', event.notification.tag);
});
