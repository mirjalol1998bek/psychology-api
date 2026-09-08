<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Component\User\CurrentUser;
use App\Entity\Appeal;
use App\Repository\AppealRepository;

/**
 * @implements ProviderInterface<Appeal>
 */
final class AppealCollectionProvider implements ProviderInterface
{
    public function __construct(
        private readonly AppealRepository $appealRepository,
        private readonly CurrentUser $currentUser,
    ) {
    }

    /**
     * @return list<Appeal>
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $user = $this->currentUser->getUser();

        if ($user->getPrimaryRole()->isStaff() === true) {
            return $this->appealRepository->findBy([], ['id' => 'DESC']);
        }

        return $this->appealRepository->findBy(['student' => $user], ['id' => 'DESC']);
    }
}
