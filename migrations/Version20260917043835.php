<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260917043835 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'appeal: preferred_date/preferred_time + appointment_slot_id (ON DELETE SET NULL) — qabulga yozilish oqimi';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE appeal ADD preferred_date DATE DEFAULT NULL, ADD preferred_time VARCHAR(5) DEFAULT NULL, ADD appointment_slot_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE appeal ADD CONSTRAINT FK_96794351C8C623B4 FOREIGN KEY (appointment_slot_id) REFERENCES appointment_slot (id) ON DELETE SET NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_96794351C8C623B4 ON appeal (appointment_slot_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE appeal DROP FOREIGN KEY FK_96794351C8C623B4');
        $this->addSql('DROP INDEX UNIQ_96794351C8C623B4 ON appeal');
        $this->addSql('ALTER TABLE appeal DROP preferred_date, DROP preferred_time, DROP appointment_slot_id');
    }
}
