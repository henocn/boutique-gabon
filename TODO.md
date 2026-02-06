# Boutique Gabon - TODO

## Phase 0 - Preparation
- [ ] Verifier la version PHP/Laravel et les extensions requises.
- [ ] Installer les dependances JS apres modification du package.json.
- [ ] Configurer le fichier .env (DB, APP_URL, MAIL, FILESYSTEM).

## Phase 1 - Socle UI (Bootstrap + palette)
- [ ] Installer Bootstrap via npm et configurer Vite.
- [ ] Definir les variables CSS de palette (accent orange pro, gris neutre).
- [ ] Mettre en place un layout Blade public + admin.
- [ ] Ajouter les composants UI de base (navbar, cards, badges, alerts, forms).

## Phase 2 - Auth + Roles
- [ ] Mettre en place l'auth (Admin/Manager) uniquement.
- [ ] Ajouter le champ role et statut actif a l'utilisateur.
- [ ] Creer policies/gates pour Admin/Manager.
- [ ] Ajouter middleware role + active pour proteger les routes admin.

## Phase 3 - Modele metier
- [ ] Creer migrations: categories, produits, images_produits, commandes.
- [ ] Creer models + relations Eloquent.
- [ ] Ajouter enums/constantes pour statuts (produit, commande, categorie).

## Phase 4 - Admin (CRUD)
- [ ] CRUD Categories (Admin only).
- [ ] CRUD Produits (Admin only) + assigner Manager.
- [ ] Gestion images produits (max 5) + stockage local/public.
- [ ] CRUD Utilisateurs (Admin only) + activer/desactiver.
- [ ] Profil (Admin/Manager) + changement mot de passe.

## Phase 5 - Manager
- [ ] Liste des commandes du Manager (filtre via produit assigne).
- [ ] Update statut commande avec historique (updated_at).

## Phase 6 - Public
- [ ] Page accueil (categories + produits).
- [ ] Page categorie (cards produits).
- [ ] Page produit (carousel images + recommandations).
- [ ] Recherche + filtre + tri.
- [ ] Panier en session (ajout/suppression/qte).
- [ ] Checkout (nom, contact, commentaire).

## Phase 7 - Qualite
- [ ] Seeds de base (roles, admin, managers, categories, produits).
- [ ] Tests basiques (permissions, panier, commandes visibles).
- [ ] Documentation courte (setup + conventions).
