<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260205000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Allow nullable action-only fields (energy, abilities, components) and backfill non-action skills.';
    }

    public function up(Schema $schema): void
    {
        // Rebuild skills table to allow nulls on action-only fields and clear them for non-action types.
        $this->addSql('CREATE TEMPORARY TABLE __temp__skills AS SELECT * FROM skills');
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
              CASE WHEN type IN ('action','bonus','reaction','attack') THEN energy_cost ELSE NULL END,
              ultimate,
              usage_limit_amount,
              usage_limit_period,
              category,
              type,
              CASE WHEN type IN ('action','bonus','reaction','attack') THEN abilities ELSE NULL END,
              range,
              duration,
              CASE WHEN type IN ('action','bonus','reaction','attack') THEN concentration ELSE NULL END,
              CASE WHEN type IN ('action','bonus','reaction','attack') THEN ritual ELSE NULL END,
              CASE WHEN type IN ('action','bonus','reaction','attack') THEN attack_roll ELSE NULL END,
              CASE WHEN type IN ('action','bonus','reaction','attack') THEN saving_throw ELSE NULL END,
              CASE WHEN type IN ('action','bonus','reaction','attack') THEN ability_check ELSE NULL END,
              source,
              CASE WHEN type IN ('action','bonus','reaction','attack') THEN materials ELSE NULL END,
              tags,
              icon,
              created_at,
              updated_at
            FROM __temp__skills
        SQL);
        $this->addSql('DROP TABLE __temp__skills');
    }

    public function down(Schema $schema): void
    {
        // Restore original NOT NULL definitions and coalesce empty values.
        $this->addSql('CREATE TEMPORARY TABLE __temp__skills AS SELECT * FROM skills');
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
              range VARCHAR(255) NOT NULL,
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
              COALESCE(energy_cost, 0),
              ultimate,
              usage_limit_amount,
              usage_limit_period,
              category,
              type,
              COALESCE(abilities, '["none"]'),
              range,
              duration,
              COALESCE(concentration, 0),
              COALESCE(ritual, 0),
              COALESCE(attack_roll, 0),
              COALESCE(saving_throw, 0),
              COALESCE(ability_check, 0),
              source,
              materials,
              tags,
              icon,
              created_at,
              updated_at
            FROM __temp__skills
        SQL);
        $this->addSql('DROP TABLE __temp__skills');
    }
}
