<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230721070326 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE categorie (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE formation (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE module (id INT AUTO_INCREMENT NOT NULL, categorie_id INT DEFAULT NULL, title VARCHAR(50) NOT NULL, INDEX IDX_C242628BCF5E72D (categorie_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE represent (id INT AUTO_INCREMENT NOT NULL, sessions_id INT DEFAULT NULL, modules_id INT DEFAULT NULL, days INT NOT NULL, INDEX IDX_C087204BF17C4D8C (sessions_id), INDEX IDX_C087204B60D6DC42 (modules_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE session (id INT AUTO_INCREMENT NOT NULL, formation_id INT DEFAULT NULL, name VARCHAR(50) NOT NULL, total_places INT NOT NULL, date_start DATE NOT NULL, date_end DATE NOT NULL, program LONGTEXT NOT NULL, INDEX IDX_D044D5D45200282E (formation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE stagiaire (id INT AUTO_INCREMENT NOT NULL, first_name VARCHAR(50) NOT NULL, name VARCHAR(50) NOT NULL, email VARCHAR(50) NOT NULL, date_birth DATE NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE stagiaire_session (stagiaire_id INT NOT NULL, session_id INT NOT NULL, INDEX IDX_D32D02D4BBA93DD6 (stagiaire_id), INDEX IDX_D32D02D4613FECDF (session_id), PRIMARY KEY(stagiaire_id, session_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE module ADD CONSTRAINT FK_C242628BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id)');
        $this->addSql('ALTER TABLE represent ADD CONSTRAINT FK_C087204BF17C4D8C FOREIGN KEY (sessions_id) REFERENCES session (id)');
        $this->addSql('ALTER TABLE represent ADD CONSTRAINT FK_C087204B60D6DC42 FOREIGN KEY (modules_id) REFERENCES module (id)');
        $this->addSql('ALTER TABLE session ADD CONSTRAINT FK_D044D5D45200282E FOREIGN KEY (formation_id) REFERENCES formation (id)');
        $this->addSql('ALTER TABLE stagiaire_session ADD CONSTRAINT FK_D32D02D4BBA93DD6 FOREIGN KEY (stagiaire_id) REFERENCES stagiaire (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE stagiaire_session ADD CONSTRAINT FK_D32D02D4613FECDF FOREIGN KEY (session_id) REFERENCES session (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE module DROP FOREIGN KEY FK_C242628BCF5E72D');
        $this->addSql('ALTER TABLE represent DROP FOREIGN KEY FK_C087204BF17C4D8C');
        $this->addSql('ALTER TABLE represent DROP FOREIGN KEY FK_C087204B60D6DC42');
        $this->addSql('ALTER TABLE session DROP FOREIGN KEY FK_D044D5D45200282E');
        $this->addSql('ALTER TABLE stagiaire_session DROP FOREIGN KEY FK_D32D02D4BBA93DD6');
        $this->addSql('ALTER TABLE stagiaire_session DROP FOREIGN KEY FK_D32D02D4613FECDF');
        $this->addSql('DROP TABLE categorie');
        $this->addSql('DROP TABLE formation');
        $this->addSql('DROP TABLE module');
        $this->addSql('DROP TABLE represent');
        $this->addSql('DROP TABLE session');
        $this->addSql('DROP TABLE stagiaire');
        $this->addSql('DROP TABLE stagiaire_session');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
