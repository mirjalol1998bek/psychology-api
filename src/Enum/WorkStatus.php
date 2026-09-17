<?php

declare(strict_types=1);

namespace App\Enum;

enum WorkStatus: string
{
    case No = 'no';
    case Partial = 'partial';
    case FullTime = 'full_time';
}
