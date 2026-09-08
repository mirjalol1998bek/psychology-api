<?php

declare(strict_types=1);

namespace App\Component\Assessment\Exceptions;

use RuntimeException;

final class ScoreRangeNotFoundException extends RuntimeException
{
    public function __construct(int $score)
    {
        parent::__construct(sprintf('No score range configured for total score %d.', $score));
    }
}
