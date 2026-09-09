<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * StudyGroup.externalId unikal — HEMIS sinxronida guruh id bo'yicha
 * moslashtirish uchun. Bo'sh satrlar NULL ga o'tkaziladi (bir nechta NULL
 * unikal indeksda ruxsat etiladi).
 */
final class Version20260909121800 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'study_group.external_id UNIQUE (HEMIS sync)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("UPDATE study_group SET external_id = NULL WHERE external_id = ''");
        $this->addSql('CREATE UNIQUE INDEX UNIQ_STUDY_GROUP_EXTERNAL_ID ON study_group (external_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX UNIQ_STUDY_GROUP_EXTERNAL_ID ON study_group');
    }
}
