<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260331074158 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE voyages (id INT AUTO_INCREMENT NOT NULL, destination VARCHAR(255) NOT NULL, dateDebut DATE NOT NULL, dateFin DATE NOT NULL, prix DOUBLE PRECISION NOT NULL, imagePath VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, pavs_depart VARCHAR(100) DEFAULT NULL, destination_id INT DEFAULT NULL, INDEX IDX_30F7F9816C6140 (destination_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE voyages ADD CONSTRAINT FK_30F7F9816C6140 FOREIGN KEY (destination_id) REFERENCES destination (id)');
        $this->addSql('ALTER TABLE voyage DROP FOREIGN KEY `voyage_ibfk_1`');
        $this->addSql('DROP TABLE voyage');
        $this->addSql('ALTER TABLE destination ADD code_iata VARCHAR(3) DEFAULT NULL, ADD image_url VARCHAR(500) DEFAULT NULL, ADD video_url VARCHAR(500) DEFAULT NULL, ADD `order` INT DEFAULT NULL, CHANGE nom nom VARCHAR(100) NOT NULL, CHANGE pays pays VARCHAR(100) NOT NULL, CHANGE description description LONGTEXT DEFAULT NULL, CHANGE date_creation date_creation DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE voyage (id INT AUTO_INCREMENT NOT NULL, destination_id INT DEFAULT NULL, destination VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_general_ci`, pays_depart VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_general_ci`, dateDebut DATE DEFAULT \'NULL\', dateFin DATE DEFAULT \'NULL\', prix NUMERIC(10, 2) DEFAULT \'NULL\', description TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, INDEX destination_id (destination_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE voyage ADD CONSTRAINT `voyage_ibfk_1` FOREIGN KEY (destination_id) REFERENCES destination (id)');
        $this->addSql('ALTER TABLE voyages DROP FOREIGN KEY FK_30F7F9816C6140');
        $this->addSql('DROP TABLE voyages');
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('ALTER TABLE destination DROP code_iata, DROP image_url, DROP video_url, DROP `order`, CHANGE nom nom VARCHAR(255) NOT NULL, CHANGE pays pays VARCHAR(255) NOT NULL, CHANGE description description TEXT DEFAULT NULL, CHANGE date_creation date_creation DATETIME DEFAULT \'current_timestamp()\'');
    }
}
