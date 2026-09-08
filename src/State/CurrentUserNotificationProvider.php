<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Component\User\CurrentUser;
use App\Entity\Notification;
use App\Repository\NotificationRepository;

/**
 * @implements ProviderInterface<Notification>
 */
final class CurrentUserNotificationProvider implements ProviderInterface
{
    public function __construct(
        private readonly NotificationRepository $notificationRepository,
        private readonly CurrentUser $currentUser,
    ) {
    }

    /**
     * @return list<Notification>
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        return $this->notificationRepository->findBy(
            ['recipient' => $this->currentUser->getUser()],
            ['id' => 'DESC'],
        );
    }
}
