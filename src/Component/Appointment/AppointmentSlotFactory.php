<?php

declare(strict_types=1);

namespace App\Component\Appointment;

use App\Entity\AppointmentSlot;
use App\Entity\User;
use App\Enum\AppointmentStatus;
use DateTime;

final class AppointmentSlotFactory
{
    public function createBooked(User $psychologist, User $student, DateTime $date, string $startTime, string $endTime): AppointmentSlot
    {
        $slot = new AppointmentSlot();
        $slot->setPsychologist($psychologist);
        $slot->setStudent($student);
        $slot->setDate($date);
        $slot->setStartTime($startTime);
        $slot->setEndTime($endTime);
        $slot->setStatus(AppointmentStatus::Booked);
        $slot->setTitle('Qabul');
        $slot->setCreatedAt(new DateTime());

        return $slot;
    }
}
