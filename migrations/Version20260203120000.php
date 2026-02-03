<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260203120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add skill trees, nodes, links, and translations.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE TABLE skill_trees (
              id BLOB NOT NULL,
              code VARCHAR(64) NOT NULL,
              name VARCHAR(120) NOT NULL,
              description CLOB DEFAULT NULL,
              columns SMALLINT NOT NULL,
              rows SMALLINT NOT NULL,
              created_at DATETIME NOT NULL,
              updated_at DATETIME NOT NULL,
              PRIMARY KEY (id)
            )
        SQL);
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4B0D65D977153098 ON skill_trees (code)');

        $this->addSql(<<<'SQL'
            CREATE TABLE skill_tree_nodes (
              id BLOB NOT NULL,
              tree_id BLOB NOT NULL,
              row SMALLINT NOT NULL,
              col SMALLINT NOT NULL,
              cost SMALLINT NOT NULL,
              is_starter BOOLEAN NOT NULL,
              skill_id BLOB DEFAULT NULL,
              anon_payload CLOB DEFAULT NULL,
              PRIMARY KEY (id),
              CONSTRAINT FK_A58D0E8FA09340EF FOREIGN KEY (tree_id) REFERENCES skill_trees (id) ON DELETE CASCADE,
              CONSTRAINT FK_A58D0E8F5585C142 FOREIGN KEY (skill_id) REFERENCES skills (id) ON DELETE SET NULL
            )
        SQL);
        $this->addSql('CREATE UNIQUE INDEX skill_tree_node_cell_unique ON skill_tree_nodes (tree_id, row, col)');
        $this->addSql('CREATE INDEX IDX_A58D0E8FA09340EF ON skill_tree_nodes (tree_id)');
        $this->addSql('CREATE INDEX IDX_A58D0E8F5585C142 ON skill_tree_nodes (skill_id)');

        $this->addSql(<<<'SQL'
            CREATE TABLE skill_tree_links (
              id BLOB NOT NULL,
              tree_id BLOB NOT NULL,
              from_node_id BLOB NOT NULL,
              to_node_id BLOB NOT NULL,
              is_directed BOOLEAN NOT NULL,
              PRIMARY KEY (id),
              CONSTRAINT FK_9C2C4C5EA09340EF FOREIGN KEY (tree_id) REFERENCES skill_trees (id) ON DELETE CASCADE,
              CONSTRAINT FK_9C2C4C5E6C2A83C3 FOREIGN KEY (from_node_id) REFERENCES skill_tree_nodes (id) ON DELETE CASCADE,
              CONSTRAINT FK_9C2C4C5E8C87ED8D FOREIGN KEY (to_node_id) REFERENCES skill_tree_nodes (id) ON DELETE CASCADE
            )
        SQL);
        $this->addSql('CREATE UNIQUE INDEX skill_tree_link_unique ON skill_tree_links (tree_id, from_node_id, to_node_id)');
        $this->addSql('CREATE INDEX IDX_9C2C4C5EA09340EF ON skill_tree_links (tree_id)');
        $this->addSql('CREATE INDEX IDX_9C2C4C5E6C2A83C3 ON skill_tree_links (from_node_id)');
        $this->addSql('CREATE INDEX IDX_9C2C4C5E8C87ED8D ON skill_tree_links (to_node_id)');

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
        $this->addSql('CREATE UNIQUE INDEX skill_tree_lookup_unique_idx ON skill_tree_translations (locale, object_class, field, foreign_key)');
        $this->addSql('CREATE INDEX skill_tree_translations_lookup_idx ON skill_tree_translations (locale, object_class, field)');
        $this->addSql('CREATE INDEX skill_tree_translation_object_idx ON skill_tree_translations (object_class, foreign_key)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE skill_tree_translations');
        $this->addSql('DROP TABLE skill_tree_links');
        $this->addSql('DROP TABLE skill_tree_nodes');
        $this->addSql('DROP TABLE skill_trees');
    }
}
