<?php

declare(strict_types=1);

namespace App\Component\Assessment\Scoring;

use App\Entity\AssessmentInterpretation;
use App\Entity\Attempt;
use App\Entity\ScoreRange;
use App\Enum\InstrumentType;
use App\Enum\StudyLanguage;

/**
 * Dembo-Rubinshteyn shkalalari. Har savol — bitta chiziq; javobi
 * `AttemptAnswer.textValue`da JSON `{"ob":int,"dd":int}` (0-100 har biri).
 * `Question.overallSign=0` bo'lgan chiziq (sog'liq — demo shkala)
 * o'rtachaga qo'shilmaydi.
 *
 * `ScoreScaleScorer`dan MUSTAQIL (boshqa ballash mantig'i: variant emas,
 * ikkita 0-100 qiymatning o'rtachasi). `ScoreRange`/`AssessmentInterpretation`
 * infratuzilmasi xuddi shunday qayta ishlatiladi — faqat subscaleKey
 * sifatida `'ob'` / `'dd'` / `'diff'` uchta "virtual" guruh ishlatiladi,
 * har biri kategoriyaning o'zida.
 *
 * Umumiy ball yo'q (KSM-20/QY-16 kabi) — natija 3 mustaqil ko'rsatkich
 * (OB o'rtachasi, DD o'rtachasi, farq) bilan chiqadi.
 */
final class DemboRubinsteinScorer implements ScorerInterface
{
    public function supports(InstrumentType $instrumentType): bool
    {
        return $instrumentType === InstrumentType::DemboRubinstein;
    }

    public function score(Attempt $attempt): ScoredResult
    {
        [$obAvg, $ddAvg] = $this->averages($attempt);
        $diff = $ddAvg - $obAvg;
        $language = $attempt->getStudyLanguage();

        $breakdown = [
            $this->buildItem($attempt, 'ob', $obAvg, $this->obLabel($language)),
            $this->buildItem($attempt, 'dd', $ddAvg, $this->ddLabel($language)),
            $this->buildItem($attempt, 'diff', $diff, $this->diffLabel($language)),
        ];

        return new ScoredResult(ScoreScaleScorer::NO_OVERALL_RESULT_KEY, null, $breakdown);
    }

    /**
     * @return array{0: int, 1: int} [obAvg, ddAvg]
     */
    private function averages(Attempt $attempt): array
    {
        $obSum = 0;
        $ddSum = 0;
        $count = 0;

        foreach ($attempt->getAnswers() as $answer) {
            $question = $answer->getQuestion();

            if ($question === null || $question->getOverallSign() === 0) {
                continue;
            }

            $line = $this->decodeLine($answer->getTextValue());

            if ($line === null) {
                continue;
            }

            $obSum += $line[0];
            $ddSum += $line[1];
            $count++;
        }

        if ($count === 0) {
            return [0, 0];
        }

        return [(int) round($obSum / $count), (int) round($ddSum / $count)];
    }

    /**
     * @return array{0: int, 1: int}|null [ob, dd]
     */
    private function decodeLine(?string $textValue): ?array
    {
        if ($textValue === null) {
            return null;
        }

        $data = json_decode($textValue, true);

        if (!is_array($data) || !isset($data['ob'], $data['dd']) || !is_numeric($data['ob']) || !is_numeric($data['dd'])) {
            return null;
        }

        return [(int) $data['ob'], (int) $data['dd']];
    }

    private function buildItem(Attempt $attempt, string $subscaleKey, int $value, string $label): BreakdownItem
    {
        $range = $this->matchRange($attempt, $subscaleKey, $value);
        $interpretation = $range === null ? null : $this->findInterpretation($attempt, $subscaleKey, $range->getResultKey());

        return new BreakdownItem($label, $value, $range?->getResultKey(), $interpretation?->getTitle(), $interpretation?->getText());
    }

    private function matchRange(Attempt $attempt, string $subscaleKey, int $value): ?ScoreRange
    {
        $language = $attempt->getStudyLanguage();

        foreach ($attempt->getQuiz()->getCategory()->getScoreRanges() as $range) {
            $matchesLanguage = $range->getStudyLanguage() === null || $range->getStudyLanguage() === $language;
            $matchesSubscale = $range->getSubscaleKey() === $subscaleKey;

            if ($matchesLanguage && $matchesSubscale && $range->containsScore($value)) {
                return $range;
            }
        }

        return null;
    }

    private function findInterpretation(Attempt $attempt, string $subscaleKey, string $resultKey): ?AssessmentInterpretation
    {
        $language = $attempt->getStudyLanguage();

        foreach ($attempt->getQuiz()->getCategory()->getInterpretations() as $interpretation) {
            $matches = $interpretation->getSubscaleKey() === $subscaleKey
                && $interpretation->getResultKey() === $resultKey
                && $interpretation->getStudyLanguage() === $language;

            if ($matches) {
                return $interpretation;
            }
        }

        return null;
    }

    private function obLabel(StudyLanguage $language): string
    {
        return $language === StudyLanguage::Russian ? 'Самооценка (СО)' : 'O\'zini baholash (OB)';
    }

    private function ddLabel(StudyLanguage $language): string
    {
        return $language === StudyLanguage::Russian ? 'Уровень притязаний (УП)' : 'Da\'vogarlik darajasi (DD)';
    }

    private function diffLabel(StudyLanguage $language): string
    {
        return $language === StudyLanguage::Russian ? 'Разница (УП − СО)' : 'Farq (DD − OB)';
    }
}
