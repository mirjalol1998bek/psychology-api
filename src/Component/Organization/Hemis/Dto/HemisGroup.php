<?php

declare(strict_types=1);

namespace App\Component\Organization\Hemis\Dto;

use App\Enum\StudyLanguage;

final readonly class HemisGroup
{
    public function __construct(
        public string $externalId,
        public string $name,
        public string $facultyExternalId,
        public string $facultyName,
        public StudyLanguage $studyLanguage,
        public bool $active,
    ) {
    }
}
