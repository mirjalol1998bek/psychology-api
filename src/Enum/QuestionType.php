<?php

declare(strict_types=1);

namespace App\Enum;

enum QuestionType: string
{
    case YesNo = 'YES_NO';
    case SingleChoice = 'SINGLE_CHOICE';
    case MultiSelect = 'MULTI_SELECT';
    case SingleChoiceImage = 'SINGLE_CHOICE_IMAGE';
    case Figure = 'FIGURE';
    case Scale = 'SCALE';
    case Writing = 'WRITING';

    public function hasOptions(): bool
    {
        return $this !== self::Writing;
    }
}
