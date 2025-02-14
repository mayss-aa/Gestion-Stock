<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250206131953 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE depot (id INT AUTO_INCREMENT NOT NULL, nom_depot VARCHAR(255) NOT NULL, localisation_depot VARCHAR(255) NOT NULL, capacite_depot DOUBLE PRECISION NOT NULL, unite_cap_depot VARCHAR(255) NOT NULL, type_stockage_depot VARCHAR(255) NOT NULL, statut_depot VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ressource (id INT AUTO_INCREMENT NOT NULL, depot_id INT DEFAULT NULL, nom_ressource VARCHAR(255) NOT NULL, type_ressource VARCHAR(255) NOT NULL, quantite_ressource DOUBLE PRECISION NOT NULL, unite_mesure VARCHAR(255) NOT NULL, date_ajout_ressource DATE NOT NULL, date_expiration_ressource DATE DEFAULT NULL, statut_ressource VARCHAR(255) NOT NULL, INDEX IDX_939F45448510D4DE (depot_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ressource ADD CONSTRAINT FK_939F45448510D4DE FOREIGN KEY (depot_id) REFERENCES depot (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ressource DROP FOREIGN KEY FK_939F45448510D4DE');
        $this->addSql('DROP TABLE depot');
        $this->addSql('DROP TABLE ressource');
    }
}
