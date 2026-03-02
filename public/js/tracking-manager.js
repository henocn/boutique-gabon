/**
 * Gestionnaire centralisé de suivi (Facebook Pixel, Google Analytics, TikTok)
 * Gère l'initialisation, la mise en queue et le tracking d'événements
 */
class TrackingManager {
    constructor(config = {}) {
        this.config = {
            facebook: {
                enabled: true,
                pixels: ['1536994954069676', '1373481401089526'],
                timeout: 5000
            },
            googleAnalytics: {
                enabled: false,
                trackingId: null
            },
            tiktok: {
                enabled: false,
                pixelId: null
            },
            debug: false,
            ...config
        };
        this.isReady = false;
        this.pageViewTracked = false;
        this.eventQueue = [];
        this.scriptsLoaded = { facebook: false, googleAnalytics: false, tiktok: false };
        this.init();
    }

    async init() {
        try {
            if (this.config.facebook.enabled) await this.initFacebook();
            if (this.config.googleAnalytics.enabled) await this.initGoogleAnalytics();
            if (this.config.tiktok.enabled) await this.initTikTok();
            this.isReady = true;
            this.processQueue();
            this.ensurePageView();
        } catch (error) {
            console.error('[TrackingManager] Erreur initialisation:', error);
            this.isReady = true;
            this.processQueue();
            this.ensurePageView();
        }
    }

    async initFacebook() {
        try {
            if (window.fbEventsInitialized) {
                return;
            }
            window.fbEventsInitialized = true;

            // Snippet officiel Meta Pixel
            !(function(f,b,e,v,n,t,s){
                if(f.fbq) return; n=f.fbq=function(){ n.callMethod ?
                    n.callMethod.apply(n,arguments) : n.queue.push(arguments) };
                if(!f._fbq) f._fbq=n; n.push=n; n.loaded=!0; n.version='2.0';
                n.queue=[]; t=b.createElement(e); t.async=!0; t.src=v;
                s=b.getElementsByTagName(e)[0]; s.parentNode.insertBefore(t,s);
            })(window, document, 'script', 'https://connect.facebook.net/en_US/fbevents.js');

            window._fbq = window._fbq || window.fbq;

            // Initialiser les pixels
            if (!window.fbInitializedPixels) window.fbInitializedPixels = [];
            this.config.facebook.pixels.forEach(pixelId => {
                if (!window.fbInitializedPixels.includes(pixelId)) {
                    fbq('init', pixelId);
                    window.fbInitializedPixels.push(pixelId);
                    if (this.config.debug) console.log('[Facebook] Pixel initié:', pixelId);
                }
            });

            fbq('track', 'PageView');
            this.pageViewTracked = true;
            this.scriptsLoaded.facebook = true;
        } catch (error) {
            console.error('[Facebook] Erreur initialisation:', error);
        }
    }

    async initGoogleAnalytics() {
        // À implémenter si nécessaire
    }

    async initTikTok() {
        // À implémenter si nécessaire
    }

    ensurePageView() {
        if (this.pageViewTracked || !this.config.facebook.enabled) return;
        this.pageViewTracked = true;
        this.track('PageView', {}, ['facebook']);
    }

    track(eventName, eventData = {}, platforms = ['facebook']) {
        if (!this.isReady) {
            this.eventQueue.push({ eventName, eventData, platforms });
            return;
        }
        platforms.forEach(platform => {
            try {
                switch (platform) {
                    case 'facebook': this.trackFacebook(eventName, eventData); break;
                    case 'googleAnalytics': this.trackGoogleAnalytics(eventName, eventData); break;
                    case 'tiktok': this.trackTikTok(eventName, eventData); break;
                }
            } catch (error) {
                console.error(`[TrackingManager] Erreur tracking ${platform}:`, error);
            }
        });
    }

