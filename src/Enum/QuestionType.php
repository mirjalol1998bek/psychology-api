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
    /** Dembo-Rubinshteyn: bitta chiziqda ikkita belgi (X = hozirgi holat,
     * — = xohlagan daraja), har biri 0-100 oralig'ida. Variantlari yo'q —
     * javob `AttemptAnswer.textValue`da JSON `{"ob":int,"dd":int}` sifatida
     * saqlanadi. */
    case SliderDual = 'SLIDER_DUAL';

    public function hasOptions(): bool
    {
        return $this !== self::Writing && $this !== self::SliderDual;
    }
}
