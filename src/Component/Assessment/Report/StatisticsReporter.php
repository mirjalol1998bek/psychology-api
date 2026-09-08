<?php

declare(strict_types=1);

namespace App\Component\Assessment\Report;

use App\Enum\InstrumentType;
use App\Repository\AssessmentResultRepository;
use App\Repository\FacultyRepository;
use App\Repository\UserRepository;
use BackedEnum;

final class StatisticsReporter
{
    public function __construct(
        private readonly FacultyRepository $facultyRepository,
        private readonly UserRepository $userRepository,
        private readonly AssessmentResultRepository $resultRepository,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function overview(): array
    {
        $faculties = [];
        $totalStudents = 0;
        $totalWithResults = 0;

        foreach ($this->facultyRepository->findAll() as $faculty) {
            $facultyId = (int) $faculty->getId();
            $students = $this->userRepository->countStudentsByFaculty($facultyId);
            $rows = $this->normalizeRows($this->resultRepository->facultyBreakdown($facultyId));
            $studentsWithResults = $this->countDistinctStudents($rows);

            $totalStudents += $students;
            $totalWithResults += $studentsWithResults;

            $faculties[] = [
                'id' => $facultyId,
                'name' => $faculty->getName(),
                'students' => $students,
                'withResults' => $studentsWithResults,
                'figures' => $this->countByKey($rows, 'FIGURE_CHOICE'),
                'temperaments' => $this->mergeTemperaments($rows),
                'scale' => $this->scaleSplit($rows, $students),
            ];
        }

        return [
            'totals' => ['students' => $totalStudents, 'withResults' => $totalWithResults],
            'faculties' => $faculties,
        ];
    }

    /**
     * @param list<array{studentId: int, algo: InstrumentType|string, resultKey: string}> $rows
     * @return list<array{studentId: int, algo: string, resultKey: string}>
     */
    private function normalizeRows(array $rows): array
    {
        $out = [];

        foreach ($rows as $row) {
            $algo = $row['algo'];
            $out[] = [
                'studentId' => (int) $row['studentId'],
                'algo' => $algo instanceof BackedEnum ? (string) $algo->value : (string) $algo,
                'resultKey' => (string) $row['resultKey'],
            ];
        }

        return $out;
    }

    /**
     * @param list<array{studentId: int, algo: string, resultKey: string}> $rows
     */
    private function countDistinctStudents(array $rows): int
    {
        $ids = [];

        foreach ($rows as $row) {
            $ids[$row['studentId']] = true;
        }

        return count($ids);
    }

    /**
     * @param list<array{studentId: int, algo: string, resultKey: string}> $rows
     * @return list<array{key: string, count: int}>
     */
    private function countByKey(array $rows, string $algo): array
    {
        $counts = [];

        foreach ($rows as $row) {
            if ($row['algo'] === $algo) {
                $counts[$row['resultKey']] = ($counts[$row['resultKey']] ?? 0) + 1;
            }
        }

        return $this->toPairs($counts);
    }

    /**
     * @param list<array{studentId: int, algo: string, resultKey: string}> $rows
     * @return list<array{key: string, count: int}>
     */
    private function mergeTemperaments(array $rows): array
    {
        $counts = [];

        foreach ($rows as $row) {
            if ($row['algo'] === 'TEMPERAMENT_STATEMENTS' || $row['algo'] === 'TEMPERAMENT_CHOICE') {
                $counts[$row['resultKey']] = ($counts[$row['resultKey']] ?? 0) + 1;
            }
        }

        return $this->toPairs($counts);
    }

    /**
     * @param list<array{studentId: int, algo: string, resultKey: string}> $rows
     * @return array{withResult: int, withoutResult: int}
     */
    private function scaleSplit(array $rows, int $students): array
    {
        $withResult = 0;

        foreach ($rows as $row) {
            if ($row['algo'] === 'SCORE_SCALE') {
                $withResult++;
            }
        }

        return ['withResult' => $withResult, 'withoutResult' => max(0, $students - $withResult)];
    }

    /**
     * @param array<string, int> $counts
     * @return list<array{key: string, count: int}>
     */
    private function toPairs(array $counts): array
    {
        $pairs = [];

        foreach ($counts as $key => $count) {
            $pairs[] = ['key' => $key, 'count' => $count];
        }

        return $pairs;
    }
}
