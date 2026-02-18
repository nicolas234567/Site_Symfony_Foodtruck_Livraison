# 🍔 Lens FoodTruck — Guide d'installation & d'intégration

## 📁 Structure du projet

```
lens-foodtruck/
├── config/
│   └── packages/
│       ├── security.yaml       ← Sécurité, rôles, access_control
│       └── doctrine.yaml       ← Config BDD
├── migrations/
│   └── Version20260101000000.php ← Migration SQL (toutes les tables)
├── src/
│   ├── Controller/
│   │   ├── ProduitController.php   ← CRUD produits
│   │   ├── CommandeController.php  ← Logique commandes
│   │   ├── AdminController.php     ← Dashboard admin
│   │   ├── ApiController.php       ← Routes /api/*
│   │   └── SecurityController.php  ← Login / Logout / Inscription
│   ├── Entity/
│   │   ├── User.php
│   │   ├── Produit.php
│   │   ├── Commande.php
│   │   └── LigneCommande.php
│   ├── Form/
│   │   ├── ProduitType.php
│   │   └── RegistrationFormType.php
│   └── Repository/             ← Requêtes BDD personnalisées
├── templates/
│   ├── base.html.twig           ← Layout Bootstrap
│   ├── produit/                 ← Vues CRUD produits
│   ├── commande/                ← Vues commandes
│   ├── admin/                   ← Dashboard admin
│   └── security/                ← Login / Inscription
├── .env                        ← Variables d'environnement
└── composer.json
```

---

## ⚙️ Prérequis

- **PHP 8.1+**
- **Composer**
- **MySQL 8.0+** (ou SQLite pour commencer plus simplement)
- **Symfony CLI** (recommandé) : https://symfony.com/download

---

## 🚀 Installation pas à pas

### Étape 1 — Créer le projet Symfony

```bash
# Depuis votre dossier de travail (ex: htdocs, www, ou un dossier dédié)
composer create-project symfony/skeleton:"6.4.*" lens-foodtruck
cd lens-foodtruck
```

### Étape 2 — Copier les fichiers du projet

Copiez tous les fichiers fournis dans leur dossier respectif.
> ⚠️ Ne remplacez PAS le fichier `symfony.lock` ni le dossier `vendor/`.

### Étape 3 — Installer les dépendances

```bash
composer require \
  doctrine/orm \
  doctrine/doctrine-bundle \
  doctrine/doctrine-migrations-bundle \
  symfony/security-bundle \
  symfony/form \
  symfony/validator \
  symfony/twig-bundle \
  twig/extra-bundle \
  symfony/asset \
  symfony/serializer

composer require --dev symfony/maker-bundle symfony/web-profiler-bundle
```

### Étape 4 — Configurer la base de données

Ouvrez le fichier `.env` et modifiez cette ligne :

```env
# Pour MySQL :
DATABASE_URL="mysql://VOTRE_USER:VOTRE_MOT_DE_PASSE@127.0.0.1:3306/lens_foodtruck?serverVersion=8.0"

# Pour SQLite (plus simple, idéal en cours) :
DATABASE_URL="sqlite:///%kernel.project_dir%/var/data.db"
```

### Étape 5 — Créer la BDD et les tables

```bash
# Créer la base de données
php bin/console doctrine:database:create

# Exécuter la migration (crée toutes les tables)
php bin/console doctrine:migrations:migrate

# OU en phase de développement (plus rapide) :
php bin/console doctrine:schema:create
```

### Étape 6 — Créer un compte admin

```bash
# Générer un hash de mot de passe
php bin/console security:hash-password

# Puis insérer en BDD via SQL :
# INSERT INTO user (email, roles, password, nom)
# VALUES ('admin@lens.fr', '["ROLE_ADMIN"]', 'LE_HASH_GÉNÉRÉ', 'Admin');
```

Ou via la page `/inscription` du site, puis modifiez le champ `roles` en BDD :
```sql
UPDATE user SET roles = '["ROLE_ADMIN"]' WHERE email = 'votre@email.fr';
```

### Étape 7 — Lancer le serveur

```bash
# Avec Symfony CLI (recommandé) :
symfony server:start

# Ou avec PHP natif :
php -S localhost:8000 -t public/
```

Ouvrez **http://localhost:8000** 🎉

---

## 🗺️ Routes disponibles

| URL | Accès | Description |
|-----|-------|-------------|
| `/` | Public | Redirige vers le menu |
| `/produits` | Public | Liste des produits (menu) |
| `/produits/nouveau` | Admin | Créer un produit |
| `/produits/{id}/modifier` | Admin | Modifier un produit |
| `/produits/{id}/supprimer` | Admin | Supprimer un produit |
| `/commandes` | Client | Mes commandes |
| `/commandes/nouvelle` | Client | Passer une commande |
| `/commandes/{id}` | Client/Admin | Détail d'une commande |
| `/admin` | Admin | Dashboard + CA du jour |
| `/admin/commande/{id}/statut` | Admin | Changer statut commande |
| `/connexion` | Public | Page de connexion |
| `/inscription` | Public | Créer un compte |
| `/deconnexion` | Connecté | Déconnexion |
| `/api/produits` | Public | JSON liste produits |
| `/api/commandes/jour` | Admin | JSON commandes + CA du jour |

---

## 🔐 Rôles et sécurité

| Rôle | Peut faire |
|------|-----------|
| `PUBLIC` | Voir le menu, s'inscrire, se connecter, GET /api/produits |
| `ROLE_USER` | Passer des commandes, voir SES commandes |
| `ROLE_ADMIN` | Tout + gérer produits, voir toutes les commandes, dashboard, API admin |

> **Important :** La sécurité est assurée côté serveur. Un client ne peut pas accéder aux commandes d'un autre client (vérification dans `CommandeController::show()`).

---

## 🧪 Tester les routes API

```bash
# Liste des produits (public)
curl http://localhost:8000/api/produits

# Commandes du jour (admin uniquement — avec session ou token)
curl http://localhost:8000/api/commandes/jour
```

---

## 🐛 Commandes utiles en développement

```bash
# Vider le cache
php bin/console cache:clear

# Voir toutes les routes
php bin/console debug:router

# Vérifier la config sécurité
php bin/console debug:firewall

# Recreer la BDD (⚠️ efface tout)
php bin/console doctrine:schema:drop --force
php bin/console doctrine:schema:create
```

---

## 📚 Correspondance avec le Roadmap (Séances)

| Séance | Fichiers concernés |
|--------|-------------------|
| **Séance 1** — Setup | `composer.json`, `base.html.twig`, `ProduitController.php`, `produit/index.html.twig` |
| **Séance 2** — Entités & CRUD | `Produit.php`, `Commande.php`, `LigneCommande.php`, `User.php`, `ProduitType.php`, CRUD dans `ProduitController.php` |
| **Séance 3** — Logique commande | `CommandeController.php`, `commande/new.html.twig`, `commande/show.html.twig`, `getTotal()` dans `Commande.php` |
| **Séance 4** — Sécurité | `security.yaml`, `SecurityController.php`, `IsGranted`, vérif dans `CommandeController::show()` |
| **Séance 5** — API & Stats | `ApiController.php`, `AdminController.php`, `CommandeRepository::findCommandesDuJour()`, `admin/dashboard.html.twig` |
