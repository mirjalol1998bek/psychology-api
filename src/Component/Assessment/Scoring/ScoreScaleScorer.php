<?php

declare(strict_types=1);

namespace App\Component\Assessment\Scoring;

use App\Component\Assessment\Exceptions\ScoreRangeNotFoundException;
use App\Entity\Attempt;
use App\Entity\Question;
use App\Entity\ScoreRange;
use App\Enum\InstrumentType;

final class ScoreScaleScorer implements ScorerInterface
{
    public function supports(InstrumentType $instrumentType): bool
    {
        return $instrumentType === InstrumentType::ScoreScale;
    }

    public function score(Attempt $attempt): ScoredResult
    {
        $total = $this->sumScore($attempt);
        $range = $this->matchRange($attempt, $total);

        return new ScoredResult($range->getResultKey(), $total, [new BreakdownItem('score', $total)]);
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

    private function matchRange(Attempt $attempt, int $total): ScoreRange
    {
        $language = $attempt->getStudyLanguage();

        foreach ($attempt->getQuiz()->getCategory()->getScoreRanges() as $range) {
            $matchesLanguage = $range->getStudyLanguage() === null || $range->getStudyLanguage() === $language;

            if ($matchesLanguage === true && $range->containsScore($total) === true) {
                return $range;
            }
        }

        throw new ScoreRangeNotFoundException($total);
    }
}
