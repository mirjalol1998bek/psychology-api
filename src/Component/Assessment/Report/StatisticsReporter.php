<?php

declare(strict_types=1);

namespace App\Component\Assessment\Report;

use App\Component\Assessment\Scoring\CategoryTallyScorer;
use App\Enum\InstrumentType;
use App\Repository\AssessmentResultRepository;
use App\Repository\CategoryRepository;
use App\Repository\FacultyRepository;
use App\Repository\UserRepository;
use BackedEnum;

final class StatisticsReporter
{
    public function __construct(
        private readonly FacultyRepository $facultyRepository,
        private readonly UserRepository $userRepository,
        private readonly AssessmentResultRepository $resultRepository,
        private readonly CategoryRepository $categoryRepository,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function overview(): array
    {
        $scaleAlgos = $this->scaleAlgos();
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
                'temperamentStudents' => $this->countTemperamentResults($rows, false),
                'temperamentMixed' => $this->countTemperamentResults($rows, true),
                'scales' => $this->scaleBreakdown($rows, $students, $scaleAlgos),
            ];
        }

        return [
            'totals' => ['students' => $totalStudents, 'withResults' => $totalWithResults],
            'faculties' => $faculties,
        ];
    }

    /**
     * Psixogeometrik/Temperament o'zining maxsus kartasi bor (`figures`/
     * `temperaments`) — qolgan har qanday algoritm (SCORE_SCALE oilasi,
     * DEMBO_RUBINSTEIN, kelajakdagi yangilari) shu yerda avtomatik
     * "natija bor/yo'q" kartasiga chiqadi, hozir mavjud Category'lardan
     * olinadi — yangi metodika qo'shilganda bu yerga qo'l tegmasdan ishlaydi.
     *
     * @return list<string>
     */
    private function scaleAlgos(): array
    {
        $excluded = ['TEMPERAMENT_STATEMENTS', 'TEMPERAMENT_CHOICE', 'FIGURE_CHOICE'];
        $algos = [];

        foreach ($this->categoryRepository->findAll() as $category) {
            $value = $category->getInstrumentType()->value;

            if (!in_array($value, $excluded, true) && !in_array($value, $algos, true)) {
                $algos[] = $value;
            }
        }

        return $algos;
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
            if ($this->isTemperament($row['algo']) === false) {
                continue;
            }

            // Aralash natija (`Flegmatik+Xolerik`) — har bir teng turga hisoblanadi.
            foreach (explode(CategoryTallyScorer::MIXED_SEPARATOR, $row['resultKey']) as $key) {
                $counts[$key] = ($counts[$key] ?? 0) + 1;
            }
        }

        return $this->toPairs($counts);
    }

    /**
     * @param list<array{studentId: int, algo: string, resultKey: string}> $rows
     */
    private function countTemperamentResults(array $rows, bool $mixedOnly): int
    {
        $count = 0;

        foreach ($rows as $row) {
            $isMixed = str_contains($row['resultKey'], CategoryTallyScorer::MIXED_SEPARATOR);

            if ($this->isTemperament($row['algo']) && ($mixedOnly === false || $isMixed)) {
                $count++;
            }
        }

        return $count;
    }

    private function isTemperament(string $algo): bool
    {
        return $algo === 'TEMPERAMENT_STATEMENTS' || $algo === 'TEMPERAMENT_CHOICE';
    }

    /**
     * @param list<array{studentId: int, algo: string, resultKey: string}> $rows
     * @param list<string>                                                 $algos
     * @return list<array{algo: string, withResult: int, withoutResult: int}>
     */
    private function scaleBreakdown(array $rows, int $students, array $algos): array
    {
        $counts = array_fill_keys($algos, 0);

        foreach ($rows as $row) {
            if (array_key_exists($row['algo'], $counts)) {
                $counts[$row['algo']]++;
            }
        }

        $out = [];

        foreach ($counts as $algo => $withResult) {
            $out[] = ['algo' => $algo, 'withResult' => $withResult, 'withoutResult' => max(0, $students - $withResult)];
        }

        return $out;
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
