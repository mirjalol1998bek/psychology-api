<?php

declare(strict_types=1);

namespace App\Enum;

enum AppealMode: string
{
    case Named = 'named';
    case Anonymous = 'anonymous';
}
