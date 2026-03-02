// Gestion des notifications push pour les admins et managers
(function() {
    'use strict';

    // Vérifier si le navigateur supporte les notifications push
    if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
        console.warn('Les notifications push ne sont pas supportées par ce navigateur');
        return;
    }

    let isSubscribed = false;
    let swRegistration = null;

    // Initialiser le service worker
    function initializeServiceWorker() {
        navigator.serviceWorker.register('/sw.js')
            .then(function(registration) {
                console.log('Service Worker enregistré avec succès:', registration.scope);
                swRegistration = registration;
                
                // Vérifier si déjà abonné
                return registration.pushManager.getSubscription();
            })
            .then(function(subscription) {
                isSubscribed = subscription !== null;
                
                if (isSubscribed) {
                    console.log('Déjà abonné aux notifications push');
                    updateSubscriptionOnServer(subscription);
                } else {
                    // S'abonner automatiquement pour les admins/managers
                    subscribeUser();
                }
            })
            .catch(function(error) {
                console.error('Erreur lors de l\'initialisation du Service Worker:', error);
            });
    }

    // Convertir la clé publique VAPID au format Uint8Array
    function urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding)
            .replace(/\-/g, '+')
            .replace(/_/g, '/');

        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);

        for (let i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }
        return outputArray;
    }

    // S'abonner aux notifications push
    function subscribeUser() {
        // Récupérer la clé publique VAPID depuis le serveur
        fetch('/api/push/public-key')
            .then(response => response.json())
            .then(data => {
                const applicationServerKey = urlBase64ToUint8Array(data.publicKey);
                
                return swRegistration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: applicationServerKey
                });
            })
            .then(function(subscription) {
                console.log('Abonné aux notifications push:', subscription);
                isSubscribed = true;
                
                // Envoyer la subscription au serveur
                return updateSubscriptionOnServer(subscription);
            })
            .catch(function(error) {
                if (Notification.permission === 'denied') {
                    console.warn('Les notifications ont été bloquées par l\'utilisateur');
                } else {
                    console.error('Erreur lors de l\'abonnement aux notifications push:', error);
                }
            });
    }

    // Mettre à jour la subscription sur le serveur
    function updateSubscriptionOnServer(subscription) {
        if (!subscription) {
            return Promise.resolve();
        }

        const key = subscription.getKey('p256dh');
        const token = subscription.getKey('auth');
        const contentEncoding = (PushManager.supportedContentEncodings || ['aesgcm'])[0];

        const data = {
            endpoint: subscription.endpoint,
            keys: {
                p256dh: key ? btoa(String.fromCharCode.apply(null, new Uint8Array(key))) : null,
                auth: token ? btoa(String.fromCharCode.apply(null, new Uint8Array(token))) : null
            },
            contentEncoding: contentEncoding
        };

        return fetch('/api/push/subscribe', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            console.log('Subscription enregistrée sur le serveur:', data);
        })
        .catch(error => {
            console.error('Erreur lors de l\'enregistrement de la subscription:', error);
        });
    }

    // Demander la permission pour les notifications
    function requestNotificationPermission() {
        return Notification.requestPermission().then(function(permission) {
            if (permission === 'granted') {
                console.log('Permission accordée pour les notifications');
                subscribeUser();
            } else {
                console.warn('Permission refusée pour les notifications');
            }
        });
    }

    // Initialiser au chargement de la page
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeServiceWorker);
    } else {
        initializeServiceWorker();
    }

    // Demander la permission si pas encore accordée
    if (Notification.permission === 'default') {
        // Attendre quelques secondes avant de demander pour ne pas être intrusif
        setTimeout(requestNotificationPermission, 2000);
    }
})();
