<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260921105025 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'StudyGroup.tutor — HEMIS tyutor-guruh biriktiruvi';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE study_group ADD tutor_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE study_group ADD CONSTRAINT FK_32BA1425208F64F1 FOREIGN KEY (tutor_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_32BA1425208F64F1 ON study_group (tutor_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE study_group DROP FOREIGN KEY FK_32BA1425208F64F1');
        $this->addSql('DROP INDEX IDX_32BA1425208F64F1 ON study_group');
        $this->addSql('ALTER TABLE study_group DROP tutor_id');
    }
}
