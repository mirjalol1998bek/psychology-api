<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Biriktirish (Assignment) o'chirilganda, talaba allaqachon test topshirgan
 * bo'lsa ham FK buzilmasin: attempt.assignment_id → ON DELETE SET NULL.
 * Urinish va natija saqlanadi.
 */
final class Version20260910050000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'attempt.assignment_id FK ON DELETE SET NULL (biriktirishni o\'chirish)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE attempt DROP FOREIGN KEY FK_18EC0266D19302F8');
        $this->addSql('ALTER TABLE attempt ADD CONSTRAINT FK_18EC0266D19302F8 FOREIGN KEY (assignment_id) REFERENCES assignment (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE attempt DROP FOREIGN KEY FK_18EC0266D19302F8');
        $this->addSql('ALTER TABLE attempt ADD CONSTRAINT FK_18EC0266D19302F8 FOREIGN KEY (assignment_id) REFERENCES assignment (id)');
    }
}
