<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260126153219 extends AbstractMigration
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
    }
}
