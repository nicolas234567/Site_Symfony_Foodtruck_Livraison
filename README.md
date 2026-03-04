# 🍔 Lens FoodTruck — Symfony

Site de commande en ligne pour un food-truck.  
Interface client pour passer commande, interface admin pour gérer le menu, les commandes et le chiffre d'affaires du jour.

---

## ⚡ Installation rapide

### 1. Cloner / télécharger le projet

```bash
git clone https://github.com/ton-repo/Site_Symfony_Foodtruck_Livraison.git
cd Site_Symfony_Foodtruck_Livraison
```

### 2. Installer les dépendances PHP

```bash
composer install
```

### 3. Créer la base de données

La base de données est **SQLite** — aucune installation de serveur nécessaire.

```bash
php bin/console doctrine:database:create
php bin/console doctrine:schema:create
```

> La base est vide à la création. Tu devras créer ton compte via `/inscription` puis te passer admin (voir étape suivante).

### 4. Lancer le serveur

```bash
symfony server:start
```

Ou sans la CLI Symfony :

```bash
php -S localhost:8000 -t public/
```

Ouvrir **http://localhost:8000**

---

## 👤 Créer un compte et devenir admin

1. Va sur **http://localhost:8000/inscription** et crée ton compte
2. Dans un terminal, exécute cette commande en remplaçant l'email :

```bash
php bin/console doctrine:query:sql "UPDATE user SET roles = '[\"ROLE_ADMIN\"]' WHERE email = 'ton@email.fr'"
```

3. Déconnecte-toi et reconnecte-toi → le lien **Admin** apparaît dans la navbar

---

## 🔐 Rôles et accès

| Rôle | Permissions |
|------|-------------|
| Public | Voir le menu, s'inscrire, se connecter |
| `ROLE_USER` | Passer et consulter ses propres commandes |
| `ROLE_ADMIN` | Tout + gérer produits, toutes les commandes, dashboard stats |

---

## 📁 Structure du projet

```
src/
├── Controller/
│   ├── AdminController.php      ← Dashboard + changement de statut
│   ├── ProduitController.php    ← CRUD produits
│   ├── CommandeController.php   ← Créer / consulter commandes
│   ├── ApiController.php        ← GET /api/produits, /api/commandes/jour
│   └── SecurityController.php  ← Login / Inscription
├── Entity/                      ← User, Produit, Commande, LigneCommande
├── Form/                        ← ProduitType, RegistrationFormType
└── Repository/                  ← Requêtes DQL personnalisées

templates/
├── base.html.twig               ← Layout Bootstrap (navbar, footer)
├── admin/dashboard.html.twig    ← Stats du jour + gestion statuts
├── produit/                     ← Liste, création, édition produits
├── commande/                    ← Nouvelle commande, mes commandes, détail
└── security/                    ← Login, inscription

var/data.db                      ← Base SQLite (créée à l'étape 3)
```

---

## 🛠️ Stack technique

- **Symfony 6.4** — routing, controllers, Doctrine ORM, Security
- **Twig** — templates
- **SQLite** — base de données embarquée, aucune config serveur
- **Bootstrap 5** — UI responsive

---

## 🔌 API

| Route | Accès | Description |
|-------|-------|-------------|
| `GET /api/produits` | Public | Liste des produits disponibles |
| `GET /api/commandes/jour` | Admin | Commandes du jour + chiffre d'affaires |
