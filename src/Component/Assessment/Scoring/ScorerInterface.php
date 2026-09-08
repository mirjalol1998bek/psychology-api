<?php

declare(strict_types=1);

namespace App\Component\Assessment\Scoring;

use App\Entity\Attempt;
use App\Enum\InstrumentType;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.assessment_scorer')]
interface ScorerInterface
{
    public function supports(InstrumentType $instrumentType): bool;

    public function score(Attempt $attempt): ScoredResult;
}
