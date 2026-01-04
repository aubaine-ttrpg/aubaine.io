<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260303000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add icon column to tag';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE tag ADD COLUMN icon VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // SQLite workaround: rebuild table without icon
        $this->addSql('CREATE TEMPORARY TABLE __temp__tag AS SELECT id, code, label, description, category, created_at, updated_at FROM tag');
        $this->addSql('DROP TABLE tag');
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
        $this->addSql('CREATE UNIQUE INDEX UNIQ_389B783177153098 ON tag (code)');
        $this->addSql('INSERT INTO tag (id, code, label, description, category, created_at, updated_at) SELECT id, code, label, description, category, created_at, updated_at FROM __temp__tag');
        $this->addSql('DROP TABLE __temp__tag');
    }
}
