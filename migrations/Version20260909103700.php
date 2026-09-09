<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Testni tahrirlashda savol/variant o'chirilsa, talabaning javob yozuvlari
 * FK'ni buzmasligi uchun ON DELETE CASCADE qo'yamiz.
 */
final class Version20260909103700 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'attempt_answer / attempt_answer_option FK ON DELETE CASCADE (test tahriri)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE attempt_answer DROP FOREIGN KEY FK_FEC920DC1E27F6BF');
        $this->addSql('ALTER TABLE attempt_answer ADD CONSTRAINT FK_FEC920DC1E27F6BF FOREIGN KEY (question_id) REFERENCES question (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE attempt_answer_option DROP FOREIGN KEY FK_4AC6600A9A3BC2B9');
        $this->addSql('ALTER TABLE attempt_answer_option ADD CONSTRAINT FK_4AC6600A9A3BC2B9 FOREIGN KEY (answer_option_id) REFERENCES answer_option (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE attempt_answer DROP FOREIGN KEY FK_FEC920DC1E27F6BF');
        $this->addSql('ALTER TABLE attempt_answer ADD CONSTRAINT FK_FEC920DC1E27F6BF FOREIGN KEY (question_id) REFERENCES question (id)');
        $this->addSql('ALTER TABLE attempt_answer_option DROP FOREIGN KEY FK_4AC6600A9A3BC2B9');
        $this->addSql('ALTER TABLE attempt_answer_option ADD CONSTRAINT FK_4AC6600A9A3BC2B9 FOREIGN KEY (answer_option_id) REFERENCES answer_option (id)');
    }
}
