<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Component\Passport\PassportProvider;
use App\Component\User\CurrentUser;
use App\Entity\StudentPassport;

/**
 * @implements ProviderInterface<StudentPassport>
 */
final class CurrentUserPassportProvider implements ProviderInterface
{
    public function __construct(
        private readonly PassportProvider $passportProvider,
        private readonly CurrentUser $currentUser,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): StudentPassport
    {
        return $this->passportProvider->forStudent($this->currentUser->getUser());
    }
}
