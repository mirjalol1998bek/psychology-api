<?php

declare(strict_types=1);

namespace App\Enum;

enum FamilyType: string
{
    case Full = 'full';
    case Incomplete = 'incomplete';
    case UnderGuardianship = 'under_guardianship';
    case LostBreadwinner = 'lost_breadwinner';
}
