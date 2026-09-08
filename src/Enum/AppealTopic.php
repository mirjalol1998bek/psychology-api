<?php

declare(strict_types=1);

namespace App\Enum;

enum AppealTopic: string
{
    case Question = 'question';
    case Appointment = 'appointment';
    case Stress = 'stress';
    case Other = 'other';
}
