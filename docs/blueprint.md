# Blueprint - Boutique Gabon

## Architecture
- Public: routes publiques (home, categories, produit, panier, checkout).
- Admin/Manager: espace securise /dashboard.
- Separation: controllers public vs admin.
- Policies/Gates: controle des acces au niveau model.

Dossiers proposes:
- app/Http/Controllers/Public
- app/Http/Controllers/Admin
- app/Http/Controllers/Manager
- app/Policies
- app/Models
- resources/views/public
- resources/views/admin
- resources/views/components

## Modeles de donnees

### Categorie
- id
- name
- description
- image_path
- is_active
- created_at
- updated_at

### Produit
- id
- name
- description_html
- price_buy
- price_sell
- price_shipping
- category_id
- manager_id (user)
- stock
- status
- created_at
- updated_at

### ProduitImage
- id
- product_id
- path
- position (1-5)
- created_at
- updated_at

### Commande
- id
- client_name
- client_contact
- client_comment
- product_id
- status
- created_at
- updated_at

### User (Admin/Manager)
- id
- name
- email
- password
- role (admin|manager)
- is_active
- created_at
- updated_at

## Routes principales

### Public
- GET / -> HomeController@index
- GET /categories/{category} -> CategoryController@show
- GET /products/{product} -> ProductController@show
- GET /cart -> CartController@index
- POST /cart/add -> CartController@add
- POST /cart/remove -> CartController@remove
- POST /checkout -> CheckoutController@store

### Admin
- GET /dashboard -> Admin\DashboardController@index
- Resource /admin/categories
- Resource /admin/products
- Resource /admin/users
- GET /admin/orders -> Admin\OrderController@index
- PATCH /admin/orders/{order} -> Admin\OrderController@update

### Manager
- GET /manager/orders -> Manager\OrderController@index
- PATCH /manager/orders/{order} -> Manager\OrderController@update

## Permissions

Gates/Policies:
- ProductPolicy: view, create, update, delete (admin only), assign manager (admin).
- OrderPolicy: view (admin or manager assigne), update (admin or manager assigne).
- CategoryPolicy: admin only.
- UserPolicy: admin only.

Middleware:
- auth
- role:admin
- role:manager
- active

## UI Bootstrap (palette editable)

Palette (variables CSS):
- --brand-500: #f28c28
- --brand-600: #e5791f
- --brand-700: #c96514
- --ink-900: #1b1f24
- --ink-700: #3a3f46
- --ink-500: #6b7178
- --bg-100: #f8f6f2
- --bg-200: #f1ede7
- --card: #ffffff

Composants:
- Navbar public + admin
- Cards produit + categorie
- Badges statut
- Forms (input, select, textarea)
- Table list orders
- Alerts/Toast
- Pagination
- Carousel images (Bootstrap)

## Choix techniques
- Bootstrap via npm + Vite.
- Rich text: TinyMCE (free) en mode inline ou classique.
- Stockage images: public disk (storage/app/public).
- Panier en session (array de produits + qte).
- Validation via FormRequest.
- Policies pour securite logique.
