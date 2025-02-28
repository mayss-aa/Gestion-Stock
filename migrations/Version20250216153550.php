<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250216153550 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE machine CHANGE nom_machine nom_machine VARCHAR(30) NOT NULL, CHANGE etat_machine etat_machine VARCHAR(30) NOT NULL, CHANGE brand_machine brand_machine VARCHAR(30) NOT NULL');
        $this->addSql('ALTER TABLE maintenance CHANGE cout_maintenance cout_maintenance NUMERIC(10, 2) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE machine CHANGE nom_machine nom_machine VARCHAR(255) NOT NULL, CHANGE etat_machine etat_machine VARCHAR(255) NOT NULL, CHANGE brand_machine brand_machine VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE maintenance CHANGE cout_maintenance cout_maintenance DOUBLE PRECISION NOT NULL');
    }
}
