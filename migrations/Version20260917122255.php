<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260917122255 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('DROP INDEX UNIQ_7411E77A12469DE2E9674A67752643C ON assessment_interpretation');
        $this->addSql('ALTER TABLE assessment_interpretation ADD subscale_key VARCHAR(8) DEFAULT \'\' NOT NULL');
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_7411E77A12469DE2B6FADDE9674A67752643C ON assessment_interpretation (
              category_id, subscale_key, result_key,
              study_language
            )
        SQL);
        $this->addSql('ALTER TABLE question ADD subscale_key VARCHAR(128) DEFAULT \'\' NOT NULL');
        $this->addSql('ALTER TABLE score_range ADD subscale_key VARCHAR(8) DEFAULT \'\' NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX UNIQ_7411E77A12469DE2B6FADDE9674A67752643C ON assessment_interpretation');
        $this->addSql('ALTER TABLE assessment_interpretation DROP subscale_key');
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_7411E77A12469DE2E9674A67752643C ON assessment_interpretation (
              category_id, result_key, study_language
            )
        SQL);
        $this->addSql('ALTER TABLE question DROP subscale_key');
        $this->addSql('ALTER TABLE score_range DROP subscale_key');
    }
}
