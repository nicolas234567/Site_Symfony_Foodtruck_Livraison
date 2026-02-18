<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260101000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Création initiale des tables : user, produit, commande, ligne_commande';
    }

    public function up(Schema $schema): void
    {
        // Table user
        $this->addSql('CREATE TABLE `user` (
            id INT AUTO_INCREMENT NOT NULL,
            email VARCHAR(180) NOT NULL,
            roles JSON NOT NULL,
            password VARCHAR(255) NOT NULL,
            nom VARCHAR(100) DEFAULT NULL,
            UNIQUE INDEX UNIQ_8D93D649E7927C74 (email),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        // Table produit
        $this->addSql('CREATE TABLE produit (
            id INT AUTO_INCREMENT NOT NULL,
            nom VARCHAR(255) NOT NULL,
            prix DOUBLE PRECISION NOT NULL,
            description LONGTEXT DEFAULT NULL,
            disponible TINYINT(1) DEFAULT 1,
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        // Table commande
        $this->addSql('CREATE TABLE commande (
            id INT AUTO_INCREMENT NOT NULL,
            client_id INT NOT NULL,
            date DATETIME NOT NULL,
            statut VARCHAR(20) NOT NULL,
            INDEX IDX_6EEAA67D19EB6921 (client_id),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        // Table ligne_commande
        $this->addSql('CREATE TABLE ligne_commande (
            id INT AUTO_INCREMENT NOT NULL,
            commande_id INT NOT NULL,
            produit_id INT NOT NULL,
            quantite INT NOT NULL,
            INDEX IDX_3170B74B82EA2E54 (commande_id),
            INDEX IDX_3170B74BF347EFB (produit_id),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        // Clés étrangères
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_commande_user
            FOREIGN KEY (client_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE ligne_commande ADD CONSTRAINT FK_lc_commande
            FOREIGN KEY (commande_id) REFERENCES commande (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ligne_commande ADD CONSTRAINT FK_lc_produit
            FOREIGN KEY (produit_id) REFERENCES produit (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE ligne_commande DROP FOREIGN KEY FK_lc_commande');
        $this->addSql('ALTER TABLE ligne_commande DROP FOREIGN KEY FK_lc_produit');
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_commande_user');
        $this->addSql('DROP TABLE ligne_commande');
        $this->addSql('DROP TABLE commande');
        $this->addSql('DROP TABLE produit');
        $this->addSql('DROP TABLE `user`');
    }
}
