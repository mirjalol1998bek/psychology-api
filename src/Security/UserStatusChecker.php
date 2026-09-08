<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\User;
use App\Enum\UserStatusEnum;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Har bir so'rovda foydalanuvchi holatini tekshiradi. Faqat `active`
 * foydalanuvchi tizimga kira oladi (docs/auth.md).
 */
final class UserStatusChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user, ?TokenInterface $token = null): void
    {
        if ($user instanceof User === false) {
            return;
        }

        if ($user->getStatus() === UserStatusEnum::Pending) {
            throw new CustomUserMessageAccountStatusException('Arizangiz administrator tasdig\'ini kutmoqda.');
        }

        if ($user->getStatus() === UserStatusEnum::Rejected) {
            throw new CustomUserMessageAccountStatusException('Tizimga kirish rad etilgan.');
        }
    }

    public function checkPostAuth(UserInterface $user, ?TokenInterface $token = null): void
    {
    }
}
