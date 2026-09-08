<?php

declare(strict_types=1);

namespace App\Enum;

enum AppointmentStatus: string
{
    case Free = 'free';
    case Booked = 'booked';
    case Cancelled = 'cancelled';
}
