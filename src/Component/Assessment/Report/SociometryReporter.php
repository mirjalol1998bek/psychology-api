<?php

declare(strict_types=1);

namespace App\Component\Assessment\Report;

use App\Entity\Attempt;
use App\Entity\StudyGroup;
use App\Entity\User;
use App\Enum\InstrumentType;
use App\Repository\AssessmentResultRepository;
use App\Repository\CategoryRepository;
use App\Repository\UserRepository;

/**
 * Sotsiometriya guruh darajasidagi tahlili — 3 mezon bo'yicha barcha
 * guruh a'zolarining tanlovlarini birlashtirib (mezonlar bo'yicha
 * takrorlanuvchi tanlov bitta "aloqa" sifatida hisoblanadi — union),
 * `observation-card.md`dagi kabi hujjat aniq belgilamagan joyda qilingan
 * qarorlar `sociometry.md`da izohlangan. Salbiy tanlovlar v1'da yo'q.
 */
final class SociometryReporter
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly CategoryRepository $categoryRepository,
        private readonly AssessmentResultRepository $resultRepository,
    ) {
    }

    public function forGroup(StudyGroup $group): SociometryGroupReport
    {
        $students = $this->userRepository->findStudentsByGroup((int) $group->getId());
        $studentIds = array_map(static fn (User $u): int => (int) $u->getId(), $students);
        $category = $this->categoryRepository->findOneBy(['instrumentType' => InstrumentType::Sociometry]);

        if ($category === null || $students === []) {
            return new SociometryGroupReport(count($students), 0, 0.0, 0.0, 0.0, []);
        }

        [$given, $submittedIds] = $this->collectChoices($group, $category->getId(), $studentIds);
        $received = $this->invert($given, $studentIds);
        $avgReceived = $this->average($received);
        $rows = $this->buildRows($students, $given, $received, $submittedIds, $avgReceived);

        return new SociometryGroupReport(
            count($students),
            count($submittedIds),
            $this->percent(count($submittedIds), count($students)),
            $this->cohesion($given, $studentIds),
            $this->percent($this->countIsolated($received), count($students)),
            $rows,
        );
    }

    /**
     * @param list<int> $studentIds
     * @return array{0: array<int, array<int, true>>, 1: list<int>}
     */
    private function collectChoices(StudyGroup $group, int $categoryId, array $studentIds): array
    {
        $given = array_fill_keys($studentIds, []);
        $submittedIds = [];

        foreach ($this->resultRepository->findByGroupAndCategory((int) $group->getId(), $categoryId) as $result) {
            $attempt = $result->getAttempt();
            $chooserId = (int) $attempt?->getStudent()?->getId();

            if ($attempt === null || in_array($chooserId, $studentIds, true) === false) {
                continue;
            }

            $submittedIds[] = $chooserId;
            $this->applyAnswers($attempt, $chooserId, $studentIds, $given);
        }

        return [$given, $submittedIds];
    }

    /**
     * @param list<int> $studentIds
     * @param array<int, array<int, true>> $given
     */
    private function applyAnswers(Attempt $attempt, int $chooserId, array $studentIds, array &$given): void
    {
        foreach ($attempt->getAnswers() as $answer) {
            foreach ($this->decodeChoices($answer->getTextValue()) as $chosenId) {
                if ($chosenId !== $chooserId && in_array($chosenId, $studentIds, true)) {
                    $given[$chooserId][$chosenId] = true;
                }
            }
        }
    }

    /**
     * @return list<int>
     */
    private function decodeChoices(?string $textValue): array
    {
        if ($textValue === null) {
            return [];
        }

        $data = json_decode($textValue, true);

        if (is_array($data) === false) {
            return [];
        }

        return array_values(array_filter(array_map(
            static fn (mixed $v): int => is_numeric($v) ? (int) $v : 0,
            $data,
        )));
    }

    /**
     * @param array<int, array<int, true>> $given
     * @param list<int> $studentIds
     * @return array<int, array<int, true>>
     */
    private function invert(array $given, array $studentIds): array
    {
        $received = array_fill_keys($studentIds, []);

        foreach ($given as $chooserId => $targets) {
            foreach (array_keys($targets) as $targetId) {
                $received[$targetId][$chooserId] = true;
            }
        }

        return $received;
    }

    /**
     * @param array<int, array<int, true>> $received
     */
    private function average(array $received): float
    {
        if ($received === []) {
            return 0.0;
        }

        $total = array_sum(array_map('count', $received));

        return $total / count($received);
    }

    /**
     * @param list<User> $students
     * @param array<int, array<int, true>> $given
     * @param array<int, array<int, true>> $received
     * @param list<int> $submittedIds
     * @return list<SociometryStudentRow>
     */
    private function buildRows(array $students, array $given, array $received, array $submittedIds, float $avgReceived): array
    {
        $n = count($students);
        $rows = [];

        foreach ($students as $student) {
            $id = (int) $student->getId();
            $m = count($received[$id] ?? []);
            $r = count($given[$id] ?? []);

            $rows[] = new SociometryStudentRow(
                $id,
                (string) $student->getFullName(),
                in_array($id, $submittedIds, true),
                $m,
                $r,
                $n > 1 ? round($m / ($n - 1), 2) : 0.0,
                $n > 1 ? round($r / ($n - 1), 2) : 0.0,
                $this->category($m, $avgReceived),
            );
        }

        return $rows;
    }

    /**
     * Rasmiy hujjatdagi kategoriyalar: "1-2 ta tanlov" va "0 ta tanlov"
     * aniq sonlar bo'lgani uchun ustuvor tekshiriladi, "o'rtachadan yuqori"/
     * "2 baravar ko'p" faqat shundan katta sonlar uchun.
     */
    private function category(int $received, float $avgReceived): string
    {
        if ($received === 0) {
            return 'izolyatsiyadagilar';
        }

        if ($received <= 2) {
            return 'etibordan_chetdagilar';
        }

        if ($avgReceived > 0 && $received >= 2 * $avgReceived) {
            return 'yulduzlar';
        }

        if ($received > $avgReceived) {
            return 'afzal_korilganlar';
        }

        return 'ortacha';
    }

    /**
     * @param array<int, array<int, true>> $given
     * @param list<int> $studentIds
     */
    private function cohesion(array $given, array $studentIds): float
    {
        $n = count($studentIds);

        if ($n < 2) {
            return 0.0;
        }

        return round((2 * $this->countMutualPairs($given)) / ($n * ($n - 1)), 3);
    }

    /**
     * @param array<int, array<int, true>> $given
     */
    private function countMutualPairs(array $given): int
    {
        $counted = [];

        foreach ($given as $a => $targets) {
            foreach (array_keys($targets) as $b) {
                $key = min($a, $b) . '-' . max($a, $b);

                if (isset($given[$b][$a]) && isset($counted[$key]) === false) {
                    $counted[$key] = true;
                }
            }
        }

        return count($counted);
    }

    /**
     * @param array<int, array<int, true>> $received
     */
    private function countIsolated(array $received): int
    {
        return count(array_filter($received, static fn (array $set): bool => $set === []));
    }

    private function percent(int $part, int $total): float
    {
        return $total > 0 ? round($part / $total * 100, 1) : 0.0;
    }
}
