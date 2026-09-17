<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260917054854 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE
              student_passport
            ADD
              living_arrangement VARCHAR(16) DEFAULT NULL,
            ADD
              commute_minutes INT DEFAULT NULL,
            ADD
              family_type VARCHAR(24) DEFAULT NULL,
            ADD
              siblings_count INT DEFAULT NULL,
            ADD
              birth_order INT DEFAULT NULL,
            ADD
              father_info VARCHAR(255) DEFAULT NULL,
            ADD
              mother_info VARCHAR(255) DEFAULT NULL,
            ADD
              financial_status VARCHAR(16) DEFAULT NULL,
            ADD
              education_form VARCHAR(16) DEFAULT NULL,
            ADD
              work_status VARCHAR(16) DEFAULT NULL,
            ADD
              prior_education VARCHAR(255) DEFAULT NULL,
            ADD
              gpa_score VARCHAR(16) DEFAULT NULL,
            ADD
              language_level VARCHAR(128) DEFAULT NULL,
            ADD
              extracurricular LONGTEXT DEFAULT NULL,
            ADD
              leisure_activity LONGTEXT DEFAULT NULL,
            ADD
              health_limitations LONGTEXT DEFAULT NULL,
            ADD
              prior_psychologist_visit TINYINT DEFAULT NULL,
            ADD
              current_concern LONGTEXT DEFAULT NULL,
            DROP
              talents,
            DROP
              parents_info,
            DROP
              tutor_info,
            CHANGE
              phone personal_code VARCHAR(32) DEFAULT NULL,
            CHANGE
              living_environment gender VARCHAR(16) DEFAULT NULL,
            CHANGE
              current_address permanent_address VARCHAR(512) DEFAULT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE
              student_passport
            ADD
              living_environment VARCHAR(16) DEFAULT NULL,
            ADD
              talents LONGTEXT DEFAULT NULL,
            ADD
              parents_info LONGTEXT DEFAULT NULL,
            ADD
              tutor_info LONGTEXT DEFAULT NULL,
            DROP
              gender,
            DROP
              living_arrangement,
            DROP
              commute_minutes,
            DROP
              family_type,
            DROP
              siblings_count,
            DROP
              birth_order,
            DROP
              father_info,
            DROP
              mother_info,
            DROP
              financial_status,
            DROP
              education_form,
            DROP
              work_status,
            DROP
              prior_education,
            DROP
              gpa_score,
            DROP
              language_level,
            DROP
              extracurricular,
            DROP
              leisure_activity,
            DROP
              health_limitations,
            DROP
              prior_psychologist_visit,
            DROP
              current_concern,
            CHANGE
              permanent_address current_address VARCHAR(512) DEFAULT NULL,
            CHANGE
              personal_code phone VARCHAR(32) DEFAULT NULL
        SQL);
    }
}
