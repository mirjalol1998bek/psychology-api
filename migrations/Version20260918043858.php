<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260918043858 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE
              assessment_interpretation
            CHANGE
              subscale_key subscale_key VARCHAR(32) DEFAULT '' NOT NULL
        SQL);
        $this->addSql('ALTER TABLE score_range CHANGE subscale_key subscale_key VARCHAR(32) DEFAULT \'\' NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE
              assessment_interpretation
            CHANGE
              subscale_key subscale_key VARCHAR(8) DEFAULT '' NOT NULL
        SQL);
        $this->addSql('ALTER TABLE score_range CHANGE subscale_key subscale_key VARCHAR(8) DEFAULT \'\' NOT NULL');
    }
}
