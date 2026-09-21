<?php

declare(strict_types=1);

namespace App\Component\Organization\Hemis\Dto;

final readonly class HemisTutorGroup
{
    public function __construct(
        public string $externalId,
        public string $name,
    ) {
    }
}
