<?php

declare(strict_types=1);

namespace App\Enum;

enum RoleEnum: string
{
    case Student = 'ROLE_STUDENT';
    case Psychologist = 'ROLE_PSYCHOLOGIST';
    case Admin = 'ROLE_ADMIN';
    case Tutor = 'ROLE_TUTOR';

    public function isStaff(): bool
    {
        return $this === self::Psychologist || $this === self::Admin;
    }
}
