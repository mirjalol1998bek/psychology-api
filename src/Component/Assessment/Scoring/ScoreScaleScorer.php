<?php

declare(strict_types=1);

namespace App\Component\Assessment\Scoring;

use App\Component\Assessment\Exceptions\ScoreRangeNotFoundException;
use App\Entity\AssessmentInterpretation;
use App\Entity\Attempt;
use App\Entity\Question;
use App\Entity\ScoreRange;
use App\Enum\InstrumentType;

/**
 * Ba'zi SCORE_SCALE metodikalar (masalan IPM-20) savollarni nomlangan
 * subshkalalarga guruhlaydi (`Question.subscaleKey`) — har biri o'z
 * ball oralig'i va talqini bilan. Qaysi jadval ishlatilishi
 * `Question.subscaleRangeKey` bilan belgilanadi: standart `'*'` — barcha
 * subshkalalar bitta umumiy jadvaldan foydalanadi (IPM-20, OKM-20: hammasi
 * 5-25). Subshkalalar TENG BO'LMAGAN o'lchamda bo'lsa (EHS-20: A/B 7-35,
 * C 6-30) — har guruh o'z maxsus kalitiga ega bo'ladi. Subshkalasi yo'q
 * metodikalarda bu guruhlash bo'sh qoladi — natija faqat umumiy ball bilan
 * chiqadi.
 *
 * `Question.overallSign` (+1/-1) — UMUMIY ball qo'shiladigan ishora
 * (masalan OKM-20: IMI = (A+B) − (C+D) — C/D savollari −1 bilan qo'shiladi;
 * EHS-20: ERI = (A+B) − C). Subshkalaning o'z ballini o'zgartirmaydi,
 * standart +1.
 *
 * Ba'zi subshkalali metodikalarda (KSM-20) yagona UMUMIY ball ma'nosiz —
 * natija faqat mustaqil subshkalalar bilan chiqadi. Bunday holatda
 * kategoriya uchun umumiy `ScoreRange` (subscaleKey='') yaratilmaydi;
 * shu holat aniqlansa, `NO_OVERALL_RESULT_KEY` qaytariladi va
 * `AssessmentInterpretation`dan shu kalit bilan (subscaleKey='') umumiy
 * xulosa matni topiladi — u faqat "natija subshkalalar bilan ko'rsatiladi"
 * kabi umumiy izoh beradi, ball esa `null` bo'ladi.
 */
final class ScoreScaleScorer implements ScorerInterface
{
    public const NO_OVERALL_RESULT_KEY = 'subscale_only';

    public function supports(InstrumentType $instrumentType): bool
    {
        return $instrumentType === InstrumentType::ScoreScale
            || $instrumentType === InstrumentType::ScoreScaleSubscale
            || $instrumentType === InstrumentType::ScoreScaleMotivation
            || $instrumentType === InstrumentType::ScoreScaleEmotional
            || $instrumentType === InstrumentType::ScoreScaleRisk
            || $instrumentType === InstrumentType::ScoreScaleCommunication;
    }

    public function score(Attempt $attempt): ScoredResult
    {
        $subscaleTotals = $this->subscaleTotals($attempt);

        if ($this->hasOverallRange($attempt) === false) {
            return new ScoredResult(self::NO_OVERALL_RESULT_KEY, null, $this->subscaleBreakdown($attempt, $subscaleTotals));
        }

        $total = $this->sumScore($attempt);
        $range = $this->matchRange($attempt, $total, '');

        $breakdown = $subscaleTotals === []
            ? [new BreakdownItem('score', $total)]
            : $this->subscaleBreakdown($attempt, $subscaleTotals);

        return new ScoredResult($range->getResultKey(), $total, $breakdown);
    }

    private function hasOverallRange(Attempt $attempt): bool
    {
        foreach ($attempt->getQuiz()->getCategory()->getScoreRanges() as $range) {
            if ($range->getSubscaleKey() === '') {
                return true;
            }
        }

        return false;
    }

