<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260127120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Rename skill categories: common -> basic, general -> common.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("UPDATE skills SET category = 'basic' WHERE category = 'common'");
        $this->addSql("UPDATE skills SET category = 'common' WHERE category = 'general'");
        $this->addSql("UPDATE old_skills SET category = 'basic' WHERE category = 'common'");
        $this->addSql("UPDATE old_skills SET category = 'common' WHERE category = 'general'");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("UPDATE skills SET category = 'general' WHERE category = 'common'");
        $this->addSql("UPDATE skills SET category = 'common' WHERE category = 'basic'");
        $this->addSql("UPDATE old_skills SET category = 'general' WHERE category = 'common'");
        $this->addSql("UPDATE old_skills SET category = 'common' WHERE category = 'basic'");
    }
}
