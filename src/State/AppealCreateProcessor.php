<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Component\Appeal\AppealCreator;
use App\Component\User\CurrentUser;
use App\Entity\Appeal;

/**
 * @implements ProcessorInterface<Appeal, Appeal>
 */
final class AppealCreateProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly AppealCreator $appealCreator,
        private readonly CurrentUser $currentUser,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Appeal
    {
        return $this->appealCreator->create($data, $this->currentUser->getUser());
    }
}
