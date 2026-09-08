<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260908120009 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'User.status (pending/active/rejected) — HEMIS kirish ruxsati';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user ADD status VARCHAR(16) DEFAULT \'active\' NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user DROP status');
    }
}
