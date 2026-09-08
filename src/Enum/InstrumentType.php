<?php

declare(strict_types=1);

namespace App\Enum;

enum InstrumentType: string
{
    case TemperamentStatements = 'TEMPERAMENT_STATEMENTS';
    case TemperamentChoice = 'TEMPERAMENT_CHOICE';
    case FigureChoice = 'FIGURE_CHOICE';
    case ScoreScale = 'SCORE_SCALE';
}
