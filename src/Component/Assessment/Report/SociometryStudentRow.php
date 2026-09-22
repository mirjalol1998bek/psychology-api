<?php

declare(strict_types=1);

namespace App\Component\Assessment\Report;

final readonly class SociometryStudentRow
{
    public function __construct(
        public int $studentId,
        public string $fullName,
        public bool $submitted,
        public int $received,
        public int $given,
        public float $statusIndex,
        public float $expansivenessIndex,
        public string $category,
    ) {
    }
}
