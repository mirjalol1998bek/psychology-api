<?php

declare(strict_types=1);

namespace App\Enum;

enum AttemptStatus: string
{
    case NotStarted = 'not_started';
    case InProgress = 'in_progress';
    case Submitted = 'submitted';
    case Reviewed = 'reviewed';

    public function isFinished(): bool
    {
        return $this === self::Submitted || $this === self::Reviewed;
    }
}
