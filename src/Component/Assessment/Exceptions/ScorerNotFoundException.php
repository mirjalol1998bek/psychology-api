<?php

declare(strict_types=1);

namespace App\Component\Assessment\Exceptions;

use RuntimeException;

final class ScorerNotFoundException extends RuntimeException
{
    public function __construct(string $instrumentType)
    {
        parent::__construct(sprintf('No scorer supports instrument type "%s".', $instrumentType));
    }
}
