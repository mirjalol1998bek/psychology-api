<?php

declare(strict_types=1);

namespace App\Component\User\Dtos;

use Symfony\Component\Serializer\Attribute\Groups;

class AccessApproveDto
{
    public function __construct(
        #[Groups(['user:write'])]
        private string $role = 'ROLE_PSYCHOLOGIST',
    ) {
    }

    public function getRole(): string
    {
        return $this->role;
    }
}
