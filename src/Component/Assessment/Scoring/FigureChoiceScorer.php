<?php

declare(strict_types=1);

namespace App\Component\Assessment\Scoring;

use App\Component\Assessment\Exceptions\IncompleteAttemptException;
use App\Entity\AnswerOption;
use App\Entity\Attempt;
use App\Enum\InstrumentType;

final class FigureChoiceScorer implements ScorerInterface
{
    public function supports(InstrumentType $instrumentType): bool
    {
        return $instrumentType === InstrumentType::FigureChoice;
    }

    public function score(Attempt $attempt): ScoredResult
    {
        $option = $this->pickChosenOption($attempt);

        return new ScoredResult($option->getCategoryKey() ?? $option->getText(), null, []);
    }

    private function pickChosenOption(Attempt $attempt): AnswerOption
    {
        foreach ($attempt->getAnswers() as $answer) {
            $option = $answer->getFirstOption();

            if ($option !== null) {
                return $option;
            }
        }

        throw new IncompleteAttemptException('No figure was chosen.');
    }
}
