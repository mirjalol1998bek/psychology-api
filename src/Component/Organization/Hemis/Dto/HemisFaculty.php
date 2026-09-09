<?php

declare(strict_types=1);

namespace App\Component\Organization\Hemis\Dto;

final readonly class HemisFaculty
{
    public function __construct(
        public string $externalId,
        public string $name,
        public bool $active,
    ) {
    }
}
