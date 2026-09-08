<?php

declare(strict_types=1);

namespace App\Enum;

enum UserStatusEnum: string
{
    case Pending = 'pending';
    case Active = 'active';
    case Rejected = 'rejected';

    public function allowsLogin(): bool
    {
        return $this === self::Active;
    }
}
