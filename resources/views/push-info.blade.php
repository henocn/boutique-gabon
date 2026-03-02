<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuration Push Notifications</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; max-width: 900px; margin: 0 auto; line-height: 1.6; }
        .info { background: #d1ecf1; padding: 15px; border-left: 4px solid #0c5460; margin: 15px 0; }
        .warning { background: #fff3cd; padding: 15px; border-left: 4px solid #856404; margin: 15px 0; }
        .error { background: #f8d7da; padding: 15px; border-left: 4px solid #721c24; margin: 15px 0; }
        .success { background: #d4edda; padding: 15px; border-left: 4px solid #155724; margin: 15px 0; }
        code { background: #f5f5f5; padding: 2px 6px; border-radius: 3px; }
        pre { background: #2d2d2d; color: #f8f8f2; padding: 15px; border-radius: 5px; overflow-x: auto; }
        h2 { color: #333; border-bottom: 2px solid #007bff; padding-bottom: 10px; }
        ol { margin-left: 20px; }
        ol li { margin-bottom: 10px; }
    </style>
</head>
<body>
    <h1>🔔 Configuration des Notifications Push</h1>

    <div class="error">
        <h3>❌ Problème détecté</h3>
        <p>Les Service Workers (nécessaires pour les notifications push) ne fonctionnent que sur :</p>
        <ul>
            <li><strong>HTTPS</strong> (domaines sécurisés)</li>
            <li><strong>localhost</strong> ou <strong>127.0.0.1</strong></li>
        </ul>
        <p>Votre domaine actuel : <code>{{ request()->getHost() }}</code></p>
    </div>

    <h2>✅ Solution 1 : Accéder via localhost</h2>
    <div class="info">
        <p><strong>La plus simple :</strong> Accédez au site via <code>localhost</code> au lieu de <code>boutique-gabon.local</code></p>
        <ol>
            <li>Allez sur : <a href="http://localhost{{ request()->getPort() != 80 ? ':' . request()->getPort() : '' }}">http://localhost</a></li>
            <li>Connectez-vous en tant qu'admin/manager</li>
            <li>Les notifications push fonctionneront automatiquement</li>
        </ol>
    </div>

    <h2>🔒 Solution 2 : Activer HTTPS en local</h2>
    <div class="info">
        <p><strong>Pour garder votre domaine personnalisé :</strong></p>
        
        <h3>Option A : Avec mkcert (recommandé)</h3>
        <pre>
# Installer mkcert
sudo apt install libnss3-tools
wget https://github.com/FiloSottile/mkcert/releases/download/v1.4.4/mkcert-v1.4.4-linux-amd64
sudo mv mkcert-v1.4.4-linux-amd64 /usr/local/bin/mkcert
sudo chmod +x /usr/local/bin/mkcert

# Créer une autorité de certification locale
mkcert -install

# Générer les certificats pour votre domaine
cd /etc/ssl/certs
sudo mkcert boutique-gabon.local localhost 127.0.0.1

# Configurer Apache/Nginx avec les certificats générés
</pre>

        <h3>Option B : Avec OpenSSL (manuel)</h3>
        <pre>
# Générer un certificat auto-signé
sudo openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
  -keyout /etc/ssl/private/boutique-gabon.key \
  -out /etc/ssl/certs/boutique-gabon.crt \
  -subj "/CN=boutique-gabon.local"

# Configurer votre serveur web pour utiliser HTTPS
</pre>
    </div>

    <h2>⚙️ Solution 3 : Forcer Chrome en mode non-sécurisé (développement uniquement)</h2>
    <div class="warning">
        <p><strong>⚠️ Uniquement pour le développement !</strong></p>
        <p>Lancez Chrome avec :</p>
        <pre>
google-chrome --unsafely-treat-insecure-origin-as-secure="http://boutique-gabon.local" \
              --user-data-dir=/tmp/chrome-dev
</pre>
        <p>Ou pour Opera :</p>
        <pre>
opera --unsafely-treat-insecure-origin-as-secure="http://boutique-gabon.local" \
      --user-data-dir=/tmp/opera-dev
</pre>
    </div>

    <h2>🎯 Recommandation</h2>
    <div class="success">
        <p><strong>Pour tester rapidement :</strong></p>
        <p>Utilisez simplement <code>http://localhost</code> au lieu de <code>boutique-gabon.local</code></p>
        <p><a href="http://localhost{{ request()->getPort() != 80 ? ':' . request()->getPort() : '' }}/push-test" 
              style="display: inline-block; background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-top: 10px;">
            🔗 Tester sur localhost
        </a></p>
    </div>

    <h2>📝 Vérifier la configuration actuelle</h2>
    <div class="info">
        <p><strong>Protocole :</strong> {{ request()->secure() ? 'HTTPS ✅' : 'HTTP ⚠️' }}</p>
        <p><strong>Hôte :</strong> {{ request()->getHost() }}</p>
        <p><strong>Port :</strong> {{ request()->getPort() }}</p>
        <p><strong>URL complète :</strong> {{ request()->url() }}</p>
    </div>

    <script>
        // Test de support
        if ('serviceWorker' in navigator) {
            document.write('<div class="success"><strong>✅ Service Workers supportés !</strong> Vous pouvez utiliser les notifications push.</div>');
        } else {
            document.write('<div class="error"><strong>❌ Service Workers NON supportés</strong> - Suivez les solutions ci-dessus.</div>');
        }
    </script>
</body>
</html>
