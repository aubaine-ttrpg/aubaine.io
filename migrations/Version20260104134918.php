<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260104134918 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE skills (
              id BLOB NOT NULL,
              code VARCHAR(64) NOT NULL,
              name VARCHAR(120) NOT NULL,
              description CLOB NOT NULL,
              energy_cost INTEGER DEFAULT NULL,
              ultimate BOOLEAN NOT NULL,
              usage_limit_amount INTEGER NOT NULL,
              usage_limit_period VARCHAR(255) NOT NULL,
              category VARCHAR(255) NOT NULL,
              type VARCHAR(255) NOT NULL,
              abilities CLOB DEFAULT NULL,
              range
                VARCHAR(255) NOT NULL,
                duration VARCHAR(255) NOT NULL,
                concentration BOOLEAN DEFAULT NULL,
                ritual BOOLEAN DEFAULT NULL,
                attack_roll BOOLEAN DEFAULT NULL,
                saving_throw BOOLEAN DEFAULT NULL,
                ability_check BOOLEAN DEFAULT NULL,
                source VARCHAR(255) NOT NULL,
                materials CLOB DEFAULT NULL,
                icon VARCHAR(255) DEFAULT NULL,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                PRIMARY KEY (id)
            )
        SQL);
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D531167077153098 ON skills (code)');
        $this->addSql(<<<'SQL'
            CREATE TABLE skills_tags (
              skills_id BLOB NOT NULL,
              tag_id INTEGER NOT NULL,
              PRIMARY KEY (skills_id, tag_id),
              CONSTRAINT FK_67DFB24C7FF61858 FOREIGN KEY (skills_id) REFERENCES skills (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE,
              CONSTRAINT FK_67DFB24CBAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
            )
        SQL);
        $this->addSql('CREATE INDEX IDX_67DFB24C7FF61858 ON skills_tags (skills_id)');
        $this->addSql('CREATE INDEX IDX_67DFB24CBAD26311 ON skills_tags (tag_id)');
        $this->addSql(<<<'SQL'
            CREATE TABLE skills_translations (
              id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
              locale VARCHAR(8) NOT NULL,
              object_class VARCHAR(191) NOT NULL,
              field VARCHAR(32) NOT NULL,
              foreign_key VARCHAR(64) NOT NULL,
              content CLOB DEFAULT NULL
            )
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE tag (
              id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
              code VARCHAR(64) NOT NULL,
              label VARCHAR(120) NOT NULL,
              description CLOB DEFAULT NULL,
              category INTEGER NOT NULL,
              created_at DATETIME NOT NULL,
              updated_at DATETIME NOT NULL
            )
        SQL);
        $this->addSql('CREATE UNIQUE INDEX UNIQ_389B78377153098 ON tag (code)');
        $this->addSql(<<<'SQL'
            CREATE TABLE tag_translations (
              id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
              locale VARCHAR(8) NOT NULL,
              object_class VARCHAR(191) NOT NULL,
              field VARCHAR(32) NOT NULL,
              foreign_key VARCHAR(64) NOT NULL,
              content CLOB DEFAULT NULL
            )
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE messenger_messages (
              id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
              body CLOB NOT NULL,
              headers CLOB NOT NULL,
              queue_name VARCHAR(190) NOT NULL,
              created_at DATETIME NOT NULL,
              available_at DATETIME NOT NULL,
              delivered_at DATETIME DEFAULT NULL
            )
        SQL);
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE skills');
        $this->addSql('DROP TABLE skills_tags');
        $this->addSql('DROP TABLE skills_translations');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE tag_translations');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
