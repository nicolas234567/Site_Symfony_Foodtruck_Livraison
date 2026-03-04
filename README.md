# Site_Symfony_Foodtruck_Livraison

Application web de gestion de commandes pour food-truck, développée avec Symfony 6.4.  
Comprend une interface client pour passer commande et une interface d'administration pour gérer le menu, les commandes et le chiffre d'affaires du jour.

---

## Installation

### 1. Cloner le projet

```bash
git clone https://github.com/nicolas234567/Site_Symfony_Foodtruck_Livraison.git
cd Site_Symfony_Foodtruck_Livraison
```

### 2. Installer les dépendances

```bash
composer install
```

### 3. Créer la base de données

Le projet utilise **SQLite** — aucune installation de serveur de base de données n'est requise.

```bash
php bin/console doctrine:database:create
php bin/console doctrine:schema:create
```

### 4. Lancer le serveur

```bash
symfony server:start
```

Sans la CLI Symfony :

```bash
php -S localhost:8000 -t public/
```

L'application est accessible sur **http://localhost:8000**

---

## Création du compte administrateur

1. Créer un compte sur **http://localhost:8000/inscription**
2. Exécuter la commande suivante en remplaçant l'adresse e-mail :

```bash
php bin/console doctrine:query:sql "UPDATE user SET roles = '[\"ROLE_ADMIN\"]' WHERE email = 'adresse@email.fr'"
```

3. Se déconnecter puis se reconnecter — le lien **Admin** apparaît dans la barre de navigation

---

## Gestion des accès

| Rôle | Permissions |
|------|-------------|
| Public | Consulter le menu, s'inscrire, se connecter |
| `ROLE_USER` | Passer et consulter ses propres commandes |
| `ROLE_ADMIN` | Toutes les permissions + gestion des produits, suivi des commandes, dashboard |

---

## Structure du projet

```
src/
├── Controller/
│   ├── AdminController.php      — Dashboard et changement de statut des commandes
│   ├── ProduitController.php    — CRUD produits
│   ├── CommandeController.php   — Création et consultation des commandes
│   ├── ApiController.php        — Endpoints /api/produits et /api/commandes/jour
│   └── SecurityController.php  — Authentification et inscription
├── Entity/                      — User, Produit, Commande, LigneCommande
├── Form/                        — ProduitType, RegistrationFormType
└── Repository/                  — Requêtes DQL personnalisées

templates/
├── base.html.twig               — Layout principal (navbar, footer)
├── admin/dashboard.html.twig    — Statistiques du jour et gestion des statuts
├── produit/                     — Liste, création et édition des produits
├── commande/                    — Nouvelle commande, historique, détail
└── security/                    — Connexion et inscription
```

---

## Stack technique

- **Symfony 6.4** — routing, controllers, Doctrine ORM, Security
- **Twig** — moteur de templates
- **SQLite** — base de données embarquée, sans configuration serveur
- **Bootstrap 5** — interface responsive via CDN

---

## API

| Route | Accès | Description |
|-------|-------|-------------|
| `GET /api/produits` | Public | Liste des produits disponibles |
| `GET /api/commandes/jour` | Admin | Commandes du jour et chiffre d'affaires |
