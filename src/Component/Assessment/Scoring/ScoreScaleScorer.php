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
 * ball oralig'i (`ScoreRange.subscaleKey = '*'`, nomiga bog'liq emas) va
 * talqini (`AssessmentInterpretation.subscaleKey = '*'`) bilan. Subshkalasi
 * yo'q metodikalarda (Zung) bu guruhlash bo'sh qoladi — eski xatti-harakat
 * o'zgarmaydi.
 */
final class ScoreScaleScorer implements ScorerInterface
{
    public function supports(InstrumentType $instrumentType): bool
    {
        return $instrumentType === InstrumentType::ScoreScale || $instrumentType === InstrumentType::ScoreScaleSubscale;
    }

    public function score(Attempt $attempt): ScoredResult
    {
        $total = $this->sumScore($attempt);
        $range = $this->matchRange($attempt, $total, '');
        $subscaleTotals = $this->subscaleTotals($attempt);

        $breakdown = $subscaleTotals === []
            ? [new BreakdownItem('score', $total)]
            : $this->subscaleBreakdown($attempt, $subscaleTotals);

        return new ScoredResult($range->getResultKey(), $total, $breakdown);
    }

    private function sumScore(Attempt $attempt): int
    {
        $total = 0;

        foreach ($attempt->getAnswers() as $answer) {
            foreach ($answer->getSelectedOptions() as $option) {
                $total += $this->pointsFor($answer->getQuestion(), $option->getScore());
            }
        }

        return $total;
    }

    /**
     * @return array<string, int>
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

            foreach ($answer->getSelectedOptions() as $option) {
                $totals[$subscaleKey] = ($totals[$subscaleKey] ?? 0) + $this->pointsFor($question, $option->getScore());
            }
        }

        return $totals;
    }

    /**
     * @param array<string, int> $subscaleTotals
     *
     * @return list<BreakdownItem>
     */
    private function subscaleBreakdown(Attempt $attempt, array $subscaleTotals): array
    {
        $items = [];

        foreach ($subscaleTotals as $subscaleKey => $subscaleTotal) {
            $range = $this->matchRange($attempt, $subscaleTotal, '*');
            $interpretation = $this->findSubscaleInterpretation($attempt, $range->getResultKey());
            $items[] = new BreakdownItem(
                $subscaleKey,
                $subscaleTotal,
                $range->getResultKey(),
                $interpretation?->getTitle(),
                $interpretation?->getText(),
            );
        }

        return $items;
    }

    private function findSubscaleInterpretation(Attempt $attempt, string $resultKey): ?AssessmentInterpretation
    {
        $language = $attempt->getStudyLanguage();

        foreach ($attempt->getQuiz()->getCategory()->getInterpretations() as $interpretation) {
            $matches = $interpretation->getSubscaleKey() === '*'
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
