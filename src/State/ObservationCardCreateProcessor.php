<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Component\ObservationCard\ObservationCardCreator;
use App\Component\User\CurrentUser;
use App\Entity\ObservationCard;

/**
 * @implements ProcessorInterface<ObservationCard, ObservationCard>
 */
final class ObservationCardCreateProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly ObservationCardCreator $observationCardCreator,
        private readonly CurrentUser $currentUser,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): ObservationCard
    {
        return $this->observationCardCreator->create($data, $this->currentUser->getUser());
    }
}
