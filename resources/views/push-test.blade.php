<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Test Push Notifications</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; max-width: 800px; margin: 0 auto; }
        .status { padding: 10px; margin: 10px 0; border-radius: 5px; }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
        .info { background: #d1ecf1; color: #0c5460; }
        button { padding: 10px 20px; margin: 10px 5px; font-size: 16px; cursor: pointer; }
        pre { background: #f5f5f5; padding: 10px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>🔔 Test des Notifications Push</h1>
    
    <div id="status"></div>
    <div id="config-status"></div>
    
    <div>
        <button onclick="checkSupport()">1. Vérifier support navigateur</button>
        <button onclick="requestPermission()">2. Demander permission</button>
        <button onclick="registerServiceWorker()">3. Enregistrer Service Worker</button>
        <button onclick="subscribe()">4. S'abonner aux notifications</button>
        <button onclick="checkSubscription()">5. Vérifier abonnement</button>
    </div>

    <h2>Logs :</h2>
    <div id="logs"></div>

    <script>
        function log(message, type = 'info') {
            const logs = document.getElementById('logs');
            const div = document.createElement('div');
            div.className = 'status ' + type;
            div.textContent = new Date().toLocaleTimeString() + ' - ' + message;
            logs.insertBefore(div, logs.firstChild);
        }

        function showConfigStatus(message, type = 'info') {
            const container = document.getElementById('config-status');
            container.innerHTML = '';
            const div = document.createElement('div');
            div.className = 'status ' + type;
            div.textContent = message;
            container.appendChild(div);
        }

        function checkSupport() {
            if (!('serviceWorker' in navigator)) {
                log('❌ Service Worker non supporté', 'error');
                return false;
            }
            if (!('PushManager' in window)) {
                log('❌ Push Manager non supporté', 'error');
                return false;
            }
            if (!('Notification' in window)) {
                log('❌ Notifications non supportées', 'error');
                return false;
            }
            log('✅ Navigateur compatible avec les notifications push', 'success');
            log('Permission actuelle: ' + Notification.permission, 'info');
            return true;
        }

        async function checkServerStatus() {
            try {
                const response = await fetch('/api/push/status');
                if (!response.ok) {
                    showConfigStatus('⚠️ Statut push indisponible (' + response.status + ')', 'error');
                    return;
                }

                const status = await response.json();

                if (!status.hasVapidKeys) {
                    showConfigStatus('❌ VAPID manquant: configurez les cles VAPID_PUBLIC_KEY et VAPID_PRIVATE_KEY', 'error');
                    return;
                }

                if (!status.hasGmp && !status.hasBcMath) {
                    showConfigStatus('⚠️ PHP sans GMP/BCMath: les push seront desactives', 'error');
                    return;
                }

                showConfigStatus('✅ Configuration serveur OK pour les notifications push', 'success');
            } catch (error) {
                showConfigStatus('⚠️ Erreur statut push: ' + error.message, 'error');
            }
        }

        async function requestPermission() {
            if (!checkSupport()) return;
            
            const permission = await Notification.requestPermission();
            if (permission === 'granted') {
                log('✅ Permission accordée', 'success');
            } else {
                log('❌ Permission refusée: ' + permission, 'error');
            }
        }

        let swRegistration = null;

        async function registerServiceWorker() {
            try {
                swRegistration = await navigator.serviceWorker.register('/sw.js');
                log('✅ Service Worker enregistré: ' + swRegistration.scope, 'success');
                
                // Attendre que le SW soit actif
                await navigator.serviceWorker.ready;
                log('✅ Service Worker prêt', 'success');
            } catch (error) {
                log('❌ Erreur Service Worker: ' + error.message, 'error');
            }
        }

        async function subscribe() {
            if (!swRegistration) {
                log('⚠️ Enregistrez d\'abord le Service Worker', 'error');
                return;
            }

            try {
                // Récupérer la clé publique VAPID
                const response = await fetch('/api/push/public-key');
                const data = await response.json();
                
                log('Clé VAPID reçue: ' + data.publicKey.substring(0, 20) + '...', 'info');

                const applicationServerKey = urlBase64ToUint8Array(data.publicKey);
                
                log('Tentative d\'abonnement...', 'info');
                const subscription = await swRegistration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: applicationServerKey
                });

                log('✅ Subscription créée localement', 'success');
                log('Endpoint: ' + subscription.endpoint.substring(0, 50) + '...', 'info');

                // Envoyer au serveur
                const key = subscription.getKey('p256dh');
                const token = subscription.getKey('auth');

                log('Envoi au serveur...', 'info');

                const subscriptionData = {
                    endpoint: subscription.endpoint,
                    keys: {
                        p256dh: key ? btoa(String.fromCharCode.apply(null, new Uint8Array(key))) : null,
                        auth: token ? btoa(String.fromCharCode.apply(null, new Uint8Array(token))) : null
                    },
                    contentEncoding: 'aesgcm'
                };

                console.log('Subscription data:', subscriptionData);

                const saveResponse = await fetch('/api/push/subscribe', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(subscriptionData)
                });

                if (!saveResponse.ok) {
                    const errorText = await saveResponse.text();
                    log('❌ Erreur serveur: ' + saveResponse.status + ' - ' + errorText, 'error');
                    console.error('Server response:', errorText);
                    return;
                }

                const result = await saveResponse.json();
                log('✅ Subscription enregistrée sur le serveur: ' + result.message, 'success');

            } catch (error) {
                log('❌ Erreur subscription: ' + error.message, 'error');
                console.error('Full error:', error);
            }
        }

        async function checkSubscription() {
            if (!swRegistration) {
                log('⚠️ Enregistrez d\'abord le Service Worker', 'error');
                return;
            }

            const subscription = await swRegistration.pushManager.getSubscription();
            if (subscription) {
                log('✅ Abonné aux notifications', 'success');
                log('Endpoint: ' + subscription.endpoint.substring(0, 50) + '...', 'info');
            } else {
                log('❌ Pas d\'abonnement actif', 'error');
            }
        }

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

        // Auto-check au chargement
        window.onload = function() {
            checkSupport();
            checkServerStatus();
        };
    </script>
</body>
</html>