    trackFacebook(eventName, eventData) {
        if (!this.config.facebook.enabled) return;
        try {
            if (typeof fbq === 'function' && fbq.callMethod) {
                const standardEvents = ['PageView', 'Purchase', 'Lead', 'InitiateCheckout', 'ViewContent', 
                    'CompleteRegistration', 'AddToCart', 'AddToWishlist', 'Search', 'Subscribe'];
                if (standardEvents.includes(eventName)) {
                    fbq('track', eventName, eventData);
                } else {
                    fbq('trackCustom', eventName, eventData);
                }
                if (this.config.debug) console.log('[Facebook]', eventName, eventData);
            } else {
                this.trackFacebookViaImage(eventName, eventData);
            }
        } catch (error) {
            this.trackFacebookViaImage(eventName, eventData);
        }
    }

    trackFacebookViaImage(eventName, eventData) {
        this.config.facebook.pixels.forEach(pixelId => {
            try {
                const params = new URLSearchParams({
                    id: pixelId,
                    ev: eventName,
                    noscript: '1',
                    t: Date.now()
                });
                if (eventData.value) params.append('cd[value]', eventData.value);
                if (eventData.currency) params.append('cd[currency]', eventData.currency);
                const img = new Image();
                img.src = 'https://www.facebook.com/tr?' + params.toString();
            } catch (error) {}
        });
    }

    trackGoogleAnalytics(eventName, eventData) {
        if (!window.gtag || !this.config.googleAnalytics.enabled) return;
        gtag('event', eventName.toLowerCase(), eventData);
    }

    trackTikTok(eventName, eventData) {
        if (!window.ttq || !this.config.tiktok.enabled) return;
        ttq.track(eventName, eventData);
    }

    processQueue() {
        while (this.eventQueue.length > 0) {
            const event = this.eventQueue.shift();
            this.track(event.eventName, event.eventData, event.platforms);
        }
    }

    addFacebookPixel(pixelId) {
        if (!this.config.facebook.pixels.includes(pixelId)) {
            this.config.facebook.pixels.push(pixelId);
            if (typeof fbq === 'function') {
                if (!window.fbInitializedPixels) window.fbInitializedPixels = [];
                if (!window.fbInitializedPixels.includes(pixelId)) {
                    fbq('init', pixelId);
                    window.fbInitializedPixels.push(pixelId);
                }
            }
        }
    }

    removeFacebookPixel(pixelId) {
        const index = this.config.facebook.pixels.indexOf(pixelId);
        if (index > -1) this.config.facebook.pixels.splice(index, 1);
    }
}

// Créer l'instance globale si elle n'existe pas
if (!window.trackingManager) {
    window.trackingManager = new TrackingManager();
}

// Fonction globale de tracking
if (!window.trackEvent) {
    window.trackEvent = function(eventName, eventData = {}, platforms = ['facebook']) {
        if (window.trackingManager) {
            window.trackingManager.track(eventName, eventData, platforms);
        }
    };
}

// Utilitaire pour tracker avec tentatives (comme dans l'implémentation fournie)
function trackWhenReady(eventName, eventData, attempts) {
    const defaultAttemptsByEvent = {
        Purchase: 40,
        InitiateCheckout: 30,
        QualifiedVisit: 20,
        FormAbandoned: 20,
        FormStarted: 20,
        FormCompleted: 20,
        FormProgress25: 15,
        FormProgress50: 15,
        FormProgress75: 15,
        FormInactive: 15,
        FormFieldFocus: 10
    };
    const fallbackAttempts = 20;
    const remaining = typeof attempts === 'number' 
        ? attempts 
        : (defaultAttemptsByEvent[eventName] || fallbackAttempts);

    if (typeof trackEvent === 'function' && (!window.trackingManager || window.trackingManager.isReady)) {
        trackEvent(eventName, eventData);
        return;
    }

    if (remaining <= 0) return;

    setTimeout(function() {
        trackWhenReady(eventName, eventData, remaining - 1);
    }, 200);
}
