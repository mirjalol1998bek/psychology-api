<?php

declare(strict_types=1);

namespace App\Enum;

enum FinancialStatus: string
{
    case Good = 'good';
    case Average = 'average';
    case Difficult = 'difficult';
}
