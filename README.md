# Site Symfony Foodtruck Livraison

Site web de commande en ligne pour un foodtruck, avec une interface client
pour passer commande et une interface admin pour gerer le menu et les livraisons.

---

## A quel besoin repond ce site

Les foodtrucks gèrent souvent leurs commandes a l'oral, ce qui entraine des erreurs
et aucune visibilite sur l'activite. Ce site centralise tout en un seul outil.

**Pour le client :**
- Consulter le menu et passer commande en ligne
- Suivre l'etat de sa commande (en attente, en preparation, prete)
- Consulter l'historique de ses commandes

**Pour le restaurateur :**
- Gerer ses produits (ajout, modification, suppression)
- Recevoir et suivre les commandes via une interface claire
- Consulter le nombre de commandes et le chiffre d'affaires du jour

---

## Prerequis

- **PHP 8.1+**
- **Composer**
- **MySQL 8.0+** ou SQLite (plus simple, ideal en dev)
- **Symfony CLI** (recommande) : https://symfony.com/download

---

## Installation & lancement

### 1 -- Cloner le projet

```
git clone https://github.com/nicolas234567/Site_Symfony_Foodtruck_Livraison.git
cd Site_Symfony_Foodtruck_Livraison
```

### 2 -- Installer les dependances PHP

```
composer install
```

### 3 -- Configurer la base de donnees

Ouvrir le fichier `.env` et modifier selon votre config :

```
# MySQL
DATABASE_URL="mysql://VOTRE_USER:VOTRE_MOT_DE_PASSE@127.0.0.1:3306/lens_foodtruck?serverVersion=8.0"

# SQLite (aucune installation requise)
DATABASE_URL="sqlite:///%kernel.project_dir%/var/data.db"
```

### 4 -- Creer la base de donnees et les tables

```
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

### 5 -- Creer un compte admin

Depuis `/inscription`, creer un compte puis passer le role en admin en BDD :

```sql
UPDATE user SET roles = '["ROLE_ADMIN"]' WHERE email = 'votre@email.fr';
```

### 6 -- Compiler les assets et lancer le serveur

```
npm install
npm run dev
symfony server:start
```

Ouvrir **http://localhost:8000**

---

## Stack technique

* **Symfony 6.4** -- framework PHP, routing, controllers, Doctrine ORM
* **Twig** -- moteur de templates
* **Doctrine / MySQL ou SQLite** -- base de donnees et migrations
* **Webpack Encore** -- compilation des assets JS et CSS

---

## Structure des fichiers

```
Site_Symfony_Foodtruck_Livraison/
├── assets/                        <- JS et CSS sources
├── config/
│   └── packages/
│       ├── security.yaml          <- Roles et access_control
│       └── doctrine.yaml          <- Config BDD
├── migrations/
│   └── Version20260101000000.php  <- Migration SQL (toutes les tables)
├── public/
│   ├── index.php                  <- Point d'entree web
│   └── uploads/produits/          <- Images uploadees
├── src/
│   ├── Controller/
│   │   ├── ProduitController.php  <- CRUD produits
│   │   ├── CommandeController.php <- Logique commandes
│   │   ├── ApiController.php      <- Routes /api/*
│   │   └── SecurityController.php <- Login / Logout / Inscription
│   ├── Entity/
│   │   ├── User.php
│   │   ├── Produit.php
│   │   ├── Commande.php
│   │   └── LigneCommande.php
│   ├── Form/
│   │   ├── ProduitType.php
│   │   └── RegistrationFormType.php
│   └── Repository/                <- Requetes BDD personnalisees
├── templates/
│   ├── base.html.twig             <- Layout principal
│   ├── produit/                   <- Vues CRUD produits
│   ├── commande/                  <- Vues commandes
│   ├── admin/                     <- Dashboard admin
│   └── security/                  <- Login / Inscription
├── .env                           <- Variables d'environnement (BDD...)
├── FIXTURES_INFO.php              <- Comptes de test
├── composer.json                  <- Dependances PHP
├── package.json                   <- Dependances JS
└── webpack.config.js              <- Configuration Webpack Encore
```

---

## Routes disponibles

| URL | Acces | Description |
|-----|-------|-------------|
| `/produits` | Public | Menu -- liste des produits |
| `/produits/nouveau` | Admin | Creer un produit |
| `/produits/{id}/modifier` | Admin | Modifier un produit |
| `/commandes` | Client | Mes commandes |
| `/commandes/nouvelle` | Client | Passer une commande |
| `/admin` | Admin | Dashboard + CA du jour |
| `/connexion` | Public | Page de connexion |
| `/inscription` | Public | Creer un compte |
| `/api/produits` | Public | JSON liste produits |
| `/api/commandes/jour` | Admin | JSON commandes du jour |

---

## Controles automatiques

- Impossible de passer une commande avec un nombre d'articles negatif ou superieur a 20

---

## Gestion des acces

| Role | Permissions |
|------|-------------|
| Public | Voir le menu, s'inscrire, se connecter |
| `ROLE_USER` | Passer des commandes, voir ses propres commandes |
| `ROLE_ADMIN` | Tout + gerer les produits, voir toutes les commandes, dashboard |

- Connexion obligatoire pour passer une commande
- Menus commandes et historique masques si non connecte
- Menu admin masque sans le role `ROLE_ADMIN`

---

## Commandes utiles en developpement

```
php bin/console cache:clear
php bin/console debug:router
php bin/console doctrine:schema:drop --force
php bin/console doctrine:schema:create
```
