<?php

declare(strict_types=1);

namespace App\Component\Assessment\Report;

final readonly class GroupResultRow
{
    public function __construct(
        public int $studentId,
        public ?string $hemisId,
        public string $fullName,
        public string $resultKey,
        public string $label,
        public ?int $score,
        public ?string $submittedAt,
        public ?int $attemptId,
    ) {
    }
}
