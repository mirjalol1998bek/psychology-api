<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260908054554 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE answer_option (id INT AUTO_INCREMENT NOT NULL, text LONGTEXT NOT NULL, image_url VARCHAR(512) DEFAULT NULL, position SMALLINT DEFAULT 0 NOT NULL, score SMALLINT DEFAULT 0 NOT NULL, category_key VARCHAR(64) DEFAULT NULL, question_id INT NOT NULL, INDEX IDX_A87F3A171E27F6BF (question_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE appeal (id INT AUTO_INCREMENT NOT NULL, mode VARCHAR(16) NOT NULL, topic VARCHAR(16) NOT NULL, message LONGTEXT NOT NULL, wants_appointment TINYINT DEFAULT 0 NOT NULL, status VARCHAR(16) NOT NULL, reply LONGTEXT DEFAULT NULL, replied_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, student_id INT NOT NULL, replied_by_id INT DEFAULT NULL, INDEX IDX_96794351CB944F1A (student_id), INDEX IDX_96794351D6FBBEB5 (replied_by_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE appointment_slot (id INT AUTO_INCREMENT NOT NULL, date DATE NOT NULL, start_time VARCHAR(5) NOT NULL, end_time VARCHAR(5) DEFAULT NULL, status VARCHAR(16) NOT NULL, title VARCHAR(255) DEFAULT NULL, room VARCHAR(64) DEFAULT NULL, created_at DATETIME NOT NULL, psychologist_id INT NOT NULL, student_id INT DEFAULT NULL, INDEX IDX_BFCDE8A6FE8EF269 (psychologist_id), INDEX IDX_BFCDE8A6CB944F1A (student_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE assessment_interpretation (id INT AUTO_INCREMENT NOT NULL, result_key VARCHAR(64) NOT NULL, study_language VARCHAR(8) NOT NULL, title VARCHAR(255) DEFAULT NULL, text LONGTEXT NOT NULL, category_id INT NOT NULL, INDEX IDX_7411E77A12469DE2 (category_id), UNIQUE INDEX UNIQ_7411E77A12469DE2E9674A67752643C (category_id, result_key, study_language), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE assessment_result (id INT AUTO_INCREMENT NOT NULL, result_key VARCHAR(64) NOT NULL, label VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, score INT DEFAULT NULL, breakdown JSON NOT NULL, created_at DATETIME NOT NULL, attempt_id INT NOT NULL, UNIQUE INDEX UNIQ_E7B7507DB191BE6B (attempt_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE assignment (id INT AUTO_INCREMENT NOT NULL, start_at DATETIME NOT NULL, end_at DATETIME DEFAULT NULL, is_active TINYINT DEFAULT 1 NOT NULL, created_at DATETIME NOT NULL, category_id INT NOT NULL, study_group_id INT NOT NULL, INDEX IDX_30C544BA12469DE2 (category_id), INDEX IDX_30C544BA5DDDCCCE (study_group_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE attempt (id INT AUTO_INCREMENT NOT NULL, status VARCHAR(16) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, submitted_at DATETIME DEFAULT NULL, student_id INT NOT NULL, assignment_id INT DEFAULT NULL, quiz_id INT NOT NULL, INDEX IDX_18EC0266CB944F1A (student_id), INDEX IDX_18EC0266D19302F8 (assignment_id), INDEX IDX_18EC0266853CD175 (quiz_id), UNIQUE INDEX UNIQ_18EC0266CB944F1A853CD175 (student_id, quiz_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE attempt_answer (id INT AUTO_INCREMENT NOT NULL, text_value LONGTEXT DEFAULT NULL, attempt_id INT NOT NULL, question_id INT NOT NULL, INDEX IDX_FEC920DCB191BE6B (attempt_id), INDEX IDX_FEC920DC1E27F6BF (question_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE attempt_answer_option (attempt_answer_id INT NOT NULL, answer_option_id INT NOT NULL, INDEX IDX_4AC6600A95EE572C (attempt_answer_id), INDEX IDX_4AC6600A9A3BC2B9 (answer_option_id), PRIMARY KEY (attempt_answer_id, answer_option_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(128) NOT NULL, description LONGTEXT DEFAULT NULL, instrument_type VARCHAR(32) NOT NULL, position SMALLINT DEFAULT 0 NOT NULL, is_active TINYINT DEFAULT 1 NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE faculty (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, external_id VARCHAR(64) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_179660439F75D7B0 (external_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE notification (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(32) NOT NULL, title VARCHAR(255) NOT NULL, body LONGTEXT NOT NULL, link VARCHAR(255) DEFAULT NULL, is_read TINYINT DEFAULT 0 NOT NULL, created_at DATETIME NOT NULL, recipient_id INT NOT NULL, INDEX IDX_BF5476CAE92F8F78 (recipient_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE question (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(16) NOT NULL, text LONGTEXT NOT NULL, image_url VARCHAR(512) DEFAULT NULL, position SMALLINT DEFAULT 0 NOT NULL, is_reversed TINYINT DEFAULT 0 NOT NULL, quiz_id INT NOT NULL, INDEX IDX_B6F7494E853CD175 (quiz_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE quiz (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, study_language VARCHAR(8) NOT NULL, time_limit_minutes SMALLINT DEFAULT 0 NOT NULL, is_active TINYINT DEFAULT 1 NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, category_id INT NOT NULL, INDEX IDX_A412FA9212469DE2 (category_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE score_range (id INT AUTO_INCREMENT NOT NULL, min_score SMALLINT NOT NULL, max_score SMALLINT NOT NULL, result_key VARCHAR(64) NOT NULL, study_language VARCHAR(8) DEFAULT NULL, category_id INT NOT NULL, INDEX IDX_2ACB96C112469DE2 (category_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE student_passport (id INT AUTO_INCREMENT NOT NULL, birth_date DATE DEFAULT NULL, current_address VARCHAR(512) DEFAULT NULL, phone VARCHAR(32) DEFAULT NULL, family_status VARCHAR(16) DEFAULT NULL, living_environment VARCHAR(16) DEFAULT NULL, talents LONGTEXT DEFAULT NULL, parents_info LONGTEXT DEFAULT NULL, tutor_info LONGTEXT DEFAULT NULL, updated_at DATETIME DEFAULT NULL, student_id INT NOT NULL, UNIQUE INDEX UNIQ_6292293CB944F1A (student_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE study_group (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(64) NOT NULL, study_language VARCHAR(255) NOT NULL, external_id VARCHAR(64) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, faculty_id INT NOT NULL, INDEX IDX_32BA1425680CAB68 (faculty_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE answer_option ADD CONSTRAINT FK_A87F3A171E27F6BF FOREIGN KEY (question_id) REFERENCES question (id)');
        $this->addSql('ALTER TABLE appeal ADD CONSTRAINT FK_96794351CB944F1A FOREIGN KEY (student_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE appeal ADD CONSTRAINT FK_96794351D6FBBEB5 FOREIGN KEY (replied_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE appointment_slot ADD CONSTRAINT FK_BFCDE8A6FE8EF269 FOREIGN KEY (psychologist_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE appointment_slot ADD CONSTRAINT FK_BFCDE8A6CB944F1A FOREIGN KEY (student_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE assessment_interpretation ADD CONSTRAINT FK_7411E77A12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE assessment_result ADD CONSTRAINT FK_E7B7507DB191BE6B FOREIGN KEY (attempt_id) REFERENCES attempt (id)');
        $this->addSql('ALTER TABLE assignment ADD CONSTRAINT FK_30C544BA12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE assignment ADD CONSTRAINT FK_30C544BA5DDDCCCE FOREIGN KEY (study_group_id) REFERENCES study_group (id)');
        $this->addSql('ALTER TABLE attempt ADD CONSTRAINT FK_18EC0266CB944F1A FOREIGN KEY (student_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE attempt ADD CONSTRAINT FK_18EC0266D19302F8 FOREIGN KEY (assignment_id) REFERENCES assignment (id)');
        $this->addSql('ALTER TABLE attempt ADD CONSTRAINT FK_18EC0266853CD175 FOREIGN KEY (quiz_id) REFERENCES quiz (id)');
        $this->addSql('ALTER TABLE attempt_answer ADD CONSTRAINT FK_FEC920DCB191BE6B FOREIGN KEY (attempt_id) REFERENCES attempt (id)');
        $this->addSql('ALTER TABLE attempt_answer ADD CONSTRAINT FK_FEC920DC1E27F6BF FOREIGN KEY (question_id) REFERENCES question (id)');
        $this->addSql('ALTER TABLE attempt_answer_option ADD CONSTRAINT FK_4AC6600A95EE572C FOREIGN KEY (attempt_answer_id) REFERENCES attempt_answer (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE attempt_answer_option ADD CONSTRAINT FK_4AC6600A9A3BC2B9 FOREIGN KEY (answer_option_id) REFERENCES answer_option (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAE92F8F78 FOREIGN KEY (recipient_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE question ADD CONSTRAINT FK_B6F7494E853CD175 FOREIGN KEY (quiz_id) REFERENCES quiz (id)');
        $this->addSql('ALTER TABLE quiz ADD CONSTRAINT FK_A412FA9212469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE score_range ADD CONSTRAINT FK_2ACB96C112469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE student_passport ADD CONSTRAINT FK_6292293CB944F1A FOREIGN KEY (student_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE study_group ADD CONSTRAINT FK_32BA1425680CAB68 FOREIGN KEY (faculty_id) REFERENCES faculty (id)');
        $this->addSql('ALTER TABLE user ADD hemis_id VARCHAR(64) DEFAULT NULL, ADD full_name VARCHAR(255) DEFAULT NULL, ADD study_language VARCHAR(8) DEFAULT NULL, ADD image VARCHAR(512) DEFAULT NULL, ADD is_active TINYINT DEFAULT 1 NOT NULL, ADD study_group_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6495DDDCCCE FOREIGN KEY (study_group_id) REFERENCES study_group (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D6497F601DCD ON user (hemis_id)');
        $this->addSql('CREATE INDEX IDX_8D93D6495DDDCCCE ON user (study_group_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE answer_option DROP FOREIGN KEY FK_A87F3A171E27F6BF');
        $this->addSql('ALTER TABLE appeal DROP FOREIGN KEY FK_96794351CB944F1A');
        $this->addSql('ALTER TABLE appeal DROP FOREIGN KEY FK_96794351D6FBBEB5');
        $this->addSql('ALTER TABLE appointment_slot DROP FOREIGN KEY FK_BFCDE8A6FE8EF269');
        $this->addSql('ALTER TABLE appointment_slot DROP FOREIGN KEY FK_BFCDE8A6CB944F1A');
        $this->addSql('ALTER TABLE assessment_interpretation DROP FOREIGN KEY FK_7411E77A12469DE2');
        $this->addSql('ALTER TABLE assessment_result DROP FOREIGN KEY FK_E7B7507DB191BE6B');
        $this->addSql('ALTER TABLE assignment DROP FOREIGN KEY FK_30C544BA12469DE2');
        $this->addSql('ALTER TABLE assignment DROP FOREIGN KEY FK_30C544BA5DDDCCCE');
        $this->addSql('ALTER TABLE attempt DROP FOREIGN KEY FK_18EC0266CB944F1A');
        $this->addSql('ALTER TABLE attempt DROP FOREIGN KEY FK_18EC0266D19302F8');
        $this->addSql('ALTER TABLE attempt DROP FOREIGN KEY FK_18EC0266853CD175');
        $this->addSql('ALTER TABLE attempt_answer DROP FOREIGN KEY FK_FEC920DCB191BE6B');
        $this->addSql('ALTER TABLE attempt_answer DROP FOREIGN KEY FK_FEC920DC1E27F6BF');
        $this->addSql('ALTER TABLE attempt_answer_option DROP FOREIGN KEY FK_4AC6600A95EE572C');
        $this->addSql('ALTER TABLE attempt_answer_option DROP FOREIGN KEY FK_4AC6600A9A3BC2B9');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CAE92F8F78');
        $this->addSql('ALTER TABLE question DROP FOREIGN KEY FK_B6F7494E853CD175');
        $this->addSql('ALTER TABLE quiz DROP FOREIGN KEY FK_A412FA9212469DE2');
        $this->addSql('ALTER TABLE score_range DROP FOREIGN KEY FK_2ACB96C112469DE2');
        $this->addSql('ALTER TABLE student_passport DROP FOREIGN KEY FK_6292293CB944F1A');
        $this->addSql('ALTER TABLE study_group DROP FOREIGN KEY FK_32BA1425680CAB68');
        $this->addSql('DROP TABLE answer_option');
        $this->addSql('DROP TABLE appeal');
        $this->addSql('DROP TABLE appointment_slot');
        $this->addSql('DROP TABLE assessment_interpretation');
        $this->addSql('DROP TABLE assessment_result');
        $this->addSql('DROP TABLE assignment');
        $this->addSql('DROP TABLE attempt');
        $this->addSql('DROP TABLE attempt_answer');
        $this->addSql('DROP TABLE attempt_answer_option');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE faculty');
        $this->addSql('DROP TABLE notification');
        $this->addSql('DROP TABLE question');
        $this->addSql('DROP TABLE quiz');
        $this->addSql('DROP TABLE score_range');
        $this->addSql('DROP TABLE student_passport');
        $this->addSql('DROP TABLE study_group');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6495DDDCCCE');
        $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74 ON user');
        $this->addSql('DROP INDEX UNIQ_8D93D6497F601DCD ON user');
        $this->addSql('DROP INDEX IDX_8D93D6495DDDCCCE ON user');
        $this->addSql('ALTER TABLE user DROP hemis_id, DROP full_name, DROP study_language, DROP image, DROP is_active, DROP study_group_id');
    }
}
