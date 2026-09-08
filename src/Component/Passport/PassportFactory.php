<?php

declare(strict_types=1);

namespace App\Component\Passport;

use App\Entity\StudentPassport;
use App\Entity\User;

final class PassportFactory
{
    public function createForStudent(User $student): StudentPassport
    {
        $passport = new StudentPassport();
        $passport->setStudent($student);

        return $passport;
    }
}
