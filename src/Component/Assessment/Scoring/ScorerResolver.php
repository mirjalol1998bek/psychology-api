<?php

declare(strict_types=1);

namespace App\Component\Assessment\Scoring;

use App\Component\Assessment\Exceptions\ScorerNotFoundException;
use App\Enum\InstrumentType;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

final class ScorerResolver
{
    /**
     * @param iterable<ScorerInterface> $scorers
     */
    public function __construct(
        #[AutowireIterator('app.assessment_scorer')]
        private iterable $scorers,
    ) {
    }

    public function resolve(InstrumentType $instrumentType): ScorerInterface
    {
        foreach ($this->scorers as $scorer) {
            if ($scorer->supports($instrumentType) === true) {
                return $scorer;
            }
        }

        throw new ScorerNotFoundException($instrumentType->value);
    }
}
