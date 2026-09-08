<?php

declare(strict_types=1);

namespace App\Enum;

enum FamilyStatus: string
{
    case Married = 'married';
    case Single = 'single';
}
