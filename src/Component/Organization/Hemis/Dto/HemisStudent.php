<?php

declare(strict_types=1);

namespace App\Component\Organization\Hemis\Dto;

use App\Enum\StudyLanguage;

final readonly class HemisStudent
{
    public function __construct(
        public string $studentIdNumber,
        public string $fullName,
        public ?string $image,
        public string $groupExternalId,
        public string $groupName,
        public StudyLanguage $studyLanguage,
        public bool $studying,
    ) {
    }
}
