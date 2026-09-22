<?php

declare(strict_types=1);

namespace App\Component\Assessment\Report;

final readonly class SociometryGroupReport
{
    /**
     * @param list<SociometryStudentRow> $rows
     */
    public function __construct(
        public int $totalStudents,
        public int $submittedCount,
        public float $participationRate,
        public float $cohesion,
        public float $isolationRate,
        public array $rows,
    ) {
    }
}
