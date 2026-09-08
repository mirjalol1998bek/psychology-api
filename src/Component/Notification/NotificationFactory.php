<?php

declare(strict_types=1);

namespace App\Component\Notification;

use App\Entity\Notification;
use App\Entity\User;
use App\Enum\NotificationType;

final class NotificationFactory
{
    public function create(
        User $recipient,
        NotificationType $type,
        string $title,
        string $body,
        ?string $link = null,
    ): Notification {
        $notification = new Notification();
        $notification->setRecipient($recipient);
        $notification->setType($type);
        $notification->setTitle($title);
        $notification->setBody($body);
        $notification->setLink($link);

        return $notification;
    }
}
