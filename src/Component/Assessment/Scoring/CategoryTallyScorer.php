<?php

declare(strict_types=1);

namespace App\Component\Assessment\Scoring;

use App\Entity\Attempt;
use App\Enum\InstrumentType;

final class CategoryTallyScorer implements ScorerInterface
{
    /** Teng ballli turlar birlashtiriladi: `Flegmatik+Xolerik`. */
    public const MIXED_SEPARATOR = '+';

    public function supports(InstrumentType $instrumentType): bool
    {
        return $instrumentType === InstrumentType::TemperamentStatements
            || $instrumentType === InstrumentType::TemperamentChoice;
    }

    public function score(Attempt $attempt): ScoredResult
    {
        $tally = new Tally();

        $this->registerCategories($attempt, $tally);
        $this->countSelectedOptions($attempt, $tally);

        return new ScoredResult(implode(self::MIXED_SEPARATOR, $tally->topKeys()), null, $tally->toBreakdown());
    }

    private function registerCategories(Attempt $attempt, Tally $tally): void
    {
        foreach ($attempt->getQuiz()->getQuestions() as $question) {
            foreach ($question->getOptions() as $option) {
                if ($option->getCategoryKey() !== null) {
                    $tally->register($option->getCategoryKey());
                }
            }
        }
    }

    private function countSelectedOptions(Attempt $attempt, Tally $tally): void
    {
        foreach ($attempt->getAnswers() as $answer) {
            foreach ($answer->getSelectedOptions() as $option) {
                if ($option->getCategoryKey() !== null) {
                    $tally->add($option->getCategoryKey(), max(1, $option->getScore()));
                }
            }
        }
    }
}
