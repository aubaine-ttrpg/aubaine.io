<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251215095001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE skills (id BLOB NOT NULL, code VARCHAR(64) NOT NULL, name VARCHAR(120) NOT NULL, description CLOB NOT NULL, category VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, abilities CLOB NOT NULL, range VARCHAR(255) NOT NULL, duration VARCHAR(255) NOT NULL, concentration BOOLEAN NOT NULL, attack_roll BOOLEAN NOT NULL, saving_throw BOOLEAN NOT NULL, ability_check BOOLEAN NOT NULL, source VARCHAR(255) NOT NULL, verbal BOOLEAN NOT NULL, somatic BOOLEAN NOT NULL, material BOOLEAN NOT NULL, material_string CLOB DEFAULT NULL, tags CLOB NOT NULL, icon VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE TABLE messenger_messages (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, body CLOB NOT NULL, headers CLOB NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL)');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE skills');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
