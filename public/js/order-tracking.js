/**
 * Suivi des événements liés aux commandes (Order Tracking)
 * Intègre Facebook Pixel avec le système de commandes
 */

class OrderTracking {
    constructor(productId, productName, productPrice) {
        this.productId = productId;
        this.productName = productName;
        this.productPrice = productPrice;
        this.formStarted = false;
        this.formSubmitted = false;
        this.formStartTime = null;
        this.abandonTimer = null;
        this.formAbandonedSent = false;
        this.fieldsTracked = new Set();
        
        this.init();
    }

    init() {
        // Attendre que le DOM soit chargé
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.setupTracking());
        } else {
            this.setupTracking();
        }
    }

    setupTracking() {
        // Tracker QualifiedVisit après 5 secondes (page produit)
        setTimeout(() => {
            trackWhenReady('QualifiedVisit', {
                content_ids: [this.productId],
                content_name: this.productName,
                value: this.productPrice,
                currency: 'XOF'
            });
        }, 5000);

        // Configurer le suivi du formulaire du modal
        const orderForm = document.querySelector('form[action*="order-modal"], form[action*="order.modal"]');
        const orderModal = document.getElementById('orderModal');

        if (orderForm) {
            this.setupFormTracking(orderForm, orderModal);
        }
    }

    setupFormTracking(form, modal) {
        const formFields = form.querySelectorAll('input[type="text"], input[type="tel"], input[type="number"], textarea, select');
        let fieldsCompleted = 0;
        const totalFields = formFields.length;

        formFields.forEach((field, index) => {
            // Passer les champs cachés
            if (field.type === 'hidden') return;

            // Tracker le focus sur chaque champ
            field.addEventListener('focus', () => {
                const fieldName = field.name || field.id || `field_${index}`;
                
                // Ne tracker qu'une fois par champ
                if (!this.fieldsTracked.has(fieldName)) {
                    this.fieldsTracked.add(fieldName);
                    
                    trackWhenReady('FormFieldFocus', {
                        content_ids: [this.productId],
                        content_name: this.productName,
                        value: this.productPrice,
                        currency: 'XOF',
                        field_name: fieldName,
                        field_index: index
                    });
                }
            });

            // Tracker la progression du formulaire
            field.addEventListener('input', () => {
                if (!this.formStarted && field.value.length > 2) {
                    this.formStarted = true;
                    this.formStartTime = Date.now();

                    trackWhenReady('FormStarted', {
                        content_ids: [this.productId],
                        content_name: this.productName,
                        value: this.productPrice,
                        currency: 'XOF'
                    });

                    // Tracker FormInactive après 5 minutes sans interaction
                    this.abandonTimer = setTimeout(() => {
                        if (this.formStarted && !this.formSubmitted) {
                            trackWhenReady('FormInactive', {
                                content_ids: [this.productId],
                                content_name: this.productName,
                                value: this.productPrice,
                                currency: 'XOF',
                                time_spent: Math.round((Date.now() - this.formStartTime) / 1000)
                            });
                        }
                    }, 300000);
                }

                if (field.value.length > 2) {
                    const currentFieldsCompleted = Array.from(formFields).filter(f => f.value.length > 2 && f.type !== 'hidden').length;

                    if (currentFieldsCompleted > fieldsCompleted) {
                        fieldsCompleted = currentFieldsCompleted;
                        const progressPercent = Math.round((fieldsCompleted / totalFields) * 100);

                        // Tracker les jalons de progression (25%, 50%, 75%, 100%)
                        if (progressPercent === 25) {
                            trackWhenReady('FormProgress25', {
                                content_ids: [this.productId],
                                content_name: this.productName,
                                value: this.productPrice,
                                currency: 'XOF',
                                progress: 25
                            });
                        } else if (progressPercent === 50) {
                            trackWhenReady('FormProgress50', {
                                content_ids: [this.productId],
                                content_name: this.productName,
                                value: this.productPrice,
                                currency: 'XOF',
                                progress: 50
                            });
                        } else if (progressPercent === 75) {
                            trackWhenReady('FormProgress75', {
                                content_ids: [this.productId],
                                content_name: this.productName,
                                value: this.productPrice,
                                currency: 'XOF',
                                progress: 75
                            });
                        } else if (progressPercent === 100) {
                            trackWhenReady('FormCompleted', {
                                content_ids: [this.productId],
                                content_name: this.productName,
                                value: this.productPrice,
                                currency: 'XOF',
                                progress: 100
                            });
                        }
                    }
                }

                // Réinitialiser le timer d'inactivité
                if (this.abandonTimer) {
                    clearTimeout(this.abandonTimer);
                    this.abandonTimer = setTimeout(() => {
                        if (this.formStarted && !this.formSubmitted) {
                            trackWhenReady('FormInactive', {
                                content_ids: [this.productId],
                                content_name: this.productName,
                                value: this.productPrice,
                                currency: 'XOF',
                                time_spent: Math.round((Date.now() - this.formStartTime) / 1000)
                            });
                        }
                    }, 300000);
                }
            });
        });

        // Tracker l'abandon du formulaire
        if (modal) {
            modal.addEventListener('hidden.bs.modal', () => {
                this.sendFormAbandoned('modal_close');
            });
        }

        window.addEventListener('beforeunload', () => {
            this.sendFormAbandoned('page_leave');
        });

        document.addEventListener('visibilitychange', () => {
            if (document.visibilityState === 'hidden') {
                this.sendFormAbandoned('page_hide');
            }
        });

        // Tracker la soumission du formulaire (Purchase)
        form.addEventListener('submit', (e) => {
            this.formSubmitted = true;

            const quantity = form.querySelector('[name="quantity"]')?.value || 1;
            trackWhenReady('Purchase', {
                content_ids: [this.productId],
                content_type: 'product',
                contents: [{
                    id: this.productId,
                    quantity: parseInt(quantity),
                    item_price: this.productPrice
                }],
                currency: 'XOF',
                num_items: parseInt(quantity),
                value: this.productPrice * parseInt(quantity)
            });
        });
    }

    sendFormAbandoned(point) {
        if (!this.formStarted || this.formSubmitted || this.formAbandonedSent) return;
        this.formAbandonedSent = true;

        const timeSpent = this.formStartTime ? Math.round((Date.now() - this.formStartTime) / 1000) : 0;

        trackWhenReady('FormAbandoned', {
            content_ids: [this.productId],
            content_name: this.productName,
            value: this.productPrice,
            currency: 'XOF',
            time_spent: timeSpent,
            abandonment_point: point
        });
    }

    // Tracker InitiateCheckout au clic sur "Commander"
    static trackCheckoutInitiate(productId, productName, productPrice) {
        trackWhenReady('InitiateCheckout', {
            content_ids: [productId],
            contents: [{
                id: productId,
                quantity: 1,
                item_price: productPrice
            }],
            currency: 'XOF',
            num_items: 1,
            value: productPrice
        });
    }

    // Tracker ViewContent quand la page produit est visualisée
    static trackViewContent(productId, productName, productPrice, category = null) {
        trackWhenReady('ViewContent', {
            content_ids: [productId],
            content_name: productName,
            content_type: 'product',
            currency: 'XOF',
            value: productPrice,
            content_category: category
        });
    }
}

// Exporter pour utilisation globale
window.OrderTracking = OrderTracking;

// Initialiser le tracking automatiquement sur la page d'accueil
document.addEventListener('DOMContentLoaded', function() {
    var modal = document.getElementById('orderModal');
    if (!modal) return;

    // Initialiser le tracking quand le modal s'ouvre
    modal.addEventListener('show.bs.modal', function(event) {
        var button = event.relatedTarget;
        if (button && button.hasAttribute('data-product-id')) {
            var productId = button.getAttribute('data-product-id');
            var productName = button.getAttribute('data-product-name');
            var priceElement = button.closest('.product-card').querySelector('.fw-bold');
            var priceText = priceElement ? priceElement.textContent.trim() : '0';
            var price = parseInt(priceText.replace(/\s/g, '').replace(/FCFA/gi, '')) || 0;

            // Créer une nouvelle instance de tracking pour ce produit
            window.currentOrderTracking = new OrderTracking(productId, productName, price);
        }
    });
});

