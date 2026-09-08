<?php

declare(strict_types=1);

namespace App\Component\Assessment\Report;

use App\Entity\AssessmentResult;
use App\Repository\AssessmentResultRepository;

final class GroupResultReporter
{
    public function __construct(private readonly AssessmentResultRepository $resultRepository)
    {
    }

    /**
     * @return list<GroupResultRow>
     */
    public function forGroupAndCategory(int $studyGroupId, int $categoryId): array
    {
        $rows = [];

        foreach ($this->resultRepository->findByGroupAndCategory($studyGroupId, $categoryId) as $result) {
            $rows[] = $this->toRow($result);
        }

        return $rows;
    }

    private function toRow(AssessmentResult $result): GroupResultRow
    {
        $student = $result->getAttempt()?->getStudent();
        $submittedAt = $result->getAttempt()?->getSubmittedAt();

        return new GroupResultRow(
            (int) $student?->getId(),
            $student?->getHemisId(),
            (string) $student?->getFullName(),
            $result->getResultKey(),
            $result->getLabel(),
            $result->getScore(),
            $submittedAt?->format(DATE_ATOM),
        );
    }
}
