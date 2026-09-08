<?php

declare(strict_types=1);

namespace App\Component\Assessment\Report;

use App\Entity\AssessmentResult;
use App\Entity\User;
use App\Repository\AssessmentResultRepository;
use App\Repository\UserRepository;

final class GroupResultReporter
{
    public function __construct(
        private readonly AssessmentResultRepository $resultRepository,
        private readonly UserRepository $userRepository,
    ) {
    }

    /**
     * All students in the group, each with their result for the category (or nulls).
     *
     * @return list<GroupResultRow>
     */
    public function forGroupAndCategory(int $studyGroupId, int $categoryId): array
    {
        $resultByStudent = $this->indexResultsByStudent($studyGroupId, $categoryId);
        $rows = [];

        foreach ($this->userRepository->findStudentsByGroup($studyGroupId) as $student) {
            $rows[] = $this->toRow($student, $resultByStudent[$student->getId()] ?? null);
        }

        return $rows;
    }

    /**
     * @return array<int, AssessmentResult>
     */
    private function indexResultsByStudent(int $studyGroupId, int $categoryId): array
    {
        $indexed = [];

        foreach ($this->resultRepository->findByGroupAndCategory($studyGroupId, $categoryId) as $result) {
            $studentId = $result->getAttempt()?->getStudent()?->getId();

            if ($studentId !== null) {
                $indexed[$studentId] = $result;
            }
        }

        return $indexed;
    }

    private function toRow(User $student, ?AssessmentResult $result): GroupResultRow
    {
        $submittedAt = $result?->getAttempt()?->getSubmittedAt();

        return new GroupResultRow(
            (int) $student->getId(),
            $student->getHemisId(),
            (string) $student->getFullName(),
            $result?->getResultKey() ?? '',
            $result?->getLabel() ?? '',
            $result?->getScore(),
            $submittedAt?->format(DATE_ATOM),
        );
    }
}
