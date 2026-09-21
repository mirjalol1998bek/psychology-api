<?php

declare(strict_types=1);

namespace App\Component\User;

use App\Component\Organization\Hemis\Dto\HemisEmployee;
use App\Component\User\Hemis\HemisProfile;
use App\Entity\StudyGroup;
use App\Entity\User;
use App\Enum\RoleEnum;
use App\Enum\UserStatusEnum;
use DateTime;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFactory
{
    public function __construct(private UserPasswordHasherInterface $passwordEncoder)
    {
    }

    public function create(string $email, string $password): User
    {
        $user = new User();
        $user->setEmail($email);
        $user->setCreatedAt(new DateTime());
        $user->setPassword($this->passwordEncoder->hashPassword($user, $password));

        return $user;
    }

    public function createFromHemis(HemisProfile $profile, ?StudyGroup $studyGroup): User
    {
        $user = $this->create($profile->resolveEmail(), bin2hex(random_bytes(16)));
        $user->setHemisId($profile->hemisId);
        $user->setFullName($profile->fullName);
        $user->setImage($profile->picture);
        $user->setIsActive(true);
        $this->applyAccessPolicy($user, $profile);

        if ($studyGroup !== null) {
            $user->setStudyGroup($studyGroup);
        }

        return $user;
    }

    /**
     * Tyutor guruh-biriktiruvi HEMIS "employee-list"dan proaktiv keladi —
     * xodim hali tizimga bir marta ham kirmagan bo'lishi mumkin. `applyAccessPolicy`
     * dagi xodim shoxobchasi bilan bir xil: pending, rolsiz — keyin admin
     * ROLE_TUTOR beradi. OAuth orqali kirganda `hemisId` bo'yicha topilib,
     * shu yozuv qayta ishlatiladi (`HemisLoginService`).
     */
    public function createFromHemisEmployee(HemisEmployee $item): User
    {
        $user = $this->create($item->hemisId . '@hemis.uzswlu.uz', bin2hex(random_bytes(16)));
        $user->setHemisId($item->hemisId);
        $user->setFullName($item->fullName);
        $user->setImage($item->image);
        $user->setIsActive(true);
        $user->setStatus(UserStatusEnum::Pending);
        $user->setRoles([]);

        return $user;
    }

    /**
     * Xodim HEMIS orqali kirsa — admin tasdig'ini kutadi (rolsiz, pending).
     * Talaba HEMIS "type" bo'yicha tasdiqlangan hisoblanadi.
     */
    private function applyAccessPolicy(User $user, HemisProfile $profile): void
    {
        if ($profile->isEmployee()) {
            $user->setStatus(UserStatusEnum::Pending);
            $user->setRoles([]);

            return;
        }

        $user->setStatus(UserStatusEnum::Active);
        $user->setRoles([RoleEnum::Student->value]);
    }
}
