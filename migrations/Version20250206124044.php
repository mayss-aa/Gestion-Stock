<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250206124044 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE intervention (id INT AUTO_INCREMENT NOT NULL, nom_intervention VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE plante (id INT AUTO_INCREMENT NOT NULL, nom_plante VARCHAR(255) NOT NULL, date_plantation DATE NOT NULL, rendement_estime DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE zone (id INT AUTO_INCREMENT NOT NULL, utilisateur_id INT DEFAULT NULL, plante_id INT DEFAULT NULL, intervention_id INT DEFAULT NULL, nom_zone VARCHAR(255) NOT NULL, superficie DOUBLE PRECISION NOT NULL, INDEX IDX_A0EBC007FB88E14F (utilisateur_id), INDEX IDX_A0EBC007177B16E8 (plante_id), INDEX IDX_A0EBC0078EAE3863 (intervention_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE zone ADD CONSTRAINT FK_A0EBC007FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE zone ADD CONSTRAINT FK_A0EBC007177B16E8 FOREIGN KEY (plante_id) REFERENCES plante (id)');
        $this->addSql('ALTER TABLE zone ADD CONSTRAINT FK_A0EBC0078EAE3863 FOREIGN KEY (intervention_id) REFERENCES intervention (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE zone DROP FOREIGN KEY FK_A0EBC007FB88E14F');
        $this->addSql('ALTER TABLE zone DROP FOREIGN KEY FK_A0EBC007177B16E8');
        $this->addSql('ALTER TABLE zone DROP FOREIGN KEY FK_A0EBC0078EAE3863');
        $this->addSql('DROP TABLE intervention');
        $this->addSql('DROP TABLE plante');
        $this->addSql('DROP TABLE zone');
    }
}
