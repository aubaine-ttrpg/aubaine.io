<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251219090000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add ultimate flag and migrate previous ultimate category entries to general + ultimate=true';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE skills ADD COLUMN ultimate BOOLEAN NOT NULL DEFAULT 0");
        // Migrate existing ultimate category rows
        $this->addSql("UPDATE skills SET ultimate = 1, category = 'general' WHERE category = 'ultimate'");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("UPDATE skills SET category = 'ultimate' WHERE ultimate = 1");
        $this->addSql("ALTER TABLE skills DROP COLUMN ultimate");
    }
}

