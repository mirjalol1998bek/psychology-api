<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Component\User\CurrentUser;
use App\Entity\Attempt;
use App\Repository\AttemptRepository;

/**
 * @implements ProviderInterface<Attempt>
 */
final class StudentAttemptProvider implements ProviderInterface
{
    public function __construct(
        private readonly AttemptRepository $attemptRepository,
        private readonly CurrentUser $currentUser,
    ) {
    }

    /**
     * @return list<Attempt>
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $user = $this->currentUser->getUser();

        if ($user->getPrimaryRole()->isStaff() === true) {
            return $this->attemptRepository->findBy([], ['id' => 'DESC']);
        }

        return $this->attemptRepository->findBy(['student' => $user], ['id' => 'DESC']);
    }
}
