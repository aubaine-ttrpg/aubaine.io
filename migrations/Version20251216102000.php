<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251216102000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add ritual flag to skills';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE skills ADD COLUMN ritual BOOLEAN NOT NULL DEFAULT 0');
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__skills AS
            SELECT
              id,
              code,
              name,
              description,
              category,
              type,
              abilities,
              range,
              duration,
              concentration,
              attack_roll,
              saving_throw,
              ability_check,
              source,
              verbal,
              somatic,
              material,
              material_string,
              tags,
              icon,
              created_at,
              updated_at,
              energy_cost
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
              category VARCHAR(255) NOT NULL,
              type VARCHAR(255) NOT NULL,
              abilities CLOB NOT NULL,
              range VARCHAR(255) NOT NULL,
              duration VARCHAR(255) NOT NULL,
              concentration BOOLEAN NOT NULL,
              attack_roll BOOLEAN NOT NULL,
              saving_throw BOOLEAN NOT NULL,
              ability_check BOOLEAN NOT NULL,
              source VARCHAR(255) NOT NULL,
              verbal BOOLEAN NOT NULL,
              somatic BOOLEAN NOT NULL,
              material BOOLEAN NOT NULL,
              material_string CLOB DEFAULT NULL,
              tags CLOB NOT NULL,
              icon VARCHAR(255) DEFAULT NULL,
              created_at DATETIME NOT NULL,
              updated_at DATETIME NOT NULL,
              energy_cost INTEGER NOT NULL,
              PRIMARY KEY (id)
            )
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO skills (
              id,
              code,
              name,
              description,
              category,
              type,
              abilities,
              range,
              duration,
              concentration,
              attack_roll,
              saving_throw,
              ability_check,
              source,
              verbal,
              somatic,
              material,
              material_string,
              tags,
              icon,
              created_at,
              updated_at,
              energy_cost
            )
            SELECT
              id,
              code,
              name,
              description,
              category,
              type,
              abilities,
              range,
              duration,
              concentration,
              attack_roll,
              saving_throw,
              ability_check,
              source,
              verbal,
              somatic,
              material,
              material_string,
              tags,
              icon,
              created_at,
              updated_at,
              energy_cost
            FROM
              __temp__skills
        SQL);
        $this->addSql('DROP TABLE __temp__skills');
    }
}

