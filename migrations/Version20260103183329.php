<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260103183329 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
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
            range
              ,
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
                tags CLOB NOT NULL,
                icon VARCHAR(255) DEFAULT NULL,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                PRIMARY KEY (id)
            )
        SQL);
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
              range
                ,
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
            range
              ,
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
            FROM
              __temp__skills
        SQL);
        $this->addSql('DROP TABLE __temp__skills');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D531167077153098 ON skills (code)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE skills_translations');
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
            range
              ,
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
              energy_cost INTEGER NOT NULL,
              ultimate BOOLEAN NOT NULL,
              usage_limit_amount INTEGER NOT NULL,
              usage_limit_period VARCHAR(255) NOT NULL,
              category VARCHAR(255) NOT NULL,
              type VARCHAR(255) NOT NULL,
              abilities CLOB NOT NULL,
              range
                VARCHAR(255) NOT NULL,
                duration VARCHAR(255) NOT NULL,
                concentration BOOLEAN NOT NULL,
                ritual BOOLEAN NOT NULL,
                attack_roll BOOLEAN NOT NULL,
                saving_throw BOOLEAN NOT NULL,
                ability_check BOOLEAN NOT NULL,
                source VARCHAR(255) NOT NULL,
                materials CLOB DEFAULT NULL,
                tags CLOB NOT NULL,
                icon VARCHAR(255) DEFAULT NULL,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                PRIMARY KEY (id)
            )
        SQL);
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
              range
                ,
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
            range
              ,
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
            FROM
              __temp__skills
        SQL);
        $this->addSql('DROP TABLE __temp__skills');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D531167077153098 ON skills (code)');
    }
}
