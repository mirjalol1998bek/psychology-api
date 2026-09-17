<?php

declare(strict_types=1);

namespace App\Component\Appointment;

use App\Entity\AppointmentSlot;
use App\Enum\AppointmentStatus;

/**
 * Band qilingan 2 soatlik blokni qamrab olgan `free` vaqt oralig'ini shu
 * blokka moslab qisqartiradi yoki bo'ladi — appointment-slot.md'dagi
 * "bo'sh oraliqni iste'mol qilish" qoidasi.
 */
final class AvailabilityWindowConsumer
{
    public function __construct(
        private readonly AppointmentSlotManager $appointmentSlotManager,
    ) {
    }

    public function consume(AppointmentSlot $window, string $bookedStart, string $bookedEnd): void
    {
        $coversFromStart = $window->getStartTime() === $bookedStart;
        $coversToEnd = $window->getEndTime() === $bookedEnd;

        if ($coversFromStart && $coversToEnd) {
            $this->appointmentSlotManager->remove($window, true);

            return;
        }

        if ($coversFromStart) {
            $window->setStartTime($bookedEnd);
            $this->appointmentSlotManager->save($window, true);

            return;
        }

        if ($coversToEnd) {
            $window->setEndTime($bookedStart);
            $this->appointmentSlotManager->save($window, true);

            return;
        }

        $this->splitAroundBlock($window, $bookedStart, $bookedEnd);
    }

    private function splitAroundBlock(AppointmentSlot $window, string $bookedStart, string $bookedEnd): void
    {
        $originalEnd = $window->getEndTime();

        $window->setEndTime($bookedStart);
        $this->appointmentSlotManager->save($window, true);

        $remainder = new AppointmentSlot();
        $remainder->setPsychologist($window->getPsychologist());
        $remainder->setDate($window->getDate());
        $remainder->setStartTime($bookedEnd);
        $remainder->setEndTime($originalEnd);
        $remainder->setStatus(AppointmentStatus::Free);
        $remainder->setRoom($window->getRoom());
        $this->appointmentSlotManager->save($remainder, true);
    }
}
