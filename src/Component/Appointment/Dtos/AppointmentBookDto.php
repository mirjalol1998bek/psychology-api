<?php

declare(strict_types=1);

namespace App\Component\Appointment\Dtos;

use Symfony\Component\Serializer\Attribute\Groups;

final class AppointmentBookDto
{
    public function __construct(
        #[Groups(['appointment:book'])]
        private ?string $date = null,
        #[Groups(['appointment:book'])]
        private ?string $startTime = null,
    ) {
    }

    public function getDate(): ?string
    {
        return $this->date;
    }

    public function getStartTime(): ?string
    {
        return $this->startTime;
    }
}
