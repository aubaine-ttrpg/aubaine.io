<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260301000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Introduce tag entity with ordered categories and pivot table, remove legacy skill tags column.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE TABLE tag (
              id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
              code VARCHAR(64) NOT NULL,
              label VARCHAR(120) NOT NULL,
              category INTEGER NOT NULL,
              created_at DATETIME NOT NULL,
              updated_at DATETIME NOT NULL
            )
        SQL);
        $this->addSql('CREATE UNIQUE INDEX UNIQ_389B783177153098 ON tag (code)');

        $this->addSql(<<<'SQL'
            CREATE TABLE tag_translations (
              id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
              locale VARCHAR(8) NOT NULL,
              object_class VARCHAR(191) NOT NULL,
              field VARCHAR(32) NOT NULL,
              foreign_key VARCHAR(64) NOT NULL,
              content CLOB DEFAULT NULL,
              CONSTRAINT tag_lookup_unique_idx UNIQUE (locale, object_class, field, foreign_key)
            )
        SQL);
        $this->addSql('CREATE INDEX tag_translations_lookup_idx ON tag_translations (locale, object_class, field)');
        $this->addSql('CREATE INDEX tag_translation_object_idx ON tag_translations (object_class, foreign_key)');

        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__skills AS
            SELECT
              id,
              code,
              name,
              description,
              energy_cost,
              ultimate,
              usage_limit_amount,
              usage_limit_period,
              category,
              type,
              abilities,
              range,
              duration,
              concentration,
              ritual,
              attack_roll,
              saving_throw,
              ability_check,
              source,
              materials,
              icon,
              created_at,
              updated_at
            FROM
              skills
        SQL);
        $this->addSql('DROP TABLE skills');
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
              range VARCHAR(255) NOT NULL,
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
            INSERT INTO skills (
              id,
              code,
              name,
              description,
              energy_cost,
              ultimate,
              usage_limit_amount,
              usage_limit_period,
              category,
              type,
              abilities,
              range,
              duration,
              concentration,
              ritual,
              attack_roll,
              saving_throw,
              ability_check,
              source,
              materials,
              icon,
              created_at,
              updated_at
            )
            SELECT
              id,
              code,
              name,
              description,
              energy_cost,
              ultimate,
              usage_limit_amount,
              usage_limit_period,
              category,
              type,
              abilities,
              range,
              duration,
              concentration,
              ritual,
              attack_roll,
              saving_throw,
              ability_check,
              source,
              materials,
              icon,
              created_at,
              updated_at
            FROM
              __temp__skills
        SQL);
        $this->addSql('DROP TABLE __temp__skills');

        $this->addSql(<<<'SQL'
            CREATE TABLE skills_tags (
              skills_id BLOB NOT NULL,
              tag_id INTEGER NOT NULL,
              PRIMARY KEY(skills_id, tag_id),
              CONSTRAINT FK_29D42A71C54C8C93 FOREIGN KEY (skills_id) REFERENCES skills (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE,
              CONSTRAINT FK_29D42A71BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
            )
        SQL);
        $this->addSql('CREATE INDEX IDX_29D42A71C54C8C93 ON skills_tags (skills_id)');
        $this->addSql('CREATE INDEX IDX_29D42A71BAD26311 ON skills_tags (tag_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE skills_tags');
        $this->addSql('DROP TABLE tag_translations');
        $this->addSql('DROP TABLE tag');

        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__skills AS
            SELECT
              id,
              code,
              name,
              description,
              energy_cost,
              ultimate,
              usage_limit_amount,
              usage_limit_period,
              category,
              type,
              abilities,
              range,
              duration,
              concentration,
              ritual,
              attack_roll,
              saving_throw,
              ability_check,
              source,
              materials,
              icon,
              created_at,
              updated_at
            FROM
              skills
        SQL);
        $this->addSql('DROP TABLE skills');
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
              range VARCHAR(255) NOT NULL,
              duration VARCHAR(255) NOT NULL,
              concentration BOOLEAN DEFAULT NULL,
              ritual BOOLEAN DEFAULT NULL,
              attack_roll BOOLEAN DEFAULT NULL,
              saving_throw BOOLEAN DEFAULT NULL,
              ability_check BOOLEAN DEFAULT NULL,
              source VARCHAR(255) NOT NULL,
              materials CLOB DEFAULT NULL,
              tags CLOB NOT NULL,
              icon VARCHAR(255) DEFAULT NULL,
              created_at DATETIME NOT NULL,
              updated_at DATETIME NOT NULL,
              PRIMARY KEY (id)
            )
        SQL);
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D531167077153098 ON skills (code)');
        $this->addSql(<<<'SQL'
            INSERT INTO skills (
              id,
              code,
              name,
              description,
              energy_cost,
              ultimate,
              usage_limit_amount,
              usage_limit_period,
              category,
              type,
              abilities,
              range,
              duration,
              concentration,
              ritual,
              attack_roll,
              saving_throw,
              ability_check,
              source,
              materials,
              tags,
              icon,
              created_at,
              updated_at
            )
            SELECT
              id,
              code,
              name,
              description,
              energy_cost,
              ultimate,
              usage_limit_amount,
              usage_limit_period,
              category,
              type,
              abilities,
              range,
              duration,
              concentration,
              ritual,
              attack_roll,
              saving_throw,
              ability_check,
              source,
              materials,
              '["none"]' AS tags,
              icon,
              created_at,
              updated_at
            FROM
              __temp__skills
        SQL);
        $this->addSql('DROP TABLE __temp__skills');
    }
}
