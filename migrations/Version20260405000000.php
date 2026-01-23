<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260405000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add simple skills tables';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE TABLE simple_skills (
              id BLOB NOT NULL,
              code VARCHAR(64) NOT NULL,
              name VARCHAR(120) NOT NULL,
              description CLOB NOT NULL,
              ultimate BOOLEAN NOT NULL,
              category VARCHAR(255) NOT NULL,
              ability VARCHAR(255) NOT NULL,
              aptitude VARCHAR(255) NOT NULL,
              limitations CLOB DEFAULT NULL,
              energy CLOB DEFAULT NULL,
              prerequisites CLOB DEFAULT NULL,
              timing CLOB DEFAULT NULL,
              range CLOB DEFAULT NULL,
              duration CLOB DEFAULT NULL,
              tags CLOB DEFAULT NULL,
              icon VARCHAR(255) DEFAULT NULL,
              created_at DATETIME NOT NULL,
              updated_at DATETIME NOT NULL,
              PRIMARY KEY (id)
            )
        SQL);
        $this->addSql('CREATE UNIQUE INDEX UNIQ_438AAC1477153098 ON simple_skills (code)');

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
        $this->addSql('CREATE UNIQUE INDEX simple_skills_lookup_unique_idx ON simple_skills_translations (locale, object_class, field, foreign_key)');
        $this->addSql('CREATE INDEX simple_skills_translations_lookup_idx ON simple_skills_translations (locale, object_class, field)');
        $this->addSql('CREATE INDEX simple_skills_translation_object_idx ON simple_skills_translations (object_class, foreign_key)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE simple_skills');
        $this->addSql('DROP TABLE simple_skills_translations');
    }
}
