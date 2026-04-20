<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260204104409 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__old_skills_tags AS
            SELECT
              skills_id,
              tag_id
            FROM
              old_skills_tags
        SQL);
        $this->addSql('DROP TABLE old_skills_tags');
        $this->addSql(<<<'SQL'
            CREATE TABLE old_skills_tags (
              skills_id BLOB NOT NULL,
              tag_id INTEGER NOT NULL,
              PRIMARY KEY (skills_id, tag_id),
              CONSTRAINT FK_EBB0ED887FF61858 FOREIGN KEY (skills_id) REFERENCES old_skills (id) ON
              UPDATE
                NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE,
                CONSTRAINT FK_EBB0ED88BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON
              UPDATE
                NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE
            )
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO old_skills_tags (skills_id, tag_id)
            SELECT
              skills_id,
              tag_id
            FROM
              __temp__old_skills_tags
        SQL);
        $this->addSql('DROP TABLE __temp__old_skills_tags');
        $this->addSql('CREATE INDEX IDX_EBB0ED887FF61858 ON old_skills_tags (skills_id)');
        $this->addSql('CREATE INDEX IDX_EBB0ED88BAD26311 ON old_skills_tags (tag_id)');
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__skill_tree_links AS
            SELECT
              id,
              tree_id,
              from_node_id,
              to_node_id,
              is_directed
            FROM
              skill_tree_links
        SQL);
        $this->addSql('DROP TABLE skill_tree_links');
        $this->addSql(<<<'SQL'
            CREATE TABLE skill_tree_links (
              id BLOB NOT NULL,
              tree_id BLOB NOT NULL,
              from_node_id BLOB NOT NULL,
              to_node_id BLOB NOT NULL,
              is_directed BOOLEAN NOT NULL,
              PRIMARY KEY (id),
              CONSTRAINT FK_9C2C4C5EA09340EF FOREIGN KEY (tree_id) REFERENCES skill_trees (id) ON
              UPDATE
                NO ACTION ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE,
                CONSTRAINT FK_9C2C4C5E6C2A83C3 FOREIGN KEY (from_node_id) REFERENCES skill_tree_nodes (id) ON
              UPDATE
                NO ACTION ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE,
                CONSTRAINT FK_9C2C4C5E8C87ED8D FOREIGN KEY (to_node_id) REFERENCES skill_tree_nodes (id) ON
              UPDATE
                NO ACTION ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
            )
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO skill_tree_links (
              id, tree_id, from_node_id, to_node_id,
              is_directed
            )
            SELECT
              id,
              tree_id,
              from_node_id,
              to_node_id,
              is_directed
            FROM
              __temp__skill_tree_links
        SQL);
        $this->addSql('DROP TABLE __temp__skill_tree_links');
        $this->addSql('CREATE INDEX IDX_7C095B0F78B64A2 ON skill_tree_links (tree_id)');
        $this->addSql('CREATE INDEX IDX_7C095B0FC0537C78 ON skill_tree_links (from_node_id)');
        $this->addSql('CREATE INDEX IDX_7C095B0FC895A222 ON skill_tree_links (to_node_id)');
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__skill_tree_nodes AS
            SELECT
              id,
              tree_id,
              "row",
              col,
              cost,
              is_starter,
              skill_id,
              anon_payload
            FROM
              skill_tree_nodes
        SQL);
        $this->addSql('DROP TABLE skill_tree_nodes');
        $this->addSql(<<<'SQL'
            CREATE TABLE skill_tree_nodes (
              id BLOB NOT NULL,
              tree_id BLOB NOT NULL,
              "row" SMALLINT NOT NULL,
              col SMALLINT NOT NULL,
              cost SMALLINT NOT NULL,
              is_starter BOOLEAN NOT NULL,
              skill_id BLOB DEFAULT NULL,
              anon_payload CLOB DEFAULT NULL,
              PRIMARY KEY (id),
              CONSTRAINT FK_A58D0E8FA09340EF FOREIGN KEY (tree_id) REFERENCES skill_trees (id) ON
              UPDATE
                NO ACTION ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE,
                CONSTRAINT FK_A58D0E8F5585C142 FOREIGN KEY (skill_id) REFERENCES skills (id) ON
              UPDATE
                NO ACTION ON DELETE
              SET
                NULL NOT DEFERRABLE INITIALLY IMMEDIATE
            )
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO skill_tree_nodes (
              id, tree_id, "row", col, cost, is_starter,
              skill_id, anon_payload
            )
            SELECT
              id,
              tree_id,
              "row",
              col,
              cost,
              is_starter,
              skill_id,
              anon_payload
            FROM
              __temp__skill_tree_nodes
        SQL);
        $this->addSql('DROP TABLE __temp__skill_tree_nodes');
        $this->addSql('CREATE INDEX IDX_B0B6FFEB78B64A2 ON skill_tree_nodes (tree_id)');
        $this->addSql('CREATE INDEX IDX_B0B6FFEB5585C142 ON skill_tree_nodes (skill_id)');
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__skill_tree_translations AS
            SELECT
              id,
              locale,
              object_class,
              field,
              foreign_key,
              content
            FROM
              skill_tree_translations
        SQL);
        $this->addSql('DROP TABLE skill_tree_translations');
        $this->addSql(<<<'SQL'
            CREATE TABLE skill_tree_translations (
              id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
              locale VARCHAR(8) NOT NULL,
              object_class VARCHAR(191) NOT NULL,
              field VARCHAR(32) NOT NULL,
              foreign_key VARCHAR(64) NOT NULL,
              content CLOB DEFAULT NULL
            )
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO skill_tree_translations (
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
              __temp__skill_tree_translations
        SQL);
        $this->addSql('DROP TABLE __temp__skill_tree_translations');
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__skill_trees AS
            SELECT
              id,
              code,
              name,
              description,
              columns,
            rows
              ,
              created_at,
              updated_at
            FROM
              skill_trees
        SQL);
        $this->addSql('DROP TABLE skill_trees');
        $this->addSql(<<<'SQL'
            CREATE TABLE skill_trees (
              id BLOB NOT NULL,
              code VARCHAR(64) NOT NULL,
              name VARCHAR(120) NOT NULL,
              description CLOB DEFAULT NULL,
              columns SMALLINT NOT NULL,
              rows
                SMALLINT NOT NULL,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                PRIMARY KEY (id)
            )
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO skill_trees (
              id,
              code,
              name,
              description,
              columns,
              rows
                ,
                created_at,
                updated_at
            )
            SELECT
              id,
              code,
              name,
              description,
              columns,
            rows
              ,
              created_at,
              updated_at
            FROM
              __temp__skill_trees
        SQL);
        $this->addSql('DROP TABLE __temp__skill_trees');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B1F4FC9677153098 ON skill_trees (code)');
        $this->addSql("ALTER TABLE skills ADD COLUMN type VARCHAR(255) NOT NULL DEFAULT 'action'");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__old_skills_tags AS
            SELECT
              skills_id,
              tag_id
            FROM
              old_skills_tags
        SQL);
        $this->addSql('DROP TABLE old_skills_tags');
        $this->addSql(<<<'SQL'
            CREATE TABLE old_skills_tags (
              skills_id BLOB NOT NULL,
              tag_id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
              CONSTRAINT FK_EBB0ED887FF61858 FOREIGN KEY (skills_id) REFERENCES old_skills (id) NOT DEFERRABLE INITIALLY IMMEDIATE,
              CONSTRAINT FK_EBB0ED88BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) NOT DEFERRABLE INITIALLY IMMEDIATE
            )
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO old_skills_tags (skills_id, tag_id)
            SELECT
              skills_id,
              tag_id
            FROM
              __temp__old_skills_tags
        SQL);
        $this->addSql('DROP TABLE __temp__old_skills_tags');
        $this->addSql('CREATE INDEX IDX_EBB0ED887FF61858 ON old_skills_tags (skills_id)');
        $this->addSql('CREATE INDEX IDX_EBB0ED88BAD26311 ON old_skills_tags (tag_id)');
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__skill_tree_links AS
            SELECT
              id,
              is_directed,
              tree_id,
              from_node_id,
              to_node_id
            FROM
              skill_tree_links
        SQL);
        $this->addSql('DROP TABLE skill_tree_links');
        $this->addSql(<<<'SQL'
            CREATE TABLE skill_tree_links (
              id BLOB NOT NULL,
              is_directed BOOLEAN NOT NULL,
              tree_id BLOB NOT NULL,
              from_node_id BLOB NOT NULL,
              to_node_id BLOB NOT NULL,
              PRIMARY KEY (id),
              CONSTRAINT FK_7C095B0F78B64A2 FOREIGN KEY (tree_id) REFERENCES skill_trees (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE,
              CONSTRAINT FK_7C095B0FC0537C78 FOREIGN KEY (from_node_id) REFERENCES skill_tree_nodes (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE,
              CONSTRAINT FK_7C095B0FC895A222 FOREIGN KEY (to_node_id) REFERENCES skill_tree_nodes (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
            )
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO skill_tree_links (
              id, is_directed, tree_id, from_node_id,
              to_node_id
            )
            SELECT
              id,
              is_directed,
              tree_id,
              from_node_id,
              to_node_id
            FROM
              __temp__skill_tree_links
        SQL);
        $this->addSql('DROP TABLE __temp__skill_tree_links');
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX skill_tree_link_unique ON skill_tree_links (
              tree_id, from_node_id, to_node_id
            )
        SQL);
        $this->addSql('CREATE INDEX IDX_9C2C4C5E8C87ED8D ON skill_tree_links (to_node_id)');
        $this->addSql('CREATE INDEX IDX_9C2C4C5E6C2A83C3 ON skill_tree_links (from_node_id)');
        $this->addSql('CREATE INDEX IDX_9C2C4C5EA09340EF ON skill_tree_links (tree_id)');
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__skill_tree_nodes AS
            SELECT
              id,
              "row",
              col,
              cost,
              is_starter,
              anon_payload,
              tree_id,
              skill_id
            FROM
              skill_tree_nodes
        SQL);
        $this->addSql('DROP TABLE skill_tree_nodes');
        $this->addSql(<<<'SQL'
            CREATE TABLE skill_tree_nodes (
              id BLOB NOT NULL,
              "row" SMALLINT NOT NULL,
              col SMALLINT NOT NULL,
              cost SMALLINT NOT NULL,
              is_starter BOOLEAN NOT NULL,
              anon_payload CLOB DEFAULT NULL,
              tree_id BLOB NOT NULL,
              skill_id BLOB DEFAULT NULL,
              PRIMARY KEY (id),
              CONSTRAINT FK_B0B6FFEB78B64A2 FOREIGN KEY (tree_id) REFERENCES skill_trees (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE,
              CONSTRAINT FK_B0B6FFEB5585C142 FOREIGN KEY (skill_id) REFERENCES skills (id) ON DELETE
              SET
                NULL NOT DEFERRABLE INITIALLY IMMEDIATE
            )
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO skill_tree_nodes (
              id, "row", col, cost, is_starter, anon_payload,
              tree_id, skill_id
            )
            SELECT
              id,
              "row",
              col,
              cost,
              is_starter,
              anon_payload,
              tree_id,
              skill_id
            FROM
              __temp__skill_tree_nodes
        SQL);
        $this->addSql('DROP TABLE __temp__skill_tree_nodes');
        $this->addSql('CREATE UNIQUE INDEX skill_tree_node_cell_unique ON skill_tree_nodes (tree_id, "row", col)');
        $this->addSql('CREATE INDEX IDX_A58D0E8F5585C142 ON skill_tree_nodes (skill_id)');
        $this->addSql('CREATE INDEX IDX_A58D0E8FA09340EF ON skill_tree_nodes (tree_id)');
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__skill_tree_translations AS
            SELECT
              id,
              locale,
              object_class,
              field,
              foreign_key,
              content
            FROM
              skill_tree_translations
        SQL);
        $this->addSql('DROP TABLE skill_tree_translations');
        $this->addSql(<<<'SQL'
            CREATE TABLE skill_tree_translations (
              id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
              locale VARCHAR(8) NOT NULL,
              object_class VARCHAR(191) NOT NULL,
              field VARCHAR(32) NOT NULL,
              foreign_key VARCHAR(64) NOT NULL,
              content CLOB DEFAULT NULL
            )
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO skill_tree_translations (
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
              __temp__skill_tree_translations
        SQL);
        $this->addSql('DROP TABLE __temp__skill_tree_translations');
        $this->addSql(<<<'SQL'
            CREATE INDEX skill_tree_translation_object_idx ON skill_tree_translations (object_class, foreign_key)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX skill_tree_translations_lookup_idx ON skill_tree_translations (locale, object_class, field)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX skill_tree_lookup_unique_idx ON skill_tree_translations (
              locale, object_class, field, foreign_key
            )
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__skill_trees AS
            SELECT
              id,
              code,
              name,
              description,
              columns,
            rows
              ,
              created_at,
              updated_at
            FROM
              skill_trees
        SQL);
        $this->addSql('DROP TABLE skill_trees');
        $this->addSql(<<<'SQL'
            CREATE TABLE skill_trees (
              id BLOB NOT NULL,
              code VARCHAR(64) NOT NULL,
              name VARCHAR(120) NOT NULL,
              description CLOB DEFAULT NULL,
              columns SMALLINT NOT NULL,
              rows
                SMALLINT NOT NULL,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                PRIMARY KEY (id)
            )
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO skill_trees (
              id,
              code,
              name,
              description,
              columns,
              rows
                ,
                created_at,
                updated_at
            )
            SELECT
              id,
              code,
              name,
              description,
              columns,
            rows
              ,
              created_at,
              updated_at
            FROM
              __temp__skill_trees
        SQL);
        $this->addSql('DROP TABLE __temp__skill_trees');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4B0D65D977153098 ON skill_trees (code)');
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__skills AS
            SELECT
              id,
              code,
              name,
              description,
              ultimate,
              category,
              ability,
              aptitude,
              limitations,
              requirements,
              energy,
              prerequisites,
              timing,
            range
              ,
              duration,
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
              ultimate BOOLEAN NOT NULL,
              category VARCHAR(255) NOT NULL,
              ability VARCHAR(255) NOT NULL,
              aptitude VARCHAR(255) NOT NULL,
              limitations CLOB DEFAULT NULL,
              requirements CLOB DEFAULT NULL,
              energy CLOB DEFAULT NULL,
              prerequisites CLOB DEFAULT NULL,
              timing CLOB DEFAULT NULL,
              range
                CLOB DEFAULT NULL,
                duration CLOB DEFAULT NULL,
                tags CLOB DEFAULT NULL,
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
              ultimate,
              category,
              ability,
              aptitude,
              limitations,
              requirements,
              energy,
              prerequisites,
              timing,
              range
                ,
                duration,
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
              ultimate,
              category,
              ability,
              aptitude,
              limitations,
              requirements,
              energy,
              prerequisites,
              timing,
            range
              ,
              duration,
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
