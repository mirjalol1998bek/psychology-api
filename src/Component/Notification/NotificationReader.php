<?php

declare(strict_types=1);

namespace App\Component\Notification;

use App\Entity\User;
use App\Repository\NotificationRepository;

final class NotificationReader
{
    public function __construct(
        private readonly NotificationRepository $notificationRepository,
        private readonly NotificationManager $notificationManager,
    ) {
    }

    public function markAllAsRead(User $recipient): void
    {
        $unread = $this->notificationRepository->findBy(['recipient' => $recipient, 'isRead' => false]);
        $lastIndex = count($unread) - 1;

        foreach ($unread as $index => $notification) {
            $notification->markAsRead();
            $this->notificationManager->save($notification, $index === $lastIndex);
        }
    }
}
