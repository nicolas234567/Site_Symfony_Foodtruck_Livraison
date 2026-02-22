# Site Symfony Foodtruck Livraison

Site de commande en ligne pour un foodtruck -- interface client pour passer commande,
interface admin pour gerer le menu, les commandes et le chiffre d'affaires du jour.

---

## Installation & lancement
```
git clone https://github.com/nicolas234567/Site_Symfony_Foodtruck_Livraison.git
cd Site_Symfony_Foodtruck_Livraison
composer install
npm install && npm run dev
```

Configurer la BDD dans `.env` puis :
```
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
symfony server:start
```

Ouvrir **http://localhost:8000**

Creer un compte via `/inscription` puis passer admin en BDD :
```sql
UPDATE user SET roles = '["ROLE_ADMIN"]' WHERE email = 'votre@email.fr';
```

---

## Stack technique

* **Symfony 6.4** -- routing, controllers, Doctrine ORM
* **Twig** -- templates
* **MySQL / SQLite** -- base de donnees et migrations
* **Webpack Encore** -- assets JS et CSS

---

## Structure des fichiers
```
Site_Symfony_Foodtruck_Livraison/
├── src/
│   ├── Controller/
│   │   ├── ProduitController.php  <- CRUD produits
│   │   ├── CommandeController.php <- Logique commandes
│   │   ├── ApiController.php      <- Routes /api/*
│   │   └── SecurityController.php <- Login / Inscription
│   ├── Entity/                    <- User, Produit, Commande, LigneCommande
│   ├── Form/                      <- ProduitType, RegistrationFormType
│   └── Repository/                <- Requetes BDD personnalisees
├── templates/                     <- Vues Twig (produit, commande, admin, security)
├── migrations/                    <- Migration SQL
├── .env                           <- Config BDD et environnement
└── FIXTURES_INFO.php              <- Comptes de test
```

---

## Gestion des acces

| Role | Permissions |
|------|-------------|
| Public | Voir le menu, s'inscrire, se connecter |
| `ROLE_USER` | Passer et consulter ses commandes |
| `ROLE_ADMIN` | Tout + gerer produits, toutes les commandes, dashboard |
