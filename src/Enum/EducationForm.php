<?php

declare(strict_types=1);

namespace App\Enum;

enum EducationForm: string
{
    case Budget = 'budget';
    case Contract = 'contract';
    case Grant = 'grant';
}
