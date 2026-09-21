<?php

declare(strict_types=1);

namespace App\Component\Organization\Hemis\Dto;

final readonly class HemisEmployee
{
    /**
     * @param list<HemisTutorGroup> $tutorGroups
     */
    public function __construct(
        public string $hemisId,
        public string $fullName,
        public ?string $image,
        public array $tutorGroups,
    ) {
    }
}
