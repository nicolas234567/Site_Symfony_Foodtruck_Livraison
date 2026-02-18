<?php
/**
 * FIXTURES — Données de test pour Lens FoodTruck
 *
 * Pour utiliser ces fixtures, installez le bundle :
 *   composer require --dev orm-fixtures
 *
 * Puis lancez :
 *   php bin/console doctrine:fixtures:load
 *
 * Ou sans fixtures bundle, copiez ce SQL directement dans votre BDD.
 */

/*
-- SQL équivalent pour insérer directement :

-- Admin (mot de passe : admin1234)
INSERT INTO `user` (email, roles, password, nom) VALUES
('admin@lens.fr', '["ROLE_ADMIN"]',
 '$2y$13$xxx_remplace_par_hash_réel', 'Admin Lens');

-- Client (mot de passe : client1234)
INSERT INTO `user` (email, roles, password, nom) VALUES
('client@test.fr', '["ROLE_USER"]',
 '$2y$13$xxx_remplace_par_hash_réel', 'Jean Dupont');

-- Produits
INSERT INTO produit (nom, prix, description, disponible) VALUES
('Burger Classic',    8.50,  'Pain brioché, steak haché, salade, tomate', 1),
('Burger Bacon',      9.90,  'Burger classique + bacon fumé',              1),
('Frites maison',     3.00,  'Frites fraîches assaisonnées',               1),
('Hot-Dog',           5.50,  'Saucisse de Francfort, pain, moutarde',      1),
('Coca-Cola 33cl',    2.50,  'Boisson gazeuse fraîche',                    1),
('Eau plate 50cl',    1.50,  NULL,                                          1);
*/

// Pour générer un vrai hash de mot de passe depuis la console Symfony :
// php bin/console security:hash-password
