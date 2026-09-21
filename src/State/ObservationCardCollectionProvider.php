<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Component\User\CurrentUser;
use App\Entity\ObservationCard;
use App\Repository\ObservationCardRepository;

/**
 * @implements ProviderInterface<ObservationCard>
 */
final class ObservationCardCollectionProvider implements ProviderInterface
{
    public function __construct(
        private readonly ObservationCardRepository $observationCardRepository,
        private readonly CurrentUser $currentUser,
    ) {
    }

    /**
     * @return list<ObservationCard>
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $user = $this->currentUser->getUser();

        if ($user->getPrimaryRole()->isStaff() === true) {
            return $this->observationCardRepository->findBy([], ['id' => 'DESC']);
        }

        return $this->observationCardRepository->findBy(['tutor' => $user], ['id' => 'DESC']);
    }
}
