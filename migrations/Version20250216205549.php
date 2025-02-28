<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250216205549 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE categorie MODIFY id INT NOT NULL');
        $this->addSql('DROP INDEX `primary` ON categorie');
        $this->addSql('ALTER TABLE categorie ADD saison_de_recolte VARCHAR(50) DEFAULT NULL, ADD temperature_ideale VARCHAR(50) DEFAULT NULL, DROP id');
        $this->addSql('ALTER TABLE categorie ADD PRIMARY KEY (nom_categorie)');
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC27BCF5E72D');
        $this->addSql('DROP INDEX IDX_29A5EC27BCF5E72D ON produit');
        $this->addSql('ALTER TABLE produit ADD categorie_nom VARCHAR(255) NOT NULL, DROP categorie_id, CHANGE quantite_produit quantite_produit INT NOT NULL, CHANGE date_semis_prod date_semis_prod DATE DEFAULT NULL, CHANGE date_recolte_prod date_recolte_prod DATE DEFAULT NULL, CHANGE cree_le_prod cree_le_prod DATE DEFAULT NULL, CHANGE mis_a_jour_le_prod mis_a_jour_le_prod DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC2742D994B4 FOREIGN KEY (categorie_nom) REFERENCES categorie (nom_categorie)');
        $this->addSql('CREATE INDEX IDX_29A5EC2742D994B4 ON produit (categorie_nom)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE categorie ADD id INT AUTO_INCREMENT NOT NULL, DROP saison_de_recolte, DROP temperature_ideale, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC2742D994B4');
        $this->addSql('DROP INDEX IDX_29A5EC2742D994B4 ON produit');
        $this->addSql('ALTER TABLE produit ADD categorie_id INT DEFAULT NULL, DROP categorie_nom, CHANGE quantite_produit quantite_produit DOUBLE PRECISION NOT NULL, CHANGE date_semis_prod date_semis_prod DATE NOT NULL, CHANGE date_recolte_prod date_recolte_prod DATE NOT NULL, CHANGE cree_le_prod cree_le_prod DATETIME NOT NULL, CHANGE mis_a_jour_le_prod mis_a_jour_le_prod DATETIME NOT NULL');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC27BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id)');
        $this->addSql('CREATE INDEX IDX_29A5EC27BCF5E72D ON produit (categorie_id)');
    }
}
