<?php

declare(strict_types=1);

namespace App\Component\Organization;

use App\Entity\Faculty;
use App\Entity\StudyGroup;
use App\Enum\StudyLanguage;
use DateTime;

final class OrganizationFactory
{
    public function createFaculty(string $name, ?string $externalId = null): Faculty
    {
        $faculty = new Faculty();
        $faculty->setName($name);
        $faculty->setExternalId($externalId);
        $faculty->setCreatedAt(new DateTime());

        return $faculty;
    }

    public function createStudyGroup(Faculty $faculty, string $name, StudyLanguage $language): StudyGroup
    {
        $group = new StudyGroup();
        $group->setFaculty($faculty);
        $group->setName($name);
        $group->setStudyLanguage($language);
        $group->setCreatedAt(new DateTime());

        return $group;
    }
}
