<?php

declare(strict_types=1);

namespace App\Controller;

use App\Component\Notification\NotificationReader;
use App\Controller\Base\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class NotificationMarkReadAction extends AbstractController
{
    public function __invoke(NotificationReader $notificationReader): Response
    {
        $notificationReader->markAllAsRead($this->getUser());

        return $this->responseEmpty();
    }
}
