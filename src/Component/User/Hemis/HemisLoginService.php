<?php

declare(strict_types=1);

namespace App\Component\User\Hemis;

use App\Component\Notification\NotificationDispatcher;
use App\Component\User\UserFactory;
use App\Component\User\UserManager;
use App\Entity\StudyGroup;
use App\Entity\User;
use App\Enum\UserStatusEnum;
use App\Repository\StudyGroupRepository;
use App\Repository\UserRepository;

final class HemisLoginService
{
    public function __construct(
        private readonly HemisClient $hemisClient,
        private readonly UserRepository $userRepository,
        private readonly UserFactory $userFactory,
        private readonly UserManager $userManager,
        private readonly StudyGroupRepository $studyGroupRepository,
        private readonly NotificationDispatcher $notificationDispatcher,
    ) {
    }

    public function loginByCode(string $code): User
    {
        $profile = $this->hemisClient->fetchProfile($this->hemisClient->fetchAccessToken($code));
        $user = $this->findExisting($profile);
        $isNew = $user === null;

        if ($isNew) {
            $user = $this->userFactory->createFromHemis($profile, $this->resolveGroup($profile));
        } else {
            $this->refreshFromProfile($user, $profile);
        }

        $this->userManager->save($user, true);

        if ($isNew && $user->getStatus() === UserStatusEnum::Pending) {
            $this->notificationDispatcher->notifyAdminsOnAccessRequest($user);
        }

        return $user;
    }

    /**
     * `id` — HEMIS'ning ichki raqami (dastlabki OAuth yozuvlari shu bilan
     * saqlangan); `login` — Xodim/Talaba ID, HEMIS sinxroni yozuvlarni shu
     * bilan saqlaydi (`employee_id_number` / `student_id_number`). Ikkalasini
     * tekshirmasak, oldindan sinxronlangan tyutor/talaba uchun ikkinchi hisob
     * ochilardi (yoki email unikalligi bo'yicha yiqilardi).
     */
    private function findExisting(HemisProfile $profile): ?User
    {
        return $this->userRepository->findOneBy(['hemisId' => $profile->hemisId])
            ?? $this->userRepository->findOneBy(['hemisId' => $profile->login]);
    }

    private function refreshFromProfile(User $user, HemisProfile $profile): void
    {
        $user->setFullName($profile->fullName);
        $user->setImage($profile->picture);

        if ($user->getStudyGroup() === null) {
            $group = $this->resolveGroup($profile);

            if ($group !== null) {
                $user->setStudyGroup($group);
            }
        }
    }

    private function resolveGroup(HemisProfile $profile): ?StudyGroup
    {
        if ($profile->groupName === null) {
            return null;
        }

        return $this->studyGroupRepository->findOneBy(['externalId' => $profile->groupName])
            ?? $this->studyGroupRepository->findOneBy(['name' => $profile->groupName]);
    }
}