    private function sumScore(Attempt $attempt): int
    {
        $total = 0;

        foreach ($attempt->getAnswers() as $answer) {
            $question = $answer->getQuestion();
            $sign = $question?->getOverallSign() ?? 1;

            foreach ($answer->getSelectedOptions() as $option) {
                $total += $sign * $this->pointsFor($question, $option->getScore());
            }
        }

        return $total;
    }

    /**
     * @return array<string, array{total: int, rangeKey: string}>
     */
    private function subscaleTotals(Attempt $attempt): array
    {
        $totals = [];

        foreach ($attempt->getAnswers() as $answer) {
            $question = $answer->getQuestion();
            $subscaleKey = $question?->getSubscaleKey() ?? '';

            if ($subscaleKey === '') {
                continue;
            }

            if (!isset($totals[$subscaleKey])) {
                $totals[$subscaleKey] = ['total' => 0, 'rangeKey' => $question?->getSubscaleRangeKey() ?? '*'];
            }

            foreach ($answer->getSelectedOptions() as $option) {
                $totals[$subscaleKey]['total'] += $this->pointsFor($question, $option->getScore());
            }
        }

        return $totals;
    }

    /**
     * @param array<string, array{total: int, rangeKey: string}> $subscaleTotals
     *
     * @return list<BreakdownItem>
     */
    private function subscaleBreakdown(Attempt $attempt, array $subscaleTotals): array
    {
        $items = [];

        foreach ($subscaleTotals as $subscaleKey => $data) {
            $range = $this->matchRange($attempt, $data['total'], $data['rangeKey']);
            $interpretation = $this->findSubscaleInterpretation($attempt, $data['rangeKey'], $range->getResultKey());
            $items[] = new BreakdownItem(
                $subscaleKey,
                $data['total'],
                $range->getResultKey(),
                $interpretation?->getTitle(),
                $interpretation?->getText(),
            );
        }

        return $items;
    }

    private function findSubscaleInterpretation(Attempt $attempt, string $rangeKey, string $resultKey): ?AssessmentInterpretation
    {
        $language = $attempt->getStudyLanguage();

        foreach ($attempt->getQuiz()->getCategory()->getInterpretations() as $interpretation) {
            $matches = $interpretation->getSubscaleKey() === $rangeKey
                && $interpretation->getResultKey() === $resultKey
                && $interpretation->getStudyLanguage() === $language;

            if ($matches === true) {
                return $interpretation;
            }
        }

        return null;
    }

    private function pointsFor(?Question $question, int $optionScore): int
    {
        if ($question !== null && $question->getIsReversed() === true) {
            return ($this->maxOptionScore($question) + $this->minOptionScore($question)) - $optionScore;
        }

        return $optionScore;
    }

    private function maxOptionScore(Question $question): int
    {
        $max = 0;

        foreach ($question->getOptions() as $option) {
            $max = max($max, $option->getScore());
        }

        return $max;
    }

    private function minOptionScore(Question $question): int
    {
        $scores = [];

        foreach ($question->getOptions() as $option) {
            $scores[] = $option->getScore();
        }

        return $scores === [] ? 0 : min($scores);
    }

    private function matchRange(Attempt $attempt, int $total, string $subscaleKey): ScoreRange
    {
        $language = $attempt->getStudyLanguage();

        foreach ($attempt->getQuiz()->getCategory()->getScoreRanges() as $range) {
            $matchesLanguage = $range->getStudyLanguage() === null || $range->getStudyLanguage() === $language;
            $matchesSubscale = $range->getSubscaleKey() === $subscaleKey;

            if ($matchesLanguage === true && $matchesSubscale === true && $range->containsScore($total) === true) {
                return $range;
            }
        }

        throw new ScoreRangeNotFoundException($total);
    }
}
