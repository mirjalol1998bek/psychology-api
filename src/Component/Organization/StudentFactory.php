<?php

declare(strict_types=1);

namespace App\Component\Organization;

use App\Entity\StudyGroup;
use App\Entity\User;
use App\Enum\RoleEnum;
use App\Enum\StudyLanguage;
use DateTime;

final class StudentFactory
{
    private const EMAIL_DOMAIN = '@students.uzswlu.uz';

    public function create(
        string $fullName,
        ?string $hemisId,
        ?StudyGroup $studyGroup,
        ?StudyLanguage $studyLanguage,
    ): User {
        $identifier = $hemisId ?? uniqid('s');
        $student = new User();
        $student->setEmail($identifier . self::EMAIL_DOMAIN);
        $student->setHemisId($hemisId);
        $student->setFullName($fullName);
        $student->setStudyGroup($studyGroup);
        $student->setStudyLanguage($studyLanguage);
        $student->setRoles([RoleEnum::Student->value]);
        $student->setIsActive(true);
        $student->setCreatedAt(new DateTime());

        return $student;
    }
}
