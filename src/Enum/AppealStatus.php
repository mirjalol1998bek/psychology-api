<?php

declare(strict_types=1);

namespace App\Enum;

enum AppealStatus: string
{
    case Open = 'open';
    case Answered = 'answered';
}
