<?php

declare(strict_types=1);

namespace App\Component\Passport;

use App\Entity\StudentPassport;
use App\Entity\User;
use App\Repository\StudentPassportRepository;

final class PassportProvider
{
    public function __construct(
        private readonly StudentPassportRepository $passportRepository,
        private readonly PassportFactory $passportFactory,
    ) {
    }

    public function forStudent(User $student): StudentPassport
    {
        $passport = $this->passportRepository->findOneBy(['student' => $student]);

        if ($passport !== null) {
            return $passport;
        }

        return $this->passportFactory->createForStudent($student);
    }
}
