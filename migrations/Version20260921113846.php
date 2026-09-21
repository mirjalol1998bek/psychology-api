<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260921113846 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'ObservationCard — 10-metodika (kuzatuv kartasi)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE observation_card (id INT AUTO_INCREMENT NOT NULL, scores JSON NOT NULL, total_score SMALLINT NOT NULL, risk_level VARCHAR(255) NOT NULL, alert_triggered TINYINT NOT NULL, created_at DATETIME NOT NULL, student_id INT NOT NULL, tutor_id INT NOT NULL, INDEX IDX_74412666208F64F1 (tutor_id), UNIQUE INDEX UNIQ_OBSERVATION_CARD_STUDENT (student_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE observation_card ADD CONSTRAINT FK_74412666CB944F1A FOREIGN KEY (student_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE observation_card ADD CONSTRAINT FK_74412666208F64F1 FOREIGN KEY (tutor_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE observation_card DROP FOREIGN KEY FK_74412666CB944F1A');
        $this->addSql('ALTER TABLE observation_card DROP FOREIGN KEY FK_74412666208F64F1');
        $this->addSql('DROP TABLE observation_card');
    }
}
