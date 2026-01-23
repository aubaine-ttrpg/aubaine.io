<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260122135133 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE simple_skills ADD COLUMN icon VARCHAR(255) DEFAULT NULL');
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__simple_skills_translations AS
            SELECT
              id,
              locale,
              object_class,
              field,
              foreign_key,
              content
            FROM
              simple_skills_translations
        SQL);
        $this->addSql('DROP TABLE simple_skills_translations');
        $this->addSql(<<<'SQL'
            CREATE TABLE simple_skills_translations (
              id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
              locale VARCHAR(8) NOT NULL,
              object_class VARCHAR(191) NOT NULL,
              field VARCHAR(32) NOT NULL,
              foreign_key VARCHAR(64) NOT NULL,
              content CLOB DEFAULT NULL
            )
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO simple_skills_translations (
              id, locale, object_class, field, foreign_key,
              content
            )
            SELECT
              id,
              locale,
              object_class,
              field,
              foreign_key,
              content
            FROM
              __temp__simple_skills_translations
        SQL);
        $this->addSql('DROP TABLE __temp__simple_skills_translations');
        $this->addSql('CREATE TEMPORARY TABLE __temp__skills_tags AS SELECT skills_id, tag_id FROM skills_tags');
        $this->addSql('DROP TABLE skills_tags');
        $this->addSql(<<<'SQL'
            CREATE TABLE skills_tags (
              skills_id BLOB NOT NULL,
              tag_id INTEGER NOT NULL,
              PRIMARY KEY (skills_id, tag_id),
              CONSTRAINT FK_67DFB24C7FF61858 FOREIGN KEY (skills_id) REFERENCES skills (id) ON
              UPDATE
                NO ACTION ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE,
                CONSTRAINT FK_67DFB24CBAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON
              UPDATE
                NO ACTION ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
            )
        SQL);
        $this->addSql('INSERT INTO skills_tags (skills_id, tag_id) SELECT skills_id, tag_id FROM __temp__skills_tags');
        $this->addSql('DROP TABLE __temp__skills_tags');
        $this->addSql('CREATE INDEX IDX_67DFB24CBAD26311 ON skills_tags (tag_id)');
        $this->addSql('CREATE INDEX IDX_67DFB24C7FF61858 ON skills_tags (skills_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__simple_skills AS
            SELECT
              id,
              code,
              name,
              description,
              energy,
              prerequisites,
              timing,
            range
              ,
              duration,
              tags,
              created_at,
              updated_at
            FROM
              simple_skills
        SQL);
        $this->addSql('DROP TABLE simple_skills');
        $this->addSql(<<<'SQL'
            CREATE TABLE simple_skills (
              id BLOB NOT NULL,
              code VARCHAR(64) NOT NULL,
              name VARCHAR(120) NOT NULL,
              description CLOB NOT NULL,
              energy CLOB DEFAULT NULL,
              prerequisites CLOB DEFAULT NULL,
              timing CLOB DEFAULT NULL,
              range
                CLOB DEFAULT NULL,
                duration CLOB DEFAULT NULL,
                tags CLOB DEFAULT NULL,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                PRIMARY KEY (id)
            )
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO simple_skills (
              id,
              code,
              name,
              description,
              energy,
              prerequisites,
              timing,
              range
                ,
                duration,
                tags,
                created_at,
                updated_at
            )
            SELECT
              id,
              code,
              name,
              description,
              energy,
              prerequisites,
              timing,
            range
              ,
              duration,
              tags,
              created_at,
              updated_at
            FROM
              __temp__simple_skills
        SQL);
        $this->addSql('DROP TABLE __temp__simple_skills');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_438AAC1477153098 ON simple_skills (code)');
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__simple_skills_translations AS
            SELECT
              id,
              locale,
              object_class,
              field,
              foreign_key,
              content
            FROM
              simple_skills_translations
        SQL);
        $this->addSql('DROP TABLE simple_skills_translations');
        $this->addSql(<<<'SQL'
            CREATE TABLE simple_skills_translations (
              id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
              locale VARCHAR(8) NOT NULL,
              object_class VARCHAR(191) NOT NULL,
              field VARCHAR(32) NOT NULL,
              foreign_key VARCHAR(64) NOT NULL,
              content CLOB DEFAULT NULL
            )
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO simple_skills_translations (
              id, locale, object_class, field, foreign_key,
              content
            )
            SELECT
              id,
              locale,
              object_class,
              field,
              foreign_key,
              content
            FROM
              __temp__simple_skills_translations
        SQL);
        $this->addSql('DROP TABLE __temp__simple_skills_translations');
        $this->addSql(<<<'SQL'
            CREATE INDEX simple_skills_translation_object_idx ON simple_skills_translations (object_class, foreign_key)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX simple_skills_translations_lookup_idx ON simple_skills_translations (locale, object_class, field)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX simple_skills_lookup_unique_idx ON simple_skills_translations (
              locale, object_class, field, foreign_key
            )
        SQL);
        $this->addSql('CREATE TEMPORARY TABLE __temp__skills_tags AS SELECT skills_id, tag_id FROM skills_tags');
        $this->addSql('DROP TABLE skills_tags');
        $this->addSql(<<<'SQL'
            CREATE TABLE skills_tags (
              skills_id BLOB NOT NULL,
              tag_id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
              CONSTRAINT FK_67DFB24C7FF61858 FOREIGN KEY (skills_id) REFERENCES skills (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE,
              CONSTRAINT FK_67DFB24CBAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
            )
        SQL);
        $this->addSql('INSERT INTO skills_tags (skills_id, tag_id) SELECT skills_id, tag_id FROM __temp__skills_tags');
        $this->addSql('DROP TABLE __temp__skills_tags');
        $this->addSql('CREATE INDEX IDX_67DFB24C7FF61858 ON skills_tags (skills_id)');
        $this->addSql('CREATE INDEX IDX_67DFB24CBAD26311 ON skills_tags (tag_id)');
    }
}
