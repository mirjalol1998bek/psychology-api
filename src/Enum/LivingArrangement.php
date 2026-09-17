<?php

declare(strict_types=1);

namespace App\Enum;

enum LivingArrangement: string
{
    case WithFamily = 'with_family';
    case Dormitory = 'dormitory';
    case Rented = 'rented';
    case WithRelatives = 'with_relatives';
}
