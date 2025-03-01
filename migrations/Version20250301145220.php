<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250301145220 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE statistique_depot (id INT AUTO_INCREMENT NOT NULL, depot_id INT NOT NULL, date DATETIME NOT NULL, taux_remplissage INT NOT NULL, INDEX IDX_E2A3595B8510D4DE (depot_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE statistique_depot ADD CONSTRAINT FK_E2A3595B8510D4DE FOREIGN KEY (depot_id) REFERENCES depot (id)');
        $this->addSql('ALTER TABLE ressource CHANGE date_ajout_ressource date_ajout_ressource DATE NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE statistique_depot DROP FOREIGN KEY FK_E2A3595B8510D4DE');
        $this->addSql('DROP TABLE statistique_depot');
        $this->addSql('ALTER TABLE ressource CHANGE date_ajout_ressource date_ajout_ressource DATE DEFAULT NULL');
    }
}
