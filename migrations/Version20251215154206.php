<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251215154206 extends AbstractMigration
{
  public function getDescription(): string
  {
    return '';
  }

  public function up(Schema $schema): void
  {
    // this up() migration is auto-generated, please modify it to your needs
    $this->addSql('ALTER TABLE skills ADD COLUMN energy_cost INTEGER NOT NULL DEFAULT 0');
  }

  public function down(Schema $schema): void
  {
    // this down() migration is auto-generated, please modify it to your needs
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
            range
              ,
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
              category VARCHAR(255) NOT NULL,
              type VARCHAR(255) NOT NULL,
              abilities CLOB NOT NULL,
              range
                VARCHAR(255) NOT NULL,
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
              range
                ,
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
                updated_at
            )
            SELECT
              id,
              code,
              name,
              description,
              category,
              type,
              abilities,
            range
              ,
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
              updated_at
            FROM
              __temp__skills
        SQL);
    $this->addSql('DROP TABLE __temp__skills');
  }
}
