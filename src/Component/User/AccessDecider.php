<?php

declare(strict_types=1);

namespace App\Component\User;

use App\Component\Notification\NotificationDispatcher;
use App\Entity\User;
use App\Enum\RoleEnum;
use App\Enum\UserStatusEnum;

/**
 * Admin `pending` kirish so'rovlarini hal qiladi: tasdiqlash (rol berish) yoki
 * rad etish. To'liq oqim: docs/auth.md.
 */
final class AccessDecider
{
    public function __construct(
        private readonly UserManager $userManager,
        private readonly NotificationDispatcher $notificationDispatcher,
    ) {
    }

    public function approve(User $user, RoleEnum $role): void
    {
        $user->setStatus(UserStatusEnum::Active);
        $user->setRoles([$role->value]);
        $this->userManager->save($user, true);

        $this->notificationDispatcher->notifyUserOnAccessApproved($user);
    }

    public function reject(User $user): void
    {
        $user->setStatus(UserStatusEnum::Rejected);
        $user->setRoles([]);
        $this->userManager->save($user, true);
    }
}
